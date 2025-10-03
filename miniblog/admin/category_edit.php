<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../middleware/admin.php';

$cat_id = $_GET['id'] ?? null;
if(!$cat_id) header("Location: categories.php");

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id=?");
$stmt->execute([$cat_id]);
$cat = $stmt->fetch();
if(!$cat) header("Location: categories.php");

$errors = [];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = cleanInput($_POST['name']);
    if(empty($name)) $errors[]="Category name required.";
    if(empty($errors)){
        $pdo->prepare("UPDATE categories SET name=? WHERE id=?")->execute([$name,$cat_id]);
        header("Location: categories.php");
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<h2>Edit Category</h2>
<?php if(!empty($errors)): ?>
<div class="alert alert-danger">
    <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
</div>
<?php endif; ?>
<form method="POST">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($cat['name']) ?>" required>
    </div>
    <button class="btn btn-primary">Update</button>
</form>
<?php include '../includes/footer.php'; ?>
