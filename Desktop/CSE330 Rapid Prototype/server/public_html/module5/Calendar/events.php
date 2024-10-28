<?php
ini_set("session.cookie_httponly", 1);

session_start();
require("database.php"); // Ensure this file correctly connects to your database
header("Content-Type: application/json");

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(array("success" => false, "msg" => "User not logged in."));
    exit;
}

$username = $_SESSION['username'];

// Fetch user ID based on the username
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

// Fetch events along with their IDs
$stmt = $mysqli->prepare("SELECT id, event_name, event_date, event_type, duration FROM Events WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$events = array();
while ($row = $result->fetch_assoc()) {
    // Create DateTime object from event_date for formatting
    $eventDateTime = new DateTime($row['event_date']);
    $formattedTime = $eventDateTime->format('g:ia'); // Format time to 12-hour format with AM/PM

    // Build event array including ID
    $events[] = array(
        'id' => $row['id'],  // Include event ID
        'name' => $row['event_name'],
        'date' => $eventDateTime->format('Y-m-d'), // Format date to 'YYYY-MM-DD'
        'time' => $formattedTime,
        'type' => $row['event_type'],
        'duration' => $row['duration'] // You might want to include this too
    );
}

// Close the statement
$stmt->close();

// Return JSON response with events
echo json_encode(array("success" => true, "events" => $events));
?>
