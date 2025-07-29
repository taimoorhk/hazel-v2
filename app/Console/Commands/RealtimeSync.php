<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\RealtimeSyncService;
use Illuminate\Console\Command;

class RealtimeSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supabase:realtime-sync 
                            {--email= : Sync specific user by email}
                            {--all : Sync all users}
                            {--force : Force sync even if user exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger real-time sync between Laravel and Supabase';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $all = $this->option('all');
        $force = $this->option('force');

        $realtimeSync = new RealtimeSyncService();

        if ($email) {
            // Sync specific user
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("User with email {$email} not found.");
                return 1;
            }
            
            $this->info("Triggering real-time sync for: {$email}");
            $result = $realtimeSync->syncUserToSupabase($user, 'manual');
            
            if ($result['success']) {
                $this->info("✓ {$email} synced successfully");
                $this->line("  Auth: " . ($result['auth_synced'] ? 'OK' : 'FAIL'));
                $this->line("  Public: " . ($result['public_synced'] ? 'OK' : 'FAIL'));
            } else {
                $this->error("✗ {$email} sync failed: {$result['message']}");
            }
            
        } elseif ($all) {
            // Sync all users
            $users = User::all();
            $this->info("Triggering real-time sync for all {$users->count()} users...");
            
            $bar = $this->output->createProgressBar($users->count());
            $bar->start();
            
            $successful = 0;
            $failed = 0;
            
            foreach ($users as $user) {
                $result = $realtimeSync->syncUserToSupabase($user, 'bulk');
                
                if ($result['success']) {
                    $successful++;
                } else {
                    $failed++;
                }
                
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            
            $this->info("Real-time sync completed:");
            $this->line("  Successful: {$successful}");
            $this->line("  Failed: {$failed}");
            
        } else {
            $this->error('Please specify --email=user@example.com or --all to sync users.');
            return 1;
        }

        return 0;
    }
} 