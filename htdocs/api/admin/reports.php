<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require admin authentication
$user = requireAuth('ADMIN');

// Handle GET request for reports list
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../config/db.php';

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
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request to update report status
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'], $data['status'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing required fields"
        ]);
        die();
    }

    $reportId = intval($data['id']);
    $status = trim($data['status']);

    require_once '../config/db.php';

    $stmt = $conn->prepare("UPDATE reports SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $reportId);

    if ($stmt->execute()) {
        // Get the updated report
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
            WHERE r.id = ?
        ");
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
        $report = $result->fetch_assoc();

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "data" => $report,
            "message" => "Report status updated"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to update report"
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>