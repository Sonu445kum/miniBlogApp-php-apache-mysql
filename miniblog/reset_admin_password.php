<?php
// reset_admin_password.php
require_once __DIR__ . '/includes/config.php';

// set email and new password here
$admin_email = 'admin@example.com';
$new_password = 'admin123'; // change if you want another password

$hash = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
if($stmt->execute([$hash, $admin_email])){
    echo "Password updated for $admin_email. Now login with password: $new_password";
} else {
    echo "Update failed.";
}
