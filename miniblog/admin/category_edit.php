<?php
// Include database configuration and helper functions
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Include admin middleware to ensure only admins can access this page
require_once '../middleware/admin.php';

// Get the category ID from the URL parameter "id"
// If no ID is provided, redirect back to categories list
$cat_id = $_GET['id'] ?? null;
if(!$cat_id) header("Location: categories.php");

// Fetch the category details from the database
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id=?");
$stmt->execute([$cat_id]);
$cat = $stmt->fetch();

// If category not found, redirect back to categories list
if(!$cat) header("Location: categories.php");

// Initialize errors array to store validation errors
$errors = [];

// Handle form submission for updating category
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Clean and get the category name from POST
    $name = cleanInput($_POST['name']);
    
    // Validate: category name should not be empty
    if(empty($name)) $errors[] = "Category name required.";

    // If no errors, update the category in the database
    if(empty($errors)){
        $pdo->prepare("UPDATE categories SET name=? WHERE id=?")->execute([$name, $cat_id]);
        
        // Redirect back to categories list after successful update
        header("Location: categories.php");
        exit; // Ensure no further code runs
    }
}
?>

<?php include '../includes/header.php'; ?>

<h2>Edit Category</h2>

<!-- Display validation errors if any -->
<?php if(!empty($errors)): ?>
<div class="alert alert-danger">
    <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
</div>
<?php endif; ?>

<!-- Edit Category Form -->
<form method="POST">
    <div class="mb-3">
        <label>Name</label>
        <!-- Pre-fill input with current category name -->
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($cat['name']) ?>" required>
    </div>
    <button class="btn btn-primary">Update</button>
</form>

<?php include '../includes/footer.php'; ?>
