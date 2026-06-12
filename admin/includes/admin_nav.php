<?php
// admin/includes/admin_nav.php — боковое меню администратора
// Этот файл include-ится внутри <body>, перед <main>
if (!defined('ADMIN_NAV_INCLUDED')) {
    define('ADMIN_NAV_INCLUDED', true);
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="admin-sidebar__logo">
        <a href="/admin/index.php">⚙ Панель</a>
    </div>
    <nav class="admin-nav">
        <a href="/admin/index.php"
           class="admin-nav__item <?= $currentPage === 'index.php' ? 'admin-nav__item--active' : '' ?>">
            📊 Дашборд
        </a>
        <a href="/admin/categories.php"
           class="admin-nav__item <?= $currentPage === 'categories.php' ? 'admin-nav__item--active' : '' ?>">
            🗂 Категории
        </a>
        <a href="/admin/products.php"
           class="admin-nav__item <?= in_array($currentPage, ['products.php','product_edit.php']) ? 'admin-nav__item--active' : '' ?>">
            📦 Товары
        </a>
        <a href="/admin/admin_orders.php"
           class="admin-nav__item <?= $currentPage === 'admin_orders.php' ? 'admin-nav__item--active' : '' ?>">
            🛒 Заказы (live)
        </a>
    </nav>
    <div class="admin-sidebar__footer">
        <span><?= e($_SESSION['username'] ?? '') ?></span>
        <a href="/index.php" class="btn btn--sm" 
            style="background:#E7F0F7;color:#330000;border:none">
            На главную
        </a>
    </div>
</aside>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>