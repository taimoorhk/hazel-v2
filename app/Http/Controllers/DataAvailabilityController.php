<?php

namespace App\Http\Controllers;

use App\Services\EnhancedDigitalOceanSpacesService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DataAvailabilityController extends Controller
{
    private $digitalOceanService;

    public function __construct(EnhancedDigitalOceanSpacesService $digitalOceanService)
    {
        $this->digitalOceanService = $digitalOceanService;
    }

    /**
     * Check if data exists for a specific profile
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

        $accountId = $request->input('account_id');
        $profileId = $request->input('profile_id');

        $data = $this->digitalOceanService->checkProfileDataExists($accountId, $profileId);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $data['has_data'] ? 'Data found for profile' : 'No data found for profile'
        ]);
    }

    /**
     * Check data availability for multiple profiles
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkMultipleProfiles(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|integer',
            'profile_ids' => 'required|array',
            'profile_ids.*' => 'integer'
        ]);

        $accountId = $request->input('account_id');
        $profileIds = $request->input('profile_ids');

        $data = $this->digitalOceanService->checkMultipleProfilesData($accountId, $profileIds);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Data availability checked for multiple profiles'
        ]);
    }

    /**
     * Check data availability for entire account (main + all elderly profiles)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkAccountData(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|integer'
        ]);

        $accountId = $request->input('account_id');

        $data = $this->digitalOceanService->checkAccountDataAvailability($accountId);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Account data availability checked'
        ]);
    }

    /**
     * Real-time data availability check with status updates
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function realtimeDataCheck(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|integer',
            'profile_id' => 'nullable|integer'
        ]);

        $accountId = $request->input('account_id');
        $profileId = $request->input('profile_id');

        if ($profileId) {
            // Check specific profile
            $data = $this->digitalOceanService->checkProfileDataExists($accountId, $profileId);
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString(),
                'message' => $data['has_data'] ? 'Data available' : 'No data available'
            ]);
        } else {
            // Check entire account
            $data = $this->digitalOceanService->checkAccountDataAvailability($accountId);
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString(),
                'message' => 'Account data availability updated'
            ]);
        }
    }

    /**
     * Get data status summary for dashboard
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDataStatusSummary(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|integer'
        ]);

        $accountId = $request->input('account_id');

        $data = $this->digitalOceanService->checkAccountDataAvailability($accountId);
        
        // Create a summary for frontend display
        $summary = [
            'account_id' => $accountId,
            'main_profile' => [
                'has_data' => $data['main_account']['has_data'],
                'has_canary_data' => $data['main_account']['has_canary_data'],
                'file_count' => $data['main_account']['file_count'],
                'last_modified' => $data['main_account']['last_modified']
            ],
            'elderly_profiles' => [],
            'overall_status' => [
                'has_any_data' => $data['summary']['has_any_data'],
                'has_main_data' => $data['summary']['has_main_data'],
                'has_elderly_data' => $data['summary']['has_elderly_data'],
                'total_elderly_profiles_with_data' => $data['summary']['total_elderly_profiles_with_data'],
                'total_profile_folders_found' => $data['summary']['total_profile_folders_found'],
                'main_account_has_folder' => $data['summary']['main_account_has_folder']
            ],
            'digitalocean_structure' => $data['digitalocean_structure']
        ];

        // Add elderly profiles data
        foreach ($data['elderly_profiles'] as $profileId => $profileData) {
            $summary['elderly_profiles'][$profileId] = [
                'has_data' => $profileData['has_data'],
                'has_canary_data' => $profileData['has_canary_data'],
                'file_count' => $profileData['file_count'],
                'last_modified' => $profileData['last_modified']
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $summary,
            'timestamp' => now()->toISOString(),
            'message' => 'Data status summary retrieved'
        ]);
    }

    /**
     * Get elderly profiles data status for an account
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getElderlyProfilesDataStatus(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|integer',
            'expected_elderly_profile_ids' => 'nullable|array',
            'expected_elderly_profile_ids.*' => 'integer'
        ]);

        $accountId = $request->input('account_id');
        $expectedElderlyProfileIds = $request->input('expected_elderly_profile_ids', []);

        $data = $this->digitalOceanService->checkElderlyProfilesDataStatus($accountId, $expectedElderlyProfileIds);

        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toISOString(),
            'message' => 'Elderly profiles data status retrieved'
        ]);
    }

    /**
     * Get all elderly profile IDs that have data in DigitalOcean
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getElderlyProfilesWithData(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|integer'
        ]);

        $accountId = $request->input('account_id');

        $elderlyProfileIds = $this->digitalOceanService->getElderlyProfileIdsForAccount($accountId);

        return response()->json([
            'success' => true,
            'data' => [
                'account_id' => $accountId,
                'elderly_profile_ids_with_data' => $elderlyProfileIds,
                'total_elderly_profiles_with_data' => count($elderlyProfileIds)
            ],
            'timestamp' => now()->toISOString(),
            'message' => 'Elderly profiles with data retrieved'
        ]);
    }

    /**
     * Get comprehensive status for all elderly profiles (with and without data)
     * This is used to show "No results" cards for elderly profiles without data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getComprehensiveElderlyProfilesStatus(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|integer',
            'system_elderly_profile_ids' => 'required|array',
            'system_elderly_profile_ids.*' => 'integer'
        ]);

        $accountId = $request->input('account_id');
        $systemElderlyProfileIds = $request->input('system_elderly_profile_ids');

        $data = $this->digitalOceanService->getComprehensiveElderlyProfilesStatus($accountId, $systemElderlyProfileIds);

        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toISOString(),
            'message' => 'Comprehensive elderly profiles status retrieved'
        ]);
    }
}
