<?php
// ----------------------------
// Include required files
// ----------------------------
require_once '../includes/config.php';    // Database connection & session start
require_once '../includes/functions.php'; // Helper functions (e.g., cleanInput)
require_once '../middleware/admin.php';   // Ensure only admin can access this page

// ----------------------------
// Get tag ID from query parameter
// ----------------------------
$tag_id = $_GET['id'] ?? null;

// If tag ID is not provided, redirect back to tags list
if(!$tag_id) header("Location: tags.php");

// ----------------------------
// Fetch the tag from the database
// ----------------------------
$stmt = $pdo->prepare("SELECT * FROM tags WHERE id=?");
$stmt->execute([$tag_id]);
$tag = $stmt->fetch();

// If tag not found, redirect back
if(!$tag) header("Location: tags.php");

// ----------------------------
// Initialize errors array
// ----------------------------
$errors = [];

// ----------------------------
// Handle form submission
// ----------------------------
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Sanitize input
    $name = cleanInput($_POST['name']);

    // Validate
    if(empty($name)) $errors[] = "Tag name required.";

    // If no errors, update the tag
    if(empty($errors)){
        $pdo->prepare("UPDATE tags SET name=? WHERE id=?")->execute([$name, $tag_id]);

        // Redirect back to tags list
        header("Location: tags.php");
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>

<h2>Edit Tag</h2>

<!-- Display validation errors -->
<?php if(!empty($errors)): ?>
<div class="alert alert-danger">
    <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
</div>
<?php endif; ?>

<!-- Edit Tag Form -->
<form method="POST">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($tag['name']) ?>" required>
    </div>
    <button class="btn btn-primary">Update</button>
</form>

<?php include '../includes/footer.php'; ?>
