<?php
// Include database configuration and helper functions
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Include admin middleware to ensure only admins can access this page
require_once '../middleware/admin.php';

// Get the category ID from the URL parameter "id"
$cat_id = $_GET['id'] ?? null;

// If a category ID is provided, proceed to delete
if($cat_id){
    // Prepare and execute the SQL statement to delete the category from database
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$cat_id]);
}

// Redirect back to the categories list page after deletion
header("Location: categories.php");
exit; // Terminate the script to ensure no further code runs
