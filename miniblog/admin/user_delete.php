<?php
// ----------------------------
// Include required files
// ----------------------------
require_once '../includes/config.php';     // Config file (includes database connection and session start)
require_once '../includes/functions.php';  // Helper functions
require_once '../includes/middleare/admin.php'; // Admin check middleware to restrict access

// ----------------------------
// Get the user ID to delete from query string
// ----------------------------
$user_id = $_GET['id'] ?? null;

// ----------------------------
// Delete the user
// ----------------------------
// Only delete if a valid user ID is provided
// and the user is not trying to delete themselves
if ($user_id && $user_id != $_SESSION['user_id']) {
    // Prepare and execute the DELETE query using PDO
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$user_id]);
}

// ----------------------------
// Redirect back to users list
// ----------------------------
header("Location: users.php");
exit;
