<?php

use App\Http\Controllers\ConversationController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::resource('conversation', ConversationController::class);

Route::get('/elderly-profiles', function () {
    return Inertia::render('ElderlyProfiles');
})->name('elderly-profiles');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
