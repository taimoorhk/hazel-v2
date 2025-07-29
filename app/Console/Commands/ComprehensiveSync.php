<?php

namespace App\Console\Commands;

use App\Services\SupabaseSyncService;
use Illuminate\Console\Command;

class ComprehensiveSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supabase:comprehensive-sync 
                            {--direction=both : Sync direction (laravel-to-supabase, supabase-to-laravel, both)}
                            {--email= : Sync specific user by email}
                            {--force : Force sync even if user exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprehensive sync between Laravel and Supabase (both auth.users and public.users)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $direction = $this->option('direction');
        $email = $this->option('email');
        $force = $this->option('force');

        $this->info('Starting comprehensive Supabase sync...');
        $this->info("Direction: {$direction}");
        
        if ($email) {
            $this->info("Target email: {$email}");
        }

        $syncService = new SupabaseSyncService();

        try {
            switch ($direction) {
                case 'laravel-to-supabase':
                    $this->syncLaravelToSupabase($syncService, $email);
                    break;
                    
                case 'supabase-to-laravel':
                    $this->syncSupabaseToLaravel($syncService, $email);
                    break;
                    
                case 'both':
                default:
                    $this->syncBothDirections($syncService, $email);
                    break;
            }

            $this->info('Comprehensive sync completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Comprehensive sync failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function syncLaravelToSupabase(SupabaseSyncService $syncService, ?string $email)
    {
        if ($email) {
            $this->info("Syncing specific user: {$email}");
            // This would need to be implemented in the service
            $this->warn('Single user sync not yet implemented in service');
        } else {
            $this->info('Syncing all Laravel users to Supabase...');
            $result = $syncService->syncAllLaravelUsersToSupabase();
            
            $this->displaySyncResults($result, 'Laravel to Supabase');
        }
    }

    private function syncSupabaseToLaravel(SupabaseSyncService $syncService, ?string $email)
    {
        if ($email) {
            $this->info("Syncing specific user: {$email}");
            // This would need to be implemented in the service
            $this->warn('Single user sync not yet implemented in service');
        } else {
            $this->info('Syncing all Supabase users to Laravel...');
            $result = $syncService->syncAllSupabaseUsersToLaravel();
            
            $this->displaySyncResults($result, 'Supabase to Laravel');
        }
    }

    private function syncBothDirections(SupabaseSyncService $syncService, ?string $email)
    {
        $this->info('Performing bidirectional sync...');
        
        // First, sync Laravel to Supabase
        $this->info('Step 1: Syncing Laravel users to Supabase...');
        $laravelResult = $syncService->syncAllLaravelUsersToSupabase();
        $this->displaySyncResults($laravelResult, 'Laravel to Supabase');
        
        // Then, sync Supabase to Laravel
        $this->info('Step 2: Syncing Supabase users to Laravel...');
        $supabaseResult = $syncService->syncAllSupabaseUsersToLaravel();
        $this->displaySyncResults($supabaseResult, 'Supabase to Laravel');
        
        // Summary
        $this->info('Bidirectional sync summary:');
        $this->line("  Laravel → Supabase: {$laravelResult['successful']}/{$laravelResult['total']} successful");
        $this->line("  Supabase → Laravel: {$supabaseResult['successful']}/{$supabaseResult['total']} successful");
    }

    private function displaySyncResults(array $result, string $direction)
    {
        $this->info("{$direction} sync results:");
        $this->line("  Total users: {$result['total']}");
        $this->line("  Successful: {$result['successful']}");
        $this->line("  Failed: {$result['failed']}");
        
        if ($result['failed'] > 0) {
            $this->warn("  {$result['failed']} users failed to sync");
            
            // Show first few failures
            $failures = array_filter($result['details'], function($detail) {
                return !$detail['result']['success'];
            });
            
            $failureCount = 0;
            foreach ($failures as $failure) {
                if ($failureCount >= 5) {
                    $this->line("  ... and " . (count($failures) - 5) . " more failures");
                    break;
                }
                $this->line("    ✗ {$failure['email']}: {$failure['result']['message']}");
                $failureCount++;
            }
        }
        
        if ($result['successful'] > 0) {
            $this->info("  ✓ {$result['successful']} users synced successfully");
        }
    }
} 