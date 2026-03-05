<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require authentication
$user = requireAuth();

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['shop_id'], $data['total_amount'], $data['items'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing required fields"
        ]);
        die();
    }

    $shopId = intval($data['shop_id']);
    $totalAmount = floatval($data['total_amount']);
    $items = $data['items'];

    // Validate items
    if (!is_array($items) || empty($items)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Invalid items"
        ]);
        die();
    }

    require_once '../config/db.php';

    // Start transaction
    $conn->begin_transaction();

    try {
        // Create order
        $stmt = $conn->prepare("
            INSERT INTO orders (user_id, shop_id, total_amount, status, created_at) 
            VALUES (?, ?, ?, 'pending', NOW())
        ");
        $stmt->bind_param("iid", $user['id'], $shopId, $totalAmount);
        $stmt->execute();
        $orderId = $conn->insert_id;

        // Create order items
        foreach ($items as $item) {
            $productId = intval($item['product_id']);
            $quantity = intval($item['quantity']);
            $price = floatval($item['price']);

            $stmt = $conn->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("iiid", $orderId, $productId, $quantity, $price);
            $stmt->execute();
        }

        // Commit transaction
        $conn->commit();

        // Get the created order
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
            WHERE o.id = ?
        ");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
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

        http_response_code(201);
        echo json_encode([
            "success" => true,
            "data" => $order,
            "message" => "Order created successfully"
        ]);
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to create order: " . $e->getMessage()
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