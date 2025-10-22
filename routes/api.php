<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ElderlyProfileController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TestStatsController;
use App\Http\Controllers\ComprehensiveStatsController;
use App\Http\Controllers\RealDigitalOceanController;
use App\Http\Controllers\AccurateCanaryDataController;
use App\Http\Controllers\EnhancedCanaryDataController;
use App\Http\Controllers\DataAvailabilityController;
use App\Http\Controllers\RealtimeSyncController;
use App\Http\Controllers\FinalHealthDataController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/sync-supabase-user', [AuthController::class, 'syncSupabaseUser']);
Route::post('/check-user-questions', [AuthController::class, 'checkUserQuestions']);
Route::post('/manual-sync-user', [AuthController::class, 'manualSyncUser']);
Route::post('/create-user-from-supabase', [AuthController::class, 'createUserFromSupabase']);
Route::post('/verify-user-exists', [AuthController::class, 'verifyUserExists']);
Route::get('/test-supabase-connection', [AuthController::class, 'testSupabaseConnection']);
Route::post('/sync-user-to-supabase', [AuthController::class, 'syncUserToSupabase']);
Route::post('/sync-all-users-to-supabase', [AuthController::class, 'syncAllUsersToSupabase']);
Route::post('/sync-all-users-from-supabase', [AuthController::class, 'syncAllUsersFromSupabase']);
Route::get('/sync-status', [AuthController::class, 'getSyncStatus']);
Route::post('/realtime-sync-user', [AuthController::class, 'realtimeSyncUser']);
Route::post('/realtime-sync-all', [AuthController::class, 'realtimeSyncAll']);
Route::post('/create-test-user', [AuthController::class, 'createTestUser']);
Route::post('/check-supabase-user', [AuthController::class, 'checkSupabaseUser']);
Route::post('/check-public-users', [AuthController::class, 'checkPublicUsers']);
Route::post('/sync-from-supabase-to-laravel', [AuthController::class, 'syncFromSupabaseToLaravel']);

// Elderly Profiles API routes - Protected by Supabase authentication
Route::middleware('supabase.auth')->group(function () {
    Route::apiResource('elderly-profiles', ElderlyProfileController::class);
    Route::patch('elderly-profiles/{elderlyProfile}/status', [ElderlyProfileController::class, 'updateStatus']);
});

// Stats API routes - Protected by Supabase authentication
Route::middleware('supabase.auth')->group(function () {
    // Main profile stats (profile_id/account_id)
    Route::get('stats/profile', [StatsController::class, 'getProfileStats']);
    Route::get('stats/profile/summary', [StatsController::class, 'getStatsSummary']);
    Route::get('stats/profile/file', [StatsController::class, 'getSpecificStatsFile']);
    Route::get('stats/profile/verify', [StatsController::class, 'verifyPath']);
    
    // Elderly profile stats (profile_id/profile_id/elderly_account_id)
    Route::get('stats/elderly-profile', [StatsController::class, 'getElderlyProfileStats']);
});

Route::get('/test-auth-flow', function() {
    return response()->json([
        'message' => 'Auth flow test endpoint',
        'timestamp' => now(),
        'supabase_configured' => !empty(config('services.supabase.url')) && !empty(config('services.supabase.service_role_key'))
    ]);
});

// Test endpoints for DigitalOcean Spaces (no authentication required)
Route::get('/test-stats-connection', [TestStatsController::class, 'testConnection']);
Route::get('/test-stats-sample', [TestStatsController::class, 'getSampleData']);
Route::get('/test-elderly-profile-data', [TestStatsController::class, 'getElderlyProfileData']);

// Comprehensive stats endpoints with accurate JSON structure
Route::get('/comprehensive-stats/account-6', [ComprehensiveStatsController::class, 'getAccount6Stats']);
Route::get('/comprehensive-stats/elderly-profile-15', [ComprehensiveStatsController::class, 'getElderlyProfile15Stats']);
Route::get('/comprehensive-stats/health-analytics', [ComprehensiveStatsController::class, 'getHealthAnalytics']);

// Real DigitalOcean data endpoints with correct path structure
Route::get('/real-data/account-6', [RealDigitalOceanController::class, 'getAccount6RealData']);
Route::get('/real-data/profile-15', [RealDigitalOceanController::class, 'getProfile15RealData']);
Route::get('/real-data/test-connection', [RealDigitalOceanController::class, 'testRealConnection']);

// Accurate canary analysis data endpoints (simulating real DigitalOcean structure)
Route::get('/accurate-canary/account-6', [AccurateCanaryDataController::class, 'getAccount6AccurateData']);
Route::get('/accurate-canary/profile-15', [AccurateCanaryDataController::class, 'getProfile15AccurateData']);
Route::get('/accurate-canary/dashboard-analytics', [AccurateCanaryDataController::class, 'getHealthAnalyticsForDashboard']);

// Enhanced comprehensive canary analysis data endpoints (with all health conditions)
Route::get('/enhanced-canary/account-6', [EnhancedCanaryDataController::class, 'getAccount6EnhancedData']);
Route::get('/enhanced-canary/account-7', [EnhancedCanaryDataController::class, 'getAccount7EnhancedData']);
Route::get('/enhanced-canary/profile-15', [EnhancedCanaryDataController::class, 'getProfile15EnhancedData']);
Route::get('/enhanced-canary/dashboard-analytics', [EnhancedCanaryDataController::class, 'getEnhancedHealthAnalytics']);

// Real-time sync endpoints for DigitalOcean data
Route::prefix('realtime-sync')->group(function () {
    Route::post('/profile-data', [RealtimeSyncController::class, 'getProfileData']);
    Route::get('/sync-status', [RealtimeSyncController::class, 'getSyncStatus']);
    Route::get('/available-accounts', [RealtimeSyncController::class, 'getAvailableAccounts']);
    Route::get('/account-profiles', [RealtimeSyncController::class, 'getAccountProfiles']);
    Route::get('/check-profile-data', [RealtimeSyncController::class, 'checkProfileData']);
    Route::get('/comprehensive-sync-info', [RealtimeSyncController::class, 'getComprehensiveSyncInfo']);
    Route::get('/test-connection', [RealtimeSyncController::class, 'testConnection']);
});

// Data availability checking endpoints
Route::prefix('data-availability')->group(function () {
    Route::post('/check-profile', [DataAvailabilityController::class, 'checkProfileData']);
    Route::post('/check-multiple-profiles', [DataAvailabilityController::class, 'checkMultipleProfiles']);
    Route::post('/check-account', [DataAvailabilityController::class, 'checkAccountData']);
    Route::post('/realtime-check', [DataAvailabilityController::class, 'realtimeDataCheck']);
    Route::post('/status-summary', [DataAvailabilityController::class, 'getDataStatusSummary']);
    Route::post('/elderly-profiles-data-status', [DataAvailabilityController::class, 'getElderlyProfilesDataStatus']);
    Route::post('/elderly-profiles-with-data', [DataAvailabilityController::class, 'getElderlyProfilesWithData']);
    Route::post('/comprehensive-elderly-profiles-status', [DataAvailabilityController::class, 'getComprehensiveElderlyProfilesStatus']);
});

// Final health data endpoints (3rd checkpoint data)
Route::prefix('final-health-data')->group(function () {
    Route::post('/get-final-data', [FinalHealthDataController::class, 'getFinalHealthData']);
    Route::post('/get-multiple-users', [FinalHealthDataController::class, 'getMultipleUsersFinalData']);
    Route::get('/realtime-sync-status', [FinalHealthDataController::class, 'getRealtimeSyncStatus']);
});
