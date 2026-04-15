<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    @vite('resources/js/statistician/template-editor.js')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .tab-active { position: relative; }
        .tab-active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0; right: 0;
            height: 2px;
            background: #2563eb;
            border-radius: 2px 2px 0 0;
        }
        .template-textarea {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.8;
            resize: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .template-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .ph-chip {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .ph-chip:hover { transform: translateY(-1px); }
    </style>
    <title>Analysis Template Editor</title>
</head>
<body x-data="templateEditor()" x-init="init()" class="bg-slate-100 flex h-screen overflow-hidden">
    @include('partials.statisticianSidebar')

    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Header -->
        <header class="bg-white border-b border-slate-200 shadow-sm flex-shrink-0">
            <!-- Top row -->
            <div class="h-14 flex items-center justify-between px-8">
                <h2 class="text-lg font-bold text-slate-800">Analysis Template Editor <span class="text-slate-400 font-normal">• Statistician</span></h2>
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
                    <span x-show="allPendingDrafts.length > 0"
                        class="text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none"
                        :class="mainTab === 'editor' ? 'bg-amber-500 text-white' : 'bg-amber-100 text-amber-700'"
                        x-text="allPendingDrafts.length"></span>
                </button>
                <button @click="mainTab = 'approved'"
                    class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold border-b-2 transition -mb-px"
                    :class="mainTab === 'approved'
                        ? 'border-emerald-600 text-emerald-700 bg-emerald-50/60'
                        : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Approved
                    <span x-show="allApprovedTemplates.length > 0"
                        class="text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none"
                        :class="mainTab === 'approved' ? 'bg-emerald-500 text-white' : 'bg-emerald-100 text-emerald-700'"
                        x-text="allApprovedTemplates.length"></span>
                </button>
            </div>
        </header>

        <!-- Canvas -->
        <div class="flex-1 overflow-y-auto bg-slate-100 p-6">

            <!-- ══ APPROVED PANEL (shown when mainTab === approved) ══ -->
            <div x-show="mainTab === 'approved'" x-cloak class="h-full">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <!-- Panel header -->
                    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-gradient-to-r from-emerald-50 to-teal-50">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Approved & Published Templates</p>
                                <p class="text-xs text-slate-500">Read-only history of all published analysis templates</p>
                            </div>
                        </div>
                        <span class="text-xs bg-emerald-100 text-emerald-700 font-bold px-3 py-1 rounded-full" x-text="allApprovedTemplates.length + ' period(s)'"></span>
                    </div>

                    <!-- Loading -->
                    <div x-show="loadingAllApproved" class="flex items-center justify-center py-20">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-500"></div>
                        <span class="ml-3 text-slate-500 text-sm">Loading approved templates...</span>
                    </div>

                    <!-- Empty state -->
                    <div x-show="!loadingAllApproved && allApprovedTemplates.length === 0" class="flex flex-col items-center justify-center py-24 text-slate-400">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-sm font-semibold mb-1">No approved templates yet</p>
                        <p class="text-xs text-slate-300">Published templates will appear here once approved</p>
                    </div>

                    <!-- Approved records grid -->
                    <div x-show="!loadingAllApproved && allApprovedTemplates.length > 0" class="p-6">
                        <!-- Period selector row -->
                        <div class="flex flex-wrap gap-2 mb-6">
                            <template x-for="(item, idx) in allApprovedTemplates" :key="idx">
                                <button @click="approvedDetailItem = item; approvedDetailTab = item.template_keys[0]"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg border text-xs font-semibold transition"
                                    :class="approvedDetailItem && approvedDetailItem.year === item.year && approvedDetailItem.month === item.month
                                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm'
                                        : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-300 hover:text-emerald-700'">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span x-text="(quarterLabels[item.month] || item.month) + ' ' + item.year"></span>
                                </button>
                            </template>
                        </div>

                        <!-- Detail pane for selected period -->
                        <template x-if="approvedDetailItem">
                            <div class="border border-slate-200 rounded-xl overflow-hidden">
                                <!-- Meta bar -->
                                <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center gap-4 text-xs text-slate-600">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Submitted by <strong class="ml-1" x-text="approvedDetailItem.submitted_by || 'Admin'"></strong>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Published <strong class="ml-1" x-text="formatDate(approvedDetailItem.approved_at)"></strong>
                                    </span>
                                    <span class="ml-auto flex items-center gap-1 bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-bold">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                        Published
                                    </span>
                                </div>

                                <!-- Template key tabs -->
                                <div class="flex border-b border-slate-200 bg-white px-5 gap-1 pt-2">
                                    <template x-for="key in (approvedDetailItem.template_keys || [])" :key="key">
                                        <button @click="approvedDetailTab = key"
                                            class="px-4 py-2 text-xs font-semibold border-b-2 transition capitalize -mb-px"
                                            :class="approvedDetailTab === key
                                                ? 'border-emerald-600 text-emerald-700 bg-emerald-50/50'
                                                : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'">
                                            <span x-text="key"></span>
                                        </button>
                                    </template>
                                </div>

                                <!-- Template text content -->
                                <div class="p-6 bg-white">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider capitalize" x-text="approvedDetailTab + ' Rate'"></span>
                                        <span class="h-px flex-1 bg-slate-100"></span>
                                        <span class="text-[10px] text-slate-400 font-medium">Template text</span>
                                    </div>
                                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-5">
                                        <p class="text-sm text-slate-700 leading-relaxed font-mono whitespace-pre-wrap"
                                           x-text="approvedDetailItem.templates[approvedDetailTab] || '—'"></p>
                                    </div>

                                    <!-- All templates quick-glance -->
                                    <div class="mt-5 grid grid-cols-2 gap-3">
                                        <template x-for="key in (approvedDetailItem.template_keys || [])" :key="key">
                                            <div class="rounded-lg border p-3 cursor-pointer transition"
                                                 :class="approvedDetailTab === key
                                                    ? 'border-emerald-300 bg-emerald-50 ring-1 ring-emerald-400'
                                                    : 'border-slate-100 bg-slate-50 hover:border-emerald-200'"
                                                 @click="approvedDetailTab = key">
                                                <p class="text-[10px] font-bold uppercase tracking-wider mb-1 capitalize"
                                                   :class="approvedDetailTab === key ? 'text-emerald-700' : 'text-slate-400'"
                                                   x-text="key + ' rate'"></p>
                                                <p class="text-xs text-slate-500 leading-relaxed line-clamp-2"
                                                   x-text="approvedDetailItem.templates[key] || '—'"></p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- ══ EDITOR PANEL (shown when mainTab === editor) ══ -->
            <div x-show="mainTab === 'editor'" class="flex gap-6 items-stretch">

                <!-- LEFT PANEL -->
                <div class="w-72 flex-shrink-0 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-5 space-y-5">
                        <div x-show="isUnlocked && lastSaved" class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <p class="text-xs font-bold text-green-800 mb-0.5"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Last Saved</p>
                            <p class="text-xs text-green-600" x-text="lastSaved"></p>
                        </div>
                        <!-- ── Pending Drafts ── -->
                        <div>

                            <!-- Section Label -->
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span class="text-xs font-bold text-slate-600 uppercase tracking-wide">Pending Drafts</span>
                                <span x-show="allPendingDrafts.length > 0"
                                    class="text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none bg-amber-500 text-white"
                                    x-text="allPendingDrafts.length"></span>
                            </div>

                            <!-- ── PENDING DRAFTS ── -->
                            <div>
                                <p class="text-xs text-slate-400 mb-3">Load a draft to unlock the editor.</p>
                                <div x-show="loadingAllPending" class="flex justify-center py-4">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-amber-500"></div>
                                </div>
                                <div x-show="!loadingAllPending" class="space-y-2 max-h-64 overflow-y-auto pr-1 custom-scrollbar">
                                    <template x-for="draft in allPendingDrafts" :key="draft.id">
                                        <div class="border border-amber-200 rounded-lg p-3 bg-amber-50 hover:border-amber-400 hover:bg-amber-100 transition">
                                            <div class="flex items-center justify-between mb-0.5">
                                                <p class="font-semibold text-xs text-amber-900">
                                                    <span x-text="quarterLabels[draft.month] || draft.month"></span>
                                                    <span x-text="draft.year"></span>
                                                </p>
                                                <span class="text-[10px] bg-amber-200 text-amber-800 px-1.5 py-0.5 rounded font-bold">Pending</span>
                                            </div>
                                            <p class="text-xs text-amber-700 mb-0.5">By <strong x-text="draft.submitted_by"></strong></p>
                                            <p class="text-xs text-amber-500 mb-1.5" x-text="formatDate(draft.submitted_at)"></p>
                                            <div class="flex flex-wrap gap-1 mb-2">
                                                <template x-for="key in draft.template_keys" :key="key">
                                                    <span class="text-xs bg-amber-200 text-amber-800 px-1.5 py-0.5 rounded font-medium capitalize" x-text="key"></span>
                                                </template>
                                            </div>
                                            <button @click="loadDraftIntoEditor(draft)" class="w-full text-xs bg-amber-600 hover:bg-amber-700 text-white py-1.5 rounded-lg font-medium transition">
                                                <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg> Load Draft
                                            </button>
                                        </div>
                                    </template>
                                    <div x-show="allPendingDrafts.length === 0" class="text-center py-8 text-slate-400">
                                        <div class="text-slate-300 mb-2"><svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div>
                                        <p class="text-xs font-medium">No pending drafts</p>
                                        <p class="text-xs text-slate-300 mt-0.5">Check back later</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div x-show="isUnlocked" class="border-t border-slate-200 pt-4">
                            <p class="text-sm font-bold text-slate-600 mb-2"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Validation</p>
                            <div class="space-y-1.5">
                                <template x-for="key in templateKeys" :key="key">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-slate-600 capitalize" x-text="labelFor(key)"></span>
                                        <span x-show="validation[key] && validation[key].valid" class="text-xs text-green-600 font-medium">✓ Valid</span>
                                        <span x-show="validation[key] && !validation[key].valid" class="text-xs text-red-500 font-medium"><svg class="w-3.5 h-3.5 inline-block flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Error</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT PANEL -->
                <div class="flex-1 flex flex-col gap-0 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden relative">

                    <!-- Lock overlay -->
                    <div x-show="!isUnlocked" class="absolute inset-0 z-10 bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center rounded-xl">
                        <div class="mb-3 w-14 h-14 mx-auto bg-slate-100 rounded-full flex items-center justify-center"><svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                        <p class="text-sm font-bold text-slate-600 mb-1">Editor Locked</p>
                        <p class="text-xs text-slate-400">Load a pending draft to start editing</p>
                    </div>

                    <!-- Top Bar -->
                    <div class="border-b border-slate-200 px-5 py-3 flex items-center justify-between flex-shrink-0 bg-white">
                        <div class="flex items-center gap-2">
                            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-1.5 rounded-lg">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-700"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> ANALYSIS TEMPLATES</p>
                                <p class="text-xs text-slate-400" x-text="currentPeriodLabel ? `Period: ${currentPeriodLabel}` : 'No period selected'"></p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <!-- Edit / Preview toggle — no Split -->
                            <div class="flex rounded-lg border border-slate-200 overflow-hidden text-xs">
                                <button @click="viewMode = 'edit'" class="px-3 py-1.5 font-medium transition"
                                    :class="viewMode === 'edit' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'">
                                    <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Edit
                                </button>
                                <button @click="viewMode = 'preview'" class="px-3 py-1.5 font-medium transition"
                                    :class="viewMode === 'preview' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'">
                                    <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> Preview
                                </button>
                            </div>
                            <button @click="resetAll()" :disabled="!isUnlocked"
                                class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg transition disabled:opacity-40">
                                Reset
                            </button>
                            <button @click="confirmBeforeSave()" 
                            :disabled="!isUnlocked || saving || hasValidationErrors()"
                            class="text-xs px-4 py-1.5 rounded-lg font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed"
                            :class="isUnlocked && !hasValidationErrors() ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-slate-200 text-slate-400 cursor-not-allowed'">
                            <span x-html="saving 
                                ? 'Saving...' 
                                : '<svg class=\'w-3.5 h-3.5 inline-block\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4\'/></svg> Save &amp; Publish'">
                            </span>
                        </button>
                        </div>
                    </div>

                    <!-- Metric Tabs — SVG icons, no emojis -->
                    <div class="flex border-b border-slate-200 bg-white flex-shrink-0">
                        <button @click="activeTab = 'employment'" class="relative px-5 py-3 text-xs font-semibold transition border-b-2"
                                :class="activeTab === 'employment' ? 'border-blue-600 text-blue-700 bg-blue-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>Employment</span>
                                <span x-show="validation['employment'] && !validation['employment'].valid" class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                <span x-show="isTabChanged('employment')" class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                            </div>
                        </button>
                        <button @click="activeTab = 'underemployment'" class="relative px-5 py-3 text-xs font-semibold transition border-b-2"
                                :class="activeTab === 'underemployment' ? 'border-blue-600 text-blue-700 bg-blue-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Underemployment</span>
                                <span x-show="validation['underemployment'] && !validation['underemployment'].valid" class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                <span x-show="isTabChanged('underemployment')" class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                            </div>
                        </button>
                        <button @click="activeTab = 'unemployment'" class="relative px-5 py-3 text-xs font-semibold transition border-b-2"
                                :class="activeTab === 'unemployment' ? 'border-blue-600 text-blue-700 bg-blue-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                                <span>Unemployment</span>
                                <span x-show="validation['unemployment'] && !validation['unemployment'].valid" class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                <span x-show="isTabChanged('unemployment')" class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                            </div>
                        </button>
                        <button @click="activeTab = 'lfpr'" class="relative px-5 py-3 text-xs font-semibold transition border-b-2"
                                :class="activeTab === 'lfpr' ? 'border-blue-600 text-blue-700 bg-blue-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Participation Rate</span>
                                <span x-show="validation['lfpr'] && !validation['lfpr'].valid" class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                <span x-show="isTabChanged('lfpr')" class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                            </div>
                        </button>
                    </div>

                    <!-- Loading -->
                    <div x-show="loading" class="flex items-center justify-center py-20">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <span class="ml-3 text-slate-500 text-sm">Loading templates...</span>
                    </div>

                    <!-- Tab Content -->
                    <div x-show="!loading" class="flex-1 overflow-hidden">
                        <template x-for="key in templateKeys" :key="key">
                            <div x-show="activeTab === key" class="h-full flex flex-col">

                                <!-- Placeholder toolbar -->
                                <div class="px-5 py-3 bg-slate-50 border-b border-slate-100 flex items-center gap-2 flex-wrap flex-shrink-0">
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wide whitespace-nowrap">Insert:</span>
                                    <template x-for="ph in allPlaceholders" :key="ph.key">
                                        <button @click="insertAtCursor(ph.key, key)"
                                            class="ph-chip inline-flex items-center gap-1 px-2 py-1 bg-white border border-slate-200 rounded text-slate-600 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700">
                                            <span x-text="ph.icon"></span>
                                            <code x-text="ph.key"></code>
                                        </button>
                                    </template>
                                </div>

                                <!-- Edit / Preview area -->
                                <div class="flex-1 overflow-hidden flex">

                                    <!-- EDIT pane -->
                                    <div class="flex-1 overflow-auto p-5 flex flex-col gap-3" x-show="viewMode === 'edit'">
                                        <div x-show="originalSubmittedTemplates[key]">
                                            <p class="text-xs font-bold text-amber-700 uppercase tracking-wide mb-1.5"><span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-400 mr-1"></span> Admin Submitted</p>
                                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm font-mono text-amber-900 leading-relaxed whitespace-pre-wrap"
                                                 x-text="originalSubmittedTemplates[key]"></div>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-blue-700 uppercase tracking-wide mb-1.5" x-show="originalSubmittedTemplates[key]"><span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-500 mr-1"></span> Your Edited Version</p>
                                            <textarea :id="'textarea-' + key" x-model="templates[key]" @input="onInput(key)" @focus="activeField = key" rows="5"
                                                class="template-textarea w-full p-3 border rounded-lg text-slate-700"
                                                :class="validation[key] && !validation[key].valid ? 'border-red-300 bg-red-50/20' : 'border-slate-200 bg-white'"
                                                placeholder="Write analysis template here..."></textarea>
                                            <div x-show="validation[key] && !validation[key].valid" class="mt-2 flex items-center gap-2 flex-wrap">
                                                <span class="text-xs text-red-500 font-medium">Missing placeholders:</span>
                                                <template x-for="m in (validation[key] ? validation[key].missing : [])" :key="m">
                                                    <code class="text-xs px-2 py-0.5 bg-red-50 border border-red-200 text-red-600 rounded font-mono" x-text="m"></code>
                                                </template>
                                            </div>
                                            <div x-show="isTabChanged(key)" class="mt-2 text-xs text-orange-500 font-medium"><svg class="w-3.5 h-3.5 inline-block flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Unsaved changes</div>
                                        </div>
                                    </div>

                                    <!-- PREVIEW pane — 2x2 equal-height grid with word-level diff -->
                                    <div class="flex-1 overflow-auto p-5 bg-slate-50/50" x-show="viewMode === 'preview'">
                                        <div class="flex items-center gap-2 mb-4">
                                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Live Preview</p>
                                            <span x-show="hasPreviewData" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Real Data</span>
                                            <span x-show="!hasPreviewData" class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-medium">Sample Data</span>
                                            <span x-show="hasAnyChanges()" class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13z"/></svg>
                                                Changes highlighted
                                            </span>
                                        </div>

                                        <!-- 2x2 equal-height grid -->
                                        <div class="grid grid-cols-2 gap-4 auto-rows-fr">

                                            <!-- LFPR card -->
                                            <div class="group relative flex flex-col rounded-lg p-5 border-l-4 border-l-[#023E8A] shadow-sm hover:shadow-md transition-all duration-200"
                                                 :class="isTabChanged('lfpr') ? 'bg-blue-50/60 border border-blue-300 ring-2 ring-blue-400 ring-offset-2' : 'bg-white border border-[#023E8A]/20'">
                                                <div x-show="isTabChanged('lfpr')" class="absolute -top-2.5 -right-2.5 flex items-center gap-1 bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-md">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13z"/></svg>
                                                    Edited
                                                </div>
                                                <div class="flex items-center gap-2 mb-3">
                                                    <div class="w-8 h-8 bg-blue-50 group-hover:bg-[#023E8A] rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                                                        <svg class="w-4 h-4 text-[#023E8A] group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                    </div>
                                                    <p class="text-xs font-bold text-[#023E8A] uppercase tracking-wide">Participation Rate</p>
                                                </div>
                                                <div x-show="loadingPreview" class="flex items-center gap-2 text-slate-400"><div class="animate-spin rounded-full h-4 w-4 border-b-2 border-slate-400"></div><span class="text-xs">Loading...</span></div>
                                                <div x-show="!loadingPreview" x-html="renderDiffPreview(templates['lfpr'], 'lfpr')" class="text-sm text-slate-700 leading-relaxed flex-1"></div>
                                            </div>

                                            <!-- Employment card -->
                                            <div class="group relative flex flex-col rounded-lg p-5 border-l-4 border-l-[#006400] shadow-sm hover:shadow-md transition-all duration-200"
                                                 :class="isTabChanged('employment') ? 'bg-green-50/60 border border-green-300 ring-2 ring-green-400 ring-offset-2' : 'bg-white border border-[#006400]/20'">
                                                <div x-show="isTabChanged('employment')" class="absolute -top-2.5 -right-2.5 flex items-center gap-1 bg-green-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-md">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13z"/></svg>
                                                    Edited
                                                </div>
                                                <div class="flex items-center gap-2 mb-3">
                                                    <div class="w-8 h-8 bg-green-50 group-hover:bg-[#006400] rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                                                        <svg class="w-4 h-4 text-[#006400] group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                    </div>
                                                    <p class="text-xs font-bold text-[#006400] uppercase tracking-wide">Employment Rate</p>
                                                </div>
                                                <div x-show="loadingPreview" class="flex items-center gap-2 text-slate-400"><div class="animate-spin rounded-full h-4 w-4 border-b-2 border-slate-400"></div><span class="text-xs">Loading...</span></div>
                                                <div x-show="!loadingPreview" x-html="renderDiffPreview(templates['employment'], 'employment')" class="text-sm text-slate-700 leading-relaxed flex-1"></div>
                                            </div>

                                            <!-- Underemployment card -->
                                            <div class="group relative flex flex-col rounded-lg p-5 border-l-4 border-l-[#FF8C00] shadow-sm hover:shadow-md transition-all duration-200"
                                                 :class="isTabChanged('underemployment') ? 'bg-orange-50/60 border border-orange-300 ring-2 ring-orange-400 ring-offset-2' : 'bg-white border border-[#FF8C00]/20'">
                                                <div x-show="isTabChanged('underemployment')" class="absolute -top-2.5 -right-2.5 flex items-center gap-1 bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-md">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13z"/></svg>
                                                    Edited
                                                </div>
                                                <div class="flex items-center gap-2 mb-3">
                                                    <div class="w-8 h-8 bg-orange-50 group-hover:bg-[#FF8C00] rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                                                        <svg class="w-4 h-4 text-[#FF8C00] group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    </div>
                                                    <p class="text-xs font-bold text-[#FF8C00] uppercase tracking-wide">Underemployment Rate</p>
                                                </div>
                                                <div x-show="loadingPreview" class="flex items-center gap-2 text-slate-400"><div class="animate-spin rounded-full h-4 w-4 border-b-2 border-slate-400"></div><span class="text-xs">Loading...</span></div>
                                                <div x-show="!loadingPreview" x-html="renderDiffPreview(templates['underemployment'], 'underemployment')" class="text-sm text-slate-700 leading-relaxed flex-1"></div>
                                            </div>

                                            <!-- Unemployment card -->
                                            <div class="group relative flex flex-col rounded-lg p-5 border-l-4 border-l-[#D30000] shadow-sm hover:shadow-md transition-all duration-200"
                                                 :class="isTabChanged('unemployment') ? 'bg-red-50/60 border border-red-300 ring-2 ring-red-400 ring-offset-2' : 'bg-white border border-[#D30000]/20'">
                                                <div x-show="isTabChanged('unemployment')" class="absolute -top-2.5 -right-2.5 flex items-center gap-1 bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-md">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13z"/></svg>
                                                    Edited
                                                </div>
                                                <div class="flex items-center gap-2 mb-3">
                                                    <div class="w-8 h-8 bg-red-50 group-hover:bg-[#D30000] rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                                                        <svg class="w-4 h-4 text-[#D30000] group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                                                    </div>
                                                    <p class="text-xs font-bold text-[#D30000] uppercase tracking-wide">Unemployment Rate</p>
                                                </div>
                                                <div x-show="loadingPreview" class="flex items-center gap-2 text-slate-400"><div class="animate-spin rounded-full h-4 w-4 border-b-2 border-slate-400"></div><span class="text-xs">Loading...</span></div>
                                                <div x-show="!loadingPreview" x-html="renderDiffPreview(templates['unemployment'], 'unemployment')" class="text-sm text-slate-700 leading-relaxed flex-1"></div>
                                            </div>

                                        </div><!-- end 2x2 grid -->

                                        <!-- No data warning -->
                                        <div x-show="!hasPreviewData && !loadingPreview" class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                            <p class="text-xs text-yellow-700"><svg class="w-3.5 h-3.5 inline-block flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> No real data for <strong x-text="currentPeriodLabel"></strong>. Showing sample values.</p>
                                        </div>
                                    </div>
                                </div><!-- end edit/preview area -->

                            </div><!-- end tab content -->
                        </template>
                    </div><!-- end tab content wrapper -->

                    <!-- Status Bar -->
                    <div class="border-t border-slate-100 px-5 py-2 bg-slate-50 flex items-center justify-between flex-shrink-0">
                        <span class="text-xs text-slate-400">
                            <span x-show="!hasValidationErrors() && isUnlocked" class="text-green-600 font-medium">✓ All templates valid</span>
                            <span x-show="hasValidationErrors() && isUnlocked" class="text-red-500 font-medium"><svg class="w-3.5 h-3.5 inline-block flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Fix errors before saving</span>
                            <span x-show="!isUnlocked" class="text-slate-400">Editor locked</span>
                        </span>
                        <span x-show="hasAnyChanges() && isUnlocked" class="text-xs text-orange-500 font-medium"><svg class="w-3.5 h-3.5 inline-block flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Unsaved changes</span>
                    </div>

                </div><!-- end right panel -->
            </div><!-- end editor panel -->
        </div>
    </div>

    <!-- MODALS -->

    <!-- Confirm Save Modal -->
    <div x-show="showSaveModal" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/20 p-4"
         @click.self="showSaveModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[92vh] overflow-y-auto" @click.stop>
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-900">Confirm &amp; Save Templates</h3>
                <p class="text-sm text-gray-500 mt-1">Review changes before publishing to the analysis reports</p>
            </div>
            <div class="p-6">

                <!-- Info banner — always shows the LOCKED publish target -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-600 p-4 mb-4 rounded-lg">
                    <p class="font-semibold text-gray-900">Saving templates for:</p>
                    <p class="text-lg font-bold text-gray-900 mt-0.5" x-text="(quarterLabels[publishTargetMonth || selectedMonth] || '') + ' ' + (publishTargetYear || selectedYear)"></p>
                    <p class="text-sm text-gray-600 mt-1">These templates will be used to generate analysis reports for the selected period.</p>
                </div>

                <!-- Warning: filters point somewhere different from publish target -->
                <template x-if="publishTargetYear && (publishTargetYear !== selectedYear || publishTargetMonth !== selectedMonth)">
                    <div class="bg-amber-50 border-l-4 border-amber-500 p-3 mb-4 rounded-lg flex items-start gap-2">
                        <span class="text-amber-500 flex-shrink-0"><svg class="w-3.5 h-3.5 inline-block flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></span>
                        <p class="text-sm text-amber-800">
                            Your filters are on <strong x-text="(quarterLabels[selectedMonth] || selectedMonth) + ' ' + selectedYear"></strong>
                            — but this will still save to
                            <strong x-text="(quarterLabels[publishTargetMonth] || publishTargetMonth) + ' ' + publishTargetYear"></strong>.
                        </p>
                    </div>
                </template>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-5 rounded-lg">
                    <p class="text-sm font-semibold text-yellow-800"><svg class="w-3.5 h-3.5 inline-block flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> This will update the live analysis templates immediately.</p>
                </div>

                <!-- Per-template smart diff -->
                <template x-for="key in templateKeys" :key="key">
                    <div class="mb-8 pb-6 border-b border-slate-100 last:border-b-0 last:mb-0 last:pb-0">

                        <!-- Template header -->
                        <div class="flex items-center gap-2 mb-3">
                            <span x-html="iconFor(key)"></span>
                            <p class="font-semibold text-slate-800 text-sm" x-text="labelFor(key)"></p>
                            <!-- Badge: edited or unchanged -->
                            <template x-if="originalSubmittedTemplates[key] && originalSubmittedTemplates[key] !== templates[key]">
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">✏ Edited</span>
                            </template>
                            <template x-if="!originalSubmittedTemplates[key] || originalSubmittedTemplates[key] === templates[key]">
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">No changes</span>
                            </template>
                        </div>

                        <!-- Edited: show Admin Submitted + Your Edit side by side -->
                        <template x-if="originalSubmittedTemplates[key] && originalSubmittedTemplates[key] !== templates[key]">
                            <div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs font-bold text-amber-700 mb-1.5"><span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-400 mr-1"></span> Admin Submitted</p>
                                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs font-mono text-amber-900 leading-relaxed whitespace-pre-wrap min-h-16" x-text="originalSubmittedTemplates[key]"></div>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-blue-700 mb-1.5"><span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-500 mr-1"></span> Your Edit <span class="text-slate-400 font-normal">(will be published)</span></p>
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs font-mono text-blue-900 leading-relaxed whitespace-pre-wrap min-h-16" x-text="templates[key]"></div>
                                    </div>
                                </div>
                                <div class="mt-2 bg-orange-50 border border-orange-200 rounded-lg px-3 py-2 flex items-center gap-2">
                                    <span class="text-orange-500"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></span>
                                    <p class="text-xs font-semibold text-orange-700">You've edited the admin draft — your version will be published.</p>
                                </div>
                            </div>
                        </template>

                        <!-- Not edited: show Admin Submitted only -->
                        <template x-if="originalSubmittedTemplates[key] && originalSubmittedTemplates[key] === templates[key]">
                            <div>
                                <div>
                                    <p class="text-xs font-bold text-amber-700 mb-1.5"><span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-400 mr-1"></span> Admin Submitted <span class="text-slate-400 font-normal">(will be published as-is)</span></p>
                                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs font-mono text-amber-900 leading-relaxed whitespace-pre-wrap min-h-16" x-text="originalSubmittedTemplates[key]"></div>
                                </div>
                            </div>
                        </template>

                        <!-- No draft at all: single preview -->
                        <template x-if="!originalSubmittedTemplates[key]">
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 text-xs font-mono text-slate-700 leading-relaxed whitespace-pre-wrap" x-text="templates[key]"></div>
                        </template>

                    </div>
                </template>

            </div><!-- end p-6 -->
            <div class="p-6 border-t border-gray-200 bg-gray-50 flex gap-3 sticky bottom-0">
                <button @click="showSaveModal = false" class="flex-1 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition">Cancel</button>
                <button @click="confirmSave()" :disabled="saving" 
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg transition disabled:opacity-50">
                    <span x-show="saving">Saving...</span>
                    <span x-show="!saving" class="flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Save All Templates
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Reset Modal -->
    <div x-show="showResetModal" x-cloak class="fixed inset-0 flex items-center justify-center z-50 backdrop-blur-sm bg-black/20" @click.self="showResetModal = false">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4" @click.stop>
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Reset to Original Draft</h3>
                    <p class="text-sm text-gray-600 mb-1">This will restore all templates back to the originally loaded draft:</p>
                    <p class="text-sm font-semibold text-slate-700 mb-3">
                        <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> <span x-text="(quarterLabels[draftMonth] || draftMonth || '—') + ' ' + (draftYear || '')"></span>
                    </p>
                    <p class="text-xs text-orange-600 font-semibold"><svg class="w-3.5 h-3.5 inline-block flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Any changes you made to all 4 templates will be lost!</p>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button @click="showResetModal = false" class="flex-1 px-6 py-2.5 bg-white text-gray-700 font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                <button @click="confirmReset()" class="flex-1 px-6 py-2.5 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition">Reset to Original Draft</button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div x-show="showSuccessModal" x-cloak class="fixed inset-0 flex items-center justify-center z-50 backdrop-blur-sm bg-black/20" @click.self="showSuccessModal = false">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4" @click.stop>
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Templates Saved!</h3>
                <p class="text-sm text-gray-600 mb-2" x-text="successMessage"></p>
                <div class="bg-green-50 rounded-lg p-3 mb-6 border border-green-200">
                    <p class="text-xs text-green-700">Period: <strong x-text="currentPeriodLabel"></strong></p>
                </div>
                <button @click="showSuccessModal = false" class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">Continue</button>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div x-show="showErrorModal" x-cloak class="fixed inset-0 flex items-center justify-center z-50 backdrop-blur-sm bg-black/20" @click.self="showErrorModal = false">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4" @click.stop>
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3" x-text="errorTitle"></h3>
                <p class="text-sm text-gray-600 mb-6" x-text="errorMessage"></p>
                <button @click="showErrorModal = false" class="w-full px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition">Close</button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed bottom-6 right-6 z-50 bg-slate-800 text-white px-5 py-3 rounded-xl shadow-xl text-sm font-medium flex items-center gap-2">
        <svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span x-text="toastMessage"></span>
    </div>

</body>
</html>