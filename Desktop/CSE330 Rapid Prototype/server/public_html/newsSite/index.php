<?php
session_start();
require 'database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$stmt = $mysqli->prepare("SELECT stories.id, stories.title, stories.body, stories.link, stories.user_id, users.username FROM stories JOIN users ON stories.user_id = users.id ORDER BY stories.created_at DESC");
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>News Site</title>
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
<?php if ($result->num_rows > 0): ?>
    <?php while ($story = $result->fetch_assoc()): ?>
        <h3><?= htmlspecialchars($story['title']) ?></h3>
        <p><?= htmlspecialchars($story['body']) ?></p>
        <?php if (!empty($story['link'])): ?>
            <a href="<?= htmlspecialchars($story['link']) ?>">Link Here!!!</a><br>
        <?php endif; ?>
        <small>Posted by: <?= htmlspecialchars($story['username']) ?></small>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $story['user_id']): ?>
            <p>
                <a href="edit.php?id=<?= $story['id'] ?>">Edit</a> | 
                <form method="post" action="delete.php" class="delete-form">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
    <input type="hidden" name="id" value="<?= $story['id'] ?>">
    <input type="submit" value="Delete" class="button-link" onclick="return confirm('Clicking this will delete. Are you sure!?');">
</form>

            </p>
        <?php endif; ?>

        <p><a href="view.php?id=<?= $story['id'] ?>">Comments</a></p>
        <hr>
    <?php endwhile; ?>
<?php else: ?>
    <p>Empty</p>
<?php endif; ?>

<?php $stmt->close(); ?>
</main>
</body>
</html>
