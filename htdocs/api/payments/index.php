<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require authentication
$user = requireAuth();

// Handle GET request for payments list
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../config/db.php';

    $sql = "
        SELECT 
            p.id, 
            p.order_id, 
            p.user_id, 
            p.amount, 
            p.payment_method, 
            p.status, 
            p.created_at,
            o.total_amount,
            o.status as order_status
        FROM payments p
        JOIN orders o ON p.order_id = o.id
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $payments
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>
