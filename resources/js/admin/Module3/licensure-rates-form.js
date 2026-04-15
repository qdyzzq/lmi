// ─── State Variables ────────────────────────────────────────────────────────
let oldYear = null;
let pendingYearData = null;
let pendingData = null;
let originalData = {};

// ─── Sector Data Configuration ────────────────────────────────────────────────
const sectorsData = [
    {
        name: "Engineering, Architecture & Technical",
        icon: "<svg class=\"w-6 h-6 text-slate-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z\"/><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 12a3 3 0 11-6 0 3 3 0 016 0z\"/></svg>",
        professions: [
            "Aeronautical Engineers", "Agri-Bio Engineering", "Architect", "Chemical Engineer",
            "Civil Engineer", "Electronics Engineer", "Electronics Technician", "Geodetic Engineer",
            "Mechanical Engineer", "Metallurgical Engineer", "Mining Engineer",
            "Registered Electrical Engr.", "Registered Master Electrician",
            "Certified Plant Mechanic", "Master Plumber"
        ]
    },
    {
        name: "Healthcare & Nursing",
        icon: "<svg class=\"w-6 h-6 text-red-500\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4\"/></svg>",
        professions: [
            "Physician", "Nurse", "Midwife", "Dentist (Written)", "Medical Technologist",
            "Radiologic Technology", "X-Ray Technologist", "Pharmacist",
            "Nutritionist Dietitian", "Veterinary Medicine", "Occupational Therapist",
            "Physical Therapist", "Respiratory Therapist", "Speech Language Pathologist"
        ]
    },
    {
        name: "Natural Sciences",
        icon: "<svg class=\"w-6 h-6 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z\"/></svg>",
        professions: [
            "Environmental Planner", "Agriculturist", "Chemist", "Chemical Technician",
            "Fisheries Professionals", "Food Technologist", "Forester"
        ]
    },
    {
        name: "Education",
        icon: "<svg class=\"w-6 h-6 text-blue-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z\"/></svg>",
        professions: [
            "Professional Teachers (Elementary)",
            "Professional Teachers (Secondary)",
            "Professional Teachers (General)"
        ]
    },
    {
        name: "Social Work & Behavioral Sciences",
        icon: "<svg class=\"w-6 h-6 text-purple-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z\"/></svg>",
        professions: [
            "Social Worker", "Guidance Counselor", "Psychologist", "Psychometrician", "Librarian"
        ]
    },
    {
        name: "Real Estate Industry",
        icon: "<svg class=\"w-6 h-6 text-amber-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16M3 21h18M9 21V9m6 12V9m-3-6v3\"/></svg>",
        professions: ["Real Estate Appraiser", "Real Estate Broker"]
    },
    {
        name: "Defense Industry",
        icon: "<svg class=\"w-6 h-6 text-slate-700\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z\"/></svg>",
        professions: ["Criminologist"]
    },
    {
        name: "Business, Finance & Logistics",
        icon: "<svg class=\"w-6 h-6 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z\"/></svg>",
        professions: ["Certified Public Accountant (CPA)", "Custom Broker"]
    }
];

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

// ─── Form Lock / Unlock ───────────────────────────────────────────────────────
function lockForm() {
    document.getElementById('formContent').classList.add('opacity-50', 'pointer-events-none', 'select-none');
    document.getElementById('formBlocker').style.display = '';
    document.getElementById('lockBanner').classList.remove('hidden');
}

function unlockForm() {
    document.getElementById('formContent').classList.remove('opacity-50', 'pointer-events-none', 'select-none');
    document.getElementById('formBlocker').style.display = 'none';
    document.getElementById('lockBanner').classList.add('hidden');
}

