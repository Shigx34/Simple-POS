<?php
require "_header.php";
require "../config/database.php";

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<h4 class="mb-3">Product Inventory</h4>

<a href="product_add.php" class="btn btn-success mb-2">➕ Add Product</a>

<table class="table table-bordered bg-white align-middle">
<thead class="table-light">
<tr>
  <th>Image</th>
  <th>Name</th>
  <th>Price</th>
  <th>Stock</th>
  <th>Barcode</th>
  <th width="160">Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($products as $p): ?>
<tr>
  <td>
    <img src="../uploads/products/<?= $p['image'] ?>" width="50">
  </td>
  <td><?= htmlspecialchars($p['name']) ?></td>
  <td>₱<?= number_format($p['price'],2) ?></td>
  <td><?= $p['stock'] ?></td>
  <td><?= htmlspecialchars($p['barcode']) ?></td>
  <td>
    <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
    <a href="product_delete.php?id=<?= $p['id'] ?>"
       class="btn btn-danger btn-sm"
       onclick="return confirm('Delete this product?')">
       Delete
    </a>
  </td>
</tr>
<?php endforeach ?>
</tbody>
</table>

<?php require "_footer.php"; ?>
