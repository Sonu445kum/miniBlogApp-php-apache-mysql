<?php
// ----------------------------
// Include required files
// ----------------------------
require_once '../includes/config.php';    // Database connection & session start
require_once '../includes/functions.php'; // Helper functions (e.g., isLoggedIn, isAdmin)

// ----------------------------
// Start session if not already started
// ----------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ----------------------------
// Admin access check
// ----------------------------
if(!isLoggedIn() || !isAdmin()){
    // Redirect non-admin users to login page
    header('Location: ../auth/login.php');
    exit;
}

// ----------------------------
// Handle Add New Tag
// ----------------------------
if(isset($_POST['add'])){
    // Trim and sanitize tag name
    $name = trim($_POST['name']);

    if($name){
        // Insert new tag into the database
        $stmt = $pdo->prepare("INSERT INTO tags (name) VALUES (?)");
        $stmt->execute([$name]);

        // Store success message in session and redirect
        $_SESSION['success'] = "Tag added.";
        header('Location: tags.php');
        exit;
    }
}

// ----------------------------
// Handle Delete Tag
// ----------------------------
if(isset($_GET['delete'])){
    // Get tag ID and cast to integer
    $id = (int)$_GET['delete'];

    // Delete tag from database
    $stmt = $pdo->prepare("DELETE FROM tags WHERE id = ?");
    $stmt->execute([$id]);

    // Store success message in session and redirect
    $_SESSION['success'] = "Tag deleted.";
    header('Location: tags.php');
    exit;
}

// ----------------------------
// Fetch all tags for display
// ----------------------------
$tags = $pdo->query("SELECT * FROM tags ORDER BY name ASC")->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Manage Tags</h2>

    <!-- Display success message if any -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <!-- Form to add new tag -->
    <form method="POST" class="mb-3">
        <div class="input-group">
            <input type="text" name="name" class="form-control" placeholder="New tag name" required>
            <button class="btn btn-primary" type="submit" name="add">Add</button>
        </div>
    </form>

    <!-- Display all tags in a table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($tags as $tag): ?>
            <tr>
                <td><?= $tag['id'] ?></td>
                <td><?= htmlspecialchars($tag['name']) ?></td>
                <td>
                    <!-- Delete action -->
                    <a href="tags.php?delete=<?= $tag['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this tag?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
