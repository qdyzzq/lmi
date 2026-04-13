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
                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Region XI • 2026
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>
        <!-- Main Form Area -->
        <div class="flex-1 overflow-hidden flex flex-col p-8 gap-0">
            <div class="max-w-7xl w-full mx-auto flex flex-col flex-1 min-h-0">
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

                <div id="formContent" class="relative flex flex-col flex-1 min-h-0 opacity-50 pointer-events-none select-none">
                <div id="formBlocker" class="absolute inset-0 z-10 cursor-not-allowed rounded-2xl bg-transparent"></div>

                <!-- Sectors Form — scrollable -->
                <form id="licensureForm" class="flex flex-col flex-1 min-h-0">
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

                    <div class="flex-1 overflow-y-auto space-y-4 pr-1 min-h-0" id="sectorsContainer">
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


    {{-- Blade route data passed to JS --}}
    <script>
        window.AppRoutes = {
            licensureRatesStore: "{{ route('admin.licensure-rates.store') }}"
        };
    </script>
    @vite('resources/js/admin/Module3/licensure-rates-form.js')


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