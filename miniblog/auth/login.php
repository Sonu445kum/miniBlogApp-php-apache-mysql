<?php
require_once '../includes/config.php';   // Include database connection and config
require_once '../includes/functions.php'; // Include helper functions like cleanInput

// Start session if not already started
if(session_status()===PHP_SESSION_NONE) session_start();

// Array to store validation errors
$errors = [];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize email input
    $email = cleanInput($_POST['email'] ?? '');
    // Password input (not sanitized because we hash/verify it)
    $password = $_POST['password'] ?? '';

    // ----------------------------
    // Validation
    // ----------------------------
    if (empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    } else {
        // Fetch user by email from database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verify user exists and password matches
        if ($user && password_verify($password, $user['password'])) {
            // ----------------------------
            // Successful login
            // ----------------------------
            // Set session variables for logged-in user
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            // Redirect to homepage
            header("Location: ../index.php");
            exit;
        } else {
            // Invalid credentials
            $errors[] = "Invalid email or password.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<h2>Login</h2>

<!-- Display validation errors if any -->
<?php if(!empty($errors)): ?>
<div class="alert alert-danger">
    <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
</div>
<?php endif; ?>

<!-- Display success message (from registration or other actions) -->
<?php if(isset($_SESSION['success'])): ?>
<div class="alert alert-success">
    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<!-- Login Form -->
<form method="POST">
    <div class="mb-3">
        <label>Email</label>
        <!-- Preserve email input after failed submission -->
        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>

<!-- Link to registration page -->
<p class="mt-2">Don't have an account? <a href="register.php">Register here</a></p>

<?php include '../includes/footer.php'; ?>
