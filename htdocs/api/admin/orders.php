<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require admin authentication
$user = requireAuth('ADMIN');

// Handle GET request for orders list
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../config/db.php';

    $sql = "
        SELECT 
            o.id, 
            o.user_id, 
            o.shop_id, 
            o.total_amount, 
            o.status, 
            o.created_at,
            u.name as user_name,
            u.email as user_email,
            s.shop_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN shops s ON o.shop_id = s.id
        ORDER BY o.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $orders
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>