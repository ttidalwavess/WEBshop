<?php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

session_start_safe();
require_login('/login.php');

// Получаем данные пользователя из БД
$pdo  = db();
$stmt = $pdo->prepare('SELECT username, email, created_at FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$page_title = 'LIGHT | Мой профиль';
include ROOT . '/includes/header.php';
?>

<main class="auth-page">
    <div class="auth-image">
        <img src="/img/auth-bg.png" alt="LIGHT Studio">
    </div>

    <div class="auth-form-side">
        <div class="auth-box">
            <h1 class="auth-title">Мой профиль</h1>

            <div class="profile-field">
                <div class="profile-field__label">Имя пользователя</div>
                <div class="profile-field__value"><?= e($user['username'] ?? '') ?></div>
            </div>

            <div class="profile-field">
                <div class="profile-field__label">E-mail</div>
                <div class="profile-field__value"><?= e($user['email'] ?? '') ?></div>
            </div>

            <div class="profile-actions">
                <form method="post" action="/logout.php">
                    <button type="submit" class="auth-btn auth-btn--ghost">Выйти из аккаунта</button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include ROOT . '/includes/footer.php'; ?>