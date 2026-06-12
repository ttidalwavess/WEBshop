<?php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

session_start_safe();

if (!is_logged_in()) { header('Location: /login_required.php'); exit; }

$order_id = (int)($_GET['id'] ?? 0);
if ($order_id <= 0) { header('Location: /orders.php'); exit; }

$pdo = db();

$stmt = $pdo->prepare("
    SELECT o.id, o.total, o.created_at, os.name AS status
    FROM orders o
    JOIN order_statuses os ON os.id = o.status_id
    WHERE o.id = ? AND o.user_id = ?
    LIMIT 1
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT oi.quantity, oi.price,
           p.id AS product_id, p.name, p.size,
           pi.filename AS img
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

// -------------------------------------------------------
// ЗАГЛУШКА
$order = [
    'id'         => 7,
    'total'      => 15180,
    'created_at' => '2025-05-15 18:32:00',
    'status'     => 'shipped',
];
$items = [
    ['product_id'=>1, 'name'=>'Платье красное',  'price'=>4990, 'quantity'=>1, 'size'=>'S',  'img'=>'dress_red.png'],
    ['product_id'=>3, 'name'=>'Платье браун',    'price'=>6200, 'quantity'=>1, 'size'=>'M',  'img'=>'dress_brown.png'],
    ['product_id'=>7, 'name'=>'Блузка бежевая',  'price'=>2200, 'quantity'=>2, 'size'=>'S',  'img'=>'blouse_beige.png'],
];
// -------------------------------------------------------

if (!$order) { header('Location: /orders.php'); exit; }

function status_label(string $s): array {
    return [
        'pending'    => ['Принят',      '#b8860b'],
        'processing' => ['В обработке', '#1a6b9a'],
        'shipped'    => ['Отправлен',   '#2d7a2d'],
        'delivered'  => ['Доставлен',   '#27ae60'],
        'cancelled'  => ['Отменён',     '#c0392b'],
    ][$s] ?? [$s, '#888'];
}

[$status_label, $status_color] = status_label($order['status']);

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

        <!-- Шапка заказа -->
        <div class="order-detail__header">
            <h1 class="orders-title">Заказ №<?= (int)$order['id'] ?></h1>
        </div>

        <!-- Мета-инфо -->
        <div class="order-detail__meta">
            <div class="order-meta-item">
                <div class="order-meta-item__label">Дата заказа</div>
                <div class="order-meta-item__value">
                    <?= date('d.m.Y в H:i', strtotime($order['created_at'])) ?>
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
                <div class="order-meta-item__value" style="color:<?= $status_color ?>;font-weight:700">
                    <?= e($status_label) ?>
                </div>
            </div>
        </div>

        <!-- Товары -->
        <h2 class="order-detail__subtitle">Состав заказа</h2>

        <div class="order-items">
            <?php foreach ($items as $item):
                $img = !empty($item['img'])
                    ? UPLOAD_URL . htmlspecialchars($item['img'])
                    : '/img/placeholder.jpg';
            ?>
                <div class="order-item">
                    <a href="/product.php?id=<?= (int)$item['product_id'] ?>"
                       class="order-item__img">
                        <img src="<?= $img ?>" alt="<?= e($item['name']) ?>">
                    </a>
                    <div class="order-item__info">
                        <a href="/product.php?id=<?= (int)$item['product_id'] ?>"
                           class="order-item__name"><?= e($item['name']) ?></a>
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

        <!-- Итого -->
        <div class="order-detail__total">
            <span>Итого</span>
            <span>₽ <?= number_format((float)$order['total'], 0, '.', ' ') ?></span>
        </div>

        <a href="/orders.php" class="orders-back">Назад к заказам</a>

    </div>
</main>

<?php include ROOT . '/includes/footer.php'; ?>