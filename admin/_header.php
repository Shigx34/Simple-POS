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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar bg-primary text-white px-3">
  <span>Admin Panel</span>
  <div>
    <a href="../index.php" class="btn btn-light btn-sm">POS</a>
    <a href="products.php" class="btn btn-warning btn-sm">Inventory</a>
    <a href="../reports/analytics.php" class="btn btn-success btn-sm">Reports</a>
    <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
  </div>
</nav>
<div class="container mt-4">
