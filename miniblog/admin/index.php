<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin check
if(!isLoggedIn() || !isAdmin()){
    header('Location: ../auth/login.php');
    exit;
}

// Fetch stats
$total_users      = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_posts      = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$total_comments   = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Admin Dashboard</h2>
    <div class="row mt-3">
        <!-- Total Users -->
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Users</h5>
                    <p class="card-text fs-3"><?= $total_users ?></p>
                </div>
            </div>
        </div>

        <!-- Total Posts -->
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Posts</h5>
                    <p class="card-text fs-3"><?= $total_posts ?></p>
                </div>
            </div>
        </div>

        <!-- Total Comments -->
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Comments</h5>
                    <p class="card-text fs-3"><?= $total_comments ?></p>
                </div>
            </div>
        </div>

        <!-- Total Categories -->
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Categories</h5>
                    <p class="card-text fs-3"><?= $total_categories ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick links -->
    <div class="mt-4">
        <a href="users.php" class="btn btn-primary me-2">Manage Users</a>
        <a href="posts.php" class="btn btn-primary me-2">Manage Posts</a>
        <a href="categories.php" class="btn btn-secondary me-2">Manage Categories</a>
        <a href="tags.php" class="btn btn-success me-2">Manage Tags</a>
        <a href="../index.php" class="btn btn-dark">Go to Site</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
