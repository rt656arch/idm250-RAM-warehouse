<?php
ob_start();
define('API_REQUEST', true);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, x-api-key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once dirname(dirname(__DIR__)) . '/includes/db.php';
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
require_once dirname(dirname(__DIR__)) . '/includes/log.php';

ob_end_clean();

$env = require dirname(dirname(__DIR__)) . '/.env.php';
check_api_key($env);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['sku']) || empty($data['description'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad Request', 'details' => 'Missing required fields: sku, description']);
    exit;
}

$existing = get_sku_by_code($data['sku']);

if ($existing) {
    save_sku($existing['id'], $data);
    log_event("SKU " . $data['sku'] . " updated via CMS sync");
    echo json_encode(['success' => true, 'message' => 'SKU updated']);
} else {
    $id = insert_sku($data);
    log_event("SKU " . $data['sku'] . " created via CMS sync");
    http_response_code(201);
    echo json_encode(['success' => true, 'id' => $id]);
}
