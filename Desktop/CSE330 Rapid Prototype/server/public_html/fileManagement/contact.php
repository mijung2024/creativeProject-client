<?php
session_start();

//Gets the username value while the user session is on
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = '';
}

// What users write in Contact Us will be saved in messageFile which can only be accessed by admin 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);
    $messageFile = '/srv/module2group/contact_messages.txt';
    $contactMessage = "From: $name <$email>\nMessage: $message\n---\n";
    file_put_contents($messageFile, $contactMessage, FILE_APPEND);
    echo "<p>Message sent to the admin. We will get back to you if necessary. </p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div id="contact-container">
        <h1 id="contact-title">Contact Us</h1>
        <form id="contact-form" method="POST" action="contact.php">
            <label for="name" class="form-label">Name:</label>
            <input type="text" id="name" name="name" class="form-input" required><br><br>
            
            <label for="email" class="form-label">Email:</label>
            <input type="email" id="email" name="email" class="form-input" required><br><br>
            
            <label for="message" class="form-label">Message:</label>
            <textarea id="message" name="message" class="form-textarea" required></textarea><br><br>
            
            <button type="submit" class="form-button">Send Message</button>
        </form>
    </div>
</body>
</html>