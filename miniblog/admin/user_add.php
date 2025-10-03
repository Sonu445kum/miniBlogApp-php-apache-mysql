<?php
// Include configuration, helper functions, and admin middleware
require_once '../includes/config.php';     // Config file also safely starts session
require_once '../includes/functions.php';
require_once '../includes/middleare/admin.php';    // Admin check middleware to restrict access

$errors = [];    // Array to hold validation errors
$success = '';   // Success message

// ----------------------------
// Handle form submission
// ----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize POST input
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'user';

    // ----------------------------
    // Validation
    // ----------------------------
    if (!$username || !$email || !$password) {
        $errors[] = "All fields are required."; // Check for empty fields
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address."; // Validate email format
    } else {
        // Check if email already exists in the database
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Email is already registered.";
        }
    }

    // ----------------------------
    // If validation passes, insert new user
    // ----------------------------
    if (empty($errors)) {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");

        try {
            // Execute the query
            $stmt->execute([$username, $email, $hashed_password, $role]);
            $success = "User created successfully!";
        } catch (PDOException $e) {
            // Catch any database errors
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Create New User</h2>

    <!-- ----------------------------
         Display Success Message
         ---------------------------- -->
    <?php if(!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <!-- ----------------------------
         Display Validation Errors
         ---------------------------- -->
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
        </div>
    <?php endif; ?>

    <!-- ----------------------------
         User Creation Form
         ---------------------------- -->
    <form method="POST">
        <!-- Username -->
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <!-- Role Selection -->
        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control">
                <option value="user" <?= (($_POST['role'] ?? '') === 'user')?'selected':'' ?>>User</option>
                <option value="admin" <?= (($_POST['role'] ?? '') === 'admin')?'selected':'' ?>>Admin</option>
            </select>
        </div>

        <!-- Submit and Back Buttons -->
        <button type="submit" class="btn btn-primary">Create User</button>
        <a href="users.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
