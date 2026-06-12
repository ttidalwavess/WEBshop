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

    // ── Функция форматирования цены ───────────────────────────
    function formatPrice(price) {
        return Math.round(price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }

    // ── Пересчёт общей суммы в корзине ────────────────────────
    function recalcCartTotal() {
        var total = 0;
        $('.cart-item').each(function() {
            var $item = $(this);
            var price = parseFloat($item.find('.cart-item__price').data('price'));
            var qty = parseInt($item.find('.qty-value').text());
            if (!isNaN(price) && !isNaN(qty)) {
                total += price * qty;
            }
        });
        $('#summary-total, #summary-final').text('₽ ' + formatPrice(total));
    }

    // ── Обновление суммы в правой колонке для товара ──────────
    function updateSummaryItem(cartId, newQuantity, price) {
        var $summaryItem = $('.cart-summary__item[data-cart-id="' + cartId + '"]');
        if ($summaryItem.length) {
            var newTotal = price * newQuantity;
            $summaryItem.find('.cart-summary__item-price').text('₽ ' + formatPrice(newTotal));
        }
    }

    // ── Обновление количества товаров в заголовке ─────────────
    function updateCartItemsCount() {
        var count = $('.cart-item').length;
        $('.cart-title__count').text('(' + count + ')');
        if (count === 0) {
            location.reload();
        }
    }

    // ── Обработчик кнопок + и - в корзине ─────────────────────
    $(document).on('click', '.qty-plus, .qty-minus', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var cartId = $btn.data('cart-id');
        var $cartItem = $btn.closest('.cart-item');
        var $qtySpan = $cartItem.find('.qty-value');
        var currentQty = parseInt($qtySpan.text());
        var newQty = currentQty;
        
        if ($btn.hasClass('qty-plus')) {
            newQty = currentQty + 1;
        } else if ($btn.hasClass('qty-minus')) {
            newQty = Math.max(1, currentQty - 1);
        }
        
        if (newQty === currentQty) return;
        
        // Блокируем кнопки на время запроса
        $('.qty-btn').prop('disabled', true);
        
        $.ajax({
            url: '/api/cart/update.php',
            method: 'POST',
            dataType: 'json',
            data: { cart_id: cartId, qty: newQty },
            success: function(response) {
                if (response.success) {
                    // Обновляем количество на странице
                    $qtySpan.text(response.new_quantity);
                    
                    // Обновляем цену строки
                    var $priceSpan = $cartItem.find('.cart-item__price');
                    var price = parseFloat($priceSpan.data('price'));
                    var newRowPrice = price * response.new_quantity;
                    $priceSpan.text('₽ ' + formatPrice(newRowPrice));
                    
                    // Обновляем итоговую колонку
                    updateSummaryItem(cartId, response.new_quantity, price);
                    
                    // Пересчитываем общую сумму
                    recalcCartTotal();
                    
                    // Обновляем бейдж корзины
                    updateCartBadge(response.cart_count);
                } else {
                    alert(response.error || 'Ошибка при обновлении');
                }
                $('.qty-btn').prop('disabled', false);
            },
            error: function(xhr) {
                console.error('Ошибка:', xhr.responseText);
                alert('Ошибка при обновлении количества');
                $('.qty-btn').prop('disabled', false);
            }
        });
    });

    // ── Обработчик удаления товара из корзины ─────────────────
    $(document).on('click', '.cart-item__remove', function() {
        var cartId = $(this).data('cart-id');
        var $item = $(this).closest('.cart-item');
        
        if (!confirm('Удалить товар из корзины?')) return;
        
        $.ajax({
            url: '/api/cart/remove.php',
            method: 'POST',
            dataType: 'json',
            data: { cart_id: cartId },
            success: function(response) {
                if (response.success) {
                    $item.fadeOut(300, function() {
                        $(this).remove();
                        $('.cart-summary__item[data-cart-id="' + cartId + '"]').remove();
                        recalcCartTotal();
                        updateCartItemsCount();
                        updateCartBadge(response.cart_count);
                    });
                } else {
                    alert(response.error || 'Ошибка при удалении');
                }
            },
            error: function(xhr) {
                console.error('Ошибка:', xhr.responseText);
                alert('Ошибка при удалении товара');
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