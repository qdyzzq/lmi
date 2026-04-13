// =============================================================================
// Admin – LMI Submissions Review JS
// File: public/js/admin/lmi-submissions.js
// =============================================================================
// NOTE: The following values are injected via data-attributes in the Blade template:
//   #status-tabs-container[data-index-url]  → route('admin.lmi-submissions.index')
//   #lmi-page-meta[data-pending-count]      → $pendingCount
//   #lmi-page-meta[data-approved-count]     → $approvedCount
//   #lmi-page-meta[data-rejected-count]     → $rejectedCount
//   #lmi-page-meta[data-active-tab]         → $activeTab
// =============================================================================

// ─── Toast Notification System ──────────────────────────────────────────
function showToast(message, type = 'error', onExpire = null) {
    const container = document.getElementById('toastContainer');

    const configs = {
        error: {
            bg: 'bg-red-50 border-red-400',
            icon: `<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                   </svg>`,
            text: 'text-red-800',
            bar: 'bg-red-400',
        },
        warning: {
            bg: 'bg-amber-50 border-amber-400',
            icon: `<svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                   </svg>`,
            text: 'text-amber-800',
            bar: 'bg-amber-400',
        },
        success: {
            bg: 'bg-green-50 border-green-400',
            icon: `<svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                   </svg>`,
            text: 'text-green-800',
            bar: 'bg-green-400',
        },
        info: {
            bg: 'bg-blue-50 border-blue-400',
            icon: `<svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                   </svg>`,
            text: 'text-blue-800',
            bar: 'bg-blue-400',
        },
    };

    const c = configs[type] || configs.error;

    const toast = document.createElement('div');
    toast.className = `pointer-events-auto w-full border-l-4 ${c.bg} rounded-xl shadow-xl overflow-hidden
                       transform transition-all duration-300 translate-x-full opacity-0`;

    toast.innerHTML = `
        <div class="flex items-start gap-3 px-4 py-4">
            ${c.icon}
            <p class="text-sm font-medium ${c.text} flex-1 leading-snug">${message}</p>
            <button onclick="this.closest('.pointer-events-auto').remove()"
                    class="text-gray-400 hover:text-gray-600 transition ml-1 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="h-1 ${c.bar} animate-shrink" style="animation: shrink 4s linear forwards;"></div>
    `;

    container.appendChild(toast);

    // Slide in
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });
    });

    // Auto-remove after 4s, then fire onExpire callback if provided
    setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            toast.remove();
            if (typeof onExpire === 'function') onExpire();
        }, 300);
    }, 4000);
}

// CSS for the shrink progress bar
if (!document.getElementById('toastStyle')) {
    const style = document.createElement('style');
    style.id = 'toastStyle';
    style.textContent = `@keyframes shrink { from { width: 100%; } to { width: 0%; } }`;
    document.head.appendChild(style);
}

// Status tab switching
function switchStatusTab(status) {
    window.location.href = window.AppRoutes.lmiSubmissionsIndex + '?status=' + status;
}

// Content tab switching
function switchTab(button, tabId) {
    const card = button.closest('.admin-review-card');

    // Update tab button styles
    card.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'text-blue-600', 'border-blue-600');
        btn.classList.add('text-slate-600');
    });
    button.classList.add('active', 'text-blue-600', 'border-blue-600');
    button.classList.remove('text-slate-600');

    // Update tab content panels
    card.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');

    // Show the matching top-right edit group, hide the rest
    // tabId is like "company-5", "roles-5", "impact-5", "engagement-5"
    // Strip the numeric submission id to get the key e.g. "company", "roles", "impact"
    const parts = tabId.split('-');
    parts.pop(); // remove trailing id number
    const tabKey = parts.join('-');
    card.querySelectorAll('.tab-edit-group').forEach(g => g.classList.add('hidden'));
    const activeGroup = card.querySelector('.tab-edit-' + tabKey);
    if (activeGroup) activeGroup.classList.remove('hidden');
}

// Helpers — buttons now live in the tab bar (outside <form>)
function _getCard(btn) { return btn.closest('.admin-review-card'); }
function _getForm(btn, prefix) { return _getCard(btn).querySelector('[id^="' + prefix + '"]'); }

// Company Profile Edit
function toggleEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-company-');
    form.querySelectorAll('.editable-field').forEach(f => f.disabled = false);
    card.querySelector('.edit-btn').classList.add('hidden');
    card.querySelector('.save-btn').classList.remove('hidden');
    card.querySelector('.cancel-btn').classList.remove('hidden');

    // Show contact edit controls, hide view display
    const viewDisplay  = form.querySelector('.contact-view-display');
    const editControls = form.querySelector('.contact-edit-controls');
    if (viewDisplay)  viewDisplay.classList.add('hidden');
    if (editControls) editControls.classList.remove('hidden');

    // Strip dial code from mobile input NOW (field was disabled at DOMContentLoaded
    // so the pre-populate script could not read/modify its value then).
    form.querySelectorAll('.admin-mobile-field').forEach(mobileInput => {
        const wrapper = mobileInput.closest('[id^="admin-mobile-wrapper-"]');
        if (wrapper && wrapper.classList.contains('hidden')) return; // inactive type
        const subId    = mobileInput.id.replace('admin-mobile-input-', '');
        const rawValue = mobileInput.value.trim();
        const sortedCountries = [...ADMIN_COUNTRIES].sort((a, b) => b.dial.length - a.dial.length);
        const matched = sortedCountries.find(c => rawValue.startsWith(c.dial));
        if (matched) {
            const bareDigits = rawValue.slice(matched.dial.length);
            mobileInput.value = bareDigits;
            mobileInput.dataset.original = bareDigits;
            // Update the dial code span & flag to match
            const dialSpan = document.getElementById('admin-country-dial-' + subId);
            const flagSpan = document.getElementById('admin-country-flag-' + subId);
            if (dialSpan) dialSpan.textContent = matched.dial;
            if (flagSpan) flagSpan.textContent = matched.flag;
            adminSelectedCountry[subId] = matched;
        } else {
            // No dial code found — value is already bare digits, just sync data-original
            mobileInput.dataset.original = rawValue;
        }
    });
}

function cancelEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-company-');
    // Restore all fields to their data-original values so the next edit
    // session starts clean. form.reset() is NOT used because it restores
    // to the HTML value attribute, which may differ from data-original
    // (e.g. mobile field was already stripped to bare digits).
    form.querySelectorAll('[data-original]').forEach(f => {
        if (f.type === 'checkbox' || f.type === 'radio') return;
        f.value = f.dataset.original ?? f.defaultValue;
    });

    // Restore checkboxes and radios to their original checked state
    form.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(cb => {
        cb.checked = cb.defaultChecked;
    });
    form.querySelectorAll('.editable-field').forEach(f => f.disabled = true);
    card.querySelector('.edit-btn').classList.remove('hidden');
    card.querySelector('.save-btn').classList.add('hidden');
    card.querySelector('.cancel-btn').classList.add('hidden');

    // Restore contact view display, hide edit controls
    const viewDisplay  = form.querySelector('.contact-view-display');
    const editControls = form.querySelector('.contact-edit-controls');
    if (viewDisplay)  viewDisplay.classList.remove('hidden');
    if (editControls) editControls.classList.add('hidden');
}

// Roles Edit
// ─── SKILL TAG SYSTEM FOR ADMIN ROLES ──────────────────────────────────────

function initSkillTagSystem(container, addButton, textInput, hiddenInput, tagsContainer) {
    // Read existing tags from already-rendered spans
    const tags = [];
    tagsContainer.querySelectorAll('[data-tag]').forEach(el => {
        const val = el.getAttribute('data-tag');
        if (val) tags.push(val);
    });

    function renderTags() {
        tagsContainer.innerHTML = '';
        tags.forEach((tag, i) => {
            const span = document.createElement('span');
            span.className = 'inline-flex items-center gap-1 px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm';
            span.setAttribute('data-tag', tag);
            span.innerHTML = `<span>${tag}</span>
                <button type="button" class="remove-tag hover:bg-teal-200 rounded-full p-0.5" data-index="${i}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>`;
            tagsContainer.appendChild(span);
        });
        hiddenInput.value = tags.join(', ');
        // Re-bind remove buttons
        tagsContainer.querySelectorAll('.remove-tag').forEach(btn => {
            btn.addEventListener('click', e => {
                const idx = parseInt(e.currentTarget.getAttribute('data-index'));
                tags.splice(idx, 1);
                renderTags();
            });
        });
    }

    function addTag() {
        const val = textInput.value.trim().replace(/,$/, '');
        if (val && !tags.includes(val)) {
            tags.push(val);
            textInput.value = '';
            renderTags();
        }
    }

    addButton.addEventListener('click', addTag);
    textInput.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addTag();
        }
    });

    // Initial render to bind remove buttons on existing tags
    renderTags();
}

