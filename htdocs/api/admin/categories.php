<?php
require_once '../config/cors.php';
require_once '../config/jwt.php';

// Require admin authentication
$user = requireAuth('ADMIN');

// Handle GET request for categories list
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once '../config/db.php';

    $sql = "
        SELECT 
            id, 
            name 
        FROM categories 
        ORDER BY name ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $categories
    ]);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request to create category
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing required fields"
        ]);
        die();
    }

    $name = trim($data['name']);

    require_once '../config/db.php';

    // Check if category already exists
    $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "error" => "Category already exists"
        ]);
        die();
    }

    // Create category
    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        $categoryId = $conn->insert_id;

        $category = [
            'id' => $categoryId,
            'name' => $name
        ];

        http_response_code(201);
        echo json_encode([
            "success" => true,
            "data" => $category,
            "message" => "Category created successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to create category"
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