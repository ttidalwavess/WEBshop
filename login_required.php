<?php
define('ROOT', __DIR__);
require_once ROOT . '/includes/security.php';

session_start_safe();

if (is_logged_in()) { header('Location: /index.php'); exit; }

$from = htmlspecialchars($_GET['from'] ?? '', ENT_QUOTES, 'UTF-8');

$page_title = 'LIGHT | Вход';
include ROOT . '/includes/header.php';
?>

<main class="auth-page">
    <div class="auth-image">
        <img src="/img/auth-bg.png" alt="LIGHT Studio">
    </div>

    <div class="auth-form-side">
        <div class="auth-box auth-box--center">

            <div class="auth-required-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>

            <h1 class="auth-title">Вы ещё не вошли<br>в аккаунт</h1>
            <p class="auth-required-text">
                Чтобы продолжить, войдите или создайте аккаунт — это займёт меньше минуты.
            </p>

            <a href="/login.php" class="auth-btn">Войти</a>
            <a href="/register.php" class="auth-btn auth-btn--outline">Создать аккаунт</a>

        </div>
    </div>
</main>

<?php include ROOT . '/includes/footer.php'; ?>