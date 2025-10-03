<?php
// tag_posts.php
// ------------------------
// This page displays all posts associated with a specific tag.
// ------------------------

// Include database configuration and helper functions
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get tag ID from URL parameter (?id=)
// If no ID provided, redirect to homepage
$tag_id = $_GET['id'] ?? null;
if(!$tag_id) header("Location:index.php");

// Fetch the tag details from the database
$tag = $pdo->prepare("SELECT * FROM tags WHERE id=?");
$tag->execute([$tag_id]);
$tag = $tag->fetch();

// If tag not found, redirect to homepage
if(!$tag) header("Location:index.php");

// ------------------------
// Fetch posts associated with this tag
// Join posts and post_tags table to get relevant posts
// Ordered by creation date (newest first)
// ------------------------
$posts = $pdo->prepare("
    SELECT p.* 
    FROM posts p 
    JOIN post_tags pt ON p.id = pt.post_id 
    WHERE pt.tag_id=? 
    ORDER BY p.created_at DESC
");
$posts->execute([$tag_id]);
$posts = $posts->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<!-- Display tag name -->
<h2>Tag: <?= htmlspecialchars($tag['name']) ?></h2>

<div class="row">
    <!-- Loop through each post and display as card -->
    <?php foreach($posts as $p): ?>
        <div class="col-md-4 mb-3">
            <div class="card">
                <!-- Display thumbnail if exists -->
                <?php if($p['thumbnail']): ?>
                    <img src="assets/uploads/<?= $p['thumbnail'] ?>" class="card-img-top">
                <?php endif; ?>
                <div class="card-body">
                    <!-- Post title -->
                    <h5><?= htmlspecialchars($p['title']) ?></h5>
                    <!-- Link to full post -->
                    <a href="post.php?id=<?= $p['id'] ?>" class="btn btn-primary">Read More</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
