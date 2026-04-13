// ─── State Variables ──────────────────────────────────────────────────────────
let selectedYear = null;
const edits = {};
const editModeActive = {};
const snapshots = {};

// ─── Toast Notification System ────────────────────────────────────────────────
function showToast(message, type = 'error') {
    const container = document.getElementById('toastContainer');

    const configs = {
        error: {
            bg:   'bg-red-50 border-red-400',
            icon: `<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                   </svg>`,
            text: 'text-red-800',
            bar:  'bg-red-400',
        },
        warning: {
            bg:   'bg-amber-50 border-amber-400',
            icon: `<svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                   </svg>`,
            text: 'text-amber-800',
            bar:  'bg-amber-400',
        },
        success: {
            bg:   'bg-green-50 border-green-400',
            icon: `<svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                   </svg>`,
            text: 'text-green-800',
            bar:  'bg-green-400',
        },
        info: {
            bg:   'bg-blue-50 border-blue-400',
            icon: `<svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                   </svg>`,
            text: 'text-blue-800',
            bar:  'bg-blue-400',
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

// Inject toast animation CSS once
if (!document.getElementById('toastStyle')) {
    const style = document.createElement('style');
    style.id = 'toastStyle';
    style.textContent = `@keyframes shrink { from { width: 100%; } to { width: 0%; } }`;
    document.head.appendChild(style);
}

// ─── Toggle Edit Mode ─────────────────────────────────────────────────────────
function toggleEditMode(year) {
    const card      = document.getElementById(`card-${year}`);
    const btn       = document.getElementById(`editBtn-${year}`);
    const btnText   = document.getElementById(`editBtnText-${year}`);
    const cancelBtn = document.getElementById(`cancelEditBtn-${year}`);

    if (editModeActive[year]) {
        // Turning OFF — remove any unsaved new rows
        const pendingNewRows = card.querySelectorAll('[id^="row-new-"]');
        pendingNewRows.forEach(row => { if (row.parentNode) row.remove(); });
        delete snapshots[year];
    } else {
        // Turning ON — take a snapshot of current state
        snapshots[year] = {};
        card.querySelectorAll('.editable-row').forEach(row => {
            const id = row.id.replace('row-', '');
            const titleEl = document.getElementById(`title-text-${id}`);
            const countEl = document.getElementById(`count-text-${id}`);
            if (titleEl && countEl) {
                snapshots[year][id] = {
                    title: titleEl.textContent.trim(),
                    count: countEl.textContent.trim()
                };
            }
        });
    }

    editModeActive[year] = !editModeActive[year];

    const rows = card.querySelectorAll('.editable-row');
    rows.forEach(row => row.classList.toggle('edit-mode', editModeActive[year]));

    btn.classList.toggle('active', editModeActive[year]);
    btnText.textContent     = editModeActive[year] ? 'Done' : 'Edit';
    cancelBtn.style.display = editModeActive[year] ? 'inline-flex' : 'none';
}

// ─── Cancel Edit Mode — revert all changes ────────────────────────────────────
function cancelEditMode(year) {
    const card      = document.getElementById(`card-${year}`);
    const btn       = document.getElementById(`editBtn-${year}`);
    const btnText   = document.getElementById(`editBtnText-${year}`);
    const cancelBtn = document.getElementById(`cancelEditBtn-${year}`);
    const tbody     = document.getElementById(`tbody-${year}`);

    const snap = snapshots[year] || {};

    // Remove any newly added rows
    tbody.querySelectorAll('[id^="row-new-"]').forEach(r => r.remove());

    // Restore deleted rows and revert edits using snapshot
    card.querySelectorAll('.editable-row').forEach(row => {
        const id = row.id.replace('row-', '');
        if (snap[id]) {
            const titleEl = document.getElementById(`title-text-${id}`);
            if (titleEl) titleEl.textContent = snap[id].title;
            const titleInput = document.getElementById(`title-input-${id}`);
            if (titleInput) titleInput.value = snap[id].title;
            const titleBadge = document.getElementById(`title-badge-${id}`);
            if (titleBadge) titleBadge.style.display = 'none';

            const countEl = document.getElementById(`count-text-${id}`);
            if (countEl) countEl.textContent = snap[id].count;
            const countInput = document.getElementById(`count-input-${id}`);
            if (countInput) countInput.value = snap[id].count.replace(/,/g, '');
            const countBadge = document.getElementById(`count-badge-${id}`);
            if (countBadge) countBadge.style.display = 'none';

            // Close any open inline editors
            const titleDisplay = document.getElementById(`title-display-${id}`);
            const titleEdit    = document.getElementById(`title-edit-${id}`);
            if (titleDisplay) titleDisplay.style.display = '';
            if (titleEdit)    titleEdit.style.display    = 'none';
            const countDisplay = document.getElementById(`count-display-${id}`);
            const countEdit    = document.getElementById(`count-edit-${id}`);
            if (countDisplay) countDisplay.style.display = '';
            if (countEdit)    countEdit.style.display    = 'none';
        }
        row.classList.remove('edit-mode');
    });

    // Clear edits tracking
    Object.keys(edits).forEach(k => delete edits[k]);

    // Hide the edited banner
    document.getElementById(`banner-${year}`).classList.remove('visible');

    // Update total count
    updateTotalCount(year);

    // Reset button state
    editModeActive[year]    = false;
    btn.classList.remove('active');
    btnText.textContent     = 'Edit';
    cancelBtn.style.display = 'none';

    delete snapshots[year];
}

// ─── Update Total Count ───────────────────────────────────────────────────────
function updateTotalCount(year) {
    const tbody = document.getElementById(`tbody-${year}`);
    const count = tbody ? tbody.querySelectorAll('.editable-row').length : 0;
    const el    = document.getElementById(`totalCount-${year}`);
    if (el) el.textContent = `${count} job${count !== 1 ? 's' : ''}`;
}

// ─── Inline Edit ──────────────────────────────────────────────────────────────
function startEdit(jobId, field) {
    const row = document.getElementById(`row-${jobId}`);
    if (!row.classList.contains('edit-mode')) return;

    const display = document.getElementById(`${field}-display-${jobId}`);
    const editBox = document.getElementById(`${field}-edit-${jobId}`);
    const input   = document.getElementById(`${field}-input-${jobId}`);

    display.style.display = 'none';
    editBox.style.display = 'flex';
    input.classList.remove('error');
    input.focus();
    input.select();
}

function cancelEdit(jobId, field) {
    const display = document.getElementById(`${field}-display-${jobId}`);
    const editBox = document.getElementById(`${field}-edit-${jobId}`);
    const input   = document.getElementById(`${field}-input-${jobId}`);
    const textEl  = document.getElementById(`${field}-text-${jobId}`);

    input.value = textEl.textContent.trim();
    input.classList.remove('error');
    display.style.display = '';
    editBox.style.display = 'none';
}

function saveEdit(jobId, field) {
    const display = document.getElementById(`${field}-display-${jobId}`);
    const editBox = document.getElementById(`${field}-edit-${jobId}`);
    const input   = document.getElementById(`${field}-input-${jobId}`);
    const textEl  = document.getElementById(`${field}-text-${jobId}`);
    const badge   = document.getElementById(`${field}-badge-${jobId}`);

    let newValue = input.value.trim().replace(/,/g, '');

    if (field === 'title' && newValue === '') {
        input.classList.add('error');
        input.focus();
        return;
    }
    if (field === 'count') {
        if (newValue === '' || isNaN(newValue) || parseInt(newValue) < 0) {
            input.classList.add('error');
            input.focus();
            return;
        }
        newValue = parseInt(newValue).toString();
    }

    input.classList.remove('error');

    textEl.textContent = (field === 'count') ? Number(newValue).toLocaleString() : newValue;

    if (!edits[jobId]) edits[jobId] = {};
    edits[jobId][field] = (field === 'count') ? parseInt(newValue) : newValue;

    badge.style.display = 'inline-block';

    // Show year banner
    const row  = document.getElementById(`row-${jobId}`);
    const card = row.closest('[id^="card-"]');
    const year = card.id.replace('card-', '');
    document.getElementById(`banner-${year}`).classList.add('visible');

    // Close editor
    display.style.display = '';
    editBox.style.display = 'none';
}

function handleKeydown(event, jobId, field) {
    if (event.key === 'Enter')  { event.preventDefault(); saveEdit(jobId, field); }
    if (event.key === 'Escape') { cancelEdit(jobId, field); }
}

// ─── Approve Modals ───────────────────────────────────────────────────────────
function openApproveSummaryModal(year) {
    selectedYear = year;

    const card = document.getElementById(`card-${selectedYear}`);

    document.getElementById('summaryModalYear').textContent = selectedYear;

    const submitterEl   = card.querySelector('p.text-base.opacity-90');
    const submitterText = submitterEl
        ? submitterEl.textContent.replace('Submitted by:', '').trim()
        : 'Unknown';
    document.getElementById('summaryModalSubmitter').textContent = submitterText;

    const tbody  = document.getElementById('summaryModalTableBody');
    tbody.innerHTML = '';
    let total    = 0;
    const jobData = [];

    card.querySelectorAll('.editable-row').forEach(row => {
        const titleEl = row.querySelector('[id^="title-text-"]');
        const countEl = row.querySelector('[id^="count-text-"]');
        if (titleEl && countEl) {
            const title = titleEl.textContent.trim();
            const count = parseInt(countEl.textContent.replace(/,/g, '')) || 0;
            jobData.push({ title, count });
        }
    });

    jobData.sort((a, b) => b.count - a.count);

    jobData.forEach((job, i) => {
        total += job.count;
        const tr = document.createElement('tr');
        tr.className = i % 2 === 0 ? 'bg-white' : 'bg-slate-50/60';
        tr.innerHTML = `
            <td class="px-3 py-2 text-xs text-slate-400 font-semibold">${i + 1}</td>
            <td class="px-3 py-2 text-xs text-slate-700">${job.title}</td>
            <td class="px-3 py-2 text-xs font-bold text-green-700 text-right bg-green-50">${job.count.toLocaleString()}</td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('summaryModalCount').textContent = jobData.length;
    document.getElementById('approveSummaryModal').classList.remove('hidden');
}

function closeApproveSummaryModal() {
    document.getElementById('approveSummaryModal').classList.add('hidden');
}

async function confirmApprove() {
    closeApproveSummaryModal();

    try {
        const response = await fetch(`/statistician/job-titles/${selectedYear}/approve`, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ edits: edits })
        });
        const result = await response.json();

        if (result.success) {
            document.getElementById('approveSuccessModal').classList.remove('hidden');
        } else {
            document.getElementById('errorMessage').textContent = result.message || 'Failed to approve job titles';
            document.getElementById('errorModal').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('errorMessage').textContent = 'An error occurred while approving.';
        document.getElementById('errorModal').classList.remove('hidden');
    }
}

function closeApproveSuccessModal() {
    document.getElementById('approveSuccessModal').classList.add('hidden');
    selectedYear = null;
    location.reload();
}

// ─── Reject Modals ────────────────────────────────────────────────────────────
function showRejectModal(year) {
    selectedYear = year;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    selectedYear = null;
    document.getElementById('rejectModal').classList.add('hidden');
}

async function confirmReject() {
    const yearToReject = selectedYear;
    closeRejectModal();

    try {
        const response = await fetch(`/statistician/job-titles/${yearToReject}/reject`, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({})
        });
        const result = await response.json();

        if (result.success) {
            document.getElementById('rejectSuccessModal').classList.remove('hidden');
        } else {
            document.getElementById('errorMessage').textContent = result.message || 'Failed to reject submission';
            document.getElementById('errorModal').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('errorMessage').textContent = 'An error occurred while rejecting.';
        document.getElementById('errorModal').classList.remove('hidden');
    }
}

function closeRejectSuccessModal() {
    document.getElementById('rejectSuccessModal').classList.add('hidden');
    location.reload();
}

// ─── Error Modal ──────────────────────────────────────────────────────────────
function closeErrorModal() {
    document.getElementById('errorModal').classList.add('hidden');
}

// ─── Comma Formatting for Count Inputs ───────────────────────────────────────
function formatCountInput(el) {
    const raw = el.value.replace(/[^0-9]/g, '');
    el.value  = raw === '' ? '' : parseInt(raw).toLocaleString();
}

function stripCommas(el) {
    el.value = el.value.replace(/,/g, '');
}

function reformatOnBlur(el) {
    const raw = parseInt(el.value.replace(/[^0-9]/g, ''));
    el.value  = isNaN(raw) ? '' : raw.toLocaleString();
}

// ─── Live Polling — detect new submissions every 30s ─────────────────────────
(function () {
    // Blade data passed via window.AppData (set inline in the blade)
    const appData    = window.AppData ?? {};
    let knownPending = parseInt(appData.submissionsCount ?? 0);
    const pollUrl    = appData.pendingCountUrl ?? '';

    const POLL_INTERVAL = 30_000;
    let accumulatedNew  = 0;
    let notifToast      = null;

    function fetchCounts() {
        if (!pollUrl) return;
        fetch(pollUrl, {
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
        const msgText   = `[!] ${accumulatedNew} new job title submission${accumulatedNew > 1 ? 's' : ''} — click to refresh`;
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


// ─── Global Exports (required because Vite wraps modules in a private scope) ──
window.showToast                 = showToast;
window.toggleEditMode            = toggleEditMode;
window.cancelEditMode            = cancelEditMode;
window.updateTotalCount          = updateTotalCount;
window.startEdit                 = startEdit;
window.cancelEdit                = cancelEdit;
window.saveEdit                  = saveEdit;
window.handleKeydown             = handleKeydown;
window.openApproveSummaryModal   = openApproveSummaryModal;
window.closeApproveSummaryModal  = closeApproveSummaryModal;
window.confirmApprove            = confirmApprove;
window.closeApproveSuccessModal  = closeApproveSuccessModal;
window.showRejectModal           = showRejectModal;
window.closeRejectModal          = closeRejectModal;
window.confirmReject             = confirmReject;
window.closeRejectSuccessModal   = closeRejectSuccessModal;
window.closeErrorModal           = closeErrorModal;
window.formatCountInput          = formatCountInput;
window.stripCommas               = stripCommas;
window.reformatOnBlur            = reformatOnBlur;