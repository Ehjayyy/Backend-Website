<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require authentication
$user = requireAuth();

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['shop_name'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing required fields"
        ]);
        die();
    }

    $shopName = trim($data['shop_name']);
    $description = isset($data['description']) ? trim($data['description']) : null;

    // Check if user already has a shop
    require_once '../config/db.php';
    $stmt = $conn->prepare("SELECT id FROM shops WHERE user_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "error" => "You already have a shop"
        ]);
        die();
    }

    // Create shop
    $stmt = $conn->prepare("
        INSERT INTO shops (user_id, shop_name, description, created_at) 
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->bind_param("iss", $user['id'], $shopName, $description);

    if ($stmt->execute()) {
        $shopId = $conn->insert_id;

        // Get the created shop
        $stmt = $conn->prepare("
            SELECT 
                id, 
                user_id, 
                shop_name, 
                description, 
                created_at 
            FROM shops 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $shopId);
        $stmt->execute();
        $result = $stmt->get_result();
        $shop = $result->fetch_assoc();

        http_response_code(201);
        echo json_encode([
            "success" => true,
            "data" => $shop,
            "message" => "Shop created successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to create shop"
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