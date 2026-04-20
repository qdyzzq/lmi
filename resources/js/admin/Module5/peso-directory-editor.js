// ─── Carousel Section ────────────────────────────────────────────────────────
function pesoCarouselSection(slides) {
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


// ─── Carousel Modals ─────────────────────────────────────────────────────────
function pesoCarouselModals() {
    return {
        modal: null,
        saving: false,
        addFile: null,
        addPreview: null,
        addError: '',
        editData: { id: null, image: null, sort_order: 0 },
        editFile: null,
        editPreview: null,
        editError: '',
        deleteId: null,

        init() {},

        handleOpen(detail) {
            this.modal = detail.type;
            if (detail.type === 'edit-slide') {
                this.editData    = { ...detail.data };
                this.editFile    = null;
                this.editPreview = null;
                this.editError   = '';
            }
            if (detail.type === 'delete-slide') {
                this.deleteId = detail.id;
            }
            if (detail.type === 'add-slide') {
                this.addFile    = null;
                this.addPreview = null;
                this.addError   = '';
            }
        },

        close() { this.modal = null; },

        handleDropAdd(event) {
            const file = event.dataTransfer.files[0];
            if (file) this._processFile(file, 'add');
        },

        handleDropEdit(event) {
            const file = event.dataTransfer.files[0];
            if (file) this._processFile(file, 'edit');
        },

        _processFile(file, mode) {
            const ALLOWED = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

            if (!ALLOWED.includes(file.type)) {
                const err = 'Invalid file type. Please upload a JPG, PNG, WebP, or GIF.';
                if (mode === 'add') this.addError = err;
                else this.editError = err;
                return;
            }

            if (file.size > 20 * 1024 * 1024) {
                const err = 'Image is too large (max 20 MB).';
                if (mode === 'add') this.addError = err;
                else this.editError = err;
                return;
            }

            if (mode === 'add') this.addError = '';
            else this.editError = '';

            const img = new Image();
            const objectUrl = URL.createObjectURL(file);

            img.onload = () => {
                URL.revokeObjectURL(objectUrl);

                const MAX_WIDTH  = 1920;
                const MAX_HEIGHT = 1080;
                let w = img.width;
                let h = img.height;

                if (w > MAX_WIDTH || h > MAX_HEIGHT) {
                    const ratio = Math.min(MAX_WIDTH / w, MAX_HEIGHT / h);
                    w = Math.round(w * ratio);
                    h = Math.round(h * ratio);
                }

                const canvas = document.createElement('canvas');
                canvas.width  = w;
                canvas.height = h;
                canvas.getContext('2d').drawImage(img, 0, 0, w, h);

                const outputType    = file.type === 'image/gif' ? 'image/gif' : 'image/webp';
                const outputQuality = 0.85;

                canvas.toBlob(blob => {
                    const compressed = new File([blob], file.name.replace(/\.[^.]+$/, '.webp'), { type: outputType });
                    const reader = new FileReader();
                    reader.onload = e => {
                        if (mode === 'add') {
                            this.addFile    = compressed;
                            this.addPreview = e.target.result;
                            this.addError   = '';
                        } else {
                            this.editFile    = compressed;
                            this.editPreview = e.target.result;
                            this.editError   = '';
                        }
                    };
                    reader.readAsDataURL(compressed);
                }, outputType, outputQuality);
            };

            img.onerror = () => {
                URL.revokeObjectURL(objectUrl);
                const err = 'Could not read image. Please try a different file.';
                if (mode === 'add') this.addError = err;
                else this.editError = err;
            };

            img.src = objectUrl;
        },

        previewFile(event, mode) {
            const file = event.target.files[0];
            if (!file) return;
            this._processFile(file, mode);
        },

        csrf() {
            return document.querySelector('meta[name="csrf-token"]').content;
        },

        reloadCarousel(slides) {
            const el = document.getElementById('carousel-section');
            if (el) {
                const data = Alpine.$data(el);
                data.slides.splice(0, data.slides.length, ...slides);
                data.currentSlide = 0;
            }
        },

        async submitAdd() {
            if (!this.addFile) { this.addError = 'Please select an image.'; return; }
            this.saving = true;
            this.addError = '';
            const fd = new FormData();
            fd.append('image', this.addFile);
            try {
                const res  = await fetch('/admin/peso-carousel-slides', { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrf() }, body: fd });
                const data = await res.json();
                if (data.success) { this.reloadCarousel(data.slides); this.close(); }
                else { this.addError = data.message ?? 'Upload failed.'; }
            } catch (e) {
                this.addError = 'Error: ' + e.message;
                console.error(e);
            }
            this.saving = false;
        },

        async submitEdit() {
            this.saving = true;
            this.editError = '';
            const fd = new FormData();
            fd.append('_method', 'PUT');
            if (this.editFile) fd.append('image', this.editFile);
            try {
                const res  = await fetch('/admin/peso-carousel-slides/' + this.editData.id, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrf() }, body: fd });
                const data = await res.json();
                if (data.success) { this.reloadCarousel(data.slides); this.close(); }
                else { this.editError = data.message ?? 'Update failed.'; }
            } catch { this.editError = 'Network error. Please try again.'; }
            this.saving = false;
        },

        async submitDelete() {
            this.saving = true;
            try {
                const res  = await fetch('/admin/peso-carousel-slides/' + this.deleteId, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrf(), 'Content-Type': 'application/json' } });
                const data = await res.json();
                if (data.success) { this.reloadCarousel(data.slides); this.close(); }
            } catch {}
            this.saving = false;
        },
    };
}


