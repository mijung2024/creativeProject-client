
<?php
ini_set("session.cookie_httponly", 1);

// logout.php
session_start();
header("Content-Type: application/json");

// Unset all session variables
$_SESSION = array();


if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

echo json_encode(array("success" => true, "msg" => "Successfully logged out."));
?>
