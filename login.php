<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once 'includes/db_connect.php';
include 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT user_id, email, password, first_name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // LOGIN SUCCESSFUL
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'];
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<main>
    <section class="login">
        <h1>Log In</h1>
        
        <?php if ($error): ?>
            <p class="login-error">
                <?php echo htmlspecialchars($error); ?>
            </p>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            
            <input type="password" name="password" placeholder="Password" required>
            
            <button type="submit">
                Log In
            </button>
        </form>
        
        <p class="login-message-text">
            Don't have an account? <a class="login-message-link" href="register.php">Sign up here</a>
        </p>
    </section>
</main>

<?php include 'includes/footer.php'; ?>