<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../middleware/admin.php';

$tag_id = $_GET['id'] ?? null;
if(!$tag_id) header("Location: tags.php");

$stmt = $pdo->prepare("SELECT * FROM tags WHERE id=?");
$stmt->execute([$tag_id]);
$tag = $stmt->fetch();
if(!$tag) header("Location: tags.php");

$errors = [];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = cleanInput($_POST['name']);
    if(empty($name)) $errors[]="Tag name required.";

    if(empty($errors)){
        $pdo->prepare("UPDATE tags SET name=? WHERE id=?")->execute([$name,$tag_id]);
        header("Location: tags.php");
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<h2>Edit Tag</h2>
<?php if(!empty($errors)): ?>
<div class="alert alert-danger">
    <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
</div>
<?php endif; ?>
<form method="POST">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($tag['name']) ?>" required>
    </div>
    <button class="btn btn-primary">Update</button>
</form>
<?php include '../includes/footer.php'; ?>
