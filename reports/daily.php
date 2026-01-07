<?php
require "../config/database.php";

$sales = $pdo->query("
  SELECT DATE(created_at) day, SUM(total) total
  FROM sales
  GROUP BY DATE(created_at)
")->fetchAll();
?>
<!doctype html>
<html>
<head>
<title>Daily Sales Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
<h3>Daily Sales Report</h3>

<table class="table table-bordered">
<tr><th>Date</th><th>Total Sales</th></tr>
<?php foreach($sales as $s): ?>
<tr>
  <td><?= $s['day'] ?></td>
  <td>₱<?= number_format($s['total'],2) ?></td>
</tr>
<?php endforeach ?>
</table>

<button onclick="window.print()" class="btn btn-primary">
Print / Save as PDF
</button>

</div>
</body>
</html>
