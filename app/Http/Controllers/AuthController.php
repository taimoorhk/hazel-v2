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
        $supabaseId = $request->input('id');
        $email = $request->input('email');
        $name = $request->input('name', '');
        $role = $request->input('role', 'Normal User'); // default
        $userQuestions = $request->input('user_questions', null);

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt(Str::random(32)),
                'supabase_id' => $supabaseId,
                'user_questions' => $userQuestions,
            ]
        );

        // If user already exists but supabase_id is not set, update it
        if (!$user->supabase_id) {
            $user->supabase_id = $supabaseId;
            $user->save();
        }

        // Attach role if not already attached
        $roleModel = Role::where('name', $role)->first();
        if ($roleModel && !$user->roles->contains($roleModel->id)) {
            $user->roles()->attach($roleModel->id, ['account_id' => 1]); // Adjust account_id as needed
        }

        // Always fetch the latest role from user_roles
        $latestRole = $user->roles()->latest('user_roles.created_at')->first();
        $latestRoleName = $latestRole ? $latestRole->name : $role;

        // Update Supabase user metadata using Supabase Admin API
        $supabaseApiKey = config('services.supabase.service_role_key');
        $supabaseUrl = config('services.supabase.url');
        if ($supabaseApiKey && $supabaseUrl && $user->supabase_id) {
            Http::withHeaders([
                'apikey' => $supabaseApiKey,
                'Authorization' => 'Bearer ' . $supabaseApiKey,
                'Content-Type' => 'application/json',
            ])->patch("$supabaseUrl/auth/v1/admin/users/{$user->supabase_id}", [
                'user_metadata' => [
                    'role' => $latestRoleName,
                    'user_questions' => $user->user_questions,
                ],
            ]);
        }

        return response()->json(['success' => true]);
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
            'role' => $user->roles->first()?->name ?? 'Normal User'
        ];
        
        \Log::info('checkUserQuestions result', $result);
        
        return response()->json($result);
    }
} 