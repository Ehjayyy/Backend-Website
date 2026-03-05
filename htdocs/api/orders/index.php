<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require authentication
$user = requireAuth();

// Handle GET request for orders list
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../config/db.php';

    $sql = "
        SELECT 
            o.id, 
            o.user_id, 
            o.total_amount, 
            o.status, 
            o.created_at,
            s.shop_name
        FROM orders o
        JOIN shops s ON o.shop_id = s.id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user['id']);
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
