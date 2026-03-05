<?php
require_once '../config/cors.php';
require_once '../config/db.php';

// Handle GET request for reports list (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../config/jwt.php';
    $user = requireAuth('ADMIN');

    $sql = "
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
        ORDER BY r.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $reports = [];
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $reports
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>
