<?php
function getDBConnection() {
    $env_vars = [
        'DB_HOST' => $_SERVER['REDIRECT_DB_HOST'] ?? $_SERVER['DB_HOST'] ?? null,
        'DB_USER' => $_SERVER['REDIRECT_DB_USER'] ?? $_SERVER['DB_USER'] ?? null,
        'DB_PASS' => $_SERVER['REDIRECT_DB_PASS'] ?? $_SERVER['DB_PASS'] ?? null,
        'DB_NAME' => $_SERVER['REDIRECT_DB_NAME'] ?? $_SERVER['DB_NAME'] ?? null
    ];

    if (in_array(null, $env_vars, true))
        die('Missing required environment variables');

    define('DB_HOST', $env_vars['DB_HOST']);
    define('DB_USER', $env_vars['DB_USER']);
    define('DB_PASS', $env_vars['DB_PASS']);
    define('DB_NAME', $env_vars['DB_NAME']);

    // Create database connection 
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error)
        die("Connection failed: " . $conn->connect_error);

    return $conn;
}
