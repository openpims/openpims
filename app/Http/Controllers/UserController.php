<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Consent;
use App\Models\ConsentCategory;
use App\Models\ConsentProvider;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
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
        if (Auth::user()->user_id!=1) {
            abort(403, 'Unauthorized');
        }

        return view('user', [
            'users' => User::all(),
        ]);
    }

    /**
     * Show user settings page
     */
    public function settings()
    {
        $user = Auth::user();

        // Calculate statistics
        $stats = [
            'consents_categories' => ConsentCategory::where('user_id', $user->user_id)->count(),
            'consents_providers' => ConsentProvider::where('user_id', $user->user_id)->count(),
            'consents_cookies' => Consent::where('user_id', $user->user_id)->count(),
            'visits' => Visit::where('user_id', $user->user_id)->count(),
        ];

        return view('user.settings', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * DSGVO Art. 15: Structured data request
     */
    public function dataRequest()
    {
        $user = Auth::user();

        $data = [
            'generated_at' => date('Y-m-d H:i:s'),
            'request_type' => 'DSGVO Art. 15 - Auskunftsrecht',

            'personal_data' => [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at?->format('Y-m-d H:i:s'),
                'account_created' => $user->created_at->format('Y-m-d H:i:s'),
                'last_updated' => $user->updated_at->format('Y-m-d H:i:s'),
            ],

            'subscription' => [
                'stripe_customer_id' => $user->stripe_customer_id,
                'status' => $user->subscription_status ?? 'none',
                'subscription_id' => $user->subscription_id,
            ],

            'consent_statistics' => [
                'category_consents' => ConsentCategory::where('user_id', $user->user_id)->count(),
                'provider_consents' => ConsentProvider::where('user_id', $user->user_id)->count(),
                'cookie_consents' => Consent::where('user_id', $user->user_id)->count(),
                'total_visits' => Visit::where('user_id', $user->user_id)->count(),
            ],

            'detailed_consents' => [
                'categories' => ConsentCategory::where('user_id', $user->user_id)
                    ->with('site:site_id,site,url')
                    ->get()
                    ->map(function($consent) {
                        return [
                            'site' => $consent->site->site,
                            'site_url' => $consent->site->url,
                            'category' => $consent->category,
                            'consent_status' => $consent->consent_status ? 'accepted' : 'rejected',
                            'updated_at' => $consent->updated_at->format('Y-m-d H:i:s'),
                        ];
                    }),

                'providers' => ConsentProvider::where('user_id', $user->user_id)
                    ->with('site:site_id,site,url')
                    ->get()
                    ->map(function($consent) {
                        return [
                            'site' => $consent->site->site,
                            'site_url' => $consent->site->url,
                            'category' => $consent->category,
                            'provider' => $consent->provider,
                            'consent_status' => $consent->consent_status ? 'accepted' : 'rejected',
                            'updated_at' => $consent->updated_at->format('Y-m-d H:i:s'),
                        ];
                    }),

                'cookies' => Consent::where('user_id', $user->user_id)
                    ->with(['cookie' => function($query) {
                        $query->with('site:site_id,site,url');
                    }])
                    ->get()
                    ->map(function($consent) {
                        return [
                            'site' => $consent->cookie->site->site,
                            'site_url' => $consent->cookie->site->url,
                            'cookie_name' => $consent->cookie->cookie,
                            'category' => $consent->cookie->category,
                            'providers' => $consent->cookie->providers,
                            'consent_status' => $consent->consent_status ? 'accepted' : 'rejected',
                            'consented_at' => $consent->consented_at?->format('Y-m-d H:i:s'),
                            'updated_at' => $consent->updated_at->format('Y-m-d H:i:s'),
                        ];
                    }),
            ],

            'data_recipients' => [
                [
                    'name' => 'Stripe Inc.',
                    'purpose' => 'Payment processing',
                    'country' => 'USA',
                    'legal_basis' => 'Art. 6 Abs. 1 lit. b DSGVO (Contract performance)',
                    'safeguards' => 'Standard Contractual Clauses (SCC)',
                ],
                [
                    'name' => 'Mailgun Technologies Inc.',
                    'purpose' => 'Email delivery (Magic Link authentication)',
                    'country' => 'USA',
                    'legal_basis' => 'Art. 6 Abs. 1 lit. b DSGVO (Contract performance)',
                    'safeguards' => 'Standard Contractual Clauses (SCC)',
                ],
                [
                    'name' => 'Cloudflare Inc.',
                    'purpose' => 'Bot protection (Turnstile)',
                    'country' => 'USA',
                    'legal_basis' => 'Art. 6 Abs. 1 lit. f DSGVO (Legitimate interest)',
                    'safeguards' => 'Standard Contractual Clauses (SCC)',
                ],
            ],

            'retention' => [
                'storage_period' => 'Until account deletion or 3 years of inactivity',
                'legal_basis' => 'Art. 6 Abs. 1 lit. b DSGVO',
            ],

            'rights' => [
                'rectification' => 'Art. 16 DSGVO - You can change your email in account settings',
                'erasure' => 'Art. 17 DSGVO - You can delete your account in account settings',
                'restriction' => 'Art. 18 DSGVO - Contact us at the email provided',
                'data_portability' => 'Art. 20 DSGVO - Use the export function in your account',
                'objection' => 'Art. 21 DSGVO - Contact us at the email provided',
                'complaint' => 'Art. 77 DSGVO - You can file a complaint with BfDI (Germany)',
            ],
        ];

        $fileName = 'dsgvo-auskunft-' . date('Y-m-d-His') . '.json';

        return response()->json($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * DSGVO Art. 17: Delete user account and all associated data
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        $userId = $user->user_id;
        $email = $user->email;

        try {
            DB::beginTransaction();

            // Delete all user data
            Consent::where('user_id', $userId)->delete();
            ConsentCategory::where('user_id', $userId)->delete();
            ConsentProvider::where('user_id', $userId)->delete();
            Visit::where('user_id', $userId)->delete();

            // Cancel Stripe subscription if exists
            if ($user->subscription_id) {
                try {
                    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                    \Stripe\Subscription::update($user->subscription_id, [
                        'cancel_at_period_end' => true
                    ]);
                    Log::info('Stripe subscription cancelled for deleted user: ' . $userId);
                } catch (\Exception $e) {
                    Log::error('Failed to cancel Stripe subscription: ' . $e->getMessage());
                    // Continue with deletion even if Stripe fails
                }
            }

            // Delete user account
            $user->delete();

            DB::commit();

            // Logout user
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('User account deleted: ' . $userId . ' (' . $email . ')');

            return redirect('/')->with('status', 'Ihr Account wurde erfolgreich gelöscht. Alle Ihre Daten wurden unwiderruflich entfernt.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Account deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Fehler beim Löschen des Accounts: ' . $e->getMessage());
        }
    }
}
