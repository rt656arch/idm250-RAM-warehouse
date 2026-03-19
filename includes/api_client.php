<?php
function api_request($url, $method, $data, $api_key) {
    $options = [
        'http' => [
            'method'        => $method,
            'header'        => "Content-Type: application/json\r\n" .
                                "x-api-key: $api_key\r\n",
            'content'       => json_encode($data),
            'ignore_errors' => true
        ]
    ];
    $context  = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    return json_decode($response, true);
}

function notify_cms_mpl_confirmed($reference_number) {
    $env = require __DIR__ . '/../.env.php';
    return api_request(
        $env['CMS_API_URL'] . '/mpls.php',
        'POST',
        ['action' => 'confirm', 'reference_number' => $reference_number],
        $env['CMS_API_KEY']
    );
}

function notify_cms_order_shipped($order_number, $shipped_at) {
    $env = require __DIR__ . '/../.env.php';
    return api_request(
        $env['CMS_API_URL'] . '/orders.php',
        'POST',
        ['action' => 'ship', 'order_number' => $order_number, 'shipped_at' => $shipped_at],
        $env['CMS_API_KEY']
    );
}
