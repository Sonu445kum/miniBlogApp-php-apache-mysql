<?php
// Include configuration and utility functions
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if(!isLoggedIn() || !isAdmin()){
    // Redirect non-admins to login page
    header('Location: ../auth/login.php');
    exit;
}

// ======================
// Handle Add New Category
// ======================
if(isset($_POST['add'])){
    // Get the category name from form and trim whitespace
    $name = trim($_POST['name']);

    if($name){
        // Insert new category into the database
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);

        // Set success message in session and redirect
        $_SESSION['success'] = "Category added.";
        header('Location: categories.php');
        exit;
    }
}

// ======================
// Handle Delete Category
// ======================
if(isset($_GET['delete'])){
    // Get the category ID from query string
    $id = (int)$_GET['delete'];

    // Delete the category from database
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);

    // Set success message and redirect
    $_SESSION['success'] = "Category deleted.";
    header('Location: categories.php');
    exit;
}

// ======================
// Fetch all categories
// ======================
// This will be displayed in the table below
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Manage Categories</h2>

    <!-- Display success message if available -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <!-- Form to add new category -->
    <form method="POST" class="mb-3">
        <div class="input-group">
            <input type="text" name="name" class="form-control" placeholder="New category name" required>
            <button class="btn btn-primary" type="submit" name="add">Add</button>
        </div>
    </form>

    <!-- Table displaying all categories -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th> <!-- Delete button -->
            </tr>
        </thead>
        <tbody>
            <?php foreach($categories as $cat): ?>
            <tr>
                <td><?= $cat['id'] ?></td>
                <td><?= htmlspecialchars($cat['name']) ?></td>
                <td>
                    <!-- Delete category button -->
                    <a href="categories.php?delete=<?= $cat['id'] ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('Delete this category?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
