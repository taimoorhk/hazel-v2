<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AddSupabaseId extends Command
{
    protected $signature = 'supabase:add-id {email} {supabase_id}';
    protected $description = 'Add Supabase ID to an existing user';

    public function handle()
    {
        $email = $this->argument('email');
        $supabaseId = $this->argument('supabase_id');

        $this->info("Adding Supabase ID to user: $email");

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User not found: $email");
            return 1;
        }

        if ($user->supabase_id) {
            $this->warn("User already has Supabase ID: {$user->supabase_id}");
            if ($this->confirm('Do you want to update it?')) {
                $user->update(['supabase_id' => $supabaseId]);
                $this->info("Updated Supabase ID for user: $email");
            } else {
                $this->info("Skipped update.");
                return 0;
            }
        } else {
            $user->update(['supabase_id' => $supabaseId]);
            $this->info("Added Supabase ID to user: $email");
        }

        return 0;
    }
} 