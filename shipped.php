<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$history = get_shipped_summary();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipped History — RAM WMS</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-title">Shipped History</div>
    <div class="page-subtitle">All fulfilled orders</div>

    <div class="toolbar">
        <div class="toolbar-left"><?php echo count($history); ?> shipped order(s)</div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Ship To</th>
                    <th>City / State</th>
                    <th>Units</th>
                    <th>Shipped</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
            <?php if (!$history) { ?>
                <tr class="empty-row"><td colspan="6">No shipped orders yet.</td></tr>
            <?php } ?>

            <?php foreach ($history as $row) { ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['order_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['ship_to_company']); ?></td>
                    <td>
                        <?php echo htmlspecialchars($row['ship_to_city']); ?>,
                        <?php echo htmlspecialchars($row['ship_to_state']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['item_count']); ?></td>
                    <td><?php echo htmlspecialchars($row['shipped_at']); ?></td>
                    <td><a href="order_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-view">View</a></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
