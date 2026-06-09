<?php
if (!defined('ROOT')) die('Direct access forbidden');
?>

<footer class="footer">
    <div class="footer-container">

        <!-- Соцсети -->
        <div class="footer-col">
            <h4>Социальные сети</h4>
            <ul>
                <li><a href="#" target="_blank" rel="noopener">Вконтакте</a></li>
                <li><a href="#" target="_blank" rel="noopener">Telegram</a></li>
                <li><a href="#" target="_blank" rel="noopener">Instagram</a></li>
            </ul>
        </div>

        <!-- Адреса -->
        <div class="footer-col">
            <h4>Адреса магазинов</h4>
            <ul>
                <li>Владивосток, ул. Аллилуева, 12А</li>
            </ul>
        </div>

        <!-- Контакты -->
        <div class="footer-col">
            <h4>Контакты</h4>
            <ul>
                <li><a href="tel:+74953431233">+7 (495) 343-12-33</a></li>
                <li><a href="tel:+74958787387">+7 (495) 878-73-87</a></li>
            </ul>
        </div>

        <!-- Поддержка -->
        <div class="footer-col">
            <h4>Поддержка</h4>
            <ul>
                <li><a href="/about.php#help">Помощь покупателю</a></li>
                <li><a href="/about.php#delivery">Доставка и возврат</a></li>
            </ul>
        </div>

        <!-- Информация -->
        <div class="footer-col">
            <h4>Информация</h4>
            <ul>
                <li><a href="#">Блог</a></li>
                <li><a href="#">Вакансии</a></li>
            </ul>
        </div>

    </div>

    <div class="copyright">
        © <?= date('Y') ?> LIGHT — Светлая сторона стиля.
    </div>
</footer>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

<script src="/js/carousel.js"></script>
<script src="/js/cart.js"></script>

<?php if (!empty($extra_js)): ?>
    <?php foreach ($extra_js as $js): ?>
        <script src="<?= htmlspecialchars($js) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>