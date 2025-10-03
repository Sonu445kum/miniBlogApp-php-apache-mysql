<?php
// ----------------------------
// Include required files
// ----------------------------
require_once '../includes/config.php';      // Database connection
require_once '../includes/functions.php';   // Helper functions (e.g., auth checks)
require_once '../middleware/admin.php';     // Ensure only admin can access

// ----------------------------
// Get comment ID from query string
// ----------------------------
$comment_id = $_GET['id'] ?? null; // Get 'id' parameter if exists, otherwise null

// ----------------------------
// Delete comment if ID is provided
// ----------------------------
if ($comment_id) {
    // Prepare and execute deletion using PDO
    $pdo->prepare("DELETE FROM comments WHERE id = ?")->execute([$comment_id]);
}

// ----------------------------
// Redirect back to comments list
// ----------------------------
header("Location: comments.php");
exit; // Stop further script execution
