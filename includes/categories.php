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

function categories_accessories(): array {
    $stmt = db()->prepare(
        "SELECT id, name, slug, sort_order
         FROM product_categories
         WHERE slug IN ('sumki', 'ukrasheniya')
         ORDER BY sort_order, name"
    );
    $stmt->execute();
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

function category_create(string $name, int $sort_order): array {
    require_admin();
    $name = mb_substr(trim($name), 0, 100);
    if ($name === '') {
        return ['error' => 'Название категории не может быть пустым.'];
    }

    $pdo = db();
    
    if ($sort_order > 0) {
        $stmt = $pdo->prepare("UPDATE product_categories SET sort_order = sort_order + 1 WHERE sort_order >= ?");
        $stmt->execute([$sort_order]);
    }
    
    if ($sort_order <= 0) {
        $stmt = $pdo->query("SELECT MAX(sort_order) as max_order FROM product_categories");
        $maxOrder = (int)$stmt->fetch()['max_order'];
        $sort_order = $maxOrder + 1;
    }

    $slug = make_slug($name);
    $slug = unique_slug($pdo, 'product_categories', $slug);

    $stmt = $pdo->prepare(
        'INSERT INTO product_categories (name, slug, sort_order) VALUES (?, ?, ?)'
    );
    $stmt->execute([$name, $slug, $sort_order]);
    return ['ok' => true, 'id' => (int)$pdo->lastInsertId()];
}

function category_update(int $id, string $name, int $sort_order): array {
    require_admin();
    $name = mb_substr(trim($name), 0, 100);
    if ($name === '') {
        return ['error' => 'Название не может быть пустым.'];
    }

    $pdo = db();
    
    $stmt = $pdo->prepare("SELECT sort_order FROM product_categories WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetch();
    
    if (!$current) {
        return ['error' => 'Категория не найдена.'];
    }
    
    $old_order = (int)$current['sort_order'];
    $new_order = $sort_order;
    
    if ($old_order !== $new_order) {
        if ($new_order > $old_order) {
            $stmt = $pdo->prepare("
                UPDATE product_categories 
                SET sort_order = sort_order - 1 
                WHERE sort_order > ? AND sort_order <= ?
            ");
            $stmt->execute([$old_order, $new_order]);
        } elseif ($new_order < $old_order) {
            $stmt = $pdo->prepare("
                UPDATE product_categories 
                SET sort_order = sort_order + 1 
                WHERE sort_order >= ? AND sort_order < ?
            ");
            $stmt->execute([$new_order, $old_order]);
        }
    }

    $slug = make_slug($name);
    $slug = unique_slug($pdo, 'product_categories', $slug, $id);

    $stmt = $pdo->prepare(
        'UPDATE product_categories SET name=?, slug=?, sort_order=? WHERE id=?'
    );
    $stmt->execute([$name, $slug, $new_order, $id]);
    return ['ok' => true];
}

function category_delete(int $id): array {
    require_admin();
    
    $pdo = db();
    
    // Получаем sort_order удаляемой категории
    $stmt = $pdo->prepare("SELECT sort_order FROM product_categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch();
    
    if (!$category) {
        return ['error' => 'Категория не найдена.'];
    }
    
    // Проверяем, есть ли товары в категории
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$id]);
    if ((int)$stmt->fetchColumn() > 0) {
        return ['error' => 'Нельзя удалить категорию, в которой есть товары.'];
    }
    
    $deleted_order = (int)$category['sort_order'];
    
    // Удаляем категорию
    $stmt = $pdo->prepare("DELETE FROM product_categories WHERE id = ?");
    $stmt->execute([$id]);
    
    // Сдвигаем все категории с sort_order больше удалённой на 1 вверх
    $stmt = $pdo->prepare("UPDATE product_categories SET sort_order = sort_order - 1 WHERE sort_order > ?");
    $stmt->execute([$deleted_order]);

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
