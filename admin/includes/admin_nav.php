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
        <a href="/admin/orders.php"
           class="admin-nav__item <?= $currentPage === 'orders.php' ? 'admin-nav__item--active' : '' ?>">
            🛒 Заказы (live)
        </a>
    </nav>
    <div class="admin-sidebar__footer">
        <span><?= e($_SESSION['username'] ?? '') ?></span>
        <form method="post" action="/index.php" style="display:inline">
            <button type="submit" class="btn btn--sm btn--ghost">Выйти</button>
        </form>
    </div>
</aside>
