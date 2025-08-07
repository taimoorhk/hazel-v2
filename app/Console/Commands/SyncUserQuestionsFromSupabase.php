<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncUserQuestionsFromSupabase extends Command
{
    protected $signature = 'supabase:sync-user-questions {--email= : Sync specific user by email}';
    protected $description = 'Sync user_questions from Supabase Auth user_metadata to Laravel users';

    public function handle()
    {
        $supabaseUrl = config('services.supabase.url');
        $supabaseServiceKey = config('services.supabase.service_role_key');

        if (!$supabaseUrl || !$supabaseServiceKey) {
            $this->error('Supabase configuration is missing');
            return 1;
        }

        // Get all users from Supabase Auth
        $response = Http::withHeaders([
            'apikey' => $supabaseServiceKey,
            'Authorization' => 'Bearer ' . $supabaseServiceKey,
        ])->get("{$supabaseUrl}/auth/v1/admin/users");

        if (!$response->successful()) {
            $this->error('Failed to get users from Supabase Auth: ' . $response->status());
            return 1;
        }

        $supabaseUsers = $response->json()['users'] ?? $response->json();
        $emailFilter = $this->option('email');
        $synced = 0;
        $updated = 0;

        foreach ($supabaseUsers as $supabaseUser) {
            $email = $supabaseUser['email'] ?? null;
            $userMetadata = $supabaseUser['user_metadata'] ?? [];
            $userQuestions = $userMetadata['user_questions'] ?? null;

            if (!$email) continue;

            // Skip if filtering by email and this isn't the target
            if ($emailFilter && $email !== $emailFilter) continue;

            // Find Laravel user
            $laravelUser = User::where('email', $email)->first();
            if (!$laravelUser) {
                $this->warn("Laravel user not found for email: {$email}");
                continue;
            }

            // Check if sync is needed
            if ($userQuestions && $laravelUser->user_questions !== $userQuestions) {
                $laravelUser->update(['user_questions' => $userQuestions]);
                $this->info("Updated user_questions for {$email}: {$userQuestions}");
                $updated++;
            } elseif ($userQuestions) {
                $this->line("User {$email} already has matching user_questions");
            } else {
                $this->line("User {$email} has no user_questions in Supabase");
            }

            $synced++;
        }

        $this->info("Sync completed. Processed: {$synced}, Updated: {$updated}");
        return 0;
    }
} 