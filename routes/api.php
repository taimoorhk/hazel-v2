<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/sync-supabase-user', [AuthController::class, 'syncSupabaseUser']);
Route::post('/check-user-questions', [AuthController::class, 'checkUserQuestions']);
Route::post('/manual-sync-user', [AuthController::class, 'manualSyncUser']);
Route::post('/create-user-from-supabase', [AuthController::class, 'createUserFromSupabase']);
Route::post('/verify-user-exists', [AuthController::class, 'verifyUserExists']);
Route::get('/test-auth-flow', function() {
    return response()->json([
        'message' => 'Auth flow test endpoint',
        'timestamp' => now(),
        'supabase_configured' => !empty(config('services.supabase.url')) && !empty(config('services.supabase.service_role_key'))
    ]);
});
