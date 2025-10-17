<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use App\Models\User;
use App\Services\SupabaseSyncService;

class CustomSettingsController extends Controller
{
    protected $supabaseSyncService;

    public function __construct(SupabaseSyncService $supabaseSyncService)
    {
        $this->supabaseSyncService = $supabaseSyncService;
    }

    /**
     * Display the custom settings page
     */
    public function index(Request $request): Response|RedirectResponse|InertiaResponse
    {
        // Try to get user from Laravel session first
        $user = auth()->user();
        
        // If no user in session, try to get from Supabase headers
        if (!$user) {
            $supabaseEmail = $request->header('X-Supabase-Email');
            if ($supabaseEmail) {
                $user = User::where('email', $supabaseEmail)->first();
            }
        }
        
        // If still no user, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // Load user data with all audio processing fields
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'supabase_id' => $user->supabase_id,
            'min_endpointing_delay' => $user->min_endpointing_delay ?? 0.5,
            'max_endpointing_delay' => $user->max_endpointing_delay ?? 6.0,
            'min_speech_duration' => $user->min_speech_duration ?? 0.05,
            'min_silence_duration' => $user->min_silence_duration ?? 0.55,
            'prefix_padding_duration' => $user->prefix_padding_duration ?? 0.5,
            'max_buffered_speech' => $user->max_buffered_speech ?? 60,
            'activation_threshold' => $user->activation_threshold ?? 0.5,
        ];

        return Inertia::render('CustomSettings', [
            'user' => $userData,
        ]);
    }

    /**
     * Update user's custom settings
     */
    public function update(Request $request): Response|RedirectResponse
    {
        // Try to get user from Laravel session first
        $user = auth()->user();
        
        // If no user in session, try to get from Supabase headers
        if (!$user) {
            $supabaseEmail = $request->header('X-Supabase-Email');
            if ($supabaseEmail) {
                $user = User::where('email', $supabaseEmail)->first();
            }
        }
        
        // If still no user, return error
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate the request
        $validated = $request->validate([
            'min_endpointing_delay' => 'required|numeric|min:0.0|max:2.0',
            'max_endpointing_delay' => 'required|numeric|min:3.0|max:9.0',
            'min_speech_duration' => 'required|numeric|min:0.02|max:0.35',
            'min_silence_duration' => 'required|numeric|min:0.25|max:1.4',
            'prefix_padding_duration' => 'required|numeric|min:0.2|max:1.2',
            'max_buffered_speech' => 'required|integer|min:40|max:80',
            'activation_threshold' => 'required|numeric|min:0.1|max:0.9',
        ]);

        // Ensure min_endpointing_delay <= max_endpointing_delay
        if ($validated['min_endpointing_delay'] >= $validated['max_endpointing_delay']) {
            return response()->json([
                'error' => 'Minimum endpointing delay must be less than maximum endpointing delay'
            ], 422);
        }

        try {
            // Update user in Laravel database
            $user->update($validated);

            // Sync to Supabase
            $this->supabaseSyncService->syncToSupabasePublicUsers($user);

            return response()->json([
                'message' => 'Settings updated successfully',
                'user' => $user->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update settings: ' . $e->getMessage()
            ], 500);
        }
    }
}
