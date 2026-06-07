<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/products.php';

session_start_safe();
$slug    = input_str('slug', $_GET);
$product = product_by_slug($slug);
if (!$product) { http_response_code(404); die('Товар не найден'); }
$images  = product_images($product['id']);
?>
// ... HTML — двухколоночный макет галерея + описание