<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>
    <title>LMI - Job Titles Form</title>
    <style>
        .jt-card {
            position: relative;
            overflow: hidden;
        }
        .jt-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #2563eb, #0d9488);
        }
        .jt-label {
            font-size: 12.5px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 6px;
            display: block;
        }
        .jt-input {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 13.5px;
            color: #0f172a;
            background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }
        .jt-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .jt-entry {
            display: grid;
            grid-template-columns: 32px 1fr 180px;
            gap: 10px;
            align-items: end;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 14px;
            transition: border-color 0.15s;
        }
        .jt-entry:hover {
            border-color: #bfdbfe;
        }
        .jt-entry-num {
            width: 32px;
            height: 32px;
            background: #eff6ff;
            border: 1.5px solid #bfdbfe;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #2563eb;
            flex-shrink: 0;
            margin-bottom: 1px;
        }
        .jt-remove-btn {
            width: 36px;
            height: 36px;
            background: #fff0f0;
            border: 1.5px solid #fecaca;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #ef4444;
            transition: background 0.15s, border-color 0.15s;
            flex-shrink: 0;
        }
        .jt-remove-btn:hover {
            background: #fef2f2;
            border-color: #f87171;
        }
        .jt-add-btn {
            width: 100%;
            padding: 10px;
            background: #f0fdf4;
            border: 1.5px dashed #86efac;
            border-radius: 10px;
            color: #16a34a;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .jt-add-btn:hover {
            background: #dcfce7;
            border-color: #4ade80;
        }
        .jt-reset-btn {
            font-size: 13.5px;
            font-weight: 600;
            padding: 9px 22px;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            background: #fff;
            color: #334155;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.15s, border-color 0.15s;
        }
        .jt-reset-btn:hover { background: #f8fafc; border-color: #cbd5e1; }
        .jt-submit-btn {
            font-size: 13.5px;
            font-weight: 600;
            padding: 9px 22px;
            border-radius: 8px;
            border: none;
            background: #2563eb;
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 3px rgba(37,99,235,0.3), 0 4px 12px rgba(37,99,235,0.2);
            transition: background 0.15s, transform 0.15s;
        }
        .jt-submit-btn:hover { background: #1d4ed8; transform: translateY(-1px); }
    </style>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden" >
    @include('partials.sidebar')

    <div id="mainContent" class="flex-1 flex flex-col overflow-hidden transition-all duration-300">
        <!-- HEADER -->
        <header class="bg-white border-b border-slate-200 shadow-sm">
            <!-- Top row: title + right controls -->
            <div class="flex items-center justify-between px-8 h-14">
                <h2 class="text-xl font-bold text-slate-800">Job Titles Form • Admin</h2>
                <div class="flex items-center gap-4">
                    <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200">
                        <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Region XI • 2024
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
                </div>
            </div>
            <!-- Bottom row: status tab pills -->
            <div class="flex items-center gap-2 px-8 pb-3">
                <!-- Submit -->
                <button
                    onclick="switchTab('submit')"
                    id="tab-submit"
                    class="header-tab flex items-center gap-2 px-4 py-1.5 rounded-full text-[13px] font-semibold border border-blue-200 bg-blue-50 text-blue-700 transition"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Submit
                </button>
                <!-- Pending -->
                <button
                    onclick="switchTab('pending')"
                    id="tab-pending"
                    class="header-tab flex items-center gap-2 px-4 py-1.5 rounded-full text-[13px] font-semibold border border-slate-200 bg-white text-slate-500 transition hover:border-amber-200 hover:bg-amber-50 hover:text-amber-700"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Pending
                    <span id="badge-pending" class="bg-amber-100 text-amber-700 text-[11px] font-bold px-2 py-0.5 rounded-full min-w-[20px] text-center">0</span>
                </button>
                <!-- Approved -->
                <button
                    onclick="switchTab('approved')"
                    id="tab-approved"
                    class="header-tab flex items-center gap-2 px-4 py-1.5 rounded-full text-[13px] font-semibold border border-emerald-200 bg-emerald-50 text-emerald-700 transition"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Approved
                    <span id="badge-approved" class="bg-emerald-600 text-white text-[11px] font-bold px-2 py-0.5 rounded-full min-w-[20px] text-center">0</span>
                </button>
                <!-- Rejected -->
                <button
                    onclick="switchTab('rejected')"
                    id="tab-rejected"
                    class="header-tab flex items-center gap-2 px-4 py-1.5 rounded-full text-[13px] font-semibold border border-slate-200 bg-white text-slate-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Rejected
                    <span id="badge-rejected" class="bg-red-100 text-red-600 text-[11px] font-bold px-2 py-0.5 rounded-full min-w-[20px] text-center">0</span>
                </button>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <div class="flex-1 overflow-auto p-8">

            <!-- ══ SUBMIT TAB PANEL ══ -->
            <div id="panel-submit">
            <div class="jt-card max-w-6xl mx-auto bg-white rounded-xl shadow p-8">

                <!-- Card header -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-[15px] font-bold text-slate-800 leading-tight">High-Volume Job Titles Form</h1>
                        <p class="text-[11px] text-slate-700 font-medium mt-0.5">Add job titles and their employment counts</p>
                    </div>
                </div>

                <form id="jobTitlesForm">
                    <!-- Year field -->
                    <div class="mb-6 pb-6 border-b border-dashed border-slate-200">
                        <label for="year" class="jt-label">
                            Year <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="year"
                            placeholder="e.g. 2024"
                            min="2000"
                            max="2100"
                            required
                            class="jt-input w-48"
                            onblur="checkYearOnBlur(this)"
                        >
                        <p id="yearError" class="text-xs text-red-500 font-semibold mt-1 hidden"></p>
                    </div>

                    <!-- Job entries -->
                    <p class="text-[10px] font-700 uppercase tracking-widest text-slate-700 font-bold mb-3">Job Entries</p>
                    <div id="jobEntries" class="space-y-3 mb-8" style="max-height: 500px; overflow-y: auto; padding-right: 4px;">
                        <!-- entries injected here -->
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-slate-100 pt-5 flex justify-end gap-3">
                        <button type="button" onclick="resetForm()" class="jt-reset-btn">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset
                        </button>
                        <button type="submit" class="jt-submit-btn">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            Submit Data
                        </button>
                    </div>
                </form>
            </div>
            </div><!-- end #panel-submit -->

        <!-- ══════════════════════════════════════════════════════════
             APPROVED / REJECTED SUBMISSIONS PANEL
        ══════════════════════════════════════════════════════════ -->
        <div id="panel-history" class="max-w-6xl mx-auto mt-6 hidden">

            <!-- Panel body (no tab buttons here — tabs are in the header) -->
            <div class="bg-white rounded-xl border border-slate-200 shadow p-6" style="position: relative; overflow: hidden;">
                <!-- top accent bar -->
                <div id="histAccentBar" class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-300"></div>

                <!-- Panel title + Filter row -->
                <div class="flex items-center justify-between mb-5 mt-1">
                    <div class="flex items-center gap-2">
                        <span id="histPanelIcon" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-50">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        <span id="histPanelLabel" class="text-[14px] font-bold text-slate-800">Approved Submissions</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Filter by Year</label>
                        <select id="histYearFilter" onchange="loadHistory()" class="jt-input w-36 text-sm">
                            <option value="">All Years</option>
                        </select>
                        <button onclick="loadHistory()" class="flex items-center gap-1.5 text-[12px] font-semibold text-blue-600 hover:text-blue-800 transition">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Refresh
                        </button>
                        <div id="histLoader" class="hidden">
                            <svg class="w-4 h-4 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Year-grouped content area -->
                <div id="histGroupedContent">
                    <div class="text-center py-12 text-slate-400 text-sm">
                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Loading records…
                    </div>
                </div>

                <!-- Footer count -->
                <p id="histFooter" class="text-[11px] text-slate-400 mt-4 text-right"></p>
            </div>
        </div>

        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.1); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-lg w-full mx-4" style="max-height: 90vh; overflow-y: auto;">
            <!-- Warning Icon + Title -->
            <div class="text-center mb-5">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Confirm Submission</h3>
                <p class="text-sm text-gray-600">Please review the data below before submitting. The statistician will verify it before posting to the database.</p>
            </div>

            <!-- Summary Preview -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-5 text-left">
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-3">Submission Preview</p>

                <!-- Year badge -->
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xs font-semibold text-slate-500">Year:</span>
                    <span id="confirmSummaryYear" class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full"></span>
                </div>

                <!-- Job entries table -->
                <div class="rounded-lg overflow-hidden border border-slate-200" style="max-height: 260px; overflow-y: auto;">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0">
                            <tr class="bg-slate-100">
                                <th class="text-left px-3 py-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">#</th>
                                <th class="text-left px-3 py-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Job Title</th>
                                <th class="text-right px-3 py-2 text-[11px] font-semibold text-blue-700 uppercase tracking-wide bg-blue-50">Count</th>
                            </tr>
                        </thead>
                        <tbody id="confirmSummaryTableBody">
                            <!-- rows injected by JS -->
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="flex gap-3">
                <button 
                    onclick="closeConfirmModal()"
                    class="flex-1 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium border border-gray-300 rounded-lg transition"
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

    <!-- Success Modal -->
    <div id="successModal" class="hidden fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.1); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-lg w-full mx-4" style="max-height: 90vh; overflow-y: auto;">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-1">Successfully Submitted!</h3>
                <p class="text-sm text-gray-500 mb-5">Your data has been submitted to the pending queue and will be reviewed by a statistician.</p>
            </div>

            <!-- Summary Section -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-5 text-left">
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-3">Submission Summary</p>

                <!-- Year badge -->
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xs font-semibold text-slate-500">Year:</span>
                    <span id="summaryYear" class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full"></span>
                </div>

                <!-- Job entries table -->
                <div class="rounded-lg overflow-hidden border border-slate-200" style="max-height: 280px; overflow-y: auto;">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0">
                            <tr class="bg-slate-100">
                                <th class="text-left px-3 py-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">#</th>
                                <th class="text-left px-3 py-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Job Title</th>
                                <th class="text-right px-3 py-2 text-[11px] font-semibold text-blue-700 uppercase tracking-wide bg-blue-50">Count</th>
                            </tr>
                        </thead>
                        <tbody id="summaryTableBody">
                            <!-- rows injected by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Stats strip -->
                <div class="flex gap-3 mt-3">
                    <div class="flex-1 bg-white border border-slate-200 rounded-lg px-3 py-2 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Total Job Titles</p>
                        <p id="successJobTitleCount" class="text-lg font-bold text-slate-800"></p>
                    </div>
                </div>
            </div>

            <button 
                onclick="closeSuccessModal()"
                class="w-full px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition"
            >
                OK
            </button>
        </div>
    </div>

    <!-- Reset Confirmation Modal -->
    <div id="resetModal" class="hidden fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.1); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center">
                <!-- Warning Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-orange-100 mb-4">
                    <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-3">Reset Form?</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to reset the form? All entered data will be lost and cannot be recovered.</p>

                <div class="flex gap-3">
                    <button 
                        onclick="closeResetModal()"
                        class="flex-1 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium border border-gray-300 rounded-lg transition"
                    >
                        Cancel
                    </button>
                    <button 
                        onclick="confirmReset()"
                        class="flex-1 px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition"
                    >
                        Yes, Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let entryCount = 0;
        let pendingData = null;

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
        }

        function removeJobEntry(id) {
            const entry = document.getElementById(`entry-${id}`);
            if (entry) {
                entry.remove();
                // Renumber remaining entries
                const entries = document.getElementById('jobEntries').children;
                Array.from(entries).forEach((el, i) => {
                    const numBadge = el.querySelector('.jt-entry-num');
                    if (numBadge) numBadge.textContent = i + 1;
                });
            }
        }

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

        function showConfirmModal(data) {
            pendingData = data;

            // Populate year
            document.getElementById('confirmSummaryYear').textContent = data.year;

            // Populate table rows
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

        function showSuccessModal(data) {
            // Populate year
            document.getElementById('summaryYear').textContent = data ? data.year : '';

            // Populate table rows
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

       async function confirmSubmit() {
    const dataToSubmit = pendingData; // Save it first
    closeConfirmModal();              // Now safe to null it out

    try {
        const response = await fetch('{{ route("admin.job-titles.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(dataToSubmit) // Use the local copy
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
        document.getElementById('jobTitlesForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const year = document.getElementById('year').value.trim();

            // ── Year validation ──
            if (!year || parseInt(year) < 2000 || parseInt(year) > 2100) {
                showToast('Please enter a valid year (2000–2100).', 'warning');
                document.getElementById('year').focus();
                return;
            }

            const titles = document.querySelectorAll('input[name="jobTitle[]"]');
            const counts = document.querySelectorAll('input[name="jobCount[]"]');

            // ── Validate every row: both fields must be filled ──
            let hasAtLeastOne = false;
            let hasIncomplete = false;

            for (let i = 0; i < titles.length; i++) {
                const titleVal = titles[i].value.trim();
                const countVal = counts[i].value.replace(/,/g, '').trim();
                const bothEmpty = !titleVal && !countVal;
                const onlyTitle = titleVal && !countVal;
                const onlyCount = !titleVal && countVal;

                if (bothEmpty) continue; // allow fully-empty rows (they are skipped)

                if (onlyTitle || onlyCount) {
                    hasIncomplete = true;
                    // Highlight the incomplete field
                    if (onlyTitle) {
                        counts[i].style.borderColor = '#ef4444';
                        counts[i].style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
                    } else {
                        titles[i].style.borderColor = '#ef4444';
                        titles[i].style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
                    }
                } else {
                    hasAtLeastOne = true;
                    // Clear any error styling
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

            // Build jobData from only filled rows
            const jobData = [];
            for (let i = 0; i < titles.length; i++) {
                const titleVal = titles[i].value.trim();
                const countVal = counts[i].value.replace(/,/g, '').trim();
                if (titleVal && countVal) {
                    jobData.push({
                        title: titleVal,
                        count: parseInt(countVal)
                    });
                }
            }

            // ── Check if this year already has a pending submission ──
            try {
                const checkRes = await fetch('{{ route("admin.job-titles.check-year") }}?year=' + parseInt(year), {
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

            const dataToSave = {
                year: parseInt(year),
                jobs: jobData
            };

            // Show confirmation modal instead of directly submitting
            showConfirmModal(dataToSave);
        });


        // ─── Comma formatting for number inputs ─────────────────────────────────
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

        // ─── Year duplicate check on blur ────────────────────────────────────────
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
                const res = await fetch('{{ route("admin.job-titles.check-year") }}?year=' + year, {
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

        // Add 10 initial entries when page loads
        for (let i = 0; i < 10; i++) addJobEntry();

        // ─── Approved / Rejected History Panel ──────────────────────────────────
        let currentTab = 'pending';

        function switchTab(tab) {
            currentTab = tab;

            // Show/hide main panels
            const isSubmit = tab === 'submit';
            document.getElementById('panel-submit').classList.toggle('hidden', !isSubmit);
            document.getElementById('panel-history').classList.toggle('hidden', isSubmit);

            // Style the Submit tab pill
            const submitBtn = document.getElementById('tab-submit');
            if (isSubmit) {
                submitBtn.className = 'header-tab flex items-center gap-2 px-4 py-1.5 rounded-full text-[13px] font-semibold border transition border-blue-200 bg-blue-50 text-blue-700';
                return; // no history panel work needed
            } else {
                submitBtn.className = 'header-tab flex items-center gap-2 px-4 py-1.5 rounded-full text-[13px] font-semibold border transition border-slate-200 bg-white text-slate-500 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700';
            }

            // Tab config: [activeClasses, hoverClasses, badgeActiveClass]
            const tabConfig = {
                pending:  {
                    active:  'border-amber-200 bg-amber-50 text-amber-700',
                    inactive:'border-slate-200 bg-white text-slate-500 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-700',
                    badgeOn: 'bg-amber-500 text-white',
                    badgeOff:'bg-slate-200 text-slate-500',
                    bar:     'bg-gradient-to-r from-amber-400 to-yellow-400',
                    panelBg: 'bg-amber-50 hover:bg-amber-100/70',
                    yearText:'text-amber-800',
                    countBadge:'bg-amber-100 text-amber-700 border-amber-200',
                    chevron: 'text-amber-600',
                    label:   'Pending Submissions',
                    icon:    `<svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                    iconBg:  'bg-amber-50',
                },
                approved: {
                    active:  'border-emerald-200 bg-emerald-50 text-emerald-700',
                    inactive:'border-slate-200 bg-white text-slate-500 hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700',
                    badgeOn: 'bg-emerald-600 text-white',
                    badgeOff:'bg-slate-200 text-slate-500',
                    bar:     'bg-gradient-to-r from-emerald-500 to-teal-400',
                    panelBg: 'bg-emerald-50 hover:bg-emerald-100/70',
                    yearText:'text-emerald-800',
                    countBadge:'bg-emerald-100 text-emerald-700 border-emerald-200',
                    chevron: 'text-emerald-600',
                    label:   'Approved Submissions',
                    icon:    `<svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                    iconBg:  'bg-emerald-50',
                },
                rejected: {
                    active:  'border-red-200 bg-red-50 text-red-600',
                    inactive:'border-slate-200 bg-white text-slate-500 hover:border-red-200 hover:bg-red-50 hover:text-red-600',
                    badgeOn: 'bg-red-500 text-white',
                    badgeOff:'bg-slate-200 text-slate-500',
                    bar:     'bg-gradient-to-r from-red-500 to-rose-400',
                    panelBg: 'bg-red-50 hover:bg-red-100/70',
                    yearText:'text-red-800',
                    countBadge:'bg-red-100 text-red-600 border-red-200',
                    chevron: 'text-red-500',
                    label:   'Rejected Submissions',
                    icon:    `<svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                    iconBg:  'bg-red-50',
                },
            };

            // Update all pill buttons (badge colors are fixed — never overridden)
            const badgeClasses = {
                pending:  { active: 'bg-amber-500 text-white',   inactive: 'bg-amber-100 text-amber-700'   },
                approved: { active: 'bg-emerald-600 text-white', inactive: 'bg-emerald-100 text-emerald-700' },
                rejected: { active: 'bg-red-500 text-white',     inactive: 'bg-red-100 text-red-600'       },
            };
            ['pending', 'approved', 'rejected'].forEach(t => {
                const btn   = document.getElementById(`tab-${t}`);
                const badge = document.getElementById(`badge-${t}`);
                const cfg   = tabConfig[t];
                const bc    = badgeClasses[t];
                btn.className   = `header-tab flex items-center gap-2 px-4 py-1.5 rounded-full text-[13px] font-semibold border transition ${t === tab ? cfg.active : cfg.inactive}`;
                badge.className = `text-[11px] font-bold px-2 py-0.5 rounded-full min-w-[20px] text-center ${t === tab ? bc.active : bc.inactive}`;
            });

            // Accent bar
            const cfg = tabConfig[tab];
            document.getElementById('histAccentBar').className =
                `absolute top-0 left-0 right-0 h-[3px] transition-all duration-300 ${cfg.bar}`;

            // Panel label + icon
            document.getElementById('histPanelLabel').textContent = cfg.label;
            document.getElementById('histPanelIcon').innerHTML    = cfg.icon;
            document.getElementById('histPanelIcon').className    = `inline-flex items-center justify-center w-7 h-7 rounded-lg ${cfg.iconBg}`;

            loadHistory();
        }

        async function loadHistory() {
            const year    = document.getElementById('histYearFilter').value;
            const loader  = document.getElementById('histLoader');
            const content = document.getElementById('histGroupedContent');

            loader.classList.remove('hidden');
            content.innerHTML = `<div class="text-center py-12 text-slate-400 text-sm">Loading…</div>`;

            try {
                const params = new URLSearchParams({ status: currentTab });
                if (year) params.append('year', year);

                const res  = await fetch(`{{ route('admin.job-titles.history') }}?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await res.json();

                // Update header badges for all three
                document.getElementById('badge-pending').textContent  = data.counts?.pending  ?? 0;
                document.getElementById('badge-approved').textContent = data.counts?.approved ?? 0;
                document.getElementById('badge-rejected').textContent = data.counts?.rejected ?? 0;

                // Repopulate year filter with only years that exist for this status
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

                // Group records by year
                const grouped = {};
                records.forEach(r => {
                    const y = r.year ?? 'Unknown';
                    if (!grouped[y]) grouped[y] = [];
                    grouped[y].push(r);
                });

                // Colour config per tab (mirrors switchTab)
                const colourMap = {
                    pending:  { panelBg:'bg-amber-50 hover:bg-amber-100/70',   yearText:'text-amber-800',   countBadge:'bg-amber-100 text-amber-700 border-amber-200',   chevron:'text-amber-600'  },
                    approved: { panelBg:'bg-emerald-50 hover:bg-emerald-100/70',yearText:'text-emerald-800', countBadge:'bg-emerald-100 text-emerald-700 border-emerald-200',chevron:'text-emerald-600'},
                    rejected: { panelBg:'bg-red-50 hover:bg-red-100/70',        yearText:'text-red-800',     countBadge:'bg-red-100 text-red-600 border-red-200',           chevron:'text-red-500'    },
                };
                const cc = colourMap[currentTab] || colourMap.approved;

                content.innerHTML = '';
                const years = Object.keys(grouped).sort((a, b) => b - a);

                years.forEach(yr => {
                    const rows = grouped[yr];
                    // For pending, show submitted_at; for others, reviewed_at
                    const dateField = currentTab === 'pending' ? (rows[0]?.created_at ?? rows[0]?.submitted_at) : rows[0]?.reviewed_at;
                    const dateLabel = currentTab === 'pending' ? 'Submitted' : 'Reviewed';
                    const displayDate = dateField
                        ? new Date(dateField).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' })
                        : '—';

                    const section = document.createElement('div');
                    section.className = 'mb-3 rounded-xl border border-slate-200 overflow-hidden';

                    // Year header (clickable to collapse)
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

                    // Table wrapper (collapsible)
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

                    // Toggle on click
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

        // Load on page ready — default to Submit tab, but fetch badge counts immediately
        document.addEventListener('DOMContentLoaded', () => {
            switchTab('submit');
            // Pre-fetch counts so badges are populated right away
            fetch(`{{ route('admin.job-titles.history') }}?status=pending`, {
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
        });
    </script>

    <!-- TOAST NOTIFICATION -->
    <div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none" style="min-width: 340px;"></div>

</body>
</html>