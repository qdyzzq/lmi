// ─── Analysis Template Editor ─────────────────────────────────────────────────
function analysisEditor() {
    return {
        loading: true,
        saving: false,
        viewMode: 'edit',

        // ── Selection ──
        selectedYear:    null,
        selectedMonth:   null,
        availableYears:  [],
        availableMonths: [],
        quarterLabels:   {},  // { 1: "January", 4: "April", 7: "July", 10: "October" }

        activeField: null,

        // ── Modals ──
        showResetModal: false,

        // ── Submission state ──
        pendingSubmission:     null,   // { submitted_by, submitted_at } — first-time pending
        pendingEditSubmission: null,   // { submitted_by, submitted_at } — edit on top of published
        publishedExists:       false,  // true if already published for this year+month
        publishedIsEdited:     false,  // true if the published version is an approved admin edit

        showSaveModal:    false,
        showSuccessModal: false,
        showErrorModal:   false,
        successTitle:     '',
        successMessage:   '',
        errorTitle:       '',
        errorMessage:     '',

        // ── Templates ──
        templates: {
            employment:      '',
            underemployment: '',
            unemployment:    '',
            lfpr:            ''
        },

        // Snapshot of templates as loaded from server (for diff)
        originalTemplates: {
            employment:      '',
            underemployment: '',
            unemployment:    '',
            lfpr:            ''
        },

        fieldLabels: {
            employment:      'Employment Rate',
            underemployment: 'Underemployment Rate',
            unemployment:    'Unemployment Rate',
            lfpr:            'Participation Rate'
        },

        // Diff result populated before opening submit modal
        templateDiffs: [],

        validation: {
            employment:      { valid: true, missing: [] },
            underemployment: { valid: true, missing: [] },
            unemployment:    { valid: true, missing: [] },
            lfpr:            { valid: true, missing: [] }
        },

        allPlaceholders: [
            { key: '{current_period}',  icon: 'cal'   },
            { key: '{previous_period}', icon: 'cal'   },
            { key: '{current_rate}',    icon: 'chart' },
            { key: '{previous_rate}',   icon: 'chart' },
            { key: '{trend}',           icon: 'trend' }
        ],

        requiredPlaceholders: [
            '{current_period}', '{previous_period}',
            '{current_rate}',   '{previous_rate}',
            '{trend}'
        ],

        // ── Preview data (real data from database) ──
        previewData:    {},
        hasPreviewData: false,
        loadingPreview: false,

        // ── Computed ──
        get currentPeriodLabel() {
            const name = this.quarterLabels[this.selectedMonth] || '';
            return name ? `${name} ${this.selectedYear}` : '—';
        },

        // ── Init ──
        async init() {
            await this.loadTemplates();
        },

        // ── Load ──
        async loadTemplates() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.selectedYear)  params.set('year',  this.selectedYear);
                if (this.selectedMonth) params.set('month', this.selectedMonth);

                const res  = await fetch('/api/analysis-templates?' + params.toString());
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const json = await res.json();

                if (json.success) {
                    this.availableYears  = json.years          || [];
                    this.availableMonths = json.months         || [];
                    this.quarterLabels   = json.quarter_labels || {};
                    this.selectedYear    = json.selected_year;
                    this.selectedMonth   = json.selected_month;

                    Object.keys(this.templates).forEach(k => this.templates[k] = '');
                    Object.keys(json.data).forEach(k => {
                        if (this.templates.hasOwnProperty(k)) {
                            this.templates[k] = json.data[k].template_text || '';
                        }
                    });
                    // Snapshot originals for diff on submit
                    Object.keys(this.templates).forEach(k => {
                        this.originalTemplates[k] = this.templates[k];
                    });
                    this.validateAll();

                    // Load preview data and submission status
                    await Promise.all([this.loadPreviewData(), this.loadSubmissionStatus()]);
                }
            } catch (e) {
                console.error('Load error:', e);
                this.errorTitle     = 'Loading Error';
                this.errorMessage   = 'An error occurred while loading the templates. Please refresh and try again.';
                this.showErrorModal = true;
            } finally {
                this.loading = false;
            }
        },

        // Year changed → reload everything
        async onYearChange() {
            await this.loadTemplates();
        },

        // ── Load pending/published status for current year+month ──
        async loadSubmissionStatus() {
            try {
                const params = new URLSearchParams({ year: this.selectedYear, month: this.selectedMonth });
                const res  = await fetch('/api/analysis-templates/pending-show?' + params.toString());
                if (!res.ok) return;
                const json = await res.json();
                if (json.success) {
                    this.pendingSubmission     = json.pending            || null;
                    this.pendingEditSubmission = json.pending_edit        || null;
                    this.publishedExists       = json.published_exists    || false;
                    this.publishedIsEdited     = json.published_is_edited || false;
                }
            } catch (e) {
                console.error('Error loading submission status:', e);
            }
        },

        // ── Load real preview data from database ──
        async loadPreviewData() {
            if (!this.selectedYear || !this.selectedMonth) return;

            this.loadingPreview = true;
            try {
                const params = new URLSearchParams({
                    year:  this.selectedYear,
                    month: this.selectedMonth
                });

                const r = await fetch(`/api/analysis-templates/preview-data?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!r.ok) {
                    console.warn('No preview data available for this period');
                    this.hasPreviewData = false;
                    return;
                }

                const json = await r.json();

                if (json.success && json.has_data) {
                    this.previewData    = json.data;
                    this.hasPreviewData = true;
                } else {
                    this.hasPreviewData = false;
                }
            } catch (e) {
                console.error('Preview data error:', e);
                this.hasPreviewData = false;
            } finally {
                this.loadingPreview = false;
            }
        },

        // ── Insert placeholder ──
        insertAtCursor(placeholder) {
            const key      = this.activeField || 'employment';
            const textarea = document.getElementById('textarea-' + key);
            if (!textarea) return;

            const start = textarea.selectionStart;
            const end   = textarea.selectionEnd;
            const text  = this.templates[key];

            this.templates[key] = text.substring(0, start) + placeholder + text.substring(end);

            this.$nextTick(() => {
                textarea.focus();
                const pos = start + placeholder.length;
                textarea.setSelectionRange(pos, pos);
                this.validateTemplate(key);
            });
        },

        // ── Validation ──
        onInput(key) { this.validateTemplate(key); },

        validateTemplate(key) {
            const text    = this.templates[key] || '';
            const missing = this.requiredPlaceholders.filter(p => !text.includes(p));
            this.validation[key] = { valid: missing.length === 0, missing };
        },

        validateAll() {
            Object.keys(this.templates).forEach(k => this.validateTemplate(k));
        },

        hasValidationErrors() {
            return Object.values(this.validation).some(v => !v.valid);
        },

        // ── Preview ──
        renderPreview(text, key) {
            if (!text || !text.trim()) return '<span class="text-slate-300 italic">No content</span>';

            let out = text;

            // Use real preview data if available for this metric
            if (this.hasPreviewData && this.previewData[key]) {
                Object.entries(this.previewData[key]).forEach(([placeholder, value]) => {
                    out = out.replaceAll(placeholder, value);
                });
            } else {
                // Fallback to mock data
                const mockData = this.getMockData();
                Object.entries(mockData).forEach(([k, v]) => { out = out.replaceAll(k, v); });
            }

            return out;
        },

        // ── Mock data for preview (fallback when no real data) ──
        getMockData() {
            const currentName = this.quarterLabels[this.selectedMonth] || 'January';
            const prev        = this.getPreviousPeriod(this.selectedMonth, this.selectedYear);
            const prevName    = this.quarterLabels[prev.month] || 'October';

            return {
                '{current_period}':  `<strong>${currentName} ${this.selectedYear}</strong>`,
                '{previous_period}': `<strong>${prevName} ${prev.year}</strong>`,
                '{current_rate}':    '<strong>89.0%</strong>',
                '{previous_rate}':   '<strong>99.0%</strong>',
                '{trend}':           '<span class="text-red-600 font-semibold">lower ↓</span>'
            };
        },

        // Get previous period (Jan→Jan prev year, others→prev quarter)
        getPreviousPeriod(month, year) {
            // January compares to January of previous year (annual data)
            if (month == 1) {
                return { month: 1, year: parseInt(year) - 1 };
            }

            // Other quarters compare to previous quarter in same year
            const map = {
                4:  { month: 1, yearOffset:  0 },  // April   → January
                7:  { month: 4, yearOffset:  0 },  // July    → April
                10: { month: 7, yearOffset:  0 }   // October → July
            };
            const prev = map[month] || { month: 1, yearOffset: -1 };
            return { month: prev.month, year: parseInt(year) + prev.yearOffset };
        },

        // ── Save ──
        saveAll() {
            if (this.hasValidationErrors()) {
                this.errorTitle     = 'Validation Errors';
                this.errorMessage   = 'Please fix validation errors before saving.';
                this.showErrorModal = true;
                return;
            }

            // Build diff
            this.templateDiffs = [];
            Object.keys(this.templates).forEach(k => {
                const before = (this.originalTemplates[k] || '').trim();
                const after  = (this.templates[k] || '').trim();
                if (before !== after) {
                    this.templateDiffs.push({
                        key:   k,
                        label: this.fieldLabels[k] || k,
                        before,
                        after,
                        isNew: before === ''
                    });
                }
            });

            this.showSaveModal = true;
        },

        async confirmSave() {
            this.showSaveModal = false;
            this.saving = true;

            try {
                const r = await fetch('/api/analysis-templates/submit', {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        year:      this.selectedYear,
                        month:     this.selectedMonth,
                        templates: this.templates,
                    })
                });

                const json = await r.json();

                if (json.success) {
                    // Update snapshot so subsequent edits diff correctly
                    Object.keys(this.templates).forEach(k => {
                        this.originalTemplates[k] = this.templates[k];
                    });
                    this.templateDiffs = [];

                    if (this.publishedExists) {
                        // Edit on top of a published version
                        this.pendingEditSubmission = { submitted_by: 'Admin', submitted_at: new Date().toISOString() };
                        this.successTitle   = 'Edit Submitted for Review!';
                        this.successMessage = `Your edited templates for ${this.currentPeriodLabel} have been submitted. The statistician will review and approve the changes. The current published version remains live until then.`;
                    } else {
                        // First-time submission
                        this.pendingSubmission = { submitted_by: 'Admin', submitted_at: new Date().toISOString() };
                        this.successTitle   = 'Submitted for Review!';
                        this.successMessage = `Your templates for ${this.currentPeriodLabel} have been submitted. The statistician will review and publish them.`;
                    }
                    this.showSuccessModal = true;
                } else {
                    this.errorTitle     = 'Submission Error';
                    this.errorMessage   = json.error || 'An error occurred. Please try again.';
                    this.showErrorModal = true;
                }
            } catch (e) {
                this.errorTitle     = 'Submission Error';
                this.errorMessage   = 'An unexpected error occurred. Please try again.';
                this.showErrorModal = true;
                console.error(e);
            } finally {
                this.saving = false;
            }
        },

        // ── Reset ──
        resetAll() {
            this.showResetModal = true;
        },

        async confirmReset() {
            this.showResetModal = false;
            try {
                for (const key of Object.keys(this.templates)) {
                    const r = await fetch(`/api/analysis-templates/${key}/reset`, {
                        method:  'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept':       'application/json'
                        }
                    });
                    if (!r.ok) { console.error(`Reset ${key} failed`); continue; }
                    const json = await r.json();
                    if (json.success) this.templates[key] = json.default_text;
                }
                this.validateAll();
                this.successTitle    = 'Reset Complete!';
                this.successMessage  = 'All templates have been reset to their default values.';
                this.showSuccessModal = true;
            } catch (e) {
                this.errorTitle     = 'Reset Error';
                this.errorMessage   = 'An error occurred while resetting. Please try again.';
                this.showErrorModal = true;
                console.error(e);
            }
        },
    };
}


// ─── Global Exports (required because Vite wraps modules in a private scope) ──
window.analysisEditor = analysisEditor;