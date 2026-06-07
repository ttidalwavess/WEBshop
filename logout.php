<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . 'includes/auth.php';

session_start_safe();
csrf_verify();
auth_logout();
header('Location: /index.php');
exit;
?>