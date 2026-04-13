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

// ─── Reset Modal ─────────────────────────────────────────────────────────────
function closeResetModal() {
    document.getElementById('resetModal').classList.add('hidden');
}

function doReset() {
    document.getElementById('resetModal').classList.add('hidden');
    document.querySelectorAll('input[type="number"]').forEach(function (input) {
        if (input.id !== 'year') {
            input.value = '';
        }
    });
    document.getElementById('month').value = '';
}

// ─── Populate Confirm Summary Modal ──────────────────────────────────────────
function populateConfirmSummary() {
    const monthNames = { 1: 'January', 4: 'April', 7: 'July', 10: 'October' };
    const year = document.getElementById('year').value;
    const month = document.getElementById('month').value;
    const monthLabel = monthNames[parseInt(month)] || '—';

    document.getElementById('summaryPeriod').textContent = `${monthLabel} ${year}`;

    const fields = [
        { label: 'Household Population',           id: 'householdPopulation', unit: '',  auto: false },
        { label: 'Labor Force Participation Rate',  id: 'lfpr',                unit: '%', auto: false },
        { label: 'Employment Rate',                 id: 'employmentrate',      unit: '%', auto: false },
        { label: 'Underemployment Rate',            id: 'underemploymentrate', unit: '%', auto: false },
        { label: 'Unemployment Rate',               id: 'unemploymentrate',    unit: '%', auto: false },
        { label: 'Labor Force',                     id: 'laborForce',          unit: '',  auto: true  },
        { label: 'Employed',                        id: 'employed',            unit: '',  auto: true  },
        { label: 'Underemployed',                   id: 'underemployed',       unit: '',  auto: true  },
        { label: 'Unemployed',                      id: 'unemployed',          unit: '',  auto: true  },
    ];

    const container = document.getElementById('summaryRows');
    container.innerHTML = '';

    fields.forEach(f => {
        const raw = document.getElementById(f.id).value;
        const display = raw !== '' ? (parseFloat(raw).toLocaleString() + (f.unit ? ' ' + f.unit : '')) : '—';

        const row = document.createElement('div');
        row.className = 'flex justify-between items-center px-3 py-2 rounded-lg ' + (f.auto ? 'bg-emerald-50' : 'bg-slate-50');
        row.innerHTML = `
            <span class="text-[12.5px] font-medium text-slate-600 flex items-center gap-1.5">
                ${f.auto ? '<span class="text-[10px] font-semibold text-teal-600 bg-teal-50 border border-teal-200 px-1.5 py-0.5 rounded">auto</span>' : ''}
                ${f.label}
            </span>
            <span class="text-[13px] font-bold ${raw !== '' ? (f.auto ? 'text-teal-700' : 'text-slate-800') : 'text-slate-300'}">${display}</span>
        `;
        container.appendChild(row);
    });
}

