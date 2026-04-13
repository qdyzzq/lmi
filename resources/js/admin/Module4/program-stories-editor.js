// =============================================================================
// FILE:    program-stories-editor.js
// PAGE:    Programs & Stories Editor (Admin)
// =============================================================================
// Blade values injected via window globals set inline before this file loads:
//   window._pesoInitData.directoryHasDraft    → $directoryHasDraft
//   window._pesoInitData.directoryChangelog   → $directoryChangelog
// =============================================================================


// ─── Carousel Section ────────────────────────────────────────────────────────
function carouselSection(slides) {
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
            this.autoplayInterval = setInterval(() => this.nextSlide(), 5000);
        },
        stopAutoplay() {
            clearInterval(this.autoplayInterval);
        },
    };
}


// ─── Core Functions (escapeText, storiesCarousel, adminPage) ─────────────────
/* ── Global reactive store for PESO directory draft state ── */
// FIX #11: Helper to safely escape user-supplied strings used in event details
// Prevents HTML injection if strings end up rendered via innerHTML elsewhere.
function escapeText(str) {
    const div = document.createElement('div');
    div.textContent = String(str ?? '');
    return div.innerHTML;
}

/* ══════════════════════════════════════════════════════
                   STORIES CAROUSEL — Alpine component factory
                   One independent instance per program accordion.
                ══════════════════════════════════════════════════════ */
