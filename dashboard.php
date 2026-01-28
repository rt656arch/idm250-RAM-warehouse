<?php 
require_once 'includes/auth.php';
include 'includes/header.php';
?>
<main>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>

    <nav>
        <ul>
            <li><a href="skus/index.php">Manage SKUs</a></li>
            <li><a href="mpls/index.php">MPL Records</a></li>
            <li><a href="inventory/index.php">Inventory</a></li>
            <li><a href="orders/index.php">Orders</a></li>
        </ul>
    </nav>
</main>

<?php 
include 'includes/footer.php';
?>