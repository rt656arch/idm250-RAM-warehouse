<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: dashboard.php', true, 303);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (login_user($_POST['username'] ?? '', $_POST['password'] ?? '')) {
        header('Location: dashboard.php', true, 303);
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login — RAM WMS</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-wrap">
    <div class="login-box">
        <img src="assets/logo-ram.svg" alt="RAM WMS" class="login-logo">
        <h2>Warehouse Management System</h2>

        <?php if ($error) { ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <form method="POST" action="login.php">
            <div class="field">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Sign In</button>
        </form>
    </div>
</div>
</body>
</html>
