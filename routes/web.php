<?php

use App\Http\Controllers\ConversationController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;




Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::resource('conversation', ConversationController::class);

Route::get('/elderly-profiles', function () {
    // Check if user is authenticated and has the right role
    if (auth()->check()) {
        $user = auth()->user();
        // If user has Normal User role, redirect to dashboard
        if ($user->role === 'Normal User') {
            return redirect()->route('dashboard')->with('message', 'Access denied. Elderly Profiles are not available for Normal Users.');
        }
    }
    return inertia('ElderlyProfiles');
});

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::get('/billing', function () {
    return Inertia::render('Billing');
})->name('billing');

Route::get('/help', function () {
    return Inertia::render('Help');
})->name('help');

Route::get('/reports', function () {
    // Check if user is authenticated and has the right role
    if (auth()->check()) {
        $user = auth()->user();
        // If user has Organization role, redirect to dashboard
        if ($user->role === 'Organization') {
            return redirect()->route('dashboard')->with('message', 'Access denied. Reports are not available for Organization users.');
        }
    }
    return Inertia::render('Reports');
})->name('reports');

Route::get('/login', function () {
    return Inertia::render('auth/Login');
})->name('login');

Route::get('/register', function () {
    return Inertia::render('auth/Register');
})->name('register');

Route::get('/auth/confirm', function () {
    return Inertia::render('auth/Confirm');
})->name('auth.confirm');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
