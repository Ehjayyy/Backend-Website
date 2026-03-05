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

$stmt = $pdo->prepare('SELECT o.*, u.name as user_name FROM orders o 
                        JOIN users u ON o.user_id = u.id');
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$formattedOrders = [];
foreach ($orders as $order) {
    $stmtItems = $pdo->prepare('SELECT oi.*, p.name as product_name FROM order_items oi 
                                JOIN products p ON oi.product_id = p.id 
                                WHERE oi.order_id = ?');
    $stmtItems->execute([$order['id']]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
    
    $formattedItems = [];
    foreach ($items as $item) {
        $formattedItems[] = [
            'id' => (int)$item['id'],
            'order_id' => (int)$item['order_id'],
            'product_id' => (int)$item['product_id'],
            'quantity' => (int)$item['quantity'],
            'price' => (float)$item['price'],
            'product' => [
                'id' => (int)$item['product_id'],
                'name' => $item['product_name']
            ]
        ];
    }
    
    $formattedOrders[] = [
        'id' => (int)$order['id'],
        'user_id' => (int)$order['user_id'],
        'order_date' => $order['order_date'],
        'status' => $order['status'],
        'total_amount' => (float)$order['total_amount'],
        'items' => $formattedItems
    ];
}

echo json_encode($formattedOrders);
?>
