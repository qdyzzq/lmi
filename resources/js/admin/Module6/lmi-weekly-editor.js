// =============================================================================
// FILE: resources/js/admin/Module6/lmi-weekly-editor.js
// PAGE: LMI Publication Admin Editor — Weekly Issues
// =============================================================================

// ── Weekly Modal Alpine Component ─────────────────────────────────────────────

function weeklyModal() {
    return {
        open: false,
        type: null,
        title: '',
        issueId: null,
        loading: false,
        confirming: false,
        form: {
            year: '',
            month: '',
            week_number: '',
            date_range: '',
            link_url: '',
            title: '',
            subtitle: '',
            description: '',
        },
        errors: {},
        imageFile: null,
        previewUrl: null,
        months: [
            'January', 'February', 'March', 'April',
            'May', 'June', 'July', 'August',
            'September', 'October', 'November', 'December',
        ],

        // ── Open modal ──────────────────────────────────────────────────────────
        openModal(detail) {
            const titles = {
                'add-weekly':       'Add Weekly Issue',
                'edit-weekly':      'Edit Weekly Issue',
                'delete-weekly':    'Delete Weekly Issue',
                'edit-card-text':   'Edit Card Text',
                'edit-card-media':  'Edit Card Image & Link',
                'weekly-publish':   'Publish Weekly Issues',
                'weekly-unpublish': 'Unpublish Weekly Issues',
                'weekly-republish': 'Republish Weekly Issues',
            };

            this.type       = detail.type;
            this.title      = titles[detail.type] ?? 'Weekly Issue';
            this.issueId    = detail.issueId ?? null;
            this.loading    = false;
            this.confirming = false;
            this.errors     = {};
            this.imageFile  = null;
            this.previewUrl = null;

            if (detail.type === 'add-weekly') {
                this.form = {
                    year:        new Date().getFullYear(),
                    month:       '',
                    week_number: '',
                    date_range:  '',
                    link_url:    '',
                };
            } else if (detail.type === 'edit-weekly') {
                this.form = {
                    year:        detail.data?.year        ?? '',
                    month:       detail.data?.month       ?? '',
                    week_number: detail.data?.week_number ?? '',
                    date_range:  detail.data?.date_range  ?? '',
                    link_url:    detail.data?.link_url    ?? '',
                };
                // Show current image as preview
                this.previewUrl = detail.data?.image_url ?? null;
            } else if (detail.type === 'edit-card-text') {
                this.form = {
                    title:       detail.data?.title       ?? '',
                    subtitle:    detail.data?.subtitle    ?? '',
                    description: detail.data?.description ?? '',
                };
            } else if (detail.type === 'edit-card-media') {
                this.form = {
                    link_url: detail.data?.link_url ?? '',
                };
            } else {
                this.form = {};
            }

            this.open = true;
        },

        // ── Image preview ───────────────────────────────────────────────────────
        onImageChange(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.imageFile    = file;
            this.previewUrl   = URL.createObjectURL(file);
            this.errors.image = false;
        },

        // ── Build multipart form data ───────────────────────────────────────────
        buildFormData() {
            const fd = new FormData();
            fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
            Object.entries(this.form).forEach(([k, v]) => {
                if (v !== null && v !== undefined) fd.append(k, v);
            });
            if (this.imageFile) fd.append('image', this.imageFile);
            return fd;
        },

        // ── Request confirmation: add weekly ────────────────────────────────────
        requestConfirmAdd() {
            this.errors = {};
            if (!this.form.year)        this.errors.year        = true;
            if (!this.form.month)       this.errors.month       = true;
            if (!this.form.week_number) this.errors.week_number = true;
            if (!this.form.date_range)  this.errors.date_range  = true;
            if (!this.imageFile)        this.errors.image       = true;
            if (Object.values(this.errors).some(Boolean)) {
                lmiToast('Please fill in all required fields.', 'warning');
                return;
            }
            this.confirming = true;
        },

        // ── Request confirmation: edit weekly ───────────────────────────────────
        requestConfirmEdit() {
            this.errors = {};
            if (!this.form.year)        this.errors.year        = true;
            if (!this.form.month)       this.errors.month       = true;
            if (!this.form.week_number) this.errors.week_number = true;
            if (!this.form.date_range)  this.errors.date_range  = true;
            if (Object.values(this.errors).some(Boolean)) {
                lmiToast('Please fill in all required fields.', 'warning');
                return;
            }
            this.confirming = true;
        },

        // ── Request confirmation: card text ─────────────────────────────────────
        requestConfirmCardText() {
            this.errors = {};
            if (!this.form.title?.trim())       this.errors.title       = true;
            if (!this.form.subtitle?.trim())     this.errors.subtitle    = true;
            if (!this.form.description?.trim())  this.errors.description = true;
            if (Object.values(this.errors).some(Boolean)) {
                lmiToast('Please fill in all required fields.', 'warning');
                return;
            }
            this.confirming = true;
        },

        // ── Request confirmation: card media ────────────────────────────────────
        requestConfirmCardMedia() {
            this.confirming = true;
        },

        // ── Submit: add ─────────────────────────────────────────────────────────
        async submitAdd() {
            this.loading = true;
            const res  = await fetch('/admin/lmi-weekly', { method: 'POST', body: this.buildFormData() });
            const data = await res.json();
            this.loading = false;

            if (data.success) {
                this.open = false;
                lmiToast('Weekly issue added.', 'success');
                window.dispatchEvent(new CustomEvent('weekly-refresh'));
            } else {
                lmiToast(data.message || 'Something went wrong.', 'error');
            }
        },

        // ── Submit: edit ────────────────────────────────────────────────────────
        async submitEdit() {
            this.loading = true;
            const res  = await fetch(`/admin/lmi-weekly/${this.issueId}`, { method: 'POST', body: this.buildFormData() });
            const data = await res.json();
            this.loading = false;

            if (data.success) {
                this.open = false;
                lmiToast('Weekly issue updated.', 'success');
                window.dispatchEvent(new CustomEvent('weekly-refresh'));
            } else {
                lmiToast(data.message || 'Something went wrong.', 'error');
            }
        },

        // ── Submit: delete ──────────────────────────────────────────────────────
        async submitDelete() {
            this.loading = true;
            const res  = await fetch(`/admin/lmi-weekly/${this.issueId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept':       'application/json',
                },
            });
            const data = await res.json();
            this.loading = false;

            if (data.success) {
                this.open = false;
                lmiToast('Weekly issue deleted.', 'success');
                window.dispatchEvent(new CustomEvent('weekly-refresh'));
            } else {
                lmiToast(data.message || 'Something went wrong.', 'error');
            }
        },

        // ── Submit: card text ───────────────────────────────────────────────────
        async submitCardText() {
            this.loading = true;
            const res  = await fetch('/admin/lmi-weekly-card/text', { method: 'POST', body: this.buildFormData() });
            const data = await res.json();
            this.loading = false;

            if (data.success) {
                this.open = false;
                lmiToast('Card text updated.', 'success');
                window.dispatchEvent(new CustomEvent('card-setting-refresh', { detail: data.data }));
            } else {
                lmiToast(data.message || 'Something went wrong.', 'error');
            }
        },

        // ── Submit: publish weekly ──────────────────────────────────────────────
        async submitWeeklyPublish() {
            this.loading = true;
            const res  = await fetch('/admin/lmi-weekly/toggle-publish', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept':       'application/json',
                },
            });
            const data = await res.json();
            this.loading = false;

            if (data.success) {
                this.open = false;
                lmiToast('Weekly section published.', 'success');
                window.dispatchEvent(new CustomEvent('weekly-publish-refresh', { detail: data.publishState }));
            } else {
                lmiToast(data.message || 'Something went wrong.', 'error');
            }
        },

        // ── Submit: unpublish weekly ────────────────────────────────────────────
        async submitWeeklyUnpublish() {
            this.loading = true;
            const res  = await fetch('/admin/lmi-weekly/toggle-publish', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept':       'application/json',
                },
            });
            const data = await res.json();
            this.loading = false;

            if (data.success) {
                this.open = false;
                lmiToast('Weekly section unpublished.', 'success');
                window.dispatchEvent(new CustomEvent('weekly-publish-refresh', { detail: data.publishState }));
            } else {
                lmiToast(data.message || 'Something went wrong.', 'error');
            }
        },

        // ── Submit: republish weekly ────────────────────────────────────────────
        async submitWeeklyRepublish() {
            this.loading = true;
            const res  = await fetch('/admin/lmi-weekly/republish', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept':       'application/json',
                },
            });
            const data = await res.json();
            this.loading = false;

            if (data.success) {
                this.open = false;
                lmiToast('Weekly changes republished.', 'success');
                window.dispatchEvent(new CustomEvent('weekly-publish-refresh', { detail: data.publishState }));
            } else {
                lmiToast(data.message || 'Something went wrong.', 'error');
            }
        },

        // ── Submit: card media ──────────────────────────────────────────────────
        async submitCardMedia() {
            this.loading = true;
            const res  = await fetch('/admin/lmi-weekly-card/media', { method: 'POST', body: this.buildFormData() });
            const data = await res.json();
            this.loading = false;

            if (data.success) {
                this.open = false;
                lmiToast('Card image & link updated.', 'success');
                window.dispatchEvent(new CustomEvent('card-setting-refresh', { detail: data.data }));
            } else {
                lmiToast(data.message || 'Something went wrong.', 'error');
            }
        },
    };
}

// ── Lightbox ──────────────────────────────────────────────────────────────────

function openLightbox(src, caption = '') {
    const lb = document.getElementById('imageLightbox');
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxCaption').textContent = caption;
    lb.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('imageLightbox').classList.add('hidden');
    document.getElementById('lightboxImg').src = '';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeLightbox();
});

// ── Global exports ────────────────────────────────────────────────────────────
window.weeklyModal   = weeklyModal;
window.openLightbox  = openLightbox;
window.closeLightbox = closeLightbox;