/**
 * Before / After Slider – carousel (mouse-drag) + range-input compare
 */
(function () {
    'use strict';

    document.querySelectorAll('.component-ba-slider').forEach(function (section) {
        var track  = section.querySelector('.ba-track');
        var slides = Array.from(section.querySelectorAll('.ba-slide'));
        var prev   = section.querySelector('.ba-nav--prev');
        var next   = section.querySelector('.ba-nav--next');

        if (!track || slides.length === 0) return;

        var current         = 0;
        var isDragging       = false;
        var dragOccurred     = false;
        var isHandleDragging = false; // true while handle drag is active
        var dragStartX       = 0;
        var dragStartTX      = 0; // translateX at drag start
        var currentTX        = 0; // live translateX

        /* ── Carousel: snap to slide ──────────────────── */
        function goTo(index) {
            if (index < 0) index = slides.length - 1;
            if (index >= slides.length) index = 0;
            current = index;

            slides.forEach(function (s) { s.classList.remove('is-active'); });
            slides[current].classList.add('is-active');

            // Center the active slide inside the carousel viewport
            var carousel  = track.parentElement;
            var slideEl   = slides[current];
            var offset    = slideEl.offsetLeft - (carousel.offsetWidth / 2) + (slideEl.offsetWidth / 2);
            currentTX     = -offset;
            track.style.transition = 'transform 0.35s ease';
            track.style.transform  = 'translateX(' + currentTX + 'px)';
        }

        /* ── Mouse-drag to scroll carousel ───────────── */
        track.style.cursor = 'grab';

        track.addEventListener('mousedown', function (e) {
            // Ignore if clicking on the before/after handle
            if (e.target.closest('.ba-handle')) return;
            isDragging  = true;
            dragStartX  = e.clientX;
            dragStartTX = currentTX;
            track.style.transition = 'none';
            track.style.cursor     = 'grabbing';
            e.preventDefault();
        });

        document.addEventListener('mousemove', function (e) {
            if (!isDragging) return;
            if (isHandleDragging) return; // handle drag takes priority
            var dx    = e.clientX - dragStartX;
            currentTX = dragStartTX + dx;
            track.style.transform = 'translateX(' + currentTX + 'px)';
        });

        document.addEventListener('mouseup', function (e) {
            if (!isDragging) return;
            isDragging         = false;
            track.style.cursor = 'grab';
            var dx = e.clientX - dragStartX;
            if (Math.abs(dx) > 10) {
                dragOccurred = true;
                goTo(current + (dx < 0 ? 1 : -1));
            } else {
                dragOccurred = false;
                goTo(current); // snap back to current
            }
        });

        // Keep prev/next buttons working as fallback
        if (prev) prev.addEventListener('click', function () { goTo(current - 1); });
        if (next) next.addEventListener('click', function () { goTo(current + 1); });

        /* ── Click on slide to navigate ──────────────── */
        slides.forEach(function (slide, i) {
            slide.addEventListener('click', function (e) {
                if (e.target.closest('.ba-handle')) return;
                if (dragOccurred) { dragOccurred = false; return; }
                goTo(i);
            });
        });

        /* ── Touch swipe ──────────────────────────────── */
        var touchStartX = 0;
        track.addEventListener('touchstart', function (e) {
            touchStartX = e.touches[0].clientX;
        }, { passive: true });
        track.addEventListener('touchend', function (e) {
            var dx = e.changedTouches[0].clientX - touchStartX;
            if (Math.abs(dx) > 40) {
                goTo(current + (dx < 0 ? 1 : -1));
            }
        });

        // Start centered on third slide (index 2)
        goTo(slides.length > 2 ? 2 : slides.length > 1 ? 1 : 0);

        /* ── Before/After handle drag ─────────────────── */
        slides.forEach(function (slide) {
            var divisor   = slide.querySelector('.ba-divisor');
            var handle    = slide.querySelector('.ba-handle');
            var beforeImg = slide.querySelector('.ba-img--before');
            var figure    = slide.querySelector('figure');
            if (!divisor || !handle || !beforeImg || !figure) return;

            var thisHandleDragging = false; // per-slide flag

            function applyPosition(clientX) {
                var rect = figure.getBoundingClientRect();
                var val  = Math.max(0, Math.min(100, ((clientX - rect.left) / rect.width) * 100));
                handle.style.left        = val + '%';
                divisor.style.left       = val + '%';
                beforeImg.style.clipPath = 'inset(0 ' + (100 - val) + '% 0 0)';
            }

            // Set initial 50% position
            handle.style.left        = '50%';
            divisor.style.left       = '50%';
            beforeImg.style.clipPath = 'inset(0 50% 0 0)';

            /* Mouse drag from handle – active slide only */
            handle.addEventListener('mousedown', function (e) {
                if (!slide.classList.contains('is-active')) return;
                thisHandleDragging = true;
                isHandleDragging   = true;  // block carousel drag
                isDragging         = false;
                e.preventDefault();
                e.stopPropagation();
            });

            document.addEventListener('mousemove', function (e) {
                if (!thisHandleDragging) return;
                applyPosition(e.clientX);
            });

            document.addEventListener('mouseup', function () {
                if (thisHandleDragging) {
                    thisHandleDragging = false;
                    isHandleDragging   = false;
                }
            });

            /* Touch drag from handle – active slide only */
            handle.addEventListener('touchstart', function (e) {
                if (!slide.classList.contains('is-active')) return;
                thisHandleDragging = true;
                isHandleDragging   = true;
                e.stopPropagation();
            }, { passive: true });

            document.addEventListener('touchmove', function (e) {
                if (!thisHandleDragging) return;
                applyPosition(e.touches[0].clientX);
            }, { passive: true });

            document.addEventListener('touchend', function () {
                if (thisHandleDragging) {
                    thisHandleDragging = false;
                    isHandleDragging   = false;
                }
            });
        });
    });
})();
