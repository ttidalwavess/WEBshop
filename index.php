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
                Мы создаем одежду для женщин, которые ценят легкость, качество и современную эстетику. Каждая деталь в наших коллекциях рождается из любви к красоте.
            </p>
            <ul class="hero-for-list">
                <li>Комфорт в каждом движении</li>
                <li>Качество вместо трендов</li>
                <li>Свобода быть собой</li>
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