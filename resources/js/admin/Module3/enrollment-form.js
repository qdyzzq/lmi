// ─── State Variables ────────────────────────────────────────────────────────
let existingData = null;
let oldYear = null;
let pendingData = null;
let pendingYearData = null;

// ─── Discipline Labels ───────────────────────────────────────────────────────
const disciplineLabels = {
    agriculture: 'Agriculture, Forestry, Fisheries',
    architecture: 'Architecture and Town Planning',
    business: 'Business Administration',
    criminal_justice: 'Criminal Justice Education',
    education: 'Education Science and Teacher Training',
    engineering: 'Engineering and Technology',
    arts: 'Fine and Applied Arts',
    general: 'General Programs',
    home_economics: 'Home Economics',
    humanities: 'Humanities',
    it: 'IT-Related Disciplines',
    law: 'Law and Jurisprudence',
    maritime: 'Maritime',
    mass_comm: 'Mass Communication',
    mathematics: 'Mathematics',
    medical: 'Medical and Allied',
    natural_science: 'Natural Science',
    other_disciplines: 'Other Disciplines',
    religion: 'Religion and Theology',
    service_trades: 'Service Trades',
    social_sciences: 'Social and Behavioral Sciences'
};

// ─── Toast Notification System ───────────────────────────────────────────────
function showToast(message, type = 'error') {
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

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });
    });

    setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// Inject toast animation style once
if (!document.getElementById('toastStyle')) {
    const style = document.createElement('style');
    style.id = 'toastStyle';
    style.textContent = `@keyframes shrink { from { width: 100%; } to { width: 0%; } }`;
    document.head.appendChild(style);
}

// ─── Province Change Handler ─────────────────────────────────────────────────
function handleProvinceChange(province) {
    document.querySelectorAll('input[name="institution_type"]').forEach(r => r.checked = false);
    const lockBannerText = document.getElementById('lockBannerText');
    if (lockBannerText) {
        lockBannerText.innerHTML = 'Select a <strong>Province</strong> and <strong>Institution Type</strong> above, enter the academic year, then click <strong>Check Year</strong> to unlock the enrollment fields.';
    }
}

