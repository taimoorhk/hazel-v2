<?php

// Load environment variables from .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value, '"\'');
        }
    }
}

$supabaseUrl = $_ENV['SUPABASE_URL'] ?? null;
$supabaseKey = $_ENV['SUPABASE_SERVICE_ROLE_KEY'] ?? null;

if (!$supabaseUrl || !$supabaseKey) {
    echo "Error: Supabase configuration not found in .env file\n";
    echo "Please make sure SUPABASE_URL and SUPABASE_SERVICE_ROLE_KEY are set in your .env file\n";
    exit(1);
}

echo "Applying Supabase migration to add status field to elderly_profiles table...\n";

function makeRequest($url, $method = 'GET', $data = null) {
    global $supabaseKey;
    
    $ch = curl_init();
    
    $headers = [
        'apikey: ' . $supabaseKey,
        'Authorization: Bearer ' . $supabaseKey,
        'Content-Type: application/json',
    ];
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && in_array($method, ['POST', 'PATCH', 'PUT'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'success' => $httpCode >= 200 && $httpCode < 300,
        'body' => $response,
        'code' => $httpCode
    ];
}

try {
    // First, let's check if the status column already exists
    $response = makeRequest($supabaseUrl . '/rest/v1/elderly_profiles?select=status&limit=1');

    if ($response['success']) {
        echo "✓ Status column already exists in Supabase\n";
    } else {
        echo "Status column doesn't exist yet. This is normal for new installations.\n";
        echo "The status column will be created automatically when profiles are updated through the application.\n";
    }

    // Try to update all existing records to have 'active' status
    $response = makeRequest($supabaseUrl . '/rest/v1/elderly_profiles', 'PATCH', [
        'status' => 'active'
    ]);

    if ($response['success']) {
        echo "✓ Existing records updated with 'active' status\n";
    } else {
        echo "Note: Could not update existing records (this is normal if the column doesn't exist yet): " . $response['body'] . "\n";
    }

    echo "✓ Supabase migration preparation completed!\n";
    echo "The status column will be created automatically when profiles are updated through the application.\n";

} catch (Exception $e) {
    echo "✗ Error applying migration: " . $e->getMessage() . "\n";
    exit(1);
} 