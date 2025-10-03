<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../middleware/admin.php';

$tag_id = $_GET['id'] ?? null;
if($tag_id){
    $pdo->prepare("DELETE FROM post_tags WHERE tag_id=?")->execute([$tag_id]);
    $pdo->prepare("DELETE FROM tags WHERE id=?")->execute([$tag_id]);
}
header("Location: tags.php");
exit;
