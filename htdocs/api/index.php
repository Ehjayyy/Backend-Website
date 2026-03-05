<?php
// Main API router

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remove /api prefix
$path = preg_replace('#^/api#', '', $path);
$path = trim($path, '/');

// Split into segments
$segments = explode('/', $path);

// Dispatch request
switch ($segments[0]) {
    case 'auth':
        if (count($segments) < 2) {
            http_response_code(404);
            echo json_encode(['message' => 'Not found']);
            exit;
        }
        $endpoint = $segments[1];
        switch ($endpoint) {
            case 'register':
                require_once 'auth/register.php';
                break;
            case 'login':
                require_once 'auth/login.php';
                break;
            case 'me':
                require_once 'auth/me.php';
                break;
            default:
                http_response_code(404);
                echo json_encode(['message' => 'Not found']);
                exit;
        }
        break;
    case 'products':
        if (count($segments) < 2) {
            require_once 'products/index.php';
            break;
        }
        $endpoint = $segments[1];
        if ($endpoint === 'shop') {
            require_once 'products/shop.php';
        } elseif (is_numeric($endpoint)) {
            require_once 'products/[id].php';
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Not found']);
            exit;
        }
        break;
    case 'categories':
        require_once 'categories/index.php';
        break;
    case 'shops':
        if (count($segments) < 2) {
            require_once 'shops/index.php';
            break;
        }
        $endpoint = $segments[1];
        if ($endpoint === 'me') {
            require_once 'shops/me.php';
        } elseif (is_numeric($endpoint)) {
            require_once 'shops/[id].php';
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Not found']);
            exit;
        }
        break;
    case 'orders':
        require_once 'orders/index.php';
        break;
    case 'reports':
        if (count($segments) < 2) {
            require_once 'reports/index.php';
            break;
        }
        $endpoint = $segments[1];
        if ($endpoint === 'me') {
            require_once 'reports/me.php';
        } elseif (is_numeric($endpoint)) {
            require_once 'reports/[id].php';
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Not found']);
            exit;
        }
        break;
    case 'payments':
        if (count($segments) < 2) {
            require_once 'payments/index.php';
            break;
        }
        $endpoint = $segments[1];
        if ($endpoint === 'order' && count($segments) === 3 && is_numeric($segments[2])) {
            require_once 'payments/order/[orderId].php';
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Not found']);
            exit;
        }
        break;
    case 'admin':
        if (count($segments) < 2) {
            http_response_code(404);
            echo json_encode(['message' => 'Not found']);
            exit;
        }
        $resource = $segments[1];
        switch ($resource) {
            case 'dashboard':
                if (count($segments) === 3 && $segments[2] === 'stats') {
                    require_once 'admin/dashboard/stats.php';
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Not found']);
                    exit;
                }
                break;
            case 'users':
                if (count($segments) === 2) {
                    require_once 'admin/users/index.php';
                } elseif (count($segments) === 3 && is_numeric($segments[2])) {
                    require_once 'admin/users/[id].php';
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Not found']);
                    exit;
                }
                break;
            case 'shops':
                if (count($segments) === 2) {
                    require_once 'admin/shops/index.php';
                } elseif (count($segments) === 3 && is_numeric($segments[2])) {
                    require_once 'admin/shops/[id].php';
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Not found']);
                    exit;
                }
                break;
            case 'products':
                if (count($segments) === 2) {
                    require_once 'admin/products/index.php';
                } elseif (count($segments) === 3 && is_numeric($segments[2])) {
                    require_once 'admin/products/[id].php';
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Not found']);
                    exit;
                }
                break;
            case 'reports':
                if (count($segments) === 2) {
                    require_once 'admin/reports/index.php';
                } elseif (count($segments) === 3 && is_numeric($segments[2])) {
                    require_once 'admin/reports/[id].php';
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Not found']);
                    exit;
                }
                break;
            case 'orders':
                require_once 'admin/orders/index.php';
                break;
            case 'categories':
                if (count($segments) === 2) {
                    require_once 'admin/categories/index.php';
                } elseif (count($segments) === 3 && is_numeric($segments[2])) {
                    require_once 'admin/categories/[id].php';
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Not found']);
                    exit;
                }
                break;
            default:
                http_response_code(404);
                echo json_encode(['message' => 'Not found']);
                exit;
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Not found']);
        exit;
}
?>
