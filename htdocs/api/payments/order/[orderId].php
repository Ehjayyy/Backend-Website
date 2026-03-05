<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require authentication
$user = requireAuth();

// Handle GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $orderId = isset($_GET['orderId']) ? intval($_GET['orderId']) : null;

    if (!$orderId) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing order ID"
        ]);
        die();
    }

    require_once '../config/db.php';

    $stmt = $conn->prepare("
        SELECT 
            id, 
            order_id, 
            user_id, 
            amount, 
            payment_method, 
            status, 
            created_at 
        FROM payments 
        WHERE order_id = ? AND user_id = ?
    ");
    $stmt->bind_param("ii", $orderId, $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Payment not found or not authorized"
        ]);
        die();
    }

    $payment = $result->fetch_assoc();

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $payment
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>
