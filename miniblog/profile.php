<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if(session_status()===PHP_SESSION_NONE) session_start();

// Check if user is logged in
if(!isLoggedIn()){
    header("Location: auth/login.php");
    exit;
}

// Fetch user data from database
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, email, role, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if(!$user){
    echo "<div class='container mt-4'><div class='alert alert-danger'>User not found.</div></div>";
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2>Profile</h2>
    <div class="card p-3">
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <p><strong>Member since:</strong> <?= date('d M Y, H:i', strtotime($user['created_at'])) ?></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
