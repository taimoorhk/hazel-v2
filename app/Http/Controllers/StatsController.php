<?php

namespace App\Http\Controllers;

use App\Services\DigitalOceanSpacesService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class StatsController extends Controller
{
    private $digitalOceanService;

    public function __construct(DigitalOceanSpacesService $digitalOceanService)
    {
        $this->digitalOceanService = $digitalOceanService;
    }

    /**
     * Get stats for a specific profile and account
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getProfileStats(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|integer|min:1',
            'account_id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $profileId = $request->input('profile_id');
        $accountId = $request->input('account_id');

        try {
            // Verify the path exists
            if (!$this->digitalOceanService->verifyPath($profileId, $accountId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the specified profile and account',
                    'profile_id' => $profileId,
                    'account_id' => $accountId
                ], 404);
            }

            // Get all stats files
            $stats = $this->digitalOceanService->getStatsForProfile($profileId, $accountId);

            return response()->json([
                'success' => true,
                'message' => 'Stats retrieved successfully',
                'profile_id' => $profileId,
                'account_id' => $accountId,
                'data' => $stats,
                'count' => count($stats)
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting profile stats: ' . $e->getMessage(), [
                'profile_id' => $profileId,
                'account_id' => $accountId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stats summary for a specific profile and account
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getStatsSummary(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|integer|min:1',
            'account_id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $profileId = $request->input('profile_id');
        $accountId = $request->input('account_id');

        try {
            // Verify the path exists
            if (!$this->digitalOceanService->verifyPath($profileId, $accountId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the specified profile and account',
                    'profile_id' => $profileId,
                    'account_id' => $accountId
                ], 404);
            }

            // Get stats summary
            $summary = $this->digitalOceanService->getStatsSummary($profileId, $accountId);

            return response()->json([
                'success' => true,
                'message' => 'Stats summary retrieved successfully',
                'profile_id' => $profileId,
                'account_id' => $accountId,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting stats summary: ' . $e->getMessage(), [
                'profile_id' => $profileId,
                'account_id' => $accountId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stats summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific stats file
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getSpecificStatsFile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|integer|min:1',
            'account_id' => 'required|integer|min:1',
            'filename' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $profileId = $request->input('profile_id');
        $accountId = $request->input('account_id');
        $filename = $request->input('filename');

        try {
            // Get specific stats file
            $statsFile = $this->digitalOceanService->getSpecificStatsFile($profileId, $accountId, $filename);

            if (!$statsFile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stats file not found',
                    'profile_id' => $profileId,
                    'account_id' => $accountId,
                    'filename' => $filename
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Stats file retrieved successfully',
                'profile_id' => $profileId,
                'account_id' => $accountId,
                'data' => $statsFile
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting specific stats file: ' . $e->getMessage(), [
                'profile_id' => $profileId,
                'account_id' => $accountId,
                'filename' => $filename
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stats file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify if a path exists for profile and account
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyPath(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|integer|min:1',
            'account_id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $profileId = $request->input('profile_id');
        $accountId = $request->input('account_id');

        try {
            $exists = $this->digitalOceanService->verifyPath($profileId, $accountId);

            return response()->json([
                'success' => true,
                'message' => 'Path verification completed',
                'profile_id' => $profileId,
                'account_id' => $accountId,
                'exists' => $exists
            ]);

        } catch (\Exception $e) {
            Log::error('Error verifying path: ' . $e->getMessage(), [
                'profile_id' => $profileId,
                'account_id' => $accountId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify path',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stats for elderly profile (profile_id/profile_id/elderly_account_id)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getElderlyProfileStats(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|integer|min:1',
            'elderly_account_id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $profileId = $request->input('profile_id');
        $elderlyAccountId = $request->input('elderly_account_id');

        try {
            // For elderly profiles, we use the same profile_id for both path segments
            // Path: profile_id/profile_id/elderly_account_id
            if (!$this->digitalOceanService->verifyPath($profileId, $elderlyAccountId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the specified elderly profile',
                    'profile_id' => $profileId,
                    'elderly_account_id' => $elderlyAccountId
                ], 404);
            }

            // Get all stats files
            $stats = $this->digitalOceanService->getStatsForProfile($profileId, $elderlyAccountId);

            return response()->json([
                'success' => true,
                'message' => 'Elderly profile stats retrieved successfully',
                'profile_id' => $profileId,
                'elderly_account_id' => $elderlyAccountId,
                'data' => $stats,
                'count' => count($stats)
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting elderly profile stats: ' . $e->getMessage(), [
                'profile_id' => $profileId,
                'elderly_account_id' => $elderlyAccountId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve elderly profile stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
