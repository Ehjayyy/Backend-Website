<?php
require_once '../config/cors.php';
require_once '../config/db.php';

// Handle GET request for single product
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $productId = isset($_GET['id']) ? intval($_GET['id']) : null;

    if (!$productId) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing product ID"
        ]);
        die();
    }

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

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Product not found"
        ]);
        die();
    }

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
        "data" => $product
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>
