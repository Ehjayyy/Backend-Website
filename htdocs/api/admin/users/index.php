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

$stmt = $pdo->prepare('SELECT u.*, s.id as shop_id, s.shop_name FROM users u 
                        LEFT JOIN shops s ON u.id = s.user_id');
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$formattedUsers = [];
foreach ($users as $user) {
    $formattedUsers[] = [
        'id' => (int)$user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
        'created_at' => $user['created_at'],
        'shops' => $user['shop_id'] ? [
            'id' => (int)$user['shop_id'],
            'shop_name' => $user['shop_name']
        ] : []
    ];
}

echo json_encode($formattedUsers);
?>
