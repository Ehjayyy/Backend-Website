<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require authentication
$user = requireAuth();

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'], $data['category_id'], $data['name'], $data['price'], $data['stock'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing required fields"
        ]);
        die();
    }

    $productId = intval($data['id']);
    $categoryId = intval($data['category_id']);
    $name = trim($data['name']);
    $price = floatval($data['price']);
    $stock = intval($data['stock']);
    $description = isset($data['description']) ? trim($data['description']) : null;

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

    // Update product
    $stmt = $conn->prepare("
        UPDATE products 
        SET category_id = ?, name = ?, price = ?, stock = ?, description = ? 
        WHERE id = ?
    ");
    $stmt->bind_param("isdssi", $categoryId, $name, $price, $stock, $description, $productId);

    if ($stmt->execute()) {
        // Get the updated product
        $stmt = $conn->prepare("
            SELECT 
                p.id, 
                p.shop_id, 
                p.category_id, 
                p.name, 
                p.price, 
                p.stock, 
                p.description, 
                p.created_at,
                c.name as category_name,
                s.shop_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN shops s ON p.shop_id = s.id
            WHERE p.id = ?
        ");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $product = [
            'id' => $row['id'],
            'shop_id' => $row['shop_id'],
            'category_id' => $row['category_id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'stock' => $row['stock'],
            'description' => $row['description'],
            'created_at' => $row['created_at'],
            'category' => [
                'id' => $row['category_id'],
                'name' => $row['category_name']
            ],
            'shop' => [
                'id' => $row['shop_id'],
                'shop_name' => $row['shop_name']
            ]
        ];

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "data" => $product,
            "message" => "Product updated successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to update product"
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