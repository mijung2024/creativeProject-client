
<?php
ini_set("session.cookie_httponly", 1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("database.php");
header("Content-Type: application/json");

$rawData = file_get_contents('php://input');
$json_obj = json_decode($rawData, true);

if (!$json_obj) {
    echo json_encode(array(
        "success" => false,
        "msg" => "Invalid JSON input."
    ));
    error_log("Raw input received: " . $rawData); 
    exit;
}


$username = $json_obj['username'];
$password = $json_obj['password'];
$msg = '';

$stmt = $mysqli->prepare("SELECT password, full_name FROM Users WHERE username = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "msg" => "Query prepare failed: " . $mysqli->error
    ));
    exit;
}

$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($hashedPassword, $fullName);

if (!$stmt->fetch()) {
    $msg = "No user with the following username.";
} else {
    if (password_verify($password, $hashedPassword)) {
        ini_set("session.cookie_httponly", 1);
        session_start();
        $_SESSION['username'] = $username; 
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));

    
        echo json_encode(array(
            "success" => true,
            "username" => $username,  
            "fullname" => $fullName,
            "token" => $_SESSION['token']
        ));
        exit;
    }else {
        $msg = "Wrong password! Register if you do not have an account here.";
    } 
}

echo json_encode(array(
    "success" => false,
    "msg" => $msg
));

$stmt->close();
?>
