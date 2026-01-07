<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require __DIR__ . "/../config/database.php";
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary px-3">
  <span class="navbar-brand">Reports & Analytics</span>
  <div>
    <a href="../admin/products.php" class="btn btn-warning btn-sm me-2">Inventory</a>
    <a href="../index.php" class="btn btn-light btn-sm me-2">POS</a>
    <a href="daily_sales.php" class="btn btn-light btn-sm me-2">
  Daily Item Sales
</a>
    <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
  </div>
</nav>

<div class="container mt-4">
