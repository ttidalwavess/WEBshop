<?php
define('ROOT', __DIR__);
require_once ROOT . '/includes/security.php';
require_once ROOT . '/includes/auth.php';

session_start_safe();
auth_logout();
header('Location: /index.php');
exit;
