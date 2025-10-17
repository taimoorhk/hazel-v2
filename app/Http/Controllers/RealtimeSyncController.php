<?php

namespace App\Http\Controllers;

use App\Services\EnhancedDigitalOceanSpacesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RealtimeSyncController extends Controller
{
    protected $digitalOceanService;

    public function __construct(EnhancedDigitalOceanSpacesService $digitalOceanService)
    {
        $this->digitalOceanService = $digitalOceanService;
    }

    /**
     * Get data for any profile with fallback for missing data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getProfileData(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|integer',
            'profile_id' => 'required|integer'
        ]);

        try {
            $accountId = $request->input('account_id');
            $profileId = $request->input('profile_id');
            
            $data = $this->digitalOceanService->getStatsSummary($profileId, $accountId);
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [
                    'has_data' => false,
                    'message' => 'Error fetching data',
                    'account_id' => $request->input('account_id'),
                    'profile_id' => $request->input('profile_id'),
                ]
            ], 500);
        }
    }

    /**
     * Get sync status for all accounts and profiles
     *
     * @return JsonResponse
     */
    public function getSyncStatus(): JsonResponse
    {
        try {
            $syncStatus = $this->digitalOceanService->getSyncStatus();
            
            return response()->json([
                'success' => true,
                'data' => $syncStatus,
                'message' => 'Sync status retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting sync status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all available account IDs
     *
     * @return JsonResponse
     */
    public function getAvailableAccounts(): JsonResponse
    {
        try {
            $accountIds = $this->digitalOceanService->getAllAccountIds();
            
            return response()->json([
                'success' => true,
                'data' => $accountIds,
                'message' => 'Available accounts retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting available accounts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all profile IDs for a specific account
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAccountProfiles(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|integer'
        ]);

        try {
            $accountId = $request->input('account_id');
            $profileIds = $this->digitalOceanService->getProfileIdsForAccount($accountId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'account_id' => $accountId,
                    'profile_ids' => $profileIds,
                    'total_profiles' => count($profileIds)
                ],
                'message' => 'Account profiles retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting account profiles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if a specific profile has data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkProfileData(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|integer',
            'profile_id' => 'required|integer'
        ]);

        try {
            $accountId = $request->input('account_id');
            $profileId = $request->input('profile_id');
            
            $hasData = $this->digitalOceanService->hasProfileData($profileId, $accountId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'account_id' => $accountId,
                    'profile_id' => $profileId,
                    'has_data' => $hasData,
                    'message' => $hasData ? 'Data available' : 'No Reports'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking profile data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive sync information
     *
     * @return JsonResponse
     */
    public function getComprehensiveSyncInfo(): JsonResponse
    {
        try {
            $accountIds = $this->digitalOceanService->getAllAccountIds();
            $comprehensiveData = [];

            foreach ($accountIds as $accountId) {
                $profileIds = $this->digitalOceanService->getProfileIdsForAccount($accountId);
                $profileData = [];

                foreach ($profileIds as $profileId) {
                    $hasData = $this->digitalOceanService->hasProfileData($profileId, $accountId);
                    $profileData[] = [
                        'profile_id' => $profileId,
                        'has_data' => $hasData,
                        'status' => $hasData ? 'Data Available' : 'No Reports'
                    ];
                }

                $comprehensiveData[] = [
                    'account_id' => $accountId,
                    'total_profiles' => count($profileIds),
                    'profiles_with_data' => count(array_filter($profileData, fn($p) => $p['has_data'])),
                    'profiles_without_data' => count(array_filter($profileData, fn($p) => !$p['has_data'])),
                    'profiles' => $profileData
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_accounts' => count($accountIds),
                    'accounts' => $comprehensiveData,
                    'summary' => [
                        'total_profiles' => array_sum(array_column($comprehensiveData, 'total_profiles')),
                        'profiles_with_data' => array_sum(array_column($comprehensiveData, 'profiles_with_data')),
                        'profiles_without_data' => array_sum(array_column($comprehensiveData, 'profiles_without_data')),
                    ]
                ],
                'message' => 'Comprehensive sync information retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting comprehensive sync info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test DigitalOcean connection and credentials
     *
     * @return JsonResponse
     */
    public function testConnection(): JsonResponse
    {
        try {
            $accountIds = $this->digitalOceanService->getAllAccountIds();
            
            return response()->json([
                'success' => true,
                'message' => 'DigitalOcean connection successful',
                'data' => [
                    'connection_status' => 'connected',
                    'available_accounts' => count($accountIds),
                    'account_ids' => $accountIds,
                    'configuration' => [
                        'bucket' => config('services.digitalocean.bucket_name'),
                        'region' => config('services.digitalocean.region'),
                        'endpoint' => config('services.digitalocean.endpoint'),
                        'base_path' => config('services.digitalocean.base_path'),
                        'has_credentials' => !empty(config('services.digitalocean.key')) && !empty(config('services.digitalocean.secret'))
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'DigitalOcean connection failed: ' . $e->getMessage(),
                'data' => [
                    'connection_status' => 'failed',
                    'error_details' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }
}
