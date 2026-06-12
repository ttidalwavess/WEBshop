<?php
define('ROOT', dirname(__DIR__));
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

session_start_safe();
require_admin();

$pdo   = db();
$stats = [
    'categories'      => (int)$pdo->query('SELECT COUNT(*) FROM product_categories')->fetchColumn(),
    'products_total'  => (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'users'           => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn(),
    'orders_today'    => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at)=CURDATE()")->fetchColumn(),
    'orders_total'    => (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
];

$recentOrders = $pdo->query(
    "SELECT o.id, o.total, o.created_at, u.username, os.name AS status_name
     FROM orders o
     JOIN users u ON u.id=o.user_id
     JOIN order_statuses os ON os.id=o.status_id
     ORDER BY o.created_at DESC LIMIT 5"
)->fetchAll();

$statusColors = ['pending'=>'badge--warning','processing'=>'badge--info',
                 'shipped'=>'badge--primary','delivered'=>'badge--success','cancelled'=>'badge--danger'];
$statusLabels = ['pending'=>'Ожидает','processing'=>'Обрабатывается',
                 'shipped'=>'Отправлен','delivered'=>'Доставлен','cancelled'=>'Отменён'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дашборд — Админка</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/admin/assets/css/admin.css">
</head>
<body class="admin-layout">
<?php include __DIR__ . '/includes/admin_nav.php'; ?>
<main class="admin-main">
    <div class="admin-header">
        <h1>Дашборд</h1>
        <span class="admin-header__time"><?= date('d.m.Y, H:i') ?></span>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card__icon">🗂</div>
            <div class="stat-card__body">
                <div class="stat-card__value"><?= $stats['categories'] ?></div>
                <div class="stat-card__label">Категорий</div>
            </div>
            <a href="/admin/categories.php" class="stat-card__link">Управлять →</a>
        </div>
        <div class="stat-card">
            <div class="stat-card__icon">👗</div>
            <div class="stat-card__body">
                <div class="stat-card__value"><?= $stats['products_total'] ?></div>
                <div class="stat-card__label">Товаров <small>(активных: <?= $stats['products_active'] ?>)</small></div>
            </div>
            <a href="/admin/products.php" class="stat-card__link">Управлять →</a>
        </div>
        <div class="stat-card">
            <div class="stat-card__icon">👤</div>
            <div class="stat-card__body">
                <div class="stat-card__value"><?= $stats['users'] ?></div>
                <div class="stat-card__label">Покупателей</div>
            </div>
        </div>
        <div class="stat-card stat-card--accent">
            <div class="stat-card__icon">🛒</div>
            <div class="stat-card__body">
                <div class="stat-card__value"><?= $stats['orders_today'] ?></div>
                <div class="stat-card__label">Заказов сегодня <small>(всего: <?= $stats['orders_total'] ?>)</small></div>
            </div>
            <a href="/admin/admin_orders.php" class="stat-card__link">Live-заказы →</a>
        </div>
    </div>

    <section class="admin-card">
        <div class="admin-card__header">
            <h2>Последние заказы</h2>
            <a href="/admin/admin_orders.php" class="btn btn--sm btn--ghost">Все заказы</a>
        </div>
        <?php if (empty($recentOrders)): ?>
            <p class="empty-hint">Заказов пока нет.</p>
        <?php else: ?>
        <table class="admin-table">
            <thead><tr><th>#</th><th>Покупатель</th><th>Сумма</th><th>Статус</th><th>Дата</th></tr></thead>
            <tbody>
            <?php foreach ($recentOrders as $o):
                $badge = $statusColors[$o['status_name']] ?? 'badge--secondary';
                $label = $statusLabels[$o['status_name']] ?? htmlspecialchars($o['status_name'], ENT_QUOTES, 'UTF-8');
            ?>
                <tr>
                    <td><strong>#<?= (int)$o['id'] ?></strong></td>
                    <td><?= htmlspecialchars($o['username'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= number_format((float)$o['total'], 0, '.', ' ') ?> ₽</td>
                    <td><span class="badge <?= $badge ?>"><?= $label ?></span></td>
                    <td><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>

    <section class="admin-card">
        <h2>Быстрые действия</h2>
        <div class="quick-actions">
            <a href="/admin/categories.php" class="btn btn--secondary">➕ Новая категория</a>
            <a href="/admin/products.php"   class="btn btn--secondary">➕ Новый товар</a>
            <a href="/" target="_blank"     class="btn btn--ghost">🌐 Открыть сайт</a>
        </div>
    </section>
</main>
<script src="/admin/assets/js/admin.js"></script>
</body>
</html>
