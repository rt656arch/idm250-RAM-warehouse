<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$stock = get_inventory();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory — RAM WMS</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-title">Current Inventory</div>
    <div class="page-subtitle">Units stocked when MPLs are confirmed. Removed when orders ship.</div>

    <div class="toolbar">
        <div class="toolbar-left"><?php echo count($stock); ?> unit(s) in stock</div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Unit ID</th>
                    <th>SKU</th>
                    <th>Description</th>
                    <th>UOM</th>
                    <th>Pcs</th>
                    <th>L &times; W &times; H (in)</th>
                    <th>Wt (lbs)</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!$stock) { ?>
                <tr class="empty-row"><td colspan="7">No units in stock — confirm an MPL to populate inventory.</td></tr>
            <?php } ?>
            
            <?php foreach ($stock as $unit) { ?>
                <tr>
                    <td><span class="chip"><?php echo htmlspecialchars($unit['unit_id']); ?></span></td>
                    <td><?php echo htmlspecialchars($unit['sku_code']); ?></td>
                    <td><?php echo htmlspecialchars($unit['description']); ?></td>
                    <td><?php echo htmlspecialchars($unit['uom_primary']); ?></td>
                    <td><?php echo htmlspecialchars($unit['piece_count']); ?></td>
                    <td>
                        <?php echo $unit['length_inches']; ?> &times;
                        <?php echo $unit['width_inches']; ?> &times;
                        <?php echo $unit['height_inches']; ?>
                    </td>
                    <td><?php echo htmlspecialchars($unit['weight_lbs']); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
