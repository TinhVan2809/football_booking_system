<?php

require_once 'service.php';


/**
 * Handle CORS (Cross-Origin Resource Sharing)
 */
function handleCORS()
{
    $allowedOrigins = [
        'http://localhost:3000',
        'http://localhost:5173',
        'http://127.0.0.1:3000',
        'http://127.0.0.1:5173',
        'http://localhost:5174',
        'http://localhost:5175',
    ];

    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    if (in_array($origin, $allowedOrigins, true)) {
        header('Access-Control-Allow-Origin: ' . $origin);
    }

    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

handleCORS();

function sendJson($payload, int $status = 200)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

header('Content-Type: application/json');


try {
    $action = $_REQUEST['action'] ?? null;
    $action = is_string($action) ? trim(filter_var($action, FILTER_SANITIZE_FULL_SPECIAL_CHARS)) : null;

    if (!$action) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No action specified']);
        exit();
    }

    $services = new Service();

    switch ($action) {
        case 'get':

            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]) ?: 1;
            $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT, ['options' => ['default' => 25, 'min_range' => 1]]) ?: 25;
            $branch_id = filter_input(INPUT_POST, 'branch_id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_GET, 'branch_id', FILTER_VALIDATE_INT);

            $offset = ($page - 1) * $limit;
            $totalItems = $services->coutServicesBybranch($branch_id);
            $totalPages = ceil($totalItems / $limit);
            $data = $services->getServicesByBranchId($branch_id, $limit, $offset);

            sendJson([
                'success' => true,
                'data' => $data,
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
                'current_page' => $page,
                'limit' => $limit
            ]);
            break;

        case 'delete':
            $branch_id = filter_input(INPUT_POST, 'branch_id', FILTER_VALIDATE_INT);
            $service_id = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
            

            if (!$branch_id) {
                sendJson(['success' => false, 'message' => 'Invalid branch_id ID'], 400);
            }
            if (!$service_id) {
                sendJson(['success' => false, 'message' => 'Invalid service_id ID'], 400);
            }

            $result = $services->deleteService($service_id, $branch_id);

            if ($result) {
                sendJson(['success' => true, 'message' => 'Xóa dịch vụ thành công']);
            } else {
                sendJson(['success' => false, 'message' => 'Không thể xóa dịch vụ'], 400);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (PDOException $e) {
    error_log('General Error in Cart_api_endpoint.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
}
