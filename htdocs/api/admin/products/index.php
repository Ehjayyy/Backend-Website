<?php
require_once '../../../config.php';
require_once '../../../utils/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

$payload = validateToken();
requireRole('ADMIN', $payload['role']);

$stmt = $pdo->prepare('SELECT p.*, c.name as category_name, s.shop_name FROM products p 
                        JOIN categories c ON p.category_id = c.id 
                        JOIN shops s ON p.shop_id = s.id');
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$formattedProducts = [];
foreach ($products as $product) {
    $formattedProducts[] = [
        'id' => (int)$product['id'],
        'shop_id' => (int)$product['shop_id'],
        'category_id' => (int)$product['category_id'],
        'name' => $product['name'],
        'price' => (float)$product['price'],
        'stock' => (int)$product['stock'],
        'description' => $product['description'],
        'created_at' => $product['created_at'],
        'category' => [
            'id' => (int)$product['category_id'],
            'name' => $product['category_name']
        ],
        'shop' => [
            'id' => (int)$product['shop_id'],
            'shop_name' => $product['shop_name']
        ]
    ];
}

echo json_encode($formattedProducts);
?>
