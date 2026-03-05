<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require admin authentication
$user = requireAuth('ADMIN');

// Handle GET request for shops list
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../config/db.php';

    $sql = "
        SELECT 
            s.id, 
            s.user_id, 
            s.shop_name, 
            s.description, 
            s.created_at,
            u.name as owner_name,
            u.email as owner_email
        FROM shops s
        JOIN users u ON s.user_id = u.id
        ORDER BY s.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $shops = [];
    while ($row = $result->fetch_assoc()) {
        $shops[] = $row;
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $shops
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>