<?php

require_once 'detail.php';


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

    $detail = new Detail();

    switch ($action) {
        case 'get':

            $field_id = filter_input(INPUT_POST, 'field_id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_GET, 'field_id', FILTER_VALIDATE_INT);
            
            if (!$field_id) {
                sendJson(['success' => false, 'message' => 'field_id required'], 400);
            }

            $data = $detail->getFieldDetail($field_id);

            sendJson([
                'success' => true,
                'data' => $data,
            ]);
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
