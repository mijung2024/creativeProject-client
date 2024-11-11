<?php
ini_set("session.cookie_httponly", 1);

session_start();
require("database.php");
header("Content-Type: application/json");

if (!isset($_SESSION['username'])) {
    echo json_encode(array("success" => false, "msg" => "User not logged in."));
    exit;
}

$username = $_SESSION['username'];

$stmt = $mysqli->prepare("SELECT id FROM Users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if (!$user_id) {
    echo json_encode(array("success" => false, "msg" => "User not found."));
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$eventName = $data['name'];
$eventDate = $data['date'];
$eventType = $data['type'];
$duration = $data['duration'];

$stmt = $mysqli->prepare("INSERT INTO Events (user_id, event_name, event_date, event_type, duration) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isssi", $user_id, $eventName, $eventDate, $eventType, $duration);

if ($stmt->execute()) {
    echo json_encode(array("success" => true));
} else {
    echo json_encode(array("success" => false, "msg" => "Error adding event."));
}

$stmt->close();
?>