<?php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

session_start_safe();

if (!is_logged_in()) { header('Location: /login_required.php'); exit; }

$pdo = db();

$stmt = $pdo->prepare("
    SELECT o.id, o.total, o.created_at, os.name AS status
    FROM orders o
    JOIN order_statuses os ON os.id = o.status_id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

// -------------------------------------------------------
// ЗАГЛУШКА — удалить когда появятся реальные заказы
$orders = [
    ['id'=>1, 'total'=>11990, 'created_at'=>'2025-05-01 14:22:00', 'status'=>'delivered'],
    ['id'=>2, 'total'=>4990,  'created_at'=>'2025-05-10 09:45:00', 'status'=>'shipped'],
    ['id'=>3, 'total'=>6200,  'created_at'=>'2025-05-15 18:00:00', 'status'=>'pending'],
];
// -------------------------------------------------------


function status_label(string $s): array {
    return [
        'pending'    => ['Принят',      '#b8860b'],
        'processing' => ['В обработке', '#1a6b9a'],
        'shipped'    => ['Отправлен',   '#2d7a2d'],
        'delivered'  => ['Доставлен',   '#27ae60'],
        'cancelled'  => ['Отменён',     '#c0392b'],
    ][$s] ?? [$s, '#888'];
}

$page_title = 'LIGHT | Мои заказы';
include ROOT . '/includes/header.php';
?>

<main class="orders-page">
    <div class="orders-inner">

        <h1 class="orders-title">Мои заказы</h1>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <div class="empty-state__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="2"/>
                        <line x1="9" y1="12" x2="15" y2="12"/>
                        <line x1="9" y1="16" x2="13" y2="16"/>
                    </svg>
                </div>
                <p class="empty-state__text">Заказов пока нет</p>
                <a href="/index.php" class="empty-state__btn">Перейти на главную</a>
            </div>

        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order):
                    [$label, $color] = status_label($order['status']);
                ?>
                    <div class="order-card">
                        <div class="order-card__left">
                            <div class="order-card__number">Заказ №<?= (int)$order['id'] ?></div>
                            <div class="order-card__date">
                                <?= date('d.m.Y', strtotime($order['created_at'])) ?>
                            </div>
                        </div>
                        <div class="order-card__center">
                            <div class="order-card__total">
                                ₽ <?= number_format((float)$order['total'], 0, '.', ' ') ?>
                            </div>
                        </div>
                        <div class="order-card__right">
                            <span class="order-status" style="color:<?= $color ?>">
                                <?= e($label) ?>
                            </span>
                            <a href="/order.php?id=<?= (int)$order['id'] ?>"
                               class="order-card__btn">Подробнее →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <a href="/catalog.php" class="orders-back">Продолжить покупки</a>
        <?php endif; ?>

    </div>
</main>

<?php include ROOT . '/includes/footer.php'; ?>