function initRolesTagSystems(form) {
    // Find all role index wrappers by looking for indexed containers
    // We look for any element with a class matching the pattern
    const allClasses = [...form.querySelectorAll('[class]')]
        .flatMap(el => [...el.classList]);
    const indices = new Set(
        allClasses
            .map(c => c.match(/^technical-tags-container-(\d+)$/))
            .filter(Boolean)
            .map(m => m[1])
    );

    indices.forEach(i => {
        // Technical
        const techTagsContainer = form.querySelector(`.technical-tags-container-${i}`);
        const techTextInput     = form.querySelector(`.technical-skill-input-${i}`);
        const techAddBtn        = form.querySelector(`.add-technical-skill-${i}`);
        const techHidden        = form.querySelector(`.technical-skills-input-${i}`);
        const techCheckbox      = form.querySelector(`.technical-checkbox-${i}`);
        const techDetails       = form.querySelector(`.technical-details-${i}`);

        if (techCheckbox && techDetails) {
            techCheckbox.addEventListener('change', () => {
                const label = techCheckbox.closest('label');
                if (techCheckbox.checked) {
                    techDetails.classList.remove('hidden');
                    label.classList.add('border-teal-500', 'bg-teal-50');
                    label.classList.remove('border-gray-200', 'hover:bg-gray-50');
                } else {
                    techDetails.classList.add('hidden');
                    label.classList.remove('border-teal-500', 'bg-teal-50');
                    label.classList.add('border-gray-200', 'hover:bg-gray-50');
                }
            });
        }

        if (techTagsContainer && techTextInput && techAddBtn && techHidden) {
            initSkillTagSystem(null, techAddBtn, techTextInput, techHidden, techTagsContainer);
        }

        // Soft
        const softTagsContainer = form.querySelector(`.soft-tags-container-${i}`);
        const softTextInput     = form.querySelector(`.soft-skill-input-${i}`);
        const softAddBtn        = form.querySelector(`.add-soft-skill-${i}`);
        const softHidden        = form.querySelector(`.soft-skills-input-${i}`);
        const softCheckbox      = form.querySelector(`.soft-checkbox-${i}`);
        const softDetails       = form.querySelector(`.soft-details-${i}`);

        if (softCheckbox && softDetails) {
            softCheckbox.addEventListener('change', () => {
                const label = softCheckbox.closest('label');
                if (softCheckbox.checked) {
                    softDetails.classList.remove('hidden');
                    label.classList.add('border-teal-500', 'bg-teal-50');
                    label.classList.remove('border-gray-200', 'hover:bg-gray-50');
                } else {
                    softDetails.classList.add('hidden');
                    label.classList.remove('border-teal-500', 'bg-teal-50');
                    label.classList.add('border-gray-200', 'hover:bg-gray-50');
                }
            });
        }

        if (softTagsContainer && softTextInput && softAddBtn && softHidden) {
            initSkillTagSystem(null, softAddBtn, softTextInput, softHidden, softTagsContainer);
        }
    });
}

// Track whether tag systems have been initialised per form
const _rolesTagInited = new WeakSet();

function toggleRolesEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-roles-');
    form.querySelectorAll('.role-editable-field').forEach(f => f.disabled = false);
    form.querySelectorAll('[class*="technical-skill-input-"], [class*="soft-skill-input-"]').forEach(el => el.disabled = false);
    form.querySelectorAll('[class*="add-technical-skill-"], [class*="add-soft-skill-"]').forEach(el => el.disabled = false);

    if (!_rolesTagInited.has(form)) {
        initRolesTagSystems(form);
        if (typeof initLmiAutocompletes === 'function') initLmiAutocompletes(form);
        _rolesTagInited.add(form);
    }

    card.querySelector('.edit-roles-btn').classList.add('hidden');
    card.querySelector('.save-roles-btn').classList.remove('hidden');
    card.querySelector('.cancel-roles-btn').classList.remove('hidden');
}

function cancelRolesEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-roles-');

    // 1. Restore plain text/select fields from data-original
    form.querySelectorAll('[data-original]').forEach(f => {
        if (f.type === 'checkbox' || f.type === 'radio') return; // handled below
        f.value = f.dataset.original ?? f.defaultValue;
    });

    // 2. Restore checkboxes & radios to their original checked state
    form.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(cb => {
        cb.checked = cb.defaultChecked;
    });

    // 3. Re-render skill tag chips from data-original on the hidden inputs,
    //    and show/hide the details section to match the restored checkbox state.
    const allClasses = [...form.querySelectorAll('[class]')].flatMap(el => [...el.classList]);
    const indices = new Set(
        allClasses.map(c => c.match(/^technical-tags-container-(\d+)$/))
                  .filter(Boolean).map(m => m[1])
    );

    indices.forEach(i => {
        // Technical
        const techHidden    = form.querySelector(`.technical-skills-input-${i}`);
        const techContainer = form.querySelector(`.technical-tags-container-${i}`);
        const techCheckbox  = form.querySelector(`.technical-checkbox-${i}`);
        const techDetails   = form.querySelector(`.technical-details-${i}`);
        const techLabel     = form.querySelector(`.technical-skills-label-${i}`);
        if (techHidden && techContainer) {
            const originalSkills = (techHidden.dataset.original || '').split(',').map(s => s.trim()).filter(Boolean);
            techContainer.innerHTML = originalSkills.map(skill => `
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm" data-tag="${skill}">
                    <span>${skill}</span>
                    <button type="button" class="remove-tag hover:bg-teal-200 rounded-full p-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </span>`).join('');
        }
        if (techDetails && techCheckbox) {
            techCheckbox.checked ? techDetails.classList.remove('hidden') : techDetails.classList.add('hidden');
        }
        if (techLabel && techCheckbox) {
            techCheckbox.checked
                ? (techLabel.classList.add('border-teal-500','bg-teal-50'), techLabel.classList.remove('border-gray-200','hover:bg-gray-50'))
                : (techLabel.classList.remove('border-teal-500','bg-teal-50'), techLabel.classList.add('border-gray-200','hover:bg-gray-50'));
        }

        // Soft
        const softHidden    = form.querySelector(`.soft-skills-input-${i}`);
        const softContainer = form.querySelector(`.soft-tags-container-${i}`);
        const softCheckbox  = form.querySelector(`.soft-checkbox-${i}`);
        const softDetails   = form.querySelector(`.soft-details-${i}`);
        const softLabel     = form.querySelector(`.soft-skills-label-${i}`);
        if (softHidden && softContainer) {
            const originalSkills = (softHidden.dataset.original || '').split(',').map(s => s.trim()).filter(Boolean);
            softContainer.innerHTML = originalSkills.map(skill => `
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm" data-tag="${skill}">
                    <span>${skill}</span>
                    <button type="button" class="remove-tag hover:bg-teal-200 rounded-full p-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </span>`).join('');
        }
        if (softDetails && softCheckbox) {
            softCheckbox.checked ? softDetails.classList.remove('hidden') : softDetails.classList.add('hidden');
        }
        if (softLabel && softCheckbox) {
            softCheckbox.checked
                ? (softLabel.classList.add('border-teal-500','bg-teal-50'), softLabel.classList.remove('border-gray-200','hover:bg-gray-50'))
                : (softLabel.classList.remove('border-teal-500','bg-teal-50'), softLabel.classList.add('border-gray-200','hover:bg-gray-50'));
        }
    });

    form.querySelectorAll('.role-editable-field').forEach(f => f.disabled = true);
    form.querySelectorAll('[class*="technical-skill-input-"], [class*="soft-skill-input-"]').forEach(el => el.disabled = true);
    form.querySelectorAll('[class*="add-technical-skill-"], [class*="add-soft-skill-"]').forEach(el => el.disabled = true);

    card.querySelector('.edit-roles-btn').classList.remove('hidden');
    card.querySelector('.save-roles-btn').classList.add('hidden');
    card.querySelector('.cancel-roles-btn').classList.add('hidden');
}

