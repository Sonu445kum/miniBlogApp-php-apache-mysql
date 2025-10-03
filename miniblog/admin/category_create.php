<?php
// Include database configuration and helper functions
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Include admin middleware to ensure only admins can access this page
require_once '../middleware/admin.php';

// Initialize an array to store validation errors
$errors = [];

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Clean and retrieve the category name from POST data
    $name = cleanInput($_POST['name']);

    // Validate: Check if name is empty
    if(empty($name)) $errors[] = "Category name required.";

    // Check if category with same name already exists in database
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE name=?");
    $stmt->execute([$name]);
    if($stmt->rowCount() > 0) $errors[] = "Category already exists.";

    // If no errors, insert new category into database
    if(empty($errors)){
        $pdo->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$name]);

        // Redirect to categories list page after successful insertion
        header("Location: categories.php");
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>

<!-- Page Title -->
<h2>Add Category</h2>

<!-- Display validation errors -->
<?php if(!empty($errors)): ?>
<div class="alert alert-danger">
    <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
</div>
<?php endif; ?>

<!-- Form to add a new category -->
<form method="POST">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <button class="btn btn-success">Add Category</button>
</form>

<?php include '../includes/footer.php'; ?>
