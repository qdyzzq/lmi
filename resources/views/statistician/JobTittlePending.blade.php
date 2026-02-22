<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
            <h2 class="text-xl font-bold text-slate-800">Job Title Pending • Statistician</h2>
            <div class="flex items-center gap-4">
                <div class="bg-yellow-100 px-4 py-2 rounded-lg text-sm font-medium text-yellow-700 border border-yellow-300">
                    <span id="pending-badge-count" class="font-bold">{{ $submissions->count() }}</span> Pending
                </div>
                <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200">📅 Region XI • 2024</div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>

        <div class="flex-1 overflow-auto">
            <div class="bg-gray-100 py-10 px-4">
                <div class="max-w-6xl mx-auto">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Pending Job Title Submissions</h1>
                    <p class="text-base text-gray-500 mb-8">Review submissions from admins. Use the edit button to correct values before approving.</p>

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
                                    <button class="btn-edit-mode" id="editBtn-{{ $year }}" onclick="toggleEditMode({{ $year }})">
                                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11.5 1.5l3 3L5.5 13H2.5v-3L11.5 1.5z"/>
                                        </svg>
                                        <span id="editBtnText-{{ $year }}">Edit</span>
                                    </button>
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
                                        <tbody>
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
                                                            <input type="number" min="0" class="inline-input" id="count-input-{{ $job->id }}" value="{{ $job->count }}" style="width:160px;" onkeydown="handleKeydown(event, '{{ $job->id }}', 'count')">
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
                                                <td class="py-5 px-6 text-right font-bold text-lg">{{ $jobs->count() }} jobs</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <!-- FOOTER: Approve + Reject bottom-right -->
                                <div class="card-footer">
                                    <button class="btn-approve" onclick="showApproveModal({{ $year }})">
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

    <!-- APPROVE CONFIRMATION MODAL -->
    <div id="approveConfirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- Blur Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeApproveConfirmModal()"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Confirm Approval</h3>
                <p class="text-sm text-slate-600 mb-6">
                    Are you sure you want to approve all job titles for year <strong id="approveYear"></strong>? This action will move the data to the main database.
                </p>
                <div class="flex gap-3 w-full">
                    <button 
                        onclick="closeApproveConfirmModal()"
                        class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition font-medium"
                    >
                        Cancel
                    </button>
                    <button 
                        onclick="confirmApprove()"
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium"
                    >
                        Yes, Approve
                    </button>
                </div>
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
        <div class="relative bg-white rounded-xl shadow-2xl p-8 max-w-lg w-full mx-4">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Reject Submission</h3>
            <p class="text-base text-gray-600 mb-4">Please provide a reason for rejection:</p>
            <textarea id="rejectionReason" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-md text-base focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Enter rejection reason..."></textarea>
            <div class="flex gap-3 mt-6">
                <button onclick="closeRejectModal()" class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition text-base">Cancel</button>
                <button onclick="confirmReject()" class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition text-base">Reject</button>
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
                    The job titles submission has been rejected and the admin has been notified.
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
        function toggleEditMode(year) {
            const card    = document.getElementById(`card-${year}`);
            const btn     = document.getElementById(`editBtn-${year}`);
            const btnText = document.getElementById(`editBtnText-${year}`);
            const rows    = card.querySelectorAll('.editable-row');

            editModeActive[year] = !editModeActive[year];

            rows.forEach(row => {
                row.classList.toggle('edit-mode', editModeActive[year]);
            });

            btn.classList.toggle('active', editModeActive[year]);
            btnText.textContent = editModeActive[year] ? 'Done' : 'Edit';
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

            input.value = textEl.textContent.replace(/,/g, '');
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

            let newValue = input.value.trim();

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
        function showApproveModal(year) {
            selectedYear = year;
            document.getElementById('approveYear').textContent = year;
            document.getElementById('approveConfirmModal').classList.remove('hidden');
        }

        function closeApproveConfirmModal() {
    document.getElementById('approveConfirmModal').classList.add('hidden');
    // Don't reset selectedYear yet, we need it for confirmApprove
}

        async function confirmApprove() {
            closeApproveConfirmModal();

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
    selectedYear = null; // ✅ Reset here instead
    location.reload();
}

        // -------- REJECT MODALS --------
        function showRejectModal(year) {
            selectedYear = year;
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectionReason').value = '';
        }

        function closeRejectModal() {
            selectedYear = null;
            document.getElementById('rejectModal').classList.add('hidden');
        }

        async function confirmReject() {
            const reason = document.getElementById('rejectionReason').value.trim();
            if (!reason) { 
                showToast('Please provide a reason for rejection', 'warning'); 
                return; 
            }

            const yearToReject = selectedYear; // ✅ capture before closeRejectModal nulls it

            closeRejectModal();

            try {
                const response = await fetch(`/statistician/job-titles/${yearToReject}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ reason })
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
            const msgText   = `🔔 ${accumulatedNew} new job title submission${accumulatedNew > 1 ? 's' : ''} — click to refresh`;
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