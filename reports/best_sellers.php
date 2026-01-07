<?php require "_header.php";

$items = $pdo->query("
  SELECT product_name,
         SUM(qty) total_qty,
         SUM(qty * price) total_sales
  FROM sale_items
  GROUP BY product_name
  ORDER BY total_qty DESC
")->fetchAll();
?>

<h4>Best-Selling Items</h4>

<button onclick="window.print()" class="btn btn-success mb-3">
  Print / Save PDF
</button>

<table class="table table-bordered bg-white">
<tr class="table-light">
  <th>Rank</th>
  <th>Item</th>
  <th>Qty Sold</th>
  <th>Total Sales</th>
</tr>

<?php $rank=1; foreach ($items as $i): ?>
<tr>
  <td><?= $rank++ ?></td>
  <td><?= htmlspecialchars($i['product_name']) ?></td>
  <td><?= $i['total_qty'] ?></td>
  <td>₱<?= number_format($i['total_sales'],2) ?></td>
</tr>
<?php endforeach ?>

</table>

<?php require "_footer.php"; ?>
