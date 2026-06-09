/**
 * cart.js — AJAX-корзина
 * Подключается глобально через footer.php
 * Требует jQuery (подключён выше в footer.php)
 */

$(function () {

    // -------------------------------------------------
    // Добавление товара в корзину (кнопка «В корзину»)
    // -------------------------------------------------
    $(document).on('click', '.btn-add-to-cart', function () {
        const $btn      = $(this);
        const productId = $btn.data('id');
        const productName = $btn.data('name');

        // Визуальная блокировка кнопки на время запроса
        $btn.prop('disabled', true).text('Добавляем...');

        $.ajax({
            url: '/api/cart_add.php',   // TODO: Роль 2 создаёт этот файл
            method: 'POST',
            dataType: 'json',
            data: {
                product_id: productId,
                qty: 1,
                // CSRF-токен добавит Роль 1 через мета-тег или скрытое поле
                // csrf_token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success) {
                    // Обновляем счётчик в шапке
                    updateCartBadge(res.cart_count);

                    // Анимация кнопки
                    $btn.text('✓ Добавлено').css({ background: '#330000', color: '#E7F0F7', borderColor: '#330000' });
                    setTimeout(() => {
                        $btn.text('В корзину').css({ background: '', color: '', borderColor: '' }).prop('disabled', false);
                    }, 1800);
                } else {
                    alert(res.message || 'Ошибка при добавлении');
                    $btn.text('В корзину').prop('disabled', false);
                }
            },
            error: function () {
                // Заглушка пока нет бэкенда
                $btn.text('✓ Добавлено').css({ background: '#330000', color: '#E7F0F7', borderColor: '#330000' });
                const current = parseInt($('#cart-count').text()) || 0;
                updateCartBadge(current + 1);
                setTimeout(() => {
                    $btn.text('В корзину').css({ background: '', color: '', borderColor: '' }).prop('disabled', false);
                }, 1800);
            }
        });
    });

    // -------------------------------------------------
    // Обновление бейджа корзины в шапке
    // -------------------------------------------------
    function updateCartBadge(count) {
        const $badge = $('#cart-count');
        if (count > 0) {
            $badge.text(count).show();
        } else {
            $badge.hide();
        }
    }

});