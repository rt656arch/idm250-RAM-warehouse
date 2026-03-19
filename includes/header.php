<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<header class="topbar">
    <img src="assets/logo-ram.svg" alt="RAM WMS" class="topbar-logo">
    <nav class="topbar-nav">
        <a href="dashboard.php"  class="<?php if ($current == 'dashboard.php')                                               echo 'active'; ?>">Dashboard</a>
        <a href="skus.php"       class="<?php if ($current == 'skus.php')                                                    echo 'active'; ?>">SKUs</a>
        <a href="inventory.php"  class="<?php if ($current == 'inventory.php')                                               echo 'active'; ?>">Inventory</a>
        <a href="mpls.php"       class="<?php if ($current == 'mpls.php'       || $current == 'mpl_detail.php')             echo 'active'; ?>">MPLs</a>
        <a href="orders.php"     class="<?php if ($current == 'orders.php'     || $current == 'order_detail.php')           echo 'active'; ?>">Orders</a>
        <a href="shipped.php"    class="<?php if ($current == 'shipped.php')                                                 echo 'active'; ?>">Shipped</a>
    </nav>
    <div class="topbar-user">
        <?php echo htmlspecialchars($_SESSION['username']); ?>
        &nbsp;|&nbsp;
        <a href="logout.php">Log out</a>
    </div>
</header>
<div class="page-wrap">