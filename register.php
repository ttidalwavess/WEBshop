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
        $success = 'Регистрация прошла успешно! <a href="/login.php">Войти</a>';
    }
}

$page_title = 'LIGHT | Регистрация';
include ROOT . '/includes/header.php';
?>
<main class="auth-page">
    <div class="auth-box">
        <h1>Регистрация</h1>
        <?php if ($error): ?>
            <div class="alert alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert--success"><?= $success ?></div>
        <?php endif; ?>
        <form method="post" action="/register.php" novalidate>
            <label for="username">Имя пользователя</label>
            <input type="text" id="username" name="username"
                   value="<?= e($_POST['username'] ?? '') ?>"
                   required minlength="3" maxlength="50" autocomplete="username">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="<?= e($_POST['username'] ?? '') ?>"
                   required autocomplete="email">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password"
                   required minlength="6" autocomplete="new-password">
            <button type="submit" class="btn btn--primary btn--full">Зарегистрироваться</button>
        </form>
        <p class="auth-box__footer">Уже есть аккаунт? <a href="/login.php">Войти</a></p>
    </div>
</main>
<?php include ROOT . '/includes/footer.php'; ?>
