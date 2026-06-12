// admin/assets/js/admin.js

$(function () {

    // ── Flash-сообщения ──────────────────────────────────────
    setTimeout(function () { $('.alert').fadeOut(400); }, 5000);

    // ── Превью файла ─────────────────────────────────────────
    $('input[type="file"][accept*="image"]').on('change', function () {
        var file = this.files[0];
        if (!file) return;
        var $preview = $(this).closest('.form-group').find('.file-preview');
        if (!$preview.length) {
            $preview = $('<img class="file-preview admin-file-preview">').insertAfter(this);
        }
        var reader = new FileReader();
        reader.onload = function (e) { $preview.attr('src', e.target.result).show(); };
        reader.readAsDataURL(file);
    });

    // ── Мобильное меню (гамбургер) ───────────────────────────
    var $sidebar  = $('#admin-sidebar');
    var $overlay  = $('#sidebar-overlay');
    var $burger   = $('#sidebar-toggle');

    function openSidebar() {
        $sidebar.addClass('is-open');
        $overlay.css('display', 'block');
        // небольшая задержка чтобы transition сработал
        setTimeout(function () { $overlay.addClass('is-visible'); }, 10);
        $burger.addClass('is-open');
        $('body').css('overflow', 'hidden');
    }

    function closeSidebar() {
        $sidebar.removeClass('is-open');
        $overlay.removeClass('is-visible');
        $burger.removeClass('is-open');
        $('body').css('overflow', '');
        // убрать display после анимации
        setTimeout(function () {
            if (!$overlay.hasClass('is-visible')) {
                $overlay.css('display', 'none');
            }
        }, 260);
    }

    $burger.on('click', function (e) {
        e.stopPropagation();
        $sidebar.hasClass('is-open') ? closeSidebar() : openSidebar();
    });

    $overlay.on('click', function () {
        closeSidebar();
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeSidebar();
    });

    // закрывать при клике на ссылку в мобильном меню
    $sidebar.find('.admin-nav__item').on('click', function () {
        closeSidebar();
    });

});