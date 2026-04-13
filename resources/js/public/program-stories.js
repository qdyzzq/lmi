// ─── Program Stories — Public JS ────────────────────────────────────────────
// No Blade data bridge needed — both functions receive data via HTML x-data attributes.

// ─── Hero Carousel ───────────────────────────────────────────────────────────

function publicCarousel(slides) {
    return {
        currentSlide: 0,
        slides: slides,
        autoplayInterval: null,
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.slides.length;
        },
        prevSlide() {
            this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
        },
        goToSlide(index) {
            this.currentSlide = index;
        },
        startAutoplay() {
            this.autoplayInterval = setInterval(() => {
                this.nextSlide();
            }, 5000);
        },
        stopAutoplay() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
            }
        },
    };
}

// ─── Stories Carousel ───────────────────────────────────────────────────────

function storiesCarousel(wrapperId, accentColor) {
    return {
        wrapperId,
        accentColor,
        trackId: wrapperId + '-track',
        currentPage: 0,
        totalPages: 1,
        PER_PAGE: 5,
        _resizeHandler: null,

        init() {
            this.$nextTick(() => {
                this.recalc();
                this._resizeHandler = () => this.recalc();
                window.addEventListener('resize', this._resizeHandler);

                const wrapper = document.getElementById(this.wrapperId + '-wrapper');
                if (wrapper) {
                    let _wheelLocked = false;
                    wrapper.addEventListener('wheel', (e) => {
                        if (this.totalPages <= 1) return;
                        const isScrollingDown = e.deltaY > 0;
                        const atStart = this.currentPage === 0;
                        const atEnd = this.currentPage >= this.totalPages - 1;
                        if ((isScrollingDown && atEnd) || (!isScrollingDown && atStart)) return;
                        e.preventDefault();
                        if (_wheelLocked) return;
                        _wheelLocked = true;
                        setTimeout(() => {
                            _wheelLocked = false;
                        }, 500);
                        if (isScrollingDown) {
                            this.next();
                        } else {
                            this.prev();
                        }
                    }, {
                        passive: false
                    });
                }
            });
        },

        destroy() {
            if (this._resizeHandler) {
                window.removeEventListener('resize', this._resizeHandler);
                this._resizeHandler = null;
            }
        },

        recalc() {
            const track = document.getElementById(this.trackId);
            if (!track) return;

            // Responsive cards per page
            const w = window.innerWidth;
            this.PER_PAGE = w < 481 ? 1 : w < 768 ? 2 : w < 1024 ? 3 : 5;

            const cards = track.querySelectorAll('.story-card-slide');
            const total = cards.length;
            this.totalPages = Math.max(1, Math.ceil(total / this.PER_PAGE));
            if (this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1;
            }
            const outerWidth = track.parentElement.offsetWidth;
            const gap = 12;
            const cardWidth = (outerWidth - gap * (this.PER_PAGE - 1)) / this.PER_PAGE;
            cards.forEach(card => {
                card.style.flex = `0 0 ${cardWidth}px`;
                card.style.width = `${cardWidth}px`;
            });
            this.slide();
        },

        slide() {
            const track = document.getElementById(this.trackId);
            if (!track) return;
            const outerWidth = track.parentElement.offsetWidth;
            const gap = 12;
            const cardWidth = (outerWidth - gap * (this.PER_PAGE - 1)) / this.PER_PAGE;
            const pageWidth = this.PER_PAGE * cardWidth + (this.PER_PAGE - 1) * gap + gap;
            track.style.transform = `translateX(-${this.currentPage * pageWidth}px)`;
        },

        prev() {
            if (this.currentPage > 0) {
                this.currentPage--;
                this.slide();
            }
        },
        next() {
            if (this.currentPage < this.totalPages - 1) {
                this.currentPage++;
                this.slide();
            }
        },
        goTo(page) {
            this.currentPage = page;
            this.slide();
        },
    };
}

// ─── Global Exports ─────────────────────────────────────────────────────────
// Both are called from x-data in the blade HTML — must be on window.
window.publicCarousel  = publicCarousel;
window.storiesCarousel = storiesCarousel;