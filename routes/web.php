<?php

use App\Http\Controllers\ConversationController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;




Route::get('/', function () {
    // Check if user is authenticated via Supabase
    $user = auth()->user();
    
    if ($user) {
        // User is authenticated, redirect to dashboard
        return redirect()->route('dashboard');
    } else {
        // User is not authenticated, redirect to login
        return redirect()->route('login');
    }
})->name('home');

Route::resource('conversation', ConversationController::class);

Route::get('/elderly-profiles', function () {
    return inertia('ElderlyProfiles');
})->name('elderly-profiles');

Route::get('/elderly-profiles/{id}', function ($id) {
    return inertia('ElderlyProfileDetail', ['id' => $id]);
})->name('frontend.elderly-profiles.show');

// User-side elderly profile management routes
Route::prefix('elderly-profiles')->group(function () {
    Route::post('/', [App\Http\Controllers\ElderlyProfileController::class, 'store'])->name('frontend.elderly-profiles.store');
    Route::patch('/{profile}', [App\Http\Controllers\ElderlyProfileController::class, 'update'])->name('frontend.elderly-profiles.update');
    Route::patch('/{profile}/status', [App\Http\Controllers\ElderlyProfileController::class, 'updateStatus'])->name('frontend.elderly-profiles.update-status');
});

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::get('/final-health-dashboard', function () {
    return Inertia::render('FinalHealthDashboard');
})->name('final-health-dashboard');

Route::get('/weekly-summary-dashboard', function () {
    return Inertia::render('WeeklySummaryDashboard');
})->name('weekly-summary-dashboard');

Route::get('/test-weekly-summary', function () {
    return Inertia::render('TestWeeklySummary');
})->name('test-weekly-summary');



// Simple billing route (no backend authentication required)
Route::get('/billing', function () {
    return Inertia::render('Billing');
})->name('billing');

// Stripe and Billing API Routes (these will handle authentication)
Route::prefix('billing')->group(function () {
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

Route::get('/custom-settings', function () {
    return Inertia::render('CustomSettings');
})->name('custom-settings');

Route::patch('/custom-settings', [App\Http\Controllers\CustomSettingsController::class, 'update'])->name('custom-settings.update');

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
