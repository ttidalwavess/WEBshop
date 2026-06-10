// assets/js/admin.js — JS для панели администратора
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

    // ── Подсветка строки при наведении ───────────────────────
    $('.admin-table tbody tr').on('mouseenter', function () {
        $(this).addClass('row--hover');
    }).on('mouseleave', function () {
        $(this).removeClass('row--hover');
    });

});
