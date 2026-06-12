<?php
define('ROOT', dirname(__DIR__));
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

session_start_safe();
header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Необходима авторизация']);
    exit;
}

$product_id = (int)($_POST['product_id'] ?? 0);
$qty        = max(1, (int)($_POST['qty'] ?? 1));
$size       = trim($_POST['size'] ?? 'Универсальный');

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Неверный товар']);
    exit;
}

$pdo     = db();
$user_id = (int)$_SESSION['user_id'];

// Проверяем что товар существует
$stmt = $pdo->prepare('SELECT id FROM products WHERE id = ? LIMIT 1');
$stmt->execute([$product_id]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Товар не найден']);
    exit;
}

// INSERT или UPDATE по комбинации user+product+size
$stmt = $pdo->prepare('
    INSERT INTO cart (user_id, product_id, size, quantity)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
');
$stmt->execute([$user_id, $product_id, $size, $qty]);

// Считаем общее количество
$stmt = $pdo->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart WHERE user_id = ?');
$stmt->execute([$user_id]);
$cart_count = (int)$stmt->fetchColumn();

echo json_encode(['success' => true, 'cart_count' => $cart_count]);