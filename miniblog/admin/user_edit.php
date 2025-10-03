<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/middleare/admin.php';

$user_id = $_GET['id'] ?? null;
if(!$user_id) header("Location: users.php");

$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if(!$user) header("Location: users.php");

$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $role = $_POST['role'] ?? 'user';
    if(!in_array($role, ['user','admin'])) $errors[] = "Invalid role.";
    if(empty($errors)){
        $pdo->prepare("UPDATE users SET role=? WHERE id=?")->execute([$role,$user_id]);
        header("Location: users.php");
        exit;
    }
}
?>
<?php include '../includes/header.php'; ?>
<h2>Edit User</h2>
<?php if(!empty($errors)): ?>
<div class="alert alert-danger">
    <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
</div>
<?php endif; ?>
<form method="POST">
    <div class="mb-3">
        <label>Username</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
    </div>
    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-select">
            <option value="user" <?= $user['role']=='user'?'selected':'' ?>>User</option>
            <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
        </select>
    </div>
    <button class="btn btn-primary">Update</button>
</form>
<?php include '../includes/footer.php'; ?>
