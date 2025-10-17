<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Services\RealtimeSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncAllUsersFromSupabase extends Command
{
    protected $signature = 'supabase:sync-all-users-from-supabase {--force : Force sync even if user exists}';
    protected $description = 'Sync all users from Supabase Auth to Laravel database';

    public function handle()
    {
        $this->info('Starting sync of all users from Supabase to Laravel...');

        $supabaseUrl = config('services.supabase.url');
        $supabaseServiceKey = config('services.supabase.service_role_key');
        
        if (!$supabaseUrl || !$supabaseServiceKey) {
            $this->error('Supabase configuration not found. Please check your .env file.');
            return 1;
        }

        try {
            // Get all users from Supabase Auth
            $response = Http::withHeaders([
                'apikey' => $supabaseServiceKey,
                'Authorization' => 'Bearer ' . $supabaseServiceKey,
            ])->get("{$supabaseUrl}/auth/v1/admin/users");

            if (!$response->successful()) {
                $this->error('Failed to get users from Supabase Auth: ' . $response->status());
                return 1;
            }

            $responseData = $response->json();
            $supabaseUsers = $responseData['users'] ?? $responseData;

            $this->info("Found " . count($supabaseUsers) . " users in Supabase Auth");

            $created = 0;
            $updated = 0;
            $skipped = 0;
            $errors = 0;

            foreach ($supabaseUsers as $supabaseUser) {
                $email = $supabaseUser['email'] ?? null;
                $supabaseId = $supabaseUser['id'] ?? null;

                if (!$email || !$supabaseId) {
                    $this->warn("Skipping user with missing email or ID: " . json_encode($supabaseUser));
                    $skipped++;
                    continue;
                }

                try {
                    // Check if user exists in Laravel
                    $laravelUser = User::where('email', $email)->orWhere('supabase_id', $supabaseId)->first();

                    if ($laravelUser) {
                        if ($this->option('force') || !$laravelUser->supabase_id) {
                            // Update existing user
                            $laravelUser->update([
                                'name' => $supabaseUser['user_metadata']['name'] ?? 
                                         $supabaseUser['user_metadata']['display_name'] ?? 
                                         $laravelUser->name,
                                'supabase_id' => $supabaseId,
                                'user_questions' => $supabaseUser['user_metadata']['user_questions'] ?? $laravelUser->user_questions,
                                'min_endpointing_delay' => $supabaseUser['user_metadata']['min_endpointing_delay'] ?? $laravelUser->min_endpointing_delay ?? 0.5,
                                'max_endpointing_delay' => $supabaseUser['user_metadata']['max_endpointing_delay'] ?? $laravelUser->max_endpointing_delay ?? 6.0,
                                'min_speech_duration' => $supabaseUser['user_metadata']['min_speech_duration'] ?? $laravelUser->min_speech_duration ?? 0.05,
                                'min_silence_duration' => $supabaseUser['user_metadata']['min_silence_duration'] ?? $laravelUser->min_silence_duration ?? 0.55,
                                'prefix_padding_duration' => $supabaseUser['user_metadata']['prefix_padding_duration'] ?? $laravelUser->prefix_padding_duration ?? 0.5,
                                'max_buffered_speech' => $supabaseUser['user_metadata']['max_buffered_speech'] ?? $laravelUser->max_buffered_speech ?? 60,
                                'activation_threshold' => $supabaseUser['user_metadata']['activation_threshold'] ?? $laravelUser->activation_threshold ?? 0.5,
                            ]);

                            $this->line("Updated user: {$email}");
                            $updated++;
                        } else {
                            $this->line("Skipped existing user: {$email}");
                            $skipped++;
                        }
                    } else {
                        // Create new user
                        $user = User::create([
                            'name' => $supabaseUser['user_metadata']['name'] ?? 
                                     $supabaseUser['user_metadata']['display_name'] ?? 
                                     'User',
                            'email' => $email,
                            'password' => bcrypt(Str::random(32)),
                            'supabase_id' => $supabaseId,
                            'user_questions' => $supabaseUser['user_metadata']['user_questions'] ?? null,
                            'min_endpointing_delay' => $supabaseUser['user_metadata']['min_endpointing_delay'] ?? 0.5,
                            'max_endpointing_delay' => $supabaseUser['user_metadata']['max_endpointing_delay'] ?? 6.0,
                            'min_speech_duration' => $supabaseUser['user_metadata']['min_speech_duration'] ?? 0.05,
                            'min_silence_duration' => $supabaseUser['user_metadata']['min_silence_duration'] ?? 0.55,
                            'prefix_padding_duration' => $supabaseUser['user_metadata']['prefix_padding_duration'] ?? 0.5,
                            'max_buffered_speech' => $supabaseUser['user_metadata']['max_buffered_speech'] ?? 60,
                            'activation_threshold' => $supabaseUser['user_metadata']['activation_threshold'] ?? 0.5,
                        ]);

                        // Assign default role
                        $roleName = $supabaseUser['user_metadata']['role'] ?? 'Normal User';
                        $role = Role::where('name', $roleName)->first();
                        if ($role) {
                            $user->roles()->attach($role->id, ['account_id' => 1]);
                        }

                        $this->line("Created user: {$email}");
                        $created++;
                    }

                    // Sync to Supabase public.users table
                    $realtimeSync = new RealtimeSyncService();
                    $syncResult = $realtimeSync->syncUserToSupabase($laravelUser ?? $user, 'synced');

                    if (!$syncResult['success']) {
                        $this->warn("Warning: Failed to sync user {$email} to Supabase public.users: " . $syncResult['message']);
                    }

                } catch (\Exception $e) {
                    $this->error("Error processing user {$email}: " . $e->getMessage());
                    $errors++;
                    Log::error('Error syncing user from Supabase', [
                        'email' => $email,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $this->newLine();
            $this->info('Sync completed!');
            $this->table(
                ['Action', 'Count'],
                [
                    ['Created', $created],
                    ['Updated', $updated],
                    ['Skipped', $skipped],
                    ['Errors', $errors],
                ]
            );

            return 0;

        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            Log::error('Failed to sync users from Supabase', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
} 