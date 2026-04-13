<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    
    <!-- Quill.js Rich Text Editor -->
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.6/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.6/dist/quill.js"></script>
    
    <!-- Custom Quill Toolbar Styling -->
    <style>
        .ql-toolbar.ql-snow { padding: 8px 8px; border-radius: 0; border-left: none; border-right: none; }
        /* Custom scrollbar — same as public page */
        .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .ql-toolbar.ql-snow .ql-formats { margin-right: 20px; }
        .ql-toolbar.ql-snow button { width: 32px !important; height: 32px !important; padding: 4px; }
        .ql-toolbar.ql-snow .ql-stroke { stroke-width: 2.5; }
        .ql-toolbar.ql-snow select { height: 32px !important; padding: 4px 8px; }
        .ql-container.ql-snow { border-left: none; border-right: none; border-bottom: none; }
        .ql-editor { min-height: 580px; font-size: 14px; line-height: 1.7; padding: 20px 24px; }
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
        .ql-editor .ql-size-8pt  { font-size: 8pt;  }
        .ql-editor .ql-size-9pt  { font-size: 9pt;  }
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

        /* Ensure Quill HTML output renders bullets correctly everywhere */
        [x-html] ul, .prose ul { list-style-type: disc; padding-left: 1.5rem; margin: 0.5rem 0; }
        [x-html] ol, .prose ol { list-style-type: decimal; padding-left: 1.5rem; margin: 0.5rem 0; }
        [x-html] li, .prose li { margin: 0.2rem 0; display: list-item; }

        /* ── Preview: render Quill-formatted HTML correctly ── */
        .ql-editor-preview { line-height: 1.7; }
        .ql-editor-preview strong, .ql-editor-preview b { font-weight: 700; }
        .ql-editor-preview em, .ql-editor-preview i { font-style: italic; }
        .ql-editor-preview u { text-decoration: underline; }
        .ql-editor-preview s { text-decoration: line-through; }
        .ql-editor-preview h1 { font-size: 1.5rem; font-weight: 700; margin: 0.75rem 0; }
        .ql-editor-preview h2 { font-size: 1.25rem; font-weight: 700; margin: 0.5rem 0; }
        .ql-editor-preview h3 { font-size: 1.1rem; font-weight: 600; margin: 0.5rem 0; }
        .ql-editor-preview p { margin: 0.4rem 0; }
        .ql-editor-preview ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin: 0.5rem 0 !important; }
        .ql-editor-preview ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin: 0.5rem 0 !important; }
        .ql-editor-preview li { display: list-item !important; margin: 0.2rem 0 !important; }
        .ql-editor-preview .ql-align-justify { text-align: justify; }
        .ql-editor-preview .ql-align-center  { text-align: center; }
        .ql-editor-preview .ql-align-right   { text-align: right; }
        .ql-editor-preview .ql-align-left    { text-align: left; }
        .ql-editor-preview blockquote { border-left: 4px solid #cbd5e1; padding-left: 1rem; color: #64748b; margin: 0.5rem 0; }
    </style>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/js/statistician/supply-side-editor.js')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>
    <title>Supply Side Analysis Editor</title>
</head>
<body x-data="supplySideEditor()" x-init="init()" class="bg-slate-100 flex h-screen overflow-hidden">
    @include('partials.statisticianSidebar')

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-slate-200 shadow-sm flex-shrink-0">
            <!-- Top row -->
            <div class="h-14 flex items-center justify-between px-8">
                <h2 class="text-lg font-bold text-slate-800">Supply Side Analysis Editor <span class="text-slate-400 font-normal">• Statistician</span></h2>
                <div class="flex items-center gap-4">
                    <div class="bg-slate-100 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Region XI • {{ date('Y') }}</div>
                    <div class="w-9 h-9 bg-blue-100 rounded-full border-2 border-blue-500"></div>
                </div>
            </div>
            <!-- Main mode tabs -->
            <div class="flex px-8 gap-1 border-t border-slate-100">
                <button @click="mainTab = 'editor'"
                    class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold border-b-2 transition -mb-px"
                    :class="mainTab === 'editor'
                        ? 'border-blue-600 text-blue-700 bg-blue-50/60'
                        : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Editor
                    <span x-show="allPendingSubmissions.length > 0"
                        class="text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none"
                        :class="mainTab === 'editor' ? 'bg-amber-500 text-white' : 'bg-amber-100 text-amber-700'"
                        x-text="allPendingSubmissions.length"></span>
                </button>
                <button @click="mainTab = 'approved'"
                    class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold border-b-2 transition -mb-px"
                    :class="mainTab === 'approved'
                        ? 'border-emerald-600 text-emerald-700 bg-emerald-50/60'
                        : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Approved
                    <span x-show="allApprovedRecords.length > 0"
                        class="text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none"
                        :class="mainTab === 'approved' ? 'bg-emerald-500 text-white' : 'bg-emerald-100 text-emerald-700'"
                        x-text="allApprovedRecords.length"></span>
                </button>
            </div>
        </header>

        <!-- Side Drawer Layout — bg canvas with cards inside -->
        <div class="flex-1 overflow-y-auto bg-slate-100 p-6">

            <!-- ══ APPROVED PANEL ══ -->
            <div x-show="mainTab === 'approved'" x-cloak>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <!-- Panel header -->
                    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-gradient-to-r from-emerald-50 to-teal-50">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Approved & Published Analyses</p>
                                <p class="text-xs text-slate-500">Read-only history of all published supply side analyses</p>
                            </div>
                        </div>
                        <span class="text-xs bg-emerald-100 text-emerald-700 font-bold px-3 py-1 rounded-full" x-text="allApprovedRecords.length + ' record(s)'"></span>
                    </div>

                    <!-- Loading -->
                    <div x-show="loadingAllApproved" class="flex items-center justify-center py-20">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-500"></div>
                        <span class="ml-3 text-slate-500 text-sm">Loading approved records...</span>
                    </div>

                    <!-- Empty state -->
                    <div x-show="!loadingAllApproved && allApprovedRecords.length === 0" class="flex flex-col items-center justify-center py-24 text-slate-400">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-sm font-semibold mb-1">No approved analyses yet</p>
                        <p class="text-xs text-slate-300">Published records will appear here</p>
                    </div>

                    <!-- Records grid -->
                    <div x-show="!loadingAllApproved && allApprovedRecords.length > 0" class="p-6">
                        <!-- Province/Year selector pills -->
                        <div class="flex flex-wrap gap-2 mb-6">
                            <template x-for="(item, idx) in allApprovedRecords" :key="idx">
                                <button @click="approvedSelected = item"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg border text-xs font-semibold transition"
                                    :class="approvedSelected && approvedSelected.id === item.id
                                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm'
                                        : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-300 hover:text-emerald-700'">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span x-text="item.province + ' • ' + item.academic_year"></span>
                                </button>
                            </template>
                        </div>

                        <!-- Detail pane -->
                        <template x-if="approvedSelected">
                            <div class="border border-slate-200 rounded-xl overflow-hidden">
                                <!-- Meta bar -->
                                <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center gap-4 text-xs text-slate-600">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <strong x-text="approvedSelected.province"></strong>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <strong x-text="approvedSelected.academic_year"></strong>
                                    </span>
                                    <span x-show="approvedSelected.submitted_by" class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Submitted by <strong class="ml-1" x-text="approvedSelected.submitted_by"></strong>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Published <strong class="ml-1" x-text="formatDate(approvedSelected.approved_at)"></strong>
                                    </span>
                                    <span class="ml-auto flex items-center gap-1 bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-bold text-[11px]">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                        Published
                                    </span>
                                </div>

                                <!-- Analysis text content -->
                                <div class="p-6 bg-white">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Analysis Text</span>
                                        <span class="h-px flex-1 bg-slate-100"></span>
                                        <span class="text-[10px] text-slate-400 font-medium">Rich text content</span>
                                    </div>
                                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-5 prose prose-sm max-w-none text-slate-700 leading-relaxed overflow-y-auto max-h-[60vh] custom-scrollbar"
                                         x-html="approvedSelected.analysis_text || '<em>No content</em>'"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- ══ EDITOR PANEL ══ -->
            <div x-show="mainTab === 'editor'">

            <!-- ── EDITOR MODE: two-column layout ── -->
            <div x-show="!showPreviewModal" class="flex gap-6 items-stretch">

            <!-- ═══════════════════════════════════════════
                 LEFT CARD — Filters + Status
            ════════════════════════════════════════════ -->
            <div class="w-72 flex-shrink-0 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                <!-- Panel Header -->
                <div class="px-5 py-4 border-b border-slate-200 bg-white flex items-center justify-between">
                    <p class="text-sm font-bold text-slate-700 uppercase tracking-wide">Status</p>
                </div>

                <!-- Content -->
                <div class="p-5 space-y-5">

                    <!-- Lock notice -->
                    <div x-show="!isUnlocked" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-400 text-center">
                        <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> Load a draft below to unlock the editor
                    </div>

                    <!-- Currently Published Card -->
                    <div x-show="lastUpdated && isUnlocked"
                         class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-sm font-bold text-green-800 mb-1"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Currently Published</p>
                        <p class="text-xs text-green-600">Last updated: <span x-text="lastUpdated"></span></p>
                    </div>

                    <!-- ── All Pending Submissions ── -->
                    <div class="border-t border-slate-200 pt-4">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-bold text-slate-600"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg> Pending Submissions</p>
                            <span x-show="allPendingSubmissions.length > 0"
                                  class="bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                                  x-text="allPendingSubmissions.length"></span>
                        </div>
                        <p class="text-xs text-slate-400 mb-3">Load a draft to unlock the editor.</p>

                        <div x-show="loadingAllPending" class="flex justify-center py-4">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-amber-500"></div>
                        </div>

                        <!-- Scrollable list -->
                        <div x-show="!loadingAllPending" class="space-y-2 max-h-56 overflow-y-auto pr-1 custom-scrollbar">
                            <template x-for="item in allPendingSubmissions" :key="item.id">
                                <div class="border border-amber-200 rounded-lg p-3 bg-amber-50 hover:border-amber-400 hover:bg-amber-100 transition">
                                    <div class="flex items-center justify-between mb-0.5">
                                        <p class="font-semibold text-xs text-amber-900 truncate" x-text="item.province"></p>
                                        <span class="text-xs bg-amber-200 text-amber-800 px-1.5 py-0.5 rounded font-medium ml-1 flex-shrink-0" x-text="item.academic_year"></span>
                                    </div>
                                    <p class="text-xs text-amber-700 mb-0.5">By <strong x-text="item.submitted_by"></strong></p>
                                    <p class="text-xs text-amber-500 mb-2" x-text="formatDate(item.submitted_at)"></p>
                                    <button
                                        @click="loadPendingItemIntoEditor(item)"
                                        class="w-full text-xs bg-amber-600 hover:bg-amber-700 text-white py-1.5 rounded-lg font-medium transition">
                                        <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg> Load Draft
                                    </button>
                                </div>
                            </template>

                            <div x-show="allPendingSubmissions.length === 0" class="text-center py-6 text-slate-400">
                                <p class="text-sm">No pending submissions</p>
                            </div>
                        </div>
                    </div>

                    <!-- Archived Analysis — always visible, no pending gate -->
                    <div class="border-t border-slate-200 pt-4">
                        <p class="text-sm font-bold text-slate-600 mb-1"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg> Archived Analysis</p>
                        <p class="text-xs text-slate-400 mb-3">Click an archive to copy its text into the editor.</p>

                        <div x-show="loadingArchives" class="flex  justify-center py-4">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-500"></div>
                        </div>

                        <div x-show="!loadingArchives" class="space-y-2 max-h-30 overflow-y-auto pr-1 custom-scrollbars">
                            <template x-for="archive in archivedAnalysis" :key="archive.id">
                                <div class="border border-slate-200 rounded-lg bg-white hover:border-indigo-300 hover:bg-indigo-50 transition group overflow-y-auto">

                                    <!-- Header row — click to toggle preview -->
                                    <div class="flex items-center justify-between p-3 cursor-pointer min-h-[56px]"
                                         @click="archive._open = !archive._open">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-slate-800 truncate" x-text="archive.academic_year"></p>
                                            <p class="text-xs text-slate-400 mt-0.5 truncate" x-text="archive.province + ' • ' + archive.updated_at"></p>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <!-- Eye toggle -->
                                            <span class="text-slate-400 hover:text-indigo-500 transition p-1 rounded">
                                                <svg x-show="!archive._open" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <svg x-show="archive._open" class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.223-3.592M6.343 6.343A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.97 9.97 0 01-1.284 2.482M3 3l18 18"/>
                                                </svg>
                                            </span>
                                            <!-- Copy icon on hover -->
                                            <svg class="w-4 h-4 text-indigo-500 opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Expandable preview -->
                                    <div x-show="archive._open"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 -translate-y-1"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         class="px-3 pb-3 border-t border-slate-100">
                                        <div class="mt-2 max-h-40 overflow-y-auto custom-scrollbar rounded bg-slate-50 border border-slate-200 p-2 text-xs text-slate-600 leading-relaxed"
                                             x-html="archive.analysis_text || '<em class=\'text-slate-400\'>No content.</em>'">
                                        </div>
                                        <button
                                            @click="copyFromArchive(archive)"
                                            class="mt-2 w-full text-xs bg-indigo-600 hover:bg-indigo-700 text-white py-1.5 rounded-lg font-medium transition">
                                            <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg> Copy to Editor
                                        </button>
                                    </div>

                                </div>
                            </template>

                            <div x-show="archivedAnalysis.length === 0" class="text-center py-6 text-slate-400">
                                <p class="text-sm">No archived analysis</p>
                            </div>
                        </div>
                    </div>

                </div><!-- end content -->
            </div><!-- end left card -->

            <!-- ═══════════════════════════════════════════
                 RIGHT CARD — Editor / Preview Toggle
            ════════════════════════════════════════════ -->
            <div class="flex-1 flex flex-col gap-6">

                <!-- ── EDITOR MODE ── -->
                <div x-show="!showPreviewModal" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col relative">

                    <!-- Lock overlay -->
                    <div x-show="!isUnlocked"
                         class="absolute inset-0 z-10 bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center rounded-xl">
                        <div class="mb-3 w-14 h-14 mx-auto bg-slate-100 rounded-full flex items-center justify-center"><svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                        <p class="text-sm font-bold text-slate-600 mb-1">Editor Locked</p>
                        <p class="text-xs text-slate-400">Load a pending draft to start editing</p>
                    </div>

                    <!-- Top Bar -->
                    <div class="border-b border-slate-200 px-5 py-3 flex items-center justify-between flex-shrink-0">
                        <div class="flex items-center gap-2">
                            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-1.5 rounded-lg">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-700"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> EXECUTIVE ANALYSIS: SUPPLY SIDE</p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                @click="showPreviewModal = true"
                                class="text-xs bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 px-3 py-1.5 rounded-lg transition font-medium">
                                <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> Preview
                            </button>
                            <button
                                @click="resetToDefault()"
                                :disabled="loading"
                                class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg transition disabled:opacity-50">
                                Reset
                            </button>
                            <button
                                @click="showConfirmModal = true"
                                :disabled="loading || !hasChanges"
                                class="text-xs px-4 py-1.5 rounded-lg font-semibold transition disabled:opacity-50"
                                :class="hasChanges
                                    ? 'bg-blue-600 hover:bg-blue-700 text-white'
                                    : 'bg-slate-200 text-slate-400 cursor-not-allowed'">
                                <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg> Save &amp; Publish
                            </button>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div x-show="loading" class="flex items-center justify-center py-20">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>

                    <!-- Quill -->
                    <div x-show="!loading">
                        <div id="quillEditor"></div>
                        <input type="hidden" x-model="analysisText">
                    </div>

                    <!-- Status Bar -->
                    <div class="border-t border-slate-100 px-5 py-2 bg-slate-50 flex items-center justify-between">
                        <span class="text-xs text-slate-400"><span x-text="getWordCount()"></span> words</span>
                        <span x-show="hasChanges" class="text-xs text-orange-500 font-medium"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Unsaved changes</span>
                    </div>

                </div><!-- end editor mode -->

            </div><!-- end right card -->
            </div><!-- end editor flex row -->
            </div><!-- end editor panel -->

            <!-- ── PREVIEW MODE — fixed full viewport, independent of parent ── -->
            <div x-show="showPreviewModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="fixed inset-0 z-40 bg-slate-100 overflow-y-auto">

                <!-- Back to editor sticky bar at top -->
                <div class="bg-slate-900 px-6 py-2 flex items-center justify-end sticky top-0 z-10">
                    <button @click="showPreviewModal = false"
                            class="flex items-center gap-2 text-xs bg-slate-700 hover:bg-slate-600 text-slate-300 px-3 py-1.5 rounded-lg transition">
                        <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Back to Editor
                    </button>
                </div>

                <!-- Same outer wrapper as real public page -->
                <div class="max-w-screen-xl mx-auto px-4">
                    <div class="mt-6 rounded-2xl overflow-hidden shadow-lg">

                        <!-- Dark header — exact copy from main UI (but not a button, no collapse) -->
                        <div class="w-full bg-slate-800 px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="bg-white/10 p-2.5 rounded-xl">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <h3 class="text-base font-bold text-white">Enrollment Overview</h3>
                                    <p class="text-xs text-slate-400">Discipline market share &amp; executive supply analysis</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-slate-400 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                    <!-- Content area — exact copy from main UI -->
                    <div class="bg-slate-50 border border-t-0 border-slate-200 rounded-b-2xl overflow-hidden">

                        <!-- Panel Filter Bar — exact copy -->
                        <div class="flex items-center justify-end px-6 py-3 bg-white border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm">
                                    <span class="text-slate-400"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
                                    <span class="text-sm text-slate-500 font-medium">Province:</span>
                                    <span class="text-sm font-bold text-slate-800" x-text="selectedProvince"></span>
                                </div>
                                <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm">
                                    <span class="text-slate-400"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>
                                    <span class="text-sm text-slate-500 font-medium">Year:</span>
                                    <span class="text-sm font-bold text-slate-800" x-text="selectedAcademicYear"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Cards Row — matches public page layout (stacked full-width) -->
                        <div class="flex flex-col gap-6 p-6">

                            <!-- Executive Analysis: Supply Side — full width, matches public page -->
                            <div class="w-full bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                                <div class="flex flex-col p-6">
                                    <div class="flex items-start gap-3 mb-4 flex-shrink-0">
                                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-2.5 rounded-xl shadow-lg">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-800">EXECUTIVE ANALYSIS: SUPPLY SIDE</h3>
                                        </div>
                                    </div>
                                    <!-- Dynamic Analysis Text — full width, Quill styles applied, matches public page -->
                                    <div class="flex-1 text-sm text-slate-700 prose prose-sm max-w-none leading-relaxed ql-editor-preview"
                                         style="font-family: inherit;"
                                         x-html="analysisText || '<em class=\'text-slate-400\'>No content yet.</em>'">
                                    </div>
                                </div>
                            </div>

                            <!-- Discipline Market Share Pie Chart — full width, matches public page -->
                            <div class="w-full bg-slate-50 rounded-2xl shadow-xl border border-slate-200 p-6 relative overflow-hidden">
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <div class="flex items-start gap-3">
                                        <div class="bg-gradient-to-br from-purple-500 to-pink-600 p-2.5 rounded-xl shadow-lg">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-800">Distribution of enrollees</h3>
                                        </div>
                                    </div>
                                    <!-- Expand Button — disabled in preview -->
                                    <button class="flex-shrink-0 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-semibold rounded-lg shadow-md flex items-center gap-2 text-sm opacity-60 cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                        </svg>
                                        Expand
                                    </button>
                                </div>

                                <!-- Pie Chart with Side Legends — matches public page pie-inline-layout -->
                                <div class="flex items-center justify-center gap-10 flex-wrap mt-6">
                                    <!-- LEFT LEGEND -->
                                    <div class="w-56 shrink-0 space-y-3">
                                        <template x-for="(item, index) in previewDisciplineLeft" :key="item.name">
                                            <div class="flex items-start gap-2 min-w-0">
                                                <div class="w-3 h-3 rounded-full flex-shrink-0 mt-1"
                                                     :style="`background-color: ${item.color}`"></div>
                                                <div class="min-w-0">
                                                    <div class="text-slate-700 font-medium text-sm leading-snug" x-text="item.name"></div>
                                                    <div class="text-slate-500 text-xs font-semibold" x-text="item.pct + '%'"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- CENTER PIE CHART -->
                                    <div class="flex-shrink-0 flex items-center justify-center">
                                        <canvas id="previewDonutChart" width="300" height="300"></canvas>
                                    </div>

                                    <!-- RIGHT LEGEND -->
                                    <div class="w-56 shrink-0 space-y-3">
                                        <template x-for="(item, index) in previewDisciplineRight" :key="item.name">
                                            <div class="flex items-start gap-2 min-w-0">
                                                <div class="w-3 h-3 rounded-full flex-shrink-0 mt-1"
                                                     :style="`background-color: ${item.color}`"></div>
                                                <div class="min-w-0">
                                                    <div class="text-slate-700 font-medium text-sm leading-snug" x-text="item.name"></div>
                                                    <div class="text-slate-500 text-xs font-semibold" x-text="item.pct + '%'"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-400 text-center mt-4 italic">* Sample data shown for preview layout purposes</p>
                            </div>

                        </div><!-- end Cards Row -->
                    </div><!-- end content area -->
                    </div><!-- end rounded-2xl panel -->
                </div><!-- end max-w-screen-xl -->
            </div><!-- end preview mode -->

        </div><!-- end bg canvas -->

        </div><!-- end side drawer flex -->
    </div><!-- end main content -->

    <!-- ══════════════════════════════════════════════════════
         MODALS + TOASTS — outside all overflow-hidden containers
         so fixed positioning works correctly across all browsers
    ══════════════════════════════════════════════════════ -->

    <!-- ── CONFIRM PUBLISH MODAL ── -->
    <div x-show="showConfirmModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/20 p-4"
         @click.self="showConfirmModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[92vh] overflow-y-auto" @click.stop>

            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                <h3 class="text-2xl font-bold text-gray-900">Confirm &amp; Publish</h3>
                <p class="text-sm text-gray-600 mt-1">Review changes before publishing to the public page</p>
            </div>

            <div class="p-6">

                <!-- Info banner — always shows the LOCKED publish target, not the filter dropdowns -->
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 border-l-4 border-blue-600 p-4 mb-4 rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="font-semibold text-gray-900">Publishing analysis for:</p>
                    </div>
                    <p class="font-bold text-lg text-gray-900" x-text="publishTargetYear || selectedAcademicYear"></p>
                    <p class="text-sm text-gray-700">Province: <span x-text="publishTargetProvince || selectedProvince"></span></p>
                </div>

                <!-- Warning: filter dropdowns point somewhere different from the publish target -->
                <template x-if="publishTargetProvince && (publishTargetProvince !== selectedProvince || publishTargetYear !== selectedAcademicYear)">
                    <div class="bg-amber-50 border-l-4 border-amber-500 p-3 mb-4 rounded-lg flex items-start gap-2">
                        <span class="text-amber-500 flex-shrink-0"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></span>
                        <p class="text-sm text-amber-800">
                            Admin Submitted
                            <strong x-text="publishTargetProvince"></strong> • <strong x-text="publishTargetYear"></strong>.
                            — but this will be publish to
                            <strong x-text="selectedProvince"></strong> • <strong x-text="selectedAcademicYear"></strong>
                        </p>
                    </div>
                </template>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-5 rounded-lg">
                    <p class="text-sm font-semibold text-yellow-800">
                        <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> This will immediately make the analysis visible on the public Supply Side page.
                    </p>
                </div>

                <!-- ─────────────────────────────────────────────────────────
                     SMART COMPARISON — 2 cases:
                     A. Statistician CHANGED the admin draft → show comparison
                     B. No changes made, or no draft loaded  → show single summary
                ──────────────────────────────────────────────────────── -->

                <!-- CASE A: Draft loaded + statistician EDITED → 2-col comparison -->
                <template x-if="originalSubmittedText && originalSubmittedText !== analysisText">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="h-px flex-1 bg-slate-200"></div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Comparison</p>
                            <div class="h-px flex-1 bg-slate-200"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <!-- Admin Submitted (original) -->
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 flex-shrink-0"></span>
                                    <p class="text-xs font-bold text-amber-700 uppercase tracking-wide">Admin Submitted</p>
                                </div>
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 max-h-72 overflow-y-auto custom-scrollbar text-sm ql-editor-preview">
                                    <div x-html="originalSubmittedText"></div>
                                </div>
                                <p class="text-xs text-amber-600 mt-1.5 text-right">
                                    <span x-text="originalSubmittedText.replace(/<[^>]*>/g,'').trim().split(/\s+/).filter(w=>w.length>0).length"></span> words
                                </p>
                            </div>
                            <!-- Your Edit -->
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 flex-shrink-0"></span>
                                    <p class="text-xs font-bold text-blue-700 uppercase tracking-wide">Your Edit</p>
                                </div>
                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 max-h-72 overflow-y-auto custom-scrollbar text-sm ql-editor-preview">
                                    <div x-html="analysisText"></div>
                                </div>
                                <p class="text-xs text-blue-600 mt-1.5 text-right">
                                    <span x-text="getWordCount()"></span> words
                                </p>
                            </div>
                        </div>
                        <div class="bg-orange-50 border border-orange-200 rounded-lg px-4 py-2 flex items-center gap-2 mb-5">
                            <span class="text-orange-500"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></span>
                            <p class="text-xs font-semibold text-orange-700">You've made changes from the admin's original submission.</p>
                        </div>
                    </div>
                </template>

                <!-- CASE B: No changes made, OR no draft loaded → single summary card -->
                <template x-if="!originalSubmittedText || originalSubmittedText === analysisText">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="h-px flex-1 bg-slate-200"></div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Content to be Published</p>
                            <div class="h-px flex-1 bg-slate-200"></div>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 max-h-80 overflow-y-auto custom-scrollbar text-sm text-slate-700 ql-editor-preview mb-2">
                            <div x-html="analysisText || '<em class=\'text-slate-400\'>No content.</em>'"></div>
                        </div>
                        <p class="text-xs text-slate-500 text-right mb-4">
                            <span x-text="getWordCount()"></span> words
                        </p>
                        <template x-if="originalSubmittedText && originalSubmittedText === analysisText">
                            <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2 flex items-center gap-2 mb-5">
                                <span class="text-green-500"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                                <p class="text-xs font-semibold text-green-700">No changes made — publishing admin's submission as-is.</p>
                            </div>
                        </template>
                        <template x-if="!originalSubmittedText">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2 flex items-center gap-2 mb-5">
                                <span class="text-blue-500"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                                <p class="text-xs font-semibold text-blue-700">This will be published to the public Supply Side page.</p>
                            </div>
                        </template>
                    </div>
                </template>

            </div><!-- end p-6 content -->

            <!-- Footer -->
            <div class="p-6 border-t border-gray-200 bg-gray-50 flex gap-3 sticky bottom-0">
                <button @click="showConfirmModal = false"
                        class="flex-1 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition">
                    Cancel
                </button>
                <button @click="confirmSave()"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg transition">
                    <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg> Save &amp; Publish
                </button>
            </div>
        </div>
    </div>

    <!-- ── SUCCESS MODAL ── -->
    <div x-show="showSuccessModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/20 p-4"
         @click.self="showSuccessModal = false">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full" @click.stop>
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Published!</h3>
                <p class="text-gray-600 mb-2">The analysis is now live on the public Supply Side page.</p>
                <div class="bg-green-50 rounded-lg p-3 mb-6 border border-green-200">
                    <p class="text-xs text-green-700">
                        <strong x-text="publishTargetProvince || selectedProvince"></strong> • <strong x-text="publishTargetYear || selectedAcademicYear"></strong>
                    </p>
                </div>
                <button @click="showSuccessModal = false"
                        class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                    Continue
                </button>
            </div>
        </div>
    </div>

    <!-- ── COPY ARCHIVE MODAL ── -->
    <div x-show="showCopyModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/20"
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

    <!-- ── RESET MODAL ── -->
    <div x-show="showResetModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/20"
         @click.self="showResetModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6" @click.stop>
            <div class="flex items-start gap-4 mb-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Reset to Original Draft</h3>
                    <p class="text-sm text-slate-600 mb-1">This will restore the editor back to the original loaded draft:</p>
                    <p class="text-sm font-semibold text-slate-700 mb-3">
                        <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> <span x-text="draftProvince || '—'"></span> &nbsp;•&nbsp; <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg> <span x-text="draftYear || '—'"></span>
                    </p>
                    <p class="text-xs text-orange-600 font-semibold"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Any changes you made will be lost!</p>
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button @click="showResetModal = false"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition">
                    Cancel
                </button>
                <button @click="confirmReset()"
                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition">
                    Reset to Original Draft
                </button>
            </div>
        </div>
    </div>

    <!-- Error Toast -->
    <div x-show="showError" x-transition @click="showError = false"
         class="fixed bottom-6 right-6 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg cursor-pointer z-50">
        <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span x-text="errorMessage"></span>
    </div>

    <!-- Success Toast -->
    <div x-show="showSuccessToast" x-transition @click="showSuccessToast = false"
         class="fixed bottom-6 right-6 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg cursor-pointer z-50">
        <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span x-text="successToastMessage"></span>
    </div>

</body>
</html>