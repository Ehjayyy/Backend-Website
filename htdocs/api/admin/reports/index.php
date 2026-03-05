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

$stmt = $pdo->prepare('SELECT * FROM reports');
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

$formattedReports = [];
foreach ($reports as $report) {
    $formattedReports[] = [
        'id' => (int)$report['id'],
        'user_id' => (int)$report['user_id'],
        'target_type' => $report['target_type'],
        'target_id' => (int)$report['target_id'],
        'reason' => $report['reason'],
        'created_at' => $report['created_at'],
        'status' => $report['status']
    ];
}

echo json_encode($formattedReports);
?>
