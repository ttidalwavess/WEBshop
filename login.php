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
        header('Location: ' . '/index.php');
        exit;
    }
}

$page_title = 'LIGHT | Вход';
include ROOT . '/includes/header.php';
?>
<main class="auth-page">
    <div class="auth-box">
        <h1>Вход</h1>
        <?php if ($error): ?>
            <div class="alert alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <form method="post" action="/login.php" novalidate>
            <label for="username">Имя пользователя</label>
            <input type="text" id="username" name="username"
                   value="<?= e($_POST['username'] ?? '') ?>"
                   required autocomplete="username">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password"
                   required autocomplete="current-password">
            <button type="submit" class="btn btn--primary btn--full">Войти</button>
        </form>
        <p class="auth-box__footer">Нет аккаунта? <a href="/register.php">Зарегистрироваться</a></p>
    </div>
</main>
<?php include ROOT . '/includes/footer.php'; ?>
