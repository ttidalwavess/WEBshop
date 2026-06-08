<?php
// работа с группами товаров
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/security.php';

//чтение
function categories_all(): array {
    $stmt = db()->query(
        'SELECT id, name, slug, sort_order FROM product_categories ORDER BY sort_order, name'
    );
    return $stmt->fetchAll();
}

function category_by_id(int $id) {
    $stmt = db()->prepare('SELECT * FROM product_categories WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function category_by_slug(string $slug) {
    $stmt = db()->prepare('SELECT * FROM product_categories WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function category_create(string $name, string $description, int $sort_order, int $is_accessory): array {
    require_admin();

    $name = mb_substr(trim($name), 0, 100);
    if ($name === '') {
        return ['error' => 'Название категории не может быть пустым.'];
    }

    $slug = make_slug($name);
    $pdo  = db();
    $slug = unique_slug($pdo, 'product_categories', $slug);

    $stmt = $pdo->prepare(
        'INSERT INTO product_categories (name, slug, description, sort_order, is_accessory) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$name, $slug, $description, $sort_order, $is_accessory]);

    return ['ok' => true, 'id' => (int)$pdo->lastInsertId()];
}

function category_update(int $id, string $name, string $description, int $sort_order, int $is_accessory): array {
    require_admin();

    $name = mb_substr(trim($name), 0, 100);
    if ($name === '') {
        return ['error' => 'Название не может быть пустым.'];
    }

    $pdo  = db();
    $slug = make_slug($name);
    $slug = unique_slug($pdo, 'product_categories', $slug, $id);

    $stmt = $pdo->prepare(
        'UPDATE product_categories SET name=?, slug=?, description=?, sort_order=?, is_accessory = ? WHERE id=?'
    );
    $stmt->execute([$name, $slug, $description, $sort_order, $id, $is_accessory]);

    return ['ok' => true];
}

function category_delete(int $id): array {
    require_admin();

    // Проверяем, есть ли товары в категории
    $stmt = db()->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
    $stmt->execute([$id]);
    if ((int)$stmt->fetchColumn() > 0) {
        return ['error' => 'Нельзя удалить категорию, в которой есть товары.'];
    }

    $stmt = db()->prepare('DELETE FROM product_categories WHERE id = ?');
    $stmt->execute([$id]);

    return ['ok' => true];
}

/**
 * Если slug занят другой записью — добавляет -2, -3 и т.д.
 * $excludeId — текущая запись при редактировании (её slug пропускаем).
 */
function unique_slug(PDO $pdo, string $table, string $slug, int $excludeId = 0): string {
    $base = $slug;
    $suffix = 2;
    while (true) {
        $stmt = $pdo->prepare(
            "SELECT id FROM {$table} WHERE slug = ? AND id != ? LIMIT 1"
        );
        $stmt->execute([$slug, $excludeId]);
        if (!$stmt->fetch()) {
            break;
        }
        $slug = $base . '-' . $suffix++;
    }
    return $slug;
}
