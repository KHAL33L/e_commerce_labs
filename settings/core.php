<?php
// settings/core.php
// Put this file in settings/core.php and include it wherever you need session/privilege helpers.

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Check whether a user is logged in.
 * Returns true if session contains customer_id, false otherwise.
 */
function is_logged_in(): bool {
    return !empty($_SESSION['customer_id']);
}

/**
 * Check whether current user is admin.
 * We assume admin role is indicated by user_role === 1 (adjust if your schema differs).
 * Return true for admin, false otherwise.
 */
function is_admin(): bool {
    // If no session, not admin
    if (!is_logged_in()) return false;
    // user_role stored in session earlier upon login
    $role = $_SESSION['user_role'] ?? null;
    // treat 1 as admin (you can change the numeric value if your system uses a different code)
    return (int)$role === 1;
}
