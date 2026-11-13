/* ── ACF Components – shared JS ─────────────────────────────────────────── */

/* Treatment Journey: Before/After drag slider */
(function () {
    'use strict';

    function initTjSlider(wrap) {
        var range  = wrap.querySelector('.tj-ba-range');
        var before = wrap.querySelector('.tj-ba-img--before');
        var div    = wrap.querySelector('.tj-ba-divisor');
        var handle = wrap.querySelector('.tj-ba-handle');

        if ( ! range || ! before ) { return; }

        function update(val) {
            var pct = val + '%';
            before.style.clipPath = 'inset(0 ' + (100 - val) + '% 0 0)';
            if ( div )    { div.style.left    = pct; }
            if ( handle ) { handle.style.left = pct; }
        }

        range.addEventListener('input', function () { update(this.value); });
        update(50);
    }

    document.querySelectorAll('[data-tj-slider]').forEach(initTjSlider);
})();