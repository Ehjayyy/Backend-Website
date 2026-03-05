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
    
    if (!isset($data['status'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing fields']);
        exit;
    }
    
    $stmt = $pdo->prepare('UPDATE reports SET status = ? WHERE id = ?');
    $stmt->execute([$data['status'], $id]);
    
    $stmt = $pdo->prepare('SELECT * FROM reports WHERE id = ?');
    $stmt->execute([$id]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $formattedReport = [
        'id' => (int)$report['id'],
        'user_id' => (int)$report['user_id'],
        'target_type' => $report['target_type'],
        'target_id' => (int)$report['target_id'],
        'reason' => $report['reason'],
        'created_at' => $report['created_at'],
        'status' => $report['status']
    ];
    
    echo json_encode($formattedReport);
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $payload = validateToken();
    requireRole('ADMIN', $payload['role']);
    
    $stmt = $pdo->prepare('DELETE FROM reports WHERE id = ?');
    $stmt->execute([$id]);
    
    http_response_code(204);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
