<?php

namespace App\Services;

use App\Models\ElderlyProfile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ElderlyProfileRealtimeService
{
    protected $supabaseUrl;
    protected $supabaseServiceKey;
    protected $supabaseAnonKey;

    public function __construct()
    {
        $this->supabaseUrl = config('services.supabase.url');
        $this->supabaseServiceKey = config('services.supabase.service_role_key');
        $this->supabaseAnonKey = config('services.supabase.anon_key');
    }

    /**
     * Start listening for real-time changes in elderly profiles
     */
    public function startListening()
    {
        if (!$this->supabaseAnonKey) {
            Log::error('Supabase anon key not configured for real-time listening');
            return false;
        }

        try {
            // Subscribe to real-time changes on the elderly_profiles table
            $response = Http::withHeaders([
                'apikey' => $this->supabaseAnonKey,
                'Authorization' => 'Bearer ' . $this->supabaseAnonKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->supabaseUrl}/rest/v1/rpc/subscribe", [
                'table' => 'elderly_profiles',
                'event' => 'INSERT,UPDATE,DELETE'
            ]);

            if ($response->successful()) {
                Log::info('Real-time listener started for elderly_profiles table');
                return true;
            } else {
                Log::error('Failed to start real-time listener', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Error starting real-time listener', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Handle real-time profile changes from Supabase
     */
    public function handleProfileChange(array $change)
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
                    $this->handleProfileInsert($record);
                    break;
                case 'UPDATE':
                    $this->handleProfileUpdate($record);
                    break;
                case 'DELETE':
                    $this->handleProfileDelete($record);
                    break;
                default:
                    Log::warning('Unknown real-time event', ['event' => $event]);
            }

        } catch (\Exception $e) {
            Log::error('Error handling real-time profile change', [
                'change' => $change,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle profile insert from Supabase
     */
    protected function handleProfileInsert(array $record)
    {
        $email = $record['email'] ?? null;
        if (!$email) {
            Log::warning('Profile insert missing email', $record);
            return;
        }

        // Check if profile already exists in Laravel
        $existingProfile = ElderlyProfile::where('email', $email)->first();
        if ($existingProfile) {
            Log::info('Profile already exists in Laravel, skipping insert', ['email' => $email]);
            return;
        }

        // Create new profile in Laravel
        try {
            ElderlyProfile::create([
                'name' => $record['name'] ?? null,
                'email' => $email,
                'phone' => $record['phone'] ?? null,
                'status' => $record['status'] ?? 'active',
                'associated_account_email' => $record['associated_account_email'] ?? null,
            ]);

            Log::info('Profile created from Supabase real-time sync', ['email' => $email]);
        } catch (\Exception $e) {
            Log::error('Failed to create profile from Supabase sync', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle profile update from Supabase
     */
    protected function handleProfileUpdate(array $record)
    {
        $email = $record['email'] ?? null;
        if (!$email) {
            Log::warning('Profile update missing email', $record);
            return;
        }

        // Find and update profile in Laravel
        $profile = ElderlyProfile::where('email', $email)->first();
        if (!$profile) {
            Log::warning('Profile not found for update', ['email' => $email]);
            return;
        }

        try {
            $profile->update([
                'name' => $record['name'] ?? $profile->name,
                'phone' => $record['phone'] ?? $profile->phone,
                'status' => $record['status'] ?? $profile->status,
                'associated_account_email' => $record['associated_account_email'] ?? $profile->associated_account_email,
            ]);

            Log::info('Profile updated from Supabase real-time sync', ['email' => $email]);
        } catch (\Exception $e) {
            Log::error('Failed to update profile from Supabase sync', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle profile delete from Supabase
     */
    protected function handleProfileDelete(array $record)
    {
        $email = $record['email'] ?? null;
        if (!$email) {
            Log::warning('Profile delete missing email', $record);
            return;
        }

        // Find and delete profile in Laravel
        $profile = ElderlyProfile::where('email', $email)->first();
        if (!$profile) {
            Log::warning('Profile not found for deletion', ['email' => $email]);
            return;
        }

        try {
            $profile->delete();
            Log::info('Profile deleted from Supabase real-time sync', ['email' => $email]);
        } catch (\Exception $e) {
            Log::error('Failed to delete profile from Supabase sync', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Sync all profiles from Supabase to Laravel
     */
    public function syncAllProfiles()
    {
        if (!$this->supabaseServiceKey) {
            Log::error('Supabase service key not configured for bulk sync');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'apikey' => $this->supabaseServiceKey,
                'Authorization' => 'Bearer ' . $this->supabaseServiceKey,
            ])->get("{$this->supabaseUrl}/rest/v1/elderly_profiles?select=*");

            if (!$response->successful()) {
                Log::error('Failed to fetch profiles from Supabase', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }

            $profiles = $response->json();
            $syncedCount = 0;

            foreach ($profiles as $profileData) {
                $email = $profileData['email'] ?? null;
                if (!$email) continue;

                $existingProfile = ElderlyProfile::where('email', $email)->first();
                
                if ($existingProfile) {
                    // Update existing profile
                    $existingProfile->update([
                        'name' => $profileData['name'] ?? $existingProfile->name,
                        'phone' => $profileData['phone'] ?? $existingProfile->phone,
                        'status' => $profileData['status'] ?? $existingProfile->status,
                        'associated_account_email' => $profileData['associated_account_email'] ?? $existingProfile->associated_account_email,
                    ]);
                } else {
                    // Create new profile
                    ElderlyProfile::create([
                        'name' => $profileData['name'] ?? null,
                        'email' => $email,
                        'phone' => $profileData['phone'] ?? null,
                        'status' => $profileData['status'] ?? 'active',
                        'associated_account_email' => $profileData['associated_account_email'] ?? null,
                    ]);
                }
                $syncedCount++;
            }

            Log::info("Successfully synced {$syncedCount} profiles from Supabase");
            return true;

        } catch (\Exception $e) {
            Log::error('Error during bulk profile sync', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Push profile changes to Supabase
     */
    public function pushToSupabase(ElderlyProfile $profile, string $action = 'update')
    {
        if (!$this->supabaseServiceKey) {
            Log::error('Supabase service key not configured for pushing changes');
            return false;
        }

        try {
            $profileData = [
                'name' => $profile->name,
                'email' => $profile->email,
                'phone' => $profile->phone,
                'status' => $profile->status,
                'associated_account_email' => $profile->associated_account_email,
                'updated_at' => now()->toISOString(),
            ];

            Log::info("Attempting to push profile {$action} to Supabase", [
                'email' => $profile->email,
                'data' => $profileData,
                'url' => "{$this->supabaseUrl}/rest/v1/elderly_profiles"
            ]);

            if ($action === 'delete') {
                $response = Http::withHeaders([
                    'apikey' => $this->supabaseServiceKey,
                    'Authorization' => 'Bearer ' . $this->supabaseServiceKey,
                ])->delete("{$this->supabaseUrl}/rest/v1/elderly_profiles?email=eq.{$profile->email}");
            } else {
                $response = Http::withHeaders([
                    'apikey' => $this->supabaseServiceKey,
                    'Authorization' => 'Bearer ' . $this->supabaseServiceKey,
                    'Content-Type' => 'application/json',
                ])->post("{$this->supabaseUrl}/rest/v1/elderly_profiles", $profileData);
            }

            Log::info("Supabase response received", [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            if ($response->successful()) {
                Log::info("Profile {$action}d in Supabase", ['email' => $profile->email]);
                return true;
            } else {
                Log::error("Failed to {$action} profile in Supabase", [
                    'email' => $profile->email,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error("Error pushing profile {$action} to Supabase", [
                'email' => $profile->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