// Diagnosis Edit
function toggleDiagnosisEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-diagnosis-');
    form.querySelectorAll('.diagnosis-editable-field').forEach(f => f.disabled = false);
    card.querySelector('.edit-diagnosis-btn').classList.add('hidden');
    card.querySelector('.save-diagnosis-btn').classList.remove('hidden');
    card.querySelector('.cancel-diagnosis-btn').classList.remove('hidden');
}

function cancelDiagnosisEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-diagnosis-');
    // Restore all fields to their data-original values so the next edit
    // session starts clean. form.reset() is NOT used because it restores
    // to the HTML value attribute, which may differ from data-original
    // (e.g. mobile field was already stripped to bare digits).
    form.querySelectorAll('[data-original]').forEach(f => {
        if (f.type === 'checkbox' || f.type === 'radio') return;
        f.value = f.dataset.original ?? f.defaultValue;
    });

    // Restore checkboxes and radios to their original checked state
    form.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(cb => {
        cb.checked = cb.defaultChecked;
    });
    form.querySelectorAll('.diagnosis-editable-field').forEach(f => f.disabled = true);
    card.querySelector('.edit-diagnosis-btn').classList.remove('hidden');
    card.querySelector('.save-diagnosis-btn').classList.add('hidden');
    card.querySelector('.cancel-diagnosis-btn').classList.add('hidden');
}

// Engagement Edit
function toggleEngagementEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-engagement-');
    form.querySelectorAll('.engagement-editable-field').forEach(f => f.disabled = false);
    card.querySelector('.edit-engagement-btn').classList.add('hidden');
    card.querySelector('.save-engagement-btn').classList.remove('hidden');
    card.querySelector('.cancel-engagement-btn').classList.remove('hidden');

    const otherCheckbox = form.querySelector('.admin-lmi-other-checkbox');
    const otherInput = form.querySelector('.admin-lmi-other-input');
    if (otherCheckbox && otherInput) {
        otherCheckbox.addEventListener('change', function () {
            this.checked ? otherInput.classList.remove('hidden') : otherInput.classList.add('hidden');
        });
    }
}

function cancelEngagementEdit(button) {
    const card = _getCard(button);
    const form = _getForm(button, 'form-engagement-');
    // Restore all fields to their data-original values so the next edit
    // session starts clean. form.reset() is NOT used because it restores
    // to the HTML value attribute, which may differ from data-original
    // (e.g. mobile field was already stripped to bare digits).
    form.querySelectorAll('[data-original]').forEach(f => {
        if (f.type === 'checkbox' || f.type === 'radio') return;
        f.value = f.dataset.original ?? f.defaultValue;
    });

    // Restore checkboxes and radios to their original checked state
    form.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(cb => {
        cb.checked = cb.defaultChecked;
    });
    form.querySelectorAll('.engagement-editable-field').forEach(f => f.disabled = true);

    // Re-evaluate other input visibility after checkbox restore
    const otherCheckbox = form.querySelector('.admin-lmi-other-checkbox');
    const otherInput = form.querySelector('.admin-lmi-other-input');
    if (otherCheckbox && otherInput) {
        otherCheckbox.checked ? otherInput.classList.remove('hidden') : otherInput.classList.add('hidden');
    }

    card.querySelector('.edit-engagement-btn').classList.remove('hidden');
    card.querySelector('.save-engagement-btn').classList.add('hidden');
    card.querySelector('.cancel-engagement-btn').classList.add('hidden');
}

// Modal Management Functions
let currentSubmissionId = null;
let currentForm = null;
let detectedChanges = [];

// Form Submit Handler with Change Detection
function handleFormSubmit(event, form, formType) {
    event.preventDefault();
    
    // Detect changes
    detectedChanges = [];
    const fields = form.querySelectorAll('[data-original]');
    
    fields.forEach(field => {
        // SKIP fields inside a hidden wrapper — the mobile/telephone inputs both
        // carry data-original but only one wrapper is visible at a time.
        // We check the wrapper instead of field.disabled because toggleEdit()
        // re-enables ALL editable-fields including the inactive contact type.
        const contactWrapper = field.closest('[id^="admin-mobile-wrapper-"], [id^="admin-telephone-wrapper-"]');
        if (contactWrapper && contactWrapper.classList.contains('hidden')) return;

        const originalValue = field.getAttribute('data-original');
        const label         = field.getAttribute('data-label');

        // FIX 1: Contact Number (mobile)
        // data-original now stores bare digits (dial code stripped server-side in Blade).
        // field.value also holds bare digits (DOMContentLoaded pre-populate strips it).
        // Comparison is digits vs digits — no stripping needed here.
        // displayNew reassembles dial + bare so the modal shows the full number.
        let currentValue = field.value;
        let displayNew   = field.value;

        if (field.classList.contains('admin-mobile-field')) {
            const subId    = field.id.replace('admin-mobile-input-', '');
            const dialSpan = document.getElementById('admin-country-dial-' + subId);
            const dial     = dialSpan ? dialSpan.textContent.trim() : '+63';
            const bare     = field.value.trim();
            currentValue   = bare; // stays as bare digits for comparison against data-original
            displayNew     = bare ? dial + bare : ''; // full number shown in the modal
        }

        // FIX 2: Industry Sector / any <select>
        // field.value is the stored option value. Use the visible option text
        // in the modal so users see the full label instead of the short key.
        if (field.tagName === 'SELECT') {
            displayNew = field.options[field.selectedIndex]?.text ?? currentValue;
        }

        if (originalValue !== currentValue) {
            detectedChanges.push({
                label: label,
                old:   originalValue,
                new:   displayNew,
                value: currentValue
            });
        }
    });
    
    // If no changes detected — show toast, then auto-cancel edit mode after timer
    if (detectedChanges.length === 0) {
        // Figure out which cancel function to call based on the form id
        const formId = form.id; // e.g. "form-company-5"
        let cancelFn = null;
        if (formId.startsWith('form-company-')) {
            const cancelBtn = form.closest('.admin-review-card').querySelector('.cancel-btn');
            cancelFn = () => cancelBtn && cancelEdit(cancelBtn);
        } else if (formId.startsWith('form-roles-')) {
            const cancelBtn = form.closest('.admin-review-card').querySelector('.cancel-roles-btn');
            cancelFn = () => cancelBtn && cancelRolesEdit(cancelBtn);
        } else if (formId.startsWith('form-diagnosis-')) {
            const cancelBtn = form.closest('.admin-review-card').querySelector('.cancel-diagnosis-btn');
            cancelFn = () => cancelBtn && cancelDiagnosisEdit(cancelBtn);
        } else if (formId.startsWith('form-engagement-')) {
            const cancelBtn = form.closest('.admin-review-card').querySelector('.cancel-engagement-btn');
            cancelFn = () => cancelBtn && cancelEngagementEdit(cancelBtn);
        }
        showToast('No changes detected. Exiting edit mode...', 'info', cancelFn);
        return false;
    }
    
    // Show confirmation modal with changes
    currentForm = form;
    showEditChangesModal(detectedChanges, formType);
    return false;
}

