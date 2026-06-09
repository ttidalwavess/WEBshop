<?php
define('ROOT', __DIR__);
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

session_start_safe();

$page_title = 'LIGHT | Женская одежда';

// Получаем новинки и топ продаж из БД
// Новинки — последние добавленные активные товары
function get_products_with_image(PDO $pdo, string $order, int $limit = 8): array {
    $stmt = $pdo->prepare("
        SELECT
            p.id,
            p.name,
            p.slug,
            p.price,
            c.name AS category_name,
            pi.filename AS img
        FROM products p
        LEFT JOIN product_categories c ON c.id = p.category_id
        LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
        WHERE p.is_active = 1
        ORDER BY {$order}
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

$pdo = db();
$new_products = get_products_with_image($pdo, 'p.created_at DESC');
$top_products = get_products_with_image($pdo, 'p.id ASC'); // TODO: заменить на рейтинг/продажи

// Функция рендера карточки
function render_product_card(array $p): void {
    $id       = (int)$p['id'];
    $name     = htmlspecialchars($p['name']);
    $nameAttr = htmlspecialchars($p['name'], ENT_QUOTES);
    $category = htmlspecialchars($p['category_name'] ?? '');
    $price    = number_format((float)$p['price'], 0, '.', ' ');
    $img      = !empty($p['img'])
        ? UPLOAD_URL . htmlspecialchars($p['img'])
        : '/img/placeholder.jpg'; // заглушка если нет фото

    echo '<a href="/product.php?id=' . $id . '" class="product-card">';
    echo '<div class="product-img"><img src="' . $img . '" alt="' . $name . '" loading="lazy"></div>';
    echo '<div class="product-info">';
    echo '<div class="product-category">' . $category . '</div>';
    echo '<div class="product-title">' . $name . '</div>';
    echo '<div class="product-price">&#8381; ' . $price . '</div>';
    echo '<button class="btn-outline btn-add-to-cart" data-id="' . $id . '" data-name="' . $nameAttr . '" onclick="event.preventDefault()">В корзину</button>';
    echo '</div>';
    echo '</a>';
}

include ROOT . '/includes/header.php';
?>

<main>

    <!-- HERO -->
    <section class="hero-section">
        <div class="hero-image">
            <img src="/img/hero.jpg" alt="LIGHT — женская одежда">
        </div>
        <div class="hero-content">
            <h1 class="hero-title">LIGHT</h1>
            <p class="hero-brand-label">✦ Light Studio ✦</p>
            <p class="hero-description">
                Мы создаём одежду для женщин, которые ценят лёгкость и характер в каждой детали.
                LIGHT — это не просто бренд, а ощущение: когда силуэт безупречен,
                ткань дышит, а образ говорит сам за себя.
            </p>
            <ul class="hero-for-list">
                <li>Новые коллекции каждый сезон</li>
                <li>Доставка по всей России</li>
                <li>Возврат в течение 14 дней</li>
            </ul>
        </div>
    </section>

    <!-- РАЗДЕЛИТЕЛЬ 1 -->
    <div class="banner-placeholder" aria-hidden="true"></div>

    <!-- НОВАЯ КОЛЛЕКЦИЯ -->
    <section class="products-section">
        <h2 class="section-title">Новая коллекция</h2>
        <div class="carousel-wrapper">
            <button class="carousel-btn carousel-btn--prev" aria-label="Назад">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </button>
            <div class="carousel-track-outer">
                <div class="carousel-track">
                    <?php if (empty($new_products)): ?>
                        <p style="padding:2rem;opacity:0.5">Товары появятся совсем скоро</p>
                    <?php else: ?>
                        <?php foreach ($new_products as $p): ?>
                            <?php render_product_card($p); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <button class="carousel-btn carousel-btn--next" aria-label="Вперёд">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </button>
        </div>
    </section>

    <!-- РАЗДЕЛИТЕЛЬ 2 -->
    <div class="banner-placeholder" aria-hidden="true"></div>

    <!-- ТОП ПРОДАЖ -->
    <section class="products-section">
        <h2 class="section-title">Топ продаж</h2>
        <div class="carousel-wrapper">
            <button class="carousel-btn carousel-btn--prev" aria-label="Назад">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </button>
            <div class="carousel-track-outer">
                <div class="carousel-track">
                    <?php if (empty($top_products)): ?>
                        <p style="padding:2rem;opacity:0.5">Товары появятся совсем скоро</p>
                    <?php else: ?>
                        <?php foreach ($top_products as $p): ?>
                            <?php render_product_card($p); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <button class="carousel-btn carousel-btn--next" aria-label="Вперёд">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </button>
        </div>
    </section>

</main>

<?php include ROOT . '/includes/footer.php'; ?>