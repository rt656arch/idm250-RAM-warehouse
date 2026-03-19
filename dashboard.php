<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$result    = $connection->query('SELECT COUNT(*) AS c FROM ram_skus');
$sku_count = $result->fetch_assoc()['c'];

$result    = $connection->query('SELECT COUNT(*) AS c FROM ram_inventory');
$inv_count = $result->fetch_assoc()['c'];

$result   = $connection->query("SELECT COUNT(*) AS c FROM ram_mpls WHERE status = 'open'");
$mpl_open = $result->fetch_assoc()['c'];

$result     = $connection->query("SELECT COUNT(*) AS c FROM ram_mpls WHERE status = 'closed'");
$mpl_closed = $result->fetch_assoc()['c'];

$result     = $connection->query("SELECT COUNT(*) AS c FROM ram_orders WHERE status = 'open'");
$order_open = $result->fetch_assoc()['c'];

$history       = get_shipped_summary();
$shipped_count = count($history);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — RAM WMS</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-title">Dashboard</div>
    <div class="page-subtitle">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></div>

    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-num"><?php echo $sku_count; ?></div>
            <div class="stat-label">SKUs</div>
        </div>

        <div class="stat-box highlight">
            <div class="stat-num"><?php echo $inv_count; ?></div>
            <div class="stat-label">In Stock</div>
        </div>

        <div class="stat-box">
            <div class="stat-num"><?php echo $mpl_open; ?></div>
            <div class="stat-label">Open MPLs</div>
        </div>

        <div class="stat-box">
            <div class="stat-num"><?php echo $mpl_closed; ?></div>
            <div class="stat-label">Confirmed MPLs</div>
        </div>

        <div class="stat-box">
            <div class="stat-num"><?php echo $order_open; ?></div>
            <div class="stat-label">Open Orders</div>
        </div>

        <div class="stat-box highlight">
            <div class="stat-num"><?php echo $shipped_count; ?></div>
            <div class="stat-label">Shipped Orders</div>
        </div>

    </div>

</div>
</body>
</html>
