<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../middleware/admin.php';

$cat_id = $_GET['id'] ?? null;
if($cat_id){
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$cat_id]);
}
header("Location: categories.php");
exit;
