<?php
// Include configuration and helper functions
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../middleware/admin.php'; // Ensure only admin can access

// Get the post ID from URL parameter
$post_id = $_GET['id'] ?? null;

// Check if a post ID is provided
if ($post_id) {
    // Delete all tag associations for this post
    $pdo->prepare("DELETE FROM post_tags WHERE post_id=?")->execute([$post_id]);

    // Delete all comments related to this post
    $pdo->prepare("DELETE FROM comments WHERE post_id=?")->execute([$post_id]);

    // Delete all likes related to this post
    $pdo->prepare("DELETE FROM likes WHERE post_id=?")->execute([$post_id]);

    // Finally, delete the post itself from the posts table
    $pdo->prepare("DELETE FROM posts WHERE id=?")->execute([$post_id]);
}

// Redirect back to posts management page
header("Location: posts.php");
exit;
