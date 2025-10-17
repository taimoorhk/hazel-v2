<?php

namespace App\Http\Controllers;

use App\Models\ElderlyProfile;
use App\Services\ElderlyProfileRealtimeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ElderlyProfileController extends Controller
{
    /**
     * Store a newly created elderly profile
     */
    public function store(Request $request)
    {
        // Get user from middleware
        $user = $request->get('auth_user');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:elderly_profiles,email',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,pending',
            'associated_account_email' => 'required|email|max:255',
            'preferred_voice' => 'nullable|string|in:sage,alloy,echo,fable,onyx,nova,shimmer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $profile = ElderlyProfile::create($request->all());

            // Sync to Supabase
            $realtimeService = new ElderlyProfileRealtimeService();
            $realtimeService->pushToSupabase($profile, 'insert');

            return response()->json([
                'success' => true,
                'message' => 'Profile created successfully',
                'profile' => $profile
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified elderly profile
     */
    public function update(Request $request, ElderlyProfile $elderlyProfile)
    {
        // Get user from middleware
        $user = $request->get('auth_user');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Check if user can edit this profile
        Log::info('ElderlyProfileController: Checking authorization', [
            'profile_associated_email' => $elderlyProfile->associated_account_email,
            'user_email' => $user->email,
            'match' => $elderlyProfile->associated_account_email === $user->email
        ]);
        
        if ($elderlyProfile->associated_account_email !== $user->email) {
            Log::warning('ElderlyProfileController: Authorization failed', [
                'profile_associated_email' => $elderlyProfile->associated_account_email,
                'user_email' => $user->email
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to edit this profile'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:elderly_profiles,email,' . $elderlyProfile->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,pending',
            'associated_account_email' => 'required|email|max:255',
            'preferred_voice' => 'nullable|string|in:sage,alloy,echo,fable,onyx,nova,shimmer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $elderlyProfile->update($request->all());

            // Sync to Supabase
            try {
                $realtimeService = new ElderlyProfileRealtimeService();
                $syncResult = $realtimeService->pushToSupabase($elderlyProfile, 'update');
                
                if (!$syncResult) {
                    Log::warning('Profile updated in Laravel but Supabase sync failed', [
                        'profile_id' => $elderlyProfile->id,
                        'email' => $elderlyProfile->email
                    ]);
                }
            } catch (\Exception $syncError) {
                Log::error('Supabase sync error during profile update', [
                    'profile_id' => $elderlyProfile->id,
                    'email' => $elderlyProfile->email,
                    'error' => $syncError->getMessage()
                ]);
                // Don't fail the entire request if Supabase sync fails
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'profile' => $elderlyProfile
            ]);

        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'profile_id' => $elderlyProfile->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Update the status of an elderly profile
     */
    public function updateStatus(Request $request, ElderlyProfile $elderlyProfile)
    {
        // Get user from middleware
        $user = $request->get('auth_user');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Check if user can update this profile
        if ($elderlyProfile->associated_account_email !== $user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to edit this profile'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive,pending',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $elderlyProfile->update(['status' => $request->status]);

            // Sync to Supabase
            try {
                $realtimeService = new ElderlyProfileRealtimeService();
                $syncResult = $realtimeService->pushToSupabase($elderlyProfile, 'update');
                
                if (!$syncResult) {
                    Log::warning('Profile status updated in Laravel but Supabase sync failed', [
                        'profile_id' => $elderlyProfile->id,
                        'email' => $elderlyProfile->email
                    ]);
                }
            } catch (\Exception $syncError) {
                Log::error('Supabase sync error during profile status update', [
                    'profile_id' => $elderlyProfile->id,
                    'email' => $elderlyProfile->email,
                    'error' => $syncError->getMessage()
                ]);
                // Don't fail the entire request if Supabase sync fails
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile status updated successfully',
                'profile' => $elderlyProfile
            ]);

        } catch (\Exception $e) {
            Log::error('Profile status update failed', [
                'profile_id' => $elderlyProfile->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile status: ' . $e->getMessage()
            ], 500);
        }
    }
}
