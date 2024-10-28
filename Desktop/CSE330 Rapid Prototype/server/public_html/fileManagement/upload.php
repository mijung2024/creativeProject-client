<?php
session_start();

// Define path to user files
$base_dir = '/srv/module2group/';
$uploads_dir = isset($_SESSION['username']) ? $base_dir . $_SESSION['username'] . '/' : '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['uploadedfile'])) {
    $filename = basename($_FILES['uploadedfile']['name']);
    
    // Validate filename
    if (preg_match('/^[\w_\.\-]+$/', $filename)) {
        if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $uploads_dir . $filename)) {
            echo "<p>File uploaded successfully.</p>";
        } else {
            echo "<p>File upload failed.</p>";
        }
    } else {
        echo "<p>Invalid filename.</p>";
    }
}

// Display upload form and files if user is logged in
if (isset($_SESSION['username'])) {
    echo "<h2>Welcome, " . htmlspecialchars($_SESSION['username']) . "</h2>";
    echo "<h3>Upload a File:</h3>";
    echo "<form enctype='multipart/form-data' action='upload.php' method='POST'>
        <input type='hidden' name='MAX_FILE_SIZE' value='20000000' />
        <label for='uploadfile_input'>Choose a file to upload:</label>
        <input name='uploadedfile' type='file' id='uploadfile_input' />
        <input type='submit' value='Upload File' />
    </form>";

    // List uploaded files
    $files = scandir($uploads_dir);
    echo "<h3>Uploaded Files:</h3>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "<li><a href='view.php?file=" . urlencode($file) . "'>" . htmlspecialchars($file) . "</a></li>";
        }
    }
    echo "</ul>";
} else {
    echo "<h2>Login Required</h2>";
    echo "<p>You need to log in to upload files.</p>";
}
?>
