<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/categories.php';

session_start_safe();
require_admin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $action = input_str('action');
    $id = input_int('id');

    if ($action === 'create' || $action === 'update') {
        $name = input_str('name');
        $desc = input_str('discription');
        $sortOrder = input_int('sort_order');

        $result = ($action === 'create')
            ? category_create($name, $desc, $sortOrder)
            : category_update($id, $name, $desc, $sortOrder);
        
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            $message = ($action === 'create') ? 'Категория создана' : 'Категория обновлена';
        }
    }

    if ($action === 'delete') {
        $result = category_delete($id);
        if(isset($result['error'])){
            $error = $result['error'];
        } else {
            $message = 'Категория удалена';
        }
    }
}

$categories = categories_all();

$editCategory = null;
if (isset($_GET['edit'])) {
    $editCategory = category_by_id((int)$_GET['edit']);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Категории — Админка</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-layout">
<?php include __DIR__ . '/includes/admin_nav.php'; ?>

<main class="admin-main">
    <div class="admin-header">
        <h1>Группы товаров</h1>
    </div>

    <?php if ($message): ?>
        <div class="alert alert--success"><?= e($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert--error"><?= e($error) ?></div>
    <?php endif; ?>

    <!-- ФОРМА СОЗДАНИЯ / РЕДАКТИРОВАНИЯ -->
    <section class="admin-card">
        <h2><?= $editCategory ? 'Редактировать категорию' : 'Новая категория' ?></h2>
        <form method="post" action="/admin/categories.php">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="<?= $editCategory ? 'update' : 'create' ?>">
            <?php if ($editCategory): ?>
                <input type="hidden" name="id" value="<?= (int)$editCategory['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="name">Название *</label>
                <input type="text" id="name" name="name" required maxlength="100"
                       value="<?= e($editCategory['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="description">Описание</label>
                <textarea id="description" name="description" rows="3"><?= e($editCategory['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group form-group--small">
                <label for="sort_order">Порядок сортировки</label>
                <input type="number" id="sort_order" name="sort_order" min="0"
                       value="<?= (int)($editCategory['sort_order'] ?? 0) ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary">
                    <?= $editCategory ? 'Сохранить' : 'Создать' ?>
                </button>
                <?php if ($editCategory): ?>
                    <a href="/admin/categories.php" class="btn btn--ghost">Отмена</a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <!-- ТАБЛИЦА КАТЕГОРИЙ -->
    <section class="admin-card">
        <h2>Все категории (<?= count($categories) ?>)</h2>
        <?php if (empty($categories)): ?>
            <p class="empty-hint">Категорий пока нет.</p>
        <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Название</th>
                    <th>Slug</th>
                    <th>Порядок</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= (int)$cat['id'] ?></td>
                    <td><?= e($cat['name']) ?></td>
                    <td><code><?= e($cat['slug']) ?></code></td>
                    <td><?= (int)$cat['sort_order'] ?></td>
                    <td class="actions">
                        <a href="/admin/categories.php?edit=<?= (int)$cat['id'] ?>"
                           class="btn btn--sm btn--ghost">Изменить</a>

                        <form method="post" action="/admin/categories.php"
                              onsubmit="return confirm('Удалить категорию «<?= e($cat['name']) ?>»?')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
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
