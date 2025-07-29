<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Services\SupabaseSyncService;
use App\Services\RealtimeSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function syncSupabaseUser(Request $request)
    {
        try {
            $supabaseId = $request->input('id');
            $email = $request->input('email');
            $name = $request->input('name', '');
            $role = $request->input('role', 'Normal User'); // default
            $userQuestions = $request->input('user_questions', null);

            \Log::info('syncSupabaseUser called', [
                'supabase_id' => $supabaseId,
                'email' => $email,
                'name' => $name,
                'role' => $role
            ]);

            if (!$email || !$supabaseId) {
                \Log::error('syncSupabaseUser failed: Missing email or supabase_id', [
                    'email' => $email,
                    'supabase_id' => $supabaseId
                ]);
                return response()->json(['error' => 'Email and Supabase ID are required'], 400);
            }

            // Check if user exists by email or supabase_id
            $user = User::where('email', $email)->orWhere('supabase_id', $supabaseId)->first();

            if ($user) {
                // Update existing user
                $user->update([
                    'name' => $name ?: $user->name,
                    'supabase_id' => $supabaseId,
                    'user_questions' => $userQuestions ?: $user->user_questions,
                ]);
                \Log::info('Updated existing user', ['email' => $email, 'user_id' => $user->id]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $name ?: 'User',
                    'email' => $email,
                    'password' => bcrypt(Str::random(32)),
                    'supabase_id' => $supabaseId,
                    'user_questions' => $userQuestions,
                ]);
                \Log::info('Created new user', ['email' => $email, 'user_id' => $user->id]);
            }

            // Handle role assignment
            $roleModel = Role::where('name', $role)->first();
            if ($roleModel && !$user->roles->contains($roleModel->id)) {
                $user->roles()->attach($roleModel->id, ['account_id' => 1]);
                \Log::info('Assigned role to user', ['email' => $email, 'role' => $role]);
            }

            // Always fetch the latest role from user_roles
            $latestRole = $user->roles()->latest('user_roles.created_at')->first();
            $latestRoleName = $latestRole ? $latestRole->name : $role;

            // Update Supabase user metadata using Supabase Admin API
            $supabaseApiKey = config('services.supabase.service_role_key');
            $supabaseUrl = config('services.supabase.url');
            if ($supabaseApiKey && $supabaseUrl && $user->supabase_id) {
                $response = Http::withHeaders([
                    'apikey' => $supabaseApiKey,
                    'Authorization' => 'Bearer ' . $supabaseApiKey,
                    'Content-Type' => 'application/json',
                ])->patch("$supabaseUrl/auth/v1/admin/users/{$user->supabase_id}", [
                    'user_metadata' => [
                        'role' => $latestRoleName,
                        'user_questions' => $user->user_questions,
                    ],
                ]);

                if ($response->successful()) {
                    \Log::info('Updated Supabase user metadata', ['email' => $email]);
                } else {
                    \Log::warning('Failed to update Supabase user metadata', [
                        'email' => $email,
                        'response' => $response->body()
                    ]);
                }
            }

            \Log::info('syncSupabaseUser completed successfully', ['email' => $email]);
            return response()->json(['success' => true, 'user_id' => $user->id]);

        } catch (\Exception $e) {
            \Log::error('syncSupabaseUser failed with exception', [
                'email' => $email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function manualSyncUser(Request $request)
    {
        try {
            $email = $request->input('email');
            
            if (!$email) {
                return response()->json(['error' => 'Email is required'], 400);
            }

            \Log::info('manualSyncUser called', ['email' => $email]);

            // Find user by email
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                \Log::warning('User not found for manual sync', ['email' => $email]);
                return response()->json(['error' => 'User not found'], 404);
            }

            // If user has no supabase_id, we can't sync
            if (!$user->supabase_id) {
                \Log::warning('User has no supabase_id', ['email' => $email]);
                return response()->json(['error' => 'User has no Supabase ID'], 400);
            }

            // Get user's role
            $role = $user->roles->first();
            $roleName = $role ? $role->name : 'Normal User';

            // Update Supabase user metadata
            $supabaseApiKey = config('services.supabase.service_role_key');
            $supabaseUrl = config('services.supabase.url');
            
            if ($supabaseApiKey && $supabaseUrl) {
                $response = Http::withHeaders([
                    'apikey' => $supabaseApiKey,
                    'Authorization' => 'Bearer ' . $supabaseApiKey,
                    'Content-Type' => 'application/json',
                ])->patch("$supabaseUrl/auth/v1/admin/users/{$user->supabase_id}", [
                    'user_metadata' => [
                        'role' => $roleName,
                        'user_questions' => $user->user_questions,
                        'name' => $user->name,
                    ],
                ]);

                if ($response->successful()) {
                    \Log::info('Manual sync successful', ['email' => $email]);
                    return response()->json(['success' => true, 'message' => 'User synced successfully']);
                } else {
                    \Log::warning('Manual sync failed - Supabase update failed', [
                        'email' => $email,
                        'response' => $response->body()
                    ]);
                    return response()->json(['error' => 'Failed to update Supabase'], 500);
                }
            } else {
                \Log::warning('Manual sync failed - Missing Supabase configuration');
                return response()->json(['error' => 'Supabase configuration missing'], 500);
            }

        } catch (\Exception $e) {
            \Log::error('manualSyncUser failed with exception', [
                'email' => $email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function createUserFromSupabase(Request $request)
    {
        try {
            $email = $request->input('email');
            $supabaseId = $request->input('supabase_id');
            $name = $request->input('name', 'User');
            $role = $request->input('role', 'Normal User');
            $userQuestions = $request->input('user_questions', null);

            \Log::info('createUserFromSupabase called', [
                'email' => $email,
                'supabase_id' => $supabaseId,
                'name' => $name,
                'role' => $role
            ]);

            if (!$email) {
                return response()->json(['error' => 'Email is required'], 400);
            }

            // Check if user already exists
            $existingUser = User::where('email', $email)->first();
            
            if ($existingUser) {
                // Update existing user with Supabase ID if provided
                $updateData = [
                    'name' => $name,
                    'user_questions' => $userQuestions ?: $existingUser->user_questions,
                ];
                
                // Only update supabase_id if it's provided and different
                if ($supabaseId && $existingUser->supabase_id !== $supabaseId) {
                    $updateData['supabase_id'] = $supabaseId;
                    \Log::info('Updating user with new Supabase ID', ['email' => $email, 'old_id' => $existingUser->supabase_id, 'new_id' => $supabaseId]);
                }
                
                $existingUser->update($updateData);
                $user = $existingUser;
                \Log::info('Updated existing user', ['email' => $email]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt(Str::random(32)),
                    'supabase_id' => $supabaseId,
                    'user_questions' => $userQuestions,
                ]);
                \Log::info('Created new user', ['email' => $email]);
            }

            // Handle role assignment
            $roleModel = Role::where('name', $role)->first();
            if ($roleModel && !$user->roles->contains($roleModel->id)) {
                $user->roles()->attach($roleModel->id, ['account_id' => 1]);
                \Log::info('Assigned role to user', ['email' => $email, 'role' => $role]);
            }

            // Real-time sync to Supabase
            $realtimeSync = new RealtimeSyncService();
            $syncResult = $realtimeSync->syncUserToSupabase($user, 'created');
            \Log::info('User real-time synced to Supabase after creation', [
                'email' => $email,
                'sync_result' => $syncResult
            ]);

            \Log::info('createUserFromSupabase completed successfully', ['email' => $email]);
            return response()->json([
                'success' => true, 
                'user_id' => $user->id,
                'message' => $existingUser ? 'User updated' : 'User created'
            ]);

        } catch (\Exception $e) {
            \Log::error('createUserFromSupabase failed with exception', [
                'email' => $email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function verifyUserExists(Request $request)
    {
        try {
            $email = $request->input('email');
            
            if (!$email) {
                return response()->json(['error' => 'Email is required'], 400);
            }

            \Log::info('verifyUserExists called', ['email' => $email]);

            // Check if user exists in Laravel database
            $laravelUser = User::where('email', $email)->first();
            
            if (!$laravelUser) {
                \Log::info('User not found in Laravel database', ['email' => $email]);
                return response()->json([
                    'exists' => false,
                    'message' => 'User not found. Please register first.'
                ]);
            }

            // If user exists in Laravel but has no Supabase ID, allow them to proceed
            // The Supabase user will be created during the magic link process
            if (!$laravelUser->supabase_id) {
                \Log::info('User exists in Laravel but has no Supabase ID - allowing authentication', ['email' => $email]);
                return response()->json([
                    'exists' => true,
                    'message' => 'User verified successfully',
                    'needs_supabase_sync' => true,
                    'user' => [
                        'id' => $laravelUser->id,
                        'name' => $laravelUser->name,
                        'email' => $laravelUser->email,
                        'supabase_id' => null,
                        'role' => $laravelUser->roles->first()?->name ?? 'Normal User'
                    ]
                ]);
            }

            // Verify user exists in Supabase Auth
            $supabaseApiKey = config('services.supabase.service_role_key');
            $supabaseUrl = config('services.supabase.url');
            
            if ($supabaseApiKey && $supabaseUrl) {
                $response = Http::withHeaders([
                    'apikey' => $supabaseApiKey,
                    'Authorization' => 'Bearer ' . $supabaseApiKey,
                    'Content-Type' => 'application/json',
                ])->get("$supabaseUrl/auth/v1/admin/users/{$laravelUser->supabase_id}");

                if ($response->successful()) {
                    $supabaseUser = $response->json();
                    \Log::info('User verified in both systems', ['email' => $email]);
                    return response()->json([
                        'exists' => true,
                        'message' => 'User verified successfully',
                        'user' => [
                            'id' => $laravelUser->id,
                            'name' => $laravelUser->name,
                            'email' => $laravelUser->email,
                            'supabase_id' => $laravelUser->supabase_id,
                            'role' => $laravelUser->roles->first()?->name ?? 'Normal User'
                        ]
                    ]);
                } else {
                    \Log::warning('User not found in Supabase Auth', ['email' => $email, 'supabase_id' => $laravelUser->supabase_id]);
                    return response()->json([
                        'exists' => false,
                        'message' => 'User account not found in authentication system. Please contact support.'
                    ]);
                }
            } else {
                // Fallback: If we have a Supabase ID, assume the user exists in Supabase
                // This is a reasonable assumption since Supabase IDs are only assigned to valid users
                if ($laravelUser->supabase_id) {
                    \Log::info('User verified (fallback mode)', ['email' => $email]);
                    return response()->json([
                        'exists' => true,
                        'message' => 'User verified successfully',
                        'user' => [
                            'id' => $laravelUser->id,
                            'name' => $laravelUser->name,
                            'email' => $laravelUser->email,
                            'supabase_id' => $laravelUser->supabase_id,
                            'role' => $laravelUser->roles->first()?->name ?? 'Normal User'
                        ]
                    ]);
                } else {
                    \Log::warning('Supabase configuration missing and no Supabase ID', ['email' => $email]);
                    return response()->json([
                        'exists' => false,
                        'message' => 'User account not properly configured. Please contact support.'
                    ]);
                }
            }

        } catch (\Exception $e) {
            \Log::error('verifyUserExists failed with exception', [
                'email' => $email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function checkUserQuestions(Request $request)
    {
        $supabaseId = $request->input('supabase_id');
        $email = $request->input('email');
        
        \Log::info('checkUserQuestions called', ['email' => $email, 'supabase_id' => $supabaseId]);
        
        if (!$supabaseId && !$email) {
            return response()->json(['error' => 'Supabase ID or email required'], 400);
        }

        $user = null;
        if ($supabaseId) {
            $user = User::where('supabase_id', $supabaseId)->first();
        }
        
        if (!$user && $email) {
            $user = User::where('email', $email)->first();
        }
        
        if (!$user) {
            \Log::warning('User not found', ['email' => $email, 'supabase_id' => $supabaseId]);
            return response()->json(['error' => 'User not found'], 404);
        }

        $result = [
            'has_questions' => !empty($user->user_questions),
            'user_questions' => $user->user_questions,
            'role' => $user->roles->first()?->name ?? 'Normal User',
            'supabase_id' => $user->supabase_id // Include this for debugging
        ];
        
        \Log::info('checkUserQuestions result', $result);
        
        return response()->json($result);
    }

    /**
     * Sync Laravel user to Supabase (both auth.users and public.users)
     */
    public function syncUserToSupabase(Request $request)
    {
        try {
            $email = $request->input('email');
            
            if (!$email) {
                return response()->json(['error' => 'Email is required'], 400);
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $syncService = new SupabaseSyncService();
            $result = $syncService->syncLaravelUserToSupabase($user);

            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('syncUserToSupabase failed', [
                'email' => $email ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Sync all Laravel users to Supabase
     */
    public function syncAllUsersToSupabase(Request $request)
    {
        try {
            $syncService = new SupabaseSyncService();
            $result = $syncService->syncAllLaravelUsersToSupabase();

            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('syncAllUsersToSupabase failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Sync all Supabase users to Laravel
     */
    public function syncAllUsersFromSupabase(Request $request)
    {
        try {
            $syncService = new SupabaseSyncService();
            $result = $syncService->syncAllSupabaseUsersToLaravel();

            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('syncAllUsersFromSupabase failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get sync status for all users
     */
    public function getSyncStatus(Request $request)
    {
        try {
            $laravelUsers = User::all(['id', 'name', 'email', 'supabase_id']);
            $usersWithSupabaseId = $laravelUsers->whereNotNull('supabase_id')->count();
            $usersWithoutSupabaseId = $laravelUsers->whereNull('supabase_id')->count();

            return response()->json([
                'total_laravel_users' => $laravelUsers->count(),
                'users_with_supabase_id' => $usersWithSupabaseId,
                'users_without_supabase_id' => $usersWithoutSupabaseId,
                'sync_percentage' => $laravelUsers->count() > 0 ? 
                    round(($usersWithSupabaseId / $laravelUsers->count()) * 100, 2) : 0,
                'users_needing_sync' => $laravelUsers->whereNull('supabase_id')
                    ->map(function($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email
                        ];
                    })->values()
            ]);

        } catch (\Exception $e) {
            \Log::error('getSyncStatus failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Real-time sync specific user
     */
    public function realtimeSyncUser(Request $request)
    {
        try {
            $email = $request->input('email');
            
            if (!$email) {
                return response()->json(['error' => 'Email is required'], 400);
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $realtimeSync = new RealtimeSyncService();
            $result = $realtimeSync->syncUserToSupabase($user, 'api');

            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('realtimeSyncUser failed', [
                'email' => $email ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Real-time sync all users
     */
    public function realtimeSyncAll(Request $request)
    {
        try {
            $users = User::all();
            $realtimeSync = new RealtimeSyncService();
            
            $results = [
                'total' => $users->count(),
                'successful' => 0,
                'failed' => 0,
                'details' => []
            ];

            foreach ($users as $user) {
                $result = $realtimeSync->syncUserToSupabase($user, 'bulk');
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

            return response()->json($results);

        } catch (\Exception $e) {
            \Log::error('realtimeSyncAll failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Create a test user for real-time sync testing
     */
    public function createTestUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'role' => 'required|string'
        ]);

        try {
            // Create user in Laravel (this will trigger real-time sync)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt('password'), // Temporary password
            ]);

            // Assign role (skip for now due to account_id constraint)
            // $role = Role::where('name', $request->role)->first();
            // if ($role) {
            //     $user->roles()->attach($role->id);
            // }

            \Log::info('Test user created for real-time sync testing', [
                'email' => $request->email,
                'name' => $request->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test user created successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create test user', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create test user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user exists in Supabase Auth
     */
    public function checkSupabaseUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $supabaseUrl = config('services.supabase.url');
        $supabaseServiceKey = config('services.supabase.service_role_key');

        if (!$supabaseServiceKey) {
            return response()->json([
                'exists' => false,
                'message' => 'Supabase service key not configured'
            ]);
        }

        try {
            $response = Http::withHeaders([
                'apikey' => $supabaseServiceKey,
                'Authorization' => 'Bearer ' . $supabaseServiceKey,
            ])->get("{$supabaseUrl}/auth/v1/admin/users");

            if ($response->successful()) {
                $responseData = $response->json();
                $users = $responseData['users'] ?? $responseData; // Handle both formats
                $user = collect($users)->firstWhere('email', $request->email);
                
                return response()->json([
                    'exists' => !is_null($user),
                    'supabase_id' => $user['id'] ?? null,
                    'user' => $user
                ]);
            }

            return response()->json([
                'exists' => false,
                'message' => 'Failed to check Supabase user'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exists' => false,
                'message' => 'Error checking Supabase user: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check if user exists in public.users table
     */
    public function checkPublicUsers(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $supabaseUrl = config('services.supabase.url');
        $supabaseServiceKey = config('services.supabase.service_role_key');

        if (!$supabaseServiceKey) {
            return response()->json([
                'exists' => false,
                'message' => 'Supabase service key not configured'
            ]);
        }

        try {
            $response = Http::withHeaders([
                'apikey' => $supabaseServiceKey,
                'Authorization' => 'Bearer ' . $supabaseServiceKey,
            ])->get("{$supabaseUrl}/rest/v1/users", [
                'select' => '*',
                'email' => 'eq.' . $request->email
            ]);

            if ($response->successful()) {
                $users = $response->json();
                $user = !empty($users) ? $users[0] : null;
                
                return response()->json([
                    'exists' => !is_null($user),
                    'name' => $user['name'] ?? null,
                    'email' => $user['email'] ?? null,
                    'supabase_id' => $user['supabase_id'] ?? null,
                    'user' => $user
                ]);
            }

            return response()->json([
                'exists' => false,
                'message' => 'Failed to check public.users table'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exists' => false,
                'message' => 'Error checking public.users table: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Sync user from Supabase to Laravel (for users that exist in Supabase but not in Laravel)
     */
    public function syncFromSupabaseToLaravel(Request $request)
    {
        try {
            $email = $request->input('email');
            
            if (!$email) {
                return response()->json(['error' => 'Email is required'], 400);
            }

            \Log::info('syncFromSupabaseToLaravel called', ['email' => $email]);

            // First check if user exists in Laravel
            $laravelUser = User::where('email', $email)->first();
            if ($laravelUser) {
                return response()->json([
                    'success' => true,
                    'message' => 'User already exists in Laravel',
                    'user_id' => $laravelUser->id
                ]);
            }

            // Get user from Supabase Auth
            $supabaseUrl = config('services.supabase.url');
            $supabaseServiceKey = config('services.supabase.service_role_key');
            
            if (!$supabaseUrl || !$supabaseServiceKey) {
                return response()->json(['error' => 'Supabase not configured'], 500);
            }

            // Get user from Supabase Auth
            $response = Http::withHeaders([
                'apikey' => $supabaseServiceKey,
                'Authorization' => 'Bearer ' . $supabaseServiceKey,
            ])->get("{$supabaseUrl}/auth/v1/admin/users");

            if (!$response->successful()) {
                return response()->json(['error' => 'Failed to get user from Supabase Auth'], 500);
            }

            $responseData = $response->json();
            $supabaseUsers = $responseData['users'] ?? $responseData; // Handle both formats
            
            // Find user by email
            $supabaseUser = collect($supabaseUsers)->firstWhere('email', $email);
            if (!$supabaseUser) {
                return response()->json(['error' => 'User not found in Supabase Auth'], 404);
            }
            
            // Create user in Laravel
            $user = User::create([
                'name' => $supabaseUser['user_metadata']['name'] ?? 
                         $supabaseUser['user_metadata']['display_name'] ?? 
                         'User',
                'email' => $email,
                'password' => bcrypt(Str::random(32)),
                'supabase_id' => $supabaseUser['id'],
                'user_questions' => $supabaseUser['user_metadata']['user_questions'] ?? null,
            ]);

            // Assign default role
            $roleName = $supabaseUser['user_metadata']['role'] ?? 'Normal User';
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->roles()->attach($role->id, ['account_id' => 1]);
            }

            // Sync to Supabase public.users table
            $realtimeSync = new RealtimeSyncService();
            $syncResult = $realtimeSync->syncUserToSupabase($user, 'created');

            \Log::info('User synced from Supabase to Laravel', [
                'email' => $email,
                'user_id' => $user->id,
                'sync_result' => $syncResult
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User synced from Supabase to Laravel',
                'user_id' => $user->id,
                'sync_result' => $syncResult
            ]);

        } catch (\Exception $e) {
            \Log::error('syncFromSupabaseToLaravel failed', [
                'email' => $email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
} 