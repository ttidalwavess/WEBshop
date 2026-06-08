<?php
//регистрация. сделать направление с корзины по ссылке Регистрация
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/auth.php';

session_start_safe();

if (is_logged_in()) {
    header('Location: /index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $user_name = input_str('username');
    $email = input_str('email');
    $password = $_POST['password'] ?? '';

    $result = auth_register($user_name, $email, $password);
    if (isset($result['error'])){
        $error = $result['error'];
    } else {
        $success = 'Регистрация прошла успешно! <a href = "/login.php">Войти</a>';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация — ShopName</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<main class="auth-page">
    <div class="auth-box">
        <h1>Регистрация</h1>

        <?php if ($error): ?>
            <div class="alert alert--error"><?= e($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert--success"><?= $success /* содержит безопасный HTML */ ?></div>
        <?php endif; ?>

        <form method="post" action="/register.php" novalidate>
            <label for="username">Имя пользователя</label>
            <input type="text" id="username" name="username"
                   value="<?= e($_POST['username'] ?? '') ?>"
                   required minlength="3" maxlength="50" autocomplete="username">

            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="<?= e($_POST['email'] ?? '') ?>"
                   required autocomplete="email">

            <label for="password">Пароль</label>
            <input type="password" id="password" name="password"
                   required minlength="6" autocomplete="new-password">

            <button type="submit" class="btn btn--primary btn--full">Зарегистрироваться</button>
        </form>

        <p class="auth-box__footer">Уже есть аккаунт? <a href="/login.php">Войти</a></p>
    </div>
</main>
</body>
</html>
