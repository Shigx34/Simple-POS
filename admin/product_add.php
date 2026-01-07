<?php
require "_header.php";
?>

<h4>Add Product</h4>

<form method="post" action="product_save.php" enctype="multipart/form-data">
  <input class="form-control mb-2" name="name" placeholder="Product name" required>
  <input class="form-control mb-2" name="price" type="number" step="0.01" placeholder="Price" required>
  <input class="form-control mb-2" name="stock" type="number" placeholder="Stock" required>

  <div class="input-group mb-2">
    <input class="form-control" name="barcode" id="barcode" placeholder="Barcode">
    <button type="button" class="btn btn-primary" onclick="startBarcodeScan()">📷 Scan</button>
  </div>

  <div id="reader" class="mb-2"></div>

  <input class="form-control mb-3" name="image" type="file">

  <button class="btn btn-success">Save</button>
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
      reader.innerHTML = "";
    }
  );
}
</script>

<?php require "_footer.php"; ?>
