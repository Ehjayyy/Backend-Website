<?php
require_once 'db.php';

// JWT configuration
define('JWT_SECRET', 'your_jwt_secret_key');
define('JWT_EXPIRATION', 3600 * 24); // 24 hours

function createJWT($payload) {
    $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
    $payload['exp'] = time() + JWT_EXPIRATION;
    $payload['iat'] = time();
    $payload = json_encode($payload);

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, JWT_SECRET, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

function verifyJWT($token) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return false;
    }

    list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;

    $expectedSignature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, JWT_SECRET, true);
    $expectedBase64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));

    if (!hash_equals($expectedBase64UrlSignature, $base64UrlSignature)) {
        return false;
    }

    $payload = json_decode(base64_decode($base64UrlPayload), true);

    if (!isset($payload['exp']) || $payload['exp'] < time()) {
        return false;
    }

    return $payload;
}

function getCurrentUser() {
    $headers = getallheaders();
    $authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    
    if (preg_match('/^Bearer\s+(.*)$/', $authorization, $matches)) {
        $token = $matches[1];
        $payload = verifyJWT($token);
        
        if ($payload && isset($payload['userId'])) {
            global $conn;
            $userId = $payload['userId'];
            
            $stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
        }
    }
    
    return null;
}

function requireAuth($requiredRole = null) {
    $user = getCurrentUser();
    
    if (!$user) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "error" => "Unauthorized"
        ]);
        die();
    }
    
    if ($requiredRole && $user['role'] !== $requiredRole) {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "error" => "Forbidden"
        ]);
        die();
    }
    
    return $user;
}
?>