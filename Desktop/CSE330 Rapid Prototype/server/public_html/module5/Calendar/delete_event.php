<?php
ini_set("session.cookie_httponly", 1);

session_start();
header("Content-Type: application/json");
require("database.php");

if (!isset($_SESSION['username'])) {
    echo json_encode(array("success" => false, "msg" => "You must be logged in to delete."));
    exit;
}

$json_obj = json_decode(file_get_contents('php://input'), true);

if (!$json_obj || !isset($json_obj['event_id'])) {
    echo json_encode(array("success" => false, "msg" => "Invalid JSON input or event ID missing."));
    exit;
}

if (!isset($_SESSION['token']) || $_SESSION['token'] !== $json_obj['token']) {
    echo json_encode(array("success" => false, "msg" => "Invalid or missing CSRF token."));
    exit;
}

$event_id = $json_obj['event_id'];
$user_id = $_SESSION['user_id'];

$stmt = $mysqli->prepare("SELECT user_id FROM Events WHERE id = ?");
$stmt->bind_param('i', $event_id);
$stmt->execute();
$stmt->bind_result($owner_id);
$stmt->fetch();
$stmt->close();

if ($owner_id !== $user_id) {
    echo json_encode(array("success" => false, "msg" => "Unauthorized deletion attempt."));
    exit;
}

// Delete the event
$stmt = $mysqli->prepare("DELETE FROM Events WHERE id = ?");
$stmt->bind_param('i', $event_id);

if ($stmt->execute()) {
    echo json_encode(array("success" => true, "msg" => "Event deleted successfully."));
} else {
    echo json_encode(array("success" => false, "msg" => "Failed to delete event."));
}

$stmt->close();
?>