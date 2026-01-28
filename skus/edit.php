<?php
require_once '../includes/auth.php';
require_once '../includes/db_connect.php';
include '../includes/header.php';

$conn = getDBConnection();

$error = '';
$success = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$sku_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM skus WHERE sku_id = ?");
$stmt->bind_param("i", $sku_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    header('Location: index.php');
    exit;
}

$sku = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ficha = !empty($_POST['ficha']) ? intval($_POST['ficha']) : null;
    $sku_code = trim($_POST['sku_code']);
    $description = trim($_POST['description']);
    $uom = trim($_POST['uom']);
    $piece_count = intval($_POST['piece_count']);
    $length = floatval($_POST['length']);
    $width = floatval($_POST['width']);
    $height = floatval($_POST['height']);
    $weight = floatval($_POST['weight']);

    if (empty($sku_code) || empty($description) || empty($uom) || $piece_count <= 0) {
        $error = 'Please fill in all required fields.';
    } else {
        $update_sql = "UPDATE skus SET
                            ficha = ?,
                            sku_code = ?,
                            description = ?,
                            uom = ?,
                            piece_count = ?,
                            length_inches = ?,
                            width_inches = ?,
                            height_inches = ?,
                            weight_lbs = ?
                        WHERE sku_id = ?";

        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param(
            "isssiddidi",
            $ficha,
            $sku_code,
            $description,
            $uom,
            $piece_count,
            $length,
            $width,
            $height,
            $weight,
            $sku_id
        );

        if ($stmt->execute()) {
            $success = 'SKU updated successfully.';
            $sku = array_merge($sku, $_POST);
        } else {
            $error = 'Error updating SKU. Please try again.';
        }

        $stmt->close();
    }
}
?>

<main>
    <h1>Edit SKU</h1>

    <a href="../skus/index.php">← Back to SKU List</a>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?php echo $sku_id; ?>" class="sku-form">

        <label>
            Ficha (Manufacturer Reference)
            <input type="number" name="ficha" value="<?php echo htmlspecialchars($sku['ficha']); ?>">
        </label>

        <label>
            SKU Code *
            <input type="text" name="sku_code" value="<?php echo htmlspecialchars($sku['sku_code']); ?>" required>
        </label>

        <label>
            Description *
            <textarea name="description" required><?php echo htmlspecialchars($sku['description']); ?></textarea>
        </label>

        <label>
            Unit of Measure (UOM) *
            <input type="text" name="uom" value="<?php echo htmlspecialchars($sku['uom']); ?>" required>
        </label>

        <label>
            Piece Count *
            <input type="number" name="piece_count" value="<?php echo htmlspecialchars($sku['piece_count']); ?>" required>
        </label>

        <fieldset>
            <legend>Dimensions (inches)</legend>

            <label>
                Length
                <input type="number" step="0.01" name="length" value="<?php echo htmlspecialchars($sku['length_inches']); ?>">
            </label>

            <label>
                Width
                <input type="number" step="0.01" name="width" value="<?php echo htmlspecialchars($sku['width_inches']); ?>">
            </label>

            <label>
                Height
                <input type="number" step="0.01" name="height" value="<?php echo htmlspecialchars($sku['height_inches']); ?>">
            </label>
        </fieldset>

        <label>
            Weight (lbs)
            <input type="number" step="0.01" name="weight" value="<?php echo htmlspecialchars($sku['weight_lbs']); ?>">
        </label>

        <button type="submit" class="btn">Update SKU</button>
    </form>
</main>

<?php
$conn->close();
include '../includes/footer.php';
?>
