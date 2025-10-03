<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/middleare/admin.php';

$user_id = $_GET['id'] ?? null;
if($user_id && $user_id != $_SESSION['user_id']){
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$user_id]);
}
header("Location: users.php");
exit;
