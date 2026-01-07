<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>POS Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://unpkg.com/html5-qrcode"></script>

<style>
body { background:#f8f9fa; }
.total-box { font-size: 1.5rem; font-weight: bold; }
</style>
</head>
<body>

<!-- TOP BAR -->
<nav class="navbar navbar-dark bg-primary px-3">
  <span class="navbar-brand">Simple POS</span>
  <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
</nav>

<div class="container-fluid mt-3">
  <div class="row g-3">

    <!-- LEFT PANEL -->
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body d-grid gap-2">
          <button class="btn btn-primary btn-lg" onclick="startScanner()">
            📷 Scan Barcode
          </button>

          <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#addItemModal">
            ➕ Add Item
          </button>
        </div>
      </div>

      <!-- CAMERA -->
      <div class="card mt-3 d-none" id="cameraCard">
        <div class="card-body">
          <div id="reader"></div>
        </div>
      </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header fw-semibold">
          Customer Items
        </div>
        <div class="card-body p-0">
          <table class="table table-bordered m-0">
            <thead class="table-light">
              <tr>
                <th>Item</th>
                <th width="80">Qty</th>
                <th width="120">Price</th>
                <th width="120">Subtotal</th>
              </tr>
            </thead>
            <tbody id="cartBody"></tbody>
          </table>
        </div>
        <div class="card-footer text-end total-box">
          Total: ₱<span id="total">0.00</span>
        </div>
      </div>
    </div>

  </div>
</div>
