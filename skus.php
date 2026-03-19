<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$action = $_GET['action'] ?? 'list';
$id     = $_GET['id']     ?? 0;
$notice = $_GET['notice'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_action = $_POST['action'] ?? '';

    if ($post_action == 'create') {
        insert_sku($_POST);
        header('Location: skus.php?notice=created', true, 303);
        exit;
    }

    if ($post_action == 'update') {
        $sku_id = $_POST['sku_id'] ?? 0;
        save_sku($sku_id, $_POST);
        header('Location: skus.php?notice=updated', true, 303);
        exit;
    }

    if ($post_action == 'delete') {
        $sku_id = $_POST['sku_id'] ?? 0;
        remove_sku($sku_id);
        header('Location: skus.php?notice=deleted', true, 303);
        exit;
    }
}

$record = null;
if ($action == 'edit' && $id) {
    $record = get_sku($id);
}

$all_skus = get_skus();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SKUs — RAM WMS</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="page-title">
    <?php if ($action == 'add')  echo 'Add SKU'; ?>
    <?php if ($action == 'edit') echo 'Edit SKU'; ?>
    <?php if ($action == 'list') echo 'SKU Catalog'; ?>
</div>
<div class="page-subtitle">
    <?php if ($action == 'list') { ?>
        SKUs are auto-created when MPLs arrive from the CMS.
    <?php } else { ?>
        <a href="skus.php">&larr; Back to SKU Catalog</a>
    <?php } ?>
</div>

<?php if ($notice == 'created') { ?>
    <div class="alert alert-success">SKU created successfully.</div>
<?php } ?>
<?php if ($notice == 'updated') { ?>
    <div class="alert alert-success">SKU updated successfully.</div>
<?php } ?>
<?php if ($notice == 'deleted') { ?>
    <div class="alert alert-success">SKU removed.</div>
<?php } ?>

<?php if ($action == 'list') { ?>

    <div class="toolbar">
        <div class="toolbar-left"><?php echo count($all_skus); ?> SKU(s) on file</div>
        <a href="skus.php?action=add" class="btn btn-primary">+ New SKU</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>SKU Code</th>
                    <th>Description</th>
                    <th>UOM</th>
                    <th>Pcs</th>
                    <th>L &times; W &times; H (in)</th>
                    <th>Wt (lbs)</th>
                    <th>Rate</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$all_skus) { ?>
                <tr class="empty-row">
                    <td colspan="8">No SKUs on file — they are created automatically when an MPL arrives.</td>
                </tr>
            <?php } ?>
            <?php foreach ($all_skus as $row) { ?>
                <tr>
                    <td><span class="chip"><?php echo htmlspecialchars($row['sku']); ?></span></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['uom_primary']); ?></td>
                    <td><?php echo htmlspecialchars($row['piece_count']); ?></td>
                    <td>
                        <?php echo $row['length_inches']; ?> &times;
                        <?php echo $row['width_inches']; ?> &times;
                        <?php echo $row['height_inches']; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['weight_lbs']); ?></td>
                    <td><?php echo htmlspecialchars($row['rate']); ?></td>
                    <td>
                        <a href="skus.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-secondary">Edit</a>
                        <form method="POST" action="skus.php" style="display:inline">
                            <input type="hidden" name="action"  value="delete">
                            <input type="hidden" name="sku_id"  value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Delete this SKU?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

<?php } ?>

