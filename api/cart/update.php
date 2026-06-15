<?php
define('ROOT', dirname(__DIR__, 2));
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/security.php';

session_start_safe();
header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
    exit;
}

$cart_id = (int)($_POST['cart_id'] ?? 0);
$quantity = max(0, (int)($_POST['qty'] ?? 0));

if ($cart_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Неверный ID корзины']);
    exit;
}

$pdo = db();
$user_id = $_SESSION['user_id'];

try {
    // корзина принадлежит пользователю, получение цены
    $stmt = $pdo->prepare("
        SELECT c.id, c.quantity, p.price 
        FROM cart c
        JOIN products p ON p.id = c.product_id
        WHERE c.id = ? AND c.user_id = ?
    ");
    $stmt->execute([$cart_id, $user_id]);
    $cart_item = $stmt->fetch();
    
    if (!$cart_item) {
        echo json_encode(['success' => false, 'error' => 'Доступ запрещён']);
        exit;
    }

    if ($quantity <= 0) {
        // удаление товара
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
        $stmt->execute([$cart_id]);
    } else {
        // обновление количества
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$quantity, $cart_id]);
    }

    // общее количество
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_count = (int)($stmt->fetch()['total'] ?? 0);

    echo json_encode([
        'success' => true, 
        'cart_count' => $cart_count,
        'new_quantity' => $quantity,
        'price' => (float)$cart_item['price']
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}