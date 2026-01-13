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
        
        
        <div :class="(showReportModal || showLmiMatrix) ? 'blur-sm' : ''" class="flex w-full h-full transition-all duration-200">
            
            <!-- SIDEBAR -->
            <aside class="w-72 bg-[#1e3a8a] text-white flex flex-col shadow-xl z-10">
                
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
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
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
                                <button class="px-4 py-2 text-sm font-semibold bg-blue-600 text-white rounded-md">
                                    Regional Statistics
                                </button>
                                <button @click="activeView = 'job-market'" class="px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-md">
                                    Job Market Demands
                                </button>
                            </div>
                        </div>
                        
                        <!-- KPI  -->
                        <div class="flex items-center justify-between pb-5 border-b border-gray-200">
                            <div>
                                <h2 class="font-semibold text-slate-700">Key Performance Indicators</h2>
                                <p class="text-xs text-slate-500">
                                    Regional employment estimates based on PSA Labor Force Survey.
                                </p>
                            </div>
                            <div class="flex items-center gap-2 bg-white border rounded-lg px-3 py-2 text-xs">
                                <span class="text-slate-400">📅 KPI Period:</span>
                                <select class="outline-none font-semibold">
                                    <option>2024</option>
                                    <option>2025</option>
                                </select>
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

                        <!-- KPI Cards  -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Employment Rate Card -->
                            <div class="bg-white border rounded-xl p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs text-slate-500 font-semibold uppercase">Employment Rate</p>
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold">▲ 0%</span>
                                </div>
                                <h2 class="text-3xl font-bold text-slate-800">96.4%</h2>
                                <div class="mt-4 h-1.5 bg-slate-100 rounded-full">
                                    <div class="h-full w-[96%] bg-blue-600 rounded-full"></div>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-2 text-right">Target: &gt;95.0%</p>
                            </div>

                            <!-- Unemployment Card -->
                            <div class="bg-white border rounded-xl p-5 shadow-sm border-l-4 border-red-500">
                                <div class="flex justify-between items-center mb-2">
                                <p class="text-xs text-slate-500 font-semibold uppercase mb-2">Unemployment Rate</p>
                                <svg class="w-6 h-6 text-red-200"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                                        M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                                        M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                                </svg>

                                </div>
                                <h2 class="text-3xl font-bold text-slate-800">3.6%</h2>
                                <p class="text-xs text-slate-500 mt-2">86k Unemployed Persons</p>
                            </div>

                            <!-- Underemployment Card -->
                            <div class="bg-white border rounded-xl p-5 shadow-sm border-l-4 border-orange-500">
                                <div class="flex justify-between items-center mb-2">
                                <p class="text-xs text-slate-500 font-semibold uppercase mb-2">Underemp. Rate</p>
                               <svg class="w-6 h-6 text-orange-200"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                </div>
                                
                                <h2 class="text-3xl font-bold text-slate-800">10.5%</h2>
                                <p class="text-xs text-slate-500 mt-2">241k Seeking More Hours</p>
                            </div>

                            <!-- Participation Card -->
                            <div class="bg-white border rounded-xl p-5 shadow-sm border-l-4 border-green-500">
                                <div class="flex justify-between items-center mb-2">
                                <p class="text-xs text-slate-500 font-semibold uppercase mb-2">Participation Rate</p>
                               <svg class="w-6 h-6 text-green-200"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V7a2 2 0 012-2h3l1-2h2l1 2h3a2 2 0 012 2v12a2 2 0 01-2 2z"/>
                                </svg>

                                </div>
                                <h2 class="text-3xl font-bold text-slate-800">57.7%</h2>
                                <div class="mt-4 h-1.5 bg-slate-100 rounded-full">
                                    <div class="h-full w-[58%] bg-green-500 rounded-full"></div>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-2 text-right">Active Workforce vs Pop 15+</p>
                            </div>
                        </div>

                        <!-- Charts Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Labor Chart -->
                        <div class="bg-white border rounded-xl p-5 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="font-semibold text-slate-800">Labor Force vs Employment Rate</h3>
                                    <p class="text-xs text-slate-500">Comparing workforce size (bars) vs employment rate (line)</p>
                                </div>
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="text-xs bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-lg flex items-center gap-2 transition">
                                        <span id="laborYearRange">2012 – 2025 Q4</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    
                                    <div x-show="open" 
                                        @click.away="open = false"
                                        x-transition
                                        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border z-10 p-5">
                                        
                                        <div class="mb-4">
                                            <label class="block text-xs font-semibold text-slate-700 mb-3">Select Year Range</label>
                                            
                                            <!-- Year Range Inputs -->
                                            <div class="flex items-center gap-3">
                                                <div class="flex-1">
                                                    <label class="text-[10px] text-slate-500 mb-1 block">From</label>
                                                    <select id="laborStartYear" class=" form-select w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                                        <option value="" selected disabled>Select year</option>
                                                        @for ($year = 2012; $year <= 2025; $year++)
                                                            <option value="{{ $year }}">{{ $year }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                
                                                <span class="text-slate-400 mt-5">—</span>
                                                
                                                <div class="flex-1">
                                                    <label class="text-[10px] text-slate-500 mb-1 block">To</label>
                                                    <select id="laborEndYear" class="form-select w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                                        <option value="" selected disabled>Select year</option>
                                                        @for ($year = 2012; $year <= 2025; $year++)
                                                           <option value="{{ $year }}">{{ $year }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Quarter Selectors (shown when year >= 2018) -->
                                            <div class="flex items-center gap-3 mt-3">
                                                <div class="flex-1">
                                                    <label class="text-[10px] text-slate-500 mb-1 block">Quarter (From)</label>
                                                    <select id="laborStartQuarter" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500  cursor-pointer">
                                                        <option value="Q1">Q1</option>
                                                        <option value="Q2">Q2</option>
                                                        <option value="Q3">Q3</option>
                                                        <option value="Q4">Q4</option>
                                                    </select>
                                                </div>
                                                
                                                <span class="text-slate-400 mt-5">—</span>
                                                
                                                <div class="flex-1">
                                                    <label class="text-[10px] text-slate-500 mb-1 block">Quarter (To)</label>
                                                    <select id="laborEndQuarter" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500  cursor-pointer">
                                                        <option value="Q1">Q1</option>
                                                        <option value="Q2">Q2</option>
                                                        <option value="Q3">Q3</option>
                                                        <option value="Q4" selected>Q4</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <button onclick="updateLaborChart()" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-4 rounded-lg transition cursor-pointer">
                                            Apply Filter
                                        </button>
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
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="text-xs bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-lg flex items-center gap-2 transition">
                                        <span id="unempYearRange">2012 – 2025 Q4</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    
                                    <div x-show="open" 
                                        @click.away="open = false"
                                        x-transition
                                        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border z-10 p-5">
                                        
                                        <div class="mb-4">
                                            <label class="block text-xs font-semibold text-slate-700 mb-3">Select Year Range</label>
                                            
                                            <div class="flex items-center gap-3">
                                                <div class="flex-1">
                                                    <label class="text-[10px] text-slate-500 mb-1 block">From</label>
                                                    <select id="unempStartYear" class=" form-select w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                                        <option value="" selected disabled>Select year</option>
                                                        @for ($year = 2012; $year <= 2025; $year++)
                                                            <option value="{{ $year }}">{{ $year }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                
                                                <span class="text-slate-400 mt-5">—</span>
                                                
                                                <div class="flex-1">
                                                    <label class="text-[10px] text-slate-500 mb-1 block">To</label>
                                                    <select id="unempEndYear" class="form-select w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                                        <option value="" selected disabled>Select year</option>
                                                        @for ($year = 2012; $year <= 2025; $year++)
                                                            <option value="{{ $year }}">{{ $year }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Quarter Selectors -->
                                            <div class="flex items-center gap-3 mt-3">
                                                <div class="flex-1">
                                                    <label class="text-[10px] text-slate-500 mb-1 block">Quarter (From)</label>
                                                    <select id="unempStartQuarter" class="form-select w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                                        <option value="Q1">Q1</option>
                                                        <option value="Q2">Q2</option>
                                                        <option value="Q3">Q3</option>
                                                        <option value="Q4">Q4</option>
                                                    </select>
                                                </div>
                                                
                                                <span class="text-slate-400 mt-5">—</span>
                                                
                                                <div class="flex-1">
                                                    <label class="text-[10px] text-slate-500 mb-1 block">Quarter (To)</label>
                                                    <select id="unempEndQuarter" class="form-select w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                                        <option value="Q1">Q1</option>
                                                        <option value="Q2">Q2</option>
                                                        <option value="Q3">Q3</option>
                                                        <option value="Q4" selected>Q4</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <button onclick="updateUnempChart()" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-4 rounded-lg transition cursor-pointer">
                                            Apply Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="relative h-80 w-full">
                                <canvas id="unemploymentChart"></canvas>
                            </div>
                        </div>
                    </div>

                        <!-- Data Table -->
                        <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
                            <div class="p-5 border-b border-gray-200 bg-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-semibold text-slate-800 text-lg">Consolidated Regional Statistics</h3>
                                        <p class="text-xs text-slate-500 mt-1">Detailed breakdown for selected KPI Period (2024 - 2025).</p>
                                    </div>
                                    <button class="flex items-center gap-2 text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 px-3 py-2 rounded-lg hover:bg-blue-100">
                                        <span>⬇️</span> Export CSV
                                    </button>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
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
                                        @foreach($regionalStats as $stat)
                                        <tr class="border-b border-gray-100 hover:bg-slate-50 transition">
                                            <td class="px-4 py-3 font-semibold text-slate-700">{{ $stat['period'] }}</td>
                                            <td class="px-4 py-3 text-right text-slate-600">{{ number_format($stat['labor_force']) }}</td>
                                            <td class="px-4 py-3 text-right text-slate-600">{{ number_format($stat['employed']) }}</td>
                                            <td class="px-4 py-3 text-right text-slate-600">{{ $stat['unemployed'] }}</td>
                                            <td class="px-4 py-3 text-right text-slate-600">{{ $stat['underemployed'] }}</td>
                                            <td class="px-4 py-3 text-right font-semibold bg-blue-50 text-blue-700">{{ number_format($stat['emp_rate'], 1) }}%</td>
                                            <td class="px-4 py-3 text-right text-slate-600">{{ number_format($stat['unemp_rate'], 1) }}%</td>
                                            <td class="px-4 py-3 text-right text-slate-600">{{ number_format($stat['underemp_rate'], 1) }}%</td>
                                            <td class="px-4 py-3 text-right text-slate-600">{{ number_format($stat['particip_rate'], 1) }}%</td>
                                        </tr>
                                        @endforeach
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
               

                
                <div x-show="activeView === 'job-market'" x-transition>
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
                                <button @click="activeView = 'overview'" class="px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-md">
                                    Regional Statistics
                                </button>
                                <button class="px-4 py-2 text-sm font-semibold bg-blue-600 text-white rounded-md">
                                    Job Market Demands
                                </button>
                            </div>
                        </div>

                       
                        <div class="bg-slate-900 rounded-xl p-6 text-white flex justify-between items-center shadow-lg">
                            <div class="flex items-start gap-4">
                                <div class="p-2 bg-emerald-500/20 rounded-lg text-emerald-400">🤝</div>
                                <div>
                                    <h2 class="text-lg font-bold">Help us map the future of Davao's workforce.</h2>
                                    <p class="text-sm text-slate-400 max-w-xl">Official data lags behind real-time market needs. Help us bridge the gap by identifying hard-to-fill roles and critical skill shortages.</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button @click="showReportModal = true" class="bg-indigo-600 px-5 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                                    Report Hard-to-Fill Roles
                                </button>
                                <button @click="showLmiMatrix = true" class="bg-emerald-500/10 border border-emerald-500 text-emerald-500 px-5 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-500/20 transition">
                                    Update LMI Matrix
                                </button>
                            </div>
                        </div>

                        
                        <div class="grid grid-cols-12 gap-6">
                            <!-- High Volume Jobs Chart -->
                            <div class="col-span-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                                <div class="flex justify-between mb-4">
                                    <h3 class="font-bold text-gray-800">Top 10 High-Volume Job Titles</h3>
                                    <span class="text-gray-300">ⓘ</span>
                                </div>
                                <canvas id="jobsChart" height="140"></canvas>
                            </div>

                            <!-- Hard to Fill Roles -->
                            <div class="col-span-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                                <div class="flex justify-between mb-4">
                                    <h3 class="font-bold text-gray-800">Hard-to-Fill Roles</h3>
                                    <span class="text-gray-300">ⓘ</span>
                                </div>
                                <div class="space-y-5">
                                    @foreach($hard_to_fill as $job)
                                    <div class="flex justify-between items-center">
                                        <div class="space-y-1">
                                            <p class="font-bold text-sm text-slate-800">{{ $job['role'] }}</p>
                                            <p class="text-[10px] text-gray-400 flex items-center gap-1 uppercase">
                                                🕒 Bottleneck: {{ $job['bottleneck'] }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-red-500 font-bold text-xs">{{ $job['days'] }} days</p>
                                            <p class="text-[9px] text-gray-300">({{ $job['year'] }})</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        
                        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                            <h3 class="font-bold text-lg mb-4">"Critical Skill Gaps" Per Sector</h3>
                            
                            
                            <div class="flex gap-2 mb-8 pb-5 border-b border-gray-200">
                                @foreach(['All', 'BPO/IT', 'Construction', 'Healthcare', 'Agriculture', 'Tourism'] as $tab)
                                <button class="px-4 py-1 text-sm rounded-full {{ $loop->first ? 'bg-purple-600 text-white' : 'border text-gray-500 hover:bg-gray-50' }} transition">{{ $tab }}</button>
                                @endforeach
                            </div>

                            
                            <div class="grid grid-cols-2 gap-12 ">
                                
                                <div class="border-r border-gray-200 ">
                                    <h4 class="text-xs font-bold text-gray-400 mb-4 uppercase">🚫 Missing Soft Skills (Critical Gaps)</h4>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($soft_skills as $skill)
                                        <div class="bg-red-100 text-red-800 px-3 py-2 rounded-lg text-sm">
                                            {{ $skill['name'] }} <span class="text-[10px] opacity-60">({{ $skill['sector'] }})</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                
                                <div>
                                    <h4 class="text-xs font-bold text-gray-400 mb-4 uppercase">🔍 Missing Technical Skills</h4>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($tech_skills as $skill)
                                        <div class="bg-blue-100 text-blue-800 px-3 py-2 rounded-lg text-sm">
                                            {{ $skill['name'] }} <span class="text-[10px] opacity-60">({{ $skill['sector'] }})</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LMI Matrix Table -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="p-6 border-b flex justify-between items-center">
                                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                    <span class="text-emerald-500">田</span> LMI Granularity Matrix Results: Competency Gap Analysis
                                </h3>
                                <button class="text-emerald-600 border border-emerald-100 bg-emerald-50 px-3 py-1 rounded text-xs hover:bg-emerald-100 transition">
                                    Export Analysis
                                </button>
                            </div>
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-400 uppercase text-[10px] font-bold">
                                    <tr>
                                        <th class="px-6 py-4">Job Title / Role</th>
                                        <th class="px-6 py-4">Sector</th>
                                        <th class="px-6 py-4">Missing Skill / Competency</th>
                                        <th class="px-6 py-4 text-center">Type</th>
                                        <th class="px-6 py-4">Required Level</th>
                                        <th class="px-6 py-4">Observed Level</th>
                                        <th class="px-6 py-4">Gap Impact</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y border-t">
                                    @foreach($matrix_results as $row)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 font-bold text-slate-800">{{ $row['role'] }}</td>
                                        <td class="px-6 py-4 text-xs font-medium text-gray-500 uppercase">{{ $row['sector'] }}</td>
                                        <td class="px-6 py-4 text-blue-600 font-medium">{{ $row['skill'] }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 py-0.5 rounded text-[10px] border {{ $row['type'] == 'Hard' ? 'text-blue-500 border-blue-200' : 'text-pink-500 border-pink-200' }}">{{ $row['type'] }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500">{{ $row['req'] }}</td>
                                        <td class="px-6 py-4 text-gray-500">{{ $row['obs'] }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 rounded-md text-[10px] font-bold {{ $row['impact'] == 'Critical' ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600' }}">
                                                {{ $row['impact'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        
                        <div class="flex items-center justify-center">
                            <p class="text-xs text-slate-500">
                                Source: Tab1-Employment-Davao-Region-with-JUL2025.xlsx (Rates) | Module 2 Sources: PhilJobNet, PSA ISLE, Industry Surveys.
                            </p>
                        </div>
                    </div>
                </div>
</div>
</div>
<div x-show="showReportModal" 
     class="fixed inset-0 z-50 flex items-center justify-center  px-4"
     x-cloak
     style="display: none;">
    <div @click.away="showReportModal = false" 
         class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transition-all transform">
        
        <div class="bg-indigo-600 p-5 flex justify-between items-center text-white">
            <div class="flex items-center gap-3">
                <span class="text-xl">💬</span>
                <h3 class="text-lg font-bold">Report Hard-to-Fill Roles</h3>
            </div>
            <button @click="showReportModal = false" class="text-white hover:text-gray-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="p-8 space-y-6">
            <p class="text-gray-500 text-sm leading-relaxed">
                Help us improve regional labor data. Your input helps identify skills gaps in real-time.
            </p>

            <form action="#" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Job Title</label>
                    <input type="text" placeholder="e.g. Senior Data Analyst" 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition text-gray-600 placeholder-gray-400 shadow-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Industry</label>
                        <select class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-white text-gray-600 shadow-sm">
                            <option>BPO / IT</option>
                            <option>Construction</option>
                            <option>Healthcare</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Duration Open</label>
                        <select class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-white text-gray-600 shadow-sm">
                            <option>30-60 Days</option>
                            <option>60-90 Days</option>
                            <option>90+ Days</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Primary Reason for Difficulty</label>
                    <select class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-white text-gray-600 shadow-sm">
                        <option>Lack of Technical Skills</option>
                        <option>Lack of Soft Skills</option>
                        <option>Salary Mismatch</option>
                        <option>Location / Logistics</option>
                    </select>
                </div>

                <button type="button" 
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-indigo-200 mt-4">
                    Submit Report
                </button>
            </form>
        </div>
    </div>
</div>
<div x-show="showLmiMatrix" 
     class="fixed inset-0 z-50 flex items-center justify-center px-4"
     x-cloak
     style="display: none;">
    <div @click.away="showLmiMatrix = false" 
         class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden transition-all transform"> 
        
        
        <div class="bg-teal-700 p-5 flex justify-between items-center text-white sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-bold">Annex A: LMI Granularity Matrix</h3>
            </div>
            <button @click="showLmiMatrix = false" class="text-white hover:bg-teal-600 p-1 rounded transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="overflow-y-auto max-h-[calc(90vh-80px)]">
        
        <div class="p-8">
           
            <p class="text-gray-600 text-sm leading-relaxed mb-8 pb-6 border-b border-gray-200">
                Please list high-volume or hard-to-fill job titles. For each, indicate critical hard and soft skills 
                missing. Be as specific as possible.
            </p>

            
            <div class="bg-gray-50   rounded-lg p-6 mt-8">
                <div class="flex items-start gap-2 text-base font-semibold mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Section A: Respondent Information
            </div>

            
            <form action="#" method="POST" class="space-y-5 ">
`                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">
                            Company / Organization
                        </label>
                        <input 
                            type="text" 
                            name="company"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">
                            Industry / Sector
                        </label>
                        <input 
                            type="text" 
                            name="industry"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"
                        />
                    </div>
                </div>

                
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">
                        Address (City/Municipality)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>
                        <input 
                            type="text" 
                            name="address"
                            class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"
                        />
                    </div>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">
                            Respondent Name
                        </label>
                        <input 
                            type="text" 
                            name="respondent_name"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">
                            Position / Role
                        </label>
                        <input 
                            type="text" 
                            name="position"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"
                        />
                    </div>
                </div>

                
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">
                        Email / Contact Number
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input 
                            type="text" 
                            name="contact"
                            class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"
                        />
                    </div>
                </div>
           
        </div>
             
                <div class="bg-teal-50 border border-teal-200 rounded-lg p-6 mt-8 overflow-hidden">
                    <div class="flex items-start gap-2 text-teal-700 text-base font-semibold mb-2">
                        <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Section B: Job Titles & Competency Gaps
                    </div>
                    <p class="text-teal-600 text-xs italic mb-4">
                        Tip: Think about the last 10–20 applicants you rejected. What specific skills were missing?
                    </p>

                    <div id="jobTitlesContainer" class="space-y-4">
                        
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2 uppercase tracking-wide">
                                    Job Title
                                </label>
                                <input 
                                    type="text" 
                                    name="job_title[]"
                                    placeholder="e.g. Senior Java Developer"
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"
                                />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">
                                        Critical Hard Skills Missing
                                    </label>
                                    <textarea 
                                        name="hard_skills[]"
                                        rows="3"
                                        placeholder="e.g. Spring Boot framework..."
                                        class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm resize-none"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">
                                        Critical Soft Skills Missing
                                    </label>
                                    <textarea 
                                        name="soft_skills[]"
                                        rows="3"
                                        placeholder="e.g. Ability to explain code to non-tech..."
                                        class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm resize-none"
                                    ></textarea>
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2">
                                    Additional Notes
                                </label>
                                <input 
                                    type="text" 
                                    name="additional_notes[]"
                                    placeholder="Specific certifications needed?"
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"
                                />
                            </div>
                        </div>
                    </div>

                    <button type="button" 
                            onclick="addJobTitle()"
                            class="mt-4 text-teal-600 hover:text-teal-700 font-medium text-sm flex items-center gap-1">
                        <span class="text-lg">+</span> Add another Job Title
                    </button>
                </div>

                
                <div class="bg-gray-50   rounded-lg p-6 mt-8">
                    <div class="flex items-start gap-2 text-base font-semibold mb-2">
                        <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2zm7 4v10m-5-5h10"/>
                        </svg>
                        Section B: Job Titles & Competency Gaps
                    </div>

                    <div class="space-y-5">
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">
                                1. Which 1–3 job titles have the most severe competency gaps, and why?
                            </label>
                            <textarea 
                                name="question_1"
                                rows="3"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm resize-none"
                            ></textarea>
                        </div>

                        
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">
                                2. Of the missing competencies, which are trainable internally vs requiring external training?
                            </label>
                            <textarea 
                                name="question_2"
                                rows="3"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm resize-none"
                            ></textarea>
                        </div>

                        
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">
                                3. Are there emerging skills not yet reflected in current job descriptions?
                            </label>
                            <textarea 
                                name="question_3"
                                rows="3"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm resize-none"
                            ></textarea>
                        </div>
                    </div>
                </div>

                
                <div class="flex items-start gap-3 mt-6">
                    <input 
                        type="checkbox" 
                        id="consent"
                        name="consent"
                        class="mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                        required
                    />
                    <label for="consent" class="text-gray-600 text-sm">
                        I agree to contribute this data to the Regional LMI Database.
                    </label>
                </div>

                
                <button type="submit" 
                        class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-lg transition shadow-lg mt-6">
                    Submit LMI Matrix
                </button>
            </form>
        </div>
    </div>
</div>

 <script>
        const ctx = document.getElementById('jobsChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(collect($high_volume_jobs)->pluck('title')) !!},
                datasets: [{
                    data: {!! json_encode(collect($high_volume_jobs)->pluck('count')) !!},
                    backgroundColor: ['#2563eb', '#2563eb', '#3b82f6', '#93c5fd', '#bfdbfe', '#bfdbfe', '#dbeafe', '#dbeafe', '#dbeafe', '#dbeafe'],
                    borderRadius: 4,
                    barThickness: 15
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { stepSize: 350 } },
                    y: { grid: { display: false } }
                }
            }
        });
    </script>
<script>
// Global chart instances
let laborChart = null;
let unempChart = null;

// Initialize charts on page load
document.addEventListener('DOMContentLoaded', async function () {
    await initializeLaborChart();
    await initializeUnempChart();
});

// ==============================
// Labor Force Chart
// ==============================
async function initializeLaborChart() {
    const laborCtx = document.getElementById('laborEmploymentChart');
    if (!laborCtx) return;

    const response = await fetch("/api/labor-vs-employment");
    if (!response.ok) {
        console.error('Labor API failed:', response.status);
        return;
    }

    const data = await response.json();

    laborChart = new Chart(laborCtx.getContext('2d'), {
        data: {
            labels: data.map(d => d.year),
            datasets: [
                // 🟦 Labor Force (Bars - Background Context)
                {
                    type: 'bar',
                    label: 'Labor Force (thousands)',
                    data: data.map(d => d.labor_force_thousands),
                    backgroundColor: 'rgba(203, 213, 225, 0.45)',
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.85,
                    categoryPercentage: 0.75,
                    yAxisID: 'y'
                },

                // 🔵 Employment Rate (Line – Main Focus)
                {
                    type: 'line',
                    label: 'Employment Rate (%)',
                    data: data.map(d => d.employment_rate),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.15)',
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    fill: false,
                    yAxisID: 'y1'
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
                    intersect: false
                }
            },

            scales: {
                // 🟨 X-Axis (Readable Timeline)
                x: {
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 12,
                        maxRotation: 45,
                        minRotation: 45
                    },
                    grid: {
                        display: false
                    }
                },

                // 🟦 Left Y-Axis (Labor Force)
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Labor Force (thousands)'
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.15)'
                    }
                },

                // 🔵 Right Y-Axis (Employment Rate)
                y1: {
                    position: 'right',
                    min: 80,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Employment Rate (%)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
}

async function updateLaborChart() {
    const startYear = parseInt(document.getElementById('laborStartYear').value);
    const endYear = parseInt(document.getElementById('laborEndYear').value);
    const startQuarter = document.getElementById('laborStartQuarter').value;
    const endQuarter = document.getElementById('laborEndQuarter').value;

    // Validate years
    if (startYear > endYear) {
        alert('Start year cannot be greater than end year');
        return;
    }
    
    // Validate quarters (enforce ascending order)
    // Check if either year is >= 2018 (covers both pure and mixed ranges)
    if (startYear >= 2018 || endYear >= 2018) {
        if (startQuarter > endQuarter) {
            alert('Start quarter cannot be greater than end quarter');
            return;
        }
    }

    let labels = [];
    let laborData = [];
    let empRateData = [];

    // Handle mixed range (e.g., 2015-2018)
    if (startYear < 2018 && endYear >= 2018) {
        // Fetch annual data first (2012-2017 range)
        const annualResponse = await fetch("/api/labor-vs-employment");
        const annualData = await annualResponse.json();
        
        // Add annual data for years before 2018
        annualData.forEach(item => {
            if (item.year >= startYear && item.year < 2018) {
                labels.push(item.year.toString());
                laborData.push(item.labor_force_thousands);
                empRateData.push(item.employment_rate);
            }
        });

        // Then fetch quarterly data for 2018 onwards
        for (let year = 2018; year <= endYear; year++) {
            const response = await fetch(`/api/quarterly/${year}`);
            const data = await response.json();
            
            data.forEach(item => {
                // For mixed ranges: apply quarter filters to all quarterly years
                if (item.quarter < startQuarter) return;
                if (item.quarter > endQuarter) return;
                
                const yearQuarter = `${year} ${item.quarter}`;
                labels.push(yearQuarter);
                laborData.push(item.labor_force_thousands);
                empRateData.push(item.employment_rate);
            });
        }
    } 
    // Pure quarterly range (e.g., 2018-2025)
    else if (startYear >= 2018) {
        for (let year = startYear; year <= endYear; year++) {
            const response = await fetch(`/api/quarterly/${year}`);
            const data = await response.json();
            
            data.forEach(item => {
                const yearQuarter = `${year} ${item.quarter}`;
                
                // Apply quarter filtering to ALL years consistently
                if (item.quarter < startQuarter) return;
                if (item.quarter > endQuarter) return;
                
                labels.push(yearQuarter);
                laborData.push(item.labor_force_thousands);
                empRateData.push(item.employment_rate);
            });
        }
    } 
    // Pure annual range (e.g., 2012-2017)
    else {
        const response = await fetch("/api/labor-vs-employment");
        const data = await response.json();
        
        const filteredData = data.filter(item => {
            return item.year >= startYear && item.year <= endYear;
        });
        
        labels = filteredData.map(d => d.year.toString());
        laborData = filteredData.map(d => d.labor_force_thousands);
        empRateData = filteredData.map(d => d.employment_rate);
    }

    // Update chart
    laborChart.data.labels = labels;
    laborChart.data.datasets[0].data = laborData;
    laborChart.data.datasets[1].data = empRateData;
    laborChart.update();

    // Update display text
    let displayText = `${startYear}`;
    if (startYear >= 2018) {
        displayText += ` ${startQuarter}`;
    }
    displayText += ` — ${endYear}`;
    if (endYear >= 2018) {
        displayText += ` ${endQuarter}`;
    }
    document.getElementById('laborYearRange').textContent = displayText;
}

// ==============================
// Unemployment Chart
// ==============================
async function initializeUnempChart() {
    const unempCtx = document.getElementById('unemploymentChart');
    if (!unempCtx) return;

    const response = await fetch("/api/unemployment-volume");
    if (!response.ok) {
        console.error('Unemployment API failed:', response.status);
        return;
    }
    
    const data = await response.json();

    unempChart = new Chart(unempCtx.getContext('2d'), {
        type: 'line',
        data: {
            labels: data.map(item => item.year),
            datasets: [{
                label: 'Unemployed Persons (thousands)',
                data: data.map(item => item.unemployed_thousands),
                fill: true,
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderColor: '#ef4444',
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });
}

async function updateUnempChart() {
    const startYear = parseInt(document.getElementById('unempStartYear').value);
    const endYear = parseInt(document.getElementById('unempEndYear').value);
    const startQuarter = document.getElementById('unempStartQuarter').value;
    const endQuarter = document.getElementById('unempEndQuarter').value;

    // Validate years
    if (startYear > endYear) {
        alert('Start year cannot be greater than end year');
        return;
    }

    // Validate quarters (enforce ascending order)
    // Check if either year is >= 2018 (covers both pure and mixed ranges)
    if (startYear >= 2018 || endYear >= 2018) {
        if (startQuarter > endQuarter) {
            alert('Start quarter cannot be greater than end quarter');
            return;
        }
    }

    let labels = [];
    let unempData = [];

    // Determine the actual quarterly start year (either startYear or 2018, whichever is later)
    const quarterlyStartYear = Math.max(startYear, 2018);

    // Handle mixed range (e.g., 2015-2019, 2017-2020)
    if (startYear < 2018 && endYear >= 2018) {
        // Fetch annual data first (2012-2017 range)
        const annualResponse = await fetch("/api/unemployment-volume");
        const annualData = await annualResponse.json();
        
        // Add annual data for years before 2018
        annualData.forEach(item => {
            if (item.year >= startYear && item.year < 2018) {
                labels.push(item.year.toString());
                unempData.push(item.unemployed_thousands);
            }
        });

        // Then fetch quarterly data for 2018 onwards
        for (let year = quarterlyStartYear; year <= endYear; year++) {
            const response = await fetch(`/api/quarterly/${year}`);
            const data = await response.json();
            
            data.forEach(item => {
                // For mixed ranges: apply quarter filters to all quarterly years
                if (item.quarter < startQuarter) return;
                if (item.quarter > endQuarter) return;
                
                const yearQuarter = `${year} ${item.quarter}`;
                labels.push(yearQuarter);
                unempData.push(item.unemployed_thousands);
            });
        }
    } 
    // Pure quarterly range (e.g., 2018-2025)
    else if (startYear >= 2018) {
        for (let year = startYear; year <= endYear; year++) {
            const response = await fetch(`/api/quarterly/${year}`);
            const data = await response.json();
            
            data.forEach(item => {
                const yearQuarter = `${year} ${item.quarter}`;
                
                // Apply quarter filtering to ALL years consistently
                if (item.quarter < startQuarter) return;
                if (item.quarter > endQuarter) return;
                
                labels.push(yearQuarter);
                unempData.push(item.unemployed_thousands);
            });
        }
    } 
    // Pure annual range (e.g., 2012-2017)
    else {
        const response = await fetch("/api/unemployment-volume");
        const data = await response.json();
        
        const filteredData = data.filter(item => {
            return item.year >= startYear && item.year <= endYear;
        });
        
        labels = filteredData.map(d => d.year.toString());
        unempData = filteredData.map(d => d.unemployed_thousands);
    }

    // Update chart
    unempChart.data.labels = labels;
    unempChart.data.datasets[0].data = unempData;
    unempChart.update();

    // Update display text
    let displayText = `${startYear}`;
    if (startYear >= 2018) {
        displayText += ` ${startQuarter}`;
    }
    displayText += ` — ${endYear}`;
    if (endYear >= 2018) {
        displayText += ` ${endQuarter}`;
    }
    document.getElementById('unempYearRange').textContent = displayText;
}

// Helper function to add job title form
function addJobTitle() {
    const container = document.getElementById('jobTitlesContainer');
    const newJobTitle = container.children[0].cloneNode(true);
    
    // Clear all inputs
    newJobTitle.querySelectorAll('input, textarea').forEach(input => {
        input.value = '';
    });
    
    container.appendChild(newJobTitle);
}
</script>


</body>
</html>