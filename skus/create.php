<?php
require_once '../includes/auth.php';
require_once '../includes/db_connect.php';
include '../includes/header.php';

$conn = getDBConnection();
$error = '';
$success = '';

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

    if (
        empty($sku_code) ||
        empty($description) ||
        empty($uom) ||
        $piece_count <= 0
    ) {
        $error = 'Please fill in all required fields.';
    } else {

        $sql = "INSERT INTO skus (
                    ficha,
                    sku_code,
                    description,
                    uom,
                    piece_count,
                    length_inches,
                    width_inches,
                    height_inches,
                    weight_lbs
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "isssiddid",
            $ficha,
            $sku_code,
            $description,
            $uom,
            $piece_count,
            $length,
            $width,
            $height,
            $weight
        );

        if ($stmt->execute()) {
            $success = 'SKU created successfully.';
        } else {
            $error = 'Error creating SKU. Please try again.';
        }

        $stmt->close();
    }
}
?>

<main>
    <h1>Add New SKU</h1>

    <a href="../skus/index.php">← Back to SKU List</a>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="POST" action="create.php" class="sku-form">

        <label>
            Ficha (Manufacturer Reference)
            <input type="number" name="ficha">
        </label>

        <label>
            SKU Code *
            <input type="text" name="sku_code" required>
        </label>

        <label>
            Description *
            <textarea name="description" required></textarea>
        </label>

        <label>
            Unit of Measure (UOM) *
            <input type="text" name="uom" required>
        </label>

        <label>
            Piece Count *
            <input type="number" name="piece_count" required>
        </label>

        <fieldset>
            <legend>Dimensions (inches)</legend>

            <label>
                Length
                <input type="number" step="0.01" name="length">
            </label>

            <label>
                Width
                <input type="number" step="0.01" name="width">
            </label>

            <label>
                Height
                <input type="number" step="0.01" name="height">
            </label>
        </fieldset>

        <label>
            Weight (lbs)
            <input type="number" step="0.01" name="weight">
        </label>

        <button type="submit" class="btn">Create SKU</button>
    </form>
</main>

<?php
$conn->close();
include '../includes/footer.php';
?>
