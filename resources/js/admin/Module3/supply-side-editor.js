// ─── Supply Side Analysis Editor ─────────────────────────────────────────────
function adminSupplySideEditor() {
    return {
        // Options
        provinces:            [],
        academicYears:        [],
        selectedProvince:     null,
        selectedAcademicYear: null,

        // Editor state
        analysisText:    '',
        hasChanges:      false,
        loading:         false,
        quill:           null,
        _loadingContent: false,  // suppresses text-change during programmatic loads

        // Status
        pendingSubmission:  null,
        publishedExists:    false,
        publishedText:      '',
        publishedUpdatedAt: null,
        loadingPublished:   false,

        // Archives
        archivedAnalyses: [],
        loadingArchives:  false,
        selectedArchive:  null,

        // Modals
        showConfirmModal: false,
        showSuccessModal: false,
        showCopyModal:    false,

        // Toasts
        showError:           false,
        errorMessage:        '',
        showSuccessToast:    false,
        successToastMessage: '',

        async init() {
            await this.loadOptions();
            // Init Quill BEFORE loading data so it exists when loadData sets its content
            this.$nextTick(async () => {
                this.initQuillEditor();
                await this.loadData();
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
                    if (this.provinces.length > 0) {
                        this.selectedProvince = this.provinces[0];
                    }
                    if (this.academicYears.length > 0) {
                        this.selectedAcademicYear = this.academicYears[0];
                    }
                }
            } catch (e) {
                this.showErrorToast('Failed to load options');
            }
        },

        async loadYears() {
            try {
                const res  = await fetch(`/api/supply-side-analysis/years?province=${encodeURIComponent(this.selectedProvince)}`);
                const data = await res.json();
                if (data.success) {
                    this.academicYears        = data.academic_years;
                    // Reset to the first valid year for the newly selected province
                    this.selectedAcademicYear = this.academicYears[0] ?? null;
                }
            } catch (e) {
                console.error('Error loading years:', e);
            }
        },

        async loadData() {
            if (!this.selectedAcademicYear) return;

            // Clear stale state immediately so old data doesn't linger while fetching
            this.analysisText        = '';
            this.publishedExists     = false;
            this.publishedText       = '';
            this.publishedUpdatedAt  = null;
            this.pendingSubmission   = null;
            this.archivedAnalyses    = [];
            this.hasChanges          = false;
            this.setQuillContent('');

            // Run sequentially so pendingSubmission is set before loadPublished checks it
            await this.loadPendingSubmission();
            await this.loadPublished();
            await this.loadArchivedAnalyses();
        },

        async loadPendingSubmission() {
            try {
                const params = new URLSearchParams({
                    province:      this.selectedProvince,
                    academic_year: this.selectedAcademicYear,
                });
                const res  = await fetch(`/api/supply-side-analysis/pending-show?${params}`);
                const data = await res.json();
                if (data.success) {
                    this.pendingSubmission = data.data;
                    if (data.data) {
                        this.analysisText = data.data.analysis_text;
                        this.setQuillContent(this.analysisText);
                        this.hasChanges = false;
                    }
                }
            } catch (e) {
                console.error('Error loading pending submission:', e);
            }
        },

        async loadPublished() {
            this.loadingPublished = true;
            try {
                const params = new URLSearchParams({
                    province:      this.selectedProvince,
                    academic_year: this.selectedAcademicYear,
                });
                const res  = await fetch(`/api/supply-side-analysis/show?${params}`);
                const data = await res.json();
                if (data.success && data.data.id) {
                    this.publishedExists    = true;
                    this.publishedText      = data.data.analysis_text;
                    this.publishedUpdatedAt = data.data.updated_at
                        ? new Date(data.data.updated_at).toLocaleString()
                        : null;

                    // If no pending draft, pre-fill the editor with published content
                    if (!this.pendingSubmission) {
                        this.analysisText = data.data.analysis_text;
                        this.setQuillContent(this.analysisText);
                        this.hasChanges = false;
                    }
                } else {
                    this.publishedExists    = false;
                    this.publishedText      = '';
                    this.publishedUpdatedAt = null;

                    // If nothing at all, load default text
                    if (!this.pendingSubmission) {
                        await this.loadDefaultText(false);
                    }
                }
            } catch (e) {
                console.error('Error loading published:', e);
            } finally {
                this.loadingPublished = false;
            }
        },

        async loadArchivedAnalyses() {
            this.loadingArchives = true;
            try {
                const params = new URLSearchParams({
                    province:      this.selectedProvince,
                    academic_year: this.selectedAcademicYear,
                });
                const res  = await fetch(`/api/supply-side-analysis/archives?${params}`);
                const data = await res.json();
                if (data.success) { this.archivedAnalyses = data.archives; }
            } catch (e) {
                console.error('Error loading archives:', e);
            } finally {
                this.loadingArchives = false;
            }
        },

        async loadDefaultText(markChanged = true) {
            // Only allowed when there is no pending submission and no published analysis
            if (this.pendingSubmission || this.publishedExists) return;
            try {
                const res  = await fetch('/api/supply-side-analysis/reset');
                const data = await res.json();
                if (data.success) {
                    this.analysisText = data.default_text;
                    this.setQuillContent(this.analysisText);
                    if (markChanged) {
                        this.hasChanges = true;
                        this.showSuccessToastMessage('Reset to default text');
                    }
                }
            } catch (e) {
                this.showErrorToast('Failed to load default text');
            }
        },

        // ── Actions ───────────────────────────────────────────────────────────

        async confirmSubmit() {
            if (this.pendingSubmission || this.publishedExists) {
                this.showConfirmModal = false;
                return;
            }
            this.showConfirmModal = false;
            this.loading = true;
            try {
                const res = await fetch('/api/supply-side-analysis/submit', {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        province:      this.selectedProvince,
                        academic_year: this.selectedAcademicYear,
                        analysis_text: this.analysisText,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    this.hasChanges        = false;
                    this.pendingSubmission = data.data;
                    this.showSuccessModal  = true;
                } else {
                    throw new Error(data.error || 'Failed to submit');
                }
            } catch (e) {
                console.error('Error submitting:', e);
                this.showErrorToast('Failed to submit analysis. Please try again.');
            } finally {
                this.loading = false;
            }
        },

        copyFromPublished() {
            this.analysisText = this.publishedText;
            this.setQuillContent(this.analysisText);
            this.hasChanges = true;
            this.showSuccessToastMessage('Copied from currently published version');
        },

        copyFromArchive(archive) {
            this.selectedArchive = archive;
            this.showCopyModal   = true;
        },

        confirmCopy() {
            if (this.selectedArchive) {
                this.analysisText = this.selectedArchive.analysis_text;
                this.setQuillContent(this.analysisText);
                this.hasChanges   = true;
                this.showCopyModal = false;
                this.showSuccessToastMessage('Text copied from archive');
                this.selectedArchive = null;
            }
        },

        // ── Quill ─────────────────────────────────────────────────────────────

        initQuillEditor() {
            if (this.quill) return;
            const el = document.getElementById('quillEditor');
            if (!el) return;

            const SizeStyle = Quill.import('attributors/style/size');
            SizeStyle.whitelist = ['8pt','10pt','11pt','12pt','14pt','16pt','18pt','24pt','36pt'];
            Quill.register(SizeStyle, true);

            this.quill = new Quill('#quillEditor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'font': [] }, { 'size': ['8pt','10pt','11pt','12pt','14pt','16pt','18pt','24pt','36pt'] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'header': [1, 2, 3, false] }],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link'],
                        ['clean'],
                    ]
                },
                placeholder: 'Write your supply side analysis here...',
            });

            this.quill.on('text-change', () => {
                if (this._loadingContent) return;
                this.analysisText = this.quill.root.innerHTML;
                this.hasChanges   = true;
            });

            if (this.analysisText) {
                this.setQuillContent(this.analysisText);
            }
        },

        setQuillContent(html) {
            if (!this.quill) return;

            this._loadingContent = true;

            // Step 1: Write HTML directly to the editor DOM
            this.quill.root.innerHTML = html || '';

            // Step 2: Re-sync Quill's internal Delta from the DOM silently
            this.quill.update('silent');

            // Step 3: Clear any saved selection / cursor position
            this.quill.setSelection(null);

            // Step 4: Reset all cursor-format keys so no format bleeds
            const formatsToClear = [
                'size', 'font', 'bold', 'italic', 'underline', 'strike',
                'color', 'background', 'header', 'list', 'align', 'link'
            ];
            formatsToClear.forEach(fmt => {
                try { this.quill.format(fmt, false, 'silent'); } catch(e) {}
            });

            this._loadingContent = false;
        },

        // ── Toasts ────────────────────────────────────────────────────────────

        showErrorToast(message) {
            this.errorMessage = message;
            this.showError    = true;
            setTimeout(() => { this.showError = false; }, 3500);
        },

        showSuccessToastMessage(message) {
            this.successToastMessage = message;
            this.showSuccessToast    = true;
            setTimeout(() => { this.showSuccessToast = false; }, 3000);
        },
    };
}


// ─── Global Exports (required because Vite wraps modules in a private scope) ──
window.adminSupplySideEditor = adminSupplySideEditor;