<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/api_client.php';
require_once __DIR__ . '/includes/log.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'] ?? 0;
    $record   = get_order($order_id);

    if ($record['status'] == 'closed') {
        header('Location: orders.php?notice=already_shipped', true, 303);
        exit;
    }

    $line_items = get_order_items($order_id);
    $shipped_at = date('Y-m-d');

    record_shipped_items($order_id, $record['order_number'], $line_items, $shipped_at);

    foreach ($line_items as $item) {
        remove_inventory_unit($item['unit_id']);
    }

    ship_order($order_id, $shipped_at);
    notify_cms_order_shipped($record['order_number'], $shipped_at);
    log_event("Order " . $record['order_number'] . " shipped — " . count($line_items) . " units removed from stock");

    header('Location: orders.php?notice=shipped', true, 303);
    exit;
}

$notice  = $_GET['notice'] ?? '';
$records = get_orders();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders — RAM WMS</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-title">Orders</div>
    <div class="page-subtitle">Orders received from the CMS</div>

    <?php if ($notice == 'shipped') { ?>
        <div class="alert alert-success">Order shipped. Inventory updated and CMS notified.</div>
    <?php } ?>
    <?php if ($notice == 'already_shipped') { ?>
        <div class="alert alert-error">That order has already been shipped.</div>
    <?php } ?>

    <div class="toolbar">
        <div class="toolbar-left"><?php echo count($records); ?> order(s)</div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Ship To</th>
                    <th>City / State</th>
                    <th>Shipped</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$records) { ?>
                <tr class="empty-row"><td colspan="6">No orders received yet.</td></tr>
            <?php } ?>
            <?php foreach ($records as $row) { ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['order_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['ship_to_company']); ?></td>
                    <td>
                        <?php echo htmlspecialchars($row['ship_to_city']); ?>,
                        <?php echo htmlspecialchars($row['ship_to_state']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['shipped_at'] ?: '—'); ?></td>
                    <td>
                        <?php if ($row['status'] == 'open') { ?>
                            <span class="badge badge-open">Open</span>
                        <?php } else { ?>
                            <span class="badge badge-closed">Shipped</span>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="order_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-view">View Items</a>
                        <?php if ($row['status'] == 'open') { ?>
                            <form method="POST" action="orders.php" style="display:inline">
                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-success"
                                        onclick="return confirm('Ship this order and notify the CMS?')">Ship</button>
                            </form>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
