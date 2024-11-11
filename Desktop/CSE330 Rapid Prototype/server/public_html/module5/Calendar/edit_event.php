<?php
ini_set("session.cookie_httponly", 1);
session_start();
require("database.php");
header("Content-Type: application/json");


if (!isset($_SESSION['username'])) {
    echo json_encode(array("success" => false, "msg" => "User not logged in."));
    exit;
}
$json_obj = json_decode(file_get_contents('php://input'), true);
if (!$json_obj || !isset($json_obj['id'])) {
    echo json_encode(array("success" => false, "msg" => "Invalid JSON input or event ID missing."));
    exit;
}

if (!isset($_SESSION['token']) || $_SESSION['token'] !== $json_obj['token']) {
    echo json_encode(array("success" => false, "msg" => "Invalid or missing CSRF token."));
    exit;
}

$event_id = $json_obj['id']; // Change 'event_id' to 'id'
$user_id = $_SESSION['user_id'];

// Check if it's a fetch request (no additional data provided)
if ($event_id && empty($_POST['name'])) {
    // Fetch the event details
    $stmt = $mysqli->prepare("SELECT name, date, type, duration FROM Events WHERE id = ?");
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

    if ($event) {
        echo json_encode(array("success" => true, "event" => $event));
    } else {
        echo json_encode(array("success" => false, "msg" => "Event not found."));
    }

    $stmt->close();
    exit;
}

$stmt->close();
?>
