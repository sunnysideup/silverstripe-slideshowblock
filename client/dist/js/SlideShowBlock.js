/**
 * Slide show block
 *
 * Under 48rem: slides stack and this script does nothing.
 * From 48rem: a scroll-snap deck with dots, previous/next buttons, arrow keys,
 * a progress bar, a counter and a fade-in for the active slide.
 *
 * Slide types can hook in through two events, fired on the slide <li>:
 *   slideshow:activate    a slide became the visible one
 *   slideshow:deactivate  it stopped being the visible one
 * Both bubble, so one listener on document is enough:
 *   document.addEventListener('slideshow:activate', (e) => { … e.target … });
 */
(() => {
    const DECK_QUERY = window.matchMedia('(min-width: 48rem)');
    const REDUCED_MOTION = window.matchMedia('(prefers-reduced-motion: reduce)');

    class SlideShowBlock {
        #root;
        #track;
        #slides;
        #dots = [];
        #prev;
        #next;
        #counter;
        #progress;
        #observer;
        #index = 0;
        #on = false;

        constructor(root) {
            this.#root = root;
            this.#track = root.querySelector('[data-track]');
            this.#slides = this.#track ? [...this.#track.children] : [];
            this.#prev = root.querySelector('[data-prev]');
            this.#next = root.querySelector('[data-next]');
            this.#counter = root.querySelector('[data-counter]');
            this.#progress = root.querySelector('[data-progress]');

            if (this.#slides.length < 2) {
                return;
            }

            this.#buildDots(root.querySelector('[data-dots]'));
            this.#prev?.addEventListener('click', () => this.go(this.#index - 1));
            this.#next?.addEventListener('click', () => this.go(this.#index + 1));
            this.#track.addEventListener('keydown', (event) => this.#onKey(event));

            this.#observer = new IntersectionObserver((entries) => {
                for (const entry of entries) {
                    if (this.#on && entry.isIntersecting) {
                        this.#update(this.#slides.indexOf(entry.target));
                    }
                }
            }, { root: this.#track, threshold: 0.6 });

            const sync = () => (DECK_QUERY.matches ? this.#enable() : this.#disable());
            DECK_QUERY.addEventListener('change', sync);
            sync();
        }

        go(index) {
            const target = Math.max(0, Math.min(index, this.#slides.length - 1));
            this.#track.scrollTo({
                left: this.#slides[target].offsetLeft,
                behavior: REDUCED_MOTION.matches ? 'instant' : 'smooth',
            });
        }

        #enable() {
            if (this.#on) {
                return;
            }
            this.#on = true;
            this.#root.classList.add('is-enhanced');
            this.#root.setAttribute('aria-roledescription', 'carousel');
            this.#track.tabIndex = 0;
            this.#track.setAttribute('aria-label', 'Slides: use the arrow keys to move between them');
            this.#slides.forEach((slide, n) => {
                slide.setAttribute('aria-roledescription', 'slide');
                slide.setAttribute('aria-label', `${n + 1} of ${this.#slides.length}`);
                this.#observer.observe(slide);
            });
            this.#track.scrollTo({ left: this.#slides[this.#index].offsetLeft, behavior: 'instant' });
            this.#update(this.#index, true);
        }

        #disable() {
            this.#on = false;
            this.#observer.disconnect();
            this.#root.classList.remove('is-enhanced');
            this.#root.removeAttribute('aria-roledescription');
            this.#track.removeAttribute('tabindex');
            this.#track.removeAttribute('aria-label');
            this.#slides.forEach((slide) => {
                slide.inert = false;
                slide.classList.remove('is-active');
                slide.removeAttribute('aria-roledescription');
                slide.removeAttribute('aria-label');
            });
        }

        #buildDots(list) {
            if (!list) {
                return;
            }
            this.#slides.forEach((_, i) => {
                const item = document.createElement('li');
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'slideshow__dot';
                button.setAttribute('aria-label', `Go to slide ${i + 1}`);
                button.addEventListener('click', () => this.go(i));
                item.append(button);
                list.append(item);
                this.#dots.push(button);
            });
        }

        #onKey(event) {
            if (!this.#on || event.target !== this.#track) {
                return;
            }
            const step = { ArrowRight: 1, ArrowDown: 1, PageDown: 1, ArrowLeft: -1, ArrowUp: -1, PageUp: -1 }[event.key];
            if (step) {
                event.preventDefault();
                this.go(this.#index + step);
            } else if (event.key === 'Home') {
                event.preventDefault();
                this.go(0);
            } else if (event.key === 'End') {
                event.preventDefault();
                this.go(this.#slides.length - 1);
            }
        }

        #update(index, force = false) {
            if (index < 0 || (index === this.#index && !force)) {
                return;
            }
            const previous = this.#slides[this.#index];
            this.#index = index;
            const total = this.#slides.length;

            this.#slides.forEach((slide, n) => {
                slide.classList.toggle('is-active', n === index);
                slide.inert = n !== index;
            });
            this.#dots.forEach((dot, n) => dot.setAttribute('aria-current', String(n === index)));
            if (this.#prev) this.#prev.disabled = index === 0;
            if (this.#next) this.#next.disabled = index === total - 1;
            if (this.#counter) this.#counter.textContent = `Slide ${index + 1} of ${total}`;
            if (this.#progress) this.#progress.style.width = `${((index + 1) / total) * 100}%`;

            if (previous && previous !== this.#slides[index]) {
                previous.dispatchEvent(new CustomEvent('slideshow:deactivate', { bubbles: true }));
            }
            this.#slides[index].dispatchEvent(new CustomEvent('slideshow:activate', { bubbles: true, detail: { index } }));
        }
    }

    const init = () => {
        document.querySelectorAll('[data-slideshow]').forEach((element) => {
            if (!element.slideShowBlock) {
                element.slideShowBlock = new SlideShowBlock(element);
            }
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
