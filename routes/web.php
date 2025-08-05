<?php

use App\Http\Controllers\ConversationController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::resource('conversation', ConversationController::class);

Route::get('/elderly-profiles', function () {
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
