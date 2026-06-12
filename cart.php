<?php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

session_start_safe();

if (!is_logged_in()) { header('Location: /login_required.php'); exit; }

$pdo = db();

$stmt = $pdo->prepare("
    SELECT c.id AS cart_id, c.quantity, c.product_id,
           p.name, p.price, p.size,
           pi.filename AS img
    FROM cart c
    JOIN products p ON p.id = c.product_id
    LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
    WHERE c.user_id = ?
    ORDER BY c.added_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

// -------------------------------------------------------
// ЗАГЛУШКА — удалить когда корзина заработает через AJAX

$items = [
    ['cart_id'=>1, 'quantity'=>1, 'product_id'=>1,
    'name'=>'Платье красное', 'price'=>4990, 'size'=>'S', 'img'=>'dress_red.png'],
    ['cart_id'=>2, 'quantity'=>2, 'product_id'=>3,
    'name'=>'Платье браун', 'price'=>6200, 'size'=>'M', 'img'=>'dress_brown.png'],
];
// -------------------------------------------------------

$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

$page_title = 'LIGHT | Корзина';
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
                        <div class="cart-item"
                             data-cart-id="<?= (int)$item['cart_id'] ?>"
                             data-price="<?= (float)$item['price'] ?>">

                            <a href="/product.php?id=<?= (int)$item['product_id'] ?>" class="cart-item__img">
                                <img src="<?= $img ?>" alt="<?= e($item['name']) ?>">
                            </a>

                            <div class="cart-item__info">
                                <a href="/product.php?id=<?= (int)$item['product_id'] ?>"
                                   class="cart-item__name"><?= e($item['name']) ?></a>
                                <?php if (!empty($item['size']) && $item['size'] !== 'Универсальный'): ?>
                                    <div class="cart-item__size">Размер: <?= e($item['size']) ?></div>
                                <?php endif; ?>
                                <div class="cart-item__price">
                                    ₽ <?= number_format($item['price'] * $item['quantity'], 0, '.', ' ') ?>
                                </div>
                            </div>

                            <div class="cart-item__controls">
                                <div class="qty-control">
                                    <button class="qty-btn qty-minus"
                                            data-cart-id="<?= (int)$item['cart_id'] ?>">−</button>
                                    <span class="qty-value"><?= (int)$item['quantity'] ?></span>
                                    <button class="qty-btn qty-plus"
                                            data-cart-id="<?= (int)$item['cart_id'] ?>">+</button>
                                </div>
                            </div>

                            <button class="cart-item__remove"
                                    data-cart-id="<?= (int)$item['cart_id'] ?>"
                                    aria-label="Удалить">×</button>
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

                <div class="cart-summary__items">
                    <?php foreach ($items as $item): ?>
                        <div class="cart-summary__item"
                             data-cart-id="<?= (int)$item['cart_id'] ?>">
                            <span class="cart-summary__item-name"><?= e($item['name']) ?></span>
                            <span class="cart-summary__item-price">
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

<script>
$(function () {

    $(document).on('click', '.qty-plus, .qty-minus', function () {
        var $btn    = $(this);
        var cartId  = $btn.data('cart-id');
        var $item   = $btn.closest('.cart-item');
        var $qty    = $item.find('.qty-value');
        var current = parseInt($qty.text());
        var newQty  = Math.max(1, current + ($btn.hasClass('qty-plus') ? 1 : -1));

        $.ajax({
            url: '/api/cart_update.php',
            method: 'POST',
            dataType: 'json',
            data: { cart_id: cartId, qty: newQty },
            success: function (res) {
                if (res.success) {
                    updateItem($item, cartId, newQty, parseFloat($item.data('price')));
                    updateCartBadge(res.cart_count);
                }
            },
            error: function () {
                // заглушка до готовности API
                updateItem($item, cartId, newQty, parseFloat($item.data('price')));
            }
        });
    });

    $(document).on('click', '.cart-item__remove', function () {
        var cartId = $(this).data('cart-id');
        var $item  = $(this).closest('.cart-item');

        $.ajax({
            url: '/api/cart_remove.php',
            method: 'POST',
            dataType: 'json',
            data: { cart_id: cartId },
            success: function (res) {
                if (res.success) {
                    removeItem($item, cartId);
                    updateCartBadge(res.cart_count);
                }
            },
            error: function () { removeItem($item, cartId); }
        });
    });

    function updateItem($item, cartId, qty, price) {
        $item.find('.qty-value').text(qty);
        $item.find('.cart-item__price').text('₽ ' + fmt(price * qty));
        $('[data-cart-id="' + cartId + '"].cart-summary__item .cart-summary__item-price')
            .text('₽ ' + fmt(price * qty));
        recalc();
    }

    function removeItem($item, cartId) {
        $item.fadeOut(300, function () { $(this).remove(); recalc(); updateCount(); });
        $('[data-cart-id="' + cartId + '"].cart-summary__item').remove();
    }

    function recalc() {
        var total = 0;
        $('.cart-item').each(function () {
            total += parseFloat($(this).data('price')) * parseInt($(this).find('.qty-value').text());
        });
        $('#summary-total, #summary-final').text('₽ ' + fmt(total));
    }

    function updateCount() {
        var n = $('.cart-item').length;
        $('.cart-title__count').text('(' + n + ')');
        if (n === 0) setTimeout(function(){ location.reload(); }, 400);
    }

    function updateCartBadge(count) {
        count > 0 ? $('#cart-count').text(count).show() : $('#cart-count').hide();
    }

    function fmt(n) {
        return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
});
</script>

<?php include ROOT . '/includes/footer.php'; ?>