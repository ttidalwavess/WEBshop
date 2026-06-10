<?php
function session_start_safe(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => false,   
            'httponly' => true,    
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**строка из $_POST / $_GET */
function input_str(string $key, array $source = []): string {
    $source = $source ?: $_POST;
    $val = $source[$key] ?? '';
    return trim(strip_tags((string)$val));
}

/** Целое из $_POST / $_GET */
function input_int(string $key, array $source = []): int {
    $source = $source ?: $_POST;
    return (int)($source[$key] ?? 0);
}

//тлько +число
function input_float(string $key, array $source = []): float {
    $source = $source ?: $_POST;
    $val = str_replace(',', '.', (string)($source[$key] ?? '0'));
    return max(0.0, (float)$val);
}


function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function is_admin(): bool {
    return is_logged_in() && ($_SESSION['user_role'] ?? '') === 'admin';
}

//проверка регистрации
function require_login(string $redirect = '/login.php'): void {
    if (!is_logged_in()) {
        header('Location: ' . $redirect);
        exit;
    }
}

//проверяет админство
function require_admin(): void {
    if (!is_admin()) {
        http_response_code(403);
        die('Access denied.');
    }
}

// хэш паролей

function hash_password(string $plain): string {
    return password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verify_password(string $plain, string $hash): bool {
    return password_verify($plain, $hash);
}

function make_slug(string $str): string {
    $trans = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo',
        'ж'=>'zh','з'=>'z','и'=>'i','й'=>'j','к'=>'k','л'=>'l','м'=>'m',
        'н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u',
        'ф'=>'f','х'=>'h','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch',
        'ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
    ];
    $str = mb_strtolower($str, 'UTF-8');
    $str = strtr($str, $trans);
    $str = preg_replace('/[^a-z0-9]+/', '-', $str);
    return trim($str, '-');
}
