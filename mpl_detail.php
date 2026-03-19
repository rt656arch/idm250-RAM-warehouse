<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$id         = $_GET['id'] ?? 0;
$record     = get_mpl($id);
$line_items = get_mpl_items($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MPL Detail — RAM WMS</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-title"><?php echo htmlspecialchars($record['reference_number']); ?></div>
    <div class="page-subtitle">
        <a href="mpls.php">&larr; Back to MPLs</a>
        &nbsp;&mdash;&nbsp; Trailer: <?php echo htmlspecialchars($record['trailer_number'] ?? '—'); ?>
        &nbsp;&mdash;&nbsp; Expected: <?php echo htmlspecialchars($record['expected_arrival'] ?: '—'); ?>
        &nbsp;&mdash;&nbsp;
        <?php if ($record['status'] == 'open') { ?>
            <span class="badge badge-open">Open</span>
        <?php } else { ?>
            <span class="badge badge-closed">Confirmed</span>
        <?php } ?>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Unit ID</th>
                    <th>SKU</th>
                    <th>Description</th>
                    <th>UOM</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$line_items) { ?>
                <tr class="empty-row"><td colspan="4">No line items in this MPL.</td></tr>
            <?php } ?>
            <?php foreach ($line_items as $item) { ?>
                <tr>
                    <td><span class="chip"><?php echo htmlspecialchars($item['unit_id']); ?></span></td>
                    <td><?php echo htmlspecialchars($item['sku']); ?></td>
                    <td><?php echo htmlspecialchars($item['description'] ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($item['uom_primary'] ?? '—'); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
