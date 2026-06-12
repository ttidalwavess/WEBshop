<?php
// orders.php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';
require_once ROOT . '/includes/orders.php';

session_start_safe();

if (!is_logged_in()) {
    header('Location: /login_required.php');
    exit;
}

$orders = order_get_by_user($_SESSION['user_id']);

$page_title = 'LIGHT | Мои заказы';
include ROOT . '/includes/header.php';
?>

<main class="orders-page">
    <div class="orders-inner">
        <h1 class="orders-title">Мои заказы</h1>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <div class="empty-state__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="2"/>
                        <line x1="9" y1="12" x2="15" y2="12"/>
                        <line x1="9" y1="16" x2="13" y2="16"/>
                    </svg>
                </div>
                <p class="empty-state__text">У вас пока нет заказов</p>
                <a href="/catalog.php" class="empty-state__btn">Перейти в каталог</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
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
                            <span class="order-status">
                                <?php
                                $status_labels = [
                                    'pending' => 'Принят',
                                    'processing' => 'В обработке',
                                    'shipped' => 'Отправлен',
                                    'delivered' => 'Доставлен',
                                    'cancelled' => 'Отменён'
                                ];
                                echo $status_labels[$order['status']] ?? $order['status'];
                                ?>
                            </span>
                            <a href="/order.php?id=<?= (int)$order['id'] ?>" class="order-card__btn">Подробнее →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="/catalog.php" class="orders-back">Продолжить покупки</a>
        <?php endif; ?>
    </div>
</main>

<?php include ROOT . '/includes/footer.php'; ?>