// ─── Sector Generation ────────────────────────────────────────────────────────
function generateSectors() {
    const container = document.getElementById('sectorsContainer');

    sectorsData.forEach((sector, sectorIndex) => {
        const sectorCard = document.createElement('div');
        sectorCard.className = 'bg-white rounded-2xl shadow-lg overflow-hidden border-2 border-transparent transition-all duration-300 hover:shadow-xl hover:-translate-y-1';
        sectorCard.id = `sector-${sectorIndex}`;
        sectorCard.dataset.sectorName = sector.name.toLowerCase();

        let professionsHTML = '';
        sector.professions.forEach((profession, profIndex) => {
            professionsHTML += `
                <div class="prof-row grid grid-cols-[2fr_1fr_1fr_1fr] gap-4 px-6 py-4 items-center border-b border-gray-100 hover:bg-blue-50 transition-colors" data-profession="${profession.toLowerCase()}">
                    <div class="font-medium text-gray-700 text-sm prof-label">${profession}</div>
                    <div class="relative">
                        <input type="text" inputmode="numeric"
                            name="takers_${sectorIndex}_${profIndex}"
                            class="num-input w-full px-3 py-2 border-2 border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-right"
                            placeholder="0"
                            data-sector="${sectorIndex}" data-prof="${profIndex}" data-type="takers">
                    </div>
                    <div class="relative">
                        <input type="text" inputmode="numeric"
                            name="passers_${sectorIndex}_${profIndex}"
                            class="num-input w-full px-3 py-2 border-2 border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-right"
                            placeholder="0"
                            data-sector="${sectorIndex}" data-prof="${profIndex}" data-type="passers">
                    </div>
                    <div class="relative">
                        <input type="text"
                            name="rate_${sectorIndex}_${profIndex}"
                            class="w-full px-3 py-2 border-2 border-blue-200 bg-blue-50 rounded-lg text-sm font-semibold text-blue-700 text-center"
                            placeholder="0.00%"
                            readonly
                            data-sector="${sectorIndex}" data-prof="${profIndex}" data-type="rate">
                    </div>
                </div>
            `;
        });

        sectorCard.innerHTML = `
            <div id="header-${sectorIndex}" class="p-6 cursor-pointer flex justify-between items-center bg-gradient-to-r from-gray-50 to-white hover:from-gray-100 hover:to-gray-50 transition-all" onclick="toggleSector(${sectorIndex})">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-white shadow-md">${sector.icon}</div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">${sector.name}</h3>
                        <p class="text-sm text-gray-500 mt-1">${sector.professions.length} professions</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800" id="badge-${sectorIndex}">0/${sector.professions.length}</span>
                    <svg id="chevron-${sectorIndex}" class="chevron w-6 h-6 text-gray-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
            <div id="content-${sectorIndex}" class="sector-content">
                <div class="grid grid-cols-[2fr_1fr_1fr_1fr] gap-4 px-6 py-4 items-center bg-blue-50 font-semibold border-b-2 border-blue-200">
                    <div class="text-gray-700">Profession</div>
                    <div class="text-center text-gray-700">Takers</div>
                    <div class="text-center text-gray-700">Passers</div>
                    <div class="text-center text-gray-700">Passing Rate %</div>
                </div>
                ${professionsHTML}
            </div>
        `;

        container.appendChild(sectorCard);
    });
}

function toggleSector(index) {
    const sector = document.getElementById(`sector-${index}`);
    const content = document.getElementById(`content-${index}`);
    const chevron = document.getElementById(`chevron-${index}`);
    const isExpanded = sector.classList.contains('expanded');

    if (isExpanded) {
        sector.classList.remove('expanded', 'border-blue-500');
        content.style.maxHeight = '0px';
        chevron.style.transform = 'rotate(0deg)';
    } else {
        sector.classList.add('expanded', 'border-blue-500');
        content.style.maxHeight = '3000px';
        chevron.style.transform = 'rotate(180deg)';
    }
}

// ─── Number Input Formatting ──────────────────────────────────────────────────
function initNumInputs() {
    document.querySelectorAll('input.num-input').forEach(input => {
        input.addEventListener('input', function () {
            const raw = this.value.replace(/[^0-9]/g, '');
            this.value = raw === '' ? '' : parseInt(raw).toLocaleString();
            const s = this.dataset.sector;
            const p = this.dataset.prof;
            if (s !== undefined && p !== undefined) calculateRate(parseInt(s), parseInt(p));
        });
        input.addEventListener('focus', function () {
            const raw = this.value.replace(/,/g, '');
            this.value = raw === '0' ? '' : raw;
        });
        input.addEventListener('blur', function () {
            const raw = parseInt(this.value.replace(/[^0-9]/g, ''));
            this.value = isNaN(raw) ? '' : raw.toLocaleString();
            const s = this.dataset.sector;
            const p = this.dataset.prof;
            if (s !== undefined && p !== undefined) calculateRate(parseInt(s), parseInt(p));
        });
    });
}

