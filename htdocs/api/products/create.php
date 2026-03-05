<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require authentication
$user = requireAuth();

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['category_id'], $data['name'], $data['price'], $data['stock'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing required fields"
        ]);
        die();
    }

    $categoryId = intval($data['category_id']);
    $name = trim($data['name']);
    $price = floatval($data['price']);
    $stock = intval($data['stock']);
    $description = isset($data['description']) ? trim($data['description']) : null;

    // Check if user has a shop
    require_once '../config/db.php';
    $stmt = $conn->prepare("SELECT id FROM shops WHERE user_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $shopResult = $stmt->get_result();

    if ($shopResult->num_rows === 0) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "You must create a shop first"
        ]);
        die();
    }

    $shop = $shopResult->fetch_assoc();

    // Create product
    $stmt = $conn->prepare("
        INSERT INTO products (shop_id, category_id, name, price, stock, description, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("iisdss", $shop['id'], $categoryId, $name, $price, $stock, $description);

    if ($stmt->execute()) {
        $productId = $conn->insert_id;

        // Get the created product
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

        http_response_code(201);
        echo json_encode([
            "success" => true,
            "data" => $product,
            "message" => "Product created successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to create product"
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