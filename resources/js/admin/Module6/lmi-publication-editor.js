// =============================================================================
// FILE: resources/js/admin/Module6/lmi-publication-editor.js
// PAGE: LMI Publication Admin Editor
// =============================================================================

// ── CSRF + fetch helpers ──────────────────────────────────────────────────────

function lmiCsrf() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

async function lmiJson(method, url, body = {}) {
    try {
        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': lmiCsrf(),
                'Accept': 'application/json',
            },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (!res.ok) {
            const msg = data.message
                || Object.values(data.errors || {})[0]?.[0]
                || `Server error (${res.status})`;
            return { success: false, message: msg };
        }
        return data;
    } catch {
        return { success: false, message: 'Network error. Please check your connection.' };
    }
}

// ── Toast ─────────────────────────────────────────────────────────────────────

function lmiToast(message, type = 'error') {
    const container = document.getElementById('lmiToastContainer');
    if (!container) return;

    const colors = {
        error:   { border: 'border-red-500',    label: 'Error',   text: 'text-red-600'   },
        success: { border: 'border-emerald-500', label: 'Success', text: 'text-emerald-600' },
        warning: { border: 'border-amber-500',   label: 'Warning', text: 'text-amber-600'  },
    };
    const c = colors[type] || colors.error;

    const toast = document.createElement('div');
    toast.className = `pointer-events-auto w-full border-l-4 bg-white rounded-xl shadow-xl overflow-hidden transition-all duration-300 ease-out translate-x-full opacity-0 ${c.border}`;

    const inner = document.createElement('div');
    inner.className = 'flex items-start gap-3 px-4 py-3.5';

    const textDiv = document.createElement('div');
    textDiv.className = 'flex-1 min-w-0';

    const labelEl = document.createElement('p');
    labelEl.className = `text-xs font-bold uppercase tracking-wide ${c.text} mb-0.5`;
    labelEl.textContent = c.label;

    const msgEl = document.createElement('p');
    msgEl.className = 'text-sm text-slate-700 leading-snug';
    msgEl.textContent = message;

    textDiv.appendChild(labelEl);
    textDiv.appendChild(msgEl);
    inner.appendChild(textDiv);

    const closeBtn = document.createElement('button');
    closeBtn.className = 'flex-shrink-0 w-5 h-5 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition mt-0.5';
    closeBtn.innerHTML = `<svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
    closeBtn.addEventListener('click', () => toast.remove());
    inner.appendChild(closeBtn);
    toast.appendChild(inner);

    const bar = document.createElement('div');
    bar.className = `h-1 w-full origin-left ${c.border.replace('border-', 'bg-')}`;
    toast.appendChild(bar);

    container.appendChild(toast);
    requestAnimationFrame(() => requestAnimationFrame(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
    }));

    const duration = type === 'error' ? 5000 : 3500;
    bar.style.transition = `transform ${duration}ms linear`;
    requestAnimationFrame(() => { bar.style.transform = 'scaleX(0)'; });

    setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

// ── Alpine state refresh (no page reload) ─────────────────────────────────────

async function lmiRefreshAllGroups() {
    try {
        const res = await fetch('/admin/lmi-publication/data', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': lmiCsrf(),
                'Accept': 'application/json',
            },
        });
        if (!res.ok) throw new Error();
        const data = await res.json();

        Object.keys(data).forEach(gId => {
            // Update the card
            const card = document.getElementById(`lmi-card-${gId}`);
            if (card) {
                const state = Alpine.$data(card);
                if (state) {
                    state.issues          = data[gId].issues;
                    state.isPublished     = data[gId].is_published;
                    state.hasDraftChanges = data[gId].has_draft_changes;

                    const years = [...new Set(state.issues.map(i => String(i.year)))].sort((a, b) => b.localeCompare(a));
                    if (years.length && !years.includes(state.activeYear)) {
                        state.activeYear = years[0];
                    }
                }
            }
        });

    } catch {
        window.location.reload();
    }
}

// ── Main Alpine component ─────────────────────────────────────────────────────

function lmiAdminPage() {
    return {
        // modal state
        modal: {
            open:       false,
            type:       null,
            title:      '',
            groupId:    null,
            groupName:  '',
            yearType:   'single',
            issueId:    null,
            loading:    false,
            confirming: false,
        },
        form: {},
        formErrors: {},

        // ── Lightbox state ────────────────────────────────────────────────────
        lightbox: {
            open:    false,
            src:     '',
            caption: '',
            scale:   0.3,
            panX:    0,
            panY:    0,
            drag:    false,
            dx:      0,
            dy:      0,
        },

        openModal(detail) {
            const titles = {
                'add-issue':    `Add Issue — ${detail.groupName}`,
                'edit-issue':   'Edit Issue',
                'delete-issue': 'Delete Issue',
                'publish':      `Publish — ${detail.groupName}`,
                'unpublish':    `Unpublish — ${detail.groupName}`,
                'republish':    `Republish — ${detail.groupName}`,
            };

            this.modal = {
                open:       true,
                type:       detail.type,
                title:      titles[detail.type] ?? 'Edit',
                groupId:    detail.groupId   ?? null,
                groupName:  detail.groupName ?? '',
                yearType:   detail.yearType  ?? 'single',
                issueId:    detail.issueId   ?? null,
                loading:    false,
                confirming: false,
            };
            this.formErrors = {};
            this.form = detail.data ? { ...detail.data } : {};
        },

        // ── Validation ──────────────────────────────────────────────────────────
        validate(rules) {
            this.formErrors = {};
            for (const r of rules) {
                if (!r.check) this.formErrors[r.key] = true;
            }
            const failed = rules.filter(r => !r.check);
            if (!failed.length) return true;
            lmiToast(`"${failed[0].label}" is required.`, 'warning');
            return false;
        },

        // ── Close modal + refresh Alpine state without page reload ──────────────
async done(returnedGroupData = null) {
    this.modal.open = false;

    if (returnedGroupData) {
        Object.keys(returnedGroupData).forEach(gId => {
            const card = document.getElementById(`lmi-card-${gId}`);
            if (!card) return;

            const state = Alpine.$data(card);
            if (!state) return;

            state.issues          = returnedGroupData[gId].issues;
            state.isPublished     = returnedGroupData[gId].is_published;
            state.hasDraftChanges = returnedGroupData[gId].has_draft_changes;

            const years = [...new Set(state.issues.map(i => String(i.year)))].sort((a, b) => b.localeCompare(a));
            if (years.length && !years.includes(state.activeYear)) {
                state.activeYear = years[0];
            }
        });
    } else {
        await lmiRefreshAllGroups();
    }
},

        fail(msg) {
            this.modal.loading = false;
            lmiToast(msg || 'Something went wrong.', 'error');
        },

        // ── Lightbox helpers ──────────────────────────────────────────────────
        openLightbox(src, caption = '') {
            this.lightbox = {
                open:    true,
                src:     src,
                caption: caption,
                scale:   0.3,
                panX:    0,
                panY:    0,
                drag:    false,
                dx:      0,
                dy:      0,
            };
        },
        lmiCloseLightbox() {
            this.lightbox.open    = false;
            this.lightbox.scale   = 0.3;
            this.lightbox.panX    = 0;
            this.lightbox.panY    = 0;
            this.lightbox.drag    = false;
        },

        // ── Year format hint ────────────────────────────────────────────────────
        get yearPlaceholder() {
            return this.modal.yearType === 'range' ? 'e.g. 2026-2027' : 'e.g. 2024';
        },
        get yearPattern() {
            return this.modal.yearType === 'range' ? '^\\d{4}-\\d{4}$' : '^\\d{4}$';
        },
        get yearHint() {
            return this.modal.yearType === 'range'
                ? 'Enter a year range, e.g. 2026-2027'
                : 'Enter a 4-digit year, e.g. 2024';
        },

        // ── Request confirmation before submitting add/edit ────────────────────
        requestConfirm() {
            const isEdit = this.modal.type === 'edit-issue';

            const yearOk = this.modal.yearType === 'range'
                ? /^\d{4}-\d{4}$/.test(this.form.year ?? '')
                : /^\d{4}$/.test(this.form.year ?? '');

            const rules = [
                { key: 'title',       label: 'Title',           check: !!this.form.title?.trim() },
                { key: 'subtitle',    label: 'Subtitle',        check: !!this.form.subtitle?.trim() },
                { key: 'year',        label: 'Year',            check: yearOk },
                { key: 'description', label: 'Description',     check: !!this.form.description?.trim() },
                { key: 'drive_url',   label: 'Google Drive URL',check: !!this.form.drive_url?.trim() },
            ];
            if (!this.validate(rules)) return;

            this.modal.confirming = true;
        },

        // ── Submit: add / edit issue ────────────────────────────────────────────
        async submitIssue() {
            const isEdit = this.modal.type === 'edit-issue';

            this.modal.loading = true;

            const body = {
                title:       this.form.title?.trim(),
                subtitle:    this.form.subtitle?.trim() || null,
                description: this.form.description?.trim() || null,
                year:        this.form.year.trim(),
                drive_url:   this.form.drive_url?.trim() || null,
            };

            let res;
            if (isEdit) {
                res = await lmiJson('PUT', `/admin/lmi-publication/issues/${this.modal.issueId}`, body);
            } else {
                body.group_id = this.modal.groupId;
                res = await lmiJson('POST', '/admin/lmi-publication/issues', body);
            }

            if (res.success) {
                lmiToast(isEdit ? 'Publication updated.' : 'Publication added.', 'success');
                await this.done(res.groupData);
            } else {
                this.fail(res.message);
            }
        },

        // ── Submit: delete ──────────────────────────────────────────────────────
        async submitDelete() {
            this.modal.loading = true;
            const res = await lmiJson('DELETE', `/admin/lmi-publication/issues/${this.modal.issueId}`);

            if (res.success) {
                lmiToast('Publication deleted.', 'success');
                await this.done(res.groupData);
            } else {
                this.fail(res.message);
            }
        },

        // ── Submit: publish ─────────────────────────────────────────────────────
        async submitTogglePublish() {
            this.modal.loading = true;
            const res = await lmiJson('PATCH', `/admin/lmi-publication/${this.modal.groupId}/toggle-publish`);

            if (res.success) {
                lmiToast('Publish status updated.', 'success');
                await this.done(res.groupData);
            } else {
                this.fail(res.message);
            }
        },

        // ── Submit: unpublish ───────────────────────────────────────────────────
        async submitUnpublish() {
            return this.submitTogglePublish();
        },

        // ── Submit: republish ───────────────────────────────────────────────────
        async submitRepublish() {
            this.modal.loading = true;
            const res = await lmiJson('PATCH', `/admin/lmi-publication/${this.modal.groupId}/republish`);

            if (res.success) {
                lmiToast('Changes republished successfully.', 'success');
                await this.done(res.groupData);
            } else {
                this.fail(res.message);
            }
        },
    };
}

// ── Global exports ────────────────────────────────────────────────────────────
window.lmiAdminPage        = lmiAdminPage;
window.lmiToast            = lmiToast;
window.lmiRefreshAllGroups = lmiRefreshAllGroups;

// ── Lightbox shims (keep existing blade calls working) ────────────────────────
// The blade calls openLightbox(url, caption) via plain JS; these shims forward
// the call into the lmiAdminPage() Alpine instance on <body>.
function openLightbox(src, caption) {
    const body = document.querySelector('body[x-data]');
    if (body) Alpine.$data(body).openLightbox(src, caption || '');
}
function closeLightbox() {
    const body = document.querySelector('body[x-data]');
    if (body) Alpine.$data(body).lmiCloseLightbox();
}
window.openLightbox  = openLightbox;
window.closeLightbox = closeLightbox;