<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>LMI - Licensure Passing Rates</title>
    <style>
        .sector-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden" ">
    @include('partials.sidebar')
    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- HEADER -->
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm shrink-0">
            <h2 class="text-xl font-bold text-slate-800">Licensure Passing Rates • Admin</h2>
            <div class="flex items-center gap-4">
                <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200">
                    📅 Region XI • 2024
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>
        <!-- Main Form Area -->
        <div class="flex-1 overflow-auto p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Year Selection Card -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Select Reporting Year</h3>
                            <p class="text-sm text-gray-600">Enter a year to create new data or edit existing data</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div id="yearInputGroup" class="flex items-center gap-4">
                                <input 
                                    type="number" 
                                    id="year" 
                                    placeholder="e.g. 2024" 
                                    min="2000" 
                                    max="2100" 
                                    required 
                                    class="w-40 px-4 py-3 border-2 border-gray-300 rounded-lg text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                <button 
                                    type="button"
                                    onclick="checkAndLoadYear()"
                                    class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Check / Edit Year
                                </button>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800" id="overallProgress">0/0</span>
                            </div>
                            <div id="yearDisplay" class="hidden flex items-center gap-3">
                                <span id="displayYear" class="text-2xl font-bold text-blue-600">----</span>
                                <button 
                                    type="button"
                                    onclick="changeYear()"
                                    title="Change to a different year"
                                    class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2 text-sm"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Change Year
                                </button>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800" id="overallProgressDisplay">0/0</span>
                            </div>
                            <!-- Cancel button - shown only in display mode -->
                            <button 
                                type="button"
                                id="cancelYearChangeBtn"
                                onclick="cancelYearChange()"
                                class="hidden px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2 text-sm"
                                title="Enter a different year"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Notification -->
                <div id="statusNotification" class="hidden mb-8 p-6 rounded-2xl shadow-lg">
                    <div class="flex items-start gap-4">
                        <div id="statusIcon" class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full"></div>
                        <div class="flex-1">
                            <h4 id="statusTitle" class="text-lg font-bold mb-1"></h4>
                            <p id="statusMessage" class="text-sm"></p>
                        </div>
                    </div>
                </div>

                <!-- Sectors Form -->
                <form id="licensureForm">
                    <div class="space-y-4" id="sectorsContainer">
                        <!-- Sectors will be dynamically generated here -->
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-4 mt-8">
                        <button 
                            type="button" 
                            onclick="resetForm()" 
                            class="py-3 px-8 bg-white hover:bg-gray-50 text-gray-700 font-semibold border-2 border-gray-300 rounded-xl transition-all shadow-md hover:shadow-lg"
                        >
                            🔄 Reset All Data
                        </button>
                        <button 
                            type="submit" 
                            class="py-3 px-8 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl"
                        >
                            ✓ Submit for Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-lg w-full mx-4 transform transition-all">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-yellow-100 to-orange-100 mb-4">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-3">Confirm Submission</h3>
                <p class="text-sm text-gray-600 mb-4">You are about to submit licensure passing rate data for <strong id="confirmYear">----</strong>.</p>
                
                <!-- Incomplete Professions Warning -->
                <div id="incompleteWarning" class="hidden mb-4 p-4 bg-orange-50 border-l-4 border-orange-400 rounded text-left">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-orange-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-orange-800 text-sm mb-1">Missing Exam Data</p>
                            <p class="text-xs text-orange-700 mb-2">The following professions have no exam data for this year:</p>
                            <div id="incompleteList" class="max-h-32 overflow-y-auto text-xs text-orange-700 space-y-1"></div>
                            <p class="text-xs text-orange-600 mt-2 italic">This is normal if exams weren't conducted for these professions.</p>
                        </div>
                    </div>
                </div>

                <!-- LAYER 3: Deletion Warning (shown when changing year) -->
                <div id="deletionWarning" class="hidden mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded text-left">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-red-800 text-sm mb-1">⚠️ Year Change - Data Will Be Deleted</p>
                            <p class="text-xs text-red-700 mb-2">
                                Because you changed the year, the old year data will be permanently deleted:
                            </p>
                            <div class="bg-white border border-red-200 rounded px-2 py-1 mb-2">
                                <p class="text-xs">
                                    <span class="font-semibold text-red-800">Old year to be deleted:</span> 
                                    <span id="oldYearToDelete" class="font-bold text-red-600"></span>
                                </p>
                            </div>
                            <p class="text-xs text-red-700">
                                This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-gray-600 mb-6">This data will be queued for statistician review before being published to the database.</p>

                <div class="flex gap-3">
                    <button 
                        onclick="closeConfirmModal()"
                        class="flex-1 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium border-2 border-gray-300 rounded-lg transition"
                    >
                        Cancel
                    </button>
                    <button 
                        onclick="confirmSubmit()"
                        class="flex-1 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition"
                    >
                        Yes, Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-green-100 to-emerald-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Successfully Submitted!</h3>
                <p class="text-sm text-gray-600 mb-6">Your licensure passing rate data has been submitted to the pending queue. A statistician will review and verify it before publishing to the database.</p>
                <button 
                    onclick="closeSuccessModal()"
                    class="w-full px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition"
                >
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Reset Confirmation Modal -->
    <div id="resetModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-orange-100 to-red-100 mb-4">
                    <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-3">Reset All Data?</h3>
                <p class="text-sm text-gray-600 mb-6">This will clear all entered licensure passing rate data across all sectors. This action cannot be undone.</p>

                <div class="flex gap-3">
                    <button 
                        onclick="closeResetModal()"
                        class="flex-1 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium border-2 border-gray-300 rounded-lg transition"
                    >
                        Cancel
                    </button>
                    <button 
                        onclick="confirmReset()"
                        class="flex-1 px-6 py-2.5 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white font-medium rounded-lg transition"
                    >
                        Yes, Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let isChangingYear = false; // Flag to track if we're changing year (don't clear form)
        let oldYear = null; // Store the old year when changing
        
        // ─── Toast Notification System ──────────────────────────────────────────
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

            // Slide in
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                });
            });

            // Auto-remove after 4s
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // CSS for the shrink progress bar
        if (!document.getElementById('toastStyle')) {
            const style = document.createElement('style');
            style.id = 'toastStyle';
            style.textContent = `@keyframes shrink { from { width: 100%; } to { width: 0%; } }`;
            document.head.appendChild(style);
        }

        // Sector Data Configuration
        const sectorsData = [
            {
                name: "Engineering, Architecture & Technical",
                icon: "⚙️",
                professions: [
                    "Aeronautical Engineers",
                    "Agri-Bio Engineering",
                    "Architect",
                    "Chemical Engineer",
                    "Civil Engineer",
                    "Electronics Engineer",
                    "Electronics Technician",
                    "Geodetic Engineer",
                    "Mechanical Engineer",
                    "Metallurgical Engineer",
                    "Mining Engineer",
                    "Registered Electrical Engr.",
                    "Registered Master Electrician",
                    "Certified Plant Mechanic",
                    "Master Plumber"
                ]
            },
            {
                name: "Healthcare & Nursing",
                icon: "🏥",
                professions: [
                    "Physician",
                    "Nurse",
                    "Midwife",
                    "Dentist (Written)",
                    "Medical Technologist",
                    "Radiologic Technology",
                    "X-Ray Technologist",
                    "Pharmacist",
                    "Nutritionist Dietitian",
                    "Veterinary Medicine",
                    "Occupational Therapist",
                    "Physical Therapist",
                    "Respiratory Therapist",
                    "Speech Language Pathologist"
                ]
            },
            {
                name: "Natural Sciences",
                icon: "🌿",
                professions: [
                    "Environmental Planner",
                    "Agriculturist",
                    "Chemist",
                    "Chemical Technician",
                    "Fisheries Professionals",
                    "Food Technologist",
                    "Forester"
                ]
            },
            {
                name: "Education",
                icon: "📚",
                professions: [
                    "Professional Teachers (Elementary)",
                    "Professional Teachers (Secondary)"
                ]
            },
            {
                name: "Social Work & Behavioral Sciences",
                icon: "🤝",
                professions: [
                    "Social Worker",
                    "Guidance Counselor",
                    "Psychologist",
                    "Psychometrician",
                    "Librarian"
                ]
            },
            {
                name: "Real Estate Industry",
                icon: "🏢",
                professions: [
                    "Real Estate Appraiser",
                    "Real Estate Broker"
                ]
            },
            {
                name: "Defense Industry",
                icon: "🛡️",
                professions: [
                    "Criminologist"
                ]
            },
            {
                name: "Business, Finance & Logistics",
                icon: "💼",
                professions: [
                    "Certified Public Accountant (CPA)",
                    "Custom Broker"
                ]
            }
        ];

        let pendingData = null;

        // Initialize form on page load
        document.addEventListener('DOMContentLoaded', function() {
            generateSectors();
            updateProgress();

            // Year input listener - only update display
            document.getElementById('year').addEventListener('input', function(e) {
                const year = e.target.value;
                document.getElementById('displayYear').textContent = year || '----';
            });

            // Add input listeners to all fields for progress tracking
            document.addEventListener('input', function(e) {
                if (e.target.name && (e.target.name.startsWith('takers_') || e.target.name.startsWith('passers_') || e.target.name.startsWith('rate_'))) {
                    updateProgress();
                }
            });
        });

        function generateSectors() {
            const container = document.getElementById('sectorsContainer');
            
            sectorsData.forEach((sector, sectorIndex) => {
                const sectorCard = document.createElement('div');
                sectorCard.className = 'bg-white rounded-2xl shadow-lg overflow-hidden border-2 border-transparent transition-all duration-300 hover:shadow-xl hover:-translate-y-1';
                sectorCard.id = `sector-${sectorIndex}`;
                
                let professionsHTML = '';
                sector.professions.forEach((profession, profIndex) => {
                    professionsHTML += `
                        <div class="grid grid-cols-[2fr_1fr_1fr_1fr] gap-4 px-6 py-4 items-center border-b border-gray-100 hover:bg-blue-50 transition-colors">
                            <div class="font-medium text-gray-700 text-sm">${profession}</div>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    name="takers_${sectorIndex}_${profIndex}"
                                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="0"
                                    min="0"
                                    data-sector="${sectorIndex}"
                                    data-prof="${profIndex}"
                                    data-type="takers"
                                    onchange="calculateRate(${sectorIndex}, ${profIndex})"
                                >
                            </div>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    name="passers_${sectorIndex}_${profIndex}"
                                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="0"
                                    min="0"
                                    data-sector="${sectorIndex}"
                                    data-prof="${profIndex}"
                                    data-type="passers"
                                    onchange="calculateRate(${sectorIndex}, ${profIndex})"
                                >
                            </div>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    name="rate_${sectorIndex}_${profIndex}"
                                    class="w-full px-3 py-2 border-2 border-blue-200 bg-blue-50 rounded-lg text-sm font-semibold text-blue-700 text-center"
                                    placeholder="0.00%"
                                    readonly
                                    data-sector="${sectorIndex}"
                                    data-prof="${profIndex}"
                                    data-type="rate"
                                >
                            </div>
                        </div>
                    `;
                });
                
                sectorCard.innerHTML = `
                    <div id="header-${sectorIndex}" class="p-6 cursor-pointer flex justify-between items-center bg-gradient-to-r from-gray-50 to-white hover:from-gray-100 hover:to-gray-50 transition-all" onclick="toggleSector(${sectorIndex})">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl bg-white shadow-md">${sector.icon}</div>
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

        function checkAndLoadYear() {
            const year = document.getElementById('year').value;
            
            if (!year || year.length !== 4) {
                showToast('Please enter a valid 4-digit year.', 'error');
                return;
            }
            
            document.getElementById('displayYear').textContent = year;
            checkExistingData(year);
            toggleYearDisplay(true); // Show year display, hide input
            document.getElementById('cancelYearChangeBtn').classList.remove('hidden'); // Hide cancel button
        }

        function toggleYearDisplay(showDisplay) {
            const inputGroup = document.getElementById('yearInputGroup');
            const yearDisplay = document.getElementById('yearDisplay');
            
            if (showDisplay) {
                inputGroup.classList.add('hidden');
                yearDisplay.classList.remove('hidden');
                // Sync progress badge
                const progress = document.getElementById('overallProgress').textContent;
                document.getElementById('overallProgressDisplay').textContent = progress;
            } else {
                inputGroup.classList.remove('hidden');
                yearDisplay.classList.add('hidden');
            }
        }

        function changeYear() {
            document.getElementById('changeYearModal').classList.remove('hidden');
        }

        function closeChangeYearModal() {
            document.getElementById('changeYearModal').classList.add('hidden');
        }

        function showYearCollisionModal(targetYear) {
            // Use oldYear if available (when changing year), otherwise use displayYear
            const currentYear = oldYear && oldYear !== '----' ? oldYear : document.getElementById('displayYear').textContent;
            document.getElementById('collisionTargetYear').textContent = targetYear;
            document.getElementById('collisionCurrentYear').textContent = currentYear;
            document.getElementById('yearCollisionModal').classList.remove('hidden');
        }

        function closeYearCollisionModal() {
            document.getElementById('yearCollisionModal').classList.add('hidden');
            // Return to input mode so user can try a different year
            document.getElementById('year').value = '';
            document.getElementById('year').focus();
        }

        function confirmChangeYear() {
            document.getElementById('changeYearModal').classList.add('hidden');
            isChangingYear = true; // Set flag to prevent form clearing
            
            // Store the old year so we can delete it when saving the new one
            oldYear = document.getElementById('displayYear').textContent;
            
            document.getElementById('year').value = '';
            document.getElementById('displayYear').textContent = '----';
            clearExistingDataIndicator();
            toggleYearDisplay(false); // Show input, hide display
            
            // Show cancel button when changing year
            document.getElementById('cancelYearChangeBtn').classList.remove('hidden');
            
            // Focus on the year input for better UX
            setTimeout(() => {
                document.getElementById('year').focus();
            }, 100);
        }

        function cancelYearChange() {
            // User wants to enter a different year
            // Clear the current year and go back to input mode
            document.getElementById('year').value = '';
            document.getElementById('displayYear').textContent = '----';
            clearExistingDataIndicator();
            toggleYearDisplay(false); // Show input
            
            // If we were in change year mode, clear that state
            if (oldYear && oldYear !== '----') {
                isChangingYear = false;
                oldYear = null;
            }
            
            // Hide cancel button - user is back to input mode (can use backspace)
            document.getElementById('cancelYearChangeBtn').classList.add('hidden');
            
            // Focus on input for better UX
            setTimeout(() => {
                document.getElementById('year').focus();
            }, 100);
        }

        async function checkExistingData(year) {
            try {
                const response = await fetch(`/admin/licensure-rates/check-year/${year}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                // LAYER 1: Block collision - prevent changing to a year that already exists
                if (result.exists && isChangingYear) {
                    // User is trying to change to a year that already has data - BLOCK IT
                    showYearCollisionModal(year);
                    isChangingYear = false; // Reset flag
                    return; // Don't proceed with the change
                }
                
                if (result.exists) {
                    // Load existing data into the form
                    loadExistingData(result.data);
                    
                    // Show indicator
                    showExistingDataIndicator(result.data.length);
                } else {
                    // Year doesn't exist - show info message
                    clearExistingDataIndicator();
                    showNewYearMessage(year);
                }
            } catch (error) {
                console.error('Error checking existing data:', error);
                showToast('Error checking year data. Please try again.', 'error');
                isChangingYear = false; // Reset flag on error
            }
        }

        function showNewYearMessage(year) {
            showStatusNotification(year, false);
        }

        function loadExistingData(data) {
            // Clear all fields first
            document.querySelectorAll('[name^="takers_"], [name^="passers_"], [name^="rate_"]').forEach(input => {
                input.value = '';
                input.classList.remove('border-orange-400', 'bg-orange-50');
            });
            
            let incompleteProfessions = [];
            
            // Load data by matching sector and profession
            data.forEach(item => {
                const sectorIndex = sectorsData.findIndex(s => s.name === item.sector);
                if (sectorIndex === -1) return;
                
                const profIndex = sectorsData[sectorIndex].professions.findIndex(p => p === item.profession);
                if (profIndex === -1) return;
                
                const takersInput = document.querySelector(`[name="takers_${sectorIndex}_${profIndex}"]`);
                const passersInput = document.querySelector(`[name="passers_${sectorIndex}_${profIndex}"]`);
                const rateInput = document.querySelector(`[name="rate_${sectorIndex}_${profIndex}"]`);
                
                if (item.takers && item.passers) {
                    takersInput.value = item.takers;
                    passersInput.value = item.passers;
                    rateInput.value = item.passing_rate + '%';
                }
            });
            
            // Find professions that don't have data
            sectorsData.forEach((sector, sectorIndex) => {
                sector.professions.forEach((profession, profIndex) => {
                    const hasData = data.find(item => item.sector === sector.name && item.profession === profession);
                    
                    if (!hasData) {
                        const takersInput = document.querySelector(`[name="takers_${sectorIndex}_${profIndex}"]`);
                        const passersInput = document.querySelector(`[name="passers_${sectorIndex}_${profIndex}"]`);
                        
                        // Highlight missing data
                        takersInput.classList.add('border-orange-400', 'bg-orange-50');
                        passersInput.classList.add('border-orange-400', 'bg-orange-50');
                        
                        incompleteProfessions.push({
                            sector: sector.name,
                            profession: profession
                        });
                    }
                });
            });
            
            showExistingDataIndicator(data.length, incompleteProfessions.length);
            updateProgress();
        }

        function showExistingDataIndicator(totalRecords, incompleteCount) {
            showStatusNotification(document.getElementById('displayYear').textContent, true, totalRecords, incompleteCount);
        }

        function showStatusNotification(year, exists, totalRecords, incompleteCount) {
            const notification = document.getElementById('statusNotification');
            const icon = document.getElementById('statusIcon');
            const title = document.getElementById('statusTitle');
            const message = document.getElementById('statusMessage');

            if (exists) {
                notification.className = 'mb-8 p-6 rounded-2xl shadow-lg bg-blue-50 border-2 border-blue-200';
                icon.className = 'flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-blue-500 text-white text-2xl';
                icon.innerHTML = '📝';
                title.textContent = `Editing Existing Data (${totalRecords} professions)`;
                title.className = 'text-lg font-bold mb-1 text-blue-900';
                if (incompleteCount > 0) {
                    message.innerHTML = `⚠️ ${incompleteCount} profession(s) have no exam data <span class="text-orange-600 font-medium">(highlighted in orange)</span>`;
                } else {
                    message.innerHTML = '✓ All professions have complete data';
                }
                message.className = 'text-sm text-blue-800';
            } else {
                notification.className = 'mb-8 p-6 rounded-2xl shadow-lg bg-green-50 border-2 border-green-200';
                icon.className = 'flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-green-500 text-white text-2xl';
                icon.innerHTML = '✨';
                title.textContent = 'Creating New Data';
                title.className = 'text-lg font-bold mb-1 text-green-900';
                message.textContent = `No existing data found for ${year}. You can now enter new licensure passing rate data.`;
                message.className = 'text-sm text-green-800';
            }

            notification.classList.remove('hidden');
        }

        function hideStatusNotification() {
            document.getElementById('statusNotification').classList.add('hidden');
        }

        function clearExistingDataIndicator() {
            hideStatusNotification();
            
            // Clear all fields and remove highlights
            document.querySelectorAll('[name^="takers_"], [name^="passers_"], [name^="rate_"]').forEach(input => {
                input.value = '';
                input.classList.remove('border-orange-400', 'bg-orange-50');
            });
            
            updateProgress();
        }

        function calculateRate(sectorIndex, profIndex) {
            const takersInput = document.querySelector(`[name="takers_${sectorIndex}_${profIndex}"]`);
            const passersInput = document.querySelector(`[name="passers_${sectorIndex}_${profIndex}"]`);
            const rateInput = document.querySelector(`[name="rate_${sectorIndex}_${profIndex}"]`);
            
            const takers = parseFloat(takersInput.value) || 0;
            const passers = parseFloat(passersInput.value) || 0;
            
            if (takers > 0 && passers > 0) {
                const rate = (passers / takers) * 100;
                rateInput.value = rate.toFixed(2) + '%';
            } else if (takersInput.value === '' && passersInput.value === '') {
                rateInput.value = '';
            } else {
                rateInput.value = '';
            }
            
            updateProgress();
        }

        function updateProgress() {
            let totalRows = 0;
            let completeRows = 0;

            // Count per sector
            sectorsData.forEach((sector, sectorIndex) => {
                let sectorCompleteRows = 0;
                const sectorTotalRows = sector.professions.length;
                totalRows += sectorTotalRows;

                sector.professions.forEach((profession, profIndex) => {
                    const takersInput = document.querySelector(`[name="takers_${sectorIndex}_${profIndex}"]`);
                    const passersInput = document.querySelector(`[name="passers_${sectorIndex}_${profIndex}"]`);
                    const rateInput = document.querySelector(`[name="rate_${sectorIndex}_${profIndex}"]`);
                    
                    // A row is complete if ALL 3 fields have values
                    const hasTakers = takersInput.value && takersInput.value.trim() !== '';
                    const hasPassers = passersInput.value && passersInput.value.trim() !== '';
                    const hasRate = rateInput.value && rateInput.value.trim() !== '';
                    
                    if (hasTakers && hasPassers && hasRate) {
                        sectorCompleteRows++;
                        completeRows++;
                    }
                });

                // Update sector badge
                const badge = document.getElementById(`badge-${sectorIndex}`);
                badge.textContent = `${sectorCompleteRows}/${sectorTotalRows}`;
                if (sectorCompleteRows === sectorTotalRows) {
                    badge.className = 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800';
                } else {
                    badge.className = 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800';
                }
            });

            // Update overall progress
            const progressBadge = document.getElementById('overallProgress');
            const progressBadgeDisplay = document.getElementById('overallProgressDisplay');
            const progressText = `${completeRows}/${totalRows}`;
            const progressClass = completeRows === totalRows 
                ? 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800'
                : 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800';
            
            progressBadge.textContent = progressText;
            progressBadge.className = progressClass;
            progressBadgeDisplay.textContent = progressText;
            progressBadgeDisplay.className = progressClass;
        }

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
            
            // Collapse all sectors
            document.querySelectorAll('.sector-card').forEach(card => {
                card.classList.remove('expanded');
            });
            
            updateProgress();
            oldYear = null; // Clear old year tracking
            document.getElementById('cancelYearChangeBtn').classList.add('hidden'); // Hide cancel on reset
            toggleYearDisplay(false); // Show input, hide display - ready for next entry
        }

        function showConfirmModal(data) {
            pendingData = data;
            document.getElementById('confirmYear').textContent = data.year;
            
            // LAYER 3: Show deletion warning if changing year
            const deletionWarning = document.getElementById('deletionWarning');
            if (oldYear && oldYear !== '----') {
                // User is changing from an old year - show deletion warning
                deletionWarning.classList.remove('hidden');
                document.getElementById('oldYearToDelete').textContent = oldYear;
            } else {
                deletionWarning.classList.add('hidden');
            }
            
            // Show/hide incomplete warning
            const incompleteWarning = document.getElementById('incompleteWarning');
            const incompleteList = document.getElementById('incompleteList');
            
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
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            pendingData = null;
        }

        function showSuccessModal() {
            document.getElementById('successModal').classList.remove('hidden');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
            confirmReset();
        }

        async function confirmSubmit() {
            const dataToSubmit = pendingData;
            closeConfirmModal();

            try {
                // If we're changing from an old year, delete it first
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
                        // Continue with save even if delete fails
                    }
                }

                const response = await fetch('{{ route("admin.licensure-rates.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(dataToSubmit)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    oldYear = null; // Clear the old year after successful save
                    showSuccessModal();
                } else {
                    showToast('Error: ' + (result.message || 'An error occurred while saving the data.'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred while saving the data.', 'error');
            }
        }

        document.getElementById('licensureForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const year = document.getElementById('year').value;
            
            if (!year) {
                showToast('Please enter a year before submitting.', 'warning');
                return;
            }

            // Collect all data by sector
            const sectorResults = [];
            const incompleteProfessions = [];
            
            sectorsData.forEach((sector, sectorIndex) => {
                const professionData = [];
                
                sector.professions.forEach((profession, profIndex) => {
                    const takers = document.querySelector(`[name="takers_${sectorIndex}_${profIndex}"]`).value;
                    const passers = document.querySelector(`[name="passers_${sectorIndex}_${profIndex}"]`).value;
                    const rateRaw = document.querySelector(`[name="rate_${sectorIndex}_${profIndex}"]`).value;
                    
                    // Check if this profession has data
                    if (takers && passers && rateRaw) {
                        // Remove % sign for storage
                        const rate = parseFloat(rateRaw.replace('%', ''));
                        
                        professionData.push({
                            profession: profession,
                            takers: parseInt(takers),
                            passers: parseInt(passers),
                            passing_rate: rate
                        });
                    } else {
                        // Track incomplete professions
                        incompleteProfessions.push({
                            sector: sector.name,
                            profession: profession
                        });
                        
                        // Still add to data but with null values
                        professionData.push({
                            profession: profession,
                            takers: null,
                            passers: null,
                            passing_rate: null
                        });
                    }
                });
                
                sectorResults.push({
                    sector: sector.name,
                    data: professionData
                });
            });

            const dataToSave = {
                year: parseInt(year),
                sectors: sectorResults,
                incomplete: incompleteProfessions
            };

            showConfirmModal(dataToSave);
        });
    </script>

    <!-- Year Collision Warning Modal -->
    <div id="yearCollisionModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white">Year Already Exists</h3>
            </div>
            
            <div class="p-6">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-red-800 font-medium mb-2">
                        ⚠️ The year <span id="collisionTargetYear" class="font-bold"></span> already contains data.
                    </p>
                    <p class="text-sm text-red-700">
                        You cannot change to a year that already exists.
                    </p>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-blue-800">
                        <strong>Current year:</strong> <span id="collisionCurrentYear" class="font-bold"></span><br>
                        <strong>Your form data:</strong> Preserved and unchanged
                    </p>
                </div>
                
                <p class="text-gray-600 mb-6 text-center">Please choose a different year that doesn't already exist.</p>
                
                <button 
                    type="button"
                    onclick="closeYearCollisionModal()"
                    class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all"
                >
                    OK - Choose Different Year
                </button>
            </div>
        </div>
    </div>

    <!-- Change Year Confirmation Modal -->
    <div id="changeYearModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white">Change Year?</h3>
            </div>
            
            <div class="p-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-blue-800 font-medium">
                        ✓ Your form data will be preserved
                    </p>
                </div>
                <p class="text-gray-600 mb-6 text-center">You can enter a different year and save this data under the new year.</p>
                <div class="flex gap-3">
                    <button 
                        type="button"
                        onclick="closeChangeYearModal()"
                        class="flex-1 px-4 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition-all"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button"
                        onclick="confirmChangeYear()"
                        class="flex-1 px-4 py-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-lg transition-all"
                    >
                        Change Year
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none" style="min-width: 340px;"></div>

</body>
</html>