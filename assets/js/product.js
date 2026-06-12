/**
 * product.js — логика страницы карточки товара
 */
$(function () {
    // Переключение главного фото
    $(document).on('click', '.product-gallery__thumb', function () {
        var newSrc = $(this).data('src');
        $('#main-product-img').attr('src', newSrc);
        $('.product-gallery__thumb').removeClass('active');
        $(this).addClass('active');
    });

    // Выбор размера
    $(document).on('click', '.size-btn', function () {
        $('.size-btn').removeClass('active');
        $(this).addClass('active');
    });

    // Аккордеон
    $(document).on('click', '.accordion-trigger', function () {
        var $item = $(this).closest('.accordion-item');
        var $body = $item.find('.accordion-body');
        var $icon = $(this).find('.accordion-icon');
        var isOpen = $item.hasClass('open');

        $('.accordion-item').removeClass('open');
        $('.accordion-body').css('max-height', '0');
        $('.accordion-icon').text('+');

        if (!isOpen) {
            $item.addClass('open');
            $body.css('max-height', $body[0].scrollHeight + 'px');
            $icon.text('−');
        }
    });
});