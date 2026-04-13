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
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="24pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="24pt"]::before { content: '24'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="36pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="36pt"]::before { content: '36'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label:not([data-value])::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item:not([data-value])::before { content: '11'; }
        .ql-editor .ql-size-8pt  { font-size: 8pt;  }
        .ql-editor .ql-size-10pt { font-size: 10pt; }
        .ql-editor .ql-size-11pt { font-size: 11pt; }
        .ql-editor .ql-size-12pt { font-size: 12pt; }
        .ql-editor .ql-size-14pt { font-size: 14pt; }
        .ql-editor .ql-size-16pt { font-size: 16pt; }
        .ql-editor .ql-size-18pt { font-size: 18pt; }
        .ql-editor .ql-size-24pt { font-size: 24pt; }
        .ql-editor .ql-size-36pt { font-size: 36pt; }
    </style>

    {{-- Load our JS first so adminSupplySideEditor() is on window before Alpine boots --}}
    @vite('resources/js/admin/Module3/supply-side-editor.js')

    {{-- Alpine must come AFTER so it finds the function already defined --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Supply Side Analysis Editor • Admin</title>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden">
    @include('partials.sidebar')

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Supply Side Analysis Editor • Admin</h2>
            <div class="flex items-center gap-4">
                <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Region XI • {{ date('Y') }}
                    </div>
                <div class="w-10 h-10 bg-amber-100 rounded-full border-2 border-amber-500"></div>
            </div>
        </header>

        <div class="flex-1 overflow-auto">
            <div x-data="adminSupplySideEditor()" x-init="init()" class="p-6">

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Province Select -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Province
                            </label>
                            <select
                                x-model="selectedProvince"
                                @change="await loadYears(); await loadData()"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <template x-for="province in provinces" :key="province">
                                    <option :value="province" x-text="province"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Academic Year Select -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Academic Year
                            </label>
                            <select
                                x-model="selectedAcademicYear"
                                @change="loadData()"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <template x-for="year in academicYears" :key="year">
                                    <option :value="year" x-text="year"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Status Badge (shown once province + year are selected) -->
                    <div class="mt-4 flex items-center gap-3" x-show="selectedAcademicYear">
                        <span class="text-sm text-slate-500 font-medium">Current status:</span>

                        <!-- No submission yet -->
                        <span x-show="!pendingSubmission && !publishedExists"
                              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            No submission yet
                        </span>

                        <!-- Pending review -->
                        <span x-show="pendingSubmission"
                              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Pending review
                            <span x-show="pendingSubmission?.submitted_at" class="font-normal text-amber-600">
                                — submitted <span x-text="formatDate(pendingSubmission?.submitted_at)"></span>
                            </span>
                        </span>

                        <!-- Published -->
                        <span x-show="publishedExists && !pendingSubmission"
                              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Published
                        </span>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 gap-6">

                    <!-- Editor Card (full width) -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">

                        <!-- Card Header -->
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-2.5 rounded-xl">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800">EXECUTIVE ANALYSIS: SUPPLY SIDE</h3>
                                    <p class="text-xs text-slate-500" x-show="pendingSubmission">
                                        Draft saved: <span x-text="formatDate(pendingSubmission?.submitted_at)"></span>
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <button
                                    @click="loadDefaultText()"
                                    :disabled="loading || !!pendingSubmission"
                                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    Reset to Default
                                </button>

                                <!-- Pending state: locked button -->
                                <template x-if="pendingSubmission && !publishedExists">
                                    <button disabled
                                        class="px-4 py-2 bg-amber-100 text-amber-700 border border-amber-300 rounded-lg font-medium cursor-not-allowed flex items-center gap-2">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Awaiting Review
                                    </button>
                                </template>

                                <!-- Published state: locked button -->
                                <template x-if="publishedExists">
                                    <button disabled
                                        class="px-4 py-2 bg-green-100 text-green-700 border border-green-300 rounded-lg font-medium cursor-not-allowed flex items-center gap-2">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Already Published
                                    </button>
                                </template>

                                <!-- Normal: can submit -->
                                <template x-if="!pendingSubmission && !publishedExists">
                                    <button
                                        @click="showConfirmModal = true"
                                        :disabled="loading || !hasChanges"
                                        class="px-4 py-2 text-white rounded-lg font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
                                        :class="hasChanges ? 'bg-amber-600 hover:bg-amber-700' : 'bg-slate-400'">
                                        <svg class="w-4 h-4 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        Submit for Review
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Pending Banner -->
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
                                    This analysis was submitted on <strong x-text="formatDate(pendingSubmission?.submitted_at)"></strong> and is awaiting the statistician's review. You cannot submit again until it is reviewed.
                                </p>
                            </div>
                        </div>

                        <!-- Published Banner -->
                        <div x-show="publishedExists"
                             class="mb-5 flex items-start gap-3 bg-green-50 border border-green-200 rounded-xl px-5 py-4">
                            <div class="flex-shrink-0 w-9 h-9 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-green-800 text-sm">Analysis Already Published</p>
                                <p class="text-xs text-green-700 mt-0.5">
                                    This analysis for <strong x-text="selectedProvince"></strong> — <strong x-text="selectedAcademicYear"></strong> has been reviewed and published on <strong x-text="publishedUpdatedAt"></strong>. It is now live on the public page.
                                </p>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div x-show="loading" class="flex items-center justify-center py-12">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600"></div>
                        </div>

                        <!-- Quill Editor -->
                        <div x-show="!loading">
                            <div id="quillEditor"
                                 :class="(pendingSubmission || publishedExists) ? 'opacity-60 pointer-events-none select-none' : ''"
                                 style="height: 400px; max-height: 500px; overflow-y: auto;"></div>
                            <input type="hidden" x-model="analysisText">
                            <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                                <span><span x-text="getWordCount()"></span> words</span>
                                <span x-show="hasChanges && !pendingSubmission && !publishedExists" class="text-orange-600 font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Unsaved changes
                                </span>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div x-show="!loading" class="mt-6 pt-6 border-t border-slate-200">
                            <h4 class="text-sm font-bold text-slate-700 mb-3 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Preview (How it will appear on public page)
                            </h4>
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

                </div><!-- end grid -->

                <!-- ── CONFIRM SUBMIT MODAL ── -->
                <div x-show="showConfirmModal"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-white/30"
                     @click.self="showConfirmModal = false">
                    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6" @click.stop>
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-slate-800 mb-1">Submit for Review?</h3>
                                <p class="text-sm text-slate-600">
                                    Your analysis for <strong x-text="selectedProvince"></strong> — <strong x-text="selectedAcademicYear"></strong> will be sent to the statistician for review.
                                </p>
                                <p class="text-xs text-slate-500 mt-2">The statistician may edit it before publishing to the public page.</p>
                            </div>
                        </div>
                        <div class="flex gap-3 justify-end">
                            <button @click="showConfirmModal = false"
                                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition">
                                Cancel
                            </button>
                            <button @click="confirmSubmit()"
                                    class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium transition">
                                Submit for Review
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ── SUCCESS MODAL ── -->
                <div x-show="showSuccessModal"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-white/30"
                     @click.self="showSuccessModal = false">
                    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6 text-center" @click.stop>
                        <div class="flex items-center justify-center mb-4">
                            <div class="w-14 h-14 rounded-full border-2 border-green-500 flex items-center justify-center">
                                <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Submitted Successfully!</h3>
                        <p class="text-sm text-slate-600 mb-6">
                            Your analysis has been sent to the statistician. They will review, edit if needed, and publish it to the public Supply Side page.
                        </p>
                        <button @click="showSuccessModal = false"
                                class="px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium transition">
                            Done
                        </button>
                    </div>
                </div>

                <!-- ── COPY FROM ARCHIVE MODAL ── -->
                <div x-show="showCopyModal"
                     x-transition:enter="transition ease-out duration-200"
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
                                <p class="text-sm text-slate-600 mb-4">
                                    Copy analysis text from <strong x-text="selectedArchive?.version || selectedArchive?.academic_year"></strong>?
                                </p>
                                <div class="bg-slate-50 rounded-lg p-3 mb-4 border border-slate-200 max-h-32 overflow-y-auto">
                                    <p class="text-xs text-slate-600" x-text="selectedArchive?.analysis_text"></p>
                                </div>
                                <p class="text-xs text-slate-500">This will replace your current draft. Any unsaved changes will be lost.</p>
                            </div>
                        </div>
                        <div class="flex gap-3 justify-end">
                            <button @click="showCopyModal = false"
                                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition">
                                Cancel
                            </button>
                            <button @click="confirmCopy()"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                                Copy Text
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Error Toast -->
                <div x-show="showError" x-transition @click="showError = false"
                     class="fixed bottom-6 right-6 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg cursor-pointer z-50 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span x-text="errorMessage"></span>
                </div>

                <!-- Success Toast -->
                <div x-show="showSuccessToast" x-transition @click="showSuccessToast = false"
                     class="fixed bottom-6 right-6 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg cursor-pointer z-50 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="successToastMessage"></span>
                </div>

            </div><!-- end x-data -->
        </div>
    </div>

</body>
</html>