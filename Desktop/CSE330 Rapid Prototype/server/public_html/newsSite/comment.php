<?php
session_start();
require 'database.php';

//This is the backend implemnetation when user comments on the story. 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        die("CSRF token validation failed.");
    }
    $storyId = $_POST['story_id'];
    $comment = $_POST['comment'];
    $userId = $_SESSION['user_id'];


    //After retrieving data of which user is commenting on which story, it insert the comment. 
    $stmt = $mysqli->prepare("INSERT INTO comments (story_id, user_id, comment) VALUES (?, ?, ?)");
    if (!$stmt) {
        exit;
    }

    $stmt->bind_param('iis', $storyId, $userId, $comment);
    $stmt->execute();
    $stmt->close();
    header("Location: view.php"); 
    exit;
}
?>
