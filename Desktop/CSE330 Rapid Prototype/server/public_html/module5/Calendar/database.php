<?php
// Content of database.php
$mysqli = new mysqli('localhost', 'mijung', 'Practice@4713', 'module5');

if ($mysqli->connect_errno) {
    echo json_encode(array(
        "success" => false,
        "msg" => "Connection Failed: " . $mysqli->connect_error
    ));
    exit;
}

?>
