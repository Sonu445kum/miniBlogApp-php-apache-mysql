<?php
// ----------------------------
// Include required files
// ----------------------------
require_once '../includes/config.php';       // Database connection and session start
require_once '../includes/functions.php';    // Helper functions
require_once '../includes/middleare/admin.php'; // Admin check middleware to restrict access

// ----------------------------
// Get the user ID from query string
// ----------------------------
$user_id = $_GET['id'] ?? null;

// ----------------------------
// Redirect if no user ID provided
// ----------------------------
if (!$user_id) header("Location: users.php");

// ----------------------------
// Fetch user data from database
// ----------------------------
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// ----------------------------
// Redirect if user not found
// ----------------------------
if (!$user) header("Location: users.php");

// ----------------------------
// Initialize errors array
// ----------------------------
$errors = [];

// ----------------------------
// Handle form submission
// ----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the selected role from the form
    $role = $_POST['role'] ?? 'user';

    // Validate role value
    if (!in_array($role, ['user', 'admin'])) $errors[] = "Invalid role.";

    // If no errors, update user role in database
    if (empty($errors)) {
        $pdo->prepare("UPDATE users SET role=? WHERE id=?")->execute([$role, $user_id]);
        header("Location: users.php"); // Redirect back to users list
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Edit User</h2>

    <!-- Display Errors -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
        </div>
    <?php endif; ?>

    <!-- User Edit Form -->
    <form method="POST">
        <!-- Username (disabled, cannot edit) -->
        <div class="mb-3">
            <label>Username</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
        </div>

        <!-- Role Selection -->
        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-select">
                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <!-- Submit Button -->
        <button class="btn btn-primary">Update</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
