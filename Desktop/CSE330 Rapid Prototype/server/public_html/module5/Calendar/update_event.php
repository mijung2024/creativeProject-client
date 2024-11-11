<?php
ini_set("session.cookie_httponly", 1);
session_start();
header("Content-Type: application/json");
require("database.php");

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(array("success" => false, "msg" => "You must be logged in to update an event."));
    exit;
}

// Get JSON input
$json_obj = json_decode(file_get_contents('php://input'), true);

// Validate CSRF token if implemented
if (!isset($json_obj['token']) || $json_obj['token'] !== $_SESSION['token']) {
    echo json_encode(array("success" => false, "msg" => "Invalid session token."));
    exit;
}

// Validate and sanitize input
$event_id = isset($json_obj['id']) ? intval($json_obj['id']) : null;
$event_name = isset($json_obj['name']) ? htmlspecialchars(trim($json_obj['name'])) : null;
$event_date = isset($json_obj['date']) ? htmlspecialchars(trim($json_obj['date'])) : null;
$event_type = isset($json_obj['type']) ? htmlspecialchars(trim($json_obj['type'])) : null;
$duration = isset($json_obj['duration']) ? intval($json_obj['duration']) : null;

if (!$event_id || !$event_name || !$event_date || !$event_type || !$duration) {
    echo json_encode(array("success" => false, "msg" => "All fields are required."));
    exit;
}

// Fetch user ID based on the username
$username = $_SESSION['username'];
$stmt = $mysqli->prepare("SELECT id FROM Users WHERE username = ?");
if (!$stmt) {
    echo json_encode(array("success" => false, "msg" => "User verification failed."));
    exit;
}
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if (!$user_id) {
    echo json_encode(array("success" => false, "msg" => "User not found."));
    exit;
}

// Prepare SQL statement to update event
$stmt = $mysqli->prepare("UPDATE Events SET event_name = ?, event_date = ?, event_type = ?, duration = ? WHERE id = ? AND user_id = ?");
if (!$stmt) {
    echo json_encode(array("success" => false, "msg" => "Query preparation failed."));
    exit;
}

$stmt->bind_param('sssiii', $event_name, $event_date, $event_type, $duration, $event_id, $user_id);

// Execute the statement and check if the update was successful
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(array("success" => true, "msg" => "Event updated successfully."));
    } else {
        echo json_encode(array("success" => false, "msg" => "No changes were made or event not found."));
    }
} else {
    echo json_encode(array("success" => false, "msg" => "Failed to update event."));
}

// Close the statement
$stmt->close();
?>
