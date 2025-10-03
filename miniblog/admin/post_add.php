<?php
// Include configuration and helper functions
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) session_start();

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['error'] = "Unauthorized access.";
    header('Location: ../auth/login.php');
    exit;
}

// Ensure user session exists
if (empty($_SESSION['user_id'])) {
    $_SESSION['error'] = "User session not found. Please login again.";
    header('Location: ../auth/login.php');
    exit;
}

// Enable PDO exceptions for better error handling
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Initialize variables for form values and errors
$errors = [];
$title = $content = $category_id = '';
$tags_selected = [];

// Fetch all categories & tags for dropdowns
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$tags = $pdo->query("SELECT * FROM tags ORDER BY name ASC")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form input
    $title       = trim($_POST['title'] ?? '');
    $content     = trim($_POST['content'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $tags_selected = $_POST['tags'] ?? [];

    // Validate required fields
    if (!$title || !$content) {
        $errors[] = "Title and content are required.";
    }

    // Handle thumbnail upload if a file is selected
    $thumbnail = null;
    if (!empty($_FILES['thumbnail']['name'])) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = time() . "_" . basename($_FILES["thumbnail"]["name"]);
        $target_file = $target_dir . $file_name;

        // Move uploaded file to uploads directory
        if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file)) {
            $thumbnail = $file_name; // Only store filename in DB
        } else {
            $errors[] = "Failed to upload thumbnail. Error code: " . $_FILES['thumbnail']['error'];
        }
    }

    // If no errors, insert post into database
    if (empty($errors)) {
        try {
            // Generate slug for SEO-friendly URL
            $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $title));

            // Insert post into posts table
            $stmt = $pdo->prepare("
                INSERT INTO posts (user_id, category_id, title, slug, content, thumbnail) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'],          // Author
                $category_id ?: null,          // Category (nullable)
                $title,                        // Title
                $slug,                         // Slug
                $content,                      // Post content
                $thumbnail                     // Thumbnail filename
            ]);

            $post_id = $pdo->lastInsertId(); // Get ID of newly created post

            // Attach tags to post if any selected
            if (!empty($tags_selected)) {
                $stmt_tag = $pdo->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
                foreach ($tags_selected as $tag_id) {
                    $stmt_tag->execute([$post_id, $tag_id]);
                }
            }

            // Set success message and redirect to posts list
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

    <!-- Display validation or database errors -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
        </div>
    <?php endif; ?>

    <!-- Post form -->
    <form method="POST" enctype="multipart/form-data">
        <!-- Post Title -->
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
        </div>

        <!-- Post Content with CKEditor -->
        <div class="mb-3">
            <label>Content</label>
            <textarea name="content" class="form-control" id="editor"><?= htmlspecialchars($content) ?></textarea>
        </div>

        <!-- Category Selection -->
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

        <!-- Tags Selection (Multiple) -->
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

        <!-- Thumbnail Upload -->
        <div class="mb-3">
            <label>Thumbnail</label>
            <input type="file" name="thumbnail" class="form-control">
        </div>

        <!-- Form Buttons -->
        <button class="btn btn-primary" type="submit">Add Post</button>
        <a href="posts.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<!-- CKEditor initialization and submit validation -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor
.create( document.querySelector( '#editor' ) )
.then( editor => {
    // Add form validation for CKEditor content
    const form = editor.sourceElement.closest('form');
    form.addEventListener('submit', e => {
        const data = editor.getData();
        if(!data.trim()){
            e.preventDefault(); // Prevent form submission
            alert('Content is required'); // Alert user
            editor.editing.view.focus(); // Focus editor
        }
    });
})
.catch( error => { console.error(error); });
</script>

<?php include '../includes/footer.php'; ?>
