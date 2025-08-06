<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    /**
     * Show the password reset request form
     */
    public function showResetRequestForm(Request $request)
    {
        $originalUrl = $request->query('url');
        return view('auth.passwords.email', compact('originalUrl'));
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Diese E-Mail-Adresse ist nicht registriert.']);
        }

        $parameters = ['user' => $user->user_id];
        $originalUrl = $request->input('url');
        if ($originalUrl) {
            $parameters['url'] = $originalUrl;
        }

        $url = URL::temporarySignedRoute(
            'password.reset.form',
            now()->addMinutes(60),
            $parameters
        );

        // Send password reset email
        Mail::send('emails.password-reset', ['url' => $url, 'user' => $user], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Passwort zurücksetzen - OpenPIMS');
        });

        return back()->with('status', 'Ein Link zum Zurücksetzen des Passworts wurde an Ihre E-Mail-Adresse gesendet.');
    }

    /**
     * Show the password reset form
     */
    public function showResetForm(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            abort(401, 'Ungültiger oder abgelaufener Link.');
        }

        $originalUrl = $request->query('url');
        return view('auth.passwords.reset', compact('user', 'originalUrl'));
    }

    /**
     * Reset the password
     */
    public function resetPassword(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            abort(401, 'Ungültiger oder abgelaufener Link.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $redirectUrl = '/';
        $originalUrl = $request->input('url') ?: $request->query('url');
        if ($originalUrl) {
            $redirectUrl .= '?url=' . urlencode($originalUrl);
        }

        return redirect($redirectUrl)->with('status', 'Ihr Passwort wurde erfolgreich zurückgesetzt. Sie können sich jetzt anmelden.');
    }
}
