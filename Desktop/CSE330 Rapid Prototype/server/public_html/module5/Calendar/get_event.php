<?php
ini_set("session.cookie_httponly", 1);
session_start();
header("Content-Type: application/json");
require("database.php"); // Ensure this file correctly connects to your database

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(array("success" => false, "msg" => "You must be logged in to view event details."));
    exit;
}

// Get JSON input
$json_obj = json_decode(file_get_contents('php://input'), true);

// Get event ID from the request
$event_id = isset($json_obj['id']) ? intval($json_obj['id']) : null; // Sanitize input

// Fetch user ID based on the username
$username = $_SESSION['username'];
$stmt = $mysqli->prepare("SELECT id FROM Users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Check if user ID was found
if (!$user_id) {
    echo json_encode(array("success" => false, "msg" => "User not found."));
    exit;
}

// Prepare statement to fetch event details
$stmt = $mysqli->prepare("SELECT event_name, event_date, event_type, duration FROM Events WHERE id = ? AND user_id = ?");
$stmt->bind_param('ii', $event_id, $user_id);
$stmt->execute();
$stmt->store_result(); // Store result to check number of rows

// Check if event was found
if ($stmt->num_rows > 0) {
    // Bind result variables
    $stmt->bind_result($event_name, $event_date, $event_type, $duration);
    $stmt->fetch(); // Fetch the result

    // Return JSON response with event details
    echo json_encode(array(
        "success" => true,
        "event" => array(
            "id" => $event_id,
            "name" => $event_name,
            "date" => $event_date,
            "type" => $event_type,
            "duration" => $duration
        )
    ));
} else {
    echo json_encode(array("success" => false, "msg" => "Event not found or access denied."));
}

// Close the statement
$stmt->close();
?>
