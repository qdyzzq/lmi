<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
            grid-template-columns: 32px 1fr 180px 36px;
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
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Job Titles Form • Admin</h2>
            <div class="flex items-center gap-4">
                <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200">
                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Region XI • 2024
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <div class="flex-1 overflow-auto p-8">
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
                        >
                    </div>

                    <!-- Job entries -->
                    <p class="text-[10px] font-700 uppercase tracking-widest text-slate-700 font-bold mb-3">Job Entries</p>
                    <div id="jobEntries" class="space-y-3 mb-4" style="max-height: 350px; overflow-y: auto; padding-right: 4px;">
                        <!-- entries injected here -->
                    </div>

                    <!-- Add button -->
                    <button type="button" onclick="addJobEntry()" class="jt-add-btn mb-8">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Job Title
                    </button>

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
                                <th class="text-right px-3 py-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Count</th>
                            </tr>
                        </thead>
                        <tbody id="confirmSummaryTableBody">
                            <!-- rows injected by JS -->
                        </tbody>
                        <tfoot>
                            <tr class="bg-blue-50 border-t border-slate-200">
                                <td colspan="2" class="px-3 py-2 text-xs font-bold text-blue-700">Total Employment</td>
                                <td id="confirmSummaryTotal" class="px-3 py-2 text-xs font-bold text-blue-700 text-right"></td>
                            </tr>
                        </tfoot>
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
                                <th class="text-right px-3 py-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Count</th>
                            </tr>
                        </thead>
                        <tbody id="summaryTableBody">
                            <!-- rows injected by JS -->
                        </tbody>
                        <tfoot>
                            <tr class="bg-blue-50 border-t border-slate-200">
                                <td colspan="2" class="px-3 py-2 text-xs font-bold text-blue-700">Total Employment</td>
                                <td id="summaryTotal" class="px-3 py-2 text-xs font-bold text-blue-700 text-right"></td>
                            </tr>
                        </tfoot>
                    </table>
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
                <button
                    type="button"
                    onclick="removeJobEntry(${entryCount})"
                    title="Remove"
                    class="jt-remove-btn"
                >
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
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
            addJobEntry();
        }

        function showConfirmModal(data) {
            pendingData = data;

            // Populate year
            document.getElementById('confirmSummaryYear').textContent = data.year;

            // Populate table rows
            const tbody = document.getElementById('confirmSummaryTableBody');
            tbody.innerHTML = '';
            let total = 0;
            data.jobs.forEach((job, i) => {
                total += job.count;
                const row = document.createElement('tr');
                row.className = i % 2 === 0 ? 'bg-white' : 'bg-slate-50/60';
                row.innerHTML = `
                    <td class="px-3 py-2 text-xs text-slate-400 font-semibold">${i + 1}</td>
                    <td class="px-3 py-2 text-xs text-slate-700">${job.title}</td>
                    <td class="px-3 py-2 text-xs text-slate-700 text-right font-medium">${job.count.toLocaleString()}</td>
                `;
                tbody.appendChild(row);
            });
            document.getElementById('confirmSummaryTotal').textContent = total.toLocaleString();

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
            let total = 0;
            if (data && data.jobs) {
                data.jobs.forEach((job, i) => {
                    total += job.count;
                    const row = document.createElement('tr');
                    row.className = i % 2 === 0 ? 'bg-white' : 'bg-slate-50/60';
                    row.innerHTML = `
                        <td class="px-3 py-2 text-xs text-slate-400 font-semibold">${i + 1}</td>
                        <td class="px-3 py-2 text-xs text-slate-700">${job.title}</td>
                        <td class="px-3 py-2 text-xs text-slate-700 text-right font-medium">${job.count.toLocaleString()}</td>
                    `;
                    tbody.appendChild(row);
                });
            }
            document.getElementById('summaryTotal').textContent = total.toLocaleString();

            document.getElementById('successModal').classList.remove('hidden');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
            // Reset form
            document.getElementById('jobEntries').innerHTML = '';
            document.getElementById('year').value = '';
            entryCount = 0;
            addJobEntry();
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
        document.getElementById('jobTitlesForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const year = document.getElementById('year').value;
            const titles = document.querySelectorAll('input[name="jobTitle[]"]');
            const counts = document.querySelectorAll('input[name="jobCount[]"]');
            
            const jobData = [];
            
            for (let i = 0; i < titles.length; i++) {
                if (titles[i].value && counts[i].value) {
                    jobData.push({
                        title: titles[i].value,
                        count: parseInt(counts[i].value.replace(/,/g, ''))
                    });
                }
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

        // Add initial entry when page loads
        addJobEntry();
    </script>

    <!-- TOAST NOTIFICATION -->
    <div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none" style="min-width: 340px;"></div>

</body>
</html>