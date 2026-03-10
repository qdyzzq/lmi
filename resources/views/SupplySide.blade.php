<!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @vite('resources/css/app.css')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <title>LMI - Education Pipeline Dashboard</title>
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
                gap: 1rem;
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
                    width: min(200px, 60vw) !important;
                    height: min(200px, 60vw) !important;
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

        </style>
    </head>

    <body x-data="licensureChartData()" class="bg-slate-100 min-h-screen">
        @include('partials.navbar')
        
        <!-- Hero Image Section -->
        <div class="relative w-full h-[500px] md:h-[700px] lg:h-[900px] overflow-hidden">
            <div class="absolute inset-0">
                <img src="{{ asset('images/navbar-bg-1.jpg') }}" alt="Education Pipeline Background"
                    class="w-full h-full object-cover object-top">
                <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/40 to-slate-100"></div>
            </div>
            
            <!-- Hero Content -->
            <div class="relative z-10 h-full flex items-center justify-center px-4">
                <div class="text-center text-white">
                    <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4 drop-shadow-lg hero-title-supply">
                        Education to Employment Pipeline
                    </h1>
                    <p class="text-base md:text-xl lg:text-2xl text-slate-100 drop-shadow-md">
                        Regional Labor Market Intelligence & Trends
                    </p>
                </div>
            </div>
            
            <!-- Scroll Indicator -->
            <div class="absolute bottom-8 sm:bottom-16 left-1/2 transform -translate-x-1/2 z-20 scroll-indicator animate-bounce">
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
                    <svg class="w-8 h-8 text-white group-hover:text-blue-300 transition-colors" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" 
                              stroke-linejoin="round" 
                              stroke-width="2"
                              d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                    <p class="text-white text-sm mt-2 font-medium group-hover:text-blue-300 transition-colors">
                        Scroll to explore
                    </p>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="w-full mt-10 relative z-30">
            <div class="p-5">
                <div class="max-w-screen-xl mx-auto px-4 space-y-6">
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
                                    <span class="text-slate-400">📍</span>
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
                                    <span class="text-slate-400">📅</span>
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
                                    <h3 class="text-lg font-bold text-slate-800">EXECUTIVE ANALYSIS: SUPPLY SIDE</h3>
                                </div>
                            </div>
                            
                            <!-- Loading State -->
                            <div x-show="loadingExecutiveAnalysis" class="flex items-center justify-center py-8">
                                <div class="animate-spin rounded-full h-6 w-6 border-2 border-slate-200 border-t-slate-500"></div>
                            </div>

                            <!-- Dynamic Analysis Text — card scrolls, text has full width -->
                            <div x-show="!loadingExecutiveAnalysis" 
                                 class="flex-1 text-sm text-slate-700 prose prose-sm max-w-none"
                                 x-html="executiveAnalysisText">
                            </div>
                        </div>
                        </div>

                        <!-- Discipline Market Share Pie Chart -->
                        <div class="w-full bg-white rounded-2xl shadow-xl border border-slate-200 p-6 relative overflow-hidden">
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
                                            .slice(0, Math.ceil(entries.length / 2));
                                    })()" :key="item[0]">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <div class="w-3 h-3 rounded-full flex-shrink-0" 
                                                 :style="`background-color: ${(() => {
                                                    const colorPalette = [
                                                        'rgb(37, 99, 235)', 'rgb(220, 38, 38)', 'rgb(22, 163, 74)',
                                                        'rgb(234, 179, 8)', 'rgb(249, 115, 22)', 'rgb(124, 58, 237)',
                                                        'rgb(20, 184, 166)', 'rgb(236, 72, 153)', 'rgb(6, 182, 212)',
                                                        'rgb(132, 204, 22)', 'rgb(96, 165, 250)', 'rgb(248, 113, 113)',
                                                        'rgb(74, 222, 128)', 'rgb(250, 204, 21)', 'rgb(251, 146, 60)',
                                                        'rgb(167, 139, 250)', 'rgb(45, 212, 191)', 'rgb(244, 114, 182)',
                                                        'rgb(34, 211, 238)', 'rgb(163, 230, 53)'
                                                    ];
                                                    const sorted = Object.entries(disciplineShares).sort((a, b) => parseFloat(b[1]) - parseFloat(a[1]));
                                                    const actualIndex = sorted.findIndex(entry => entry[0] === item[0]);
                                                    return colorPalette[actualIndex % colorPalette.length];
                                                 })()}`"></div>
                                            <div class="text-slate-700 font-medium text-sm truncate" x-text="formatDisciplineName(item[0]) + ' ' + parseFloat(item[1]).toFixed(1) + '%'"></div>
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
                                        return entries
                                            .sort((a, b) => parseFloat(b[1]) - parseFloat(a[1]))
                                            .slice(Math.ceil(entries.length / 2));
                                    })()" :key="item[0]">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <div class="w-3 h-3 rounded-full flex-shrink-0" 
                                                 :style="`background-color: ${(() => {
                                                    const colorPalette = [
                                                        'rgb(37, 99, 235)', 'rgb(220, 38, 38)', 'rgb(22, 163, 74)',
                                                        'rgb(234, 179, 8)', 'rgb(249, 115, 22)', 'rgb(124, 58, 237)',
                                                        'rgb(20, 184, 166)', 'rgb(236, 72, 153)', 'rgb(6, 182, 212)',
                                                        'rgb(132, 204, 22)', 'rgb(96, 165, 250)', 'rgb(248, 113, 113)',
                                                        'rgb(74, 222, 128)', 'rgb(250, 204, 21)', 'rgb(251, 146, 60)',
                                                        'rgb(167, 139, 250)', 'rgb(45, 212, 191)', 'rgb(244, 114, 182)',
                                                        'rgb(34, 211, 238)', 'rgb(163, 230, 53)'
                                                    ];
                                                    const sorted = Object.entries(disciplineShares).sort((a, b) => parseFloat(b[1]) - parseFloat(a[1]));
                                                    const actualIndex = sorted.findIndex(entry => entry[0] === item[0]);
                                                    return colorPalette[actualIndex % colorPalette.length];
                                                 })()}`"></div>
                                            <div class="text-slate-700 font-medium text-sm truncate" x-text="formatDisciplineName(item[0]) + ' ' + parseFloat(item[1]).toFixed(1) + '%'"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div><!-- end pie chart card -->
                        <!-- Mobile: tap hint for pie chart -->
                        <p class="block sm:hidden text-center text-xs text-slate-400 mt-2 italic">Tap chart segments for details · Tap Expand for full legend</p>

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
                                                <canvas id="disciplineMarketShareChartModalMobile" width="220" height="220" style="width:220px;height:220px;display:block;"></canvas>
                                            </div>
                                            <div class="flex-1 min-h-0 overflow-y-auto px-4 py-3 custom-scrollbar">
                                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">All Disciplines</p>
                                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem 0.75rem;">
                                                    <template x-for="(item) in (() => {
                                                        const e = Object.entries(disciplineShares || {});
                                                        return e.sort((a,b) => parseFloat(b[1]) - parseFloat(a[1]));
                                                    })()" :key="'mob-'+item[0]">
                                                        <div class="flex items-start gap-1.5 min-w-0">
                                                            <div class="w-2.5 h-2.5 rounded-full flex-shrink-0 mt-0.5"
                                                                :style="`background-color:${(()=>{const cp=['rgb(37,99,235)','rgb(220,38,38)','rgb(22,163,74)','rgb(234,179,8)','rgb(249,115,22)','rgb(124,58,237)','rgb(20,184,166)','rgb(236,72,153)','rgb(6,182,212)','rgb(132,204,22)','rgb(96,165,250)','rgb(248,113,113)','rgb(74,222,128)','rgb(250,204,21)','rgb(251,146,60)','rgb(167,139,250)','rgb(45,212,191)','rgb(244,114,182)','rgb(34,211,238)','rgb(163,230,53)'];const s=Object.entries(disciplineShares).sort((a,b)=>parseFloat(b[1])-parseFloat(a[1]));return cp[s.findIndex(e=>e[0]===item[0])%cp.length];})()} `">
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
                                            <div class="w-56 shrink-0 space-y-3">
                                                <template x-for="(item) in (() => {
                                                    const e = Object.entries(disciplineShares || {});
                                                    return e.sort((a,b)=>parseFloat(b[1])-parseFloat(a[1])).slice(0, Math.ceil(e.length/2));
                                                })()" :key="'dl-'+item[0]">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <div class="w-3 h-3 rounded-full flex-shrink-0"
                                                            :style="`background-color:${(()=>{const cp=['rgb(37,99,235)','rgb(220,38,38)','rgb(22,163,74)','rgb(234,179,8)','rgb(249,115,22)','rgb(124,58,237)','rgb(20,184,166)','rgb(236,72,153)','rgb(6,182,212)','rgb(132,204,22)','rgb(96,165,250)','rgb(248,113,113)','rgb(74,222,128)','rgb(250,204,21)','rgb(251,146,60)','rgb(167,139,250)','rgb(45,212,191)','rgb(244,114,182)','rgb(34,211,238)','rgb(163,230,53)'];const s=Object.entries(disciplineShares).sort((a,b)=>parseFloat(b[1])-parseFloat(a[1]));return cp[s.findIndex(e=>e[0]===item[0])%cp.length];})()} `">
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="text-xs font-semibold text-slate-800 leading-tight" x-text="formatDisciplineName(item[0])"></p>
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
                                                    return e.sort((a,b)=>parseFloat(b[1])-parseFloat(a[1])).slice(Math.ceil(e.length/2));
                                                })()" :key="'dr-'+item[0]">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <div class="w-3 h-3 rounded-full flex-shrink-0"
                                                            :style="`background-color:${(()=>{const cp=['rgb(37,99,235)','rgb(220,38,38)','rgb(22,163,74)','rgb(234,179,8)','rgb(249,115,22)','rgb(124,58,237)','rgb(20,184,166)','rgb(236,72,153)','rgb(6,182,212)','rgb(132,204,22)','rgb(96,165,250)','rgb(248,113,113)','rgb(74,222,128)','rgb(250,204,21)','rgb(251,146,60)','rgb(167,139,250)','rgb(45,212,191)','rgb(244,114,182)','rgb(34,211,238)','rgb(163,230,53)'];const s=Object.entries(disciplineShares).sort((a,b)=>parseFloat(b[1])-parseFloat(a[1]));return cp[s.findIndex(e=>e[0]===item[0])%cp.length];})()} `">
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
                                    <span x-show="selectedTrendProvince !== 'Davao Region'" class="text-slate-400"> • </span>
                                    <span x-show="selectedTrendProvince !== 'Davao Region'" x-text="selectedTrendProvince" class="font-semibold text-green-600"></span>
                                </p>
                                <!-- Right: filters + expand grouped together -->
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl px-3 py-1.5 shadow-sm">
                                        <span class="text-slate-400">📍</span>
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
                                        <span class="text-slate-400">📅</span>
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
                                       x-text="enrollmentTrendTotals.public ? enrollmentTrendTotals.public.toLocaleString() : 'Loading...'">
                                    </p>
                                </div>
                                <div class="bg-gradient-to-br from-sky-50 to-indigo-50 rounded-lg p-3 border border-sky-200">
                                    <p class="text-xs font-semibold text-sky-700 mb-1">Total Private Schools</p>
                                    <p class="text-2xl font-bold text-sky-900"
                                       x-text="enrollmentTrendTotals.private ? enrollmentTrendTotals.private.toLocaleString() : 'Loading...'">
                                    </p>
                                </div>
                            </div>

                            <!-- Chart Container -->
                            <div class="p-6">
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
                                                <span class="text-slate-400">📍</span>
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
                                                <span class="text-slate-400">📅</span>
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

                                    <!-- Chart -->
                                    <div x-show="enrollmentData.length > 0" 
                                        class="relative w-full border-2 border-slate-200 rounded-lg p-3 bg-white"
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

                                <!-- Normal Chart -->
                                <div x-show="getFilteredData().length > 0"
                                    class="relative w-full border-2 border-slate-200 rounded-lg p-3 bg-white"
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

        <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('licensureChartData', () => ({
            selectedSector: 'all',
            selectedYear: new Date().getFullYear(),
            availableYears: [],
            sectors: [],
            allData: [],
            chart: null,
            expanded: false,
            chartHeight: 600,
            // Modal open states for each chart
            enrollmentTrendModalOpen: false,
            disciplineEnrollmentModalOpen: false,
            pieModalOpen: false,
            
            // Enrollment data variables
            selectedEnrollmentYear: '',
            availableEnrollmentYears: [],
            selectedEnrollmentProvince: 'Davao Region',
            availableEnrollmentProvinces: [], // Will be loaded from API
            enrollmentData: [],
            enrollmentChart: null,
            enrollmentNoDataForCombo: false,
            
            // New charts variables
            disciplineMarketShareChart: null,
            enrollmentTrendChart: null,
            selectedProvince: 'Davao City',
            selectedTrendYear: '',
            availableTrendYears: [], // Will be loaded from API
            selectedTrendProvince: 'Davao City',
            availableTrendProvinces: [], // Will be loaded from API
            
            // Enrollment Trend totals for stats display
            enrollmentTrendTotals: {
                public: 0,
                private: 0,
                combined: 0
            },
            trendDataCount: 0,
            
            // NEW: Enrollment Overview Data (for top cards and pie chart)
            totalEnrollees: 0,
            disciplineShares: {}, // Will be populated dynamically with all individual disciplines
            
            // 🆕 Executive Analysis
            executiveAnalysisText: 'Loading analysis...',
            loadingExecutiveAnalysis: false,
            
            // NEW: Graduation Rate Data (for top metric cards)
            graduationRateData: {
                graduate_year: null,
                enrollment_year: null,
                graduation_rate: 60,
                base_enrollees: 0,
                projected_graduates: 0,
                is_default: true
            },
            loadingGraduationRate: false,
            latestEnrollmentTotal: 0,
            latestEnrollmentYear: null,
            loadingLatestEnrollment: false,
            loadingPieChart: false,
            loadingEnrollmentTrend: false,
            loadingDisciplineEnrollment: false,
            loadingLicensure: false,
            
            
            // === INITIALIZATION FLAGS === (PREVENTS INFINITE RECURSION)
            chartInitialized: {
                disciplineMarketShare: false,
                enrollmentTrend: false
            },
            
            sectorColors: {
                'Engineering, Architecture & Technical': '#FF6B00',
                'Healthcare & Nursing': '#00AA00',
                'Natural Sciences': '#FFCC00',
                'Education': '#0066FF',
                'Social Work & Behavioral Sciences': '#9900FF',
                'Real Estate Industry': '#FF1493',
                'Defense Industry': '#FF0000',
                'Business, Finance & Logistics': '#00CCCC',
            },

            async init() {
                await this.loadAvailableYears();
                await this.loadData();
                await this.loadEnrollmentProvinces(); // Load provinces first so selectedEnrollmentProvince is valid
                await this.loadEnrollmentYearsForProvince(this.selectedEnrollmentProvince); // Years filtered by default province
                await this.loadEnrollmentData();
                await this.loadTrendProvinces(); // Load trend provinces from API
                await this.loadTrendYearsForProvince(this.selectedTrendProvince); // Load trend years filtered by default province (Davao City)
                
                // NEW: Load graduation rate data (for top metric cards)
                await this.loadGraduationRateData();
                
                // Load latest enrollment total for the Total Enrollees KPI card
                await this.loadLatestEnrollmentTotal();
                
                setTimeout(() => this.initOtherCharts(), 100);
                setTimeout(() => this.initEnrollmentTrendChart(), 100);
                
                // NEW: Load enrollment overview data
                await this.loadEnrollmentOverviewData();
                
                // Watch for expanded changes — render a fresh chart in the modal canvas
                this.$watch('expanded', (isExpanded) => {
                    if (isExpanded) {
                        document.body.style.overflow = 'hidden';
                        setTimeout(() => { this.renderModalChart(); }, 150);
                    } else {
                        document.body.style.overflow = '';
                        const modalCanvas = document.getElementById('licensurePassingChartModal');
                        if (modalCanvas) { const e = Chart.getChart(modalCanvas); if (e) e.destroy(); }
                    }
                });

                // Enrollment Trend modal
                this.$watch('enrollmentTrendModalOpen', (open) => {
                    if (open) {
                        document.body.style.overflow = 'hidden';
                        setTimeout(() => { this.renderEnrollmentTrendModal(); }, 150);
                    } else {
                        document.body.style.overflow = '';
                        const c = document.getElementById('enrollmentTrendChartModal');
                        if (c) { const e = Chart.getChart(c); if (e) e.destroy(); }
                    }
                });

                // Discipline Enrollment modal
                this.$watch('disciplineEnrollmentModalOpen', (open) => {
                    if (open) {
                        document.body.style.overflow = 'hidden';
                        setTimeout(() => { this.renderDisciplineEnrollmentModal(); }, 150);
                    } else {
                        document.body.style.overflow = '';
                        const c = document.getElementById('disciplineEnrollmentChartModal');
                        if (c) { const e = Chart.getChart(c); if (e) e.destroy(); }
                    }
                });

                // Pie modal
                this.$watch('pieModalOpen', (open) => {
                    if (open) {
                        document.body.style.overflow = 'hidden';
                        setTimeout(() => { this.renderPieModal(); }, 150);
                    } else {
                        document.body.style.overflow = '';
                        const c = document.getElementById('disciplineMarketShareChartModal');
                        if (c) { const e = Chart.getChart(c); if (e) e.destroy(); }
                    }
                });
            },

            async loadAvailableYears() {
                try {
                    const response = await fetch('/api/licensure-rates/years');
                    this.availableYears = await response.json();
                    if (this.availableYears.length > 0) {
                        this.selectedYear = this.availableYears[0];
                    }
                } catch (error) {
                    // removed error
                    this.availableYears = [2025, 2024, 2023, 2022, 2021];
                    this.selectedYear = 2025;
                }
            },

            // NEW: Load Graduation Rate Data for current year
            async loadGraduationRateData() {
                this.loadingGraduationRate = true;
                
                try {
                    // Get current academic year (adjust this logic based on your needs)
                    // For example: if it's currently 2026, the graduate year would be 2025-2026
                    const currentDate = new Date();
                    const currentYear = currentDate.getFullYear();
                    const currentMonth = currentDate.getMonth() + 1; // 1-12
                    
                    // If we're past June (month 6), use next year for second part
                    // e.g., July 2025 = 2025-2026, March 2025 = 2024-2025
                    let graduateYear;
                    if (currentMonth >= 7) {
                        graduateYear = `${currentYear}-${currentYear + 1}`;
                    } else {
                        graduateYear = `${currentYear - 1}-${currentYear}`;
                    }
                    
                    // removed debug log
                    
                    const response = await fetch(`/api/graduation-rate/${encodeURIComponent(graduateYear)}`);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    
                    const result = await response.json();
                    
                    if (result.success && result.data) {
                        this.graduationRateData = result.data;
                        // removed debug log
                    } else {
                        // removed warning
                        this.graduationRateData = {
                            graduate_year: graduateYear,
                            enrollment_year: null,
                            graduation_rate: 60,
                            base_enrollees: 0,
                            projected_graduates: 0,
                            is_default: true
                        };
                    }
                } catch (error) {
                    // removed error
                    // Set default empty data
                    this.graduationRateData = {
                        graduate_year: null,
                        enrollment_year: null,
                        graduation_rate: 60,
                        base_enrollees: 0,
                        projected_graduates: 0,
                        is_default: true
                    };
                } finally {
                    this.loadingGraduationRate = false;
                }
            },

            // Load the latest enrollment total for the Total Enrollees KPI card
            async loadLatestEnrollmentTotal() {
                this.loadingLatestEnrollment = true;
                try {
                    // Get all available enrollment years
                    const yearsResponse = await fetch('/api/discipline-enrollment/meta/years');
                    const years = await yearsResponse.json();
                    if (!years || years.length === 0) return;

                    // Walk through years newest-first, find the latest year where
                    // BOTH conditions are true:
                    //   1. The year exists in the years list (API has data for it)
                    //   2. Davao Region / Total row actually exists for that year
                    let resolvedYear = null;
                    let resolvedTotal = 0;

                    for (const year of years) {
                        const result = await fetch(
                            `/api/discipline-enrollment/check/${encodeURIComponent(year)}?province=Davao+Region&institution_type=Total`
                        );
                        if (result.ok) {
                            const raw = await result.json();
                            // API returns disciplines nested under raw.data.disciplines
                            if (raw.exists && raw.data && raw.data.disciplines) {
                                const total = Object.values(raw.data.disciplines)
                                    .reduce((sum, val) => sum + (parseInt(val) || 0), 0);
                                if (total > 0) {
                                    resolvedYear  = year;
                                    resolvedTotal = total;
                                    break; // Found the most recent valid year — stop
                                }
                            }
                        }
                    }

                    if (resolvedYear) {
                        this.latestEnrollmentYear  = resolvedYear;
                        this.latestEnrollmentTotal = resolvedTotal;
                    }
                    // If nothing found at all, card stays at 0 / 'No data' — that's fine

                } catch (error) {
                    // silently fail — card will show 0
                } finally {
                    this.loadingLatestEnrollment = false;
                }
            },

            // Helper function to format numbers with commas
            formatNumber(num) {
                if (!num && num !== 0) return '0';
                return parseInt(num).toLocaleString();
            },

            // Helper function to format discipline names (convert snake_case to Title Case)
            formatDisciplineName(discipline) {
                const fullNames = {
                    // snake_case keys (from enrollment by discipline API)
                    'agriculture':      'Agriculture, Forestry, Fisheries',
                    'architecture':     'Architecture and Town Planning',
                    'business':         'Business Administration',
                    'criminal_justice': 'Criminal Justice Education',
                    'education':        'Education Science',
                    'engineering':      'Engineering and Technology',
                    'arts':             'Fine and Applied Arts',
                    'general':          'General Programs',
                    'home_economics':   'Home Economics',
                    'humanities':       'Humanities',
                    'it':               'IT-Related Disciplines',
                    'law':              'Law and Jurisprudence',
                    'maritime':         'Maritime',
                    'mass_comm':        'Mass Communication',
                    'mathematics':      'Mathematics',
                    'medical':          'Medical and Allied',
                    'natural_science':  'Natural Science',
                    'other_disciplines':'Other Disciplines',
                    'other':            'Other Disciplines',
                    'religion':         'Religion and Theology',
                    'service_trades':   'Service Trades',
                    'social_sciences':  'Social and Behavioral Sciences',
                    // Short display strings (from trend API)
                    'Education':             'Education Science',
                    'Business & Admin':      'Business Administration',
                    'Medical & Allied':      'Medical and Allied',
                    'Engineering & Tech':    'Engineering and Technology',
                    'Criminal Justice':      'Criminal Justice Education',
                    'IT & Related':          'IT-Related Disciplines',
                    'Social Sciences':       'Social and Behavioral Sciences',
                    'Maritime':              'Maritime',
                    'Architecture':          'Architecture and Town Planning',
                    'Service Trades':        'Service Trades',
                    'Agri & Forestry':       'Agriculture, Forestry, Fisheries',
                    'Other Disciplines':     'Other Disciplines',
                    'Humanities':            'Humanities',
                    'Natural Science':       'Natural Science',
                    'Law':                   'Law and Jurisprudence',
                    'Fine Arts':             'Fine and Applied Arts',
                    'Religion':              'Religion and Theology',
                    'Mass Comm':             'Mass Communication',
                    'Mathematics':           'Mathematics',
                    'Home Economics':        'Home Economics',
                };
                return fullNames[discipline] || discipline
                    .split('_')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            },

            // Helper function to generate consistent colors for disciplines
            getDisciplineColor(discipline) {
                // Color palette inspired by portfolio chart - gradient from red through yellow, green, to blue/purple
                const colors = [
                    // Reds (hot)
                    'rgb(220, 38, 38)',    // Red
                    'rgb(239, 68, 68)',    // Light Red
                    'rgb(248, 113, 113)',  // Lighter Red
                    
                    // Oranges
                    'rgb(234, 88, 12)',    // Dark Orange
                    'rgb(249, 115, 22)',   // Orange
                    'rgb(251, 146, 60)',   // Light Orange
                    'rgb(253, 186, 116)',  // Lighter Orange
                    
                    // Yellows
                    'rgb(245, 158, 11)',   // Amber
                    'rgb(251, 191, 36)',   // Yellow
                    'rgb(253, 224, 71)',   // Light Yellow
                    
                    // Lime/Yellow-Green
                    'rgb(132, 204, 22)',   // Lime
                    'rgb(163, 230, 53)',   // Light Lime
                    
                    // Greens
                    'rgb(22, 163, 74)',    // Dark Green
                    'rgb(34, 197, 94)',    // Green
                    'rgb(74, 222, 128)',   // Light Green
                    'rgb(134, 239, 172)',  // Lighter Green
                    
                    // Teals/Cyan
                    'rgb(20, 184, 166)',   // Teal
                    'rgb(45, 212, 191)',   // Light Teal
                    
                    // Blues
                    'rgb(37, 99, 235)',    // Blue
                    'rgb(59, 130, 246)',   // Light Blue
                    'rgb(96, 165, 250)',   // Lighter Blue
                    'rgb(147, 197, 253)',  // Very Light Blue
                    
                    // Purples/Violets (cool)
                    'rgb(124, 58, 237)',   // Violet
                    'rgb(139, 92, 246)',   // Purple
                    'rgb(168, 85, 247)',   // Light Purple
                    'rgb(192, 132, 252)',  // Lighter Purple
                ];
                
                // Use hash of discipline name to get consistent color
                let hash = 0;
                for (let i = 0; i < discipline.length; i++) {
                    hash = discipline.charCodeAt(i) + ((hash << 5) - hash);
                }
                return colors[Math.abs(hash) % colors.length];
            },

            async loadData() {
                this.loadingLicensure = true;
                try {
                    const response = await fetch(`/api/licensure-rates/year/${this.selectedYear}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    this.allData = await response.json();
                    this.sectors = [...new Set(this.allData.map(item => item.sector))].sort();
                    // removed debug log
                    this.$nextTick(() => this.updateChart());
                } catch (error) {
                    // removed error
                    this.allData = [];
                    this.sectors = [];
                    alert('Failed to load data from API. Please check:\n1. API endpoint is correct\n2. Server is running\n3. API returns data in correct format');
                } finally {
                    this.loadingLicensure = false;
                }
            },

            async loadEnrollmentYears() {
                try {
                    const response = await fetch('/api/discipline-enrollment/meta/years');
                    this.availableEnrollmentYears = await response.json();
                    if (this.availableEnrollmentYears.length > 0) {
                        this.selectedEnrollmentYear = this.availableEnrollmentYears[0];
                    }
                } catch (error) {
                    // removed error
                    this.availableEnrollmentYears = ['2024-2025', '2023-2024'];
                    this.selectedEnrollmentYear = '2024-2025';
                }
            },

            async loadEnrollmentProvinces() {
                try {
                    const response = await fetch('/api/discipline-enrollment/provinces');
                    const provinces = await response.json();
                    // Always include "Davao Region" as the first option
                    // Filter out 'Davao Region' from API results to avoid duplicate, then prepend it
                    const filteredProvinces = provinces.filter(p => p !== 'Davao Region');
                    this.availableEnrollmentProvinces = ['Davao Region', ...filteredProvinces];
                    // removed debug log
                } catch (error) {
                    // removed error
                    // Fallback to Davao Region provinces
                    this.availableEnrollmentProvinces = [
                        'Davao Region',
                        'Davao del Norte',
                        'Davao del Sur', 
                        'Davao Oriental',
                        'Davao de Oro',
                        'Davao Occidental'
                    ];
                }
            },

            async loadEnrollmentYearsForProvince(province) {
                try {
                    // Always pass province so the API returns only years with data for that province
                    let url = '/api/discipline-enrollment/meta/years';
                    if (province) {
                        url += '?province=' + encodeURIComponent(province);
                    }
                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Failed');
                    const years = await response.json();
                    if (years.length > 0) {
                        this.availableEnrollmentYears = years;
                        if (!years.includes(this.selectedEnrollmentYear)) {
                            this.selectedEnrollmentYear = years[0];
                        }
                        this.enrollmentNoDataForCombo = false;
                    } else {
                        this.availableEnrollmentYears = [];
                        this.selectedEnrollmentYear = '';
                        this.enrollmentNoDataForCombo = true;
                    }
                } catch (error) {
                    // Fallback: keep existing year list
                }
            },

            async loadTrendYears() {
                try {
                    const response = await fetch('/api/discipline-enrollment/meta/years');
                    this.availableTrendYears = await response.json();
                    if (this.availableTrendYears.length > 0) {
                        this.selectedTrendYear = this.availableTrendYears[0];
                    }
                } catch (error) {
                    // removed error
                    this.availableTrendYears = ['2024-2025', '2023-2024', '2022-2023'];
                    this.selectedTrendYear = '2024-2025';
                }
            },

            async loadTrendYearsForProvince(province) {
                try {
                    let url = '/api/discipline-enrollment/meta/years';
                    if (province) {
                        url += '?province=' + encodeURIComponent(province);
                    }
                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Failed');
                    const years = await response.json();
                    if (years.length > 0) {
                        this.availableTrendYears = years;
                        if (!years.includes(this.selectedTrendYear)) {
                            this.selectedTrendYear = years[0];
                        }
                    } else {
                        this.availableTrendYears = [];
                        this.selectedTrendYear = '';
                    }
                } catch (error) {
                    // Fallback: keep existing year list
                }
            },


            async loadTrendProvinces() {
                try {
                    const response = await fetch('/api/discipline-enrollment/provinces');
                    const provinces = await response.json();
                    // Always include "Davao Region" as the first option
                    // Exclude Davao Region from trend — no Private/Public split available
                    this.availableTrendProvinces = provinces.filter(p => p !== 'Davao Region');
                    // removed debug log
                } catch (error) {
                    // removed error
                    // Fallback to Davao Region provinces
                    this.availableTrendProvinces = [
                        'Davao Region',
                        'Davao del Norte',
                        'Davao del Sur',
                        'Davao Oriental',
                        'Davao de Oro',
                        'Davao Occidental'
                    ];
                }
            },



            // Smart enrollment fetch: uses Total for Davao Region, Private+Public for specific provinces
            async fetchEnrollmentByProvince(year, province) {
                if (province === 'Davao Region') {
                    const result = await fetch(`/api/discipline-enrollment/check/${encodeURIComponent(year)}?province=Davao+Region&institution_type=Total`);
                    let totalData = { disciplines: {} };
                    if (result.ok) {
                        const raw = await result.json();
                        if (raw.exists && raw.data) { totalData = raw.data; }
                    }
                    // Return same shape as private+public pair but merged into one
                    return { privateData: totalData, publicData: { disciplines: {} }, isDavaoTotal: true };
                } else {
                    const [privateResult, publicResult] = await Promise.allSettled([
                        fetch(`/api/discipline-enrollment/check/${encodeURIComponent(year)}?province=${encodeURIComponent(province)}&institution_type=Private`),
                        fetch(`/api/discipline-enrollment/check/${encodeURIComponent(year)}?province=${encodeURIComponent(province)}&institution_type=Public`)
                    ]);
                    let privateData = { disciplines: {} };
                    let publicData = { disciplines: {} };
                    if (privateResult.status === 'fulfilled' && privateResult.value.ok) {
                        const raw = await privateResult.value.json();
                        if (raw.exists && raw.data) { privateData = raw.data; }
                    }
                    if (publicResult.status === 'fulfilled' && publicResult.value.ok) {
                        const raw = await publicResult.value.json();
                        if (raw.exists && raw.data) { publicData = raw.data; }
                    }
                    return { privateData, publicData, isDavaoTotal: false };
                }
            },
            async loadEnrollmentData() {
                this.loadingDisciplineEnrollment = true;
                try {
                    // Fetch aggregated data based on selected province for both Private and Public
                    const province = this.selectedEnrollmentProvince;

                    // Smart fetch: uses Total for Davao Region, Private+Public for specific provinces
                    const { privateData, publicData } = await this.fetchEnrollmentByProvince(this.selectedEnrollmentYear, province);

                    // Combine for total enrollment
                    this.enrollmentData = [
                        { 
                            discipline: 'Agriculture, Forestry, Fisheries', 
                            count: (privateData.disciplines.agriculture || 0) + (publicData.disciplines.agriculture || 0),
                            private: privateData.disciplines.agriculture || 0,
                            public: publicData.disciplines.agriculture || 0
                        },
                        { 
                            discipline: 'Architecture and Town Planning', 
                            count: (privateData.disciplines.architecture || 0) + (publicData.disciplines.architecture || 0),
                            private: privateData.disciplines.architecture || 0,
                            public: publicData.disciplines.architecture || 0
                        },
                        { 
                            discipline: 'Business Administration', 
                            count: (privateData.disciplines.business || 0) + (publicData.disciplines.business || 0),
                            private: privateData.disciplines.business || 0,
                            public: publicData.disciplines.business || 0
                        },
                        { 
                            discipline: 'Criminal Justice Education', 
                            count: (privateData.disciplines.criminal_justice || 0) + (publicData.disciplines.criminal_justice || 0),
                            private: privateData.disciplines.criminal_justice || 0,
                            public: publicData.disciplines.criminal_justice || 0
                        },
                        { 
                            discipline: 'Education Science', 
                            count: (privateData.disciplines.education || 0) + (publicData.disciplines.education || 0),
                            private: privateData.disciplines.education || 0,
                            public: publicData.disciplines.education || 0
                        },
                        { 
                            discipline: 'Engineering and Technology', 
                            count: (privateData.disciplines.engineering || 0) + (publicData.disciplines.engineering || 0),
                            private: privateData.disciplines.engineering || 0,
                            public: publicData.disciplines.engineering || 0
                        },
                        { 
                            discipline: 'Fine and Applied Arts', 
                            count: (privateData.disciplines.arts || 0) + (publicData.disciplines.arts || 0),
                            private: privateData.disciplines.arts || 0,
                            public: publicData.disciplines.arts || 0
                        },
                        { 
                            discipline: 'Humanities', 
                            count: (privateData.disciplines.humanities || 0) + (publicData.disciplines.humanities || 0),
                            private: privateData.disciplines.humanities || 0,
                            public: publicData.disciplines.humanities || 0
                        },
                        { 
                            discipline: 'IT-Related Disciplines', 
                            count: (privateData.disciplines.it || 0) + (publicData.disciplines.it || 0),
                            private: privateData.disciplines.it || 0,
                            public: publicData.disciplines.it || 0
                        },
                        { 
                            discipline: 'Law and Jurisprudence', 
                            count: (privateData.disciplines.law || 0) + (publicData.disciplines.law || 0),
                            private: privateData.disciplines.law || 0,
                            public: publicData.disciplines.law || 0
                        },
                        { 
                            discipline: 'Maritime', 
                            count: (privateData.disciplines.maritime || 0) + (publicData.disciplines.maritime || 0),
                            private: privateData.disciplines.maritime || 0,
                            public: publicData.disciplines.maritime || 0
                        },
                        { 
                            discipline: 'Mass Communication', 
                            count: (privateData.disciplines.mass_comm || 0) + (publicData.disciplines.mass_comm || 0),
                            private: privateData.disciplines.mass_comm || 0,
                            public: publicData.disciplines.mass_comm || 0
                        },
                        { 
                            discipline: 'Mathematics', 
                            count: (privateData.disciplines.mathematics || 0) + (publicData.disciplines.mathematics || 0),
                            private: privateData.disciplines.mathematics || 0,
                            public: publicData.disciplines.mathematics || 0
                        },
                        { 
                            discipline: 'Medical and Allied', 
                            count: (privateData.disciplines.medical || 0) + (publicData.disciplines.medical || 0),
                            private: privateData.disciplines.medical || 0,
                            public: publicData.disciplines.medical || 0
                        },
                        { 
                            discipline: 'Natural Science', 
                            count: (privateData.disciplines.natural_science || 0) + (publicData.disciplines.natural_science || 0),
                            private: privateData.disciplines.natural_science || 0,
                            public: publicData.disciplines.natural_science || 0
                        },
                        { 
                            discipline: 'Religion and Theology', 
                            count: (privateData.disciplines.religion || 0) + (publicData.disciplines.religion || 0),
                            private: privateData.disciplines.religion || 0,
                            public: publicData.disciplines.religion || 0
                        },
                        { 
                            discipline: 'Service Trades', 
                            count: (privateData.disciplines.service_trades || 0) + (publicData.disciplines.service_trades || 0),
                            private: privateData.disciplines.service_trades || 0,
                            public: publicData.disciplines.service_trades || 0
                        },
                        { 
                            discipline: 'Social and Behavioral Sciences', 
                            count: (privateData.disciplines.social_sciences || 0) + (publicData.disciplines.social_sciences || 0),
                            private: privateData.disciplines.social_sciences || 0,
                            public: publicData.disciplines.social_sciences || 0
                        }
                    ].filter(item => item.count > 0); // Only show disciplines with enrollment
                    
                    // removed debug log
                    this.$nextTick(() => this.updateEnrollmentChart());
                    
                    // NEW: Also load enrollment overview data (for top cards and pie chart)
                    await this.loadEnrollmentOverviewData();
                    
                    // 🆕 Load Executive Analysis
                    await this.loadExecutiveAnalysis();
                } catch (error) {
                    // removed error
                    this.enrollmentData = [];
                } finally {
                    this.loadingDisciplineEnrollment = false;
                }
            },

            // 🆕 Load Executive Analysis from database
            async loadExecutiveAnalysis() {
                this.loadingExecutiveAnalysis = true;
                
                try {
                    const params = new URLSearchParams({
                        province: this.selectedEnrollmentProvince,
                        academic_year: this.selectedEnrollmentYear
                    });
                    
                    const response = await fetch(`/api/supply-side-analysis/show?${params}`);
                    const data = await response.json();
                    
                    if (data.success && data.data) {
                        this.executiveAnalysisText = data.data.analysis_text;
                    } else {
                        this.executiveAnalysisText = "ERROR: Unable to load analysis";
                    }
                } catch (error) {
                    // removed error
                    this.executiveAnalysisText = "ERROR: Failed to load analysis";
                } finally {
                    this.loadingExecutiveAnalysis = false;
                }
            },

            getTotalEnrollment() {
                return this.enrollmentData.reduce((sum, item) => sum + item.count, 0);
            },

            getEnrollmentChartHeight() {
                const dataCount = this.enrollmentData.length;
                return Math.max(500, dataCount * 35);
            },

            generateBlueGradientColors(count) {
                // Blue gradient: from dark blue (#1e3a8a) to light blue (#eff6ff)
                const startColor = { r: 30, g: 58, b: 138 };   // blue-900
                const endColor = { r: 239, g: 246, b: 255 };   // blue-50
                
                const colors = [];
                
                for (let i = 0; i < count; i++) {
                    const factor = count > 1 ? i / (count - 1) : 0;
                    
                    const r = Math.round(startColor.r + (endColor.r - startColor.r) * factor);
                    const g = Math.round(startColor.g + (endColor.g - startColor.g) * factor);
                    const b = Math.round(startColor.b + (endColor.b - startColor.b) * factor);
                    
                    colors.push(`rgb(${r}, ${g}, ${b})`);
                }
                
                return colors;
            },

            updateEnrollmentChart() {
                const ctx = document.getElementById('disciplineEnrollmentChart');
                if (!ctx) {
                    // removed error
                    return;
                }
                
                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }
                
                if (this.enrollmentChart) {
                    try {
                        this.enrollmentChart.destroy();
                    } catch (e) {
                        // removed warning
                    }
                    this.enrollmentChart = null;
                }
                
                if (this.enrollmentData.length === 0) {
                    // removed debug log
                    return;
                }
                
                // Sort by count descending (highest first)
                const sortedData = [...this.enrollmentData].sort((a, b) => b.count - a.count);
                
                const labels = sortedData.map(d => d.discipline);
                const counts = sortedData.map(d => d.count);
                const colors = this.generateBlueGradientColors(sortedData.length);
                
                setTimeout(() => {
                    try {
                        this.enrollmentChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Enrollment Count',
                                    data: counts,
                                    backgroundColor: colors,
                                    borderRadius: 8,
                                    // Remove fixed barThickness, use percentage-based spacing instead
                                }]
                            },
                            options: {
                                // ✅ ENHANCED ANIMATION OPTIONS
                                animation: {
                                    duration: 1500,
                                    easing: 'easeOutQuart',
                                    delay: (context) => {
                                        let delay = 0;
                                        if (context.type === 'data' && context.mode === 'default') {
                                            delay = context.dataIndex * 30;
                                        }
                                        return delay;
                                    }
                                },
                                animations: {
                                    x: {
                                        duration: 1500,
                                        from: 0,
                                        easing: 'easeOutQuart'
                                    }
                                },
                                transitions: {
                                    active: {
                                        animation: {
                                            duration: 400
                                        }
                                    }
                                },
                                
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                layout: {
                                    padding: {
                                        right: 60
                                    }
                                },
                                plugins: {
                                    legend: { 
                                        display: false 
                                    },
                                    tooltip: {
                                        enabled: true,
                                        backgroundColor: 'rgba(0, 0, 0, 0.95)',
                                        padding: 16,
                                        titleFont: { size: 16, weight: 'bold' },
                                        bodyFont: { size: 15 },
                                        borderColor: 'rgba(255, 255, 255, 0.2)',
                                        borderWidth: 2,
                                        displayColors: false,
                                        callbacks: {
                                            title: (context) => sortedData[context[0].dataIndex].discipline,
                                            label: (context) => {
                                                const data = sortedData[context.dataIndex];
                                                const count = new Intl.NumberFormat('en-US').format(data.count);
                                                return [
                                                    `Enrollment: ${count} students`,
                                                    `Academic Year: ${this.selectedEnrollmentYear}`
                                                ];
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        grid: {
                                            color: 'rgba(148, 163, 184, 0.1)',
                                            borderDash: [8, 4]
                                        },
                                        ticks: {
                                            font: { 
                                                size: 14, 
                                                weight: 'bold' 
                                            },
                                            color: '#1e293b',
                                            callback: function(value) {
                                                return new Intl.NumberFormat('en-US').format(value);
                                            }
                                        },
                                        title: {
                                            display: true,
                                            text: 'ENROLLMENT COUNT',
                                            font: { 
                                                size: 15, 
                                                weight: 'bold' 
                                            },
                                            color: '#1e293b',
                                            padding: { top: 10 }
                                        }
                                    },
                                    y: {
                                        grid: { 
                                            display: false 
                                        },
                                        ticks: {
                                            font: { 
                                                size: 14, 
                                                weight: 'bold' 
                                            },
                                            color: '#1e293b',
                                            autoSkip: false,
                                            padding: 8
                                        },
                                        // Match Licensure chart spacing
                                        categoryPercentage: 0.6,  // Thinner bars with more space
                                        barPercentage: 0.7,        // Bar width within category
                                        title: {
                                            display: true,
                                            text: 'DISCIPLINES',
                                            font: { 
                                                size: 15, 
                                                weight: 'bold' 
                                            },
                                            color: '#1e293b',
                                            padding: { bottom: 10 }
                                        }
                                    }
                                }
                            },
                            plugins: [{
                                id: 'enrollmentValueLabels',
                                afterDatasetsDraw: function(chart) {
                                    const ctx = chart.ctx;
                                    const meta = chart.getDatasetMeta(0);
                                    
                                    if (!meta || !meta.data || meta.data.length === 0) {
                                        return;
                                    }
                                    
                                    ctx.save();
                                    
                                    meta.data.forEach((element, index) => {
                                        const count = sortedData[index].count;
                                        
                                        const base = element.base;
                                        const x = element.x;
                                        const y = element.y;
                                        
                                        // Draw count in the CENTER of the bar
                                        if (count && count > 0) {
                                            ctx.textAlign = 'center';
                                            ctx.textBaseline = 'middle';
                                            ctx.font = 'bold 13px Arial, sans-serif';
                                            
                                            const countText = new Intl.NumberFormat('en-US').format(count);
                                            const centerX = base + ((x - base) / 2);
                                            
                                            // White text with black outline for contrast
                                            ctx.strokeStyle = '#000000';
                                            ctx.lineWidth = 3;
                                            ctx.strokeText(countText, centerX, y);
                                            
                                            ctx.fillStyle = '#ffffff';
                                            ctx.fillText(countText, centerX, y);
                                        }
                                    });
                                    
                                    ctx.restore();
                                }
                            }]
                        });
                        // removed debug log
                    } catch (error) {
                        // removed error
                    }
                }, 100);
            },

            getFilteredData() {
                const filtered = this.selectedSector === 'all' 
                    ? this.allData 
                    : this.allData.filter(item => item.sector === this.selectedSector);
                
                return filtered;
            },

            getAverageRate() {
                const data = this.getFilteredData();
                if (data.length === 0) return 0;
                const avg = data.reduce((sum, item) => sum + item.passing_rate, 0) / data.length;
                return avg.toFixed(1);
            },

            getHighestRate() {
                const data = this.getFilteredData();
                if (data.length === 0) return 0;
                return Math.max(...data.map(item => item.passing_rate)).toFixed(2);
            },

            getChartHeight() {
                // Return stored height value
                return this.chartHeight;
            },

            getExpandedChartHeight() {
                const count = this.getFilteredData().length;
                // For very few items, keep height small so bar stays thin and centered
                if (count <= 3) return 300;
                if (count <= 10) return count * 60;
                return Math.min(count * 48, 4000);
            },

            renderModalChart() {
                const filteredData = this.getFilteredData();
                const ctx = document.getElementById('licensurePassingChartModal');
                if (!ctx || filteredData.length === 0) return;

                // Destroy any existing chart on this canvas
                const existing = Chart.getChart(ctx);
                if (existing) existing.destroy();

                const labels = filteredData.map(d => d.profession);
                const rates  = filteredData.map(d => d.passing_rate);
                const counts = filteredData.map(d => d.total_takers || 0);
                const colors = this.generateGradientColors(filteredData.length, this.selectedSector !== 'all' ? this.selectedSector : null);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Passing Rate (%)',
                            data: rates,
                            backgroundColor: colors,
                            borderRadius: 4,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: { top: 20, bottom: 20, left: 10, right: 60 }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.85)',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: (c) => [
                                        `Passing Rate: ${c.parsed.x.toFixed(2)}%`,
                                        `Total Takers: ${counts[c.dataIndex].toLocaleString()}`
                                    ]
                                }
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'end',
                                color: '#1e293b',
                                font: { size: 12, weight: 'bold' },
                                formatter: (v) => v.toFixed(2) + '%',
                                padding: { right: 6 }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                max: 100,
                                grid: { color: 'rgba(148,163,184,0.15)' },
                                title: {
                                    display: true,
                                    text: 'PASSING RATE (%)',
                                    font: { size: 12, weight: 'bold' },
                                    color: '#475569',
                                    padding: { top: 8 }
                                },
                                ticks: { font: { size: 12 }, color: '#64748b', callback: v => v + '%' }
                            },
                            y: {
                                grid: { display: false },
                                title: {
                                    display: window.innerWidth >= 640,
                                    text: 'PROFESSIONS',
                                    font: { size: 12, weight: 'bold' },
                                    color: '#475569',
                                    padding: { bottom: 8 }
                                },
                                ticks: { font: { size: window.innerWidth < 640 ? 10 : 12, weight: 'bold' }, color: '#1e293b', autoSkip: false },
                                categoryPercentage: 0.6,
                                barPercentage: 0.7,
                                maxBarThickness: 32
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            },

            // ── Enrollment Trend Modal Chart ──────────────────────────────
            async renderEnrollmentTrendModal() {
                const ctx = document.getElementById('enrollmentTrendChartModal');
                if (!ctx) return;
                const existing = Chart.getChart(ctx);
                if (existing) existing.destroy();

                // Re-fetch fresh data using current filters
                let labels = [], publicData = [], privateData = [];
                try {
                    const res = await fetch(`/api/discipline-enrollment/trend?year=${encodeURIComponent(this.selectedTrendYear)}&province=${encodeURIComponent(this.selectedTrendProvince)}`);
                    const d = await res.json();
                    labels = (d.disciplines || []).map(d => this.formatDisciplineName(d));
                    publicData = (d.publicSchools || []).map(v => Number(v) || 0);
                    privateData = (d.privateSchools || []).map(v => Number(v) || 0);
                } catch(e) { return; }

                const barHeight = 36;
                const modalHeight = Math.max(labels.length * barHeight * 2 + 80, 400);
                ctx.parentElement.style.height = modalHeight + 'px';
                ctx.style.height = modalHeight + 'px';

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Public Schools', data: publicData, backgroundColor: 'rgba(37,99,235,0.85)', borderRadius: { topLeft:6, bottomLeft:6, topRight:0, bottomRight:0 }, borderSkipped: false, maxBarThickness: 32 },
                            { label: 'Private Schools', data: privateData, backgroundColor: 'rgba(125,211,252,0.85)', borderRadius: { topLeft:0, bottomLeft:0, topRight:6, bottomRight:6 }, borderSkipped: false, maxBarThickness: 32 }
                        ]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: { right: 80, top: 10, bottom: 10 } },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.85)', padding: 12, cornerRadius: 8,
                                callbacks: { label: (c) => `${c.dataset.label}: ${c.parsed.x.toLocaleString()}` }
                            }
                        },
                        scales: {
                            x: {
                                stacked: true, beginAtZero: true,
                                grid: { color: 'rgba(148,163,184,0.1)' },
                                title: { display: true, text: 'ENROLLMENT COUNT', font: { size: 12, weight: 'bold' }, color: '#475569', padding: { top: 8 } },
                                ticks: { font: { size: 12 }, color: '#64748b', callback: v => v.toLocaleString() }
                            },
                            y: {
                                stacked: true, grid: { display: false },
                                title: { display: window.innerWidth >= 640, text: 'DISCIPLINE', font: { size: 12, weight: 'bold' }, color: '#475569' },
                                ticks: { font: { size: window.innerWidth < 640 ? 10 : 12, weight: 'bold' }, color: '#1e293b', autoSkip: false },
                                categoryPercentage: 0.6, barPercentage: 0.7
                            }
                        }
                    }
                });
            },

            // ── Discipline Enrollment Modal Chart ─────────────────────────
            renderDisciplineEnrollmentModal() {
                const ctx = document.getElementById('disciplineEnrollmentChartModal');
                if (!ctx || this.enrollmentData.length === 0) return;
                const existing = Chart.getChart(ctx);
                if (existing) existing.destroy();

                const sorted = [...this.enrollmentData].sort((a, b) => b.count - a.count);
                const labels = sorted.map(d => d.discipline);
                const counts = sorted.map(d => d.count);
                const colors = this.generateBlueGradientColors(sorted.length);

                new Chart(ctx, {
                    type: 'bar',
                    data: { labels, datasets: [{ label: 'Enrollment Count', data: counts, backgroundColor: colors, borderRadius: 6, maxBarThickness: 32 }] },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: { right: 80, top: 10, bottom: 10, left: 10 } },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.9)', padding: 12, cornerRadius: 8,
                                callbacks: {
                                    title: (c) => sorted[c[0].dataIndex].discipline,
                                    label: (c) => `Enrollment: ${sorted[c.dataIndex].count.toLocaleString()} students`
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: { color: 'rgba(148,163,184,0.1)' },
                                title: { display: true, text: 'ENROLLMENT COUNT', font: { size: 12, weight: 'bold' }, color: '#475569', padding: { top: 8 } },
                                ticks: { font: { size: 12 }, color: '#64748b', callback: v => v.toLocaleString() }
                            },
                            y: {
                                grid: { display: false },
                                title: { display: window.innerWidth >= 640, text: 'DISCIPLINE', font: { size: 12, weight: 'bold' }, color: '#475569' },
                                ticks: { font: { size: window.innerWidth < 640 ? 10 : 12, weight: 'bold' }, color: '#1e293b', autoSkip: false },
                                categoryPercentage: 0.9, barPercentage: 0.95
                            }
                        }
                    }
                });
            },

            // ── Pie / Doughnut Modal Chart ─────────────────────────────────
            renderPieModal() {
                const isMobile = window.innerWidth < 640;
                const canvasId = isMobile ? 'disciplineMarketShareChartModalMobile' : 'disciplineMarketShareChartModal';
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;
                const existing = Chart.getChart(ctx);
                if (existing) existing.destroy();

                // ── Build labels/data in same iteration order as normal chart ──
                const labels = [];
                const data = [];
                Object.entries(this.disciplineShares).forEach(([discipline, percentage]) => {
                    labels.push(this.formatDisciplineName(discipline));
                    data.push(parseFloat(percentage));
                });

                // ── Same color palette as normal chart ──
                const colorPalette = [
                    'rgb(37, 99, 235)', 'rgb(220, 38, 38)', 'rgb(22, 163, 74)',
                    'rgb(234, 179, 8)',  'rgb(249, 115, 22)', 'rgb(124, 58, 237)',
                    'rgb(20, 184, 166)', 'rgb(236, 72, 153)', 'rgb(6, 182, 212)',
                    'rgb(132, 204, 22)', 'rgb(96, 165, 250)', 'rgb(248, 113, 113)',
                    'rgb(74, 222, 128)', 'rgb(250, 204, 21)', 'rgb(251, 146, 60)',
                    'rgb(167, 139, 250)','rgb(45, 212, 191)', 'rgb(244, 114, 182)',
                    'rgb(34, 211, 238)', 'rgb(163, 230, 53)'
                ];

                // ── Same sort-then-assign logic as normal chart ──
                const sortedIndices = data
                    .map((value, index) => ({ value, index }))
                    .sort((a, b) => b.value - a.value);

                const colors = new Array(data.length);
                sortedIndices.forEach((item, sortedIndex) => {
                    colors[item.index] = colorPalette[sortedIndex % colorPalette.length];
                });

                // ── Top 5 for datalabels (same as normal chart) ──
                const top5Indices = sortedIndices.slice(0, 5).map(item => item.index);

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data,
                            backgroundColor: colors,
                            borderWidth: 2,
                            borderColor: '#fff',
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: false,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.85)', padding: 12, cornerRadius: 8,
                                callbacks: { label: (c) => `${c.label}: ${c.parsed.toFixed(1)}%` }
                            },
                            datalabels: {
                                color: '#fff',
                                font: { weight: 'bold', size: 13 },
                                formatter: (value, context) => {
                                    if (top5Indices.includes(context.dataIndex)) {
                                        return value.toFixed(1) + '%';
                                    }
                                    return '';
                                },
                                anchor: 'center',
                                align: 'center',
                                offset: 0
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            },

            generateGradientColors(count, sector = null) {
                let startColor, endColor;
                
                if (sector && sector !== 'all' && this.sectorColors[sector]) {
                    const baseColor = this.sectorColors[sector];
                    const rgb = this.hexToRgb(baseColor);
                    
                    startColor = { 
                        r: Math.round(rgb.r * 0.5),
                        g: Math.round(rgb.g * 0.5), 
                        b: Math.round(rgb.b * 0.5) 
                    };
                    endColor = { 
                        r: Math.min(255, Math.round(rgb.r + (255 - rgb.r) * 0.6)),
                        g: Math.min(255, Math.round(rgb.g + (255 - rgb.g) * 0.6)), 
                        b: Math.min(255, Math.round(rgb.b + (255 - rgb.b) * 0.6)) 
                    };
                } else {
                    startColor = { r: 30, g: 41, b: 59 };
                    endColor = { r: 241, g: 245, b: 249 };
                }
                
                const colors = [];
                
                for (let i = 0; i < count; i++) {
                    const factor = count > 1 ? i / (count - 1) : 0;
                    
                    const r = Math.round(startColor.r + (endColor.r - startColor.r) * factor);
                    const g = Math.round(startColor.g + (endColor.g - startColor.g) * factor);
                    const b = Math.round(startColor.b + (endColor.b - startColor.b) * factor);
                    
                    colors.push(`rgb(${r}, ${g}, ${b})`);
                }
                
                return colors;
            },

            hexToRgb(hex) {
                hex = hex.replace('#', '');
                
                return {
                    r: parseInt(hex.substring(0, 2), 16),
                    g: parseInt(hex.substring(2, 4), 16),
                    b: parseInt(hex.substring(4, 6), 16)
                };
            },

            getSectorGradient() {
                if (this.selectedSector === 'all' || !this.sectorColors[this.selectedSector]) {
                    return '#1e293b, #f1f5f9';
                }
                
                const baseColor = this.sectorColors[this.selectedSector];
                const rgb = this.hexToRgb(baseColor);
                
                const darkR = Math.round(rgb.r * 0.5);
                const darkG = Math.round(rgb.g * 0.5);
                const darkB = Math.round(rgb.b * 0.5);
                
                const lightR = Math.min(255, Math.round(rgb.r + (255 - rgb.r) * 0.6));
                const lightG = Math.min(255, Math.round(rgb.g + (255 - rgb.g) * 0.6));
                const lightB = Math.min(255, Math.round(rgb.b + (255 - rgb.b) * 0.6));
                
                return `rgb(${darkR}, ${darkG}, ${darkB}), rgb(${rgb.r}, ${rgb.g}, ${rgb.b}), rgb(${lightR}, ${lightG}, ${lightB})`;
            },

            updateChart() {
                const filteredData = this.getFilteredData();
                
                const ctx = document.getElementById('licensurePassingChart');
                if (!ctx) {
                    // removed error
                    return;
                }
                
                // 🔧 IMPROVED: Properly destroy existing chart using Chart.js registry
                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }
                
                // Also destroy our stored reference
                if (this.chart) {
                    try {
                        this.chart.destroy();
                    } catch (e) {
                        // removed warning
                    }
                    this.chart = null;
                }
                
                if (filteredData.length === 0) {
                    // removed debug log
                    return;
                }
                
                // Calculate and store height based on data count
                // For visibility: fewer items = bigger bars
                const dataCount = filteredData.length;
                if (dataCount === 1) {
                    this.chartHeight = 200;
                } else if (dataCount <= 5) {
                    this.chartHeight = dataCount * 80;
                } else if (dataCount <= 10) {
                    this.chartHeight = dataCount * 60;
                } else {
                    this.chartHeight = Math.max(600, dataCount * 40);
                }
                
                filteredData.sort((a, b) => b.passing_rate - a.passing_rate);

                const labels = filteredData.map(d => d.profession);
                const rates = filteredData.map(d => d.passing_rate);
                const colors = this.generateGradientColors(filteredData.length, this.selectedSector);
                
                // Create chart after a small delay
                setTimeout(() => {
                    try {
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Passing Rate',
                                    data: rates,
                                    backgroundColor: colors,
                                    borderRadius: 8,
                                    barThickness: 28,
                                }]
                            },
                            options: {
                                // ✅ ENHANCED ANIMATION OPTIONS - Same as enrollment chart
                                animation: {
                                    duration: 1500,
                                    easing: 'easeOutQuart',
                                    delay: (context) => {
                                        let delay = 0;
                                        if (context.type === 'data' && context.mode === 'default') {
                                            delay = context.dataIndex * 30;
                                        }
                                        return delay;
                                    }
                                },
                                animations: {
                                    x: {
                                        duration: 1500,
                                        from: 0,
                                        easing: 'easeOutQuart'
                                    }
                                },
                                transitions: {
                                    active: {
                                        animation: {
                                            duration: 400
                                        }
                                    }
                                },
                                
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                layout: {
                                    padding: {
                                        right: 60
                                    }
                                },
                                plugins: {
                                    legend: { 
                                        display: false 
                                    },
                                    tooltip: {
                                        enabled: true,
                                        backgroundColor: 'rgba(0, 0, 0, 0.95)',
                                        padding: 16,
                                        titleFont: { size: 16, weight: 'bold' },
                                        bodyFont: { size: 15 },
                                        borderColor: 'rgba(255, 255, 255, 0.2)',
                                        borderWidth: 2,
                                        displayColors: false,
                                        callbacks: {
                                            title: (context) => filteredData[context[0].dataIndex].profession,
                                            label: (context) => {
                                                const data = filteredData[context.dataIndex];
                                                const takers = data.takers ? new Intl.NumberFormat('en-US').format(data.takers) : 'N/A';
                                                const passers = data.passers ? new Intl.NumberFormat('en-US').format(data.passers) : 'N/A';
                                                const passingRate = data.passing_rate ? data.passing_rate.toFixed(2) + '%' : 'N/A';
                                                
                                                return [
                                                    `Takers: ${takers}`,
                                                    `Passers: ${passers}`,
                                                    `Passing Rate: ${passingRate}`,
                                                    `Sector: ${data.sector}`
                                                ];
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        max: 100,
                                        grid: {
                                            color: 'rgba(148, 163, 184, 0.1)',
                                            borderDash: [8, 4]
                                        },
                                        ticks: {
                                            callback: v => v + '%',
                                            stepSize: 20,
                                            font: { 
                                                size: 14, 
                                                weight: 'bold' 
                                            },
                                            color: '#1e293b'
                                        },
                                        title: {
                                            display: true,
                                            text: 'PASSING RATE (%)',
                                            font: { 
                                                size: 15, 
                                                weight: 'bold' 
                                            },
                                            color: '#1e293b',
                                            padding: { top: 10 }
                                        }
                                    },
                                    y: {
                                        grid: { display: false },
                                        ticks: {
                                            font: { size: window.innerWidth < 640 ? 10 : 13, weight: 'bold' },
                                            color: '#1e293b',
                                            autoSkip: false,
                                            padding: 8
                                        },
                                        title: {
                                            display: window.innerWidth >= 640,
                                            text: 'PROFESSIONS',
                                            font: { size: 13, weight: 'bold' },
                                            color: '#1e293b',
                                            padding: { bottom: 10 }
                                        }
                                    }
                                }
                            },
                            plugins: [{
                                id: 'valueLabels',
                                afterDatasetsDraw: function(chart) {
                                    const ctx = chart.ctx;
                                    const meta = chart.getDatasetMeta(0);
                                    
                                    if (!meta || !meta.data || meta.data.length === 0) {
                                        return;
                                    }
                                    
                                    ctx.save();
                                    
                                    meta.data.forEach((element, index) => {
                                        const passers = filteredData[index].passers;
                                        const passingRate = filteredData[index].passing_rate;
                                        
                                        const base = element.base;
                                        const x = element.x;
                                        const y = element.y;
                                        
                                        if (passers && passers > 0) {
                                            ctx.textAlign = 'center';
                                            ctx.textBaseline = 'middle';
                                            ctx.font = 'bold 13px Arial, sans-serif';
                                            
                                            const passersText = new Intl.NumberFormat('en-US').format(passers);
                                            const centerX = base + ((x - base) / 2);
                                            
                                            ctx.strokeStyle = '#000000';
                                            ctx.lineWidth = 3;
                                            ctx.strokeText(passersText, centerX, y);
                                            
                                            ctx.fillStyle = '#ffffff';
                                            ctx.fillText(passersText, centerX, y);
                                        }
                                        
                                        if (passingRate) {
                                            ctx.textAlign = 'left';
                                            ctx.textBaseline = 'middle';
                                            ctx.font = 'bold 12px Arial, sans-serif';
                                            
                                            const rateText = passingRate.toFixed(2) + '%';
                                            const endX = x + 8;
                                            
                                            ctx.fillStyle = '#1e293b';
                                            ctx.fillText(rateText, endX, y);
                                        }
                                    });
                                    
                                    ctx.restore();
                                }
                            }]
                        });
                        // removed debug log
                    } catch (error) {
                        // removed error
                    }
                }, 50);
            },

            generatePurpleGradientColors(count) {
                // Purple gradient: from dark purple (#581c87) to light purple (#faf5ff)
                const startColor = { r: 88, g: 28, b: 135 };   // purple-900
                const endColor = { r: 250, g: 245, b: 255 };   // purple-50
                
                const colors = [];
                
                for (let i = 0; i < count; i++) {
                    const factor = count > 1 ? i / (count - 1) : 0;
                    
                    const r = Math.round(startColor.r + (endColor.r - startColor.r) * factor);
                    const g = Math.round(startColor.g + (endColor.g - startColor.g) * factor);
                    const b = Math.round(startColor.b + (endColor.b - startColor.b) * factor);
                    
                    colors.push(`rgb(${r}, ${g}, ${b})`);
                }
                
                return colors;
            },

            // ==================== GRADUATE FUNCTIONS WITH ANIMATIONS ====================
            

            initOtherCharts() {
                // Other charts initialization can go here if needed
            },

            initDisciplineMarketShareChart() {
                // === PREVENT DOUBLE INITIALIZATION ===
                if (this.chartInitialized.disciplineMarketShare) {
                    // removed debug log
                    return;
                }
                
                const ctx = document.getElementById('disciplineMarketShareChart');
                if (!ctx) {
                    // removed debug log
                    return;
                }

                const data = {
                    labels: ['Business & Admin', 'Education', 'Engineering & Tech', 'IT & Related', 'Medical & Allied', 'Agri & Forestry'],
                    datasets: [{
                        data: [26.4, 21.3, 17.1, 14.7, 15.8, 4.8],
                        backgroundColor: [
                            'rgb(59, 130, 246)',   // blue
                            'rgb(34, 197, 94)',    // green
                            'rgb(249, 115, 22)',   // orange
                            'rgb(239, 68, 68)',    // red
                            'rgb(168, 85, 247)',   // purple
                            'rgb(20, 184, 166)'    // teal
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                };

                this.disciplineMarketShareChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: data,
                    options: {
                        responsive: false,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + context.parsed + '%';
                                    }
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
                
                // === MARK AS INITIALIZED ===
                this.chartInitialized.disciplineMarketShare = true;
                // removed debug log
            },

            // ── Shared builder: fetch data + (re)build the Enrollment Trend chart ──
            async buildEnrollmentTrendChart() {
                const ctx = document.getElementById('enrollmentTrendChart');
                if (!ctx) return;

                // Destroy existing chart
                const existing = Chart.getChart(ctx);
                if (existing) existing.destroy();
                if (this.enrollmentTrendChart) {
                    try { this.enrollmentTrendChart.destroy(); } catch(e) {}
                    this.enrollmentTrendChart = null;
                }

                this.loadingEnrollmentTrend = true;
                try {
                    const response = await fetch(
                        `/api/discipline-enrollment/trend?year=${encodeURIComponent(this.selectedTrendYear)}&province=${encodeURIComponent(this.selectedTrendProvince)}`
                    );
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const apiData = await response.json();

                    // Update stats
                    this.enrollmentTrendTotals = apiData.totals;
                    // Sanitize data — map raw DB keys to full display names
                    const cleanLabels  = Array.isArray(apiData.disciplines)   ? apiData.disciplines.map(d => this.formatDisciplineName(d)) : [];
                    const cleanPublic  = Array.isArray(apiData.publicSchools)  ? apiData.publicSchools.map(v  => Number(v)  || 0)     : [];
                    const cleanPrivate = Array.isArray(apiData.privateSchools) ? apiData.privateSchools.map(v => Number(v) || 0)      : [];

                    // Update dynamic height via Alpine state
                    this.trendDataCount = cleanLabels.length;

                    // Wait one tick so Alpine resizes the container before Chart.js measures it
                    await this.$nextTick();

                    setTimeout(() => {
                        this.enrollmentTrendChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: cleanLabels,
                                datasets: [
                                    {
                                        label: 'Public Schools',
                                        data: cleanPublic,
                                        backgroundColor: 'rgba(37, 99, 235, 0.8)',
                                        borderColor: 'rgb(29, 78, 216)',
                                        borderWidth: 0,
                                        borderRadius: { topLeft: 8, topRight: 0, bottomLeft: 8, bottomRight: 0 },
                                        borderSkipped: false
                                    },
                                    {
                                        label: 'Private Schools',
                                        data: cleanPrivate,
                                        backgroundColor: 'rgba(125, 211, 252, 0.8)',
                                        borderColor: 'rgb(56, 189, 248)',
                                        borderWidth: 0,
                                        borderRadius: { topLeft: 0, topRight: 8, bottomLeft: 0, bottomRight: 8 },
                                        borderSkipped: false
                                    }
                                ]
                            },
                            options: {
                                // ✅ Same animation as Enrollment by Discipline
                                animation: {
                                    duration: 1500,
                                    easing: 'easeOutQuart',
                                    delay: (context) => {
                                        if (context.type === 'data' && context.mode === 'default') {
                                            return context.dataIndex * 30;
                                        }
                                        return 0;
                                    }
                                },
                                animations: {
                                    x: { duration: 1500, from: 0, easing: 'easeOutQuart' }
                                },
                                transitions: {
                                    active: { animation: { duration: 400 } }
                                },
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                layout: { padding: { right: 100 } },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        labels: {
                                            font: { size: 13, weight: 'bold' },
                                            color: '#334155',
                                            padding: 15,
                                            usePointStyle: true,
                                            pointStyle: 'rect'
                                        }
                                    },
                                    tooltip: {
                                        enabled: true,
                                        backgroundColor: 'rgba(0, 0, 0, 0.95)',
                                        padding: 16,
                                        titleFont: { size: 16, weight: 'bold' },
                                        bodyFont: { size: 15 },
                                        borderColor: 'rgba(255, 255, 255, 0.2)',
                                        borderWidth: 2,
                                        displayColors: true,
                                        callbacks: {
                                            label: (c) => `${c.dataset.label}: ${c.parsed.x.toLocaleString()} students`
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        stacked: true,
                                        beginAtZero: true,
                                        grid: { color: 'rgba(148, 163, 184, 0.1)', borderDash: [8, 4] },
                                        ticks: {
                                            font: { size: 14, weight: 'bold' },
                                            color: '#1e293b',
                                            callback: (v) => v.toLocaleString()
                                        },
                                        title: {
                                            display: true,
                                            text: 'NUMBER OF STUDENTS',
                                            font: { size: 15, weight: 'bold' },
                                            color: '#1e293b',
                                            padding: { top: 10 }
                                        }
                                    },
                                    y: {
                                        stacked: true,
                                        grid: { display: false },
                                        ticks: {
                                            font: { size: window.innerWidth < 640 ? 10 : 13, weight: 'bold' },
                                            color: '#1e293b',
                                            autoSkip: false,
                                            padding: 8
                                        },
                                        // ✅ Same thickness as Enrollment by Discipline
                                        categoryPercentage: 0.9,
                                        barPercentage: 0.80,
                                        title: {
                                            display: window.innerWidth >= 640,
                                            text: 'DISCIPLINES',
                                            font: { size: 13, weight: 'bold' },
                                            color: '#1e293b',
                                            padding: { bottom: 10 }
                                        }
                                    }
                                }
                            },
                            plugins: [{
                                id: 'enrollmentTrendValueLabels',
                                afterDatasetsDraw(chart) {
                                    const ctx = chart.ctx;
                                    chart.data.datasets.forEach((dataset, datasetIndex) => {
                                        const meta = chart.getDatasetMeta(datasetIndex);
                                        if (!meta?.data?.length) return;
                                        ctx.save();
                                        ctx.font = 'bold 13px Arial, sans-serif';
                                        meta.data.forEach((element, index) => {
                                            const value = dataset.data[index];
                                            if (value && value > 0) {
                                                const barWidth = Math.abs(element.x - element.base);
                                                if (barWidth > 40) {
                                                    const centerX = element.base + (barWidth / 2);
                                                    ctx.textAlign = 'center';
                                                    ctx.textBaseline = 'middle';
                                                    ctx.strokeStyle = '#000000';
                                                    ctx.lineWidth = 3;
                                                    ctx.strokeText(value.toLocaleString(), centerX, element.y);
                                                    ctx.fillStyle = '#ffffff';
                                                    ctx.fillText(value.toLocaleString(), centerX, element.y);
                                                }
                                            }
                                        });
                                        ctx.restore();
                                    });
                                }
                            }]
                        });
                    }, 50);

                } catch (error) {
                    // Fall back silently
                } finally {
                    this.loadingEnrollmentTrend = false;
                }
            },

            async initEnrollmentTrendChart() {
                if (this.chartInitialized.enrollmentTrend) return;
                await this.buildEnrollmentTrendChart();
                this.chartInitialized.enrollmentTrend = true;
            },

            // NEW: Load Enrollment Overview Data (for top cards and pie chart)
            async loadEnrollmentOverviewData() {
                if (!this.selectedEnrollmentYear || !this.selectedEnrollmentProvince) {
                    // removed warning
                    return;
                }

                this.loadingPieChart = true;
                try {
                    // removed debug log
                    
                    // Smart fetch: uses Total for Davao Region, Private+Public for specific provinces
                    const province = this.selectedEnrollmentProvince;
                    const { privateData, publicData } = await this.fetchEnrollmentByProvince(this.selectedEnrollmentYear, province);

                    // Calculate totals and discipline shares
                    this.calculateEnrollmentMetrics(privateData, publicData);
                    
                    // Update pie chart
                    this.updateDisciplineMarketShareChart();
                    
                } catch (error) {
                    // removed error
                    // Reset to defaults
                    this.totalEnrollees = 0;
                    this.disciplineShares = {};
                    // Still update the chart even with zero data
                    this.updateDisciplineMarketShareChart();
                } finally {
                    this.loadingPieChart = false;
                }
            },

            calculateEnrollmentMetrics(privateData, publicData) {
                // Calculate total enrollees and graduates from discipline data
                let totalEnrolled = 0;
                let totalGraduates = 0;
                
                // Use total_enrolled_sy if available, otherwise sum disciplines
                if (privateData.total_enrolled_sy !== undefined || publicData.total_enrolled_sy !== undefined) {
                    totalEnrolled = (parseInt(privateData.total_enrolled_sy) || 0) + (parseInt(publicData.total_enrolled_sy) || 0);
                } else {
                    // Sum all discipline values as enrollment count
                    Object.values(privateData.disciplines || {}).forEach(count => totalEnrolled += (parseInt(count) || 0));
                    Object.values(publicData.disciplines || {}).forEach(count => totalEnrolled += (parseInt(count) || 0));
                }
                
                // Use total_graduates if available
                if (privateData.total_graduates !== undefined || publicData.total_graduates !== undefined) {
                    totalGraduates = (parseInt(privateData.total_graduates) || 0) + (parseInt(publicData.total_graduates) || 0);
                } else {
                    // Use total enrolled as fallback for projected graduates
                    totalGraduates = totalEnrolled;
                }
                
                this.totalEnrollees = totalEnrolled;

                // Combine private and public discipline data (NO GROUPING - show all individual disciplines)
                const combinedDisciplines = {};
                const allDisciplineKeys = new Set([
                    ...Object.keys(privateData.disciplines || {}),
                    ...Object.keys(publicData.disciplines || {})
                ]);

                allDisciplineKeys.forEach(key => {
                    combinedDisciplines[key] = (privateData.disciplines[key] || 0) + (publicData.disciplines[key] || 0);
                });

                // Calculate percentages for ALL individual disciplines
                const grandTotal = Object.values(combinedDisciplines).reduce((a, b) => a + b, 0);
                
                // Store all disciplines with their percentages (exact values, no rounding)
                this.disciplineShares = {};
                if (grandTotal > 0) {
                    Object.entries(combinedDisciplines).forEach(([discipline, count]) => {
                        // Store the exact percentage without rounding for accurate chart rendering
                        this.disciplineShares[discipline] = (count / grandTotal) * 100;
                    });
                }
            },

            updateDisciplineMarketShareChart() {
                const ctx = document.getElementById('disciplineMarketShareChart');
                if (!ctx) {
                    // removed error
                    return;
                }

                // Destroy existing chart if it exists
                if (this.disciplineMarketShareChart) {
                    this.disciplineMarketShareChart.destroy();
                    this.disciplineMarketShareChart = null;
                }
                // Belt-and-suspenders: destroy any chart Chart.js still has on this canvas
                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }

                // Prepare data for all individual disciplines
                const labels = [];
                const data = [];
                
                Object.entries(this.disciplineShares).forEach(([discipline, percentage]) => {
                    labels.push(this.formatDisciplineName(discipline));
                    data.push(parseFloat(percentage));
                });

                // Sort by percentage (highest first) to create gradient
                const sortedIndices = data
                    .map((value, index) => ({ value, index }))
                    .sort((a, b) => b.value - a.value);
                
                // Generate colors - primary colors first, then shades when primaries run out
                const colorPalette = [
                    // Primary colors
                    'rgb(37, 99, 235)',    // Blue
                    'rgb(220, 38, 38)',    // Red
                    'rgb(22, 163, 74)',    // Green
                    'rgb(234, 179, 8)',    // Yellow
                    'rgb(249, 115, 22)',   // Orange
                    'rgb(124, 58, 237)',   // Violet/Purple
                    'rgb(20, 184, 166)',   // Teal
                    'rgb(236, 72, 153)',   // Pink
                    'rgb(6, 182, 212)',    // Cyan
                    'rgb(132, 204, 22)',   // Lime
                    // Shades (used when primaries run out)
                    'rgb(96, 165, 250)',   // Blue-400 (lighter blue)
                    'rgb(248, 113, 113)',  // Red-400 (lighter red)
                    'rgb(74, 222, 128)',   // Green-400 (lighter green)
                    'rgb(250, 204, 21)',   // Yellow-400 (lighter yellow)
                    'rgb(251, 146, 60)',   // Orange-400 (lighter orange)
                    'rgb(167, 139, 250)',  // Violet-400 (lighter purple)
                    'rgb(45, 212, 191)',   // Teal-400 (lighter teal)
                    'rgb(244, 114, 182)',  // Pink-400 (lighter pink)
                    'rgb(34, 211, 238)',   // Cyan-400 (lighter cyan)
                    'rgb(163, 230, 53)',   // Lime-400 (lighter lime)
                ];
                
                // Assign colors based on sorted order
                const colors = new Array(data.length);
                sortedIndices.forEach((item, sortedIndex) => {
                    colors[item.index] = colorPalette[sortedIndex % colorPalette.length];
                });

                // Identify top 5 for labels
                const top5Indices = sortedIndices.slice(0, 5).map(item => item.index);

                const chartData = {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 10
                    }]
                };

                this.disciplineMarketShareChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: chartData,
                    options: {
                        responsive: false,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return `${context.label}: ${context.parsed.toFixed(1)}%`;
                                    }
                                }
                            },
                            datalabels: {
                                color: '#fff',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                },
                                formatter: function(value, context) {
                                    // Only show label for top 5
                                    if (top5Indices.includes(context.dataIndex)) {
                                        return value.toFixed(1) + '%';
                                    }
                                    return ''; // Hide label for others
                                },
                                anchor: 'center',
                                align: 'center',
                                offset: 0
                            }
                        },
                        cutout: '60%'
                    },
                    plugins: [ChartDataLabels] // Enable the plugin
                });

                // removed debug log
            },

            async updateProvincialChart() {
                // removed debug log
                // removed debug log
                // removed debug log
                
                try {
                    // Fetch updated data from API
                    const response = await fetch(
                        `/api/provincial-progression?year=${encodeURIComponent(this.selectedProgressionYear)}&province=${encodeURIComponent(this.selectedProgressionProvince)}`
                    );
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    // removed debug log
                    
                    // Update chart if it exists
                    if (this.provincialProgressionChart) {
                        // === SANITIZE DATA - Create clean copies to avoid circular references ===
                        const cleanLabels = Array.isArray(data.disciplines) ? [...data.disciplines] : [];
                        const cleanEnrolled = Array.isArray(data.enrolled) ? data.enrolled.map(val => Number(val) || 0) : [];
                        const cleanProjected = Array.isArray(data.projected) ? data.projected.map(val => Number(val) || 0) : [];
                        
                        // Update data
                        this.provincialProgressionChart.data.labels = cleanLabels;
                        this.provincialProgressionChart.data.datasets[0].data = cleanEnrolled;  // Placeholder
                        this.provincialProgressionChart.data.datasets[1].data = cleanProjected;  // Actual graduates
                        
                        // Trigger update with animation
                        this.provincialProgressionChart.update('active');
                        
                        // removed debug log
                    } else {
                        // removed warning
                        return;  // === PREVENT INFINITE RECURSION - Don't call init again ===
                    }
                    
                    // Update stats totals
                    this.progressionTotals = data.totals || { enrolled: 0, projected: 0 };
                    
                } catch (error) {
                    // removed error
                    alert(`Failed to load provincial progression data: ${error.message}`);
                }
            },

            async updateTrendChart() {
                // Full rebuild so dynamic height + animations always fire
                await this.buildEnrollmentTrendChart();
            }
        }));
    });
