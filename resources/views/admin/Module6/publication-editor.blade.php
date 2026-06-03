<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    @vite('resources/css/app.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/js/admin/Module6/lmi-publication-editor.js')
    @vite('resources/js/admin/Module6/lmi-weekly-editor.js')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>
    <title>LMI Publication — Admin</title>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .plus-btn {
            transition: all 0.2s ease;
        }

        .plus-btn:hover {
            transform: scale(1.06);
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen" x-data="lmiAdminPage()">

    <div class="flex h-screen overflow-hidden">
        @include('partials.sidebar')

        <div class="flex-1 flex flex-col overflow-y-auto" x-ref="mainContent">

            {{-- ── Top bar ── --}}
            <header
                class="bg-white h-16 shrink-0 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm sticky top-0 z-50">
                <h2 class="text-xl font-bold text-slate-800">LMI Publication Editor • Admin</h2>
                <button @click="window.open('/admin/lmi-publication/preview', '_blank')"
                    class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Preview Draft
                </button>
            </header>

            {{-- ── Hero Banner ── --}}
            <div class="relative w-full h-[500px] md:h-[700px] lg:h-[900px] overflow-hidden shrink-0">
                <div class="absolute inset-0">
                    <img src="{{ asset('images/publication.jpg') }}" alt="LMI Banner"
                        class="w-full h-full object-cover object-center">
                    <div class="absolute inset-0 bg-gradient-to-b from-slate-900/80 via-slate-900/40 to-slate-100">
                    </div>
                </div>
                <div class="relative z-10 h-full flex flex-col items-center justify-center px-4 text-center gap-1.5">
                    <h2 class="text-white font-black tracking-tight leading-none drop-shadow-[0_2px_16px_rgba(0,0,0,1)]"
                        style="font-size: clamp(1.4rem, 3.5vw, 2.75rem);">LMI Publication</h2>
                    <p class="text-slate-200 font-medium drop-shadow-[0_1px_8px_rgba(0,0,0,1)]"
                        style="font-size: clamp(0.7rem, 1.4vw, 0.95rem);">The Official Newsletter of the Davao Region XI
                    </p>
                </div>
                <div
                    class="absolute bottom-6 sm:bottom-16 md:bottom-24 lg:bottom-32 left-1/2 transform -translate-x-1/2 z-20 animate-bounce">
                    <a href="#publication-content" class="flex flex-col items-center cursor-pointer group"
                        @click.prevent="document.getElementById('publication-content').scrollIntoView({ behavior: 'smooth', block: 'start' })">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white group-hover:text-blue-300 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                        <p
                            class="text-white text-xs sm:text-sm mt-1 sm:mt-2 font-medium group-hover:text-blue-300 transition-colors">
                            Scroll to explore</p>
                    </a>
                </div>
            </div>

            {{-- ── Main Content ── --}}
            <div id="publication-content" class="max-w-9xl mx-auto px-3 sm:px-6 py-6 w-full">
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200/60 overflow-hidden">

                    {{-- Header --}}
                    <div
                        class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-5 sm:px-6 py-4 flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center flex-shrink-0 ring-1 ring-white/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6m-4 4h2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-[0.95rem] leading-snug">LMI Publications — Davao Region
                                XI</h3>
                            <p class="text-slate-400 text-xs mt-0.5 leading-snug">Manage annual and weekly publications
                            </p>
                        </div>
                    </div>

                    <div class="px-5 sm:px-6 py-3 bg-slate-50 border-b border-slate-200">
                        <p class="text-slate-500 text-[0.78rem] italic">Click on a publication group to expand and
                            manage its issues.</p>
                    </div>

                    <div class="px-5 sm:px-6 pb-6 pt-4 flex flex-col gap-4">

                        {{-- ── ANNUAL TAB CARDS + SHARED PANEL ── --}}
                        <div x-data="{ activeTab: null }" class="flex flex-col gap-0">

                            {{-- Tab Cards Row --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-10 items-start">

                                @foreach ($groupData as $groupId => $group)
                                    @php
                                        $isPublished = $group['is_published'];
                                        $hasDraftChanges = $group['has_draft_changes'];
                                        $issuesJson = json_encode($group['issues']);
                                        $yearType = $groupId === 'jlmf' ? 'range' : 'single';

                                        if ($groupId === 'jlmf') {
                                            $cardPanel = 'bg-blue-900';
                                            $cardPanelHover = 'group-hover:bg-blue-800';
                                            $cardFooter = 'bg-blue-950';
                                            $cardFooterHover = 'group-hover:bg-blue-900';
                                            $cardRing = 'ring-blue-500';
                                            $cardTitle = 'JOBS & LABOR MARKET FORECAST';
                                            $cardSubtitle = 'INDUSTRY GROWTH & ACTION AGENDA';
                                            $cardDesc =
                                                '"Information on Key Growth Sectors, Emerging Industries, In Demand Occupations, and action agendas for industry gaps."';
                                        } elseif ($groupId === 'lmp') {
                                            $cardPanel = 'bg-red-900';
                                            $cardPanelHover = 'group-hover:bg-red-800';
                                            $cardFooter = 'bg-red-950';
                                            $cardFooterHover = 'group-hover:bg-red-900';
                                            $cardRing = 'ring-blue-400';
                                            $cardTitle = 'LABOR MARKET PROFILE';
                                            $cardSubtitle = 'DEMOGRAPHIC & ECONOMIC ANALYSIS';
                                            $cardDesc =
                                                '"Comprehensive demographic and economic landscape analysis. Ideal for policy makers and investors seeking regional depth."';
                                        } elseif ($groupId === 'lmu') {
                                            $cardPanel = 'bg-[#8B6B5A]';
                                            $cardPanelHover = 'group-hover:bg-[#A67C6A]';
                                            $cardFooter = 'bg-[#6F4E37]';
                                            $cardFooterHover = 'group-hover:bg-[#8B6B5A]';
                                            $cardRing = 'ring-blue-400';
                                            $cardTitle = 'LABOR MARKET UPDATE';
                                            $cardSubtitle = 'REGIONAL SKILLS PROFILE';
                                            $cardDesc =
                                                '"Annual publication providing labor market information based on data from the PESO Employment Information System (PEIS)"';
                                        } else {
                                            $cardPanel = 'bg-slate-800';
                                            $cardPanelHover = 'group-hover:bg-slate-700';
                                            $cardFooter = 'bg-slate-900';
                                            $cardFooterHover = 'group-hover:bg-slate-800';
                                            $cardRing = 'ring-blue-400';
                                            $cardTitle = strtoupper($group['title']);
                                            $cardSubtitle = '';
                                            $cardDesc = $group['description'] ?? '';
                                        }

                                        $bannerThumb = null;
                                        if (!empty($group['issues'])) {
                                            $first = $group['issues'][0];
                                            if (!empty($first['drive_file_id'])) {
                                                $bannerThumb = "https://drive.google.com/thumbnail?id={$first['drive_file_id']}&sz=s500";
                                            }
                                        }
                                    @endphp

                                    <div id="lmi-card-{{ $groupId }}" x-data="{
                                        issues: {{ $issuesJson }},
                                        isPublished: {{ $isPublished ? 'true' : 'false' }},
                                        hasDraftChanges: {{ $hasDraftChanges ? 'true' : 'false' }},
                                        activeYear: '',
                                        get years() { return [...new Set(this.issues.map(i => String(i.year)))].sort((a, b) => b.localeCompare(a)); },
                                        get filtered() { return this.activeYear ? this.issues.filter(i => String(i.year) === this.activeYear) : this.issues; },
                                        init() { this.$nextTick(() => { if (this.years.length) this.activeYear = this.years[0]; }); },
                                    }"
                                        class="rounded-xl shadow-md cursor-pointer group select-none transition-all duration-200"
                                        :class="activeTab === '{{ $groupId }}' ?
                                            'ring-2 {{ $cardRing }} shadow-xl' :
                                            'ring-1 ring-white/10 hover:shadow-xl hover:ring-blue-300/40'"
                                        @click="activeTab = activeTab === '{{ $groupId }}' ? null : '{{ $groupId }}';
                                                if (activeTab === '{{ $groupId }}') { $nextTick(() => document.getElementById('annual-panel').scrollIntoView({ behavior: 'smooth', block: 'nearest' })) }">

                                        {{-- Card image + text --}}
                                        <div class="relative flex overflow-hidden rounded-t-xl bg-slate-800"
                                            style="height:250px;">
                                            <div class="w-1/3 flex-shrink-0 relative overflow-hidden">
                                                @if ($bannerThumb)
                                                    <img src="{{ $bannerThumb }}"
                                                        class="absolute inset-0 w-full h-full object-cover scale-110 blur-[1px] opacity-80">
                                                    <img src="{{ $bannerThumb }}"
                                                        class="absolute inset-0 w-full h-full object-contain p-2">
                                                @else
                                                    <div
                                                        class="absolute inset-0 bg-slate-700 flex items-center justify-center">
                                                        <svg class="w-10 h-10 text-slate-500" fill="none"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex-1 flex flex-col justify-center px-5 py-4 transition-colors duration-200 {{ $cardPanel }} {{ $cardPanelHover }}"
                                                :class="activeTab === '{{ $groupId }}' ? '{{ $cardPanel }}' : ''">

                                                {{-- Status badges --}}
                                                <div class="flex items-center gap-1.5 mb-2" @click.stop>
                                                    <span
                                                        class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full"
                                                        :class="isPublished ? 'bg-emerald-500/20 text-emerald-300' :
                                                            'bg-amber-500/20 text-amber-300'">
                                                        <span class="w-1.5 h-1.5 rounded-full"
                                                            :class="isPublished ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                                                        <span x-text="isPublished ? 'Published' : 'Draft'"></span>
                                                    </span>
                                                    <span x-show="isPublished && hasDraftChanges" x-cloak
                                                        class="inline-flex items-center gap-1 text-[0.6rem] font-bold px-2 py-0.5 rounded-full bg-orange-500/20 text-orange-300 border border-orange-400/30">
                                                        ⚠ Unpublished changes
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-3 w-full max-w-m">
                                                    
                                                    <div class="flex-1 border-t border-white/25"></div>
                                                    
                                                </div>
                                                <h2
                                                    class="text-white text-[20px] font-bold text-center tracking-widest leading-snug drop-shadow mb-2 mt-2"
                                                    x-text="issues[0]?.title?.toUpperCase() ?? '{{ $cardTitle }}'">
                                                </h2>
                                                <h3
                                                    class="text-slate-100 text-center text-[0.65rem] tracking-widest font-semibold"
                                                    x-text="issues[0]?.subtitle?.toUpperCase() ?? '{{ $cardSubtitle }}'">
                                                </h3>
                                                <p
                                                    class="pt-3 text-slate-100 text-center text-[0.55rem] tracking-widest line-clamp-3"
                                                    x-text="issues[0]?.description ?? '{{ $cardDesc }}'">
                                                </p>
                                                <div class="flex items-center gap-3 w-full max-w-m mt-2">
                                                    
                                                    <div class="flex-1 border-t border-white/25"></div>
                                                    
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Chevron footer --}}
                                        <div
                                            class="flex items-center justify-center gap-1.5 py-2 rounded-b-xl transition-colors duration-200 {{ $cardFooter }} {{ $cardFooterHover }}">
                                            <span class="text-[0.6rem] font-bold tracking-widest uppercase"
                                                :class="activeTab === '{{ $groupId }}' ? 'text-white' :
                                                    'text-slate-400 group-hover:text-slate-200'"
                                                x-text="activeTab === '{{ $groupId }}' ? 'Close' : 'CLICK TO MANAGE'"></span>
                                            <svg class="w-3.5 h-3.5 transition-all duration-300"
                                                :class="activeTab === '{{ $groupId }}' ? 'rotate-180 text-white' :
                                                    'text-slate-400 group-hover:text-slate-200'"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                @endforeach

                            </div>{{-- /tab cards row --}}

                            {{-- ── Shared Annual Panel ── --}}
                            <div id="annual-panel" x-show="activeTab !== null"
                                x-transition:enter="transition ease-out duration-250"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-2"
                                class="w-full mt-3 rounded-xl overflow-hidden shadow-md border border-slate-200 bg-slate-50"
                                style="display:none;">

                                @foreach ($groupData as $groupId => $group)
                                    @php
                                        $issuesJson = json_encode($group['issues']);
                                        $isPublished = $group['is_published'];
                                        $hasDraft = $group['has_draft_changes'];
                                        $yearType = $groupId === 'jlmf' ? 'range' : 'single';

                                        if ($groupId === 'jlmf') {
                                            $accentBg = '#1a3a7a';
                                            $accentBg2 = '#1d4fa3';
                                            $thumbGrad = '#1a2035';
                                            $panelTitle = 'JOBS AND LABOR MARKET FORECAST';
                                        } elseif ($groupId === 'lmp') {
                                            $accentBg = '#6b1528';
                                            $accentBg2 = '#8b1e35';
                                            $thumbGrad = '#6b1528';
                                            $panelTitle = 'LABOR MARKET PROFILE';
                                        } elseif ($groupId === 'lmu') {
                                            $accentBg = '#6b5035';
                                            $accentBg2 = '#8b6d4c';
                                            $thumbGrad = '#6b5035';
                                            $panelTitle = 'LABOR MARKET UPDATE';
                                        } else {
                                            $accentBg = '#1e3a5f';
                                            $accentBg2 = '#1e3a5f';
                                            $thumbGrad = '#1e3a5f';
                                            $panelTitle = strtoupper($group['title']);
                                        }
                                    @endphp

                                    <template x-if="activeTab === '{{ $groupId }}'">
                                        <div x-data="{
                                                        activeYear: '',
                                                        get cardState() { return Alpine.$data(document.getElementById('lmi-card-{{ $groupId }}')); },
                                                        get issues() { return this.cardState?.issues ?? []; },
                                                        get isPublished() { return this.cardState?.isPublished ?? false; },
                                                        get hasDraftChanges() { return this.cardState?.hasDraftChanges ?? false; },
                                                        get years() { return [...new Set(this.issues.map(i => String(i.year)))].sort((a, b) => b.localeCompare(a)); },
                                                        get filtered() { return this.activeYear ? this.issues.filter(i => String(i.year) === this.activeYear) : this.issues; },
                                                        init() { this.$nextTick(() => { if (this.years.length) this.activeYear = this.years[0]; }); },
                                                    }">

                                            {{-- Panel header --}}
                                            <div
                                                class="flex items-center justify-between px-5 pt-4 pb-3 gap-4 flex-wrap">
                                                <div class="flex flex-col gap-1 min-w-0">
                                                    <span
                                                        class="inline-flex items-center gap-1.5 text-[0.62rem] font-bold tracking-widest uppercase text-rose-500">
                                                        <span
                                                            class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Regional
                                                        Archives
                                                    </span>
                                                    <h2 class="text-slate-800 font-black tracking-wide leading-tight"
                                                        style="font-size:clamp(1.1rem,2.2vw,1.6rem);">
                                                        {{ $panelTitle }}
                                                    </h2>
                                                </div>
                                                <div class="flex items-center gap-3 flex-shrink-0 flex-wrap">
                                                    <div class="flex items-center gap-2" x-show="years.length > 0">
                                                        <span
                                                            class="text-[0.72rem] font-semibold text-slate-600 tracking-wider hidden sm:block">ARCHIVE
                                                            YEAR</span>
                                                        <select @change="activeYear = $event.target.value"
                                                            :value="activeYear" @click.stop
                                                            class="text-[0.75rem] font-bold text-slate-700 bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 shadow-sm cursor-pointer hover:border-[#023E8A] focus:outline-none focus:ring-2 focus:ring-[#023E8A]/30 transition-colors">
                                                            <option value="">All years</option>
                                                            <template x-for="yr in years" :key="yr">
                                                                <option :value="yr"
                                                                    :selected="activeYear === yr"
                                                                    x-text="yr + ' Series'"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                    <button
                                                        @click.stop="$dispatch('open-modal', {
                                                            type: 'add-issue',
                                                            groupId: '{{ $groupId }}',
                                                            groupName: '{{ addslashes($group['title']) }}',
                                                            yearType: '{{ $yearType }}',
                                                        })"
                                                        class="plus-btn flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg shadow-sm text-white"
                                                        style="background:{{ $accentBg }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                        Add Publication
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="border-b border-slate-200 mx-5"></div>

                                            {{-- Issues --}}
                                            <div class="px-5 py-4">
                                                <template x-if="filtered.length > 0">
                                                    <div class="flex flex-col gap-4">
                                                        <template x-for="issue in filtered" :key="issue.id">
                                                            <div class="w-full rounded-2xl overflow-hidden shadow-xl flex flex-row relative"
                                                                style="height:480px; background:linear-gradient(120deg, {{ $accentBg }} 0%, {{ $accentBg2 }} 40%, {{ $accentBg }} 100%);">

                                                                {{-- Admin action buttons --}}
                                                                <div class="absolute top-3 right-3 z-10 flex gap-1.5"
                                                                    @click.stop>
                                                                    <button
                                                                        @click.stop="$dispatch('open-modal', {
                                                                            type: 'edit-issue',
                                                                            groupId: '{{ $groupId }}',
                                                                            groupName: '{{ addslashes($group['title']) }}',
                                                                            yearType: '{{ $yearType }}',
                                                                            issueId: issue.id,
                                                                            data: {
                                                                                title: issue.title,
                                                                                subtitle: issue.subtitle,
                                                                                description: issue.description,
                                                                                year: issue.year,
                                                                                drive_url: issue.drive_file_id ? 'https://drive.google.com/file/d/' + issue.drive_file_id + '/view?usp=sharing' : '',
                                                                            }
                                                                        })"
                                                                        class="w-8 h-8 bg-indigo-600 text-white rounded-lg flex items-center justify-center shadow-lg hover:bg-indigo-700 transition">
                                                                        <svg class="w-4 h-4" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                        </svg>
                                                                    </button>
                                                                    <button
                                                                        @click.stop="$dispatch('open-modal', {
                                                                            type: 'delete-issue',
                                                                            groupId: '{{ $groupId }}',
                                                                            issueId: issue.id,
                                                                        })"
                                                                        class="w-8 h-8 bg-red-600 text-white rounded-lg flex items-center justify-center shadow-lg hover:bg-red-700 transition">
                                                                        <svg class="w-4 h-4" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M6 18L18 6M6 6l12 12" />
                                                                        </svg>
                                                                    </button>
                                                                </div>

                                                                {{-- Thumbnail --}}
                                                                <div class="relative flex-shrink-0 overflow-hidden"
                                                                    style="width:28%;">
                                                                    <img :src="issue.thumbnail_url"
                                                                        :alt="issue.title"
                                                                        class="absolute inset-0 w-full h-full object-cover scale-110 blur-[2px] opacity-80"
                                                                        loading="lazy"
                                                                        onerror="this.style.display='none'">
                                                                    <img :src="issue.thumbnail_url"
                                                                        :alt="issue.title"
                                                                        class="absolute inset-0 w-full h-full object-contain p-8"
                                                                        loading="lazy"
                                                                        onerror="this.style.display='none'">
                                                                    <div class="absolute inset-0"
                                                                        style="background:linear-gradient(to right, transparent 55%, {{ $thumbGrad }} 100%);">
                                                                    </div>
                                                                </div>

                                                                {{-- Content --}}
                                                                <div
                                                                    class="flex flex-col justify-center items-center flex-1 px-10 py-10 gap-6 min-w-0 relative text-center">
                                                                    <div class="flex items-center gap-2 w-full mb-1">
                                                                        <div class="flex-1 border-t border-white/30">
                                                                        </div>
                                                                    </div>
                                                                    <h2 class="text-white font-black leading-tight"
                                                                        style="font-size:clamp(1.6rem,3.2vw,2.6rem);letter-spacing:0.15em;text-shadow:0 2px 16px rgba(0,0,0,0.5);"
                                                                        x-text="issue.title.toUpperCase()"></h2>
                                                                    <p class="text-white text-[1rem] font-bold tracking-[0.3em]"
                                                                        x-text="issue.subtitle"></p>
                                                                    <p class="text-slate-100 text-[0.95rem] font-semibold leading-relaxed italic max-w-2xl"
                                                                        x-text="issue.description || ''"></p>
                                                                    <div
                                                                        class="flex items-center justify-center gap-6 flex-wrap pt-2">
                                                                        <template x-if="issue.view_url">
                                                                            <a :href="issue.view_url" target="_blank"
                                                                                rel="noopener" @click.stop
                                                                                class="inline-flex items-center gap-2 text-[0.7rem] font-bold tracking-widest text-slate-900 bg-white hover:bg-slate-100 px-8 py-4 rounded-lg transition-colors shadow-md">
                                                                                <svg class="w-3.5 h-3.5"
                                                                                    fill="none"
                                                                                    stroke="currentColor"
                                                                                    stroke-width="2.5"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                                                </svg>
                                                                                DOWNLOAD
                                                                            </a>
                                                                        </template>
                                                                        <template x-if="issue.view_url">
                                                                            <a :href="issue.view_url" target="_blank"
                                                                                rel="noopener" @click.stop
                                                                                class="inline-flex items-center gap-2 text-[0.7rem] font-bold tracking-widest text-white/80 hover:text-white border border-white/30 hover:border-white/60 px-8 py-4 rounded-lg transition-colors">
                                                                                <svg class="w-3.5 h-3.5"
                                                                                    fill="none"
                                                                                    stroke="currentColor"
                                                                                    stroke-width="2.5"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                                                </svg>
                                                                                READ ONLINE
                                                                            </a>
                                                                        </template>
                                                                    </div>
                                                                    <div class="flex items-center gap-2 w-full mt-1">
                                                                        
                                                                        <div class="flex-1 border-t border-white/30">
                                                                        </div>
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="filtered.length === 0">
                                                    <div class="py-10 flex flex-col items-center gap-2 text-slate-400">
                                                        <svg class="w-7 h-7 opacity-40" fill="none"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                        </svg>
                                                        <p class="text-sm">No publications yet. Click "Add Publication"
                                                            to get started.</p>
                                                    </div>
                                                </template>
                                            </div>

                                            {{-- Publish footer --}}
                                            <div class="border-t border-slate-200 px-5 py-4 bg-white">
                                                <div x-show="isPublished && hasDraftChanges" x-cloak
                                                    class="flex items-center gap-3 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl mb-3">
                                                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                                    </svg>
                                                    <div class="flex-1">
                                                        <p class="text-sm font-semibold text-amber-800">You have
                                                            unpublished changes</p>
                                                        <p class="text-xs text-amber-600 mt-0.5">Click
                                                            <strong>Republish</strong> to push changes live.
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between gap-4 flex-wrap">
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-2.5 h-2.5 rounded-full"
                                                            :class="isPublished ? 'bg-emerald-500' : 'bg-amber-400'"></span>
                                                        <p class="text-sm font-semibold"
                                                            :class="isPublished ? 'text-emerald-700' : 'text-amber-700'">
                                                            <span x-show="isPublished && hasDraftChanges">Live — but
                                                                has unpublished changes.</span>
                                                            <span x-show="isPublished && !hasDraftChanges">This group
                                                                is live and visible to the public.</span>
                                                            <span x-show="!isPublished">This group is a draft — not
                                                                visible to the public yet.</span>
                                                        </p>
                                                    </div>
                                                    <div class="flex items-center gap-2 flex-shrink-0">
                                                        <button x-show="isPublished && hasDraftChanges" x-cloak
                                                            @click.stop="$dispatch('open-modal', { type: 'republish', groupId: '{{ $groupId }}', groupName: '{{ addslashes($group['title']) }}' })"
                                                            class="flex items-center gap-2 px-5 py-2 rounded-xl font-bold text-sm bg-amber-500 hover:bg-amber-600 text-white transition shadow-sm">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                            </svg>
                                                            Republish Changes
                                                        </button>
                                                        <button
                                                            @click.stop="$dispatch('open-modal', { type: isPublished ? 'unpublish' : 'publish', groupId: '{{ $groupId }}', groupName: '{{ addslashes($group['title']) }}' })"
                                                            class="flex items-center gap-2 px-5 py-2 rounded-xl font-bold text-sm transition shadow-sm"
                                                            :class="isPublished ?
                                                                'bg-white hover:bg-slate-100 text-slate-600 border border-slate-300' :
                                                                'bg-emerald-600 hover:bg-emerald-700 text-white'">
                                                            <template x-if="isPublished">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                                </svg>
                                                            </template>
                                                            <template x-if="!isPublished">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                            </template>
                                                            <span
                                                                x-text="isPublished ? 'Unpublish' : '✓ Done — Publish to Public'"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </template>
                                @endforeach

                            </div>{{-- /shared annual panel --}}

                        </div>{{-- /annual tab section --}}

                        {{-- ── WEEKLY SECTION ── --}}
                        <div class="flex flex-col pt-5 gap-0" x-data="{
                            cardSetting: {{ json_encode($weeklyCardSetting) }},
                            isPublished: {{ $weeklyCardSetting['is_published'] ? 'true' : 'false' }},
                            hasDraftChanges: {{ $weeklyCardSetting['has_draft_changes'] ? 'true' : 'false' }},
                            open: false,
                            activeYear: {{ count($weeklyData['years']) ? $weeklyData['years'][0] : 'new Date().getFullYear()' }},
                            years: {{ json_encode($weeklyData['years']) }},
                            issuesByYear: {{ json_encode($weeklyData['issuesByYear']) }},
                            get currentIssues() { return this.issuesByYear[this.activeYear] || []; },
                            get byMonth() {
                                const map = {};
                                this.currentIssues.forEach(i => {
                                    if (!map[i.month]) map[i.month] = { month: i.month, order: i.monthOrder, issues: [] };
                                    map[i.month].issues.push(i);
                                });
                                return Object.values(map).sort((a, b) => a.order - b.order);
                            },
                            async refresh() {
                                const res = await fetch('/admin/lmi-weekly/data', {
                                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                                });
                                if (!res.ok) return;
                                const data = await res.json();
                                this.issuesByYear = data.issuesByYear;
                                this.years = data.years;
                                if (this.years.length && !this.years.includes(Number(this.activeYear))) {
                                    this.activeYear = this.years[0];
                                }
                            },
                            init() {
                                window.addEventListener('weekly-refresh', () => this.refresh());
                                window.addEventListener('card-setting-refresh', (e) => {
                                    this.cardSetting = e.detail;
                                    this.isPublished = e.detail.is_published ?? this.isPublished;
                                    this.hasDraftChanges = e.detail.has_draft_changes ?? this.hasDraftChanges;
                                });
                                window.addEventListener('weekly-publish-refresh', (e) => {
                                    this.isPublished = e.detail.is_published;
                                    this.hasDraftChanges = e.detail.has_draft_changes;
                                });
                            },
                        }">

                            {{-- Weekly Tab Card --}}
                            <div class="rounded-xl shadow-md cursor-pointer group select-none transition-all duration-200"
                                :class="open ? 'ring-2 ring-blue-400 shadow-xl' :
                                    'ring-1 ring-white/10 hover:shadow-xl hover:ring-blue-300/40'"
                                @click="open = !open; if(open) { $nextTick(() => document.getElementById('weekly-panel').scrollIntoView({ behavior: 'smooth', block: 'nearest' })) }">

                                <div class="relative flex overflow-hidden rounded-t-xl bg-slate-800"
                                    style="height:340px;">

                                    {{-- Edit Card buttons (top-right of banner) --}}
                                    <div class="absolute top-3 right-3 z-10 flex gap-1.5" @click.stop>
                                        {{-- Edit Text --}}
                                        <button
                                            @click.stop="$dispatch('open-weekly-modal', {
                                                type: 'edit-card-text',
                                                data: {
                                                    title:       cardSetting.title,
                                                    subtitle:    cardSetting.subtitle,
                                                    description: cardSetting.description,
                                                }
                                            })"
                                            title="Edit Text"
                                            class="w-8 h-8 bg-indigo-600 text-white rounded-lg flex items-center justify-center shadow-lg hover:bg-indigo-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        {{-- Edit Image & Link --}}
                                        <button
                                            @click.stop="$dispatch('open-weekly-modal', {
                                                type: 'edit-card-media',
                                                data: { link_url: cardSetting.link_url }
                                            })"
                                            title="Edit Image & Link"
                                            class="w-8 h-8 bg-slate-600 text-white rounded-lg flex items-center justify-center shadow-lg hover:bg-slate-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="w-2/5 flex-shrink-0 relative overflow-hidden group/img"
                                        :class="cardSetting.link_url ? 'cursor-pointer' : ''"
                                        @click="cardSetting.link_url && window.open(cardSetting.link_url, '_blank')">
                                        <template x-if="cardSetting.image_url">
                                            <img :src="cardSetting.image_url"
                                                class="absolute inset-0 w-full h-full object-cover scale-110 blur-[1px] opacity-80">
                                        </template>
                                        <template x-if="cardSetting.image_url">
                                            <img :src="cardSetting.image_url"
                                                alt="Regional LMI Weekly"
                                                class="absolute inset-0 w-full h-full object-contain p-2">
                                        </template>
                                        <template x-if="!cardSetting.image_url">
                                            <div class="absolute inset-0 bg-slate-700 flex items-center justify-center">
                                                <svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </template>

                                        {{-- Link hover overlay — only shows when link_url is set --}}
                                        <template x-if="cardSetting.link_url">
                                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover/img:opacity-100 transition-opacity duration-200 bg-black/25">
                                                <svg class="w-8 h-8 text-white drop-shadow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex-1 flex flex-col justify-between gap-4 px-5 py-6 transition-colors duration-200"
                                        :class="open ? 'bg-slate-700' : 'bg-slate-900 group-hover:bg-slate-800'">
                                        <div class="flex items-center gap-3 w-full">
                                            
                                            <div class="flex-1 border-t border-white/25"></div>
                                            
                                        </div>
                                        <div class="flex flex-col items-center gap-3">
                                            <h2 class="text-white font-black text-center tracking-widest leading-tight drop-shadow"
                                                style="font-size:clamp(1.6rem,3.2vw,2.6rem);letter-spacing:0.15em;text-shadow:0 2px 16px rgba(0,0,0,0.5);"
                                                x-text="cardSetting.title ?? 'REGIONAL LMI WEEKLY'"></h2>
                                            <h3 class="text-white text-center font-bold tracking-[0.3em]"
                                                style="font-size:clamp(0.6rem,1vw,1rem);"
                                                x-text="cardSetting.subtitle ?? 'WEEKLY TRENDS BULLETIN'"></h3>
                                            <p class="text-slate-100 text-center font-semibold leading-relaxed italic max-w-2xl pt-2"
                                                style="font-size:clamp(0.75rem,1.2vw,0.95rem);"
                                                x-text="cardSetting.description ?? 'Direct insights on weekly hiring trends and vacancy fluctuations in the Davao region. (Based on PhilJobNet)'"></p>
                                        </div>
                                        <div class="flex items-center gap-3 w-full">
                                            
                                            <div class="flex-1 border-t border-white/25"></div>
                                            
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-center gap-1.5 py-2 rounded-b-xl transition-colors duration-200"
                                    :class="open ? 'bg-slate-800' : 'bg-slate-800 group-hover:bg-slate-700'">
                                    <span class="text-[0.6rem] font-bold tracking-widest uppercase"
                                        :class="open ? 'text-white' : 'text-slate-400 group-hover:text-slate-200'"
                                        x-text="open ? 'Close' : 'CLICK TO MANAGE'"></span>
                                    <svg class="w-3.5 h-3.5 transition-all duration-300"
                                        :class="open ? 'rotate-180 text-white' : 'text-slate-400 group-hover:text-slate-200'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>{{-- /weekly tab card --}}

                            {{-- Weekly Shared Panel --}}
                            <div id="weekly-panel" x-show="open"
                                x-transition:enter="transition ease-out duration-250"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-2"
                                class="w-full mt-3 rounded-xl overflow-hidden shadow-md border border-slate-200 bg-slate-50"
                                style="display:none;">

                                <div class="flex items-center justify-between px-5 pt-4 pb-3 gap-4 flex-wrap">
                                    <div class="flex flex-col gap-1 min-w-0">
                                        <span
                                            class="inline-flex items-center gap-1.5 text-[0.62rem] font-bold tracking-widest uppercase text-rose-500">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Regional Archives
                                        </span>
                                        <h2 class="text-slate-800 font-black tracking-wide leading-tight"
                                            style="font-size:clamp(1.1rem,2.2vw,1.6rem);">
                                            REGIONAL LMI WEEKLY
                                        </h2>
                                    </div>
                                    <div class="flex items-center gap-3 flex-shrink-0 flex-wrap">
                                        <span
                                            class="text-[0.72rem] font-semibold text-slate-600 tracking-wider hidden sm:block">ARCHIVE
                                            YEAR</span>
                                        <select x-model="activeYear" @click.stop
                                            class="text-[0.75rem] font-bold text-slate-700 bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 shadow-sm cursor-pointer hover:border-[#023E8A] focus:outline-none focus:ring-2 focus:ring-[#023E8A]/30 transition-colors">
                                            <template x-for="yr in years" :key="yr">
                                                <option :value="yr" x-text="yr + ' Series'"></option>
                                            </template>
                                        </select>
                                        <button @click.stop="$dispatch('open-weekly-modal', { type: 'add-weekly' })"
                                            class="plus-btn flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg shadow-sm text-white bg-slate-800 hover:bg-slate-700">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add Weekly Issue
                                        </button>
                                    </div>
                                </div>
                                <div class="border-b border-slate-200 mx-5"></div>

                                <div class="px-5 py-4">
                                    <template x-if="byMonth.length > 0">
                                        <div class="flex flex-col gap-5">
                                            <template x-for="monthGroup in byMonth" :key="monthGroup.month">
                                                <div>
                                                    <div class="flex items-center gap-2 mb-3">
                                                        <div class="h-px flex-1 bg-slate-200"></div>
                                                        <span
                                                            class="text-[0.7rem] font-bold text-slate-500 uppercase tracking-wider px-2"
                                                            x-text="monthGroup.month"></span>
                                                        <div class="h-px flex-1 bg-slate-200"></div>
                                                    </div>
                                                    <div class="flex flex-wrap justify-center gap-15">
                                                        <template x-for="issue in monthGroup.issues"
                                                            :key="issue.id">
                                                            <div
                                                                class="group/card w-60 bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-0.5 hover:border-slate-300 transition-all duration-300 flex flex-col relative">

                                                                <div class="absolute top-2 right-2 z-10 flex gap-1">
                                                                    <button
                                                                        @click.stop="$dispatch('open-weekly-modal', {
                                                                            type: 'edit-weekly',
                                                                            issueId: issue.id,
                                                                            data: {
                                                                                year: issue.year,
                                                                                month: issue.month,
                                                                                week_number: issue.weekNumber,
                                                                                date_range: issue.dateRange,
                                                                                link_url: issue.linkUrl
                                                                            }
                                                                        })"
                                                                        class="w-6 h-6 bg-indigo-600 text-white rounded flex items-center justify-center shadow hover:bg-indigo-700 transition">
                                                                        <svg class="w-3 h-3" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                        </svg>
                                                                    </button>
                                                                    <button
                                                                        @click.stop="$dispatch('open-weekly-modal', { type: 'delete-weekly', issueId: issue.id })"
                                                                        class="w-6 h-6 bg-red-600 text-white rounded flex items-center justify-center shadow hover:bg-red-700 transition">
                                                                        <svg class="w-3 h-3" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M6 18L18 6M6 6l12 12" />
                                                                        </svg>
                                                                    </button>
                                                                </div>

                                                                <div
                                                                    class="px-3 py-2 bg-slate-50 border-b border-slate-200">
                                                                    <p class="text-[0.68rem] font-bold text-slate-700 leading-snug"
                                                                        x-text="issue.weekLabel"></p>
                                                                    <p class="text-[0.62rem] text-slate-400 mt-0.5"
                                                                        x-text="issue.dateRange"></p>
                                                                </div>

                                                                <div
                                                                    class="flex-shrink-0 bg-slate-100 relative overflow-hidden">
                                                                    <template x-if="issue.imageUrl">
                                                                        <div class="relative group/img">
                                                                            <img :src="issue.imageUrl"
                                                                                :alt="issue.weekLabel"
                                                                                class="w-full object-contain cursor-pointer"
                                                                                style="aspect-ratio:3/4;display:block;background:#f1f5f9;"
                                                                                loading="lazy"
                                                                                @click.stop="openLightbox(issue.imageUrl, issue.weekLabel + (issue.dateRange ? ' · ' + issue.dateRange : ''))">
                                                                            <div class="absolute inset-0 bg-black/0 group-hover/img:bg-black/30 transition-all duration-200 flex items-center justify-center cursor-pointer"
                                                                                @click.stop="openLightbox(issue.imageUrl, issue.weekLabel + (issue.dateRange ? ' · ' + issue.dateRange : ''))">
                                                                                <span
                                                                                    class="opacity-0 group-hover/img:opacity-100 transition-opacity duration-200 flex items-center gap-1.5 text-white text-xs font-bold bg-black/50 px-3 py-1.5 rounded-lg">
                                                                                    <svg class="w-3.5 h-3.5"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            stroke-width="2"
                                                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            stroke-width="2"
                                                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                                    </svg>
                                                                                    View Image
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </template>
                                                                    <template x-if="!issue.imageUrl">
                                                                        <div class="w-full flex flex-col items-center justify-center gap-2 bg-slate-100 text-slate-400"
                                                                            style="aspect-ratio:3/4;">
                                                                            <svg class="w-6 h-6" fill="none"
                                                                                stroke="currentColor"
                                                                                stroke-width="1.5"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                            </svg>
                                                                            <span class="text-[0.65rem]">No image
                                                                                yet</span>
                                                                        </div>
                                                                    </template>
                                                                </div>

                                                                <div
                                                                    class="px-3 py-2 flex items-center justify-center gap-2 mt-auto border-t border-slate-100">
                                                                    <template x-if="issue.imageUrl">
                                                                        <button
                                                                            @click.stop="openLightbox(issue.imageUrl, issue.weekLabel + (issue.dateRange ? ' · ' + issue.dateRange : ''))"
                                                                            class="inline-flex items-center gap-1.5 text-[0.62rem] font-semibold text-[#023E8A] hover:underline">
                                                                            <svg class="w-3 h-3" fill="none"
                                                                                stroke="currentColor"
                                                                                stroke-width="2.5"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                            </svg>
                                                                            View Image
                                                                        </button>
                                                                    </template>
                                                                    <template x-if="!issue.imageUrl">
                                                                        <span
                                                                            class="text-[0.62rem] text-slate-400 italic">No
                                                                            image yet</span>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="byMonth.length === 0">
                                        <div class="py-10 flex flex-col items-center gap-2 text-slate-400">
                                            <svg class="w-7 h-7 opacity-40" fill="none" stroke="currentColor"
                                                stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                            <p class="text-sm">No weekly issues yet. Click "Add Weekly Issue" to get
                                                started.</p>
                                        </div>
                                    </template>
                                </div>

                                {{-- Publish footer --}}
                                <div class="border-t border-slate-200 px-5 py-4 bg-white">
                                    <div x-show="isPublished && hasDraftChanges" x-cloak
                                        class="flex items-center gap-3 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl mb-3">
                                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-amber-800">You have unpublished changes</p>
                                            <p class="text-xs text-amber-600 mt-0.5">Click <strong>Republish</strong> to push changes live.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between gap-4 flex-wrap">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full"
                                                :class="isPublished ? 'bg-emerald-500' : 'bg-amber-400'"></span>
                                            <p class="text-sm font-semibold"
                                                :class="isPublished ? 'text-emerald-700' : 'text-amber-700'">
                                                <span x-show="isPublished && hasDraftChanges">Live — but has unpublished changes.</span>
                                                <span x-show="isPublished && !hasDraftChanges">This section is live and visible to the public.</span>
                                                <span x-show="!isPublished">This section is a draft — not visible to the public yet.</span>
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <button x-show="isPublished && hasDraftChanges" x-cloak
                                                @click.stop="$dispatch('open-weekly-modal', { type: 'weekly-republish' })"
                                                class="flex items-center gap-2 px-5 py-2 rounded-xl font-bold text-sm bg-amber-500 hover:bg-amber-600 text-white transition shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                Republish Changes
                                            </button>
                                            <button
                                                @click.stop="$dispatch('open-weekly-modal', { type: isPublished ? 'weekly-unpublish' : 'weekly-publish' })"
                                                class="flex items-center gap-2 px-5 py-2 rounded-xl font-bold text-sm transition shadow-sm"
                                                :class="isPublished ?
                                                    'bg-white hover:bg-slate-100 text-slate-600 border border-slate-300' :
                                                    'bg-emerald-600 hover:bg-emerald-700 text-white'">
                                                <template x-if="isPublished">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                    </svg>
                                                </template>
                                                <template x-if="!isPublished">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </template>
                                                <span x-text="isPublished ? 'Unpublish' : '✓ Done — Publish to Public'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>{{-- /weekly panel --}}

                        </div>{{-- /weekly section --}}

                    </div>{{-- /publication groups --}}

                </div>{{-- /card --}}
            </div>{{-- /main content --}}

            <div class="p-4 bg-slate-50 border-t border-slate-200 text-center">
                <p class="text-xs text-slate-500">Source: Philippine Statistics Authority; Labor Force Survey</p>
            </div>

            {{-- ── ANNUAL MODALS ── --}}
            <div x-show="modal.open" x-cloak @open-modal.window="openModal($event.detail)"
                class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
                @keydown.escape.window="modal.open = false">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-lg" x-text="modal.title"></h3>
                        <button @click="modal.open = false"
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="px-6 py-6">

                        {{-- ADD / EDIT ISSUE --}}
                        <div x-show="(modal.type === 'add-issue' || modal.type === 'edit-issue') && !modal.confirming" x-cloak
                            class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Title <span
                                        class="text-red-500">*</span></label>
                                <input type="text" x-model="form.title"
                                    :class="formErrors.title ? 'border-red-500 ring-2 ring-red-200' :
                                        'border-slate-300 focus:ring-indigo-400'"
                                    @input="formErrors.title = false" placeholder="e.g. Labor Market Profile 2024"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                            </div>
                            
                            {{-- Subtitle --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Subtitle <span class="text-red-500">*</span></label>
                                    <input 
                                    type="text"
                                    x-model="form.subtitle"
                                    maxlength="100"
                                    :class="formErrors.subtitle ? 'border-red-500 ring-2 ring-red-200' :
                                        'border-slate-300 focus:ring-indigo-400'"
                                    @input="formErrors.subtitle = false" placeholder="e.g. INDUSTRY GROWTH & ACTION AGENDA"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none"
                                    />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">
                                    Year <span class="text-red-500">*</span>
                                    <span class="text-slate-400 font-normal text-xs ml-1" x-text="yearHint"></span>
                                </label>
                                <input type="text" x-model="form.year" :placeholder="yearPlaceholder"
                                    :pattern="yearPattern"
                                    :class="formErrors.year ? 'border-red-500 ring-2 ring-red-200' :
                                        'border-slate-300 focus:ring-indigo-400'"
                                    @input="formErrors.year = false"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Description <span
                                        class="text-red-500">*</span></label>
                                <textarea x-model="form.description" rows="3" placeholder="Brief description..."
                                        :class="formErrors.description ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-300 focus:ring-indigo-400'"
                                        @input="formErrors.description = false"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Google Drive URL <span
                                        class="text-red-500">*</span></label>
                                <input type="text" x-model="form.drive_url"
                                    placeholder="https://drive.google.com/file/d/..."
                                    :class="formErrors.drive_url ? 'border-red-500 ring-2 ring-red-200' :
                                        'border-slate-300 focus:ring-indigo-400'"
                                    @input="formErrors.drive_url = false"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                                <p class="text-xs text-slate-400 mt-1">Paste the full Google Drive share link — file ID
                                    extracted automatically.</p>
                            </div>
                            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
                                <button type="button" @click="modal.open = false"
                                    class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="requestConfirm()"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition">
                                    Review & Save
                                </button>
                            </div>
                        </div>

                        {{-- ADD / EDIT ISSUE — CONFIRM STEP --}}
                        <template x-if="(modal.type === 'add-issue' || modal.type === 'edit-issue') && modal.confirming">
                            <div class="text-center space-y-4">
                                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-slate-800 font-bold text-lg">
                                    <span x-text="modal.type === 'add-issue' ? 'Add this publication?' : 'Save changes to this publication?'"></span>
                                </p>
                                <div class="bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 text-left space-y-2 text-sm">
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">Title</span><span class="font-semibold text-slate-800 truncate" x-text="form.title"></span></div>
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">Subtitle</span><span class="text-slate-600 truncate" x-text="form.subtitle"></span></div>
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">Year</span><span class="text-slate-600" x-text="form.year"></span></div>
                                </div>
                                <div class="flex gap-3 justify-center pt-1">
                                    <button type="button" @click="modal.confirming = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">← Back</button>
                                    <button type="button" @click="submitIssue()" :disabled="modal.loading"
                                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading" x-text="modal.type === 'add-issue' ? 'Yes, Add' : 'Yes, Save'"></span>
                                        <span x-show="modal.loading" x-cloak>Saving…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- DELETE --}}
                        <template x-if="modal.type === 'delete-issue'">
                            <div class="text-center space-y-4">
                                <div
                                    class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <p class="text-slate-700 font-semibold">Delete this publication?</p>
                                <p class="text-slate-500 text-sm">This action cannot be undone.</p>
                                <div class="flex gap-3 justify-center">
                                    <button type="button" @click="modal.open = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                    <button type="button" @click="submitDelete()" :disabled="modal.loading"
                                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading">Yes, Delete</span>
                                        <span x-show="modal.loading" x-cloak>Deleting…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- PUBLISH --}}
                        <template x-if="modal.type === 'publish'">
                            <div class="text-center space-y-4">
                                <div
                                    class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="text-slate-800 font-bold text-lg">Publish "<span
                                        x-text="modal.groupName"></span>"?</p>
                                <p class="text-slate-500 text-sm">This will make all publications in this group visible
                                    to the public.</p>
                                <div class="flex gap-3 justify-center">
                                    <button type="button" @click="modal.open = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Not
                                        yet</button>
                                    <button type="button" @click="submitTogglePublish()" :disabled="modal.loading"
                                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading">Yes, Publish Now</span>
                                        <span x-show="modal.loading" x-cloak>Publishing…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- REPUBLISH --}}
                        <template x-if="modal.type === 'republish'">
                            <div class="text-center space-y-4">
                                <div
                                    class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </div>
                                <p class="text-slate-800 font-bold text-lg">Republish "<span
                                        x-text="modal.groupName"></span>"?</p>
                                <p class="text-slate-500 text-sm">Your latest changes will go live on the public page.
                                </p>
                                <div class="flex gap-3 justify-center">
                                    <button type="button" @click="modal.open = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Not
                                        yet</button>
                                    <button type="button" @click="submitRepublish()" :disabled="modal.loading"
                                        class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading">Yes, Republish Now</span>
                                        <span x-show="modal.loading" x-cloak>Republishing…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- UNPUBLISH --}}
                        <template x-if="modal.type === 'unpublish'">
                            <div class="text-center space-y-4">
                                <div
                                    class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </div>
                                <p class="text-slate-800 font-bold text-lg">Unpublish "<span
                                        x-text="modal.groupName"></span>"?</p>
                                <p class="text-slate-500 text-sm">This group will be hidden from the public page. You
                                    can republish anytime.</p>
                                <div class="flex gap-3 justify-center">
                                    <button type="button" @click="modal.open = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                    <button type="button" @click="submitUnpublish()" :disabled="modal.loading"
                                        class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading">Yes, Unpublish</span>
                                        <span x-show="modal.loading" x-cloak>Unpublishing…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>
            </div>
            {{-- ── END ANNUAL MODALS ── --}}

            {{-- ── WEEKLY MODALS ── --}}
            <div x-data="weeklyModal()" x-show="open" x-cloak @open-weekly-modal.window="openModal($event.detail)"
                class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
                @keydown.escape.window="open = false">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-lg" x-text="title"></h3>
                        <button @click="open = false"
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="px-6 py-6">

                        {{-- ADD WEEKLY --}}
                        <div x-show="type === 'add-weekly' && !confirming" x-cloak class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Year <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" x-model="form.year" placeholder="{{ date('Y') }}"
                                        min="2000" max="2100"
                                        :class="errors.year ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @input="errors.year = false"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Week<span
                                            class="text-red-500">*</span></label>
                                    <select x-model="form.week_number"
                                        :class="errors.week_number ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none bg-white">
                                        <option value="" disabled selected hidden >Select week</option>
                                        <option value="1">Week 1</option>
                                        <option value="2">Week 2</option>
                                        <option value="3">Week 3</option>
                                        <option value="4">Week 4</option>
                                        
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Month <span
                                        class="text-red-500">*</span></label>
                                <select x-model="form.month"
                                    :class="errors.month ? 'border-red-500 ring-2 ring-red-200' :
                                        'border-slate-300 focus:ring-indigo-400'"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none bg-white">
                                    <option value="">Select month</option>
                                    <template x-for="m in months" :key="m">
                                        <option :value="m" x-text="m"></option>
                                    </template>
                                </select>
                            </div>
                           <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Date Range <span class="text-red-500">*</span></label></label>
                            <input type="text" x-model="form.date_range" placeholder="e.g. April 6–10, 2026"
                                :class="errors.date_range ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-300 focus:ring-indigo-400'"
                                @input="errors.date_range = false"
                                class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                            <p class="text-xs text-slate-400 mt-1">Optional — shown below the week label on the card.</p>
                        </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Image <span
                                        class="text-red-500">*</span></label>
                                <input type="file" accept="image/*" @change="onImageChange"
                                    :class="errors.image ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-300'"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm outline-none file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                <template x-if="previewUrl">
                                    <img :src="previewUrl"
                                        class="mt-2 h-24 rounded-lg object-contain border border-slate-200">
                                </template>
                            </div>
                            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
                                <button type="button" @click="open = false"
                                    class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="requestConfirmAdd()"
                                    class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-semibold text-sm transition">
                                    Review & Save
                                </button>
                            </div>
                        </div>

                        {{-- ADD WEEKLY — CONFIRM STEP --}}
                        <template x-if="type === 'add-weekly' && confirming">
                            <div class="text-center space-y-4">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-slate-800 font-bold text-lg">Add this weekly issue?</p>
                                <div class="bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 text-left space-y-2 text-sm">
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">Year</span><span class="font-semibold text-slate-800" x-text="form.year"></span></div>
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">Month</span><span class="text-slate-600" x-text="form.month"></span></div>
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">Week</span><span class="text-slate-600" x-text="'Week ' + form.week_number"></span></div>
                                    <div class="flex gap-2" x-show="form.date_range"><span class="text-slate-400 w-24 flex-shrink-0">Date Range</span><span class="text-slate-600" x-text="form.date_range"></span></div>
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">Image</span><span class="text-slate-600" x-text="imageFile ? imageFile.name : '—'"></span></div>
                                </div>
                                <div class="flex gap-3 justify-center pt-1">
                                    <button type="button" @click="confirming = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">← Back</button>
                                    <button type="button" @click="submitAdd()" :disabled="loading"
                                        class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!loading">Yes, Add Issue</span>
                                        <span x-show="loading" x-cloak>Saving…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- EDIT WEEKLY --}}
                        <div x-show="type === 'edit-weekly' && !confirming" x-cloak class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Year <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" x-model="form.year" placeholder="{{ date('Y') }}"
                                        min="2000" max="2100"
                                        :class="errors.year ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @input="errors.year = false"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Week # <span
                                            class="text-red-500">*</span></label>
                                    <select x-model="form.week_number"
                                        :class="errors.week_number ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none bg-white">
                                        <option value="">Select week</option>
                                        <option value="1">Week 1</option>
                                        <option value="2">Week 2</option>
                                        <option value="3">Week 3</option>
                                        <option value="4">Week 4</option>
                                        <option value="5">Week 5</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Month <span
                                        class="text-red-500">*</span></label>
                                <select x-model="form.month"
                                    :class="errors.month ? 'border-red-500 ring-2 ring-red-200' :
                                        'border-slate-300 focus:ring-indigo-400'"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none bg-white">
                                    <option value="">Select month</option>
                                    <template x-for="m in months" :key="m">
                                        <option :value="m" x-text="m"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Date Range</label>
                                <input type="text" x-model="form.date_range" placeholder="e.g. April 6–10, 2026"
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                                <p class="text-xs text-slate-400 mt-1">Optional — shown below the week label on the
                                    card.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">New Image</label>
                                <input type="file" accept="image/*" @change="onImageChange"
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm outline-none file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                <p class="text-xs text-slate-400 mt-1">Leave blank to keep the existing image.</p>
                                <template x-if="previewUrl">
                                    <img :src="previewUrl"
                                        class="mt-2 h-24 rounded-lg object-contain border border-slate-200">
                                </template>
                            </div>
                            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
                                <button type="button" @click="open = false"
                                    class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="requestConfirmEdit()"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition">
                                    Review & Update
                                </button>
                            </div>
                        </div>

                        {{-- EDIT WEEKLY — CONFIRM STEP --}}
                        <template x-if="type === 'edit-weekly' && confirming">
                            <div class="text-center space-y-4">
                                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-slate-800 font-bold text-lg">Save changes to this weekly issue?</p>
                                <div class="bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 text-left space-y-2 text-sm">
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">Year</span><span class="font-semibold text-slate-800" x-text="form.year"></span></div>
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">Month</span><span class="text-slate-600" x-text="form.month"></span></div>
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">Week</span><span class="text-slate-600" x-text="'Week ' + form.week_number"></span></div>
                                    <div class="flex gap-2" x-show="form.date_range"><span class="text-slate-400 w-24 flex-shrink-0">Date Range</span><span class="text-slate-600" x-text="form.date_range"></span></div>
                                    <div class="flex gap-2" x-show="imageFile"><span class="text-slate-400 w-24 flex-shrink-0">New Image</span><span class="text-slate-600" x-text="imageFile ? imageFile.name : ''"></span></div>
                                </div>
                                <div class="flex gap-3 justify-center pt-1">
                                    <button type="button" @click="confirming = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">← Back</button>
                                    <button type="button" @click="submitEdit()" :disabled="loading"
                                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!loading">Yes, Update</span>
                                        <span x-show="loading" x-cloak>Updating…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- DELETE WEEKLY --}}
                        <div x-show="type === 'delete-weekly'" x-cloak class="text-center space-y-4">
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <p class="text-slate-700 font-semibold">Delete this weekly issue?</p>
                            <p class="text-slate-500 text-sm">The image will also be deleted. This cannot be undone.
                            </p>
                            <div class="flex gap-3 justify-center">
                                <button type="button" @click="open = false"
                                    class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="submitDelete()" :disabled="loading"
                                    class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                    <span x-show="!loading">Yes, Delete</span>
                                    <span x-show="loading" x-cloak>Deleting…</span>
                                </button>
                            </div>
                        </div>
                        {{-- EDIT CARD TEXT --}}
                        <div x-show="type === 'edit-card-text' && !confirming" x-cloak class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Title 
                                    <span class ="text-red-500">*</span></label>
                                <input type="text" x-model="form.title" maxlength="100"
                                    placeholder="e.g. REGIONAL LMI WEEKLY"
                                     :class="errors.title ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-300 focus:ring-indigo-400'"
                                     class="w-full border  rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Subtitle
                                    <span class="text-red-500">*</span></label>
                                <input type="text" x-model="form.subtitle" maxlength="100"
                                    placeholder="e.g. WEEKLY TRENDS BULLETIN"
                                    :class="errors.subtitle ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-300 focus:ring-indigo-400'"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2  outline-none" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Description
                                    <span class="text-red-500">*</span></label>
                                <textarea x-model="form.description" rows="3" maxlength="500"
                                    placeholder="Brief description shown on the card..."
                                    :class="errors.description ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-300 focus:ring-indigo-400'"
                                    class="w-full border  rounded-lg px-4 py-2.5 text-sm focus:ring-2  outline-none resize-none"></textarea>
                            </div>
                            
                            
                            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
                                <button type="button" @click="open = false"
                                    class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="requestConfirmCardText()"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition">
                                    Review & Save
                                </button>
                            </div>
                        </div>

                        {{-- EDIT CARD TEXT — CONFIRM STEP --}}
                        <template x-if="type === 'edit-card-text' && confirming">
                            <div class="text-center space-y-4">
                                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </div>
                                <p class="text-slate-800 font-bold text-lg">Update the weekly card text?</p>
                                <p class="text-slate-500 text-sm">This will update the title, subtitle and description visible on the weekly section card.</p>
                                <div class="bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 text-left space-y-2 text-sm">
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">Title</span><span class="font-semibold text-slate-800 truncate" x-text="form.title"></span></div>
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">Subtitle</span><span class="text-slate-600 truncate" x-text="form.subtitle"></span></div>
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">Description</span><span class="text-slate-600 line-clamp-2" x-text="form.description"></span></div>
                                </div>
                                <div class="flex gap-3 justify-center pt-1">
                                    <button type="button" @click="confirming = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">← Back</button>
                                    <button type="button" @click="submitCardText()" :disabled="loading"
                                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!loading">Yes, Save Text</span>
                                        <span x-show="loading" x-cloak>Saving…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- EDIT CARD MEDIA --}}
                        <div x-show="type === 'edit-card-media' && !confirming" x-cloak class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Card Image</label>
                                <input type="file" accept="image/*" @change="onImageChange"
                                    :class="errors.image ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-300'"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm outline-none file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                <p class="text-xs text-slate-400 mt-1">Leave blank to keep the existing image.</p>
                                <template x-if="previewUrl">
                                    <img :src="previewUrl" class="mt-2 h-24 rounded-lg object-contain border border-slate-200">
                                </template>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Link URL</label>
                                <input type="text" x-model="form.link_url"
                                    placeholder="https://www.philjobnet.gov.ph"
                                    :class="errors.link_url ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-300 focus:ring-indigo-400'"
                                    @input="errors.link_url = false"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                <p class="text-xs text-slate-400 mt-1">Optional — where the card links to.</p>
                            </div>
                            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
                                <button type="button" @click="open = false"
                                    class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="requestConfirmCardMedia()"
                                    class="px-5 py-2.5 bg-slate-700 hover:bg-slate-800 text-white rounded-lg font-semibold text-sm transition">
                                    Review & Save
                                </button>
                            </div>
                        </div>

                        {{-- EDIT CARD MEDIA — CONFIRM STEP --}}
                        <template x-if="type === 'edit-card-media' && confirming">
                            <div class="text-center space-y-4">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p class="text-slate-800 font-bold text-lg">Update card image &amp; link?</p>
                                <p class="text-slate-500 text-sm">This will replace the current card image and link URL on the weekly section.</p>
                                <div class="bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 text-left space-y-2 text-sm">
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">New Image</span><span class="text-slate-600" x-text="imageFile ? imageFile.name : 'No change (keep existing)'"></span></div>
                                    <div class="flex gap-2"><span class="text-slate-400 w-24 flex-shrink-0">Link URL</span><span class="text-slate-600 truncate" x-text="form.link_url || '(none)'"></span></div>
                                </div>
                                <div class="flex gap-3 justify-center pt-1">
                                    <button type="button" @click="confirming = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">← Back</button>
                                    <button type="button" @click="submitCardMedia()" :disabled="loading"
                                        class="px-6 py-2.5 bg-slate-700 hover:bg-slate-800 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!loading">Yes, Save Changes</span>
                                        <span x-show="loading" x-cloak>Saving…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- WEEKLY PUBLISH --}}
                        <div x-show="type === 'weekly-publish'" x-cloak class="text-center space-y-4">
                            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto">
                                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <p class="text-slate-800 font-bold text-lg">Publish Weekly Issues?</p>
                            <p class="text-slate-500 text-sm">This section will become visible to the public.</p>
                            <div class="flex gap-3 justify-center">
                                <button type="button" @click="open = false"
                                    class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="submitWeeklyPublish()" :disabled="loading"
                                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                    <span x-show="!loading">Yes, Publish</span>
                                    <span x-show="loading" x-cloak>Publishing…</span>
                                </button>
                            </div>
                        </div>

                        {{-- WEEKLY UNPUBLISH --}}
                        <div x-show="type === 'weekly-unpublish'" x-cloak class="text-center space-y-4">
                            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto">
                                <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </div>
                            <p class="text-slate-800 font-bold text-lg">Unpublish Weekly Issues?</p>
                            <p class="text-slate-500 text-sm">This section will be hidden from the public page. You can republish anytime.</p>
                            <div class="flex gap-3 justify-center">
                                <button type="button" @click="open = false"
                                    class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="submitWeeklyUnpublish()" :disabled="loading"
                                    class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                    <span x-show="!loading">Yes, Unpublish</span>
                                    <span x-show="loading" x-cloak>Unpublishing…</span>
                                </button>
                            </div>
                        </div>

                        {{-- WEEKLY REPUBLISH --}}
                        <div x-show="type === 'weekly-republish'" x-cloak class="text-center space-y-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </div>
                            <p class="text-slate-800 font-bold text-lg">Republish Weekly Issues?</p>
                            <p class="text-slate-500 text-sm">All current weekly issues will be pushed live to the public page.</p>
                            <div class="flex gap-3 justify-center">
                                <button type="button" @click="open = false"
                                    class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="submitWeeklyRepublish()" :disabled="loading"
                                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                    <span x-show="!loading">Yes, Republish</span>
                                    <span x-show="loading" x-cloak>Republishing…</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            {{-- ── END WEEKLY MODALS ── --}}

            {{-- Toast container --}}
            <div id="lmiToastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"
                style="min-width:340px;"></div>

        </div>
    </div>


    {{-- ── Image Lightbox (zoom + pan + drag, Alpine-powered) ── --}}
    <div
        x-show="lightbox.open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="lmiCloseLightbox()"
        @keydown.escape.window="lmiCloseLightbox()"
        x-effect="lightbox.open ? document.body.style.overflow = 'hidden' : document.body.style.overflow = ''"
        class="fixed inset-0 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
        style="display:none; z-index:9999;">

        {{-- Close button --}}
        <button @click="lmiCloseLightbox()"
            class="absolute top-4 right-4 text-white bg-white/10 hover:bg-white/25 rounded-full w-9 h-9 flex items-center justify-center transition-colors"
            style="z-index:10000;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Zoom controls --}}
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex items-center gap-2 bg-black/50 backdrop-blur-sm rounded-full px-3 py-1.5"
             style="z-index:10000;">
            <button @click="lightbox.scale = Math.max(0.3, lightbox.scale - 0.25)"
                    class="text-white w-7 h-7 flex items-center justify-center hover:text-blue-300 transition-colors text-lg font-bold">−</button>
            <span class="text-white text-xs font-semibold w-10 text-center" x-text="Math.round(lightbox.scale * 100) + '%'"></span>
            <button @click="lightbox.scale = Math.min(4, lightbox.scale + 0.25)"
                    class="text-white w-7 h-7 flex items-center justify-center hover:text-blue-300 transition-colors text-lg font-bold">+</button>
            <span class="text-white/40 text-xs mx-1">|</span>
            <button @click="lightbox.scale = 0.3; lightbox.panX = 0; lightbox.panY = 0;"
                    class="text-white text-xs hover:text-blue-300 transition-colors font-medium">Reset</button>
        </div>

        {{-- Caption --}}
        <p x-show="lightbox.caption"
           x-text="lightbox.caption"
           class="absolute bottom-14 left-1/2 -translate-x-1/2 text-white/60 text-xs whitespace-nowrap"
           style="z-index:10000;"></p>

        {{-- Drag-to-pan viewport --}}
        <div x-ref="lbViewport"
             @click.stop
             @wheel.prevent="
                 lightbox.scale = Math.min(4, Math.max(0.3, lightbox.scale + ($event.deltaY < 0 ? 0.15 : -0.15)));
                 if (lightbox.scale <= 0.3) { lightbox.panX = 0; lightbox.panY = 0; }
                 else {
                     let vw = $refs.lbViewport.clientWidth, vh = $refs.lbViewport.clientHeight;
                     let iw = $refs.lbImg.naturalWidth  * lightbox.scale, ih = $refs.lbImg.naturalHeight * lightbox.scale;
                     let maxX = Math.max(0, (iw - vw) / 2), maxY = Math.max(0, (ih - vh) / 2);
                     lightbox.panX = Math.min(maxX, Math.max(-maxX, lightbox.panX));
                     lightbox.panY = Math.min(maxY, Math.max(-maxY, lightbox.panY));
                 }
             "
             @mousedown.prevent="lightbox.drag = true; lightbox.dx = $event.clientX - lightbox.panX; lightbox.dy = $event.clientY - lightbox.panY;"
             @mousemove.prevent="
                 if (lightbox.drag) {
                     let vw = $refs.lbViewport.clientWidth, vh = $refs.lbViewport.clientHeight;
                     let iw = $refs.lbImg.naturalWidth * lightbox.scale, ih = $refs.lbImg.naturalHeight * lightbox.scale;
                     let maxX = Math.max(0, (iw - vw) / 2), maxY = Math.max(0, (ih - vh) / 2);
                     lightbox.panX = Math.min(maxX, Math.max(-maxX, $event.clientX - lightbox.dx));
                     lightbox.panY = Math.min(maxY, Math.max(-maxY, $event.clientY - lightbox.dy));
                 }
             "
             @mouseup="lightbox.drag = false"
             @mouseleave="lightbox.drag = false"
             @touchstart.prevent="
                 if ($event.touches.length === 1) {
                     lightbox.drag = true;
                     lightbox.dx = $event.touches[0].clientX - lightbox.panX;
                     lightbox.dy = $event.touches[0].clientY - lightbox.panY;
                 }
             "
             @touchmove.prevent="
                 if (lightbox.drag && $event.touches.length === 1) {
                     let vw = $refs.lbViewport.clientWidth, vh = $refs.lbViewport.clientHeight;
                     let iw = $refs.lbImg.naturalWidth * lightbox.scale, ih = $refs.lbImg.naturalHeight * lightbox.scale;
                     let maxX = Math.max(0, (iw - vw) / 2), maxY = Math.max(0, (ih - vh) / 2);
                     lightbox.panX = Math.min(maxX, Math.max(-maxX, $event.touches[0].clientX - lightbox.dx));
                     lightbox.panY = Math.min(maxY, Math.max(-maxY, $event.touches[0].clientY - lightbox.dy));
                 }
             "
             @touchend="lightbox.drag = false"
             :style="lightbox.drag ? 'cursor:grabbing' : 'cursor:grab'"
             style="width:90vw; height:85vh; overflow:hidden; display:flex; align-items:center; justify-content:center; position:relative;">
            <img x-ref="lbImg"
                 :src="lightbox.src"
                 :style="`position:absolute; top:50%; left:50%;
                     transform: translate(calc(-50% + ${lightbox.panX}px), calc(-50% + ${lightbox.panY}px)) scale(${lightbox.scale});
                     transform-origin: center;
                     transition: ${lightbox.drag ? 'none' : 'transform 0.2s ease'};`"
                 class="rounded-xl shadow-2xl select-none"
                 style="width:auto; height:auto; max-width:none; max-height:none; pointer-events:none;"
                 draggable="false">
        </div>
    </div>


</body>

</html>