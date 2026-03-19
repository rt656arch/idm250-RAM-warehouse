<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$id     = $_GET['id'] ?? 0;
$record = get_order($id);

if ($record['status'] == 'closed') {
    $line_items = get_shipped_items($id);
} else {
    $line_items = get_order_items($id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Detail — RAM WMS</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-title"><?php echo htmlspecialchars($record['order_number']); ?></div>
    <div class="page-subtitle">
        <a href="orders.php">&larr; Back to Orders</a>
        &nbsp;&mdash;&nbsp;
        <?php echo htmlspecialchars($record['ship_to_company']); ?>,
        <?php echo htmlspecialchars($record['ship_to_city']); ?>
        <?php echo htmlspecialchars($record['ship_to_state']); ?>
        <?php if ($record['shipped_at']) { ?>
            &nbsp;&mdash;&nbsp; Shipped: <?php echo htmlspecialchars($record['shipped_at']); ?>
        <?php } ?>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Unit ID</th>
                    <th>SKU</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$line_items) { ?>
                <tr class="empty-row"><td colspan="3">No line items in this order.</td></tr>
            <?php } ?>
            <?php foreach ($line_items as $item) { ?>
                <tr>
                    <td><span class="chip"><?php echo htmlspecialchars($item['unit_id']); ?></span></td>
                    <td><?php echo htmlspecialchars($item['sku']); ?></td>
                    <td><?php echo htmlspecialchars($item['description'] ?? ($item['sku_description'] ?? '—')); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
