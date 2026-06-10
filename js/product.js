/**
 * product.js — логика страницы карточки товара
 * Подключается только на /product.php через $extra_js = ['/js/product.js']
 * Требует jQuery.
 */
$(function () {

    // ── Переключение главного фото при клике на миниатюру ────
    $(document).on('click', '.product-gallery__thumb', function () {
        var newSrc = $(this).data('src');
        $('#main-product-img').attr('src', newSrc);
        $('.product-gallery__thumb').removeClass('product-gallery__thumb--active');
        $(this).addClass('product-gallery__thumb--active');
    });

    // ── Выбор размера ────────────────────────────────────────
    $(document).on('click', '.size-btn', function () {
        $('.size-btn').removeClass('active');
        $(this).addClass('active');
    });

    // ── Аккордеон ────────────────────────────────────────────
    $(document).on('click', '.accordion-trigger', function () {
        var $item  = $(this).closest('.accordion-item');
        var $body  = $item.find('.accordion-body');
        var $icon  = $(this).find('.accordion-icon');
        var isOpen = $item.hasClass('open');

        // Закрываем все
        $('.accordion-item').removeClass('open');
        $('.accordion-body').css('max-height', '0');
        $('.accordion-icon').text('+');

        // Открываем кликнутый, если он был закрыт
        if (!isOpen) {
            $item.addClass('open');
            $body.css('max-height', $body[0].scrollHeight + 'px');
            $icon.text('−');
        }
    });

    // ── Кнопка «В корзину» — проверка выбора размера ────────
    // Работает только для товаров с размерными кнопками (одежда, сумки).
    // Если кнопок размера нет на странице — проверка пропускается.
    $(document).on('click', '.btn-add-cart', function () {
        var hasSizeBtns = $('.size-btn').length > 0;
        if (hasSizeBtns && $('.size-btn.active').length === 0) {
            // Тряска блока размеров — подсказка пользователю
            $('.product-sizes__grid').addClass('shake');
            setTimeout(function () {
                $('.product-sizes__grid').removeClass('shake');
            }, 500);
            return false; // не даём cart.js отправить запрос
        }
        // Дальше подхватывает cart.js (делегирование на .btn-add-to-cart)
    });

    // ── Избранное ────────────────────────────────────────────
    $(document).on('click', '.btn-wishlist', function () {
        $(this).toggleClass('active');
    });

});
