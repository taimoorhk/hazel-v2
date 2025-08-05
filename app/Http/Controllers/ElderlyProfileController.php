<?php

namespace App\Http\Controllers;

use App\Models\ElderlyProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;

class ElderlyProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $userEmail = auth()->user()->email ?? request()->user()->email;
        
        $elderlyProfiles = ElderlyProfile::where('associated_account_email', $userEmail)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $elderlyProfiles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:elderly_profiles,email',
                'phone' => 'nullable|string|max:20',
            ]);

            // Always set temporary_role to 'elderly user'
            $validated['temporary_role'] = 'elderly user';
            
            // Set the associated_account_email from the authenticated user
            $validated['associated_account_email'] = auth()->user()->email ?? $request->user()->email;

            $elderlyProfile = ElderlyProfile::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Elderly profile created successfully',
                'data' => $elderlyProfile
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create elderly profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ElderlyProfile $elderlyProfile): JsonResponse
    {
        // Check if the user owns this profile
        $userEmail = auth()->user()->email ?? request()->user()->email;
        if ($elderlyProfile->associated_account_email !== $userEmail) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: You can only view your own elderly profiles'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $elderlyProfile
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ElderlyProfile $elderlyProfile): JsonResponse
    {
        try {
            // Check if the user owns this profile
            $userEmail = auth()->user()->email ?? $request->user()->email;
            if ($elderlyProfile->associated_account_email !== $userEmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: You can only update your own elderly profiles'
                ], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:elderly_profiles,email,' . $elderlyProfile->id,
                'phone' => 'nullable|string|max:20',
            ]);

            // Always set temporary_role to 'elderly user'
            $validated['temporary_role'] = 'elderly user';
            
            // Maintain the associated_account_email
            $validated['associated_account_email'] = $elderlyProfile->associated_account_email;

            $elderlyProfile->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Elderly profile updated successfully',
                'data' => $elderlyProfile
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update elderly profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ElderlyProfile $elderlyProfile): JsonResponse
    {
        try {
            // Check if the user owns this profile
            $userEmail = auth()->user()->email ?? request()->user()->email;
            if ($elderlyProfile->associated_account_email !== $userEmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: You can only delete your own elderly profiles'
                ], 403);
            }

            $elderlyProfile->delete();

            return response()->json([
                'success' => true,
                'message' => 'Elderly profile deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete elderly profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deactivate an elderly profile and sync with Supabase.
     */
    public function deactivate(ElderlyProfile $elderlyProfile): JsonResponse
    {
        try {
            // Check if the user owns this profile
            $userEmail = auth()->user()->email ?? request()->user()->email;
            if ($elderlyProfile->associated_account_email !== $userEmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: You can only deactivate your own elderly profiles'
                ], 403);
            }

            // Update the status to 'deactivated' in Laravel
            $elderlyProfile->update(['status' => 'deactivated']);

            // Sync with Supabase
            $supabaseUrl = config('services.supabase.url');
            $supabaseKey = config('services.supabase.service_role_key');

            if (!$supabaseUrl || !$supabaseKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supabase configuration not found'
                ], 500);
            }

            // Update the profile in Supabase
            $response = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=minimal'
            ])->patch($supabaseUrl . '/rest/v1/elderly_profiles?id=eq.' . $elderlyProfile->id, [
                'status' => 'deactivated',
                'updated_at' => now()->toISOString()
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to sync with Supabase',
                    'error' => $response->body()
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Elderly profile deactivated successfully and synced with Supabase',
                'data' => $elderlyProfile->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate elderly profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
