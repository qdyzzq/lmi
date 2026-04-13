// ─── Number Formatting Helpers ───────────────────────────────────────────────

// Format a numeric string with commas, preserving decimals and trailing dot
function formatWithCommas(str) {
    let clean = str.replace(/[^0-9.]/g, '');
    const parts = clean.split('.');
    if (parts.length > 2) clean = parts[0] + '.' + parts.slice(1).join('');
    const [intPart, decPart] = clean.split('.');
    const formatted = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return decPart !== undefined ? formatted + '.' + decPart : formatted;
}

// Get raw numeric value from a formatted-number input (strips commas)
function getRaw(input) {
    return parseFloat(input.value.replace(/,/g, '')) || 0;
}

// Set formatted display value on a formatted-number input
function setFormatted(input, rawValue) {
    if (rawValue === '' || isNaN(rawValue)) {
        input.value = '';
        return;
    }
    const rounded = Math.round(rawValue);
    input.value = rounded.toLocaleString('en-US');
}


// ─── Calculation Logic ────────────────────────────────────────────────────────

function normalizeRate(value) {
    return value > 1 ? value / 100 : value;
}

function getRawField(card, field) {
    const el = card.querySelector(`[data-field="${field}"]`);
    if (!el) return NaN;
    return parseFloat(el.value.replace(/,/g, ''));
}

function setAutoField(card, field, rawValue) {
    const el = card.querySelector(`[data-field="${field}"]`);
    if (!el) return;
    const rounded = Math.round(rawValue);
    el.value = rounded.toLocaleString('en-US');
}

function calculateLaborForce(card) {
    const household = getRawField(card, 'household_population');
    const lfpr = getRawField(card, 'lfpr');

    if (isNaN(household) || isNaN(lfpr)) {
        card.querySelector('[data-field="labor_force"]').value = '';
        return;
    }

    const laborForce = household * normalizeRate(lfpr);
    setAutoField(card, 'labor_force', laborForce);

    calculateEmployed(card);
    calculateUnemployed(card);
}

function calculateEmployed(card) {
    const laborForce = getRawField(card, 'labor_force');
    const employmentRate = getRawField(card, 'employment_rate');

    if (isNaN(laborForce) || isNaN(employmentRate)) {
        card.querySelector('[data-field="employed"]').value = '';
        return;
    }

    const employed = laborForce * normalizeRate(employmentRate);
    setAutoField(card, 'employed', employed);
    calculateUnderemployed(card);
}

function calculateUnderemployed(card) {
    const employed = getRawField(card, 'employed');
    const underemploymentRate = getRawField(card, 'underemployment_rate');

    if (isNaN(employed) || isNaN(underemploymentRate)) {
        card.querySelector('[data-field="underemployed"]').value = '';
        return;
    }

    const underemployed = employed * normalizeRate(underemploymentRate);
    setAutoField(card, 'underemployed', underemployed);
}

function calculateUnemployed(card) {
    const laborForce = getRawField(card, 'labor_force');
    const unemploymentRate = getRawField(card, 'unemployment_rate');

    if (isNaN(laborForce) || isNaN(unemploymentRate)) {
        card.querySelector('[data-field="unemployed"]').value = '';
        return;
    }

    const unemployed = laborForce * normalizeRate(unemploymentRate);
    setAutoField(card, 'unemployed', unemployed);
}


// ─── Post Verified Data ───────────────────────────────────────────────────────

window.postVerifiedData = function (buttonElement) {
    const card = buttonElement.closest('.pending-record-card');
    const year = card.querySelector('[data-field="year"]').value;
    const month = card.querySelector('[data-field="month"]').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    window.currentPostButton = buttonElement;
    buttonElement.disabled = true;
    buttonElement.innerHTML = '<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Checking...';

    fetch(window._statisticianRoutes.checkPost, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ year: year, month: month })
    })
    .then(response => {
        if (!response.ok) throw new Error(`Server error: ${response.status}`);
        return response.json();
    })
    .then(result => {
        buttonElement.disabled = false;
        buttonElement.innerHTML = '<svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Approve';

        if (result.exists === true) {
            document.getElementById('errorMessage').textContent = result.message || 'Data for this period already exists in the database.';
            document.getElementById('errorModal').classList.remove('hidden');
            window.currentPostButton = null;
        } else if (result.exists === false) {
            populateConfirmSummary(card);
            document.getElementById('confirmModal').classList.remove('hidden');
        } else {
            throw new Error('Unexpected response format from server');
        }
    })
    .catch(error => {
        console.error('Error during check:', error);
        document.getElementById('errorMessage').textContent = `Error: ${error.message}.`;
        document.getElementById('errorModal').classList.remove('hidden');
        buttonElement.disabled = false;
        buttonElement.innerHTML = '<svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Approve';
        window.currentPostButton = null;
    });
};


