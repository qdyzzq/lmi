// ─── Supply Side Analysis Editor ─────────────────────────────────────────────

function supplySideEditor() {
    return {
        provinces:            [],
        academicYears:        [],
        selectedProvince:     'All Provinces',
        selectedAcademicYear: null,
        analysisText:         '',
        originalText:         '',
        analysisId:           null,
        lastUpdated:          null,
        loading:              false,
        hasChanges:           false,
        quill:                null,

        // Pending submission from admin
        pendingSubmission:     null,
        loadingPending:        false,
        allPendingSubmissions: [],
        loadingAllPending:     false,

        // Approved records
        allApprovedRecords:  [],
        loadingAllApproved:  false,
        approvedSelected:    null,

        // Main top tab
        mainTab: 'editor',

        // Lock state — locked until a draft is loaded
        isUnlocked: false,
        originalSubmittedText: null,  // admin's original submitted text for diff

        // Snapshot of the originally loaded draft — used by Reset to restore everything
        draftProvince: null,
        draftYear:     null,
        draftText:     null,

        // Locked publish target — set when a draft is loaded, never changed by filter dropdowns
        publishTargetProvince:  null,
        publishTargetYear:      null,
        currentlyPublishedText: null, // what's currently live for publishTarget province/year

        // Archives
        archivedAnalysis: [],
        loadingArchives:  false,
        selectedArchive:  null,

        // Modals
        showConfirmModal: false,
        showSuccessModal: false,
        showCopyModal:    false,
        showResetModal:   false,
        showPreviewModal: false,
        previewDonutChart: null,
        previewDisciplineLeft: [
            {name:'Business Administration',          pct:'27.1', color:'rgb(15,23,83)'},
            {name:'Education Science',                pct:'20.6', color:'rgb(20,34,102)'},
            {name:'Medical and Allied',               pct:'10.8', color:'rgb(25,48,122)'},
            {name:'Criminal Justice Education',       pct:'10.0', color:'rgb(29,64,140)'},
            {name:'Engineering and Technology',       pct:'6.9',  color:'rgb(30,78,160)'},
            {name:'IT-Related Disciplines',           pct:'5.3',  color:'rgb(32,92,185)'},
            {name:'Service Trades',                   pct:'3.0',  color:'rgb(37,99,235)'},
            {name:'Social and Behavioral Sciences',   pct:'2.9',  color:'rgb(35,105,200)'},
            {name:'Agriculture, Forestry, Fisheries', pct:'2.6',  color:'rgb(50,115,240)'},
            {name:'Maritime',                         pct:'2.5',  color:'rgb(59,130,246)'},
            {name:'Mathematics',                      pct:'1.6',  color:'rgb(180,220,254)'},
        ],
        previewDisciplineRight: [
            {name:'Other Disciplines',              pct:'1.6',  color:'rgb(219,238,255)'},
            {name:'Architecture and Town Planning', pct:'1.3',  color:'rgb(80,148,248)'},
            {name:'Natural Science',                pct:'0.9',  color:'rgb(96,165,250)'},
            {name:'Humanities',                     pct:'0.7',  color:'rgb(120,182,251)'},
            {name:'Mass Communication',             pct:'0.6',  color:'rgb(140,195,252)'},
            {name:'Fine and Applied Arts',          pct:'0.6',  color:'rgb(155,206,253)'},
            {name:'Religion and Theology',          pct:'0.5',  color:'rgb(168,214,253)'},
            {name:'Law and Jurisprudence',          pct:'0.4',  color:'rgb(190,226,254)'},
            {name:'Home Economics',                 pct:'0.0',  color:'rgb(200,232,255)'},
            {name:'General Programs',               pct:'0.0',  color:'rgb(210,237,255)'},
        ],

        // Toasts
        showError:           false,
        errorMessage:        '',
        showSuccessToast:    false,
        successToastMessage: '',


        // ── Init ─────────────────────────────────────────────────────────────

        async init() {
            await this.loadOptions();
            await Promise.all([this.loadAll(), this.loadAllPending(), this.loadArchivedAnalysis(), this.loadAllApproved()]);
            this.$nextTick(() => { this.initQuillEditor(); });
            this.$watch('showPreviewModal', val => {
                if (val) this.$nextTick(() => this.initPreviewDonut());
            });
        },


        // ── Helpers ──────────────────────────────────────────────────────────

        getWordCount() {
            const text = this.analysisText.replace(/<[^>]*>/g, '').trim();
            return text.split(/\s+/).filter(w => w.length > 0).length;
        },

        formatDate(dateStr) {
            if (!dateStr) return '—';
            return new Date(dateStr).toLocaleString();
        },


        // ── Data Loading ──────────────────────────────────────────────────────

        async loadOptions() {
            try {
                const res  = await fetch('/api/supply-side-analysis/options');
                const data = await res.json();
                if (data.success) {
                    this.provinces     = data.provinces;
                    this.academicYears = data.academic_years;
                    if (this.academicYears.length > 0) {
                        this.selectedAcademicYear = this.academicYears[0];
                    }
                }
            } catch (e) {
                this.showErrorToast('Failed to load options');
            }
        },

        // Reload academic years whenever the province dropdown changes.
        async loadYears() {
            try {
                const res  = await fetch(`/api/supply-side-analysis/years?province=${encodeURIComponent(this.selectedProvince)}`);
                const data = await res.json();
                if (data.success) {
                    this.academicYears        = data.academic_years;
                    this.selectedAcademicYear = this.academicYears[0] ?? null;
                }
            } catch (e) {
                console.error('Error loading years:', e);
            }
        },

        async loadAll() {
            if (!this.selectedAcademicYear && this.academicYears.length > 0) {
                this.selectedAcademicYear = this.academicYears[0];
            }
            if (!this.selectedAcademicYear) return;
            await Promise.all([
                this.loadAnalysis(),
                this.loadPendingSubmission(),
                this.loadArchivedAnalysis(),
            ]);
        },

        // Only refresh sidebar (archives + pending) — never touches the editor.
        async loadSidebarOnly() {
            if (!this.selectedAcademicYear) return;
            await Promise.all([
                this.loadPendingSubmission(),
                this.loadArchivedAnalysis(),
            ]);
        },

        async loadAnalysis() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    province:      this.selectedProvince,
                    academic_year: this.selectedAcademicYear,
                });
                const res  = await fetch(`/api/supply-side-analysis/show?${params}`);
                const data = await res.json();
                if (data.success) {
                    this.analysisId   = data.data.id;
                    this.analysisText = data.data.analysis_text;
                    this.originalText = data.data.analysis_text;
                    this.lastUpdated  = data.data.updated_at
                        ? new Date(data.data.updated_at).toLocaleString()
                        : null;
                    this.hasChanges = false;
                    if (this.quill) { this.setQuillContent(this.analysisText); }
                }
            } catch (e) {
                this.showErrorToast('Failed to load analysis');
            } finally {
                this.loading = false;
            }
        },

        async loadPendingSubmission() {
            this.loadingPending = true;
            try {
                const params = new URLSearchParams({
                    province:      this.selectedProvince,
                    academic_year: this.selectedAcademicYear,
                });
                const res  = await fetch(`/api/supply-side-analysis/pending-show?${params}`);
                const data = await res.json();
                this.pendingSubmission = data.success ? data.data : null;
            } catch (e) {
                console.error('Error loading pending:', e);
            } finally {
                this.loadingPending = false;
            }
        },

        async loadAllPending() {
            this.loadingAllPending = true;
            try {
                const res  = await fetch('/api/supply-side-analysis/pending-all');
                const data = await res.json();
                if (data.success) { this.allPendingSubmissions = data.data; }
            } catch (e) {
                console.error('Error loading all pending:', e);
            } finally {
                this.loadingAllPending = false;
            }
        },

        async loadAllApproved() {
            this.loadingAllApproved = true;
            try {
                const res  = await fetch('/api/supply-side-analysis/approved-all');
                const data = await res.json();
                if (data.success) {
                    this.allApprovedRecords = data.data;
                    // Auto-select first record for immediate display
                    if (this.allApprovedRecords.length > 0 && !this.approvedSelected) {
                        this.approvedSelected = this.allApprovedRecords[0];
                    }
                }
            } catch (e) {
                console.error('Error loading approved records:', e);
            } finally {
                this.loadingAllApproved = false;
            }
        },

        async loadArchivedAnalysis() {
            this.loadingArchives = true;
            try {
                // No province/year filter — load ALL archives so they always
                // show on page load, not just after a submission is made.
                const res  = await fetch('/api/supply-side-analysis/archives');
                const data = await res.json();
                if (data.success) { this.archivedAnalysis = data.archives; }
            } catch (e) {
                console.error('Error loading archives:', e);
            } finally {
                this.loadingArchives = false;
            }
        },


        // ── Pending Draft Actions ─────────────────────────────────────────────

        async loadPendingIntoEditor() {
            if (!this.pendingSubmission) return;
            const prov = this.pendingSubmission.province;
            const year = this.pendingSubmission.academic_year;
            const text = this.pendingSubmission.analysis_text;
            this.selectedProvince      = prov;
            this.selectedAcademicYear  = year;
            this.analysisText          = text;
            this.originalSubmittedText = text;
            // Snapshot for Reset — restores exactly what was loaded
            this.draftProvince = prov;
            this.draftYear     = year;
            this.draftText     = text;
            if (this.quill) { this.setQuillContent(this.analysisText); }
            this.hasChanges  = true;
            this.isUnlocked  = true;
            // Lock publish target
            this.publishTargetProvince = prov;
            this.publishTargetYear     = year;
            // Fetch what's currently live for this province/year
            try {
                const params = new URLSearchParams({ province: prov, academic_year: year });
                const res  = await fetch(`/api/supply-side-analysis/show?${params}`);
                const data = await res.json();
                this.currentlyPublishedText = (data.success && data.data?.analysis_text)
                    ? data.data.analysis_text
                    : null;
            } catch (e) {
                this.currentlyPublishedText = null;
            }
            this.showSuccessToastMessage('Admin draft loaded — review and publish when ready');
        },

        async loadPendingItemIntoEditor(item) {
            const prov = item.province;
            const year = item.academic_year;
            const text = item.analysis_text;
            this.selectedProvince      = prov;
            this.selectedAcademicYear  = year;
            this.analysisText          = text;
            this.originalSubmittedText = text;
            // Snapshot for Reset — restores exactly what was loaded
            this.draftProvince = prov;
            this.draftYear     = year;
            this.draftText     = text;
            if (this.quill) { this.setQuillContent(this.analysisText); }
            this.hasChanges        = true;
            this.isUnlocked        = true;
            this.pendingSubmission = item;
            // Lock publish target
            this.publishTargetProvince = prov;
            this.publishTargetYear     = year;
            // Fetch what's currently live for this province/year
            try {
                const params = new URLSearchParams({ province: prov, academic_year: year });
                const res  = await fetch(`/api/supply-side-analysis/show?${params}`);
                const data = await res.json();
                this.currentlyPublishedText = (data.success && data.data?.analysis_text)
                    ? data.data.analysis_text
                    : null;
            } catch (e) {
                this.currentlyPublishedText = null;
            }
            this.mainTab = 'editor'; // stay in editor while working
            this.showSuccessToastMessage(`Draft loaded: ${prov} • ${year}`);
        },


        // ── Save & Publish ────────────────────────────────────────────────────

        async confirmSave() {
            this.showConfirmModal = false;
            this.loading = true;
            // Use locked publish target if available; fall back to filter dropdowns
            const province      = this.publishTargetProvince || this.selectedProvince;
            const academic_year = this.publishTargetYear     || this.selectedAcademicYear;
            try {
                const res = await fetch('/api/supply-side-analysis/save', {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        province,
                        academic_year,
                        analysis_text: this.analysisText,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    this.analysisId             = data.data.id;
                    this.lastUpdated            = new Date().toLocaleString();
                    this.pendingSubmission      = null;
                    this.originalSubmittedText  = null;  // clear diff after publish
                    this.currentlyPublishedText = null;  // clear 3-way diff state
                    this.publishTargetProvince  = null;
                    this.publishTargetYear      = null;
                    this.draftProvince          = null;  // clear reset snapshot
                    this.draftYear              = null;
                    this.draftText              = null;
                    this.isUnlocked             = false;
                    this.showSuccessModal        = true;
                    await Promise.all([
                        this.loadArchivedAnalysis(), // refresh so newly published appears
                        this.loadAllPending(),
                        this.loadAllApproved(),
                    ]);
                    this.mainTab = 'approved'; // switch to approved view
                } else {
                    throw new Error(data.error || 'Failed to save');
                }
            } catch (e) {
                this.showErrorToast('Failed to save analysis');
            } finally {
                this.loading = false;
            }
        },


        // ── Archive Copy ──────────────────────────────────────────────────────

        copyFromArchive(archive) {
            this.selectedArchive = archive;
            this.showCopyModal   = true;
        },

        confirmCopy() {
            if (this.selectedArchive) {
                this.analysisText    = this.selectedArchive.analysis_text;
                this.hasChanges      = true;
                this.showCopyModal   = false;
                if (this.quill) { this.setQuillContent(this.analysisText); }
                this.showSuccessToastMessage('Text copied from ' + this.selectedArchive.version);
                this.selectedArchive = null;
            }
        },


        // ── Reset ─────────────────────────────────────────────────────────────

        resetToDefault() { this.showResetModal = true; },

        confirmReset() {
            this.showResetModal = false;
            if (!this.draftText) return; // nothing to restore
            // Restore province, year, and text exactly as they were when the draft was loaded
            this.selectedProvince      = this.draftProvince;
            this.selectedAcademicYear  = this.draftYear;
            this.analysisText          = this.draftText;
            this.originalSubmittedText = this.draftText;
            if (this.quill) { this.setQuillContent(this.analysisText); }
            this.hasChanges = false; // back to original — no unsaved changes
            this.showSuccessToastMessage(`Reset to original draft: ${this.draftProvince} • ${this.draftYear}`);
        },


        // ── Quill ─────────────────────────────────────────────────────────────

        initQuillEditor() {
            if (this.quill) return;
            const el = document.getElementById('quillEditor');
            if (!el) { console.error('Quill editor element not found'); return; }

            const SizeStyle = Quill.import('attributors/style/size');
            SizeStyle.whitelist = ['8pt','9pt','10pt','11pt','12pt','14pt','16pt','18pt','20pt','22pt','24pt','28pt','36pt','48pt','72pt'];
            Quill.register(SizeStyle, true);

            this.quill = new Quill('#quillEditor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'font': [] }, { 'size': ['8pt','9pt','10pt','11pt','12pt','14pt','16pt','18pt','20pt','22pt','24pt','28pt','36pt','48pt','72pt'] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'header': [1, 2, 3, false] }],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link'],
                        ['clean'],
                    ]
                },
                placeholder: 'Enter executive analysis for supply side...',
            });

            this.quill.on('text-change', () => {
                this.analysisText = this.quill.root.innerHTML;
                this.hasChanges   = true;
            });

            if (this.analysisText) { this.setQuillContent(this.analysisText); }
        },

        setQuillContent(html) {
            if (!this.quill) return;
            this.quill.root.innerHTML = html || '';
            this.quill.update('silent');
            this.quill.setSelection(null);
            this._syncToolbarSize(html);
        },

        _syncToolbarSize(html) {
            const match      = html && html.match(/font-size:\s*([\d.]+pt)/);
            const sizeValue  = match ? match[1] : '8pt';
            const toolbar    = this.quill.getModule('toolbar');
            if (!toolbar) return;
            const pickerLabel = toolbar.container.querySelector('.ql-size .ql-picker-label');
            if (!pickerLabel) return;
            pickerLabel.setAttribute('data-value', sizeValue);
        },


        // ── Toasts ────────────────────────────────────────────────────────────

        showErrorToast(message) {
            this.errorMessage = message;
            this.showError    = true;
            setTimeout(() => { this.showError = false; }, 3000);
        },

        showSuccessToastMessage(message) {
            this.successToastMessage = message;
            this.showSuccessToast    = true;
            setTimeout(() => { this.showSuccessToast = false; }, 3000);
        },


        // ── Preview Donut Chart ───────────────────────────────────────────────

        initPreviewDonut() {
            const canvas = document.getElementById('previewDonutChart');
            if (!canvas) return;
            if (this.previewDonutChart) { this.previewDonutChart.destroy(); }
            const all = [...this.previewDisciplineLeft, ...this.previewDisciplineRight];
            this.previewDonutChart = new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: all.map(i => i.name),
                    datasets: [{
                        data:            all.map(i => parseFloat(i.pct)),
                        backgroundColor: all.map(i => i.color),
                        borderWidth:     2,
                        borderColor:     '#fff',
                    }]
                },
                options: {
                    responsive: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (c) => `${c.label}: ${c.parsed}%`
                            }
                        }
                    },
                    cutout: '60%',
                }
            });
        },
    };
}

// ─── Global Export (required because Vite wraps modules in a private scope) ───
window.supplySideEditor = supplySideEditor;