// ─── Rate Calculation & Progress ─────────────────────────────────────────────
function calculateRate(sectorIndex, profIndex) {
    const takersInput  = document.querySelector(`[name="takers_${sectorIndex}_${profIndex}"]`);
    const passersInput = document.querySelector(`[name="passers_${sectorIndex}_${profIndex}"]`);
    const rateInput    = document.querySelector(`[name="rate_${sectorIndex}_${profIndex}"]`);

    const takers  = parseFloat(takersInput.value.replace(/,/g, '')) || 0;
    const passers = parseFloat(passersInput.value.replace(/,/g, '')) || 0;

    if (takers > 0 && passers > 0) {
        rateInput.value = ((passers / takers) * 100).toFixed(2) + '%';
    } else {
        rateInput.value = '';
    }

    updateProgress();
}

function updateProgress() {
    let totalRows = 0;
    let completeRows = 0;

    sectorsData.forEach((sector, sectorIndex) => {
        let sectorCompleteRows = 0;
        const sectorTotalRows = sector.professions.length;
        totalRows += sectorTotalRows;

        sector.professions.forEach((profession, profIndex) => {
            const takersInput  = document.querySelector(`[name="takers_${sectorIndex}_${profIndex}"]`);
            const passersInput = document.querySelector(`[name="passers_${sectorIndex}_${profIndex}"]`);
            const rateInput    = document.querySelector(`[name="rate_${sectorIndex}_${profIndex}"]`);

            const hasTakers  = takersInput?.value?.trim() !== '';
            const hasPassers = passersInput?.value?.trim() !== '';
            const hasRate    = rateInput?.value?.trim() !== '';

            if (hasTakers && hasPassers && hasRate) {
                sectorCompleteRows++;
                completeRows++;
            }
        });

        const badge = document.getElementById(`badge-${sectorIndex}`);
        if (badge) {
            badge.textContent = `${sectorCompleteRows}/${sectorTotalRows}`;
            badge.className = sectorCompleteRows === sectorTotalRows
                ? 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800'
                : 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800';
        }
    });

    const progressText  = `${completeRows}/${totalRows}`;
    const progressClass = completeRows === totalRows
        ? 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800'
        : 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800';

    const overallProgress = document.getElementById('overallProgress');
    const overallProgressDisplay = document.getElementById('overallProgressDisplay');
    if (overallProgress) { overallProgress.textContent = progressText; overallProgress.className = progressClass; }
    if (overallProgressDisplay) { overallProgressDisplay.textContent = progressText; overallProgressDisplay.className = progressClass; }
}

// ─── Search / Filter ──────────────────────────────────────────────────────────
function filterProfessions(query) {
    const q = query.toLowerCase().trim();
    const clearBtn  = document.getElementById('searchClearBtn');
    const noResults = document.getElementById('noSearchResults');
    clearBtn.classList.toggle('hidden', q === '');

    if (q === '') {
        document.querySelectorAll('.prof-row').forEach(row => {
            row.style.display = '';
            const label = row.querySelector('.prof-label');
            if (label) label.innerHTML = label.textContent;
        });
        document.querySelectorAll('[id^="sector-"]').forEach((card, i) => {
            const content = document.getElementById(`content-${i}`);
            const chevron = document.getElementById(`chevron-${i}`);
            if (content && chevron) {
                card.style.display = '';
                card.classList.remove('expanded', 'border-blue-500');
                content.style.maxHeight = '0px';
                chevron.style.transform = 'rotate(0deg)';
            }
        });
        noResults.classList.add('hidden');
        return;
    }

    let anyVisible = false;

    sectorsData.forEach((sector, sectorIndex) => {
        const card    = document.getElementById(`sector-${sectorIndex}`);
        const content = document.getElementById(`content-${sectorIndex}`);
        const chevron = document.getElementById(`chevron-${sectorIndex}`);
        const rows    = card.querySelectorAll('.prof-row');

        const sectorMatches = sector.name.toLowerCase().includes(q);
        let sectorHasMatch = sectorMatches;

        rows.forEach(row => {
            const profName = row.dataset.profession;
            const match    = sectorMatches || profName.includes(q);
            row.style.display = match ? '' : 'none';

            const label = row.querySelector('.prof-label');
            if (label) {
                const original = label.textContent;
                if (match && !sectorMatches) {
                    const regex = new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                    label.innerHTML = original.replace(regex, '<mark class="bg-yellow-200 rounded px-0.5">$1</mark>');
                } else {
                    label.innerHTML = original;
                }
            }

            if (match) sectorHasMatch = true;
        });

        if (sectorHasMatch) {
            anyVisible = true;
            card.style.display = '';
            card.classList.add('expanded', 'border-blue-500');
            content.style.maxHeight = '3000px';
            chevron.style.transform = 'rotate(180deg)';
        } else {
            card.style.display = 'none';
            card.classList.remove('expanded', 'border-blue-500');
            content.style.maxHeight = '0px';
            chevron.style.transform = 'rotate(0deg)';
        }
    });

    noResults.classList.toggle('hidden', anyVisible);
}

