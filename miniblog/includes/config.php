<?php
session_start();

// DB settings
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = ''; // XAMPP default
$db_name = 'miniblog';
$charset = 'utf8mb4';

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Base URL
$base_url = 'http://localhost/miniBlogApp/miniblog';

// Upload folder
$upload_dir = __DIR__ . '/../assets/uploads/';
$upload_url = $base_url . '/assets/uploads/';
?>
