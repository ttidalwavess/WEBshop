<?php
define('ROOT', __DIR__);
require_once ROOT . '/includes/security.php';

session_start_safe();

$page_title = 'LIGHT | О нас';
include ROOT . '/includes/header.php';
?>

<main class="about-page">

    <!-- Hero блок -->
    <section class="about-hero">
        <div class="about-hero__image">
            <img src="/img/auth-bg.png" alt="LIGHT Studio">
        </div>
        <div class="about-hero__content">
            <p class="about-hero__label">✦ Light Studio ✦</p>
            <h1 class="about-hero__title">О нас</h1>
            <p class="about-hero__text">
                LIGHT — это бренд женской одежды, созданный для тех, кто ценит
                лёгкость, качество и современную эстетику. Каждая вещь в нашей
                коллекции — результат внимания к деталям и любви к красоте.
            </p>
        </div>
    </section>

    <!-- Блоки с инфо -->
    <section class="about-blocks">

        <div class="about-block" id="delivery">
            <h2 class="about-block__title">Самовывоз</h2>
            <p class="about-block__text">
                Мы работаем только на самовывоз. После оформления заказа
                мы подготовим его в течение часа. Заберите заказ в удобное время.
            </p>
            <div class="about-block__info">
                <div class="about-block__info-row">
                    <span class="about-block__info-label">Адрес</span>
                    <span>Владивосток, ул. Аллилуева, 12А</span>
                </div>
                <div class="about-block__info-row">
                    <span class="about-block__info-label">Часы работы</span>
                    <span>Пн–Вс, 10:00 – 21:00</span>
                </div>
            </div>
        </div>

        <div class="about-block" id="return">
            <h2 class="about-block__title">Возврат</h2>
            <p class="about-block__text">
                Вы можете вернуть товар в течение 14 дней с момента получения.
                Товар должен быть в оригинальной упаковке, с бирками и без следов носки.
            </p>
        </div>

        <div class="about-block" id="contacts">
            <h2 class="about-block__title">Контакты</h2>
            <div class="about-block__info">
                <div class="about-block__info-row">
                    <span class="about-block__info-label">Телефон</span>
                    <span>+7 (495) 343-12-33</span>
                </div>
                <div class="about-block__info-row">
                    <span class="about-block__info-label">Телефон</span>
                    <span>+7 (495) 878-73-87</span>
                </div>
                <div class="about-block__info-row">
                    <span class="about-block__info-label">Instagram</span>
                    <span>@light.studio</span>
                </div>
                <div class="about-block__info-row">
                    <span class="about-block__info-label">Telegram</span>
                    <span>@lightstudio</span>
                </div>
            </div>
        </div>

    </section>

</main>

<?php include ROOT . '/includes/footer.php'; ?>