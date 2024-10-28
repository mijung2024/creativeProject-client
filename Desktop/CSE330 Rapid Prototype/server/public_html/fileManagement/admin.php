<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: users.php');
    exit;
}

// Only allow admin to view messages
if ($_SESSION['username'] !== 'admin') {
    echo "Access Denied.";
    exit;
}

// Display messages from contact_messages which stores all the messages from Contact Us 
$messages_file = "/srv/module2group/contact_messages.txt";

// Check if the file exists and is readable
if (!file_exists($messages_file) || !is_readable($messages_file)) {
    echo "Unable to access messages file.";
    exit;
}

// Read messages from the file
$messages = file($messages_file);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - View Messages</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div id="admin-container">
        <h1 id="admin-title">Contact Us Messages</h1>
        <ul id="messages-list">
            <?php if (empty($messages)): ?>
                <li class="message-item">No messages available.</li>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <li class="message-item"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <a id="back-link" href="filesOperation.php">Back to File Management</a>
    </div>
</body>
</html>
