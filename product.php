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
$allImgs = product_image_filenames($id); // ['img_abc.jpg', 'img_def.png', ...]

$clothSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
$bagSizes   = ['Большой', 'Средний', 'Маленький'];
$curSize    = $product['size'] ?? '';

$page_title = 'LIGHT | ' . $product['name'];
$extra_js   = ['/js/product.js'];

include ROOT . '/includes/header.php';
?>

<main class="product-page">

    <nav class="breadcrumb">
        <a href="/index.php">Главная</a>
        <span>/</span>
        <a href="/catalog.php">Каталог</a>
        <span>/</span>
        <a href="/catalog.php?slug=<?= htmlspecialchars($product['category_slug'], ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </a>
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
                        доставка и возврат <span class="accordion-icon">+</span>
                    </button>
                    <div class="accordion-body">
                        <p>Доставка по России от 3 до 7 рабочих дней. Возврат в течение 14 дней.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</main>

<?php include ROOT . '/includes/footer.php'; ?>
