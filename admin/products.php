<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/products.php';
require_once __DIR__ . '/../includes/categories.php';

session_start_safe();
require_admin();

$message = '';
$error   = '';
$editProduct = null;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $action = input_str('action');
    $id     = input_int('id');

    if ($action === 'create') {
        $result = product_create([
            'name'        => input_str('name'),
            'category_id' => input_int('category_id'),
            'description' => input_str('description'),
            'price'       => input_float('price'),
            'stock'       => input_int('stock'),
        ]);
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            if (!empty($_FILES['main_image']['name'])) {
                product_image_upload($result['id'], 'main_image', true);
            }
            $message = 'Товар создан. <a href="/admin/product_edit.php?id=' . $result['id'] . '">Редактировать</a>';
        }
    }

    if ($action === 'delete') {
        $result = product_delete($id);
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            $message = 'Товар удалён.';
        }
    }
}

$products   = products_all(50, (int)($_GET['offset'] ?? 0));
$categories = categories_all();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Товары — Админка</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-layout">
<?php include __DIR__ . '/includes/admin_nav.php'; ?>

<main class="admin-main">
    <div class="admin-header">
        <h1>Товары</h1>
    </div>

    <?php if ($message): ?>
        <div class="alert alert--success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert--error"><?= e($error) ?></div>
    <?php endif; ?>

    <!-- ФОРМА ДОБАВЛЕНИЯ ТОВАРА -->
    <section class="admin-card">
        <h2>Добавить товар</h2>
        <form method="post" action="/admin/products.php" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create">

            <div class="form-row">
                <div class="form-group">
                    <label for="name">Название *</label>
                    <input type="text" id="name" name="name" required maxlength="200">
                </div>
                <div class="form-group">
                    <label for="category_id">Категория *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">— выберите —</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>"><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Описание</label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group form-group--small">
                    <label for="price">Цена (₽) *</label>
                    <input type="number" id="price" name="price" required min="0" step="0.01">
                </div>
                <div class="form-group form-group--small">
                    <label for="stock">Кол-во на складе</label>
                    <input type="number" id="stock" name="stock" min="0" value="0">
                </div>
            </div>

            <div class="form-group">
                <label for="main_image">Главное фото (JPEG/PNG/WebP, до 5 МБ)</label>
                <input type="file" id="main_image" name="main_image" accept="image/*">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary">Создать товар</button>
            </div>
        </form>
    </section>

    <!-- СПИСОК ТОВАРОВ -->
    <section class="admin-card">
        <h2>Все товары (<?= count($products) ?>)</h2>
        <?php if (empty($products)): ?>
            <p class="empty-hint">Товаров пока нет.</p>
        <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Фото</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Цена</th>
                    <th>Склад</th>
                    <th>Активен</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr class="<?= $p['is_active'] ? '' : 'row--inactive' ?>">
                    <td><?= (int)$p['id'] ?></td>
                    <td>
                        <?php if ($p['main_image']): ?>
                            <img src="<?= UPLOAD_URL . e($p['main_image']) ?>"
                                 alt="" class="thumb">
                        <?php else: ?>
                            <span class="no-photo">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($p['name']) ?></td>
                    <td><?= e($p['category_name']) ?></td>
                    <td><?= number_format($p['price'], 2, '.', ' ') ?> ₽</td>
                    <td><?= (int)$p['stock'] ?></td>
                    <td><?= $p['is_active'] ? '✓' : '✗' ?></td>
                    <td class="actions">
                        <a href="/admin/product_edit.php?id=<?= (int)$p['id'] ?>"
                           class="btn btn--sm btn--ghost">Изменить</a>

                        <form method="post" action="/admin/products.php"
                              onsubmit="return confirm('Удалить «<?= e($p['name']) ?>»? Это нельзя отменить.')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" class="btn btn--sm btn--danger">Удалить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>
</main>

<script src="/assets/js/admin.js"></script>
</body>
</html>
