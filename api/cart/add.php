<?php
define('ROOT', dirname(__DIR__, 2));
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/security.php';

session_start_safe();
header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация']);
    exit;
}

$product_id = (int)($_POST['product_id'] ?? 0);
$quantity = max(1, (int)($_POST['qty'] ?? 1));
$size = trim($_POST['size'] ?? '');

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Неверный ID товара']);
    exit;
}

$pdo = db();
$user_id = $_SESSION['user_id'];

try {
    // увеличиваем количество одинакового размера
    $stmt = $pdo->prepare("
        INSERT INTO cart (user_id, product_id, size, quantity) 
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + ?
    ");
    $stmt->execute([$user_id, $product_id, $size, $quantity, $quantity]);

    // общее количество товаров в корзине
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_count = (int)$stmt->fetch()['total'];

    echo json_encode(['success' => true, 'cart_count' => $cart_count]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
}