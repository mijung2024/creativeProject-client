<?php
session_start();
require 'database.php';
//This is backend implementation of user deleting the story

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        die("CSRF token validation failed.");
    }

    $story_id = $_POST['id'] ?? null;

    // Fetch the story after retrieving data of story id 
    $stmt = $mysqli->prepare("SELECT user_id FROM stories WHERE id = ?");
    $stmt->bind_param('i', $story_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Story not found.");
    }

    $story = $result->fetch_assoc();

    // logged in user should match the session of user id. Only the author user can delete.
    if ($story['user_id'] !== $_SESSION['user_id']) {
        die("Unauthorized access.");
    }

    
    $stmt = $mysqli->prepare("DELETE FROM stories WHERE id = ?");
    $stmt->bind_param('i', $story_id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}
?>
