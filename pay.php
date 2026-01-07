<?php
require "config/database.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['cart'])) {
    echo json_encode(["success"=>false,"message"=>"Cart empty"]);
    exit;
}

$cash = (float)$data['cash'];
$total = 0;

foreach ($data['cart'] as $item) {
    if ($item['price'] <= 0 || $item['qty'] <= 0) {
        echo json_encode(["success"=>false,"message"=>"Invalid item"]);
        exit;
    }
    $total += $item['price'] * $item['qty'];
}

if ($cash < $total) {
    echo json_encode(["success"=>false,"message"=>"Insufficient cash"]);
    exit;
}

$change = $cash - $total;

$pdo->beginTransaction();

$stmt = $pdo->prepare(
  "INSERT INTO sales (total, cash, change_amount) VALUES (?, ?, ?)"
);
$stmt->execute([$total, $cash, $change]);
$saleId = $pdo->lastInsertId();

$itemStmt = $pdo->prepare(
  "INSERT INTO sale_items (sale_id, product_name, qty, price)
   VALUES (?, ?, ?, ?)"
);

foreach ($data['cart'] as $item) {
    $itemStmt->execute([
        $saleId,
        $item['name'],
        $item['qty'],
        $item['price']
    ]);

    if (is_numeric($item['id'])) {
        $pdo->prepare(
          "UPDATE products SET stock = stock - ? WHERE id = ?"
        )->execute([$item['qty'], $item['id']]);
    }
}

$pdo->commit();

echo json_encode([
    "success" => true,
    "sale_id" => $saleId,
    "total"   => number_format($total,2),
    "cash"    => number_format($cash,2),
    "change"  => number_format($change,2)
]);
