<?php
session_start();
header("Content-Type: application/json");

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array("success" => false, "msg" => "Invalid request method."));
    exit;
}

// Get the JSON input
$json_obj = json_decode(file_get_contents('php://input'), true);

// Check if the token is provided
if (!isset($json_obj['token'])) {
    echo json_encode(array("success" => false, "msg" => "No token provided."));
    exit;
}

$token = $json_obj['token'];

// Check if the token is valid (this is a placeholder for your validation logic)
if ($token === $_SESSION['token'] && !empty($token)) {
    // Assuming the session has the username and full name stored upon successful login
    $username = $_SESSION['username'];
    $fullname = $_SESSION['fullname']; // Make sure you store this during login

    echo json_encode(array("success" => true, "msg" => "Token is valid.", "fullname" => $fullname));
} else {
    echo json_encode(array("success" => false, "msg" => "Invalid token."));
}
?>
