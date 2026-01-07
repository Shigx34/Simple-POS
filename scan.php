<?php
require "config/database.php";

$stmt=$pdo->prepare("SELECT id,name,price FROM products WHERE barcode=?");
$stmt->execute([$_POST['barcode']]);
$p=$stmt->fetch();

echo json_encode($p?["success"=>true,"product"=>$p]:["success"=>false]);
