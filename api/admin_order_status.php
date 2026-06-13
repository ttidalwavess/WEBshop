<?php
// /api/admin_order_status.php — меняет статус заказа
define('ROOT', dirname(__DIR__));
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

session_start_safe();

header('Content-Type: application/json');

if (!is_admin()) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

$orderId  = (int)($_POST['order_id']  ?? 0);
$statusId = (int)($_POST['status_id'] ?? 0);

if (!$orderId || !$statusId) {
    echo json_encode(['success' => false, 'error' => 'Invalid params']);
    exit;
}

$pdo = db();

$pdo->prepare('UPDATE orders SET status_id = ? WHERE id = ?')
    ->execute([$statusId, $orderId]);

$stmt = $pdo->prepare('SELECT name FROM order_statuses WHERE id = ? LIMIT 1');
$stmt->execute([$statusId]);
$statusName = $stmt->fetchColumn();

echo json_encode(['success' => true, 'status_name' => $statusName]);