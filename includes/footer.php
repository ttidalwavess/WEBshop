<?php
if (!defined('ROOT')) die('Direct access forbidden');
?>

<footer class="footer">
    <div class="footer-container">

        <div class="footer-col">
            <h4>Социальные сети</h4>
            <ul>
                <li><a>Вконтакте</a></li>
                <li><a>Telegram</a></li>
                <li><a>Instagram</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Адреса магазинов</h4>
            <ul>
                <li>Владивосток, ул. Аллилуева, 12А</li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Контакты</h4>
            <ul>
                <li><a>+7 (495) 343-12-33</a></li>
                <li><a>+7 (495) 878-73-87</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Поддержка</h4>
            <ul>
                <li><a>Помощь покупателю</a></li>
                <li><a>Доставка и возврат</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Информация</h4>
            <ul>
                <li><a>Блог</a></li>
                <li><a>Вакансии</a></li>
            </ul>
        </div>

    </div>

    <div class="copyright">
        © <?= date('Y') ?> LIGHT — Светлая сторона стиля.
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

<script src="/assets/js/carousel.js"></script>
<script src="/assets/js/cart.js"></script>

<?php if (!empty($extra_js)): ?>
    <?php foreach ($extra_js as $js): ?>
        <script src="<?= htmlspecialchars($js) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>