<?php
require "_header.php";
require "../config/database.php";

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) die("Product not found");
?>

<h4>Edit Product</h4>

<form method="post" action="product_update.php" enctype="multipart/form-data">
  <input type="hidden" name="id" value="<?= $p['id'] ?>">
  <input type="hidden" name="old_image" value="<?= $p['image'] ?>">

  <input class="form-control mb-2" name="name" value="<?= htmlspecialchars($p['name']) ?>" required>
  <input class="form-control mb-2" name="price" type="number" step="0.01" value="<?= $p['price'] ?>" required>
  <input class="form-control mb-2" name="stock" type="number" value="<?= $p['stock'] ?>" required>

  <div class="input-group mb-2">
    <input class="form-control" name="barcode" id="barcode" value="<?= htmlspecialchars($p['barcode']) ?>">
    <button type="button" class="btn btn-primary" onclick="startBarcodeScan()">📷 Scan</button>
  </div>

  <img src="../uploads/products/<?= $p['image'] ?>" width="80" class="mb-2"><br>
  <input class="form-control mb-3" name="image" type="file">

  <button class="btn btn-success">Update</button>
  <a href="products.php" class="btn btn-secondary">Cancel</a>
</form>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
function startBarcodeScan() {
  const scanner = new Html5Qrcode("reader");
  scanner.start(
    { facingMode: "environment" },
    {
      fps: 10,
      formatsToSupport: [
        Html5QrcodeSupportedFormats.EAN_13,
        Html5QrcodeSupportedFormats.UPC_A,
        Html5QrcodeSupportedFormats.CODE_128
      ]
    },
    code => {
      barcode.value = code;
      scanner.stop();
    }
  );
}
</script>

<?php require "_footer.php"; ?>
