<?php
require "../config/database.php";

$name    = trim($_POST['name']);
$price   = (float)$_POST['price'];
$stock   = (int)$_POST['stock'];
$barcode = trim($_POST['barcode']);

$image = "NO_IMAGE.png";

/* IMAGE UPLOAD */
if (!empty($_FILES['image']['name'])) {
    $image = time() . "_" . basename($_FILES['image']['name']);
    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        "../uploads/products/" . $image
    );
}

/* CHECK EXISTING BARCODE */
$check = $pdo->prepare(
    "SELECT id, stock
     FROM products
     WHERE barcode = ?
     LIMIT 1"
);
$check->execute([$barcode]);
$product = $check->fetch(PDO::FETCH_ASSOC);

if ($product) {
    // ✅ BARCODE EXISTS → ADD STOCK
    $newStock = $product['stock'] + $stock;

    $update = $pdo->prepare(
        "UPDATE products
         SET stock = ?
         WHERE id = ?"
    );
    $update->execute([$newStock, $product['id']]);

} else {
    // ✅ NEW BARCODE → INSERT
    $insert = $pdo->prepare(
        "INSERT INTO products (name, price, stock, barcode, image)
         VALUES (?, ?, ?, ?, ?)"
    );
    $insert->execute([
        $name,
        $price,
        $stock,
        $barcode,
        $image
    ]);
}

header("Location: products.php");
exit;
