<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncAdminRolesFromSupabase extends Command
{
    protected $signature = 'supabase:sync-admin-roles {--email= : Sync specific user by email}';
    protected $description = 'Sync admin roles from Supabase Auth user_metadata to Laravel users';

    public function handle()
    {
        $supabaseUrl = config('services.supabase.url');
        $supabaseServiceKey = config('services.supabase.service_role_key');

        if (!$supabaseUrl || !$supabaseServiceKey) {
            $this->error('Supabase configuration is missing');
            return 1;
        }

        // Ensure Administration role exists
        $adminRole = Role::firstOrCreate(['name' => 'Administration']);
        $this->info("Administration role ensured (ID: {$adminRole->id})");

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
            $role = $userMetadata['role'] ?? null;

            if (!$email) continue;

            // Skip if filtering by email and this isn't the target
            if ($emailFilter && $email !== $emailFilter) continue;

            // Only process users with Administration role in Supabase
            if ($role !== 'Administration') {
                $this->line("User {$email} does not have Administration role in Supabase (has: {$role})");
                continue;
            }

            // Find Laravel user
            $laravelUser = User::where('email', $email)->first();
            if (!$laravelUser) {
                $this->warn("Laravel user not found for email: {$email}");
                continue;
            }

            // Check if user already has Admin or Administration role
            $hasAdminRole = $laravelUser->roles()->whereIn('name', ['Admin', 'Administration'])->exists();

            if (!$hasAdminRole) {
                // Remove all existing roles and add Administration role
                $laravelUser->roles()->detach();
                $laravelUser->roles()->attach($adminRole->id, ['account_id' => 1]);
                
                $this->info("Granted Administration role to {$email}");
                $updated++;
            } else {
                $currentRole = $laravelUser->roles->first()->name ?? 'None';
                $this->line("User {$email} already has admin role: {$currentRole}");
            }

            $synced++;
        }

        $this->info("Admin role sync completed. Processed: {$synced}, Updated: {$updated}");
        return 0;
    }
} 