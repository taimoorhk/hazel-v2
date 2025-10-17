<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncSupabaseUsers extends Command
{
    protected $signature = 'supabase:sync-users';
    protected $description = 'Sync all Supabase users to Laravel database';

    public function handle()
    {
        $this->info('Starting Supabase users sync...');

        $supabaseUrl = config('services.supabase.url');
        $supabaseKey = config('services.supabase.service_role_key');
        $supabaseAnonKey = config('services.supabase.anon_key');

        if (!$supabaseUrl) {
            $this->error('Supabase URL missing. Please check your .env file.');
            return 1;
        }

        // Use service role key if available, otherwise use anon key
        $keyToUse = $supabaseKey ?: $supabaseAnonKey;
        if (!$keyToUse) {
            $this->error('Supabase key missing. Please add SUPABASE_SERVICE_ROLE_KEY or SUPABASE_ANON_KEY to your .env file.');
            return 1;
        }

        try {
            // Fetch all users from Supabase
            $response = Http::withHeaders([
                'apikey' => $keyToUse,
                'Authorization' => 'Bearer ' . $keyToUse,
            ])->get("$supabaseUrl/auth/v1/admin/users");

            if (!$response->successful()) {
                $this->error('Failed to fetch users from Supabase: ' . $response->body());
                return 1;
            }

            $supabaseUsers = $response->json();
            $this->info("Found " . count($supabaseUsers) . " users in Supabase");

            $syncedCount = 0;
            $errorCount = 0;

            foreach ($supabaseUsers as $supabaseUser) {
                try {
                    $email = $supabaseUser['email'] ?? null;
                    $supabaseId = $supabaseUser['id'] ?? null;
                    $userMetadata = $supabaseUser['user_metadata'] ?? [];
                    
                    if (!$email || !$supabaseId) {
                        $this->warn("Skipping user with missing email or ID: " . json_encode($supabaseUser));
                        continue;
                    }

                    // Check if user already exists
                    $existingUser = User::where('email', $email)->orWhere('supabase_id', $supabaseId)->first();

                    if ($existingUser) {
                        // Update existing user
                        $existingUser->update([
                            'supabase_id' => $supabaseId,
                            'name' => $userMetadata['name'] ?? $userMetadata['display_name'] ?? $existingUser->name,
                            'min_endpointing_delay' => $userMetadata['min_endpointing_delay'] ?? $existingUser->min_endpointing_delay ?? 0.5,
                            'max_endpointing_delay' => $userMetadata['max_endpointing_delay'] ?? $existingUser->max_endpointing_delay ?? 6.0,
                            'min_speech_duration' => $userMetadata['min_speech_duration'] ?? $existingUser->min_speech_duration ?? 0.05,
                            'min_silence_duration' => $userMetadata['min_silence_duration'] ?? $existingUser->min_silence_duration ?? 0.55,
                            'prefix_padding_duration' => $userMetadata['prefix_padding_duration'] ?? $existingUser->prefix_padding_duration ?? 0.5,
                            'max_buffered_speech' => $userMetadata['max_buffered_speech'] ?? $existingUser->max_buffered_speech ?? 60,
                            'activation_threshold' => $userMetadata['activation_threshold'] ?? $existingUser->activation_threshold ?? 0.5,
                        ]);
                        $user = $existingUser;
                        $this->line("Updated user: $email");
                    } else {
                        // Create new user
                        $user = User::create([
                            'name' => $userMetadata['name'] ?? $userMetadata['display_name'] ?? 'User',
                            'email' => $email,
                            'password' => bcrypt(Str::random(32)),
                            'supabase_id' => $supabaseId,
                            'user_questions' => $userMetadata['user_questions'] ?? null,
                            'min_endpointing_delay' => $userMetadata['min_endpointing_delay'] ?? 0.5,
                            'max_endpointing_delay' => $userMetadata['max_endpointing_delay'] ?? 6.0,
                            'min_speech_duration' => $userMetadata['min_speech_duration'] ?? 0.05,
                            'min_silence_duration' => $userMetadata['min_silence_duration'] ?? 0.55,
                            'prefix_padding_duration' => $userMetadata['prefix_padding_duration'] ?? 0.5,
                            'max_buffered_speech' => $userMetadata['max_buffered_speech'] ?? 60,
                            'activation_threshold' => $userMetadata['activation_threshold'] ?? 0.5,
                        ]);
                        $this->line("Created user: $email");
                    }

                    // Handle role assignment
                    $roleName = $userMetadata['role'] ?? 'Normal User';
                    $role = Role::where('name', $roleName)->first();
                    
                    if ($role && !$user->roles->contains($role->id)) {
                        $user->roles()->attach($role->id, ['account_id' => 1]);
                        $this->line("Assigned role '$roleName' to user: $email");
                    }

                    $syncedCount++;
                } catch (\Exception $e) {
                    $this->error("Error syncing user {$email}: " . $e->getMessage());
                    $errorCount++;
                }
            }

            $this->info("Sync completed!");
            $this->info("Successfully synced: $syncedCount users");
            if ($errorCount > 0) {
                $this->warn("Errors encountered: $errorCount users");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            return 1;
        }
    }
} 