<?php
if (!defined('ADMIN_NAV_INCLUDED')) {
    define('ADMIN_NAV_INCLUDED', true);
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<header class="admin-topbar">
    <button class="admin-topbar__burger" id="sidebar-toggle" aria-label="Меню">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <span class="admin-topbar__logo">Панель</span>
</header>

<div class="admin-sidebar-overlay" id="sidebar-overlay"></div>

<aside class="admin-sidebar" id="admin-sidebar">
    <div class="admin-sidebar__logo">
        <a href="/admin/index.php">Панель</a>
    </div>
    <nav class="admin-nav">
        <a href="/admin/index.php"
           class="admin-nav__item <?= $currentPage === 'index.php' ? 'admin-nav__item--active' : '' ?>">
            Дашборд
        </a>
        <a href="/admin/categories.php"
           class="admin-nav__item <?= $currentPage === 'categories.php' ? 'admin-nav__item--active' : '' ?>">
            Категории
        </a>
        <a href="/admin/products.php"
           class="admin-nav__item <?= in_array($currentPage, ['products.php','product_edit.php']) ? 'admin-nav__item--active' : '' ?>">
            Товары
        </a>
        <a href="/admin/admin_orders.php"
           class="admin-nav__item <?= $currentPage === 'admin_orders.php' ? 'admin-nav__item--active' : '' ?>">
            Заказы
        </a>
    </nav>
    <div class="admin-sidebar__footer">
        <span><?= e($_SESSION['username'] ?? '') ?></span>
        <a href="/" class="btn btn--sm btn--ghost">На главную</a>
    </div>
</aside>