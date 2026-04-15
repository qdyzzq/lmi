<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    {{-- Blade data passed to JS --}}
    <script>
        window.AppData = {
            submissionsCount: {{ $submissions->count() }},
            pendingCountUrl:  '{{ route("statistician.job-titles.pending-count") }}',
        };
    </script>

    {{-- Load JS before Alpine so all functions are on window when Alpine boots --}}
    @vite('resources/js/statistician/job-title-pending.js')

    {{-- Alpine must come AFTER --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>
    <title>LMI</title>
    <style>
        /* ---------- EDIT BUTTON IN HEADER ---------- */
        .btn-edit-mode {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: rgba(255,255,255,0.15);
            border: 1.5px solid rgba(255,255,255,0.4);
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s, box-shadow 0.2s;
            letter-spacing: 0.02em;
        }
        .btn-edit-mode:hover {
            background: rgba(255,255,255,0.25);
            border-color: rgba(255,255,255,0.7);
        }
        .btn-edit-mode.active {
            background: #fbbf24;
            border-color: #f59e0b;
            color: #1e3a8a;
            box-shadow: 0 2px 8px rgba(251, 191, 36, 0.4);
        }
        .btn-edit-mode svg {
            width: 18px;
            height: 18px;
        }

        /* ---------- APPROVE / REJECT AT BOTTOM ---------- */
        .card-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding: 20px 28px 24px;
        }
        .btn-approve, .btn-reject {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 28px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            letter-spacing: 0.02em;
        }
        .btn-approve {
            background: #16a34a;
            color: #fff;
        }
        .btn-approve:hover { background: #15803d; }
        .btn-approve:active { transform: scale(0.96); }
        .btn-reject {
            background: #dc2626;
            color: #fff;
        }
        .btn-reject:hover { background: #b91c1c; }
        .btn-reject:active { transform: scale(0.96); }
        .btn-approve svg, .btn-reject svg {
            width: 18px;
            height: 18px;
        }

        /* ---------- EDITABLE ROW HIGHLIGHT ---------- */
        .editable-row {
            transition: background 0.25s ease;
        }
        .editable-row.edit-mode {
            background: #eef2ff !important;
        }
        .editable-row.edit-mode td {
            position: relative;
        }

        /* ---------- CELL EDITABLE ---------- */
        .cell-editable {
            border-radius: 6px;
            padding: 6px 10px;
            margin: -6px -10px;
            transition: background 0.15s, box-shadow 0.15s;
            cursor: default;
            outline: none;
        }
        .edit-mode .cell-editable {
            cursor: pointer;
            background: #fff;
            border: 1.5px dashed #a5b4fc;
            box-shadow: inset 0 1px 3px rgba(99,102,241,0.08);
        }
        .edit-mode .cell-editable:hover {
            border-color: #6366f1;
            background: #f5f3ff;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
        }
        .edit-mode .cell-editable .edit-hint {
            display: inline;
        }

        /* tiny "click to edit" hint that shows on hover inside edit mode */
        .edit-hint {
            display: none;
            font-size: 11px;
            color: #6366f1;
            margin-left: 10px;
            font-style: italic;
            opacity: 0.7;
        }

        /* ---------- INLINE INPUT ---------- */
        .inline-input {
            width: 100%;
            border: 1.5px solid #6366f1;
            border-radius: 6px;
            outline: none;
            font-size: 1.125rem;
            padding: 7px 10px;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .inline-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        }
        .inline-input.error {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2);
        }

        /* ---------- INLINE SAVE / CANCEL BUTTONS ---------- */
        .inline-actions {
            display: flex;
            gap: 6px;
            align-items: center;
        }
        .inline-actions button {
            width: 34px;
            height: 34px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s, transform 0.1s;
            flex-shrink: 0;
        }
        .inline-actions button:active { transform: scale(0.9); }
        .btn-save-inline { background: #16a34a; color: #fff; }
        .btn-save-inline:hover { background: #15803d; }
        .btn-cancel-inline { background: #e5e7eb; color: #374151; }
        .btn-cancel-inline:hover { background: #d1d5db; }

        /* ---------- EDITED BADGE ---------- */
        .edited-badge {
            display: none;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            background: #fef3c7;
            color: #b45309;
            padding: 3px 9px;
            border-radius: 20px;
            margin-left: 10px;
            vertical-align: middle;
        }

        /* ---------- YEAR EDITED BANNER ---------- */
        .year-edited-banner {
            display: none;
            align-items: center;
            gap: 10px;
            background: #fffbeb;
            border-top: 1px solid #f59e0b;
            padding: 12px 28px;
            font-size: 0.9rem;
            color: #92400e;
        }
        .year-edited-banner.visible { display: flex; }
        .year-edited-banner svg { flex-shrink: 0; width: 20px; height: 20px; }

    </style>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden" >
   @include('partials.statisticianSidebar')
    <!-- MAIN -->
    <div id="mainContent" class="flex-1 flex flex-col overflow-hidden transition-all duration-300">
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800">Job Title Pending <span class="text-slate-400 font-normal">• Statistician</span></h2>
            <div class="flex items-center gap-4">
                <div class="bg-yellow-100 px-3 py-1.5 rounded-lg text-xs font-medium text-yellow-700 border border-yellow-300">
                    <span id="pending-badge-count" class="font-bold">{{ $submissions->count() }}</span> Pending
                </div>
                <div class="bg-slate-100 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Region XI • {{ date('Y') }}</div>
                <div class="w-9 h-9 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>

        <div class="flex-1 overflow-auto">
            <div class="bg-gray-100 py-10 px-4">
                <div class="max-w-6xl mx-auto">
                    <h1 class="text-3xl font-bold text-gray-900 mb-5">Pending Job Title Submissions</h1>
                    
                    @if($submissions->isEmpty())
                        <div class="bg-white rounded-lg shadow p-12 text-center">
                            <p class="text-gray-500 text-lg">No pending submissions</p>
                        </div>
                    @else
                        @foreach($submissions as $year => $jobs)
                            <div class="bg-white rounded-lg shadow mb-6 overflow-hidden" id="card-{{ $year }}">

                                <!-- BLUE HEADER: year info LEFT, edit button RIGHT -->
                                <div class="bg-blue-600 text-white px-8 py-5 flex justify-between items-center">
                                    <div>
                                        <h2 class="text-2xl font-semibold">Year: {{ $year }}</h2>
                                        <p class="text-base opacity-90 mt-0.5">Submitted by: {{ $jobs->first()->submitter->name ?? 'Unknown' }}</p>
                                        <p class="text-sm opacity-75 mt-0.5">{{ $jobs->first()->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                    <!-- EDIT TOGGLE BUTTON -->
                                    <div class="flex items-center gap-2">
                                        <button class="btn-edit-mode" id="cancelEditBtn-{{ $year }}" onclick="cancelEditMode({{ $year }})" style="display:none; background:rgba(255,255,255,0.15); border-color:rgba(255,255,255,0.4);">
                                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;">
                                                <path d="M3 3l10 10M13 3L3 13"/>
                                            </svg>
                                            <span>Cancel</span>
                                        </button>
                                        <button class="btn-edit-mode" id="editBtn-{{ $year }}" onclick="toggleEditMode({{ $year }})">
                                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11.5 1.5l3 3L5.5 13H2.5v-3L11.5 1.5z"/>
                                            </svg>
                                            <span id="editBtnText-{{ $year }}">Edit</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- EDITED BANNER -->
                                <div class="year-edited-banner" id="banner-{{ $year }}">
                                    <svg viewBox="0 0 16 16" fill="none">
                                        <circle cx="8" cy="8" r="7" stroke="#f59e0b" stroke-width="1.5"/>
                                        <path d="M8 4v4l2.5 2" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                    <span>This submission has been <strong>edited</strong>. Changes will be saved when you approve.</span>
                                </div>

                                <!-- TABLE -->
                                <div class="p-8" id="tableWrap-{{ $year }}">
                                    <table class="w-full">
                                        <thead class="border-b-2 border-gray-200">
                                            <tr>
                                                <th class="text-left py-5 px-6 font-semibold text-gray-700 text-lg">Job Title</th>
                                                <th class="text-right py-5 px-6 font-semibold text-gray-700 text-lg">Count</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-{{ $year }}">
                                            @foreach($jobs->sortByDesc('count') as $job)
                                                <tr class="border-b border-gray-100 editable-row" id="row-{{ $job->id }}">
                                                    <!-- TITLE CELL -->
                                                    <td class="py-5 px-6 text-lg">
                                                        <div class="cell-editable" id="title-display-{{ $job->id }}" onclick="startEdit('{{ $job->id }}', 'title')">
                                                            <span id="title-text-{{ $job->id }}">{{ $job->title }}</span>
                                                            <span class="edit-hint">click to edit</span>
                                                            <span class="edited-badge" id="title-badge-{{ $job->id }}">edited</span>
                                                        </div>
                                                        <div style="display:none;" id="title-edit-{{ $job->id }}" class="inline-actions">
                                                            <input type="text" class="inline-input" id="title-input-{{ $job->id }}" value="{{ $job->title }}" onkeydown="handleKeydown(event, '{{ $job->id }}', 'title')">
                                                            <button class="btn-save-inline" onclick="saveEdit('{{ $job->id }}', 'title')" title="Save">
                                                                <svg width="16" height="16" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7l3.5 3.5L12 4"/></svg>
                                                            </button>
                                                            <button class="btn-cancel-inline" onclick="cancelEdit('{{ $job->id }}', 'title')" title="Cancel">
                                                                <svg width="16" height="16" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M3 3l8 8M11 3l-8 8"/></svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <!-- COUNT CELL -->
                                                    <td class="py-5 px-6 text-right text-lg">
                                                        <div class="cell-editable flex justify-end items-center gap-2" id="count-display-{{ $job->id }}" onclick="startEdit('{{ $job->id }}', 'count')">
                                                            <span class="edit-hint">click to edit</span>
                                                            <span id="count-text-{{ $job->id }}" class="font-medium">{{ number_format($job->count) }}</span>
                                                            <span class="edited-badge" id="count-badge-{{ $job->id }}">edited</span>
                                                        </div>
                                                        <div style="display:none;" id="count-edit-{{ $job->id }}" class="inline-actions justify-end">
                                                            <input type="text" inputmode="numeric" class="inline-input" id="count-input-{{ $job->id }}" value="{{ number_format($job->count) }}" style="width:160px;" onkeydown="handleKeydown(event, '{{ $job->id }}', 'count')" oninput="formatCountInput(this)" onfocus="stripCommas(this)" onblur="reformatOnBlur(this)">
                                                            <button class="btn-save-inline" onclick="saveEdit('{{ $job->id }}', 'count')" title="Save">
                                                                <svg width="16" height="16" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7l3.5 3.5L12 4"/></svg>
                                                            </button>
                                                            <button class="btn-cancel-inline" onclick="cancelEdit('{{ $job->id }}', 'count')" title="Cancel">
                                                                <svg width="16" height="16" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M3 3l8 8M11 3l-8 8"/></svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                                            <tr>
                                                <td class="py-5 px-6 font-bold text-lg">Total Entries</td>
                                                <td class="py-5 px-6 text-right font-bold text-lg" id="totalCount-{{ $year }}">{{ $jobs->count() }} jobs</td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                </div>

                                <!-- FOOTER: Approve + Reject bottom-right -->
                                <div class="card-footer">
                                    <button class="btn-approve" onclick="openApproveSummaryModal({{ $year }})">
                                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 8.5l3.5 3.5 7.5-7.5"/></svg>
                                        Approve
                                    </button>
                                    <button class="btn-reject" onclick="showRejectModal({{ $year }})">
                                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M4 4l8 8M12 4l-8 8"/></svg>
                                        Reject
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- APPROVE SUMMARY MODAL -->
    <div id="approveSummaryModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeApproveSummaryModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl p-8 max-w-lg w-full mx-4" style="max-height: 90vh; overflow-y: auto;">

            <!-- Header -->
            <div class="text-center mb-5">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-1">Review Before Approving</h3>
                <p class="text-sm text-gray-500">Please review the submission summary below before finalizing your approval.</p>
            </div>

            <!-- Summary preview -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-5 text-left">
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500 mb-3">Submission Preview</p>

                <!-- Meta -->
                <div class="flex flex-wrap gap-3 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-slate-500">Year:</span>
                        <span id="summaryModalYear" class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-slate-500">Submitted by:</span>
                        <span id="summaryModalSubmitter" class="bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-1 rounded-full"></span>
                    </div>
                </div>

                <!-- Jobs table -->
                <div class="rounded-lg overflow-hidden border border-slate-200" style="max-height: 300px; overflow-y: auto;">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0">
                            <tr class="bg-slate-100">
                                <th class="text-left px-3 py-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">#</th>
                                <th class="text-left px-3 py-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Job Title</th>
                                <th class="text-right px-3 py-2 text-[11px] font-semibold text-green-700 uppercase tracking-wide bg-green-50">Count</th>
                            </tr>
                        </thead>
                        <tbody id="summaryModalTableBody"></tbody>
                    </table>
                </div>

                <!-- Stats strip -->
                <div class="flex gap-3 mt-3">
                    <div class="flex-1 bg-white border border-slate-200 rounded-lg px-3 py-2 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Total Job Titles</p>
                        <p id="summaryModalCount" class="text-lg font-bold text-slate-800"></p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button 
                    onclick="closeApproveSummaryModal()"
                    class="flex-1 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium border border-gray-300 rounded-lg transition"
                >
                    Cancel
                </button>
                <button 
                    onclick="confirmApprove()"
                    class="flex-1 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition"
                >
                    Confirm Approval
                </button>
            </div>
        </div>
    </div>

    <!-- APPROVE SUCCESS MODAL -->
    <div id="approveSuccessModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- Blur Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeApproveSuccessModal()"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Successfully Approved!</h3>
                <p class="text-sm text-slate-600 mb-6">
                    Job titles have been approved successfully and added to the database.
                </p>
                <button 
                    onclick="closeApproveSuccessModal()"
                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium"
                >
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- REJECT MODAL -->
    <div id="rejectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- Blur Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeRejectModal()"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Reject Submission</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to reject this submission? The admin will be notified.</p>
                <div class="flex gap-3 w-full">
                    <button onclick="closeRejectModal()" class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition text-base">Cancel</button>
                    <button onclick="confirmReject()" class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition text-base">Yes, Reject</button>
                </div>
            </div>
        </div>
    </div>

    <!-- REJECT SUCCESS MODAL -->
    <div id="rejectSuccessModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- Blur Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeRejectSuccessModal()"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Submission Rejected</h3>
                <p class="text-sm text-slate-600 mb-6">
                    The job titles submission has been rejected.
                </p>
                <button 
                    onclick="closeRejectSuccessModal()"
                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium"
                >
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- ERROR MODAL -->
    <div id="errorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- Blur Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeErrorModal()"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Error</h3>
                <p id="errorMessage" class="text-sm text-slate-600 mb-6">
                    An error occurred. Please try again.
                </p>
                <button 
                    onclick="closeErrorModal()"
                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium"
                >
                    Close
                </button>
            </div>
        </div>
    </div>


    <!-- TOAST NOTIFICATION -->
    <div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none" style="min-width: 340px;"></div>

    <!-- ─── Live Polling — detect new pending job title submissions every 30s ─── -->

</body>
</html>