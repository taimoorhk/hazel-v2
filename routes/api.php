<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ElderlyProfileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/sync-supabase-user', [AuthController::class, 'syncSupabaseUser']);
Route::post('/check-user-questions', [AuthController::class, 'checkUserQuestions']);
Route::post('/manual-sync-user', [AuthController::class, 'manualSyncUser']);
Route::post('/create-user-from-supabase', [AuthController::class, 'createUserFromSupabase']);
Route::post('/verify-user-exists', [AuthController::class, 'verifyUserExists']);
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

Route::get('/test-auth-flow', function() {
    return response()->json([
        'message' => 'Auth flow test endpoint',
        'timestamp' => now(),
        'supabase_configured' => !empty(config('services.supabase.url')) && !empty(config('services.supabase.service_role_key'))
    ]);
});