</script>

<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<!-- COMPLETE CHART FIX - Clean Chart Manager System -->
<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<script>
// ═══════════════════════════════════════════════════════════════════════════
// CHART MANAGER - Clean implementation without Alpine.js proxy issues
// ═══════════════════════════════════════════════════════════════════════════
(function() {
    'use strict';
    
    window.cleanChartManager = {
        charts: {},
        
        // Deep clone to remove ALL proxies
        clone(data) {
            if (!data) return null;
            try {
                return JSON.parse(JSON.stringify(data));
            } catch (e) {
                return null;
            }
        },
        
        // Safe array sanitization
        toNumbers(arr) {
            if (!arr) return [];
            const clean = this.clone(arr);
            return Array.isArray(clean) ? clean.map(n => Number(n) || 0) : [];
        },
        
        // Destroy a chart safely
        destroy(chartName) {
            if (this.charts[chartName]) {
                try {
                    this.charts[chartName].destroy();
                } catch (e) {
                    // removed warning
                }
                this.charts[chartName] = null;
            }
        },
        
        // Enrollment Trend Chart
        async enrollmentTrend(year, province) {
            // removed debug log
            
            const canvas = document.getElementById('enrollmentTrendChart');
            if (!canvas) {
                // removed error
                return { public: 0, private: 0, combined: 0 };
            }
            
            this.destroy('enrollmentTrend');
            
            try {
                const res = await fetch(`/api/discipline-enrollment/trend?year=${encodeURIComponent(year)}&province=${encodeURIComponent(province)}`);
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                
                const raw = await res.json();
                const labels = this.clone(raw.disciplines) || [];
                const publicData = this.toNumbers(raw.publicSchools);
                const privateData = this.toNumbers(raw.privateSchools);
                
                this.charts.enrollmentTrend = new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Public Schools',
                            data: publicData,
                            backgroundColor: 'rgba(37,99,235,0.8)',
                            borderColor: 'rgb(29,78,216)',
                            borderWidth: 0,
                            borderRadius: {
                                topLeft: 8,
                                topRight: 0,
                                bottomLeft: 8,
                                bottomRight: 0
                            },
                            borderSkipped: false
                        }, {
                            label: 'Private Schools',
                            data: privateData,
                            backgroundColor: 'rgba(125,211,252,0.8)',
                            borderColor: 'rgb(56,189,248)',
                            borderWidth: 0,
                            borderRadius: {
                                topLeft: 0,
                                topRight: 8,
                                bottomLeft: 0,
                                bottomRight: 8
                            },
                            borderSkipped: false
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { 
                            duration: 750,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: { 
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    font: { size: 11, weight: '600' }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: (c) => `${c.dataset.label}: ${c.parsed.x.toLocaleString()}`
                                }
                            }
                        },
                        scales: {
                            x: { 
                                stacked: true, 
                                beginAtZero: true,
                                grid: { color: 'rgba(148, 163, 184, 0.1)' },
                                ticks: {
                                    font: { size: 14, weight: 'bold' },
                                    color: '#1e293b'
                                }
                            },
                            y: { 
                                stacked: true, 
                                grid: { display: false },
                                ticks: {
                                    font: { size: 14, weight: 'bold' },
                                    color: '#1e293b',
                                    autoSkip: false,
                                    padding: 8
                                },
                                // Make bars wider (similar to Discipline Enrollment)
                                categoryPercentage: 0.6,  // Use 90% of space = less gap
                                barPercentage: 0.7        // Bar is 95% of category = wider bars
                            }
                        }
                    },
                    plugins: [{
                        id: 'valueLabels',
                        afterDatasetsDraw: function(chart) {
                            const ctx = chart.ctx;
                            
                            chart.data.datasets.forEach((dataset, datasetIndex) => {
                                const meta = chart.getDatasetMeta(datasetIndex);
                                
                                if (!meta || !meta.data || meta.data.length === 0) {
                                    return;
                                }
                                
                                ctx.save();
                                ctx.font = 'bold 12px Arial, sans-serif';
                                
                                meta.data.forEach((element, index) => {
                                    const value = dataset.data[index];
                                    
                                    if (value && value > 0) {
                                        const barWidth = Math.abs(element.x - element.base);
                                        const valueText = value.toLocaleString();
                                        const textWidth = ctx.measureText(valueText).width;
                                        
                                        // Only show label if there's enough space (at least 40px)
                                        if (barWidth > 40) {
                                            const centerX = element.base + (barWidth / 2);
                                            const y = element.y;
                                            
                                            // Draw value in center of bar segment
                                            ctx.textAlign = 'center';
                                            ctx.textBaseline = 'middle';
                                            
                                            // White text with black outline for contrast
                                            ctx.strokeStyle = '#000000';
                                            ctx.lineWidth = 3;
                                            ctx.strokeText(valueText, centerX, y);
                                            
                                            ctx.fillStyle = '#ffffff';
                                            ctx.fillText(valueText, centerX, y);
                                        }
                                    }
                                });
                                
                                ctx.restore();
                            });
                        }
                    }]
                });
                
                // removed debug log
                return raw.totals || { public: 0, private: 0, combined: 0 };
                
            } catch (e) {
                // removed error
                return { public: 0, private: 0, combined: 0 };
            }
        }
    };
    
    // removed debug log
})();
</script>

<script>
// ═══════════════════════════════════════════════════════════════════════════
// OVERRIDE Alpine.js chart functions to use the clean manager
// ═══════════════════════════════════════════════════════════════════════════
document.addEventListener('alpine:init', () => {
    // Wait for Alpine to be ready, then override the functions
    setTimeout(() => {
        const alpineComponent = Alpine.$data(document.querySelector('[x-data="licensureChartData()"]'));
        if (alpineComponent) {
            // removed debug log
            
            // initEnrollmentTrendChart and updateTrendChart now use buildEnrollmentTrendChart()
            // No override needed — cleanChartManager is no longer used for enrollment trend.
            
            // removed debug log
        } else {
            // removed error
        }
    }, 500);
});
</script>


    </body>

    </html>