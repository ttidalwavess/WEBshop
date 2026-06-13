<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'shop_db');
define('DB_USER', 'root');        
define('DB_PASS', '');            
define('DB_CHARSET', 'utf8mb4');

define('UPLOAD_DIR', __DIR__ . '/../uploads/products/');
define('UPLOAD_URL', '/uploads/products/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_MIME', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            
            error_log($e->getMessage());
            die('Database connection error. Please try again later.');
        }
    }
    return $pdo;
}
