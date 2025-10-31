<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use App\Models\Visit;
use App\Models\ConsentCategory;
use App\Models\ConsentProvider;
use App\Models\Consent;
use App\Models\Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    /**
     * Identify user from deterministic 32-bit token
     */
    private function identifyUser(string $subdomainToken, string $requestingDomain): ?User
    {
        $today = intval(floor(time() / 86400));

        $users = User::all();

        foreach ($users as $user) {
            $input = "{$user->user_id}{$requestingDomain}{$today}";
            $expectedToken = substr(
                hash_hmac('sha256', $input, $user->token),
                0,
                32
            );

            if ($expectedToken === $subdomainToken) {
                return $user;
            }
        }

        $yesterday = $today - 1;
        foreach ($users as $user) {
            $input = "{$user->user_id}{$requestingDomain}{$yesterday}";
            $expectedToken = substr(
                hash_hmac('sha256', $input, $user->token),
                0,
                32
            );

            if ($expectedToken === $subdomainToken) {
                return $user;
            }
        }

        return null;
    }

    /**
     * API endpoint to retrieve user consents for a website
     *
     * 3-Tier Consent Model:
     * 1. Category-Level: User accepts/rejects entire categories (e.g., "all analytics")
     * 2. Provider-Level: User accepts/rejects specific providers (e.g., "Google Analytics" but not "Matomo")
     * 3. Cookie-Level: User accepts/rejects individual cookies (e.g., "_ga" but not "_gid")
     *
     * Priority: Cookie-Level > Provider-Level > Category-Level > Default (reject)
     */
    public function index(Request $request, string $token)
    {
        $url = $request->get('url');

        Log::info('API Request - Token: ' . $token . ', URL: ' . $url);

        $cookies = [];

        if (!is_null($url)) {
            // Get site from cookie definition URL
            $site = Site::where('url', $url)->first();

            if ($site instanceof Site) {
                // Identify user via deterministic token
                $requestingDomain = parse_url($url, PHP_URL_HOST);
                $user = $this->identifyUser($token, $requestingDomain);

                if ($user instanceof User) {
                    // Get all cookies for this site
                    $siteCookies = Cookie::where('site_id', $site->site_id)->get();

                    // Get category-level consents (Tier 1)
                    $categoryConsents = ConsentCategory::where('user_id', $user->user_id)
                        ->where('site_id', $site->site_id)
                        ->get()
                        ->keyBy('category');

                    // Get provider-level consents (Tier 2)
                    $providerConsents = ConsentProvider::where('user_id', $user->user_id)
                        ->where('site_id', $site->site_id)
                        ->get()
                        ->keyBy(function($item) {
                            return $item->category . '|' . $item->provider; // Compound key
                        });

                    // Get cookie-level consents (Tier 3 - most specific)
                    $cookieConsents = Consent::where('user_id', $user->user_id)
                        ->whereIn('cookie_id', $siteCookies->pluck('cookie_id'))
                        ->get()
                        ->keyBy('cookie_id');

                    // Build response: For each cookie, determine consent status
                    foreach ($siteCookies as $cookie) {
                        $checked = null;

                        // Normalize provider name for consistent matching
                        $normalizedProvider = ConsentProvider::normalizeProvider($cookie->provider ?? '');

                        // Priority 1: Cookie-level consent (most specific)
                        if (isset($cookieConsents[$cookie->cookie_id])) {
                            $checked = $cookieConsents[$cookie->cookie_id]->consent_status;
                        }
                        // Priority 2: Provider-level consent
                        elseif (!empty($normalizedProvider)) {
                            $providerKey = $cookie->category . '|' . $normalizedProvider;
                            if (isset($providerConsents[$providerKey])) {
                                $checked = $providerConsents[$providerKey]->consent_status;
                            }
                        }

                        // Priority 3: Category-level consent (fallback)
                        if ($checked === null && isset($categoryConsents[$cookie->category])) {
                            $checked = $categoryConsents[$cookie->category]->consent_status;
                        }

                        // Priority 4: Functional cookies default to allowed (TDDDG §25 Abs. 2)
                        if ($checked === null && ($cookie->necessary || $cookie->category === 'functional')) {
                            $checked = 1;
                        }

                        // Default: Reject (no consent given)
                        if ($checked === null) {
                            $checked = 0;
                        }

                        $cookies[] = [
                            'cookie' => $cookie->cookie,
                            'allowed' => (bool) $checked
                        ];
                    }

                    // Log user visit
                    Visit::updateOrCreate([
                        'site_id' => $site->site_id
                    ], [
                        'user_id' => $user->user_id,
                        'updated_at' => now(),
                    ]);

                    Log::info('API Response - User: ' . $user->user_id . ', Cookies: ' . count($cookies));
                }
            }
        }

        // Response format documented in api.yaml (reference only)
        return response()->json($cookies);
    }
}
