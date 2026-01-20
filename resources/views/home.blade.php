<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('path/to/chart-filtering.js') }}"></script>

    <title>LMI</title>
</head>
<body class="bg-slate-100 flex min-h-screen ">
    <div x-data="{ activeView: 'overview', showReportModal: false, showLmiMatrix: false }" class="flex w-full h-full">
        
        
        
            
            <!-- SIDEBAR -->
            <aside class="w-72 bg-[#1e3a8a] text-white 
              flex flex-col shadow-xl z-10 overflow-y-auto 
              scrollbar-thin scrollbar-thumb-white/20 
              scrollbar-track-transparent 
              hover:scrollbar-thumb-white/40">
                
                <div class="p-6 border-b border-blue-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-900 font-bold">LMI</div>
                        <div class="leading-tight">
                            <p class="font-bold text-sm">Labor Market Intelligence</p>
                            <p class="text-[10px] opacity-70 italic">Bridging Education & Industry</p>
                        </div>
                    </div>
                </div>

                <!-- NavMenu -->
                <nav class="flex-1 px-4 py-6 space-y-1 ">
                    <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mb-4 px-2">Main Menu</p>
                    
                    <a href="#" class="flex items-center gap-3 p-3 bg-yellow-400 text-blue-900 font-bold rounded-lg transition shadow-md">
                        <span>📊</span> Dashboard
                    </a>
                    
                    <a href="{{ route('hei.graduate') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                        <span class="opacity-70 group-hover:opacity-100">🎓</span> HEI Graduate Data
                    </a>

                    <a href="{{ route('Skill.Gap.Demand') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                        <span class="opacity-70 group-hover:opacity-100">⚖️</span> Skills Gap & Demand
                    </a>

                    <a href="{{ route('Job.Market.Overview') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                        <span class="opacity-70 group-hover:opacity-100">📈</span> Job Market Overview
                    </a>

                    <a href="{{ route('Government.Data') }}" class="flex item-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                        <span class="opacity-70 group-hover:opacity-100">🗂️</span> Government Data
                    </a>

                    <a href="{{ route('Stake.Holder.Engagement') }}" class="flex item-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                        <span class="opacity-70 group-hover:opacity-100">🤝</span> Stakeholder Engagement
                    </a>

                    <a href="{{ route('Report') }}" class="flex item-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                        <span class="opacity-70 group-hover:opacity-100">📑</span> Reports
                    </a>
                    
                    
                    <div class="pt-6">
                        <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mb-4 px-2">Account</p>
                        <a href="{{ route('Setting') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                            <span class="opacity-70 group-hover:opacity-100">⚙️</span> Settings
                        </a>
                        <a href="#" class="flex items-center gap-3 p-3 text-red-300 hover:bg-red-900/30 rounded-lg transition group">
                            <span class="opacity-70 group-hover:opacity-100">🚪</span> Logout
                        </a>
                    </div>
                </nav>

                
                <div class="p-4 bg-blue-950 text-[10px] text-center opacity-50">
                    © 2026 DOLE Region XI
                </div>
            </aside>
            

            <!-- MAIN -->
            <div class="flex-1 flex flex-col overflow-y-auto">
                
                
                <div x-show="activeView === 'overview'" x-transition>
                    <div class="space-y-6 m-5">
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                                    <span class="text-blue-600">📈</span>
                                    Davao Employment Dashboard
                                </h1>
                                <p class="text-sm text-slate-500">
                                    Regional Labor Market Intelligence & Trends
                                </p>
                            </div>

                            
                            <div class="flex bg-white rounded-lg p-1 shadow-sm border">
                                <a href="{{ route('home') }}" class="px-4 py-2 text-sm font-semibold bg-blue-600 text-white rounded-md">
                                    Regional Statistics
                                </a>
                                <a href="{{ route('Job.Market.Demands') }}" 
                                class="px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-md">
                                Job Market Demands
                                </a>
                            </div>
                        </div>

                        <!-- AI Box -->
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 relative">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-blue-700 flex items-center gap-2">
                                    ✨ AI Executive Summary (Jul 2025)
                                </h3>
                                <button class="text-xs font-semibold text-blue-600 bg-white border px-3 py-1 rounded-lg hover:bg-blue-50">
                                    Regenerate Analysis
                                </button>
                            </div>
                            <ul class="text-sm text-slate-700 space-y-1">
                                <li>• <b>Employment Rate</b> is at <b>96.4%</b>, which is <span class="text-green-600 font-semibold">up by 0%</span>.</li>
                                <li>• The <b>Labor Force</b> size is currently <b>2,378k</b>.</li>
                                <li class="text-xs text-slate-500 italic">
                                    Click "Regenerate Analysis" for AI-powered insights.
                                </li>
                            </ul>
                        </div>
                            
                        <div x-data="kpiPeriodFilter()">
            <!-- KPI Header with Dropdown -->
            <div class="flex items-center justify-between pb-5 border-b border-gray-200 mb-6">
                <div>
                    <h2 class="font-semibold text-slate-700">Key Performance Indicators</h2>
                    <p class="text-xs text-slate-500">
                        Regional employment estimates based on PSA Labor Force Survey.
                    </p>
                </div>
                
                <!-- Period Dropdown -->
                <div class="relative">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="text-xs bg-slate-280 border-1 border-gray-200 hover:bg-slate-200 px-3 py-2 rounded-lg flex items-center gap-2 min-w-30 transition">
                            <span x-text="selectedPeriodLabel">Loading...</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                            @click.away="open = false"
                            x-transition
                            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border z-20 p-5">
                            <label class="block text-xs font-semibold text-slate-700 mb-3">Select Period</label>
                            
                            <!-- Quarter and Year side by side -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <!-- Quarter Selector -->
                                <div>
                                    <label class="text-[10px] text-slate-500 mb-1 block">Quarter</label>
                                    <select x-model="selectedMonth" 
                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                        <option value="">Select</option>
                                        <option value="1">Jan</option>
                                        <option value="4">Apr</option>
                                        <option value="7">Jul</option>
                                        <option value="10">Oct</option>
                                    </select>
                                </div>
                                <!-- Year Selector -->
                                <div>
                                    <label class="text-[10px] text-slate-500 mb-1 block">Year</label>
                                    <select x-model="selectedYear" 
                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                        <option value="">Select</option>
                                        <template x-for="year in availableYears" :key="year">
                                            <option :value="year" x-text="year"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                            
                            <button @click="applyPeriodFilter(); open = false;" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-4 rounded-lg transition">
                                Apply Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Employment Rate Card -->
        <div class="bg-white border border-l-4 border-black-500 rounded-xl p-5 shadow-sm">
            <div class="flex justify-between items-center mb-2">
                <p class="text-xs text-slate-500 font-semibold uppercase">Employment Rate</p>
                <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-slate-800" x-text="kpiData.employment_rate?.rate || '0%'">96.4%</h2>
            <div class="mt-4 h-1.5 bg-slate-100 rounded-full">
                <div class="h-full bg-blue-600 rounded-full transition-all duration-500" 
                     :style="`width: ${kpiData.employment_rate?.raw_value || 0}%`"></div>
            </div>
            <p class="text-[10px] text-slate-400 mt-2 text-right">Target: >95.0%</p>
        </div>

        <!-- Unemployment Card -->
        <div class="bg-white border rounded-xl p-5 shadow-sm border-l-4 border-red-500">
            <div class="flex justify-between items-center mb-2">
                <p class="text-xs text-slate-500 font-semibold uppercase">Unemployment Rate</p>
                <svg class="w-6 h-6 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-slate-800" x-text="kpiData.unemployment_rate?.rate || '0%'">3.6%</h2>
            <div class="mt-4 h-1.5 bg-slate-100 rounded-full">
                <div class="h-full bg-red-500 rounded-full transition-all duration-500" 
                     :style="`width: ${Math.min(kpiData.unemployment_rate?.raw_value || 0, 100)}%`"></div>
            </div>
            <p class="text-xs text-slate-500 mt-2" 
               x-text="(kpiData.unemployment_rate?.count_formatted || '0') + ' Unemployed Persons'">86k Unemployed Persons</p>
        </div>

        <!-- Underemployment Card -->
        <div class="bg-white border rounded-xl p-5 shadow-sm border-l-4 border-orange-500">
            <div class="flex justify-between items-center mb-2">
                <p class="text-xs text-slate-500 font-semibold uppercase">Underemp. Rate</p>
                <svg class="w-6 h-6 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-slate-800" x-text="kpiData.underemployment_rate?.rate || '0%'">10.5%</h2>
            <div class="mt-4 h-1.5 bg-slate-100 rounded-full">
                <div class="h-full bg-orange-500 rounded-full transition-all duration-500" 
                     :style="`width: ${Math.min(kpiData.underemployment_rate?.raw_value || 0, 100)}%`"></div>
            </div>
            <p class="text-xs text-slate-500 mt-2" 
               x-text="(kpiData.underemployment_rate?.count_formatted || '0') + ' Seeking More Hours'">241k Seeking More Hours</p>
        </div>

        <!-- Participation Card -->
        <div class="bg-white border rounded-xl p-5 shadow-sm border-l-4 border-green-500">
            <div class="flex justify-between items-center mb-2">
                <p class="text-xs text-slate-500 font-semibold uppercase">Participation Rate</p>
                <svg class="w-6 h-6 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V7a2 2 0 012-2h3l1-2h2l1 2h3a2 2 0 012 2v12a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-slate-800" x-text="kpiData.participation_rate?.rate || '0%'">57.7%</h2>
            <div class="mt-4 h-1.5 bg-slate-100 rounded-full">
                <div class="h-full bg-green-500 rounded-full transition-all duration-500" 
                     :style="`width: ${kpiData.participation_rate?.raw_value || 0}%`"></div>
            </div>
            <p class="text-[10px] text-slate-400 mt-2 text-right">Active Workforce vs Pop 15+</p>
        </div>
    </div>
