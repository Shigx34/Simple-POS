<?php require "_header.php";

$date = $_GET['date'] ?? date('Y-m-d');
?>

<h4>Daily Sales Report (Items Sold)</h4>

<form class="row g-2 mb-3">
  <div class="col-md-4">
    <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" class="form-control">
  </div>
  <div class="col-md-4">
    <button class="btn btn-primary w-100">View</button>
  </div>
  <div class="col-md-4">
    <button type="button" onclick="window.print()" class="btn btn-success w-100">
      Print / Save PDF
    </button>
  </div>
</form>

<?php
$stmt = $pdo->prepare("
  SELECT 
    DATE(s.created_at) AS sale_date,
    si.product_name,
    SUM(si.qty) AS total_qty,
    si.price,
    SUM(si.qty * si.price) AS subtotal
  FROM sales s
  JOIN sale_items si ON si.sale_id = s.id
  WHERE DATE(s.created_at) = ?
  GROUP BY si.product_name, si.price
  ORDER BY total_qty DESC
");
$stmt->execute([$date]);
$items = $stmt->fetchAll();
?>

<table class="table table-bordered bg-white">
  <thead class="table-light">
    <tr>
      <th>Item</th>
      <th width="120">Qty Sold</th>
      <th width="120">Price</th>
      <th width="150">Subtotal</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!$items): ?>
      <tr>
        <td colspan="4" class="text-center text-muted">
          No sales for this date
        </td>
      </tr>
    <?php endif; ?>

    <?php
    $grandTotal = 0;
    foreach ($items as $i):
      $grandTotal += $i['subtotal'];
    ?>
    <tr>
      <td><?= htmlspecialchars($i['product_name']) ?></td>
      <td><?= $i['total_qty'] ?></td>
      <td>₱<?= number_format($i['price'],2) ?></td>
      <td>₱<?= number_format($i['subtotal'],2) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>

  <?php if ($items): ?>
  <tfoot class="table-light">
    <tr>
      <th colspan="3" class="text-end">Daily Total</th>
      <th>₱<?= number_format($grandTotal,2) ?></th>
    </tr>
  </tfoot>
  <?php endif; ?>
</table>

<?php require "_footer.php"; ?>
