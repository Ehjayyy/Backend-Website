<?php
require_once '../../../config.php';
require_once '../../../utils/jwt.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT id, name FROM categories');
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $formattedCategories = [];
    foreach ($categories as $category) {
        $formattedCategories[] = [
            'id' => (int)$category['id'],
            'name' => $category['name']
        ];
    }
    
    echo json_encode($formattedCategories);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = validateToken();
    requireRole('ADMIN', $payload['role']);
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['name'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing fields']);
        exit;
    }
    
    $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (?)');
    $stmt->execute([$data['name']]);
    
    $categoryId = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare('SELECT id, name FROM categories WHERE id = ?');
    $stmt->execute([$categoryId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $formattedCategory = [
        'id' => (int)$category['id'],
        'name' => $category['name']
    ];
    
    echo json_encode($formattedCategory);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
