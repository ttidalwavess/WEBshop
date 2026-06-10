// assets/js/main.js — общий JS для публичной части
$(function () {

    // ── Мобильный гамбургер ──────────────────────────────────
    $('#navToggle').on('click', function () {
        $('.site-nav').toggleClass('site-nav--open');
        $(this).toggleClass('nav-toggle--active');
    });

    // ── Flash-сообщения: автоматически скрыть через 4 сек ────
    setTimeout(function () {
        $('.alert').fadeOut(400);
    }, 4000);

    // ── Подтверждение опасных действий ──────────────────────
    // Примечание: в админке confirm() вызывается через onsubmit.
    // Здесь — для любых кнопок/ссылок с data-confirm.
    $('[data-confirm]').on('click', function (e) {
        var msg = $(this).data('confirm') || 'Вы уверены?';
        if (!confirm(msg)) {
            e.preventDefault();
        }
    });

    // ── Превью изображения при выборе файла ──────────────────
    $('input[type="file"][accept*="image"]').on('change', function () {
        var file = this.files[0];
        if (!file) return;

        var $preview = $(this).siblings('.file-preview');
        if (!$preview.length) {
            $preview = $('<img class="file-preview">').insertAfter(this);
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            $preview.attr('src', e.target.result).show();
        };
        reader.readAsDataURL(file);
    });

});
