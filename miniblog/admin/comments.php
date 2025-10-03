<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../middleware/admin.php';

$comments = $pdo->query("SELECT c.*, u.username, p.title 
                         FROM comments c
                         JOIN users u ON c.user_id = u.id
                         JOIN posts p ON c.post_id = p.id
                         ORDER BY c.created_at DESC")->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<h2>All Comments</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Post</th>
            <th>Comment</th>
            <th>Date</th>
            <th>Actions</th>
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
                <a href="comment_delete.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>
