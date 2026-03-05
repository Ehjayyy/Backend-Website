<?php
require_once '../config/cors.php';
require_once '../config/db.php';

// Handle GET request for single shop
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $shopId = isset($_GET['id']) ? intval($_GET['id']) : null;

    if (!$shopId) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing shop ID"
        ]);
        die();
    }

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

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Shop not found"
        ]);
        die();
    }

    $shop = $result->fetch_assoc();

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $shop
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>
