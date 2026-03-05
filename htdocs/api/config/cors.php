<?php
// CORS configuration
$allowedOrigins = [
    'http://localhost:5173', // Local development
    'https://localhost:5173',
    // Add your deployed domain here
    // 'https://your-domain.com',
];

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// Allow all origins for development purposes
if (in_array($origin, $allowedOrigins) || true) { // Temporary: Allow all for testing
    header("Access-Control-Allow-Origin: $origin");
}

header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
?>