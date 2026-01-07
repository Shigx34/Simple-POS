<?php
session_start();
require "config/database.php";

$stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
$stmt->execute([$_POST['username']]);
$user = $stmt->fetch();

if ($user && password_verify($_POST['password'], $user['password'])) {
  $_SESSION['user_id'] = $user['id'];
  header("Location: index.php");
  exit;
}
header("Location: login.php");
