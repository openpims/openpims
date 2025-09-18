<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'OpenPIMS Monatliche Subscription',
                            'description' => 'Monatlicher Zugang zu OpenPIMS',
                        ],
                        'unit_amount' => 999, // 9.99 EUR in Cent
                        'recurring' => [
                            'interval' => 'month',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => url('/subscription/success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => url('/subscription/cancel'),
                'client_reference_id' => $user->user_id,
                'customer_email' => $user->email,
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Stripe Checkout Session Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['stripe' => 'Fehler beim Erstellen der Checkout-Session.']);
        }
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        
        if (!$sessionId) {
            return redirect()->route('home')->withErrors(['stripe' => 'Ungültige Session-ID.']);
        }

        try {
            $session = Session::retrieve($sessionId);
            $user = User::find($session->client_reference_id);
            
            if ($user && $session->payment_status === 'paid') {
                $user->stripe_customer_id = $session->customer;
                $user->subscription_status = 'active';
                $user->subscription_id = $session->subscription;
                $user->save();
                
                return redirect()->route('home')->with('success', 'Subscription erfolgreich aktiviert!');
            }
            
            return redirect()->route('home')->withErrors(['stripe' => 'Zahlung nicht erfolgreich.']);
        } catch (\Exception $e) {
            Log::error('Stripe Success Error: ' . $e->getMessage());
            return redirect()->route('home')->withErrors(['stripe' => 'Fehler beim Verarbeiten der Zahlung.']);
        }
    }

    public function cancel()
    {
        return redirect()->route('home')->with('info', 'Subscription wurde abgebrochen. Du kannst es jederzeit erneut versuchen.');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('stripe-signature');
        $webhookSecret = config('services.stripe.webhook.secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook Signature Verification Failed: ' . $e->getMessage());
            return response('Invalid signature', 400);
        }

        switch ($event['type']) {
            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
                $subscription = $event['data']['object'];
                $this->handleSubscriptionChange($subscription);
                break;
            
            case 'invoice.payment_succeeded':
                $invoice = $event['data']['object'];
                $this->handlePaymentSucceeded($invoice);
                break;
                
            case 'invoice.payment_failed':
                $invoice = $event['data']['object'];
                $this->handlePaymentFailed($invoice);
                break;
        }

        return response('Webhook handled', 200);
    }

    private function handleSubscriptionChange($subscription)
    {
        $user = User::where('stripe_customer_id', $subscription['customer'])->first();
        
        if ($user) {
            $user->subscription_status = $subscription['status'];
            $user->save();
            Log::info('Updated subscription status for user ' . $user->user_id . ' to ' . $subscription['status']);
        }
    }

    private function handlePaymentSucceeded($invoice)
    {
        $user = User::where('stripe_customer_id', $invoice['customer'])->first();
        
        if ($user) {
            $user->subscription_status = 'active';
            $user->save();
            Log::info('Payment succeeded for user ' . $user->user_id);
        }
    }

    private function handlePaymentFailed($invoice)
    {
        $user = User::where('stripe_customer_id', $invoice['customer'])->first();
        
        if ($user) {
            $user->subscription_status = 'past_due';
            $user->save();
            Log::info('Payment failed for user ' . $user->user_id);
        }
    }
}