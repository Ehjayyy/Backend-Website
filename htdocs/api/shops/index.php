<?php
require_once '../config/cors.php';
require_once '../config/db.php';

// Handle GET request for shops list
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = isset($_GET['search']) ? trim($_GET['search']) : null;

    $sql = "
        SELECT 
            id, 
            user_id, 
            shop_name, 
            description, 
            created_at 
        FROM shops 
        WHERE 1 = 1
    ";

    $params = [];
    $types = '';

    if ($search) {
        $sql .= " AND (shop_name LIKE ? OR description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'ss';
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $shops = [];
    while ($row = $result->fetch_assoc()) {
        $shops[] = $row;
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $shops
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>
