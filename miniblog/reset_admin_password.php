<?php
// reset_admin_password.php
// ------------------------
// This script resets the password for an admin user in the database.
// WARNING: Only use this on development/testing or secure environment!
// ------------------------

// Include configuration for DB connection
require_once __DIR__ . '/includes/config.php';

// ------------------------
// Set the admin email and new password here
// ------------------------
$admin_email = 'admin@example.com';  // Admin account email
$new_password = 'admin123';          // New password to set (change as needed)

// Hash the new password using PHP's secure password_hash function
$hash = password_hash($new_password, PASSWORD_DEFAULT);

// Prepare SQL statement to update password for the admin email
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");

// Execute the update and check result
if($stmt->execute([$hash, $admin_email])){
    // Success message
    echo "Password updated for $admin_email. Now login with password: $new_password";
} else {
    // Failure message
    echo "Update failed.";
}
?>
