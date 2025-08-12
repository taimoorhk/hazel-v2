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

// Stripe and Billing Routes
Route::prefix('billing')->group(function () {
    Route::get('/', [App\Http\Controllers\BillingController::class, 'index'])->name('billing.index');
    Route::get('/success', [App\Http\Controllers\BillingController::class, 'success'])->name('billing.success');
    Route::get('/cancel', [App\Http\Controllers\BillingController::class, 'cancel'])->name('billing.cancel');
    Route::post('/cancel-subscription', [App\Http\Controllers\BillingController::class, 'cancelSubscription'])->name('billing.cancel-subscription');
    Route::post('/resume-subscription', [App\Http\Controllers\BillingController::class, 'resumeSubscription'])->name('billing.resume-subscription');
    Route::post('/update-payment-method', [App\Http\Controllers\BillingController::class, 'updatePaymentMethod'])->name('billing.update-payment-method');
});

// Stripe API Routes
Route::prefix('stripe')->group(function () {
    Route::post('/create-checkout-session', [App\Http\Controllers\StripeController::class, 'createCheckoutSession'])->name('stripe.create-checkout-session');
    Route::post('/webhook', [App\Http\Controllers\StripeController::class, 'handleWebhook'])->name('stripe.webhook');
});

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
