<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: page.php');
    exit;
}

$username = $_SESSION['username'];
$directory = "/srv/module2group/$username/";

if (!is_dir($directory)) {
    echo "No files available.";
    exit;
}

$files = array_diff(scandir($directory), array('.', '..'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File List</title>
</head>
<body>
    <h1>Files for <?php echo htmlspecialchars($username); ?></h1>
    <ul>
        <?php foreach ($files as $file): ?>
            <li>
                <a href="view.php?file=<?php echo urlencode($file); ?>"><?php echo htmlspecialchars($file); ?></a>
                <a href="delete.php?file=<?php echo urlencode($file); ?>" onclick="return confirm('Are you sure?');">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="upload.php">Upload File</a>
    <a href="logout.php">Logout</a>
</body>
</html>
