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

if (!$data['reference_number'] || !$data['items']) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad Request', 'details' => 'Missing reference_number or items']);
    exit;
}

if (get_mpl_by_reference($data['reference_number'])) {
    http_response_code(409);
    echo json_encode(['error' => 'Conflict', 'details' => 'MPL already exists']);
    exit;
}

// if sku exists in wms, continue
// If sku doesn't exist but sku_details were provided, create it
// If sku doesn't exist and no sku_details, reject
$missing_skus = [];

foreach ($data['items'] as $item) {
    if (!$item['sku']) continue;
    if (get_sku_by_code($item['sku'])) continue;

    if (!empty($item['sku_details'])) {
        $sku_data        = $item['sku_details'];
        $sku_data['sku'] = $item['sku'];
        insert_sku($sku_data);
        log_event("Auto-created SKU " . $item['sku'] . " from incoming MPL");
    } else {
        $missing_skus[] = $item['sku'];
    }
}

if (!empty($missing_skus)) {
    http_response_code(400);
    echo json_encode([
        'error'   => 'Bad Request',
        'details' => 'Missing SKUs in WMS: ' . implode(', ', $missing_skus) . '. Provide full SKU details to auto-create.'
    ]);
    exit;
}

$mpl_id = save_mpl($data, $data['items']);
log_event("MPL " . $data['reference_number'] . " received from CMS");
echo json_encode(['success' => true, 'message' => 'MPL received', 'mpl_id' => $mpl_id]);
