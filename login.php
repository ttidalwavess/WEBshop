<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/auth.php';

session_start_safe();

if (is_logged_in) {
    header('Location: /index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $user_name = input_str('username');
    $password = $_POST['password'] ?? '';

    $result = auth_login($user_name, $password);
    if (isset($result['error'])) {
        $error = $result['error'];
    } else {
        $redirect = is_admin() ? '/admin/index.php' : 'index.php';
        header('Location: ' .$redirect);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — Light</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<main class="auth-page">
    <div class="auth-box">
        <h1>Вход</h1>

        <?php if ($error): ?>
            <div class="alert alert--error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="/login.php" novalidate>
            <?= csrf_field() ?>

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

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
