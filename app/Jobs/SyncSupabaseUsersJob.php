<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncSupabaseUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    public function handle()
    {
        Log::info('Starting Supabase users sync job');

        $supabaseUrl = config('services.supabase.url');
        $supabaseAnonKey = config('services.supabase.anon_key');

        if (!$supabaseUrl || !$supabaseAnonKey) {
            Log::error('Supabase configuration missing for sync job');
            return;
        }

        try {
            // Get existing users from Laravel database
            $existingUsers = User::all(['id', 'email', 'supabase_id'])->keyBy('email');
            
            // Try to get users from Supabase (this might not work without service role key)
            $response = Http::withHeaders([
                'apikey' => $supabaseAnonKey,
                'Authorization' => 'Bearer ' . $supabaseAnonKey,
            ])->get("$supabaseUrl/rest/v1/auth_users?select=*");

            if ($response->successful()) {
                $supabaseUsers = $response->json();
                Log::info("Found " . count($supabaseUsers) . " users in Supabase");
                
                foreach ($supabaseUsers as $supabaseUser) {
                    $this->processSupabaseUser($supabaseUser, $existingUsers);
                }
            } else {
                Log::warning("Could not fetch users from Supabase API: " . $response->body());
            }

            Log::info('Supabase users sync job completed');

        } catch (\Exception $e) {
            Log::error('Supabase users sync job failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function processSupabaseUser($supabaseUser, $existingUsers)
    {
        $email = $supabaseUser['email'] ?? null;
        $supabaseId = $supabaseUser['id'] ?? null;
        $userMetadata = $supabaseUser['user_metadata'] ?? [];

        if (!$email || !$supabaseId) {
            Log::warning('Skipping user with missing email or ID', $supabaseUser);
            return;
        }

        // Check if user exists in Laravel database
        $user = $existingUsers->get($email);

        if (!$user) {
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

            // Assign default role
            $roleName = $userMetadata['role'] ?? 'Normal User';
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->roles()->attach($role->id, ['account_id' => 1]);
            }

            Log::info('Created new user from Supabase', [
                'email' => $email,
                'supabase_id' => $supabaseId,
                'role' => $roleName
            ]);
        } else {
            // Update existing user if needed
            $updated = false;
            
            if (!$user->supabase_id) {
                $user->supabase_id = $supabaseId;
                $updated = true;
            }

            if ($updated) {
                $user->save();
                Log::info('Updated existing user from Supabase', [
                    'email' => $email,
                    'supabase_id' => $supabaseId
                ]);
            }
        }
    }
} 