// ─── DOMContentLoaded Init ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const yearInput               = document.getElementById('year');
    const monthInput              = document.getElementById('month');
    const householdInput          = document.getElementById('householdPopulation');
    const lfprInput               = document.getElementById('lfpr');
    const employmentRateInput     = document.getElementById('employmentrate');
    const underemploymentRateInput = document.getElementById('underemploymentrate');
    const unemploymentRateInput   = document.getElementById('unemploymentrate');
    const laborForceInput         = document.getElementById('laborForce');
    const employedInput           = document.getElementById('employed');
    const underemployedInput      = document.getElementById('underemployed');
    const unemployedInput         = document.getElementById('unemployed');
    const resetBtn                = document.getElementById('resetBtn');
    const saveBtn                 = document.getElementById('saveBtn');

    // ─── Auto-calculation Helpers ─────────────────────────────────────────────
    function normalizeRate(rate) {
        return rate > 1 ? rate / 100 : rate;
    }

    function calculateLaborForce() {
        const household = parseFloat(householdInput.value);
        const lfpr = parseFloat(lfprInput.value);

        if (isNaN(household) || isNaN(lfpr)) {
            laborForceInput.value = '';
            employedInput.value = '';
            underemployedInput.value = '';
            return;
        }

        const laborForce = household * normalizeRate(lfpr);
        laborForceInput.value = Math.round(laborForce);

        calculateEmployed();
        calculateUnemployed();
    }

    function calculateEmployed() {
        const laborForce = parseFloat(laborForceInput.value);
        const employmentRate = parseFloat(employmentRateInput.value);

        if (isNaN(laborForce) || isNaN(employmentRate)) {
            employedInput.value = '';
            return;
        }

        const employed = laborForce * normalizeRate(employmentRate);
        employedInput.value = Math.round(employed);

        calculateUnderemployed();
    }

    function calculateUnderemployed() {
        const employed = parseFloat(employedInput.value);
        const underemploymentRate = parseFloat(underemploymentRateInput.value);

        if (isNaN(employed) || isNaN(underemploymentRate)) {
            underemployedInput.value = '';
            return;
        }

        underemployedInput.value = Math.round(employed * normalizeRate(underemploymentRate));
    }

    function calculateUnemployed() {
        const laborForce = parseFloat(laborForceInput.value);
        const unemploymentRate = parseFloat(unemploymentRateInput.value);

        if (isNaN(laborForce) || isNaN(unemploymentRate)) {
            unemployedInput.value = '';
            return;
        }

        unemployedInput.value = Math.round(laborForce * normalizeRate(unemploymentRate));
    }

    // ─── Input Listeners ──────────────────────────────────────────────────────
    householdInput.addEventListener('input', calculateLaborForce);
    lfprInput.addEventListener('input', calculateLaborForce);
    employmentRateInput.addEventListener('input', calculateEmployed);
    underemploymentRateInput.addEventListener('input', calculateUnderemployed);
    unemploymentRateInput.addEventListener('input', calculateUnemployed);

    // Block non-digit keys and enforce max 4 chars on year input
    yearInput.addEventListener('keydown', function (e) {
        const allowed = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'];
        if (!allowed.includes(e.key) && !/^\d$/.test(e.key)) {
            e.preventDefault();
        }
        if (this.value.length >= 4 && !allowed.includes(e.key)) {
            e.preventDefault();
        }
    });
    yearInput.addEventListener('input', function () {
        this.value = this.value.replace(/[^\d]/g, '').slice(0, 4);
    });

    // ─── Reset Button ─────────────────────────────────────────────────────────
    resetBtn.addEventListener('click', function () {
        document.getElementById('resetModal').classList.remove('hidden');
    });

    // ─── Save Button — Validation & Submit ────────────────────────────────────
    saveBtn.addEventListener('click', function () {

        // 1. Year required
        if (!yearInput.value) {
            showToast('Year is required. Please enter a valid 4-digit year (e.g. 2026).', 'warning');
            yearInput.focus();
            return;
        }

        // 2. Year format & range
        const yearVal = yearInput.value.toString().trim();
        const yearNum = parseInt(yearVal);

        if (!/^\d{4}$/.test(yearVal)) {
            showToast('Invalid year format. Please enter a complete 4-digit year (e.g. 2026).', 'warning');
            yearInput.focus();
            return;
        }

        if (yearNum < 2000) {
            showToast('Year must not be earlier than 2000. Please enter a valid year.', 'warning');
            yearInput.focus();
            return;
        }

        if (yearNum > 2100) {
            showToast('Year must not exceed 2100. Please enter a valid year.', 'warning');
            yearInput.focus();
            return;
        }

        // 3. Month required
        if (!monthInput.value) {
            showToast('Please select a Month before submitting.', 'warning');
            monthInput.focus();
            return;
        }

        // 4. All manual fields required
        const requiredFields = [
            { input: householdInput,            label: 'Household Population' },
            { input: lfprInput,                 label: 'Labor Force Participation Rate' },
            { input: employmentRateInput,       label: 'Employment Rate' },
            { input: underemploymentRateInput,  label: 'Underemployment Rate' },
            { input: unemploymentRateInput,     label: 'Unemployment Rate' },
        ];

        for (const field of requiredFields) {
            if (field.input.value === '' || field.input.value === null) {
                showToast(`"${field.label}" is required. Please fill in all fields before submitting.`, 'warning');
                field.input.focus();
                return;
            }
        }

        saveBtn.disabled = true;
        saveBtn.textContent = 'Checking...';

        // 5. Check for existing record
        fetch(window.AppRoutes.laborMarketCheck, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                year: yearInput.value,
                month: monthInput.value
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.exists) {
                document.getElementById('errorMessage').textContent = result.message;
                document.getElementById('errorModal').classList.remove('hidden');
            } else {
                populateConfirmSummary();
                document.getElementById('confirmModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Check failed:', error);
            showToast('An error occurred while checking for existing data. Please try again.', 'error');
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Data';
        });
    });

    // ─── Confirm Modal — Cancel ───────────────────────────────────────────────
    document.getElementById('cancelBtn').addEventListener('click', function () {
        document.getElementById('confirmModal').classList.add('hidden');
    });

    // ─── Confirm Modal — Submit ───────────────────────────────────────────────
    document.getElementById('confirmBtn').addEventListener('click', function () {
        document.getElementById('confirmModal').classList.add('hidden');

        const data = {
            year:                        parseInt(yearInput.value),
            month:                       parseInt(monthInput.value),
            household_population:        parseFloat(householdInput.value) || null,
            labor_force:                 parseFloat(laborForceInput.value) || null,
            employed:                    parseFloat(employedInput.value) || null,
            underemployed:               parseFloat(underemployedInput.value) || null,
            unemployed:                  parseFloat(unemployedInput.value) || null,
            labor_force_participation_rate: parseFloat(lfprInput.value) || null,
            employment_rate:             parseFloat(employmentRateInput.value) || null,
            underemployment_rate:        parseFloat(underemploymentRateInput.value) || null,
            unemployment_rate:           parseFloat(unemploymentRateInput.value) || null
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        saveBtn.disabled = true;
        saveBtn.textContent = 'Submitting...';

        fetch(window.AppRoutes.laborMarketSubmitPending, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(result => {
            if (result.success === true) {
                document.getElementById('successModal').classList.remove('hidden');
                console.log('Saved data:', result.data);
            } else {
                document.getElementById('errorMessage').textContent = result.message || 'Failed to save data';
                document.getElementById('errorModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            let errorMsg = 'Network error occurred';

            if (error.message) {
                errorMsg = error.message;
            } else if (error.errors) {
                errorMsg = Object.values(error.errors).flat().join('\n');
            }

            document.getElementById('errorMessage').textContent = errorMsg;
            document.getElementById('errorModal').classList.remove('hidden');
            console.error('Error:', error);
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Data';
        });
    });

    // ─── Success Modal — Close ────────────────────────────────────────────────
    document.getElementById('closeModalBtn').addEventListener('click', function () {
        document.getElementById('successModal').classList.add('hidden');
        doReset();
    });

    // ─── Error Modal — Close ──────────────────────────────────────────────────
    document.getElementById('closeErrorBtn').addEventListener('click', function () {
        document.getElementById('errorModal').classList.add('hidden');
    });
});

// ─── Global Exports (required for Blade onclick handlers) ────────────────────
window.closeResetModal    = closeResetModal;
window.doReset            = doReset;