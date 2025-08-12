<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;

class BillingController extends Controller
{
    /**
     * Show billing dashboard
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $subscription = $user->subscription('default');
        $paymentMethod = null;

        if ($user->stripe_id) {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                $customer = \Stripe\Customer::retrieve($user->stripe_id);
                
                if ($customer->invoice_settings->default_payment_method) {
                    $paymentMethod = \Stripe\PaymentMethod::retrieve(
                        $customer->invoice_settings->default_payment_method
                    );
                }
            } catch (\Exception $e) {
                // Handle Stripe error
            }
        }

        return Inertia::render('Billing', [
            'user' => $user,
            'subscription' => $subscription ? [
                'id' => $subscription->id,
                'stripe_id' => $subscription->stripe_id,
                'status' => $subscription->stripe_status,
                'price' => $subscription->stripe_price,
                'quantity' => $subscription->quantity,
                'trial_ends_at' => $subscription->trial_ends_at,
                'ends_at' => $subscription->ends_at,
                'created_at' => $subscription->created_at,
            ] : null,
            'paymentMethod' => $paymentMethod ? [
                'type' => $paymentMethod->type,
                'card' => $paymentMethod->card ? [
                    'brand' => $paymentMethod->card->brand,
                    'last4' => $paymentMethod->card->last4,
                    'exp_month' => $paymentMethod->card->exp_month,
                    'exp_year' => $paymentMethod->card->exp_year,
                ] : null,
            ] : null,
            'stripeKey' => config('services.stripe.key'),
        ]);
    }

    /**
     * Show billing success page
     */
    public function success(Request $request): Response
    {
        $sessionId = $request->query('session_id');
        
        return Inertia::render('BillingSuccess', [
            'sessionId' => $sessionId,
        ]);
    }

    /**
     * Show billing cancel page
     */
    public function cancel(): Response
    {
        return Inertia::render('BillingCancel');
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        try {
            $subscription = $user->subscription('default');
            
            if ($subscription) {
                $subscription->cancel();
                return response()->json(['message' => 'Subscription cancelled successfully']);
            }
            
            return response()->json(['error' => 'No active subscription found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to cancel subscription'], 500);
        }
    }

    /**
     * Resume subscription
     */
    public function resumeSubscription(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        try {
            $subscription = $user->subscription('default');
            
            if ($subscription && $subscription->cancelled()) {
                $subscription->resume();
                return response()->json(['message' => 'Subscription resumed successfully']);
            }
            
            return response()->json(['error' => 'No cancelled subscription found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to resume subscription'], 500);
        }
    }

    /**
     * Update payment method
     */
    public function updatePaymentMethod(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            
            $paymentMethodId = $request->input('payment_method_id');
            
            // Attach payment method to customer
            $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach([
                'customer' => $user->stripe_id,
            ]);

            // Set as default payment method
            \Stripe\Customer::update($user->stripe_id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            ]);

            return response()->json(['message' => 'Payment method updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update payment method'], 500);
        }
    }
}
