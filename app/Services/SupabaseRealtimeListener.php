<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupabaseRealtimeListener
{
    protected $supabaseUrl;
    protected $supabaseAnonKey;

    public function __construct()
    {
        $this->supabaseUrl = config('services.supabase.url');
        $this->supabaseAnonKey = config('services.supabase.anon_key');
    }

    /**
     * Listen for real-time changes in Supabase users table
     */
    public function listenForUserChanges()
    {
        if (!$this->supabaseAnonKey) {
            Log::error('Supabase anon key not configured for real-time listening');
            return;
        }

        try {
            // Subscribe to real-time changes on the users table
            $response = Http::withHeaders([
                'apikey' => $this->supabaseAnonKey,
                'Authorization' => 'Bearer ' . $this->supabaseAnonKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->supabaseUrl}/rest/v1/rpc/subscribe", [
                'table' => 'users',
                'event' => 'INSERT,UPDATE,DELETE'
            ]);

            if ($response->successful()) {
                Log::info('Real-time listener started for users table');
            } else {
                Log::error('Failed to start real-time listener', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error starting real-time listener', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle real-time user changes from Supabase
     */
    public function handleUserChange(array $change)
    {
        try {
            $event = $change['event'] ?? null;
            $record = $change['record'] ?? null;

            if (!$event || !$record) {
                Log::warning('Invalid real-time change data', $change);
                return;
            }

            switch ($event) {
                case 'INSERT':
                    $this->handleUserInsert($record);
                    break;
                case 'UPDATE':
                    $this->handleUserUpdate($record);
                    break;
                case 'DELETE':
                    $this->handleUserDelete($record);
                    break;
                default:
                    Log::warning('Unknown real-time event', ['event' => $event]);
            }

        } catch (\Exception $e) {
            Log::error('Error handling real-time user change', [
                'change' => $change,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle user insert from Supabase
     */
    protected function handleUserInsert(array $record)
    {
        $email = $record['email'] ?? null;
        $supabaseId = $record['supabase_id'] ?? null;

        if (!$email || !$supabaseId) {
            Log::warning('Invalid user insert data', $record);
            return;
        }

        // Check if user already exists in Laravel
        $user = User::where('email', $email)->orWhere('supabase_id', $supabaseId)->first();

        if ($user) {
            // Update existing user
            $user->update([
                'name' => $record['name'] ?? $user->name,
                'supabase_id' => $supabaseId,
                'user_questions' => $record['user_questions'] ?? $user->user_questions,
            ]);
            Log::info('Updated existing user from Supabase real-time', ['email' => $email]);
        } else {
            // Create new user
            $user = User::create([
                'name' => $record['name'] ?? 'User',
                'email' => $email,
                'password' => bcrypt(Str::random(32)),
                'supabase_id' => $supabaseId,
                'user_questions' => $record['user_questions'] ?? null,
            ]);

            // Assign default role
            $defaultRole = Role::where('name', 'Normal User')->first();
            if ($defaultRole) {
                $user->roles()->attach($defaultRole->id, ['account_id' => 1]);
            }

            Log::info('Created new user from Supabase real-time', ['email' => $email]);
        }
    }

    /**
     * Handle user update from Supabase
     */
    protected function handleUserUpdate(array $record)
    {
        $email = $record['email'] ?? null;
        $supabaseId = $record['supabase_id'] ?? null;

        if (!$email && !$supabaseId) {
            Log::warning('Invalid user update data', $record);
            return;
        }

        // Find user by email or supabase_id
        $user = null;
        if ($email) {
            $user = User::where('email', $email)->first();
        }
        if (!$user && $supabaseId) {
            $user = User::where('supabase_id', $supabaseId)->first();
        }

        if ($user) {
            $user->update([
                'name' => $record['name'] ?? $user->name,
                'user_questions' => $record['user_questions'] ?? $user->user_questions,
            ]);
            Log::info('Updated user from Supabase real-time', ['email' => $user->email]);
        } else {
            Log::warning('User not found for update from Supabase real-time', $record);
        }
    }

    /**
     * Handle user delete from Supabase
     */
    protected function handleUserDelete(array $record)
    {
        $email = $record['email'] ?? null;
        $supabaseId = $record['supabase_id'] ?? null;

        if (!$email && !$supabaseId) {
            Log::warning('Invalid user delete data', $record);
            return;
        }

        // Find user by email or supabase_id
        $user = null;
        if ($email) {
            $user = User::where('email', $email)->first();
        }
        if (!$user && $supabaseId) {
            $user = User::where('supabase_id', $supabaseId)->first();
        }

        if ($user) {
            $user->delete();
            Log::info('Deleted user from Supabase real-time', ['email' => $user->email]);
        } else {
            Log::warning('User not found for deletion from Supabase real-time', $record);
        }
    }
} 