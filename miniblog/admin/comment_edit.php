<?php
// ----------------------------
// Include required files
// ----------------------------
require '../includes/header.php';       // Header template
require '../includes/config.php';       // Database connection
require '../includes/middleware/admin.php'; // Ensure only admin can access

// ----------------------------
// Get comment ID from query string
// ----------------------------
$id = $_GET['id'] ?? null;  // Fetch 'id' parameter from URL, default to null

// If no ID is provided, terminate the script
if (!$id) {
    die("No comment ID provided.");
}

// ----------------------------
// Fetch existing comment from database
// ----------------------------
$stmt = $conn->prepare("SELECT * FROM comments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$comment = $result->fetch_assoc();

// If comment not found, terminate
if (!$comment) {
    die("Comment not found.");
}

// ----------------------------
// Handle form submission to update comment
// ----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author  = trim($_POST['author']);    // Sanitize author name
    $content = trim($_POST['content']);   // Sanitize comment content

    // Prepare and execute UPDATE query
    $stmt = $conn->prepare("UPDATE comments SET author = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $author, $content, $id);

    if ($stmt->execute()) {
        $success = "Comment updated successfully!";
        // Refresh comment data for display
        $comment['author'] = $author;
        $comment['content'] = $content;
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>

<!-- ----------------------------
     HTML Form for Editing Comment
---------------------------- -->
<h2>Edit Comment</h2>

<!-- Display success or error messages -->
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
    <label>Author Name:</label><br>
    <input type="text" name="author" value="<?= htmlspecialchars($comment['author']); ?>" required><br>

    <label>Comment:</label><br>
    <textarea name="content" required><?= htmlspecialchars($comment['content']); ?></textarea><br><br>

    <button type="submit">Update Comment</button>
</form>
