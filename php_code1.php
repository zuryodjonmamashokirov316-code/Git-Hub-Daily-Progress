<?php
// 6) dashboard.php - himoyalangan sahifa (faqat kirgan foydalanuvchi uchun)
require_once __DIR__ . '/functions.php';
require_login();

$user = current_user();
$stats = db()->query('SELECT COUNT(*) AS cnt, COALESCE(AVG(price),0) AS avg_price, COALESCE(MAX(price),0) AS max_price FROM products')->fetch();
?>
<!DOCTYPE html>
<html lang="uz"><head><meta charset="UTF-8"><title>Dashboard</title></head>
<body>
<h2>Salom, <?= e($user['name']) ?>!</h2>
<p>Email: <?= e($user['email']) ?></p>
<ul>
    <li>Mahsulotlar soni: <b><?= (int)$stats['cnt'] ?></b></li>
    <li>O'rtacha narx: <b><?= number_format((float)$stats['avg_price'], 2) ?></b></li>
    <li>Eng qimmat: <b><?= number_format((float)$stats['max_price'], 2) ?></b></li>
</ul>
<a href="products.php">Mahsulotlar</a> |
<a href="upload.php">Rasm yuklash</a> |
<a href="cart.php">Savat</a> |
<a href="logout.php">Chiqish</a>
</body></html>
