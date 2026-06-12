/**
 * cart.js — AJAX-корзина
 * Требует jQuery (подключён в footer.php выше этого скрипта).
 */
$(function () {

    // ── Добавление в корзину ─────────────────────────────────
    $(document).on('click', '.btn-add-to-cart', function () {
        var $btn        = $(this);
        var productId   = $btn.data('id');
        var productName = $btn.data('name');

        $btn.prop('disabled', true).text('Добавляем...');

        $.ajax({
            url:      '/api/cart_add.php', // этот файл создаёт Роль 2
            method:   'POST',
            dataType: 'json',
            data: {
                product_id:  productId,
                qty:         1,
                csrf_token:  $('meta[name="csrf-token"]').attr('content') || ''
            },
            success: function (res) {
                if (res.success) {
                    updateCartBadge(res.cart_count);
                    $btn.text('Добавлено').css({ background: '#330000', color: '#E7F0F7' });
                    setTimeout(function () {
                        $btn.text('В корзину').css({ background: '', color: '' }).prop('disabled', false);
                    }, 1800);
                } else {
                    alert(res.message || 'Ошибка при добавлении');
                    $btn.text('В корзину').prop('disabled', false);
                }
            },
            error: function () {
                // Заглушка пока нет бэкенда корзины
                var current = parseInt($('#cart-count').text()) || 0;
                updateCartBadge(current + 1);
                $btn.text('Добавлено').css({ background: '#330000', color: '#E7F0F7' });
                setTimeout(function () {
                    $btn.text('В корзину').css({ background: '', color: '' }).prop('disabled', false);
                }, 1800);
            }
        });
    });

    // ── Обновление бейджа корзины ────────────────────────────
    function updateCartBadge(count) {
        var $badge = $('#cart-count');
        if (count > 0) {
            $badge.text(count).show();
        } else {
            $badge.hide();
        }
    }

});


// ---- Бургер-меню ----
$(document).on('click', '#navToggle', function () {
    $('.nav-center').toggleClass('open');
    $('.header-icons').toggleClass('open');
    $(this).toggleClass('open');
});

// Закрываем при клике на ссылку или иконку
$(document).on('click', '.nav-center .nav-link, .header-icons .icon-btn', function () {
    $('.nav-center').removeClass('open');
    $('.header-icons').removeClass('open');
    $('#navToggle').removeClass('open');
});

// Закрываем при клике вне меню
$(document).on('click', function (e) {
    if (!$(e.target).closest('.nav-center, .header-icons, #navToggle').length) {
        $('.nav-center').removeClass('open');
        $('.header-icons').removeClass('open');
        $('#navToggle').removeClass('open');
    }
});