// ─── Populate Confirm Summary Modal ──────────────────────────────────────────

window.populateConfirmSummary = function (card) {
    const monthNames = { 1: 'January', 4: 'April', 7: 'July', 10: 'October' };

    // Collect submitted values from the "Submitted Value" td (2nd column)
    const rows = card.querySelectorAll('tbody tr');
    const submittedMap = {};
    rows.forEach(row => {
        const input = row.querySelector('[data-field]');
        if (!input) return;
        const f   = input.getAttribute('data-field');
        const tds = row.querySelectorAll('td');
        if (tds.length >= 2) {
            submittedMap[f] = tds[1].textContent.trim().replace(/%$/, '').replace(/,/g, '').trim();
        }
    });

    // Year & Month: string comparison
    const verifiedYear   = card.querySelector('[data-field="year"]').value.trim();
    const verifiedMonth  = parseInt(card.querySelector('[data-field="month"]').value);
    const submittedYear  = (submittedMap['year']  ?? '').trim();
    const submittedMonth = parseInt(submittedMap['month'] ?? '');

    const yearChanged  = submittedYear  !== '' && submittedYear  !== verifiedYear;
    const monthChanged = !isNaN(submittedMonth) && submittedMonth !== verifiedMonth;

    // Reporting Period banner
    const periodEl = document.getElementById('summaryPeriod');
    if (yearChanged || monthChanged) {
        const oldPeriod = `${monthNames[submittedMonth] ?? submittedMonth} ${submittedYear}`;
        const newPeriod = `${monthNames[verifiedMonth]  ?? verifiedMonth} ${verifiedYear}`;
        periodEl.innerHTML = `
            <span class="line-through text-blue-400 font-normal text-[13px]">${oldPeriod}</span>
            <svg class="inline w-3.5 h-3.5 text-amber-500 mx-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            <span class="text-amber-700">${newPeriod}</span>
        `;
    } else {
        periodEl.textContent = `${monthNames[verifiedMonth] ?? verifiedMonth} ${verifiedYear}`;
    }

    // Numeric field definitions
    const fields = [
        { label: 'Household Population',          field: 'household_population', unit: '',  auto: false },
        { label: 'Labor Force Participation Rate', field: 'lfpr',                unit: '%', auto: false },
        { label: 'Employment Rate',                field: 'employment_rate',      unit: '%', auto: false },
        { label: 'Underemployment Rate',           field: 'underemployment_rate', unit: '%', auto: false },
        { label: 'Unemployment Rate',              field: 'unemployment_rate',    unit: '%', auto: false },
        { label: 'Labor Force',                    field: 'labor_force',          unit: '',  auto: true  },
        { label: 'Employed',                       field: 'employed',             unit: '',  auto: true  },
        { label: 'Underemployed',                  field: 'underemployed',        unit: '',  auto: true  },
        { label: 'Unemployed',                     field: 'unemployed',           unit: '',  auto: true  },
    ];

    // Count total edits (year + month + numeric fields)
    let editedCount = (yearChanged ? 1 : 0) + (monthChanged ? 1 : 0);
    const container = document.getElementById('summaryRows');
    container.innerHTML = '';

    // Year row
    const yearRow = document.createElement('div');
    if (yearChanged) {
        yearRow.className = 'grid grid-cols-[1fr_auto_auto] gap-2 items-center px-3 py-2.5 rounded-lg bg-amber-50 border border-amber-200';
        yearRow.innerHTML = `
            <span class="text-[12.5px] font-medium text-slate-700 flex items-center gap-1.5">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                Year
            </span>
            <span class="text-[12px] text-slate-400 line-through text-right w-28">${submittedYear}</span>
            <span class="text-[13px] font-bold text-amber-700 text-right w-28">${verifiedYear}</span>
        `;
    } else {
        yearRow.className = 'flex justify-between items-center px-3 py-2 rounded-lg bg-slate-50';
        yearRow.innerHTML = `
            <span class="text-[12.5px] font-medium text-slate-600">Year</span>
            <span class="text-[13px] font-bold text-slate-800">${verifiedYear}</span>
        `;
    }
    container.appendChild(yearRow);

    // Month row
    const monthRow = document.createElement('div');
    if (monthChanged) {
        monthRow.className = 'grid grid-cols-[1fr_auto_auto] gap-2 items-center px-3 py-2.5 rounded-lg bg-amber-50 border border-amber-200';
        monthRow.innerHTML = `
            <span class="text-[12.5px] font-medium text-slate-700 flex items-center gap-1.5">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                Month
            </span>
            <span class="text-[12px] text-slate-400 line-through text-right w-28">${monthNames[submittedMonth] ?? submittedMonth}</span>
            <span class="text-[13px] font-bold text-amber-700 text-right w-28">${monthNames[verifiedMonth] ?? verifiedMonth}</span>
        `;
    } else {
        monthRow.className = 'flex justify-between items-center px-3 py-2 rounded-lg bg-slate-50';
        monthRow.innerHTML = `
            <span class="text-[12.5px] font-medium text-slate-600">Month</span>
            <span class="text-[13px] font-bold text-slate-800">${monthNames[verifiedMonth] ?? verifiedMonth}</span>
        `;
    }
    container.appendChild(monthRow);

    // Divider between period fields and indicators
    const divider = document.createElement('hr');
    divider.className = 'border-slate-200 my-1';
    container.appendChild(divider);

    fields.forEach(f => {
        const inputEl = card.querySelector(`[data-field="${f.field}"]`);
        if (!inputEl) return;

        const verifiedRaw  = parseFloat(inputEl.value.replace(/,/g, '')) || 0;
        const submittedRaw = parseFloat(submittedMap[f.field] ?? '');
        const isEdited     = !isNaN(submittedRaw) && submittedRaw !== verifiedRaw;

        if (isEdited) editedCount++;

        const fmtVerified  = isNaN(verifiedRaw)  ? '—' : (f.unit === '%' ? verifiedRaw + '%'  : verifiedRaw.toLocaleString());
        const fmtSubmitted = isNaN(submittedRaw) ? '—' : (f.unit === '%' ? submittedRaw + '%' : submittedRaw.toLocaleString());

        // Delta badge
        let deltaBadge = '';
        if (isEdited) {
            const delta = verifiedRaw - submittedRaw;
            const sign  = delta > 0 ? '+' : '';
            const color = delta > 0 ? 'text-green-700 bg-green-50 border-green-200' : 'text-red-700 bg-red-50 border-red-200';
            const deltaFmt = f.unit === '%'
                ? sign + delta.toFixed(2) + '%'
                : sign + delta.toLocaleString();
            deltaBadge = `<span class="text-[11px] font-semibold px-1.5 py-0.5 rounded border ${color} ml-1">${deltaFmt}</span>`;
        }

        const row = document.createElement('div');

        if (isEdited) {
            row.className = 'grid grid-cols-[1fr_auto_auto] gap-2 items-center px-3 py-2.5 rounded-lg bg-amber-50 border border-amber-200';
            row.innerHTML = `
                <span class="text-[12.5px] font-medium text-slate-700 flex items-center gap-1.5">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                    ${f.auto ? '<span class="text-[10px] font-semibold text-teal-600 bg-teal-50 border border-teal-200 px-1.5 py-0.5 rounded">auto</span>' : ''}
                    ${f.label}
                </span>
                <span class="text-[12px] text-slate-400 line-through text-right w-28">${fmtSubmitted}</span>
                <span class="text-[13px] font-bold text-amber-700 text-right w-28 flex items-center justify-end gap-1">
                    ${fmtVerified}${deltaBadge}
                </span>
            `;
        } else {
            row.className = 'flex justify-between items-center px-3 py-2 rounded-lg ' + (f.auto ? 'bg-emerald-50' : 'bg-slate-50');
            row.innerHTML = `
                <span class="text-[12.5px] font-medium text-slate-600 flex items-center gap-1.5">
                    ${f.auto ? '<span class="text-[10px] font-semibold text-teal-600 bg-teal-50 border border-teal-200 px-1.5 py-0.5 rounded">auto</span>' : ''}
                    ${f.label}
                </span>
                <span class="text-[13px] font-bold ${f.auto ? 'text-teal-700' : 'text-slate-800'}">${fmtVerified}</span>
            `;
        }

        container.appendChild(row);
    });

    // Show/hide edit badge and column headers
    const editBadge         = document.getElementById('editBadge');
    const comparisonHeader  = document.getElementById('comparisonHeader');
    const summaryOnlyHeader = document.getElementById('summaryOnlyHeader');

    if (editedCount > 0) {
        editBadge.classList.remove('hidden');
        editBadge.classList.add('flex');
        document.getElementById('editBadgeText').textContent = `${editedCount} field${editedCount > 1 ? 's' : ''} edited by statistician — highlighted below`;
        comparisonHeader.classList.remove('hidden');
        summaryOnlyHeader.classList.add('hidden');
    } else {
        editBadge.classList.add('hidden');
        editBadge.classList.remove('flex');
        comparisonHeader.classList.add('hidden');
        summaryOnlyHeader.classList.remove('hidden');
    }
};


