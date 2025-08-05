<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class MagicLinkController extends Controller
{
    /**
     * Send magic link for registration or login
     */
    public function sendMagicLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Create new user for registration
            $user = User::create([
                'email' => $request->email,
                'password' => null,
            ]);

            // Send registration magic link
            $this->sendRegistrationMagicLink($user);

            return back()->with('status', 'Registrierung erfolgreich! Bitte überprüfen Sie Ihre E-Mails und klicken Sie auf den Link, um Ihr Passwort zu setzen.');
        } else {
            // Send login magic link
            $this->sendLoginMagicLink($user);

            return back()->with('status', 'Ein Login-Link wurde an Ihre E-Mail-Adresse gesendet.');
        }
    }

    /**
     * Send registration magic link
     */
    private function sendRegistrationMagicLink(User $user)
    {
        $url = URL::temporarySignedRoute(
            'auth.set-password',
            now()->addMinutes(120), // Extended to 2 hours
            ['user' => $user->user_id]
        );

        // Send email with magic link for password setting
        Mail::send('emails.set-password', ['url' => $url, 'user' => $user], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Setzen Sie Ihr Passwort - OpenPIMS');
        });
    }

    /**
     * Send login magic link
     */
    private function sendLoginMagicLink(User $user)
    {
        $url = URL::temporarySignedRoute(
            'auth.magic-login',
            now()->addMinutes(120), // Extended to 2 hours
            ['user' => $user->user_id]
        );

        // Send email with magic link for login
        Mail::send('emails.magic-login', ['url' => $url, 'user' => $user], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Ihr Login-Link - OpenPIMS');
        });
    }

    /**
     * Show password setting form
     */
    public function showSetPasswordForm(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            // Log detailed information for debugging
            Log::warning('Invalid signature for password setting form', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'request_url' => $request->fullUrl(),
                'request_method' => $request->method(),
                'expires' => $request->query('expires'),
                'signature' => $request->query('signature'),
                'current_time' => now()->timestamp,
                'user_agent' => $request->userAgent(),
            ]);

            return redirect('/')
                ->with('error', 'Der Link ist ungültig oder abgelaufen. Bitte fordern Sie einen neuen Link an, indem Sie sich erneut registrieren oder anmelden.');
        }

        return view('auth.set-password', compact('user'));
    }

    /**
     * Set password for new user
     */
    public function setPassword(Request $request, User $user)
    {
        // For POST requests, signature parameters come from form data, not query string
        $expires = $request->input('expires') ?: $request->query('expires');
        $signature = $request->input('signature') ?: $request->query('signature');

        // Create a temporary request with query parameters for signature validation
        $validationRequest = clone $request;
        if ($request->isMethod('POST') && $expires && $signature) {
            // For POST requests, we need to validate against the original GET URL
            $originalUrl = $request->url() . '?' . http_build_query([
                'expires' => $expires,
                'signature' => $signature
            ]);
            $validationRequest = Request::create($originalUrl, 'GET');
        }

        if (!$validationRequest->hasValidSignature()) {
            // Log detailed information for debugging
            Log::warning('Invalid signature for password setting', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'request_url' => $request->fullUrl(),
                'request_method' => $request->method(),
                'expires' => $expires,
                'signature' => $signature,
                'current_time' => now()->timestamp,
                'user_agent' => $request->userAgent(),
                'validation_url' => $validationRequest->fullUrl(),
            ]);

            return redirect('/')
                ->with('error', 'Der Link ist ungültig oder abgelaufen. Bitte fordern Sie einen neuen Link an, indem Sie sich erneut registrieren oder anmelden.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        // Send welcome email with token instructions
        $this->sendWelcomeEmail($user);

        return redirect('/')->with('status', 'Ihr Passwort wurde erfolgreich gesetzt und Sie sind jetzt angemeldet!');
    }

    /**
     * Magic link login
     */
    public function magicLogin(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            // Log detailed information for debugging
            Log::warning('Invalid signature for magic login', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'request_url' => $request->fullUrl(),
                'request_method' => $request->method(),
                'expires' => $request->query('expires'),
                'signature' => $request->query('signature'),
                'current_time' => now()->timestamp,
                'user_agent' => $request->userAgent(),
            ]);

            return redirect('/')
                ->with('error', 'Der Link ist ungültig oder abgelaufen. Bitte fordern Sie einen neuen Link an, indem Sie sich erneut registrieren oder anmelden.');
        }

        Auth::login($user);

        return redirect('/')->with('status', 'Sie wurden erfolgreich angemeldet!');
    }

    /**
     * Send welcome email with extension instructions
     */
    private function sendWelcomeEmail(User $user)
    {
        Mail::send('emails.welcome', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Willkommen bei OpenPIMS - Installationsanleitung');
        });
    }
}
