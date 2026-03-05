<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require admin authentication
$user = requireAuth('ADMIN');

// Handle GET request for users list
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../config/db.php';

    $sql = "
        SELECT 
            id, 
            name, 
            email, 
            role, 
            created_at 
        FROM users 
        ORDER BY created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $users
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>