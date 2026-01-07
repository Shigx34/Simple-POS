<?php
require "../config/database.php";

$image = "NO_IMAGE.png";

if (!empty($_FILES['image']['name'])) {
    $image = time() . "_" . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/products/" . $image);
}

$stmt = $pdo->prepare(
  "INSERT INTO products (name, price, stock, barcode, image)
   VALUES (?, ?, ?, ?, ?)"
);

$stmt->execute([
  $_POST['name'],
  $_POST['price'],
  $_POST['stock'],
  $_POST['barcode'],
  $image
]);

header("Location: products.php");
exit;
