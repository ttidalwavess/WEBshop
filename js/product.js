$(function () {

    // Выбор размера
    $(document).on('click', '.size-btn', function () {
        $('.size-btn').removeClass('active');
        $(this).addClass('active');
    });

    // Аккордеон
    $(document).on('click', '.accordion-trigger', function () {
        const $item = $(this).closest('.accordion-item');
        const $body = $item.find('.accordion-body');
        const $icon = $(this).find('.accordion-icon');
        const isOpen = $item.hasClass('open');

        // Закрываем все
        $('.accordion-item').removeClass('open');
        $('.accordion-body').css('max-height', '0');
        $('.accordion-icon').text('+');

        // Открываем кликнутый если был закрыт
        if (!isOpen) {
            $item.addClass('open');
            $body.css('max-height', $body[0].scrollHeight + 'px');
            $icon.text('−');
        }
    });

    // Кнопка «В корзину» — валидация выбора размера
    $(document).on('click', '.btn-add-cart', function () {
        if ($('.size-btn.active').length === 0) {
            // Подсвечиваем блок размеров
            $('.product-sizes__grid').addClass('shake');
            setTimeout(() => $('.product-sizes__grid').removeClass('shake'), 500);
            return;
        }
        // AJAX добавление — cart.js подхватит
    });

    // Избранное
    $(document).on('click', '.btn-wishlist', function () {
        $(this).toggleClass('active');
    });

});
