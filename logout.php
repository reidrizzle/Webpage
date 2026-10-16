<?php
/**
 * User Logout Handler
 */
require_once __DIR__ . '/config/db.php';

// Unset all session variables
$_SESSION = [];

// Destroy the session cookie if present
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

// Destroy session
session_destroy();

// Redirect to home page
header("Location: index.php");
exit;
