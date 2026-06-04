<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/security.php';

function auth_register(string $username, string $email, string $password): array {
        if (mb_strlen($username) < 3 || mb_strlen($username) > 50) {
        return ['error' => 'Имя пользователя: от 3 до 50 символов.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['error' => 'Неверный формат email.'];
    }
    if (mb_strlen($password) < 6) {
        return ['error' => 'Пароль должен быть не менее 6 символов.'];
    }

    $pdo = db();

    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        return ['error' => 'Пользователь с таким именем или email уже существует.'];
    }

    $hash = hash_password($password);
    $stmt = $pdo->prepare(
        'INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, "user")'
    );
    $stmt->execute([$username, $email, $hash]);

    return ['ok' => true, 'user_id' => (int)$pdo->lastInsertId()];
}

function auth_login(string $username, string $password): array {
    $pdo = db();

    $stmt = $pdo->prepare('SELECT id, password, role FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !verify_password($password, $user['password'])) {
        return ['error' => 'Неверное имя пользователя или пароль.'];
    }

    session_regenerate_id(true);

    $_SESSION['user_id']   = (int)$user['id'];
    $_SESSION['username']  = $username;
    $_SESSION['user_role'] = $user['role'];

    return ['ok' => true];
}

function auth_logout(): void {
    $_SESSION = [];
    session_destroy();
}
