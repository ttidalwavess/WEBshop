<?php
// includes/orders.php
require_once __DIR__ . '/../config/db.php';

/**
 * Создание заказа из корзины пользователя
 */
function order_create_from_cart(int $user_id, string $customer_name, string $customer_phone): array
{
    $pdo = db();
    
    try {
        $pdo->beginTransaction();
        
        // 1. Получаем товары из корзины пользователя
        $stmt = $pdo->prepare("
            SELECT c.product_id, c.quantity, c.size, p.price, p.name
            FROM cart c
            JOIN products p ON p.id = c.product_id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll();
        
        if (empty($cart_items)) {
            return ['error' => 'Корзина пуста'];
        }
        
        // 2. Считаем общую сумму
        $total = 0;
        foreach ($cart_items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        // 3. Создаём заказ (статус 1 = 'pending')
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, status_id, total, customer_name, customer_phone, created_at)
            VALUES (?, 1, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, $total, $customer_name, $customer_phone]);
        $order_id = (int)$pdo->lastInsertId();
        
        // 4. Добавляем товары в order_items
        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($cart_items as $item) {
            $stmt->execute([
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);
        }
        
        // 5. Очищаем корзину пользователя
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        $pdo->commit();
        
        return ['success' => true, 'order_id' => $order_id];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('Order creation error: ' . $e->getMessage());
        return ['error' => 'Ошибка при создании заказа: ' . $e->getMessage()];
    }
}

/**
 * Получить заказы пользователя
 */
function order_get_by_user(int $user_id): array
{
    $pdo = db();
    
    $stmt = $pdo->prepare("
        SELECT o.id, o.total, o.created_at, o.customer_name, o.customer_phone, os.name AS status
        FROM orders o
        JOIN order_statuses os ON os.id = o.status_id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

/**
 * Получить детали заказа
 */
function order_get_details(int $order_id, int $user_id): ?array
{
    $pdo = db();
    
    // Информация о заказе
    $stmt = $pdo->prepare("
        SELECT o.id, o.total, o.created_at, o.customer_name, o.customer_phone, os.name AS status
        FROM orders o
        JOIN order_statuses os ON os.id = o.status_id
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        return null;
    }
    
    // Товары в заказе
    $stmt = $pdo->prepare("
        SELECT oi.quantity, oi.price,
               p.id AS product_id, p.name, p.size,
               pi.filename AS img
        FROM order_items oi
        JOIN products p ON p.id = oi.product_id
        LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order['items'] = $stmt->fetchAll();
    
    return $order;
}

/**
 * Обновить статус заказа (для админки)
 */
function order_update_status(int $order_id, int $status_id): bool
{
    require_admin();
    
    $pdo = db();
    $stmt = $pdo->prepare("UPDATE orders SET status_id = ? WHERE id = ?");
    return $stmt->execute([$status_id, $order_id]);
}

/**
 * Получить все заказы (для админки)
 */
function order_get_all(int $limit = 100): array
{
    require_admin();
    
    $pdo = db();
    
    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            o.total,
            o.created_at,
            o.customer_name,
            o.customer_phone,
            u.username,
            u.email,
            os.name AS status,
            os.id AS status_id,
            GROUP_CONCAT(p.name SEPARATOR ', ') AS items
        FROM orders o
        JOIN users u ON u.id = o.user_id
        JOIN order_statuses os ON os.id = o.status_id
        LEFT JOIN order_items oi ON oi.order_id = o.id
        LEFT JOIN products p ON p.id = oi.product_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    
    return $stmt->fetchAll();
}

/**
 * Получить все возможные статусы заказов
 */
function order_get_statuses(): array
{
    $pdo = db();
    return $pdo->query("SELECT id, name FROM order_statuses ORDER BY id")->fetchAll();
}