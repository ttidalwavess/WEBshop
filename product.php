<?php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';
require_once ROOT . '/includes/products.php';

session_start_safe();

$id = input_int('id', $_GET);
$product = $id > 0 ? product_by_id($id) : false;

if (!$product) {
    http_response_code(404);
    $page_title = 'LIGHT | Не найдено';
    include ROOT . '/includes/header.php';
    echo '<main><div style="padding:4rem 2rem;text-align:center">';
    echo '<h1>Товар не найден</h1><a href="/catalog.php">← В каталог</a>';
    echo '</div></main>';
    include ROOT . '/includes/footer.php';
    exit;
}

$mainImgSrc = product_img_url(product_main_image($id));
$allImgs = product_image_filenames($id);

$clothSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
$bagSizes   = ['Большой', 'Средний', 'Маленький'];
$curSize    = $product['size'] ?? '';

// ── Хлебные крошки ──
// ── Хлебные крошки ──
$from_cat  = input_str('from_cat',  $_GET);
$from_slug = input_str('from_slug', $_GET);

$navTitles = [
    'new'         => 'Новинки',
    'women'       => 'Одежда',
    'accessories' => 'Аксессуары',
];

// Определяем раздел по slug категории товара если from не передан
$accessorySlugs = ['sumki', 'ukrasheniya'];

if ($from_slug !== '') {
    $from_url   = '/catalog.php?slug=' . urlencode($from_slug);
    $from_label = htmlspecialchars(
        category_by_slug($from_slug)['name'] ?? $from_slug,
        ENT_QUOTES, 'UTF-8'
    );
} elseif ($from_cat !== '') {
    $from_url   = '/catalog.php?cat=' . urlencode($from_cat);
    $from_label = htmlspecialchars($navTitles[$from_cat], ENT_QUOTES, 'UTF-8');
} else {
    // Fallback — определяем по категории самого товара
    $cat_slug = $product['category_slug'] ?? '';
    if (in_array($cat_slug, $accessorySlugs, true)) {
        $from_url   = '/catalog.php?cat=accessories';
        $from_label = 'Аксессуары';
    } else {
        $from_url   = '/catalog.php?cat=women';
        $from_label = 'Одежда';
    }
}

$category_label = htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8');

$page_title = 'LIGHT | ' . $product['name'];
$extra_js   = ['/js/product.js'];

include ROOT . '/includes/header.php';
?>

<main class="product-page">

    <nav class="breadcrumb">
        <a href="/index.php">Главная</a>
        <span>/</span>
        <a href="<?= $from_url ?>"><?= $from_label ?></a>
        <span>/</span>
        <span><?= $category_label ?></span>
    </nav>

    <div class="product-layout">

        <!-- ── ГАЛЕРЕЯ ── -->
        <div class="product-gallery">
            <div class="product-gallery__main">
                <img src="<?= $mainImgSrc ?>"
                     alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                     id="main-product-img">
            </div>

            <?php if (count($allImgs) > 1): ?>
            <div class="product-gallery__thumbs">
                <?php foreach ($allImgs as $i => $fname): ?>
                    <img src="<?= product_img_url($fname) ?>"
                         alt="Фото <?= $i + 1 ?>"
                         class="product-gallery__thumb <?= $i === 0 ? 'product-gallery__thumb--active' : '' ?>"
                         data-src="<?= product_img_url($fname) ?>">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- ── ИНФО ── -->
        <div class="product-details">

            <h1 class="product-details__name">
                <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <div class="product-details__price">
                &#8381; <?= number_format((float)$product['price'], 0, '.', ' ') ?>
            </div>

            <!-- Размеры -->
            <div class="product-sizes">
                <div class="product-sizes__label">размер</div>
                <div class="product-sizes__grid">
                    <?php if (in_array($curSize, $clothSizes, true)): ?>
                        <?php foreach ($clothSizes as $sz): ?>
                            <button class="size-btn <?= $sz === $curSize ? 'active' : '' ?>"
                                    data-size="<?= $sz ?>"><?= $sz ?></button>
                        <?php endforeach; ?>
                    <?php elseif (in_array($curSize, $bagSizes, true)): ?>
                        <?php foreach ($bagSizes as $sz): ?>
                            <button class="size-btn <?= $sz === $curSize ? 'active' : '' ?>"
                                    data-size="<?= $sz ?>"><?= $sz ?></button>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="size-universal">Универсальный</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Кнопки -->
            <div class="product-details__actions">
                <?php if (is_logged_in()): ?>
                    <button class="btn-add-cart btn-add-to-cart"
                            data-id="<?= (int)$product['id'] ?>"
                            data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
                        В корзину
                    </button>
                <?php else: ?>
                    <a href="/login.php" class="btn-add-cart">Войдите, чтобы купить</a>
                <?php endif; ?>
            </div>

            <!-- Аккордеон -->
            <div class="accordion">
                <div class="accordion-item">
                    <button class="accordion-trigger">
                        описание <span class="accordion-icon">+</span>
                    </button>
                    <div class="accordion-body">
                        <p><?= htmlspecialchars($product['description'] ?? 'Описание отсутствует.', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-trigger">
                        уход за товаром<span class="accordion-icon">+</span>
                    </button>
                    <div class="accordion-body">
                        <p>Чтобы изделие радовало вас как можно дольше, соблюдайте базовые правила ухода:
                            берегите его от прямых солнечных лучей и источников тепла,
                            избегайте контакта с агрессивными жидкостями и парфюмом.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</main>

<?php include ROOT . '/includes/footer.php'; ?>