<?php
require_once 'includes/auth.php';
require_once 'includes/db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: sku_index.php');
    exit;
}

$sku_id = intval($_GET['id']);

$conn = getDBConnection();

$sql = "DELETE FROM skus WHERE sku_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sku_id);
$stmt->execute();

$stmt->close();
$conn->close();

header('Location: sku_index.php');
exit;
