<?php
if (!defined('API_REQUEST') && session_status() === PHP_SESSION_NONE) {
    session_start();
}

function login_user($username, $password) {
    global $connection;
    $stmt = $connection->prepare('SELECT * FROM ram_users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        return true;
    }
    return false;
}

function check_api_key($env) {
    $headers = getallheaders();
    $headers = array_change_key_case($headers, CASE_LOWER);
    if (($headers['x-api-key'] ?? '') != $env['API_KEY']) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}

function is_logged_in() {
    return $_SESSION['user_id'] != '';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
