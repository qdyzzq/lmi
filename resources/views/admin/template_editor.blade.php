<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
            <div class="bg-white border-b border-slate-200 px-6 py-3 flex items-center shadow-sm flex-shrink-0">
                <h1 class="text-xl font-bold text-slate-800">Analysis Template Editor • Admin</h1>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto bg-blue-50/30 p-8">
                <div class="max-w-6xl mx-auto">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                        <!-- Card Header: Title + Controls -->
                        <div class="border-b border-slate-200 px-8 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">✨</span>
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

                        <!-- Card Body -->
                        <div class="p-8">

                        <!-- Loading -->
                        <div x-show="loading" class="flex flex-col items-center py-20">
                            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
                            <p class="mt-4 text-slate-500">Fetching templates...</p>
                        </div>

                        <!-- EDITOR -->
                        <div x-show="!loading && viewMode === 'edit'" class="space-y-8">

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
                                    <span class="text-yellow-600 text-xl">⚠️</span>
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

                        <!-- Footer — Submit/Reset buttons -->
                        <div class="mt-12 pt-6 border-t border-slate-100 flex justify-between items-center" x-show="!loading">
                            <div class="text-sm">
                                <span x-show="hasValidationErrors()" class="text-red-500 font-medium">⚠️ Fix errors before submitting</span>
                                <span x-show="!hasValidationErrors()" class="text-green-600 font-medium">✓ All templates valid</span>
                            </div>
                            <div class="flex gap-3" x-show="viewMode === 'edit'">
                                <button @click="resetAll()" class="px-6 py-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition font-semibold">Reset Defaults</button>
                                <button
                                    @click="saveAll()"
                                    :disabled="saving || hasValidationErrors()"
                                    class="px-8 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-text="saving ? 'Submitting...' : '📬 Submit for Review'"></span>
                                </button>
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
                <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-amber-100 mb-4">
                            <svg class="h-8 w-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Submit for Review?</h3>
                        <p class="text-sm text-gray-600 mb-6">Submit all template changes for <span x-text="currentPeriodLabel" class="font-semibold"></span> to the statistician for review and publishing.</p>
                        <div class="flex gap-3">
                            <button @click="showSaveModal = false" class="flex-1 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium border border-gray-300 rounded-lg transition">Cancel</button>
                            <button @click="confirmSave()" class="flex-1 px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition">Yes, Submit</button>
                        </div>
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

    <script>
        function analysisEditor() {
            return {
                loading: true,
                saving: false,
                viewMode: 'edit',

                // ── Selection ──
                selectedYear:    null,
                selectedMonth:   null,
                availableYears:  [],
                availableMonths: [],
                quarterLabels:   {},  // { 1: "January", 4: "April", 7: "July", 10: "October" }

                activeField: null,

                // ── Modals ──
                showResetModal:   false,
                showSaveModal:    false,
                showSuccessModal: false,
                showErrorModal:   false,
                successTitle:     '',
                successMessage:   '',
                errorTitle:       '',
                errorMessage:     '',

                // ── Templates ──
                templates: {
                    employment:      '',
                    underemployment: '',
                    unemployment:    '',
                    lfpr:            ''
                },

                validation: {
                    employment:      { valid: true, missing: [] },
                    underemployment: { valid: true, missing: [] },
                    unemployment:    { valid: true, missing: [] },
                    lfpr:            { valid: true, missing: [] }
                },

                allPlaceholders: [
                    { key: '{current_period}',   icon: '📅' },
                    { key: '{previous_period}',  icon: '📅' },
                    { key: '{current_rate}',     icon: '📊' },
                    { key: '{previous_rate}',    icon: '📊' },
                    { key: '{trend}',            icon: '📈' }
                ],

                requiredPlaceholders: [
                    '{current_period}', '{previous_period}',
                    '{current_rate}',   '{previous_rate}',
                    '{trend}'
                ],

                // ── Preview data (real data from database) ──
                previewData: {},
                hasPreviewData: false,
                loadingPreview: false,

                // ── Computed ──
                get currentPeriodLabel() {
                    const name = this.quarterLabels[this.selectedMonth] || '';
                    return name ? `${name} ${this.selectedYear}` : '—';
                },

                // ── Init ──
                async init() {
                    await this.loadTemplates();
                },

                // ── Load ──
                async loadTemplates() {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams();
                        if (this.selectedYear)  params.set('year',  this.selectedYear);
                        if (this.selectedMonth) params.set('month', this.selectedMonth);

                        const res  = await fetch('/api/analysis-templates?' + params.toString());
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        const json = await res.json();

                        if (json.success) {
                            this.availableYears  = json.years           || [];
                            this.availableMonths = json.months          || [];
                            this.quarterLabels   = json.quarter_labels  || {};
                            this.selectedYear    = json.selected_year;
                            this.selectedMonth   = json.selected_month;

                            Object.keys(this.templates).forEach(k => this.templates[k] = '');
                            Object.keys(json.data).forEach(k => {
                                if (this.templates.hasOwnProperty(k)) {
                                    this.templates[k] = json.data[k].template_text || '';
                                }
                            });
                            this.validateAll();

                            // Load preview data
                            await this.loadPreviewData();
                        }
                    } catch (e) {
                        console.error('Load error:', e);
                        this.errorTitle     = 'Loading Error';
                        this.errorMessage   = 'An error occurred while loading the templates. Please refresh and try again.';
                        this.showErrorModal = true;
                    } finally {
                        this.loading = false;
                    }
                },

                // Year changed → reload everything
                async onYearChange() {
                    await this.loadTemplates();
                },

                // ── Load real preview data from database ──
                async loadPreviewData() {
                    if (!this.selectedYear || !this.selectedMonth) return;
                    
                    this.loadingPreview = true;
                    try {
                        const params = new URLSearchParams({
                            year: this.selectedYear,
                            month: this.selectedMonth
                        });

                        const r = await fetch(`/api/analysis-templates/preview-data?${params.toString()}`, {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (!r.ok) {
                            console.warn('No preview data available for this period');
                            this.hasPreviewData = false;
                            return;
                        }

                        const json = await r.json();
                        
                        if (json.success && json.has_data) {
                            this.previewData = json.data;
                            this.hasPreviewData = true;
                        } else {
                            this.hasPreviewData = false;
                        }

                    } catch (e) {
                        console.error('Preview data error:', e);
                        this.hasPreviewData = false;
                    } finally {
                        this.loadingPreview = false;
                    }
                },

                // ── Insert placeholder ──
                insertAtCursor(placeholder) {
                    const key      = this.activeField || 'employment';
                    const textarea = document.getElementById('textarea-' + key);
                    if (!textarea) return;

                    const start = textarea.selectionStart;
                    const end   = textarea.selectionEnd;
                    const text  = this.templates[key];

                    this.templates[key] = text.substring(0, start) + placeholder + text.substring(end);

                    this.$nextTick(() => {
                        textarea.focus();
                        const pos = start + placeholder.length;
                        textarea.setSelectionRange(pos, pos);
                        this.validateTemplate(key);
                    });
                },

                // ── Validation ──
                onInput(key) { this.validateTemplate(key); },

                validateTemplate(key) {
                    const text    = this.templates[key] || '';
                    const missing = this.requiredPlaceholders.filter(p => !text.includes(p));
                    this.validation[key] = { valid: missing.length === 0, missing };
                },

                validateAll() {
                    Object.keys(this.templates).forEach(k => this.validateTemplate(k));
                },

                hasValidationErrors() {
                    return Object.values(this.validation).some(v => !v.valid);
                },

                // ── Preview ──
                renderPreview(text, key) {
                    if (!text || !text.trim()) return '<span class="text-slate-300 italic">No content</span>';
                    
                    let out = text;
                    
                    // Use real preview data if available for this metric
                    if (this.hasPreviewData && this.previewData[key]) {
                        Object.entries(this.previewData[key]).forEach(([placeholder, value]) => {
                            out = out.replaceAll(placeholder, value);
                        });
                    } else {
                        // Fallback to mock data
                        const mockData = this.getMockData();
                        Object.entries(mockData).forEach(([k, v]) => { out = out.replaceAll(k, v); });
                    }
                    
                    return out;
                },

                // ── Mock data for preview (fallback when no real data) ──
                getMockData() {
                    const currentName = this.quarterLabels[this.selectedMonth] || 'January';
                    const prev        = this.getPreviousPeriod(this.selectedMonth, this.selectedYear);
                    const prevName    = this.quarterLabels[prev.month] || 'October';

                    return {
                        '{current_period}':  `<strong>${currentName} ${this.selectedYear}</strong>`,
                        '{previous_period}': `<strong>${prevName} ${prev.year}</strong>`,
                        '{current_rate}':    '<strong>89.0%</strong>',
                        '{previous_rate}':   '<strong>99.0%</strong>',
                        '{trend}':           '<span class="text-red-600 font-semibold">lower ↓</span>'
                    };
                },

                // Get previous period (Jan→Jan prev year, others→prev quarter)
                getPreviousPeriod(month, year) {
                    // January compares to January of previous year (annual data)
                    if (month == 1) {
                        return { month: 1, year: parseInt(year) - 1 };
                    }
                    
                    // Other quarters compare to previous quarter in same year
                    const map = {
                        4:  { month: 1,  yearOffset:  0 },  // April → January
                        7:  { month: 4,  yearOffset:  0 },  // July → April
                        10: { month: 7,  yearOffset:  0 }   // October → July
                    };
                    const prev = map[month] || { month: 1, yearOffset: -1 };
                    return { month: prev.month, year: parseInt(year) + prev.yearOffset };
                },

                // ── Save ──
                saveAll() {
                    if (this.hasValidationErrors()) {
                        this.errorTitle     = 'Validation Errors';
                        this.errorMessage   = 'Please fix validation errors before saving.';
                        this.showErrorModal = true;
                        return;
                    }
                    this.showSaveModal = true;
                },

                async confirmSave() {
                    this.showSaveModal = false;
                    this.saving = true;

                    try {
                        const r = await fetch('/api/analysis-templates/submit', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept':       'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                year:      this.selectedYear,
                                month:     this.selectedMonth,
                                templates: this.templates,
                            })
                        });

                        const json = await r.json();

                        if (json.success) {
                            this.successTitle   = '📬 Submitted for Review!';
                            this.successMessage = `Your templates for ${this.currentPeriodLabel} have been submitted. The statistician will review and publish them.`;
                            this.showSuccessModal = true;
                        } else {
                            this.errorTitle     = 'Submission Error';
                            this.errorMessage   = json.error || 'An error occurred. Please try again.';
                            this.showErrorModal = true;
                        }
                    } catch (e) {
                        this.errorTitle     = 'Submission Error';
                        this.errorMessage   = 'An unexpected error occurred. Please try again.';
                        this.showErrorModal = true;
                        console.error(e);
                    } finally {
                        this.saving = false;
                    }
                },

                // ── Reset ──
                resetAll() {
                    this.showResetModal = true;
                },

                async confirmReset() {
                    this.showResetModal = false;
                    try {
                        for (const key of Object.keys(this.templates)) {
                            const r = await fetch(`/api/analysis-templates/${key}/reset`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept':       'application/json'
                                }
                            });
                            if (!r.ok) { console.error(`Reset ${key} failed`); continue; }
                            const json = await r.json();
                            if (json.success) this.templates[key] = json.default_text;
                        }
                        this.validateAll();
                        this.successTitle   = 'Reset Complete!';
                        this.successMessage = 'All templates have been reset to their default values.';
                        this.showSuccessModal = true;
                    } catch (e) {
                        this.errorTitle     = 'Reset Error';
                        this.errorMessage   = 'An error occurred while resetting. Please try again.';
                        this.showErrorModal = true;
                        console.error(e);
                    }
                }
            }
        }
    </script>
</body>
</html>