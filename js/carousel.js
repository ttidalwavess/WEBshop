document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.carousel-wrapper').forEach(function (wrapper) {
        var track   = wrapper.querySelector('.carousel-track');
        var btnPrev = wrapper.querySelector('.carousel-btn--prev');
        var btnNext = wrapper.querySelector('.carousel-btn--next');

        if (!track || !btnPrev || !btnNext) return;

        var origCards = Array.from(track.children);
        var total     = origCards.length;
        if (total === 0) return;

        function visibleCount() {
            var outer = wrapper.querySelector('.carousel-track-outer');
            if (!outer) return 1;
            var outerW = outer.offsetWidth;
            var cardW  = origCards[0].offsetWidth;
            var gap    = parseFloat(getComputedStyle(track).gap) || 32;
            return Math.max(1, Math.floor((outerW + gap) / (cardW + gap)));
        }

        function buildClones() {
            track.querySelectorAll('[data-clone]').forEach(function (el) { el.remove(); });

            var n = visibleCount();

            // Клоны в конец (первые N оригиналов)
            for (var i = 0; i < n; i++) {
                var clone = origCards[i % total].cloneNode(true);
                clone.dataset.clone = 'end';
                track.appendChild(clone);
            }
            // Клоны в начало (последние N оригиналов)
            for (var j = n - 1; j >= 0; j--) {
                var c = origCards[(total - 1 - (n - 1 - j) % total)].cloneNode(true);
                c.dataset.clone = 'start';
                track.insertBefore(c, track.firstChild);
            }

            return n;
        }

        var startClones   = buildClones();
        var currentIndex  = startClones;

        function cardWidth() {
            var gap = parseFloat(getComputedStyle(track).gap) || 32;
            return origCards[0].offsetWidth + gap;
        }

        function setPosition(noAnim) {
            track.style.transition = noAnim
                ? 'none'
                : 'transform 0.45s cubic-bezier(0.4, 0, 0.2, 1)';
            track.style.transform = 'translateX(-' + (currentIndex * cardWidth()) + 'px)';
        }

        setPosition(true);

        track.addEventListener('transitionend', function () {
            var n = visibleCount();
            if (currentIndex >= total + startClones) {
                currentIndex = startClones + (currentIndex - total - startClones) % total;
                setPosition(true);
            }
            if (currentIndex < startClones) {
                currentIndex = startClones + total - (startClones - currentIndex) % total;
                setPosition(true);
            }
        });

        btnNext.addEventListener('click', function () { currentIndex++; setPosition(false); });
        btnPrev.addEventListener('click', function () { currentIndex--; setPosition(false); });

        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                startClones  = buildClones();
                currentIndex = startClones;
                setPosition(true);
            }, 200);
        });
    });

});
