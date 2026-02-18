<?php
define('API_REQUEST', true);

header('Content-Type: application/json'); //other team pass info through one of these headers

require_once('../includes/db_connect.php');
require_once('../includes/auth.php');