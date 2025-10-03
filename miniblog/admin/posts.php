<?php
// Include configuration, helper functions, and admin middleware
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/middleare/admin.php'; // Ensure only admin can access this page

// ----------------------------
// Handle post deletion if requested
// ----------------------------
if(isset($_GET['delete'])){
    $post_id = (int)$_GET['delete']; // Get post ID from URL and cast to integer for safety
    try {
        // Delete post from database
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);

        // Set success message for user feedback
        $_SESSION['success'] = "Post deleted successfully.";
    } catch (PDOException $e){
        // Catch any database errors and show error message
        $_SESSION['error'] = "Error deleting post: " . $e->getMessage();
    }
    // Redirect back to posts page after deletion
    header('Location: posts.php');
    exit;
}

// ----------------------------
// Fetch all posts along with author username and category name
// ----------------------------
try {
    $stmt = $pdo->query("
        SELECT 
            p.id, 
            p.title, 
            p.slug, 
            p.views, 
            p.created_at, 
            COALESCE(u.username, '—') AS author,  -- Show dash if author missing
            COALESCE(c.name, '—') AS category    -- Show dash if category missing
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC              -- Latest posts first
    ");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e){
    // If database query fails, set error message and empty posts array
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    $posts = [];
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Manage Posts</h2>

    <!-- Display success message if any -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <!-- Display error message if any -->
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Button to add new post -->
    <a href="post_add.php" class="btn btn-primary mb-3">Add New Post</a>

    <!-- Posts table -->
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
            <!-- Check if there are posts -->
            <?php if(!empty($posts)): ?>
                <!-- Loop through each post and display data -->
                <?php foreach($posts as $post): ?>
                    <tr>
                        <td><?= $post['id'] ?></td>
                        <td><?= htmlspecialchars($post['title']) ?></td>
                        <td><?= htmlspecialchars($post['author']) ?></td>
                        <td><?= htmlspecialchars($post['category']) ?></td>
                        <td><?= $post['views'] ?></td>
                        <td><?= $post['created_at'] ?></td>
                        <td>
                            <!-- Edit button -->
                            <a href="post_edit.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <!-- Delete button with confirmation -->
                            <a href="posts.php?delete=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Show message if no posts found -->
                <tr>
                    <td colspan="7" class="text-center">No posts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
