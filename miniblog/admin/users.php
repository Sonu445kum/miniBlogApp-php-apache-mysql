<?php
// ----------------------------
// Include required files
// ----------------------------
require_once '../includes/config.php';        // Database connection and session start
require_once '../includes/functions.php';     // Helper functions
require_once '../includes/middleare/admin.php'; // Admin check middleware to restrict access

// ----------------------------
// Fetch all users from the database
// ----------------------------
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC"); // Fetch users in descending order of ID
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If database error occurs, store error in session and set users as empty array
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    $users = [];
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>All Users</h2>

    <!-- Display Success Message -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <!-- Display Error Message -->
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Button to add a new user -->
    <a href="user_add.php" class="btn btn-primary mb-3">Add New User</a>

    <!-- Users Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>          <!-- User ID -->
                <th>Username</th>    <!-- Username -->
                <th>Email</th>       <!-- Email -->
                <th>Role</th>        <!-- Role: User/Admin -->
                <th>Actions</th>     <!-- Actions: Edit/Delete -->
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($users)): ?>
                <?php foreach($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars(ucfirst($user['role'])) ?></td>
                        <td>
                            <?php if($user['id'] != $_SESSION['user_id']): ?>
                                <!-- Edit user link -->
                                <a href="user_edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <!-- Delete user link with confirmation -->
                                <a href="user_delete.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            <?php else: ?>
                                <!-- Current logged-in user cannot delete themselves -->
                                <span class="text-muted">Self</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Display message if no users found -->
                <tr>
                    <td colspan="5" class="text-center">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
