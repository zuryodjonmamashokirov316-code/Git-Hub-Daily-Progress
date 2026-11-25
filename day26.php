<?php
// 10) cart.php - OOP Cart klassi (session asosida) + sahifa
require_once __DIR__ . '/functions.php';
require_login();

class Cart
{
    public function __construct(private PDO $pdo)
    {
        $_SESSION['cart'] ??= [];
    }

    public function add(int $id, int $qty = 1): void
    {
        $st = $this->pdo->prepare('SELECT 1 FROM products WHERE id = ?');
        $st->execute([$id]);
        if (!$st->fetch()) return;
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + max(1, $qty);
    }

    public function remove(int $id): void
    {
        unset($_SESSION['cart'][$id]);
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    public function items(): array
    {
        if (!$_SESSION['cart']) return [];
        $ids = array_keys($_SESSION['cart']);
        $in = implode(',', array_fill(0, count($ids), '?'));
        $st = $this->pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
        $st->execute($ids);
        $rows = [];
        foreach ($st->fetchAll() as $p) {
            $p['qty'] = $_SESSION['cart'][$p['id']];
            $p['subtotal'] = $p['qty'] * $p['price'];
            $rows[] = $p;
        }
        return $rows;
    }

    public function total(): float
    {
        return array_sum(array_column($this->items(), 'subtotal'));
    }
}

$cart = new Cart(db());

if (isset($_GET['add'])) {
    $cart->add((int)$_GET['add']);
    redirect('cart.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    match ($_POST['action'] ?? '') {
        'remove' => $cart->remove((int)($_POST['id'] ?? 0)),
        'clear'  => $cart->clear(),
        default  => null,
    };
    redirect('cart.php');
}
$items = $cart->items();
?>
<!DOCTYPE html>
<html lang="uz"><head><meta charset="UTF-8"><title>Savat</title></head>
<body>
<h2>Savat</h2>
<?php if (!$items): ?>
    <p>Savat bo'sh.</p>
<?php else: ?>
<table border="1" cellpadding="6">
<tr><th>Nomi</th><th>Narx</th><th>Soni</th><th>Jami</th><th></th></tr>
<?php foreach ($items as $it): ?>
<tr>
    <td><?= e($it['title']) ?></td>
    <td><?= number_format($it['price'], 2) ?></td>
    <td><?= (int)$it['qty'] ?></td>
    <td><?= number_format($it['subtotal'], 2) ?></td>
    <td>
        <form method="post"><?= csrf_field() ?>
            <input type="hidden" name="action" value="remove">
            <input type="hidden" name="id" value="<?= $it['id'] ?>">
            <button>X</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>
<h3>Umumiy: <?= number_format($cart->total(), 2) ?></h3>
<form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="clear"><button>Tozalash</button></form>
<?php endif; ?>
<a href="products.php">Mahsulotlar</a> | <a href="dashboard.php">Dashboard</a>
</body></html>
