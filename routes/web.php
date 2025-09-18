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
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\StripeController;
use Illuminate\Http\Request;


Route::group([
    'domain' => App::environment('local') ? 'openpims.test' : env('APP_DOMAIN')
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
    //Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware(['auth', 'verified']);
    // Standard Laravel authentication routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Custom magic link authentication routes (preserved for later use)
    Route::post('/magic-register', [MagicLinkController::class, 'sendMagicLink'])->name('magic.register');
    Route::post('/magic-login', [MagicLinkController::class, 'sendMagicLink'])->name('magic.login');
    Route::get('/auth/set-password/{user}', [MagicLinkController::class, 'showSetPasswordForm'])->name('auth.set-password');
    Route::post('/auth/set-password/{user}', [MagicLinkController::class, 'setPassword']);
    Route::get('/auth/magic-login/{user}', [MagicLinkController::class, 'magicLogin'])->name('auth.magic-login');

    // Keep logout route from Laravel auth
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Email verification routes
    Route::get('/email/verify', function () {
        return view('auth.verify');
    })->middleware('auth')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['auth', 'signed'])
        ->name('verification.verify');

    Route::post('/email/resend', [VerificationController::class, 'resend'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.resend');

    // Password reset routes
    Route::get('/password/reset', [PasswordResetController::class, 'showResetRequestForm'])->name('password.request');
    Route::post('/password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/password/reset/{user}', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/password/reset/{user}', [PasswordResetController::class, 'resetPassword'])->name('password.update');
    Route::resource('site', SiteController::class)->middleware(['auth']);
    Route::get('/setup', [SetupController::class, 'index'])->name('setup')->middleware(['auth']);
    Route::post('/setup', [SetupController::class, 'index'])->name('setup')->middleware(['auth']);
    Route::get('/export', [HomeController::class, 'export'])->name('export')->middleware(['auth']);
    Route::get('/visit/{siteId}', [HomeController::class, 'visit'])->name('visit')->middleware(['auth']);
    Route::post('/consent/save', [HomeController::class, 'saveConsent'])->name('saveConsent')->middleware(['auth']);
    Route::get('/get-site-cookies/{siteId}', [HomeController::class, 'getSiteCookies'])->name('getSiteCookies')->middleware(['auth']);
    Route::post('/edit-consent', [HomeController::class, 'editConsent'])->name('editConsent')->middleware(['auth']);
    Route::get('/user', [UserController::class, 'index'])->name('user')->middleware(['auth']);
    
    // Stripe Subscription Routes
    Route::get('/subscription/checkout', [StripeController::class, 'createCheckoutSession'])->name('subscription.checkout')->middleware(['auth']);
    Route::get('/subscription/success', [StripeController::class, 'success'])->name('subscription.success');
    Route::get('/subscription/cancel', [StripeController::class, 'cancel'])->name('subscription.cancel');
    Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');
});

Route::group([
    'domain' => App::environment('local') ? 'me.openpims.test' : 'me.' . env('APP_DOMAIN')
], function () {
    Route::get('/', function (Request $request) {
        $username = $request->getUser();
        $password = $request->getPassword();

        $user = User::where('email', $username)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response('Unauthorized', 401, ['WWW-Authenticate' => 'Basic']);
        }

        // Benutzer für die Dauer des Requests setzen
        //Auth::setUser($user);

        if (App::environment('local')) {
            return sprintf("https://%s.openpims.test", $user->token);
        } else {
            return sprintf("https://%s.%s", $user->token, env('APP_DOMAIN'));
        }

        //return response()->json($user);
    });
});

Route::group([
    'domain' => App::environment('local') ? '{token}.openpims.test' : '{token}.' . env('APP_DOMAIN')
], function () {
    Route::resource('/', ApiController::class)->names('api');
});
