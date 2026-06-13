<?php
if (!defined('ROOT')) die('Direct access forbidden');
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

$cart_count = 0;
if (is_logged_in()) {
    $stmt = db()->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = (int)$stmt->fetchColumn();
}

$current     = basename($_SERVER['PHP_SELF']);
$current_cat = $_GET['cat'] ?? '';
$current_sale = isset($_GET['sale']);
?>

<!DOCTYPE html>
<html lang="ru" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=devise-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="user-logged-in" content="<?= is_logged_in() ? '1' : '0' ?>">
    <title><?= e($page_title ?? 'LIGHT | Женская одежда') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <?php if (!empty($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?= e($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
<header class="header">
    <a href="/index.php" class="brand-name">Light</a>

    <nav class="nav-center">
        <a href="/catalog.php?cat=new"
           class="nav-link <?= ($current === 'catalog.php' && $current_cat === 'new') ? 'active' : '' ?>">
            Новинки
        </a>
        <span class="nav-divider"></span>
        <a href="/catalog.php?cat=women"
           class="nav-link <?= ($current === 'catalog.php' && $current_cat === 'women') ? 'active' : '' ?>">
            Одежда
        </a>
        <span class="nav-divider"></span>
        <a href="/catalog.php?cat=accessories"
           class="nav-link <?= ($current === 'catalog.php' && $current_cat === 'accessories') ? 'active' : '' ?>">
            Аксессуары
        </a>
        <span class="nav-divider"></span>
        <a href="/about.php"
           class="nav-link <?= ($current === 'about.php') ? 'active' : '' ?>">
            О нас
        </a>
    </nav>

        <div class="header-icons">
        <?php $orders_href = is_logged_in() ? '/orders.php' : '/login_required.php'; ?>
        <a href="<?= $orders_href ?>" class="icon-btn" aria-label="Мои заказы">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="2"/>
                <line x1="9" y1="12" x2="15" y2="12"/>
                <line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
        </a>

        <!-- Корзина -->
        <a href="/cart.php" class="icon-btn cart-icon-wrap" aria-label="Корзина" style="position:relative">
            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"/>
                <circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            <span class="cart-badge" id="cart-count"
                  style="<?= $cart_count === 0 ? 'display:none' : '' ?>">
                <?= $cart_count ?>
            </span>
        </a>

        <!-- Вход / Кабинет -->
        <?php if (is_logged_in()): ?>
            <a href="/account.php" class="icon-btn" aria-label="Личный кабинет">
                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </a>
        <?php else: ?>
            <a href="/login.php" class="icon-btn" aria-label="Войти">
                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </a>
        <?php endif; ?>

        <?php if (is_admin()): ?>
            <a href="/admin/index.php" class="icon-btn" aria-label="Админка">
                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </a>
        <?php endif; ?>
    </div>
    <button class="nav-toggle" id="navToggle" aria-label="Меню"><span></span><span></span><span></span></button>
</header>
</body>