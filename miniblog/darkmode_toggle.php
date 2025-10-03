<?php
session_start();

// Toggle dark mode session variable
if (!isset($_SESSION['dark_mode']) || $_SESSION['dark_mode'] === false) {
    $_SESSION['dark_mode'] = true;
} else {
    $_SESSION['dark_mode'] = false;
}

// Agar AJAX request hai to simple response bhej do
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode([
        "success" => true,
        "dark_mode" => $_SESSION['dark_mode']
    ]);
    exit;
}

// Agar direct access ho to home page pe redirect kar do
header("Location: index.php");
exit;
