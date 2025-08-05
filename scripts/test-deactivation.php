<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$baseUrl = 'http://localhost:8000';
$supabaseUrl = $_ENV['SUPABASE_URL'];
$supabaseKey = $_ENV['SUPABASE_SERVICE_ROLE_KEY'];

echo "Testing Elderly Profile Deactivation Functionality\n";
echo "================================================\n\n";

// Test 1: Check if the API endpoint exists
echo "1. Testing API endpoint availability...\n";
try {
    $response = Http::get($baseUrl . '/api/elderly-profiles');
    if ($response->successful()) {
        echo "✓ API endpoint is accessible\n";
    } else {
        echo "✗ API endpoint returned status: " . $response->status() . "\n";
    }
} catch (Exception $e) {
    echo "✗ Could not connect to API: " . $e->getMessage() . "\n";
}

// Test 2: Check Supabase connection
echo "\n2. Testing Supabase connection...\n";
try {
    $response = Http::withHeaders([
        'apikey' => $supabaseKey,
        'Authorization' => 'Bearer ' . $supabaseKey,
        'Content-Type' => 'application/json',
    ])->get($supabaseUrl . '/rest/v1/elderly_profiles?select=id,status&limit=1');

    if ($response->successful()) {
        echo "✓ Supabase connection successful\n";
        $data = $response->json();
        if (!empty($data)) {
            echo "  - Found " . count($data) . " profile(s) in database\n";
            if (isset($data[0]['status'])) {
                echo "  - Status field exists and is accessible\n";
            } else {
                echo "  - Warning: Status field not found in response\n";
            }
        } else {
            echo "  - No profiles found in database\n";
        }
    } else {
        echo "✗ Supabase connection failed: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "✗ Supabase connection error: " . $e->getMessage() . "\n";
}

// Test 3: Check Laravel database
echo "\n3. Testing Laravel database...\n";
try {
    // Create a simple database connection test
    $pdo = new PDO(
        "pgsql:host=" . $_ENV['DB_HOST'] . ";port=" . $_ENV['DB_PORT'] . ";dbname=" . $_ENV['DB_DATABASE'],
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
    );
    
    $stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'elderly_profiles' AND column_name = 'status'");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✓ Status column exists in Laravel database\n";
    } else {
        echo "✗ Status column not found in Laravel database\n";
    }
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM elderly_profiles");
    $result = $stmt->fetch();
    echo "  - Found " . $result['count'] . " profile(s) in Laravel database\n";
    
} catch (Exception $e) {
    echo "✗ Laravel database connection error: " . $e->getMessage() . "\n";
}

// Test 4: Check if the deactivation route is registered
echo "\n4. Testing deactivation route...\n";
try {
    $response = Http::patch($baseUrl . '/api/elderly-profiles/999/deactivate');
    // We expect a 401 or 403 since we're not authenticated, but the route should exist
    if ($response->status() === 401 || $response->status() === 403) {
        echo "✓ Deactivation route exists (authentication required)\n";
    } else if ($response->status() === 404) {
        echo "✗ Deactivation route not found\n";
    } else {
        echo "? Deactivation route returned unexpected status: " . $response->status() . "\n";
    }
} catch (Exception $e) {
    echo "✗ Could not test deactivation route: " . $e->getMessage() . "\n";
}

echo "\n================================================\n";
echo "Test completed. Check the results above.\n";
echo "\nTo test the full functionality:\n";
echo "1. Start the Laravel server: php artisan serve\n";
echo "2. Visit http://localhost:8000/elderly-profiles\n";
echo "3. Try deactivating a profile using the dropdown menu\n"; 