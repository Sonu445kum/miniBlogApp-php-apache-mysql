<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Ensure base URL
$base_url = $base_url ?? 'http://localhost/miniBlogApp/miniblog';

$post_id = $_GET['id'] ?? null;
if (!$post_id) header("Location: index.php");

// Increment views
incrementViews($post_id);

// Fetch post with category
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id=?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch();
if (!$post) header("Location: index.php");

// Fetch comments
$comments = $pdo->prepare("
    SELECT c.*, u.username 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.post_id=? 
    ORDER BY c.created_at DESC
");
$comments->execute([$post_id]);
$comments = $comments->fetchAll();

// Count likes
$like_count = $pdo->prepare("SELECT COUNT(*) as cnt FROM likes WHERE post_id=?");
$like_count->execute([$post_id]);
$like_count = $like_count->fetch()['cnt'];

// Check if user liked
$user_liked = 0;
if (isLoggedIn()) {
    $user_liked = userLiked($post_id, $_SESSION['user_id']);
}

// Base URL for uploads (only folder name, no double "uploads/")
$uploads_path = $base_url . '/uploads';
?>

<?php include 'includes/header.php'; ?>

<div class="container my-4">
    <div class="card mb-4 shadow-sm">
        <?php if (!empty($post['thumbnail'])): ?>
            <img src="<?= $uploads_path ?>/<?= htmlspecialchars($post['thumbnail']) ?>" class="card-img-top img-fluid" alt="<?= htmlspecialchars($post['title']) ?>">
        <?php endif; ?>
        <div class="card-body">
            <h2 class="card-title"><?= htmlspecialchars($post['title']) ?></h2>
            <p class="text-muted">
                Category: <?= htmlspecialchars($post['category_name']) ?> | 
                Views: <?= $post['views'] ?> | 
                Posted on: <?= date('d M Y', strtotime($post['created_at'])) ?>
            </p>
            <div class="card-text mb-3"><?= $post['content'] ?></div>

            <button id="likeBtn" class="btn btn-<?= $user_liked ? 'danger' : 'outline-danger' ?>">
                ❤️ <span id="likeCount"><?= $like_count ?></span>
            </button>
        </div>
    </div>

    <!-- Comments Section -->
    <h4>Comments</h4>
    <div id="commentsList" class="mb-3">
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $c): ?>
                <div class="border p-2 mb-2 rounded">
                    <strong><?= htmlspecialchars($c['username']) ?>:</strong> <?= htmlspecialchars($c['content']) ?>
                    <br><small class="text-muted"><?= date('d M Y H:i', strtotime($c['created_at'])) ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>
    </div>

    <?php if (isLoggedIn()): ?>
        <form id="commentForm">
            <input type="hidden" name="post_id" value="<?= $post_id ?>">
            <textarea name="content" class="form-control mb-2" placeholder="Write a comment..." required></textarea>
            <button class="btn btn-primary">Add Comment</button>
        </form>
    <?php else: ?>
        <p><a href="auth/login.php">Login</a> to comment.</p>
    <?php endif; ?>
</div>

<!-- JS assets -->
<script src="<?= $base_url ?>/assets/js/jquery-3.6.0.min.js"></script>
<script>
$('#likeBtn').click(function(){
    $.post('<?= $base_url ?>/like.php', {post_id: <?= $post_id ?>}, function(res){
        let data = JSON.parse(res);
        if(data.status){
            $('#likeCount').text(data.count);
            if(data.liked) $('#likeBtn').removeClass('btn-outline-danger').addClass('btn-danger');
            else $('#likeBtn').removeClass('btn-danger').addClass('btn-outline-danger');
        }
    });
});

$('#commentForm').submit(function(e){
    e.preventDefault();
    $.post('<?= $base_url ?>/comment.php', $(this).serialize(), function(res){
        let data = JSON.parse(res);
        if(data.status) location.reload();
        else alert(data.msg);
    });
});
</script>

<?php include 'includes/footer.php'; ?>
