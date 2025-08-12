<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupabaseAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Get user email from Supabase header
        $supabaseEmail = $request->header('X-Supabase-Email');
        
        Log::info('SupabaseAuthMiddleware: Processing request', [
            'url' => $request->url(),
            'method' => $request->method(),
            'supabase_email' => $supabaseEmail
        ]);
        
        if (!$supabaseEmail) {
            Log::warning('SupabaseAuthMiddleware: Missing X-Supabase-Email header');
            return response()->json([
                'success' => false,
                'message' => 'X-Supabase-Email header is required'
            ], 401);
        }

        // Check if user exists in Laravel database
        $user = User::where('email', $supabaseEmail)->first();
        
        if (!$user) {
            Log::warning('SupabaseAuthMiddleware: User not found', ['email' => $supabaseEmail]);
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 401);
        }

        Log::info('SupabaseAuthMiddleware: User authenticated', [
            'user_id' => $user->id,
            'user_email' => $user->email
        ]);

        // Add user to request for use in controllers
        $request->merge(['auth_user' => $user]);
        
        return $next($request);
    }
}
