<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require authentication
$user = requireAuth();

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing product ID"
        ]);
        die();
    }

    $productId = intval($data['id']);

    // Check if product exists and belongs to user's shop
    require_once '../config/db.php';
    $stmt = $conn->prepare("
        SELECT p.id 
        FROM products p
        JOIN shops s ON p.shop_id = s.id
        WHERE p.id = ? AND s.user_id = ?
    ");
    $stmt->bind_param("ii", $productId, $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Product not found or not authorized"
        ]);
        die();
    }

    // Delete product
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Product deleted successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to delete product"
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