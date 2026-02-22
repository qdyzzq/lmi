<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('path/to/chart-filtering.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <title>LMI</title>

    <style>
        html {
            scroll-behavior: smooth;
        }
        
        /* Ensure smooth scrolling works properly */
        body {
            scroll-behavior: smooth;
        }
        
        /* Additional animation for the scroll button */
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .scroll-indicator {
            animation: bounce 2s infinite;
        }
    </style>
    
    <!-- Polyfill for smooth scroll in older browsers -->
    <script src="https://unpkg.com/smoothscroll-polyfill@0.4.4/dist/smoothscroll.min.js"></script>
</head>

<body class="bg-slate-100 min-h-screen">
    <div x-data="{
        activeView: 'overview',
        showReportModal: false,
        showLmiMatrix: false,
        sidebarExpanded: true,
        mobileMenuOpen: false
    }" class="w-full h-full">
        @include('partials.navbar')

        <div class="relative w-full h-[900px] overflow-hidden">
            <div class="absolute inset-0">
                <img src="{{ asset('images/navbar-bg.png') }}" alt="Background"
                    class="w-full h-full object-cover object-top">
                <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/40 to-slate-100"></div>
            </div>

            <div class="relative z-10 h-full flex items-center justify-center px-4">
                <div class="text-center text-white">
                    <h1 class="text-5xl md:text-6xl font-bold mb-4 drop-shadow-lg">
                        Davao Regional Labor Market Situation
                    </h1>
                    <p class="text-xl md:text-2xl text-slate-100 drop-shadow-md">
                        Regional Labor Market Intelligence & Trends
                    </p>
                </div>
            </div>
            <!-- Scroll Button with improved implementation -->
            <div class="absolute bottom-32 left-1/2 transform -translate-x-1/2 z-20 scroll-indicator">
                <a href="#kpi-section" 
                   id="scroll-to-kpi-btn"
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

        <div class="flex-1 flex flex-col overflow-y-auto mt-10 relative z-30">
            <div x-show="activeView === 'overview'" x-transition>
                <div class="space-y-6 m-5">
                    <div x-data="kpiPeriodFilter()" class="pt-10 relative z-20" id="kpi-section">
                        <div class="max-w-7xl mx-auto px-6 space-y-6">

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                                <div
                                    class="group bg-white/95 backdrop-blur-sm border-l-4 border-[#023E8A] rounded-xl p-6 shadow-xl hover:shadow-2xl hover:scale-105 hover:-translate-y-2 transition-all duration-300">
                                    <div class="flex justify-between items-center mb-3">
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
                                    <h2 class="text-4xl font-black text-slate-900 mb-2"
                                        x-text="kpiData.participation_rate?.rate || '0%'">67.0%</h2>
                                    <div class="h-1 w-16 bg-gradient-to-r from-[#023E8A] to-blue-300 rounded-full mb-3">
                                    </div>
                                    <p class="text-xs text-slate-600 font-medium"
                                        x-text="(kpiData.participation_rate?.active_workforce || '0') + ' Estimate number of people'">
                                    </p>
                                </div>

                                <div
                                    class="group bg-white/95 backdrop-blur-sm border-l-4 border-[#006400] rounded-xl p-6 shadow-xl hover:shadow-2xl hover:scale-105 hover:-translate-y-2 transition-all duration-300">
                                    <div class="flex justify-between items-center mb-3">
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
                                    <h2 class="text-4xl font-black text-slate-900 mb-2"
                                        x-text="kpiData.employment_rate?.rate || '0%'">90.0%</h2>
                                    <div
                                        class="h-1 w-16 bg-gradient-to-r from-[#006400] to-green-300 rounded-full mb-3">
                                    </div>
                                    <p class="text-xs text-slate-600 font-medium"
                                        x-text="(kpiData.employment_rate?.count || '0') + ' Estimate number of people'">
                                    </p>
                                </div>

                                <div
                                    class="group bg-white/95 backdrop-blur-sm border-l-4 border-[#FF8C00] rounded-xl p-6 shadow-xl hover:shadow-2xl hover:scale-105 hover:-translate-y-2 transition-all duration-300">
                                    <div class="flex justify-between items-center mb-3">
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
                                    <h2 class="text-4xl font-black text-slate-900 mb-2"
                                        x-text="kpiData.underemployment_rate?.rate || '0%'">67.0%</h2>
                                    <div
                                        class="h-1 w-16 bg-gradient-to-r from-[#FF8C00] to-orange-300 rounded-full mb-3">
                                    </div>
                                    <p class="text-xs text-slate-600 font-medium"
                                        x-text="(kpiData.underemployment_rate?.count_formatted || '0') + ' seeking more hours'">
                                    </p>
                                </div>

                                <div
                                    class="group bg-white/95 backdrop-blur-sm border-l-4 border-[#D30000] rounded-xl p-6 shadow-xl hover:shadow-2xl hover:scale-105 hover:-translate-y-2 transition-all duration-300">
                                    <div class="flex justify-between items-center mb-3">
                                        <p class="text-xs text-slate-600 font-bold uppercase tracking-wide">Unemployment
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
                                    <h2 class="text-4xl font-black text-slate-900 mb-2"
                                        x-text="kpiData.unemployment_rate?.rate || '0%'">7.0%</h2>
                                    <div class="h-1 w-16 bg-gradient-to-r from-[#D30000] to-red-300 rounded-full mb-3">
                                    </div>
                                    <p class="text-xs text-slate-600 font-medium"
                                        x-text="(kpiData.unemployment_rate?.count_formatted || '0') + ' Estimate number of people'">
                                    </p>
                                </div>

                            </div>

                            <!--  ANALYSIS BOX -->
                            <div class="bg-white border border-slate-200 rounded-xl shadow-lg overflow-visible">
                                <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-6 py-4">
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
                                                    Labor Market Analysis
                                                </h3>
                                                <p class="text-slate-300 text-sm" x-text="selectedPeriodLabel">
                                                    Loading...</p>
                                            </div>
                                        </div>

                                        <div class="relative" x-data="{ open: false }" @click.stop>
                                            <button @click="open = !open"
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
                                                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border z-30 p-5">
                                                <label class="block text-xs font-semibold text-slate-700 mb-3">Select
                                                    Period</label>

                                                <div class="grid grid-cols-2 gap-3 mb-4">
                                                    <div>
                                                        <label
                                                            class="text-[10px] text-slate-500 mb-1 block">Quarter</label>
                                                        <select x-model="selectedMonth"
                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                                            <option value="">Select</option>
                                                            <option value="1">Jan</option>
                                                            <option value="4">Apr</option>
                                                            <option value="7">Jul</option>
                                                            <option value="10">Oct</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-[10px] text-slate-500 mb-1 block">Year</label>
                                                        <select x-model="selectedYear"
                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                                            <option value="">Select</option>
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
                    <!-- Charts Section - Replace your existing charts div -->
                    <div x-data="{ ...chartFilters(), activeChart: 'labor', chartsExpanded: true }" class="max-w-7xl mx-auto px-6 space-y-4 mt-6">
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
                                <div class="p-6 space-y-4">

                                    <!-- Centered Tab Navigation -->
                                    <div
                                        class="flex items-center justify-center bg-white p-1 rounded-lg border border-slate-200">
                                        <button
                                            @click="activeChart = 'side'; $nextTick(() => { window.laborChart?.resize(); window.unempChart?.resize(); })"
                                            :class="activeChart === 'side' ? 'bg-slate-100 text-slate-900 shadow-sm' :
                                                'text-slate-500 hover:text-slate-700'"
                                            class="px-6 py-2 text-sm font-medium rounded-md transition-all cursor-pointer">
                                            Overview
                                        </button>
                                        <button
                                            @click="activeChart = 'labor'; $nextTick(() => { window.laborChart?.resize(); window.laborChart?.update(); })"
                                            :class="activeChart === 'labor' ? 'bg-slate-100 text-blue-600 shadow-sm' :
                                                'text-slate-500 hover:text-slate-700'"
                                            class="px-6 py-2 text-sm font-medium rounded-md transition-all cursor-pointer">
                                            Labor Force & Employment
                                        </button>
                                        <button
                                            @click="activeChart = 'compiled'; $nextTick(() => { window.unempChart?.resize(); window.unempChart?.update(); })"
                                            :class="activeChart === 'compiled' ? 'bg-slate-100 text-blue-600 shadow-sm' :
                                                'text-slate-500 hover:text-slate-700'"
                                            class="px-6 py-2 text-sm font-medium rounded-md transition-all cursor-pointer">
                                            Compiled Indicators
                                        </button>
                                    </div>

                                    <!-- Charts Container -->
                                    <div
                                        :class="activeChart === 'side' ? 'grid grid-cols-1 lg:grid-cols-2 gap-6' :
                                            'grid grid-cols-1 gap-6'">

                                        <!-- Labor Force Chart -->
                                        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm"
                                            x-show="activeChart === 'labor' || activeChart === 'side'" x-transition>
                                            <div class="flex items-center justify-between mb-4">
                                                <div>
                                                    <h3 class="font-semibold text-slate-800 text-base">Labor Force vs
                                                        Employment Rate</h3>
                                                    <p class="text-xs text-slate-500">Comparing workforce size (bars)
                                                        vs employment rate (line)</p>
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
                                                        <button @click="laborOpen = !laborOpen"
                                                            class="text-xs bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3 py-2 rounded-lg flex items-center gap-2 transition">
                                                            <span x-text="laborYearRange"
                                                                class="whitespace-nowrap"></span>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </button>
                                                        <div x-show="laborOpen" @click.away="laborOpen = false"
                                                            x-transition
                                                            class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border z-30 p-5">
                                                            <div class="mb-4">
                                                                <label
                                                                    class="block text-xs font-semibold text-slate-700 mb-3">Select
                                                                    Year Range</label>
                                                                <div class="flex items-center gap-3">
                                                                    <div class="flex-1">
                                                                        <label
                                                                            class="text-[10px] text-slate-500 mb-1 block">From</label>
                                                                        <select x-model="laborStartYear"
                                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                            <option value="">Select year</option>
                                                                            <template
                                                                                x-for="year in laborAvailableYears"
                                                                                :key="year">
                                                                                <option :value="year"
                                                                                    x-text="year"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                    <span class="text-slate-400 mt-5">—</span>
                                                                    <div class="flex-1">
                                                                        <label
                                                                            class="text-[10px] text-slate-500 mb-1 block">To</label>
                                                                        <select x-model="laborEndYear"
                                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                            <option value="">Select year</option>
                                                                            <template
                                                                                x-for="year in laborAvailableYears"
                                                                                :key="year">
                                                                                <option :value="year"
                                                                                    x-text="year"></option>
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
                                                                class="w-full bg-slate-700 hover:bg-slate-800 text-white text-sm py-2 px-4 rounded-lg font-medium transition">
                                                                Apply Filter
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="relative h-96 w-full">
                                                <canvas id="laborEmploymentChart"></canvas>
                                            </div>
                                        </div>

                                        <!-- Compiled Indicators Chart -->
                                        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm"
                                            x-show="activeChart === 'compiled' || activeChart === 'side'" x-transition>
                                            <div class="flex items-center justify-between mb-4">
                                                <div>
                                                    <h3 class="font-semibold text-slate-800 text-base">Visualization of
                                                        Compiled Data</h3>
                                                    <p class="text-xs text-slate-500">Key Employment Indicators</p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button @click="openChartModal('unemployment')"
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
                                                        <button @click="unempOpen = !unempOpen"
                                                            class="text-xs bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3 py-2 rounded-lg flex items-center gap-2 transition">
                                                            <span x-text="unempYearRange"
                                                                class="whitespace-nowrap"></span>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </button>
                                                        <div x-show="unempOpen" @click.away="unempOpen = false"
                                                            x-transition
                                                            class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border z-30 p-5">
                                                            <div class="mb-4">
                                                                <label
                                                                    class="block text-xs font-semibold text-slate-700 mb-3">Select
                                                                    Year Range</label>
                                                                <div class="flex items-center gap-3">
                                                                    <div class="flex-1">
                                                                        <label
                                                                            class="text-[10px] text-slate-500 mb-1 block">From</label>
                                                                        <select x-model="unempStartYear"
                                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                            <option value="">Select year</option>
                                                                            <template
                                                                                x-for="year in unempAvailableYears"
                                                                                :key="year">
                                                                                <option :value="year"
                                                                                    x-text="year"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                    <span class="text-slate-400 mt-5">—</span>
                                                                    <div class="flex-1">
                                                                        <label
                                                                            class="text-[10px] text-slate-500 mb-1 block">To</label>
                                                                        <select x-model="unempEndYear"
                                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                                            <option value="">Select year</option>
                                                                            <template
                                                                                x-for="year in unempAvailableYears"
                                                                                :key="year">
                                                                                <option :value="year"
                                                                                    x-text="year"></option>
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
                                                                class="w-full bg-slate-700 hover:bg-slate-800 text-white text-sm py-2 px-4 rounded-lg font-medium transition">
                                                                Apply Filter
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="relative h-96 w-full">
                                                <canvas id="unemploymentChart"></canvas>
                                            </div>
                                        </div>

                                        <!-- Modal (keep your existing modal code) -->
                                        <div x-show="expandedChart !== null"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                            @keydown.escape.window="closeChartModal()"
                                            class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                                                @click="closeChartModal()"></div>
                                            <div class="flex min-h-screen items-center justify-center p-4">
                                                <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-7xl max-h-[95vh] overflow-hidden"
                                                    @click.stop>
                                                    <div
                                                        class="flex items-center justify-between p-6 border-b border-gray-200">
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
                                                        <div class="relative w-full" style="height: 600px;">
                                                            <canvas id="expandedChart"></canvas>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="p-6 border-t border-gray-200 bg-slate-50 flex justify-between items-center">
                                                        <p class="text-xs text-slate-500">Press ESC or click outside to
                                                            close</p>
                                                        <button @click="closeChartModal()"
                                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                                                            Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Section -->
                        <div x-data="{ tableExpanded: true, startYear: '', endYear: '', ...statsFilter() }" class="mt-6">
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

                                <!-- Controls -->
                                <div
                                    class="p-5 bg-slate-50 border-b border-slate-200 flex items-center justify-end gap-3">
                                    <div class="flex items-center gap-2">
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open"
                                                class="text-xs bg-white hover:bg-slate-50 border border-slate-300 px-4 py-2 rounded-lg flex items-center gap-2 transition shadow-sm">
                                                <span x-text="displayRange" class="font-medium text-slate-700"></span>
                                                <svg class="w-4 h-4 text-slate-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false" x-transition
                                                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border z-50 p-5">
                                                <label class="block text-xs font-semibold text-slate-700 mb-3">Select
                                                    Year Range</label>
                                                <div class="flex items-center gap-3 mb-4">
                                                    <div class="flex-1">
                                                        <label
                                                            class="text-[10px] text-slate-500 mb-1 block">From</label>
                                                        <select x-model="startYear"
                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                            <option value="">Select year</option>
                                                            <template x-for="year in availableYears"
                                                                :key="year">
                                                                <option :value="year" x-text="year">
                                                                </option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                    <span class="text-slate-400 mt-5">—</span>
                                                    <div class="flex-1">
                                                        <label class="text-[10px] text-slate-500 mb-1 block">To</label>
                                                        <select x-model="endYear"
                                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                                            <option value="">Select year</option>
                                                            <template x-for="year in availableYears"
                                                                :key="year">
                                                                <option :value="year" x-text="year">
                                                                </option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </div>
                                                <button @click="applyFilter(); open = false;"
                                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-4 rounded-lg font-medium transition">
                                                    Apply Filter
                                                </button>
                                            </div>
                                        </div>
                                        <button @click="exportCSV()"
                                            class="flex items-center gap-2 text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 px-4 py-2 rounded-lg hover:bg-blue-100 transition shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Export CSV
                                        </button>
                                    </div>
                                </div>

                                <!-- Table -->
                                <div x-show="tableExpanded" x-collapse>
                                    <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                                        <table class="w-full text-sm border-collapse">
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
                                                        Underemp. Rate</th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider border-r border-slate-200">
                                                        Unemp. Rate</th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider">
                                                        Particip. Rate</th>
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
                                </div>

                                <!-- Footer -->
                                <div class="p-4 bg-slate-50 border-t border-slate-200 text-center">
                                    <p class="text-xs text-slate-500">Source: Philippine Statistics Authority; Labor
                                        Force Survey</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function statsFilter() {
            return {
                allData: @json($regionalStats ?? []),
                filteredData: [],
                startYear: '',
                endYear: '',
                availableYears: [],
                loading: false,

                get displayRange() {
                    if (this.startYear && this.endYear) {
                        return `${this.startYear} — ${this.endYear}`;
                    }
                    return 'Select Range';
                },

                async init() {
                    await this.fetchAvailableYears();

                    if (this.availableYears.length >= 2) {
                        this.endYear = String(this.availableYears[0]);
                        this.startYear = String(this.availableYears[1]);
                    } else if (this.availableYears.length === 1) {
                        this.endYear = String(this.availableYears[0]);
                        this.startYear = String(this.availableYears[0]);
                    }

                    this.$nextTick(() => {
                        this.applyFilter();
                    });
                },

                async fetchAvailableYears() {
                    try {
                        const response = await fetch('/api/available-years');
                        const result = await response.json();

                        if (result.success) {
                            this.availableYears = result.data;
                        } else {
                            console.error('Failed to fetch years:', result.message);
                            const currentYear = new Date().getFullYear();
                            this.availableYears = [currentYear, currentYear - 1];
                        }
                    } catch (error) {
                        console.error('Error fetching available years:', error);
                        const currentYear = new Date().getFullYear();
                        this.availableYears = [currentYear, currentYear - 1];
                    }
                },

                applyFilter() {
                    if (!this.startYear || !this.endYear || this.startYear === '' || this.endYear === '') {
                        alert('Please select both start and end years');
                        return;
                    }

                    if (parseInt(this.startYear) > parseInt(this.endYear)) {
                        alert('Start year cannot be greater than end year');
                        return;
                    }

                    this.loading = true;

                    this.filteredData = this.allData.filter(stat => {
                        const yearMatch = stat.period.match(/\d{4}/);
                        if (!yearMatch) return false;

                        const year = parseInt(yearMatch[0]);
                        return year >= parseInt(this.startYear) && year <= parseInt(this.endYear);
                    });

                    this.filteredData.sort((a, b) => {
                        const yearA = parseInt(a.period.match(/\d{4}/)[0]);
                        const yearB = parseInt(b.period.match(/\d{4}/)[0]);
                        return yearB - yearA;
                    });

                    this.loading = false;
                },

                formatNumber(value) {
                    return new Intl.NumberFormat('en-US').format(value);
                },

                formatRate(value) {
                    return parseFloat(value).toFixed(1) + '%';
                },

                formatPeriod(period) {
                    const parts = period.split(/[\s\n]+/);
                    if (parts.length >= 2) {
                        return {
                            month: parts[0],
                            year: parts[1]
                        };
                    }
                    return {
                        month: period,
                        year: ''
                    };
                },

                exportCSV() {
                    const headers = ['Period', 'Labor Force (\'000)', 'Employed (\'000)', 'Unemployed (\'000)',
                        'Underemployed (\'000)', 'Emp. Rate', 'Unemp. Rate', 'Underemp. Rate', 'Particip. Rate'
                    ];

                    const csvContent = [
                        headers.join(','),
                        ...this.filteredData.map(stat => [
                            `"${stat.period}"`,
                            stat.labor_force,
                            stat.employed,
                            stat.unemployed,
                            stat.underemployed,
                            stat.emp_rate + '%',
                            stat.unemp_rate + '%',
                            stat.underemp_rate + '%',
                            stat.particip_rate + '%'
                        ].join(','))
                    ].join('\n');

                    const blob = new Blob([csvContent], {
                        type: 'text/csv'
                    });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `regional-statistics-${this.startYear}-${this.endYear}.csv`;
                    a.click();
                    window.URL.revokeObjectURL(url);
                }
            }
        }
    </script>
    <script>
        function kpiPeriodFilter() {
            return {
                availableYears: [],
                selectedMonth: '',
                selectedYear: '',
                selectedPeriodLabel: 'Loading...',
                loading: false,

                analysis: {
                    employment: '',
                    underemployment: '',
                    unemployment: '',
                    lfpr: ''
                },

                // KPI Data structure
                kpiData: {
                    employment_rate: {
                        rate: '0%',
                        count_formatted: '0',
                        raw_value: 0
                    },
                    unemployment_rate: {
                        rate: '0%',
                        count_formatted: '0',
                        raw_value: 0
                    },
                    underemployment_rate: {
                        rate: '0%',
                        count_formatted: '0',
                        raw_value: 0
                    },
                    participation_rate: {
                        rate: '0%',
                        count_formatted: '0',
                        raw_value: 0
                    }
                },

                async init() {
                    await this.fetchAvailableYears();

                    if (this.selectedYear && this.selectedMonth) {
                        await this.applyPeriodFilter();
                    } else {
                        await this.loadLatestKpiData();
                        await this.generateAnalysis();
                    }
                },

                async fetchAvailableYears() {
                    try {
                        const response = await fetch('/api/kpi-cards/periods');
                        const result = await response.json();

                        if (result.success && result.data && result.data.length > 0) {
                            this.availableYears = [...new Set(result.data.map(p => p.year))].sort((a, b) => b - a);

                            // Identify the latest available period
                            const latest = result.data[0];
                            this.selectedMonth = latest.month.toString();
                            this.selectedYear = latest.year.toString();

                            this.updatePeriodLabel();
                        }
                    } catch (e) {
                        console.error("Failed to load available periods:", e);
                    }
                },

                async loadLatestKpiData() {
                    this.loading = true;
                    try {
                        const response = await fetch('/api/kpi-cards');
                        const result = await response.json();
                        if (result.success) {
                            this.kpiData = result.data;
                        }
                    } finally {
                        this.loading = false;
                    }
                },

                async applyPeriodFilter() {
                    if (!this.selectedMonth || !this.selectedYear) return;

                    this.updatePeriodLabel();
                    this.loading = true;

                    try {
                        const response = await fetch(
                            `/api/kpi-cards?year=${this.selectedYear}&month=${this.selectedMonth}`
                        );
                        const result = await response.json();

                        if (result.success) {
                            this.kpiData = result.data;
                            await this.generateAnalysis();
                        }
                    } catch (e) {
                        console.error("Error applying period filter:", e);
                    } finally {
                        this.loading = false;
                    }
                },

                async generateAnalysis() {
                    const currentYear = parseInt(this.selectedYear);
                    const prevYear = currentYear - 1;
                    const monthName = this.selectedPeriodLabel.split(' ')[0];

                    try {
                        const response = await fetch(`/api/kpi-cards?year=${prevYear}&month=${this.selectedMonth}`);
                        const result = await response.json();

                        if (result.success) {
                            const cur = this.kpiData;
                            const prev = result.data;

                            const b = (val) => `<span class="font-bold text-slate-900">${val}</span>`;
                            const trendBold = (text) => `<span class="font-bold text-slate-900">${text}</span>`;

                            // 1. Employment Rate Analysis
                            let empHigher = parseFloat(cur.employment_rate.raw_value) >= parseFloat(prev.employment_rate
                                .raw_value);
                            let empWord = empHigher ? 'higher' : 'lower';
                            this.analysis.employment =
                                `The employment rate in ${b(monthName + ' ' + currentYear)} was estimated at ${b(cur.employment_rate.rate)}. This was ${trendBold(empWord)} than the recorded rate in ${b(monthName + ' ' + prevYear)} of ${b(prev.employment_rate.rate)}.`;

                            // 2. Underemployment Rate Analysis
                            let underHigher = parseFloat(cur.underemployment_rate.raw_value) >= parseFloat(prev
                                .underemployment_rate.raw_value);
                            let underWord = underHigher ? 'went up' : 'went down';
                            this.analysis.underemployment =
                                `The underemployment rate in ${b(monthName + ' ' + currentYear)} ${trendBold(underWord)} to ${b(cur.underemployment_rate.rate)}, from ${b(prev.underemployment_rate.rate)} in ${b(monthName + ' ' + prevYear)}.`;

                            // 3. Unemployment Rate Analysis
                            let unempHigher = parseFloat(cur.unemployment_rate.raw_value) >= parseFloat(prev
                                .unemployment_rate.raw_value);
                            let unempWord = unempHigher ? 'rose' : 'dropped';
                            this.analysis.unemployment =
                                `The unemployment rate ${trendBold(unempWord)} to ${b(cur.unemployment_rate.rate)} in ${b(monthName + ' ' + currentYear)}, from its rate in ${b(monthName + ' ' + prevYear)} of ${b(prev.unemployment_rate.rate)}.`;

                            // 4. LFPR Analysis
                            let lfprHigher = parseFloat(cur.participation_rate.raw_value) >= parseFloat(prev
                                .participation_rate.raw_value);
                            let lfprWord = lfprHigher ? 'higher' : 'lower';
                            this.analysis.lfpr =
                                `The country’s labor force participation rate (LFPR) in ${b(monthName + ' ' + currentYear)} was recorded at ${b(cur.participation_rate.rate)}, ${trendBold(lfprWord)} than the estimated LFPR in ${b(monthName + ' ' + prevYear)} at ${b(prev.participation_rate.rate)}.`;

                        } else {
                            this.analysis.employment = `Historical comparison data not found for ${prevYear}.`;
                            this.analysis.underemployment = this.analysis.unemployment = this.analysis.lfpr = "";
                        }
                    } catch (e) {
                        this.analysis.employment = "Could not generate analysis due to a network error.";
                    }
                },

                updatePeriodLabel() {
                    const quarterMonths = {
                        '1': 'January',
                        '4': 'April',
                        '7': 'July',
                        '10': 'October'
                    };
                    if (this.selectedMonth && this.selectedYear) {
                        this.selectedPeriodLabel = `${quarterMonths[this.selectedMonth]} ${this.selectedYear}`;
                    }
                }
            };
        }
    </script>

    <script>
        // Chart Filters - UPDATED VERSION WITH EXPAND MODAL
        function chartFilters() {
            return {
                activeChart: 'labor',
                expandedChart: null,

                quarterToMonth(q) {
                    const map = {
                        Q1: 'Jan',
                        Q2: 'Apr',
                        Q3: 'Jul',
                        Q4: 'Oct'
                    };
                    return map[q] || q;
                },

                // Labor Chart State
                laborAvailableYears: [],
                laborStartYear: '',
                laborEndYear: '',
                laborStartQuarter: 'Q1',
                laborEndQuarter: 'Q4',
                laborYearRange: 'Loading...',
                laborOpen: false,

                // Unemployment Chart State
                unempAvailableYears: [],
                unempStartYear: '',
                unempEndYear: '',
                unempStartQuarter: 'Q1',
                unempEndQuarter: 'Q4',
                unempYearRange: 'Loading...',
                unempOpen: false,

                async init() {
                    console.log('Initializing chart filters...');
                    await this.fetchAvailableYears();
                    await this.initializeLaborChart();
                    await this.initializeUnempChart();
                },

                // Modal methods
                openChartModal(chartType) {
                    this.expandedChart = chartType;
                    this.$nextTick(() => {
                        if (chartType === 'labor') {
                            this.drawExpandedLaborChart();
                        } else if (chartType === 'unemployment') {
                            this.drawExpandedUnemploymentChart();
                        }
                    });
                },

                closeChartModal() {
                    this.expandedChart = null;
                    if (window.expandedChartInstance) {
                        window.expandedChartInstance.destroy();
                        window.expandedChartInstance = null;
                    }
                },

                drawExpandedLaborChart() {
                    const ctx = document.getElementById('expandedChart');
                    if (!ctx) return;

                    if (window.expandedChartInstance) {
                        window.expandedChartInstance.destroy();
                    }

                    const originalChart = window.laborChart;
                    if (!originalChart) return;

                    window.expandedChartInstance = new Chart(ctx.getContext('2d'), {
                        data: {
                            labels: originalChart.data.labels,
                            datasets: [{
                                    type: 'line',
                                    label: 'Employment Rate (%)',
                                    data: originalChart.data.datasets[0].data,
                                    borderColor: '#3B82F6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4,
                                    borderWidth: 4,
                                    pointRadius: 7,
                                    pointHoverRadius: 9,
                                    pointBackgroundColor: '#3B82F6',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 3,
                                    fill: false,
                                    yAxisID: 'y1',
                                    datalabels: {
                                        display: true,
                                        anchor: 'end',
                                        align: 'top',
                                        offset: 10,
                                        color: '#1e293b',
                                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                        borderRadius: 6,
                                        padding: 6,
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            weight: '700',
                                            size: 16
                                        },
                                        formatter: (value) => value.toFixed(1) + '%'
                                    }
                                },
                                {
                                    type: 'bar',
                                    label: 'Labor Force (thousands)',
                                    data: originalChart.data.datasets[1].data,
                                    backgroundColor: function(context) {
                                        const chart = context.chart;
                                        const {
                                            ctx,
                                            chartArea
                                        } = chart;
                                        if (!chartArea) return '#182337';

                                        // ✅ GRADIENT FOR EXPANDED VIEW
                                        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0,
                                            chartArea.top);
                                        gradient.addColorStop(0, '#3B5175');
                                        gradient.addColorStop(0.5, '#2A3F5F');
                                        gradient.addColorStop(1, '#182337');
                                        return gradient;
                                    },
                                    borderColor: '#4A6FA5',
                                    borderWidth: 1.5,
                                    borderRadius: 4,
                                    yAxisID: 'y',
                                    datalabels: {
                                        display: true,
                                        anchor: 'center',
                                        align: 'center',
                                        color: '#FFFFFF',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            weight: 'bold',
                                            size: 22
                                        },
                                        formatter: (value) => new Intl.NumberFormat('en-US').format(value)
                                    }
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 10,
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 16,
                                            weight: '600'
                                        },
                                        color: '#1e293b',
                                        padding: 20
                                    }
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) label += ': ';
                                            if (context.dataset.yAxisID === 'y') {
                                                const actualValue = Math.round(context.parsed.y * 1000);
                                                label += new Intl.NumberFormat('en-US').format(actualValue);
                                            } else {
                                                label += context.parsed.y.toFixed(1) + '%';
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        autoSkip: false,
                                        maxRotation: 45,
                                        minRotation: 45,
                                        color: '#475569',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 20,
                                            weight: '600'
                                        },
                                        padding: 10
                                    },
                                    grid: {
                                        display: false
                                    },
                                    border: {
                                        color: '#e2e8f0',
                                        width: 2
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Labor Force (thousands)',
                                        color: '#1e293b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 16,
                                            weight: '600'
                                        },
                                        padding: 15
                                    },
                                    ticks: {
                                        color: '#64748b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 20,
                                            weight: '500'
                                        },
                                        padding: 10,
                                        callback: (value) => new Intl.NumberFormat('en-US').format(value * 1000)
                                    },
                                    grid: {
                                        color: '#f1f5f9',
                                        lineWidth: 1
                                    },
                                    border: {
                                        display: false
                                    }
                                },
                                y1: {
                                    display: false,
                                    position: 'right',
                                    min: 80,
                                    max: 100
                                }
                            }
                        }
                    });
                },

                drawExpandedUnemploymentChart() {
                    const ctx = document.getElementById('expandedChart');
                    if (!ctx) return;

                    if (window.expandedChartInstance) {
                        window.expandedChartInstance.destroy();
                    }

                    const originalChart = window.unempChart;
                    if (!originalChart) return;

                    window.expandedChartInstance = new Chart(ctx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: originalChart.data.labels,
                            datasets: [{
                                    label: 'LABOR FORCE PARTICIPATION RATE',
                                    data: originalChart.data.datasets[0].data,
                                    borderColor: '#023E8A',
                                    backgroundColor: '#023E8A',
                                    borderWidth: 4,
                                    pointRadius: 6,
                                    pointBackgroundColor: '#023E8A',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    tension: 0.3,
                                },
                                {
                                    label: 'EMPLOYMENT RATE',
                                    data: originalChart.data.datasets[1].data,
                                    borderColor: '#006400', // ✅ KPI colors
                                    backgroundColor: '#006400',
                                    borderWidth: 4,
                                    pointRadius: 6,
                                    pointBackgroundColor: '#006400',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    tension: 0.3,
                                },
                                {
                                    label: 'UNDEREMPLOYMENT RATE',
                                    data: originalChart.data.datasets[2].data,
                                    borderColor: '#FF8C00', // ✅ KPI colors
                                    backgroundColor: '#FF8C00',
                                    borderWidth: 4,
                                    pointRadius: 6,
                                    pointBackgroundColor: '#FF8C00',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    tension: 0.3,
                                },
                                {
                                    label: 'UNEMPLOYMENT RATE',
                                    data: originalChart.data.datasets[3].data,
                                    borderColor: '#D30000', // ✅ KPI colors
                                    backgroundColor: '#D30000',
                                    borderWidth: 4,
                                    pointRadius: 6,
                                    pointBackgroundColor: '#D30000',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    tension: 0.3,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    left: 40,
                                    right: 80,
                                    top: 20,
                                    bottom: 60
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    align: 'center',
                                    labels: {
                                        padding: 20,
                                        boxWidth: 12,
                                        boxHeight: 12,
                                        color: '#1e293b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 14,
                                            weight: '600'
                                        },
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
                                },
                                datalabels: {
                                    display: true,
                                    color: '#1e293b',
                                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                    borderRadius: 6,
                                    padding: 8,
                                    align: 'top',
                                    anchor: 'end',
                                    offset: 8,
                                    font: {
                                        family: 'Inter, system-ui, -apple-system, sans-serif',
                                        size: 18,
                                        weight: 'bold'
                                    },
                                    formatter: (value) => value.toFixed(1) + '%'
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        autoSkip: false,
                                        maxRotation: 45,
                                        minRotation: 45,
                                        padding: 15,
                                        color: '#475569',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 20,
                                            weight: '600'
                                        }
                                    },
                                    grid: {
                                        display: false
                                    },
                                    border: {
                                        color: '#e2e8f0',
                                        width: 2
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        padding: 25,
                                        stepSize: 20,
                                        color: '#64748b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 20,
                                            weight: '500'
                                        }
                                    },
                                    title: {
                                        display: true,
                                        text: 'Rate (%)',
                                        color: '#1e293b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 15,
                                            weight: '600'
                                        },
                                        padding: 12
                                    },
                                    grid: {
                                        color: '#f1f5f9',
                                        lineWidth: 1
                                    },
                                    border: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                },

                async fetchAvailableYears() {
                    try {
                        const response = await fetch('/api/available-years');

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const result = await response.json();

                        if (result.success && result.data) {
                            this.laborAvailableYears = result.data;
                            this.unempAvailableYears = result.data;

                            if (result.data.length >= 2) {
                                this.laborEndYear = result.data[0].toString();
                                this.laborStartYear = result.data[1].toString();
                                this.unempEndYear = result.data[0].toString();
                                this.unempStartYear = result.data[1].toString();
                            } else if (result.data.length === 1) {
                                this.laborEndYear = result.data[0].toString();
                                this.laborStartYear = result.data[0].toString();
                                this.unempEndYear = result.data[0].toString();
                                this.unempStartYear = result.data[0].toString();
                            }

                            this.updateLaborYearRange();
                            this.updateUnempYearRange();
                        } else {
                            console.error('Invalid response format:', result);
                            const currentYear = new Date().getFullYear();
                            this.laborAvailableYears = [currentYear, currentYear - 1];
                            this.unempAvailableYears = [currentYear, currentYear - 1];
                            this.laborEndYear = currentYear.toString();
                            this.laborStartYear = (currentYear - 1).toString();
                            this.unempEndYear = currentYear.toString();
                            this.unempStartYear = (currentYear - 1).toString();
                            this.updateLaborYearRange();
                            this.updateUnempYearRange();
                        }
                    } catch (error) {
                        console.error('Error fetching available years:', error);
                        const currentYear = new Date().getFullYear();
                        this.laborAvailableYears = [currentYear, currentYear - 1];
                        this.unempAvailableYears = [currentYear, currentYear - 1];
                        this.laborEndYear = currentYear.toString();
                        this.laborStartYear = (currentYear - 1).toString();
                        this.unempEndYear = currentYear.toString();
                        this.unempStartYear = (currentYear - 1).toString();
                        this.updateLaborYearRange();
                        this.updateUnempYearRange();
                    }
                },

                updateLaborYearRange() {
                    const startMonth = this.quarterToMonth(this.laborStartQuarter);
                    const endMonth = this.quarterToMonth(this.laborEndQuarter);
                    this.laborYearRange =
                        `${this.laborStartYear} ${startMonth} - ${this.laborEndYear} ${endMonth}`;
                },

                updateUnempYearRange() {
                    const startMonth = this.quarterToMonth(this.unempStartQuarter);
                    const endMonth = this.quarterToMonth(this.unempEndQuarter);
                    this.unempYearRange =
                        `${this.unempStartYear} ${startMonth} - ${this.unempEndYear} ${endMonth}`;
                },

                async applyLaborFilter() {
                    const startYear = parseInt(this.laborStartYear);
                    const endYear = parseInt(this.laborEndYear);

                    if (!this.laborStartYear || !this.laborEndYear) {
                        alert('Please select both start and end years');
                        return;
                    }

                    if (startYear > endYear) {
                        alert('Start year cannot be greater than end year');
                        return;
                    }

                    const quarterToNum = (q) => parseInt(q.replace('Q', ''));
                    const startQ = quarterToNum(this.laborStartQuarter);
                    const endQ = quarterToNum(this.laborEndQuarter);

                    if (startYear === endYear && startQ > endQ) {
                        alert('Start quarter cannot be greater than end quarter in the same year');
                        return;
                    }

                    this.updateLaborYearRange();
                    this.laborOpen = false;
                    await this.updateLaborChart();
                },

                async applyUnempFilter() {
                    const startYear = parseInt(this.unempStartYear);
                    const endYear = parseInt(this.unempEndYear);

                    if (!this.unempStartYear || !this.unempEndYear) {
                        alert('Please select both start and end years');
                        return;
                    }

                    if (startYear > endYear) {
                        alert('Start year cannot be greater than end year');
                        return;
                    }

                    const quarterToNum = (q) => parseInt(q.replace('Q', ''));
                    const startQ = quarterToNum(this.unempStartQuarter);
                    const endQ = quarterToNum(this.unempEndQuarter);

                    if (startYear === endYear && startQ > endQ) {
                        alert('Start quarter cannot be greater than end quarter in the same year');
                        return;
                    }

                    this.updateUnempYearRange();
                    this.unempOpen = false;
                    await this.updateUnempChart();
                },

                async initializeLaborChart() {
                    const laborCtx = document.getElementById('laborEmploymentChart');
                    if (!laborCtx) return;

                    let labels = [];
                    let laborData = [];
                    let empRateData = [];

                    const startYear = parseInt(this.laborStartYear);
                    const endYear = parseInt(this.laborEndYear);

                    for (let year = startYear; year <= endYear; year++) {
                        const response = await fetch(`/api/quarterly/${year}`);
                        if (!response.ok) continue;

                        const data = await response.json();

                        data.forEach(item => {
                            labels.push(`${year} ${this.quarterToMonth(item.quarter)}`);
                            laborData.push(parseFloat(item.labor_force_thousands) || 0);
                            empRateData.push(parseFloat(item.employment_rate) || 0);
                        });
                    }

                    window.laborChart = new Chart(laborCtx.getContext('2d'), {
                        data: {
                            labels: labels,
                            datasets: [{
                                    type: 'line',
                                    label: 'Employment Rate (%)',
                                    data: empRateData,
                                    borderColor: '#3B82F6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4,
                                    borderWidth: 3,
                                    pointRadius: 6,
                                    pointHoverRadius: 8,
                                    pointBackgroundColor: '#3B82F6',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    yAxisID: 'y1',

                                    datalabels: {
                                        display: true,
                                        anchor: 'end',
                                        align: 'top',
                                        offset: 8,
                                        color: '#1e293b',
                                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                        borderRadius: 4,
                                        padding: 4,
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            weight: '700',
                                            size: 13
                                        },
                                        formatter: (value) => value.toFixed(1) + '%'
                                    }
                                },
                                {
                                    type: 'bar',
                                    label: 'Labor Force (thousands)',
                                    data: laborData,
                                    backgroundColor: function(context) {
                                        const chart = context.chart;
                                        const {
                                            ctx,
                                            chartArea
                                        } = chart;
                                        if (!chartArea) return '#182337';

                                        const gradient = ctx.createLinearGradient(0, chartArea.bottom,
                                            0, chartArea.top);
                                        gradient.addColorStop(0, '#3B5175');
                                        gradient.addColorStop(0.5, '#2A3F5F');
                                        gradient.addColorStop(1, '#182337');
                                        return gradient;
                                    },
                                    borderColor: '#22324D',
                                    borderWidth: 1,
                                    borderRadius: 2,
                                    yAxisID: 'y',
                                    datalabels: {
                                        display: true,
                                        anchor: 'center',
                                        align: 'center',
                                        color: "#FFFFFF",
                                        font: {
                                            weight: 'bold',
                                            size: 18
                                        },
                                        formatter: (value) => new Intl.NumberFormat('en-US').format(value)
                                    }
                                }

                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 10
                                    }
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) label += ': ';
                                            if (context.dataset.yAxisID === 'y') {
                                                const actualValue = Math.round(context
                                                    .parsed.y * 1000);
                                                label += new Intl.NumberFormat('en-US')
                                                    .format(actualValue);
                                            } else {
                                                label += context.parsed.y.toFixed(1) +
                                                    '%';
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        autoSkip: true,
                                        maxTicksLimit: 12,
                                        maxRotation: 45,
                                        minRotation: 45,
                                        color: '#475569',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 12,
                                            weight: '600',

                                        },
                                        padding: 8
                                    },
                                    grid: {
                                        display: false
                                    },
                                    border: {
                                        color: '#e2e8f0',
                                        width: 2
                                    }
                                },

                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Labor Force (thousands)',
                                        color: '#1e293b', // Slate 800
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 13,
                                            weight: '600',
                                        },
                                        padding: 12
                                    },
                                    ticks: {
                                        color: '#000000',
                                        color: '#64748b', // Slate 500
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 12,
                                            weight: '500'
                                        },
                                        padding: 8,
                                        callback: (value) => new Intl.NumberFormat('en-US')
                                            .format(value * 1000)
                                    },
                                    grid: {
                                        color: '#f1f5f9', // Very light slate
                                        lineWidth: 1
                                    },
                                    border: {
                                        display: false
                                    }
                                },
                                y1: {
                                    display: false,
                                    position: 'right',
                                    min: 80,
                                    max: 100,
                                }
                            }
                        }
                    });
                },

                async updateLaborChart() {
                    const startYear = parseInt(this.laborStartYear);
                    const endYear = parseInt(this.laborEndYear);
                    const quarterToNum = (q) => parseInt(q.replace('Q', ''));
                    const startQ = quarterToNum(this.laborStartQuarter);
                    const endQ = quarterToNum(this.laborEndQuarter);

                    let labels = [];
                    let laborData = [];
                    let empRateData = [];

                    for (let year = startYear; year <= endYear; year++) {
                        const response = await fetch(`/api/quarterly/${year}`);
                        if (!response.ok) continue;

                        const data = await response.json();

                        data.forEach(item => {
                            const itemQ = quarterToNum(item.quarter);

                            if (year > startYear && year < endYear) {
                                labels.push(
                                    `${year} ${this.quarterToMonth(item.quarter)}`);
                                laborData.push(parseFloat(item.labor_force_thousands) ||
                                    0);
                                empRateData.push(parseFloat(item.employment_rate) || 0);
                                return;
                            }

                            if (year === startYear && itemQ < startQ) return;
                            if (year === endYear && itemQ > endQ) return;

                            labels.push(`${year} ${this.quarterToMonth(item.quarter)}`);
                            laborData.push(parseFloat(item.labor_force_thousands) || 0);
                            empRateData.push(parseFloat(item.employment_rate) || 0);
                        });
                    }

                    window.laborChart.data.labels = labels;
                    window.laborChart.data.datasets[1].data = laborData;
                    window.laborChart.data.datasets[0].data = empRateData;
                    window.laborChart.update();
                },

                async initializeUnempChart() {
                    const unempCtx = document.getElementById('unemploymentChart');
                    if (!unempCtx) return;

                    let labels = [];
                    let lfprData = [],
                        empRateData = [],
                        underempData = [],
                        unempRateData = [];
                    const startYear = parseInt(this.unempStartYear);
                    const endYear = parseInt(this.unempEndYear);

                    for (let year = startYear; year <= endYear; year++) {
                        const response = await fetch(`/api/quarterly/${year}`);
                        if (!response.ok) continue;

                        const data = await response.json();

                        data.forEach(item => {
                            labels.push(
                                `${year} ${this.quarterToMonth(item.quarter)}`);
                            empRateData.push(parseFloat(item.employment_rate) || 0);
                            lfprData.push(parseFloat(item.lfpr) || 0);
                            underempData.push(parseFloat(item
                                .underemployment_rate) || 0);
                            unempRateData.push(parseFloat(item.unemployment_rate) ||
                                0);
                        });
                    }

                    window.unempChart = new Chart(unempCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'LABOR FORCE PARTICIPATION RATE',
                                    data: lfprData,
                                    borderColor: '#023E8A', // ← Matches KPI card
                                    backgroundColor: '#023E8A',
                                    borderWidth: 3,
                                    pointRadius: 5,
                                    pointBackgroundColor: '#023E8A',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    tension: 0.3,
                                    datalabels: {
                                        display: true,
                                        align: 'top',
                                        offset: 8
                                    }

                                },
                                {
                                    label: 'EMPLOYMENT RATE',
                                    data: empRateData,
                                    borderColor: '#006400', // ← Matches KPI card
                                    backgroundColor: '#006400',
                                    borderWidth: 3,
                                    pointRadius: 5,
                                    pointBackgroundColor: '#006400',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    tension: 0.3,
                                    datalabels: {
                                        display: true,
                                        align: 'top',
                                        offset: 8
                                    }
                                },
                                {
                                    label: 'UNDEREMPLOYMENT RATE',
                                    data: underempData,
                                    borderColor: '#FF8C00', // ← Matches KPI card
                                    backgroundColor: '#FF8C00',
                                    borderWidth: 3,
                                    pointRadius: 5,
                                    pointBackgroundColor: '#FF8C00',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    tension: 0.3,
                                    datalabels: {
                                        display: true,
                                        align: 'top',
                                        offset: 8
                                    }
                                },
                                {
                                    label: 'UNEMPLOYMENT RATE',
                                    data: unempRateData,
                                    borderColor: '#D30000', // ← Matches KPI card
                                    backgroundColor: '#D30000',
                                    borderWidth: 3,
                                    pointRadius: 5,
                                    pointBackgroundColor: '#D30000',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: false,
                                    tension: 0.3,
                                    datalabels: {
                                        display: true,
                                        align: 'bottom',
                                        offset: 8
                                    }
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        color: '#1e293b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 11,
                                            weight: '600'
                                        },
                                        padding: 15,
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
                                },
                                datalabels: {
                                    display: true,
                                    color: '#1e293b',
                                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                    borderRadius: 4,
                                    padding: {
                                        top: 4,
                                        bottom: 4,
                                        left: 6,
                                        right: 6
                                    },
                                    font: {
                                        family: 'Inter, system-ui, -apple-system, sans-serif',
                                        size: 11,
                                        weight: '700'
                                    },
                                    formatter: (value) => value.toFixed(1)
                                },
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        padding: 12,
                                        color: '#475569',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 12,
                                            weight: '600'
                                        }
                                    },
                                    grid: {
                                        display: false
                                    },
                                    border: {
                                        color: '#e2e8f0',
                                        width: 2
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        padding: 12,
                                        stepSize: 20,
                                        color: '#64748b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 12,
                                            weight: '500'
                                        }
                                    },
                                    title: {
                                        display: true,
                                        text: 'Rate (%)',
                                        color: '#1e293b',
                                        font: {
                                            family: 'Inter, system-ui, -apple-system, sans-serif',
                                            size: 13,
                                            weight: '600'
                                        },
                                        padding: 12
                                    },
                                    grid: {
                                        color: '#f1f5f9',
                                        lineWidth: 1
                                    },
                                    border: {
                                        display: false
                                    }


                                }
                            }
                        }
                    });
                },

                async updateUnempChart() {
                    const startYear = parseInt(this.unempStartYear);
                    const endYear = parseInt(this.unempEndYear);
                    const quarterToNum = (q) => parseInt(q.replace('Q', ''));
                    const startQ = quarterToNum(this.unempStartQuarter);
                    const endQ = quarterToNum(this.unempEndQuarter);

                    let labels = [];
                    let lfpr = [],
                        emp = [],
                        under = [],
                        unemp = [];
                    for (let year = startYear; year <= endYear; year++) {
                        const response = await fetch(`/api/quarterly/${year}`);
                        if (!response.ok) continue;
                        const data = await response.json();

                        data.forEach(item => {
                            const itemQ = quarterToNum(item.quarter);
                            if (year === startYear && itemQ < startQ) return;
                            if (year === endYear && itemQ > endQ) return;

                            labels.push(
                                `${year} ${this.quarterToMonth(item.quarter)}`
                            );
                            emp.push(parseFloat(item.employment_rate) || 0);
                            lfpr.push(parseFloat(item.lfpr) || 0);
                            under.push(parseFloat(item.underemployment_rate) ||
                                0);
                            unemp.push(parseFloat(item.unemployment_rate) || 0);
                        });
                    }

                    window.unempChart.data.labels = labels;
                    window.unempChart.data.datasets[0].data = lfpr;
                    window.unempChart.data.datasets[1].data = emp;
                    window.unempChart.data.datasets[2].data = under;
                    window.unempChart.data.datasets[3].data = unemp;
                    window.unempChart.update();
                }
            }
        }
    </script>
    
    <!-- Fallback scroll implementation (works without Alpine.js) -->
    <script>
        // This ensures scroll works even if Alpine.js fails to load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Scroll fallback script loaded');
            
            // Initialize smooth scroll polyfill
            if (typeof window !== 'undefined' && window.__forceSmoothScrollPolyfill__) {
                window.__forceSmoothScrollPolyfill__ = true;
            }
            
            // Get the scroll button
            const scrollButton = document.getElementById('scroll-to-kpi-btn');
            
            if (scrollButton) {
                console.log('Scroll button found, attaching listener');
                
                // Add click event listener as fallback
                scrollButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Scroll button clicked');
                    
                    const targetElement = document.getElementById('kpi-section');
                    
                    if (targetElement) {
                        console.log('Target element found, scrolling...');
                        
                        // Try modern smooth scroll first
                        try {
                            targetElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        } catch (error) {
                            // Fallback for browsers that don't support smooth scroll
                            console.log('Using fallback scroll');
                            targetElement.scrollIntoView(true);
                        }
                    } else {
                        console.error('Target element #kpi-section not found');
                    }
                });
            } else {
                console.error('Scroll button not found');
            }
            
            // Also handle any other anchor links
            const anchorLinks = document.querySelectorAll('a[href^="#"]');
            anchorLinks.forEach(link => {
                if (link.id !== 'scroll-to-kpi-btn') { // Skip the main button (already handled)
                    link.addEventListener('click', function(e) {
                        const href = this.getAttribute('href');
                        if (href && href.startsWith('#') && href.length > 1) {
                            e.preventDefault();
                            const target = document.querySelector(href);
                            if (target) {
                                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>