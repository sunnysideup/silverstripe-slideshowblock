/**
 * Proof slide: counts the big number up (e.g. 0 → 20+) the first time it
 * comes into view. Works in both the stacked and the deck layout.
 * Loaded because SlideShowBlockSlideProof::$has_js = true.
 */
(() => {
    const REDUCED_MOTION = window.matchMedia('(prefers-reduced-motion: reduce)');

    const countUp = (element) => {
        const text = element.textContent.trim();
        const match = text.match(/^(\D*)(\d+)(.*)$/s);
        if (!match || REDUCED_MOTION.matches) {
            return;
        }
        const [, before, digits, after] = match;
        const target = Number.parseInt(digits, 10);
        const duration = 1200;
        const start = performance.now();

        const frame = (now) => {
            const t = Math.min(1, (now - start) / duration);
            const eased = 1 - (1 - t) ** 3;
            element.textContent = `${before}${Math.round(target * eased)}${after}`;
            if (t < 1) {
                requestAnimationFrame(frame);
            }
        };
        element.textContent = `${before}0${after}`;
        requestAnimationFrame(frame);
    };

    const init = () => {
        const observer = new IntersectionObserver((entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting) {
                    observer.unobserve(entry.target);
                    countUp(entry.target);
                }
            }
        }, { threshold: 0.6 });

        document.querySelectorAll('.slideshow-slide--proof [data-count-up]').forEach((element) => {
            // This script can be loaded twice (module bundle + theme bundle).
            // A second run would read the "0+" the first one just wrote and
            // count to 0, so only ever bind an element once.
            if (element.dataset.countUpBound) {
                return;
            }
            element.dataset.countUpBound = '1';
            observer.observe(element);
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
