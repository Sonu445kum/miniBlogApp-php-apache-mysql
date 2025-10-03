<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if(!isLoggedIn()) exit(json_encode(['status'=>0,'msg'=>'Login required']));

$post_id = $_POST['post_id'] ?? null;
$content = cleanInput($_POST['content'] ?? '');
$user_id = $_SESSION['user_id'];

if($post_id && $content){
    $stmt = $pdo->prepare("INSERT INTO comments (post_id,user_id,content,created_at) VALUES (?,?,?,NOW())");
    $stmt->execute([$post_id,$user_id,$content]);
    echo json_encode(['status'=>1,'msg'=>'Comment added']);
}
?>