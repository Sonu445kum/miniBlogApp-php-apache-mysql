<?php
require_once '../includes/config.php';   // Include database connection and config
require_once '../includes/functions.php'; // Include helper functions like cleanInput

// Start session if not already started
if(session_status()===PHP_SESSION_NONE) session_start();

// Array to store validation errors
$errors = [];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ----------------------------
    // Collect and sanitize form inputs
    // ----------------------------
    $username = cleanInput($_POST['username'] ?? '');
    $email    = cleanInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // ----------------------------
    // Validations
    // ----------------------------
    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // Check if email already exists in database
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Email already registered.";
    }

    // ----------------------------
    // If no errors, insert user into database
    // ----------------------------
    if (empty($errors)) {
        // Hash the password securely
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert user with default role 'user'
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        if ($stmt->execute([$username, $email, $hash])) {
            // Registration successful, redirect to login
            $_SESSION['success'] = "Registration successful. Please login.";
            header("Location: login.php");
            exit;
        } else {
            // Database error
            $errors[] = "Something went wrong. Try again.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<h2>Register</h2>

<!-- Display validation errors -->
<?php if(!empty($errors)): ?>
<div class="alert alert-danger">
    <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
</div>
<?php endif; ?>

<!-- Registration Form -->
<form method="POST">
    <div class="mb-3">
        <label>Username</label>
        <!-- Preserve input after failed submission -->
        <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>

<!-- Link to login page -->
<p class="mt-2">Already have an account? <a href="login.php">Login here</a></p>

<?php include '../includes/footer.php'; ?>
