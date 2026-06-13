<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/categories.php';

function products_by_category(int $categoryId, int $limit = 40, int $offset = 0): array
{
    $stmt = db()->prepare(
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                pi.filename AS main_image
         FROM products p
         JOIN product_categories c ON c.id = p.category_id
         LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
         WHERE p.category_id = ? AND p.is_active = 1
         ORDER BY p.created_at DESC
         LIMIT ? OFFSET ?'
    );
    $stmt->execute([$categoryId, $limit, $offset]);
    return $stmt->fetchAll();
}

function products_by_nav(string $cat, int $limit = 60): array
{
    $pdo = db();

    if ($cat === 'new') {
        $stmt = $pdo->prepare(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    pi.filename AS main_image
             FROM products p
             JOIN product_categories c ON c.id = p.category_id
             LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
             WHERE p.is_new = 1
             ORDER BY p.created_at DESC LIMIT ?'
        );
        $stmt->execute([$limit]);

    } elseif ($cat === 'women') {
        $stmt = $pdo->prepare(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    pi.filename AS main_image
             FROM products p
             JOIN product_categories c ON c.id = p.category_id
             LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
             WHERE c.slug NOT IN ('sumki','ukrasheniya')
             ORDER BY c.sort_order ASC, p.created_at DESC LIMIT ?"
        );
        $stmt->execute([$limit]);

    } elseif ($cat === 'accessories') {
        $stmt = $pdo->prepare(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    pi.filename AS main_image
             FROM products p
             JOIN product_categories c ON c.id = p.category_id
             LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
             WHERE c.slug IN ('sumki','ukrasheniya')
             ORDER BY c.sort_order ASC, p.created_at DESC LIMIT ?"
        );
        $stmt->execute([$limit]);

    } else {
        $stmt = $pdo->prepare(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    pi.filename AS main_image
             FROM products p
             JOIN product_categories c ON c.id = p.category_id
             LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
             ORDER BY c.sort_order ASC, p.created_at DESC LIMIT ?'
        );
        $stmt->execute([$limit]);
    }
    return $stmt->fetchAll();
}

