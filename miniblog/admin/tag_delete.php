<?php
// ----------------------------
// Include required files
// ----------------------------
require_once '../includes/config.php';    // Database connection and session start
require_once '../includes/functions.php'; // Helper functions
require_once '../middleware/admin.php';   // Ensure only admin can access this page

// ----------------------------
// Get the tag ID from query parameter
// ----------------------------
$tag_id = $_GET['id'] ?? null;

// ----------------------------
// If tag ID exists, delete the tag
// ----------------------------
if($tag_id){
    // 1. Delete any associations of this tag with posts from post_tags table
    $pdo->prepare("DELETE FROM post_tags WHERE tag_id=?")->execute([$tag_id]);

    // 2. Delete the tag itself from the tags table
    $pdo->prepare("DELETE FROM tags WHERE id=?")->execute([$tag_id]);
}

// ----------------------------
// Redirect back to the tags list page
// ----------------------------
header("Location: tags.php");
exit;
