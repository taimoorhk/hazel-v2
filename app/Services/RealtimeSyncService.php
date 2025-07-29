<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Events\UserDeleted;

class RealtimeSyncService
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
     * Initialize real-time sync listeners
     */
    public function initializeRealtimeSync()
    {
        // Listen for Laravel user events
        Event::listen(UserCreated::class, [$this, 'onUserCreated']);
        Event::listen(UserUpdated::class, [$this, 'onUserUpdated']);
        Event::listen(UserDeleted::class, [$this, 'onUserDeleted']);

        Log::info('Realtime sync listeners initialized');
    }

    /**
     * Handle user creation event
     */
    public function onUserCreated(UserCreated $event)
    {
        $user = $event->user;
        Log::info('Realtime sync: User created', ['email' => $user->email]);
        
        $this->syncUserToSupabase($user, 'created');
    }

    /**
     * Handle user update event
     */
    public function onUserUpdated(UserUpdated $event)
    {
        $user = $event->user;
        Log::info('Realtime sync: User updated', ['email' => $user->email]);
        
        $this->syncUserToSupabase($user, 'updated');
    }

    /**
     * Handle user deletion event
     */
    public function onUserDeleted(UserDeleted $event)
    {
        $user = $event->user;
        Log::info('Realtime sync: User deleted', ['email' => $user->email]);
        
        $this->deleteUserFromSupabase($user);
    }

    /**
     * Real-time sync user to Supabase
     */
    public function syncUserToSupabase(User $user, string $action = 'sync'): array
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

            // Step 1: Sync to Supabase Auth
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
                $result['message'] = "User {$action} successfully in both Supabase Auth and public.users";
            } else {
                $result['message'] = "Partial sync: Auth=" . ($result['auth_synced'] ? 'OK' : 'FAIL') . 
                                   ", Public=" . ($result['public_synced'] ? 'OK' : 'FAIL');
            }

            Log::info("Realtime sync {$action} result", [
                'email' => $user->email,
                'action' => $action,
                'result' => $result
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error("Realtime sync {$action} failed", [
                'email' => $user->email,
                'action' => $action,
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
     * Delete user from Supabase
     */
    public function deleteUserFromSupabase(User $user): array
    {
        try {
            $result = [
                'success' => false,
                'auth_deleted' => false,
                'public_deleted' => false,
                'message' => ''
            ];

            // Step 1: Delete from Supabase Auth
            if ($user->supabase_id) {
                $authResult = $this->deleteSupabaseAuthUser($user->supabase_id);
                $result['auth_deleted'] = $authResult['success'];
            }

            // Step 2: Delete from Supabase public.users table
            $publicResult = $this->deleteFromSupabasePublicUsers($user);
            $result['public_deleted'] = $publicResult['success'];

            $result['success'] = $result['auth_deleted'] && $result['public_deleted'];
            
            if ($result['success']) {
                $result['message'] = 'User deleted successfully from both Supabase Auth and public.users';
            } else {
                $result['message'] = 'Partial deletion: Auth=' . ($result['auth_deleted'] ? 'OK' : 'FAIL') . 
                                   ', Public=' . ($result['public_deleted'] ? 'OK' : 'FAIL');
            }

            Log::info('Realtime sync delete result', [
                'email' => $user->email,
                'result' => $result
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Realtime sync delete failed', [
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'auth_deleted' => false,
                'public_deleted' => false,
                'message' => 'Exception: ' . $e->getMessage()
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

        // First, try to find existing user in Supabase Auth
        $existingUserResponse = Http::withHeaders([
            'apikey' => $this->supabaseServiceKey,
            'Authorization' => 'Bearer ' . $this->supabaseServiceKey,
        ])->get("{$this->supabaseUrl}/auth/v1/admin/users");

        if ($existingUserResponse->successful()) {
            $responseData = $existingUserResponse->json();
            $existingUsers = $responseData['users'] ?? $responseData;
            
            Log::info('Checking for existing Supabase user', [
                'email' => $user->email,
                'total_users' => count($existingUsers),
                'supabase_users' => array_map(function($u) {
                    return [
                        'id' => $u['id'] ?? 'N/A',
                        'email' => $u['email'] ?? 'N/A'
                    ];
                }, $existingUsers)
            ]);
            
            // Find user by email
            foreach ($existingUsers as $existingUser) {
                if (($existingUser['email'] ?? '') === $user->email) {
                    return [
                        'success' => true,
                        'supabase_id' => $existingUser['id'],
                        'message' => 'User already exists in Supabase Auth, linked existing account'
                    ];
                }
            }
        }

        // Create new user in Supabase Auth
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
     * Delete user from Supabase Auth
     */
    protected function deleteSupabaseAuthUser(string $supabaseId): array
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
        ])->delete("{$this->supabaseUrl}/auth/v1/admin/users/{$supabaseId}");

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'User deleted from Supabase Auth'
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
     * Delete user from Supabase public.users table
     */
    protected function deleteFromSupabasePublicUsers(User $user): array
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
            'Prefer' => 'return=minimal'
        ])->delete("{$this->supabaseUrl}/rest/v1/users?email=eq.{$user->email}");

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'User deleted from public.users table'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'HTTP ' . $response->status() . ': ' . $response->body()
            ];
        }
    }
} 