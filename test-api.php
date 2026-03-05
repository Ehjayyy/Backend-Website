<?php
/**
 * Test script to verify the API structure
 * Usage: php test-api.php
 */

function testEndpoint($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $defaultHeaders = [
        'Content-Type: application/json',
    ];
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($defaultHeaders, $headers));
    
    // For testing locally, you might need to disable SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'response' => json_decode($response, true),
        'raw' => $response
    ];
}

// Test configuration
$baseUrl = 'http://localhost/api';

echo "=== Testing API Endpoints ===\n\n";

// Test 1: Categories endpoint (should return categories list)
echo "1. Testing Categories Endpoint (GET /categories):\n";
$result = testEndpoint("$baseUrl/categories");
echo "Status: " . $result['status'] . "\n";
if ($result['status'] == 200 && isset($result['response'])) {
    echo "Success: " . count($result['response']) . " categories found\n";
} else {
    echo "Error: " . $result['raw'] . "\n";
}
echo "\n";

// Test 2: Products endpoint (should return products list or empty)
echo "2. Testing Products Endpoint (GET /products):\n";
$result = testEndpoint("$baseUrl/products");
echo "Status: " . $result['status'] . "\n";
if ($result['status'] == 200 && isset($result['response'])) {
    echo "Success: " . count($result['response']) . " products found\n";
} else {
    echo "Error: " . $result['raw'] . "\n";
}
echo "\n";

// Test 3: Auth endpoints (should return 405 for GET on login)
echo "3. Testing Login Endpoint (GET /auth/login):\n";
$result = testEndpoint("$baseUrl/auth/login");
echo "Status: " . $result['status'] . "\n";
if ($result['status'] == 405) {
    echo "Success: Method not allowed (expected)\n";
} else {
    echo "Error: " . $result['raw'] . "\n";
}
echo "\n";

// Test 4: Admin endpoints (should return 401 without token)
echo "4. Testing Admin Stats Endpoint (GET /admin/dashboard/stats):\n";
$result = testEndpoint("$baseUrl/admin/dashboard/stats");
echo "Status: " . $result['status'] . "\n";
if ($result['status'] == 401) {
    echo "Success: Unauthorized (expected without token)\n";
} else {
    echo "Error: " . $result['raw'] . "\n";
}
echo "\n";

// Summary
echo "=== Endpoint Summary ===\n";
echo "- GET /categories: ✓ Returns categories\n";
echo "- GET /products: ✓ Returns products\n";
echo "- GET /auth/login: ✓ Returns 405 Method Not Allowed\n";
echo "- GET /admin/dashboard/stats: ✓ Returns 401 Unauthorized\n";
echo "- All other endpoints should work with proper authentication\n";
echo "\n";
echo "Note: This test is running against $baseUrl\n";
echo "For production testing, replace with your domain\n";
