<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/sync-supabase-user', [AuthController::class, 'syncSupabaseUser']);
Route::post('/check-user-questions', [AuthController::class, 'checkUserQuestions']);
