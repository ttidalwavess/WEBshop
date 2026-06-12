<?php
define('ROOT_DIR', dirname(__DIR__));
define('INCLUDES_DIR', ROOT_DIR . '/includes/');
define('UPLOAD_DIR', ROOT_DIR . '/uploads/products/');
define('UPLOAD_URL', '/uploads/products/');

define('ASSETS_URL', '/assets/');
define('ADMIN_ASSETS_URL', '/admin/assets/');

define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_MIME', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);