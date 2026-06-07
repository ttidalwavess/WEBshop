<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/categories.php';
require_once __DIR__ . '/includes/products.php';

session_start_safe();
$slug = input_str('slug', $_GET);
$category = category_by_slug($slug);
if (!$category) { http_response_code(404); die('Категория не найдена'); }
$products = products_by_category($category['id']);
?>
// ... HTML — сетка карточек товаров