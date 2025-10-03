<?php
// ----------------------------
// Include required files
// ----------------------------
require_once '../includes/config.php';      // Database connection and session start
require_once '../includes/functions.php';   // Helper functions like cleanInput()
require_once '../middleware/admin.php';     // Admin check middleware to restrict access to admins only

// ----------------------------
// Initialize errors array
// ----------------------------
$errors = [];

// ----------------------------
// Handle form submission
// ----------------------------
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the tag name from POST data and sanitize it
    $name = cleanInput($_POST['name']);

    // Validation: Check if name is empty
    if(empty($name)) $errors[] = "Tag name required.";

    // Check if the tag already exists in the database
    $stmt = $pdo->prepare("SELECT * FROM tags WHERE name=?");
    $stmt->execute([$name]);
    if($stmt->rowCount() > 0) $errors[] = "Tag already exists.";

    // If no errors, insert the new tag
    if(empty($errors)) {
        $pdo->prepare("INSERT INTO tags (name) VALUES (?)")->execute([$name]);
        // Redirect to tags list after successful insertion
        header("Location: tags.php");
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Add Tag</h2>

    <!-- Display validation errors if any -->
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
        </div>
    <?php endif; ?>

    <!-- Tag form -->
    <form method="POST">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <button class="btn btn-success">Add Tag</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
