<?php
define('ROOT', __DIR__);
require_once ROOT . '/includes/security.php';

session_start_safe();

http_response_code(404);
$page_title = 'LIGHT | Страница не найдена';
include ROOT . '/includes/header.php';
?>

<main class="auth-page">
    <div class="auth-image">
        <img src="/images/auth-bg.png" alt="LIGHT Studio">
        <div class="auth-image__logo">
            <div class="auth-image__brand">LIGHT</div>
            <div class="auth-image__sub">STUDIO</div>
        </div>
    </div>

    <div class="auth-form-side">
        <div class="auth-box auth-box--center">

            <div class="auth-required-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>

            <h1 class="auth-title">404</h1>
            <p class="auth-required-text">
                Страница не найдена. Возможно, она была удалена или вы ввели неверный адрес.
            </p>

            <a href="/index.php" class="auth-btn">На главную</a>
            <a href="/catalog.php" class="auth-btn auth-btn--outline">В каталог</a>

        </div>
    </div>
</main>

<?php include ROOT . '/includes/footer.php'; ?>