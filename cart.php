<?php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

session_start_safe();

if (!is_logged_in()) { header('Location: /login_required.php'); exit; }

$pdo = db();

$stmt = $pdo->prepare("
    SELECT c.id AS cart_id, c.quantity, c.product_id, c.size,
           p.name, p.price,
           pi.filename AS img
    FROM cart c
    JOIN products p ON p.id = c.product_id
    LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
    WHERE c.user_id = ?
    ORDER BY c.added_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

$page_title = 'LIGHT | Корзина';

// ДОБАВЬТЕ ЭТУ СТРОКУ ДЛЯ ПОДКЛЮЧЕНИЯ ДОПОЛНИТЕЛЬНОГО JS
$extra_js = ['/assets/js/cart-page.js'];

include ROOT . '/includes/header.php';
?>

<main class="cart-page">
    <div class="cart-layout">

        <div class="cart-left">
            <h1 class="cart-title">
                Корзина
                <?php if (!empty($items)): ?>
                    <span class="cart-title__count">(<?= count($items) ?>)</span>
                <?php endif; ?>
            </h1>

            <?php if (empty($items)): ?>
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"/>
                            <circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                    </div>
                    <p class="empty-state__text">Ваша корзина пуста</p>
                    <a href="/catalog.php" class="empty-state__btn">Перейти в каталог</a>
                </div>

            <?php else: ?>
                <div class="cart-items" id="cart-items">
                    <?php foreach ($items as $item): ?>
                        <?php
                        $img = !empty($item['img'])
                            ? UPLOAD_URL . htmlspecialchars($item['img'])
                            : '/img/placeholder.jpg';
                        ?>
                        <div class="cart-item" data-cart-id="<?= (int)$item['cart_id'] ?>">
                            <a href="/product.php?id=<?= (int)$item['product_id'] ?>" class="cart-item__img">
                                <img src="<?= $img ?>" alt="<?= e($item['name']) ?>">
                            </a>
                            <div class="cart-item__info">
                                <a href="/product.php?id=<?= (int)$item['product_id'] ?>"
                                   class="cart-item__name"><?= e($item['name']) ?></a>
                                <?php if (!empty($item['size']) && $item['size'] !== 'Универсальный'): ?>
                                    <div class="cart-item__size">Размер: <?= e($item['size']) ?></div>
                                <?php endif; ?>
                                <div class="cart-item__price" data-price="<?= (float)$item['price'] ?>">
                                    ₽ <?= number_format($item['price'] * $item['quantity'], 0, '.', ' ') ?>
                                </div>
                            </div>
                            <div class="cart-item__controls">
                                <div class="qty-control">
                                    <button class="qty-btn qty-minus" data-cart-id="<?= (int)$item['cart_id'] ?>">−</button>
                                    <span class="qty-value" data-cart-id="<?= (int)$item['cart_id'] ?>"><?= (int)$item['quantity'] ?></span>
                                    <button class="qty-btn qty-plus" data-cart-id="<?= (int)$item['cart_id'] ?>">+</button>
                                </div>
                            </div>
                            <button class="cart-item__remove" data-cart-id="<?= (int)$item['cart_id'] ?>" aria-label="Удалить">×</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="/catalog.php" class="cart-continue">Продолжить покупки</a>
            <?php endif; ?>
        </div>

        <?php if (!empty($items)): ?>
        <div class="cart-right">
            <div class="cart-summary">
                <h2 class="cart-summary__title">Итого</h2>
                <div class="cart-summary__row cart-summary__row--header">
                    <span>Товары (<?= count($items) ?>)</span>
                    <span id="summary-total">₽ <?= number_format($total, 0, '.', ' ') ?></span>
                </div>
                <div class="cart-summary__items" id="cart-summary-items">
                    <?php foreach ($items as $item): ?>
                        <div class="cart-summary__item" data-cart-id="<?= (int)$item['cart_id'] ?>">
                            <span class="cart-summary__item-name"><?= e($item['name']) ?></span>
                            <span class="cart-summary__item-price" data-price="<?= (float)$item['price'] ?>">
                                ₽ <?= number_format($item['price'] * $item['quantity'], 0, '.', ' ') ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="cart-summary__row cart-summary__row--total">
                    <span>К оплате</span>
                    <span id="summary-final">₽ <?= number_format($total, 0, '.', ' ') ?></span>
                </div>
                <a href="/checkout.php" class="cart-checkout-btn">Оформить заказ</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php include ROOT . '/includes/footer.php'; ?>