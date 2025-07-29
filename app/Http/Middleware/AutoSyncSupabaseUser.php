<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AutoSyncSupabaseUser
{
    public function handle(Request $request, Closure $next)
    {
        // Only run this middleware for authenticated requests
        if ($request->user()) {
            return $next($request);
        }

        // Check if we have Supabase user data in the request
        $supabaseUser = $request->header('X-Supabase-User');
        $supabaseEmail = $request->header('X-Supabase-Email');

        if ($supabaseUser || $supabaseEmail) {
            try {
                $this->syncSupabaseUser($supabaseUser, $supabaseEmail);
            } catch (\Exception $e) {
                Log::error('Auto sync failed', [
                    'error' => $e->getMessage(),
                    'user' => $supabaseUser,
                    'email' => $supabaseEmail
                ]);
            }
        }

        return $next($request);
    }

    private function syncSupabaseUser($supabaseUser, $supabaseEmail)
    {
        if (!$supabaseEmail) {
            return;
        }

        // Check if user exists in Laravel database
        $user = User::where('email', $supabaseEmail)->first();

        if (!$user) {
            // Create new user
            $user = User::create([
                'name' => 'User', // Default name, can be updated later
                'email' => $supabaseEmail,
                'password' => bcrypt(Str::random(32)),
                'supabase_id' => $supabaseUser,
            ]);

            // Assign default role
            $defaultRole = Role::where('name', 'Normal User')->first();
            if ($defaultRole) {
                $user->roles()->attach($defaultRole->id, ['account_id' => 1]);
            }

            Log::info('Auto-created user from Supabase', [
                'email' => $supabaseEmail,
                'supabase_id' => $supabaseUser
            ]);
        } else {
            // Update existing user's Supabase ID if missing
            if (!$user->supabase_id && $supabaseUser) {
                $user->update(['supabase_id' => $supabaseUser]);
                Log::info('Updated user Supabase ID', [
                    'email' => $supabaseEmail,
                    'supabase_id' => $supabaseUser
                ]);
            }
        }
    }
} 