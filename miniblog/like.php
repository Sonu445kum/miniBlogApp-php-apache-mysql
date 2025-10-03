<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if(!isLoggedIn()) exit(json_encode(['status'=>0,'msg'=>'Login required']));
$post_id = $_POST['post_id'] ?? null;
$user_id = $_SESSION['user_id'];

if($post_id){
    if(userLiked($post_id,$user_id)){
        $pdo->prepare("DELETE FROM likes WHERE post_id=? AND user_id=?")->execute([$post_id,$user_id]);
        $liked = 0;
    } else {
        $pdo->prepare("INSERT INTO likes (post_id,user_id) VALUES (?,?)")->execute([$post_id,$user_id]);
        $liked = 1;
    }
    $count = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id=?")->execute([$post_id]);
    $count = $pdo->query("SELECT COUNT(*) as cnt FROM likes WHERE post_id=$post_id")->fetch()['cnt'];
    echo json_encode(['status'=>1,'liked'=>$liked,'count'=>$count]);
}
?>