// ─── State Variables ────────────────────────────────────────────────────────
let currentYear = null;
let graduationRateData = null;
let pendingYearData = null;
let descriptionQuill = null;

// ─── Init Quill Description Editor ──────────────────────────────────────────
function initDescriptionQuill() {
    if (descriptionQuill) return;
    descriptionQuill = new Quill('#descriptionQuillEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['clean']
            ]
        },
        placeholder: 'e.g. Rate adjusted based on regional trends and post-pandemic recovery data...',
    });

    descriptionQuill.on('text-change', () => {
        document.getElementById('descriptionEditorWrapper').classList.remove('border-error');
        document.getElementById('rateStatusUnsaved').style.display = 'block';
        document.getElementById('rateStatusSaved').style.display = 'none';
    });
}

function getDescriptionHtml() {
    if (!descriptionQuill) return '';
    const text = descriptionQuill.getText().trim();
    return text.length === 0 ? '' : descriptionQuill.root.innerHTML;
}

function setDescriptionHtml(html) {
    if (!descriptionQuill) return;
    descriptionQuill.root.innerHTML = html || '';
}

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

// ─── Year Input Helpers ──────────────────────────────────────────────────────
function toggleClearBtn(value) {
    const btn = document.getElementById('clearYearBtn');
    value.trim() ? btn.classList.remove('hidden') : btn.classList.add('hidden');
}

function clearYearInput() {
    resetForm();
}

// ─── Year Check & Load ───────────────────────────────────────────────────────
async function checkAndLoadYear() {
    const yearInput = document.getElementById('academicYear');
    const year = yearInput.value.trim();

    if (!year) {
        showToast('Please enter an academic year (e.g., 2024-2025)', 'error');
        return;
    }

    if (!/^\d{4}-\d{4}$/.test(year)) {
        showToast('Please enter a valid academic year format (e.g., 2024-2025)', 'error');
        return;
    }

    const [startYear, endYear] = year.split('-').map(Number);
    if (endYear !== startYear + 1) {
        showToast('Invalid academic year — the second year must be exactly one after the first (e.g., 2024-2025)', 'error');
        return;
    }

    try {
        const response = await fetch(`/api/graduation-rate/${year}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        const hasSavedRecord = result.success && result.data && result.data.id;

        if (hasSavedRecord) {
            pendingYearData = { year, data: result.data };
            showExistingDataModal(year, result.data.graduation_rate);
        } else {
            loadNewYear(year);
        }
    } catch (error) {
        console.error('Error checking year:', error);
        showToast('An error occurred while checking the year. Please try again.', 'error');
    }
}

function loadNewYear(year) {
    currentYear = year;
    graduationRateData = null;
    showStatusNotification(year, false);
    loadEnrollmentContext(year);
}

// ─── Existing Data Modal ─────────────────────────────────────────────────────
function showExistingDataModal(year, rate) {
    document.getElementById('existingDataYear').textContent = year;
    document.getElementById('existingDataRate').textContent = parseFloat(rate || 0).toFixed(2);
    document.getElementById('existingDataModal').classList.remove('hidden');
}

function closeExistingDataModal() {
    document.getElementById('existingDataModal').classList.add('hidden');
    pendingYearData = null;
    resetForm();
}

function confirmLoadExistingData() {
    if (!pendingYearData) return;
    const { year, data } = pendingYearData;
    currentYear = year;
    graduationRateData = data;
    document.getElementById('graduationRateCard').style.display = 'block';
    initDescriptionQuill();
    displayGraduationRateData(data);
    showStatusNotification(year, true);
    document.getElementById('existingDataModal').classList.add('hidden');
    pendingYearData = null;
}

// ─── Reset Form ──────────────────────────────────────────────────────────────
function resetForm() {
    const input = document.getElementById('academicYear');
    input.value = '';
    document.getElementById('clearYearBtn').classList.add('hidden');
    if (descriptionQuill) { descriptionQuill.setContents([]); }
    hideStatusNotification();
    clearMissingEnrollmentWarning();
    hideFutureYearWarning();
    document.getElementById('graduationRateCard').style.display = 'none';
    currentYear = null;
    graduationRateData = null;
    setTimeout(() => document.getElementById('academicYear').focus(), 100);
}

// ─── Status Notification ─────────────────────────────────────────────────────
function showStatusNotification(year, exists) {
    const notification = document.getElementById('statusNotification');
    const icon = document.getElementById('statusIcon');
    const title = document.getElementById('statusTitle');
    const message = document.getElementById('statusMessage');

    if (exists) {
        notification.className = 'mb-8 p-6 rounded-2xl shadow-lg bg-purple-50 border-2 border-purple-200';
        icon.className = 'flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-purple-500 text-white text-2xl';
        icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
        title.textContent = 'Editing Existing Data';
        title.className = 'text-lg font-bold mb-1 text-purple-900';
        message.textContent = `Loading graduation rate data for ${year}. You can now edit the existing rate.`;
        message.className = 'text-sm text-purple-800';
    } else {
        notification.className = 'mb-8 p-6 rounded-2xl shadow-lg bg-green-50 border-2 border-green-200';
        icon.className = 'flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-green-500 text-white text-2xl';
        icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        title.textContent = 'Creating New Data';
        title.className = 'text-lg font-bold mb-1 text-green-900';
        message.textContent = `No existing graduation rate found for ${year}. You can now set a new rate.`;
        message.className = 'text-sm text-green-800';
    }

    notification.classList.remove('hidden');
}

function hideStatusNotification() {
    document.getElementById('statusNotification').classList.add('hidden');
}

// ─── Load & Display Graduation Rate Data ─────────────────────────────────────
async function loadGraduationRateData(graduateYear) {
    try {
        const response = await fetch(`/api/graduation-rate/${graduateYear}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) return;

        const result = await response.json();

        if (result.success && result.data) {
            graduationRateData = result.data;
            document.getElementById('graduationRateCard').style.display = 'block';
            initDescriptionQuill();
            displayGraduationRateData(result.data);
        }
    } catch (error) {
        console.error('Error loading graduation rate:', error);
    }
}

