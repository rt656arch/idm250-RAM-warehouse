<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

//checking config, headers, keys  
function check_api_key($env) {
    $valid_key = $env['X-API-KEY'];
    $provided_key = null; //this comes from the other team, through headers
    $headers = getallheaders();

    foreach ($headers as $name => $value) {
        if (strtolower($name) === 'x-api-key') { //inbetween the data is going from the other team to the server to me, linux sends everything as lowercase, so not knowing if the server is running linux mac or windows, we need to be safe and convert to lowercase
            $provided_key = $value; 
            break;
        }
    }

    if ($provided_key !== $valid_key) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized', 'details' => 'Invalid API Key']);
        exit();
    }
}