<?php
session_start();

//If there is no account logged in, it redirect to login page 
if (!isset($_SESSION['username'])) {
    header('Location: users.php');
    exit;
}

////Gets the username value while the user session is on and find the directory where the user's data is stored
$username = $_SESSION['username'];
$directory = "/srv/module2group/$username/";

if (!is_dir($directory)) {
    echo "<p>No files!</p>";
    exit;
}

// User can upload the file after file name and format has been validated
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['uploadedfile'])) {
    $filename = basename($_FILES['uploadedfile']['name']);
    
    if (preg_match('/^[\w_\.\-]+$/', $filename)) {
        if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $directory . $filename)) {
            echo "<p>File uploaded successfully.</p>";
        } else {
            echo "<p>File upload failed.</p>";
        }
    } else {
        echo "<p>Invalid filename.</p>";
    }
}

// User can delete the file on the system permanently
if (isset($_GET['delete'])) {
    $file_to_delete = basename($_GET['delete']);
    $file_path = $directory . $file_to_delete;

    if (file_exists($file_path) && unlink($file_path)) {
        echo "<p>File deleted.</p>";
    } else {
        echo "<p>Failed to delete file.</p>";
    }
}

// User can rename the file on the system 
if (isset($_POST['rename'])) {
    $old_name = basename($_POST['old_name']);
    $new_name = basename($_POST['new_name']);
    $old_path = $directory . $old_name;
    $new_path = $directory . $new_name;

    if (preg_match('/^[\w_\.\-]+$/', $new_name) && file_exists($old_path)) {
        if (rename($old_path, $new_path)) {
            echo "<p>File renamed.</p>";
        } else {
            echo "<p>Failed to rename file.</p>";
        }
    } else {
        echo "<p>Error</p>";
    }
}

// User can download the file from the site system to their local system. Codes from php.net
if (isset($_GET['download'])) {
    $file_to_download = basename($_GET['download']);
    $file_path = $directory . $file_to_download;

    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.
        
        basename($file_path).'"');

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;

    } else {
        echo "<p>Error</p>";
    }
}

// User can view the file. However, not all file extensioins can be viewed. Only the photos can be viewed and rest will guid the user to download to view
if (isset($_GET['view'])) {
    $file_to_view = basename($_GET['view']);
    $file_path = $directory . $file_to_view;

    if (file_exists($file_path)) {
        $file_type = mime_content_type($file_path);
        header('Content-Type: ' . $file_type);
        readfile($file_path);
        exit;
    } else {
        echo "<p>Error</p>";
    }
}

$files = array_diff(scandir($directory), array('.', '..'));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header id="main-header">
        <h1 id="page-title">Files for <?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></h1>
        <nav id="main-nav">
            <a href="contact.php" target="_blank" class="nav-link">Contact Us</a>
            <?php if ($username === 'admin'): ?>
                <a href="admin.php" target="_blank" class="nav-link">View Messages</a>
            <?php endif; ?>
            <a href="users.php?action=logout" class="nav-link">Logout</a>
        </nav>
    </header>

    <main id="main-content">
        <section id="file-list-section">
            <h2 id="file-list-title">File List</h2>
            <ul id="file-list">
                <?php foreach ($files as $file): ?>
                    <?php
                    $file_path = $directory . $file;
                    $file_size = filesize($file_path);
                    $file_type = mime_content_type($file_path);
                    $file_date = date("F d Y H:i:s", filemtime($file_path));
                    ?>
                    <li class="file-item">
                        <span class="file-name"><?php echo htmlspecialchars($file, ENT_QUOTES, 'UTF-8'); ?></span>
                        <div class="file-actions">
                            <a href="filesOperation.php?view=<?php echo urlencode($file); ?>" class="button view-button" target="_blank">View</a>
                            <a href="filesOperation.php?download=<?php echo urlencode($file); ?>" class="button download-button">Download</a>
                            <a href="filesOperation.php?delete=<?php echo urlencode($file); ?>" class="button delete-button" onclick="return confirm('Are you sure?');">Delete</a>
                            <form action="filesOperation.php" method="POST" class="rename-form">
                                <input type="hidden" name="old_name" value="<?php echo htmlspecialchars($file, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="text" name="new_name" placeholder="New name" required class="rename-input">
                                <input type="submit" name="rename" value="Rename" class="button rename-button">
                            </form>
                        </div>
                        <div class="file-info">
                            <span class="file-size">Size: <?php echo number_format($file_size / 1024, 2); ?> KB</span>
                            <span class="file-type">Type: <?php echo htmlspecialchars($file_type, ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="file-date">Uploaded: <?php echo htmlspecialchars($file_date, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <section id="upload-section">
            <h2 id="upload-title">Upload a File</h2>
            <form enctype="multipart/form-data" action="filesOperation.php" method="POST" class="upload-form">
                <input type="hidden" name="MAX_FILE_SIZE" value="20000000">
                <label for="uploadfile_input" class="upload-label">Choose a file to upload:</label>
                <input name="uploadedfile" type="file" id="uploadfile_input" class="upload-input">
                <input type="submit" value="Upload File" class="button upload-button">
            </form>
        </section>
    </main>
</body>
</html>
