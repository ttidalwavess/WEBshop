<?php
// order.php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';
require_once ROOT . '/includes/orders.php';

session_start_safe();

if (!is_logged_in()) {
    header('Location: /login_required.php');
    exit;
}

$order_id = (int)($_GET['id'] ?? 0);
if ($order_id <= 0) {
    header('Location: /orders.php');
    exit;
}

$order = order_get_details($order_id, $_SESSION['user_id']);

if (!$order) {
    header('Location: /orders.php');
    exit;
}

$page_title = 'LIGHT | Заказ №' . $order['id'];
include ROOT . '/includes/header.php';
?>

<main class="order-page">
    <div class="orders-inner">
        <nav class="breadcrumb">
            <a href="/orders.php">Мои заказы</a>
            <span>/</span>
            <span>Заказ №<?= (int)$order['id'] ?></span>
        </nav>

        <div class="order-detail__header">
            <h1 class="orders-title">Заказ №<?= (int)$order['id'] ?></h1>
        </div>

        <div class="order-detail__meta">
            <div class="order-meta-item">
                <div class="order-meta-item__label">Дата заказа</div>
                <div class="order-meta-item__value">
                    <?= date('d.m.Y в H:i', strtotime($order['created_at'])) ?>
                </div>
            </div>
            <div class="order-meta-item">
                <div class="order-meta-item__label">Получатель</div>
                <div class="order-meta-item__value">
                    <?= e($order['customer_name']) ?><br>
                    <small><?= e($order['customer_phone']) ?></small>
                </div>
            </div>
            <div class="order-meta-item">
                <div class="order-meta-item__label">Сумма заказа</div>
                <div class="order-meta-item__value order-meta-item__value--price">
                    ₽ <?= number_format((float)$order['total'], 0, '.', ' ') ?>
                </div>
            </div>
            <div class="order-meta-item">
                <div class="order-meta-item__label">Статус</div>
                <div class="order-meta-item__value">
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
                </div>
            </div>
        </div>

        <h2 class="order-detail__subtitle">Состав заказа</h2>

        <div class="order-items">
            <?php foreach ($order['items'] as $item):
                $img = !empty($item['img'])
                    ? UPLOAD_URL . htmlspecialchars($item['img'])
                    : '/img/placeholder.jpg';
            ?>
                <div class="order-item">
                    <a href="/product.php?id=<?= (int)$item['product_id'] ?>" class="order-item__img">
                        <img src="<?= $img ?>" alt="<?= e($item['name']) ?>">
                    </a>
                    <div class="order-item__info">
                        <a href="/product.php?id=<?= (int)$item['product_id'] ?>" class="order-item__name">
                            <?= e($item['name']) ?>
                        </a>
                        <?php if (!empty($item['size']) && $item['size'] !== 'Универсальный'): ?>
                            <div class="order-item__size">Размер: <?= e($item['size']) ?></div>
                        <?php endif; ?>
                        <div class="order-item__qty">Количество: <?= (int)$item['quantity'] ?></div>
                    </div>
                    <div class="order-item__price">
                        ₽ <?= number_format($item['price'] * $item['quantity'], 0, '.', ' ') ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="order-detail__total">
            <span>Итого</span>
            <span>₽ <?= number_format((float)$order['total'], 0, '.', ' ') ?></span>
        </div>

        <a href="/orders.php" class="orders-back">Назад к заказам</a>
    </div>
</main>

<?php include ROOT . '/includes/footer.php'; ?>