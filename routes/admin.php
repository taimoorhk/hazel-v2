<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group and admin middleware.
|
*/

// Admin login routes (no middleware required)
Route::get('login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('login', [AdminController::class, 'login'])->name('admin.login.post');

// Protected admin routes
Route::middleware(['admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard.alt');
    
    // User management
    Route::get('users', [AdminController::class, 'users'])->name('admin.users');
    Route::patch('users/{user}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.update-role');
    Route::delete('users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    // Elderly profiles management
    Route::get('elderly-profiles', [AdminController::class, 'elderlyProfiles'])->name('admin.elderly-profiles');
    Route::patch('elderly-profiles/{profile}/status', [AdminController::class, 'updateProfileStatus'])->name('admin.elderly-profiles.update-status');
    Route::patch('elderly-profiles/{profile}', [AdminController::class, 'updateProfile'])->name('admin.elderly-profiles.update');
    Route::delete('elderly-profiles/{profile}', [AdminController::class, 'deleteProfile'])->name('admin.elderly-profiles.delete');
    Route::post('sync-elderly-profiles', [AdminController::class, 'syncFromSupabase'])->name('admin.elderly-profiles.sync');
    
    // Admin logout
    Route::post('logout', [AdminController::class, 'logout'])->name('admin.logout');
}); 