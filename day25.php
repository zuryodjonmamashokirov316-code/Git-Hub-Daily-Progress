<?php
// 9) api.php - oddiy JSON REST API
// GET    api.php            -> barcha mahsulotlar
// GET    api.php?id=1       -> bitta mahsulot
// POST   api.php            -> yaratish (JSON body, login kerak)
// DELETE api.php?id=1       -> o'chirish (login kerak)
require_once __DIR__ . '/functions.php';
header('Content-Type: application/json; charset=utf-8');

function respond(array $data, int $code = 200): never {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

switch ($method) {
    case 'GET':
        if ($id) {
            $st = $pdo->prepare('SELECT * FROM products WHERE id = ?');
            $st->execute([$id]);
            $row = $st->fetch();
            $row ? respond($row) : respond(['error' => 'Topilmadi'], 404);
        }
        respond($pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll());

    case 'POST':
        if (!is_logged_in()) respond(['error' => 'Avval login qiling'], 401);
        $in = json_decode(file_get_contents('php://input'), true) ?? [];
        $title = trim($in['title'] ?? '');
        $price = (float)($in['price'] ?? 0);
        if ($title === '' || $price <= 0) respond(['error' => "title va price kerak"], 422);
        $pdo->prepare('INSERT INTO products (title, price, description) VALUES (?, ?, ?)')
            ->execute([$title, $price, trim($in['description'] ?? '')]);
        respond(['id' => (int)$pdo->lastInsertId()], 201);

    case 'DELETE':
        if (!is_logged_in()) respond(['error' => 'Avval login qiling'], 401);
        if (!$id) respond(['error' => 'id kerak'], 422);
        $st = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $st->execute([$id]);
        respond(['deleted' => $st->rowCount()]);

    default:
        respond(['error' => 'Method qo\'llab-quvvatlanmaydi'], 405);
}