function showEditChangesModal(changes, formType) {
    const changesList = document.getElementById('changesList');
    changesList.innerHTML = '';
    
    changes.forEach(change => {
        const changeItem = document.createElement('div');
        changeItem.className = 'bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg';
        changeItem.innerHTML = `
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
                <div class="flex-1">
                    <p class="font-bold text-gray-900 mb-1">${change.label}</p>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="bg-white p-2 rounded border border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">From:</p>
                            <p class="text-gray-700 font-medium">${change.old || '(empty)'}</p>
                        </div>
                        <div class="bg-white p-2 rounded border border-blue-300">
                            <p class="text-xs text-blue-600 mb-1">To:</p>
                            <p class="text-blue-700 font-bold">${change.new || '(empty)'}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        changesList.appendChild(changeItem);
    });
    
    document.getElementById('editChangesModal').classList.remove('hidden');
}

function closeEditChangesModal() {
    document.getElementById('editChangesModal').classList.add('hidden');
    currentForm = null;
    detectedChanges = [];
}

function confirmEditChanges() {
    if (!currentForm) return;

    // Capture form reference BEFORE closeEditChangesModal()
    // because that function sets currentForm = null.
    const form = currentForm;
    closeEditChangesModal();

    const url    = form.action;
    const method = (form.querySelector('input[name="_method"]')?.value || form.method).toUpperCase();
    const fd     = new FormData(form);

    // FIX 3: Explicitly set the assembled contact number in FormData.
    // The form has TWO inputs named "contact_number" (mobile + telephone),
    // so modifying the DOM value is not enough — FormData picks up both and
    // the server receives the last (empty) one. fd.set() replaces both entries
    // with the single correct full number, only for the company form.
    if (form.id && form.id.startsWith('form-company-')) {
        const mobileWrapper = form.querySelector('[id^="admin-mobile-wrapper-"]');
        if (mobileWrapper && !mobileWrapper.classList.contains('hidden')) {
            const mobileInput = mobileWrapper.querySelector('.admin-mobile-field');
            if (mobileInput) {
                const subId    = mobileInput.id.replace('admin-mobile-input-', '');
                const dialSpan = document.getElementById('admin-country-dial-' + subId);
                const dial     = dialSpan ? dialSpan.textContent.trim() : '+63';
                const bare     = mobileInput.value.trim();
                if (bare) fd.set('contact_number', dial + bare);
            }
        }
    }

    // Show a saving indicator
    const saveBtn = form.closest('.admin-review-card')?.querySelector('.save-btn, .save-roles-btn, .save-diagnosis-btn, .save-engagement-btn');
    if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Saving…'; }

    fetch(url, {
        method: 'POST', // Laravel needs POST + _method spoofing for PUT
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json, text/html, */*',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: fd
    })
    .then(async res => {
        if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Save'; }

        if (res.ok) {
            showToast('Changes saved successfully.', 'success');
            setTimeout(() => window.location.reload(), 1200);
            return;
        }

        // Try to parse validation errors from JSON response
        const ct = res.headers.get('Content-Type') || '';
        if (ct.includes('application/json')) {
            const data = await res.json();
            // Laravel validation errors come back as { errors: { field: ['msg'] } }
            if (data.errors) {
                const messages = Object.values(data.errors).flat().join(' ');
                showToast(messages, 'error');
            } else if (data.message) {
                showToast(data.message, 'error');
            } else {
                showToast('Something went wrong. Please try again.', 'error');
            }
        } else {
            showToast(`Server error (${res.status}). Please try again.`, 'error');
        }
    })
    .catch(() => {
        if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Save'; }
        showToast('Network error. Please check your connection.', 'error');
    });
}

// ═══════════════════════════════════════════════════════
// TAB REVIEW CHECKLIST SYSTEM
// ═══════════════════════════════════════════════════════
const _CL_TABS = ['company','roles','impact','engagement'];
const _clReviewed = { company:false, roles:false, impact:false, engagement:false };
let _clSubId   = null;
let _clCompany = null;

/**
 * Called from the "Mark as Reviewed" button at the bottom of each tab.
 * Clicking again will toggle it back to unreviewed.
 */
function markTabReviewed(tabKey, submissionId) {
    const isCurrentlyReviewed = _clReviewed[tabKey];
    _clReviewed[tabKey] = !isCurrentlyReviewed;
    if (!_clSubId) _clSubId = submissionId;

    const bar = document.getElementById('tab-review-bar-' + tabKey + '-' + submissionId);
    const btn = document.getElementById('tab-review-btn-' + tabKey + '-' + submissionId);

    if (_clReviewed[tabKey]) {
        // Mark as reviewed
        if (bar) bar.classList.add('is-reviewed');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Reviewed — click to undo`;
            btn.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all bg-green-100 border-green-400 text-green-700 hover:bg-red-50 hover:border-red-300 hover:text-red-600';
        }
        showToast('Tab marked as reviewed.', 'success');
    } else {
        // Unmark
        if (bar) bar.classList.remove('is-reviewed');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Mark as Reviewed`;
            btn.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all bg-white border-slate-300 text-slate-600 hover:border-teal-500 hover:text-teal-700 hover:bg-teal-50';
        }
        showToast('Tab unmarked as reviewed.', 'info');
    }

    // Update footer progress hint
    _updateProgressHint(submissionId);

    // If checklist modal is open, refresh its state
    if (!document.getElementById('checklistModal').classList.contains('hidden')) {
        _renderChecklistModal();
    }
}

/** Updates the footer "x / 4 tabs reviewed" hint. */
function _updateProgressHint(submissionId) {
    const done = _CL_TABS.filter(t => _clReviewed[t]).length;
    const el   = document.getElementById('checklist-progress-text-' + submissionId);
    if (!el) return;
    if (done === 4) {
        el.textContent = 'All tabs reviewed — ready to approve';
        el.classList.add('text-green-600', 'font-semibold');
        el.classList.remove('text-slate-500');
        const icon = el.previousElementSibling;
        if (icon) { icon.classList.remove('text-amber-500'); icon.classList.add('text-green-500'); }
    } else {
        el.textContent = done + ' / 4 tabs reviewed';
    }
}

/**
 * Opens the checklist modal when Approve is clicked.
 */
function openChecklistModal(submissionId, companyName) {
    _clSubId   = submissionId;
    _clCompany = companyName;

    // Set company name in the checklist modal banner
    const clName = document.getElementById('cl-company-name');
    if (clName) clName.textContent = companyName;

    // Sync state from DOM (in case tabs were already marked)
    _CL_TABS.forEach(tab => {
        const bar = document.getElementById('tab-review-bar-' + tab + '-' + submissionId);
        if (bar && bar.classList.contains('is-reviewed')) _clReviewed[tab] = true;
        else _clReviewed[tab] = false;
    });

    _renderChecklistModal();
    document.getElementById('checklistModal').classList.remove('hidden');
}

/** Re-renders all checklist rows + progress bar + button state. */
function _renderChecklistModal() {
    let doneCount = 0;
    _CL_TABS.forEach(tab => {
        const done  = _clReviewed[tab];
        const row   = document.getElementById('cl-row-' + tab);
        const dot   = document.getElementById('cl-dot-' + tab);
        const label = document.getElementById('cl-label-' + tab);
        if (done) {
            doneCount++;
            row?.classList.add('is-done');
            if (dot)   { dot.textContent = '✓'; dot.classList.add('is-done'); }
            if (label) { label.textContent = 'Reviewed ✓'; label.className = 'text-xs font-semibold text-green-600 whitespace-nowrap'; }
        } else {
            row?.classList.remove('is-done');
            if (dot)   { dot.textContent = '✕'; dot.classList.remove('is-done'); }
            if (label) { label.textContent = 'Not reviewed'; label.className = 'text-xs font-semibold text-slate-400 whitespace-nowrap'; }
        }
    });

    const pct  = Math.round(doneCount / 4 * 100);
    const bar  = document.getElementById('cl-bar');
    const cnt  = document.getElementById('cl-count');
    const approveBtn = document.getElementById('cl-approve-btn');
    const allDone    = doneCount === 4;

    if (bar) {
        bar.style.width = pct + '%';
        bar.className   = 'h-2 rounded-full transition-all duration-400 ' + (allDone ? 'bg-green-500' : 'bg-amber-400');
    }
    if (cnt) cnt.textContent = doneCount + ' / 4 reviewed';

    document.getElementById('cl-warning')?.classList.toggle('hidden', allDone);
    document.getElementById('cl-alldone')?.classList.toggle('hidden', !allDone);

    if (approveBtn) {
        approveBtn.disabled = !allDone;
        if (allDone) {
            approveBtn.className = 'px-5 py-2.5 font-semibold rounded-xl transition-all text-sm flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white shadow-md hover:shadow-lg';
        } else {
            approveBtn.className = 'px-5 py-2.5 font-semibold rounded-xl transition-all text-sm flex items-center gap-2 bg-slate-200 text-slate-400 cursor-not-allowed';
        }
    }
}

function closeChecklistModal() {
    document.getElementById('checklistModal').classList.add('hidden');
}

/**
 * Called when "Approve Submission" is clicked inside the checklist modal
 * (only possible when all 4 tabs are reviewed). Directly submits the approval.
 */
function proceedToApprove() {
    if (!_CL_TABS.every(t => _clReviewed[t])) return;
    closeChecklistModal();
    // Directly submit — no second modal needed, company name already shown in checklist
    const form = document.getElementById('approveForm');
    form.action = `/admin/lmi-submissions/${_clSubId}/approve`;
    form.submit();
}
// ═══════════════════════════════════════════════════════

function showApproveModal(submissionId, companyName) {
    currentSubmissionId = submissionId;
    document.getElementById('approveCompanyName').textContent = companyName;
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    currentSubmissionId = null;
}

function confirmApprove() {
    if (currentSubmissionId) {
        const form = document.getElementById('approveForm');
        form.action = `/admin/lmi-submissions/${currentSubmissionId}/approve`;
        
        // Close the approve modal
        closeApproveModal();
        
        // Submit the form
        form.submit();
        
        // Show success modal
        showSuccessModal('approved', 'Submission has been approved successfully!');
    }
}

function showRejectModal(submissionId, companyName) {
    currentSubmissionId = submissionId;
    document.getElementById('rejectCompanyName').textContent = companyName;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    currentSubmissionId = null;
}

function confirmReject() {
    if (currentSubmissionId) {
        const form = document.getElementById('rejectForm');
        form.action = `/admin/lmi-submissions/${currentSubmissionId}/reject`;
        
        // Close the reject modal
        closeRejectModal();
        
        // Submit the form
        form.submit();
        
        // Show success modal
        showSuccessModal('rejected', 'Submission has been rejected.');
    }
}

function showRestorePendingModal(submissionId, companyName) {
    currentSubmissionId = submissionId;
    document.getElementById('restoreCompanyName').textContent = companyName;
    document.getElementById('restoreConfirmText').value = '';
    document.getElementById('restoreConfirmButton').disabled = true;
    document.getElementById('restoreError').classList.add('hidden');
    document.getElementById('restorePendingModal').classList.remove('hidden');
}

function closeRestorePendingModal() {
    document.getElementById('restorePendingModal').classList.add('hidden');
    currentSubmissionId = null;
}

function validateRestoreInput() {
    const input = document.getElementById('restoreConfirmText');
    const button = document.getElementById('restoreConfirmButton');
    const error = document.getElementById('restoreError');
    
    if (input.value === 'CONFIRM') {
        button.disabled = false;
        error.classList.add('hidden');
        input.classList.remove('border-red-300');
        input.classList.add('border-green-500');
    } else {
        button.disabled = true;
        if (input.value.length > 0) {
            error.classList.remove('hidden');
            input.classList.remove('border-green-500');
            input.classList.add('border-red-300');
        } else {
            error.classList.add('hidden');
            input.classList.remove('border-green-500', 'border-red-300');
        }
    }
}

function confirmRestorePending() {
    const input = document.getElementById('restoreConfirmText');
    
    if (input.value === 'CONFIRM' && currentSubmissionId) {
        const form = document.getElementById('restorePendingForm');
        form.action = `/admin/lmi-submissions/${currentSubmissionId}/restore-pending`;
        
        // Close the restore modal
        closeRestorePendingModal();
        
        // Submit the form
        form.submit();
        
        // Show success modal
        showSuccessModal('restored', 'Submission has been restored to pending status.');
    }
}

function showSuccessModal(action, message) {
    const modal = document.getElementById('successModal');
    const header = document.getElementById('successModalHeader');
    const icon = document.getElementById('successIcon');
    const title = document.getElementById('successTitle');
    const messageEl = document.getElementById('successMessage');
    const button = document.getElementById('successButton');
    
    if (action === 'approved') {
        header.className = 'bg-gradient-to-r from-green-500 to-green-600 p-8 text-center';
        icon.className = 'w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-green-500';
        icon.querySelector('svg').className = 'w-12 h-12 text-green-500';
        icon.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>';
        title.className = 'text-3xl font-bold text-white mb-2';
        messageEl.className = 'text-green-100';
        button.className = 'px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg w-full';
    } else if (action === 'rejected') {
        header.className = 'bg-gradient-to-r from-red-500 to-red-600 p-8 text-center';
        icon.className = 'w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-red-500';
        icon.querySelector('svg').className = 'w-12 h-12 text-red-500';
        icon.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>';
        title.className = 'text-3xl font-bold text-white mb-2';
        messageEl.className = 'text-red-100';
        button.className = 'px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg w-full';
    } else if (action === 'restored') {
        header.className = 'bg-gradient-to-r from-orange-500 to-orange-600 p-8 text-center';
        icon.className = 'w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-orange-500';
        icon.querySelector('svg').className = 'w-12 h-12 text-orange-500';
        icon.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>';
        title.className = 'text-3xl font-bold text-white mb-2';
        messageEl.className = 'text-orange-100';
        button.className = 'px-8 py-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg w-full';
    }
    
    title.textContent = action === 'approved' ? 'Approved!' : action === 'rejected' ? 'Rejected' : 'Restored!';
    messageEl.textContent = message;
    
    modal.classList.remove('hidden');
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    // Reload the page to show updated data
    window.location.reload();
}

// ─── AJAX Pagination ────────────────────────────────────────────────────
// Intercept pagination link clicks so we swap just the card+pagination
// without a full page reload — no more scroll jump to the bottom.

// Use a module-level flag so the listener is only ever attached ONCE,
// and an AbortController so rapid clicks cancel the previous in-flight request.
let _paginationListenerAttached = false;
let _paginationAbortController  = null;

function initAjaxPagination() {
    // Guard: only attach the delegated listener once, even after innerHTML swaps
    if (_paginationListenerAttached) return;

    const container = document.getElementById('submission-ajax-container');
    if (!container) return;

    // Use event delegation on the document so it survives innerHTML swaps
    document.addEventListener('click', function (e) {
        const container = document.getElementById('submission-ajax-container');
        if (!container) return;

        const link = e.target.closest('a[href]');
        if (!link || !container.contains(link)) return;

        e.preventDefault();
        loadSubmissionPage(link.href);
    });

    _paginationListenerAttached = true;
}

function loadSubmissionPage(url) {
    const container = document.getElementById('submission-ajax-container');
    if (!container) return;

    // Cancel any previous in-flight request before starting a new one
    if (_paginationAbortController) {
        _paginationAbortController.abort();
    }
    _paginationAbortController = new AbortController();

    // Subtle fade while loading
    container.style.transition = 'opacity 0.15s';
    container.style.opacity = '0.45';
    container.style.pointerEvents = 'none';

    fetch(url, {
        signal: _paginationAbortController.signal,
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
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newContainer = doc.getElementById('submission-ajax-container');

        if (newContainer) {
            container.innerHTML = newContainer.innerHTML;
        }

        // Restore
        container.style.opacity = '1';
        container.style.pointerEvents = '';

        // Scroll the TOP of the card into view — smooth, no jump
        const card = container.querySelector('.admin-review-card');
        if (card) {
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Update browser URL bar
        window.history.pushState({}, '', url);

        // NOTE: do NOT call initAjaxPagination() here — listener lives on document now
    })
    .catch(err => {
        if (err.name === 'AbortError') return; // Intentionally cancelled — do nothing
        console.error('AJAX pagination failed:', err);
        container.style.opacity = '1';
        container.style.pointerEvents = '';
        // Graceful fallback to normal navigation
        window.location.href = url;
    });
}

document.addEventListener('DOMContentLoaded', initAjaxPagination);

// ─── Live Polling — detect new submissions every 30s ─────────────────────
(function () {
    let knownCounts = {
        pending:  parseInt(window.AppData.pendingCount),
        approved: parseInt(window.AppData.approvedCount),
        rejected: parseInt(window.AppData.rejectedCount),
    };

    const POLL_INTERVAL = 30_000;
    const activeTab     = window.AppData.activeTab;

    // Track accumulated new count and the single persistent toast
    let accumulatedNew  = 0;
    let notifToast      = null;

    function fetchCounts() {
        fetch('/admin/lmi-submissions/counts', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (!data) return;

            const newPending  = parseInt(data.pending  ?? 0);
            const newApproved = parseInt(data.approved ?? 0);
            const newRejected = parseInt(data.rejected ?? 0);

            // Always update badges live
            updateBadge('pending',  newPending);
            updateBadge('approved', newApproved);
            updateBadge('rejected', newRejected);

            // Check if active tab has grown
            const activeNew = parseInt(data[activeTab] ?? 0);
            const activeOld = parseInt(knownCounts[activeTab] ?? 0);

            if (activeNew > activeOld) {
                accumulatedNew += (activeNew - activeOld);
                showOrUpdateNotifToast();
            }

            knownCounts = { pending: newPending, approved: newApproved, rejected: newRejected };
        })
        .catch(() => {});
    }

    function updateBadge(type, count) {
        ['header', 'tab'].forEach(prefix => {
            const el = document.getElementById(`${prefix}-${type}-count`);
            if (el) el.textContent = count;
        });
    }

    function showOrUpdateNotifToast() {
        const label     = activeTab.charAt(0).toUpperCase() + activeTab.slice(1);
        const msgText   = `[!] ${accumulatedNew} new ${label} submission${accumulatedNew > 1 ? 's' : ''} — click to refresh`;
        const container = document.getElementById('toastContainer');

        if (notifToast && container.contains(notifToast)) {
            // Already showing — just update the text, don't create a new one
            notifToast.querySelector('.notif-text').textContent = msgText;
            // Pulse the toast to draw attention to the update
            notifToast.classList.add('scale-105');
            setTimeout(() => notifToast.classList.remove('scale-105'), 200);
            return;
        }

        // Create a fresh persistent toast
        notifToast = document.createElement('div');
        notifToast.className = [
            'pointer-events-auto w-full rounded-xl shadow-xl overflow-hidden',
            'border-l-4 border-blue-500 bg-blue-50',
            'transform transition-all duration-300 translate-x-full opacity-0',
            'cursor-pointer hover:shadow-2xl hover:scale-[1.02] active:scale-[0.99]',
            'transition-transform'
        ].join(' ');

        notifToast.innerHTML = `
            <div class="flex items-center gap-3 px-4 py-4">
                <span class="relative flex-shrink-0 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                </span>
                <p class="notif-text text-sm font-semibold text-blue-800 flex-1 leading-snug">${msgText}</p>
                <button class="notif-dismiss text-blue-400 hover:text-blue-700 transition ml-1 flex-shrink-0"
                        title="Dismiss">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;

        // Clicking the toast body refreshes submissions
        notifToast.addEventListener('click', function (e) {
            if (e.target.closest('.notif-dismiss')) return; // ignore dismiss button
            dismissNotifToast();
            reloadSubmissions();
        });

        // Dismiss button just closes without refreshing
        notifToast.querySelector('.notif-dismiss').addEventListener('click', function (e) {
            e.stopPropagation();
            dismissNotifToast();
        });

        container.appendChild(notifToast);

        // Slide in
        requestAnimationFrame(() => requestAnimationFrame(() => {
            notifToast.classList.remove('translate-x-full', 'opacity-0');
        }));
    }

    function dismissNotifToast() {
        if (!notifToast) return;
        notifToast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            notifToast?.remove();
            notifToast = null;
            accumulatedNew = 0;
        }, 300);
    }

    setInterval(fetchCounts, POLL_INTERVAL);

    window.reloadSubmissions = function () {
        loadSubmissionPage(window.location.href);
        setTimeout(() => {
            knownCounts = {
                pending:  parseInt(document.getElementById('header-pending-count')?.textContent  || 0),
                approved: parseInt(document.getElementById('header-approved-count')?.textContent || 0),
                rejected: parseInt(document.getElementById('header-rejected-count')?.textContent || 0),
            };
            accumulatedNew = 0;
        }, 1500);
    };
})();

