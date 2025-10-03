<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../middleware/admin.php';

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = cleanInput($_POST['name']);
    if(empty($name)) $errors[] = "Category name required.";

    // Check if exists
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE name=?");
    $stmt->execute([$name]);
    if($stmt->rowCount()>0) $errors[] = "Category already exists.";

    if(empty($errors)){
        $pdo->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$name]);
        header("Location: categories.php");
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<h2>Add Category</h2>
<?php if(!empty($errors)): ?>
<div class="alert alert-danger">
    <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
</div>
<?php endif; ?>
<form method="POST">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <button class="btn btn-success">Add Category</button>
</form>
<?php include '../includes/footer.php'; ?>
