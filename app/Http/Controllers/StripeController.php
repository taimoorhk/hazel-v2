<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\User;
use App\Services\SupabaseSyncService;

class StripeController extends Controller
{
    protected $supabaseSyncService;

    public function __construct(SupabaseSyncService $supabaseSyncService)
    {
        $this->supabaseSyncService = $supabaseSyncService;
    }

    /**
     * Create a Stripe checkout session
     */
    public function createCheckoutSession(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            Stripe::setApiKey(config('services.stripe.secret'));

            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => config('services.stripe.price_id'),
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('billing.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('billing.cancel'),
                'customer_email' => $user->email,
                'metadata' => [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ],
            ]);

            return response()->json(['sessionId' => $checkout_session->id]);
        } catch (\Exception $e) {
            Log::error('Stripe checkout session creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create checkout session'], 500);
        }
    }

    /**
     * Handle Stripe webhooks
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (SignatureVerificationException $e) {
            Log::error('Webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event->data->object);
                    break;
                case 'customer.subscription.created':
                    $this->handleSubscriptionCreated($event->data->object);
                    break;
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event->data->object);
                    break;
                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object);
                    break;
                case 'invoice.payment_succeeded':
                    $this->handleInvoicePaymentSucceeded($event->data->object);
                    break;
                case 'invoice.payment_failed':
                    $this->handleInvoicePaymentFailed($event->data->object);
                    break;
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Webhook handling failed: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook handling failed'], 500);
        }
    }

    /**
     * Handle checkout session completed
     */
    protected function handleCheckoutSessionCompleted($session): void
    {
        $user = User::where('email', $session->customer_email)->first();
        if ($user) {
            $user->stripe_id = $session->customer;
            $user->save();
            
            // Sync to Supabase
            $this->supabaseSyncService->syncUser($user);
        }
    }

    /**
     * Handle subscription created
     */
    protected function handleSubscriptionCreated($subscription): void
    {
        $user = User::where('stripe_id', $subscription->customer)->first();
        if ($user) {
            $user->subscriptions()->create([
                'name' => 'default',
                'stripe_id' => $subscription->id,
                'stripe_status' => $subscription->status,
                'stripe_price' => $subscription->items->data[0]->price->id,
                'quantity' => $subscription->items->data[0]->quantity,
                'trial_ends_at' => $subscription->trial_end ? date('Y-m-d H:i:s', $subscription->trial_end) : null,
                'ends_at' => $subscription->cancel_at ? date('Y-m-d H:i:s', $subscription->cancel_at) : null,
            ]);

            // Sync to Supabase
            $this->syncSubscriptionToSupabase($subscription, $user);
        }
    }

    /**
     * Handle subscription updated
     */
    protected function handleSubscriptionUpdated($subscription): void
    {
        $user = User::where('stripe_id', $subscription->customer)->first();
        if ($user) {
            $userSubscription = $user->subscriptions()->where('stripe_id', $subscription->id)->first();
            if ($userSubscription) {
                $userSubscription->update([
                    'stripe_status' => $subscription->status,
                    'stripe_price' => $subscription->items->data[0]->price->id,
                    'quantity' => $subscription->items->data[0]->quantity,
                    'trial_ends_at' => $subscription->trial_end ? date('Y-m-d H:i:s', $subscription->trial_end) : null,
                    'ends_at' => $subscription->cancel_at ? date('Y-m-d H:i:s', $subscription->cancel_at) : null,
                ]);

                // Sync to Supabase
                $this->syncSubscriptionToSupabase($subscription, $user);
            }
        }
    }

    /**
     * Handle subscription deleted
     */
    protected function handleSubscriptionDeleted($subscription): void
    {
        $user = User::where('stripe_id', $subscription->customer)->first();
        if ($user) {
            $userSubscription = $user->subscriptions()->where('stripe_id', $subscription->id)->first();
            if ($userSubscription) {
                $userSubscription->update([
                    'stripe_status' => 'canceled',
                    'ends_at' => now(),
                ]);

                // Sync to Supabase
                $this->syncSubscriptionToSupabase($subscription, $user);
            }
        }
    }

    /**
     * Handle invoice payment succeeded
     */
    protected function handleInvoicePaymentSucceeded($invoice): void
    {
        $user = User::where('stripe_id', $invoice->customer)->first();
        if ($user) {
            // Update payment method info
            if ($invoice->payment_intent) {
                $paymentIntent = \Stripe\PaymentIntent::retrieve($invoice->payment_intent);
                if ($paymentIntent->payment_method) {
                    $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentIntent->payment_method);
                    $user->update([
                        'pm_type' => $paymentMethod->card->brand,
                        'pm_last_four' => $paymentMethod->card->last4,
                    ]);
                }
            }

            // Sync to Supabase
            $this->supabaseSyncService->syncUser($user);
        }
    }

    /**
     * Handle invoice payment failed
     */
    protected function handleInvoicePaymentFailed($invoice): void
    {
        $user = User::where('stripe_id', $invoice->customer)->first();
        if ($user) {
            // Handle failed payment
            Log::warning('Payment failed for user: ' . $user->email);
            
            // Sync to Supabase
            $this->supabaseSyncService->syncUser($user);
        }
    }

    /**
     * Sync subscription data to Supabase
     */
    protected function syncSubscriptionToSupabase($stripeSubscription, $user): void
    {
        try {
            $supabase = $this->supabaseSyncService->getSupabaseClient();
            
            // Sync subscription
            $subscriptionData = [
                'id' => $stripeSubscription->id,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'stripe_id' => $stripeSubscription->id,
                'stripe_status' => $stripeSubscription->status,
                'stripe_price' => $stripeSubscription->items->data[0]->price->id,
                'quantity' => $stripeSubscription->items->data[0]->quantity,
                'trial_ends_at' => $stripeSubscription->trial_end ? date('Y-m-d H:i:s', $stripeSubscription->trial_end) : null,
                'ends_at' => $stripeSubscription->cancel_at ? date('Y-m-d H:i:s', $subscription->cancel_at) : null,
                'created_at' => date('Y-m-d H:i:s', $stripeSubscription->created),
                'updated_at' => date('Y-m-d H:i:s', $stripeSubscription->created),
            ];

            $supabase->from('subscriptions')->upsert($subscriptionData, ['stripe_id']);

            // Sync subscription items
            foreach ($stripeSubscription->items->data as $item) {
                $itemData = [
                    'subscription_id' => $stripeSubscription->id,
                    'stripe_id' => $item->id,
                    'stripe_product' => $item->price->product,
                    'stripe_price' => $item->price->id,
                    'quantity' => $item->quantity,
                    'created_at' => date('Y-m-d H:i:s', $item->created),
                    'updated_at' => date('Y-m-d H:i:s', $item->created),
                ];

                $supabase->from('subscription_items')->upsert($itemData, ['stripe_id']);
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync subscription to Supabase: ' . $e->getMessage());
        }
    }
}
