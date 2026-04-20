<!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
        @vite('resources/css/app.css')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
        {{-- Load JS before Alpine so licensureChartData() is on window when Alpine boots --}}
        @vite('resources/js/public/supply-side.js')

        {{-- Alpine must come AFTER --}}
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>
        <title>Labor Supply Data</title>
        <style>
            /* Custom scrollbar for better UX */
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
                border-radius: 999px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 999px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
            /* Quill rendered output styles (for x-html display on public page) */
            .ql-align-justify { text-align: justify; }
            .ql-align-center  { text-align: center; }
            .ql-align-right   { text-align: right; }
            .ql-align-left    { text-align: left; }
            .ql-size-8pt  { font-size: 8pt; }
            .ql-size-9pt  { font-size: 9pt; }
            .ql-size-10pt { font-size: 10pt; }
            .ql-size-11pt { font-size: 11pt; }
            .ql-size-12pt { font-size: 12pt; }
            .ql-size-14pt { font-size: 14pt; }
            .ql-size-16pt { font-size: 16pt; }
            .ql-size-18pt { font-size: 18pt; }
            .ql-size-20pt { font-size: 20pt; }
            .ql-size-22pt { font-size: 22pt; }
            .ql-size-24pt { font-size: 24pt; }
            .ql-size-28pt { font-size: 28pt; }
            .ql-size-36pt { font-size: 36pt; }
            .ql-size-48pt { font-size: 48pt; }
            .ql-size-72pt { font-size: 72pt; }

            /* ── Bullet & list rendering fix ──
               Tailwind resets all list styles to none. These rules restore them
               for any element that renders Quill HTML output via x-html.
               Applied broadly so it covers all current and future Quill editors. */
            [x-html] ul, .prose ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin: 0.5rem 0 !important; }
            [x-html] ol, .prose ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin: 0.5rem 0 !important; }
            [x-html] li, .prose li { display: list-item !important; margin: 0.25rem 0 !important; }
            [x-html] ul ul, .prose ul ul { list-style-type: circle !important; }
            [x-html] ul ul ul, .prose ul ul ul { list-style-type: square !important; }
            [x-html] .ql-indent-1 { padding-left: 3rem !important; }
            [x-html] .ql-indent-2 { padding-left: 4.5rem !important; }
            [x-html] .ql-indent-3 { padding-left: 6rem !important; }
            [x-html] blockquote { border-left: 4px solid #cbd5e1; padding-left: 1rem; color: #64748b; margin: 0.5rem 0; }
            [x-html] h1 { font-size: 1.5rem; font-weight: 700; margin: 0.75rem 0; }
            [x-html] h2 { font-size: 1.25rem; font-weight: 700; margin: 0.5rem 0; }
            [x-html] h3 { font-size: 1.1rem; font-weight: 600; margin: 0.5rem 0; }

            /* ── Hero: smaller title on small phones ── */
            @media (max-width: 480px) {
                .hero-title-supply { font-size: 1.6rem !important; line-height: 1.2 !important; }
            }

            /* ── KPI cards: scale down huge number on small phones ── */
            @media (max-width: 400px) {
                .kpi-number { font-size: 2.5rem !important; }
            }

            /* ── KPI cards: reduce padding on mobile ── */
            @media (max-width: 640px) {
                .kpi-card { padding: 1.25rem !important; }
            }

            /* ── Filter bars: wrap on mobile ── */
            .filter-bar-wrap {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                align-items: center;
            }

            /* ── Pie chart inline layout: stack vertically on mobile ── */
            .pie-inline-layout {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 2.5rem;
                flex-wrap: wrap;
            }
            @media (max-width: 767px) {
                .pie-inline-layout {
                    flex-direction: column;
                    align-items: center;
                }
                /* Hide side legends on mobile — too cramped */
                .pie-legend-col {
                    display: none !important;
                }
                .pie-canvas-wrap canvas {
                    width: min(240px, 75vw) !important;
                    height: min(240px, 75vw) !important;
                }
            }

            /* ── Mobile mini legend below pie chart ── */
            .pie-mini-legend {
                display: none;
            }
            @media (max-width: 767px) {
                .pie-mini-legend {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 0.5rem 0.75rem;
                    padding: 0.75rem 0.5rem 0.25rem;
                    width: 100%;
                }
                .pie-mini-legend-item {
                    display: flex;
                    align-items: flex-start;
                    gap: 0.4rem;
                }
                .pie-mini-legend-dot {
                    width: 8px;
                    height: 8px;
                    border-radius: 50%;
                    flex-shrink: 0;
                    margin-top: 3px;
                }
                .pie-mini-legend-name {
                    font-size: 0.72rem;
                    color: #475569;
                    font-weight: 500;
                    line-height: 1.3;
                }
                .pie-mini-legend-pct {
                    font-size: 0.7rem;
                    color: #94a3b8;
                    font-weight: 600;
                }
                .pie-mini-legend-more {
                    grid-column: 1 / -1;
                    text-align: center;
                    font-size: 0.7rem;
                    color: #94a3b8;
                    padding-top: 0.35rem;
                }
            }

            /* ── Pie modal: responsive layout ── */
            .pie-modal-body {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 1.5rem;
                padding: 1.5rem;
                overflow-y: auto;
                flex: 1;
                min-height: 0;
            }
            @media (max-width: 767px) {
                .pie-modal-body {
                    flex-direction: column;
                    align-items: center;
                    padding: 0.75rem;
                    gap: 0;
                    overflow-y: auto;
                }
                .pie-modal-legend-side { display: none !important; }
                .pie-modal-legend-bottom { display: grid !important; }
                .pie-modal-canvas {
                    width: 100% !important;
                    height: auto !important;
                    flex-shrink: 0;
                }
                .pie-modal-canvas canvas {
                    width: min(300px, 80vw) !important;
                    height: min(300px, 80vw) !important;
                }
            }
            @media (min-width: 768px) {
                .pie-modal-legend-bottom { display: none !important; }
            }

            /* ── Modal headers: stack on mobile ── */
            @media (max-width: 640px) {
                .modal-header-inner {
                    flex-direction: column !important;
                    align-items: flex-start !important;
                    gap: 0.75rem;
                }
                .modal-header-inner .modal-close-group { align-self: flex-end; }
                .modal-title { font-size: 1rem !important; }
                .modal-panel { padding: 0 !important; border-radius: 0.75rem !important; }
            }

            /* ── Enrollment trend modal header legend: hide on very small screens ── */
            @media (max-width: 480px) {
                .trend-modal-legend { display: none !important; }
            }

            /* ── Enrollment Trend: mobile = inline bar table, desktop = canvas chart ── */
            .enrollment-trend-mobile { display: none; }
            .enrollment-trend-desktop { display: block; }
            @media (max-width: 767px) {
                .enrollment-trend-mobile  { display: block; }
                .enrollment-trend-desktop { display: none; }
            }

            /* ── Mobile trend bar: slide-in from left animation ── */
            @keyframes slideInBar {
                from { width: 0 !important; opacity: 0.4; }
                to   { opacity: 1; }
            }
            .trend-bar-animate {
                animation: slideInBar 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
            }
            /* Row fade-up entrance */
            @keyframes fadeUpRow {
                from { opacity: 0; transform: translateY(6px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            .trend-row-animate {
                animation: fadeUpRow 0.4s ease both;
            }

            /* ── Discipline Enrollment: mobile = inline bar table, desktop = canvas ── */
            .discipline-enrollment-mobile  { display: none; }
            .discipline-enrollment-desktop { display: block; }
            @media (max-width: 767px) {
                .discipline-enrollment-mobile  { display: block; }
                .discipline-enrollment-desktop { display: none; }
            }

            /* ── Licensure: mobile = inline list, desktop = canvas chart ── */
            .licensure-mobile  { display: none; }
            .licensure-desktop { display: block; }
            @media (max-width: 767px) {
                .licensure-mobile  { display: block; }
                .licensure-desktop { display: none; }
            }

        </style>
    </head>

    <body x-data="licensureChartData()" class="bg-slate-100 min-h-screen">
        @include('partials.navbar')
        
        <!-- Hero Image Section -->
        <div class="relative w-full h-[380px] sm:h-[420px] md:h-[700px] lg:h-[900px] overflow-hidden">
            <div class="absolute inset-0">
                <img src="{{ asset('images/ARD.jpg') }}" alt="Assistant Regional Director"
                    class="w-full h-full object-cover object-top">
                <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/40 to-slate-100"></div>
            </div>
            
            <!-- Hero Content -->
            <div class="relative z-10 h-full flex items-center justify-center px-4 pb-16 sm:pb-0">
            <div class="text-center text-white pointer-events-none">
                <h1 class="text-white font-black leading-tight tracking-tight hero-title-supply"
                    style="font-size: clamp(1.25rem, 4vw, 3.5rem); text-shadow: 0 2px 16px rgba(0,0,0,1), 0 0 40px rgba(0,0,0,0.7);">
                    Education to Employment Pipeline
                </h1>
                <p class="text-slate-200 font-medium mt-2"
                    style="font-size: clamp(0.75rem, 1.5vw, 1.125rem); text-shadow: 0 1px 8px rgba(0,0,0,1);">
                    Regional Labor Market Information & Trends
                </p>
            </div>
        </div>
            
            <!-- Scroll Indicator -->
            <div class="absolute bottom-6 sm:bottom-16 left-1/2 transform -translate-x-1/2 z-20 scroll-indicator animate-bounce">
                <a href="#kpi-section"
                   class="flex flex-col items-center cursor-pointer group"
                   @click.prevent="() => {
                       const element = document.getElementById('kpi-section');
                       if (element) {
                           element.scrollIntoView({ 
                               behavior: 'smooth', 
                               block: 'start' 
                           });
                       }
                   }">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white group-hover:text-blue-300 transition-colors" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" 
                              stroke-linejoin="round" 
                              stroke-width="2"
                              d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                    <p class="text-white text-xs sm:text-sm mt-1 sm:mt-2 font-medium group-hover:text-blue-300 transition-colors">
                        Scroll to explore
                    </p>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="w-full mt-10 relative z-30">
            <div class="px-2 py-3 sm:p-5">
                <div class="max-w-screen-xl mx-auto px-0 sm:px-4 space-y-6">
                    <!-- Dashboard Overview Header -->
                   

                    <!-- Data Scope Note -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-yellow-800">Data Scope Note: Higher Education Only</h3>
                                <p class="text-sm text-yellow-700 mt-1">
                                    This analysis currently reflects student data from <strong>Higher Education Institutions (HEIs)</strong> under CHED. It <strong>does not</strong> yet include workforce supply from <strong>Technical-Vocational (TESDA)</strong> or <strong>Senior High School (DepEd)</strong> graduates. Future dashboard updates aim to integrate these additional data sources to provide a comprehensive view of the total regional labor supply.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Key Metrics Cards - Full Width 2-Card Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4" id="kpi-section">

                        <!-- Card 1: Total Enrollees -->
                        <div class="group bg-white rounded-2xl p-5 sm:p-8 border-l-4 border-blue-500 shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5 relative overflow-hidden kpi-card">
                            <div x-show="loadingGraduationRate || loadingLatestEnrollment" class="absolute inset-0 bg-white/80 rounded-2xl flex items-center justify-center z-10">
                                <div class="animate-spin rounded-full h-10 w-10 border-2 border-slate-200 border-t-slate-500"></div>
                            </div>
                            <!-- Background decoration -->
                            <div class="absolute -right-6 -top-6 w-36 h-36 bg-blue-50 rounded-full opacity-70"></div>
                            <div class="absolute -right-2 -bottom-8 w-24 h-24 bg-blue-100 rounded-full opacity-40"></div>
                            <div class="relative">
                                <div class="flex items-start justify-between mb-5">
                                    <div>
                                        <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-1">Total Enrollees</p>
                                        <p class="text-xs text-slate-400">Latest enrollment data</p>
                                    </div>
                                    <div class="bg-blue-100 p-3.5 rounded-full flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                                    <svg class="w-8 h-8 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                </div>
                                <p class="text-4xl sm:text-6xl font-black text-slate-800 mb-3 tracking-tight kpi-number" x-text="formatNumber(latestEnrollmentTotal || 0)">0</p>
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                                    <p class="text-sm text-slate-500 font-medium" x-text="latestEnrollmentYear ?? 'No data'">No data</p>
                                </div>
                            </div>
                            <!-- Tooltip -->
                            <div class="absolute top-5 right-5" x-data="{ showTooltip: false }">
                                <button @mouseenter="showTooltip = true" @mouseleave="showTooltip = false" class="text-slate-300 hover:text-slate-500 transition">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                </button>
                                <div x-show="showTooltip" x-transition class="absolute right-0 top-6 w-64 p-3 bg-slate-800 text-white text-xs rounded-lg shadow-xl z-50">
                                    Total number of students currently enrolled across all disciplines for the latest available academic year.
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Projected Graduates -->
                        <div class="group bg-white rounded-2xl p-5 sm:p-8 border-l-4 border-violet-500 shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5 relative overflow-hidden kpi-card">
                            <div x-show="loadingGraduationRate" class="absolute inset-0 bg-white/80 rounded-2xl flex items-center justify-center z-10">
                                <div class="animate-spin rounded-full h-10 w-10 border-2 border-slate-200 border-t-slate-500"></div>
                            </div>
                            <div class="absolute -right-6 -top-6 w-36 h-36 bg-violet-50 rounded-full opacity-70"></div>
                            <div class="absolute -right-2 -bottom-8 w-24 h-24 bg-violet-100 rounded-full opacity-40"></div>
                            <div class="relative">
                                <div class="flex items-start justify-between mb-5">
                                    <div>
                                        <p class="text-xs font-bold text-violet-500 uppercase tracking-widest mb-1">Projected Graduates</p>
                                        <!-- Year + rate badge (replaces "Based on graduation rate") -->
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-xs font-semibold text-slate-500"
                                                  x-text="graduationRateData.graduate_year ?? '—'"></span>
                                            <span class="text-xs font-bold text-violet-600 bg-violet-100 px-2.5 py-1 rounded-full"
                                                  x-show="graduationRateData.graduation_rate"
                                                  x-text="`${parseFloat(graduationRateData.graduation_rate).toFixed(2)}% rate`"></span>
                                        </div>
                                    </div>
                                    <div class="bg-violet-100 p-3.5 rounded-full flex items-center justify-center group-hover:bg-violet-600 transition-colors">
                                        <svg class="w-8 h-8 text-violet-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-4xl sm:text-6xl font-black text-slate-800 mb-3 tracking-tight kpi-number" x-text="formatNumber(graduationRateData.projected_graduates || 0)">0</p>

                                <!-- Description from graduation rate record -->
                                <template x-if="graduationRateData.description">
                                    <div class="mt-4 pt-4 border-t border-slate-100">
                                        <div class="text-xs text-justify text-slate-500 leading-relaxed prose prose-xs max-w-none
                                                    [&_strong]:text-slate-700 [&_em]:text-slate-600 [&_mark]:bg-yellow-100 [&_mark]:px-0.5 [&_mark]:rounded"
                                             x-html="graduationRateData.description"></div>
                                    </div>
                                </template>
                            </div>
                            <div class="absolute top-5 right-5" x-data="{ showTooltip: false }">
                                <button @mouseenter="showTooltip = true" @mouseleave="showTooltip = false" class="text-slate-300 hover:text-slate-500 transition">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                </button>
                                <div x-show="showTooltip" x-transition class="absolute right-0 top-6 w-64 p-3 bg-slate-800 text-white text-xs rounded-lg shadow-xl z-50">
                                    Based on <span class="font-semibold" x-text="graduationRateData.enrollment_year ?? '—'"></span> enrollment data <span class="italic">(4 years ago)</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Executive Analysis and Pie Chart Side by Side - COLLAPSIBLE -->
                    <div class="mt-6 rounded-2xl overflow-hidden shadow-lg" x-data="{ enrollmentOverviewExpanded: true }">

                        <!-- Dark Collapsible Header -->
                        <button
                            @click="enrollmentOverviewExpanded = !enrollmentOverviewExpanded"
                            class="w-full bg-slate-800 hover:bg-slate-700 transition-colors duration-200 px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="bg-white/10 p-2.5 rounded-xl">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <h3 class="text-base font-bold text-white">
                                        Enrollment Overview — <span x-text="selectedEnrollmentProvince"></span>
                                    </h3>
                                    <p class="text-xs text-slate-400">Discipline market share &amp; executive supply analysis
                                        <span x-show="!enrollmentOverviewExpanded" class="text-slate-500 ml-2">• Click to expand</span>
                                    </p>
                                </div>
                            </div>
                            <svg
                                class="w-5 h-5 text-slate-400 transition-transform duration-300"
                                :class="{ 'rotate-180': enrollmentOverviewExpanded }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Collapsible Content -->
                        <div
                            x-show="enrollmentOverviewExpanded"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="bg-slate-50 border border-t-0 border-slate-200 rounded-b-2xl overflow-hidden">

                        <!-- Panel Filter Bar -->
                        <div class="flex flex-wrap items-center justify-end px-4 sm:px-6 py-3 bg-white border-b border-slate-200 gap-2 sm:gap-3">
                                <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl px-3 py-1.5 shadow-sm">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="text-xs text-slate-500 font-medium hidden sm:inline">Province:</span>
                                    <select 
                                        x-model="selectedEnrollmentProvince" 
                                        @change="loadEnrollmentYearsForProvince(selectedEnrollmentProvince).then(() => loadEnrollmentData())"
                                        class="text-sm font-bold text-slate-800 bg-transparent focus:outline-none cursor-pointer">
                                        <template x-for="province in availableEnrollmentProvinces" :key="province">
                                            <option :value="province" x-text="province"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl px-3 py-1.5 shadow-sm">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-xs text-slate-500 font-medium hidden sm:inline">Year:</span>
                                    <select 
                                        x-model="selectedEnrollmentYear" 
                                        @change="loadEnrollmentData()"
                                        class="text-sm font-bold text-slate-800 bg-transparent focus:outline-none cursor-pointer">
                                        <template x-for="year in availableEnrollmentYears" :key="year">
                                            <option :value="year" x-text="year"></option>
                                        </template>
                                    </select>
                                </div>
                        </div>

                        <!-- Cards Row -->
                        <div class="flex flex-col gap-6 p-6">

                        <!-- Executive Analysis: Supply Side -->
                        <div class="w-full bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                        <div class="flex flex-col p-6">
                            <div class="flex items-start gap-3 mb-4 flex-shrink-0">
                                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-2.5 rounded-xl shadow-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800">EXECUTIVE ANALYSIS</h3>
                                </div>
                            </div>
                            
                            <!-- Loading State -->
                            <div x-show="loadingExecutiveAnalysis" class="flex items-center justify-center py-8">
                                <div class="animate-spin rounded-full h-6 w-6 border-2 border-slate-200 border-t-slate-500"></div>
                            </div>

                            <!-- Dynamic Analysis Text — card scrolls, text has full width -->
                            <div x-show="!loadingExecutiveAnalysis" 
                                 class="flex-1 text-sm text-slate-700 prose prose-sm max-w-none overflow-y-auto"
                                 style="max-height: 320px;"
                                 x-html="executiveAnalysisText">
                            </div>
                        </div>
                        </div>

                        <!-- Discipline Market Share Pie Chart -->
                        <div class="w-full bg-slate-50 rounded-2xl shadow-xl border border-slate-200 p-6 relative overflow-hidden">
                            <div x-show="loadingPieChart" class="absolute inset-0 bg-white/75 flex items-center justify-center z-10 rounded-2xl backdrop-blur-sm">
                                <div class="animate-spin rounded-full h-10 w-10 border-2 border-slate-200 border-t-slate-500"></div>
                            </div>
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
                                <!-- Expand Button -->
                                <button
                                    @click="pieModalOpen = true"
                                    class="flex-shrink-0 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                    </svg>
                                    Expand
                                </button>
                            </div>

                            <!-- Pie Chart with Side Legends -->
                            <div class="pie-inline-layout mt-6">
                                <!-- LEFT LEGEND (Top Half - Highest Values) -->
                                <div class="w-56 pie-legend-col shrink-0 space-y-3">
                                    <template x-for="(item, index) in (() => {
                                        const entries = Object.entries(disciplineShares || {});
                                        if (entries.length === 0) return [];
                                        return entries
                                            .sort((a, b) => parseFloat(b[1]) - parseFloat(a[1]))
                                            .slice(0, Math.ceil(entries.length / 2))
                                            .map((e, i) => [...e, i]);
                                    })()" :key="item[0]">
                                        <div class="flex items-start gap-2 min-w-0">
                                            <div class="w-3 h-3 rounded-full flex-shrink-0 mt-1" 
                                                 :style="`background-color: ${(() => {
                                                    return getDeepBlueForDiscipline(item[0]);
                                                 })()}`"></div>
                                            <div class="min-w-0">
                                                <div class="text-slate-700 font-medium text-sm leading-snug" x-text="formatDisciplineName(item[0])"></div>
                                                <div class="text-slate-500 text-xs font-semibold" x-text="parseFloat(item[1]).toFixed(1) + '%'"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- CENTER PIE CHART -->
                                <div class="pie-canvas-wrap flex-shrink-0 flex items-center justify-center">
                                    <canvas id="disciplineMarketShareChart" width="400" height="350"></canvas>
                                </div>

                                <!-- RIGHT LEGEND (Bottom Half - Lower Values) -->
                                <div class="w-56 pie-legend-col shrink-0 space-y-3">
                                    <template x-for="(item, index) in (() => {
                                        const entries = Object.entries(disciplineShares || {});
                                        if (entries.length === 0) return [];
                                        const half = Math.ceil(entries.length / 2);
                                        return entries
                                            .sort((a, b) => parseFloat(b[1]) - parseFloat(a[1]))
                                            .slice(half)
                                            .map((e, i) => [...e, half + i]);
                                    })()" :key="item[0]">
                                        <div class="flex items-start gap-2 min-w-0">
                                            <div class="w-3 h-3 rounded-full flex-shrink-0 mt-1" 
                                                 :style="`background-color: ${(() => {
                                                    return getDeepBlueForDiscipline(item[0]);
                                                 })()}`"></div>
                                            <div class="min-w-0">
                                                <div class="text-slate-700 font-medium text-sm leading-snug" x-text="formatDisciplineName(item[0])"></div>
                                                <div class="text-slate-500 text-xs font-semibold" x-text="parseFloat(item[1]).toFixed(1) + '%'"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div><!-- end pie chart card -->

                        <!-- Mobile mini legend: top 5 disciplines -->
                        <div class="pie-mini-legend">
                            <template x-for="(item, index) in (() => {
                                const entries = Object.entries(disciplineShares || {});
                                return entries
                                    .sort((a, b) => parseFloat(b[1]) - parseFloat(a[1]))
                                    .slice(0, 6)
                                    .map((e, i) => [...e, i]);
                            })()" :key="item[0]">
                                <div class="pie-mini-legend-item">
                                    <div class="pie-mini-legend-dot"
                                         :style="`background-color: ${getDeepBlueForDiscipline(item[0])}`"></div>
                                    <div>
                                        <div class="pie-mini-legend-name" x-text="formatDisciplineName(item[0])"></div>
                                        <div class="pie-mini-legend-pct" x-text="parseFloat(item[1]).toFixed(1) + '%'"></div>
                                    </div>
                                </div>
                            </template>
                            <div class="pie-mini-legend-more">
                                Click <strong>Expand for more</strong>
                            </div>
                        </div>

                        <!-- Pie Chart Modal -->
                        <template x-teleport="body">
                            <div
                                x-show="pieModalOpen"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="fixed inset-0 z-[9999] bg-black/60 flex items-end sm:items-center justify-center p-0 sm:p-4"
                                style="display:none;">
                                <div class="bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl w-full sm:max-w-[80vw] flex flex-col overflow-hidden" style="height:92vh; max-height:92vh;" @click.stop>
                                    <!-- Header -->
                                    <div class="flex flex-wrap items-center justify-between gap-3 px-4 sm:px-6 py-4 border-b border-slate-200 flex-shrink-0">
                                        <div>
                                            <h2 class="text-base sm:text-xl font-bold text-slate-800">Distribution of Enrollees — Expanded View</h2>
                                            <p class="text-sm text-slate-500 mt-0.5"><span x-text="selectedEnrollmentYear"></span> &bull; <span x-text="selectedEnrollmentProvince"></span></p>
                                        </div>
                                        <button @click="pieModalOpen = false"
                                            class="px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white font-semibold rounded-lg transition-colors flex items-center gap-2 text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Close
                                        </button>
                                    </div>
                                    <!-- Body: mobile = chart top fixed + legend scrollable | desktop = side legends + pie -->
                                    <div class="flex-1 min-h-0 overflow-hidden flex flex-col sm:flex-row">

                                        <!-- MOBILE: chart fixed, legend scrollable below — hidden on sm+ -->
                                        <div class="flex flex-col w-full h-full sm:hidden">
                                            <div class="flex-shrink-0 flex justify-center items-center py-3 border-b border-slate-100">
                                                <canvas id="disciplineMarketShareChartModalMobile" width="300" height="300" style="width:300px;height:300px;display:block;"></canvas>
                                            </div>
                                            <div class="flex-1 min-h-0 overflow-y-auto px-4 py-3 custom-scrollbar">
                                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">All Disciplines</p>
                                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem 0.75rem;">
                                                    <template x-for="(item) in (() => {
                                                        const e = Object.entries(disciplineShares || {});
                                                        return e.sort((a,b) => parseFloat(b[1]) - parseFloat(a[1])).map((x,i)=>[...x,i]);
                                                    })()" :key="'mob-'+item[0]">
                                                        <div class="flex items-start gap-1.5 min-w-0">
                                                            <div class="w-2.5 h-2.5 rounded-full flex-shrink-0 mt-0.5"
                                                                :style="`background-color:${getDeepBlueForDiscipline(item[0])}`">
                                                            </div>
                                                            <div class="min-w-0">
                                                                <p class="text-xs font-medium text-slate-700 leading-tight" x-text="formatDisciplineName(item[0])"></p>
                                                                <p class="text-xs text-slate-400" x-text="parseFloat(item[1]).toFixed(1)+'%'"></p>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- DESKTOP: left legend | pie | right legend — hidden on mobile -->
                                        <div class="hidden sm:flex flex-1 items-center justify-center gap-6 p-8 overflow-y-auto custom-scrollbar">
                                            <div class="w-72 shrink-0 space-y-3">
                                                <template x-for="(item) in (() => {
                                                    const e = Object.entries(disciplineShares || {});
                                                    return e.sort((a,b)=>parseFloat(b[1])-parseFloat(a[1])).slice(0, Math.ceil(e.length/2)).map((x,i)=>[...x,i]);
                                                })()" :key="'dl-'+item[0]">
                                                    <div class="flex items-start gap-2 min-w-0">
                                                        <div class="w-3 h-3 rounded-full flex-shrink-0 mt-0.5"
                                                            :style="`background-color:${getDeepBlueForDiscipline(item[0])}`">
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="text-xs font-semibold text-slate-800 leading-snug" x-text="formatDisciplineName(item[0])"></p>
                                                            <p class="text-xs text-slate-400" x-text="parseFloat(item[1]).toFixed(1)+'%'"></p>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="flex-shrink-0 flex items-center justify-center" style="width:420px;height:420px;">
                                                <canvas id="disciplineMarketShareChartModal" width="420" height="420"></canvas>
                                            </div>
                                            <div class="w-56 shrink-0 space-y-3">
                                                <template x-for="(item) in (() => {
                                                    const e = Object.entries(disciplineShares || {});
                                                    const half = Math.ceil(e.length/2);
                                                    return e.sort((a,b)=>parseFloat(b[1])-parseFloat(a[1])).slice(half).map((x,i)=>[...x,half+i]);
                                                })()" :key="'dr-'+item[0]">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <div class="w-3 h-3 rounded-full flex-shrink-0"
                                                            :style="`background-color:${getDeepBlueForDiscipline(item[0])}`">
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="text-xs font-semibold text-slate-800 leading-tight" x-text="formatDisciplineName(item[0])"></p>
                                                            <p class="text-xs text-slate-400" x-text="parseFloat(item[1]).toFixed(1)+'%'"></p>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </template>

                        </div><!-- end Cards Row -->

                        </div><!-- end Collapsible Content -->
                    </div><!-- end Enrollment Overview Panel -->

                    <!-- ─── Section Divider ─────────────────────────────────── -->
                    <div class="relative flex items-center gap-4 my-2">
                        <div class="flex-1 h-px bg-slate-300"></div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest px-2 whitespace-nowrap">Data Charts</span>
                        <div class="flex-1 h-px bg-slate-300"></div>
                    </div>

                    <!-- Enrollment Trend in Davao Region Chart - COLLAPSIBLE -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-6" x-data="{ enrollmentTrendExpanded: false }">
                        <!-- Collapsible Header -->
                        <button 
                            @click="enrollmentTrendExpanded = !enrollmentTrendExpanded; if(enrollmentTrendExpanded) { setTimeout(() => { updateTrendChart(); }, 100); }"
                            class="w-full bg-slate-800 hover:bg-slate-700 transition-colors duration-200 px-6 py-5 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="bg-white/10 p-2.5 rounded-xl">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <h3 class="text-base font-bold text-white">
                                        Enrollment Trend — <span x-text="selectedTrendProvince"></span>
                                    </h3>
                                    <p class="text-xs text-slate-400">
                                        Comparing Public vs. Private Sector
                                        <span x-show="!enrollmentTrendExpanded" class="text-slate-500 ml-2">• Click to expand</span>
                                    </p>
                                </div>
                            </div>
                            <!-- Expand/Collapse Icon -->
                            <svg 
                                class="w-5 h-5 text-slate-400 transition-transform duration-300" 
                                :class="{ 'rotate-180': enrollmentTrendExpanded }"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Collapsible Content -->
                        <div 
                            x-show="enrollmentTrendExpanded" 
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">
                            
                            <!-- Filters Bar -->
                            <div class="bg-gradient-to-r from-slate-50 to-white border-b border-slate-200 px-4 sm:px-6 py-3 flex flex-wrap items-center justify-between gap-2 sm:gap-3">
                                <!-- Left: dynamic label -->
                                <p class="text-xs text-slate-500 w-full sm:w-auto">
                                    Student enrollment public and private - <span x-text="selectedTrendYear" class="font-semibold text-blue-600"></span>
                                    <span class="text-slate-400"> • </span>
                                    <span x-text="selectedTrendProvince" class="font-semibold text-green-600"></span>
                                </p>
                                <!-- Right: filters + expand grouped together -->
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl px-3 py-1.5 shadow-sm">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span class="text-xs text-slate-500 font-medium hidden sm:inline">Province:</span>
                                        <select 
                                            x-model="selectedTrendProvince"
                                            @change="loadTrendYearsForProvince(selectedTrendProvince).then(() => buildEnrollmentTrendChart())"
                                            class="text-sm font-bold text-slate-800 bg-transparent focus:outline-none cursor-pointer">
                                            <template x-for="province in availableTrendProvinces" :key="province">
                                                <option :value="province" x-text="province"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl px-3 py-1.5 shadow-sm">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="text-xs text-slate-500 font-medium hidden sm:inline">Year:</span>
                                        <select 
                                            x-model="selectedTrendYear"
                                            @change="updateTrendChart()"
                                            class="text-sm font-bold text-slate-800 bg-transparent focus:outline-none cursor-pointer">
                                            <template x-for="year in availableTrendYears" :key="year">
                                                <option :value="year" x-text="year"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <!-- Expand Button -->
                                    <button
                                        @click="enrollmentTrendModalOpen = true"
                                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                        </svg>
                                        Expand
                                    </button>
                                </div>
                            </div>

                            <!-- Stats Cards (separate) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4">
                                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg p-3 border border-blue-200">
                                    <p class="text-xs font-semibold text-blue-700 mb-1">Total Public Schools</p>
                                    <p class="text-2xl font-bold text-blue-900" 
                                       x-text="enrollmentTrendTotals.public ? enrollmentTrendTotals.public.toLocaleString() : 'No Data Available'">
                                    </p>
                                </div>
                                <div class="bg-gradient-to-br from-sky-50 to-indigo-50 rounded-lg p-3 border border-sky-200">
                                    <p class="text-xs font-semibold text-sky-700 mb-1">Total Private Schools</p>
                                    <p class="text-2xl font-bold text-sky-900"
                                       x-text="enrollmentTrendTotals.private ? enrollmentTrendTotals.private.toLocaleString() : 'No Data Available'">
                                    </p>
                                </div>
                            </div>

                            <!-- ═══════════════════════════════════════════════════════ -->
                            <!-- MOBILE VIEW: Inline stacked bar table (≤ 767px)      -->
                            <!-- ═══════════════════════════════════════════════════════ -->
                            <div class="enrollment-trend-mobile px-4 pb-6 pt-2">
                                <div x-show="loadingEnrollmentTrend" class="flex items-center justify-center py-10">
                                    <div class="animate-spin rounded-full h-8 w-8 border-2 border-slate-200 border-t-slate-500"></div>
                                </div>
                                <div x-show="!loadingEnrollmentTrend && trendTableData.length === 0" class="text-center py-8 text-slate-400 text-sm italic">
                                    No enrollment data available for this selection.
                                </div>
                                <div x-show="!loadingEnrollmentTrend && trendTableData.length > 0" class="space-y-0">
                                    <!-- Legend -->
                                    <div class="flex items-center gap-4 mb-3">
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-3 h-3 rounded-sm bg-blue-600"></div>
                                            <span class="text-xs font-semibold text-slate-600">Public</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-3 h-3 rounded-sm bg-sky-300"></div>
                                            <span class="text-xs font-semibold text-slate-600">Private</span>
                                        </div>
                                    </div>
                                    <!-- Rows -->
                                    <template x-for="(row, rowIndex) in trendTableData" :key="row.label">
                                        <div class="py-2 border-b border-slate-100 last:border-0 trend-row-animate"
                                             :style="`animation-delay: ${rowIndex * 50}ms`">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-semibold text-slate-700 truncate pr-2" x-text="row.label" style="max-width: 55%;"></span>
                                                <span class="text-xs text-slate-500 font-medium flex-shrink-0">
                                                    <span class="text-blue-700 font-bold" x-text="row.publicFormatted"></span>
                                                    <span class="text-slate-300 mx-1">·</span>
                                                    <span class="text-sky-600 font-bold" x-text="row.privateFormatted"></span>
                                                </span>
                                            </div>
                                            <!-- Stacked bar -->
                                            <div class="flex h-5 rounded overflow-hidden w-full bg-slate-100">
                                                <div class="h-full bg-blue-600 trend-bar-animate"
                                                     :style="`width: ${row.publicPct}%; animation-delay: ${rowIndex * 50 + 80}ms`"></div>
                                                <div class="h-full bg-sky-300 trend-bar-animate"
                                                     :style="`width: ${row.privatePct}%; animation-delay: ${rowIndex * 50 + 160}ms`"></div>
                                            </div>
                                            <div class="text-[10px] text-slate-400 mt-0.5 text-right" x-text="'Total: ' + row.totalFormatted"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- ═══════════════════════════════════════════════════════ -->
                            <!-- DESKTOP VIEW: Chart.js canvas chart (≥ 768px)         -->
                            <!-- ═══════════════════════════════════════════════════════ -->
                            <div class="enrollment-trend-desktop p-6">
                                <div class="relative w-full border-2 border-slate-200 rounded-lg p-3 bg-white"
                                     :style="`height: ${Math.max(500, trendDataCount * 55)}px`">
                                    <div x-show="loadingEnrollmentTrend" class="absolute inset-0 bg-white/75 flex items-center justify-center z-10 rounded-lg backdrop-blur-sm">
                                        <div class="animate-spin rounded-full h-10 w-10 border-2 border-slate-200 border-t-slate-500"></div>
                                    </div>
                                    <canvas id="enrollmentTrendChart" class="w-full h-full"></canvas>
                                </div>
                            </div>
                            <!-- Enrollment Trend Modal -->
                            <template x-teleport="body">
                                <div
                                    x-show="enrollmentTrendModalOpen"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="fixed inset-0 z-[9999] bg-black/60 flex items-center justify-center p-4"
                                    @keydown.escape.window="enrollmentTrendModalOpen = false"
                                    style="display:none;">
                                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[95vw] flex flex-col overflow-hidden" style="height:92vh;" @click.stop>
                                        <!-- Modal Header -->
                                        <div class="flex flex-wrap items-start justify-between gap-3 px-4 sm:px-6 py-4 border-b border-slate-200 flex-shrink-0">
                                            <div>
                                                <h2 class="text-sm sm:text-xl font-bold text-slate-800">
                                                    Enrollment Trend — <span x-text="selectedTrendProvince"></span> — Expanded View
                                                </h2>
                                                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                                                    Public vs. Private &bull; <span x-text="selectedTrendProvince"></span> &bull; <span x-text="selectedTrendYear"></span>
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2 sm:gap-3">
                                                <!-- Legend inline -->
                                                <div class="trend-modal-legend flex items-center gap-4 mr-2 sm:mr-4">
                                                    <div class="flex items-center gap-2"><div class="w-5 h-3 bg-blue-600 rounded"></div><span class="text-xs text-slate-600 font-medium">Public</span></div>
                                                    <div class="flex items-center gap-2"><div class="w-5 h-3 bg-sky-400 rounded"></div><span class="text-xs text-slate-600 font-medium">Private</span></div>
                                                </div>
                                                <button @click="enrollmentTrendModalOpen = false"
                                                    class="px-3 sm:px-5 py-2 sm:py-2.5 bg-slate-700 hover:bg-slate-800 text-white font-semibold rounded-lg transition-colors flex items-center gap-2 text-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Stats row -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 px-6 pt-4 flex-shrink-0">
                                            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg p-3 border border-blue-200">
                                                <p class="text-xs font-semibold text-blue-700 mb-1">Total Public Schools</p>
                                                <p class="text-2xl font-bold text-blue-900" x-text="enrollmentTrendTotals.public ? enrollmentTrendTotals.public.toLocaleString() : '—'"></p>
                                            </div>
                                            <div class="bg-gradient-to-br from-sky-50 to-indigo-50 rounded-lg p-3 border border-sky-200">
                                                <p class="text-xs font-semibold text-sky-700 mb-1">Total Private Schools</p>
                                                <p class="text-2xl font-bold text-sky-900" x-text="enrollmentTrendTotals.private ? enrollmentTrendTotals.private.toLocaleString() : '—'"></p>
                                            </div>
                                        </div>
                                        <!-- Chart -->
                                        <div class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden custom-scrollbar p-6">
                                            <div id="enrollmentTrendModalChartWrap" style="width:100%;">
                                                <canvas id="enrollmentTrendChartModal" class="w-full"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <!-- Discipline Enrollment Chart - COLLAPSIBLE -->
                    <div class="mt-6">
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden" x-data="{ disciplineEnrollmentExpanded: false, disciplineEnrollmentInitialized: false }">
                            <!-- Collapsible Header -->
                            <button 
                                @click="disciplineEnrollmentExpanded = !disciplineEnrollmentExpanded; if(disciplineEnrollmentExpanded && !disciplineEnrollmentInitialized) { disciplineEnrollmentInitialized = true; setTimeout(() => loadEnrollmentData(), 100); }"
                                class="w-full bg-slate-800 hover:bg-slate-700 transition-colors duration-200 px-6 py-5 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="bg-white/10 p-2.5 rounded-xl">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <h3 class="text-base font-bold text-white">
                                            Enrollment by Discipline — <span x-text="selectedEnrollmentProvince"></span>
                                        </h3>
                                        
                                        <p class="text-xs text-slate-400">
                                            Student enrollment by academic discipline
                                            <span x-show="!disciplineEnrollmentExpanded" class="text-slate-500 ml-2">• Click to expand</span>
                                        </p>
                                    </div>
                                </div>
                                <!-- Expand/Collapse Icon -->
                                <svg 
                                    class="w-5 h-5 text-slate-400 transition-transform duration-300" 
                                    :class="{ 'rotate-180': disciplineEnrollmentExpanded }"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Collapsible Content -->
                            <div 
                                x-show="disciplineEnrollmentExpanded" 
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0">
                                
                                <!-- Controls Bar -->
                                <div class="bg-gradient-to-r from-slate-50 to-white border-b border-slate-200 p-4">
                                    <div class="flex flex-wrap items-center justify-between mb-3 gap-2">
                                        <div class="flex items-center gap-3">
                                            <p class="text-xs text-slate-500">
                                                Student enrollment by discipline - <span x-text="selectedEnrollmentYear" class="font-semibold text-blue-600"></span>
                                                <span x-show="selectedEnrollmentProvince !== 'Davao Region'" class="text-slate-400"> • </span>
                                                <span x-show="selectedEnrollmentProvince !== 'Davao Region'" x-text="selectedEnrollmentProvince" class="font-semibold text-green-600"></span>
                                            </p>
                                        </div>
                                        
                                        <div class="flex flex-wrap items-center gap-2">
                                            <!-- Province Selector -->
                                            <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl px-3 py-1.5 shadow-sm">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span class="text-xs text-slate-500 font-medium hidden sm:inline">Province:</span>
                                                <select
                                                    x-model="selectedEnrollmentProvince"
                                                    @change="loadEnrollmentYearsForProvince(selectedEnrollmentProvince).then(() => loadEnrollmentData())"
                                                    class="text-sm font-bold text-slate-800 bg-transparent focus:outline-none cursor-pointer"
                                                >
                                                    <template x-for="province in availableEnrollmentProvinces" :key="province">
                                                        <option :value="province" x-text="province"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <!-- Year Selector -->
                                            <div x-show="availableEnrollmentYears.length > 0" class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl px-3 py-1.5 shadow-sm">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span class="text-xs text-slate-500 font-medium hidden sm:inline">Year:</span>
                                                <select 
                                                    x-model="selectedEnrollmentYear"
                                                    @change="loadEnrollmentData()"
                                                    class="text-sm font-bold text-slate-800 bg-transparent focus:outline-none cursor-pointer"
                                                >
                                                    <template x-for="year in availableEnrollmentYears" :key="year">
                                                        <option :value="year" x-text="year"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <!-- Expand Button -->
                                            <button
                                                @click="disciplineEnrollmentModalOpen = true"
                                                class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2 text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                                </svg>
                                                Expand
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Stats Summary -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-3 border border-blue-200">
                                            <p class="text-xs font-semibold text-blue-700 mb-1">Total Disciplines</p>
                                            <p class="text-2xl font-bold text-blue-900" x-text="enrollmentData.length"></p>
                                        </div>
                                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-3 border border-purple-200">
                                            <p class="text-xs font-semibold text-purple-700 mb-1">Total Enrolled</p>
                                            <p class="text-2xl font-bold text-purple-900" x-text="getTotalEnrollment().toLocaleString()"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart Container -->
                                <div class="p-6">
                                    <!-- Empty State -->
                                    <div x-show="enrollmentData.length === 0" class="flex flex-col items-center justify-center py-20">
                                        <svg class="w-20 h-20 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-slate-700 mb-2">No Enrollment Data</h3>
                                        <p class="text-sm text-slate-500">No disciplines found for <span x-text="selectedEnrollmentYear"></span></p>
                                    </div>

                                    <!-- ══════════════════════════════════════════════════════ -->
                                    <!-- MOBILE VIEW: Inline bar table (≤ 767px)              -->
                                    <!-- ══════════════════════════════════════════════════════ -->
                                    <div x-show="enrollmentData.length > 0" class="discipline-enrollment-mobile space-y-0">
                                        <div x-show="loadingDisciplineEnrollment" class="flex items-center justify-center py-10">
                                            <div class="animate-spin rounded-full h-8 w-8 border-2 border-slate-200 border-t-slate-500"></div>
                                        </div>
                                        <template x-if="!loadingDisciplineEnrollment && enrollmentData.length > 0">
                                            <div>
                                                <template x-for="(row, rowIndex) in [...enrollmentData].sort((a,b) => b.count - a.count)" :key="row.discipline">
                                                    <div class="py-2 border-b border-slate-100 last:border-0 trend-row-animate"
                                                         :style="`animation-delay: ${rowIndex * 40}ms`">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <span class="text-xs font-semibold text-slate-700 truncate pr-2"
                                                                  x-text="row.discipline"
                                                                  style="max-width: 60%;"></span>
                                                            <span class="text-xs font-bold text-blue-700 flex-shrink-0"
                                                                  x-text="row.count.toLocaleString()"></span>
                                                        </div>
                                                        <!-- Single blue gradient bar -->
                                                        <div class="h-5 rounded overflow-hidden w-full bg-slate-100">
                                                            <div class="h-full trend-bar-animate"
                                                                 :style="`
                                                                    width: ${getTotalEnrollment() > 0 ? (row.count / [...enrollmentData].sort((a,b) => b.count - a.count)[0].count * 100) : 0}%;
                                                                    background: linear-gradient(to right, rgb(30,58,138), rgb(96,165,250));
                                                                    animation-delay: ${rowIndex * 40 + 80}ms
                                                                 `"></div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- ══════════════════════════════════════════════════════ -->
                                    <!-- DESKTOP VIEW: Chart.js canvas (≥ 768px)              -->
                                    <!-- ══════════════════════════════════════════════════════ -->
                                    <div x-show="enrollmentData.length > 0"
                                        class="discipline-enrollment-desktop relative w-full border-2 border-slate-200 rounded-lg p-3 bg-white"
                                        :style="`height: ${getEnrollmentChartHeight()}px`">
                                        <div x-show="loadingDisciplineEnrollment" class="absolute inset-0 bg-white/75 flex items-center justify-center z-10 rounded-lg backdrop-blur-sm">
                                            <div class="animate-spin rounded-full h-10 w-10 border-2 border-slate-200 border-t-slate-500"></div>
                                        </div>
                                        <div style="height: 100%">
                                            <canvas id="disciplineEnrollmentChart" class="w-full h-full"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <!-- Discipline Enrollment Modal -->
                                <template x-teleport="body">
                                    <div
                                        x-show="disciplineEnrollmentModalOpen"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        class="fixed inset-0 z-[9999] bg-black/60 flex items-center justify-center p-4"
                                        @keydown.escape.window="disciplineEnrollmentModalOpen = false"
                                        style="display:none;">
                                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[95vw] flex flex-col overflow-hidden" style="height:92vh;" @click.stop>
                                            <!-- Header -->
                                            <div class="flex flex-wrap items-start justify-between gap-3 px-4 sm:px-6 py-4 border-b border-slate-200 flex-shrink-0">
                                                <div>
                                                    <h2 class="text-sm sm:text-xl font-bold text-slate-800">Enrollment by Discipline — Expanded View</h2>
                                                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                                                        <span x-text="enrollmentData.length"></span> disciplines &bull; <span x-text="selectedEnrollmentYear"></span>
                                                        <span x-show="selectedEnrollmentProvince !== 'Davao Region'" x-text="' • ' + selectedEnrollmentProvince"></span>
                                                    </p>
                                                </div>
                                                <div class="flex items-center gap-2 sm:gap-3">
                                                    <div class="hidden sm:flex items-center gap-2 mr-2">
                                                        <div class="w-20 h-3 rounded" style="background: linear-gradient(to right, #1e3a8a, #bfdbfe);"></div>
                                                        <span class="text-xs text-slate-500">High → Low</span>
                                                    </div>
                                                    <button @click="disciplineEnrollmentModalOpen = false"
                                                        class="px-3 sm:px-5 py-2 sm:py-2.5 bg-slate-700 hover:bg-slate-800 text-white font-semibold rounded-lg transition-colors flex items-center gap-2 text-sm">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- Stats -->
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 px-6 pt-4 flex-shrink-0">
                                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-3 border border-blue-200">
                                                    <p class="text-xs font-semibold text-blue-700 mb-1">Total Disciplines</p>
                                                    <p class="text-2xl font-bold text-blue-900" x-text="enrollmentData.length"></p>
                                                </div>
                                                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-3 border border-purple-200">
                                                    <p class="text-xs font-semibold text-purple-700 mb-1">Total Enrolled</p>
                                                    <p class="text-2xl font-bold text-purple-900" x-text="getTotalEnrollment().toLocaleString()"></p>
                                                </div>
                                            </div>
                                            <!-- Chart -->
                                            <div class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden custom-scrollbar px-6 pb-6 pt-4">
                                                <div :style="`height: ${Math.max(enrollmentData.length * 52, 300)}px; min-height: 200px; width: 100%;`">
                                                    <canvas id="disciplineEnrollmentChartModal" class="w-full h-full"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Licensure Passing Rates Chart - COLLAPSIBLE -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-6" x-data="{ licensureExpanded: false, licensureInitialized: false }">
                        <!-- Collapsible Header -->
                        <button 
                            @click="licensureExpanded = !licensureExpanded; if(licensureExpanded && !licensureInitialized) { licensureInitialized = true; setTimeout(() => { loadData(); }, 100); }"
                            class="w-full bg-slate-800 hover:bg-slate-700 transition-colors duration-200 px-6 py-5 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="bg-white/10 p-2.5 rounded-xl">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <h3 class="text-base font-bold text-white">Licensure Passing Rates</h3>
                                    <p class="text-xs text-slate-400">
                                        Professional board exam performance by Discipline
                                        <span x-show="!licensureExpanded" class="text-slate-500 ml-2">• Click to expand</span>
                                    </p>
                                </div>
                            </div>
                            <!-- Expand/Collapse Icon -->
                            <svg 
                                class="w-5 h-5 text-slate-400 transition-transform duration-300" 
                                :class="{ 'rotate-180': licensureExpanded }"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Collapsible Content -->
                        <div 
                            x-show="licensureExpanded" 
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">
                            
                            <!-- Controls Bar -->
                            <div class="bg-gradient-to-r from-slate-50 to-white border-b border-slate-200 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <!-- Title Section -->
                                    <div class="flex items-center gap-3">
                                        <p class="text-xs text-slate-500">
                                            Performance in regulated professions - <span x-text="selectedYear" class="font-semibold text-emerald-600"></span>
                                            <span x-show="selectedSector !== 'all'" class="ml-2 px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold">
                                                <span x-text="selectedSector"></span> only
                                            </span>
                                        </p>
                                    </div>

                                    <!-- Filters -->
                                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                                        <!-- Sector Filter -->
                                        <div class="flex items-center gap-2">
                                            <label class="text-sm font-semibold text-slate-700">Discipline:</label>
                                            <select 
                                                x-model="selectedSector" 
                                                @change="updateChart()"
                                                class="px-3 py-2 bg-white border-2 border-slate-200 rounded-lg font-medium text-slate-700 hover:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all cursor-pointer text-sm"
                                            >
                                                <option value="all">All Discipline</option>
                                                <template x-for="sector in sectors" :key="sector">
                                                    <option :value="sector" x-text="sector"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <!-- Year Filter -->
                                        <div class="flex items-center gap-2">
                                            <label class="text-sm font-semibold text-slate-700">Year:</label>
                                            <select 
                                                x-model="selectedYear" 
                                                @change="loadData()"
                                                class="px-3 py-2 bg-white border-2 border-slate-200 rounded-lg font-medium text-slate-700 hover:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all cursor-pointer text-sm"
                                            >
                                                <template x-for="year in availableYears" :key="year">
                                                    <option :value="year" x-text="year"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <!-- Expand Button -->
                                        <button 
                                            @click="expanded = !expanded"
                                            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2 text-sm"
                                        >
                                            <svg x-show="!expanded" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                            </svg>
                                            <svg x-show="expanded" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <span x-text="expanded ? 'Collapse' : 'Expand'"></span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Stats Summary -->
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg p-3 border border-emerald-200">
                                        <p class="text-xs font-semibold text-emerald-700 mb-1">Total Professions</p>
                                        <p class="text-2xl font-bold text-emerald-900" x-text="getFilteredData().length"></p>
                                    </div>
                                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg p-3 border border-amber-200">
                                        <p class="text-xs font-semibold text-amber-700 mb-1">Highest Passing Rate</p>
                                        <p class="text-2xl font-bold text-amber-900" x-text="getHighestRate() + '%'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Chart Container (Normal View) -->
                            <div class="p-6">
                                <!-- Empty State -->
                                <div x-show="getFilteredData().length === 0" class="flex flex-col items-center justify-center py-20">
                                    <svg class="w-20 h-20 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-slate-700 mb-2">No Data Available</h3>
                                    <p class="text-sm text-slate-500">No professions found for <span x-text="selectedSector"></span> in <span x-text="selectedYear"></span></p>
                                </div>

                                <!-- ══════════════════════════════════════════════════════ -->
                                <!-- MOBILE VIEW: Inline list (≤ 767px)                   -->
                                <!-- ══════════════════════════════════════════════════════ -->
                                <div x-show="getFilteredData().length > 0" class="licensure-mobile space-y-0">
                                    <div x-show="loadingLicensure" class="flex items-center justify-center py-10">
                                        <div class="animate-spin rounded-full h-8 w-8 border-2 border-slate-200 border-t-slate-500"></div>
                                    </div>
                                    <template x-if="!loadingLicensure && getFilteredData().length > 0">
                                        <div>
                                            <template x-for="(item, rowIndex) in [...getFilteredData()].sort((a,b) => b.passing_rate - a.passing_rate)" :key="item.profession">
                                                <div class="py-2.5 border-b border-slate-100 last:border-0 trend-row-animate"
                                                     :style="`animation-delay: ${rowIndex * 40}ms`">
                                                    <!-- Profession name + passing rate -->
                                                    <div class="flex items-start justify-between gap-2 mb-1">
                                                        <span class="text-xs font-semibold text-slate-800 leading-snug"
                                                              x-text="item.profession"
                                                              style="max-width: 65%;"></span>
                                                        <span class="text-xs font-black text-slate-700 flex-shrink-0"
                                                              x-text="item.passing_rate.toFixed(2) + '%'"></span>
                                                    </div>
                                                    <!-- Passers · Takers -->
                                                    <div class="text-[10px] text-slate-400 mb-1.5">
                                                        Passers: <span class="font-bold text-slate-600"
                                                                        x-text="item.passers ? item.passers.toLocaleString() : '—'"></span>
                                                        <span class="mx-1 text-slate-300">·</span>
                                                        Takers: <span class="font-bold text-slate-600"
                                                                       x-text="item.takers ? item.takers.toLocaleString() : '—'"></span>
                                                    </div>
                                                    <!-- Bar color matches desktop: sector-specific or default slate, interpolated by rank -->
                                                    <div class="h-4 rounded overflow-hidden w-full bg-slate-100">
                                                        <div class="h-full trend-bar-animate"
                                                             :style="(() => {
                                                                const total = getFilteredData().length;
                                                                const factor = total > 1 ? rowIndex / (total - 1) : 0;
                                                                let sR, sG, sB, eR, eG, eB;
                                                                const hex = sectorColors[selectedSector];
                                                                if (selectedSector !== 'all' && hex) {
                                                                    const h = hex.replace('#','');
                                                                    const br = parseInt(h.substring(0,2),16);
                                                                    const bg = parseInt(h.substring(2,4),16);
                                                                    const bb = parseInt(h.substring(4,6),16);
                                                                    sR = Math.round(br * 0.5); sG = Math.round(bg * 0.5); sB = Math.round(bb * 0.5);
                                                                    eR = Math.min(255, Math.round(br + (255-br)*0.6));
                                                                    eG = Math.min(255, Math.round(bg + (255-bg)*0.6));
                                                                    eB = Math.min(255, Math.round(bb + (255-bb)*0.6));
                                                                } else {
                                                                    sR=30;  sG=41;  sB=59;
                                                                    eR=241; eG=245; eB=249;
                                                                }
                                                                const r = Math.round(sR + (eR-sR) * factor);
                                                                const g = Math.round(sG + (eG-sG) * factor);
                                                                const b = Math.round(sB + (eB-sB) * factor);
                                                                return `width: ${item.passing_rate}%; background: rgb(${r},${g},${b}); animation-delay: ${rowIndex * 40 + 80}ms`;
                                                             })()"></div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>

                                <!-- ══════════════════════════════════════════════════════ -->
                                <!-- DESKTOP VIEW: Chart.js canvas (≥ 768px)              -->
                                <!-- ══════════════════════════════════════════════════════ -->
                                <div x-show="getFilteredData().length > 0"
                                    class="licensure-desktop relative w-full border-2 border-slate-200 rounded-lg p-3 bg-white"
                                    :style="`height: ${getChartHeight()}px`">
                                    <div x-show="loadingLicensure" class="absolute inset-0 bg-white/75 flex items-center justify-center z-10 rounded-lg backdrop-blur-sm">
                                        <div class="animate-spin rounded-full h-10 w-10 border-2 border-slate-200 border-t-slate-500"></div>
                                    </div>
                                    <canvas id="licensurePassingChart" class="w-full h-full"></canvas>
                                </div>
                            </div>

                            <!-- ===== FULLSCREEN MODAL (teleported to body) ===== -->
                            <template x-teleport="body">
                                <div 
                                    x-show="expanded"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="fixed inset-0 z-[9999] bg-black/60 flex items-center justify-center p-4"
                                    @keydown.escape.window="expanded = false"
                                    style="display:none;">

                                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[95vw] flex flex-col overflow-hidden"
                                         style="height: 92vh;"
                                         @click.stop>

                                        <!-- Modal Header -->
                                        <div class="flex flex-wrap items-start justify-between gap-3 px-4 sm:px-6 py-4 border-b border-slate-200 flex-shrink-0">
                                            <div>
                                                <h2 class="text-sm sm:text-xl font-bold text-slate-800">Licensure Passing Rates — Expanded View</h2>
                                                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                                                    <span x-show="selectedSector !== 'all'" class="mr-1 px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold" x-text="selectedSector"></span>
                                                    <span x-show="selectedSector === 'all'">All Disciplines</span>
                                                    &bull; <span x-text="selectedYear"></span>
                                                </p>
                                            </div>
                                            <button
                                                @click="expanded = false"
                                                class="ml-auto flex-shrink-0 px-3 sm:px-5 py-2 sm:py-2.5 bg-slate-700 hover:bg-slate-800 text-white font-semibold rounded-lg transition-colors flex items-center gap-2 text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Close
                                            </button>
                                        </div>

                                        <!-- Stats Cards Row (matching enrollment trend style) -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 px-6 pt-4 flex-shrink-0">
                                            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg p-3 border border-emerald-200">
                                                <p class="text-xs font-semibold text-emerald-700 mb-1">Total Professions</p>
                                                <p class="text-2xl font-bold text-emerald-900" x-text="getFilteredData().length"></p>
                                            </div>
                                            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg p-3 border border-amber-200">
                                                <p class="text-xs font-semibold text-amber-700 mb-1">Highest Passing Rate</p>
                                                <p class="text-2xl font-bold text-amber-900" x-text="getHighestRate() + '%'"></p>
                                            </div>
                                        </div>

                                        <!-- Chart — only this scrolls -->
                                        <div class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden custom-scrollbar px-6 pb-6 pt-4">
                                            <div :style="`height: ${getExpandedChartHeight()}px; min-height: 200px; width: 100%;`">
                                                <canvas id="licensurePassingChartModal" class="w-full h-full"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>


                        </div>
                    </div>
                </div>
            </div>
        </div>




    </body>

    </html>