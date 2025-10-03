<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$tag_id = $_GET['id'] ?? null;
if(!$tag_id) header("Location:index.php");

$tag = $pdo->prepare("SELECT * FROM tags WHERE id=?");
$tag->execute([$tag_id]);
$tag = $tag->fetch();
if(!$tag) header("Location:index.php");

// Fetch posts
$posts = $pdo->prepare("SELECT p.* FROM posts p 
                        JOIN post_tags pt ON p.id = pt.post_id 
                        WHERE pt.tag_id=? ORDER BY p.created_at DESC");
$posts->execute([$tag_id]);
$posts = $posts->fetchAll();
?>

<?php include 'includes/header.php'; ?>
<h2>Tag: <?= htmlspecialchars($tag['name']) ?></h2>

<div class="row">
<?php foreach($posts as $p): ?>
    <div class="col-md-4 mb-3">
        <div class="card">
            <?php if($p['thumbnail']): ?>
            <img src="assets/uploads/<?= $p['thumbnail'] ?>" class="card-img-top">
            <?php endif; ?>
            <div class="card-body">
                <h5><?= htmlspecialchars($p['title']) ?></h5>
                <a href="post.php?id=<?= $p['id'] ?>" class="btn btn-primary">Read More</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php include 'includes/footer.php'; ?>