</div>
<!-- Charts Section - WRAP ENTIRE SECTION IN x-data -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-data="chartFilters()">
    <!-- Labor Chart -->
    <div class="bg-white border rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-slate-800">Labor Force vs Employment Rate</h3>
                <p class="text-xs text-slate-500">Comparing workforce size (bars) vs employment rate (line)</p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Expand Button -->
                <button @click="openChartModal('labor')" 
                        class="text-xs bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-lg flex items-center gap-2 transition"
                        title="Expand chart">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                    <span>Expand</span>
                </button>
                
                <!-- Year Range Filter -->
                <div class="relative">
                    <button @click="laborOpen = !laborOpen" 
                            class="text-xs bg-slate-100 w-40 hover:bg-slate-200 px-3 py-2 rounded-lg flex items-center gap-2 min-w-38 transition">
                        <span x-text="laborYearRange"></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div x-show="laborOpen" 
                        @click.away="laborOpen = false"
                        x-transition
                        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border z-10 p-5">
                        
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-700 mb-3">Select Year Range</label>
                            
                            <!-- Year Range Inputs -->
                            <div class="flex items-center gap-3">
                                <div class="flex-1">
                                    <label class="text-[10px] text-slate-500 mb-1 block">From</label>
                                    <select x-model="laborStartYear" 
                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                        <option value="">Select year</option>
                                        <template x-for="year in laborAvailableYears" :key="year">
                                            <option :value="year" x-text="year"></option>
                                        </template>
                                    </select>
                                </div>
                                
                                <span class="text-slate-400 mt-5">—</span>
                                
                                <div class="flex-1">
                                    <label class="text-[10px] text-slate-500 mb-1 block">To</label>
                                    <select x-model="laborEndYear" 
                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                        <option value="">Select year</option>
                                        <template x-for="year in laborAvailableYears" :key="year">
                                            <option :value="year" x-text="year"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <!-- Quarter Selectors -->
                            <div class="flex items-center gap-3 mt-3">
                                <div class="flex-1">
                                    <label class="text-[10px] text-slate-500 mb-1 block">Quarter (From)</label>
                                    <select x-model="laborStartQuarter" 
                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                        <option value="Q1">Jan</option>
                                        <option value="Q2">Apr</option>
                                        <option value="Q3">Jul</option>
                                        <option value="Q4">Oct</option>
                                    </select>
                                </div>
                                
                                <span class="text-slate-400 mt-5">—</span>
                                
                                <div class="flex-1">
                                    <label class="text-[10px] text-slate-500 mb-1 block">Quarter (To)</label>
                                    <select x-model="laborEndQuarter" 
                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                        <option value="Q1">Jan</option>
                                        <option value="Q2">Apr</option>
                                        <option value="Q3">Jul</option>
                                        <option value="Q4">Oct</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button @click="applyLaborFilter()" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-4 rounded-lg transition cursor-pointer">
                            Apply Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative h-80 w-full">
            <canvas id="laborEmploymentChart"></canvas>
        </div>
    </div>

    <!-- Unemployment Chart -->
    <div class="bg-white border rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-slate-800">Unemployment Volume</h3>
                <p class="text-xs text-slate-500">Headcount of unemployed persons</p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Expand Button -->
                <button @click="openChartModal('unemployment')" 
                        class="text-xs bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-lg flex items-center gap-2 transition"
                        title="Expand chart">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                    <span>Expand</span>
                </button>
                
                <!-- Year Range Filter -->
                <div class="relative">
                    <button @click="unempOpen = !unempOpen" 
                            class="text-xs bg-slate-100 w-40 hover:bg-slate-200 px-3 py-2 rounded-lg flex items-center gap-2 transition">
                        <span x-text="unempYearRange"></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div x-show="unempOpen" 
                        @click.away="unempOpen = false"
                        x-transition
                        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border z-10 p-5">
                        
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-700 mb-3">Select Year Range</label>
                            
                            <div class="flex items-center gap-3">
                                <div class="flex-1">
                                    <label class="text-[10px] text-slate-500 mb-1 block">From</label>
                                    <select x-model="unempStartYear" 
                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                        <option value="">Select year</option>
                                        <template x-for="year in unempAvailableYears" :key="year">
                                            <option :value="year" x-text="year"></option>
                                        </template>
                                    </select>
                                </div>
                                
                                <span class="text-slate-400 mt-5">—</span>
                                
                                <div class="flex-1">
                                    <label class="text-[10px] text-slate-500 mb-1 block">To</label>
                                    <select x-model="unempEndYear" 
                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                        <option value="">Select year</option>
                                        <template x-for="year in unempAvailableYears" :key="year">
                                            <option :value="year" x-text="year"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <!-- Quarter Selectors -->
                            <div class="flex items-center gap-3 mt-3">
                                <div class="flex-1">
                                    <label class="text-[10px] text-slate-500 mb-1 block">Quarter (From)</label>
                                    <select x-model="unempStartQuarter" 
                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                        <option value="Q1">Jan</option>
                                        <option value="Q2">Apr</option>
                                        <option value="Q3">Jul</option>
                                        <option value="Q4">Oct</option>
                                    </select>
                                </div>
                                
                                <span class="text-slate-400 mt-5">—</span>
                                
                                <div class="flex-1">
                                    <label class="text-[10px] text-slate-500 mb-1 block">Quarter (To)</label>
                                    <select x-model="unempEndQuarter" 
                                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                        <option value="Q1">Jan</option>
                                        <option value="Q2">Apr</option>
                                        <option value="Q3">Jul</option>
                                        <option value="Q4">Oct</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button @click="applyUnempFilter()" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-4 rounded-lg transition cursor-pointer">
                            Apply Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative h-80 w-full">
            <canvas id="unemploymentChart"></canvas>
        </div>
    </div>

    <!-- MODAL FOR EXPANDED CHART -->
    <div x-show="expandedChart !== null" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="closeChartModal()"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
             @click="closeChartModal()"></div>
        
        <!-- Modal Content -->
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-7xl max-h-[90vh] overflow-hidden"
                 @click.stop>
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800" 
                            x-text="expandedChart === 'labor' ? 'Labor Force vs Employment Rate' : 'Unemployment Volume'"></h3>
                        <p class="text-sm text-slate-500 mt-1"
                           x-text="expandedChart === 'labor' ? 'Comparing workforce size (bars) vs employment rate (line)' : 'Headcount of unemployed persons'"></p>
                    </div>
                    
                    <button @click="closeChartModal()" 
                            class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Modal Body - Chart Container -->
                <div class="p-6">
                    <div class="relative w-full" style="height: 600px;">
                        <canvas id="expandedChart"></canvas>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="p-6 border-t border-gray-200 bg-slate-50 flex justify-between items-center">
                    <p class="text-xs text-slate-500">
                        Press ESC or click outside to close
                    </p>
                    <button @click="closeChartModal()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

                        <!-- Data Table -->
                        <div class="bg-white border rounded-xl shadow-sm overflow-hidden" x-data="statsFilter()">
                        <div class="p-5 border-b border-gray-200 bg-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-slate-800 text-lg">Consolidated Regional Statistics</h3>
                                    <p class="text-xs text-slate-500 mt-1">Detailed breakdown for selected period.</p>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <!-- Year Range Filter -->
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-xs bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-lg flex items-center gap-2 min-w-25 transition">
                                            <span x-text="displayRange"></span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        
                                        <div x-show="open" 
                                            @click.away="open = false"
                                            x-transition
                                            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border z-10 p-5">
                                            
                                            <div class="mb-4">
                                                <label class="block text-xs font-semibold text-slate-700 mb-3">Select Year Range</label>
                                                
                                                <!-- Year Range Inputs -->
                                                <div class="flex items-center gap-3">
                                                    <div class="flex-1">
                                                        <label class="text-[10px] text-slate-500 mb-1 block">From</label>
                                                        <select x-model="startYear" class="form-select w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                                            <option value="">Select year</option>
                                                            <template x-for="year in availableYears" :key="year">
                                                                <option :value="year" x-text="year"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                    
                                                    <span class="text-slate-400 mt-5">—</span>
                                                    
                                                    <div class="flex-1">
                                                        <label class="text-[10px] text-slate-500 mb-1 block">To</label>
                                                        <select x-model="endYear" class="form-select w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                                            <option value="">Select year</option>
                                                            <template x-for="year in availableYears" :key="year">
                                                                <option :value="year" x-text="year"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <button @click="applyFilter(); open = false;" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-4 rounded-lg transition cursor-pointer">
                                                Apply Filter
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <button @click="exportCSV()" class="flex items-center gap-2 text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 px-3 py-2 rounded-lg hover:bg-blue-100">
                                        <span>⬇️</span> Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-slate-50 border-b border-gray-200">
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Period</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Labor Force ('000)</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Employed ('000)</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Unemployed ('000)</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Underemp. ('000)</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider bg-blue-50 text-blue-700">Emp. Rate</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Unemp. Rate</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Underemp. Rate</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Particip. Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-if="loading">
                                            <tr>
                                                <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                        Loading data...
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        
                                        <template x-if="!loading && filteredData.length === 0">
                                            <tr>
                                                <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                                                    No data available for the selected period.
                                                </td>
                                            </tr>
                                        </template>
                                        
                                        <template x-for="stat in filteredData" :key="stat.period">
                                            <tr class="border-b border-gray-100 hover:bg-slate-50 transition">
                                                <td class="px-4 py-3 font-semibold text-slate-700">
                                                    <div class="flex flex-col">
                                                        <span x-text="formatPeriod(stat.period).month"></span>
                                                        <span x-text="formatPeriod(stat.period).year"></span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-right text-slate-600" x-text="formatNumber(stat.labor_force)"></td>
                                                <td class="px-4 py-3 text-right text-slate-600" x-text="formatNumber(stat.employed)"></td>
                                                <td class="px-4 py-3 text-right text-slate-600" x-text="stat.unemployed"></td>
                                                <td class="px-4 py-3 text-right text-slate-600" x-text="stat.underemployed"></td>
                                                <td class="px-4 py-3 text-right font-semibold bg-blue-50 text-blue-700" x-text="formatRate(stat.emp_rate)"></td>
                                                <td class="px-4 py-3 text-right text-slate-600" x-text="formatRate(stat.unemp_rate)"></td>
                                                <td class="px-4 py-3 text-right text-slate-600" x-text="formatRate(stat.underemp_rate)"></td>
                                                <td class="px-4 py-3 text-right text-slate-600" x-text="formatRate(stat.particip_rate)"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        
                        <div class="flex items-center justify-center">
                            <p class="text-xs text-slate-500">
                                Source: Tab1-Employment-Davao-Region-with-JUL2025.xlsx (Rates) | Module 2 Sources: PhilJobNet, PSA ISLE, Industry Surveys.
                            </p>
                        </div>
                    </div>
                </div>
               

                
                
    

    <script>