// ─── Admin Contact Number: Toggle + Formatter + Area Code Suggestions ─────────

const PH_AREA_CODES_ADMIN = [
    { code: "02",  label: "Metro Manila, Rizal, Bacoor, San Pedro" },
    { code: "032", label: "Cebu" },
    { code: "033", label: "Guimaras, Iloilo (part)" },
    { code: "034", label: "Iloilo, Negros Occidental" },
    { code: "035", label: "Negros Oriental, Siquijor" },
    { code: "036", label: "Aklan, Antique, Capiz" },
    { code: "038", label: "Bohol" },
    { code: "042", label: "Aurora, Marinduque, Quezon" },
    { code: "043", label: "Batangas, Occidental Mindoro, Oriental Mindoro" },
    { code: "044", label: "Bulacan, Nueva Ecija" },
    { code: "045", label: "Pampanga, Tarlac" },
    { code: "046", label: "Cavite (except Bacoor)" },
    { code: "047", label: "Bataan, Zambales" },
    { code: "048", label: "Palawan" },
    { code: "049", label: "Laguna (except San Pedro)" },
    { code: "052", label: "Albay, Catanduanes" },
    { code: "053", label: "Biliran, Leyte, Southern Leyte" },
    { code: "054", label: "Camarines Norte, Camarines Sur, Romblon" },
    { code: "055", label: "Eastern Samar, Northern Samar, Samar" },
    { code: "056", label: "Masbate, Sorsogon" },
    { code: "062", label: "Basilan, Zamboanga del Sur, Zamboanga Sibugay" },
    { code: "063", label: "Lanao del Norte" },
    { code: "064", label: "Lanao del Sur, Maguindanao, North Cotabato, Sultan Kudarat" },
    { code: "065", label: "Zamboanga del Norte" },
    { code: "068", label: "Tawi-Tawi" },
    { code: "072", label: "La Union" },
    { code: "074", label: "Abra, Benguet, Ifugao, Kalinga, Mountain Province" },
    { code: "075", label: "Pangasinan" },
    { code: "077", label: "Ilocos Norte, Ilocos Sur" },
    { code: "078", label: "Apayao, Batanes, Cagayan, Isabela, Nueva Vizcaya, Quirino" },
    { code: "082", label: "Davao del Sur, Davao Occidental" },
    { code: "083", label: "Sarangani, South Cotabato" },
    { code: "084", label: "Compostela Valley, Davao del Norte" },
    { code: "085", label: "Agusan del Norte, Agusan del Sur, Sulu" },
    { code: "086", label: "Dinagat Islands, Surigao del Norte, Surigao del Sur" },
    { code: "087", label: "Davao de Oro, Davao Oriental" },
    { code: "088", label: "Bukidnon, Camiguin, Misamis Occidental, Misamis Oriental" },
];

