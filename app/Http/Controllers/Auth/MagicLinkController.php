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
use Illuminate\Validation\Rule;

class MagicLinkController extends Controller
{
    /**
     * Send magic link for registration or login
     */
    public function sendMagicLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'cf-turnstile-response' => ['required', Rule::turnstile()],
        ]);

        $user = User::where('email', $request->email)->first();
        $url = $request->input('url');

        if (!$user) {
            // Create new user for registration (passwordless)
            $user = User::create([
                'email' => $request->email,
                'password' => null,
                'email_verified_at' => now(), // Auto-verify via magic link
            ]);

            // Send login magic link (no password setup needed)
            $this->sendLoginMagicLink($user, $url);

            return back()->with('status', 'Willkommen! Ein Login-Link wurde an Ihre E-Mail-Adresse gesendet.');
        } else {
            // Send login magic link
            $this->sendLoginMagicLink($user, $url);

            return back()->with('status', 'Ein Login-Link wurde an Ihre E-Mail-Adresse gesendet.');
        }
    }

    /**
     * Send login magic link
     */
    private function sendLoginMagicLink(User $user, $originalUrl = null)
    {
        $parameters = ['user' => $user->user_id];
        if ($originalUrl) {
            $parameters['url'] = $originalUrl;
        }

        $url = URL::temporarySignedRoute(
            'auth.magic-login',
            now()->addMinutes(120), // Extended to 2 hours
            $parameters
        );

        // Send email with magic link for login
        Mail::send('emails.magic-login', ['url' => $url, 'user' => $user], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Ihr Login-Link - OpenPIMS');
        });
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

            return redirect('/login')
                ->with('error', 'Der Link ist ungültig oder abgelaufen. Bitte fordern Sie einen neuen Link an.');
        }

        // Email verification through magic link
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
        }

        Auth::login($user);

        $redirectUrl = '/';
        if ($request->query('url')) {
            $redirectUrl .= '?url=' . urlencode($request->query('url'));
        }

        return redirect($redirectUrl)->with('status', 'Erfolgreich angemeldet!');
    }
}
