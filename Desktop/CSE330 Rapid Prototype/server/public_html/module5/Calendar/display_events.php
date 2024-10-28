<?php
ini_set("session.cookie_httponly", 1);

session_start();
require("database.php");
header("Content-Type: application/json");
/* 
// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(array("success" => false, "msg" => "You must be logged in to display events."));
    exit;
}
 */
// Get the JSON input
$json_input = file_get_contents('php://input');
$json_obj = json_decode($json_input, true);



$user_id = $_SESSION['user_id'];
$token = $json_obj['token'];

if($token != $_SESSION['token']){
    echo json_encode(array(
        "success" => false,
        "message" => "Tokens UNMATCH"
    ));
    exit;
}


// Prepare and execute the statement to fetch events
$stmt = $mysqli->prepare("SELECT id, event_name, event_date, event_type, duration, created_at FROM Events WHERE user_id = ?");
if ($stmt) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $events = array();
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }

    echo json_encode(array("success" => true, "events" => $events));

    $stmt->close();
} else {
    echo json_encode(array("success" => false, "msg" => "Database error: Unable to prepare statement."));
}
?>
