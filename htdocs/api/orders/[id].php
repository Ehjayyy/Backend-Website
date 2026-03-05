<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require authentication
$user = requireAuth();

// Handle GET request for single order
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $orderId = isset($_GET['id']) ? intval($_GET['id']) : null;

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
            o.id, 
            o.user_id, 
            o.shop_id, 
            o.total_amount, 
            o.status, 
            o.created_at,
            s.shop_name
        FROM orders o
        JOIN shops s ON o.shop_id = s.id
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->bind_param("ii", $orderId, $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Order not found or not authorized"
        ]);
        die();
    }

    $order = $result->fetch_assoc();

    // Get order items
    $stmt = $conn->prepare("
        SELECT 
            id, 
            order_id, 
            product_id, 
            quantity, 
            price 
        FROM order_items 
        WHERE order_id = ?
    ");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $itemsResult = $stmt->get_result();

    $orderItems = [];
    while ($row = $itemsResult->fetch_assoc()) {
        $orderItems[] = $row;
    }

    $order['items'] = $orderItems;

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $order
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>