// ─── Analysis Template Editor ─────────────────────────────────────────────────

function templateEditor() {
    return {
        // ── State ──────────────────────────────────────────────────────────────
        loading:     true,
        saving:      false,
        isUnlocked:  false,
        viewMode:    'edit',
        activeTab:   'employment',
        activeField: 'employment',
        lastSaved:   null,

        // ── Filters ────────────────────────────────────────────────────────────
        selectedYear:    null,
        selectedMonth:   null,
        availableYears:  [],
        availableMonths: [],
        quarterLabels:   {},

        // ── Templates ──────────────────────────────────────────────────────────
        templateKeys: ['employment', 'underemployment', 'unemployment', 'lfpr'],
        templates:     { employment: '', underemployment: '', unemployment: '', lfpr: '' },
        originalSubmittedTemplates: { employment: null, underemployment: null, unemployment: null, lfpr: null },
        savedTemplates:             { employment: '',   underemployment: '',   unemployment: '',   lfpr: '' },

        validation: {
            employment:      { valid: true, missing: [] },
            underemployment: { valid: true, missing: [] },
            unemployment:    { valid: true, missing: [] },
            lfpr:            { valid: true, missing: [] },
        },

        allPlaceholders: [
            { key: '{current_period}',  icon: 'cal'   },
            { key: '{previous_period}', icon: 'cal'   },
            { key: '{current_rate}',    icon: 'chart' },
            { key: '{previous_rate}',   icon: 'chart' },
            { key: '{trend}',           icon: 'trend' },
        ],
        requiredPlaceholders: ['{current_period}', '{previous_period}', '{current_rate}', '{previous_rate}', '{trend}'],

        allPendingDrafts:  [],
        loadingAllPending: false,
        previewData:       {},
        hasPreviewData:    false,
        loadingPreview:    false,

        // ── Approved / Published ───────────────────────────────────────────────
        allApprovedTemplates: [],
        loadingAllApproved:   false,
        approvedDetailItem:   null,
        approvedDetailTab:    'employment',

        // ── Sidebar tab ────────────────────────────────────────────────────────
        sidebarTab: 'pending',

        // ── Main top tab ───────────────────────────────────────────────────────
        mainTab: 'editor',

        // Locked publish target — set when a draft is loaded, never changed by filter dropdowns
        publishTargetYear:  null,
        publishTargetMonth: null,

        // What's currently live for the publish target period (for 3-way diff)
        currentlyPublishedTemplates: { employment: null, underemployment: null, unemployment: null, lfpr: null },

        // Draft snapshot — used by Reset to restore exactly what was loaded
        draftYear:      null,
        draftMonth:     null,
        draftTemplates: { employment: null, underemployment: null, unemployment: null, lfpr: null },

        showSaveModal: false, showResetModal: false, showSuccessModal: false, showErrorModal: false,
        successMessage: '', errorTitle: '', errorMessage: '',
        showToast: false, toastMessage: '',


        // ── Computed ───────────────────────────────────────────────────────────

        get currentPeriodLabel() {
            const name = this.quarterLabels[this.selectedMonth] || '';
            return name ? `${name} ${this.selectedYear}` : '—';
        },


        // ── Init ───────────────────────────────────────────────────────────────

        async init() {
            await Promise.all([this.loadTemplates(), this.loadAllPending(), this.loadAllApproved()]);
        },


        // ── Label / Icon Helpers ───────────────────────────────────────────────

        labelFor(key) {
            return { employment: 'Employment', underemployment: 'Underemployment', unemployment: 'Unemployment', lfpr: 'Participation Rate' }[key] || key;
        },

        iconFor(key) {
            const svgs = {
                employment:      '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>',
                underemployment: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                unemployment:    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>',
                lfpr:            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>',
            };
            return svgs[key] || '';
        },

        borderColorFor(key) {
            return { employment: 'border-l-4 border-green-600/30', underemployment: 'border-l-4 border-orange-500/30', unemployment: 'border-l-4 border-red-600/30', lfpr: 'border-l-4 border-blue-600/30' }[key] || '';
        },

        iconBgFor(key) {
            return { employment: 'bg-green-50', underemployment: 'bg-orange-50', unemployment: 'bg-red-50', lfpr: 'bg-blue-50' }[key] || 'bg-slate-50';
        },

        textColorFor(key) {
            return { employment: 'text-green-700', underemployment: 'text-orange-700', unemployment: 'text-red-700', lfpr: 'text-blue-700' }[key] || 'text-slate-700';
        },


        // ── Change Detection ───────────────────────────────────────────────────

        isTabChanged(key) {
            return this.originalSubmittedTemplates[key] != null &&
                   this.templates[key].trim() !== this.originalSubmittedTemplates[key].trim();
        },

        hasAnyChanges() {
            return this.templateKeys.some(k => this.isTabChanged(k));
        },

        formatDate(dateStr) {
            if (!dateStr) return '—';
            return new Date(dateStr).toLocaleString();
        },


        // ── Data Loading ───────────────────────────────────────────────────────

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
                    this.templateKeys.forEach(k => {
                        const text = json.data[k]?.template_text || '';
                        this.templates[k]      = text;
                        this.savedTemplates[k] = text;
                    });
                    this.validateAll();
                    await this.loadPreviewData();
                }
            } catch (e) {
                console.error('Load error:', e);
                this.errorTitle   = 'Loading Error';
                this.errorMessage = 'Could not load templates. Please refresh and try again.';
                this.showErrorModal = true;
            } finally {
                this.loading = false;
            }
        },

        async loadSidebarOnly() { await this.loadPreviewData(); },

        async loadAllPending() {
            this.loadingAllPending = true;
            try {
                const res  = await fetch('/api/analysis-templates/pending-all');
                const data = await res.json();
                if (data.success) { this.allPendingDrafts = data.data; }
            } catch (e) { console.error('Error loading pending drafts:', e); }
            finally { this.loadingAllPending = false; }
        },

        async loadAllApproved() {
            this.loadingAllApproved = true;
            try {
                const res  = await fetch('/api/analysis-templates/approved-all');
                const data = await res.json();
                if (data.success) { this.allApprovedTemplates = data.data; }
            } catch (e) { console.error('Error loading approved templates:', e); }
            finally { this.loadingAllApproved = false; }
        },

        viewApprovedDetail(item) {
            this.approvedDetailItem = item;
            this.approvedDetailTab  = item.template_keys[0] || 'employment';
            this.mainTab = 'approved';
        },

        async loadPreviewData() {
            if (!this.selectedYear || !this.selectedMonth) return;
            this.loadingPreview = true;
            try {
                const params = new URLSearchParams({ year: this.selectedYear, month: this.selectedMonth });
                const r    = await fetch(`/api/analysis-templates/preview-data?${params}`);
                const json = await r.json();
                if (json.success && json.has_data) {
                    this.previewData    = json.data;
                    this.hasPreviewData = true;
                } else { this.hasPreviewData = false; }
            } catch (e) { this.hasPreviewData = false; }
            finally { this.loadingPreview = false; }
        },


        // ── Draft Actions ──────────────────────────────────────────────────────

        async loadDraftIntoEditor(draft) {
            const yr = draft.year;
            const mo = draft.month;
            this.selectedYear  = yr;
            this.selectedMonth = mo;
            this.templateKeys.forEach(k => {
                if (draft.templates[k] !== undefined) {
                    this.originalSubmittedTemplates[k] = draft.templates[k];
                    this.templates[k]                  = draft.templates[k];
                }
            });
            // Lock publish target — never changes even if filters move
            this.publishTargetYear  = yr;
            this.publishTargetMonth = mo;
            // Snapshot for Reset — restores exactly what was loaded
            this.draftYear  = yr;
            this.draftMonth = mo;
            this.templateKeys.forEach(k => { this.draftTemplates[k] = draft.templates[k] ?? null; });
            // Fetch what's currently live for this period (for 3-way diff in save modal)
            try {
                const params = new URLSearchParams({ year: yr, month: mo });
                const res  = await fetch(`/api/analysis-templates?${params}`);
                const json = await res.json();
                if (json.success) {
                    this.templateKeys.forEach(k => {
                        this.currentlyPublishedTemplates[k] = json.data[k]?.template_text || null;
                    });
                }
            } catch (e) {
                this.templateKeys.forEach(k => { this.currentlyPublishedTemplates[k] = null; });
            }
            this.validateAll();
            this.isUnlocked = true;
            this.activeTab  = this.templateKeys[0];
            this.sidebarTab = 'pending'; // stay on pending while editing
            this.loadPreviewData();
            this.showSuccessToast(`Draft loaded: ${this.quarterLabels[mo] || mo} ${yr}`);
        },

        lockEditor() {
            this.isUnlocked = false;
            this.templateKeys.forEach(k => {
                this.originalSubmittedTemplates[k]  = null;
                this.currentlyPublishedTemplates[k] = null;
                this.draftTemplates[k]              = null;
            });
            this.publishTargetYear  = null;
            this.publishTargetMonth = null;
            this.draftYear  = null;
            this.draftMonth = null;
        },


        // ── Cursor Insert ──────────────────────────────────────────────────────

        insertAtCursor(placeholder, key) {
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


        // ── Validation ─────────────────────────────────────────────────────────

        onInput(key) { this.validateTemplate(key); },

        validateTemplate(key) {
            const text    = this.templates[key] || '';
            const missing = this.requiredPlaceholders.filter(p => !text.includes(p));
            this.validation[key] = { valid: missing.length === 0, missing };
        },

        validateAll() { this.templateKeys.forEach(k => this.validateTemplate(k)); },

        hasValidationErrors() { return this.templateKeys.some(k => this.validation[k] && !this.validation[k].valid); },


        // ── Word-level Diff ────────────────────────────────────────────────────

        wordDiff(original, current) {
            const tokenize = str => str.match(/(\s+|\S+)/g) || [];
            const a = tokenize(original);
            const b = tokenize(current);
            const m = a.length, n = b.length;
            const dp = Array.from({ length: m + 1 }, () => new Array(n + 1).fill(0));
            for (let i = 1; i <= m; i++)
                for (let j = 1; j <= n; j++)
                    dp[i][j] = a[i-1] === b[j-1] ? dp[i-1][j-1] + 1 : Math.max(dp[i-1][j], dp[i][j-1]);
            const ops = [];
            let i = m, j = n;
            while (i > 0 || j > 0) {
                if (i > 0 && j > 0 && a[i-1] === b[j-1]) {
                    ops.unshift({ type: 'eq',  val: b[j-1] }); i--; j--;
                } else if (j > 0 && (i === 0 || dp[i][j-1] >= dp[i-1][j])) {
                    ops.unshift({ type: 'ins', val: b[j-1] }); j--;
                } else {
                    ops.unshift({ type: 'del', val: a[i-1] }); i--;
                }
            }
            return ops.map(op => {
                const isSpace = /^\s+$/.test(op.val);
                if (op.type === 'eq')  return op.val;
                if (op.type === 'ins') return isSpace ? op.val : `<mark class="bg-yellow-200 text-yellow-900 rounded px-0.5 font-medium">${op.val}</mark>`;
                if (op.type === 'del') return isSpace ? '' : `<span class="line-through text-red-400 opacity-75 text-xs">${op.val}</span>`;
            }).join('');
        },


        // ── Preview Rendering ──────────────────────────────────────────────────

        renderDiffPreview(text, key) {
            if (!text || !text.trim()) return '<span class="text-slate-300 italic">No content</span>';
            const original   = this.originalSubmittedTemplates[key];
            const renderText = (t) => {
                let out = t;
                if (this.hasPreviewData && this.previewData[key]) {
                    Object.entries(this.previewData[key]).forEach(([ph, val]) => { out = out.replaceAll(ph, val); });
                } else {
                    Object.entries(this.getMockData()).forEach(([k, v]) => { out = out.replaceAll(k, v); });
                }
                return out;
            };
            if (!original) return renderText(text);
            const diffHtml = this.wordDiff(original.trim(), text.trim());
            let out = diffHtml;
            if (this.hasPreviewData && this.previewData[key]) {
                Object.entries(this.previewData[key]).forEach(([ph, val]) => { out = out.replaceAll(ph, val); });
            } else {
                Object.entries(this.getMockData()).forEach(([k, v]) => { out = out.replaceAll(k, v); });
            }
            return out;
        },

        renderPreview(text, key) {
            if (!text || !text.trim()) return '<span class="text-slate-300 italic">No content</span>';
            let out = text;
            if (this.hasPreviewData && this.previewData[key]) {
                Object.entries(this.previewData[key]).forEach(([ph, val]) => { out = out.replaceAll(ph, val); });
            } else {
                Object.entries(this.getMockData()).forEach(([k, v]) => { out = out.replaceAll(k, v); });
            }
            return out;
        },

        getMockData() {
            const currentName = this.quarterLabels[this.selectedMonth] || 'January';
            const prev        = this.getPreviousPeriod(this.selectedMonth, this.selectedYear);
            const prevName    = this.quarterLabels[prev.month] || 'October';
            return {
                '{current_period}':  `<strong>${currentName} ${this.selectedYear}</strong>`,
                '{previous_period}': `<strong>${prevName} ${prev.year}</strong>`,
                '{current_rate}':    '<strong>89.0%</strong>',
                '{previous_rate}':   '<strong>92.5%</strong>',
                '{trend}':           '<span class="text-red-600 font-semibold">lower ↓</span>',
            };
        },

        getPreviousPeriod(month, year) {
            if (month == 1) return { month: 1, year: parseInt(year) - 1 };
            const map  = { 4: { month: 1, yearOffset: 0 }, 7: { month: 4, yearOffset: 0 }, 10: { month: 7, yearOffset: 0 } };
            const prev = map[month] || { month: 1, yearOffset: -1 };
            return { month: prev.month, year: parseInt(year) + prev.yearOffset };
        },


        // ── Save & Publish ─────────────────────────────────────────────────────

        confirmBeforeSave() {
            if (this.hasValidationErrors()) {
                this.errorTitle    = 'Validation Errors';
                this.errorMessage  = 'Please fix all validation errors before saving.';
                this.showErrorModal = true;
                return;
            }
            this.showSaveModal = true;
        },

        async confirmSave() {
            if (this.saving) return;
            this.showSaveModal = false;
            this.saving = true;
            // Use locked publish target if set, fall back to filter dropdowns
            const saveYear  = this.publishTargetYear  || this.selectedYear;
            const saveMonth = this.publishTargetMonth || this.selectedMonth;
            let errors = 0;
            try {
                for (const key of this.templateKeys) {
                    const r = await fetch(`/api/analysis-templates/${key}`, {
                        method:  'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept':       'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ template_text: this.templates[key], year: saveYear, month: saveMonth }),
                    });
                    if (!r.ok) { errors++; console.error(`Save ${key} failed`, await r.text()); }
                }
                if (errors === 0) {
                    this.templateKeys.forEach(k => { this.savedTemplates[k] = this.templates[k]; });
                    this.lastSaved       = new Date().toLocaleString();
                    this.isUnlocked      = false;
                    this.successMessage  = `All templates saved for ${this.quarterLabels[saveMonth] || saveMonth} ${saveYear}.`;
                    this.showSuccessModal = true;
                    // Clear all snapshot/publish-target/diff state after successful save
                    this.templateKeys.forEach(k => {
                        this.originalSubmittedTemplates[k]  = null;
                        this.currentlyPublishedTemplates[k] = null;
                        this.draftTemplates[k]              = null;
                    });
                    this.publishTargetYear  = null;
                    this.publishTargetMonth = null;
                    this.draftYear  = null;
                    this.draftMonth = null;
                    await this.loadAllPending();
                    await this.loadAllApproved();
                    this.sidebarTab = 'approved'; // show the newly published record
                    this.mainTab    = 'approved'; // switch main view to approved
                } else {
                    this.errorTitle   = 'Save Error';
                    this.errorMessage = `${errors} error(s) occurred. Please check the console.`;
                    this.showErrorModal = true;
                }
            } catch (e) {
                this.errorTitle   = 'Save Error';
                this.errorMessage = 'An unexpected error occurred. Please try again.';
                this.showErrorModal = true;
                console.error(e);
            } finally { this.saving = false; }
        },


        // ── Reset ──────────────────────────────────────────────────────────────

        resetAll() { this.showResetModal = true; },

        confirmReset() {
            this.showResetModal = false;
            if (!this.draftYear) return; // nothing to restore
            // Restore year, month, and all template texts exactly as loaded
            this.selectedYear  = this.draftYear;
            this.selectedMonth = this.draftMonth;
            this.templateKeys.forEach(k => {
                if (this.draftTemplates[k] !== null) {
                    this.templates[k]                  = this.draftTemplates[k];
                    this.originalSubmittedTemplates[k] = this.draftTemplates[k];
                }
            });
            this.validateAll();
            this.showSuccessToast(`Reset to original draft: ${this.quarterLabels[this.draftMonth] || this.draftMonth} ${this.draftYear}`);
        },


        // ── Toast ──────────────────────────────────────────────────────────────

        showSuccessToast(message) {
            this.toastMessage = message;
            this.showToast    = true;
            setTimeout(() => { this.showToast = false; }, 3000);
        },
    };
}

// ─── Global Export (required because Vite wraps modules in a private scope) ───
window.templateEditor = templateEditor;