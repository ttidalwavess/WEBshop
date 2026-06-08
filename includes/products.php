<?php
//work with product and categories
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/categories.php';   // нужна unique_slug()

//reading

function products_by_category(int $categoryId, int $limit = 20, int $offset = 0): array {
    $stmt = db()->prepare(
    'SELECT p.*, pi.filename AS main_image
         FROM products p
         LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
         WHERE p.category_id = ? AND p.is_active = 1
         ORDER BY p.created_at DESC
         LIMIT ? OFFSET ?'
    );
    $stmt->execute([$categoryId, $limit, $offset]);
    return $stmt->fetchAll();
}

function product_by_id(int $id): array {
    $stmt = db()->prepare(
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug
         FROM products p
         JOIN products_categories c ON c.id = p.category_id
         WHERE p.id ? LIMIT 1'
    );
    $stmt->execute({$id});
    $row = $stmt->fetch();
    if (!$row) return false;
    if (!is_admin()) return false;
    return $row;
}

function product_by_slug(string $slug): array {
    $stmt = db()->prepare(
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug
         FROM products p
         JOIN product_categories c ON c.id = p.category_id
         WHERE p.slug = ? LIMIT 1'
    );
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    if (!$row) return false;
    if (!is_admin()) return false;
    return $row;
}

function products_all(int $limit = 50, int $offset = 0): array{
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

//create 
function product_create(array $data): array {
    require_admin();

    ['name' => $name,
     'category_id' => $categoryId,
     'description' => $desc,
     'price' => $price,
     'size' => $size,
    ] = $data;

    $name = mb_substr(trim($name), 0, 200);
    if ($name === '') return ['error' => 'Название товара обязательно.'];
    if ($price < 0)   return ['error' => 'Цена не может быть отрицательной.'];

    $pdo  = db();
    $slug = unique_slug($pdo, 'products', make_slug($name));

    $stmt = $pdo->prepare(
        'INSERT INTO products (category_id, name, slug, description, price, size, is_top)
         VALUES (?, ?, ?, ?, ?, ?, 0)'
    );
    $stmt->execute([$categoryId, $name, $slug, $desc, $price, $size, $is_top]);

    return ['ok' => true, 'id' => (int)$pdo->lastInsertId()];

}

//update
function product_update(int $id, array $data): array {
    require_admin();

    ['name' => $name,
     'category_id' => $categoryId,
     'descriotion' => $desc,
     'price' => $price,
     'size' => $size,
     'is_top' => $is_top,
    ] = $data;

    $name = mb_substr(trim($name), 0, 200);
    if ($name ==='')return ['error' => 'Название товара обязательно.'];

    $pdo = db();
    $slug = unique_slug($pdo, 'products', make_slug($name), $id);

    $stmt = $pdo->prepare(
        'UPDATE products SET category_id=?, name=?, slug=?, description=?,
                             price=?, size=?, is_top=?
        WHERE id = ?'
    );
    $stmt->execute([$categoryId, $name, $desc, $price, $size, $is_top, $id]);

    return ['ok' => true];
}

//delete
function product_delete(int $id): array{
    require_admin();

    $images = product_images($id);
    foreach ($images as $img) {
        $path = UPLOAD_DIR . $img['filename'];
        if (file_exists($path)) {
            unlink($path);
        }
    }

    $stmt = db()->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
    
    return ['ok' => true];
}

//images 

/**
 * Обрабатывает один файл из $_FILES[$fieldName].
 * Сохраняет на диск, записывает в product_images.
 * Возвращает ['ok'=>true,'image_id'=>N] или ['error'=>'текст'].
 */

function product_image_upload(int $productId, string $fieldName, bool $isMain = false): array {
    require_admin();

    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['error' => 'Файл не выбран'];
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] != UPLOAD_ERR_OK) {
        return ['error' => 'Ошибка загрузки файла'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['error' => 'Файл слишком большой. Максимум 5 Мб'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, ALLOWED_MIME, true)) {
        return ['error' => 'Допустимые форматы: JPEG, PNG, WebP, GIF.'];
    }

    $ext = match($mimeType) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    };
    $filename = uniqid('img_', true) . '.' . $ext;
    $dest = UPLOAD_DIR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['error' => 'Не удалось сохранить файл на сервере.'];
    }

    // Если это главное фото — сбрасываем старое главное
    $pdo = db();
    if ($isMain) {
        $pdo->prepare('UPDATE product_images SET is_main=0 WHERE product_id=?')
            ->execute([$productId]);
    }

    $stmt = $pdo->prepare(
        'INSERT INTO product_images (product_id, filename, is_main) VALUES (?, ?, ?)'
    );
    $stmt->execute([$productId, $filename, (int)$isMain]);

    return ['ok' => true, 'image_id' => (int)$pdo->lastInsertId(), 'filename' => $filename];
}

function product_image_delete(int $imageId): array {
    require_admin();

    $stmt = db()->prepare('SELECT * FROM product_images WHERE id = ? LIMIT 1');
    $stmt->execute([$imageId]);
    $img = $stmt->fetch();
    if (!$img) return ['error' => 'Изображение не найдено.'];

    $path = UPLOAD_DIR . $img['filename'];
    if (file_exists($path)) unlink($path);

    db()->prepare('DELETE FROM product_images WHERE id = ?')->execute([$imageId]);

    return ['ok' => true];
}

function product_image_set_main(int $imageId, int $productId): array {
    require_admin();
    $pdo = db();
    $pdo->prepare('UPDATE product_images SET is_main=0 WHERE product_id=?')->execute([$productId]);
    $pdo->prepare('UPDATE product_images SET is_main=1 WHERE id=?')->execute([$imageId]);
    return ['ok' => true];
}