function clearSearch() {
    document.getElementById('professionSearch').value = '';
    filterProfessions('');
}

// ─── Year Check & Load ────────────────────────────────────────────────────────
function checkAndLoadYear() {
    const year = document.getElementById('year').value;
    if (!year || year.length !== 4) {
        showToast('Please enter a valid 4-digit year.', 'error');
        return;
    }
    checkExistingData(year);
}

function toggleYearDisplay(showDisplay) {
    const inputGroup  = document.getElementById('yearInputGroup');
    const yearDisplay = document.getElementById('yearDisplay');

    if (showDisplay) {
        inputGroup.classList.add('hidden');
        yearDisplay.classList.remove('hidden');
        const progress = document.getElementById('overallProgress').textContent;
        document.getElementById('overallProgressDisplay').textContent = progress;
    } else {
        inputGroup.classList.remove('hidden');
        yearDisplay.classList.add('hidden');
    }
}

function cancelYearChange() {
    document.getElementById('year').value = '';
    document.getElementById('displayYear').textContent = '----';
    clearExistingDataIndicator();
    toggleYearDisplay(false);

    oldYear = null;

    document.getElementById('cancelYearChangeBtn').classList.add('hidden');
    lockForm();
    setTimeout(() => document.getElementById('year').focus(), 100);
}

