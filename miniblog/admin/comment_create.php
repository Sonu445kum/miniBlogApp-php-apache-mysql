<?php
// ----------------------------
// Include required files
// ----------------------------
require '../includes/header.php';          // Header template
require '../includes/config.php';          // Database connection
require '../includes/middleware/admin.php'; // Admin access middleware

// ----------------------------
// Handle form submission
// ----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $post_id = $_POST['post_id'];              // ID of the post to comment on
    $author  = trim($_POST['author']);         // Author name
    $content = trim($_POST['content']);        // Comment content

    // Prepare SQL statement using prepared statements for security
    $stmt = $conn->prepare("INSERT INTO comments (post_id, author, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $post_id, $author, $content);

    // Execute and handle result
    if ($stmt->execute()) {
        $success = "Comment added successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
}

// ----------------------------
// Fetch posts for dropdown selection
// ----------------------------
$posts = $conn->query("SELECT id, title FROM posts ORDER BY created_at DESC");
?>

<!-- ----------------------------
     HTML Section
---------------------------- -->
<h2>Add Comment</h2>

<!-- Display success message -->
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

<!-- Display error message -->
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<!-- Comment Form -->
<form method="POST">
    <!-- Post selection dropdown -->
    <label>Select Post:</label><br>
    <select name="post_id">
        <?php while($row = $posts->fetch_assoc()): ?>
            <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['title']); ?></option>
        <?php endwhile; ?>
    </select><br>

    <!-- Author input -->
    <label>Author Name:</label><br>
    <input type="text" name="author" required><br>

    <!-- Comment textarea -->
    <label>Comment:</label><br>
    <textarea name="content" required></textarea><br><br>

    <!-- Submit button -->
    <button type="submit">Add Comment</button>
</form>
