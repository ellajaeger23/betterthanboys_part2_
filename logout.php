<?php
session_start();

// Remove all session variables
session_unset();

// Destroy the session completely
session_destroy();

// Optional: Clear the session cookie (extra safety)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect back to login page
header("Location: login.php");
exit;
?>
