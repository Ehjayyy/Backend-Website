<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require authentication
$user = requireAuth();

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['report_type'], $data['content'], $data['reported_id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing required fields"
        ]);
        die();
    }

    $reportType = trim($data['report_type']);
    $content = trim($data['content']);
    $reportedId = intval($data['reported_id']);

    require_once '../config/db.php';

    // Create report
    $stmt = $conn->prepare("
        INSERT INTO reports (user_id, report_type, content, reported_id, status, created_at) 
        VALUES (?, ?, ?, ?, 'pending', NOW())
    ");
    $stmt->bind_param("issi", $user['id'], $reportType, $content, $reportedId);

    if ($stmt->execute()) {
        $reportId = $conn->insert_id;

        // Get the created report
        $stmt = $conn->prepare("
            SELECT 
                id, 
                user_id, 
                report_type, 
                content, 
                reported_id, 
                status, 
                created_at 
            FROM reports 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
        $report = $result->fetch_assoc();

        http_response_code(201);
        echo json_encode([
            "success" => true,
            "data" => $report,
            "message" => "Report created successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to create report"
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