// ─── PESO Info Editor ─────────────────────────────────────────────────────────
// NOTE: Blade data is injected via window._pesoInitData (set in the inline
//       <script> block in the blade, just before @vite loads this file).
function pesoInfoEditor() {
    const d = window._pesoInitData ?? {};
    const pi = d.pesoInfo ?? {};
    return {
        collapsed: false,
        saving: false,
        publishing: false,
        pesoInfoHasDraft:  d.pesoInfoHasDraft  ?? false,
        pesoInfoChangelog: d.pesoInfoChangelog  ?? [],

        form: {
            description:   pi.description   ?? '',
            objective:     pi.objective      ?? '',
            how_to_avail:  pi.how_to_avail   ?? '',
            core_services: pi.core_services  ?? [],
            beneficiaries: pi.beneficiaries  ?? [],
        },

        extraSections: pi.extra_sections ?? [],

        _saved: {
            description:    pi.description   ?? '',
            objective:      pi.objective      ?? '',
            how_to_avail:   pi.how_to_avail   ?? '',
            core_services:  JSON.stringify(pi.core_services  ?? []),
            beneficiaries:  JSON.stringify(pi.beneficiaries  ?? []),
            extra_sections: JSON.stringify(pi.extra_sections ?? []),
        },

        init() {
            this.$nextTick(() => {
                this.initQuill('quill-peso-description',  'quill-peso-description-wordcount',  'description');
                this.initQuill('quill-peso-objective',    'quill-peso-objective-wordcount',    'objective');
                this.initQuill('quill-peso-how-to-avail', 'quill-peso-how-to-avail-wordcount', 'how_to_avail');
                this.extraSections.forEach((_, idx) => this.initExtraQuill(idx));
            });
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
            if (window._quillInstances[editorId]) return;

            const quill = new Quill('#' + editorId, {
                theme: 'snow',
                placeholder: 'Enter text...',
                modules: {
                    toolbar: [
                        [{ font: [] }, { size: ['8pt', '10pt', '11pt', '12pt', '14pt', '16pt', '18pt', '24pt', '36pt'] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ color: [] }, { background: [] }],
                        [{ header: [1, 2, 3, false] }],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        [{ align: [] }],
                        ['link'],
                        ['clean'],
                    ]
                }
            });
            window._quillInstances[editorId] = quill;
            quill.root.innerHTML = this.form[formField] || '';

            const updateWordCount = () => {
                const text  = quill.root.innerText.trim();
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

        initExtraQuill(idx) {
            const editorId  = 'quill-extra-' + idx;
            const wordCntId = 'quill-extra-wordcount-' + idx;
            if (!window._quillInstances) window._quillInstances = {};
            if (window._quillInstances[editorId]) return;
            const el = document.getElementById(editorId);
            if (!el) return;

            const quill = new Quill('#' + editorId, {
                theme: 'snow',
                placeholder: 'Enter section content...',
                modules: { toolbar: [['bold', 'italic', 'underline'], ['link', 'clean']] }
            });
            window._quillInstances[editorId] = quill;
            quill.root.innerHTML = this.extraSections[idx]?.content || '';

            const updateWc = () => {
                const text = quill.root.innerText.trim();
                const wc   = document.getElementById(wordCntId);
                if (wc) wc.textContent = text ? text.split(/\s+/).filter(w => w.length > 0).length : 0;
            };
            updateWc();
            quill.on('text-change', () => {
                if (this.extraSections[idx]) this.extraSections[idx].content = quill.root.innerHTML;
                updateWc();
            });
        },

        addExtraSection() {
            this.extraSections.push({ title: '', content: '' });
            this.$nextTick(() => this.initExtraQuill(this.extraSections.length - 1));
        },

        removeExtraSection(idx) {
            const editorId = 'quill-extra-' + idx;
            if (window._quillInstances?.[editorId]) delete window._quillInstances[editorId];
            this.extraSections.splice(idx, 1);
        },

        showToast(success, message) {
            showToast(message, success ? 'success' : 'error');
        },

        getValueForKey(key) {
            if (key === 'core_services')
                return JSON.stringify(this.form.core_services.filter(v => v.name?.trim()));
            if (key === 'beneficiaries')
                return JSON.stringify(this.form.beneficiaries.filter(v => v.name?.trim()));
            if (key === 'extra_sections')
                return JSON.stringify(this.extraSections.map(s => ({ title: s.title, content: s.content })));
            return this.form[key] ?? '';
        },

        async saveKey(key, value, label = null) {
            const res = await fetch(`/admin/peso-info/${key}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ value: String(value), label }),
            });
            const data = await res.json();
            if (!res.ok || !data.success) throw new Error(data.message ?? 'Failed to save.');
        },

        async saveKeyDraft(key, label) {
            this.saving = true;
            try {
                const value = this.getValueForKey(key);

                const stripHtmlText = html => {
                    const t = document.createElement('div');
                    t.innerHTML = html || '';
                    return t.innerText.trim();
                };
                const isTextKey  = ['description', 'objective', 'how_to_avail'].includes(key);
                const hasChanged = isTextKey
                    ? stripHtmlText(value) !== stripHtmlText(this._saved[key] ?? '')
                    : value !== (this._saved[key] ?? '');
                if (!hasChanged) {
                    this.showToast(true, `No changes to save for "${label}".`);
                    return;
                }

                const wordCount = str => str.trim().split(/\s+/).filter(Boolean).length;
                const listDiffDetail = (nowJson, savedJson, lbl) => {
                    const nowArr   = JSON.parse(nowJson   || '[]');
                    const savedArr = JSON.parse(savedJson || '[]');
                    if (nowArr.length > savedArr.length)
                        return `${lbl}: ${nowArr.length - savedArr.length} item(s) added (${nowArr.length} total)`;
                    if (nowArr.length < savedArr.length)
                        return `${lbl}: ${savedArr.length - nowArr.length} item(s) removed (${nowArr.length} total)`;
                    return `${lbl}: item(s) edited (${nowArr.length} total)`;
                };
                const textDetail = (nowHtml, savedHtml, lbl) => {
                    const t = document.createElement('div');
                    t.innerHTML = nowHtml  || ''; const wNow   = wordCount(t.innerText.trim());
                    t.innerHTML = savedHtml || ''; const wSaved = wordCount(t.innerText.trim());
                    const diff   = wNow - wSaved;
                    const detail = diff > 0 ? `+${diff} words added` : diff < 0 ? `${Math.abs(diff)} words removed` : 'text edited';
                    return `${lbl} — ${detail} (${wNow} words total)`;
                };
                let detailLabel = label;
                if (key === 'description')     detailLabel = textDetail(this.form.description,  this._saved.description,  label);
                else if (key === 'objective')      detailLabel = textDetail(this.form.objective,   this._saved.objective,   label);
                else if (key === 'how_to_avail')   detailLabel = textDetail(this.form.how_to_avail, this._saved.how_to_avail, label);
                else if (key === 'core_services')  detailLabel = listDiffDetail(value, this._saved.core_services,  label);
                else if (key === 'beneficiaries')  detailLabel = listDiffDetail(value, this._saved.beneficiaries,  label);
                else if (key === 'extra_sections') detailLabel = listDiffDetail(value, this._saved.extra_sections, label);

                await this.saveKey(key, value, detailLabel);

                this._saved[key] = value;

                this.pesoInfoHasDraft = true;
                this.pesoInfoChangelog.push({ field: key, label: detailLabel, time: new Date().toISOString() });

                this.showToast(true, `"${detailLabel}" saved as draft. Click "Publish Changes" to go live.`);
            } catch (e) {
                this.showToast(false, e.message ?? 'Failed to save.');
            } finally {
                this.saving = false;
            }
        },

        openSaveAllConfirm() {
            const changes = [];
            const stripHtml = html => { const t = document.createElement('div'); t.innerHTML = html || ''; return t.innerText.trim(); };
            const wordCount = str => str.trim().split(/\s+/).filter(Boolean).length;
            const listDiff  = (nowJson, savedJson, label) => {
                const nowArr   = JSON.parse(nowJson   || '[]');
                const savedArr = JSON.parse(savedJson || '[]');
                if (nowArr.length > savedArr.length)  return `${label}: ${nowArr.length - savedArr.length} item(s) added (${nowArr.length} total)`;
                if (nowArr.length < savedArr.length)  return `${label}: ${savedArr.length - nowArr.length} item(s) removed (${nowArr.length} total)`;
                return `${label}: item(s) edited (${nowArr.length} total)`;
            };

            const descNow   = stripHtml(this.form.description);
            const descSaved = stripHtml(this._saved.description);
            if (descNow !== descSaved) {
                const wNow = wordCount(descNow), wSaved = wordCount(descSaved);
                const diff = wNow - wSaved;
                changes.push({ icon: 'doc', text: `Description updated — ${diff > 0 ? '+'+diff+' words added' : diff < 0 ? Math.abs(diff)+' words removed' : 'text edited'} (${wNow} words total)` });
            }
            const objNow   = stripHtml(this.form.objective);
            const objSaved = stripHtml(this._saved.objective);
            if (objNow !== objSaved) {
                const wNow = wordCount(objNow), wSaved = wordCount(objSaved);
                const diff = wNow - wSaved;
                changes.push({ icon: 'doc', text: `Objective updated — ${diff > 0 ? '+'+diff+' words added' : diff < 0 ? Math.abs(diff)+' words removed' : 'text edited'} (${wNow} words total)` });
            }
            const htaNow   = stripHtml(this.form.how_to_avail);
            const htaSaved = stripHtml(this._saved.how_to_avail);
            if (htaNow !== htaSaved) {
                const wNow = wordCount(htaNow), wSaved = wordCount(htaSaved);
                const diff = wNow - wSaved;
                changes.push({ icon: 'doc', text: `How to Avail updated — ${diff > 0 ? '+'+diff+' words added' : diff < 0 ? Math.abs(diff)+' words removed' : 'text edited'} (${wNow} words total)` });
            }
            const csNow = JSON.stringify(this.form.core_services.filter(v => v.name?.trim()));
            if (csNow !== this._saved.core_services) changes.push({ icon: 'list', text: listDiff(csNow, this._saved.core_services, 'Core Services') });
            const bnNow = JSON.stringify(this.form.beneficiaries.filter(v => v.name?.trim()));
            if (bnNow !== this._saved.beneficiaries) changes.push({ icon: 'list', text: listDiff(bnNow, this._saved.beneficiaries, 'Beneficiaries') });
            const exNow = JSON.stringify(this.extraSections.map(s => ({ title: s.title, content: s.content })));
            if (exNow !== this._saved.extra_sections) changes.push({ icon: 'list', text: listDiff(exNow, this._saved.extra_sections, 'Additional Sections') });

            if (changes.length === 0) {
                this.showToast(true, 'No changes detected — all fields are already up to date.');
                return;
            }
            window._pesoInfoPendingChanges = changes;
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { type: 'save-all-confirm' } }));
        },

        async saveAll() {
            this.saving = true;
            try {
                const stripHtml = html => { const t = document.createElement('div'); t.innerHTML = html || ''; return t.innerText.trim(); };
                const now = new Date().toISOString();

                const csNow = JSON.stringify(this.form.core_services.filter(v => v.name?.trim()));
                const bnNow = JSON.stringify(this.form.beneficiaries.filter(v => v.name?.trim()));
                const exNow = JSON.stringify(this.extraSections.map(s => ({ title: s.title, content: s.content })));

                const fieldLabels = { description: 'Description', objective: 'Objective', how_to_avail: 'How to Avail', core_services: 'Core Services', beneficiaries: 'Beneficiaries', extra_sections: 'Additional Sections' };
                const changedFields = [];
                if (stripHtml(this.form.description)  !== stripHtml(this._saved.description))  changedFields.push('description');
                if (stripHtml(this.form.objective)    !== stripHtml(this._saved.objective))    changedFields.push('objective');
                if (stripHtml(this.form.how_to_avail) !== stripHtml(this._saved.how_to_avail)) changedFields.push('how_to_avail');
                if (csNow !== this._saved.core_services)  changedFields.push('core_services');
                if (bnNow !== this._saved.beneficiaries)  changedFields.push('beneficiaries');
                if (exNow !== this._saved.extra_sections) changedFields.push('extra_sections');

                await Promise.all([
                    this.saveKey('description',    this.form.description),
                    this.saveKey('objective',      this.form.objective),
                    this.saveKey('how_to_avail',   this.form.how_to_avail),
                    this.saveKey('core_services',  csNow),
                    this.saveKey('beneficiaries',  bnNow),
                    this.saveKey('extra_sections', exNow),
                ]);

                this._saved.description    = this.form.description;
                this._saved.objective      = this.form.objective;
                this._saved.how_to_avail   = this.form.how_to_avail;
                this._saved.core_services  = csNow;
                this._saved.beneficiaries  = bnNow;
                this._saved.extra_sections = exNow;

                this.pesoInfoHasDraft = true;

                if (changedFields.length > 0) {
                    const wordCount = str => str.trim().split(/\s+/).filter(Boolean).length;
                    const listDiff  = (nowJson, savedJson, label) => {
                        const nowArr = JSON.parse(nowJson || '[]'), savedArr = JSON.parse(savedJson || '[]');
                        if (nowArr.length > savedArr.length) return `${label}: ${nowArr.length - savedArr.length} item(s) added (${nowArr.length} total)`;
                        if (nowArr.length < savedArr.length) return `${label}: ${savedArr.length - nowArr.length} item(s) removed (${nowArr.length} total)`;
                        return `${label}: item(s) edited (${nowArr.length} total)`;
                    };
                    const textDetail = (nowHtml, savedHtml, label) => {
                        const t = document.createElement('div');
                        t.innerHTML = nowHtml  || ''; const wNow   = wordCount(t.innerText.trim());
                        t.innerHTML = savedHtml || ''; const wSaved = wordCount(t.innerText.trim());
                        const diff = wNow - wSaved;
                        return `${label} — ${diff > 0 ? '+'+diff+' words added' : diff < 0 ? Math.abs(diff)+' words removed' : 'text edited'} (${wNow} words total)`;
                    };
                    changedFields.forEach(field => {
                        let detail = fieldLabels[field] ?? field;
                        if      (field === 'description')    detail = textDetail(this.form.description,  this._saved.description,  'Description');
                        else if (field === 'objective')      detail = textDetail(this.form.objective,    this._saved.objective,    'Objective');
                        else if (field === 'how_to_avail')   detail = textDetail(this.form.how_to_avail, this._saved.how_to_avail, 'How to Avail');
                        else if (field === 'core_services')  detail = listDiff(csNow, this._saved.core_services,  'Core Services');
                        else if (field === 'beneficiaries')  detail = listDiff(bnNow, this._saved.beneficiaries,  'Beneficiaries');
                        else if (field === 'extra_sections') detail = listDiff(exNow, this._saved.extra_sections, 'Additional Sections');
                        this.pesoInfoChangelog.push({ field, label: detail, time: now, via: 'save_all' });
                    });
                }

                const count   = changedFields.length;
                const summary = count > 0
                    ? `${count} field${count > 1 ? 's' : ''} saved as draft (${changedFields.map(f => fieldLabels[f]).join(', ')}). Click "Publish Changes" to go live.`
                    : 'No changes detected — all fields re-saved as draft.';
                this.showToast(true, summary);
            } catch (e) {
                this.showToast(false, e.message ?? 'Something went wrong saving all fields.');
            } finally {
                this.saving = false;
            }
        },

        openPublishPesoInfoConfirm() {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { type: 'publish-peso-info' } }));
        },

        async publishPesoInfo() {
            this.publishing = true;
            try {
                const res = await fetch('/admin/peso-info/publish', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.pesoInfoHasDraft  = false;
                    this.pesoInfoChangelog = [];
                    window.dispatchEvent(new CustomEvent('show-success-modal', {
                        detail: { title: 'PESO Info Published!', message: 'The PESO Info section is now live and visible to the public.' }
                    }));
                } else {
                    showToast(data.message ?? 'Publish failed. Please try again.', 'error');
                }
            } catch (e) {
                showToast('An error occurred. Please try again.', 'error');
            } finally {
                this.publishing = false;
            }
        },
    };
}


// ─── Directory Browser ────────────────────────────────────────────────────────
function pesoDirectory() {
    return {
        province: '',
        officeType: '',
        showType: false,
        showResults: false,
        openId: null,
        pesoData: {},
        officeTypes: [],
        search: '',
        _fuseCache: {},
        async init() {
            // Normalize server data: DB column is `office_type` but JS/blade uses `entry.type`.
            const raw = window._pesoData ?? {};
            const normalized = {};
            for (const province in raw) {
                normalized[province] = raw[province].map(e => ({
                    ...e,
                    type: e.type ?? e.office_type ?? '',
                }));
            }
            this.pesoData = normalized;
            try {
                const res = await fetch('/admin/office-types', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (res.ok) this.officeTypes = await res.json();
            } catch (e) {}
            window.addEventListener('office-type-added', evt => {
                if (!this.officeTypes.includes(evt.detail.name)) { this.officeTypes.push(evt.detail.name); this.officeTypes.sort(); }
            });
            window.addEventListener('office-type-deleted', evt => {
                this.officeTypes = this.officeTypes.filter(t => t !== evt.detail.name);
                if (this.officeType === evt.detail.name) { this.officeType = ''; this.showResults = false; }
            });
            window.addEventListener('office-type-renamed', evt => {
                const idx = this.officeTypes.indexOf(evt.detail.oldName);
                if (idx !== -1) this.officeTypes.splice(idx, 1, evt.detail.newName);
                this.officeTypes.sort();
                if (this.officeType === evt.detail.oldName) this.officeType = evt.detail.newName;
            });
        },
        toggleCard(id) { this.openId = (this.openId === id) ? null : id; },
        isOpen(id) { return this.openId === id; },
        selectProvince(val) {
            this.province = val; this.officeType = ''; this.showResults = false;
            this.showType = !!val; this.openId = null; this.search = ''; this._fuseCache = {};
            if (val) this.$nextTick(() => this.$refs.typeSection?.scrollIntoView({ behavior: 'smooth', block: 'nearest' }));
        },
        selectType(t) {
            this.officeType = t; this.showResults = !!t; this.openId = null; this.search = '';
            if (t) this.$nextTick(() => this.$refs.resultsSection?.scrollIntoView({ behavior: 'smooth', block: 'nearest' }));
        },
        countFor(province, type) {
            const entries = this.pesoData?.[province] ?? [];
            if (type === 'ALL') return entries.length;
            return entries.filter(e => e.type === type).length;
        },
        // Sort entries: PESO first, then JPO, then alphabetically by name within each group
        sortEntries(entries) {
            return [...entries].sort((a, b) => {
                const typeOrder = t => t === 'PESO' ? 0 : t === 'JPO' ? 1 : 2;
                const tDiff = typeOrder(a.type) - typeOrder(b.type);
                if (tDiff !== 0) return tDiff;
                return (a.name ?? '').localeCompare(b.name ?? '');
            });
        },
        filteredEntries() {
            let entries = this.pesoData?.[this.province] ?? [];
            if (this.officeType !== 'ALL') entries = entries.filter(e => e.type === this.officeType);
            // Always sort: PESO first, then JPO, then alphabetically by name
            entries = this.sortEntries(entries);
            if (!this.search.trim()) return entries;
            const cacheKey = this.province + '|' + this.officeType;
            if (!this._fuseCache[cacheKey] || this._fuseCache[cacheKey]._list !== entries) {
                this._fuseCache[cacheKey] = new Fuse(entries, {
                    keys: [{ name: 'name', weight: 0.6 }, { name: 'persons_name', weight: 0.3 }, { name: 'type', weight: 0.1 }],
                    threshold: 0.4, distance: 200, minMatchCharLength: 2, includeScore: true,
                });
                this._fuseCache[cacheKey]._list = entries;
            }
            return this._fuseCache[cacheKey].search(this.search.trim()).map(r => r.item);
        },
        typeColor(idx, part) {
            const p = [
                { main: '#3b82f6', bg: '#eff6ff', border: '#bfdbfe' },
                { main: '#f97316', bg: '#fff7ed', border: '#fed7aa' },
                { main: '#10b981', bg: '#ecfdf5', border: '#a7f3d0' },
                { main: '#8b5cf6', bg: '#f5f3ff', border: '#ddd6fe' },
                { main: '#ec4899', bg: '#fdf2f8', border: '#fbcfe8' },
                { main: '#14b8a6', bg: '#f0fdfa', border: '#99f6e4' },
                { main: '#f59e0b', bg: '#fffbeb', border: '#fde68a' },
                { main: '#6366f1', bg: '#eef2ff', border: '#c7d2fe' },
            ];
            return p[idx % p.length][part];
        },
    };
}


// ─── Admin PESO Page ──────────────────────────────────────────────────────────
function adminPesoPage() {
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    async function jsonRequest(method, url, body = {}) {
        try {
            const res = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify(body),
            });
            if (!res.ok) {
                try {
                    const errBody = await res.json();
                    const msg = errBody.message || Object.values(errBody.errors || {})[0]?.[0] || `Server error (${res.status}).`;
                    return { success: false, message: msg };
                } catch {
                    return { success: false, message: `Server error (${res.status}).` };
                }
            }
            return res.json();
        } catch (e) {
            return { success: false, message: 'Network error. Please check your connection.' };
        }
    }

    return {
        modal: { open: false, type: null, title: '', id: null, loading: false, error: null },
        form: {},
        formErrors: {},
        allPositionTitles: [],

        get filteredPositionTitles() {
            const type     = (this.form?.type     ?? '').toUpperCase().trim();
            const province = (this.form?.province ?? '').toUpperCase().trim();
            if (!type && !province) return this.allPositionTitles;
            const rules = [
                { match: () => type === 'JPO',                                                  allowed: ['JPO MANAGER'] },
                { match: () => type === 'PESO' && province === 'DAVAO CITY',                    allowed: ['PESO MANAGER', 'DISTRICT HEAD'] },
                { match: () => type === 'PESO' && province !== 'DAVAO CITY' && province !== '', allowed: ['PESO MANAGER'] },
            ];
            const rule = rules.find(r => r.match());
            if (!rule) return this.allPositionTitles;
            return this.allPositionTitles.filter(t => rule.allowed.some(a => a.toUpperCase() === t.toUpperCase()));
        },

        async fetchPositionTitles() {
            try {
                const res = await fetch('/admin/position-titles', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (res.ok) this.allPositionTitles = await res.json();
            } catch (e) {}
        },

        openModal(detail) {
            if (['add-slide', 'edit-slide', 'delete-slide'].includes(detail.type)) return;
            const titles = {
                'add-peso': 'Add PESO / JPO Office', 'edit-peso': 'Edit PESO / JPO Office',
                'delete-peso': 'Delete PESO / JPO Office', 'publish-directory': 'Publish PESO / JPO Directory',
                'publish-peso-info': 'Publish PESO Info to Public', 'delete-list-item': 'Delete Item',
                'save-all-confirm': 'Save All Changes',
            };
            this.modal = { open: true, type: detail.type, title: titles[detail.type] ?? 'Edit', id: detail.id ?? null, loading: false, error: null, listKey: detail.listKey ?? null, listIndex: detail.listIndex ?? null };
            this.formErrors = {};
            // Mutate form in-place so Alpine's reactive proxy is preserved.
            // Replacing this.form with a new object breaks x-model bindings.
            const incoming = detail.data ? { ...detail.data } : {};
            // Clear keys not present in incoming data
            Object.keys(this.form).forEach(k => { if (!(k in incoming)) delete this.form[k]; });
            Object.assign(this.form, incoming);
            if (detail.type === 'add-peso' || detail.type === 'edit-peso') {
                const savedPositionTitle = this.form.position_title ?? '';
                this.fetchPositionTitles().then(() => {
                    // Restore position_title after titles load (fetch resets allPositionTitles reactively)
                    this.$nextTick(() => { this.form.position_title = savedPositionTitle; });
                });
            }
        },

        fail(msg) {
            this.modal.loading = false;
            showToast(msg || 'Something went wrong. Please try again.', 'error');
        },

        confirmListItemDelete() {
            const key = this.modal.listKey;
            const idx = this.modal.listIndex;
            if (key !== null && idx !== null) {
                const editorEl   = document.getElementById('peso-info-editor');
                const editorData = editorEl?._x_dataStack?.[0];
                if (editorData && Array.isArray(editorData.form[key])) {
                    editorData.form[key] = editorData.form[key].filter((_, i) => i !== idx);
                }
            }
            this.modal.open = false;
        },

        async submitFieldOffice() {
            this.formErrors = {};
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!this.form.name?.trim())          this.formErrors.name = true;
            if (!this.form.type)                  this.formErrors.type = true;
            if (!this.form.province?.trim())       this.formErrors.province = true;
            if (!this.form.persons_name?.trim())   this.formErrors.persons_name = true;
            if (!this.form.position_title?.trim()) this.formErrors.position_title = true;
            if (!this.form.email?.trim() || !emailRegex.test(this.form.email.trim())) this.formErrors.email = true;
            if (!this.form.address?.trim())        this.formErrors.address = true;
            if (Object.keys(this.formErrors).length) return;

            this.modal.loading = true;
            const isEdit = this.modal.type === 'edit-peso';
            const body = {
                name: this.form.name,
                office_type: this.form.type,  // used by controller
                type: this.form.type,          // alias in case controller reads `type`
                province: this.form.province,
                persons_name: this.form.persons_name,
                position_title: this.form.position_title,
                email: this.form.email,
                address: this.form.address
            };
            const res = await jsonRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/admin/field-offices/${this.modal.id}` : '/admin/field-offices', body);

            if (res.success) {
                const prov = this.form.province;
                const pesoState = document.getElementById('peso-ajax-container')?._x_dataStack?.[0];
                if (pesoState) {
                    if (!pesoState.pesoData[prov]) pesoState.pesoData[prov] = [];
                    if (isEdit) {
                        const idx = pesoState.pesoData[prov].findIndex(e => e.id === this.modal.id);
                        if (idx !== -1) {
                            pesoState.pesoData[prov][idx] = { ...pesoState.pesoData[prov][idx], name: body.name, type: body.office_type, persons_name: body.persons_name, position_title: body.position_title, email: body.email, address: body.address, id: this.modal.id };
                            pesoState.pesoData[prov] = [...pesoState.pesoData[prov]];
                        }
                    } else {
                        pesoState.pesoData[prov] = [...pesoState.pesoData[prov], { id: res.id ?? Date.now(), name: body.name, type: body.office_type, persons_name: body.persons_name, position_title: body.position_title, email: body.email, address: body.address }];
                    }
                }
                const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                Alpine.store('pesoDirectory').markDirty({ action: isEdit ? 'edited' : 'added', label: body.name, type: body.office_type, province: prov, time: timeStr });
                jsonRequest('POST', '/admin/field-offices/touch', { action: isEdit ? 'edited' : 'added', label: body.name, type: body.office_type, province: prov, time: timeStr }).catch(() => {});
                this.modal.open = false;
                window.dispatchEvent(new CustomEvent('show-success-modal', {
                    detail: { title: isEdit ? 'Office Updated!' : 'Office Added!', message: escapeText(body.name) + (isEdit ? ' has been updated successfully.' : ' has been added to ' + escapeText(prov) + '.') }
                }));
            } else {
                this.fail(res.message);
            }
        },

        async destroyFieldOffice() {
            this.modal.loading = true;
            const res = await jsonRequest('DELETE', `/admin/field-offices/${this.modal.id}`);
            if (res.success) {
                const pesoState = document.getElementById('peso-ajax-container')?._x_dataStack?.[0];
                let deletedName = 'Unknown', deletedType = '', deletedProv = '';
                if (pesoState) {
                    for (const prov in pesoState.pesoData) {
                        const found = pesoState.pesoData[prov].find(e => e.id === this.modal.id);
                        if (found) { deletedName = found.name; deletedType = found.type; deletedProv = prov; break; }
                    }
                    for (const prov in pesoState.pesoData) {
                        pesoState.pesoData[prov] = pesoState.pesoData[prov].filter(e => e.id !== this.modal.id);
                    }
                }
                const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                Alpine.store('pesoDirectory').markDirty({ action: 'deleted', label: deletedName, type: deletedType, province: deletedProv, time: timeStr });
                jsonRequest('POST', '/admin/field-offices/touch', { action: 'deleted', label: deletedName, type: deletedType, province: deletedProv, time: timeStr }).catch(() => {});
                this.modal.open = false;
                window.dispatchEvent(new CustomEvent('show-success-modal', {
                    detail: { title: 'Office Deleted', message: escapeText(deletedName) + ' has been removed from the directory.' }
                }));
            } else {
                this.fail(res.message);
            }
        },

        async submitPublishDirectory() {
            this.modal.loading = true;
            const res = await jsonRequest('POST', '/admin/field-offices/publish');
            if (res.success) {
                Alpine.store('pesoDirectory').reset();
                this.modal.open = false;
                window.dispatchEvent(new CustomEvent('show-success-modal', {
                    detail: { title: 'Directory Published!', message: 'The PESO / JPO Directory is now live and visible to the public.' }
                }));
            } else {
                this.fail(res.message ?? 'Failed to publish directory.');
            }
        },
    };
}


// ─── Office Type Selector ─────────────────────────────────────────────────────
function officeTypeSelector() {
    return {
        types: [], mode: 'select', inputName: '', saving: false, typeError: '',
        async init() {
            try {
                const res = await fetch('/admin/office-types', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (res.ok) this.types = await res.json();
            } catch (e) {}
        },
        startEdit() { this.inputName = ''; this.mode = 'edit'; },
        async saveNewType(form) {
            this.typeError = '';
            const name = this.inputName.trim().toUpperCase();
            if (!name) { this.typeError = 'Please enter a type name.'; return; }
            if (this.types.includes(name)) { this.typeError = 'That type already exists.'; return; }
            this.saving = true;
            try {
                const res = await fetch('/admin/office-types', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ name }) });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.types.push(data.name); this.types.sort(); form.type = data.name; this.mode = 'select'; this.inputName = '';
                    window.dispatchEvent(new CustomEvent('office-type-added', { detail: { name: data.name } }));
                } else { this.typeError = data.message ?? 'Failed to save type.'; }
            } catch (e) { this.typeError = 'Network error. Please try again.'; }
            this.saving = false;
        },
        async updateType(form) {
            this.typeError = '';
            const oldName = form.type, newName = this.inputName.trim().toUpperCase();
            if (!newName) { this.typeError = 'Please enter a new name.'; return; }
            if (newName === oldName) { this.mode = 'select'; return; }
            if (this.types.includes(newName)) { this.typeError = 'That type already exists.'; return; }
            this.saving = true;
            try {
                const res = await fetch('/admin/office-types/' + encodeURIComponent(oldName), { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ name: newName }) });
                const data = await res.json();
                if (res.ok && data.success) {
                    const idx = this.types.indexOf(oldName);
                    if (idx !== -1) this.types.splice(idx, 1, newName);
                    this.types.sort(); form.type = newName; this.mode = 'select'; this.inputName = '';
                    window.dispatchEvent(new CustomEvent('office-type-renamed', { detail: { oldName, newName } }));
                } else { this.typeError = data.message ?? 'Failed to rename type.'; }
            } catch (e) { this.typeError = 'Network error. Please try again.'; }
            this.saving = false;
        },
        async deleteType(form) {
            const name = form.type;
            this.saving = true;
            try {
                const res = await fetch('/admin/office-types/' + encodeURIComponent(name), { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.types = this.types.filter(t => t !== name); form.type = ''; this.mode = 'select';
                    window.dispatchEvent(new CustomEvent('office-type-deleted', { detail: { name } }));
                    window.dispatchEvent(new CustomEvent('show-success-modal', { detail: { title: 'Type Deleted', message: 'Office type "' + escapeText(name) + '" has been removed.' } }));
                } else { this.typeError = data.message ?? 'Failed to delete type.'; this.mode = 'select'; }
            } catch (e) { this.typeError = 'Network error. Please try again.'; this.mode = 'select'; }
            this.saving = false;
        },
    };
}