function adminFormatTelephone(digits) {
    if (!digits) return "";
    if (!digits.startsWith("0")) digits = "0" + digits;
    const withoutTrunk = digits.slice(1);
    if (withoutTrunk.startsWith("2")) {
        const local = withoutTrunk.slice(1);
        if (local.length === 0) return "02";
        if (local.length <= 4)  return "02-" + local;
        return "02-" + local.slice(0, 4) + "-" + local.slice(4);
    }
    const area  = withoutTrunk.slice(0, 2);
    const local = withoutTrunk.slice(2);
    if (local.length === 0) return "0" + area;
    if (local.length <= 3)  return "0" + area + "-" + local;
    return "0" + area + "-" + local.slice(0, 3) + "-" + local.slice(3);
}

function adminSwitchContactType(type, id) {
    const mobileWrapper    = document.getElementById("admin-mobile-wrapper-" + id);
    const telephoneWrapper = document.getElementById("admin-telephone-wrapper-" + id);
    const mobileInput      = document.getElementById("admin-mobile-input-" + id);
    const telephoneInput   = document.getElementById("admin-telephone-input-" + id);
    const contactType      = document.getElementById("admin-contact-type-" + id);
    const hint             = document.getElementById("admin-contact-hint-" + id);
    const toggleMobile     = document.getElementById("admin-toggle-mobile-" + id);
    const toggleTelephone  = document.getElementById("admin-toggle-telephone-" + id);

    [toggleMobile, toggleTelephone].forEach(btn => {
        btn.classList.remove("bg-white", "text-teal-700", "shadow-sm", "border", "border-gray-200");
        btn.classList.add("text-gray-500");
    });

    if (type === "mobile") {
        mobileWrapper.classList.remove("hidden");
        telephoneWrapper.classList.add("hidden");
        mobileInput.disabled = false;
        telephoneInput.disabled = true;
        telephoneInput.value = "";
        contactType.value = "mobile";
        hint.textContent = "Enter mobile number with country code";
        toggleMobile.classList.add("bg-white", "text-teal-700", "shadow-sm", "border", "border-gray-200");
        toggleMobile.classList.remove("text-gray-500");
        mobileInput.focus();
    } else {
        telephoneWrapper.classList.remove("hidden");
        mobileWrapper.classList.add("hidden");
        telephoneInput.disabled = false;
        mobileInput.disabled = true;
        mobileInput.value = "";
        contactType.value = "telephone";
        hint.textContent = "Auto-formats to 082-123-4567";
        toggleTelephone.classList.add("bg-white", "text-teal-700", "shadow-sm", "border", "border-gray-200");
        toggleTelephone.classList.remove("text-gray-500");
        telephoneInput.focus();
    }
    // Close any open suggestions
    const suggBox = document.getElementById("admin-area-suggestions-" + id);
    if (suggBox) suggBox.classList.add("hidden");
}