<?php if ($action == 'add') { ?>

    <div class="form-box">
        <form method="POST" action="skus.php">
            <input type="hidden" name="action" value="create">

            <div class="field-row">
                <div class="field">
                    <label>Ficha</label>
                    <input type="text" name="ficha" value="0">
                </div>
                <div class="field">
                    <label>SKU Code *</label>
                    <input type="text" name="sku" required>
                </div>
            </div>

            <div class="field">
                <label>Description *</label>
                <input type="text" name="description" required>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Unit of Measure</label>
                    <select name="uom_primary">
                        <option value="BUNDLE">BUNDLE</option>
                        <option value="PALLET">PALLET</option>
                    </select>
                </div>
                <div class="field">
                    <label>Piece Count</label>
                    <input type="text" name="piece_count" value="0">
                </div>
            </div>

            <div class="field-row-3">
                <div class="field">
                    <label>Length (in)</label>
                    <input type="text" name="length_inches" value="0">
                </div>
                <div class="field">
                    <label>Width (in)</label>
                    <input type="text" name="width_inches" value="0">
                </div>
                <div class="field">
                    <label>Height (in)</label>
                    <input type="text" name="height_inches" value="0">
                </div>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Weight (lbs)</label>
                    <input type="text" name="weight_lbs" value="0">
                </div>
                <div class="field">
                    <label>Rate</label>
                    <input type="text" name="rate" value="0">
                </div>
            </div>

            <div class="field">
                <label>Assembly Required</label>
                <select name="assembly">
                    <option value="false">False</option>
                    <option value="true">True</option>
                </select>
            </div>

            <div class="form-actions">
                <a href="skus.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create SKU</button>
            </div>
        </form>
    </div>

<?php } ?>

<?php if ($action == 'edit' && $record) { ?>

    <div class="form-box">
        <form method="POST" action="skus.php">
            <input type="hidden" name="action"  value="update">
            <input type="hidden" name="sku_id"  value="<?php echo $record['id']; ?>">

            <div class="field-row">
                <div class="field">
                    <label>Ficha</label>
                    <input type="text" name="ficha" value="<?php echo htmlspecialchars($record['ficha']); ?>">
                </div>
                <div class="field">
                    <label>SKU Code *</label>
                    <input type="text" name="sku" value="<?php echo htmlspecialchars($record['sku']); ?>" required>
                </div>
            </div>

            <div class="field">
                <label>Description *</label>
                <input type="text" name="description" value="<?php echo htmlspecialchars($record['description']); ?>" required>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Unit of Measure</label>
                    <select name="uom_primary">
                        <option value="BUNDLE" <?php if ($record['uom_primary'] == 'BUNDLE') echo 'selected'; ?>>BUNDLE</option>
                        <option value="PALLET" <?php if ($record['uom_primary'] == 'PALLET') echo 'selected'; ?>>PALLET</option>
                    </select>
                </div>
                <div class="field">
                    <label>Piece Count</label>
                    <input type="text" name="piece_count" value="<?php echo htmlspecialchars($record['piece_count']); ?>">
                </div>
            </div>

            <div class="field-row-3">
                <div class="field">
                    <label>Length (in)</label>
                    <input type="text" name="length_inches" value="<?php echo htmlspecialchars($record['length_inches']); ?>">
                </div>
                <div class="field">
                    <label>Width (in)</label>
                    <input type="text" name="width_inches" value="<?php echo htmlspecialchars($record['width_inches']); ?>">
                </div>
                <div class="field">
                    <label>Height (in)</label>
                    <input type="text" name="height_inches" value="<?php echo htmlspecialchars($record['height_inches']); ?>">
                </div>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Weight (lbs)</label>
                    <input type="text" name="weight_lbs" value="<?php echo htmlspecialchars($record['weight_lbs']); ?>">
                </div>
                <div class="field">
                    <label>Rate</label>
                    <input type="text" name="rate" value="<?php echo htmlspecialchars($record['rate']); ?>">
                </div>
            </div>

            <div class="field">
                <label>Assembly Required</label>
                <select name="assembly">
                    <option value="false" <?php if ($record['assembly'] == 'false') echo 'selected'; ?>>False</option>
                    <option value="true"  <?php if ($record['assembly'] == 'true')  echo 'selected'; ?>>True</option>
                </select>
            </div>

            <div class="form-actions">
                <a href="skus.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>

<?php } ?>

</div>
</body>
</html>
