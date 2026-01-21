<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/svg" sizes="32x32" href="../assets/logo-ram.svg">
    <link rel="icon" type="image/svg" sizes="16x16" href="../assets/logo-ram.svg">

    <title>IDM-250 | RAM Warehouse</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
?>

<header class="site-header">
    <div class="navbar">
        <a href="index.php"><img class="logo" src="../assets/logo-ram.png" alt="Placeholder Logo"></a>

        <div class="menu-toggle">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>

        <nav class="nav-links">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="help.php">Help</a></li>
            </ul>
        </nav>

        <div class="login">
            <?php if ($is_logged_in): ?>
                <button class="login-button">
                    <a href="logout.php">Log Out</a>
                </button>
            <?php else: ?>
                <button class="login-button">
                    <a href="login.php">Log In</a>
                </button>
            <?php endif; ?>
        </div>
    </div>
</header>

