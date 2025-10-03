<?php
require '../includes/header.php';
require '../includes/config.php';
require '../includes/middleware/admin.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("No comment ID provided.");
}

// Fetch existing comment
$stmt = $conn->prepare("SELECT * FROM comments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$comment = $result->fetch_assoc();

if (!$comment) {
    die("Comment not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author  = trim($_POST['author']);
    $content = trim($_POST['content']);

    $stmt = $conn->prepare("UPDATE comments SET author = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $author, $content, $id);

    if ($stmt->execute()) {
        $success = "Comment updated successfully!";
        // Refresh comment data
        $comment['author'] = $author;
        $comment['content'] = $content;
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>

<h2>Edit Comment</h2>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
    <label>Author Name:</label><br>
    <input type="text" name="author" value="<?= htmlspecialchars($comment['author']); ?>" required><br>

    <label>Comment:</label><br>
    <textarea name="content" required><?= htmlspecialchars($comment['content']); ?></textarea><br><br>

    <button type="submit">Update Comment</button>
</form>
