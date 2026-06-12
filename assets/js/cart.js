/**
 * cart.js — AJAX-корзина
 * Требует jQuery.
 */

// ── Модалка незалогиненного ──────────────────────────────
function showLoginModal() {
    $('#login-modal').remove();
    $('body').append(
        '<div id="login-modal" style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:10000;display:flex;align-items:center;justify-content:center">' +
            '<div style="background:#fff;border-radius:16px;padding:2.5rem 2rem;max-width:370px;width:90%;text-align:center">' +
                '<p style="font-size:1.1rem;font-weight:700;margin-bottom:0.8rem">Вы не вошли в аккаунт</p>' +
                '<p style="font-size:0.88rem;color:#666;margin-bottom:1.8rem">Войдите, чтобы добавлять товары в корзину</p>' +
                '<a href="/login.php" style="display:block;padding:14px;background:#330000;color:#fff;border-radius:8px;font-weight:700;text-decoration:none;margin-bottom:0.8rem">Войти</a>' +
                '<button onclick="$(\'#login-modal\').remove()" style="background:none;border:none;font-size:0.85rem;color:#666;cursor:pointer">Закрыть</button>' +
            '</div>' +
        '</div>'
    );
    $('#login-modal').on('click', function(e) {
        if ($(e.target).is('#login-modal')) $(this).remove();
    });
}

// ── Модалка выбора размера (для каталога) ───────────────
function showSizeModal(productId, productName, $btn) {
    $('#size-modal').remove();

    var sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
    var sizeButtons = '';
    sizes.forEach(function(s) {
        sizeButtons += '<button class="size-modal__btn" data-size="' + s + '">' + s + '</button>';
    });

    $('body').append(
        '<div id="size-modal" style="position:fixed;inset:0;background:#00000073;z-index:10000;display:flex;align-items:center;justify-content:center">' +
            '<div style="background:#fff;border-radius:16px;padding:2rem;max-width:370px;width:90%;font-family:Montserrat,sans-serif;text-align:center">' +
                '<p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;opacity:0.45;margin-bottom:0.4rem">Добавить в корзину</p>' +
                '<p style="font-size:1rem;font-weight:700;color:#1A1A1A;margin-bottom:1.5rem">' + productName + '</p>' +
                '<p style="font-size:0.8rem;font-weight:600;opacity:0.5;margin-bottom:0.7rem">выберите размер</p>' +
                '<div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1.5rem">' + sizeButtons + '</div>' +
                '<button id="size-modal-confirm" disabled style="width:100%;padding:14px;background:#330000;color:#fff;border:none;border-radius:8px;font-family:Montserrat,sans-serif;font-size:0.82rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;opacity:0.4">В корзину</button>' +
                '<button onclick="$(\'#size-modal\').remove()" style="width:100%;margin-top:0.6rem;background:none;border:none;font-size:0.82rem;color:#1A1A1A;opacity:0.45;cursor:pointer">Закрыть</button>' +
            '</div>' +
        '</div>'
    );

    setTimeout(function() {
        $('.size-modal__btn').css({ background: 'transparent', color: '#1A1A1A', border: '1px solid #EBE4DA', borderRadius: '4px', padding: '10px 14px', fontWeight: '600', fontSize: '0.8rem', cursor: 'pointer' });
    }, 10);

    $(document).on('click.sizemodal', '.size-modal__btn', function() {
        $('.size-modal__btn').css({ background: 'transparent', color: '#1A1A1A', border: '1px solid #EBE4DA' });
        $(this).css({ background: '#330000', color: '#fff', border: '1px solid #330000' });
        $('#size-modal-confirm').prop('disabled', false).css('opacity', '1').data('size', $(this).data('size'));
    });

    $(document).one('click.sizeconfirm', '#size-modal-confirm', function() {
        var size = $(this).data('size');
        if (!size) return;
        $(document).off('click.sizemodal');
        $('#size-modal').remove();
        doAddToCart($btn, productId, size);
    });

    $('#size-modal').on('click', function(e) {
        if ($(e.target).is('#size-modal')) {
            $(document).off('click.sizemodal click.sizeconfirm');
            $(this).remove();
        }
    });
}

// ── Отправка в корзину ───────────────────────────────────
function doAddToCart($btn, productId, size) {
    $btn.prop('disabled', true);
    $.ajax({
        url: '/api/cart/add.php',
        method: 'POST',
        dataType: 'json',
        data: { product_id: productId, qty: 1, size: size },
        success: function(res) {
            if (res.success) {
                updateCartBadge(res.cart_count);
                $btn.text('Добавлено').css({ background: '#330000', color: '#fff' });
                setTimeout(function() {
                    $btn.text('В корзину').css({ background: '', color: '' }).prop('disabled', false);
                }, 1800);
            } else {
                alert(res.message || 'Ошибка');
                $btn.prop('disabled', false);
            }
        },
        error: function(xhr) {
            console.error('Ошибка:', xhr.responseText);
            $btn.prop('disabled', false);
        }
    });
}

