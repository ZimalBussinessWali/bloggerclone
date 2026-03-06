<?php
require_once 'db.php';

if (is_logged_in()) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BloggerClone</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body style="background: #fdfdfd;">

<header>
    <a href="index.php" class="logo">Blogger.</a>
    <div class="auth-btns">
        <a href="login.php" style="margin-right: 15px; text-decoration: none; color: #333; font-weight: 600;">Login</a>
        <a href="signup.php" class="btn btn-primary">Sign Up</a>
    </div>
</header>

<div class="form-container">
    <h1 class="form-title">Welcome Back</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem; margin-top: 1rem;">Login to Account</button>
    </form>
    
    <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: #666;">
        Don't have an account? <a href="signup.php" style="color: var(--primary-color); font-weight: 600;">Create one</a>
    </p>
</div>

<script src="script.js"></script>
</body>
</html>
