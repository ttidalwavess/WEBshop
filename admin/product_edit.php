<?php
define('ROOT', dirname(__DIR__));
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';
require_once ROOT . '/includes/products.php';
require_once ROOT . '/includes/categories.php';

session_start_safe();
require_admin();

$productId = input_int('id', $_GET);
$product = product_by_id($productId);

if (!$product) {
    http_response_code(404);
    die('Товар не найден.');
}

$messege = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = input_str('action');

    if ($action === 'update_product'){
        $result = product_update($productId, [
            'name' => input_str('name'),
            'category_id' => input_int('category_id'),
            'description' => input_str('description'),
            'price' => input_float('price'),
            'size' => input_str('size'),
            'is_top' => isset($_POST['is_top']) ? 1 : 0,
            'is_new' => isset($_POST['is_new']) ? 1 : 0,
        ]);
        $error   = $result['error'] ?? '';
        $message = $error ? '' : 'Товар обновлён.';
        if (!$error) $product = product_by_id($productId);
    }
    if ($action === 'upload_image') {
        $result  = product_image_upload($productId, 'new_image', isset($_POST['is_main']));
        $error   = $result['error'] ?? '';
        $message = $error ? '' : 'Изображение загружено.';
    }
    if ($action === 'delete_image') {
        $result  = product_image_delete(input_int('image_id'));
        $error   = $result['error'] ?? '';
        $message = $error ? '' : 'Изображение удалено.';
    }
    if ($action === 'set_main_image') {
        product_image_set_main(input_int('image_id'), $productId);
        $message = 'Главное фото изменено.';
    }
}

$categories = categories_all();
$images = product_images($productId);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование: <?= e($product['name']) ?> — Админка</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-layout">
<?php include __DIR__ . '/includes/admin_nav.php'; ?>

<main class="admin-main">
    <div class="admin-header">
        <h1>Редактирование товара</h1>
        <a href="/admin/products.php" class="btn btn--ghost">← К списку</a>
    </div>

    <?php if ($message): ?><div class="alert alert--success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

    <section class="admin-card">
        <h2>Основные данные</h2>
        <form method="post" action="/admin/product_edit.php?id=<?= $productId ?>">
            <input type="hidden" name="action" value="update_product">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Название *</label>
                    <input type="text" id="name" name="name" required maxlength="200"
                           value="<?= e($product['name']) ?>">
                </div>
                <div class="form-group">
                    <label for="category_id">Категория *</label>
                    <select id="category_id" name="category_id" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>"
                                <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Описание</label>
                <textarea id="description" name="description" rows="5"><?= e($product['description']) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group form-group--small">
                    <label for="price">Цена (₽) *</label>
                    <input type="number" id="price" name="price" required min="0" step="0.01"
                           value="<?= e($product['price']) ?>">
                </div>
                <div class="form-group form-group--small">
                    <label>Размер</label>
                    <select name="size">
                        <?php
                        $sizes = [
                            'Одежда'    => ['XS','S','M','L','XL','XXL'],
                            'Сумки'     => ['Большой','Средний','Маленький'],
                            'Украшения' => ['Универсальный'],
                        ];
                        foreach ($sizes as $group => $opts): ?>
                            <optgroup label="<?= $group ?>">
                                <?php foreach ($opts as $s): ?>
                                    <option <?= $product['size'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group form-group--checkbox">
                    <label><input type="checkbox" name="is_new" <?= !empty($product['is_new']) ? 'checked' : '' ?>> Новинка</label>
                </div>
                <div class="form-group form-group--checkbox">
                    <label><input type="checkbox" name="is_top" <?= !empty($product['is_top']) ? 'checked' : '' ?>> Топ продаж</label>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn--primary">Сохранить изменения</button>
            </div>
        </form>
    </section>

    <section class="admin-card">
        <h2>Фотографии товара</h2>

        <form method="post" action="/admin/product_edit.php?id=<?= $productId ?>"
              enctype="multipart/form-data" class="upload-form">
            <input type="hidden" name="action" value="upload_image">

            <div class="form-row form-row--align-end">
                <div class="form-group">
                    <label for="new_image">Добавить фото (JPEG/PNG/WebP, до 5 МБ)</label>
                    <input type="file" id="new_image" name="new_image" accept="image/*" required>
                </div>
                <div class="form-group form-group--checkbox">
                    <label>
                        <input type="checkbox" name="is_main"> Сделать главным
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn--secondary">Загрузить</button>
                </div>
            </div>
        </form>

        <?php if (empty($images)): ?>
            <p class="empty-hint">Фотографий пока нет.</p>
        <?php else: ?>
        <div class="image-gallery">
            <?php foreach ($images as $img): ?>
                <div class="image-card <?= $img['is_main'] ? 'image-card--main' : '' ?>">
                    <img src="<?= product_img_url($img['filename']) ?>"
                         alt="<?= e($product['name'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php if ($img['is_main']): ?><span class="badge badge--main">Главное</span><?php endif; ?>
                    <div class="image-card__actions">
                        <?php if (!$img['is_main']): ?>
                        <form method="post" action="/admin/product_edit.php?id=<?= $productId ?>">
                            <input type="hidden" name="action" value="set_main_image">
                            <input type="hidden" name="image_id" value="<?= (int)$img['id'] ?>">
                            <button type="submit" class="btn btn--xs btn--ghost">Сделать главным</button>
                        </form>
                        <?php endif; ?>
                        <form method="post" action="/admin/product_edit.php?id=<?= $productId ?>"
                              onsubmit="return confirm('Удалить фото?')">
                            <input type="hidden" name="action" value="delete_image">
                            <input type="hidden" name="image_id" value="<?= (int)$img['id'] ?>">
                            <button type="submit" class="btn btn--xs btn--danger">Удалить</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>
</main>

<script src="/assets/js/admin.js"></script>
</body>
</html>
