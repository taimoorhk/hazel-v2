<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Test real-time sync from auth.users to public.users
echo "🧪 Testing Real-time Sync from auth.users to public.users\n";
echo "=====================================================\n\n";

// 1. Check current sync status
echo "1. Current Sync Status:\n";
$response = Http::get('http://localhost:8000/api/sync-status');
$status = $response->json();
echo "   - Total Laravel Users: {$status['total_laravel_users']}\n";
echo "   - Users with Supabase ID: {$status['users_with_supabase_id']}\n";
echo "   - Sync Percentage: {$status['sync_percentage']}%\n\n";

// 2. Test creating a new user (should trigger real-time sync)
echo "2. Testing User Creation (Real-time Sync):\n";
$testEmail = 'realtime-test-' . time() . '@example.com';
$testName = 'Real-time Test User ' . time();

// Create user via Laravel (should trigger real-time sync to Supabase)
$createResponse = Http::post('http://localhost:8000/api/create-test-user', [
    'email' => $testEmail,
    'name' => $testName,
    'role' => 'Normal User'
]);

if ($createResponse->successful()) {
    echo "   ✅ User created successfully: {$testEmail}\n";
    echo "   🔄 Real-time sync should have triggered automatically\n\n";
} else {
    echo "   ❌ Failed to create user: " . $createResponse->body() . "\n\n";
}

// 3. Check if user exists in both systems
echo "3. Verifying User in Both Systems:\n";

// Check Laravel
$laravelResponse = Http::post('http://localhost:8000/api/verify-user-exists', [
    'email' => $testEmail
]);
$laravelResult = $laravelResponse->json();

if ($laravelResult['exists']) {
    echo "   ✅ User exists in Laravel\n";
    echo "   - Supabase ID: " . ($laravelResult['supabase_id'] ?? 'Not set') . "\n";
} else {
    echo "   ❌ User not found in Laravel\n";
}

// Check Supabase Auth (via our API)
$supabaseResponse = Http::post('http://localhost:8000/api/check-supabase-user', [
    'email' => $testEmail
]);
$supabaseResult = $supabaseResponse->json();

if ($supabaseResult['exists']) {
    echo "   ✅ User exists in Supabase Auth\n";
    echo "   - Supabase ID: {$supabaseResult['supabase_id']}\n";
} else {
    echo "   ❌ User not found in Supabase Auth\n";
}

// 4. Check public.users table (should be synced via triggers)
echo "\n4. Checking public.users table (Real-time Sync):\n";
$publicUsersResponse = Http::post('http://localhost:8000/api/check-public-users', [
    'email' => $testEmail
]);
$publicUsersResult = $publicUsersResponse->json();

if ($publicUsersResult['exists']) {
    echo "   ✅ User exists in public.users table\n";
    echo "   - Name: {$publicUsersResult['name']}\n";
    echo "   - Email: {$publicUsersResult['email']}\n";
    echo "   - Supabase ID: {$publicUsersResult['supabase_id']}\n";
    echo "   🎉 Real-time sync is working perfectly!\n";
} else {
    echo "   ❌ User not found in public.users table\n";
    echo "   ⚠️  Real-time sync may not be working\n";
    echo "   💡 Make sure the Supabase migration has been applied\n";
}

echo "\n5. Final Sync Status:\n";
$finalResponse = Http::get('http://localhost:8000/api/sync-status');
$finalStatus = $finalResponse->json();
echo "   - Total Laravel Users: {$finalStatus['total_laravel_users']}\n";
echo "   - Users with Supabase ID: {$finalStatus['users_with_supabase_id']}\n";
echo "   - Sync Percentage: {$finalStatus['sync_percentage']}%\n";

echo "\n✅ Test completed!\n";
echo "\n📋 Next Steps:\n";
echo "1. Apply the Supabase migration if not done already\n";
echo "2. Run this test script again to verify real-time sync\n";
echo "3. Monitor the logs for real-time sync events\n"; 