<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: page.php');
    exit;
}

$filename = $_GET['file'];
$username = $_SESSION['username'];

if (preg_match('/^[\w_\.\-]+$/', $filename) && preg_match('/^[\w_\-]+$/', $username)) {
    $full_path = sprintf("/srv/module2group/%s/%s", $username, $filename);

    if (file_exists($full_path)) {
        unlink($full_path);
        header('Location: file_list.php');
        exit;
    } else {
        echo "File not found.";
    }
} else {
    echo "Invalid request.";
}
?>
