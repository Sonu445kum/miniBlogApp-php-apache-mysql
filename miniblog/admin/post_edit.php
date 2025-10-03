<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Admin check
if(!isLoggedIn() || !isAdmin()){
    header('Location: ../auth/login.php');
    exit;
}

$post_id = (int)($_GET['id'] ?? 0);
if(!$post_id){
    header('Location: posts.php');
    exit;
}

// Fetch post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();
if(!$post){
    $_SESSION['error'] = "Post not found.";
    header('Location: posts.php');
    exit;
}

$errors = [];
$title = $post['title'];
$content = $post['content'];
$category_id = $post['category_id'];
$tags_selected = $pdo->query("SELECT tag_id FROM post_tags WHERE post_id = $post_id")->fetchAll(PDO::FETCH_COLUMN);

// Fetch categories & tags
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$tags = $pdo->query("SELECT * FROM tags ORDER BY name ASC")->fetchAll();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title       = trim($_POST['title']);
    $content     = trim($_POST['content']);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $tags_selected = $_POST['tags'] ?? [];

    if(!$title || !$content){
        $errors[] = "Title and content are required.";
    }

    // Handle thumbnail upload
    $thumbnail = $post['thumbnail'];
    if(!empty($_FILES['thumbnail']['name'])){
        $target_dir = "../uploads/";
        if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = time() . "_" . basename($_FILES["thumbnail"]["name"]);
        $target_file = $target_dir . $file_name;

        if(move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file)){
            $thumbnail = 'uploads/' . $file_name;
        } else {
            $errors[] = "Failed to upload thumbnail.";
        }
    }

    if(empty($errors)){
        // Safe slug generation
        $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $title));

        // Update post
        $stmt = $pdo->prepare("UPDATE posts SET title=?, slug=?, content=?, category_id=?, thumbnail=? WHERE id=?");
        $stmt->execute([$title, $slug, $content, $category_id ?: null, $thumbnail, $post_id]);

        // Update tags
        $pdo->prepare("DELETE FROM post_tags WHERE post_id=?")->execute([$post_id]);
        foreach($tags_selected as $tag_id){
            $stmt = $pdo->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
            $stmt->execute([$post_id, $tag_id]);
        }

        $_SESSION['success'] = "Post updated successfully.";
        header('Location: posts.php');
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-4">
    <h2>Edit Post</h2>

    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
        </div>

        <div class="mb-3">
            <label>Content</label>
            <textarea name="content" class="form-control" id="editor" required><?= htmlspecialchars($content) ?></textarea>
        </div>

        <div class="mb-3">
            <label>Category</label>
            <select name="category_id" class="form-control">
                <option value="">Select Category</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id']==$category_id?'selected':'' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Tags</label>
            <select name="tags[]" class="form-control" multiple>
                <?php foreach($tags as $tag): ?>
                    <option value="<?= $tag['id'] ?>" <?= in_array($tag['id'],$tags_selected)?'selected':'' ?>><?= htmlspecialchars($tag['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Thumbnail</label>
            <input type="file" name="thumbnail" class="form-control">
            <?php if($post['thumbnail']): ?>
                <img src="../<?= $post['thumbnail'] ?>" alt="Thumbnail" class="img-thumbnail mt-2" width="150">
            <?php endif; ?>
        </div>

        <button class="btn btn-primary" type="submit">Update Post</button>
        <a href="posts.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('#editor')).catch(error => { console.error(error); });
</script>
<?php include '../includes/footer.php'; ?>
