<?php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';
require_once ROOT . '/includes/auth.php';

session_start_safe();
if (is_logged_in()) { header('Location: /index.php'); exit; }

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = auth_register(
        input_str('username'),
        input_str('email'),
        $_POST['password'] ?? ''
    );
    if (isset($result['error'])) {
        $error = $result['error'];
    } else {
        // Сразу логиним после регистрации
        auth_login(input_str('username'), $_POST['password'] ?? '');
        header('Location: /index.php'); exit;
    }
}

$page_title = 'LIGHT | Регистрация';
include ROOT . '/includes/header.php';
?>

<main class="auth-page">
    <div class="auth-image">
        <img src="auth-bg.png" alt="LIGHT Studio">
    </div>

    <div class="auth-form-side">
        <div class="auth-box">
            <h1 class="auth-title">Регистрация</h1>

            <?php if ($error): ?>
                <div class="alert alert--error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" action="/register.php" novalidate id="register-form">
                <div class="auth-field">
                    <label for="username">Имя пользователя</label>
                    <input type="text" id="username" name="username"
                           placeholder="Введите имя пользователя"
                           value="<?= e($_POST['username'] ?? '') ?>"
                           required minlength="3" maxlength="50" autocomplete="username">
                </div>
                <div class="auth-field">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email"
                           placeholder="Введите e-mail"
                           value="<?= e($_POST['email'] ?? '') ?>"
                           required autocomplete="email">
                </div>
                <div class="auth-field">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password"
                           placeholder="Введите пароль"
                           required minlength="6" autocomplete="new-password">
                </div>
                <button type="submit" class="auth-btn">Зарегистрироваться</button>
            </form>

            <p class="auth-footer">Есть аккаунт? <a href="/login.php">Войти</a></p>
        </div>
    </div>
</main>

<?php include ROOT . '/includes/footer.php'; ?>