<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../middleware/admin.php';

$errors = [];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = cleanInput($_POST['name']);
    if(empty($name)) $errors[]="Tag name required.";

    $stmt = $pdo->prepare("SELECT * FROM tags WHERE name=?");
    $stmt->execute([$name]);
    if($stmt->rowCount()>0) $errors[]="Tag already exists.";

    if(empty($errors)){
        $pdo->prepare("INSERT INTO tags (name) VALUES (?)")->execute([$name]);
        header("Location: tags.php");
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<h2>Add Tag</h2>
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
    <button class="btn btn-success">Add Tag</button>
</form>
<?php include '../includes/footer.php'; ?>
