<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require authentication
$user = requireAuth();

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['order_id'], $data['amount'], $data['payment_method'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing required fields"
        ]);
        die();
    }

    $orderId = intval($data['order_id']);
    $amount = floatval($data['amount']);
    $paymentMethod = trim($data['payment_method']);

    require_once '../config/db.php';

    // Check if order exists and belongs to user
    $stmt = $conn->prepare("
        SELECT id, total_amount 
        FROM orders 
        WHERE id = ? AND user_id = ?
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

    // Verify amount
    if (abs($amount - $order['total_amount']) > 0.01) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Invalid payment amount"
        ]);
        die();
    }

    // Create payment
    $stmt = $conn->prepare("
        INSERT INTO payments (order_id, user_id, amount, payment_method, status, created_at) 
        VALUES (?, ?, ?, ?, 'completed', NOW())
    ");
    $stmt->bind_param("iid", $orderId, $user['id'], $amount, $paymentMethod);

    if ($stmt->execute()) {
        $paymentId = $conn->insert_id;

        // Update order status
        $stmt = $conn->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        // Get the created payment
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
            WHERE id = ?
        ");
        $stmt->bind_param("i", $paymentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $payment = $result->fetch_assoc();

        http_response_code(201);
        echo json_encode([
            "success" => true,
            "data" => $payment,
            "message" => "Payment created successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to create payment"
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>