<?php
require_once '../../../config.php';
require_once '../../../utils/jwt.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));
$id = (int)end($segments);

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $payload = validateToken();
    requireRole('ADMIN', $payload['role']);
    
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
    
    http_response_code(204);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
