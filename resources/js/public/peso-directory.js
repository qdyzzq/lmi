// ─── PESO Directory — Public JS ─────────────────────────────────────────────
// Blade PHP values are injected via window._pesoDirectoryData (set inline in the blade).

// ─── Carousel ────────────────────────────────────────────────────────────────

            function pesoPhotoCarousel(images) {
                return {
                    slides: images,
                    current: 0,
                    autoplayTimer: null,

                    next() {
                        this.current = (this.current + 1) % this.slides.length;
                    },
                    prev() {
                        this.current = (this.current - 1 + this.slides.length) % this.slides.length;
                    },
                    goTo(i) {
                        this.current = i;
                    },
                    startAutoplay() {
                        this.autoplayTimer = setInterval(() => this.next(), 5000);
                    },
                    stopAutoplay() {
                        if (this.autoplayTimer) {
                            clearInterval(this.autoplayTimer);
                            this.autoplayTimer = null;
                        }
                    },
                };
            }

// ─────────────────────────────────────────────────────────────────────────────

            const _pesoDataset = window._pesoDirectoryData.pesoJson;

            document.addEventListener('alpine:init', () => {
                Alpine.data('pesoDirectory', () => ({
                    pesoData: _pesoDataset,
                    province: '',
                    officeType: '',
                    showType: false,
                    showResults: false,
                    search: '',
                    _fuseCache: {},
                    _filteredCache: null,
                    _filteredCacheKey: '',

                    typeColor(type, part) {
                        const map = {
                            'PESO': {
                                main: '#16a34a',
                                bg: '#f0fdf4',
                                border: '#bbf7d0'
                            },
                            'JPO': {
                                main: '#2563eb',
                                bg: '#eff6ff',
                                border: '#bfdbfe'
                            },
                        };
                        return (map[type] ?? {
                            main: '#6366f1',
                            bg: '#eef2ff',
                            border: '#c7d2fe'
                        })[part];
                    },

                    get officeTypes() {
                        const all = Object.values(this.pesoData ?? {}).flat();
                        return [...new Set(all.map(e => e.type).filter(Boolean))].sort();
                    },

                    selectProvince(val) {
                        this.province = val;
                        this.officeType = '';
                        this.showResults = false;
                        this.showType = !!val;
                        this.search = '';
                        this._fuseCache = {};
                        this._filteredCache = null;
                        if (val) this.$nextTick(() =>
                            this.$refs.typeSection?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'nearest'
                            })
                        );
                    },

                    selectType(t) {
                        this.officeType = t;
                        this.showResults = !!t;
                        this.search = '';
                        this._filteredCache = null;
                        if (t) this.$nextTick(() =>
                            this.$refs.resultsSection?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'nearest'
                            })
                        );
                    },

                    countFor(province, type) {
                        const entries = this.pesoData?.[province] ?? [];
                        return type === 'ALL' ? entries.length : entries.filter(e => e.type === type)
                            .length;
                    },

                    filteredEntries() {
                        const cacheKey = this.province + '|' + this.officeType + '|' + this.search;
                        if (this._filteredCache !== null && this._filteredCacheKey === cacheKey)
                            return this._filteredCache;

                        let entries = this.pesoData?.[this.province] ?? [];
                        if (this.officeType !== 'ALL') entries = entries.filter(e => e.type === this
                            .officeType);

                        let result;
                        if (!this.search.trim()) {
                            result = entries;
                        } else {
                            const fuseCacheKey = this.province + '|' + this.officeType;
                            if (!this._fuseCache[fuseCacheKey] || this._fuseCache[fuseCacheKey]._list !==
                                entries) {
                                this._fuseCache[fuseCacheKey] = new Fuse(entries, {
                                    keys: [{
                                        name: 'name',
                                        weight: 0.6
                                    }, {
                                        name: 'persons_name',
                                        weight: 0.3
                                    }, {
                                        name: 'type',
                                        weight: 0.1
                                    }],
                                    threshold: 0.4,
                                    distance: 200,
                                    minMatchCharLength: 2,
                                    includeScore: true,
                                });
                                this._fuseCache[fuseCacheKey]._list = entries;
                            }
                            result = this._fuseCache[fuseCacheKey].search(this.search.trim()).map(r => r
                                .item);
                        }

                        this._filteredCache = result;
                        this._filteredCacheKey = cacheKey;
                        return result;
                    },
                }));
            });

// ─── Global Exports ─────────────────────────────────────────────────────────
// pesoPhotoCarousel is used in x-data="pesoPhotoCarousel(...)" in the blade HTML.
// pesoDirectory (Alpine.data) is registered inside alpine:init — no window. export needed.
window.pesoPhotoCarousel = pesoPhotoCarousel;