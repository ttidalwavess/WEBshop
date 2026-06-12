/**
 * cart.js — AJAX-корзина
 * Требует jQuery (подключён в footer.php выше этого скрипта).
 */
$(function () {

    // ── Добавление в корзину ─────────────────────────────────
    $(document).on('click', '.btn-add-to-cart', function () {
        if ($('meta[name="user-logged-in"]').attr('content') !== '1') {
        showLoginModal();
        return;
        }
        var $btn        = $(this);
        var productId   = $btn.data('id');
        var productName = $btn.data('name');

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

function showLoginModal() {
    // Удаляем старое если есть
    $('#login-modal').remove();

    $('body').append(`
        <div id="login-modal" style="
            position:fixed;inset:0;background:rgba(0,0,0,0.4);
            z-index:1000;display:flex;align-items:center;justify-content:center">
            <div style="
                background:#fff;border-radius:16px;padding:2.5rem 2rem;
                max-width:360px;width:90%;text-align:center;font-family:Montserrat,sans-serif">
                <p style="font-size:1.1rem;font-weight:700;margin-bottom:0.8rem;color:#1A1A1A">
                    Вы не вошли в аккаунт
                </p>
                <p style="font-size:0.88rem;color:#1A1A1A;opacity:0.6;margin-bottom:1.8rem;line-height:1.5">
                    Войдите, чтобы добавлять товары в корзину
                </p>
                <a href="/login.php" style="
                    display:block;padding:14px;background:#330000;color:#E7F0F7;
                    border-radius:8px;font-weight:700;font-size:0.85rem;
                    letter-spacing:1px;text-transform:uppercase;text-decoration:none;
                    margin-bottom:0.8rem">
                    Войти
                </a>
                <button onclick="$('#login-modal').remove()" style="
                    background:none;border:none;font-size:0.85rem;
                    color:#1A1A1A;opacity:0.5;cursor:pointer;font-family:Montserrat,sans-serif">
                    Закрыть
                </button>
            </div>
        </div>
    `);

    // Закрытие по клику на фон
    $('#login-modal').on('click', function(e) {
        if ($(e.target).is('#login-modal')) $(this).remove();
    });
}