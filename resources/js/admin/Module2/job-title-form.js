// ─── State Variables ────────────────────────────────────────────────────────
let entryCount = 0;
let pendingData = null;
let currentTab = 'pending';

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

// ─── Number Input Formatting ──────────────────────────────────────────────────
function formatNumInput(el) {
    const raw = el.value.replace(/[^0-9]/g, '');
    el.value = raw === '' ? '' : parseInt(raw).toLocaleString();
}

function stripCommas(el) {
    el.value = el.value.replace(/,/g, '');
}

function refornatOnBlur(el) {
    const raw = parseInt(el.value.replace(/[^0-9]/g, ''));
    el.value = isNaN(raw) ? '' : raw.toLocaleString();
}

// ─── Job Entry Management ─────────────────────────────────────────────────────
function addJobEntry() {
    entryCount++;
    const jobEntries = document.getElementById('jobEntries');
    const currentNum = jobEntries.children.length + 1;

    const entryDiv = document.createElement('div');
    entryDiv.className = 'jt-entry';
    entryDiv.id = `entry-${entryCount}`;

    entryDiv.innerHTML = `
        <div class="jt-entry-num">${currentNum}</div>
        <div>
            <label class="jt-label">Job Title</label>
            <input
                type="text"
                name="jobTitle[]"
                placeholder="e.g. Customer Service Rep"
                required
                class="jt-input"
            >
        </div>
        <div>
            <label class="jt-label">Count</label>
            <input
                type="text"
                inputmode="numeric"
                name="jobCount[]"
                placeholder="e.g. 1,250"
                required
                class="jt-input num-input text-right"
                oninput="formatNumInput(this)"
                onfocus="stripCommas(this)"
                onblur="refornatOnBlur(this)"
            >
        </div>
    `;

    jobEntries.appendChild(entryDiv);
    jobEntries.scrollTop = jobEntries.scrollHeight;
}

// ─── Reset Modal ──────────────────────────────────────────────────────────────
function resetForm() {
    document.getElementById('resetModal').classList.remove('hidden');
}

function closeResetModal() {
    document.getElementById('resetModal').classList.add('hidden');
}

function confirmReset() {
    document.getElementById('resetModal').classList.add('hidden');
    document.getElementById('jobEntries').innerHTML = '';
    document.getElementById('year').value = '';
    entryCount = 0;
    for (let i = 0; i < 10; i++) addJobEntry();
}

// ─── Confirm Submit Modal ─────────────────────────────────────────────────────
function showConfirmModal(data) {
    pendingData = data;

    document.getElementById('confirmSummaryYear').textContent = data.year;

    const tbody = document.getElementById('confirmSummaryTableBody');
    tbody.innerHTML = '';
    let totalEmployment = 0;
    data.jobs.forEach((job, i) => {
        totalEmployment += Number(job.count) || 0;
        const row = document.createElement('tr');
        row.className = i % 2 === 0 ? 'bg-white' : 'bg-slate-50/60';
        row.innerHTML = `
            <td class="px-3 py-2 text-xs text-slate-400 font-semibold">${i + 1}</td>
            <td class="px-3 py-2 text-xs text-slate-700">${job.title}</td>
            <td class="px-3 py-2 text-xs font-bold text-blue-700 text-right bg-blue-50">${Number(job.count).toLocaleString()}</td>
        `;
        tbody.appendChild(row);
    });

    // Total Employment footer row
    const totalRow = document.createElement('tr');
    totalRow.className = 'border-t-2 border-slate-300 bg-white';
    totalRow.innerHTML = `
        <td class="px-3 py-2.5" colspan="2">
            <span class="text-xs font-bold text-slate-700">Total Employment</span>
        </td>
        <td class="px-3 py-2.5 text-right bg-blue-50">
            <span class="text-xs font-bold text-blue-700">${totalEmployment.toLocaleString()}</span>
        </td>
    `;
    tbody.appendChild(totalRow);

    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    pendingData = null;
}

