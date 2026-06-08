// Кол-во категорий, товаров, пользователей, заказов за сегодня
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/security.php';

seccion_start_safe();
require_admin();

$pdo = db();
$stats = [
  'categories' => (int)$pdo->query('SELECT COUNT(*) FROM product_categories')->fetchColumn(),
  'products' => (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
  'users' => (int)$pdo->query('SELECT COUNT(*) FROM users WHERE role="user"')->fetchColumn(),
  'orders_today' => (int)$pdo->query(
      'SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()'
  )->fetchColumn(),
];
?>
