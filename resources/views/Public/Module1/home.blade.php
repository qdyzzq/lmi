<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    @vite('resources/css/app.css')
    @vite('resources/js/public/home.js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <!-- Collapse plugin must be loaded (no defer) before Alpine core initializes -->
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.15.8/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>

    <title>Regional Statistics</title>

    <style>
        html {
            scroll-behavior: smooth;
        }

        /* ── Responsive chart containers ── */
        .chart-wrapper {
            position: relative;
            width: 100%;
        }

        /* Mobile: taller aspect so charts breathe */
        @media (max-width: 640px) {
            .chart-wrapper { aspect-ratio: 4 / 3; min-height: 220px; }
        }

        /* Tablet */
        @media (min-width: 641px) and (max-width: 1023px) {
            .chart-wrapper { aspect-ratio: 16 / 9; }
        }

        /* Desktop */
        @media (min-width: 1024px) {
            .chart-wrapper { height: 384px; } /* same as old h-96 */
        }

        /* Responsive filter dropdowns — prevent overflow on mobile */
        .filter-dropdown {
            position: fixed;
            width: min(320px, calc(100vw - 2rem));
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 20px 40px -8px rgb(0 0 0 / 0.18), 0 8px 16px -6px rgb(0 0 0 / 0.1);
            border: 1px solid #e2e8f0;
            z-index: 9998;
            padding: 1.25rem;
            max-height: min(400px, calc(100dvh - 10rem));
            overflow-y: auto;
        }

        /* Scroll hint for table on mobile */
        .table-scroll-hint {
            display: none;
        }
        @media (max-width: 767px) {
            .table-scroll-hint {
                display: flex;
            }
        }

        /* ── Employment table: hide table on mobile, show cards ── */
        .emp-table-view { display: block; }
        .emp-cards-view { display: none;  }
        @media (max-width: 767px) {
            .emp-table-view { display: none;  }
            .emp-cards-view { display: block; }
        }

        /* ── Employment mobile card styles ── */
        .emp-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 14px 16px;
            margin-bottom: 10px;
        }
        .emp-card-period {
            font-size: 0.9375rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 10px;
        }
        .emp-card-highlight {
            background: #eff6ff;
            border-radius: 0.5rem;
            padding: 10px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .emp-card-highlight-label {
            font-size: 10px;
            font-weight: 700;
            color: #1d4ed8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin: 0 0 2px;
        }
        .emp-card-highlight-value {
            font-size: 22px;
            font-weight: 700;
            color: #1e40af;
            margin: 0;
        }
        .emp-card-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 0;
        }
        .emp-card-stat {
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 8px 10px;
        }
        .emp-card-stat-label {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin: 0 0 2px;
            line-height: 1.3;
        }
        .emp-card-stat-value {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        .emp-card-expand-btn {
            margin-top: 8px;
            width: 100%;
            padding: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            background: transparent;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            transition: background 0.12s;
        }
        .emp-card-expand-btn:hover { background: #f1f5f9; }
        .emp-card-extra {
            display: none;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 8px;
        }
        .emp-card-extra.open { display: grid; }

        /* ── Toast Notifications ── */
        #toast-container {
            position: fixed;
            top: 1.25rem;
            right: 1.25rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            pointer-events: none;
        }
        .toast {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            min-width: 280px;
            max-width: 380px;
            background: #fff;
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15), 0 4px 10px -5px rgba(0,0,0,0.1);
            border-left: 4px solid;
            pointer-events: all;
            animation: toastIn 0.35s cubic-bezier(0.21, 1.02, 0.73, 1) forwards;
        }
        .toast.removing { animation: toastOut 0.3s ease-in forwards; }
        .toast.toast-error   { border-color: #ef4444; }
        .toast.toast-warning { border-color: #f59e0b; }
        .toast.toast-success { border-color: #22c55e; }
        .toast.toast-info    { border-color: #3b82f6; }
        .toast-icon { flex-shrink: 0; width: 1.25rem; height: 1.25rem; margin-top: 1px; }
        .toast-error   .toast-icon { color: #ef4444; }
        .toast-warning .toast-icon { color: #f59e0b; }
        .toast-success .toast-icon { color: #22c55e; }
        .toast-info    .toast-icon { color: #3b82f6; }
        .toast-body { flex: 1; }
        .toast-title { font-size: 0.8125rem; font-weight: 700; color: #0f172a; margin-bottom: 2px; }
        .toast-message { font-size: 0.8125rem; color: #475569; line-height: 1.4; }
        .toast-close { background: none; border: none; cursor: pointer; color: #94a3b8; padding: 0; flex-shrink: 0; line-height: 1; }
        .toast-close:hover { color: #475569; }
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(100%); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes toastOut {
            from { opacity: 1; transform: translateX(0); }
            to   { opacity: 0; transform: translateX(110%); }
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen">

    <!-- Toast Notification Container -->
    <div id="toast-container" aria-live="polite" aria-atomic="true"></div>

    
    <div x-data="{
        activeView: 'overview',
        showReportModal: false,
        showLmiMatrix: false,
        sidebarExpanded: true,
        mobileMenuOpen: false
    }" class="w-full h-full">
        @include('partials.navbar')

        <div class="relative w-full h-[500px] md:h-[700px] lg:h-[900px] overflow-hidden">
            <div class="absolute inset-0">
                <img src="{{ asset('images/JobFair.webp') }}" alt="Background"
                    class="w-full h-full object-cover object-center">
                <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/40 to-slate-100"></div>
            </div>

            <div class="relative z-10 h-full flex items-center justify-center px-4">
            <div class="text-center text-white pointer-events-none">
                <h1 class="text-white font-black leading-tight tracking-tight"
                    style="font-size: clamp(1.25rem, 4vw, 3.5rem); text-shadow: 0 2px 16px rgba(0,0,0,1), 0 0 40px rgba(0,0,0,0.7);">
                    Davao Regional Labor Market Situation
                </h1>
                <p class="text-slate-200 font-medium mt-2"
                    style="font-size: clamp(0.75rem, 1.5vw, 1.125rem); text-shadow: 0 1px 8px rgba(0,0,0,1);">
                    Regional Labor Market Information & Trends
                </p>
            </div>
        </div>
            <!-- Scroll Indicator -->
            <div class="absolute bottom-6 sm:bottom-16 md:bottom-24 lg:bottom-32 left-1/2 transform -translate-x-1/2 z-20 animate-bounce"
                 x-data="{ menuOpen: false }"
                 @menu-toggled.window="menuOpen = $event.detail.open"
                 :class="{ 'opacity-0 pointer-events-none': menuOpen }">
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

        <div class="flex-1 flex flex-col overflow-y-auto mt-10 relative z-30">
            <div x-show="activeView === 'overview'" x-transition>
                <div class="space-y-6 mx-2 my-3 sm:m-5">
                    <div x-data="kpiPeriodFilter()" class="pt-10 relative z-20" id="kpi-section">
                        <div class="max-w-7xl mx-auto px-2 sm:px-6 space-y-6">
                            <!-- Period Header KPI Cards -->
                            <div
                                class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-xl px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-lg">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-white font-bold text-lg">Labor Force Survey</h3>
                                        <p class="text-slate-300 text-sm" x-text="selectedPeriodLabel">Loading...</p>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                                <div
                                    class="group relative bg-white/95 backdrop-blur-sm border-l-4 border-[#023E8A] rounded-xl p-6 shadow-xl hover:shadow-2xl hover:scale-105 hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                                    <!-- Bubble decorations -->
                                    <div class="absolute -top-6 -right-6 w-28 h-28 bg-blue-100/60 rounded-full pointer-events-none"></div>
                                    <div class="absolute -top-2 -right-2 w-16 h-16 bg-blue-200/40 rounded-full pointer-events-none"></div>
                                    <div class="flex justify-between items-center mb-3 relative z-10">
                                        <p class="text-xs text-slate-600 font-bold uppercase tracking-wide">
                                            Participation Rate</p>
                                        <div
                                            class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center group-hover:bg-[#023E8A] transition-colors">
                                            <svg class="w-6 h-6 text-[#023E8A] group-hover:text-white transition-colors"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <h2 class="text-4xl font-black text-slate-900 mb-2 relative z-10"
                                        x-text="kpiData.participation_rate?.rate || '0%'">67.0%</h2>
                                    <div class="h-1 w-16 bg-gradient-to-r from-[#023E8A] to-blue-300 rounded-full mb-3 relative z-10">
                                    </div>
                                    <p class="text-xs text-slate-600 font-medium relative z-10"
                                        x-text="(kpiData.participation_rate?.active_workforce || '0') + ' Estimate number of people'">
                                    </p>
                                </div>

                                <div
                                    class="group relative bg-white/95 backdrop-blur-sm border-l-4 border-[#006400] rounded-xl p-6 shadow-xl hover:shadow-2xl hover:scale-105 hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                                    <!-- Bubble decorations -->
                                    <div class="absolute -top-6 -right-6 w-28 h-28 bg-green-100/60 rounded-full pointer-events-none"></div>
                                    <div class="absolute -top-2 -right-2 w-16 h-16 bg-green-200/40 rounded-full pointer-events-none"></div>
                                    <div class="flex justify-between items-center mb-3 relative z-10">
                                        <p class="text-xs text-slate-600 font-bold uppercase tracking-wide">Employment
                                            Rate</p>
                                        <div
                                            class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center group-hover:bg-[#006400] transition-colors">
                                            <svg class="w-6 h-6 text-[#006400] group-hover:text-white transition-colors"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <h2 class="text-4xl font-black text-slate-900 mb-2 relative z-10"
                                        x-text="kpiData.employment_rate?.rate || '0%'">90.0%</h2>
                                    <div
                                        class="h-1 w-16 bg-gradient-to-r from-[#006400] to-green-300 rounded-full mb-3 relative z-10">
                                    </div>
                                    <p class="text-xs text-slate-600 font-medium relative z-10"
                                        x-text="(kpiData.employment_rate?.count || '0') + ' Estimate number of people'">
                                    </p>
                                </div>

                                <div
                                    class="group relative bg-white/95 backdrop-blur-sm border-l-4 border-[#FF8C00] rounded-xl p-6 shadow-xl hover:shadow-2xl hover:scale-105 hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                                    <!-- Bubble decorations -->
                                    <div class="absolute -top-6 -right-6 w-28 h-28 bg-orange-100/60 rounded-full pointer-events-none"></div>
                                    <div class="absolute -top-2 -right-2 w-16 h-16 bg-orange-200/40 rounded-full pointer-events-none"></div>
                                    <div class="flex justify-between items-center mb-3 relative z-10">
                                        <p class="text-xs text-slate-600 font-bold uppercase tracking-wide">
                                            Underemployment</p>
                                        <div
                                            class="w-10 h-10 bg-orange-50 rounded-full flex items-center justify-center group-hover:bg-[#FF8C00] transition-colors">
                                            <svg class="w-6 h-6 text-[#FF8C00] group-hover:text-white transition-colors"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <h2 class="text-4xl font-black text-slate-900 mb-2 relative z-10"
                                        x-text="kpiData.underemployment_rate?.rate || '0%'">67.0%</h2>
                                    <div
                                        class="h-1 w-16 bg-gradient-to-r from-[#FF8C00] to-orange-300 rounded-full mb-3 relative z-10">
                                    </div>
                                    <p class="text-xs text-slate-600 font-medium relative z-10"
                                        x-text="(kpiData.underemployment_rate?.count_formatted || '0') + ' seeking more hours'">
                                    </p>
                                </div>

                                <div
                                    class="group relative bg-white/95 backdrop-blur-sm border-l-4 border-[#D30000] rounded-xl p-6 shadow-xl hover:shadow-2xl hover:scale-105 hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                                    <!-- Bubble decorations -->
                                    <div class="absolute -top-6 -right-6 w-28 h-28 bg-red-100/60 rounded-full pointer-events-none"></div>
                                    <div class="absolute -top-2 -right-2 w-16 h-16 bg-red-200/40 rounded-full pointer-events-none"></div>
                                    <div class="flex justify-between items-center mb-3 relative z-10">
                                        <p class="text-xs text-slate-600 font-bold uppercase tracking-wide">
                                            Unemployment
                                        </p>
                                        <div
                                            class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center group-hover:bg-[#D30000] transition-colors">
                                            <svg class="w-6 h-6 text-[#D30000] group-hover:text-white transition-colors"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                            </svg>
                                        </div>
                                    </div>
                                    <h2 class="text-4xl font-black text-slate-900 mb-2 relative z-10"
                                        x-text="kpiData.unemployment_rate?.rate || '0%'">7.0%</h2>
                                    <div class="h-1 w-16 bg-gradient-to-r from-[#D30000] to-red-300 rounded-full mb-3 relative z-10">
                                    </div>
                                    <p class="text-xs text-slate-600 font-medium relative z-10"
                                        x-text="(kpiData.unemployment_rate?.count_formatted || '0') + ' Estimate number of people'">
                                    </p>
                                </div>

                            </div>

                            <!--  ANALYSIS BOX -->
                            <div class="bg-white border border-slate-200 rounded-xl shadow-lg overflow-visible">
                                <div
                                    class="bg-gradient-to-r from-slate-900 via-slate-800 rounded-t-xl to-slate-900 px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-white font-bold text-lg">
                                                    Labor Force Analysis
                                                </h3>
                                                <p class="text-slate-300 text-sm" x-text="selectedPeriodLabel">
                                                    Loading...</p>
                                            </div>
                                        </div>

                                        <div class="relative" x-data="{ open: false }" @click.stop>
                                            <button @click="open = !open; if(open) positionDropdown($el, 'kpi-period-dropdown')"
                                                id="kpi-period-btn"
                                                class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white text-sm font-medium rounded-lg flex items-center gap-2 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span>Change Period</span>
                                            </button>

                                            <div x-show="open" @click.away="open = false" x-transition
                                                id="kpi-period-dropdown"
                                                class="filter-dropdown">
                                                <label class="block text-xs font-semibold text-slate-700 mb-3">Select
                                                    Period</label>

                                                <div class="grid grid-cols-2 gap-3 mb-4">
                                                    <div>
                                                        <label
                                                            class="text-[10px] text-slate-500 mb-1 block">Quarter</label>
                                                        <select x-model="pendingMonth"
                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                                            <template x-for="month in availableMonths" :key="month">
                                                                <option :value="month.toString()" x-text="{1:'Jan',4:'Apr',7:'Jul',10:'Oct'}[month] ?? month"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-[10px] text-slate-500 mb-1 block">Year</label>
                                                        <select x-model="pendingYear"
                                                            @change="updateAvailableMonths()"
                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                                            <option value="" disabled>Select year</option>
                                                            <template x-for="year in availableYears"
                                                                :key="year">
                                                                <option :value="year" x-text="year"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </div>

                                                <button @click="applyPeriodFilter(); open = false;"
                                                    class="w-full bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 hover:from-slate-800 hover:via-slate-700 hover:to-slate-800 text-white text-sm py-2 px-4 rounded-lg font-medium transition">
                                                    Apply Filter
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6">
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div x-show="analysis.lfpr"
                                            class="group bg-white rounded-lg p-5 border border-[#023E8A]/20 border-l-4 hover:border-[#023E8A] hover:bg-blue-50/30 shadow-sm hover:shadow-md transition-all cursor-pointer">
                                            <div class="flex items-center gap-2 mb-3">
                                                <div
                                                    class="w-8 h-8 bg-blue-50 group-hover:bg-[#023E8A] rounded-lg flex items-center justify-center transition-colors">
                                                    <svg class="w-4 h-4 text-[#023E8A] group-hover:text-white"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                </div>
                                                <p class="text-xs font-bold text-[#023E8A] uppercase tracking-wide">
                                                    Participation Rate</p>
                                            </div>
                                            <div x-html="analysis.lfpr"
                                                class="text-sm text-slate-700 leading-relaxed"></div>
                                        </div>

                                        <div x-show="analysis.employment"
                                            class="group bg-white rounded-lg p-5 border border-[#006400]/20 border-l-4 hover:border-[#006400] hover:bg-green-50/30 shadow-sm hover:shadow-md transition-all cursor-pointer">
                                            <div class="flex items-center gap-2 mb-3">
                                                <div
                                                    class="w-8 h-8 bg-green-50 group-hover:bg-[#006400] rounded-lg flex items-center justify-center transition-colors">
                                                    <svg class="w-4 h-4 text-[#006400] group-hover:text-white"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <p class="text-xs font-bold text-[#006400] uppercase tracking-wide">
                                                    Employment Rate</p>
                                            </div>
                                            <div x-html="analysis.employment"
                                                class="text-sm text-slate-700 leading-relaxed"></div>
                                        </div>

                                        <div x-show="analysis.underemployment"
                                            class="group bg-white rounded-lg p-5 border border-[#FF8C00]/20 border-l-4 hover:border-[#FF8C00] hover:bg-orange-50/30 shadow-sm hover:shadow-md transition-all cursor-pointer">
                                            <div class="flex items-center gap-2 mb-3">
                                                <div
                                                    class="w-8 h-8 bg-orange-50 group-hover:bg-[#FF8C00] rounded-lg flex items-center justify-center transition-colors">
                                                    <svg class="w-4 h-4 text-[#FF8C00] group-hover:text-white"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <p class="text-xs font-bold text-[#FF8C00] uppercase tracking-wide">
                                                    Underemployment Rate</p>
                                            </div>
                                            <div x-html="analysis.underemployment"
                                                class="text-sm text-slate-700 leading-relaxed"></div>
                                        </div>

                                        <div x-show="analysis.unemployment"
                                            class="group bg-white rounded-lg p-5 border border-[#D30000]/20 border-l-4 hover:border-[#D30000] hover:bg-red-50/30 shadow-sm hover:shadow-md transition-all cursor-pointer">
                                            <div class="flex items-center gap-2 mb-3">
                                                <div
                                                    class="w-8 h-8 bg-red-50 group-hover:bg-[#D30000] rounded-lg flex items-center justify-center transition-colors">
                                                    <svg class="w-4 h-4 text-[#D30000] group-hover:text-white"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                                    </svg>
                                                </div>
                                                <p class="text-xs font-bold text-[#D30000] uppercase tracking-wide">
                                                    Unemployment Rate</p>
                                            </div>
                                            <div x-html="analysis.unemployment"
                                                class="text-sm text-slate-700 leading-relaxed"></div>
                                        </div>
                                    </div>
                                    <!-- Loading indicator -->
                                    <p x-show="loading"
                                        class="text-xs text-slate-500 animate-pulse mt-4 text-center flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span>Calculating comparison with previous year data...</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-connector"></div>

                    <script>
                        Chart.register(ChartDataLabels);
                    </script>
                    <!-- Charts Section -->
                    <div x-data="{ ...chartFilters(), activeChart: 'labor', chartsExpanded: false }" class="max-w-7xl mx-auto px-6 space-y-4 mt-6">
                        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

                            <!-- Dark Header with Icon -->
                            <div @click="chartsExpanded = !chartsExpanded"
                                class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 px-6 py-4 cursor-pointer hover:from-slate-700 hover:to-slate-700 transition flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-white text-lg">Detailed Visualizations</h3>
                                        <p class="text-xs text-slate-300">Toggle between workforce comparisons and
                                            indicator trends</p>
                                    </div>
                                </div>
                                <button class="text-white hover:text-slate-300 transition">
                                    <svg class="w-5 h-5 transform transition-transform"
                                        :class="chartsExpanded ? 'rotate-180' : ''" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>

                            <div x-show="chartsExpanded" x-collapse class="border-t border-slate-200">
                                <div class="p-3 sm:p-6 space-y-4">

                                    <!-- Centered Tab Navigation — pill-style -->
                                    <div class="flex flex-wrap items-center justify-center gap-1 sm:gap-2 p-1 bg-slate-100 rounded-xl border border-slate-200">
                                        <button
                                            @click="activeChart = 'side'; $nextTick(() => { window.laborChart?.resize(); window.unempChart?.resize(); })"
                                            :class="activeChart === 'side'
                                                ? 'bg-white text-slate-900 shadow font-semibold border border-slate-200'
                                                : 'text-slate-500 hover:text-slate-800 hover:bg-white/60'"
                                            class="px-3 sm:px-5 py-2 text-xs sm:text-sm rounded-lg transition-all duration-200 cursor-pointer whitespace-nowrap">
                                            Overview
                                        </button>
                                        <button
                                            @click="activeChart = 'labor'; $nextTick(() => { window.laborChart?.resize(); window.laborChart?.update(); })"
                                            :class="activeChart === 'labor'
                                                ? 'bg-white text-blue-700 shadow font-semibold border border-blue-100'
                                                : 'text-slate-500 hover:text-slate-800 hover:bg-white/60'"
                                            class="px-3 sm:px-5 py-2 text-xs sm:text-sm rounded-lg transition-all duration-200 cursor-pointer whitespace-nowrap">
                                            Labor Force &amp; Employment
                                        </button>
                                        <button
                                            @click="activeChart = 'compiled'; $nextTick(() => { window.unempChart?.resize(); window.unempChart?.update(); })"
                                            :class="activeChart === 'compiled'
                                                ? 'bg-white text-blue-700 shadow font-semibold border border-blue-100'
                                                : 'text-slate-500 hover:text-slate-800 hover:bg-white/60'"
                                            class="px-3 sm:px-5 py-2 text-xs sm:text-sm rounded-lg transition-all duration-200 cursor-pointer text-center leading-snug">
                                            DAVAO REGION<br>LABOR MARKET PERFORMANCE
                                        </button>
                                    </div>

                                    <!-- Charts Container -->
                                    <div
                                        :class="activeChart === 'side' ? 'grid grid-cols-1 lg:grid-cols-2 gap-6' :
                                            'grid grid-cols-1 gap-6'">

                                        <!-- Labor Force Chart -->
                                        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-6 shadow-sm relative"
                                            x-show="activeChart === 'labor' || activeChart === 'side'" x-transition>
                                            <div class="flex flex-col gap-3 mb-4">
                                                <div>
                                                    <h3 class="font-semibold text-slate-800 text-base">Labor Force vs Employment Rate</h3>
                                                    <p class="text-xs text-slate-500 mt-0.5">Comparing workforce size (bars) vs employment rate (line)</p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button @click="openChartModal('labor')"
                                                        class="text-xs bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3 py-2 rounded-lg flex items-center gap-2 transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                                        </svg>
                                                        Expand
                                                    </button>
                                                    <div class="relative">
                                                        <button @click="laborOpen = !laborOpen; if(laborOpen) positionDropdown($el, 'labor-dropdown')"
                                                            id="labor-filter-btn"
                                                            class="text-xs bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3 py-2 rounded-lg flex items-center gap-2 transition">
                                                            <span x-text="laborYearRange" class="whitespace-nowrap"></span>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </button>
                                                        <div x-show="laborOpen" @click.away="laborOpen = false"
                                                            x-transition id="labor-dropdown" class="filter-dropdown">
                                                            <div class="mb-4">
                                                                <label class="block text-xs font-semibold text-slate-700 mb-3">Select Year Range</label>
                                                                <div class="flex items-center gap-3">
                                                                    <div class="flex-1">
                                                                        <label class="text-[10px] text-slate-500 mb-1 block">From</label>
                                                                        <select x-model="laborStartYear"
                                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                            <option value="" disabled>Select year</option>
                                                                            <template x-for="year in laborAvailableYears" :key="year">
                                                                                <option :value="year" x-text="year"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                    <span class="text-slate-400 mt-5">—</span>
                                                                    <div class="flex-1">
                                                                        <label class="text-[10px] text-slate-500 mb-1 block">To</label>
                                                                        <select x-model="laborEndYear"
                                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                            <option value="" disabled>Select year</option>
                                                                            <template x-for="year in laborAvailableYears" :key="year">
                                                                                <option :value="year" x-text="year"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="flex items-center gap-3 mt-3">
                                                                    <div class="flex-1">
                                                                        <label
                                                                            class="text-[10px] text-slate-500 mb-1 block">Quarter
                                                                            (From)</label>
                                                                        <select x-model="laborStartQuarter"
                                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                            <option value="Q1">Jan</option>
                                                                            <option value="Q2">Apr</option>
                                                                            <option value="Q3">Jul</option>
                                                                            <option value="Q4">Oct</option>
                                                                        </select>
                                                                    </div>
                                                                    <span class="text-slate-400 mt-5">—</span>
                                                                    <div class="flex-1">
                                                                        <label
                                                                            class="text-[10px] text-slate-500 mb-1 block">Quarter
                                                                            (To)</label>
                                                                        <select x-model="laborEndQuarter"
                                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                            <option value="Q1">Jan</option>
                                                                            <option value="Q2">Apr</option>
                                                                            <option value="Q3">Jul</option>
                                                                            <option value="Q4">Oct</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button @click="applyLaborFilter()"
                                                                class="w-full bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 hover:from-slate-800 hover:via-slate-700 hover:to-slate-800 text-white text-sm py-2 px-4 rounded-lg font-bold transition">
                                                                Apply Filter
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chart-wrapper">
                                                <canvas id="laborEmploymentChart"></canvas>
                                            </div>
                                            <!-- Mobile tip -->
                                            <p class="block sm:hidden text-center text-xs text-slate-400 mt-2 italic">Tap "Expand" above for a full-screen view</p>
                                        </div>

                                        <!-- Compiled Indicators Chart -->
                                        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-6 shadow-sm relative"
                                            x-show="activeChart === 'compiled' || activeChart === 'side'" x-transition>
                                            <div class="flex flex-col gap-3 mb-4">
                                                <div>
                                                    <h3 class="font-semibold text-slate-800 text-base">DAVAO REGION LABOR MARKET PERFORMANCE</h3>
                                                    <p class="text-xs text-slate-500 mt-0.5">Key Employment Indicators</p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button @click="openChartModal('unemployment')"
                                                        class="text-xs bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3 py-2 rounded-lg flex items-center gap-2 transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                                        </svg>
                                                        Expand
                                                    </button>
                                                    <div class="relative">
                                                        <button @click="unempOpen = !unempOpen; if(unempOpen) positionDropdown($el, 'unemp-dropdown')"
                                                            id="unemp-filter-btn"
                                                            class="text-xs bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3 py-2 rounded-lg flex items-center gap-2 transition">
                                                            <span x-text="unempYearRange" class="whitespace-nowrap"></span>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </button>
                                                        <div x-show="unempOpen" @click.away="unempOpen = false"
                                                            x-transition id="unemp-dropdown" class="filter-dropdown">
                                                            <div class="mb-4">
                                                                <label class="block text-xs font-semibold text-slate-700 mb-3">Select Year Range</label>
                                                                <div class="flex items-center gap-3">
                                                                    <div class="flex-1">
                                                                        <label class="text-[10px] text-slate-500 mb-1 block">From</label>
                                                                        <select x-model="unempStartYear"
                                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                            <option value="" disabled>Select year</option>
                                                                            <template x-for="year in unempAvailableYears" :key="year">
                                                                                <option :value="year" x-text="year"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                    <span class="text-slate-400 mt-5">—</span>
                                                                    <div class="flex-1">
                                                                        <label class="text-[10px] text-slate-500 mb-1 block">To</label>
                                                                        <select x-model="unempEndYear"
                                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                            <option value="" disabled>Select year</option>
                                                                            <template x-for="year in unempAvailableYears" :key="year">
                                                                                <option :value="year" x-text="year"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="flex items-center gap-3 mt-3">
                                                                    <div class="flex-1">
                                                                        <label
                                                                            class="text-[10px] text-slate-500 mb-1 block">Quarter
                                                                            (From)</label>
                                                                        <select x-model="unempStartQuarter"
                                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                            <option value="Q1">Jan</option>
                                                                            <option value="Q2">Apr</option>
                                                                            <option value="Q3">Jul</option>
                                                                            <option value="Q4">Oct</option>
                                                                        </select>
                                                                    </div>
                                                                    <span class="text-slate-400 mt-5">—</span>
                                                                    <div class="flex-1">
                                                                        <label
                                                                            class="text-[10px] text-slate-500 mb-1 block">Quarter
                                                                            (To)</label>
                                                                        <select x-model="unempEndQuarter"
                                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                            <option value="Q1">Jan</option>
                                                                            <option value="Q2">Apr</option>
                                                                            <option value="Q3">Jul</option>
                                                                            <option value="Q4">Oct</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button @click="applyUnempFilter()"
                                                                class="w-full bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 hover:from-slate-800 hover:via-slate-700 hover:to-slate-800 text-white text-sm py-2 px-4 rounded-lg font-bold transition">
                                                                Apply Filter
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chart-wrapper">
                                                <canvas id="unemploymentChart"></canvas>
                                            </div>
                                            <!-- Mobile tip -->
                                            <p class="block sm:hidden text-center text-xs text-slate-400 mt-2 italic">Tap "Expand" above for a full-screen view</p>
                                        </div>

                                        <!-- Modal -->
                                        <div x-show="expandedChart !== null"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                            @keydown.escape.window="closeChartModal()"
                                            class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                            <div class="fixed inset-0 bg-black/60 bg-opacity-25 transition-opacity"
                                                @click="closeChartModal()"></div>
                                            <div class="flex min-h-screen items-center justify-center p-4">
                                                <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-10xl max-h-[95vh] overflow-hidden"
                                                    @click.stop>
                                                    <div
                                                        class="flex items-center  justify-between p-6 border-b border-gray-200">
                                                        <div>
                                                            <h3 class="text-xl font-semibold text-slate-800"
                                                                x-text="expandedChart === 'labor' ? 'Labor Force vs Employment Rate' : 'DAVAO REGION LABOR MARKET PERFORMANCE'">
                                                            </h3>
                                                            <p class="text-sm text-slate-500 mt-1"
                                                                x-text="expandedChart === 'labor' ? 'Comparing workforce size (bars) vs employment rate (line)' : 'Key Employment Indicators'">
                                                            </p>
                                                        </div>
                                                        <button @click="closeChartModal()"
                                                            class="text-slate-400 hover:text-slate-600 transition">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <div class="p-6">
                                                        <div class="relative w-full" style="height: clamp(280px, 60vh, 600px);">
                                                            <canvas id="expandedChart"></canvas>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="p-6 border-t border-gray-200 bg-slate-50 flex justify-between items-center">
                                                        <p class="text-xs text-slate-500">Press ESC or click outside to
                                                            close</p>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Table Section -->
                        <div x-data="statsFilter()" class="mt-6">
                            <!-- Section divider -->
                            <div class="flex items-center gap-3 mb-3 px-1">
                                <div class="flex-1 h-px bg-slate-300"></div>
                                <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Data Table</span>
                                <div class="flex-1 h-px bg-slate-300"></div>
                            </div>
                            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

                                <!-- Dark Header with Icon -->
                                <div @click="tableExpanded = !tableExpanded"
                                    class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 px-6 py-4 cursor-pointer hover:from-slate-700 hover:to-slate-700 transition flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-white text-lg">Consolidated Regional Employment
                                                Statistics</h3>
                                            <p class="text-xs text-slate-300">Summary of Key Employment Indicators
                                                <span class="italic text-slate-400">In thousands</span>
                                            </p>
                                        </div>
                                    </div>
                                    <button class="text-white hover:text-slate-300 transition">
                                        <svg class="w-5 h-5 transform transition-transform"
                                            :class="tableExpanded ? 'rotate-180' : ''" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                </div>

                                <div x-show="tableExpanded" x-collapse>
                                    <!-- Controls -->
                                    <div
                                        class="p-5 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-end gap-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open; if(open) positionDropdown($el, 'table-year-dropdown')"
                                                    id="table-year-btn"
                                                    class="text-xs bg-white hover:bg-slate-50 border border-slate-300 px-4 py-2 rounded-lg flex items-center gap-2 transition shadow-sm">
                                                    <span x-text="displayRange"
                                                        class="font-medium text-slate-700"></span>
                                                    <svg class="w-4 h-4 text-slate-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.away="open = false" x-transition
                                                    id="table-year-dropdown"
                                                    class="filter-dropdown">
                                                    <label class="block text-xs font-semibold text-slate-700 mb-3">Select Year Range</label>
                                                    <div class="flex items-center gap-3 mb-4">
                                                        <div class="flex-1">
                                                            <label class="text-[10px] text-slate-500 mb-1 block">From</label>
                                                            <select x-model="startYear"
                                                                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                <option value="" disabled>Select year</option>
                                                                <template x-for="year in availableYears" :key="year">
                                                                    <option :value="year" x-text="year"></option>
                                                                </template>
                                                            </select>
                                                        </div>
                                                        <span class="text-slate-400 mt-5">—</span>
                                                        <div class="flex-1">
                                                            <label class="text-[10px] text-slate-500 mb-1 block">To</label>
                                                            <select x-model="endYear"
                                                                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                <option value="" disabled>Select year</option>
                                                                <template x-for="year in availableYears" :key="year">
                                                                    <option :value="year" x-text="year"></option>
                                                                </template>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <button @click="applyFilter(); open = false;"
                                                        class="w-full bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 hover:from-slate-800 hover:via-slate-700 hover:to-slate-800 text-white text-sm py-2 px-4 rounded-lg font-bold transition">
                                                        Apply Filter
                                                    </button>
                                                </div>
                                            </div>
                                            <button @click="exportCSV()"
                                                class="flex items-center gap-2 text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 px-4 py-2 rounded-lg hover:bg-blue-100 transition shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Export CSV
                                            </button>
                                        </div>
                                    </div>
                                    {{-- ── MOBILE CARD VIEW (shown on < 768px) ── --}}
                                    <div class="emp-cards-view px-4 py-4 bg-gray-50 space-y-0" style="max-height: 500px; overflow-y: auto;">
                                        <template x-for="stat in filteredData" :key="'card-'+stat.period">
                                            <div class="emp-card">
                                                {{-- Period --}}
                                                <p class="emp-card-period">
                                                    <span x-text="formatPeriod(stat.period).month"></span>
                                                    <span class="text-slate-500 text-sm font-semibold" x-text="formatPeriod(stat.period).year"></span>
                                                </p>
                                                {{-- Highlight: Employment Rate + Labor Force --}}
                                                <div class="emp-card-highlight">
                                                    <div>
                                                        <p class="emp-card-highlight-label">Employment rate</p>
                                                        <p class="emp-card-highlight-value" x-text="formatRate(stat.emp_rate)"></p>
                                                    </div>
                                                    <div style="text-align:right">
                                                        <p class="emp-card-highlight-label">Labor force</p>
                                                        <p style="font-size:18px;font-weight:700;color:#1e40af;margin:0" x-text="formatNumber(stat.labor_force)"></p>
                                                    </div>
                                                </div>
                                                {{-- Primary stats: raw counts always visible --}}
                                                <div class="emp-card-grid">
                                                    <div class="emp-card-stat">
                                                        <p class="emp-card-stat-label">Employed</p>
                                                        <p class="emp-card-stat-value" x-text="formatNumber(stat.employed)"></p>
                                                    </div>
                                                    <div class="emp-card-stat">
                                                        <p class="emp-card-stat-label">Unemployed</p>
                                                        <p class="emp-card-stat-value" x-text="stat.unemployed ?? '—'"></p>
                                                    </div>
                                                </div>
                                                <button class="emp-card-expand-btn"
                                                    onclick="const extra = this.nextElementSibling; extra.classList.toggle('open'); this.querySelector('span').textContent = extra.classList.contains('open') ? 'Hide details' : 'More details'; this.querySelector('svg').style.transform = extra.classList.contains('open') ? 'rotate(180deg)' : '';">
                                                    <svg style="width:12px;height:12px;transition:transform 0.2s" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                    <span>More details</span>
                                                </button>
                                                <div class="emp-card-extra">
                                                    <div class="emp-card-stat">
                                                        <p class="emp-card-stat-label">Underemployed</p>
                                                        <p class="emp-card-stat-value" x-text="stat.underemployed ?? '—'"></p>
                                                    </div>
                                                    <div class="emp-card-stat">
                                                        <p class="emp-card-stat-label">Unemployment rate</p>
                                                        <p class="emp-card-stat-value" x-text="formatRate(stat.unemp_rate)"></p>
                                                    </div>
                                                    <div class="emp-card-stat">
                                                        <p class="emp-card-stat-label">Underemployment rate</p>
                                                        <p class="emp-card-stat-value" x-text="formatRate(stat.underemp_rate)"></p>
                                                    </div>
                                                    <div class="emp-card-stat">
                                                        <p class="emp-card-stat-label">Participation rate</p>
                                                        <p class="emp-card-stat-value" x-text="formatRate(stat.particip_rate)"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- ── DESKTOP TABLE VIEW (hidden on < 768px) ── --}}
                                    <div class="emp-table-view">
                                    <div class="table-scroll-hint items-center gap-2 px-5 py-2 bg-blue-50 border-b border-blue-100 text-xs text-blue-600 font-medium">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                        Scroll horizontally to see all columns
                                    </div>
                                    <!-- Table -->
                                    <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                                        <table class="w-full min-w-[640px] text-sm border-collapse">
                                            <thead class="sticky top-0">
                                                <tr class="bg-slate-50 border-b border-slate-200">
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider border-r border-slate-200">
                                                        Period</th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider border-r border-slate-200">
                                                        Labor Force</th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider border-r border-slate-200">
                                                        Employed</th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider border-r border-slate-200">
                                                        Underemployed</th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider border-r border-slate-200">
                                                        Unemployed</th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider border-r border-slate-200">
                                                        Employment Rate</th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider border-r border-slate-200">
                                                        Underemployment Rate</th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider border-r border-slate-200">
                                                        Unemployment Rate</th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider">
                                                        Participation Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="stat in filteredData" :key="stat.period">
                                                    <tr
                                                        class="border-b border-slate-100 hover:bg-blue-50/30 transition-colors group">
                                                        <td
                                                            class="px-4 py-3 font-bold text-slate-900 text-center bg-slate-50/50 border-r border-slate-100">
                                                            <div class="flex flex-col leading-tight">
                                                                <span class="text-sm"
                                                                    x-text="formatPeriod(stat.period).month"></span>
                                                                <span class="text-xs text-slate-500 font-semibold"
                                                                    x-text="formatPeriod(stat.period).year"></span>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3 text-center text-slate-700 border-r border-slate-100"
                                                            x-text="formatNumber(stat.labor_force)"></td>
                                                        <td class="px-4 py-3 text-center text-slate-700 border-r border-slate-100"
                                                            x-text="formatNumber(stat.employed)"></td>
                                                        <td class="px-4 py-3 text-center text-slate-700 border-r border-slate-100"
                                                            x-text="stat.underemployed"></td>
                                                        <td class="px-4 py-3 text-center text-slate-700 border-r border-slate-100"
                                                            x-text="stat.unemployed"></td>
                                                        <td class="px-4 py-3 text-center text-base font-black bg-blue-50 text-blue-900 border-r border-blue-100"
                                                            x-text="formatRate(stat.emp_rate)"></td>
                                                        <td class="px-4 py-3 text-center text-slate-700 border-r border-slate-100"
                                                            x-text="formatRate(stat.underemp_rate)"></td>
                                                        <td class="px-4 py-3 text-center text-slate-700 border-r border-slate-100"
                                                            x-text="formatRate(stat.unemp_rate)"></td>
                                                        <td class="px-4 py-3 text-center text-slate-700"
                                                            x-text="formatRate(stat.particip_rate)"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>{{-- end emp-table-view --}}

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Footer -->
                <div class="p-4 bg-slate-50 border-t border-slate-200 text-center">
                    <p class="text-xs text-slate-500">Source: Philippine Statistics Authority; Labor
                        Force Survey</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Regional stats passed to JS --}}
    <script>
        window.AppData = {
            regionalStats: @json($regionalStats ?? [])
        };
    </script>
    @vite('resources/js/public/home.js')
   

</body>

</html>