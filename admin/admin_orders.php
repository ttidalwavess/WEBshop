<?php
define('ROOT', dirname(__DIR__));
require_once ROOT . '/config/db.php';
require_once ROOT . '/includes/security.php';

session_start_safe();
require_admin();

$pdo = db();

// Получаем все статусы для дропдауна
$statuses = $pdo->query('SELECT id, name FROM order_statuses ORDER BY id')->fetchAll();

$statusLabels = [
    'pending'    => 'Принят',
    'processing' => 'В обработке',
    'shipped'    => 'Отправлен',
    'delivered'  => 'Доставлен',
    'cancelled'  => 'Отменён',
];
$statusColors = [
    'pending'    => 'badge--warning',
    'processing' => 'badge--info',
    'shipped'    => 'badge--primary',
    'delivered'  => 'badge--success',
    'cancelled'  => 'badge--danger',
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказы — Админка</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/admin/assets/css/admin.css">
</head>
<body class="admin-layout">
<?php include __DIR__ . '/includes/admin_nav.php'; ?>

<main class="admin-main">

    <div class="orders-live-header">
        <h1 style="font-size:1.5rem;font-weight:800;text-transform:uppercase;letter-spacing:0.03em">
            Заказы
        </h1>
        <div class="live-indicator">
            <span class="live-dot"></span>
            Live
        </div>
        <span id="last-update" style="font-size:0.75rem;opacity:0.4;margin-left:auto"></span>
    </div>

    <div class="admin-card" style="padding:0;overflow:hidden">
        <table class="admin-table" id="orders-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Покупатель</th>
                    <th>Товары</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Дата</th>
                    <th>Изменить статус</th>
                </tr>
            </thead>
            <tbody id="orders-tbody">
                <tr><td colspan="7" style="text-align:center;padding:3rem;opacity:0.4">
                    Загрузка...
                </td></tr>
            </tbody>
        </table>
    </div>

</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

<script>
var STATUS_LABELS = <?= json_encode($statusLabels) ?>;
var STATUS_COLORS = <?= json_encode($statusColors) ?>;
var STATUSES      = <?= json_encode($statuses) ?>;

function renderOrders(orders) {
    if (!orders.length) {
        $('#orders-tbody').html(
            '<tr><td colspan="7" style="text-align:center;padding:3rem;opacity:0.4">Заказов пока нет</td></tr>'
        );
        return;
    }

    var html = '';
    $.each(orders, function(i, o) {
        var badge  = STATUS_COLORS[o.status]  || 'badge--secondary';
        var label  = STATUS_LABELS[o.status]  || o.status;

        // Дропдаун статусов
        var select = '<select class="order-status-select" data-order-id="' + o.id + '">';
        $.each(STATUSES, function(j, s) {
            select += '<option value="' + s.id + '"'
                + (s.name === o.status ? ' selected' : '')
                + '>' + (STATUS_LABELS[s.name] || s.name) + '</option>';
        });
        select += '</select>';

        html += '<tr id="order-row-' + o.id + '">'
            + '<td><strong>#' + o.id + '</strong></td>'
            + '<td>' + $('<div>').text(o.username).html() + '</td>'
            + '<td style="max-width:200px;font-size:0.78rem;opacity:0.7">'
                + $('<div>').text(o.items).html()
            + '</td>'
            + '<td style="font-weight:700">' + o.total + ' ₽</td>'
            + '<td><span class="badge ' + badge + '">' + label + '</span></td>'
            + '<td style="white-space:nowrap;font-size:0.8rem">' + o.created_at + '</td>'
            + '<td>' + select + '</td>'
            + '</tr>';
    });

    $('#orders-tbody').html(html);
}

function loadOrders() {
    $.ajax({
        url: '/api/admin_orders.php',
        method: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                renderOrders(res.orders);
                var now = new Date();
                $('#last-update').text('Обновлено: '
                    + now.toLocaleTimeString('ru-RU', {hour:'2-digit', minute:'2-digit', second:'2-digit'}));
            }
        },
        error: function() {
            $('#last-update').text('Ошибка соединения');
        }
    });
}

// Смена статуса
$(document).on('change', '.order-status-select', function() {
    var $sel     = $(this);
    var orderId  = $sel.data('order-id');
    var statusId = $sel.val();

    $sel.prop('disabled', true);

    $.ajax({
        url:    '/api/admin_order_status.php',
        method: 'POST',
        dataType: 'json',
        data: { order_id: orderId, status_id: statusId },
        success: function(res) {
            if (res.success) {
                // Обновляем бейдж в строке без перезагрузки всей таблицы
                var $row   = $('#order-row-' + orderId);
                var badge  = STATUS_COLORS[res.status_name]  || 'badge--secondary';
                var label  = STATUS_LABELS[res.status_name]  || res.status_name;
                $row.find('.badge').attr('class', 'badge ' + badge).text(label);
            }
            $sel.prop('disabled', false);
        },
        error: function() {
            alert('Ошибка при смене статуса');
            $sel.prop('disabled', false);
        }
    });
});

// Загружаем сразу и потом каждые 8 секунд
loadOrders();
setInterval(loadOrders, 8000);
</script>
</body>
</html>