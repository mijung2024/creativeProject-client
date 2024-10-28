<?php
session_start();
require 'database.php';

//backedn implementation of comment owner user editing the comment

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$comment_id = $_GET['id'] ?? null;

// Fetch the comment
$stmt = $mysqli->prepare("SELECT user_id, comment FROM comments WHERE id = ?");
$stmt->bind_param('i', $comment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Comment not found.");
}

$comment = $result->fetch_assoc();

if ($comment['user_id'] !== $_SESSION['user_id']) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        die("CSRF token validation failed.");
    }

    $new_comment = trim($_POST['comment']);

    $stmt = $mysqli->prepare("UPDATE comments SET comment = ? WHERE id = ?");
    $stmt->bind_param('si', $new_comment, $comment_id);
    $stmt->execute();
    $stmt->close();

    header("Location: view.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Comment</title>
</head>
<body>
    <h1>Edit Comment</h1>
    <form method="post" action="editComment.php?id=<?= $comment_id ?>">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>"> 
        
        <textarea name="comment" required><?= htmlspecialchars($comment['comment']) ?></textarea>
        <input type="submit" value="Update Comment">
    </form>
</body>
</html>
