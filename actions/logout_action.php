<?php
// actions/logout_action.php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// If you prefer JSON (AJAX), uncomment the JSON response and comment out header redirect
// header('Content-Type: application/json');
// echo json_encode(['success'=>true, 'message'=>'Logged out']);
header('Location: ../index.php');
exit;
