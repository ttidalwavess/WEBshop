#!/usr/bin/env php
<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/security.php';

$username = $argv[1] ?? 'admin';
$email    = $argv[2] ?? 'admin@shop.local';
$password = $argv[3] ?? 'admin123';

$hash = hash_password($password);

$pdo = db();

// Удаляем старого admin если есть
$pdo->prepare('DELETE FROM users WHERE username = ?')->execute([$username]);

$stmt = $pdo->prepare(
    'INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, "admin")'
);
$stmt->execute([$username, $email, $hash]);

echo "✓ Администратор создан:\n";
echo "  Логин:  $username\n";
echo "  Email:  $email\n";
echo "  Пароль: $password\n";
echo "  Hash:   $hash\n\n";
echo "Войди на: http://localhost/login.php\n";