async function checkExistingData(year) {
    try {
        const response = await fetch(`/admin/licensure-rates/check-year/${year}`, {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const result = await response.json();

        if (result.exists) {
            document.getElementById('displayYear').textContent = year;
            toggleYearDisplay(true);
            document.getElementById('cancelYearChangeBtn').classList.remove('hidden');
            pendingYearData = { year: year, data: result.data };
            showExistingDataModal(year, result.data.length);
        } else {
            document.getElementById('displayYear').textContent = year;
            toggleYearDisplay(true);
            document.getElementById('cancelYearChangeBtn').classList.remove('hidden');
            clearExistingDataIndicator();
            showStatusNotification(year, false);
            unlockForm();
        }
    } catch (error) {
        console.error('Error checking existing data:', error);
        showToast('Error checking year data. Please try again.', 'error');
    }
}

// ─── Year Collision Modal ─────────────────────────────────────────────────────
function showYearCollisionModal(targetYear) {
    const currentYear = oldYear && oldYear !== '----' ? oldYear : document.getElementById('displayYear').textContent;
    document.getElementById('collisionTargetYear').textContent = targetYear;
    document.getElementById('collisionCurrentYear').textContent = currentYear;
    document.getElementById('yearCollisionModal').classList.remove('hidden');
}

function closeYearCollisionModal() {
    document.getElementById('yearCollisionModal').classList.add('hidden');
    document.getElementById('year').value = '';
    document.getElementById('year').focus();
}

// ─── Existing Data Modal ──────────────────────────────────────────────────────
function showExistingDataModal(year, totalRecords) {
    document.getElementById('existingDataYear').textContent = year;
    document.getElementById('existingDataRecordCount').textContent = totalRecords;
    document.getElementById('existingDataModal').classList.remove('hidden');
}

function closeExistingDataModal() {
    document.getElementById('existingDataModal').classList.add('hidden');
    pendingYearData = null;
    document.getElementById('year').value = '';
    document.getElementById('displayYear').textContent = '----';
    toggleYearDisplay(false);
    document.getElementById('cancelYearChangeBtn').classList.add('hidden');
    lockForm();
    setTimeout(() => document.getElementById('year').focus(), 100);
}

function confirmLoadExistingData() {
    if (!pendingYearData) return;
    const { year, data } = pendingYearData;
    document.getElementById('existingDataModal').classList.add('hidden');
    loadExistingData(data);
    showStatusNotification(year, true, data.length, 0);
    unlockForm();
    pendingYearData = null;
}

// ─── Load & Clear Existing Data ───────────────────────────────────────────────
function loadExistingData(data) {
    document.querySelectorAll('[name^="takers_"], [name^="passers_"], [name^="rate_"]').forEach(input => {
        input.value = '';
        input.classList.remove('border-orange-400', 'bg-orange-50');
    });

    let incompleteProfessions = [];

    data.forEach(item => {
        const sectorIndex = sectorsData.findIndex(s => s.name === item.sector);
        if (sectorIndex === -1) return;
        const profIndex = sectorsData[sectorIndex].professions.findIndex(p => p === item.profession);
        if (profIndex === -1) return;

        const takersInput  = document.querySelector(`[name="takers_${sectorIndex}_${profIndex}"]`);
        const passersInput = document.querySelector(`[name="passers_${sectorIndex}_${profIndex}"]`);
        const rateInput    = document.querySelector(`[name="rate_${sectorIndex}_${profIndex}"]`);

        if (item.takers && item.passers) {
            takersInput.value  = parseInt(item.takers).toLocaleString();
            passersInput.value = parseInt(item.passers).toLocaleString();
            rateInput.value    = item.passing_rate + '%';
            originalData[`${sectorIndex}_${profIndex}`] = {
                takers: item.takers,
                passers: item.passers,
                rate: item.passing_rate
            };
        }
    });

    sectorsData.forEach((sector, sectorIndex) => {
        sector.professions.forEach((profession, profIndex) => {
            const hasData = data.find(item => item.sector === sector.name && item.profession === profession);
            if (!hasData) {
                const takersInput  = document.querySelector(`[name="takers_${sectorIndex}_${profIndex}"]`);
                const passersInput = document.querySelector(`[name="passers_${sectorIndex}_${profIndex}"]`);
                takersInput.classList.add('border-orange-400', 'bg-orange-50');
                passersInput.classList.add('border-orange-400', 'bg-orange-50');
                incompleteProfessions.push({ sector: sector.name, profession });
            }
        });
    });

    showExistingDataIndicator(data.length, incompleteProfessions.length);
    updateProgress();
}

function showExistingDataIndicator(totalRecords, incompleteCount) {
    showStatusNotification(document.getElementById('displayYear').textContent, true, totalRecords, incompleteCount);
}

function clearExistingDataIndicator() {
    hideStatusNotification();
    document.querySelectorAll('[name^="takers_"], [name^="passers_"], [name^="rate_"]').forEach(input => {
        input.value = '';
        input.classList.remove('border-orange-400', 'bg-orange-50');
    });
    updateProgress();
}

// ─── Status Notification ──────────────────────────────────────────────────────
function showStatusNotification(year, exists, totalRecords, incompleteCount) {
    const notification = document.getElementById('statusNotification');
    const icon    = document.getElementById('statusIcon');
    const title   = document.getElementById('statusTitle');
    const message = document.getElementById('statusMessage');

    if (exists) {
        notification.className = 'mb-8 p-6 rounded-2xl shadow-lg bg-blue-50 border-2 border-blue-200';
        icon.className = 'flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-blue-500 text-white text-2xl';
        icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
        title.textContent = `Editing Existing Data (${totalRecords} professions)`;
        title.className   = 'text-lg font-bold mb-1 text-blue-900';
        message.innerHTML = incompleteCount > 0
            ? `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> ${incompleteCount} profession(s) have no exam data <span class="text-orange-600 font-medium">(highlighted in orange)</span>`
            : '✓ All professions have complete data';
        message.className = 'text-sm text-blue-800';
    } else {
        notification.className = 'mb-8 p-6 rounded-2xl shadow-lg bg-green-50 border-2 border-green-200';
        icon.className = 'flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-green-500 text-white text-2xl';
        icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        title.textContent = 'Creating New Data';
        title.className   = 'text-lg font-bold mb-1 text-green-900';
        message.textContent = `No existing data found for ${year}. You can now enter new licensure passing rate data.`;
        message.className = 'text-sm text-green-800';
    }

    notification.classList.remove('hidden');
}

function hideStatusNotification() {
    document.getElementById('statusNotification').classList.add('hidden');
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
    document.getElementById('licensureForm').reset();
    document.getElementById('year').value = '';
    document.getElementById('displayYear').textContent = '----';

    document.querySelectorAll('.sector-card').forEach((card, index) => {
        card.classList.remove('expanded', 'border-blue-500');
        const content = document.getElementById(`content-${index}`);
        const chevron = document.getElementById(`chevron-${index}`);
        if (content) content.style.maxHeight = '0px';
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    });

    document.querySelectorAll('[name^="takers_"], [name^="passers_"], [name^="rate_"]').forEach(input => {
        input.classList.remove('border-orange-400', 'bg-orange-50');
    });

    hideStatusNotification();
    updateProgress();
    oldYear = null;
    originalData = {};
    document.getElementById('cancelYearChangeBtn').classList.add('hidden');
    toggleYearDisplay(false);
    lockForm();
}

// ─── Confirm Submit Modal ─────────────────────────────────────────────────────
function showConfirmModal(data) {
    pendingData = data;
    document.getElementById('confirmYear').textContent = data.year;

    const deletionWarning = document.getElementById('deletionWarning');
    if (oldYear && oldYear !== '----') {
        deletionWarning.classList.remove('hidden');
        document.getElementById('oldYearToDelete').textContent = oldYear;
    } else {
        deletionWarning.classList.add('hidden');
    }

    const incompleteWarning = document.getElementById('incompleteWarning');
    const incompleteList    = document.getElementById('incompleteList');
    if (data.incomplete && data.incomplete.length > 0) {
        incompleteWarning.classList.remove('hidden');
        incompleteList.innerHTML = data.incomplete.map(item =>
            `<div class="flex items-center gap-1">
                <span class="text-orange-500">•</span>
                <span>${item.sector}: <strong>${item.profession}</strong></span>
            </div>`
        ).join('');
    } else {
        incompleteWarning.classList.add('hidden');
    }

    document.getElementById('confirmModal').classList.remove('hidden');

    const isEditMode     = Object.keys(originalData).length > 0;
    const summaryWrapper = document.getElementById('dataSummaryWrapper');
    const summaryEl      = document.getElementById('dataSummary');
    summaryEl.innerHTML  = '';
    let hasSummaryContent = false;

    data.sectors.forEach((sector, sectorIndex) => {
        const filled = sector.data.filter(p => p.takers && p.passers);
        if (filled.length === 0) return;
        hasSummaryContent = true;

        const rows = filled.map(p => {
            const profIndex   = sectorsData[sectorIndex].professions.indexOf(p.profession);
            const key         = `${sectorIndex}_${profIndex}`;
            const orig        = originalData[key];
            const cleanTakers = parseInt(String(p.takers).replace(/,/g, '')) || 0;
            const cleanPassers = parseInt(String(p.passers).replace(/,/g, '')) || 0;

            let rowClass = 'bg-white';
            let badge    = '';
            if (isEditMode) {
                if (!orig) {
                    rowClass = 'bg-green-50';
                    badge    = '<span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-green-100 text-green-700 ml-1">New</span>';
                } else if (orig.takers != cleanTakers || orig.passers != cleanPassers) {
                    rowClass = 'bg-amber-50';
                    badge    = '<span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 ml-1">Edited</span>';
                }
            }

            return `
                <div class="grid grid-cols-[1fr_auto_auto_auto] gap-2 px-3 py-1.5 items-center ${rowClass}">
                    <span class="text-xs text-gray-700 truncate">${p.profession}${badge}</span>
                    <span class="text-xs text-gray-500">T: <strong class="text-gray-800">${Number(cleanTakers).toLocaleString()}</strong></span>
                    <span class="text-xs text-gray-500">P: <strong class="text-gray-800">${Number(cleanPassers).toLocaleString()}</strong></span>
                    <span class="text-xs font-semibold px-1.5 py-0.5 rounded bg-blue-100 text-blue-700">${p.passing_rate !== null ? p.passing_rate.toFixed(2) : '0.00'}%</span>
                </div>
            `;
        }).join('');

        const sectorBlock = document.createElement('div');
        sectorBlock.className = 'bg-gray-50 rounded-lg overflow-hidden border border-gray-200';
        sectorBlock.innerHTML = `
            <div class="px-3 py-2 bg-gray-100 border-b border-gray-200">
                <p class="text-xs font-bold text-gray-700">${sector.sector}</p>
            </div>
            <div class="divide-y divide-gray-100">${rows}</div>
        `;
        summaryEl.appendChild(sectorBlock);
    });

    summaryWrapper.classList.toggle('hidden', !hasSummaryContent);
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    pendingData = null;
}

async function confirmSubmit() {
    const dataToSubmit = pendingData;
    closeConfirmModal();

    try {
        if (oldYear && oldYear !== '----') {
            try {
                await fetch(`/admin/licensure-rates/delete-year/${oldYear}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                console.log(`Deleted old year data: ${oldYear}`);
            } catch (deleteError) {
                console.error('Error deleting old year:', deleteError);
            }
        }

        const response = await fetch(window.AppRoutes.licensureRatesStore, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(dataToSubmit)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            const wasUpdate = Object.keys(originalData).length > 0;
            oldYear = null;
            showSuccessModal(wasUpdate);
        } else {
            showToast('Error: ' + (result.message || 'An error occurred while saving the data.'), 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred while saving the data.', 'error');
    }
}

// ─── Success Modal ────────────────────────────────────────────────────────────
function showSuccessModal(isUpdate = false) {
    document.getElementById('successModalTitle').textContent = isUpdate
        ? 'Successfully Updated!'
        : 'Successfully Submitted!';
    document.getElementById('successModal').classList.remove('hidden');
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    confirmReset();
}

// ─── DOMContentLoaded Init ────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    generateSectors();
    initNumInputs();
    updateProgress();

    document.getElementById('year').addEventListener('input', function (e) {
        document.getElementById('displayYear').textContent = e.target.value || '----';
    });

    document.addEventListener('input', function (e) {
        if (e.target.name && (
            e.target.name.startsWith('takers_') ||
            e.target.name.startsWith('passers_') ||
            e.target.name.startsWith('rate_')
        )) {
            updateProgress();
        }
    });

    document.getElementById('licensureForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const year = document.getElementById('year').value;
        if (!year) {
            showToast('Please enter a year before submitting.', 'warning');
            return;
        }

        const sectorResults       = [];
        const incompleteProfessions = [];

        sectorsData.forEach((sector, sectorIndex) => {
            const professionData = [];

            sector.professions.forEach((profession, profIndex) => {
                const takersRaw  = document.querySelector(`[name="takers_${sectorIndex}_${profIndex}"]`).value.replace(/,/g, '');
                const passersRaw = document.querySelector(`[name="passers_${sectorIndex}_${profIndex}"]`).value.replace(/,/g, '');
                const rateRaw    = document.querySelector(`[name="rate_${sectorIndex}_${profIndex}"]`).value;

                if (takersRaw && passersRaw && rateRaw) {
                    professionData.push({
                        profession,
                        takers:       parseInt(takersRaw),
                        passers:      parseInt(passersRaw),
                        passing_rate: parseFloat(rateRaw.replace('%', ''))
                    });
                } else {
                    incompleteProfessions.push({ sector: sector.name, profession });
                    professionData.push({ profession, takers: null, passers: null, passing_rate: null });
                }
            });

            sectorResults.push({ sector: sector.name, data: professionData });
        });

        showConfirmModal({ year: parseInt(year), sectors: sectorResults, incomplete: incompleteProfessions });
    });
});

// ─── Global Exports (required for Blade onclick handlers) ────────────────────
window.checkAndLoadYear        = checkAndLoadYear;
window.cancelYearChange        = cancelYearChange;
window.closeYearCollisionModal = closeYearCollisionModal;
window.confirmLoadExistingData = confirmLoadExistingData;
window.closeExistingDataModal  = closeExistingDataModal;
window.toggleSector            = toggleSector;
window.filterProfessions       = filterProfessions;
window.clearSearch             = clearSearch;
window.resetForm               = resetForm;
window.closeResetModal         = closeResetModal;
window.confirmReset            = confirmReset;
window.closeConfirmModal       = closeConfirmModal;
window.confirmSubmit           = confirmSubmit;
window.closeSuccessModal       = closeSuccessModal;