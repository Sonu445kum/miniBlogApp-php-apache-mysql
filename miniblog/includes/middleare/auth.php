<?php
// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit;
}

// Optional: Store user info in a variable for easy access
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? '';
?>
