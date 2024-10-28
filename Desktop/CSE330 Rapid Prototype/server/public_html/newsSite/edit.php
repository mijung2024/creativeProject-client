<?php
session_start();
require 'database.php';

//backedn implementation of story owner user editing the story
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$story_id = $_GET['id'] ?? null;

$stmt = $mysqli->prepare("SELECT user_id, title, body, link FROM stories WHERE id = ?");
$stmt->bind_param('i', $story_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Story not found.");
}

$story = $result->fetch_assoc();

if ($story['user_id'] !== $_SESSION['user_id']) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        die("CSRF token validation failed.");
    }

    $title = trim($_POST['title']);
    $body = trim($_POST['body']); 
    $link = trim($_POST['link']);

    // Edit the story after retreiving data
    $stmt = $mysqli->prepare("UPDATE stories SET title = ?, body = ?, link = ? WHERE id = ?");
    $stmt->bind_param('sssi', $title, $body, $link, $story_id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Story</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1>Edit Story</h1>
    <form method="post" action="edit.php?id=<?= $story_id ?>">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
        
        <label for="title">Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($story['title']) ?>" required>
        
        <label for="body">Body:</label>
        <textarea name="body" required><?= htmlspecialchars($story['body']) ?></textarea>
        
        <label for="link">Link:</label>
        <input type="url" name="link" value="<?= htmlspecialchars($story['link']) ?>">

        <input type="submit" value="Update Story">
    </form>
</body>
</html>
