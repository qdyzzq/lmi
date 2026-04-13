<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    {{-- Load our JS first so analysisEditor() is on window before Alpine boots --}}
    @vite('resources/js/admin/Module1/template-editor.js')

    {{-- Alpine must come AFTER so it finds the function already defined --}}
    <script defer src="https://unpkg.com/alpinejs@3.15.8/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <title>Economic Analysis Editor</title>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden" >
    @include('partials.sidebar')

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <div x-data="analysisEditor()" x-init="init()" class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Bar (page title only) -->
            <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h1 class="text-xl font-bold text-slate-800">Analysis Template Editor • Admin</h1>
            <div class="flex items-center gap-4">
                <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200">
                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Region XI • 2026
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto bg-blue-50/30 p-8">
                <div class="max-w-6xl mx-auto">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                        <!-- Card Header: Title + Controls -->
                        <div class="border-b border-slate-200 px-8 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <span><svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg></span>
                                    <h3 class="text-blue-600 font-bold text-lg">
                                        Analysis for <span x-text="currentPeriodLabel"></span>
                                    </h3>
                                </div>

                                <!-- Year Selector -->
                                <div class="flex items-center gap-1.5">
                                    <label class="text-sm text-slate-500 font-medium">Year</label>
                                    <select
                                        x-model.number="selectedYear"
                                        @change="onYearChange()"
                                        class="border border-slate-300 rounded px-3 py-1 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none"
                                        :disabled="availableYears.length === 0">
                                        <template x-if="availableYears.length === 0">
                                            <option value="">Loading...</option>
                                        </template>
                                        <template x-for="year in availableYears" :key="year">
                                            <option :value="year" x-text="year"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Quarter Selector -->
                                <div class="flex items-center gap-1.5">
                                    <label class="text-sm text-slate-500 font-medium">Quarter</label>
                                    <select
                                        x-model.number="selectedMonth"
                                        @change="loadTemplates()"
                                        class="border border-slate-300 rounded px-3 py-1 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none"
                                        :disabled="availableMonths.length === 0">
                                        <template x-if="availableMonths.length === 0">
                                            <option value="">—</option>
                                        </template>
                                        <template x-for="m in availableMonths" :key="m">
                                            <option :value="m" x-text="quarterLabels[m] || m"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <!-- Editor / Preview Toggle -->
                            <div class="flex gap-2">
                                <button @click="viewMode = 'edit'" :class="viewMode === 'edit' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600'" class="px-4 py-1 rounded text-sm font-medium transition">Editor</button>
                                <button @click="viewMode = 'preview'" :class="viewMode === 'preview' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600'" class="px-4 py-1 rounded text-sm font-medium transition">Live Preview</button>
                            </div>
                        </div>

                        <!-- Status Badge Row -->
                        <div class="px-8 py-3 border-b border-slate-100 flex items-center gap-3">
                            <span class="text-sm text-slate-500 font-medium">Current status:</span>

                            <!-- No submission yet -->
                            <span x-show="!pendingSubmission && !pendingEditSubmission && !publishedExists"
                                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                ⬜ No submission yet
                            </span>

                            <!-- Pending review (first-time, not yet published) -->
                            <span x-show="pendingSubmission && !publishedExists"
                                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Pending Review
                                <span x-show="pendingSubmission?.submitted_at" class="font-normal text-amber-600">
                                    — submitted <span x-text="pendingSubmission?.submitted_at ? new Date(pendingSubmission.submitted_at).toLocaleString() : ''"></span>
                                </span>
                            </span>

                            <!-- Published + pending edit awaiting statistician -->
                            <template x-if="publishedExists && pendingEditSubmission">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Published
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Pending Edit Review
                                        <span class="font-normal text-amber-600">
                                            — submitted <span x-text="pendingEditSubmission?.submitted_at ? new Date(pendingEditSubmission.submitted_at).toLocaleString() : ''"></span>
                                        </span>
                                    </span>
                                </div>
                            </template>

                            <!-- Published (original), no pending edit -->
                            <span x-show="publishedExists && !pendingEditSubmission && !publishedIsEdited"
                                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Published
                            </span>

                            <!-- Published (Edited) — statistician approved an admin edit -->
                            <span x-show="publishedExists && !pendingEditSubmission && publishedIsEdited"
                                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-teal-100 text-teal-700">
                                <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Published
                                <span class="inline-flex items-center gap-1 bg-teal-200 text-teal-800 px-1.5 py-0.5 rounded text-[10px] font-bold ml-0.5">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edited
                                </span>
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-8">

                        <!-- Loading -->
                        <div x-show="loading" class="flex flex-col items-center py-20">
                            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
                            <p class="mt-4 text-slate-500">Fetching templates...</p>
                        </div>

                        <!-- EDITOR -->
                        <div x-show="!loading && viewMode === 'edit'" class="space-y-8"
                             :class="(pendingSubmission || pendingEditSubmission) ? 'opacity-60 pointer-events-none select-none' : ''">

                            <!-- Placeholder Toolbar -->
                            <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 flex-wrap">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">Insert:</span>
                                <template x-for="ph in allPlaceholders" :key="ph.key">
                                    <button
                                        @click="insertAtCursor(ph.key)"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-slate-200 rounded-md text-xs text-slate-600 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition cursor-pointer">
                                        <span class="text-slate-400" x-html="ph.icon"></span>
                                        <code class="font-mono" x-text="ph.key"></code>
                                    </button>
                                </template>
                            </div>

                            <!-- Fields -->
                            <template x-for="(text, key) in templates" :key="key">
                                <div>
                                    <label class="text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-2 block" x-text="key.replace('_', ' ')"></label>

                                    <textarea
                                        :id="'textarea-' + key"
                                        x-model="templates[key]"
                                        @input="onInput(key)"
                                        @focus="activeField = key"
                                        rows="3"
                                        class="w-full p-3 border rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-700 leading-relaxed text-sm resize-none transition outline-none"
                                        :class="validation[key] && !validation[key].valid ? 'border-red-300 bg-red-50/20' : 'border-slate-200'"
                                        placeholder="Write analysis template here..."></textarea>

                                    <div x-show="validation[key] && !validation[key].valid" class="mt-2 flex items-center gap-2 flex-wrap">
                                        <span class="text-xs text-red-500 font-medium">Missing:</span>
                                        <template x-for="m in validation[key].missing" :key="m">
                                            <code class="text-[11px] px-2 py-0.5 bg-red-50 border border-red-200 text-red-600 rounded" x-text="m"></code>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- PREVIEW -->
                        <div x-show="!loading && viewMode === 'preview'" class="space-y-5">
                            <!-- No Preview Data Warning -->
                            <div x-show="!hasPreviewData && !loadingPreview" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start gap-3">
                                    <span><svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></span>
                                    <div>
                                        <h4 class="font-semibold text-yellow-800 mb-1">No Data Available</h4>
                                        <p class="text-sm text-yellow-700">
                                            There is no statistical data available for <strong x-text="currentPeriodLabel"></strong>. 
                                            The preview below shows example data for demonstration purposes.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Loading Preview Data -->
                            <div x-show="loadingPreview" class="flex items-center justify-center py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <span class="ml-3 text-slate-500">Loading preview data...</span>
                            </div>

                            <!-- Preview Cards — new design matching home blade -->
                            <div class="grid md:grid-cols-2 gap-4">
                                <!-- Participation Rate -->
                                <div class="group bg-white rounded-lg p-5 border border-[#023E8A]/20 border-l-4 hover:border-[#023E8A] hover:bg-blue-50/30 shadow-sm hover:shadow-md transition-all">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-8 h-8 bg-blue-50 group-hover:bg-[#023E8A] rounded-lg flex items-center justify-center transition-colors">
                                            <svg class="w-4 h-4 text-[#023E8A] group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-xs font-bold text-[#023E8A] uppercase tracking-wide">Participation Rate</p>
                                    </div>
                                    <div x-html="renderPreview(templates.lfpr, 'lfpr')" class="text-sm text-slate-700 leading-relaxed"></div>
                                </div>

                                <!-- Employment Rate -->
                                <div class="group bg-white rounded-lg p-5 border border-[#006400]/20 border-l-4 hover:border-[#006400] hover:bg-green-50/30 shadow-sm hover:shadow-md transition-all">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-8 h-8 bg-green-50 group-hover:bg-[#006400] rounded-lg flex items-center justify-center transition-colors">
                                            <svg class="w-4 h-4 text-[#006400] group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-xs font-bold text-[#006400] uppercase tracking-wide">Employment Rate</p>
                                    </div>
                                    <div x-html="renderPreview(templates.employment, 'employment')" class="text-sm text-slate-700 leading-relaxed"></div>
                                </div>

                                <!-- Underemployment Rate -->
                                <div class="group bg-white rounded-lg p-5 border border-[#FF8C00]/20 border-l-4 hover:border-[#FF8C00] hover:bg-orange-50/30 shadow-sm hover:shadow-md transition-all">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-8 h-8 bg-orange-50 group-hover:bg-[#FF8C00] rounded-lg flex items-center justify-center transition-colors">
                                            <svg class="w-4 h-4 text-[#FF8C00] group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-xs font-bold text-[#FF8C00] uppercase tracking-wide">Underemployment Rate</p>
                                    </div>
                                    <div x-html="renderPreview(templates.underemployment, 'underemployment')" class="text-sm text-slate-700 leading-relaxed"></div>
                                </div>

                                <!-- Unemployment Rate -->
                                <div class="group bg-white rounded-lg p-5 border border-[#D30000]/20 border-l-4 hover:border-[#D30000] hover:bg-red-50/30 shadow-sm hover:shadow-md transition-all">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-8 h-8 bg-red-50 group-hover:bg-[#D30000] rounded-lg flex items-center justify-center transition-colors">
                                            <svg class="w-4 h-4 text-[#D30000] group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                            </svg>
                                        </div>
                                        <p class="text-xs font-bold text-[#D30000] uppercase tracking-wide">Unemployment Rate</p>
                                    </div>
                                    <div x-html="renderPreview(templates.unemployment, 'unemployment')" class="text-sm text-slate-700 leading-relaxed"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Banner (first-time, not yet published) -->
                        <div x-show="pendingSubmission && !publishedExists"
                             class="mb-5 flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-5 py-4">
                            <div class="flex-shrink-0 w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-amber-800 text-sm">Submission Pending Review</p>
                                <p class="text-xs text-amber-700 mt-0.5">
                                    Submitted on <strong x-text="pendingSubmission?.submitted_at ? new Date(pendingSubmission.submitted_at).toLocaleString() : '—'"></strong> and awaiting the statistician's approval.
                                </p>
                            </div>
                        </div>

                        <!-- Pending Edit Banner (edit on top of published) -->
                        <div x-show="publishedExists && pendingEditSubmission"
                             class="mb-5 flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-5 py-4">
                            <div class="flex-shrink-0 w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-amber-800 text-sm">Edit Pending Review</p>
                                <p class="text-xs text-amber-700 mt-0.5">
                                    Your edited templates were submitted on <strong x-text="pendingEditSubmission?.submitted_at ? new Date(pendingEditSubmission.submitted_at).toLocaleString() : '—'"></strong> and are awaiting the statistician's approval. The current published version remains live until approved.
                                </p>
                            </div>
                        </div>

                        <!-- Footer — Submit/Reset buttons -->
                        <div class="mt-12 pt-6 border-t border-slate-100 flex justify-between items-center" x-show="!loading">
                            <div class="text-sm">
                                <span x-show="hasValidationErrors()" class="text-red-500 font-medium"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Fix errors before submitting</span>
                                <span x-show="!hasValidationErrors()" class="text-green-600 font-medium"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> All templates valid</span>
                            </div>
                            <div class="flex gap-3" x-show="viewMode === 'edit'">
                                <button @click="resetAll()" :disabled="!!(pendingSubmission || pendingEditSubmission)" class="px-6 py-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed">Reset Defaults</button>

                                <!-- First-time pending: locked awaiting review -->
                                <template x-if="pendingSubmission && !publishedExists">
                                    <button disabled
                                        class="px-8 py-2 bg-amber-100 text-amber-700 border border-amber-300 rounded-lg font-medium cursor-not-allowed flex items-center gap-2">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Awaiting Review
                                    </button>
                                </template>

                                <!-- Published + pending edit: locked awaiting review -->
                                <template x-if="publishedExists && pendingEditSubmission">
                                    <button disabled
                                        class="px-8 py-2 bg-amber-100 text-amber-700 border border-amber-300 rounded-lg font-medium cursor-not-allowed flex items-center gap-2">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Edit Awaiting Review
                                    </button>
                                </template>

                                <!-- Published, no pending edit: admin can submit edits -->
                                <template x-if="publishedExists && !pendingEditSubmission">
                                    <button
                                        @click="saveAll()"
                                        :disabled="saving || hasValidationErrors()"
                                        class="px-8 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span x-text="saving ? 'Submitting...' : 'Submit Edit for Review'"></span>
                                    </button>
                                </template>

                                <!-- No submission yet: normal first submit -->
                                <template x-if="!pendingSubmission && !publishedExists">
                                    <button
                                        @click="saveAll()"
                                        :disabled="saving || hasValidationErrors()"
                                        class="px-8 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span x-html="saving ? 'Submitting...' : '<svg class=&quot;w-3.5 h-3.5 inline-block&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4&quot;/></svg> Submit for Review'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div>
            </div>

            <!-- ── MODALS ── -->

            <!-- Reset Modal -->
            <div x-show="showResetModal" x-cloak class="fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.1); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
                <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-orange-100 mb-4">
                            <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Reset to Defaults?</h3>
                        <p class="text-sm text-gray-600 mb-6">Are you sure you want to reset all templates to their default text? This will overwrite any changes you've made.</p>
                        <div class="flex gap-3">
                            <button @click="showResetModal = false" class="flex-1 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium border border-gray-300 rounded-lg transition">Cancel</button>
                            <button @click="confirmReset()" class="flex-1 px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition">Yes, Reset</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Modal -->
            <div x-show="showSaveModal" x-cloak class="fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.1); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
                <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full mx-4 max-h-[90vh] flex flex-col">

                    <!-- Modal header -->
                    <div class="p-6 border-b border-gray-200 flex-shrink-0">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 flex-shrink-0">
                                <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Submit for Review</h3>
                                <p class="text-sm text-gray-500 mt-0.5" x-text="templateDiffs.length === 0 ? 'No changes detected — submitting as-is.' : templateDiffs.length + ' template' + (templateDiffs.length > 1 ? 's' : '') + ' edited for ' + currentPeriodLabel"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal body -->
                    <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">

                        <!-- No changes: plain confirm -->
                        <template x-if="templateDiffs.length === 0">
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                                <span><svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                                <div>
                                    <p class="font-semibold text-blue-900 text-sm">No changes made</p>
                                    <p class="text-sm text-blue-700 mt-0.5">You haven't edited any templates. The existing content for <strong x-text="currentPeriodLabel"></strong> will be submitted to the statistician as-is.</p>
                                </div>
                            </div>
                        </template>

                        <!-- Has changes: diff cards -->
                        <template x-if="templateDiffs.length > 0">
                            <div class="space-y-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Changes Summary</p>
                                <template x-for="diff in templateDiffs" :key="diff.key">
                                    <div class="rounded-xl border overflow-hidden" :class="diff.isNew ? 'border-green-200' : 'border-amber-200'">
                                        <!-- Field label bar -->
                                        <div class="flex items-center gap-2 px-4 py-2" :class="diff.isNew ? 'bg-green-50' : 'bg-amber-50'">
                                            <span class="text-sm font-bold" :class="diff.isNew ? 'text-green-700' : 'text-amber-700'" x-text="diff.label"></span>
                                            <span class="ml-auto text-[11px] font-semibold px-2 py-0.5 rounded-full" :class="diff.isNew ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'" x-text="diff.isNew ? '+ New content' : '~ Edited'"></span>
                                        </div>

                                        <!-- Before (only shown when editing existing) -->
                                        <template x-if="!diff.isNew">
                                            <div class="px-4 py-3 border-t border-slate-100 bg-red-50/40">
                                                <p class="text-[10px] font-bold uppercase tracking-widest text-red-400 mb-1">Before</p>
                                                <p class="text-xs text-slate-500 leading-relaxed font-mono whitespace-pre-wrap" x-text="diff.before"></p>
                                            </div>
                                        </template>

                                        <!-- After -->
                                        <div class="px-4 py-3 border-t border-slate-100 bg-green-50/40">
                                            <p class="text-[10px] font-bold uppercase tracking-widest text-green-500 mb-1" x-text="diff.isNew ? 'Content' : 'After'"></p>
                                            <p class="text-xs text-slate-700 leading-relaxed font-mono whitespace-pre-wrap" x-text="diff.after"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Footer -->
                    <div class="flex-shrink-0 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl flex gap-3">
                        <button @click="showSaveModal = false" class="flex-1 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium border border-gray-300 rounded-lg transition">Cancel</button>
                        <button @click="confirmSave()" class="flex-1 px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition">
                            <span x-html="templateDiffs.length === 0 ? '<svg class=&quot;w-3.5 h-3.5 inline-block&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4&quot;/></svg> Submit As-Is' : '<svg class=&quot;w-3.5 h-3.5 inline-block&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4&quot;/></svg> Submit Changes'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Success Modal -->
            <div x-show="showSuccessModal" x-cloak class="fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.1); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
                <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3" x-text="successTitle"></h3>
                        <p class="text-sm text-gray-600 mb-6" x-text="successMessage"></p>
                        <button @click="showSuccessModal = false" class="w-full px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">OK</button>
                    </div>
                </div>
            </div>

            <!-- Error Modal -->
            <div x-show="showErrorModal" x-cloak class="fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.1); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
                <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3" x-text="errorTitle"></h3>
                        <p class="text-sm text-gray-600 mb-6" x-text="errorMessage"></p>
                        <button @click="showErrorModal = false" class="w-full px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition">Close</button>
                    </div>
                </div>
            </div>

        </div><!-- end x-data -->
    </div><!-- end main -->

</body>
</html>