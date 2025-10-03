<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../middleware/admin.php';

$comment_id = $_GET['id'] ?? null;
if($comment_id){
    $pdo->prepare("DELETE FROM comments WHERE id=?")->execute([$comment_id]);
}
header("Location: comments.php");
exit;
