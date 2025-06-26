<?php
// Start the session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie (if it exists)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Prevent caching to avoid back button showing previous page
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false);     // For IE
header("Pragma: no-cache");                                    // HTTP 1.0
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");                // In the past

// Redirect to login page
header("Location: login.php");
exit();
?>
