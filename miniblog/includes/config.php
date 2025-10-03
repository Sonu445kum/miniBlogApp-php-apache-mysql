<?php
// Start PHP session to manage user login, messages, etc.
session_start();

// ----------------------------
// Database settings
// ----------------------------
$db_host = '127.0.0.1';    // Database host (localhost)
$db_user = 'root';         // Database username (XAMPP default)
$db_pass = '';             // Database password (XAMPP default is empty)
$db_name = 'miniblog';     // Name of the database
$charset = 'utf8mb4';      // Character set for supporting emojis and multilingual text

// Data Source Name (DSN) for PDO connection
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";

// PDO options for better error handling and security
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,         // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,    // Fetch results as associative arrays
    PDO::ATTR_EMULATE_PREPARES => false,                // Use real prepared statements (prevents SQL injection)
];

// ----------------------------
// Create PDO instance (database connection)
// ----------------------------
try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // If connection fails, stop script and show error message
    die("Database connection failed: " . $e->getMessage());
}

// ----------------------------
// Base URL of the project
// ----------------------------
$base_url = 'http://localhost/miniBlogApp/miniblog'; // Change this if hosting on a live server

// ----------------------------
// Upload folder settings
// ----------------------------
$upload_dir = __DIR__ . '/../assets/uploads/'; // Physical folder path for uploaded files
$upload_url = $base_url . '/assets/uploads/';  // URL to access uploaded files
?>