// ─── DOM Ready ────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {

    window.currentPostButton = null;

    // Attach live formatting to all .formatted-number inputs (not readonly)
    document.querySelectorAll('input.formatted-number:not([readonly])').forEach(function (input) {
        input.addEventListener('input', function (e) {
            const raw = e.target.value;
            const cursorPos = e.target.selectionStart;
            const prevLen = raw.length;

            // Allow typing decimals: don't reformat mid-decimal entry
            if (raw.endsWith('.') || raw.endsWith('.0') || /\.\d*0$/.test(raw)) {
                const cleaned = raw.replace(/[^0-9.]/g, '');
                const cParts = cleaned.split('.');
                const intFormatted = cParts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                const newVal = cParts.length > 1 ? intFormatted + '.' + cParts[1] : intFormatted;
                e.target.value = newVal;
                return;
            }

            const formatted = formatWithCommas(raw);
            e.target.value = formatted;
            const diff = formatted.length - prevLen;
            try { e.target.setSelectionRange(cursorPos + diff, cursorPos + diff); } catch (ex) {}
        });

        // On blur: fully clean up (e.g. trailing dot)
        input.addEventListener('blur', function (e) {
            const numVal = parseFloat(e.target.value.replace(/,/g, ''));
            if (!isNaN(numVal)) {
                e.target.value = numVal.toLocaleString('en-US', { maximumFractionDigits: 4 });
            }
        });
    });

    // Initialize calculations on page load
    const card = document.querySelector('.pending-record-card');
    if (card) {
        calculateLaborForce(card);
    }

    // Handle input changes for auto-calculated fields
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('calc-trigger')) {
            const card  = e.target.closest('.pending-record-card');
            const field = e.target.getAttribute('data-field');

            if (field === 'household_population' || field === 'lfpr') {
                calculateLaborForce(card);
            } else if (field === 'employment_rate') {
                calculateEmployed(card);
            } else if (field === 'underemployment_rate') {
                calculateUnderemployed(card);
            } else if (field === 'unemployment_rate') {
                calculateUnemployed(card);
            }
        }
    });

    // Modal Controls
    document.getElementById('cancelBtn').addEventListener('click', function () {
        document.getElementById('confirmModal').classList.add('hidden');
        window.currentPostButton = null;
    });

    document.getElementById('closeModalBtn').addEventListener('click', function () {
        document.getElementById('successModal').classList.add('hidden');
        window.location.reload();
    });

    document.getElementById('closeErrorBtn').addEventListener('click', function () {
        document.getElementById('errorModal').classList.add('hidden');
    });

    // Confirm & submit
    document.getElementById('confirmBtn').addEventListener('click', function () {
        const buttonElement = window.currentPostButton;
        if (!buttonElement) return;

        const card      = buttonElement.closest('.pending-record-card');
        const pendingId = card.getAttribute('data-id');

        document.getElementById('confirmModal').classList.add('hidden');
        buttonElement.disabled = true;
        buttonElement.innerHTML = '<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Posting...';

        const verifiedData = {
            pending_id:                    pendingId,
            year:                          card.querySelector('[data-field="year"]').value,
            month:                         card.querySelector('[data-field="month"]').value,
            household_population:          parseFloat(card.querySelector('[data-field="household_population"]').value.replace(/,/g, '')),
            labor_force:                   parseFloat(card.querySelector('[data-field="labor_force"]').value.replace(/,/g, '')),
            employed:                      parseFloat(card.querySelector('[data-field="employed"]').value.replace(/,/g, '')),
            underemployed:                 parseFloat(card.querySelector('[data-field="underemployed"]').value.replace(/,/g, '')),
            unemployed:                    parseFloat(card.querySelector('[data-field="unemployed"]').value.replace(/,/g, '')),
            labor_force_participation_rate: Number(card.querySelector('[data-field="lfpr"]').value),
            employment_rate:               Number(card.querySelector('[data-field="employment_rate"]').value),
            underemployment_rate:          Number(card.querySelector('[data-field="underemployment_rate"]').value),
            unemployment_rate:             Number(card.querySelector('[data-field="unemployment_rate"]').value),
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(window._statisticianRoutes.post, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(verifiedData)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                document.getElementById('successModal').classList.remove('hidden');
            } else {
                throw new Error(result.message || 'Failed to post data');
            }
        })
        .catch(error => {
            document.getElementById('errorMessage').textContent = error.message;
            document.getElementById('errorModal').classList.remove('hidden');
            buttonElement.disabled = false;
            buttonElement.innerHTML = '<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Post to Database';
        })
        .finally(() => {
            window.currentPostButton = null;
        });
    });
});


