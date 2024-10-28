<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: users.php');
    exit;
}

$username = $_SESSION['username'];
$directory = "/srv/module2group/$username/";

$filename = isset($_GET['file']) ? basename($_GET['file']) : '';

if (!preg_match('/^[\w_\.\-]+$/', $filename)) {
    echo "Invalid filename";
    exit;
}

$full_path = sprintf("%s/%s", $directory, $filename);

if (file_exists($full_path)) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($full_path);
    
    header("Content-Type: " . $mime);
    header('Content-Disposition: inline; filename="' . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') . '"');
    readfile($full_path);
} else {
    echo "<p>File not found.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View File</title>
</head>
<body>
    <p><a href="filesOperation.php">Go Back</a></p>
</body>
</html>
