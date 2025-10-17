<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupabaseSyncService
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
     * Sync Laravel user to Supabase (both auth.users and public.users)
     */
    public function syncLaravelUserToSupabase(User $user): array
    {
        try {
            $result = [
                'success' => false,
                'auth_synced' => false,
                'public_synced' => false,
                'message' => '',
                'supabase_id' => null
            ];

            // Get user's role
            $role = $user->roles->first();
            $roleName = $role ? $role->name : 'Normal User';

            // Step 1: Create/Update user in Supabase Auth
            if (!$user->supabase_id) {
                // Create new user in Supabase Auth
                $authResult = $this->createSupabaseAuthUser($user, $roleName);
                if ($authResult['success']) {
                    $user->update(['supabase_id' => $authResult['supabase_id']]);
                    $result['auth_synced'] = true;
                    $result['supabase_id'] = $authResult['supabase_id'];
                } else {
                    $result['message'] = 'Failed to create Supabase Auth user: ' . $authResult['message'];
                    return $result;
                }
            } else {
                // Update existing user in Supabase Auth
                $authResult = $this->updateSupabaseAuthUser($user, $roleName);
                $result['auth_synced'] = $authResult['success'];
                $result['supabase_id'] = $user->supabase_id;
            }

            // Step 2: Sync to Supabase public.users table
            $publicResult = $this->syncToSupabasePublicUsers($user);
            $result['public_synced'] = $publicResult['success'];

            $result['success'] = $result['auth_synced'] && $result['public_synced'];
            
            if ($result['success']) {
                $result['message'] = 'User synced successfully to both Supabase Auth and public.users';
            } else {
                $result['message'] = 'Partial sync: Auth=' . ($result['auth_synced'] ? 'OK' : 'FAIL') . 
                                   ', Public=' . ($result['public_synced'] ? 'OK' : 'FAIL');
            }

            Log::info('Laravel to Supabase sync result', [
                'email' => $user->email,
                'result' => $result
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Error syncing Laravel user to Supabase', [
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'auth_synced' => false,
                'public_synced' => false,
                'message' => 'Exception: ' . $e->getMessage(),
                'supabase_id' => null
            ];
        }
    }

    /**
     * Sync Supabase user to Laravel
     */
    public function syncSupabaseUserToLaravel(array $supabaseUser): array
    {
        try {
            $email = $supabaseUser['email'] ?? null;
            $supabaseId = $supabaseUser['id'] ?? null;
            $userMetadata = $supabaseUser['user_metadata'] ?? [];

            if (!$email || !$supabaseId) {
                return [
                    'success' => false,
                    'message' => 'Missing email or ID in Supabase user data'
                ];
            }

            // Check if user exists in Laravel
            $user = User::where('email', $email)->orWhere('supabase_id', $supabaseId)->first();

            if ($user) {
                // Update existing user
                $user->update([
                    'name' => $userMetadata['name'] ?? $userMetadata['display_name'] ?? $user->name,
                    'supabase_id' => $supabaseId,
                    'user_questions' => $userMetadata['user_questions'] ?? $user->user_questions,
                    'min_endpointing_delay' => $userMetadata['min_endpointing_delay'] ?? $user->min_endpointing_delay ?? 0.5,
                    'max_endpointing_delay' => $userMetadata['max_endpointing_delay'] ?? $user->max_endpointing_delay ?? 6.0,
                    'min_speech_duration' => $userMetadata['min_speech_duration'] ?? $user->min_speech_duration ?? 0.05,
                    'min_silence_duration' => $userMetadata['min_silence_duration'] ?? $user->min_silence_duration ?? 0.55,
                    'prefix_padding_duration' => $userMetadata['prefix_padding_duration'] ?? $user->prefix_padding_duration ?? 0.5,
                    'max_buffered_speech' => $userMetadata['max_buffered_speech'] ?? $user->max_buffered_speech ?? 60,
                    'activation_threshold' => $userMetadata['activation_threshold'] ?? $user->activation_threshold ?? 0.5,
                ]);

                $action = 'updated';
            } else {
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

                $action = 'created';
            }

            // Handle role assignment
            $roleName = $userMetadata['role'] ?? 'Normal User';
            $role = Role::where('name', $roleName)->first();
            if ($role && !$user->roles->contains($role->id)) {
                $user->roles()->attach($role->id, ['account_id' => 1]);
            }

            Log::info("Supabase user {$action} in Laravel", [
                'email' => $email,
                'supabase_id' => $supabaseId,
                'action' => $action
            ]);

            return [
                'success' => true,
                'message' => "User {$action} successfully",
                'user_id' => $user->id,
                'action' => $action
            ];

        } catch (\Exception $e) {
            Log::error('Error syncing Supabase user to Laravel', [
                'supabase_user' => $supabaseUser,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sync all users from Laravel to Supabase
     */
    public function syncAllLaravelUsersToSupabase(): array
    {
        $users = User::all();
        $results = [
            'total' => $users->count(),
            'successful' => 0,
            'failed' => 0,
            'details' => []
        ];

        foreach ($users as $user) {
            $result = $this->syncLaravelUserToSupabase($user);
            $results['details'][] = [
                'email' => $user->email,
                'result' => $result
            ];

            if ($result['success']) {
                $results['successful']++;
            } else {
                $results['failed']++;
            }
        }

        Log::info('Bulk Laravel to Supabase sync completed', $results);
        return $results;
    }

    /**
     * Sync all users from Supabase to Laravel
     */
    public function syncAllSupabaseUsersToLaravel(): array
    {
        try {
            $supabaseUsers = $this->getAllSupabaseUsers();
            $results = [
                'total' => count($supabaseUsers),
                'successful' => 0,
                'failed' => 0,
                'details' => []
            ];

            foreach ($supabaseUsers as $supabaseUser) {
                $result = $this->syncSupabaseUserToLaravel($supabaseUser);
                $results['details'][] = [
                    'email' => $supabaseUser['email'] ?? 'unknown',
                    'result' => $result
                ];

                if ($result['success']) {
                    $results['successful']++;
                } else {
                    $results['failed']++;
                }
            }

            Log::info('Bulk Supabase to Laravel sync completed', $results);
            return $results;

        } catch (\Exception $e) {
            Log::error('Error in bulk Supabase to Laravel sync', [
                'error' => $e->getMessage()
            ]);

            return [
                'total' => 0,
                'successful' => 0,
                'failed' => 0,
                'details' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create user in Supabase Auth
     */
    protected function createSupabaseAuthUser(User $user, string $roleName): array
    {
        if (!$this->supabaseServiceKey) {
            return [
                'success' => false,
                'message' => 'Supabase service key not configured',
                'supabase_id' => null
            ];
        }

        $response = Http::withHeaders([
            'apikey' => $this->supabaseServiceKey,
            'Authorization' => 'Bearer ' . $this->supabaseServiceKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->supabaseUrl}/auth/v1/admin/users", [
            'email' => $user->email,
            'email_confirm' => true,
            'user_metadata' => [
                'name' => $user->name,
                'display_name' => $user->name,
                'role' => $roleName,
                'user_questions' => $user->user_questions,
                'min_endpointing_delay' => $user->min_endpointing_delay,
                'max_endpointing_delay' => $user->max_endpointing_delay,
                'min_speech_duration' => $user->min_speech_duration,
                'min_silence_duration' => $user->min_silence_duration,
                'prefix_padding_duration' => $user->prefix_padding_duration,
                'max_buffered_speech' => $user->max_buffered_speech,
                'activation_threshold' => $user->activation_threshold,
            ],
            'app_metadata' => [
                'role' => $roleName,
            ],
        ]);

        if ($response->successful()) {
            $supabaseUser = $response->json();
            return [
                'success' => true,
                'supabase_id' => $supabaseUser['id'],
                'message' => 'User created in Supabase Auth'
            ];
        } else {
            return [
                'success' => false,
                'supabase_id' => null,
                'message' => 'HTTP ' . $response->status() . ': ' . $response->body()
            ];
        }
    }

    /**
     * Update user in Supabase Auth
     */
    protected function updateSupabaseAuthUser(User $user, string $roleName): array
    {
        if (!$this->supabaseServiceKey) {
            return [
                'success' => false,
                'message' => 'Supabase service key not configured'
            ];
        }

        $response = Http::withHeaders([
            'apikey' => $this->supabaseServiceKey,
            'Authorization' => 'Bearer ' . $this->supabaseServiceKey,
            'Content-Type' => 'application/json',
        ])->put("{$this->supabaseUrl}/auth/v1/admin/users/{$user->supabase_id}", [
            'user_metadata' => [
                'name' => $user->name,
                'display_name' => $user->name,
                'role' => $roleName,
                'user_questions' => $user->user_questions,
                'min_endpointing_delay' => $user->min_endpointing_delay,
                'max_endpointing_delay' => $user->max_endpointing_delay,
                'min_speech_duration' => $user->min_speech_duration,
                'min_silence_duration' => $user->min_silence_duration,
                'prefix_padding_duration' => $user->prefix_padding_duration,
                'max_buffered_speech' => $user->max_buffered_speech,
                'activation_threshold' => $user->activation_threshold,
            ],
            'app_metadata' => [
                'role' => $roleName,
            ],
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'User updated in Supabase Auth'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'HTTP ' . $response->status() . ': ' . $response->body()
            ];
        }
    }

    /**
     * Sync user to Supabase public.users table
     */
    protected function syncToSupabasePublicUsers(User $user): array
    {
        if (!$this->supabaseServiceKey) {
            return [
                'success' => false,
                'message' => 'Supabase service key not configured'
            ];
        }

        // Check if user exists in public.users
        $checkResponse = Http::withHeaders([
            'apikey' => $this->supabaseServiceKey,
            'Authorization' => 'Bearer ' . $this->supabaseServiceKey,
        ])->get("{$this->supabaseUrl}/rest/v1/users?email=eq.{$user->email}&select=id");

        $userData = [
            'name' => $user->name,
            'email' => $user->email,
            'supabase_id' => $user->supabase_id,
            'user_questions' => $user->user_questions,
            'min_endpointing_delay' => $user->min_endpointing_delay,
            'max_endpointing_delay' => $user->max_endpointing_delay,
            'min_speech_duration' => $user->min_speech_duration,
            'min_silence_duration' => $user->min_silence_duration,
            'prefix_padding_duration' => $user->prefix_padding_duration,
            'max_buffered_speech' => $user->max_buffered_speech,
            'activation_threshold' => $user->activation_threshold,
            'current_account_id' => $user->current_account_id,
            'stripe_id' => $user->stripe_id,
            'pm_type' => $user->pm_type,
            'pm_last_four' => $user->pm_last_four,
            'trial_ends_at' => $user->trial_ends_at,
            'updated_at' => now()->toISOString(),
        ];

        if ($checkResponse->successful() && !empty($checkResponse->json())) {
            // Update existing user
            $response = Http::withHeaders([
                'apikey' => $this->supabaseServiceKey,
                'Authorization' => 'Bearer ' . $this->supabaseServiceKey,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=minimal'
            ])->patch("{$this->supabaseUrl}/rest/v1/users?email=eq.{$user->email}", $userData);
        } else {
            // Insert new user
            $userData['created_at'] = now()->toISOString();
            $response = Http::withHeaders([
                'apikey' => $this->supabaseServiceKey,
                'Authorization' => 'Bearer ' . $this->supabaseServiceKey,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=minimal'
            ])->post("{$this->supabaseUrl}/rest/v1/users", $userData);
        }

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'User synced to public.users table'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'HTTP ' . $response->status() . ': ' . $response->body()
            ];
        }
    }

    /**
     * Get all users from Supabase Auth
     */
    protected function getAllSupabaseUsers(): array
    {
        if (!$this->supabaseServiceKey) {
            Log::error('Supabase service key not configured');
            return [];
        }

        $response = Http::withHeaders([
            'apikey' => $this->supabaseServiceKey,
            'Authorization' => 'Bearer ' . $this->supabaseServiceKey,
        ])->get("{$this->supabaseUrl}/auth/v1/admin/users");

        if ($response->successful()) {
            return $response->json();
        } else {
            Log::error('Failed to fetch Supabase users', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return [];
        }
    }
} 