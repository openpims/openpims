<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use App\Models\User;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\StripeController;
use Illuminate\Http\Request;


Route::group([
    'domain' => env('APP_DOMAIN')
], function () {
    Route::get('/', function (Request $request) {
        if (Auth::check()) {
            // Create a new instance of HomeController without middleware
            $controller = new HomeController();
            return $controller->index($request);
        }

        // Check if URL parameter is present
        $urlParam = $request->get('url');
        if ($urlParam && filter_var($urlParam, FILTER_VALIDATE_URL)) {
            // Check domain restriction if RESTRICTED_DOMAIN is set
            $restrictedDomain = env('RESTRICTED_DOMAIN');
            if ($restrictedDomain) {
                $urlHost = parse_url($urlParam, PHP_URL_HOST);
                if (!$urlHost || (!str_ends_with($urlHost, '.' . $restrictedDomain) && $urlHost !== $restrictedDomain)) {
                    return view('index', [
                        'urlParam' => $urlParam,
                        'domainError' => true,
                        'allowedDomain' => $restrictedDomain
                    ]);
                }
            }

            // Fetch cookie data from the URL using same logic as HomeController
            $cookieData = null;
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $urlParam);
                curl_setopt($ch, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15"));
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 second timeout
                $result = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($result !== false && $httpCode === 200) {
                    $cookieData = json_decode($result, true);
                }
            } catch (Exception $e) {
                // Handle error silently, show URL without cookie data
            }

            return view('index', [
                'urlParam' => $urlParam,
                'cookieData' => $cookieData
            ]);
        }

        return view('index');
    })->name('index');
    Route::post('/', function (Request $request) {
        // Handle cookie acceptance for non-authenticated users
        if ($request->has('accept_all_cookies') && $request->get('accept_all_cookies') == '1') {
            $url = $request->get('url');
            if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                // Set a session flag that cookies were accepted
                session(['cookies_accepted_for_url' => $url]);
                session(['accept_all_cookies' => true]);

                // Redirect to the original URL
                return redirect($url);
            }
        }

        // If not handling cookie acceptance, require authentication
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // For authenticated users, use the original save method
        $controller = new HomeController();
        return $controller->save($request);
    })->name('save');
    // Passwordless authentication - Magic Link only
    Route::get('/login', function() {
        return view('auth.magic-link');
    })->name('login');

    Route::get('/register', function() {
        return view('auth.magic-link');
    })->name('register');

    Route::post('/auth/send-magic-link', [MagicLinkController::class, 'sendMagicLink'])->name('auth.send-magic-link');
    Route::get('/auth/magic-login/{user}', [MagicLinkController::class, 'magicLogin'])->name('auth.magic-login');

    Route::post('/logout', function() {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
    Route::resource('site', SiteController::class)->middleware(['auth']);
    Route::get('/setup', [SetupController::class, 'index'])->name('setup')->middleware(['auth']);
    Route::post('/setup', [SetupController::class, 'index'])->name('setup')->middleware(['auth']);
    Route::get('/export', [HomeController::class, 'export'])->name('export')->middleware(['auth']);
    Route::get('/visit/{siteId}', [HomeController::class, 'visit'])->name('visit')->middleware(['auth']);
    Route::post('/consent/save', [HomeController::class, 'saveConsent'])->name('saveConsent')->middleware(['auth']);
    Route::post('/consent/category/save', [HomeController::class, 'saveCategoryConsent'])->name('saveCategoryConsent')->middleware(['auth']);
    Route::post('/consent/provider/save', [HomeController::class, 'saveProviderConsent'])->name('saveProviderConsent')->middleware(['auth']);
    Route::post('/consent/mixed/save', [HomeController::class, 'saveMixedConsent'])->name('saveMixedConsent')->middleware(['auth']);
    Route::get('/get-site-cookies/{siteId}', [HomeController::class, 'getSiteCookies'])->name('getSiteCookies')->middleware(['auth']);
    Route::post('/edit-consent', [HomeController::class, 'editConsent'])->name('editConsent')->middleware(['auth']);
    Route::get('/user', [UserController::class, 'index'])->name('user')->middleware(['auth']);

    // Extension Setup API
    Route::get('/api/extension/setup', function(Request $request) {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'userId' => Auth::user()->user_id,
            'token' => Auth::user()->token,
            'domain' => env('APP_DOMAIN'),
            'email' => Auth::user()->email
        ]);
    })->middleware('auth')->name('extension.setup.api');

    // Extension Installation Check API
    Route::get('/api/extension-check', function(Request $request) {
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        $extension_installed = false;
        $valid_url = false;

        // Extract OpenPIMS URL from either X-OpenPIMS header or User-Agent
        $openpimsValue = null;

        // Method 1: Check X-OpenPIMS header (Chrome, Firefox, Chromium)
        if (array_key_exists('x-openpims', $headers)) {
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

        // Extension is installed if signal exists (either "not-configured" or a valid URL)
        if ($openpimsValue) {
            if ($openpimsValue === 'not-configured' || filter_var($openpimsValue, FILTER_VALIDATE_URL)) {
                $extension_installed = true;

                // Check if extension is also synchronized (URL matches)
                if (Auth::check() && filter_var($openpimsValue, FILTER_VALIDATE_URL)) {
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

                    // Debug logging
                    \Log::info('[Extension Check] Comparing values:', [
                        'openpimsValue' => $openpimsValue,
                        'expected_host_with_token' => $hostWithToken,
                        'expected_host_without_token' => $hostWithoutToken,
                        'app_url' => env('APP_URL'),
                        'app_domain' => $appDomain,
                        'token' => $deterministicToken,
                        'user_id' => Auth::user()->user_id,
                        'match' => $openpimsValue == $hostWithToken || $openpimsValue == $hostWithoutToken
                    ]);

                    if ($openpimsValue == $hostWithToken || $openpimsValue == $hostWithoutToken) {
                        $valid_url = true;
                    }
                }
            }
        }

        return response()->json([
            'extension_installed' => $extension_installed,
            'valid_url' => $valid_url
        ]);
    })->name('extension.check.api');

    // Stripe Subscription Routes
    Route::get('/subscription/checkout', [StripeController::class, 'createCheckoutSession'])->name('subscription.checkout')->middleware(['auth']);
    Route::get('/subscription/success', [StripeController::class, 'success'])->name('subscription.success');
    Route::get('/subscription/cancel', [StripeController::class, 'cancel'])->name('subscription.cancel');
    Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');
});

Route::group([
    'domain' => '{token}.' . env('APP_DOMAIN')
], function () {
    Route::resource('/', ApiController::class)->names('api');
});