function storiesCarousel(wrapperId, accentColor, programId) {
    return {
        wrapperId,
        accentColor,
        programId,
        trackId: wrapperId + '-track',
        currentPage: 0,
        totalPages: 1,
        PER_PAGE: 5,

        // Year range filter state
        yearFrom: '',
        yearTo: '',
        availableYears: [],
        isFiltering: false,

        init() {
            this.$nextTick(() => {
                this.recalc();
                this.loadYears();
                window.addEventListener('resize', () => this.recalc());

                // ── Scroll wheel → horizontal page navigation ──
                const wrapper = document.getElementById(this.wrapperId + '-wrapper');
                if (wrapper) {
                    let _wheelLocked = false;
                    wrapper.addEventListener('wheel', (e) => {
                        // Only intercept when there are multiple pages
                        if (this.totalPages <= 1) return;

                        const isScrollingDown = e.deltaY > 0;
                        const atStart = this.currentPage === 0;
                        const atEnd = this.currentPage >= this.totalPages - 1;

                        // If we're at a boundary in the scroll direction, let the
                        // page scroll normally so the user isn't trapped.
                        if ((isScrollingDown && atEnd) || (!isScrollingDown && atStart)) return;

                        // Otherwise hijack the scroll: move carousel page instead.
                        e.preventDefault();
                        if (_wheelLocked) return;
                        _wheelLocked = true;
                        setTimeout(() => {
                            _wheelLocked = false;
                        }, 500); // debounce

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

        async loadYears() {
            if (!this.programId) return;
            try {
                const res = await fetch(`/admin/stories/years?program_id=${this.programId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                this.availableYears = data.years ?? [];
            } catch (e) {
                this.availableYears = [];
            }
        },

        filterByYear() {
            this.isFiltering = true;

            if (this.yearFrom && this.yearTo) {
                const from = parseInt(this.yearFrom);
                const to = parseInt(this.yearTo);
                if (from > to) {
                    this.yearTo = this.yearFrom;
                }
            }

            const track = document.getElementById(this.trackId);
            if (!track) {
                this.isFiltering = false;
                return;
            }

            // ── Filter visibility ──
            const cards = track.querySelectorAll('.story-card-slide');
            const from = this.yearFrom ? parseInt(this.yearFrom) : null;
            const to = this.yearTo ? parseInt(this.yearTo) : null;

            cards.forEach(card => {
                if (!from && !to) {
                    card.style.display = '';
                } else {
                    const year = parseInt(card.dataset.storyYear);
                    const inRange = (!from || year >= from) && (!to || year <= to);
                    card.style.display = inRange ? '' : 'none';
                }
            });

            // ── Sort visible story cards ascending by year (oldest first) ──
            this.sortCardsByYearAsc(track);

            this.currentPage = 0;
            this.recalc();
            this.isFiltering = false;
        },

        sortCardsByYearAsc(track) {
            if (!track) return;
            // Grab all story cards and sort by data-story-year ASC
            const storyCards = Array.from(track.querySelectorAll('.story-card-slide'));
            const addSlot = track.querySelector('.story-add-slot');

            storyCards.sort((a, b) => {
                const ya = parseInt(a.dataset.storyYear) || 0;
                const yb = parseInt(b.dataset.storyYear) || 0;
                return ya - yb; // ascending: oldest first
            });

            // Re-insert sorted cards before the add-slot (or at end if no slot)
            storyCards.forEach(card => {
                if (addSlot) {
                    track.insertBefore(card, addSlot);
                } else {
                    track.appendChild(card);
                }
            });
        },

        clearFilter() {
            this.yearFrom = '';
            this.yearTo = '';
            this.filterByYear();
        },

        exportStories() {
            const track = document.getElementById(this.trackId);
            if (!track) return;

            // Collect only visible cards (respects active year filter)
            const cards = Array.from(track.querySelectorAll('.story-card-slide'))
                .filter(c => c.style.display !== 'none');

            if (cards.length === 0) {
                if (window.showToast) showToast('No stories to export.', 'error');
                return;
            }

            const headers = ['Story Title', 'Year', 'Link', 'Program'];

            const csvContent = [
                headers.join(','),
                ...cards.map(card => [
                    `"${(card.dataset.storyTitle  || '').replace(/"/g, '""')}"`,
                    card.dataset.storyYear || '',
                    `"${(card.dataset.storyLink   || '').replace(/"/g, '""')}"`,
                    `"${(card.dataset.programName || '').replace(/"/g, '""')}"`,
                ].join(','))
            ].join('\n');

            const blob = new Blob([csvContent], {
                type: 'text/csv'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;

            // Descriptive filename that includes the year range when filtered
            let filename = 'success-stories';
            if (this.yearFrom && this.yearTo) filename += `-${this.yearFrom}-${this.yearTo}`;
            else if (this.yearFrom) filename += `-from-${this.yearFrom}`;
            else if (this.yearTo) filename += `-to-${this.yearTo}`;
            a.download = filename + '.csv';

            a.click();
            window.URL.revokeObjectURL(url);
        },

        recalc() {
            const track = document.getElementById(this.trackId);
            if (!track) return;

            // Only count visible cards for pagination
            const cards = Array.from(track.querySelectorAll('.story-card-slide, .story-add-slot'))
                .filter(c => c.style.display !== 'none');
            const total = cards.length;
            this.totalPages = Math.max(1, Math.ceil(total / this.PER_PAGE));

            // Clamp currentPage after possible DOM changes
            if (this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1;
            }

            // Set card widths dynamically so 5 fit perfectly
            const outerWidth = track.parentElement.offsetWidth;
            const gap = 12;
            const cardWidth = (outerWidth - gap * (this.PER_PAGE - 1)) / this.PER_PAGE;

            // Size ALL cards (including hidden) so layout is consistent when filter changes
            track.querySelectorAll('.story-card-slide, .story-add-slot').forEach(card => {
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
            const pageWidth = this.PER_PAGE * cardWidth + (this.PER_PAGE - 1) * gap +
                gap; // +gap for the gap after last card

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

/* ══════════════════════════════════════════════════════
   ADMIN PAGE — main Alpine component
══════════════════════════════════════════════════════ */
function adminPage() {
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    async function jsonRequest(method, url, body = {}) {
        try {
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body),
            });
            if (!res.ok) {
                try {
                    const errBody = await res.json();
                    const msg = errBody.message ||
                        Object.values(errBody.errors || {})[0]?.[0] ||
                        `Server error (${res.status}). Please try again.`;
                    return {
                        success: false,
                        message: msg
                    };
                } catch {
                    return {
                        success: false,
                        message: `Server error (${res.status}). Please try again.`
                    };
                }
            }
            return res.json();
        } catch (e) {
            return {
                success: false,
                message: 'Network error. Please check your connection.'
            };
        }
    }

    async function formRequest(method, url, data = {}) {
        const fd = new FormData();
        if (method === 'PUT') {
            fd.append('_method', 'PUT');
            method = 'POST';
        }
        for (const [k, v] of Object.entries(data)) {
            if (v !== null && v !== undefined) fd.append(k, v);
        }
        try {
            const res = await fetch(url, {
                method,
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                },
                body: fd,
            });
            if (!res.ok) {
                try {
                    const errBody = await res.json();
                    const msg = errBody.message ||
                        Object.values(errBody.errors || {})[0]?.[0] ||
                        `Server error (${res.status}). Please try again.`;
                    return {
                        success: false,
                        message: msg
                    };
                } catch {
                    return {
                        success: false,
                        message: `Server error (${res.status}). Please try again.`
                    };
                }
            }
            return res.json();
        } catch (e) {
            return {
                success: false,
                message: 'Network error. Please check your connection.'
            };
        }
    }

    return {
        modal: {
            open: false,
            type: null,
            title: '',
            id: null,
            programId: null,
            programName: '',
            endpoint: null,
            data: null,
            loading: false,
            error: null
        },
        fieldErrors: {},
        form: {},
        formErrors: {},

        openModal(detail) {
            const titles = {
                'add-slide': 'Add Carousel Slide',
                'edit-slide': 'Edit Carousel Slide',
                'delete-slide': 'Delete Slide',
                'add-program': 'Add New Program',
                'edit-program': 'Edit Program',
                'delete-program': 'Delete Program',
                'publish-program': 'Publish Program',
                'unpublish-program': 'Unpublish Program',
                'republish-program': 'Republish Program',
                'edit-description': 'Edit Program Description',
                'add-qualification': 'Add Item',
                'edit-qualification': 'Edit Item',
                'add-step': 'Add Step',
                'edit-step': 'Edit Step',
                'add-story': 'Add Success Story',
                'edit-story': 'Edit Success Story',
                'add-testimonial': 'Add Testimonial',
                'edit-testimonial': 'Edit Testimonial',
                'delete-item': 'Delete Item',
                'edit-cta': 'Edit CTA Section',
                'add-peso': 'Add PESO / JPO Office',
                'edit-peso': 'Edit PESO / JPO Office',
                'delete-peso': 'Delete PESO / JPO Office',
                'publish-directory': 'Publish PESO / JPO Directory',
            };
            this.modal = {
                open: true,
                type: detail.type,
                title: titles[detail.type] ?? 'Edit',
                id: detail.id ?? null,
                programId: detail.programId ?? null,
                programName: detail.programName ?? '',
                endpoint: detail.endpoint ?? null,
                data: detail.data ?? null,
                changes: detail.changes ?? [],
                loading: false,
                error: null
            };
            this.formErrors = {};
            this.form = detail.data ? {
                ...detail.data
            } : {};
            if (detail.data?.defaultType) this.form.type = detail.data.defaultType;
            if (detail.type === 'edit-slide' && detail.data) {
                this.form.program_label = detail.data.program ?? '';
                this.form.image_preview = null;
            }

            if (['add-program', 'edit-program'].includes(detail.type)) {
                this.$nextTick(() => this.initQuill('quill-program', 'quill-program-wordcount', 'description'));
            }
            if (detail.type === 'edit-description') {
                this.$nextTick(() => this.initQuill('quill-description', 'quill-description-wordcount',
                    'description'));
            }
            if (['add-slide', 'edit-slide'].includes(detail.type)) {
                this.$nextTick(() => this.initQuill('quill-excerpt', 'quill-excerpt-wordcount', 'excerpt'));
            }
            if (['add-qualification', 'edit-qualification'].includes(detail.type)) {
                this.$nextTick(() => this.initQuill('quill-qualification', 'quill-qualification-wordcount',
                    'content'));
            }
            if (['add-step', 'edit-step'].includes(detail.type)) {
                this.$nextTick(() => this.initQuill('quill-step', 'quill-step-wordcount', 'content'));
            }
            if (['add-testimonial', 'edit-testimonial'].includes(detail.type)) {
                this.$nextTick(() => this.initQuill('quill-quote', 'quill-quote-wordcount', 'quote'));
            }
        },

        initQuill(editorId, wordCountId, formField) {
            if (!window._quillSizeRegistered) {
                const SizeStyle = Quill.import('attributors/style/size');
                SizeStyle.whitelist = ['8pt', '10pt', '11pt', '12pt', '14pt', '16pt', '18pt', '24pt', '36pt'];
                Quill.register(SizeStyle, true);
                window._quillSizeRegistered = true;
            }
            const el = document.getElementById(editorId);
            if (!el) return;
            if (!window._quillInstances) window._quillInstances = {};
            let quill = window._quillInstances[editorId];
            if (!quill) {
                quill = new Quill('#' + editorId, {
                    theme: 'snow',
                    placeholder: 'Enter text...',
                    modules: {
                        toolbar: [
                            [{
                                font: []
                            }, {
                                size: ['8pt', '10pt', '11pt', '12pt', '14pt', '16pt', '18pt',
                                    '24pt', '36pt'
                                ]
                            }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{
                                color: []
                            }, {
                                background: []
                            }],
                            [{
                                header: [1, 2, 3, false]
                            }],
                            [{
                                align: []
                            }],
                            [{
                                list: 'ordered'
                            }, {
                                list: 'bullet'
                            }],
                            ['link', 'clean'],
                        ]
                    }
                });
                window._quillInstances[editorId] = quill;
            }
            quill.off('text-change');
            quill.root.innerHTML = this.form[formField] || '';
            const updateWordCount = () => {
                const text = quill.root.innerText.trim();
                const count = text ? text.split(/\s+/).filter(w => w.length > 0).length : 0;
                const wc = document.getElementById(wordCountId);
                if (wc) wc.textContent = count;
            };
            updateWordCount();
            quill.on('text-change', () => {
                this.form[formField] = quill.root.innerHTML;
                updateWordCount();
            });
        },

        done() {
            this.modal.open = false;

            // Slide actions: refresh only the carousel
            const slideTypes = ['add-slide', 'edit-slide', 'delete-slide'];
            // Whole-list actions: refresh the full programs container
            const wholeListTypes = [
                'add-program', 'edit-program', 'delete-program',
                'publish-program', 'unpublish-program', 'republish-program',
            ];
            // Body-only actions: refresh just the open program's accordion body
            const bodyOnlyTypes = [
                'edit-description', 'delete-item',
                'add-qualification', 'edit-qualification',
                'add-step', 'edit-step',
                'add-story', 'edit-story',
                'add-testimonial', 'edit-testimonial',
            ];

            const type = this.modal.type;
            const programId = this.modal.programId ?? null;

            if (slideTypes.includes(type)) {
                setTimeout(() => refreshCarousel(), 150);
            } else if (bodyOnlyTypes.includes(type) && programId) {
                setTimeout(() => refreshProgramBody(programId), 150);
            } else {
                setTimeout(() => refreshProgramsContainer(), 150);
            }
        },
        fail(msg) {
            this.modal.loading = false;
            showToast(msg || 'Something went wrong. Please try again.', 'error');
        },
        showSuccess(title, message) {
            this.modal.open = false;
            this.modal.loading = false;
            this.fieldErrors = {};
            window.dispatchEvent(new CustomEvent('show-success-modal', {
                detail: {
                    title,
                    message
                }
            }));
        },
        clearFieldErrors() {
            this.fieldErrors = {};
            document.querySelectorAll('.field-error-highlight').forEach(el => {
                el.classList.remove('border-red-400', 'ring-2', 'ring-red-200', 'field-error-highlight');
            });
        },

        // Validates a list of {key, label, check} rules.
        // Marks all failing fields in formErrors and fires a warning
        // toast naming the first empty field. Returns true if all pass.
        validateFields(rules) {
            this.formErrors = {};
            for (const rule of rules) {
                if (!rule.check) this.formErrors[rule.key] = true;
            }
            const failed = rules.filter(r => !r.check);
            if (failed.length === 0) return true;
            const label = failed[0].label;
            showToast(
                failed.length === 1 ?
                `"${label}" is required. Please fill it in before saving.` :
                `"${label}" is required — and ${failed.length - 1} other field${failed.length > 2 ? 's are' : ' is'} also empty.`,
                'warning'
            );
            return false;
        },

        async submitSlide() {
            const isEdit = this.modal.type === 'edit-slide';
            const rules = [{
                    key: 'title',
                    label: 'Story Title',
                    check: !!this.form.title?.trim()
                },
                {
                    key: 'excerpt',
                    label: 'Short Excerpt',
                    check: !!this.form.excerpt?.trim() && this.form.excerpt !== '<p><br></p>'
                },
                {
                    key: 'program_label',
                    label: 'Program Label',
                    check: !!this.form.program_label?.trim()
                },
                ...(!isEdit ? [{
                    key: 'image',
                    label: 'Slide Image',
                    check: !!this.form.image
                }] : []),
            ];
            if (!this.validateFields(rules)) return;
            this.modal.loading = true;
            this.modal.error = null;
            const data = {
                title: this.form.title,
                excerpt: this.form.excerpt,
                program_label: this.form.program_label,
                color: this.form.color,
                link: this.form.link || null,
            };
            if (this.form.image) data.image = this.form.image;
            const res = await formRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/admin/carousel/${this.modal.id}` :
                '/admin/carousel', data);
            res.success ? this.done() : this.fail(res.message);
        },

        async submitProgram() {
            const rules = [{
                    key: 'name',
                    label: 'Program Name',
                    check: !!this.form.name?.trim()
                },
                {
                    key: 'color',
                    label: 'Theme Color',
                    check: !!this.form.color
                },
            ];
            if (!this.validateFields(rules)) return;
            this.modal.loading = true;
            this.modal.error = null;
            const isEdit = this.modal.type === 'edit-program';
            const data = {
                name: this.form.name,
                acronym: this.form.acronym || null,
                subtitle: this.form.subtitle,
                description: this.form.description,
                color: this.form.color
            };
            if (this.form.logo) data.logo = this.form.logo;
            const res = await formRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/admin/programs/${this.modal.id}` :
                '/admin/programs', data);
            res.success ? this.done() : this.fail(res.message);
        },

        async submitDescription() {
            this.modal.loading = true;
            this.modal.error = null;
            const res = await jsonRequest('PUT', `/admin/programs/${this.modal.id}/description`, {
                description: this.form.description
            });
            res.success ? this.done() : this.fail(res.message);
        },

        async submitQualification() {
            const isBlankContent = !this.form.content?.trim() || this.form.content === '<p><br></p>';
            const rules = [{
                key: 'content',
                label: 'Content',
                check: !isBlankContent
            }, ];
            if (!this.validateFields(rules)) return;
            this.modal.loading = true;
            this.modal.error = null;
            const isEdit = this.modal.type === 'edit-qualification';
            const tmp = document.createElement('div');
            tmp.innerHTML = this.form.content || '';
            const listItems = tmp.querySelectorAll('li');
            if (!isEdit && listItems.length > 1) {
                for (const li of listItems) {
                    const res = await jsonRequest('POST', '/admin/qualifications', {
                        type: this.form.type,
                        content: li.innerHTML,
                        program_id: this.modal.programId
                    });
                    if (!res.success) {
                        this.fail(res.message);
                        return;
                    }
                }
                this.done();
                return;
            }
            let content = this.form.content || '';
            if (tmp.children.length === 1 && tmp.children[0].tagName === 'P') content = tmp.children[0]
                .innerHTML;
            const body = {
                type: this.form.type,
                content
            };
            if (!isEdit) body.program_id = this.modal.programId;
            const res = await jsonRequest(isEdit ? 'PUT' : 'POST', isEdit ?
                `/admin/qualifications/${this.modal.id}` :
                '/admin/qualifications', body);
            res.success ? this.done() : this.fail(res.message);
        },

        async submitStep() {
            const isBlankStep = !this.form.content?.trim() || this.form.content === '<p><br></p>';
            const rules = [{
                key: 'content',
                label: 'Step Content',
                check: !isBlankStep
            }, ];
            if (!this.validateFields(rules)) return;
            this.modal.loading = true;
            this.modal.error = null;
            const isEdit = this.modal.type === 'edit-step';
            const body = {
                content: this.form.content,
                link: this.form.link || null
            };
            if (!isEdit) body.program_id = this.modal.programId;
            const res = await jsonRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/admin/steps/${this.modal.id}` :
                '/admin/steps', body);
            res.success ? this.done() : this.fail(res.message);
        },

        async submitStory() {
            const isAddStory = this.modal.type === 'add-story';
            const rules = [{
                    key: 'title',
                    label: 'Story Title',
                    check: !!this.form.title?.trim()
                },
                {
                    key: 'story_year',
                    label: 'Year',
                    check: !!this.form.story_year
                },
                ...(isAddStory ? [{
                    key: 'image',
                    label: 'Thumbnail Image',
                    check: !!this.form.image
                }] : []),
            ];
            if (!this.validateFields(rules)) return;
            this.modal.loading = true;
            this.modal.error = null;
            const isEdit = this.modal.type === 'edit-story';
            const data = {
                title: this.form.title,
                link: this.form.link,
                story_year: this.form.story_year ?? null
            };
            if (!isEdit) data.program_id = this.modal.programId;
            if (this.form.image) data.image = this.form.image;
            const res = await formRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/admin/stories/${this.modal.id}` :
                '/admin/stories', data);
            res.success ? this.done() : this.fail(res.message);
        },

        async submitTestimonial() {
            const rules = [{
                    key: 'quote',
                    label: 'Quote',
                    check: !!this.form.quote?.trim()
                },
                {
                    key: 'author_name',
                    label: 'Author Name',
                    check: !!this.form.author_name?.trim()
                },
            ];
            if (!this.validateFields(rules)) return;
            this.modal.loading = true;
            this.modal.error = null;
            const isEdit = this.modal.type === 'edit-testimonial';
            const body = {
                quote: this.form.quote,
                author_name: this.form.author_name,
                author_role: this.form.author_role
            };
            if (!isEdit) body.program_id = this.modal.programId;
            const res = await jsonRequest(isEdit ? 'PUT' : 'POST', isEdit ?
                `/admin/testimonials/${this.modal.id}` :
                '/admin/testimonials', body);
            res.success ? this.done() : this.fail(res.message);
        },

        async submitDelete() {
            this.modal.loading = true;
            this.modal.error = null;
            let url;
            if (this.modal.type === 'delete-slide') url = `/admin/carousel/${this.modal.id}`;
            else if (this.modal.type === 'delete-program') url = `/admin/programs/${this.modal.id}`;
            else url = this.modal.endpoint;
            const res = await jsonRequest('DELETE', url);
            if (res.success) {
                this.done();
                window.dispatchEvent(new CustomEvent('show-success-modal', {
                    detail: {
                        message: 'Item deleted successfully.'
                    }
                }));
            } else {
                this.fail(res.message);
            }
        },

        async submitTogglePublish() {
            this.modal.loading = true;
            this.modal.error = null;
            const res = await jsonRequest('PATCH', `/admin/programs/${this.modal.id}/toggle-publish`);
            res.success ? this.done() : this.fail(res.message);
        },

        async submitRepublish() {
            this.modal.loading = true;
            this.modal.error = null;
            const res = await jsonRequest('PATCH', `/admin/programs/${this.modal.id}/republish`);
            res.success ? this.done() : this.fail(res.message);
        },

        openCtaConfirm() {
            const rules = [{
                    key: 'title',
                    label: 'CTA Title',
                    check: !!this.form.title?.trim()
                },
                {
                    key: 'subtitle',
                    label: 'CTA Subtitle',
                    check: !!this.form.subtitle?.trim()
                },
            ];
            if (!this.validateFields(rules)) return;
            // Stash values, close edit modal, show plain-JS confirm modal
            window._ctaPending = {
                title: this.form.title.trim(),
                subtitle: this.form.subtitle.trim(),
            };
            this.modal.open = false;
            document.getElementById('ctaConfirmModal').classList.remove('hidden');
        },

        submitCta() {
            // No-op — submission is handled by ctaConfirmedSubmit() plain JS function
        },

        async submitFieldOffice() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const validEmail = !!this.form.email?.trim() && emailRegex.test(this.form.email.trim());
            const rules = [{
                    key: 'name',
                    label: 'Office Name',
                    check: !!this.form.name?.trim()
                },
                {
                    key: 'type',
                    label: 'Office Type',
                    check: !!this.form.type
                },
                {
                    key: 'province',
                    label: 'Province',
                    check: !!this.form.province?.trim()
                },
                {
                    key: 'manager',
                    label: 'Manager / Head Name',
                    check: !!this.form.manager?.trim()
                },
                {
                    key: 'email',
                    label: 'Email Address',
                    check: validEmail
                },
                {
                    key: 'address',
                    label: 'Address',
                    check: !!this.form.address?.trim()
                },
            ];
            if (!this.validateFields(rules)) return;
            this.modal.loading = true;
            this.modal.error = null;
            const isEdit = this.modal.type === 'edit-peso';
            const body = {
                name: this.form.name,
                office_type: this.form.type,
                province: this.form.province,
                manager_name: this.form.manager,
                email: this.form.email,
                address: this.form.address,
            };
            const res = await jsonRequest(
                isEdit ? 'PUT' : 'POST',
                isEdit ? `/admin/field-offices/${this.modal.id}` : '/admin/field-offices',
                body
            );
            if (res.success) {
                const prov = this.form.province;
                const pesoState = document.getElementById('peso-ajax-container')?._x_dataStack?.[0];
                if (pesoState) {
                    if (!pesoState.pesoData[prov]) pesoState.pesoData[prov] = [];
                    if (isEdit) {
                        const idx = pesoState.pesoData[prov].findIndex(e => e.id === this.modal.id);
                        if (idx !== -1) {
                            pesoState.pesoData[prov][idx] = {
                                ...pesoState.pesoData[prov][idx],
                                name: body.name,
                                type: body.office_type,
                                manager: body.manager_name,
                                email: body.email,
                                address: body.address,
                                id: this.modal.id,
                            };
                            pesoState.pesoData[prov] = [...pesoState.pesoData[prov]];
                        }
                    } else {
                        pesoState.pesoData[prov] = [
                            ...pesoState.pesoData[prov],
                            {
                                id: res.id ?? Date.now(),
                                name: body.name,
                                type: body.office_type,
                                manager: body.manager_name,
                                email: body.email,
                                address: body.address
                            }
                        ];
                    }
                }
                // Mark dirty via global store — triggers reactive button/banner
                Alpine.store('pesoDirectory').markDirty({
                    action: isEdit ? 'edited' : 'added',
                    label: body.name,
                    type: body.office_type,
                    province: prov,
                    time: new Date().toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    }),
                });
                // Persist dirty flag on server so button stays orange after refresh
                jsonRequest('POST', '/admin/field-offices/touch', {
                    action: isEdit ? 'edited' : 'added',
                    label: body.name,
                    type: body.office_type,
                    province: prov,
                    time: new Date().toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    }),
                }).catch(() => {});
                this.modal.open = false;
                window.dispatchEvent(new CustomEvent('show-success-modal', {
                    detail: {
                        title: isEdit ? 'Office Updated!' : 'Office Added!',
                        // FIX #11: escapeText() prevents HTML injection from user-typed names
                        message: isEdit ?
                            escapeText(body.name) + ' has been updated successfully.' : escapeText(
                                body.name) + ' has been added to ' + escapeText(prov) + '.'
                    }
                }));
            } else {
                this.fail(res.message);
            }
        },

        async destroyFieldOffice() {
            this.modal.loading = true;
            this.modal.error = null;
            const res = await jsonRequest('DELETE', `/admin/field-offices/${this.modal.id}`);
            if (res.success) {
                const pesoState = document.getElementById('peso-ajax-container')?._x_dataStack?.[0];
                let deletedName = 'Unknown',
                    deletedType = '',
                    deletedProv = '';
                if (pesoState) {
                    for (const prov in pesoState.pesoData) {
                        const found = pesoState.pesoData[prov].find(e => e.id === this.modal.id);
                        if (found) {
                            deletedName = found.name;
                            deletedType = found.type;
                            deletedProv = prov;
                            break;
                        }
                    }
                    for (const prov in pesoState.pesoData) {
                        pesoState.pesoData[prov] = pesoState.pesoData[prov].filter(e => e.id !== this.modal.id);
                    }
                }
                // Mark dirty via global store
                Alpine.store('pesoDirectory').markDirty({
                    action: 'deleted',
                    label: deletedName,
                    type: deletedType,
                    province: deletedProv,
                    time: new Date().toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    }),
                });
                // Persist dirty flag on server so button stays orange after refresh
                jsonRequest('POST', '/admin/field-offices/touch', {
                    action: 'deleted',
                    label: deletedName,
                    type: deletedType,
                    province: deletedProv,
                    time: new Date().toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    }),
                }).catch(() => {});
                this.modal.open = false;
                window.dispatchEvent(new CustomEvent('show-success-modal', {
                    detail: {
                        title: 'Office Deleted',
                        message: escapeText(deletedName) + ' has been removed from the directory.'
                    }
                }));
            } else {
                this.fail(res.message);
            }
        },

        async submitPublishDirectory() {
            this.modal.loading = true;
            this.modal.error = null;
            const res = await jsonRequest('POST', '/admin/field-offices/publish');
            if (res.success) {
                Alpine.store('pesoDirectory').reset();
                this.modal.open = false;
                window.dispatchEvent(new CustomEvent('show-success-modal', {
                    detail: {
                        title: 'Directory Published!',
                        message: 'The PESO / JPO Directory is now live and visible to the public.'
                    }
                }));
            } else {
                this.fail(res.message ?? 'Failed to publish directory.');
            }
        },
    };
}


