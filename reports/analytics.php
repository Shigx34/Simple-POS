<?php require "_header.php";

/* TOTAL SALES */
$totalSales = $pdo->query("SELECT SUM(total) FROM sales")->fetchColumn();

/* TODAY SALES */
$todaySales = $pdo->query("
  SELECT SUM(total) FROM sales
  WHERE DATE(created_at)=CURDATE()
")->fetchColumn();

/* TOTAL TRANSACTIONS */
$totalTransactions = $pdo->query("SELECT COUNT(*) FROM sales")->fetchColumn();
?>

<h4>Sales Analytics</h4>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card text-center shadow-sm">
      <div class="card-body">
        <h6>Total Sales</h6>
        <h4>₱<?= number_format($totalSales ?? 0,2) ?></h4>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card text-center shadow-sm">
      <div class="card-body">
        <h6>Today Sales</h6>
        <h4>₱<?= number_format($todaySales ?? 0,2) ?></h4>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card text-center shadow-sm">
      <div class="card-body">
        <h6>Total Transactions</h6>
        <h4><?= $totalTransactions ?></h4>
      </div>
    </div>
  </div>
</div>

<div class="d-flex gap-2">
  <a href="sales.php" class="btn btn-primary">📅 Sales by Date</a>
  <a href="best_sellers.php" class="btn btn-success">🏆 Best Sellers</a>
</div>

<?php require "_footer.php"; ?>
