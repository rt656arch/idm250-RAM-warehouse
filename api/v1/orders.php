<?php
ob_start();
define('API_REQUEST', true);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, x-api-key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

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

if (!$data['order_number'] || !$data['items']) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad Request', 'details' => 'Missing order_number or items']);
    exit;
}

if (get_order_by_number($data['order_number'])) {
    http_response_code(409);
    echo json_encode(['error' => 'Conflict', 'details' => 'Order already exists']);
    exit;
}

$stock        = get_inventory();
$stock_ids    = array_column($stock, 'unit_id');
$missing      = [];
foreach ($data['items'] as $item) {
    if (!in_array($item['unit_id'], $stock_ids)) {
        $missing[] = $item['unit_id'];
    }
}
if ($missing) {
    http_response_code(400);
    echo json_encode([
        'error'   => 'Bad Request',
        'details' => 'Units not in WMS inventory: ' . implode(', ', $missing)
    ]);
    exit;
}

$order_items = [];
foreach ($data['items'] as $item) {
    $uid = $item['unit_id'];
    $sku = $item['sku'] ?? '';
    if (!$sku) {
        foreach ($stock as $unit) {
            if ($unit['unit_id'] == $uid) {
                $sku = $unit['sku_code'];
                break;
            }
        }
    }
    $order_items[] = ['unit_id' => $uid, 'sku' => $sku];
}

$order_id = save_order($data, $order_items);
log_event("Order " . $data['order_number'] . " received from CMS");
echo json_encode(['success' => true, 'message' => 'Order received', 'order_id' => $order_id]);
