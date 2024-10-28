<?php
session_start();
require("database.php");
header("Content-Type: application/json");

if (!isset($_SESSION['username'])) {
    echo json_encode(array("success" => false, "msg" => "User not logged in."));
    exit;
}

$username = $_SESSION['username'];

// Assuming user_id can be fetched using the username
$stmt = $mysqli->prepare("SELECT id FROM Users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

if (!$userId) {
    echo json_encode(array("success" => false, "msg" => "User not found."));
    exit;
}

// Get current month and year from request
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

$stmt = $mysqli->prepare("SELECT event_name, event_date, event_type FROM Events WHERE user_id = ? AND MONTH(event_date) = ? AND YEAR(event_date) = ?");
$stmt->bind_param('iii', $userId, $month, $year);
$stmt->execute();
$stmt->bind_result($eventName, $eventDate, $eventType);

$events = [];
while ($stmt->fetch()) {
    $events[] = [
        'name' => $eventName,
        'date' => $eventDate,
        'type' => $eventType
    ];
}

$stmt->close();
echo json_encode(array("success" => true, "events" => $events));
?>
