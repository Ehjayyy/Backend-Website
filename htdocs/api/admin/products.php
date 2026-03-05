<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require admin authentication
$user = requireAuth('ADMIN');

// Handle GET request for products list
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../config/db.php';

    $sql = "
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
        ORDER BY p.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = [
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
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $products
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>