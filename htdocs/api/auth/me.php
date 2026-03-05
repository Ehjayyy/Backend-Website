<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require authentication
$user = requireAuth();

http_response_code(200);
echo json_encode([
    "success" => true,
    "data" => $user
]);
?>
