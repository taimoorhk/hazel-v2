<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncSpecificUser extends Command
{
    protected $signature = 'supabase:sync-user {email}';
    protected $description = 'Sync a specific user by email to Supabase';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Syncing user: $email");

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User not found: $email");
            return 1;
        }

        if (!$user->supabase_id) {
            $this->error("User has no Supabase ID: $email");
            return 1;
        }

        // Get user's role
        $role = $user->roles->first();
        $roleName = $role ? $role->name : 'Normal User';

        $this->info("User details:");
        $this->line("  - Name: {$user->name}");
        $this->line("  - Email: {$user->email}");
        $this->line("  - Supabase ID: {$user->supabase_id}");
        $this->line("  - Role: $roleName");
        $this->line("  - User Questions: " . ($user->user_questions ?: 'None'));
        $this->line("  - Min Endpointing Delay: {$user->min_endpointing_delay}s");
        $this->line("  - Max Endpointing Delay: {$user->max_endpointing_delay}s");
        $this->line("  - Min Speech Duration: {$user->min_speech_duration}s");
        $this->line("  - Min Silence Duration: {$user->min_silence_duration}s");
        $this->line("  - Prefix Padding Duration: {$user->prefix_padding_duration}s");
        $this->line("  - Max Buffered Speech: {$user->max_buffered_speech}s");
        $this->line("  - Activation Threshold: {$user->activation_threshold}");

        // Update Supabase user metadata
        $supabaseApiKey = config('services.supabase.service_role_key');
        $supabaseUrl = config('services.supabase.url');
        
        if (!$supabaseApiKey || !$supabaseUrl) {
            $this->error('Supabase configuration missing. Please add SUPABASE_SERVICE_ROLE_KEY to your .env file.');
            return 1;
        }

        try {
            $response = Http::withHeaders([
                'apikey' => $supabaseApiKey,
                'Authorization' => 'Bearer ' . $supabaseApiKey,
                'Content-Type' => 'application/json',
            ])->patch("$supabaseUrl/auth/v1/admin/users/{$user->supabase_id}", [
                'user_metadata' => [
                    'role' => $roleName,
                    'user_questions' => $user->user_questions,
                    'name' => $user->name,
                    'min_endpointing_delay' => $user->min_endpointing_delay,
                    'max_endpointing_delay' => $user->max_endpointing_delay,
                    'min_speech_duration' => $user->min_speech_duration,
                    'min_silence_duration' => $user->min_silence_duration,
                    'prefix_padding_duration' => $user->prefix_padding_duration,
                    'max_buffered_speech' => $user->max_buffered_speech,
                    'activation_threshold' => $user->activation_threshold,
                ],
            ]);

            if ($response->successful()) {
                $this->info("Successfully synced user to Supabase!");
                return 0;
            } else {
                $this->error("Failed to sync user to Supabase: " . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error syncing user: " . $e->getMessage());
            return 1;
        }
    }
} 