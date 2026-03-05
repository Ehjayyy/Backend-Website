<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require authentication
$user = requireAuth();

// Handle GET request for single report
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $reportId = isset($_GET['id']) ? intval($_GET['id']) : null;

    if (!$reportId) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing report ID"
        ]);
        die();
    }

    require_once '../config/db.php';

    $stmt = $conn->prepare("
        SELECT 
            r.id, 
            r.user_id, 
            r.report_type, 
            r.content, 
            r.reported_id, 
            r.status, 
            r.created_at,
            u.name as reporter_name,
            u.email as reporter_email
        FROM reports r
        JOIN users u ON r.user_id = u.id
        WHERE r.id = ? AND (r.user_id = ? OR ? = 'ADMIN')
    ");
    $isAdmin = $user['role'] === 'ADMIN';
    $stmt->bind_param("iii", $reportId, $user['id'], $isAdmin);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Report not found or not authorized"
        ]);
        die();
    }

    $report = $result->fetch_assoc();

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $report
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>