// ─── Future Year Detection ───────────────────────────────────────────────────
function isGraduateYearInFuture(graduateYear) {
    const startYear = parseInt(graduateYear.split('-')[0]);
    const now = new Date();
    const thisYear = now.getFullYear();
    return startYear > thisYear;
}

function showFutureYearWarning(graduateYear) {
    document.getElementById('futureGraduateYear').textContent = graduateYear;

    const startYear = graduateYear.split('-')[0];
    document.getElementById('futureYearUnlockYear').textContent = startYear;
    document.getElementById('futureYearWarning').classList.remove('hidden');

    const saveBtn = document.querySelector('button[onclick="saveGraduationRate()"]');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
        saveBtn.title = `Saving locked until ${startYear} — this academic year has not started yet`;
    }
}

function hideFutureYearWarning() {
    document.getElementById('futureYearWarning').classList.add('hidden');

    const saveBtn = document.querySelector('button[onclick="saveGraduationRate()"]');
    if (saveBtn) {
        saveBtn.disabled = false;
        saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        saveBtn.title = '';
    }
}

// ─── Missing Enrollment Handler ──────────────────────────────────────────────
function handleMissingEnrollment(enrollmentYear) {
    document.getElementById('missingEnrollmentYear').textContent = enrollmentYear || '(unknown year)';
    document.getElementById('missingEnrollmentWarning').classList.remove('hidden');

    const saveBtn = document.querySelector('button[onclick="saveGraduationRate()"]');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
        saveBtn.title = 'Cannot save — enrollment data for the base year is missing';
    }

    document.getElementById('graduationRateInput').disabled = true;
    document.getElementById('graduationRateInput').classList.add('bg-gray-100', 'cursor-not-allowed');
    document.getElementById('graduationRateSlider').disabled = true;
}

function clearMissingEnrollmentWarning() {
    document.getElementById('missingEnrollmentWarning').classList.add('hidden');

    const saveBtn = document.querySelector('button[onclick="saveGraduationRate()"]');
    if (saveBtn) {
        saveBtn.disabled = false;
        saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        saveBtn.title = '';
    }

    document.getElementById('graduationRateInput').disabled = false;
    document.getElementById('graduationRateInput').classList.remove('bg-gray-100', 'cursor-not-allowed');
    document.getElementById('graduationRateSlider').disabled = false;
}