// ─── Position Title Selector ──────────────────────────────────────────────────
const POSITION_TITLE_RULES = [
    { match: (type, province) => type === 'JPO',                                                  allowed: ['JPO MANAGER'] },
    { match: (type, province) => type === 'PESO' && province === 'DAVAO CITY',                    allowed: ['PESO MANAGER', 'DISTRICT HEAD'] },
    { match: (type, province) => type === 'PESO' && province !== 'DAVAO CITY' && province !== '', allowed: ['PESO MANAGER'] },
];

function positionTitleSelector() {
    return {
        titles: [], mode: 'select', inputName: '', saving: false, titleError: '',

        async init() {
            try {
                const res = await fetch('/admin/position-titles', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (res.ok) this.titles = await res.json();
            } catch (e) {}
        },

        startEdit() {
            this.inputName = ''; this.mode = 'edit';
            this.$nextTick(() => this.$refs.editTitleInput?.focus());
        },

        async saveNewTitle(form) {
            this.titleError = '';
            const name = this.inputName.trim();
            if (!name) { this.titleError = 'Please enter a position title.'; return; }
            if (this.titles.includes(name)) { this.titleError = 'That title already exists.'; return; }
            this.saving = true;
            try {
                const res = await fetch('/admin/position-titles', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ name }) });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.titles.push(data.name); this.titles.sort();
                    const _p = this.$el.closest('[x-data]')?._x_dataStack?.find(d => 'allPositionTitles' in d);
                    if (_p) { _p.allPositionTitles = [...this.titles]; }
                    form.position_title = data.name; this.mode = 'select'; this.inputName = '';
                } else { this.titleError = data.message ?? 'Failed to save.'; }
            } catch (e) { this.titleError = 'Network error. Please try again.'; }
            this.saving = false;
        },

        async updateTitle(form) {
            this.titleError = '';
            const oldName = form.position_title, newName = this.inputName.trim();
            if (!newName) { this.titleError = 'Please enter a new name.'; return; }
            if (newName === oldName) { this.mode = 'select'; return; }
            if (this.titles.includes(newName)) { this.titleError = 'That title already exists.'; return; }
            this.saving = true;
            try {
                const res = await fetch('/admin/position-titles/' + encodeURIComponent(oldName), { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ name: newName }) });
                const data = await res.json();
                if (res.ok && data.success) {
                    const idx = this.titles.indexOf(oldName);
                    if (idx !== -1) this.titles.splice(idx, 1, newName);
                    this.titles.sort(); form.position_title = newName; this.mode = 'select'; this.inputName = '';
                } else { this.titleError = data.message ?? 'Failed to rename.'; }
            } catch (e) { this.titleError = 'Network error. Please try again.'; }
            this.saving = false;
        },

        async deleteTitle(form) {
            const name = form.position_title;
            this.saving = true;
            try {
                const res = await fetch('/admin/position-titles/' + encodeURIComponent(name), { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.titles = this.titles.filter(t => t !== name); form.position_title = ''; this.mode = 'select';
                    window.dispatchEvent(new CustomEvent('show-success-modal', { detail: { title: 'Position Deleted', message: 'Position title "' + escapeText(name) + '" has been removed.' } }));
                } else { this.titleError = data.message ?? 'Failed to delete.'; this.mode = 'select'; }
            } catch (e) { this.titleError = 'Network error. Please try again.'; this.mode = 'select'; }
            this.saving = false;
        },
    };
}


