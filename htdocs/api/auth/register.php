<?php
require_once '../config/cors.php';
require_once '../config/db.php';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name'], $data['email'], $data['password'], $data['role'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing required fields"
        ]);
        die();
    }

    $name = trim($data['name']);
    $email = trim($data['email']);
    $password = $data['password'];
    $role = strtoupper($data['role']);

    // Validate role
    if (!in_array($role, ['BUYER', 'SELLER', 'ADMIN'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Invalid role"
        ]);
        die();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "error" => "Email already exists"
        ]);
        die();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Create user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

    if ($stmt->execute()) {
        $userId = $conn->insert_id;
        
        // Create JWT token
        require_once '../config/jwt.php';
        $token = createJWT(['userId' => $userId]);

        // Get user data without password
        $stmt = $conn->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        http_response_code(201);
        echo json_encode([
            "success" => true,
            "data" => [
                "user" => $user,
                "token" => $token
            ],
            "message" => "User registered successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to register user"
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
