<?php
// checkout.php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';
require_once ROOT . '/includes/orders.php';

session_start_safe();

if (!is_logged_in()) {
    header('Location: /login_required.php');
    exit;
}

$pdo = db();
$error = '';
$success = false;

// Получаем товары из корзины для отображения
$stmt = $pdo->prepare("
    SELECT c.id AS cart_id, c.quantity, c.product_id, c.size,
           p.name, p.price,
           pi.filename AS img
    FROM cart c
    JOIN products p ON p.id = c.product_id
    LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

if (empty($items)) {
    header('Location: /cart.php');
    exit;
}

$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Обработка POST — оформление заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = input_str('name');
    $phone = input_str('phone');
    
    if (mb_strlen($name) < 2) {
        $error = 'Укажите имя получателя.';
    } elseif (mb_strlen($phone) < 7) {
        $error = 'Укажите номер телефона.';
    } else {
        // СОЗДАЁМ ЗАКАЗ из корзины
        $result = order_create_from_cart($_SESSION['user_id'], $name, $phone);
        
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            $success = true;
            $_SESSION['last_order_id'] = $result['order_id'];
        }
    }
}

$page_title = 'LIGHT | Оформление заказа';
include ROOT . '/includes/header.php';
?>

<main class="checkout-page">
    <div class="checkout-layout">
        <?php if ($success): ?>
            <div class="checkout-success">
                <div class="empty-state__icon" style="margin:0 auto 1.5rem">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <h1 class="checkout-success__title">Заказ оформлен!</h1>
                <p class="checkout-success__text">
                    Спасибо за заказ! Мы свяжемся с вами в ближайшее время для подтверждения.<br>
                    Номер вашего заказа: <strong>№<?= $_SESSION['last_order_id'] ?? '' ?></strong>
                </p>
                <a href="/orders.php" class="empty-state__btn">Мои заказы</a>
                <a href="/index.php" class="checkout-success__link">Вернуться на главную</a>
            </div>
        <?php else: ?>
            <!-- Форма оформления -->
            <div class="checkout-left">
                <h1 class="checkout-title">Оформление заказа</h1>
                
                <?php if ($error): ?>
                    <div class="alert alert--error"><?= e($error) ?></div>
                <?php endif; ?>
                
                <form method="post" action="/checkout.php" id="checkout-form" novalidate>
                    <div class="checkout-section">
                        <h2 class="checkout-section__title">Данные получателя</h2>
                        
                        <div class="checkout-field">
                            <label for="name">Имя и фамилия</label>
                            <input type="text" id="name" name="name"
                                   placeholder="Иванова Анна"
                                   value="<?= e($_POST['name'] ?? '') ?>"
                                   required>
                        </div>
                        
                        <div class="checkout-field">
                            <label for="phone">Телефон</label>
                            <input type="tel" id="phone" name="phone"
                                   placeholder="+7 (900) 000-00-00"
                                   value="<?= e($_POST['phone'] ?? '') ?>"
                                   required>
                        </div>
                    </div>
                    
                    <div class="checkout-section">
                        <h2 class="checkout-section__title">Самовывоз</h2>
                        <div class="checkout-info-block">
                            <div class="checkout-info-block__row">
                                <span>Владивосток, ул. Аллилуева, 12А</span>
                            </div>
                            <div class="checkout-info-block__row">
                                <span>Оплата в магазине при получении</span>
                            </div>
                            <div class="checkout-info-block__row checkout-info-block__row--accent">
                                <span>Заказ будет готов в течение часа</span>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="checkout-submit checkout-submit--mobile">
                        Оформить заказ — ₽ <?= number_format($total, 0, '.', ' ') ?>
                    </button>
                </form>
            </div>
            
            <div class="checkout-right">
                <div class="cart-summary">
                    <h2 class="cart-summary__title">Ваш заказ</h2>
                    
                    <div class="cart-summary__items">
                        <?php foreach ($items as $item):
                            $img = !empty($item['img'])
                                ? UPLOAD_URL . htmlspecialchars($item['img'])
                                : '/img/placeholder.jpg';
                        ?>
                            <div class="checkout-item">
                                <div class="checkout-item__img">
                                    <img src="<?= $img ?>" alt="<?= e($item['name']) ?>">
                                </div>
                                <div class="checkout-item__info">
                                    <div class="checkout-item__name"><?= e($item['name']) ?></div>
                                    <?php if (!empty($item['size']) && $item['size'] !== 'Универсальный'): ?>
                                        <div class="checkout-item__meta">Размер: <?= e($item['size']) ?></div>
                                    <?php endif; ?>
                                    <div class="checkout-item__meta">× <?= (int)$item['quantity'] ?></div>
                                </div>
                                <div class="checkout-item__price">
                                    ₽ <?= number_format($item['price'] * $item['quantity'], 0, '.', ' ') ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-summary__row cart-summary__row--total">
                        <span>К оплате</span>
                        <span>₽ <?= number_format($total, 0, '.', ' ') ?></span>
                    </div>
                    
                    <button type="submit" form="checkout-form" class="cart-checkout-btn">
                        Оформить заказ
                    </button>
                    
                    <a href="/cart.php" class="checkout-back">Вернуться в корзину</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include ROOT . '/includes/footer.php'; ?>