// ─── Escape Helper ────────────────────────────────────────────────────────────
function escapeText(str) {
    const div = document.createElement('div');
    div.textContent = String(str ?? '');
    return div.innerHTML;
}


// ─── Toast Notification System ────────────────────────────────────────────────
function showToast(message, type = 'error') {
    const container = document.getElementById('toastContainer');
    const configs = {
        error:   { bg: 'bg-red-50 border-red-400',     icon: `<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,   text: 'text-red-800',   bar: 'bg-red-400' },
        warning: { bg: 'bg-amber-50 border-amber-400', icon: `<svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`, text: 'text-amber-800', bar: 'bg-amber-400' },
        success: { bg: 'bg-green-50 border-green-400', icon: `<svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`,                                                                                                                                                                                              text: 'text-green-800', bar: 'bg-green-400' },
        info:    { bg: 'bg-blue-50 border-blue-400',   icon: `<svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,   text: 'text-blue-800',   bar: 'bg-blue-400' },
    };
    const c = configs[type] || configs.error;
    const toast = document.createElement('div');
    toast.className = `pointer-events-auto w-full border-l-4 ${c.bg} rounded-xl shadow-xl overflow-hidden transform transition-all duration-300 translate-x-full opacity-0`;
    toast.innerHTML = `
        <div class="flex items-start gap-3 px-4 py-4">
            ${c.icon}
            <p class="text-sm font-medium ${c.text} flex-1 leading-snug">${message}</p>
            <button onclick="this.closest('.pointer-events-auto').remove()" class="text-gray-400 hover:text-gray-600 transition ml-1 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="h-1 ${c.bar}" style="animation: shrink 4s linear forwards;"></div>
    `;
    container.appendChild(toast);
    requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.remove('translate-x-full', 'opacity-0')));
    setTimeout(() => { toast.classList.add('translate-x-full', 'opacity-0'); setTimeout(() => toast.remove(), 300); }, 4000);
}

