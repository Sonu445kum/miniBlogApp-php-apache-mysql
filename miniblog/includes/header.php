<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$dark_mode = $_SESSION['dark_mode'] ?? false;

// Ensure $base_url always points to project root
if (!isset($base_url)) {
    $base_url = "http://localhost/miniBlogApp/miniblog";
}
?>
<!DOCTYPE html>
<html lang="en" <?= $dark_mode ? 'class="dark"' : '' ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MiniBlog</title>

  <!--  Correct local CSS -->
  <link rel="stylesheet" href="<?= $base_url ?>/assets/css/custom.css">

  <!--  Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!--  jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!--  CKEditor (only once) -->
  <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

  <!--  Local JS -->
  <script src="<?= $base_url ?>/assets/js/main.js"></script>

  <style>
      body.dark { background-color: #121212; color: #eee; }
      .dark .card { background-color: #1e1e1e; color: #eee; }
      .dark .navbar { background-color: #1a1a1a; }
      .dark .footer { background-color: #1a1a1a; color: #ccc; }
  </style>
</head>
<body class="<?= $dark_mode ? 'dark' : '' ?>">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="<?= $base_url ?>/index.php">MiniBlog</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/index.php">Home</a></li>

        <?php if (isLoggedIn()): ?>
          <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/profile.php">Profile</a></li>

          <?php if (isAdmin()): ?>
            <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/admin/index.php">Admin</a></li>
          <?php endif; ?>

          <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/auth/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/auth/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/auth/register.php">Register</a></li>
        <?php endif; ?>
      </ul>

      <!-- Search + Dark Mode Toggle -->
      <form class="d-flex ms-3" method="GET" action="<?= $base_url ?>/search.php">
          <input class="form-control me-2" type="search" placeholder="Search" name="q">
          <button class="btn btn-outline-success" type="submit">Search</button>
      </form>

      <button id="darkToggle" class="btn btn-secondary ms-2"><?= $dark_mode ? 'Light Mode' : 'Dark Mode' ?></button>
    </div>
  </div>
</nav>

<script>
$('#darkToggle').click(function(){
    $.post('<?= $base_url ?>/darkmode_toggle.php', { toggle: true }, function(){
        location.reload();
    });
});
</script>

<div class="container mt-4">
