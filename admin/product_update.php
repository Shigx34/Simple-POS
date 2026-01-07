<?php
require "../config/database.php";

$id = (int)$_POST['id'];
$image = $_POST['old_image'];

if (!empty($_FILES['image']['name'])) {
    $image = time() . "_" . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/products/" . $image);
}

$stmt = $pdo->prepare(
  "UPDATE products
   SET name=?, price=?, stock=?, barcode=?, image=?
   WHERE id=?"
);

$stmt->execute([
  $_POST['name'],
  $_POST['price'],
  $_POST['stock'],
  $_POST['barcode'],
  $image,
  $id
]);

header("Location: products.php");
exit;
