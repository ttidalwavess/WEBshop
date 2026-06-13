<?php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';
require_once ROOT . '/includes/categories.php';
require_once ROOT . '/includes/products.php';

session_start_safe();

$cat  = input_str('cat',  $_GET);
$slug = input_str('slug', $_GET);

$navTitles = [
    'new'         => 'Новинки',
    'women'       => 'Одежда',
    'accessories' => 'Аксессуары',
];

if ($slug !== '') {
    $category = category_by_slug($slug);
    if (!$category) {
        http_response_code(404);
        $page_title = 'LIGHT | 404';
        include ROOT . '/includes/header.php';
        echo '<main><div style="padding:4rem 6%;text-align:center">';
        echo '<h1>Категория не найдена</h1>';
        echo '<a href="/index.php">← На главную</a>';
        echo '</div></main>';
        include ROOT . '/includes/footer.php';
        exit;
    }
    $title    = $category['name'];
    $products = products_by_category((int)$category['id']);
    $from     = 'from_slug=' . urlencode($slug);
} else {
    $title    = $navTitles[$cat] ?? 'Каталог';
    $products = products_by_nav($cat);
    $from     = 'from_cat=' . urlencode($cat);
}

$page_title = 'LIGHT | ' . $title;
include ROOT . '/includes/header.php';
?>

<main>
    <div class="catalog-page">

        <nav class="breadcrumb">
            <a href="/index.php">Главная</a>
            <span>/</span>
            <span><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></span>
        </nav>

        <div class="catalog-header">
            <h1 class="catalog-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
            <span class="catalog-count"><?= count($products) ?> <?php
                $n = count($products);
                if ($n % 100 >= 11 && $n % 100 <= 19) echo 'товаров';
                elseif ($n % 10 === 1) echo 'товар';
                elseif ($n % 10 >= 2 && $n % 10 <= 4) echo 'товара';
                else echo 'товаров';
            ?></span>
        </div>

        <?php if (empty($products)): ?>
            <p class="catalog-empty">Товары появятся совсем скоро.</p>
        <?php else: ?>
            <div class="catalog-grid">
                <?php foreach ($products as $p): render_product_card($p); endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php include ROOT . '/includes/footer.php'; ?>