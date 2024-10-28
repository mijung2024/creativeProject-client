<?php
session_start();

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: page.php');
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    
    if (preg_match('/^[\w_\-]+$/', $username)) {
        $users_file = '/srv/module2group/users.txt';
        $users = file($users_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (in_array($username, $users)) {
            $_SESSION['username'] = $username;
            header('Location: page.php');
            exit;
        } else {
            echo "<p>Invalid username</p>";
        }
    } else {
        echo "<p>Invalid username format</p>";
    }
}

// Redirect to file list if logged in
if (isset($_SESSION['username'])) {
    header('Location: page.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <?php if (!isset($_SESSION['username'])): ?>
        <h1>Login</h1>
        <form action="page.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <input type="submit" value="Login">
        </form>
    <?php else: ?>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <a href="page.php?action=logout">Logout</a>
    <?php endif; ?>
</body>
</html>