async function confirmSubmit() {
    const dataToSubmit = pendingData;
    closeConfirmModal();

    try {
        const response = await fetch(window.AppRoutes.jobTitlesStore, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(dataToSubmit)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            showSuccessModal(dataToSubmit);
        } else {
            showToast('Error: ' + (result.message || 'An error occurred while saving the data.'), 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred while saving the data.', 'error');
    }
}

// ─── Success Modal ────────────────────────────────────────────────────────────
function showSuccessModal(data) {
    document.getElementById('summaryYear').textContent = data ? data.year : '';

    const tbody = document.getElementById('summaryTableBody');
    tbody.innerHTML = '';
    if (data && data.jobs) {
        data.jobs.forEach((job, i) => {
            const row = document.createElement('tr');
            row.className = i % 2 === 0 ? 'bg-white' : 'bg-slate-50/60';
            row.innerHTML = `
                <td class="px-3 py-2 text-xs text-slate-400 font-semibold">${i + 1}</td>
                <td class="px-3 py-2 text-xs text-slate-700">${job.title}</td>
                <td class="px-3 py-2 text-xs font-bold text-blue-700 text-right bg-blue-50">${job.count.toLocaleString()}</td>
            `;
            tbody.appendChild(row);
        });
    }
    document.getElementById('successJobTitleCount').textContent = data?.jobs?.length ?? 0;
    document.getElementById('successModal').classList.remove('hidden');
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    document.getElementById('jobEntries').innerHTML = '';
    document.getElementById('year').value = '';
    entryCount = 0;
    for (let i = 0; i < 10; i++) addJobEntry();
    switchTab('pending');
}

// ─── Year Duplicate Check on Blur ─────────────────────────────────────────────
async function checkYearOnBlur(input) {
    const year = parseInt(input.value);
    const errorEl = document.getElementById('yearError');

    if (!year || year < 2000) {
        input.style.borderColor = '';
        input.style.boxShadow = '';
        errorEl.classList.add('hidden');
        return;
    }

    try {
        const res = await fetch(window.AppRoutes.jobTitlesCheckYear + '?year=' + year, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await res.json();

        if (data.exists) {
            input.style.borderColor = '#ef4444';
            input.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
            errorEl.textContent = `Year ${year} already has a pending submission.`;
            errorEl.classList.remove('hidden');
        } else {
            input.style.borderColor = '#22c55e';
            input.style.boxShadow = '0 0 0 3px rgba(34,197,94,0.15)';
            errorEl.classList.add('hidden');
        }
    } catch (err) {
        input.style.borderColor = '';
        input.style.boxShadow = '';
        errorEl.classList.add('hidden');
    }
}

// ─── Tab Switcher ─────────────────────────────────────────────────────────────
function switchTab(tab) {
    currentTab = tab;

    const isSubmit = tab === 'submit';
    document.getElementById('panel-submit').classList.toggle('hidden', !isSubmit);
    document.getElementById('panel-history').classList.toggle('hidden', isSubmit);

    const submitBtn = document.getElementById('tab-submit');
    if (isSubmit) {
        submitBtn.className = 'header-tab flex items-center gap-2 px-4 py-1.5 rounded-full text-[13px] font-semibold border transition border-blue-200 bg-blue-50 text-blue-700';
        return;
    } else {
        submitBtn.className = 'header-tab flex items-center gap-2 px-4 py-1.5 rounded-full text-[13px] font-semibold border transition border-slate-200 bg-white text-slate-500 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700';
    }

    const tabConfig = {
        pending: {
            active:     'border-amber-200 bg-amber-50 text-amber-700',
            inactive:   'border-slate-200 bg-white text-slate-500 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-700',
            bar:        'bg-gradient-to-r from-amber-400 to-yellow-400',
            label:      'Pending Submissions',
            icon:       `<svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
            iconBg:     'bg-amber-50',
        },
        approved: {
            active:     'border-emerald-200 bg-emerald-50 text-emerald-700',
            inactive:   'border-slate-200 bg-white text-slate-500 hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700',
            bar:        'bg-gradient-to-r from-emerald-500 to-teal-400',
            label:      'Approved Submissions',
            icon:       `<svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
            iconBg:     'bg-emerald-50',
        },
        rejected: {
            active:     'border-red-200 bg-red-50 text-red-600',
            inactive:   'border-slate-200 bg-white text-slate-500 hover:border-red-200 hover:bg-red-50 hover:text-red-600',
            bar:        'bg-gradient-to-r from-red-500 to-rose-400',
            label:      'Rejected Submissions',
            icon:       `<svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
            iconBg:     'bg-red-50',
        },
    };

    const badgeClasses = {
        pending:  { active: 'bg-amber-500 text-white',   inactive: 'bg-amber-100 text-amber-700'    },
        approved: { active: 'bg-emerald-600 text-white', inactive: 'bg-emerald-100 text-emerald-700' },
        rejected: { active: 'bg-red-500 text-white',     inactive: 'bg-red-100 text-red-600'        },
    };

    ['pending', 'approved', 'rejected'].forEach(t => {
        const btn   = document.getElementById(`tab-${t}`);
        const badge = document.getElementById(`badge-${t}`);
        const cfg   = tabConfig[t];
        const bc    = badgeClasses[t];
        btn.className   = `header-tab flex items-center gap-2 px-4 py-1.5 rounded-full text-[13px] font-semibold border transition ${t === tab ? cfg.active : cfg.inactive}`;
        badge.className = `text-[11px] font-bold px-2 py-0.5 rounded-full min-w-[20px] text-center ${t === tab ? bc.active : bc.inactive}`;
    });

    const cfg = tabConfig[tab];
    document.getElementById('histAccentBar').className =
        `absolute top-0 left-0 right-0 h-[3px] transition-all duration-300 ${cfg.bar}`;
    document.getElementById('histPanelLabel').textContent = cfg.label;
    document.getElementById('histPanelIcon').innerHTML    = cfg.icon;
    document.getElementById('histPanelIcon').className    = `inline-flex items-center justify-center w-7 h-7 rounded-lg ${cfg.iconBg}`;

    loadHistory();
}

// ─── History Panel ────────────────────────────────────────────────────────────
async function loadHistory() {
    const year    = document.getElementById('histYearFilter').value;
    const loader  = document.getElementById('histLoader');
    const content = document.getElementById('histGroupedContent');

    loader.classList.remove('hidden');
    content.innerHTML = `<div class="text-center py-12 text-slate-400 text-sm">Loading…</div>`;

    try {
        const params = new URLSearchParams({ status: currentTab });
        if (year) params.append('year', year);

        const res = await fetch(`${window.AppRoutes.jobTitlesHistory}?${params}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await res.json();

        // Update header badges
        document.getElementById('badge-pending').textContent  = data.counts?.pending  ?? 0;
        document.getElementById('badge-approved').textContent = data.counts?.approved ?? 0;
        document.getElementById('badge-rejected').textContent = data.counts?.rejected ?? 0;

        // Repopulate year filter
        const yearSelect = document.getElementById('histYearFilter');
        const selectedYear = yearSelect.value;
        yearSelect.innerHTML = '<option value="">All Years</option>';
        (data.years ?? []).forEach(y => {
            const opt = document.createElement('option');
            opt.value = y;
            opt.textContent = y;
            if (String(y) === selectedYear) opt.selected = true;
            yearSelect.appendChild(opt);
        });

        const records = data.records ?? [];

        if (records.length === 0) {
            content.innerHTML = `
                <div class="text-center py-14 text-slate-400 text-sm">
                    <svg class="w-9 h-9 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    No ${currentTab} records${year ? ' for ' + year : ''}.
                </div>`;
            document.getElementById('histFooter').textContent = '';
            return;
        }

        // Group by year
        const grouped = {};
        records.forEach(r => {
            const y = r.year ?? 'Unknown';
            if (!grouped[y]) grouped[y] = [];
            grouped[y].push(r);
        });

        const colourMap = {
            pending:  { panelBg: 'bg-amber-50 hover:bg-amber-100/70',    yearText: 'text-amber-800',   countBadge: 'bg-amber-100 text-amber-700 border-amber-200',    chevron: 'text-amber-600'  },
            approved: { panelBg: 'bg-emerald-50 hover:bg-emerald-100/70', yearText: 'text-emerald-800', countBadge: 'bg-emerald-100 text-emerald-700 border-emerald-200', chevron: 'text-emerald-600' },
            rejected: { panelBg: 'bg-red-50 hover:bg-red-100/70',         yearText: 'text-red-800',     countBadge: 'bg-red-100 text-red-600 border-red-200',           chevron: 'text-red-500'    },
        };
        const cc = colourMap[currentTab] || colourMap.approved;

        content.innerHTML = '';
        const years = Object.keys(grouped).sort((a, b) => b - a);

        years.forEach(yr => {
            const rows = grouped[yr];
            const dateField  = currentTab === 'pending' ? (rows[0]?.created_at ?? rows[0]?.submitted_at) : rows[0]?.reviewed_at;
            const dateLabel  = currentTab === 'pending' ? 'Submitted' : 'Reviewed';
            const displayDate = dateField
                ? new Date(dateField).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' })
                : '—';

            const section = document.createElement('div');
            section.className = 'mb-3 rounded-xl border border-slate-200 overflow-hidden';

            const headerEl = document.createElement('div');
            headerEl.className = `flex items-center justify-between px-4 py-3 cursor-pointer select-none transition ${cc.panelBg}`;
            headerEl.innerHTML = `
                <div class="flex items-center gap-3">
                    <span class="text-[13px] font-bold ${cc.yearText}">Year ${yr}</span>
                    <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full border ${cc.countBadge}">
                        ${rows.length} job title${rows.length !== 1 ? 's' : ''}
                    </span>
                    <span class="text-[11px] text-slate-500">${dateLabel}: ${displayDate}</span>
                </div>
                <svg class="chevron-icon w-4 h-4 transition-transform ${cc.chevron}"
                     fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>`;

            const tableWrap = document.createElement('div');
            tableWrap.className = 'year-group-body';

            let rowsHtml = '';
            rows.forEach((row, i) => {
                const isApproved = currentTab === 'approved';
                rowsHtml += `
                    <tr class="${i % 2 === 0 ? 'bg-white' : 'bg-slate-50/60'} hover:bg-blue-50/30 transition">
                        <td class="px-4 py-2.5 text-xs text-slate-400 font-semibold">${i + 1}</td>
                        <td class="px-4 py-2.5 text-[13px] text-slate-800 font-medium">${row.title}</td>
                        <td class="px-4 py-2.5 text-right bg-blue-50/50">
                            <span class="text-[13px] font-bold text-blue-700">${Number(row.count).toLocaleString()}</span>
                        </td>
                        ${isApproved ? `<td class="px-4 py-2.5 text-right">
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>
                                Reviewed
                            </span>
                        </td>` : ''}
                    </tr>`;
            });

            tableWrap.innerHTML = `
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-100 border-t border-slate-200">
                            <th class="text-left px-4 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-wide w-10">#</th>
                            <th class="text-left px-4 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Job Title</th>
                            <th class="text-right px-4 py-2.5 text-[11px] font-semibold text-blue-700 uppercase tracking-wide bg-blue-50 w-28">Count</th>
                            ${currentTab === 'approved' ? '<th class="text-right px-4 py-2.5 text-[11px] font-semibold text-emerald-600 uppercase tracking-wide w-28">Status</th>' : ''}
                        </tr>
                    </thead>
                    <tbody>${rowsHtml}</tbody>
                </table>`;

            headerEl.addEventListener('click', () => {
                const isOpen = !tableWrap.classList.contains('hidden');
                tableWrap.classList.toggle('hidden', isOpen);
                headerEl.querySelector('.chevron-icon').style.transform = isOpen ? 'rotate(-90deg)' : '';
            });

            section.appendChild(headerEl);
            section.appendChild(tableWrap);
            content.appendChild(section);
        });

        document.getElementById('histFooter').textContent =
            `Showing ${records.length} record${records.length !== 1 ? 's' : ''} across ${years.length} year${years.length !== 1 ? 's' : ''}${year ? ' (filtered: ' + year + ')' : ''}`;

    } catch (err) {
        console.error(err);
        content.innerHTML = `<div class="text-center py-10 text-red-400 text-sm">Failed to load records.</div>`;
    } finally {
        loader.classList.add('hidden');
    }
}

// ─── DOMContentLoaded Init ────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // Add initial 10 job entries
    for (let i = 0; i < 10; i++) addJobEntry();

    // Add job entry button
    const addBtn = document.getElementById('addJobEntryBtn');
    if (addBtn) addBtn.addEventListener('click', addJobEntry);

    // Default to submit tab, pre-fetch badge counts
    switchTab('submit');
    fetch(`${window.AppRoutes.jobTitlesHistory}?status=pending`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('badge-pending').textContent  = data.counts?.pending  ?? 0;
        document.getElementById('badge-approved').textContent = data.counts?.approved ?? 0;
        document.getElementById('badge-rejected').textContent = data.counts?.rejected ?? 0;
    })
    .catch(() => {});

    // Form submit handler
    document.getElementById('jobTitlesForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const year = document.getElementById('year').value.trim();

        if (!year || parseInt(year) < 2000 || parseInt(year) > 2100) {
            showToast('Please enter a valid year (2000–2100).', 'warning');
            document.getElementById('year').focus();
            return;
        }

        const titles = document.querySelectorAll('input[name="jobTitle[]"]');
        const counts = document.querySelectorAll('input[name="jobCount[]"]');

        let hasAtLeastOne = false;
        let hasIncomplete = false;

        for (let i = 0; i < titles.length; i++) {
            const titleVal = titles[i].value.trim();
            const countVal = counts[i].value.replace(/,/g, '').trim();
            const bothEmpty  = !titleVal && !countVal;
            const onlyTitle  = titleVal && !countVal;
            const onlyCount  = !titleVal && countVal;

            if (bothEmpty) continue;

            if (onlyTitle || onlyCount) {
                hasIncomplete = true;
                if (onlyTitle) {
                    counts[i].style.borderColor = '#ef4444';
                    counts[i].style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
                } else {
                    titles[i].style.borderColor = '#ef4444';
                    titles[i].style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
                }
            } else {
                hasAtLeastOne = true;
                titles[i].style.borderColor = '';
                titles[i].style.boxShadow = '';
                counts[i].style.borderColor = '';
                counts[i].style.boxShadow = '';
            }
        }

        if (hasIncomplete) {
            showToast('Some rows are incomplete. Please fill in both Job Title and Count, or leave the row fully empty.', 'warning');
            return;
        }

        if (!hasAtLeastOne) {
            showToast('Please fill in at least one job title entry.', 'warning');
            return;
        }

        // Build job data from filled rows only
        const jobData = [];
        for (let i = 0; i < titles.length; i++) {
            const titleVal = titles[i].value.trim();
            const countVal = counts[i].value.replace(/,/g, '').trim();
            if (titleVal && countVal) {
                jobData.push({ title: titleVal, count: parseInt(countVal) });
            }
        }

        // Check for existing pending submission for this year
        try {
            const checkRes = await fetch(window.AppRoutes.jobTitlesCheckYear + '?year=' + parseInt(year), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const checkData = await checkRes.json();

            if (checkData.exists) {
                showToast(`Year ${year} already has a pending submission. Please wait for it to be reviewed before submitting again.`, 'warning');
                const yearInput = document.getElementById('year');
                yearInput.style.borderColor = '#ef4444';
                yearInput.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
                const errorEl = document.getElementById('yearError');
                errorEl.textContent = `Year ${year} already has a pending submission.`;
                errorEl.classList.remove('hidden');
                return;
            }
        } catch (err) {
            console.warn('Year check failed:', err);
        }

        showConfirmModal({ year: parseInt(year), jobs: jobData });
    });
});

// ─── Global Exports (required for Blade onclick handlers) ────────────────────
window.switchTab           = switchTab;
window.loadHistory         = loadHistory;
window.addJobEntry         = addJobEntry;
window.resetForm           = resetForm;
window.closeResetModal     = closeResetModal;
window.confirmReset        = confirmReset;
window.closeConfirmModal   = closeConfirmModal;
window.confirmSubmit       = confirmSubmit;
window.closeSuccessModal   = closeSuccessModal;
window.checkYearOnBlur     = checkYearOnBlur;
window.formatNumInput      = formatNumInput;
window.stripCommas         = stripCommas;
window.refornatOnBlur      = refornatOnBlur;