// ─── Admin Country Code Selector ─────────────────────────────────────────────
const ADMIN_COUNTRIES = [
    { flag: '🇵🇭', name: 'Philippines',   dial: '+63',  maxDigits: 10 },
    { flag: '🇺🇸', name: 'United States', dial: '+1',   maxDigits: 10 },
    { flag: '🇬🇧', name: 'United Kingdom',dial: '+44',  maxDigits: 10 },
    { flag: '🇦🇺', name: 'Australia',     dial: '+61',  maxDigits: 9  },
    { flag: '🇨🇦', name: 'Canada',        dial: '+1',   maxDigits: 10 },
    { flag: '🇯🇵', name: 'Japan',         dial: '+81',  maxDigits: 10 },
    { flag: '🇰🇷', name: 'South Korea',   dial: '+82',  maxDigits: 10 },
    { flag: '🇸🇬', name: 'Singapore',     dial: '+65',  maxDigits: 8  },
    { flag: '🇲🇾', name: 'Malaysia',      dial: '+60',  maxDigits: 9  },
    { flag: '🇮🇩', name: 'Indonesia',     dial: '+62',  maxDigits: 11 },
    { flag: '🇹🇭', name: 'Thailand',      dial: '+66',  maxDigits: 9  },
    { flag: '🇻🇳', name: 'Vietnam',       dial: '+84',  maxDigits: 9  },
    { flag: '🇮🇳', name: 'India',         dial: '+91',  maxDigits: 10 },
    { flag: '🇨🇳', name: 'China',         dial: '+86',  maxDigits: 11 },
    { flag: '🇭🇰', name: 'Hong Kong',     dial: '+852', maxDigits: 8  },
    { flag: '🇹🇼', name: 'Taiwan',        dial: '+886', maxDigits: 9  },
    { flag: '🇸🇦', name: 'Saudi Arabia',  dial: '+966', maxDigits: 9  },
    { flag: '🇦🇪', name: 'UAE',           dial: '+971', maxDigits: 9  },
    { flag: '🇶🇦', name: 'Qatar',         dial: '+974', maxDigits: 8  },
    { flag: '🇩🇪', name: 'Germany',       dial: '+49',  maxDigits: 11 },
    { flag: '🇫🇷', name: 'France',        dial: '+33',  maxDigits: 9  },
    { flag: '🇮🇹', name: 'Italy',         dial: '+39',  maxDigits: 10 },
    { flag: '🇪🇸', name: 'Spain',         dial: '+34',  maxDigits: 9  },
    { flag: '🇳🇱', name: 'Netherlands',   dial: '+31',  maxDigits: 9  },
    { flag: '🇳🇿', name: 'New Zealand',   dial: '+64',  maxDigits: 9  },
    { flag: '🇧🇷', name: 'Brazil',        dial: '+55',  maxDigits: 11 },
    { flag: '🇲🇽', name: 'Mexico',        dial: '+52',  maxDigits: 10 },
    { flag: '🇿🇦', name: 'South Africa',  dial: '+27',  maxDigits: 9  },
    { flag: '🇳🇬', name: 'Nigeria',       dial: '+234', maxDigits: 10 },
    { flag: '🇰🇪', name: 'Kenya',         dial: '+254', maxDigits: 9  },
];

// Per-submission selected country state
const adminSelectedCountry = {};

function getAdminCountry(id) {
    return adminSelectedCountry[id] || ADMIN_COUNTRIES[0];
}

function syncAdminCarrier(id) {
    const mobileInput = document.getElementById('admin-mobile-input-' + id);
    if (mobileInput) {
        // The mobile input stores just the number digits; the dial code is tracked separately.
        // The actual saved value is already the full number (dial + digits) from the original submission.
        // We update the input's data so the save handler picks up the full number.
        const country = getAdminCountry(id);
        mobileInput.dataset.dialCode = country.dial;
    }
}

function renderAdminCountryList(id, filter = '') {
    const list = document.getElementById('admin-country-list-' + id);
    if (!list) return;

    const filtered = ADMIN_COUNTRIES.filter(c =>
        c.name.toLowerCase().includes(filter.toLowerCase()) ||
        c.dial.includes(filter)
    );

    list.innerHTML = filtered.length
        ? filtered.map(c => `
            <div class="admin-country-option flex items-center gap-2 px-3 py-2 hover:bg-teal-50 cursor-pointer text-xs transition border-b border-gray-50 last:border-b-0"
                 data-id="${id}" data-dial="${c.dial}" data-flag="${c.flag}" data-name="${c.name}" data-max-digits="${c.maxDigits}">
                <span class="text-base">${c.flag}</span>
                <span class="flex-1 text-gray-700">${c.name}</span>
                <span class="text-gray-400 font-mono">${c.dial}</span>
            </div>`).join('')
        : '<div class="px-3 py-2 text-xs text-gray-400 text-center">No results found</div>';

    list.querySelectorAll('.admin-country-option').forEach(opt => {
        opt.addEventListener('click', () => {
            const submissionId = opt.dataset.id;
            adminSelectedCountry[submissionId] = {
                flag:      opt.dataset.flag,
                name:      opt.dataset.name,
                dial:      opt.dataset.dial,
                maxDigits: parseInt(opt.dataset.maxDigits),
            };
            document.getElementById('admin-country-flag-' + submissionId).textContent = opt.dataset.flag;
            document.getElementById('admin-country-dial-' + submissionId).textContent = opt.dataset.dial;
            document.getElementById('admin-country-dropdown-' + submissionId).classList.add('hidden');

            // Apply maxlength and trim existing value if too long
            const mobileInput = document.getElementById('admin-mobile-input-' + submissionId);
            if (mobileInput) {
                mobileInput.maxLength = parseInt(opt.dataset.maxDigits);
                mobileInput.value = mobileInput.value.slice(0, parseInt(opt.dataset.maxDigits));
                mobileInput.placeholder = `e.g. ${'9'.repeat(parseInt(opt.dataset.maxDigits))}`;
            }

            // Update hint
            const hint = document.getElementById('admin-contact-hint-' + submissionId);
            if (hint) hint.textContent = `${opt.dataset.maxDigits}-digit mobile number (${opt.dataset.name})`;

            syncAdminCarrier(submissionId);
        });
    });
}

function toggleAdminCountryDropdown(id) {
    const dropdown = document.getElementById('admin-country-dropdown-' + id);
    const search   = document.getElementById('admin-country-search-' + id);
    if (!dropdown) return;
    const isHidden = dropdown.classList.contains('hidden');
    // Close all other open dropdowns first
    document.querySelectorAll('[id^="admin-country-dropdown-"]').forEach(d => d.classList.add('hidden'));
    if (isHidden) {
        dropdown.classList.remove('hidden');
        renderAdminCountryList(id);
        if (search) { search.value = ''; setTimeout(() => search.focus(), 50); }
    }
}

// Close admin country dropdowns on outside click
document.addEventListener('click', (e) => {
    if (!e.target.closest('[id^="admin-country-btn-"]') && !e.target.closest('[id^="admin-country-dropdown-"]')) {
        document.querySelectorAll('[id^="admin-country-dropdown-"]').forEach(d => d.classList.add('hidden'));
    }
});

