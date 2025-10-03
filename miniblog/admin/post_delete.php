<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../middleware/admin.php';

$post_id = $_GET['id'] ?? null;
if($post_id){
    $pdo->prepare("DELETE FROM post_tags WHERE post_id=?")->execute([$post_id]);
    $pdo->prepare("DELETE FROM comments WHERE post_id=?")->execute([$post_id]);
    $pdo->prepare("DELETE FROM likes WHERE post_id=?")->execute([$post_id]);
    $pdo->prepare("DELETE FROM posts WHERE id=?")->execute([$post_id]);
}
header("Location: posts.php");
exit;
