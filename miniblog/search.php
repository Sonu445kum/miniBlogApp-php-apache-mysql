<?php
$search = $_GET['q'] ?? '';
header("Location: index.php?search=".urlencode($search));
exit;
