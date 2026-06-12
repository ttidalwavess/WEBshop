<?php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';
require_once ROOT . '/includes/products.php';

session_start_safe();

$page_title  = 'LIGHT | Женская одежда';
$newProducts = products_new(12);
$topProducts = products_top(12);

include ROOT . '/includes/header.php';
?>

<main>

    <section class="hero-section">
        <div class="hero-image">
            <img src="/img/hero.jpg" alt="LIGHT — женская одежда">
        </div>
        <div class="hero-content">
            <h1 class="hero-title">LIGHT</h1>
            <p class="hero-brand-label">✦ Light Studio ✦</p>
            <p class="hero-description">
                Мы создаём одежду для женщин, которые ценят лёгкость и характер в каждой детали.
            </p>
            <ul class="hero-for-list">
                <li>Для воздушных образов и свободы</li>
                <li>Новые коллекции каждый сезон</li>
                <li>Доставка по всей России</li>
            </ul>
        </div>
    </section>

    <div class="banner-placeholder">
        <img src="/img/banner2.png" alt="Баннер 1">
    </div>

    <?php render_carousel('Новая коллекция', $newProducts); ?>

    <div class="banner-placeholder">
        <img src="/img/banner3.png" alt="Баннер 1">
    </div>

    <?php render_carousel('Топ продаж', $topProducts); ?>

</main>

<?php include ROOT . '/includes/footer.php'; ?>