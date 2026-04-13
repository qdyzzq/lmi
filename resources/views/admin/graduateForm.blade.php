<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')

    <!-- Quill.js Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <!-- Quill Description Editor Styling -->
    <style>
        #descriptionEditorWrapper .ql-toolbar.ql-snow {
            padding: 8px;
            border-radius: 8px 8px 0 0;
            border-color: #d1d5db;
            background: #f9fafb;
        }
        #descriptionEditorWrapper .ql-toolbar.ql-snow .ql-formats { margin-right: 12px; }
        #descriptionEditorWrapper .ql-container.ql-snow {
            border-radius: 0 0 8px 8px;
            border-color: #d1d5db;
        }
        #descriptionEditorWrapper .ql-editor {
            min-height: 100px;
            max-height: 220px;
            overflow-y: auto;
            font-size: 14px;
            line-height: 1.6;
        }
        #descriptionEditorWrapper .ql-editor.ql-blank::before {
            color: #9ca3af;
            font-style: normal;
        }
        /* Red border state for validation */
        #descriptionEditorWrapper.border-error .ql-toolbar.ql-snow,
        #descriptionEditorWrapper.border-error .ql-container.ql-snow {
            border-color: #f87171;
        }
        /* Focus ring */
        #descriptionEditorWrapper .ql-container.ql-snow:focus-within {
            border-color: #22c55e;
            box-shadow: 0 0 0 2px rgba(34,197,94,0.2);
        }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>LMI - Discipline Graduate Form</title>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden" >
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- HEADER -->
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm shrink-0">
            <h2 class="text-xl font-bold text-slate-800">Graduate Form • Admin</h2>
            <div class="flex items-center gap-4">
                <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200">
                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Region XI • 2024
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>
        <!-- Main Form Area -->
        <div class="flex-1 overflow-auto p-8">
            <div class="max-w-5xl mx-auto">
                <!-- Year Selection Card -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Select Academic Year</h3>
                            <p class="text-sm text-gray-600">Enter an academic year to create new data or edit existing graduate data</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div id="yearInputGroup" class="flex items-center gap-4">
                                <input 
                                    type="text" 
                                    id="academicYear" 
                                    placeholder="e.g. 2024-2025" 
                                    pattern="\d{4}-\d{4}"
                                    required 
                                    oninput="toggleClearBtn(this.value)"
                                    class="w-48 px-4 py-3 border-2 border-gray-300 rounded-lg text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                >
                                <button 
                                    type="button"
                                    onclick="checkAndLoadYear()"
                                    class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2 whitespace-nowrap"
                                >
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Check / Edit Year
                                </button>
                                <button
                                    type="button"
                                    id="clearYearBtn"
                                    onclick="clearYearInput()"
                                    class="hidden px-4 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2 whitespace-nowrap"
                                >
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancel
                                </button>
                            </div>

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

                <!-- GRADUATION RATE PROJECTION CARD - NEW SECTION -->
                <div id="graduationRateCard" class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-xl p-8 mb-8 border-2 border-green-200" style="display: none;">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-green-800 mb-2 flex items-center gap-2">
                                <span><svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></span>
                                Graduation Rate & Projections
                            </h3>
                            <p class="text-sm text-green-700">Set graduation rate to project graduates based on enrollment from 4 years ago</p>
                        </div>
                        <button 
                            type="button"
                            onclick="saveGraduationRate()"
                            class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save 
                        </button>
                    </div>

                    <!-- Warning: Future Graduate Year -->
                    <div id="futureYearWarning" class="hidden mb-6 bg-blue-50 border-2 border-blue-500 rounded-xl p-5">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-blue-800 mb-1"><svg class="w-4 h-4 inline-block mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Data Not Yet Available — Future Academic Year</h4>
                                <p class="text-sm text-blue-700 mb-2">
                                    <strong id="futureGraduateYear" class="text-blue-900"></strong> is still an upcoming academic year.
                                    The enrollment base is shown below as a <strong>preview only</strong> — no graduation data can be recorded until this year is reached.
                                </p>
                                <div class="bg-blue-100 border border-blue-300 rounded-lg px-4 py-2 inline-flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                    </svg>
                                    <span class="text-xs font-semibold text-blue-800">
                                        Saving is locked until <strong id="futureYearUnlockYear" class="text-blue-900"></strong>. Come back then to record the actual graduation rate.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Warning: Missing Enrollment Data -->
                    <div id="missingEnrollmentWarning" class="hidden mb-6 bg-amber-50 border-2 border-amber-400 rounded-xl p-5">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-amber-800 mb-1">Enrollment Data Not Found</h4>
                                <p class="text-sm text-amber-700">
                                    No enrollment records exist for <strong id="missingEnrollmentYear" class="text-amber-900"></strong> (4 years before the graduate year).
                                    Projections cannot be calculated without this base data.
                                </p>
                                <p class="text-xs text-amber-600 mt-2">
                                    → Please enter enrollment data for that year in the <strong>Discipline Enrollment Form</strong> first, then come back here.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column: Input & Calculation -->
                        <div class="space-y-6">
                            <!-- Enrollment Base Information -->
                            <div class="bg-white rounded-xl p-5 shadow-sm border border-green-200">
                                <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Base Enrollment Data</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Graduate Year:</span>
                                        <span id="projGraduateYear" class="text-lg font-bold text-green-700">----</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Enrollment Year (4 yrs ago):</span>
                                        <span id="projEnrollmentYear" class="text-lg font-bold text-blue-700">----</span>
                                    </div>
                                    <div class="h-px bg-gray-200"></div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold text-gray-700">Total Enrollees:</span>
                                        <span id="projBaseEnrollees" class="text-2xl font-bold text-purple-700">0</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Graduation Rate Input -->
                            <div class="bg-white rounded-xl p-5 shadow-sm border border-green-200">
                                <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Set Graduation Rate</h4>
                                <div class="flex items-center gap-4">
                                    <div class="flex-1">
                                        <input 
                                            type="number" 
                                            id="graduationRateInput" 
                                            min="0" 
                                            max="100" 
                                            step="0.01"
                                            value="60.00"
                                            oninput="calculateProjection()"
                                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg text-xl font-bold text-center focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        >
                                    </div>
                                    <span class="text-3xl font-bold text-gray-400">%</span>
                                </div>
                                <div class="mt-3">
                                    <input 
                                        type="range" 
                                        id="graduationRateSlider" 
                                        min="0" 
                                        max="100" 
                                        step="1"
                                        value="60"
                                        oninput="updateRateFromSlider(this.value)"
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-green-600"
                                    >
                                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                                        <span>0%</span>
                                        <span>50%</span>
                                        <span>100%</span>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <!-- Right Column: Projection Results -->
                        <div class="space-y-6">
                            <!-- Projection Result -->
                            <div class="bg-gradient-to-br from-green-600 to-emerald-600 rounded-xl p-8 shadow-lg text-white">
                                <div class="text-center">
                                    <p class="text-sm font-semibold opacity-90 mb-2 uppercase tracking-wide">Projected Graduates</p>
                                    <p id="projectedGraduates" class="text-6xl font-extrabold mb-4">0</p>
                                    <div class="h-px bg-white/30 mb-4"></div>
                                    <div class="text-sm opacity-80">
                                        <p class="mb-1">
                                            <span id="projCalcEnrollees" class="font-bold">0</span> enrollees × 
                                            <span id="projCalcRate" class="font-bold">60%</span>
                                        </p>
                                        <p class="text-xs">= <span id="projCalcResult" class="font-bold">0</span> projected graduates</p>
                                    </div>
                                </div>
                            </div>



                            <!-- Status Indicator -->
                            <div id="rateStatusSaved" class="bg-green-100 border border-green-300 rounded-xl p-4 text-center" style="display: none;">
                                <p class="text-sm font-semibold text-green-700 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Rate Saved Successfully!
                                </p>
                            </div>

                            <div id="rateStatusUnsaved" class="bg-yellow-100 border border-yellow-300 rounded-xl p-4 text-center" style="display: none;">
                                <p class="text-sm font-semibold text-yellow-700 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    Unsaved Changes
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Description — full width Quill rich text editor -->
                    <div class="mt-6 bg-white rounded-xl p-5 shadow-sm border border-green-200">
                        <h4 class="text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">
                            Description <span class="text-red-500">*</span>
                        </h4>
                        <div id="descriptionEditorWrapper">
                            <div id="descriptionQuillEditor"></div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- MODAL: Existing Data Found -->
    <div id="existingDataModal" class="hidden fixed inset-0 backdrop-blur-sm bg-white/30 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900">Existing Data Found</h3>
                        <p class="text-sm text-gray-600 mt-1">This year already has graduation rate data</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-purple-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-purple-900">Year: <span id="existingDataYear" class="text-purple-700"></span></p>
                            <p class="text-sm text-purple-800 mt-1">Graduation Rate: <span id="existingDataRate" class="font-bold"></span>%</p>
                        </div>
                    </div>
                </div>
                <p class="text-gray-700 mb-6">
                    Data already exists for this academic year. Would you like to <strong>edit the existing data</strong> or <strong>enter a different year</strong>?
                </p>
                <div class="flex flex-col gap-3">
                    <button
                        onclick="confirmLoadExistingData()"
                        class="w-full px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2"
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

    <!-- MODAL: Confirm Save Rate -->
    <div id="confirmSaveModal" class="hidden fixed inset-0 backdrop-blur-sm bg-white/30 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-gray-200 shrink-0">
                <h3 class="text-2xl font-bold text-gray-900">Confirm Save</h3>
                <p class="text-sm text-gray-600 mt-1">Please review before saving the graduation rate</p>
            </div>

            <div class="p-6 overflow-y-auto flex-1">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-600 p-4 mb-6">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="font-semibold text-gray-900">
                            You are about to <span id="confirmSaveAction" class="text-green-700"></span> data for:
                        </p>
                    </div>
                    <p class="font-bold text-lg text-gray-900" id="confirmSaveYear"></p>
                </div>

                <div id="confirmSaveDeletionWarning" class="hidden mb-6">
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <p class="text-xs font-semibold text-green-800 mb-2"><svg class="w-3.5 h-3.5 inline-block mr-1 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Modified Fields</p>
                        <!-- Rate change row -->
                        <div id="confirmRateChangeRow" class="hidden flex items-center gap-2 text-sm flex-wrap mb-2">
                            <span class="font-medium text-gray-700">Graduation Rate:</span>
                            <span id="confirmOldRate" class="text-red-500 font-semibold"></span>
                            <span class="text-gray-500 font-bold">→</span>
                            <span id="confirmNewRate" class="text-green-600 font-semibold"></span>
                        </div>
                        <!-- Description change row -->
                        <div id="confirmDescriptionChangeRow" class="hidden text-sm">
                            <span class="font-medium text-gray-700">Description: </span>
                            <span class="text-amber-600 font-semibold">Modified</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Graduation Rate</span>
                        <span id="confirmSaveRate" class="text-lg font-bold text-green-600"></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Projected Graduates</span>
                        <span id="confirmSaveProjected" class="text-lg font-bold text-purple-600"></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Based on Enrollees</span>
                        <span id="confirmSaveEnrollees" class="text-lg font-bold text-blue-600"></span>
                    </div>
                    <!-- Description preview -->
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-700 mb-2">Description</p>
                        <div id="confirmSaveDescription" class="text-sm text-gray-600 leading-relaxed prose prose-sm max-w-none border-l-2 border-green-300 pl-3"></div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2"><!-- spacer --></div>
            </div>
            <!-- Sticky footer buttons -->
            <div class="px-6 pb-6 shrink-0 border-t border-gray-100 pt-4">
                <div class="flex gap-3">
                    <button
                        onclick="closeConfirmSaveModal()"
                        class="flex-1 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg"
                    >
                        Cancel
                    </button>
                    <button
                        onclick="confirmSaveRate()"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg"
                    >
                        <span id="confirmSaveBtnText">Save</span> 
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none" style="min-width: 340px;"></div>

    <!-- MODAL: Success -->
    <div id="successModal" class="hidden fixed inset-0 backdrop-blur-sm bg-white/30 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-600 mb-6">Graduation rate has been saved successfully.</p>
                <button
                    onclick="closeSuccessModal()"
                    class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg"
                >
                    Done
                </button>
            </div>
        </div>
    </div>


    @vite('resources/js/admin/Module3/graduate-form.js')

</body>
</html>