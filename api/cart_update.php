<?php
// api/cart_update.php — изменить количество товара в корзине
define('ROOT', dirname(__DIR__));
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

session_start_safe();
header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Необходима авторизация']);
    exit;
}

$cart_id = (int)($_POST['cart_id'] ?? 0);
$qty     = (int)($_POST['qty'] ?? 0);

if ($cart_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Неверный параметр']);
    exit;
}

$pdo     = db();
$user_id = (int)$_SESSION['user_id'];

if ($qty <= 0) {
    // Удаляем если количество 0
    $stmt = $pdo->prepare('DELETE FROM cart WHERE id = ? AND user_id = ?');
    $stmt->execute([$cart_id, $user_id]);
} else {
    // Обновляем — только свою запись
    $stmt = $pdo->prepare('UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?');
    $stmt->execute([$qty, $cart_id, $user_id]);
}

$stmt = $pdo->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart WHERE user_id = ?');
$stmt->execute([$user_id]);
$cart_count = (int)$stmt->fetchColumn();

echo json_encode(['success' => true, 'cart_count' => $cart_count]);