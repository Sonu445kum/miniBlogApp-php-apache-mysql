<?php
// Include configuration (DB connection, session start, etc.) and helper functions
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ----------------------------
// Ensure user is logged in
// ----------------------------
if(!isLoggedIn()) {
    // If not logged in, return JSON response and exit
    exit(json_encode(['status'=>0,'msg'=>'Login required']));
}

// Get the post ID from AJAX request
$post_id = $_POST['post_id'] ?? null;

// Get current logged-in user's ID from session
$user_id = $_SESSION['user_id'];

// ----------------------------
// Check if post ID is provided
// ----------------------------
if($post_id){
    // Check if the user has already liked this post
    if(userLiked($post_id, $user_id)){
        // User already liked: remove like (unlike)
        $pdo->prepare("DELETE FROM likes WHERE post_id=? AND user_id=?")->execute([$post_id, $user_id]);
        $liked = 0; // Flag to indicate the post is now unliked
    } else {
        // User hasn't liked yet: add like
        $pdo->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)")->execute([$post_id, $user_id]);
        $liked = 1; // Flag to indicate the post is now liked
    }

    // Fetch updated like count for the post
    $count = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id=?")->execute([$post_id]); // Executes query
    $count = $pdo->query("SELECT COUNT(*) as cnt FROM likes WHERE post_id=$post_id")->fetch()['cnt']; // Get actual count

    // Return JSON response with status, liked flag, and updated count
    echo json_encode(['status'=>1,'liked'=>$liked,'count'=>$count]);
}
?>
