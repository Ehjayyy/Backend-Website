<?php
require_once '../../../config.php';
require_once '../../../utils/jwt.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));
$id = (int)end($segments);

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $payload = validateToken();
    requireRole('ADMIN', $payload['role']);
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['name'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing fields']);
        exit;
    }
    
    $stmt = $pdo->prepare('UPDATE categories SET name = ? WHERE id = ?');
    $stmt->execute([$data['name'], $id]);
    
    $stmt = $pdo->prepare('SELECT id, name FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $formattedCategory = [
        'id' => (int)$category['id'],
        'name' => $category['name']
    ];
    
    echo json_encode($formattedCategory);
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $payload = validateToken();
    requireRole('ADMIN', $payload['role']);
    
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    
    http_response_code(204);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
