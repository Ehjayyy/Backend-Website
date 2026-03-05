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

$stmt = $pdo->prepare('SELECT * FROM shops');
$stmt->execute();
$shops = $stmt->fetchAll(PDO::FETCH_ASSOC);

$formattedShops = [];
foreach ($shops as $shop) {
    $formattedShops[] = [
        'id' => (int)$shop['id'],
        'user_id' => (int)$shop['user_id'],
        'shop_name' => $shop['shop_name'],
        'description' => $shop['description'],
        'created_at' => $shop['created_at']
    ];
}

echo json_encode($formattedShops);
?>
