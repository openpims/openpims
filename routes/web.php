<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\Auth\PasswordResetController;
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
        return view('index');
    })->name('index');
    Route::post('/', [HomeController::class, 'save'])->name('save')->middleware(['auth', 'verified']);
    //Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware(['auth', 'verified']);
    // Custom magic link authentication routes
    Route::post('/register', [MagicLinkController::class, 'sendMagicLink'])->name('register');
    Route::post('/login', [MagicLinkController::class, 'sendMagicLink'])->name('login');
    Route::get('/auth/set-password/{user}', [MagicLinkController::class, 'showSetPasswordForm'])->name('auth.set-password');
    Route::post('/auth/set-password/{user}', [MagicLinkController::class, 'setPassword']);
    Route::get('/auth/magic-login/{user}', [MagicLinkController::class, 'magicLogin'])->name('auth.magic-login');

    // Keep logout route from Laravel auth
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

    // Password reset routes
    Route::get('/password/reset', [PasswordResetController::class, 'showResetRequestForm'])->name('password.request');
    Route::post('/password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/password/reset/{user}', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/password/reset/{user}', [PasswordResetController::class, 'resetPassword'])->name('password.update');
    Route::resource('site', SiteController::class)->middleware(['auth', 'verified']);
    Route::get('/setup', [SetupController::class, 'index'])->name('setup')->middleware(['auth', 'verified']);
    Route::post('/setup', [SetupController::class, 'index'])->name('setup')->middleware(['auth', 'verified']);
    Route::get('/export', [HomeController::class, 'export'])->name('export')->middleware(['auth', 'verified']);
    Route::get('/category/{site_id}', [HomeController::class, 'category'])->name('category')->middleware(['auth', 'verified']);
    Route::get('/consent/{standard}/{category_id}', [HomeController::class, 'consent'])->name('category')->middleware(['auth', 'verified']);
    Route::post('/consent/save', [HomeController::class, 'saveConsent'])->name('saveConsent')->middleware(['auth', 'verified']);
    Route::get('/user', [UserController::class, 'index'])->name('user')->middleware(['auth', 'verified']);
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
    Route::resource('/', ApiController::class);
});
