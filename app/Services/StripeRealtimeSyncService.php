<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Subscription;

class StripeRealtimeSyncService
{
    protected $supabaseUrl;
    protected $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = config('services.supabase.url');
        $this->supabaseKey = config('services.supabase.service_role_key');
    }

    /**
     * Sync user subscription data to Supabase
     */
    public function syncUserSubscription(User $user): void
    {
        try {
            $subscription = $user->subscription('default');
            
            if ($subscription) {
                $this->syncSubscriptionToSupabase($subscription, $user);
            } else {
                // Remove subscription from Supabase if user has none
                $this->removeSubscriptionFromSupabase($user);
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync user subscription: ' . $e->getMessage());
        }
    }

    /**
     * Sync subscription to Supabase
     */
    protected function syncSubscriptionToSupabase($subscription, User $user): void
    {
        try {
            $subscriptionData = [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'stripe_id' => $subscription->stripe_id,
                'stripe_status' => $subscription->stripe_status,
                'stripe_price' => $subscription->stripe_price,
                'quantity' => $subscription->quantity,
                'trial_ends_at' => $subscription->trial_ends_at,
                'ends_at' => $subscription->ends_at,
                'created_at' => $subscription->created_at,
                'updated_at' => $subscription->updated_at,
            ];

            $response = Http::withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Content-Type' => 'application/json',
                'Prefer' => 'resolution=merge-duplicates',
            ])->post($this->supabaseUrl . '/rest/v1/subscriptions', $subscriptionData);

            if ($response->successful()) {
                Log::info('Subscription synced to Supabase for user: ' . $user->email);
                
                // Sync subscription items
                $this->syncSubscriptionItemsToSupabase($subscription);
            } else {
                Log::error('Failed to sync subscription to Supabase: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Exception syncing subscription to Supabase: ' . $e->getMessage());
        }
    }

    /**
     * Sync subscription items to Supabase
     */
    protected function syncSubscriptionItemsToSupabase($subscription): void
    {
        try {
            // Get subscription items from Laravel
            $items = $subscription->items;
            
            foreach ($items as $item) {
                $itemData = [
                    'subscription_id' => $subscription->stripe_id,
                    'stripe_id' => $item->stripe_id,
                    'stripe_product' => $item->stripe_product,
                    'stripe_price' => $item->stripe_price,
                    'quantity' => $item->quantity,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];

                $response = Http::withHeaders([
                    'apikey' => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                    'Content-Type' => 'application/json',
                    'Prefer' => 'resolution=merge-duplicates',
                ])->post($this->supabaseUrl . '/rest/v1/subscription_items', $itemData);

                if ($response->successful()) {
                    Log::info('Subscription item synced to Supabase: ' . $item->stripe_id);
                } else {
                    Log::error('Failed to sync subscription item to Supabase: ' . $response->body());
                }
            }
        } catch (\Exception $e) {
            Log::error('Exception syncing subscription items to Supabase: ' . $e->getMessage());
        }
    }

    /**
     * Remove subscription from Supabase
     */
    protected function removeSubscriptionFromSupabase(User $user): void
    {
        try {
            // Delete subscription items first
            $response = Http::withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Prefer' => 'resolution=merge-duplicates',
            ])->delete($this->supabaseUrl . '/rest/v1/subscription_items', [
                'user_email' => 'eq.' . $user->email,
            ]);

            // Delete subscription
            $response = Http::withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Prefer' => 'resolution=merge-duplicates',
            ])->delete($this->supabaseUrl . '/rest/v1/subscriptions', [
                'user_email' => 'eq.' . $user->email,
            ]);

            if ($response->successful()) {
                Log::info('Subscription removed from Supabase for user: ' . $user->email);
            }
        } catch (\Exception $e) {
            Log::error('Exception removing subscription from Supabase: ' . $e->getMessage());
        }
    }

    /**
     * Sync all subscriptions to Supabase (for bulk operations)
     */
    public function syncAllSubscriptions(): void
    {
        try {
            $users = User::whereNotNull('stripe_id')->get();
            
            foreach ($users as $user) {
                $this->syncUserSubscription($user);
            }
            
            Log::info('Bulk subscription sync completed for ' . $users->count() . ' users');
        } catch (\Exception $e) {
            Log::error('Bulk subscription sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Get subscription data from Supabase
     */
    public function getSubscriptionFromSupabase(string $userEmail): ?array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])->get($this->supabaseUrl . '/rest/v1/subscriptions', [
                'user_email' => 'eq.' . $userEmail,
                'select' => '*',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return !empty($data) ? $data[0] : null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get subscription from Supabase: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get subscription items from Supabase
     */
    public function getSubscriptionItemsFromSupabase(string $subscriptionId): array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])->get($this->supabaseUrl . '/rest/v1/subscription_items', [
                'subscription_id' => 'eq.' . $subscriptionId,
                'select' => '*',
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Failed to get subscription items from Supabase: ' . $e->getMessage());
        }

        return [];
    }
}
