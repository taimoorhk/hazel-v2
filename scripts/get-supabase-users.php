<?php

/**
 * Script to help get Supabase user IDs
 * 
 * To use this script:
 * 1. Get your service role key from Supabase Dashboard > Settings > API
 * 2. Update the variables below
 * 3. Run: php scripts/get-supabase-users.php
 */

// Update these variables with your Supabase details
$supabaseUrl = 'https://rertznygkkeykftoapge.supabase.co';
$serviceRoleKey = 'YOUR_SERVICE_ROLE_KEY_HERE'; // Get this from Supabase Dashboard

if ($serviceRoleKey === 'YOUR_SERVICE_ROLE_KEY_HERE') {
    echo "Please update the service role key in this script first.\n";
    echo "You can find it in your Supabase Dashboard > Settings > API\n";
    exit(1);
}

echo "Fetching users from Supabase...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $supabaseUrl . '/auth/v1/admin/users');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'apikey: ' . $serviceRoleKey,
    'Authorization: Bearer ' . $serviceRoleKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $users = json_decode($response, true);
    
    echo "Found " . count($users) . " users in Supabase:\n\n";
    
    foreach ($users as $user) {
        echo "Email: " . ($user['email'] ?? 'N/A') . "\n";
        echo "ID: " . ($user['id'] ?? 'N/A') . "\n";
        echo "Name: " . ($user['user_metadata']['name'] ?? $user['user_metadata']['display_name'] ?? 'N/A') . "\n";
        echo "Role: " . ($user['user_metadata']['role'] ?? 'N/A') . "\n";
        echo "Min Endpointing Delay: " . ($user['user_metadata']['min_endpointing_delay'] ?? '0.5') . "s\n";
        echo "Max Endpointing Delay: " . ($user['user_metadata']['max_endpointing_delay'] ?? '6.0') . "s\n";
        echo "Min Speech Duration: " . ($user['user_metadata']['min_speech_duration'] ?? '0.05') . "s\n";
        echo "Min Silence Duration: " . ($user['user_metadata']['min_silence_duration'] ?? '0.55') . "s\n";
        echo "Prefix Padding Duration: " . ($user['user_metadata']['prefix_padding_duration'] ?? '0.5') . "s\n";
        echo "Max Buffered Speech: " . ($user['user_metadata']['max_buffered_speech'] ?? '60') . "s\n";
        echo "Activation Threshold: " . ($user['user_metadata']['activation_threshold'] ?? '0.5') . "\n";
        echo "Created: " . ($user['created_at'] ?? 'N/A') . "\n";
        echo "---\n";
    }
    
    echo "\nTo add these users to Laravel, use:\n";
    foreach ($users as $user) {
        $email = $user['email'] ?? '';
        $id = $user['id'] ?? '';
        $name = $user['user_metadata']['name'] ?? $user['user_metadata']['display_name'] ?? 'User';
        $role = $user['user_metadata']['role'] ?? 'Normal User';
        
        if ($email && $id) {
            echo "php artisan make:user \"$email\" \"$name\" \"$role\" \"$id\"\n";
        }
    }
    
} else {
    echo "Error fetching users. HTTP Code: $httpCode\n";
    echo "Response: $response\n";
} 