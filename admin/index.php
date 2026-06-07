// Кол-во категорий, товаров, пользователей, заказов за сегодня
<? php
$pdo = db();
$stats = [
  'categories' => $pdo->query('SELECT COUNT(*) FROM product_categories')->fetchColumn(),
  'products' => $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
  'users' => $pdo->query('SELECT COUNT(*) FROM users WHERE role="user"')->fetchColumn(),
  'orders_today' => $pdo->query(
      'SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()'
  )->fetchColumn(),
];
?>