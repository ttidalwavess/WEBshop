<?php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

session_start_safe();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: /catalog.php');
    exit;
}

$pdo = db();

// Получаем товар
$stmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name, c.slug AS category_slug
    FROM products p
    LEFT JOIN product_categories c ON c.id = p.category_id
    WHERE p.id = ? AND p.is_active = 1
    LIMIT 1
");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    die('Товар не найден.');
}

// Получаем главное фото
$stmt = $pdo->prepare("
    SELECT filename FROM product_images
    WHERE product_id = ? AND is_main = 1
    LIMIT 1
");
$stmt->execute([$id]);
$main_img = $stmt->fetchColumn();

// Все фото (для будущей галереи)
$stmt = $pdo->prepare("
    SELECT filename FROM product_images
    WHERE product_id = ?
    ORDER BY sort_order, id
");
$stmt->execute([$id]);
$all_imgs = $stmt->fetchAll(PDO::FETCH_COLUMN);

$img_src = $main_img
    ? UPLOAD_URL . htmlspecialchars($main_img)
    : '/img/placeholder.jpg';

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
        <a href="/catalog.php?cat=<?= e($product['category_slug'] ?? '') ?>">
            <?= e($product['category_name'] ?? '') ?>
        </a>
    </nav>

    <div class="product-layout">

        <!-- Фото -->
        <div class="product-gallery">
            <div class="product-gallery__main">
                <img
                    src="<?= $img_src ?>"
                    alt="<?= e($product['name']) ?>"
                    id="main-product-img"
                >
            </div>
        </div>

        <!-- Инфо -->
        <div class="product-details">

            <h1 class="product-details__name"><?= e($product['name']) ?></h1>

            <div class="product-details__price">
                &#8381; <?= number_format((float)$product['price'], 0, '.', ' ') ?>
            </div>

            <!-- Размеры — TODO: добавить таблицу sizes в БД (Роль 1) -->
            <div class="product-sizes">
                <div class="product-sizes__label">размер</div>
                <div class="product-sizes__grid">
                    <?php foreach (['XS','S','M','L','XL'] as $size): ?>
                        <button class="size-btn" data-size="<?= $size ?>"><?= $size ?></button>
                    <?php endforeach; ?>
                </div>
                <a href="#" class="product-sizes__table-link">таблица размеров</a>
            </div>

            <!-- Кнопки -->
            <div class="product-details__actions">
                <?php if (is_logged_in()): ?>
                    <button class="btn-add-cart btn-add-to-cart"
                            data-id="<?= $product['id'] ?>"
                            data-name="<?= e($product['name'], ENT_QUOTES) ?>">
                        В корзину
                    </button>
                <?php else: ?>
                    <a href="/login.php" class="btn-add-cart">
                        Войдите, чтобы купить
                    </a>
                <?php endif; ?>
                <button class="btn-wishlist" aria-label="В избранное">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </button>
            </div>

            <!-- Аккордеон -->
            <div class="accordion">
                <div class="accordion-item">
                    <button class="accordion-trigger">
                        описание <span class="accordion-icon">+</span>
                    </button>
                    <div class="accordion-body">
                        <p><?= e($product['description'] ?? 'Описание отсутствует.') ?></p>
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