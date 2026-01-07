<?php require "_header.php";

$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';

$where = "";
$params = [];

if ($from && $to) {
    $where = "WHERE DATE(created_at) BETWEEN ? AND ?";
    $params = [$from, $to];
}

$stmt = $pdo->prepare("
  SELECT DATE(created_at) day, SUM(total) total
  FROM sales
  $where
  GROUP BY DATE(created_at)
  ORDER BY day DESC
");
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>

<h4>Sales Report by Date</h4>

<form class="row g-2 mb-3">
  <div class="col-md-3">
    <input type="date" name="from" class="form-control" value="<?= $from ?>">
  </div>
  <div class="col-md-3">
    <input type="date" name="to" class="form-control" value="<?= $to ?>">
  </div>
  <div class="col-md-3">
    <button class="btn btn-primary w-100">Filter</button>
  </div>
  <div class="col-md-3">
    <button type="button" onclick="window.print()" class="btn btn-success w-100">
      Print / Save PDF
    </button>
  </div>
</form>

<table class="table table-bordered bg-white">
<tr class="table-light">
  <th>Date</th>
  <th>Total Sales</th>
</tr>

<?php foreach ($rows as $r): ?>
<tr>
  <td><?= $r['day'] ?></td>
  <td>₱<?= number_format($r['total'],2) ?></td>
</tr>
<?php endforeach ?>

</table>

<?php require "_footer.php"; ?>
