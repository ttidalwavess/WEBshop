<?php
//define('ROOT', dirname(__DIR__, 2));
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

session_start_safe();
header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
    exit;
}

$cart_id = (int)($_POST['cart_id'] ?? 0);

if ($cart_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Неверный ID корзины']);
    exit;
}

$pdo = db();
$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT id FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $user_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Доступ запрещён']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->execute([$cart_id]);

    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_count = (int)($stmt->fetch()['total'] ?? 0);

    echo json_encode(['success' => true, 'cart_count' => $cart_count]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Ошибка базы данных']);
}