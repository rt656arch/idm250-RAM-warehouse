<?php
require_once '../includes/auth.php';
require_once '../includes/db_connect.php';
include '../includes/header.php';
// include __DIR__ . '/../includes/header.php';

$conn = getDBConnection();

$sql = "SELECT 
            sku_id,
            ficha,
            sku_code,
            description,
            uom,
            piece_count,
            length_inches,
            width_inches,
            height_inches,
            weight_lbs
        FROM skus
        ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<main>
    <h1>SKU Management</h1>

    <a href="create.php" class="btn">Add New SKU</a>

    <?php if ($result->num_rows > 0): ?>
        <table>
        <thead>
            <tr>
            <th>SKU</th>
            <th>Description</th>
            <th>UOM</th>
            <th>Pieces</th>
            <th>Dimensions (L × W × H)</th>
            <th>Weight (lbs)</th>
            <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($sku = $result->fetch_assoc()): ?>
            <tr>
                <td>
                <?php echo htmlspecialchars($sku['sku_code']); ?>
                <?php if (!empty($sku['ficha'])): ?>
                    <br>
                    <small>Ficha: <?php echo htmlspecialchars($sku['ficha']); ?></small>
                <?php endif; ?>
                </td>

                <td><?php echo htmlspecialchars($sku['description']); ?></td>
                <td><?php echo htmlspecialchars($sku['uom']); ?></td>
                <td><?php echo htmlspecialchars($sku['piece_count']); ?></td>

                <td>
                <?php
                    echo htmlspecialchars(
                    $sku['length_inches'] . ' × ' .
                    $sku['width_inches'] . ' × ' .
                    $sku['height_inches']
                    );
                ?>
                </td>

                <td><?php echo htmlspecialchars($sku['weight_lbs']); ?></td>

                <td>
                <a href="edit.php?id=<?php echo $sku['sku_id']; ?>">Edit</a> |
                <a href="delete.php?id=<?php echo $sku['sku_id']; ?>"
                    onclick="return confirm('Are you sure you want to delete this SKU?');">
                    Delete
                </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        </table>

    <?php else: ?>
        <p>No SKUs found. Add your first SKU to get started.</p>
    <?php endif; ?>
</main>

<?php
$conn->close();

include '../includes/footer.php';
?>