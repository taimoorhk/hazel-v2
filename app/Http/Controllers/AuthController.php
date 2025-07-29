<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
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
} 