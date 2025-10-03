<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isLoggedIn() || !isAdmin()){
    header('Location: ../auth/login.php');
    exit;
}

// Add new tag
if(isset($_POST['add'])){
    $name = trim($_POST['name']);
    if($name){
        $stmt = $pdo->prepare("INSERT INTO tags (name) VALUES (?)");
        $stmt->execute([$name]);
        $_SESSION['success'] = "Tag added.";
        header('Location: tags.php');
        exit;
    }
}

// Delete tag
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tags WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = "Tag deleted.";
    header('Location: tags.php');
    exit;
}

// Fetch tags
$tags = $pdo->query("SELECT * FROM tags ORDER BY name ASC")->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-4">
    <h2>Manage Tags</h2>
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-3">
        <div class="input-group">
            <input type="text" name="name" class="form-control" placeholder="New tag name" required>
            <button class="btn btn-primary" type="submit" name="add">Add</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($tags as $tag): ?>
            <tr>
                <td><?= $tag['id'] ?></td>
                <td><?= htmlspecialchars($tag['name']) ?></td>
                <td>
                    <a href="tags.php?delete=<?= $tag['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this tag?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
