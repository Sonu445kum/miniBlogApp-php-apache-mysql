<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include functions to use isLoggedIn() and isAdmin()
require_once __DIR__ . '/../../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = "Please login to access this page.";
    header('Location: ../auth/login.php');
    exit;
}

// Check if user is admin
if (!isAdmin()) {
    $_SESSION['error'] = "You do not have permission to access this page.";
    header('Location: ../auth/login.php');
    exit;
}

// Optional: store user info in variables
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? '';
