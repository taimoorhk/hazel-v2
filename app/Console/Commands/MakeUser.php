<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeUser extends Command
{
    protected $signature = 'make:user {email} {name} {role=Normal User} {supabase_id?}';
    protected $description = 'Create a new user in the Laravel database';

    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');
        $role = $this->argument('role');
        $supabaseId = $this->argument('supabase_id');

        $this->info("Creating user: $email");

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $this->warn("User already exists: $email");
            if ($this->confirm('Do you want to update the existing user?')) {
                $existingUser->update([
                    'name' => $name,
                    'supabase_id' => $supabaseId ?: $existingUser->supabase_id,
                ]);
                $this->info("Updated existing user: $email");
            } else {
                $this->info("Skipped user creation.");
                return 0;
            }
            $user = $existingUser;
        } else {
            // Create new user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt(Str::random(32)),
                'supabase_id' => $supabaseId,
            ]);
            $this->info("Created new user: $email");
        }

        // Assign role
        $roleModel = Role::where('name', $role)->first();
        if ($roleModel) {
            if (!$user->roles->contains($roleModel->id)) {
                $user->roles()->attach($roleModel->id, ['account_id' => 1]);
                $this->info("Assigned role '$role' to user: $email");
            } else {
                $this->info("User already has role '$role'");
            }
        } else {
            $this->warn("Role '$role' not found. Available roles:");
            Role::all()->each(function($r) {
                $this->line("  - {$r->name}");
            });
        }

        $this->info("User creation completed successfully!");
        return 0;
    }
} 