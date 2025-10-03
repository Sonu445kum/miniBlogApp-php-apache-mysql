<?php
require '../includes/header.php';
require '../includes/config.php';
require '../includes/middleware/admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $author  = trim($_POST['author']);
    $content = trim($_POST['content']);

    $stmt = $conn->prepare("INSERT INTO comments (post_id, author, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $post_id, $author, $content);

    if ($stmt->execute()) {
        $success = "Comment added successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
}

// Fetch posts for dropdown
$posts = $conn->query("SELECT id, title FROM posts ORDER BY created_at DESC");
?>

<h2>Add Comment</h2>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
    <label>Select Post:</label><br>
    <select name="post_id">
        <?php while($row = $posts->fetch_assoc()): ?>
            <option value="<?= $row['id']; ?>"><?= $row['title']; ?></option>
        <?php endwhile; ?>
    </select><br>

    <label>Author Name:</label><br>
    <input type="text" name="author" required><br>

    <label>Comment:</label><br>
    <textarea name="content" required></textarea><br><br>

    <button type="submit">Add Comment</button>
</form>
