<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SupabaseSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncExistingUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supabase:sync-existing-users {--email= : Sync specific user by email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync existing Laravel users with Supabase by creating Supabase users for them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $supabaseApiKey = config('services.supabase.service_role_key');
        $supabaseUrl = config('services.supabase.url');

        if (!$supabaseApiKey || !$supabaseUrl) {
            $this->error('Supabase configuration missing. Please check your .env file.');
            return 1;
        }

        $email = $this->option('email');
        
        if ($email) {
            // Sync specific user
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("User with email {$email} not found.");
                return 1;
            }
            
            if ($user->supabase_id) {
                $this->info("User {$email} already has Supabase ID: {$user->supabase_id}");
                return 0;
            }
            
            $this->syncUser($user, $supabaseApiKey, $supabaseUrl);
        } else {
            // Sync all users without Supabase IDs
            $users = User::whereNull('supabase_id')->get();
            
            if ($users->isEmpty()) {
                $this->info('All users already have Supabase IDs.');
                return 0;
            }
            
            $this->info("Found {$users->count()} users without Supabase IDs.");
            
            $bar = $this->output->createProgressBar($users->count());
            $bar->start();
            
            foreach ($users as $user) {
                $this->syncUser($user, $supabaseApiKey, $supabaseUrl);
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
        }
        
        $this->info('Sync completed.');
        return 0;
    }
    
    private function syncUser($user, $supabaseApiKey, $supabaseUrl)
    {
        try {
            $syncService = new SupabaseSyncService();
            $result = $syncService->syncLaravelUserToSupabase($user);
            
            if ($result['success']) {
                $this->line(" ✓ {$user->email} -> {$result['supabase_id']} (Auth: OK, Public: OK)");
            } else {
                $this->line(" ✗ {$user->email} -> {$result['message']}");
            }
        } catch (\Exception $e) {
            $this->line(" ✗ {$user->email} -> Error: " . $e->getMessage());
            Log::error('Exception syncing user with Supabase', [
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }
    }
} 