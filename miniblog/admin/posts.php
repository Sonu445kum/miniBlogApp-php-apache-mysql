<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/middleare/admin.php'; // Ensure only admin can access

// Handle delete post safely
if(isset($_GET['delete'])){
    $post_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $_SESSION['success'] = "Post deleted successfully.";
    } catch (PDOException $e){
        $_SESSION['error'] = "Error deleting post: " . $e->getMessage();
    }
    header('Location: posts.php');
    exit;
}

// Fetch all posts with author & category
try {
    $stmt = $pdo->query("
        SELECT 
            p.id, 
            p.title, 
            p.slug, 
            p.views, 
            p.created_at, 
            COALESCE(u.username, '—') AS author, 
            COALESCE(c.name, '—') AS category
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC
    ");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e){
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    $posts = [];
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Manage Posts</h2>

    <!-- Success/Error Messages -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <a href="post_add.php" class="btn btn-primary mb-3">Add New Post</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Views</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($posts)): ?>
                <?php foreach($posts as $post): ?>
                    <tr>
                        <td><?= $post['id'] ?></td>
                        <td><?= htmlspecialchars($post['title']) ?></td>
                        <td><?= htmlspecialchars($post['author']) ?></td>
                        <td><?= htmlspecialchars($post['category']) ?></td>
                        <td><?= $post['views'] ?></td>
                        <td><?= $post['created_at'] ?></td>
                        <td>
                            <a href="post_edit.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="posts.php?delete=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No posts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
