<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListMissingSupabaseIds extends Command
{
    protected $signature = 'supabase:list-missing-ids';
    protected $description = 'List users that are missing Supabase IDs';

    public function handle()
    {
        $this->info('Checking for users missing Supabase IDs...');

        $usersWithoutSupabaseId = User::whereNull('supabase_id')->get();
        $usersWithSupabaseId = User::whereNotNull('supabase_id')->get();

        $this->info("Users with Supabase ID: " . $usersWithSupabaseId->count());
        $usersWithSupabaseId->each(function($user) {
            $this->line("  ✓ {$user->email} (ID: {$user->supabase_id})");
        });

        $this->info("\nUsers missing Supabase ID: " . $usersWithoutSupabaseId->count());
        if ($usersWithoutSupabaseId->count() > 0) {
            $usersWithoutSupabaseId->each(function($user) {
                $this->line("  ✗ {$user->email} (Laravel ID: {$user->id})");
            });

            $this->info("\nTo add Supabase IDs, use:");
            $this->info("php artisan supabase:add-id {email} {supabase_id}");
        } else {
            $this->info("  All users have Supabase IDs!");
        }

        return 0;
    }
} 