// ── Обновление бейджа корзины ────────────────────────────
function updateCartBadge(count) {
    var $badge = $('#cart-count');
    count > 0 ? $badge.text(count).show() : $badge.hide();
}

$(function () {

    // ── Добавление в корзину ─────────────────────────────────
    $(document).on('click', '.btn-add-to-cart', function(e) {
        e.preventDefault();

        if ($('meta[name="user-logged-in"]').attr('content') !== '1') {
            showLoginModal();
            return;
        }

        var $btn        = $(this);
        var productId   = $btn.data('id');
        var productName = $btn.data('name') || '';

        // На странице товара — проверяем выбранный размер
        var $sizeBtns = $('.size-btn');
        if ($sizeBtns.length > 0) {
            var $activeSize = $('.size-btn.active');
            if ($activeSize.length === 0) {
                $('.product-sizes__grid').addClass('shake');
                setTimeout(function() { $('.product-sizes__grid').removeClass('shake'); }, 500);
                return;
            }
            doAddToCart($btn, productId, $activeSize.data('size'));
            return;
        }

        var size = $btn.data('size') || '';
        if (!size || size === 'Универсальный') {
        doAddToCart($btn, productId, 'Универсальный');
        return;
    }

        showSizeModal(productId, productName, $btn);
    });

    // ── Форматирование цены ──────────────────────────────────
    function formatPrice(price) {
        return Math.round(price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }

    // ── Пересчёт суммы в корзине ─────────────────────────────
    function recalcCartTotal() {
        var total = 0;
        $('.cart-item').each(function() {
            var price = parseFloat($(this).find('.cart-item__price').data('price'));
            var qty   = parseInt($(this).find('.qty-value').text());
            if (!isNaN(price) && !isNaN(qty)) total += price * qty;
        });
        $('#summary-total, #summary-final').text('₽ ' + formatPrice(total));
    }

    function updateSummaryItem(cartId, qty, price) {
        $('.cart-summary__item[data-cart-id="' + cartId + '"] .cart-summary__item-price')
            .text('₽ ' + formatPrice(price * qty));
    }

    function updateCartItemsCount() {
        var n = $('.cart-item').length;
        $('.cart-title__count').text('(' + n + ')');
        if (n === 0) location.reload();
    }

    // ── +/- количество ───────────────────────────────────────
    $(document).on('click', '.qty-plus, .qty-minus', function(e) {
        e.preventDefault();
        var $btn     = $(this);
        var cartId   = $btn.data('cart-id');
        var $item    = $btn.closest('.cart-item');
        var $qty     = $item.find('.qty-value');
        var current  = parseInt($qty.text());
        var newQty   = $btn.hasClass('qty-plus') ? current + 1 : Math.max(1, current - 1);
        if (newQty === current) return;

        $('.qty-btn').prop('disabled', true);

        $.ajax({
            url: '/api/cart/update.php',
            method: 'POST',
            dataType: 'json',
            data: { cart_id: cartId, qty: newQty },
            success: function(res) {
                if (res.success) {
                    $qty.text(res.new_quantity);
                    var price = parseFloat($item.find('.cart-item__price').data('price'));
                    $item.find('.cart-item__price').text('₽ ' + formatPrice(price * res.new_quantity));
                    updateSummaryItem(cartId, res.new_quantity, price);
                    recalcCartTotal();
                    updateCartBadge(res.cart_count);
                }
                $('.qty-btn').prop('disabled', false);
            },
            error: function() { $('.qty-btn').prop('disabled', false); }
        });
    });

    // ── Удаление товара ──────────────────────────────────────
    $(document).on('click', '.cart-item__remove', function() {
        var cartId = $(this).data('cart-id');
        var $item  = $(this).closest('.cart-item');

        $.ajax({
            url: '/api/cart/remove.php',
            method: 'POST',
            dataType: 'json',
            data: { cart_id: cartId },
            success: function(res) {
                if (res.success) {
                    $item.fadeOut(300, function() {
                        $(this).remove();
                        $('.cart-summary__item[data-cart-id="' + cartId + '"]').remove();
                        recalcCartTotal();
                        updateCartItemsCount();
                        updateCartBadge(res.cart_count);
                    });
                }
            }
        });
    });

    // ── Бургер-меню ──────────────────────────────────────────
    $(document).on('click', '#navToggle', function() {
        $('.nav-center').toggleClass('open');
        $('.header-icons').toggleClass('open');
        $(this).toggleClass('open');
    });

    $(document).on('click', '.nav-center .nav-link, .header-icons .icon-btn', function() {
        $('.nav-center, .header-icons, #navToggle').removeClass('open');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.nav-center, .header-icons, #navToggle').length) {
            $('.nav-center, .header-icons, #navToggle').removeClass('open');
        }
    });

});