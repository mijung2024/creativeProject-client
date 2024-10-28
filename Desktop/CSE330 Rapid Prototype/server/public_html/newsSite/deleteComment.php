<?php
session_start();
require 'database.php';

//backend implementation of user deleting the comment they wrote
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        die("CSRF token validation failed.");
    }

    $comment_id = $_POST['comment_id'] ?? null;

    if ($comment_id) {
        // User shoudl be the owner of the comment to delete the comment
        $stmt = $mysqli->prepare("SELECT user_id FROM comments WHERE id = ?");
        $stmt->bind_param('i', $comment_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $comment = $result->fetch_assoc();
            if ($comment['user_id'] === $_SESSION['user_id']) {
                $stmt = $mysqli->prepare("DELETE FROM comments WHERE id = ?");
                $stmt->bind_param('i', $comment_id);
                $stmt->execute();
                $stmt->close();
            } else {
                die("Unauthorized access.");
            }
        }
    }
}

header("Location: view.php");  
exit;
?>
