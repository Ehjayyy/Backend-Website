<?php
require_once '../config.php';

function createJWT($payload, $expiry = 604800) {
    global $JWT_SECRET;
    
    $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
    $payload['exp'] = time() + $expiry;
    $payload = json_encode($payload);
    
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $JWT_SECRET, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

function decodeJWT($token) {
    global $JWT_SECRET;
    
    try {
        $tokenParts = explode('.', $token);
        if (count($tokenParts) !== 3) {
            return false;
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $tokenParts;
        
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payloadEncoded)), true);
        
        if (isset($payload['exp']) && time() > $payload['exp']) {
            return false;
        }
        
        $signature = base64_decode(str_replace(['-', '_'], ['+', '/'], $signatureEncoded));
        $expectedSignature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, $JWT_SECRET, true);
        
        if (!hash_equals($signature, $expectedSignature)) {
            return false;
        }
        
        return $payload;
    } catch (Exception $e) {
        return false;
    }
}

function validateToken() {
    $headers = getallheaders();
    
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['message' => 'Authorization header missing']);
        exit;
    }
    
    $authHeader = $headers['Authorization'];
    if (!str_starts_with(strtolower($authHeader), 'bearer ')) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid authorization header format']);
        exit;
    }
    
    $token = substr($authHeader, 7);
    $payload = decodeJWT($token);
    
    if (!$payload) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid or expired token']);
        exit;
    }
    
    return $payload;
}

function requireRole($requiredRole, $userRole) {
    if ($userRole !== $requiredRole && $userRole !== 'ADMIN') {
        http_response_code(403);
        echo json_encode(['message' => 'Forbidden']);
        exit;
    }
}
?>