// ─── Load Enrollment Context (new year — no existing rate) ───────────────────
async function loadEnrollmentContext(graduateYear) {
    try {
        const response = await fetch(`/api/graduation-rate/${graduateYear}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        if (result.success && result.data) {
            const data = result.data;

            document.getElementById('projGraduateYear').textContent = data.graduate_year || graduateYear;
            document.getElementById('projEnrollmentYear').textContent = data.enrollment_year || '----';
            document.getElementById('projBaseEnrollees').textContent = (data.base_enrollees || 0).toLocaleString();

            if (isGraduateYearInFuture(graduateYear)) {
                showFutureYearWarning(graduateYear);
            } else {
                hideFutureYearWarning();
            }

            if (!data.base_enrollees || data.base_enrollees === 0) {
                handleMissingEnrollment(data.enrollment_year);
            } else {
                clearMissingEnrollmentWarning();
                document.getElementById('graduationRateInput').value = '60.00';
                document.getElementById('graduationRateSlider').value = 60;
                setDescriptionHtml('');
                document.getElementById('descriptionEditorWrapper').classList.remove('border-error');
                document.getElementById('rateStatusSaved').style.display = 'none';
                document.getElementById('rateStatusUnsaved').style.display = 'none';
                calculateProjection();
            }

            document.getElementById('graduationRateCard').style.display = 'block';
            initDescriptionQuill();
        }
    } catch (error) {
        console.error('Error loading enrollment context:', error);
    }
}

function displayGraduationRateData(data) {
    document.getElementById('projGraduateYear').textContent = data.graduate_year;
    document.getElementById('projEnrollmentYear').textContent = data.enrollment_year;
    document.getElementById('projBaseEnrollees').textContent = (data.base_enrollees || 0).toLocaleString();
    document.getElementById('graduationRateInput').value = parseFloat(data.graduation_rate || 60).toFixed(2);
    document.getElementById('graduationRateSlider').value = Math.round(data.graduation_rate || 60);
    setDescriptionHtml(data.description || '');
    document.getElementById('descriptionEditorWrapper').classList.remove('border-error');

    if (data.graduate_year && isGraduateYearInFuture(data.graduate_year)) {
        showFutureYearWarning(data.graduate_year);
    } else {
        hideFutureYearWarning();
    }

    if (!data.base_enrollees || data.base_enrollees === 0) {
        handleMissingEnrollment(data.enrollment_year);
    } else {
        clearMissingEnrollmentWarning();
        calculateProjection();
        if (!data.is_default) {
            document.getElementById('rateStatusSaved').style.display = 'block';
            document.getElementById('rateStatusUnsaved').style.display = 'none';
        } else {
            document.getElementById('rateStatusSaved').style.display = 'none';
            document.getElementById('rateStatusUnsaved').style.display = 'none';
        }
    }
}

// ─── Projection Calculation ──────────────────────────────────────────────────
function calculateProjection() {
    const baseEnrollees = parseInt(document.getElementById('projBaseEnrollees').textContent.replace(/,/g, '')) || 0;
    const rate = parseFloat(document.getElementById('graduationRateInput').value) || 0;
    const projected = Math.round(baseEnrollees * (rate / 100));

    document.getElementById('projectedGraduates').textContent = projected.toLocaleString();
    document.getElementById('projCalcEnrollees').textContent = baseEnrollees.toLocaleString();
    document.getElementById('projCalcRate').textContent = rate.toFixed(2) + '%';
    document.getElementById('projCalcResult').textContent = projected.toLocaleString();

    document.getElementById('rateStatusSaved').style.display = 'none';
    document.getElementById('rateStatusUnsaved').style.display = 'block';
}

function updateRateFromSlider(value) {
    document.getElementById('graduationRateInput').value = parseFloat(value).toFixed(2);
    calculateProjection();
}

// ─── Confirm Save Modal ──────────────────────────────────────────────────────
function saveGraduationRate() {
    if (!currentYear) {
        showToast('Please select an academic year first', 'warning');
        return;
    }

    const graduationRate = parseFloat(document.getElementById('graduationRateInput').value);

    if (isNaN(graduationRate) || graduationRate < 0 || graduationRate > 100) {
        showToast('Graduation rate must be between 0 and 100', 'error');
        return;
    }

    const description = getDescriptionHtml();
    if (!description) {
        showToast('Description is required — please provide context for this year\'s graduation rate.', 'error');
        document.getElementById('descriptionEditorWrapper').classList.add('border-error');
        document.getElementById('descriptionEditorWrapper').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    document.getElementById('descriptionEditorWrapper').classList.remove('border-error');

    const isUpdate = graduationRateData && !graduationRateData.is_default;
    const projected = document.getElementById('projectedGraduates').textContent;
    const enrollees = document.getElementById('projBaseEnrollees').textContent;

    document.getElementById('confirmSaveYear').textContent = currentYear;
    document.getElementById('confirmSaveAction').textContent = isUpdate ? 'update' : 'create new';
    document.getElementById('confirmSaveBtnText').textContent = isUpdate ? 'Update' : 'Save';
    document.getElementById('confirmSaveRate').textContent = graduationRate.toFixed(2) + '%';
    document.getElementById('confirmSaveProjected').textContent = projected;
    document.getElementById('confirmSaveEnrollees').textContent = enrollees;
    document.getElementById('confirmSaveDescription').innerHTML = description;

    if (isUpdate) {
        const oldRate = parseFloat(graduationRateData.graduation_rate || 0).toFixed(2) + '%';
        const newRate = graduationRate.toFixed(2) + '%';
        const rateChanged = oldRate !== newRate;

        const stripHtml = html => (new DOMParser().parseFromString(html, 'text/html')).body.textContent.trim();
        const oldDescTxt = stripHtml(graduationRateData.description || '');
        const newDescTxt = stripHtml(description);
        const descChanged = oldDescTxt !== newDescTxt;

        if (rateChanged || descChanged) {
            document.getElementById('confirmSaveDeletionWarning').classList.remove('hidden');

            const rateRow = document.getElementById('confirmRateChangeRow');
            if (rateChanged) {
                document.getElementById('confirmOldRate').textContent = oldRate;
                document.getElementById('confirmNewRate').textContent = newRate;
                rateRow.classList.remove('hidden');
            } else {
                rateRow.classList.add('hidden');
            }

            const descRow = document.getElementById('confirmDescriptionChangeRow');
            descChanged ? descRow.classList.remove('hidden') : descRow.classList.add('hidden');
        } else {
            document.getElementById('confirmSaveDeletionWarning').classList.add('hidden');
        }
    } else {
        document.getElementById('confirmSaveDeletionWarning').classList.add('hidden');
    }

    document.getElementById('confirmSaveModal').classList.remove('hidden');
}

function closeConfirmSaveModal() {
    document.getElementById('confirmSaveModal').classList.add('hidden');
}

async function confirmSaveRate() {
    closeConfirmSaveModal();

    const graduationRate = parseFloat(document.getElementById('graduationRateInput').value);

    try {
        const response = await fetch('/api/graduation-rate/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                graduate_year: currentYear,
                graduation_rate: graduationRate,
                description: getDescriptionHtml()
            })
        });

        const result = await response.json();

        if (result.success) {
            graduationRateData = result.data;
            document.getElementById('rateStatusSaved').style.display = 'block';
            document.getElementById('rateStatusUnsaved').style.display = 'none';
            showSuccessModal();
            setTimeout(() => {
                document.getElementById('rateStatusSaved').style.display = 'none';
            }, 3000);
        } else {
            showToast('Error saving graduation rate: ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred while saving the graduation rate. Please try again.', 'error');
    }
}

// ─── Success Modal ───────────────────────────────────────────────────────────
function showSuccessModal() {
    document.getElementById('successModal').classList.remove('hidden');
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    resetForm();
}

// ─── DOMContentLoaded Init ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    // Rate input clamp + sync slider
    const rateInput = document.getElementById('graduationRateInput');
    if (rateInput) {
        rateInput.addEventListener('input', function () {
            let val = parseFloat(this.value);
            if (!isNaN(val)) {
                if (val < 0) { this.value = '0.00'; val = 0; }
                if (val > 100) { this.value = '100.00'; val = 100; }
            }
            document.getElementById('graduationRateSlider').value = Math.round(isNaN(val) ? 0 : val);
        });
    }
});
// ─── Global Exports (required for Blade onclick handlers) ────────────────────
window.checkAndLoadYear       = checkAndLoadYear;
window.clearYearInput         = clearYearInput;
window.toggleClearBtn         = toggleClearBtn;
window.closeExistingDataModal = closeExistingDataModal;
window.confirmLoadExistingData = confirmLoadExistingData;
window.saveGraduationRate     = saveGraduationRate;
window.closeConfirmSaveModal  = closeConfirmSaveModal;
window.confirmSaveRate        = confirmSaveRate;
window.updateRateFromSlider   = updateRateFromSlider;
window.calculateProjection    = calculateProjection;
window.closeSuccessModal      = closeSuccessModal;