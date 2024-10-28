<?php
session_start();
require 'database.php';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$stmt = $mysqli->prepare("SELECT stories.id, stories.title, stories.body, stories.link, users.username FROM stories JOIN users ON stories.user_id = users.id");
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

while ($story = $result->fetch_assoc()) {
    echo "<h2>" . htmlspecialchars($story['title']) . "</h2>";
    echo "<p>" . htmlspecialchars($story['body']) . "</p>";
    if (!empty($story['link'])) {
        echo "<a href='" . htmlspecialchars($story['link']) . "'>Link Here!!!</a><br>";
    }
    echo "<small>Posted by: " . htmlspecialchars($story['username']) . "</small>";

    // Fetch comments for this story
    $stmt_comments = $mysqli->prepare("SELECT comments.id, comments.comment, users.username, comments.user_id FROM comments JOIN users ON comments.user_id = users.id WHERE story_id = ?");
    $stmt_comments->bind_param('i', $story['id']);
    $stmt_comments->execute();
    $comments_result = $stmt_comments->get_result();

    echo "<ul>"; 
    while ($comment = $comments_result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($comment['comment']) . " - " . htmlspecialchars($comment['username']);
        
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']) {
            echo " <a href='editComment.php?id=" . $comment['id'] . "'>Edit</a>";
            echo " <form method='post' action='deleteComment.php' style='display:inline;'>
                    <input type='hidden' name='comment_id' value='" . $comment['id'] . "'>
                    <input type='hidden' name='csrf' value='" . htmlspecialchars($_SESSION['csrf']) . "'> <!-- CSRF token -->
                    <input type='submit' value='Delete' onclick='return confirm(\"Are you sure you want to delete this comment?\");'>
                  </form>";
        }
        echo "</li>";
    }
    echo "</ul>"; 

    if (isset($_SESSION['user_id'])) {
        echo "<form method='post' action='comment.php'>
                <input type='hidden' name='story_id' value='" . $story['id'] . "'>
                <input type='hidden' name='csrf' value='" . htmlspecialchars($_SESSION['csrf']) . "'> <!-- CSRF token -->
                Comment: <input type='text' name='comment' required>
                <input type='submit' value='Add Comment'>
              </form>";
    }
    echo "<hr>";
}
$stmt->close();
?>
