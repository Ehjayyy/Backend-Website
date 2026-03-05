<?php
require_once '../config/cors.php';
require_once '../config/db.php';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Missing required fields"
        ]);
        die();
    }

    $email = trim($data['email']);
    $password = $data['password'];

    // Get user by email
    $stmt = $conn->prepare("SELECT id, name, email, password, role, created_at FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "error" => "Invalid email or password"
        ]);
        die();
    }

    $user = $result->fetch_assoc();

    // Verify password
    if (!password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "error" => "Invalid email or password"
        ]);
        die();
    }

    // Create JWT token
    require_once '../config/jwt.php';
    $token = createJWT(['userId' => $user['id']]);

    // Remove password from response
    unset($user['password']);

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => [
            "user" => $user,
            "token" => $token
        ],
        "message" => "Login successful"
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Method not allowed"
    ]);
}
?>