// Pre-populate country selector based on existing contact_number dial code
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[id^="admin-country-dial-"]').forEach(dialEl => {
        const id = dialEl.id.replace('admin-country-dial-', '');
        const mobileInput = document.getElementById('admin-mobile-input-' + id);
        if (!mobileInput) return;

        const rawNumber = mobileInput.value || '';
        // Sort by longest dial code first to prevent +63 matching before +6x shorter codes
        const sortedCountries = [...ADMIN_COUNTRIES].sort((a, b) => b.dial.length - a.dial.length);
        const matched = sortedCountries.find(c => rawNumber.startsWith(c.dial));
        if (matched) {
            adminSelectedCountry[id] = matched;
            dialEl.textContent = matched.dial;
            document.getElementById('admin-country-flag-' + id).textContent = matched.flag;
            // Strip dial code from display value so input only shows digits
            const bareDigits = rawNumber.slice(matched.dial.length);
            mobileInput.value = bareDigits;
            // Sync data-original to bare digits so change detection is
            // digits vs digits and never fires a false positive.
            mobileInput.dataset.original = bareDigits;
            // Apply correct maxlength
            mobileInput.maxLength = matched.maxDigits;
            // Update view mode display to show flag + number
            const viewDisplay = document.getElementById('view-contact-' + id);
            if (viewDisplay) {
                viewDisplay.innerHTML = `<span class="mr-1">${matched.flag}</span>${rawNumber}`;
            }
        } else {
            // Default to Philippines maxlength
            mobileInput.maxLength = 10;
        }
    });
});

function adminShowAreaSuggestions(telInput, suggestBox, suggestList, typedDigits) {
    if (!typedDigits || typedDigits.length < 2 || typedDigits.length > 3) {
        suggestBox.classList.add("hidden");
        return;
    }
    const matches = PH_AREA_CODES_ADMIN.filter(ac => ac.code.startsWith(typedDigits));
    if (matches.length === 0) { suggestBox.classList.add("hidden"); return; }

    suggestList.innerHTML = "";
    matches.forEach(ac => {
        const item = document.createElement("div");
        item.className = "flex items-center gap-3 px-3 py-2 hover:bg-teal-50 cursor-pointer border-b border-gray-50 last:border-b-0 text-sm transition-colors";
        const typed    = typedDigits;
        const codeHtml = `<span class="font-bold text-teal-600">${ac.code.slice(0, typed.length)}</span><span class="font-bold text-gray-800">${ac.code.slice(typed.length)}</span>`;
        item.innerHTML = `
            <span class="shrink-0 text-xs font-mono bg-teal-50 text-teal-700 border border-teal-200 rounded px-1.5 py-0.5">${codeHtml}</span>
            <span class="text-gray-600 truncate text-xs">${ac.label}</span>
        `;
        item.addEventListener("mousedown", function(e) {
            e.preventDefault();
            telInput.value = ac.code + "-";
            suggestBox.classList.add("hidden");
            telInput.focus();
        });
        suggestList.appendChild(item);
    });
    suggestBox.classList.remove("hidden");
}

// ── Admin Salary Range (native select) ───────────────────────────────────────
function formatAdminSalaryInput(input) {
    const raw = input.value.replace(/[^0-9]/g, '');
    input.value = raw ? Number(raw).toLocaleString('en-US') : '';
}

// Block non-numeric keypresses on below-30k salary inputs
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[class*="below-30k-exact-"]').forEach(function (el) {
        el.addEventListener('keydown', function (e) {
            const allowed = ['Backspace','Delete','ArrowLeft','ArrowRight','Tab','Home','End'];
            if (!allowed.includes(e.key) && !(e.key >= '0' && e.key <= '9') && !(e.ctrlKey || e.metaKey)) {
                e.preventDefault();
            }
        });
    });
});

function handleAdminSalaryChange(index, value) {
    const below30kContainer = document.querySelector('.below-30k-container-' + index);
    const below30kInput = document.querySelector('.below-30k-exact-' + index);
    if (value === 'Below ₱30,000') {
        below30kContainer.classList.remove('hidden');
        if (below30kInput) below30kInput.focus();
    } else {
        below30kContainer.classList.add('hidden');
        if (below30kInput) below30kInput.value = '';
    }
}

// Format any pre-filled below-30k inputs on page load
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[class*="below-30k-exact-"]').forEach(function (input) {
        if (input.value) {
            const raw = input.value.replace(/[^0-9]/g, '');
            input.value = raw ? Number(raw).toLocaleString('en-US') : '';
        }
    });
});
// ─────────────────────────────────────────────────────────────────────────────


document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".admin-telephone-field").forEach(function(telInput) {
        // Extract submission ID from input id: "admin-telephone-input-{id}"
        const id          = telInput.id.replace("admin-telephone-input-", "");
        const suggestBox  = document.getElementById("admin-area-suggestions-" + id);
        const suggestList = document.getElementById("admin-area-list-" + id);
        if (!suggestBox || !suggestList) return;

        telInput.addEventListener("input", function(e) {
            let digits = e.target.value.replace(/\D/g, "");
            if (digits.length > 10) digits = digits.slice(0, 10);
            e.target.value = adminFormatTelephone(digits);
            adminShowAreaSuggestions(telInput, suggestBox, suggestList, digits.length <= 3 ? digits : null);
        });

        telInput.addEventListener("keydown", function(e) {
            const allowedKeys = ["Backspace","Delete","ArrowLeft","ArrowRight","ArrowUp","ArrowDown","Tab","Home","End"];
            const isDigit = e.key >= "0" && e.key <= "9";
            const isCtrl  = e.ctrlKey || e.metaKey;
            if (!isDigit && !allowedKeys.includes(e.key) && !isCtrl) e.preventDefault();
        });

        telInput.addEventListener("paste", function(e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData("text");
            let digits   = pasted.replace(/\D/g, "").slice(0, 10);
            e.target.value = adminFormatTelephone(digits);
            suggestBox.classList.add("hidden");
        });

        telInput.addEventListener("blur", function() {
            setTimeout(() => suggestBox.classList.add("hidden"), 150);
        });
    });

    // Also hook toggleEdit / cancelEdit to show-hide contact controls
    // by overriding after original definition
    const origToggle = window.toggleEdit;
    window.toggleEdit = function(button) {
        origToggle(button);
        const form         = button.closest(".admin-review-card").querySelector('[id^="form-company-"]');
        if (!form) return;
        const viewDisplay  = form.querySelector(".contact-view-display");
        const editControls = form.querySelector(".contact-edit-controls");
        if (viewDisplay)  viewDisplay.classList.add("hidden");
        if (editControls) editControls.classList.remove("hidden");
    };

    const origCancel = window.cancelEdit;
    window.cancelEdit = function(button) {
        origCancel(button);
        const form         = button.closest(".admin-review-card").querySelector('[id^="form-company-"]');
        if (!form) return;
        const viewDisplay  = form.querySelector(".contact-view-display");
        const editControls = form.querySelector(".contact-edit-controls");
        if (viewDisplay)  viewDisplay.classList.remove("hidden");
        if (editControls) editControls.classList.add("hidden");
    };
});

// ─── Expose functions to global scope for onclick/oninput attributes ────────
window.switchStatusTab = switchStatusTab;
window.switchTab = switchTab;
window.toggleEdit = toggleEdit;
window.cancelEdit = cancelEdit;
window.toggleRolesEdit = toggleRolesEdit;
window.cancelRolesEdit = cancelRolesEdit;
window.toggleDiagnosisEdit = toggleDiagnosisEdit;
window.cancelDiagnosisEdit = cancelDiagnosisEdit;
window.toggleEngagementEdit = toggleEngagementEdit;
window.cancelEngagementEdit = cancelEngagementEdit;
window.handleFormSubmit = handleFormSubmit;
window.markTabReviewed = markTabReviewed;
window.openChecklistModal = openChecklistModal;
window.closeChecklistModal = closeChecklistModal;
window.showRejectModal = showRejectModal;
window.closeRejectModal = closeRejectModal;
window.confirmReject = confirmReject;
window.closeApproveModal = closeApproveModal;
window.proceedToApprove = proceedToApprove;
window.confirmApprove = confirmApprove;
window.showRestorePendingModal = showRestorePendingModal;
window.closeRestorePendingModal = closeRestorePendingModal;
window.confirmRestorePending = confirmRestorePending;
window.validateRestoreInput = validateRestoreInput;
window.closeEditChangesModal = closeEditChangesModal;
window.confirmEditChanges = confirmEditChanges;
window.closeSuccessModal = closeSuccessModal;
window.formatAdminSalaryInput = formatAdminSalaryInput;
window.handleAdminSalaryChange = handleAdminSalaryChange;
window.adminSwitchContactType = adminSwitchContactType;
window.toggleAdminCountryDropdown = toggleAdminCountryDropdown;
window.renderAdminCountryList = renderAdminCountryList;
window.syncAdminCarrier = syncAdminCarrier;