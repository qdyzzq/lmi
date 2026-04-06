<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>
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
                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Region XI • 2024
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>
        <!-- Main Form Area -->
        <div class="flex-1 overflow-hidden flex flex-col p-8 gap-0">
            <div class="max-w-7xl w-full mx-auto flex flex-col flex-1 overflow-hidden">
                <!-- Sticky top section: year picker + status -->
                <div class="shrink-0">
                <!-- Year Selection Card -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-4">
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
                <div id="statusNotification" class="hidden mb-4 p-6 rounded-2xl shadow-lg">
                    <div class="flex items-start gap-4">
                        <div id="statusIcon" class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full"></div>
                        <div class="flex-1">
                            <h4 id="statusTitle" class="text-lg font-bold mb-1"></h4>
                            <p id="statusMessage" class="text-sm"></p>
                        </div>
                    </div>
                </div>
                </div><!-- end sticky top section -->

                <!-- Lock Banner -->
                <div id="lockBanner" class="mb-4 flex items-center gap-3 bg-amber-50 border-2 border-amber-300 rounded-xl px-5 py-3 shrink-0">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <p class="text-sm font-semibold text-amber-800">Enter a reporting year and click <strong>Check / Edit Year</strong> to unlock the form.</p>
                </div>

                <div id="formContent" class="relative flex flex-col flex-1 overflow-hidden opacity-50 pointer-events-none select-none">
                <div id="formBlocker" class="absolute inset-0 z-10 cursor-not-allowed rounded-2xl bg-transparent"></div>

                <!-- Sectors Form — scrollable -->
                <form id="licensureForm" class="flex flex-col flex-1 overflow-hidden">
                    <!-- Search Bar -->
                    <div class="relative mb-4 shrink-0">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input
                            type="text"
                            id="professionSearch"
                            placeholder="Search profession or sector..."
                            oninput="filterProfessions(this.value)"
                            class="w-full pl-10 pr-10 py-2.5 border-2 border-gray-200 rounded-lg text-sm focus:outline-none  focus:border-b-blue-500 focus:border-transparent bg-white shadow-sm"
                        >
                        <button type="button" id="searchClearBtn" onclick="clearSearch()" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <!-- No Results Message -->
                    <div id="noSearchResults" class="hidden text-center py-8 text-sm text-gray-500 italic bg-white rounded-2xl shadow-lg mb-4 shrink-0">No professions match your search.</div>

                    <div class="flex-1 overflow-y-auto space-y-4 pr-1" id="sectorsContainer">
                        <!-- Sectors will be dynamically generated here -->
                    </div>

                    <!-- Action Buttons — pinned at bottom -->
                    <div class="shrink-0 flex justify-end gap-4 mt-4 pt-4 border-t border-gray-200 bg-slate-100">
                        <button 
                            type="button" 
                            onclick="resetForm()" 
                            class="py-3 px-8 bg-white hover:bg-gray-50 text-gray-700 font-semibold border-2 border-gray-300 rounded-xl transition-all shadow-md hover:shadow-lg"
                        >
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Reset All Data
                        </button>
                        <button 
                            type="submit" 
                            class="py-3 px-8 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl"
                        >
                            ✓ Submit
                        </button>
                    </div>
                </form>
                </div><!-- end formContent -->
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 transform transition-all flex flex-col max-h-[90vh]">
            <!-- Fixed header -->
            <div class="p-8 pb-4 text-center shrink-0">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-yellow-100 to-orange-100 mb-4">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Confirm Submission</h3>
                <p class="text-sm text-gray-600 mb-4">You are about to submit licensure passing rate data for <strong id="confirmYear">----</strong>.</p>
            </div>

            <!-- Scrollable body -->
            <div class="overflow-y-auto px-8 flex-1">
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

                <!-- Deletion Warning (shown when changing year) -->
                <div id="deletionWarning" class="hidden mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded text-left">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-red-800 text-sm mb-1">Year Change - Data Will Be Deleted</p>
                            <p class="text-xs text-red-700 mb-2">Because you changed the year, the old year data will be permanently deleted:</p>
                            <div class="bg-white border border-red-200 rounded px-2 py-1 mb-2">
                                <p class="text-xs"><span class="font-semibold text-red-800">Old year to be deleted:</span> <span id="oldYearToDelete" class="font-bold text-red-600"></span></p>
                            </div>
                            <p class="text-xs text-red-700">This action cannot be undone.</p>
                        </div>
                    </div>
                </div>

                <!-- Data Summary -->
                <div id="dataSummaryWrapper" class="mb-4 text-left">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Data Summary</p>
                    <div id="dataSummary" class="space-y-2"></div>
                </div>
            </div>

            <!-- Fixed footer -->
            <div class="px-8 pb-8 pt-4 shrink-0">
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
                <h3 id="successModalTitle" class="text-xl font-bold text-gray-900 mb-3">Successfully Submitted!</h3>
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
        let pendingYearData = null; // Holds fetched data while waiting for modal confirmation

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
                icon: "<svg class=\"w-6 h-6 text-slate-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z\"/><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 12a3 3 0 11-6 0 3 3 0 016 0z\"/></svg>",
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
                icon: "<svg class=\"w-6 h-6 text-red-500\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4\"/></svg>",
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
                icon: "<svg class=\"w-6 h-6 text-green-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z\"/></svg>",
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
                    "Social Worker",
                    "Guidance Counselor",
                    "Psychologist",
                    "Psychometrician",
                    "Librarian"
                ]
            },
            {
                name: "Real Estate Industry",
                icon: "<svg class=\"w-6 h-6 text-amber-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16M3 21h18M9 21V9m6 12V9m-3-6v3\"/></svg>",
                professions: [
                    "Real Estate Appraiser",
                    "Real Estate Broker"
                ]
            },
            {
                name: "Defense Industry",
                icon: "<svg class=\"w-6 h-6 text-slate-700\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z\"/></svg>",
                professions: [
                    "Criminologist"
                ]
            },
            {
                name: "Business, Finance & Logistics",
                icon: "<svg class=\"w-6 h-6 text-indigo-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z\"/></svg>",
                professions: [
                    "Certified Public Accountant (CPA)",
                    "Custom Broker"
                ]
            }
        ];

        let pendingData = null;
        let originalData = {}; // Snapshot of values when existing data is loaded

        // Initialize form on page load
        // ─── Comma formatting helpers ───────────────────────────────────────────
        function initNumInputs() {
            document.querySelectorAll('input.num-input').forEach(input => {
                input.addEventListener('input', function () {
                    const raw = this.value.replace(/[^0-9]/g, '');
                    const cursor = this.selectionStart;
                    this.value = raw === '' ? '' : parseInt(raw).toLocaleString();
                    // trigger rate calc
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

        document.addEventListener('DOMContentLoaded', function() {
            generateSectors();
            initNumInputs();
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
                sectorCard.dataset.sectorName = sector.name.toLowerCase();
                
                let professionsHTML = '';
                sector.professions.forEach((profession, profIndex) => {
                    professionsHTML += `
                        <div class="prof-row grid grid-cols-[2fr_1fr_1fr_1fr] gap-4 px-6 py-4 items-center border-b border-gray-100 hover:bg-blue-50 transition-colors" data-profession="${profession.toLowerCase()}">
                            <div class="font-medium text-gray-700 text-sm prof-label">${profession}</div>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    inputmode="numeric"
                                    name="takers_${sectorIndex}_${profIndex}"
                                    class="num-input w-full px-3 py-2 border-2 border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-right"
                                    placeholder="0"
                                    data-sector="${sectorIndex}"
                                    data-prof="${profIndex}"
                                    data-type="takers"
                                >
                            </div>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    inputmode="numeric"
                                    name="passers_${sectorIndex}_${profIndex}"
                                    class="num-input w-full px-3 py-2 border-2 border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-right"
                                    placeholder="0"
                                    data-sector="${sectorIndex}"
                                    data-prof="${profIndex}"
                                    data-type="passers"
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

        function filterProfessions(query) {
            const q = query.toLowerCase().trim();
            const clearBtn = document.getElementById('searchClearBtn');
            const noResults = document.getElementById('noSearchResults');

            clearBtn.classList.toggle('hidden', q === '');

            if (q === '') {
                // Reset: restore all rows, collapse all sectors, remove highlights
                document.querySelectorAll('.prof-row').forEach(row => {
                    row.style.display = '';
                    const label = row.querySelector('.prof-label');
                    if (label) label.innerHTML = label.textContent; // strip highlights
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
                const card = document.getElementById(`sector-${sectorIndex}`);
                const content = document.getElementById(`content-${sectorIndex}`);
                const chevron = document.getElementById(`chevron-${sectorIndex}`);
                const rows = card.querySelectorAll('.prof-row');

                const sectorMatches = sector.name.toLowerCase().includes(q);
                let sectorHasMatch = sectorMatches;

                rows.forEach(row => {
                    const profName = row.dataset.profession;
                    const match = sectorMatches || profName.includes(q);
                    row.style.display = match ? '' : 'none';

                    // Highlight matched text in label
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
                    // Auto-expand sectors that have matching professions
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

        function checkAndLoadYear() {
            const year = document.getElementById('year').value;
            
            if (!year || year.length !== 4) {
                showToast('Please enter a valid 4-digit year.', 'error');
                return;
            }
            
            checkExistingData(year);
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
            lockForm();
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
                    // Show confirmation modal — let admin decide whether to edit or pick a different year
                    // First commit the year display so the modal shows the right year
                    document.getElementById('displayYear').textContent = year;
                    toggleYearDisplay(true);
                    document.getElementById('cancelYearChangeBtn').classList.remove('hidden');
                    pendingYearData = { year: year, data: result.data };
                    showExistingDataModal(year, result.data.length);
                } else {
                    // Year doesn't exist — commit display and show new-year message
                    document.getElementById('displayYear').textContent = year;
                    toggleYearDisplay(true);
                    document.getElementById('cancelYearChangeBtn').classList.remove('hidden');
                    clearExistingDataIndicator();
                    showNewYearMessage(year);
                    unlockForm();
                    isChangingYear = false;
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

        function showExistingDataModal(year, totalRecords) {
            document.getElementById('existingDataYear').textContent = year;
            document.getElementById('existingDataRecordCount').textContent = totalRecords;
            document.getElementById('existingDataModal').classList.remove('hidden');
        }

        function closeExistingDataModal() {
            document.getElementById('existingDataModal').classList.add('hidden');
            pendingYearData = null;
            isChangingYear = false;
            // Return user to year input so they can pick a different year
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
            unlockForm();
            pendingYearData = null;
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
                    takersInput.value = parseInt(item.takers).toLocaleString();
                    passersInput.value = parseInt(item.passers).toLocaleString();
                    rateInput.value = item.passing_rate + '%';
                    // Store original values for change detection
                    originalData[`${sectorIndex}_${profIndex}`] = {
                        takers: item.takers,
                        passers: item.passers,
                        rate: item.passing_rate
                    };
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
                icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';
                title.textContent = `Editing Existing Data (${totalRecords} professions)`;
                title.className = 'text-lg font-bold mb-1 text-blue-900';
                if (incompleteCount > 0) {
                    message.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> ${incompleteCount} profession(s) have no exam data <span class="text-orange-600 font-medium">(highlighted in orange)</span>`;
                } else {
                    message.innerHTML = '✓ All professions have complete data';
                }
                message.className = 'text-sm text-blue-800';
            } else {
                notification.className = 'mb-8 p-6 rounded-2xl shadow-lg bg-green-50 border-2 border-green-200';
                icon.className = 'flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-green-500 text-white text-2xl';
                icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
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
            
            const takers = parseFloat(takersInput.value.replace(/,/g, '')) || 0;
            const passers = parseFloat(passersInput.value.replace(/,/g, '')) || 0;
            
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

            // Collapse all sectors - also reset maxHeight and chevron rotation
            document.querySelectorAll('.sector-card').forEach((card, index) => {
                card.classList.remove('expanded', 'border-blue-500');
                const content = document.getElementById(`content-${index}`);
                const chevron = document.getElementById(`chevron-${index}`);
                if (content) content.style.maxHeight = '0px';
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            });

            // Clear all orange highlights on takers/passers fields
            document.querySelectorAll('[name^="takers_"], [name^="passers_"], [name^="rate_"]').forEach(input => {
                input.classList.remove('border-orange-400', 'bg-orange-50');
            });

            // Hide status notification ("Editing Existing Data" banner)
            hideStatusNotification();

            updateProgress();
            oldYear = null;
            originalData = {};
            document.getElementById('cancelYearChangeBtn').classList.add('hidden');
            toggleYearDisplay(false);
            lockForm();
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

            // Build data summary — always shown
            // In edit mode, rows that changed are highlighted amber; new rows are green; unchanged are normal
            const isEditMode = Object.keys(originalData).length > 0;
            const summaryWrapper = document.getElementById('dataSummaryWrapper');
            const summaryEl = document.getElementById('dataSummary');
            summaryEl.innerHTML = '';
            let hasSummaryContent = false;

            data.sectors.forEach((sector, sectorIndex) => {
                const filled = sector.data.filter(p => p.takers && p.passers);
                if (filled.length === 0) return;
                hasSummaryContent = true;

                const rows = filled.map(p => {
                    const profIndex = sectorsData[sectorIndex].professions.indexOf(p.profession);
                    const key = `${sectorIndex}_${profIndex}`;
                    const orig = originalData[key];
                    const cleanTakers = parseInt(String(p.takers).replace(/,/g,'')) || 0;
                    const cleanPassers = parseInt(String(p.passers).replace(/,/g,'')) || 0;

                    let rowClass = 'bg-white';
                    let badge = '';
                    if (isEditMode) {
                        if (!orig) {
                            // Newly added in edit session
                            rowClass = 'bg-green-50';
                            badge = '<span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-green-100 text-green-700 ml-1">New</span>';
                        } else if (orig.takers != cleanTakers || orig.passers != cleanPassers) {
                            // Changed
                            rowClass = 'bg-amber-50';
                            badge = '<span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 ml-1">Edited</span>';
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
                    const takersRaw = document.querySelector(`[name="takers_${sectorIndex}_${profIndex}"]`).value.replace(/,/g, '');
                    const passersRaw = document.querySelector(`[name="passers_${sectorIndex}_${profIndex}"]`).value.replace(/,/g, '');
                    const rateRaw = document.querySelector(`[name="rate_${sectorIndex}_${profIndex}"]`).value;
                    const takers = takersRaw;
                    const passers = passersRaw;
                    
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

    <!-- MODAL: Existing Data Found -->
    <div id="existingDataModal" class="hidden fixed inset-0 backdrop-blur-sm bg-white/30 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900">Existing Data Found</h3>
                        <p class="text-sm text-gray-600 mt-1">This year already has licensure rate data</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-blue-900">Year: <span id="existingDataYear" class="text-blue-700"></span></p>
                            <p class="text-sm text-blue-800 mt-1">Records found: <span id="existingDataRecordCount" class="font-semibold"></span> profession(s)</p>
                        </div>
                    </div>
                </div>

                <p class="text-gray-700 mb-6">
                    Data already exists for this year. Would you like to <strong>edit the existing data</strong> or <strong>enter a different year</strong>?
                </p>

                <div class="flex flex-col gap-3">
                    <button
                        onclick="confirmLoadExistingData()"
                        class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Yes, Edit Existing Data
                    </button>
                    <button
                        onclick="closeExistingDataModal()"
                        class="w-full px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        No, Enter Different Year
                    </button>
                </div>
            </div>
        </div>
    </div>

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
                        <svg class="w-4 h-4 inline-block mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> The year <span id="collisionTargetYear" class="font-bold"></span> already contains data.
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