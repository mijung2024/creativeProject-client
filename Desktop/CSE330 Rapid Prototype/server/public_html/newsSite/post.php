<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        die("CSRF token validation failed.");
    }

    if (isset($_POST['action']) && $_POST['action'] === 'add_employee') {
        $first = trim($_POST['first']);
        $last = trim($_POST['last']);
        $dept = trim($_POST['dept']);

        $stmt = $mysqli->prepare("INSERT INTO employees (first_name, last_name, department) VALUES (?, ?, ?)");
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt->bind_param('sss', $first, $last, $dept);
        $stmt->execute();
        $stmt->close();
        
        header("Location: index.php"); 
        exit;

    } elseif (isset($_POST['action']) && $_POST['action'] === 'post_story') {
        $title = trim($_POST['title']);
        $body = trim($_POST['body']); 
        $link = trim($_POST['link']);
        $userId = $_SESSION['user_id'];

        if (!empty($title) && !empty($body)) {
            $stmt = $mysqli->prepare("INSERT INTO stories (user_id, title, body, link) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }

            $stmt->bind_param('isss', $userId, $title, $body, $link);
            if ($stmt->execute()) {
                header("Location: index.php"); 
                exit;
            } else {
                echo "Error posting story: " . $stmt->error; 
            }
            $stmt->close();
        } else {
            echo "Title and body cannot be empty.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post a Story or Add Employee</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <h1>Post a Story</h1>
    <form method="post" action="post.php">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>"> 
        <input type="hidden" name="action" value="post_story"> 
        
        <label for="title">Title:</label>
        <input type="text" name="title" required>
        
        <label for="body">Body:</label>
        <textarea name="body" required></textarea>
        
        <label for="link">Link:</label>
        <input type="url" name="link">

        <input type="submit" value="Post Story">
    </form>
</body>
</html>