// Inject toast animation CSS once
if (!document.getElementById('toastStyle')) {
    const style = document.createElement('style');
    style.id = 'toastStyle';
    style.textContent = `@keyframes shrink { from { width: 100%; } to { width: 0%; } }`;
    document.head.appendChild(style);
}



// ─── Global Exports (required because Vite wraps modules in a private scope) ──
window.pesoCarouselSection   = pesoCarouselSection;
window.pesoCarouselModals    = pesoCarouselModals;
window.pesoInfoEditor        = pesoInfoEditor;
window.pesoDirectory         = pesoDirectory;
window.adminPesoPage         = adminPesoPage;
window.officeTypeSelector    = officeTypeSelector;
window.positionTitleSelector = positionTitleSelector;
window.escapeText            = escapeText;
window.showToast             = showToast;


// ─── Alpine Store Init ────────────────────────────────────────────────────────
document.addEventListener('alpine:init', () => {
    const d = window._pesoInitData ?? {};
    Alpine.store('pesoDirectory', {
        hasDraftChanges: d.directoryHasDraft  ?? false,
        changeLog:       d.directoryChangelog ?? [],
        markDirty(entry) { this.hasDraftChanges = true; this.changeLog.push(entry); },
        reset()          { this.hasDraftChanges = false; this.changeLog = []; },
    });
    Alpine.data('pesoCard', (id) => ({ entryId: id }));
});