function statsFilter() {
    return {
        allData: @json($regionalStats ?? []),
        filteredData: [],
        startYear: null,
        endYear: null,
        availableYears: [],
        loading: false,
        
        get displayRange() {
            return `${this.startYear} — ${this.endYear}`;
        },
        
        async init() {
            await this.fetchAvailableYears();
            
            if (this.availableYears.length >= 2) {
                this.endYear = this.availableYears[0];
                this.startYear = this.availableYears[1];
            }
            
            this.applyFilter();
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
            if (!this.startYear || !this.endYear) {
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
                return yearA - yearB;
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
                           'Underemployed (\'000)', 'Emp. Rate', 'Unemp. Rate', 'Underemp. Rate', 'Particip. Rate'];
            
            const csvContent = [
                headers.join(','),
                ...this.filteredData.map(stat => [
                    stat.period,
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
            
            const blob = new Blob([csvContent], { type: 'text/csv' });
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
        kpiData: {
            employment_rate: { rate: '96.4%', raw_value: 96.4 },
            unemployment_rate: { rate: '3.6%', count_formatted: '86k' },
            underemployment_rate: { rate: '10.5%', count_formatted: '241k' },
            participation_rate: { rate: '57.7%', raw_value: 57.7 }
        },
        loading: false,
        
        async init() {
            console.log('Initializing KPI Period Filter...');
            await this.fetchAvailableYears();
            await this.loadLatestKpiData();
        },
        
        async fetchAvailableYears() {
            try {
                const response = await fetch('/api/kpi-cards/periods');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    const years = [...new Set(result.data.map(p => p.year))].sort((a, b) => b - a);
                    this.availableYears = years;
                    
                    if (result.data.length > 0) {
                        const latest = result.data[0];
                        this.selectedMonth = latest.month.toString();
                        this.selectedYear = latest.year.toString();
                        this.updatePeriodLabel();
                    }
                } else {
                    console.error('Invalid response format:', result);
                    this.selectedPeriodLabel = 'No data available';
                }
            } catch (error) {
                console.error('Error fetching periods:', error);
                this.selectedPeriodLabel = 'Error loading periods';
            }
        },
        
        async loadLatestKpiData() {
            this.loading = true;
            
            try {
                const response = await fetch('/api/kpi-cards');
                console.log('KPI API Response:', response);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('KPI Data:', result);
                
                if (result.success) {
                    this.kpiData = result.data;
                } else {
                    console.error('Error:', result.message);
                }
            } catch (error) {
                console.error('Error loading KPI data:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async loadKpiData(year, month) {
            this.loading = true;
            
            try {
                const url = `/api/kpi-cards?year=${year}&month=${month}`;
                console.log('Fetching KPI data from:', url);
                
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('KPI Data for selected period:', result);
                
                if (result.success) {
                    this.kpiData = result.data;
                } else {
                    console.error('Error:', result.message);
                    alert('No data available for the selected period');
                }
            } catch (error) {
                console.error('Error loading KPI data:', error);
                alert('Error loading data. Please try again.');
            } finally {
                this.loading = false;
            }
        },
        
        applyPeriodFilter() {
            if (!this.selectedMonth || !this.selectedYear) {
                alert('Please select both month and year');
                return;
            }
            
            console.log('Applying filter:', this.selectedMonth, this.selectedYear);
            this.updatePeriodLabel();
            this.loadKpiData(this.selectedYear, this.selectedMonth);
        },
        
        updatePeriodLabel() {
            if (this.selectedMonth && this.selectedYear) {
                const quarterMonths = {
                    '1': 'January',
                    '4': 'April', 
                    '7': 'July',
                    '10': 'October'
                };
                this.selectedPeriodLabel = `${quarterMonths[this.selectedMonth]} ${this.selectedYear}`;
            }
        }
    }
}
</script>

<script>
// Chart Filters - UPDATED VERSION WITH EXPAND MODAL
function chartFilters() {
    return {
        // Modal state
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
                    datasets: originalChart.data.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { 
                            position: 'top', 
                            labels: { 
                                usePointStyle: true, 
                                boxWidth: 10,
                                font: { size: 14 }
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
                                font: { size: 12 }
                            }, 
                            grid: { display: false } 
                        },
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Labor Force (thousands)' },
                            ticks: { callback: (value) => new Intl.NumberFormat('en-US').format(value * 1000) },
                            grid: { color: 'rgba(148, 163, 184, 2.5)' }
                        },
                        y1: {
                            position: 'right',
                            min: 80,
                            max: 100,
                            title: { display: true, text: 'Employment Rate (%)' },
                            ticks: { callback: (value) => value.toFixed(1) + '%' },
                            grid: { drawOnChartArea: false }
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
                type: originalChart.config.type,
                data: {
                    labels: originalChart.data.labels,
                    datasets: originalChart.data.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            display: true,
                            position: 'top',
                            labels: { font: { size: 14 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const actualValue = Math.round(context.parsed.y * 1000);
                                    return 'Unemployed: ' + new Intl.NumberFormat('en-US').format(actualValue);
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
                                font: { size: 12 }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Unemployed Persons (thousands)' },
                            ticks: { callback: (value) => new Intl.NumberFormat('en-US').format(value * 1000) }
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
            this.laborYearRange = `${this.laborStartYear} ${startMonth} - ${this.laborEndYear} ${endMonth}`;
        },
        
        updateUnempYearRange() {
            const startMonth = this.quarterToMonth(this.unempStartQuarter);
            const endMonth = this.quarterToMonth(this.unempEndQuarter);
            this.unempYearRange = `${this.unempStartYear} ${startMonth} - ${this.unempEndYear} ${endMonth}`;
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
                    datasets: [
                         {
                            type: 'line',
                            label: 'Employment Rate (%)',
                            data: empRateData,
                            borderColor: '#2563eb',
                            backgroundColor: '#374151',
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            fill: false,
                            yAxisID: 'y1'
                        },
                        {
                            type: 'bar',
                            label: 'Labor Force (thousands)',
                            data: laborData,
                            backgroundColor: '#1D4ED8',
                            borderWidth: 0,
                            borderRadius: 4,
                            barPercentage: 0.85,
                            categoryPercentage: 0.75,
                            yAxisID: 'y'
                        }
                       
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 10 } },
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
                            ticks: { autoSkip: true, maxTicksLimit: 12, maxRotation: 45, minRotation: 45 }, 
                            grid: { display: false } 
                        },
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Labor Force (thousands)' },
                            ticks: { callback: (value) => new Intl.NumberFormat('en-US').format(value * 1000) },
                            grid: { color: 'rgba(148, 163, 184, 2.5)' }
                        },
                        y1: {
                            position: 'right',
                            min: 80,
                            max: 100,
                            title: { display: true, text: 'Employment Rate (%)' },
                            ticks: { callback: (value) => value.toFixed(1) + '%' },
                            grid: { drawOnChartArea: false }
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
                        labels.push(`${year} ${this.quarterToMonth(item.quarter)}`);
                        laborData.push(parseFloat(item.labor_force_thousands) || 0);
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
            window.laborChart.data.datasets[0].data = laborData;
            window.laborChart.data.datasets[1].data = empRateData;
            window.laborChart.update();
        },
        
        async initializeUnempChart() {
            const unempCtx = document.getElementById('unemploymentChart');
            if (!unempCtx) return;

            let labels = [];
            let unempData = [];

            const startYear = parseInt(this.unempStartYear);
            const endYear = parseInt(this.unempEndYear);

            for (let year = startYear; year <= endYear; year++) {
                const response = await fetch(`/api/quarterly/${year}`);
                if (!response.ok) continue;
                
                const data = await response.json();
                
                data.forEach(item => {
                    labels.push(`${year} ${this.quarterToMonth(item.quarter)}`);
                    unempData.push(parseFloat(item.unemployed_thousands) || 0);
                });
            }

            window.unempChart = new Chart(unempCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Unemployed Persons (thousands)',
                        data: unempData,
                        fill: true,
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderColor: '#ef4444',
                        tension: 0.35,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const actualValue = Math.round(context.parsed.y * 1000);
                                    return 'Unemployed: ' + new Intl.NumberFormat('en-US').format(actualValue);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Unemployed Persons (thousands)' },
                            ticks: { callback: (value) => new Intl.NumberFormat('en-US').format(value * 1000) }
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
            let unempData = [];

            for (let year = startYear; year <= endYear; year++) {
                const response = await fetch(`/api/quarterly/${year}`);
                if (!response.ok) continue;
                
                const data = await response.json();
                
                data.forEach(item => {
                    const itemQ = quarterToNum(item.quarter);
                    
                    if (year > startYear && year < endYear) {
                       labels.push(`${year} ${this.quarterToMonth(item.quarter)}`);
                        unempData.push(parseFloat(item.unemployed_thousands) || 0);
                        return;
                    }
                    
                    if (year === startYear && itemQ < startQ) return;
                    if (year === endYear && itemQ > endQ) return;
                    
                    labels.push(`${year} ${this.quarterToMonth(item.quarter)}`);
                    unempData.push(parseFloat(item.unemployed_thousands) || 0);
                });
            }

            window.unempChart.data.labels = labels;
            window.unempChart.data.datasets[0].data = unempData;
            window.unempChart.update();
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
        kpiData: {
            employment_rate: { rate: '96.4%', raw_value: 96.4 },
            unemployment_rate: { rate: '3.6%', count_formatted: '86k' },
            underemployment_rate: { rate: '10.5%', count_formatted: '241k' },
            participation_rate: { rate: '57.7%', raw_value: 57.7 }
        },
        loading: false,
        
        async init() {
            console.log('Initializing KPI Period Filter...');
            await this.fetchAvailableYears();
            await this.loadLatestKpiData();
        },
        
        async fetchAvailableYears() {
            try {
                // Use the existing periods endpoint which already works
                const response = await fetch('/api/kpi-cards/periods');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    // Extract unique years from periods and sort descending
                    const years = [...new Set(result.data.map(p => p.year))].sort((a, b) => b - a);
                    this.availableYears = years;
                    
                    // Set latest period as default
                    if (result.data.length > 0) {
                        const latest = result.data[0];
                        this.selectedMonth = latest.month.toString();
                        this.selectedYear = latest.year.toString();
                        this.updatePeriodLabel();
                    }
                } else {
                    console.error('Invalid response format:', result);
                    this.selectedPeriodLabel = 'No data available';
                }
            } catch (error) {
                console.error('Error fetching periods:', error);
                this.selectedPeriodLabel = 'Error loading periods';
            }
        },
        
        async loadLatestKpiData() {
            this.loading = true;
            
            try {
                const response = await fetch('/api/kpi-cards');
                console.log('KPI API Response:', response);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('KPI Data:', result);
                
                if (result.success) {
                    this.kpiData = result.data;
                } else {
                    console.error('Error:', result.message);
                }
            } catch (error) {
                console.error('Error loading KPI data:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async loadKpiData(year, month) {
            this.loading = true;
            
            try {
                const url = `/api/kpi-cards?year=${year}&month=${month}`;
                console.log('Fetching KPI data from:', url);
                
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('KPI Data for selected period:', result);
                
                if (result.success) {
                    this.kpiData = result.data;
                } else {
                    console.error('Error:', result.message);
                    alert('No data available for the selected period');
                }
            } catch (error) {
                console.error('Error loading KPI data:', error);
                alert('Error loading data. Please try again.');
            } finally {
                this.loading = false;
            }
        },
        
        applyPeriodFilter() {
            if (!this.selectedMonth || !this.selectedYear) {
                alert('Please select both month and year');
                return;
            }
            
            console.log('Applying filter:', this.selectedMonth, this.selectedYear);
            this.updatePeriodLabel();
            this.loadKpiData(this.selectedYear, this.selectedMonth);
        },
        
        updatePeriodLabel() {
            if (this.selectedMonth && this.selectedYear) {
                const quarterMonths = {
                    '1': 'January',
                    '4': 'April', 
                    '7': 'July',
                    '10': 'October'
                };
                this.selectedPeriodLabel = `${quarterMonths[this.selectedMonth]} ${this.selectedYear}`;
            }
        }
    }
}
</script>
</body>
</html>