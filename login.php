<?php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';
require_once ROOT . '/includes/auth.php';

session_start_safe();
if (is_logged_in()) { header('Location: /index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = auth_login(input_str('username'), $_POST['password'] ?? '');
    if (isset($result['error'])) {
        $error = $result['error'];
    } else {
        header('Location: /index.php'); exit;
    }
}

$page_title = 'LIGHT | Вход';
include ROOT . '/includes/header.php';
?>

<main class="auth-page">
    <div class="auth-image">
        <img src="/images/auth-bg.png" alt="LIGHT Studio">
    </div>

    <div class="auth-form-side">
        <div class="auth-box">
            <h1 class="auth-title">Вход в аккаунт</h1>

            <?php if ($error): ?>
                <div class="alert alert--error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" action="/login.php" novalidate id="login-form">
                <div class="auth-field">
                    <label for="username">Имя пользователя</label>
                    <input type="text" id="username" name="username"
                           placeholder="Введите имя пользователя"
                           value="<?= e($_POST['username'] ?? '') ?>"
                           required autocomplete="username">
                </div>
                <div class="auth-field">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password"
                           placeholder="Введите пароль"
                           required autocomplete="current-password">
                </div>
                <button type="submit" class="auth-btn">Войти</button>
            </form>

            <p class="auth-footer">Нет аккаунта? <a href="/register.php">Зарегистрируйтесь</a></p>
        </div>
    </div>
</main>

<?php include ROOT . '/includes/footer.php'; ?>