// ─── Live Polling — detect new pending submissions every 30s ─────────────────

(function () {
    let knownPending    = parseInt(window._statisticianData?.pendingCount ?? 0);
    const POLL_INTERVAL = 30_000;
    let accumulatedNew  = 0;
    let notifToast      = null;

    function fetchCounts() {
        fetch(window._statisticianRoutes.pendingCount, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (!data) return;
            const newPending = parseInt(data.pending ?? 0);

            const badge = document.getElementById('pending-badge-count');
            if (badge) badge.textContent = newPending;

            if (newPending > knownPending) {
                accumulatedNew += (newPending - knownPending);
                showOrUpdateNotifToast();
            }
            knownPending = newPending;
        })
        .catch(() => {});
    }

    function showOrUpdateNotifToast() {
        const msgText   = `[!] ${accumulatedNew} new pending record${accumulatedNew > 1 ? 's' : ''} submitted — click to refresh`;
        const container = document.getElementById('toastContainer');

        if (notifToast && container.contains(notifToast)) {
            notifToast.querySelector('.notif-text').textContent = msgText;
            notifToast.classList.add('scale-105');
            setTimeout(() => notifToast.classList.remove('scale-105'), 200);
            return;
        }

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
                <button class="notif-dismiss text-blue-400 hover:text-blue-700 transition ml-1 flex-shrink-0" title="Dismiss">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;

        notifToast.addEventListener('click', function (e) {
            if (e.target.closest('.notif-dismiss')) return;
            dismissNotifToast();
            window.location.reload();
        });

        notifToast.querySelector('.notif-dismiss').addEventListener('click', function (e) {
            e.stopPropagation();
            dismissNotifToast();
        });

        container.appendChild(notifToast);
        requestAnimationFrame(() => requestAnimationFrame(() => {
            notifToast.classList.remove('translate-x-full', 'opacity-0');
        }));
    }

    function dismissNotifToast() {
        if (!notifToast) return;
        notifToast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            notifToast?.remove();
            notifToast      = null;
            accumulatedNew  = 0;
        }, 300);
    }

    setInterval(fetchCounts, POLL_INTERVAL);
})();