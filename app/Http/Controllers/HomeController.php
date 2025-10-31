<?php

namespace App\Http\Controllers;

use App\Models\Cookie;
use App\Models\Site;
use App\Models\Vendor;
use App\Models\Consent;
use App\Models\ConsentCategory;
use App\Models\ConsentProvider;
use App\Models\Standard;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        //check for correct site or create one
        $site = null;
        $url = $request->get('url');
        if ($url) {
            $url = trim($url);
            $site = Site::where('url', $url)->first();
            if (!$site instanceof Site) {
                $site = $this->saveSite($url);
            }
        }

        //Load cookies and consents (3-Tier Model)
        $cookies = [];
        $cookiesByCategory = [];
        $cookiesByProvider = [];
        $categoryConsents = [];
        $providerConsents = [];

        if ($site instanceof Site) {
            // Get all cookies for this site with their consents
            $sql = sprintf("
                SELECT c.*, con.consent_status as cookie_consent
                FROM cookies c
                LEFT JOIN consents con ON c.cookie_id = con.cookie_id AND con.user_id = %d
                WHERE c.site_id = %d
                ORDER BY c.category, c.provider, c.cookie
            ",
                Auth::user()->user_id,
                $site->site_id
            );
            $cookies = DB::select($sql);

            // Group cookies by category for UI
            foreach ($cookies as $cookie) {
                if (!isset($cookiesByCategory[$cookie->category])) {
                    $cookiesByCategory[$cookie->category] = [];
                }
                $cookiesByCategory[$cookie->category][] = $cookie;

                // Also group by provider
                $normalizedProvider = ConsentProvider::normalizeProvider($cookie->provider ?? 'Unknown');
                $providerKey = $cookie->category . '|' . $normalizedProvider;
                if (!isset($cookiesByProvider[$providerKey])) {
                    $cookiesByProvider[$providerKey] = [
                        'category' => $cookie->category,
                        'provider' => $normalizedProvider,
                        'cookies' => []
                    ];
                }
                $cookiesByProvider[$providerKey]['cookies'][] = $cookie;
            }

            // Get category-level consents
            $categoryConsents = ConsentCategory::where('user_id', Auth::user()->user_id)
                ->where('site_id', $site->site_id)
                ->get()
                ->keyBy('category');

            // Get provider-level consents
            $providerConsents = ConsentProvider::where('user_id', Auth::user()->user_id)
                ->where('site_id', $site->site_id)
                ->get()
                ->keyBy(function($item) {
                    return $item->category . '|' . $item->provider;
                });

            //Log user_sites Last visit
            Visit::updateOrCreate([
                'site_id' => $site->site_id
            ], [
                'user_id' => Auth::user()->user_id,
                'updated_at' => now(),
            ]);
        }

        //load all visited sites
        $sql = sprintf("
            SELECT
                v.site_id,
                s.site,
                COALESCE(SUM(CASE WHEN c.necessary = 1 AND con.consent_status = 1 THEN 1 ELSE 0 END), 0) as necessary_count,
                COALESCE(SUM(CASE WHEN c.necessary = 0 AND con.consent_status = 1 THEN 1 ELSE 0 END), 0) as voluntary_count
            FROM visits v
            JOIN sites s ON v.site_id = s.site_id
            LEFT JOIN cookies c ON s.site_id = c.site_id
            LEFT JOIN consents con ON c.cookie_id = con.cookie_id AND con.user_id = %d
            WHERE v.user_id=%d
            GROUP BY v.site_id, s.site
            ORDER BY v.updated_at DESC
        ",
            Auth::user()->user_id,
            Auth::user()->user_id
        );
        $sites = DB::select($sql);

        //check for necessary extension and correct setup
        // Generate deterministic token for host subdomain
        $today = intval(floor(time() / 86400));
        $appDomain = parse_url(env('APP_URL'), PHP_URL_HOST);
        $input = Auth::user()->user_id . $appDomain . $today;
        $deterministicToken = substr(
            hash_hmac('sha256', $input, Auth::user()->token),
            0,
            32
        );

        // Accept both variants:
        // 1. With token subdomain: https://token.openpims.test
        // 2. Without token subdomain: https://openpims.test
        $hostWithToken = str_replace([
            'http://',
            'https://'
        ], [
            'http://' . $deterministicToken . '.',
            'https://' . $deterministicToken . '.'
        ], env('APP_URL'));

        $hostWithoutToken = env('APP_URL');

        $extension_installed = false;
        $valid_url = false;
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);

        // Extract OpenPIMS URL from either X-OpenPIMS header or User-Agent
        $openpimsValue = null;

        // Method 1: Check X-OpenPIMS header (Chrome, Firefox, Chromium)
        if(array_key_exists('x-openpims', $headers)) {
            $openpimsValue = $headers['x-openpims'];
        }

        // Method 2: Check User-Agent for OpenPIMS signal (Safari, Chromium)
        if (!$openpimsValue && array_key_exists('user-agent', $headers)) {
            $userAgent = $headers['user-agent'];
            // Pattern: OpenPIMS/2.0 () or OpenPIMS/2.0 (https://token.domain.de)
            if (preg_match('/OpenPIMS\/[\d.]+\s*\(([^)]*)\)/', $userAgent, $matches)) {
                // Empty parentheses = not-configured, URL in parentheses = configured
                $openpimsValue = empty($matches[1]) ? 'not-configured' : $matches[1];
            }
        }

        // Check if extension is installed (either "not-configured" or a valid URL)
        if ($openpimsValue) {
            if ($openpimsValue === 'not-configured' || filter_var($openpimsValue, FILTER_VALIDATE_URL)) {
                $extension_installed = true;

                // Check if extension is also logged in (URL matches either variant)
                if ($openpimsValue == $hostWithToken || $openpimsValue == $hostWithoutToken) {
                    $valid_url = true;
                }
            }
        }

        $setup_unfinished = !($extension_installed && $valid_url);
        $setup_complete = $extension_installed && $valid_url;

        // Reset reward session if setup is not complete (user logged out or extension not synced)
        if ($setup_unfinished && session('setup_reward_seen')) {
            session()->forget('setup_reward_seen');
        }

        // Show setup modal if setup is unfinished OR just completed (for the reward screen)
        $show_setup = $setup_unfinished || ($setup_complete && !session('setup_reward_seen'));

        // Mark reward as seen once displayed
        if ($setup_complete && $show_setup) {
            session(['setup_reward_seen' => true]);
        }

        // Detect user's browser
        $userAgent = $request->header('User-Agent');
        $detectedBrowser = 'chrome'; // default

        if (strpos($userAgent, 'Edg/') !== false) {
            $detectedBrowser = 'edge';
        } elseif (strpos($userAgent, 'Brave/') !== false || strpos($userAgent, 'Brave') !== false) {
            $detectedBrowser = 'brave';
        } elseif (strpos($userAgent, 'OPR/') !== false || strpos($userAgent, 'Opera/') !== false) {
            $detectedBrowser = 'opera';
        } elseif (strpos($userAgent, 'Firefox/') !== false) {
            $detectedBrowser = 'firefox';
        } elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            $detectedBrowser = 'safari-ios';
        } elseif (strpos($userAgent, 'Safari/') !== false && strpos($userAgent, 'Chrome') === false) {
            $detectedBrowser = 'safari';
        } elseif (strpos($userAgent, 'Chrome/') !== false) {
            $detectedBrowser = 'chrome';
        }

        return view('home', [
            'user' => Auth::user(),
            'sites' => $sites,
            'site' => $site,
            'cookies' => $cookies,
            'cookiesByCategory' => $cookiesByCategory,
            'cookiesByProvider' => $cookiesByProvider,
            'categoryConsents' => $categoryConsents,
            'providerConsents' => $providerConsents,
            'categories' => ConsentCategory::CATEGORIES,
            'url' => $url,
            'show_site' => !is_null($url) && $site instanceof Site,
            'extension_installed' => $extension_installed,
            'valid_url' => $valid_url,
            'setup_unfinished' => $setup_unfinished,
            'setup_complete' => $setup_complete,
            'show_setup' => $show_setup,
            'host' => $hostWithToken,
            'detected_browser' => $detectedBrowser,
        ]);
    }

    private function saveSite(string $url)
    {
        //load categories and parse url
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result= curl_exec ($ch);
        curl_close ($ch);
        $array = json_decode($result, true);
        Log::info('saveSite() - JSON decoded', ['array' => $array]);

        if (!$array || !isset($array['site']) || !isset($array['cookies'])) {
            Log::error('saveSite() - Invalid JSON structure', ['array' => $array, 'result' => $result]);
            throw new \Exception('Invalid cookie definition JSON structure');
        }

        $site = Site::firstOrCreate([
            'site' => $array['site'],
            'url' => $url,
        ]);
        $site_id = $site->site_id;
        Log::info('saveSite() - Site created/found', ['site_id' => $site_id, 'cookie_count' => count($array['cookies'])]);

        foreach ($array['cookies'] as $cookie) {
            // Auto-detect category if not provided
            $category = 'functional'; // default
            if (isset($cookie['category'])) {
                $category = $cookie['category'];
            } elseif (isset($cookie['necessary']) && $cookie['necessary']) {
                $category = 'functional';
            } elseif (isset($cookie['purposes'])) {
                // Auto-categorize based on purpose keywords
                $purposes = strtolower($cookie['purposes']);
                if (strpos($purposes, 'analytic') !== false || strpos($purposes, 'statistic') !== false) {
                    $category = 'analytics';
                } elseif (strpos($purposes, 'marketing') !== false || strpos($purposes, 'advertis') !== false || strpos($purposes, 'track') !== false || strpos($purposes, 'social') !== false || strpos($purposes, 'share') !== false) {
                    $category = 'marketing';
                } elseif (strpos($purposes, 'personaliz') !== false || strpos($purposes, 'preference') !== false) {
                    $category = 'personalization';
                }
            }

            $sql = sprintf("
                INSERT IGNORE
                INTO cookies (cookie, site_id, necessary, category, provider, data_stored, purposes, retention_periods, revocation_info, created_at, updated_at)
                VALUES (
                   '%s',
                   %d,
                   %d,
                   '%s',
                   '%s',
                   '%s',
                   '%s',
                   '%s',
                   '%s',
                   TIMESTAMP(NOW()),
                   TIMESTAMP(NOW())
            )",
                $cookie['cookie'],
                $site_id,
                isset($cookie['necessary']) ? $cookie['necessary'] ? 1 : 0 : 0,
                $category,
                isset($cookie['providers']) ? addslashes($cookie['providers']) : '',
                isset($cookie['data_stored']) ? addslashes($cookie['data_stored']) : '',
                isset($cookie['purposes']) ? addslashes($cookie['purposes']) : '',
                isset($cookie['retention_periods']) ? addslashes($cookie['retention_periods']) : '',
                isset($cookie['revocation_info']) ? addslashes($cookie['revocation_info']) : ''
            );

            try {
                DB::insert($sql);
                Log::debug('saveSite() - Cookie inserted', ['cookie' => $cookie['cookie'], 'site_id' => $site_id]);
            } catch (\Exception $e) {
                Log::error('saveSite() - Cookie insert failed', [
                    'cookie' => $cookie['cookie'],
                    'error' => $e->getMessage(),
                    'sql' => $sql
                ]);
            }
        }

        Log::info('saveSite() - Completed', ['site_id' => $site_id, 'cookies_processed' => count($array['cookies'])]);
        return $site;
    }

    public function save(Request $request)
    {
        #dd($request->all());

        $site_id = $request->input('site_id');
        $consents = $request->input('consents', []);

        $cookies = Cookie::where('site_id', $site_id)->get();
        foreach ($cookies AS $cookie) {
            Consent::updateOrCreate([
                'user_id' => Auth::user()->user_id,
                'cookie_id' => $cookie->cookie_id,
            ], [
                'consent_status' => $cookie->necessary? 1: 0,
            ]);
        }

        foreach ($consents AS $consent) {
            Consent::updateOrCreate([
                'user_id' => Auth::user()->user_id,
                'cookie_id' => $consent,
            ], [
                'consent_status' => 1,
            ]);
        }

        // Get the site information
        $site = Site::find($site_id);
        if ($site && $site->url) {
            // Extract the host part of the URL with proper error handling
            $parsedUrl = parse_url($site->url);

            if ($parsedUrl && isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) {
                $host = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

                // Add port if it exists and is not default
                if (isset($parsedUrl['port']) && 
                    !(($parsedUrl['scheme'] === 'http' && $parsedUrl['port'] === 80) || 
                      ($parsedUrl['scheme'] === 'https' && $parsedUrl['port'] === 443))) {
                    $host .= ':' . $parsedUrl['port'];
                }

                return redirect($host);
            }
        }

        return redirect('/');
    }

    public function visit($siteId)
    {
        // Get the site information
        $site = Site::find($siteId);
        if ($site && $site->url) {
            $parsedUrl = parse_url($site->url);
            if ($parsedUrl && isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) {
                $host = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                if (isset($parsedUrl['port']) && !(($parsedUrl['scheme'] === 'http' && $parsedUrl['port'] === 80) || ($parsedUrl['scheme'] === 'https' && $parsedUrl['port'] === 443))) {
                    $host .= ':' . $parsedUrl['port'];
                }
                return redirect($host);
            }
        }
        return redirect('/');
    }

    public function export(Request $request)
    {
        $fileName = 'openpims.csv';

        $sql = "SELECT DISTINCT sites.site
            FROM consents
            JOIN cookies USING (cookie_id)
            JOIN sites USING (site_id)
            WHERE consents.consent_status = 1
            AND consents.user_id = " . Auth::user()->user_id;
        $consents = DB::select($sql);

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Site');

        $callback = function() use ($consents, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($consents as $consent) {
                $row['Site']  = $consent->site;

                fputcsv($file, array($row['Site']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    /**
     * Get site cookies for AJAX request
     *
     * @param string $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSiteCookies($siteId)
    {
        $site = Site::find($siteId);
        $cookies = Cookie::where('site_id', $siteId)->get();

        // Get user's current cookie-level consents
        $userConsents = Consent::where('user_id', Auth::user()->user_id)
            ->whereIn('cookie_id', $cookies->pluck('cookie_id'))
            ->pluck('consent_status', 'cookie_id');

        // Get user's current category-level consents
        $categoryConsents = ConsentCategory::where('user_id', Auth::user()->user_id)
            ->where('site_id', $siteId)
            ->get()
            ->keyBy('category')
            ->toArray();

        // Get user's current provider-level consents
        $providerConsents = ConsentProvider::where('user_id', Auth::user()->user_id)
            ->where('site_id', $siteId)
            ->get()
            ->keyBy(function($item) {
                return $item->category . '|' . $item->provider;
            })
            ->toArray();

        // Group cookies by provider
        $cookiesByProvider = [];
        foreach ($cookies as $cookie) {
            $normalizedProvider = ConsentProvider::normalizeProvider($cookie->provider ?? 'Unknown');
            $providerKey = $cookie->category . '|' . $normalizedProvider;

            if (!isset($cookiesByProvider[$providerKey])) {
                $cookiesByProvider[$providerKey] = [
                    'category' => $cookie->category,
                    'provider' => $normalizedProvider,
                    'cookieCount' => 0
                ];
            }
            $cookiesByProvider[$providerKey]['cookieCount']++;
        }

        // Add checked status to cookies
        $cookies = $cookies->map(function($cookie) use ($userConsents) {
            $cookie->checked = $userConsents->get($cookie->cookie_id, 0) == 1;
            $cookie->cookie_consent = $cookie->checked; // Alias for backward compatibility
            return $cookie;
        });

        return response()->json([
            'site' => $site,
            'cookies' => $cookies,
            'categoryConsents' => $categoryConsents,
            'providerConsents' => $providerConsents,
            'cookiesByProvider' => $cookiesByProvider
        ]);
    }

    /**
     * Save consent without redirect (for editModal)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editConsent(Request $request)
    {
        $site_id = $request->input('site_id');
        $consents = $request->input('consents', []);

        $cookies = Cookie::where('site_id', $site_id)->get();
        foreach ($cookies AS $cookie) {
            Consent::updateOrCreate([
                'user_id' => Auth::user()->user_id,
                'cookie_id' => $cookie->cookie_id,
            ], [
                'consent_status' => $cookie->necessary? 1: 0,
            ]);
        }

        foreach ($consents AS $consent) {
            Consent::updateOrCreate([
                'user_id' => Auth::user()->user_id,
                'cookie_id' => $consent,
            ], [
                'consent_status' => 1,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Consents saved successfully']);
    }

    /**
     * Save cookie consents for a site.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveConsent(Request $request)
    {
        $site_id = $request->input('site_id');
        $cookies = $request->input('cookies', []);

        Log::info($request->all());

        Log::info('Saving consents for site: ' . $site_id);
        Log::info('Cookies: ', $cookies);

        // Log the site_id
        Log::info('$site_id: ', [$site_id]);

        // First, set all cookies for this site to unchecked
        DB::table('consents')
            ->join('cookies', 'consents.cookie_id', '=', 'cookies.cookie_id')
            ->where('cookies.site_id', $site_id)
            ->where('consents.user_id', Auth::user()->user_id)
            ->update(['consents.checked' => 0]);

        // Then, set the selected cookies to checked
        foreach ($cookies as $cookie) {
            $cookieModel = DB::table('cookies')
                ->where('cookie', trim($cookie))
                ->where('site_id', $site_id)
                ->first();

            if ($cookieModel) {
                Consent::updateOrCreate(
                    [
                        'user_id' => Auth::user()->user_id,
                        'cookie_id' => $cookieModel->cookie_id
                    ],
                    [
                        'checked' => 1
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Cookie preferences saved successfully');
    }

    /**
     * Save category-level consents (Tier 1 - Standard Mode)
     *
     * This allows users to accept/reject entire categories instead of individual cookies.
     * Example: Accept all "analytics" cookies, reject all "marketing" cookies
     */
    public function saveCategoryConsent(Request $request)
    {
        $site_id = $request->input('site_id');
        $categories = $request->input('categories', []); // ['analytics' => true, 'marketing' => false]

        Log::info('Saving category consents for site: ' . $site_id, $categories);

        foreach ($categories as $category => $checked) {
            ConsentCategory::updateOrCreate(
                [
                    'user_id' => Auth::user()->user_id,
                    'site_id' => $site_id,
                    'category' => $category,
                ],
                [
                    'consent_status' => $checked ? 1 : 0,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Category preferences saved successfully',
            'mode' => 'category'
        ]);
    }

    /**
     * Save provider-level consents (Tier 2 - Advanced Mode)
     *
     * This allows users to accept/reject specific providers within categories.
     * Example: Accept "Matomo" but reject "Google Analytics" (both in analytics category)
     */
    public function saveProviderConsent(Request $request)
    {
        $site_id = $request->input('site_id');
        $providers = $request->input('providers', []); // ['analytics|Google Analytics' => true, ...]

        Log::info('Saving provider consents for site: ' . $site_id, $providers);

        foreach ($providers as $providerKey => $checked) {
            // Split the compound key
            list($category, $provider) = explode('|', $providerKey, 2);

            ConsentProvider::updateOrCreate(
                [
                    'user_id' => Auth::user()->user_id,
                    'site_id' => $site_id,
                    'category' => $category,
                    'provider' => $provider,
                ],
                [
                    'consent_status' => $checked ? 1 : 0,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Provider preferences saved successfully',
            'mode' => 'provider'
        ]);
    }

    /**
     * Save mixed consents (3-Tier Model)
     *
     * This supports the 3-Tier model where users can:
     * 1. Set category-level defaults (e.g., "all analytics = yes")
     * 2. Override specific providers (e.g., "but Google Analytics = no")
     * 3. Override specific cookies (e.g., "but _ga = yes")
     */
    public function saveMixedConsent(Request $request)
    {
        $site_id = $request->input('site_id');
        $mode = $request->input('mode', 'category'); // 'category', 'provider', or 'cookie'
        $categories = $request->input('categories', []);
        $providers = $request->input('providers', []); // provider overrides
        $cookies = $request->input('cookies', []); // cookie overrides

        Log::info('Saving mixed consents (3-Tier)', [
            'site_id' => $site_id,
            'mode' => $mode,
            'categories' => $categories,
            'providers' => $providers,
            'cookies' => $cookies
        ]);

        // Save category consents (Tier 1)
        foreach ($categories as $category => $checked) {
            ConsentCategory::updateOrCreate(
                [
                    'user_id' => Auth::user()->user_id,
                    'site_id' => $site_id,
                    'category' => $category,
                ],
                [
                    'consent_status' => $checked ? 1 : 0,
                ]
            );
        }

        // Save provider-level overrides (Tier 2)
        if ($mode === 'provider' || $mode === 'cookie') {
            foreach ($providers as $providerKey => $checked) {
                list($category, $provider) = explode('|', $providerKey, 2);

                ConsentProvider::updateOrCreate(
                    [
                        'user_id' => Auth::user()->user_id,
                        'site_id' => $site_id,
                        'category' => $category,
                        'provider' => $provider,
                    ],
                    [
                        'consent_status' => $checked ? 1 : 0,
                    ]
                );
            }
        }

        // Save cookie-level overrides (Tier 3 - most specific)
        if ($mode === 'cookie' && !empty($cookies)) {
            foreach ($cookies as $cookie_id => $checked) {
                Consent::updateOrCreate(
                    [
                        'user_id' => Auth::user()->user_id,
                        'cookie_id' => $cookie_id,
                    ],
                    [
                        'consent_status' => $checked ? 1 : 0,
                    ]
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Preferences saved successfully (3-Tier)',
            'mode' => $mode
        ]);
    }

}
