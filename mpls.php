<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/api_client.php';
require_once __DIR__ . '/includes/log.php';

$id         = $_GET['id'] ?? 0;
$record     = get_mpl($id);
$line_items = get_mpl_items($id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mpl_id = $_POST['mpl_id'] ?? 0;
    $record = get_mpl($mpl_id);

    if ($record['status'] == 'closed') {
        header('Location: mpls.php?notice=already_confirmed', true, 303);
        exit;
    }

    $line_items = get_mpl_items($mpl_id);
    foreach ($line_items as $item) {
        add_inventory_unit($item['unit_id'], $item['sku']);
    }
    close_mpl($mpl_id);
    notify_cms_mpl_confirmed($record['reference_number']);
    log_event("MPL " . $record['reference_number'] . " confirmed — " . count($line_items) . " units stocked");

    header('Location: mpls.php?notice=confirmed', true, 303);
    exit;
}

$notice  = $_GET['notice'] ?? '';
$records = get_mpls();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MPL Records — RAM WMS</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-title">MPL Records</div>
    <div class="page-subtitle">Material Packing Lists received from the CMS</div>

    <?php if ($notice == 'confirmed') { ?>
        <div class="alert alert-success">MPL confirmed. Units have been added to inventory and CMS has been notified.</div>
    <?php } ?>
    <?php if ($notice == 'already_confirmed') { ?>
        <div class="alert alert-error">That MPL has already been confirmed.</div>
    <?php } ?>

    <div class="toolbar">
        <div class="toolbar-left"><?php echo count($records); ?> MPL(s) on record</div>
    </div>

    <div class="table-wrap mpl-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Reference #</th>
                    <th>Trailer #</th>
                    <th>Expected Arrival</th>
                    <th>Received</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$records) { ?>
                <tr class="empty-row"><td colspan="6">No MPLs received yet.</td></tr>
            <?php } ?>
            <?php foreach ($records as $row) { ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['reference_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['trailer_number'] ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($row['expected_arrival'] ?: '—'); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <?php if ($row['status'] == 'open') { ?>
                            <span class="badge badge-open">Open</span>
                        <?php } else { ?>
                            <span class="badge badge-closed">Confirmed</span>
                        <?php } ?>
                    </td>
                    <td>
                        <?php $row_items = get_mpl_items($row['id']); ?>
                        <div class="row-actions">
                            <button class="btn btn-view btn-view-items" type="button" data-target="items-<?php echo $row['id']; ?>">View Items</button>

                            <?php if ($row['status'] == 'open') { ?>
                                <form method="POST" action="mpls.php" style="display:inline">
                                    <input type="hidden" name="mpl_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-success"
                                            onclick="return confirm('Confirm this MPL and stock the units?')">Confirm</button>
                                </form>
                            <?php } ?>
                        </div>
                    </td>
                    </td>
                </tr>
                <tr class="items-row" id="items-<?php echo $row['id']; ?>">
                    <td colspan="6" class="items-cell">
                        <div class="items-inner">
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
                                <?php if (!$row_items) { ?>
                                    <tr class="empty-row"><td colspan="4">No line items in this MPL.</td></tr>
                                <?php } ?>
                                <?php foreach ($row_items as $item) { ?>
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
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.querySelectorAll('.btn-view-items').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var targetId = btn.getAttribute('data-target');
        var row = document.getElementById(targetId);
        var isOpen = row.classList.contains('open');

        if (!isOpen) {
            row.classList.add('open');
            btn.textContent = 'Hide Items';
        } else {
            row.classList.remove('open');
            btn.textContent = 'View Items';
        }
    });
});
</script>
</body>
</html>