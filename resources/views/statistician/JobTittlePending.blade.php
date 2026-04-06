<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
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

        /* ---------- DELETE ROW BUTTON ---------- */
        .btn-delete-row {
            width: 30px;
            height: 30px;
            background: #fff0f0;
            border: 1.5px solid #fecaca;
            border-radius: 6px;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #ef4444;
            transition: background 0.15s, border-color 0.15s;
            flex-shrink: 0;
            margin-left: 8px;
        }
        .btn-delete-row:hover { background: #fef2f2; border-color: #f87171; }
        .edit-mode .btn-delete-row { display: flex; }

        /* ---------- ADD ROW BUTTON ---------- */
        .btn-add-row {
            display: none;
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
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 12px;
        }
        .btn-add-row:hover { background: #dcfce7; border-color: #4ade80; }
        .btn-add-row.visible { display: flex; }
    </style>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden" >
   @include('partials.statisticianSidebar')
    <!-- MAIN -->
    <div id="mainContent" class="flex-1 flex flex-col overflow-hidden transition-all duration-300">
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Job Title Pending • Statistician</h2>
            <div class="flex items-center gap-4">
                <div class="bg-yellow-100 px-4 py-2 rounded-lg text-sm font-medium text-yellow-700 border border-yellow-300">
                    <span id="pending-badge-count" class="font-bold">{{ $submissions->count() }}</span> Pending
                </div>
                <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200"><svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Region XI • 2024</div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
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
                                                <th class="w-12"></th>
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
                                                    <!-- DELETE CELL -->
                                                    <td class="py-5 px-2">
                                                        <button class="btn-delete-row" onclick="deleteRow('{{ $job->id }}', {{ $year }})" title="Remove">
                                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                                            <tr>
                                                <td class="py-5 px-6 font-bold text-lg">Total Entries</td>
                                                <td class="py-5 px-6 text-right font-bold text-lg" id="totalCount-{{ $year }}">{{ $jobs->count() }} jobs</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <!-- Add Job Title button (only visible in edit mode) -->
                                    <button class="btn-add-row" id="addRowBtn-{{ $year }}" onclick="addNewRow({{ $year }})">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Job Title
                                    </button>
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

    <script>
        let selectedYear = null;
        const edits = {};
        // Tracks which year cards currently have edit mode ON
        const editModeActive = {};

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

        // -------- TOGGLE EDIT MODE (highlights all rows) --------
        // Snapshot: { jobId: { title, count } } taken when edit mode opens
        const snapshots = {};

        function toggleEditMode(year) {
            const card      = document.getElementById(`card-${year}`);
            const btn       = document.getElementById(`editBtn-${year}`);
            const btnText   = document.getElementById(`editBtnText-${year}`);
            const cancelBtn = document.getElementById(`cancelEditBtn-${year}`);
            const addBtn    = document.getElementById(`addRowBtn-${year}`);

            // If turning OFF edit mode, auto-save any unsaved new rows first
            if (editModeActive[year]) {
                const pendingNewRows = card.querySelectorAll('[id^="row-new-"]');
                pendingNewRows.forEach(row => {
                    const tempId = row.id.replace('row-', '');
                    const titleInput = document.getElementById(`title-input-${tempId}`);
                    const countInput = document.getElementById(`count-input-${tempId}`);
                    if (titleInput && countInput && titleInput.value.trim() && countInput.value) {
                        saveNewRow(tempId, year);
                    } else if (row.parentNode) {
                        row.remove();
                    }
                });
                // Clear snapshot when done
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
            btnText.textContent = editModeActive[year] ? 'Done' : 'Edit';
            addBtn.classList.toggle('visible', editModeActive[year]);
            cancelBtn.style.display = editModeActive[year] ? 'inline-flex' : 'none';
        }

        // -------- CANCEL EDIT MODE — revert all changes --------
        function cancelEditMode(year) {
            const card      = document.getElementById(`card-${year}`);
            const btn       = document.getElementById(`editBtn-${year}`);
            const btnText   = document.getElementById(`editBtnText-${year}`);
            const cancelBtn = document.getElementById(`cancelEditBtn-${year}`);
            const addBtn    = document.getElementById(`addRowBtn-${year}`);
            const tbody     = document.getElementById(`tbody-${year}`);

            const snap = snapshots[year] || {};

            // Remove any newly added rows (they have tempIds starting with "new-")
            tbody.querySelectorAll('[id^="row-new-"]').forEach(r => r.remove());

            // Restore deleted rows and revert edits using snapshot
            card.querySelectorAll('.editable-row').forEach(row => {
                const id = row.id.replace('row-', '');
                if (snap[id]) {
                    // Restore title
                    const titleEl = document.getElementById(`title-text-${id}`);
                    if (titleEl) titleEl.textContent = snap[id].title;
                    const titleInput = document.getElementById(`title-input-${id}`);
                    if (titleInput) titleInput.value = snap[id].title;
                    const titleBadge = document.getElementById(`title-badge-${id}`);
                    if (titleBadge) titleBadge.style.display = 'none';

                    // Restore count
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
            editModeActive[year] = false;
            btn.classList.remove('active');
            btnText.textContent = 'Edit';
            addBtn.classList.remove('visible');
            cancelBtn.style.display = 'none';

            delete snapshots[year];
        }

        // -------- DELETE ROW --------
        function deleteRow(jobId, year) {
            const row = document.getElementById(`row-${jobId}`);
            if (!row) return;
            row.remove();

            // Track deletion (mark as deleted in edits)
            if (!edits[jobId]) edits[jobId] = {};
            edits[jobId]._delete = true;

            updateTotalCount(year);
            document.getElementById(`banner-${year}`).classList.add('visible');
        }

        // -------- ADD NEW ROW --------
        let newRowCounter = 0;
        function addNewRow(year) {
            newRowCounter++;
            const tempId = `new-${year}-${newRowCounter}`;
            const tbody  = document.getElementById(`tbody-${year}`);

            const tr = document.createElement('tr');
            tr.className = 'border-b border-gray-100 editable-row edit-mode';
            tr.id = `row-${tempId}`;
            tr.innerHTML = `
                <td class="py-4 px-6 text-lg">
                    <div class="inline-actions" id="title-edit-${tempId}" style="display:flex;">
                        <input type="text" class="inline-input" id="title-input-${tempId}"
                               placeholder="e.g. Customer Service Rep"
                               onkeydown="handleNewRowKeydown(event, '${tempId}', ${year})">
                        <button class="btn-save-inline" onclick="saveNewRow('${tempId}', ${year})" title="Save">
                            <svg width="16" height="16" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7l3.5 3.5L12 4"/></svg>
                        </button>
                        <button class="btn-cancel-inline" onclick="cancelNewRow('${tempId}')" title="Cancel">
                            <svg width="16" height="16" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M3 3l8 8M11 3l-8 8"/></svg>
                        </button>
                    </div>
                </td>
                <td class="py-4 px-6 text-right text-lg">
                    <div class="inline-actions justify-end" id="count-edit-${tempId}" style="display:flex;">
                        <input type="text" inputmode="numeric" class="inline-input" id="count-input-${tempId}"
                               placeholder="e.g. 1,000" style="width:160px;"
                               onkeydown="handleNewRowKeydown(event, '${tempId}', ${year})"
                               oninput="formatCountInput(this)" onfocus="stripCommas(this)" onblur="reformatOnBlur(this)">
                    </div>
                </td>
                <td class="py-4 px-2">
                    <button class="btn-delete-row" style="display:flex;" onclick="cancelNewRow('${tempId}')" title="Remove">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            document.getElementById(`title-input-${tempId}`).focus();
        }

        function saveNewRow(tempId, year) {
            const titleInput = document.getElementById(`title-input-${tempId}`);
            const countInput = document.getElementById(`count-input-${tempId}`);

            const title = titleInput.value.trim();
            const count = parseInt(countInput.value.replace(/,/g, ''));

            if (!title) { titleInput.classList.add('error'); titleInput.focus(); return; }
            if (!countInput.value || isNaN(count) || count < 0) { countInput.classList.add('error'); countInput.focus(); return; }

            // Replace the editing row with a proper display row
            const row = document.getElementById(`row-${tempId}`);
            row.innerHTML = `
                <td class="py-5 px-6 text-lg">
                    <div class="cell-editable edit-mode" id="title-display-${tempId}" onclick="startEdit('${tempId}', 'title')">
                        <span id="title-text-${tempId}">${title}</span>
                        <span class="edit-hint">click to edit</span>
                        <span class="edited-badge" id="title-badge-${tempId}" style="display:inline-block;">new</span>
                    </div>
                    <div style="display:none;" id="title-edit-${tempId}" class="inline-actions">
                        <input type="text" class="inline-input" id="title-input-${tempId}" value="${title}" onkeydown="handleKeydown(event, '${tempId}', 'title')">
                        <button class="btn-save-inline" onclick="saveEdit('${tempId}', 'title')" title="Save">
                            <svg width="16" height="16" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7l3.5 3.5L12 4"/></svg>
                        </button>
                        <button class="btn-cancel-inline" onclick="cancelEdit('${tempId}', 'title')" title="Cancel">
                            <svg width="16" height="16" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M3 3l8 8M11 3l-8 8"/></svg>
                        </button>
                    </div>
                </td>
                <td class="py-5 px-6 text-right text-lg">
                    <div class="cell-editable flex justify-end items-center gap-2 edit-mode" id="count-display-${tempId}" onclick="startEdit('${tempId}', 'count')">
                        <span class="edit-hint">click to edit</span>
                        <span id="count-text-${tempId}" class="font-medium">${count.toLocaleString()}</span>
                        <span class="edited-badge" id="count-badge-${tempId}">edited</span>
                    </div>
                    <div style="display:none;" id="count-edit-${tempId}" class="inline-actions justify-end">
                        <input type="text" inputmode="numeric" class="inline-input" id="count-input-${tempId}" value="${count.toLocaleString()}" style="width:160px;" onkeydown="handleKeydown(event, '${tempId}', 'count')" oninput="formatCountInput(this)" onfocus="stripCommas(this)" onblur="reformatOnBlur(this)">
                        <button class="btn-save-inline" onclick="saveEdit('${tempId}', 'count')" title="Save">
                            <svg width="16" height="16" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7l3.5 3.5L12 4"/></svg>
                        </button>
                        <button class="btn-cancel-inline" onclick="cancelEdit('${tempId}', 'count')" title="Cancel">
                            <svg width="16" height="16" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M3 3l8 8M11 3l-8 8"/></svg>
                        </button>
                    </div>
                </td>
                <td class="py-5 px-2">
                    <button class="btn-delete-row" onclick="deleteRow('${tempId}', ${year})" title="Remove">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </td>
            `;

            // Track as new entry
            edits[tempId] = { _new: true, title, count };
            updateTotalCount(year);
            document.getElementById(`banner-${year}`).classList.add('visible');
        }

        function cancelNewRow(tempId) {
            const row = document.getElementById(`row-${tempId}`);
            if (row) row.remove();
        }

        function handleNewRowKeydown(event, tempId, year) {
            if (event.key === 'Enter')  { event.preventDefault(); saveNewRow(tempId, year); }
            if (event.key === 'Escape') { cancelNewRow(tempId); }
        }

        function updateTotalCount(year) {
            const tbody = document.getElementById(`tbody-${year}`);
            const count = tbody ? tbody.querySelectorAll('.editable-row').length : 0;
            const el = document.getElementById(`totalCount-${year}`);
            if (el) el.textContent = `${count} job${count !== 1 ? 's' : ''}`;
        }

        // -------- INLINE EDIT --------
        function startEdit(jobId, field) {
            // Only allow if edit mode is on for this row's card
            const row  = document.getElementById(`row-${jobId}`);
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

            // Restore value — keep commas for display in the input when not focused
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

            let newValue = input.value.trim().replace(/,/g, ''); // strip commas before parsing

            // Validation
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

            // Update displayed text
            textEl.textContent = (field === 'count') ? Number(newValue).toLocaleString() : newValue;

            // Track edit
            if (!edits[jobId]) edits[jobId] = {};
            edits[jobId][field] = (field === 'count') ? parseInt(newValue) : newValue;

            // Show badge
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

        // -------- APPROVE MODALS --------

        // Step 1 – Summary modal shown immediately when Approve is clicked
        function openApproveSummaryModal(year) {
            selectedYear = year;

            const card = document.getElementById(`card-${selectedYear}`);

            // Year
            document.getElementById('summaryModalYear').textContent = selectedYear;

            // Submitter — grab from the card header paragraph
            const submitterEl = card.querySelector('p.text-base.opacity-90');
            const submitterText = submitterEl
                ? submitterEl.textContent.replace('Submitted by:', '').trim()
                : 'Unknown';
            document.getElementById('summaryModalSubmitter').textContent = submitterText;

            // Build table from live DOM (reflects any inline edits)
            const tbody = document.getElementById('summaryModalTableBody');
            tbody.innerHTML = '';
            let total = 0;
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

            // Sort by count descending
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

        // Step 2 – API call, then show success modal
        async function confirmApprove() {
            closeApproveSummaryModal();

            try {
                const response = await fetch(`/statistician/job-titles/${selectedYear}/approve`, {
                    method: 'POST',
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

        // -------- REJECT MODALS --------
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
                    method: 'POST',
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

        // -------- ERROR MODAL --------
        function closeErrorModal() {
            document.getElementById('errorModal').classList.add('hidden');
        }

        // -------- COMMA FORMATTING FOR COUNT INPUTS --------
        function formatCountInput(el) {
            const raw = el.value.replace(/[^0-9]/g, '');
            el.value = raw === '' ? '' : parseInt(raw).toLocaleString();
        }
        function stripCommas(el) {
            el.value = el.value.replace(/,/g, '');
        }
        function reformatOnBlur(el) {
            const raw = parseInt(el.value.replace(/[^0-9]/g, ''));
            el.value = isNaN(raw) ? '' : raw.toLocaleString();
        }
    </script>

    <!-- TOAST NOTIFICATION -->
    <div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none" style="min-width: 340px;"></div>

    <!-- ─── Live Polling — detect new pending job title submissions every 30s ─── -->
    <script>
    (function () {
        let knownPending    = parseInt('{{ $submissions->count() }}');
        const POLL_INTERVAL  = 30_000;
        let accumulatedNew   = 0;
        let notifToast       = null;

        function fetchCounts() {
            fetch('{{ route("statistician.job-titles.pending-count") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                if (!data) return;
                const newPending = parseInt(data.pending ?? 0);

                // Update header badge live
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

            // Click toast body → reload page
            notifToast.addEventListener('click', function (e) {
                if (e.target.closest('.notif-dismiss')) return;
                dismissNotifToast();
                window.location.reload();
            });

            // Dismiss button → close only
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
                notifToast = null;
                accumulatedNew = 0;
            }, 300);
        }

        setInterval(fetchCounts, POLL_INTERVAL);
    })();
    </script>

</body>
</html>