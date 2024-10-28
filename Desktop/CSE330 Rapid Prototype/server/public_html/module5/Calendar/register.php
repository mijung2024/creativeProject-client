

<?php
ini_set("session.cookie_httponly", 1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("database.php");
header("Content-Type: application/json");

$json_obj = json_decode(file_get_contents('php://input'), true);

if (!$json_obj) {
    echo json_encode(array(
        "success" => false,
        "msg" => "Invalid JSON input."
    ));
    exit;
}

$username = $json_obj['username'];
$password = password_hash($json_obj['password'], PASSWORD_DEFAULT); 
$full_name = $json_obj['full_name']; 

//Check if there's any empty input
if (empty($full_name) || empty($username) || empty($password)) {
    echo json_encode(array(
        "success" => false,
        "msg" => "All fields are required to create an account."
    ));
    exit;
}

// Check for existing username
$stmt = $mysqli->prepare("SELECT id FROM Users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(array(
        "success" => false,
        "msg" => "Username already exists. Choose another username."
    ));
    exit;
}

$stmt->close();

$stmt = $mysqli->prepare("INSERT INTO Users (username, password, full_name) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $username, $password, $full_name);

if ($stmt->execute()) {
    echo json_encode(array(
        "success" => true,
    ));
} else {
    echo json_encode(array(
        "success" => false,
        "msg" => "Registration Error: " . $stmt->error
    ));
}

$stmt->close();
?>
