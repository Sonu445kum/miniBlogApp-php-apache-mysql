<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Start session if not already
if (session_status() === PHP_SESSION_NONE) session_start();

// Admin check
if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['error'] = "Unauthorized access.";
    header('Location: ../auth/login.php');
    exit;
}

// Ensure user_id exists
if (empty($_SESSION['user_id'])) {
    $_SESSION['error'] = "User session not found. Please login again.";
    header('Location: ../auth/login.php');
    exit;
}

// Enable PDO exceptions
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$errors = [];
$title = $content = $category_id = '';
$tags_selected = [];

// Fetch categories & tags
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$tags = $pdo->query("SELECT * FROM tags ORDER BY name ASC")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $content     = trim($_POST['content'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $tags_selected = $_POST['tags'] ?? [];

    // Validate
    if (!$title || !$content) {
        $errors[] = "Title and content are required.";
    }

    // Handle thumbnail upload
    $thumbnail = null;
    if (!empty($_FILES['thumbnail']['name'])) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = time() . "_" . basename($_FILES["thumbnail"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file)) {
            // Only save filename in DB (remove folder prefix)
            $thumbnail = $file_name;
        } else {
            $errors[] = "Failed to upload thumbnail. Error code: " . $_FILES['thumbnail']['error'];
        }
    }

    if (empty($errors)) {
        try {
            // Generate slug
            $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $title));

            // Insert post
            $stmt = $pdo->prepare("
                INSERT INTO posts (user_id, category_id, title, slug, content, thumbnail) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $category_id ?: null,
                $title,
                $slug,
                $content,
                $thumbnail
            ]);

            $post_id = $pdo->lastInsertId();

            // Attach tags
            if (!empty($tags_selected)) {
                $stmt_tag = $pdo->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
                foreach ($tags_selected as $tag_id) {
                    $stmt_tag->execute([$post_id, $tag_id]);
                }
            }

            $_SESSION['success'] = "Post added successfully.";
            header('Location: posts.php');
            exit;

        } catch (PDOException $e) {
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Add New Post</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
        </div>

        <div class="mb-3">
            <label>Content</label>
            <textarea name="content" class="form-control" id="editor"><?= htmlspecialchars($content) ?></textarea>
        </div>

        <div class="mb-3">
            <label>Category</label>
            <select name="category_id" class="form-control">
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $category_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Tags</label>
            <select name="tags[]" class="form-control" multiple>
                <?php foreach ($tags as $tag): ?>
                    <option value="<?= $tag['id'] ?>" <?= in_array($tag['id'], $tags_selected) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tag['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Thumbnail</label>
            <input type="file" name="thumbnail" class="form-control">
        </div>

        <button class="btn btn-primary" type="submit">Add Post</button>
        <a href="posts.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<!-- CKEditor with submit validation -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor
.create( document.querySelector( '#editor' ) )
.then( editor => {
    const form = editor.sourceElement.closest('form');
    form.addEventListener('submit', e => {
        const data = editor.getData();
        if(!data.trim()){
            e.preventDefault();
            alert('Content is required');
            editor.editing.view.focus();
        }
    });
})
.catch( error => { console.error(error); });
</script>

<?php include '../includes/footer.php'; ?>
