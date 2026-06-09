/**
 * carousel.js — псевдо-бесконечная карусель
 * Техника: клонируем карточки в начало и конец ленты.
 * Когда доходим до клонов — мгновенно (без анимации) прыгаем
 * на оригинал, и пользователь ничего не замечает.
 *
 * Без jQuery, чистый JS. Работает для любого числа каруселей на странице.
 */

document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.carousel-wrapper').forEach(function (wrapper) {
        const track    = wrapper.querySelector('.carousel-track');
        const btnPrev  = wrapper.querySelector('.carousel-btn--prev');
        const btnNext  = wrapper.querySelector('.carousel-btn--next');

        if (!track || !btnPrev || !btnNext) return;

        // Получаем оригинальные карточки
        const origCards = Array.from(track.children);
        const total     = origCards.length;
        if (total === 0) return;

        // Считаем сколько карточек видно одновременно
        function visibleCount() {
            const outerW = wrapper.querySelector('.carousel-track-outer').offsetWidth;
            const cardW  = origCards[0].offsetWidth;
            const gap    = parseFloat(getComputedStyle(track).gap) || 32;
            return Math.max(1, Math.floor((outerW + gap) / (cardW + gap)));
        }

        // Клонируем по N карточек с каждой стороны (N = visible)
        function buildClones() {
            // Удаляем старые клоны
            track.querySelectorAll('[data-clone]').forEach(el => el.remove());

            const n = visibleCount();

            // Клоны в конец (первые N оригиналов)
            for (let i = 0; i < n; i++) {
                const clone = origCards[i % total].cloneNode(true);
                clone.dataset.clone = 'end';
                track.appendChild(clone);
            }
            // Клоны в начало (последние N оригиналов)
            for (let i = n - 1; i >= 0; i--) {
                const clone = origCards[(total - 1 - (n - 1 - i) % total)].cloneNode(true);
                clone.dataset.clone = 'start';
                track.insertBefore(clone, track.firstChild);
            }

            return n; // сколько клонов вставили в начало = начальный offset
        }

        let startClones = buildClones();
        let currentIndex = startClones; // начинаем с первого оригинала

        function cardWidth() {
            const gap = parseFloat(getComputedStyle(track).gap) || 32;
            return origCards[0].offsetWidth + gap;
        }

        // Перемещаем ленту без анимации
        function setPosition(noAnim) {
            if (noAnim) {
                track.style.transition = 'none';
            } else {
                track.style.transition = 'transform 0.45s cubic-bezier(0.4, 0, 0.2, 1)';
            }
            track.style.transform = 'translateX(-' + (currentIndex * cardWidth()) + 'px)';
        }

        setPosition(true);

        // После каждой анимации проверяем не попали ли на клон
        track.addEventListener('transitionend', function () {
            const allCards = Array.from(track.children);
            const n = visibleCount();

            // Зашли в клоны в конце — прыгаем на начало оригиналов
            if (currentIndex >= total + startClones) {
                currentIndex = startClones + (currentIndex - total - startClones) % total;
                setPosition(true);
            }
            // Зашли в клоны в начале — прыгаем на конец оригиналов
            if (currentIndex < startClones) {
                currentIndex = startClones + total - (startClones - currentIndex) % total;
                setPosition(true);
            }
        });

        btnNext.addEventListener('click', function () {
            currentIndex++;
            setPosition(false);
        });

        btnPrev.addEventListener('click', function () {
            currentIndex--;
            setPosition(false);
        });

        // Пересчитываем при ресайзе
        let resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                startClones = buildClones();
                currentIndex = startClones;
                setPosition(true);
            }, 200);
        });
    });

});