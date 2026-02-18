<?php
define('API_REQUEST', true);

require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

check_api_key($env);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        exit();
    }

    $stmt = $connection->prepare("INSERT INTO skus (ficha, sku, description, uom_primary, piece_count, length_inches, width_inches, height_inches, weight_lbs, assembly, rate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isssiiddddi', 
        $data['ficha'],
        $data['sku'],
        $data['description'],
        $data['uom_primary'],
        $data['piece_count'],
        $data['length_inches'],
        $data['width_inches'],
        $data['height_inches'],
        $data['weight_lbs'],
        $data['assembly'],
        $data['rate']
    );

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Server Error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}