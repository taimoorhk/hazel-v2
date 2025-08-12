<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StripeRealtimeSyncService;
use App\Models\User;

class SyncStripeSubscriptionsToSupabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:sync-subscriptions {--user= : Sync specific user by email} {--all : Sync all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Stripe subscriptions to Supabase database';

    /**
     * Execute the console command.
     */
    public function handle(StripeRealtimeSyncService $syncService): int
    {
        $this->info('Starting Stripe subscription sync to Supabase...');

        if ($this->option('user')) {
            $email = $this->option('user');
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $this->error("User with email '{$email}' not found.");
                return 1;
            }

            $this->info("Syncing subscription for user: {$email}");
            $syncService->syncUserSubscription($user);
            $this->info("Subscription sync completed for user: {$email}");
            
        } elseif ($this->option('all')) {
            $this->info('Syncing all user subscriptions...');
            $syncService->syncAllSubscriptions();
            $this->info('All subscription syncs completed.');
            
        } else {
            $this->error('Please specify either --user=email or --all option.');
            $this->info('Usage examples:');
            $this->info('  php artisan stripe:sync-subscriptions --user=user@example.com');
            $this->info('  php artisan stripe:sync-subscriptions --all');
            return 1;
        }

        $this->info('Stripe subscription sync completed successfully!');
        return 0;
    }
}
