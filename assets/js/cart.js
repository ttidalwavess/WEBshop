/**
 * cart.js — AJAX-корзина
 */
$(function () {
    // ── Добавление в корзину ─────────────────────────────────
    $(document).on('click', '.btn-add-to-cart', function (e) {
        e.preventDefault();
        
        if ($('meta[name="user-logged-in"]').attr('content') !== '1') {
            showLoginModal();
            return;
        }
        
        var $btn = $(this);
        var productId = $btn.data('id');
        var selectedSize = '';
        
        // Проверяем, есть ли на странице выбор размера (только на странице товара)
        var $sizeBtns = $('.size-btn');
        if ($sizeBtns.length > 0) {
            var $activeSize = $('.size-btn.active');
            if ($activeSize.length === 0) {
                $('.product-sizes__grid').addClass('shake');
                setTimeout(function () {
                    $('.product-sizes__grid').removeClass('shake');
                }, 500);
                return false;
            }
            selectedSize = $activeSize.data('size');
        }
        
        // Отключаем кнопку на время запроса
        $btn.prop('disabled', true);
        
        $.ajax({
            url: '/api/cart/add.php',
            method: 'POST',
            dataType: 'json',
            data: {
                product_id: productId,
                qty: 1,
                size: selectedSize
            },
            success: function (res) {
                if (res.success) {
                    updateCartBadge(res.cart_count);
                    $btn.text('Добавлено').css({ background: '#27ae60', color: '#fff' });
                    setTimeout(function () {
                        $btn.text('В корзину').css({ background: '', color: '' }).prop('disabled', false);
                    }, 1800);
                } else {
                    alert(res.message || 'Ошибка при добавлении');
                    $btn.prop('disabled', false);
                }
            },
            error: function (xhr) {
                console.error('Ошибка:', xhr.responseText);
                alert('Ошибка при добавлении товара');
                $btn.prop('disabled', false);
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
    $('#login-modal').remove();
    $('body').append(`
        <div id="login-modal" style="
            position:fixed;inset:0;background:rgba(0,0,0,0.5);
            z-index:10000;display:flex;align-items:center;justify-content:center">
            <div style="
                background:#fff;border-radius:16px;padding:2.5rem 2rem;
                max-width:360px;width:90%;text-align:center">
                <p style="font-size:1.1rem;font-weight:700;margin-bottom:0.8rem">
                    Вы не вошли в аккаунт
                </p>
                <p style="font-size:0.88rem;color:#666;margin-bottom:1.8rem">
                    Войдите, чтобы добавлять товары в корзину
                </p>
                <a href="/login.php" style="
                    display:block;padding:14px;background:#330000;color:#fff;
                    border-radius:8px;font-weight:700;text-decoration:none;
                    margin-bottom:0.8rem">
                    Войти
                </a>
                <button onclick="$('#login-modal').remove()" style="
                    background:none;border:none;font-size:0.85rem;
                    color:#666;cursor:pointer">
                    Закрыть
                </button>
            </div>
        </div>
    `);
    $('#login-modal').on('click', function(e) {
        if ($(e.target).is('#login-modal')) $(this).remove();
    });
}