function products_new(int $limit = 12): array
{
    $stmt = db()->prepare(
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                pi.filename AS main_image
         FROM products p
         JOIN product_categories c ON c.id = p.category_id
         LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
         WHERE p.is_new = 1
         ORDER BY p.created_at DESC LIMIT ?'
    );
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function products_top(int $limit = 12): array
{
    $stmt = db()->prepare(
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                pi.filename AS main_image
         FROM products p
         JOIN product_categories c ON c.id = p.category_id
         LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
         WHERE p.is_top = 1
         ORDER BY p.name ASC LIMIT ?'
    );
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function product_by_id(int $id) 
{
    $stmt = db()->prepare(
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug
         FROM products p
         JOIN product_categories c ON c.id = p.category_id
         WHERE p.id = ? LIMIT 1'
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) return false;
    return $row;
}

function product_by_slug(string $slug) 
{
    $stmt = db()->prepare(
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug
         FROM products p
         JOIN product_categories c ON c.id = p.category_id
         WHERE p.slug = ? LIMIT 1'
    );
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    if (!$row) return false;
    return $row;
}

function product_images(int $productId): array
{
    $stmt = db()->prepare(
        'SELECT * FROM product_images
         WHERE product_id = ?
         ORDER BY is_main DESC, sort_order ASC'
    );
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

function product_image_filenames(int $productId): array
{
    $stmt = db()->prepare(
        'SELECT filename FROM product_images
         WHERE product_id = ?
         ORDER BY is_main DESC, sort_order ASC'
    );
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function product_main_image(int $productId): string
{
    $stmt = db()->prepare(
        'SELECT filename FROM product_images
         WHERE product_id = ? AND is_main = 1 LIMIT 1'
    );
    $stmt->execute([$productId]);
    return (string)($stmt->fetchColumn() ?: '');
}

function product_img_url(string $filename): string
{
    if ($filename === '') return '/img/placeholder.jpg';
    return UPLOAD_URL . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8');
}

function products_all(int $limit = 50, int $offset = 0): array
{
    $stmt = db()->prepare(
        'SELECT p.*, c.name AS category_name, pi.filename AS main_image
         FROM products p
         JOIN product_categories c ON c.id = p.category_id
         LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
         ORDER BY p.created_at DESC
         LIMIT ? OFFSET ?'
    );
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll();
}

function render_product_card(array $p): void
{
    $id = (int)$p['id'];
    $name = e($p['name']);
    $category = e($p['category_name']);
    $price = number_format((float)$p['price'], 0, '.', ' ');
    $imgSrc = product_img_url($p['main_image'] ?? '');
    ?>
    <a href="/product.php?id=<?= $id ?>" class="product-card">
        <div class="product-img">
            <img src="<?= $imgSrc ?>" alt="<?= $name ?>" loading="lazy">
        </div>
        <div class="product-info">
            <div class="product-category"><?= $category ?></div>
            <div class="product-title"><?= $name ?></div>
            <div class="product-price">&#8381; <?= $price ?></div>
            <button class="btn-outline btn-add-to-cart"
                data-id="<?= $id ?>"
                data-name="<?= $name ?>"
                data-size="<?= e($p['size'] ?? '') ?>"
                onclick="event.preventDefault()">В корзину</button>
        </div>
    </a>
    <?php
}

function render_carousel(string $title, array $products): void
{
    ?>
    <section class="products-section">
        <h2 class="section-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>
        <div class="carousel-wrapper">
            <button class="carousel-btn carousel-btn--prev" aria-label="Назад">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </button>
            <div class="carousel-track-outer">
                <div class="carousel-track">
                    <?php if (empty($products)): ?>
                        <p style="padding:2rem;opacity:0.5">Товары появятся совсем скоро</p>
                    <?php else: ?>
                        <?php foreach ($products as $p): render_product_card($p); endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <button class="carousel-btn carousel-btn--next" aria-label="Вперёд">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </button>
        </div>
    </section>
    <?php
}


function product_create(array $data): array
{
    require_admin();
    $name = mb_substr(trim($data['name'] ?? ''), 0, 200);
    $categoryId = (int)($data['category_id'] ?? 0);
    $desc = trim($data['description'] ?? '');
    $price = max(0.0, (float)($data['price'] ?? 0));
    $size = mb_substr(trim($data['size'] ?? 'Универсальный'), 0, 50);
    $isTop = !empty($data['is_top']) ? 1 : 0;
    $isNew = !empty($data['is_new']) ? 1 : 0;

    if ($name === '') return ['error' => 'Название товара обязательно.'];

    $pdo  = db();
    $slug = unique_slug($pdo, 'products', make_slug($name));

    $stmt = $pdo->prepare(
        'INSERT INTO products
             (category_id, name, slug, description, price, size, is_top, is_new)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$categoryId, $name, $slug, $desc, $price, $size, $isTop, $isNew]);

    return ['ok' => true, 'id' => (int)$pdo->lastInsertId()];
}

function product_update(int $id, array $data): array
{
    require_admin();
    $name = mb_substr(trim($data['name'] ?? ''), 0, 200);
    $categoryId = (int)($data['category_id'] ?? 0);
    $desc = trim($data['description'] ?? '');
    $price = max(0.0, (float)($data['price'] ?? 0));
    $size = mb_substr(trim($data['size'] ?? 'Универсальный'), 0, 50);
    $isTop = (int)($data['is_top'] ?? 0);
    $isNew = (int)($data['is_new'] ?? 0);

    if ($name === '') return ['error' => 'Название товара обязательно.'];

    $pdo  = db();
    $slug = unique_slug($pdo, 'products', make_slug($name), $id);

    $stmt = $pdo->prepare(
        'UPDATE products
         SET category_id=?, name=?, slug=?, description=?,
             price=?, size=?, is_top=?, is_new=?
         WHERE id=?'
    );
    $stmt->execute([$categoryId, $name, $slug, $desc, $price, $size, $isTop, $isNew, $id]);

    return ['ok' => true];
}

function product_delete(int $id): array
{
    require_admin();
    $images = product_images($id);
    foreach ($images as $img) {
        $path = UPLOAD_DIR . $img['filename'];
        if (file_exists($path)) unlink($path);
    }
    $stmt = db()->prepare('SELECT COUNT(*) FROM order_items WHERE product_id = ?');
    $stmt->execute([$id]);
    if ((int)$stmt->fetchColumn() > 0) {
        return ['error' => 'Нельзя удалить товар, который есть в заказах.'];
    }
    return ['ok' => true];
}

function product_image_upload(int $productId, string $fieldName, bool $isMain = false): array
{
    require_admin();
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['error' => 'Файл не выбран.'];
    }
    $file = $_FILES[$fieldName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Ошибка загрузки (код ' . $file['error'] . ').'];
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['error' => 'Файл слишком большой. Максимум 5 МБ.'];
    }

    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, ALLOWED_MIME, true)) {
        return ['error' => 'Допустимые форматы: JPEG, PNG, WebP, GIF.'];
    }

    switch ($mimeType) {
        case 'image/jpeg': $ext = 'jpg';  break;
        case 'image/png': $ext = 'png';  break;
        case 'image/webp': $ext = 'webp'; break;
        default: $ext = 'gif';  break;
    }

    $filename = uniqid('img_', true) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $filename)) {
        return ['error' => 'Не удалось сохранить файл.'];
    }

    $pdo = db();
    if ($isMain) {
        $pdo->prepare('UPDATE product_images SET is_main=0 WHERE product_id=?')->execute([$productId]);
    }
    $pdo->prepare('INSERT INTO product_images (product_id, filename, is_main) VALUES (?,?,?)')
        ->execute([$productId, $filename, (int)$isMain]);

    return ['ok' => true, 'image_id' => (int)$pdo->lastInsertId(), 'filename' => $filename];
}

function product_image_delete(int $imageId): array
{
    require_admin();
    $stmt = db()->prepare('SELECT * FROM product_images WHERE id=? LIMIT 1');
    $stmt->execute([$imageId]);
    $img = $stmt->fetch();
    if (!$img) return ['error' => 'Изображение не найдено.'];
    $path = UPLOAD_DIR . $img['filename'];
    if (file_exists($path)) unlink($path);
    db()->prepare('DELETE FROM product_images WHERE id=?')->execute([$imageId]);
    return ['ok' => true];
}

function product_image_set_main(int $imageId, int $productId): array
{
    require_admin();
    $pdo = db();
    $pdo->prepare('UPDATE product_images SET is_main=0 WHERE product_id=?')->execute([$productId]);
    $pdo->prepare('UPDATE product_images SET is_main=1 WHERE id=?')->execute([$imageId]);
    return ['ok' => true];
}