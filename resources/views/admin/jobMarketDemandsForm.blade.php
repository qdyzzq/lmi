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
        #mainContent input:not([readonly]),
        #mainContent select {
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 13.5px;
            color: #0f172a;
            background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
            width: 100%;
        }
        #mainContent input:not([readonly]):focus,
        #mainContent select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        #mainContent input[readonly] {
            font-size: 13px;
            font-weight: 600;
            border: 1.5px solid #bbf7d0;
            border-radius: 8px;
            padding: 9px 12px;
            background: #f0fdf4;
            color: #166534;
            width: 100%;
            cursor: not-allowed;
        }
        .lm-card {
            position: relative;
            overflow: hidden;
        }
        .lm-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #2563eb, #0d9488);
        }
        .lm-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #334155;
            margin-bottom: 14px;
        }
        .lm-label {
            font-size: 12.5px;
            font-weight: 600;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
        }
        .lm-label .lm-formula {
            font-size: 10px;
            font-weight: 500;
            color: #334155;
            background: #f1f5f9;
            padding: 1px 6px;
            border-radius: 4px;
        }
        .lm-label .lm-auto-badge {
            font-size: 10px;
            font-weight: 500;
            color: #0d9488;
            background: #f0fdfa;
            border: 1px solid #99f6e4;
            border-radius: 4px;
            padding: 1px 5px;
        }
        .lm-period-divider {
            border: none;
            border-top: 1px dashed #e2e8f0;
            margin: 20px 0 24px;
        }
        .lm-actions-divider {
            border: none;
            border-top: 1px solid #f1f5f9;
            margin: 24px 0 0;
        }
        #resetBtn {
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
        #resetBtn:hover { background: #f8fafc; border-color: #cbd5e1; }
        #saveBtn {
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
            transition: background 0.15s, box-shadow 0.15s, transform 0.15s;
        }
        #saveBtn:hover {
            background: #1d4ed8;
            box-shadow: 0 2px 6px rgba(37,99,235,0.35), 0 6px 16px rgba(37,99,235,0.25);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden" >
    @include('partials.sidebar')

    <div id="mainContent" class="flex-1 flex flex-col overflow-hidden transition-all duration-300">
        <!-- HEADER -->
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Job Market Overview • Admin</h2>
            <div class="flex items-center gap-4">
                <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200">
                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Region XI • 2024
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto p-8 bg-slate-100">
            <div class="lm-card max-w-5xl mx-auto bg-white rounded-xl shadow p-8">

                <!-- Card title with icon -->
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5h4.5v7.5H3zM9.75 9h4.5v12h-4.5zM16.5 4.5H21V21h-4.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-[15px] font-bold text-slate-800 leading-tight">Labor Market Form</h3>
                        <p class="text-[11px] text-slate-700 font-medium mt-0.5">Regional Statistics Input — Region XI</p>
                    </div>
                </div>

                <!-- Year and Month Section -->
                <p class="lm-section-label">Reporting Period</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="lm-label">Year <span class="text-red-500">*</span></p>
                        <input
                            type="text"
                            id="year"
                            maxlength="4"
                            value="2024"
                            placeholder="e.g. 2026"
                            required
                        >
                    </div>
                    <div>
                        <p class="lm-label">Month <span class="text-red-500">*</span></p>
                        <select id="month" required>
                            <option value="">Select Month</option>
                            <option value="1">January</option>
                            <option value="4">April</option>
                            <option value="7">July</option>
                            <option value="10">October</option>
                        </select>
                    </div>
                </div>

                <hr class="lm-period-divider">

                <!-- Two Column Layout -->
                <p class="lm-section-label">Labor Market Indicators</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- LEFT COLUMN -->
                    <div class="space-y-6">
                        <!-- Household Population -->
                        <div>
                            <p class="lm-label">Household Population <span style="font-size:11px;font-weight:600;color:#334155">(15 yrs old and over)</span> <span class="text-red-500">*</span></p>
                            <input
                                type="number"
                                id="householdPopulation"
                                placeholder="e.g. 3182"
                                step="0.001"
                            >
                        </div>

                        <!-- Labor Force Participation Rate -->
                        <div>
                            <p class="lm-label">Labor Force Participation Rate <span style="font-size:11px;font-weight:600;color:#334155">(%)</span> <span class="text-red-500">*</span></p>
                            <input
                                type="number"
                                id="lfpr"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="e.g. 65.2"
                            >
                        </div>

                        <!-- Employment Rate -->
                        <div>
                            <p class="lm-label">Employment Rate <span style="font-size:11px;font-weight:600;color:#334155">(%)</span> <span class="text-red-500">*</span></p>
                            <input
                                type="number"
                                id="employmentrate"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="e.g. 93.9"
                            >
                        </div>

                        <!-- Underemployment Rate -->
                        <div>
                            <p class="lm-label">Underemployment Rate <span style="font-size:11px;font-weight:600;color:#334155">(%)</span> <span class="text-red-500">*</span></p>
                            <input
                                type="number"
                                id="underemploymentrate"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="e.g. 19.3"
                            >
                        </div>

                        <!-- Unemployment Rate -->
                        <div>
                            <p class="lm-label">Unemployment Rate <span style="font-size:11px;font-weight:600;color:#334155">(%)</span> <span class="text-red-500">*</span></p>
                            <input
                                type="number"
                                id="unemploymentrate"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="e.g. 6.1"
                            >
                        </div>
                    </div>

                    <!-- RIGHT COLUMN -->
                    <div class="space-y-6">
                        <!-- Labor Force (Auto) -->
                        <div class="mt-[34px]">
                            <p class="lm-label">
                                Labor Force
                                <span class="lm-auto-badge">auto</span>
                                <span class="lm-formula">HOP × LFPR</span>
                            </p>
                            <input type="number" id="laborForce" readonly>
                        </div>

                        <!-- Employed (Auto) -->
                        <div class="mt-[34px]">
                            <p class="lm-label">
                                Employed
                                <span class="lm-auto-badge">auto</span>
                                <span class="lm-formula">EMPR × LF</span>
                            </p>
                            <input type="number" id="employed" readonly>
                        </div>

                        <!-- Underemployed (Auto) -->
                        <div class="mt-[34px]">
                            <p class="lm-label">
                                Underemployed
                                <span class="lm-auto-badge">auto</span>
                                <span class="lm-formula">EMP × UEMP</span>
                            </p>
                            <input type="number" id="underemployed" readonly>
                        </div>

                        <!-- Unemployed (Auto) -->
                        <div class="mt-[34px]">
                            <p class="lm-label">
                                Unemployed
                                <span class="lm-auto-badge">auto</span>
                                <span class="lm-formula">LF × UEMPR</span>
                            </p>
                            <input type="number" id="unemployed" readonly>
                        </div>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <hr class="lm-actions-divider">
                <div class="mt-5 flex justify-end gap-3">
                    <button id="resetBtn">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </button>
                    <button id="saveBtn">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Data
                    </button>
                </div>
            </div>
        </main>
    </div>

    <!-- Confirmation Modal with Data Summary -->
    <div id="confirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- Blur Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="document.getElementById('confirmModal').classList.add('hidden')"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col">
            <!-- Sticky Header -->
            <div class="px-7 pt-7 pb-5 border-b border-slate-100 shrink-0">
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-[16px] font-bold text-slate-800 leading-tight">Review Before Submitting</h3>
                        <p class="text-[11.5px] text-slate-500 mt-0.5">Please verify the data below before sending to the pending queue.</p>
                    </div>
                </div>
            </div>

            <!-- Scrollable Body -->
            <div class="overflow-y-auto px-7 py-5 flex-1">

                <!-- Reporting Period Banner -->
                <div class="flex items-center gap-3 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl px-4 py-3 mb-5">
                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <p class="text-[11px] font-700 text-blue-500 uppercase tracking-wide leading-none mb-0.5">Reporting Period</p>
                        <p class="text-[14px] font-bold text-blue-800" id="summaryPeriod">—</p>
                    </div>
                </div>

                <!-- Summary Table -->
                <p class="text-[11px] font-700 uppercase tracking-widest text-slate-400 mb-3">Labor Market Indicators</p>
                <div class="space-y-2" id="summaryRows">
                    <!-- rows injected by JS -->
                </div>

                <!-- Info note -->
                <div class="mt-5 flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2.5">
                    <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-[12px] text-amber-800 leading-snug">This data will be sent to the <strong>pending queue</strong>. A statistician will review and verify it before posting to the database.</p>
                </div>
            </div>

            <!-- Sticky Footer -->
            <div class="px-7 py-5 border-t border-slate-100 flex gap-3 shrink-0">
                <button 
                    id="cancelBtn"
                    class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition font-semibold text-sm"
                >
                    Cancel
                </button>
                <button 
                    id="confirmBtn"
                    class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-sm flex items-center justify-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Yes, Submit
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal with Blur Backdrop -->
    <div id="successModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- Blur Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="document.getElementById('successModal').classList.add('hidden')"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Successfully Submitted!</h3>
                <p class="text-sm text-slate-600 mb-6">
                    Your data has been successfully submitted to the pending queue. It will be reviewed by a statistician before being posted to the database.
                </p>
                <button 
                    id="closeModalBtn"
                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                >
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Error Modal with Blur Backdrop -->
    <div id="errorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- Blur Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="document.getElementById('errorModal').classList.add('hidden')"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Error</h3>
                <p id="errorMessage" class="text-sm text-slate-600 mb-6">
                    An error occurred. Please try again.
                </p>
                <button 
                    id="closeErrorBtn"
                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium"
                >
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Reset Confirmation Modal -->
    <div id="resetModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeResetModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-orange-100 mb-4">
                    <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Reset Form?</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to reset the form? All entered data will be lost and cannot be recovered.</p>
                <div class="flex gap-3">
                    <button onclick="closeResetModal()" class="flex-1 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium border border-gray-300 rounded-lg transition">
                        Cancel
                    </button>
                    <button onclick="doReset()" class="flex-1 px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition">
                        Yes, Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
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

    document.addEventListener('DOMContentLoaded', function() {
        const yearInput = document.getElementById('year');
        const monthInput = document.getElementById('month');
        const householdInput = document.getElementById('householdPopulation');
        const lfprInput = document.getElementById('lfpr');
        const employmentRateInput = document.getElementById('employmentrate');
        const underemploymentRateInput = document.getElementById('underemploymentrate');
        const unemploymentRateInput = document.getElementById('unemploymentrate');
        const laborForceInput = document.getElementById('laborForce');
        const employedInput = document.getElementById('employed');
        const underemployedInput = document.getElementById('underemployed');
        const unemployedInput = document.getElementById('unemployed');
        const resetBtn = document.getElementById('resetBtn');
        const saveBtn = document.getElementById('saveBtn');

        function normalizeRate(rate) {
            if (rate > 1) {
                return rate / 100;
            }
            return rate;
        }

        function calculateLaborForce() {
            const household = parseFloat(householdInput.value);
            const lfpr = parseFloat(lfprInput.value);

            if (isNaN(household) || isNaN(lfpr)) {
                laborForceInput.value = '';
                employedInput.value = '';
                underemployedInput.value = '';
                return;
            }

            const normalizedLfpr = normalizeRate(lfpr);
            const laborForce = household * normalizedLfpr;
            laborForceInput.value = Math.round(laborForce);
            
            calculateEmployed();
            calculateUnemployed();
        }

        function calculateEmployed() {
            const laborForce = parseFloat(laborForceInput.value);
            const employmentRate = parseFloat(employmentRateInput.value);

            if (isNaN(laborForce) || isNaN(employmentRate)) {
                employedInput.value = '';
                return;
            }

            const normalizedRate = normalizeRate(employmentRate);
            const employed = laborForce * normalizedRate;
            employedInput.value = Math.round(employed);
            
            calculateUnderemployed();
        }

        function calculateUnderemployed() {
            const employed = parseFloat(employedInput.value);
            const underemploymentRate = parseFloat(underemploymentRateInput.value);

            if (isNaN(employed) || isNaN(underemploymentRate)) {
                underemployedInput.value = '';
                return;
            }

            const normalizedRate = normalizeRate(underemploymentRate);
            const underemployed = employed * normalizedRate;
            underemployedInput.value = Math.round(underemployed);
        }

        function calculateUnemployed() {
            const laborForce = parseFloat(laborForceInput.value);
            const unemploymentRate = parseFloat(unemploymentRateInput.value);

            if (isNaN(laborForce) || isNaN(unemploymentRate)) {
                unemployedInput.value = '';
                return;
            }

            const normalizedRate = normalizeRate(unemploymentRate);
            const unemployed = laborForce * normalizedRate;
            unemployedInput.value = Math.round(unemployed);
        }

        householdInput.addEventListener('input', calculateLaborForce);
        lfprInput.addEventListener('input', calculateLaborForce);
        employmentRateInput.addEventListener('input', calculateEmployed);
        underemploymentRateInput.addEventListener('input', calculateUnderemployed);
        unemploymentRateInput.addEventListener('input', calculateUnemployed);

        // Block special characters on year input (digits only, max 4)
        yearInput.addEventListener('keydown', function(e) {
            const allowed = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'];
            if (!allowed.includes(e.key) && !/^\d$/.test(e.key)) {
                e.preventDefault();
            }
            // Block if already 4 digits and not a control key
            if (this.value.length >= 4 && !allowed.includes(e.key)) {
                e.preventDefault();
            }
        });
        yearInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^\d]/g, '').slice(0, 4);
        });

        resetBtn.addEventListener('click', function() {
            document.getElementById('resetModal').classList.remove('hidden');
        });

        saveBtn.addEventListener('click', function() {

            // 1. Year required check
            if (!yearInput.value) {
                showToast('Year is required. Please enter a valid 4-digit year (e.g. 2026).', 'warning');
                yearInput.focus();
                return;
            }

            // 2. Year format & range validation
            const yearVal = yearInput.value.toString().trim();
            const yearNum = parseInt(yearVal);

            if (!/^\d{4}$/.test(yearVal)) {
                showToast('Invalid year format. Please enter a complete 4-digit year (e.g. 2026).', 'warning');
                yearInput.focus();
                return;
            }

            if (yearNum < 2000) {
                showToast('Year must not be earlier than 2000. Please enter a valid year.', 'warning');
                yearInput.focus();
                return;
            }

            if (yearNum > 2100) {
                showToast('Year must not exceed 2100. Please enter a valid year.', 'warning');
                yearInput.focus();
                return;
            }

            // 3. Month required check
            if (!monthInput.value) {
                showToast('Please select a Month before submitting.', 'warning');
                monthInput.focus();
                return;
            }

            // 4. Required: all manual input fields must be filled
            const requiredFields = [
                { input: householdInput,           label: 'Household Population' },
                { input: lfprInput,                label: 'Labor Force Participation Rate' },
                { input: employmentRateInput,      label: 'Employment Rate' },
                { input: underemploymentRateInput, label: 'Underemployment Rate' },
                { input: unemploymentRateInput,    label: 'Unemployment Rate' },
            ];

            for (const field of requiredFields) {
                if (field.input.value === '' || field.input.value === null) {
                    showToast(`"${field.label}" is required. Please fill in all fields before submitting.`, 'warning');
                    field.input.focus();
                    return;
                }
            }

            saveBtn.disabled = true;
            saveBtn.textContent = 'Checking...';

            fetch('{{ route('labor.market.check') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    year: yearInput.value,
                    month: monthInput.value
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.exists) {
                    document.getElementById('errorMessage').textContent = result.message;
                    document.getElementById('errorModal').classList.remove('hidden');
                } else {
                    populateConfirmSummary();
                    document.getElementById('confirmModal').classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Check failed:', error);
                showToast('An error occurred while checking for existing data. Please try again.', 'error');
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Data';
            });
        });

        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('confirmModal').classList.add('hidden');
        });

        document.getElementById('confirmBtn').addEventListener('click', function() {
            document.getElementById('confirmModal').classList.add('hidden');

            const data = {
                year: parseInt(yearInput.value),
                month: parseInt(monthInput.value),
                household_population: parseFloat(householdInput.value) || null,
                labor_force: parseFloat(laborForceInput.value) || null,
                employed: parseFloat(employedInput.value) || null,
                underemployed: parseFloat(underemployedInput.value) || null,
                unemployed: parseFloat(unemployedInput.value) || null,
                labor_force_participation_rate: parseFloat(lfprInput.value) || null,
                employment_rate: parseFloat(employmentRateInput.value) || null,
                underemployment_rate: parseFloat(underemploymentRateInput.value) || null,
                unemployment_rate: parseFloat(unemploymentRateInput.value) || null
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            saveBtn.disabled = true;
            saveBtn.textContent = 'Submitting...';

            fetch('{{ route('labor.market.submit.pending') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(result => {
                if (result.success === true) {
                    document.getElementById('successModal').classList.remove('hidden');
                    console.log('Saved data:', result.data);
                } else {
                    document.getElementById('errorMessage').textContent = result.message || 'Failed to save data';
                    document.getElementById('errorModal').classList.remove('hidden');
                }
            })
            .catch(error => {
                let errorMsg = 'Network error occurred';
                
                if (error.message) {
                    errorMsg = error.message;
                } else if (error.errors) {
                    errorMsg = Object.values(error.errors).flat().join('\n');
                }
                
                document.getElementById('errorMessage').textContent = errorMsg;
                document.getElementById('errorModal').classList.remove('hidden');
                console.error('Error:', error);
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Data';
            });
        });

        document.getElementById('closeModalBtn').addEventListener('click', function() {
            document.getElementById('successModal').classList.add('hidden');
            doReset();
        });

        document.getElementById('closeErrorBtn').addEventListener('click', function() {
            document.getElementById('errorModal').classList.add('hidden');
        });
    });

    // ─── Populate Confirm Summary Modal ─────────────────────────────────────
    function populateConfirmSummary() {
        const monthNames = { 1: 'January', 4: 'April', 7: 'July', 10: 'October' };
        const year  = document.getElementById('year').value;
        const month = document.getElementById('month').value;
        const monthLabel = monthNames[parseInt(month)] || '—';

        document.getElementById('summaryPeriod').textContent = `${monthLabel} ${year}`;

        const fields = [
            { label: 'Household Population',          id: 'householdPopulation', unit: '',  auto: false },
            { label: 'Labor Force Participation Rate', id: 'lfpr',                unit: '%', auto: false },
            { label: 'Employment Rate',                id: 'employmentrate',      unit: '%', auto: false },
            { label: 'Underemployment Rate',           id: 'underemploymentrate', unit: '%', auto: false },
            { label: 'Unemployment Rate',              id: 'unemploymentrate',    unit: '%', auto: false },
            { label: 'Labor Force',                    id: 'laborForce',          unit: '',  auto: true  },
            { label: 'Employed',                       id: 'employed',            unit: '',  auto: true  },
            { label: 'Underemployed',                  id: 'underemployed',       unit: '',  auto: true  },
            { label: 'Unemployed',                     id: 'unemployed',          unit: '',  auto: true  },
        ];

        const container = document.getElementById('summaryRows');
        container.innerHTML = '';

        fields.forEach(f => {
            const raw = document.getElementById(f.id).value;
            const display = raw !== '' ? (parseFloat(raw).toLocaleString() + (f.unit ? ' ' + f.unit : '')) : '—';

            const row = document.createElement('div');
            row.className = 'flex justify-between items-center px-3 py-2 rounded-lg ' + (f.auto ? 'bg-emerald-50' : 'bg-slate-50');
            row.innerHTML = `
                <span class="text-[12.5px] font-medium text-slate-600 flex items-center gap-1.5">
                    ${f.auto ? '<span class="text-[10px] font-semibold text-teal-600 bg-teal-50 border border-teal-200 px-1.5 py-0.5 rounded">auto</span>' : ''}
                    ${f.label}
                </span>
                <span class="text-[13px] font-bold ${raw !== '' ? (f.auto ? 'text-teal-700' : 'text-slate-800') : 'text-slate-300'}">${display}</span>
            `;
            container.appendChild(row);
        });
    }

    // Global scope — accessible by onclick attributes in HTML
    function closeResetModal() {
        document.getElementById('resetModal').classList.add('hidden');
    }

    function doReset() {
        document.getElementById('resetModal').classList.add('hidden');
        document.querySelectorAll('input[type="number"]').forEach(function(input) {
            if (input.id !== 'year') {
                input.value = '';
            }
        });
        document.getElementById('month').value = '';
    }
    </script>

    <!-- TOAST NOTIFICATION -->
    <div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none" style="min-width: 340px;"></div>

</body>
</html>