// ─── AJAX Helpers, officeTypeSelector, showToast, CTA ───────────────────────
// ─── AJAX Refresh Helpers ────────────────────────────────────────────────

    // Refreshes only the accordion body of a specific program card.
    // Used for descriptions, qualifications, steps, stories, testimonials.
    function refreshProgramBody(programId) {
        const body = document.getElementById('program-body-' + programId);
        if (!body) {
            refreshProgramsContainer();
            return;
        }

        body.style.transition = 'opacity 0.1s';
        body.style.opacity = '0.6';
        body.style.pointerEvents = 'none';

        fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newBody = doc.getElementById('program-body-' + programId);
                if (newBody) {
                    body.innerHTML = newBody.innerHTML;
                    // Re-init Alpine on the new inner nodes (stories carousel,
                    // buttons, testimonials etc.) so they are fully reactive.
                    if (window.Alpine) Alpine.initTree(body);

                    // Re-load year dropdowns for any story carousels in this body
                    // (must run after Alpine.initTree so the component state exists)
                    body.querySelectorAll('[id$="-wrapper"]').forEach(wrapper => {
                        const alpine = wrapper._x_dataStack?.[0];
                        if (alpine && typeof alpine.loadYears === 'function') {
                            alpine.loadYears();
                        }
                    });
                }
                body.style.opacity = '1';
                body.style.pointerEvents = '';
            })
            .catch(() => {
                body.style.opacity = '1';
                body.style.pointerEvents = '';
                window.location.reload();
            });
    }

    // Refreshes just the carousel section (used after add/edit/delete slide).
    // Instead of replacing the DOM node (which breaks Alpine's existing instance,
    // leaks the autoplay interval, and causes double-init), we fetch the fresh
    // server-rendered page, parse the new slides JSON from the x-data attribute,
    // and directly mutate the live Alpine component's reactive `slides` array.
    // Alpine's reactivity system then updates the DOM automatically.
    function refreshCarousel() {
        const section = document.getElementById('carousel-section');
        if (!section) {
            window.location.reload();
            return;
        }

        section.style.transition = 'opacity 0.15s';
        section.style.opacity = '0.5';
        section.style.pointerEvents = 'none';

        fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newSection = doc.getElementById('carousel-section');

                if (newSection) {
                    // Parse the fresh slides array from the server-rendered x-data attribute.
                    // The attribute looks like: x-data="carouselSection([{...}, ...])"
                    const xDataAttr = newSection.getAttribute('x-data') || '';
                    const match = xDataAttr.match(/^carouselSection\(([\s\S]*)\)$/);

                    if (match) {
                        try {
                            const freshSlides = JSON.parse(match[1]);
                            // Mutate the LIVE Alpine instance — no DOM swap, no re-init.
                            const alpineData = section._x_dataStack?.[0];
                            if (alpineData) {
                                alpineData.slides = freshSlides;
                                // Clamp currentSlide to valid range after add/delete.
                                alpineData.currentSlide = Math.min(
                                    alpineData.currentSlide,
                                    Math.max(0, freshSlides.length - 1)
                                );
                            } else {
                                window.location.reload();
                                return;
                            }
                        } catch (e) {
                            window.location.reload();
                            return;
                        }
                    } else {
                        window.location.reload();
                        return;
                    }
                }

                section.style.opacity = '1';
                section.style.pointerEvents = '';
            })
            .catch(() => {
                section.style.opacity = '1';
                section.style.pointerEvents = '';
                window.location.reload();
            });
    }

    // Used for add/edit/delete program, publish toggle, carousel changes.
    function refreshProgramsContainer() {
        const container = document.getElementById('programs-ajax-container');
        if (!container) {
            window.location.reload();
            return;
        }

        // Remember which accordion is currently open so we can restore it after the DOM swap
        const currentOpenId = container._x_dataStack?.[0]?.openId ?? null;

        container.style.transition = 'opacity 0.1s';
        container.style.opacity = '0.6';
        container.style.pointerEvents = 'none';

        fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newContainer = doc.getElementById('programs-ajax-container');
                if (newContainer) {
                    container.innerHTML = newContainer.innerHTML;
                    // Re-init Alpine on new child nodes so accordions and
                    // inner components are fully reactive after the swap.
                    if (window.Alpine) Alpine.initTree(container);
                }
                container.style.opacity = '1';
                container.style.pointerEvents = '';

                // Re-open the accordion that was open before the swap
                if (currentOpenId !== null) {
                    // Wait for Alpine to initialise the new DOM nodes
                    setTimeout(() => {
                        if (container._x_dataStack?.[0]) {
                            container._x_dataStack[0].openId = currentOpenId;
                        }
                    }, 50);
                }
            })
            .catch(() => {
                container.style.opacity = '1';
                container.style.pointerEvents = '';
                window.location.reload();
            });
    }



    // ─── Office Type Selector ────────────────────────────────────────────────
    function officeTypeSelector() {
        return {
            types: [],
            mode: 'select', // 'select' | 'add' | 'edit' | 'delete'
            inputName: '',
            saving: false,
            typeError: '',

            async init() {
                try {
                    const res = await fetch('/admin/office-types', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (res.ok) this.types = await res.json();
                } catch (e) {
                    // fallback — admin can still add a type manually
                }
            },

            startEdit() {
                this.inputName = this.form ? '' : '';
                this.inputName = '';
                this.mode = 'edit';
            },

            async saveNewType(form) {
                this.typeError = '';
                const name = this.inputName.trim().toUpperCase();
                if (!name) {
                    this.typeError = 'Please enter a type name.';
                    return;
                }
                if (this.types.includes(name)) {
                    this.typeError = 'That type already exists.';
                    return;
                }

                this.saving = true;
                try {
                    const res = await fetch('/admin/office-types', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            name
                        }),
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        this.types.push(data.name);
                        this.types.sort();
                        form.type = data.name;
                        this.mode = 'select';
                        this.inputName = '';
                        window.dispatchEvent(new CustomEvent('office-type-added', {
                            detail: {
                                name: data.name
                            }
                        }));
                    } else {
                        this.typeError = data.message ?? 'Failed to save type.';
                    }
                } catch (e) {
                    this.typeError = 'Network error. Please try again.';
                }
                this.saving = false;
            },

            async updateType(form) {
                this.typeError = '';
                const oldName = form.type;
                const newName = this.inputName.trim().toUpperCase();
                if (!newName) {
                    this.typeError = 'Please enter a new name.';
                    return;
                }
                if (newName === oldName) {
                    this.mode = 'select';
                    return;
                }
                if (this.types.includes(newName)) {
                    this.typeError = 'That type already exists.';
                    return;
                }

                this.saving = true;
                try {
                    const res = await fetch('/admin/office-types/' + encodeURIComponent(oldName), {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            name: newName
                        }),
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        const idx = this.types.indexOf(oldName);
                        if (idx !== -1) this.types.splice(idx, 1, newName);
                        this.types.sort();
                        form.type = newName;
                        this.mode = 'select';
                        this.inputName = '';
                        window.dispatchEvent(new CustomEvent('office-type-renamed', {
                            detail: {
                                oldName,
                                newName
                            }
                        }));
                    } else {
                        this.typeError = data.message ?? 'Failed to rename type.';
                    }
                } catch (e) {
                    this.typeError = 'Network error. Please try again.';
                }
                this.saving = false;
            },

            async deleteType(form) {
                const name = form.type;
                this.saving = true;
                try {
                    const res = await fetch('/admin/office-types/' + encodeURIComponent(name), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        this.types = this.types.filter(t => t !== name);
                        form.type = '';
                        this.mode = 'select';
                        window.dispatchEvent(new CustomEvent('office-type-deleted', {
                            detail: {
                                name
                            }
                        }));
                        window.dispatchEvent(new CustomEvent('show-success-modal', {
                            detail: {
                                title: 'Type Deleted',
                                message: 'Office type "' + escapeText(name) + '" has been removed.'
                            }
                        }));
                    } else {
                        this.typeError = data.message ?? 'Failed to delete type.';
                        this.mode = 'select';
                    }
                } catch (e) {
                    this.typeError = 'Network error. Please try again.';
                    this.mode = 'select';
                }
                this.saving = false;
            },
        };
    }

    function showToast(message, type = 'error') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const configs = {
            error: {
                bg: 'bg-white',
                border: 'border-red-500',
                icon: `<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                labelColor: 'text-red-600',
                label: 'Error'
            },
            success: {
                bg: 'bg-white',
                border: 'border-green-500',
                icon: `<svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                labelColor: 'text-green-600',
                label: 'Success'
            },
            info: {
                bg: 'bg-white',
                border: 'border-blue-500',
                icon: `<svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                labelColor: 'text-blue-600',
                label: 'Info'
            },
            warning: {
                bg: 'bg-white',
                border: 'border-yellow-500',
                icon: `<svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`,
                labelColor: 'text-yellow-600',
                label: 'Warning'
            }
        };

        const c = configs[type] || configs.error;

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto w-full border-l-4 ${c.bg} rounded-xl shadow-xl overflow-hidden
transition-all duration-300 ease-out translate-x-full opacity-0`;
        toast.classList.add('toast-item');

        // FIX #2: Build toast DOM via safe DOM API instead of innerHTML
        // to prevent XSS when message comes from server error responses.
        const inner = document.createElement('div');
        inner.className = 'flex items-start gap-3 px-4 py-3.5';

        // Icon (static SVG — safe)
        const iconWrap = document.createElement('div');
        iconWrap.innerHTML = c.icon; // icon is a trusted static string, not user data
        inner.appendChild(iconWrap.firstChild);

        // Text block
        const textDiv = document.createElement('div');
        textDiv.className = 'flex-1 min-w-0';

        const labelEl = document.createElement('p');
        labelEl.className = `text-xs font-bold uppercase tracking-wide ${c.labelColor} mb-0.5`;
        labelEl.textContent = c.label; // safe — static string

        const msgEl = document.createElement('p');
        msgEl.className = 'text-sm text-slate-700 leading-snug';
        msgEl.textContent = message; // FIX: textContent prevents any HTML injection

        textDiv.appendChild(labelEl);
        textDiv.appendChild(msgEl);
        inner.appendChild(textDiv);

        // Close button
        const closeBtn = document.createElement('button');
        closeBtn.className =
            'flex-shrink-0 w-5 h-5 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition mt-0.5';
        closeBtn.innerHTML =
            `<svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
        closeBtn.addEventListener('click', () => toast.remove());
        inner.appendChild(closeBtn);

        toast.appendChild(inner);

        // Progress bar
        const progress = document.createElement('div');
        progress.className = `toast-progress h-1 ${c.border.replace('border-', 'bg-')} w-full origin-left`;
        toast.appendChild(progress);

        container.appendChild(toast);

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            });
        });

        const duration = type === 'error' ? 5000 : 3500;
        if (progress) {
            progress.style.transition = `transform ${duration}ms linear`;
            requestAnimationFrame(() => {
                progress.style.transform = 'scaleX(0)';
            });
        }

        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    function closectaConfirmModal() {
        document.getElementById('ctaConfirmModal').classList.add('hidden');
    }

    async function publishCta() {
        const cta = Alpine.$data(document.getElementById('cta-section-root'));
        if (!cta) return;
        cta.ctaPublishing = true;

        try {
            const res = await fetch('/admin/cta-section/publish', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
            const data = await res.json();
            if (data.success) {
                cta.ctaHasDraft = false;
                cta.ctaIsPublished = true;
                const successEl = document.getElementById('ctaSuccessModal');
                const alpineData = successEl?._x_dataStack?.[0];
                if (alpineData) {
                    alpineData.title = 'CTA Published!';
                    alpineData.message = 'The CTA section is now live and visible to the public.';
                    alpineData.open = true;
                }
            } else {
                showToast(data.message ?? 'Publish failed. Please try again.', 'error');
            }
        } catch (err) {
            showToast('An error occurred. Please try again.', 'error');
        }

        cta.ctaPublishing = false;
    }

    async function ctaConfirmedSubmit() {
        const pending = window._ctaPending;
        if (!pending) return;
        try {
            const res = await fetch('/admin/cta-section', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(pending),
            });
            const data = await res.json();
            if (data.success) {
                // Directly mutate Alpine state — use server-confirmed has_draft value
                const ctaRoot = Alpine.$data(document.getElementById('cta-section-root'));
                if (ctaRoot) {
                    ctaRoot.ctaTitle = pending.title;
                    ctaRoot.ctaSubtitle = pending.subtitle;
                    ctaRoot.ctaHasDraft = data.has_draft ?? true;
                }
                // Also dispatch event as fallback
                window.dispatchEvent(new CustomEvent('cta-updated', {
                    detail: {
                        title: pending.title,
                        subtitle: pending.subtitle
                    }
                }));
                // Directly set Alpine state on the success modal and open it
                const successEl = document.getElementById('ctaSuccessModal');
                const alpineData = successEl._x_dataStack?.[0];
                if (alpineData) {
                    alpineData.title = 'CTA Saved!';
                    alpineData.message =
                        'Changes saved. Click \'Publish to Public\' or \'Update Published\' to make them live.';
                    alpineData.open = true;
                }
            } else {
                showToast(data.message ?? 'Something went wrong. Please try again.', 'error');
            }
        } catch (err) {
            showToast('An error occurred. Please try again.', 'error');
        }
        window._ctaPending = null;
    }


// ─── Global Exports (Vite wraps modules — expose to window for x-data & onclick) ──
// Must come BEFORE alpine:init so Alpine finds these when it boots.
window.carouselSection          = carouselSection;
window.storiesCarousel          = storiesCarousel;
window.adminPage                = adminPage;
window.officeTypeSelector       = officeTypeSelector;
window.escapeText               = escapeText;
window.showToast                = showToast;
window.closectaConfirmModal     = closectaConfirmModal;
window.ctaConfirmedSubmit       = ctaConfirmedSubmit;
window.publishCta               = publishCta;
window.refreshProgramBody       = refreshProgramBody;
window.refreshCarousel          = refreshCarousel;
window.refreshProgramsContainer = refreshProgramsContainer;


// ─── Alpine Store Init ────────────────────────────────────────────────────────
document.addEventListener('alpine:init', () => {
    Alpine.store('pesoDirectory', {
        hasDraftChanges: window._pesoInitData?.directoryHasDraft ?? false,
        changeLog: window._pesoInitData?.directoryChangelog ?? [],
        markDirty(entry) {
            this.hasDraftChanges = true;
            this.changeLog.push(entry);
        },
        reset() {
            this.hasDraftChanges = false;
            this.changeLog = [];
        },
    });

    Alpine.data('pesoCard', (id) => ({
        entryId: id,
    }));
});