
<?php
?>
<header class="site-header">
    <div class="container">
        <a href="/index.php" class="site-header__logo">ShopName</a>

        <nav class="site-nav">
            <a href="/index.php">Каталог</a>
            <?php if (is_logged_in()): ?>
                <a href="/cart.php">Корзина</a>
                <a href="/orders.php">Мои заказы</a>
                <?php if (is_admin()): ?>
                    <a href="/admin/index.php">Админка</a>
                <?php endif; ?>
                <form method="post" action="/logout.php" style="display:inline">
                    <button type="submit" class="btn-link">Выйти (<?= e($_SESSION['username']) ?>)</button>
                </form>
            <?php else: ?>
                <a href="/login.php">Войти</a>
                <a href="/register.php">Регистрация</a>
            <?php endif; ?>
        </nav>

        <!-- Гамбургер для мобильных -->
        <button class="nav-toggle" id="navToggle" aria-label="Меню">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
