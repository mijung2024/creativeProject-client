<?php
session_start();
require 'database.php';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        die("CSRF token validation failed.");
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE username = ?");
    if (!$stmt) {
        exit("Database query failed.");
    }

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();
        
        if (password_verify($password, $hashed_password)) {
            // Store user ID in session
            $_SESSION['user_id'] = $user_id; 
            header("Location: index.php"); 
            exit;
        } else {
            $error = "Wrong password! Register if you do not have an account here.";
        }
    } else {
        $error = "No user with the following username. Register if you do not have an account here.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="header">
    <h1>Students News Site</h1>
    <nav class="navigation">
    <ul>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li>Hello!</li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="post.php">Post a Story</a></li>
        <?php else: ?>
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>
</header>
<main>
    <div id="login-title">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </div>
    <form id="login-form" method="post" action="login.php">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>"> 
        
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <input type="submit" value="Login">
    </form>            
    <a id="logout-link" href="register.php">Don't have an account? Register here.</a>
</main>
</body>
</html>
