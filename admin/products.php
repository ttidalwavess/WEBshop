<?php
define('ROOT', dirname(__DIR__));
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';
require_once ROOT . '/includes/products.php';
require_once ROOT . '/includes/categories.php';

session_start_safe();
require_admin();

$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = input_str('action');
    $id = input_int('id');

    if ($action === 'create') {
        $result = product_create([
            'name' => input_str('name'),
            'category_id' => input_int('category_id'),
            'description' => input_str('description'),
            'price' => input_float('price'),
            'size' => input_str('size'),
            'is_top' => isset($_POST['is_top']),
            'is_new' => isset($_POST['is_new']),
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
        $result  = product_delete($id);
        $error   = $result['error'] ?? '';
        $message = $error ? '' : 'Товар удалён.';
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
    <link rel="stylesheet" href="/admin/assets/css/admin.css">
</head>
<body class="admin-layout">
<?php include __DIR__ . '/includes/admin_nav.php'; ?>

<main class="admin-main">
    <div class="admin-header">
        <h1>Товары</h1>
    </div>

    <?php if ($message): ?><div class="alert alert--success"><?= $message ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert--error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

    <section class="admin-card">
        <h2>Добавить товар</h2>
        <form method="post" action="/admin/products.php" enctype="multipart/form-data">
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
                <label>Описание</label>
                <textarea name="description" rows="3"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group form-group--small">
                    <label>Цена (₽) *</label>
                    <input type="number" name="price" required min="0" step="0.01">
                </div>
                <div class="form-group form-group--small">
                    <label>Размер</label>
                    <select name="size">
                        <optgroup label="Одежда">
                            <option>XS</option><option>S</option><option>M</option>
                            <option>L</option><option>XL</option><option>XXL</option>
                        </optgroup>
                        <optgroup label="Сумки">
                            <option>Большой</option><option>Средний</option><option>Маленький</option>
                        </optgroup>
                        <optgroup label="Украшения">
                            <option selected>Универсальный</option>
                        </optgroup>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group form-group--checkbox">
                    <label><input type="checkbox" name="is_new"> Новинка</label>
                </div>
                <div class="form-group form-group--checkbox">
                    <label><input type="checkbox" name="is_top"> Топ продаж</label>
                </div>
            </div>
            <div class="form-group">
                <label>Главное фото (JPEG/PNG/WebP, до 5 МБ)</label>
                <input type="file" name="main_image" accept="image/*">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn--primary">Создать товар</button>
            </div>
        </form>
    </section>

    <section class="admin-card">
        <h2>Все товары (<?= count($products) ?>)</h2>
        <?php if (empty($products)): ?>
            <p class="empty-hint">Товаров пока нет.</p>
        <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr><th>Фото</th><th>Название</th><th>Категория</th>
                    <th>Размер</th><th>Цена</th><th>New</th><th>Top</th><th>Действия</th></tr>
            </thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr class="row--inactive">
                    <td>
                        <?php if ($p['main_image']): ?>
                            <img src="<?= product_img_url($p['main_image']) ?>" alt="" class="thumb">
                        <?php else: ?>
                            <span class="no-photo">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($p['name']) ?></td>
                    <td><?= e($p['category_name']) ?></td>
                    <td><?= e($p['size']) ?></td>
                    <td><?= number_format($p['price'], 2, '.', ' ') ?> ₽</td>
                    <td><?= $p['is_new'] ? '✨' : '—' ?></td>
                    <td><?= $p['is_top'] ? '🔥' : '—' ?></td>
                    <td class="actions">
                        <a href="/admin/product_edit.php?id=<?= (int)$p['id'] ?>"
                           class="btn btn--sm btn--ghost">Изменить</a>
                        <form method="post" action="/admin/products.php"
                              onsubmit="return confirm('Удалить «<?= e($p['name']) ?>»? Это нельзя отменить.')">
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

<script src="/admin/assets/js/admin.js"></script>
</body>
</html>
