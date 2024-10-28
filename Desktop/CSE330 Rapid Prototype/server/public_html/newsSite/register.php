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
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
    if (!$stmt) {
        exit("Database query failed.");
    }

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows <= 0) {
        $stmt = $mysqli->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        if (!$stmt) {
            exit("Database query failed.");
        }

        $stmt->bind_param('ss', $username, $password);
        if ($stmt->execute()) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Failed to register. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "Following username already exists. Choose another username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
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
        <h2>Register</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </div>
    <form id="login-form" method="post" action="register.php">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>"> 
        
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        
        <input type="submit" value="Register">
    </form>
</main>
</body>
</html>
