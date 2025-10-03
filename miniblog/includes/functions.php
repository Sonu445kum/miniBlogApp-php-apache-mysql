<?php
require_once 'config.php';

// Sanitize input
function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect
function redirect($url) {
    header("Location: $url");
    exit;
}

// Get categories
function getCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    return $stmt->fetchAll();
}

// Get tags
function getTags() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM tags ORDER BY name ASC");
    return $stmt->fetchAll();
}

// Count views
function incrementViews($post_id) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
    $stmt->execute([$post_id]);
}

// Like check
function userLiked($post_id, $user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    return $stmt->rowCount() > 0;
}
?>
