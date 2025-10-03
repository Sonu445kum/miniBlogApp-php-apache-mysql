<?php
// ----------------------------
// Include required files
// ----------------------------
require_once '../includes/config.php';    // Database connection & config
require_once '../includes/functions.php'; // Common helper functions
require_once '../middleware/admin.php';   // Only allow admin access

// ----------------------------
// Fetch all comments with related user and post information
// ----------------------------
// Using JOIN to get the username of the comment author (users table) 
// and the title of the post (posts table) for each comment.
// Ordered by newest comments first (created_at DESC)
$comments = $pdo->query("
    SELECT 
        c.*,       -- All columns from comments table
        u.username, -- Comment author's username
        p.title     -- Title of the post the comment belongs to
    FROM comments c
    JOIN users u ON c.user_id = u.id
    JOIN posts p ON c.post_id = p.id
    ORDER BY c.created_at DESC
")->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>All Comments</h2>

    <!-- Comments Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>         <!-- Comment ID -->
                <th>User</th>       <!-- Comment author's username -->
                <th>Post</th>       <!-- Post title the comment belongs to -->
                <th>Comment</th>    <!-- Comment content -->
                <th>Date</th>       <!-- Date comment was created -->
                <th>Actions</th>    <!-- Action buttons like delete -->
            </tr>
        </thead>
        <tbody>
            <?php foreach($comments as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['username']) ?></td>
                <td><?= htmlspecialchars($c['title']) ?></td>
                <td><?= htmlspecialchars($c['content']) ?></td>
                <td><?= $c['created_at'] ?></td>
                <td>
                    <!-- Delete comment button with JS confirm -->
                    <a href="comment_delete.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