// ─── Year Check & Load ───────────────────────────────────────────────────────
async function checkAndLoadYear() {
    const yearInput = document.getElementById('academicYear');
    const year = yearInput.value.trim();

    if (!year) {
        showToast('Please enter an academic year', 'error');
        return;
    }

    const yearPattern = /^\d{4}-\d{4}$/;
    if (!yearPattern.test(year)) {
        showToast('Please enter year in format: YYYY-YYYY (e.g., 2024-2025)', 'error');
        return;
    }

    const province = document.getElementById('province').value;
    const institutionType = document.querySelector('input[name="institution_type"]:checked');

    if (!province || !institutionType) {
        showToast('Please select a Province and Institution Type before checking the year.', 'error');
        const provinceEl = document.getElementById('province');
        if (!province) {
            provinceEl.classList.add('border-red-500', 'ring-2', 'ring-red-300');
            provinceEl.addEventListener('change', () => provinceEl.classList.remove('border-red-500', 'ring-2', 'ring-red-300'), { once: true });
        }
        if (!institutionType) {
            document.querySelectorAll('input[name="institution_type"]').forEach(r => {
                r.closest('label').classList.add('text-red-600');
                r.addEventListener('change', () => {
                    document.querySelectorAll('input[name="institution_type"]').forEach(x => x.closest('label').classList.remove('text-red-600'));
                }, { once: true });
            });
        }
        return;
    }

    try {
        const response = await fetch(`/api/discipline-enrollment/check/${year}?province=${encodeURIComponent(province)}&institution_type=${encodeURIComponent(institutionType.value)}`);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (!data.exists) {
            loadNewYear(year, province, institutionType.value);
            return;
        }

        if (data.exists && data.data) {
            const isEmpty = Object.values(data.data.disciplines ?? data.data).every(v => !parseInt(v));

            if (isEmpty) {
                loadNewYear(year, province, institutionType.value);
                return;
            }

            pendingYearData = {
                year: year,
                province: province,
                institutionType: institutionType.value,
                data: data.data
            };
            showExistingDataModal(year, province, institutionType.value);
        } else {
            loadNewYear(year, province, institutionType.value);
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred while checking the year. Please try again.', 'error');
    }
}

function loadNewYear(year, province, institutionType) {
    document.getElementById('displayYear').textContent = year;
    existingData = null;
    clearForm();
    showStatusNotification(year, false, province, institutionType);
    toggleYearDisplay(true);
    document.getElementById('cancelYearChangeBtn').classList.remove('hidden');
    unlockForm();
    lockSelections();
}

// ─── Year Display Toggle ─────────────────────────────────────────────────────
function toggleYearDisplay(showDisplay) {
    const inputGroup = document.getElementById('yearInputGroup');
    const yearDisplay = document.getElementById('yearDisplay');

    if (showDisplay) {
        inputGroup.classList.add('hidden');
        yearDisplay.classList.remove('hidden');
        yearDisplay.classList.add('flex');
    } else {
        inputGroup.classList.remove('hidden');
        yearDisplay.classList.add('hidden');
        yearDisplay.classList.remove('flex');
    }
}



function cancelYearChange() {
    document.getElementById('academicYear').value = '';
    document.getElementById('displayYear').textContent = '----';
    hideStatusNotification();
    toggleYearDisplay(false);
    unlockSelections();

    document.getElementById('province').value = '';
    document.querySelectorAll('input[name="institution_type"]').forEach(r => r.checked = false);

    clearForm();
    document.getElementById('grandTotal').textContent = '0';

    if (oldYear && oldYear !== '----') {
        oldYear = null;
        existingData = null;
    }

    document.getElementById('cancelYearChangeBtn').classList.add('hidden');
    lockForm();

    setTimeout(() => {
        document.getElementById('academicYear').focus();
    }, 100);
}

// ─── Existing Data Modal ─────────────────────────────────────────────────────
function showExistingDataModal(year, province, institutionType) {
    document.getElementById('existingDataYear').textContent = year;
    document.getElementById('existingDataProvince').textContent = province;
    document.getElementById('existingDataType').textContent = institutionType;
    document.getElementById('existingDataModal').classList.remove('hidden');
}

function closeExistingDataModal() {
    document.getElementById('existingDataModal').classList.add('hidden');
    pendingYearData = null;
    document.getElementById('academicYear').value = '';
    setTimeout(() => {
        document.getElementById('academicYear').focus();
    }, 100);
}

function confirmLoadExistingData() {
    if (!pendingYearData) return;

    const { year, province, institutionType, data } = pendingYearData;

    document.getElementById('displayYear').textContent = year;
    existingData = data;
    populateForm(data.disciplines);
    showStatusNotification(year, true, province, institutionType);
    toggleYearDisplay(true);
    document.getElementById('cancelYearChangeBtn').classList.remove('hidden');

    closeExistingDataModal();
    unlockForm();
    lockSelections();
}

// ─── Year Collision Modal ─────────────────────────────────────────────────────
function showYearCollisionModal(targetYear, province, institutionType) {
    const currentYear = oldYear && oldYear !== '----' ? oldYear : document.getElementById('displayYear').textContent;
    document.getElementById('collisionTargetYear').textContent = `${targetYear} - ${province} - ${institutionType}`;
    document.getElementById('collisionCurrentYear').textContent = currentYear;
    document.getElementById('yearCollisionModal').classList.remove('hidden');
}

function closeYearCollisionModal() {
    document.getElementById('yearCollisionModal').classList.add('hidden');
    document.getElementById('academicYear').value = '';
    document.getElementById('academicYear').focus();
}

// ─── Status Notification ─────────────────────────────────────────────────────
function showStatusNotification(year, exists, province, institutionType) {
    const notification = document.getElementById('statusNotification');
    const icon = document.getElementById('statusIcon');
    const title = document.getElementById('statusTitle');
    const message = document.getElementById('statusMessage');

    if (exists) {
        notification.className = 'mb-8 p-6 rounded-2xl shadow-lg bg-blue-50 border-2 border-blue-200';
        icon.className = 'flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-blue-500 text-white text-2xl';
        icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
        title.textContent = 'Editing Existing Data';
        title.className = 'text-lg font-bold mb-1 text-blue-900';
        message.textContent = `Loading data for ${year} - ${province} - ${institutionType}. You can now edit the existing enrollment data.`;
        message.className = 'text-sm text-blue-800';
    } else {
        notification.className = 'mb-8 p-6 rounded-2xl shadow-lg bg-green-50 border-2 border-green-200';
        icon.className = 'flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-green-500 text-white text-2xl';
        icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 000-2H5a1 1 0 000 2zm0 0v2m0-2h.01M5 20h2a1 1 0 000-2H5a1 1 0 000 2zm0 0v2m0-2h.01"/></svg>';
        title.textContent = 'Creating New Data';
        title.className = 'text-lg font-bold mb-1 text-green-900';
        message.textContent = `No existing data found for ${year} - ${province} - ${institutionType}. You can now enter new enrollment data.`;
        message.className = 'text-sm text-green-800';
    }

    notification.classList.remove('hidden');
}

function hideStatusNotification() {
    document.getElementById('statusNotification').classList.add('hidden');
}

// ─── Form Lock / Unlock ──────────────────────────────────────────────────────
function unlockForm() {
    const formContent = document.getElementById('formContent');
    const lockBanner = document.getElementById('lockBanner');
    const formBlocker = document.getElementById('formBlocker');
    if (formContent) formContent.classList.remove('opacity-50', 'pointer-events-none', 'select-none');
    if (lockBanner) lockBanner.style.display = 'none';
    if (formBlocker) formBlocker.style.display = 'none';
}

function lockForm() {
    const formContent = document.getElementById('formContent');
    const lockBanner = document.getElementById('lockBanner');
    const formBlocker = document.getElementById('formBlocker');
    if (formContent) formContent.classList.add('opacity-50', 'pointer-events-none', 'select-none');
    if (lockBanner) lockBanner.style.display = 'flex';
    if (formBlocker) formBlocker.style.display = 'block';
}

// ─── Selection Lock / Unlock ─────────────────────────────────────────────────
function lockSelections() {
    const province = document.getElementById('province');
    const radios = document.querySelectorAll('input[name="institution_type"]');
    const wrapper = document.getElementById('institutionTypeWrapper');

    if (province) {
        province.disabled = true;
        province.classList.add('opacity-50', 'cursor-not-allowed');
    }
    radios.forEach(r => {
        r.disabled = true;
        r.closest('label').classList.add('opacity-50', 'cursor-not-allowed');
    });
    if (wrapper && !document.getElementById('selectionLockHint')) {
        const label = wrapper.querySelector('label');
        if (label) {
            const span = document.createElement('span');
            span.id = 'selectionLockHint';
            span.className = 'ml-2 inline-flex items-center gap-1 text-xs font-normal text-amber-600';
            span.innerHTML = `<svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> Locked — Cancel to change`;
            label.appendChild(span);
        }
    }
}

function unlockSelections() {
    const province = document.getElementById('province');
    const radios = document.querySelectorAll('input[name="institution_type"]');

    if (province) {
        province.disabled = false;
        province.classList.remove('opacity-50', 'cursor-not-allowed');
    }
    radios.forEach(r => {
        r.disabled = false;
        r.closest('label').classList.remove('opacity-50', 'cursor-not-allowed');
    });
    const hint = document.getElementById('selectionLockHint');
    if (hint) hint.remove();
}

// ─── Discipline Input Helpers ────────────────────────────────────────────────
function getRawValue(input) {
    return parseInt(input.value.replace(/,/g, '')) || 0;
}

function formatWithCommas(n) {
    if (!n && n !== 0) return '';
    return parseInt(n).toLocaleString();
}

function updateGrandTotal() {
    const inputs = document.querySelectorAll('input.discipline-input');
    let total = 0;
    inputs.forEach(input => {
        total += getRawValue(input);
    });
    document.getElementById('grandTotal').textContent = total.toLocaleString();
}

function initDisciplineInputs() {
    const inputs = document.querySelectorAll('input.discipline-input');
    inputs.forEach(input => {
        input.addEventListener('input', function () {
            const raw = this.value.replace(/[^0-9]/g, '');
            const num = parseInt(raw);
            this.value = raw === '' ? '' : num.toLocaleString();
            updateGrandTotal();
        });

        input.addEventListener('focus', function () {
            const raw = this.value.replace(/,/g, '');
            this.value = raw === '0' ? '' : raw;
        });

        input.addEventListener('blur', function () {
            const raw = parseInt(this.value.replace(/[^0-9]/g, ''));
            this.value = isNaN(raw) ? '' : raw.toLocaleString();
            updateGrandTotal();
        });
    });
}

function populateForm(disciplines) {
    for (const [key, value] of Object.entries(disciplines)) {
        const input = document.querySelector(`input[name="${key}"]`);
        if (input) {
            input.value = (value && value > 0) ? parseInt(value).toLocaleString() : '';
        }
    }
    updateGrandTotal();
}

function clearForm() {
    const inputs = document.querySelectorAll('input.discipline-input');
    inputs.forEach(input => {
        input.value = '';
    });
    updateGrandTotal();
}

function filterDisciplines(query) {
    const q = query.toLowerCase().trim();
    const rows = document.querySelectorAll('#disciplineList > div[class*="grid"]');
    let visibleCount = 0;
    rows.forEach(row => {
        const label = row.querySelector('label');
        if (!label) return;
        const match = label.textContent.toLowerCase().includes(q);
        row.style.display = match ? '' : 'none';
        if (match) visibleCount++;
    });
    document.getElementById('noResultsMsg').classList.toggle('hidden', visibleCount > 0);
}

// ─── Reset Modal ─────────────────────────────────────────────────────────────
function confirmReset() {
    const title = document.getElementById('resetModalTitle');
    const msg = document.getElementById('resetModalMessage');
    if (existingData && existingData.disciplines) {
        title.textContent = 'Restore Original Values?';
        msg.textContent = 'This will undo your changes and restore the fields back to the originally loaded values.';
    } else {
        title.textContent = 'Reset Form?';
        msg.textContent = 'Are you sure you want to reset all fields? All entered data will be lost and cannot be recovered.';
    }
    document.getElementById('resetModal').classList.remove('hidden');
}

function closeResetModal() {
    document.getElementById('resetModal').classList.add('hidden');
}

function doReset() {
    document.getElementById('resetModal').classList.add('hidden');

    if (existingData && existingData.disciplines) {
        populateForm(existingData.disciplines);
        showToast('Fields restored to the original loaded values.', 'info');
    } else {
        clearForm();
        document.getElementById('grandTotal').textContent = '0';
        document.getElementById('province').value = '';
        document.querySelectorAll('input[name="institution_type"]').forEach(r => r.checked = false);
    }
}

// ─── Confirm Submit Modal ────────────────────────────────────────────────────
function showConfirmModal(data) {
    pendingData = data;

    document.getElementById('confirmYear').textContent = data.academic_year;
    document.getElementById('confirmProvince').textContent = data.province;
    document.getElementById('confirmInstitutionType').textContent = data.institution_type;

    const actionType = document.getElementById('actionType');
    const confirmActionWarning = document.getElementById('confirmActionWarning');
    const deletionWarning = document.getElementById('deletionWarning');

    if (existingData) {
        actionType.textContent = 'update';
        confirmActionWarning.textContent = 'Update';
        deletionWarning.classList.remove('hidden');
    } else {
        actionType.textContent = 'create new';
        confirmActionWarning.textContent = 'Save';
        deletionWarning.classList.add('hidden');
    }

    const dataSummary = document.getElementById('dataSummary');
    dataSummary.innerHTML = '';

    let grandTotal = 0;
    for (const [key, value] of Object.entries(data.disciplines)) {
        const numValue = parseInt(value) || 0;
        grandTotal += numValue;

        const originalValue = existingData && existingData.disciplines
            ? (parseInt(existingData.disciplines[key]) || 0)
            : null;
        const isEdited = originalValue !== null && originalValue !== numValue;

        let diffBadge = '';
        if (isEdited) {
            const delta = numValue - originalValue;
            const sign = delta > 0 ? '+' : '';
            const color = delta > 0 ? 'text-green-600 bg-green-50 border-green-200' : 'text-red-600 bg-red-50 border-red-200';
            diffBadge = `
                <span class="text-xs text-slate-400 line-through mr-1">${originalValue.toLocaleString()}</span>
                <span class="text-sm font-bold text-blue-600 mr-1">${numValue.toLocaleString()}</span>
                <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded border ${color}">${sign}${delta.toLocaleString()}</span>
            `;
        } else {
            diffBadge = `<span class="text-sm font-bold ${numValue > 0 ? 'text-blue-600' : 'text-gray-400'}">${numValue.toLocaleString()}</span>`;
        }

        const row = document.createElement('div');
        row.className = `flex justify-between items-center p-3 rounded-lg transition ${isEdited ? 'bg-amber-50 border border-amber-200' : 'bg-gray-50 hover:bg-gray-100'}`;
        row.innerHTML = `
            <span class="text-sm font-medium text-gray-700 flex items-center gap-1.5">
                ${isEdited ? '<span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500 flex-shrink-0"></span>' : ''}
                ${disciplineLabels[key]}
            </span>
            <span class="flex items-center gap-1">${diffBadge}</span>
        `;
        dataSummary.appendChild(row);
    }

    // Show edit summary banner if in update mode
    const existingSummaryBanner = document.getElementById('editSummaryBanner');
    if (existingSummaryBanner) existingSummaryBanner.remove();
    if (existingData && existingData.disciplines) {
        const changedCount = Object.keys(data.disciplines).filter(k => {
            return (parseInt(data.disciplines[k]) || 0) !== (parseInt(existingData.disciplines[k]) || 0);
        }).length;
        if (changedCount > 0) {
            const banner = document.createElement('div');
            banner.id = 'editSummaryBanner';
            banner.className = 'mb-4 flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2.5 text-sm text-amber-800 font-medium';
            banner.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> ${changedCount} discipline${changedCount > 1 ? 's' : ''} edited — highlighted below`;
            dataSummary.parentNode.insertBefore(banner, dataSummary);
        }
    }

    document.getElementById('confirmGrandTotal').textContent = grandTotal.toLocaleString();
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    pendingData = null;
}

// ─── Success Modal ───────────────────────────────────────────────────────────
function showSuccessModal(isUpdate = false) {
    const message = document.querySelector('#successModal p.text-gray-600');
    if (message) {
        message.textContent = isUpdate
            ? 'Enrollment data has been updated successfully.'
            : 'Enrollment data has been submitted successfully.';
    }
    document.getElementById('successModal').classList.remove('hidden');
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');

    clearForm();

    document.getElementById('academicYear').value = '';
    const yearStatusMessage = document.getElementById('yearStatusMessage');
    if (yearStatusMessage) {
        yearStatusMessage.textContent = '';
        yearStatusMessage.className = 'text-sm mt-1';
    }

    document.getElementById('displayYear').textContent = '----';
    toggleYearDisplay(false);
    document.getElementById('cancelYearChangeBtn').classList.add('hidden');
    hideStatusNotification();

    document.getElementById('province').value = '';
    document.querySelectorAll('input[name="institution_type"]').forEach(r => r.checked = false);
    document.getElementById('grandTotal').textContent = '0';

    existingData = null;
    oldYear = null;
    lockForm();
}

// ─── Form Submit Handler ─────────────────────────────────────────────────────
async function confirmSubmit() {
    const dataToSubmit = pendingData;
    closeConfirmModal();

    try {
        if (oldYear && oldYear !== '----') {
            try {
                const province = dataToSubmit.province;
                const institutionType = dataToSubmit.institution_type;
                await fetch(`/api/discipline-enrollment/delete/${oldYear}?province=${encodeURIComponent(province)}&institution_type=${encodeURIComponent(institutionType)}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                console.log(`Deleted old year data: ${oldYear} - ${province} - ${institutionType}`);
            } catch (deleteError) {
                console.error('Error deleting old year:', deleteError);
            }
        }

        const response = await fetch(window.AppRoutes.storeEnrollment, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(dataToSubmit)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            const wasUpdate = !!existingData;
            oldYear = null;
            unlockSelections();
            showSuccessModal(wasUpdate);
        } else {
            showToast('Error: ' + (result.message || 'An error occurred while saving the data.'), 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred while saving the data.', 'error');
    }
}

// ─── DOMContentLoaded Init ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    // Province change listener
    const provinceSelect = document.getElementById('province');
    if (provinceSelect) {
        provinceSelect.addEventListener('change', function () {
            handleProvinceChange(this.value);
        });
    }

    // Discipline input formatting
    initDisciplineInputs();

    // Form submit listener
    const disciplineForm = document.getElementById('disciplineForm');
    if (disciplineForm) {
        disciplineForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const academicYear = document.getElementById('displayYear').textContent;

            if (academicYear === '----') {
                showToast('Please click "Check Year" first to select an academic year.', 'warning');
                return;
            }

            const province = document.getElementById('province').value;
            const institutionType = document.querySelector('input[name="institution_type"]:checked');

            if (!province || !institutionType) {
                showToast('Please select a Province and Institution Type before checking the year.', 'error');
                if (!province) {
                    const provinceEl = document.getElementById('province');
                    provinceEl.classList.add('border-red-500', 'ring-2', 'ring-red-300');
                    provinceEl.addEventListener('change', () => provinceEl.classList.remove('border-red-500', 'ring-2', 'ring-red-300'), { once: true });
                }
                if (!institutionType) {
                    document.querySelectorAll('input[name="institution_type"]').forEach(r => {
                        r.closest('label').classList.add('text-red-600');
                        r.addEventListener('change', () => {
                            document.querySelectorAll('input[name="institution_type"]').forEach(x => x.closest('label').classList.remove('text-red-600'));
                        }, { once: true });
                    });
                }
                return;
            }

            const disciplines = {
                agriculture: document.querySelector('input[name="agriculture"]').value,
                architecture: document.querySelector('input[name="architecture"]').value,
                business: document.querySelector('input[name="business"]').value,
                criminal_justice: document.querySelector('input[name="criminal_justice"]').value,
                education: document.querySelector('input[name="education"]').value,
                engineering: document.querySelector('input[name="engineering"]').value,
                arts: document.querySelector('input[name="arts"]').value,
                general: document.querySelector('input[name="general"]').value,
                home_economics: document.querySelector('input[name="home_economics"]').value,
                humanities: document.querySelector('input[name="humanities"]').value,
                it: document.querySelector('input[name="it"]').value,
                law: document.querySelector('input[name="law"]').value,
                maritime: document.querySelector('input[name="maritime"]').value,
                mass_comm: document.querySelector('input[name="mass_comm"]').value,
                mathematics: document.querySelector('input[name="mathematics"]').value,
                medical: document.querySelector('input[name="medical"]').value,
                natural_science: document.querySelector('input[name="natural_science"]').value,
                other_disciplines: document.querySelector('input[name="other_disciplines"]').value,
                religion: document.querySelector('input[name="religion"]').value,
                service_trades: document.querySelector('input[name="service_trades"]').value,
                social_sciences: document.querySelector('input[name="social_sciences"]').value
            };

            const cleanedDisciplines = {};
            for (const [key, value] of Object.entries(disciplines)) {
                const raw = String(value).replace(/,/g, '');
                cleanedDisciplines[key] = raw ? parseInt(raw) : 0;
            }

            const dataToSave = {
                academic_year: academicYear,
                province: province,
                institution_type: institutionType.value,
                disciplines: cleanedDisciplines
            };

            console.log('Data being sent to server:', dataToSave);
            console.log('Existing data flag:', existingData ? 'UPDATE MODE' : 'CREATE MODE');

            showConfirmModal(dataToSave);
        });
    }
});
window.checkAndLoadYear = checkAndLoadYear;
window.cancelYearChange = cancelYearChange;
window.closeExistingDataModal = closeExistingDataModal;
window.confirmLoadExistingData = confirmLoadExistingData;
window.closeYearCollisionModal = closeYearCollisionModal;
window.confirmReset = confirmReset;
window.closeResetModal = closeResetModal;
window.doReset = doReset;
window.closeConfirmModal = closeConfirmModal;
window.confirmSubmit = confirmSubmit;
window.closeSuccessModal = closeSuccessModal;
window.filterDisciplines = filterDisciplines;