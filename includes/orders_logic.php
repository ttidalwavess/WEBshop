<?php
// includes/orders.php — логика заказов
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/security.php';

/**
 * Создаёт заказ из корзины пользователя.
 * Возвращает ['ok' => true, 'order_id' => int] или ['error' => '...']
 */
function create_order(int $user_id, string $name, string $phone): array
{
    $pdo = db();

    // Получаем корзину пользователя
    $stmt = $pdo->prepare('
        SELECT c.id AS cart_id, c.product_id, c.quantity,
               p.price, p.name, p.is_active
        FROM cart c
        JOIN products p ON p.id = c.product_id
        WHERE c.user_id = ?
    ');
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll();

    if (empty($items)) {
        return ['error' => 'Корзина пуста'];
    }

    // Проверяем что все товары активны
    foreach ($items as $item) {
        if (!$item['is_active']) {
            return ['error' => 'Товар «' . $item['name'] . '» больше недоступен'];
        }
    }

    // Считаем итого
    $total = 0;
    foreach ($items as $item) {
        $total += (float)$item['price'] * (int)$item['quantity'];
    }

    // Всё в транзакции — если что-то упадёт, заказ не создастся
    $pdo->beginTransaction();

    try {
        // Создаём заказ
        $stmt = $pdo->prepare('
            INSERT INTO orders (user_id, status_id, total)
            VALUES (?, 1, ?)
        ');
        $stmt->execute([$user_id, $total]);
        $order_id = (int)$pdo->lastInsertId();

        // Добавляем товары заказа (цена фиксируется на момент покупки)
        $stmt = $pdo->prepare('
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ');
        foreach ($items as $item) {
            $stmt->execute([
                $order_id,
                (int)$item['product_id'],
                (int)$item['quantity'],
                (float)$item['price'],
            ]);
        }

        // Очищаем корзину
        $stmt = $pdo->prepare('DELETE FROM cart WHERE user_id = ?');
        $stmt->execute([$user_id]);

        $pdo->commit();

        return ['ok' => true, 'order_id' => $order_id];

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('create_order error: ' . $e->getMessage());
        return ['error' => 'Ошибка при создании заказа. Попробуйте ещё раз.'];
    }
}

/**
 * Возвращает количество товаров в корзине пользователя.
 */
function cart_count(int $user_id): int
{
    $stmt = db()->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart WHERE user_id = ?');
    $stmt->execute([$user_id]);
    return (int)$stmt->fetchColumn();
}