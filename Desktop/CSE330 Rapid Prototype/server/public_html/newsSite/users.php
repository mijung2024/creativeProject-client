<?php
session_start();


// When users log out, their session gets destroyed and the brose will be redirected to the login page
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: users.php');
    exit;
}

// When user exists in the system, the user the log in to the system and will be redirected to the file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    
    if (preg_match('/^[\w_\-]+$/', $username)) {
        $users_file = '/srv/module2group/users.txt';
        $users = file($users_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (in_array($username, $users)) {
            $_SESSION['username'] = $username;
            header('Location: filesOperation.php');
            exit;
        } else {
            echo "<p>Wrong username</p>";
        }
    } else {
        echo "<p>Wrong username</p>";
    }
}
if (isset($_SESSION['username'])) {
    header('Location: filesOperation.php');
    exit;
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
    <div id="login-container">
        <?php if (!isset($_SESSION['username'])): ?>
            <h1 id="login-title">Login</h1>
            <?php if (isset($login_error)): ?>
                <p id="login-error"><?php echo htmlspecialchars($login_error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <form id="login-form" action="users.php" method="post">
                <label for="username" class="form-label">Username:</label>
                <input type="text" id="username" name="username" class="form-input" required>
                <input type="submit" value="Login" class="form-button">
            </form>
        <?php else: ?>
            <h1 id="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <a href="users.php?action=logout" id="logout-link">Logout</a>
        <?php endif; ?>
    </div>
</body>
</html>