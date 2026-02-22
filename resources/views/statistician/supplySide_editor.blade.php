<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    
    <!-- Quill.js Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    
    <!-- Custom Quill Toolbar Styling -->
    <style>
        .ql-toolbar.ql-snow { padding: 12px 8px; border-radius: 8px 8px 0 0; }
        .ql-toolbar.ql-snow .ql-formats { margin-right: 20px; }
        .ql-toolbar.ql-snow button { width: 32px !important; height: 32px !important; padding: 4px; }
        .ql-toolbar.ql-snow .ql-stroke { stroke-width: 2.5; }
        .ql-toolbar.ql-snow select { height: 32px !important; padding: 4px 8px; }
        .ql-container.ql-snow { border-radius: 0 0 8px 8px; max-height: 500px; overflow-y: auto; }
        .ql-editor { min-height: 400px; font-size: 14px; line-height: 1.6; }
        .ql-editor::-webkit-scrollbar { width: 8px; }
        .ql-editor::-webkit-scrollbar-track { background: #f1f5f9; }
        .ql-editor::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .ql-editor::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="8pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="8pt"]::before { content: '8'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="9pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="9pt"]::before { content: '9'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="10pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="10pt"]::before { content: '10'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="11pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="11pt"]::before { content: '11'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="12pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="12pt"]::before { content: '12'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="14pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="14pt"]::before { content: '14'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="16pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="16pt"]::before { content: '16'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="18pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="18pt"]::before { content: '18'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="20pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="20pt"]::before { content: '20'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="22pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="22pt"]::before { content: '22'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="24pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="24pt"]::before { content: '24'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="28pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="28pt"]::before { content: '28'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="36pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="36pt"]::before { content: '36'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="48pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="48pt"]::before { content: '48'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="72pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="72pt"]::before { content: '72'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label:not([data-value])::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item:not([data-value])::before { content: '11'; }
        .ql-editor .ql-size-8pt { font-size: 8pt; }
        .ql-editor .ql-size-9pt { font-size: 9pt; }
        .ql-editor .ql-size-10pt { font-size: 10pt; }
        .ql-editor .ql-size-11pt { font-size: 11pt; }
        .ql-editor .ql-size-12pt { font-size: 12pt; }
        .ql-editor .ql-size-14pt { font-size: 14pt; }
        .ql-editor .ql-size-16pt { font-size: 16pt; }
        .ql-editor .ql-size-18pt { font-size: 18pt; }
        .ql-editor .ql-size-20pt { font-size: 20pt; }
        .ql-editor .ql-size-22pt { font-size: 22pt; }
        .ql-editor .ql-size-24pt { font-size: 24pt; }
        .ql-editor .ql-size-28pt { font-size: 28pt; }
        .ql-editor .ql-size-36pt { font-size: 36pt; }
        .ql-editor .ql-size-48pt { font-size: 48pt; }
        .ql-editor .ql-size-72pt { font-size: 72pt; }
    </style>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Supply Side Analysis Editor</title>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden" >
    @include('partials.statisticianSidebar')

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Supply Side Analysis Editor • Statistician</h2>
            <div class="flex items-center gap-4">
                <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200">📅 Region XI • {{ date('Y') }}</div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>
        <div class="flex-1 overflow-auto">
            <div x-data="supplySideEditor()" x-init="init()" class="p-6">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-slate-800">Supply Side Analysis Editor</h1>
                    <p class="text-slate-600 mt-2">Edit executive analysis for different provinces and academic years</p>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Province Select -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                📍 Province
                            </label>
                            <select 
                                x-model="selectedProvince" 
                                @change="loadAnalysis(); loadArchivedAnalyses();"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <template x-for="province in provinces" :key="province">
                                    <option :value="province" x-text="province"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Academic Year Select -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                📖 Academic Year
                            </label>
                            <select 
                                x-model="selectedAcademicYear" 
                                @change="loadAnalysis(); loadArchivedAnalyses();"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <template x-for="year in academicYears" :key="year">
                                    <option :value="year" x-text="year"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Editor Card (2 columns) -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <!-- Card Header -->
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-2.5 rounded-xl">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800">EXECUTIVE ANALYSIS: SUPPLY SIDE</h3>
                                    <p class="text-xs text-slate-500" x-show="lastUpdated">
                                        Last updated: <span x-text="lastUpdated"></span>
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <button 
                                    @click="resetToDefault()"
                                    :disabled="loading"
                                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition disabled:opacity-50">
                                    Reset to Default
                                </button>
                                <button 
                                    @click="showConfirmModal = true"
                                    :disabled="loading || !hasChanges"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition disabled:opacity-50"
                                    :class="{'bg-green-600 hover:bg-green-700': hasChanges}">
                                    💾 Save Changes
                                </button>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div x-show="loading" class="flex items-center justify-center py-12">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        </div>

                        <!-- Editor Textarea -->
                        <div x-show="!loading">
                            <div id="quillEditor" style="height: 400px; max-height: 500px; overflow-y: auto;"></div>
                            <input type="hidden" x-model="analysisText">
                            <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                                <span>
                                    <span x-text="getWordCount()"></span> words
                                </span>
                                <span x-show="hasChanges" class="text-orange-600 font-semibold">
                                    ⚠️ Unsaved changes
                                </span>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div x-show="!loading" class="mt-6 pt-6 border-t border-slate-200">
                            <h4 class="text-sm font-bold text-slate-700 mb-3">📄 Preview (How it will appear on public page)</h4>
                            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6">
                                <div class="flex items-start gap-3 mb-4">
                                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-2.5 rounded-xl shadow-lg">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-800">EXECUTIVE ANALYSIS: SUPPLY SIDE</h3>
                                    </div>
                                </div>
                                <div class="space-y-4 text-sm text-slate-700 prose prose-sm max-w-none" 
                                     x-html="analysisText || '<span class=\'text-slate-400 italic\'>No content to preview</span>'">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Archive Sidebar (1 column) -->
                    <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                            <h3 class="text-lg font-bold text-slate-800">Archived Analyses</h3>
                        </div>
                        
                        <p class="text-xs text-slate-500 mb-4">
                            Copy text from previous analyses to save time
                        </p>

                        <div x-show="loadingArchives" class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
                        </div>

                        <div x-show="!loadingArchives" class="space-y-3 max-h-[600px] overflow-y-auto">
                            <template x-for="archive in archivedAnalyses" :key="archive.id">
                                <div class="border border-slate-200 rounded-lg p-3 hover:border-indigo-300 hover:bg-indigo-50 transition cursor-pointer group"
                                     @click="copyFromArchive(archive)">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <p class="font-semibold text-sm text-slate-800" x-text="archive.academic_year"></p>
                                            <p class="text-xs text-slate-500" x-text="archive.updated_at"></p>
                                        </div>
                                        <button class="text-indigo-600 opacity-0 group-hover:opacity-100 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-xs text-slate-600 line-clamp-3" x-text="archive.analysis_text"></p>
                                </div>
                            </template>

                            <div x-show="archivedAnalyses.length === 0" class="text-center py-8 text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-sm">No archived analyses</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CONFIRMATION MODAL -->
                <div x-show="showConfirmModal" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-white/30 p-4"
                     @click.self="showConfirmModal = false">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
                        <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                            <h3 class="text-2xl font-bold text-gray-900">Confirm Submission</h3>
                            <p class="text-sm text-gray-600 mt-1">Please review your analysis before submitting</p>
                        </div>
                        <div class="p-6">
                            <div class="bg-gradient-to-r from-blue-50 to-purple-50 border-l-4 border-blue-600 p-4 mb-6">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="font-semibold text-gray-900">You are about to save analysis for:</p>
                                </div>
                                <p class="font-bold text-lg text-gray-900" x-text="selectedAcademicYear"></p>
                                <p class="text-sm text-gray-700">Province: <span x-text="selectedProvince"></span></p>
                            </div>
                            <div x-show="analysisId" class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                                <p class="text-sm font-semibold text-yellow-800">
                                    ⚠️ This will create a new version and mark the current analysis as inactive!
                                </p>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-3">Analysis Content:</h4>
                            <div class="bg-gray-50 rounded-lg p-4 mb-6 max-h-96 overflow-y-auto border border-gray-200 prose prose-sm max-w-none">
                                <div x-html="analysisText"></div>
                            </div>
                            <div class="bg-gray-100 rounded-lg p-4 flex justify-between items-center">
                                <span class="font-bold text-gray-900">Word Count:</span>
                                <span class="text-lg font-bold text-blue-600" x-text="analysisText.trim().split(/\s+/).filter(w => w.length > 0).length + ' words'"></span>
                            </div>
                        </div>
                        <div class="p-6 border-t border-gray-200 bg-gray-50 flex gap-3">
                            <button @click="showConfirmModal = false" class="flex-1 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg">Cancel</button>
                            <button @click="confirmSave()" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg">Save Analysis</button>
                        </div>
                    </div>
                </div>

                <!-- SUCCESS MODAL -->
                <div x-show="showSuccessModal" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-white/30 p-4"
                     @click.self="showSuccessModal = false">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all" @click.stop>
                        <div class="p-8 text-center">
                            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Success!</h3>
                            <p class="text-gray-600 mb-2">Analysis has been saved successfully.</p>
                            <div class="bg-green-50 rounded-lg p-3 mb-6 border border-green-200">
                                <p class="text-xs text-green-700">
                                    <strong x-text="selectedProvince"></strong> • <strong x-text="selectedAcademicYear"></strong>
                                </p>
                            </div>
                            <button @click="showSuccessModal = false" class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg">Continue</button>
                        </div>
                    </div>
                </div>

                <!-- COPY ARCHIVE MODAL -->
                <div x-show="showCopyModal" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-white/30"
                     @click.self="showCopyModal = false">
                    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6" @click.stop>
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-slate-800 mb-2">Copy from Archive</h3>
                                <p class="text-sm text-slate-600 mb-4">Copy analysis text from <strong x-text="selectedArchive?.version"></strong>?</p>
                                <div class="bg-slate-50 rounded-lg p-3 mb-4 border border-slate-200 max-h-32 overflow-y-auto">
                                    <p class="text-xs text-slate-600" x-text="selectedArchive?.analysis_text"></p>
                                </div>
                                <p class="text-xs text-slate-500">This will replace your current text. Any unsaved changes will be lost.</p>
                            </div>
                        </div>
                        <div class="flex gap-3 justify-end">
                            <button @click="showCopyModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition">Cancel</button>
                            <button @click="confirmCopy()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">Copy Text</button>
                        </div>
                    </div>
                </div>

                <!-- RESET MODAL -->
                <div x-show="showResetModal" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-white/30"
                     @click.self="showResetModal = false">
                    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6" @click.stop>
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-slate-800 mb-2">Reset to Default</h3>
                                <p class="text-sm text-slate-600 mb-4">Are you sure you want to reset to the default text?</p>
                                <p class="text-xs text-orange-600 font-semibold">⚠️ Any unsaved changes will be lost!</p>
                            </div>
                        </div>
                        <div class="flex gap-3 justify-end">
                            <button @click="showResetModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition">Cancel</button>
                            <button @click="confirmReset()" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition">Reset to Default</button>
                        </div>
                    </div>
                </div>

                <!-- Error Toast -->
                <div x-show="showError" x-transition @click="showError = false"
                     class="fixed bottom-6 right-6 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg cursor-pointer z-50">
                    ❌ <span x-text="errorMessage"></span>
                </div>

                <!-- Success Toast -->
                <div x-show="showSuccessToast" x-transition @click="showSuccessToast = false"
                     class="fixed bottom-6 right-6 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg cursor-pointer z-50">
                    ✅ <span x-text="successToastMessage"></span>
                </div>
            </div>
        </div>
        </div>
    </div>

    <script>
        function supplySideEditor() {
            return {
                provinces: [],
                academicYears: [],
                selectedProvince: 'All Provinces',
                selectedAcademicYear: null,
                analysisText: '',
                originalText: '',
                analysisId: null,
                lastUpdated: null,
                loading: false,
                hasChanges: false,
                showError: false,
                errorMessage: '',
                quill: null,
                showConfirmModal: false,
                showSuccessModal: false,
                showCopyModal: false,
                showResetModal: false,
                selectedArchive: null,
                archivedAnalyses: [],
                loadingArchives: false,
                showSuccessToast: false,
                successToastMessage: '',

                async init() {
                    await this.loadOptions();
                    await this.loadAnalysis();
                    await this.loadArchivedAnalyses();
                    this.$nextTick(() => {
                        this.initQuillEditor();
                    });
                },
                
                getWordCount() {
                    const text = this.analysisText.replace(/<[^>]*>/g, '').trim();
                    const words = text.split(/\s+/).filter(w => w.length > 0);
                    return words.length;
                },

                initQuillEditor() {
                    if (this.quill) return;
                    const editorElement = document.getElementById('quillEditor');
                    if (!editorElement) { console.error('Quill editor element not found'); return; }
                    const SizeStyle = Quill.import('attributors/style/size');
                    SizeStyle.whitelist = ['8pt', '9pt', '10pt', '11pt', '12pt', '14pt', '16pt', '18pt', '20pt', '22pt', '24pt', '28pt', '36pt', '48pt', '72pt'];
                    Quill.register(SizeStyle, true);
                    this.quill = new Quill('#quillEditor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{ 'font': [] }, { 'size': ['8pt', '9pt', '10pt', '11pt', '12pt', '14pt', '16pt', '18pt', '20pt', '22pt', '24pt', '28pt', '36pt', '48pt', '72pt'] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'header': [1, 2, 3, false] }],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                [{ 'align': [] }],
                                ['link'],
                                ['clean']
                            ]
                        },
                        placeholder: 'Enter executive analysis for supply side...'
                    });
                    this.quill.on('text-change', () => {
                        this.analysisText = this.quill.root.innerHTML;
                        this.hasChanges = true;
                    });
                    if (this.analysisText) { this.quill.root.innerHTML = this.analysisText; }
                },

                async loadOptions() {
                    try {
                        const response = await fetch('/api/supply-side-analysis/options');
                        const data = await response.json();
                        if (data.success) {
                            this.provinces = data.provinces;
                            this.academicYears = data.academic_years;
                            if (this.academicYears.length > 0) { this.selectedAcademicYear = this.academicYears[0]; }
                        }
                    } catch (error) { console.error('Error loading options:', error); this.showErrorToast('Failed to load options'); }
                },

                async loadAnalysis() {
                    if (!this.selectedAcademicYear) return;
                    this.loading = true;
                    try {
                        const params = new URLSearchParams({ province: this.selectedProvince, academic_year: this.selectedAcademicYear });
                        const response = await fetch(`/api/supply-side-analysis/show?${params}`);
                        const data = await response.json();
                        if (data.success) {
                            this.analysisId = data.data.id;
                            this.analysisText = data.data.analysis_text;
                            this.originalText = data.data.analysis_text;
                            this.lastUpdated = data.data.updated_at ? new Date(data.data.updated_at).toLocaleString() : null;
                            this.hasChanges = false;
                            if (this.quill) { this.quill.root.innerHTML = this.analysisText; }
                        }
                    } catch (error) { console.error('Error loading analysis:', error); this.showErrorToast('Failed to load analysis'); }
                    finally { this.loading = false; }
                },

                async loadArchivedAnalyses() {
                    this.loadingArchives = true;
                    try {
                        const params = new URLSearchParams({ province: this.selectedProvince, academic_year: this.selectedAcademicYear });
                        const response = await fetch(`/api/supply-side-analysis/archives?${params}`);
                        const data = await response.json();
                        if (data.success) { this.archivedAnalyses = data.archives; }
                    } catch (error) { console.error('Error loading archives:', error); }
                    finally { this.loadingArchives = false; }
                },

                copyFromArchive(archive) { this.selectedArchive = archive; this.showCopyModal = true; },

                confirmCopy() {
                    if (this.selectedArchive) {
                        this.analysisText = this.selectedArchive.analysis_text;
                        this.hasChanges = true;
                        this.showCopyModal = false;
                        if (this.quill) { this.quill.root.innerHTML = this.analysisText; }
                        this.showSuccessToastMessage('Text copied from ' + this.selectedArchive.version);
                        this.selectedArchive = null;
                    }
                },

                async confirmSave() {
                    this.showConfirmModal = false;
                    this.loading = true;
                    try {
                        const response = await fetch('/api/supply-side-analysis/save', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({ province: this.selectedProvince, academic_year: this.selectedAcademicYear, analysis_text: this.analysisText })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.originalText = this.analysisText;
                            this.hasChanges = false;
                            this.analysisId = data.data.id;
                            this.lastUpdated = new Date().toLocaleString();
                            this.showSuccessModal = true;
                            await this.loadArchivedAnalyses();
                        } else { throw new Error(data.error || 'Failed to save'); }
                    } catch (error) { console.error('Error saving analysis:', error); this.showErrorToast('Failed to save analysis'); }
                    finally { this.loading = false; }
                },

                resetToDefault() { this.showResetModal = true; },

                async confirmReset() {
                    this.showResetModal = false;
                    try {
                        const response = await fetch('/api/supply-side-analysis/reset');
                        const data = await response.json();
                        if (data.success) {
                            this.analysisText = data.default_text;
                            this.hasChanges = true;
                            if (this.quill) { this.quill.root.innerHTML = this.analysisText; }
                            this.showSuccessToastMessage('Reset to default text');
                        }
                    } catch (error) { console.error('Error resetting:', error); this.showErrorToast('Failed to reset to default'); }
                },

                showErrorToast(message) {
                    this.errorMessage = message; this.showError = true;
                    setTimeout(() => { this.showError = false; }, 3000);
                },

                showSuccessToastMessage(message) {
                    this.successToastMessage = message; this.showSuccessToast = true;
                    setTimeout(() => { this.showSuccessToast = false; }, 3000);
                }
            };
        }
    </script>
</body>
</html>