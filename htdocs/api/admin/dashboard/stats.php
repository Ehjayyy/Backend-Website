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

// Get dashboard stats
$stmt = $pdo->prepare('SELECT COUNT(*) as users FROM users');
$stmt->execute();
$users = $stmt->fetch(PDO::FETCH_ASSOC)['users'];

$stmt = $pdo->prepare('SELECT COUNT(*) as shops FROM shops');
$stmt->execute();
$shops = $stmt->fetch(PDO::FETCH_ASSOC)['shops'];

$stmt = $pdo->prepare('SELECT COUNT(*) as products FROM products');
$stmt->execute();
$products = $stmt->fetch(PDO::FETCH_ASSOC)['products'];

$stmt = $pdo->prepare('SELECT COUNT(*) as orders FROM orders');
$stmt->execute();
$orders = $stmt->fetch(PDO::FETCH_ASSOC)['orders'];

$stmt = $pdo->prepare('SELECT COUNT(*) as reports FROM reports');
$stmt->execute();
$reports = $stmt->fetch(PDO::FETCH_ASSOC)['reports'];

echo json_encode([
    'users' => (int)$users,
    'shops' => (int)$shops,
    'products' => (int)$products,
    'orders' => (int)$orders,
    'reports' => (int)$reports
]);
?>
