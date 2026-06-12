<?php
// /api/admin_orders.php — отдаёт список заказов для live-таблицы
define('ROOT', dirname(__DIR__));
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

session_start_safe();

header('Content-Type: application/json');

if (!is_admin()) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

$pdo = db();

$stmt = $pdo->query("
    SELECT
        o.id,
        o.total,
        o.created_at,
        u.username,
        os.name AS status,
        GROUP_CONCAT(p.name ORDER BY p.name SEPARATOR ', ') AS items
    FROM orders o
    JOIN users u         ON u.id  = o.user_id
    JOIN order_statuses os ON os.id = o.status_id
    LEFT JOIN order_items oi ON oi.order_id = o.id
    LEFT JOIN products p     ON p.id = oi.product_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 100
");

$orders = $stmt->fetchAll();

$result = [];
foreach ($orders as $o) {
    $result[] = [
        'id'         => (int)$o['id'],
        'username'   => $o['username'],
        'total'      => number_format((float)$o['total'], 0, '.', ' '),
        'status'     => $o['status'],
        'items'      => $o['items'] ?? '—',
        'created_at' => date('d.m.Y H:i', strtotime($o['created_at'])),
    ];
}

echo json_encode(['success' => true, 'orders' => $result]);