<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <title>LMI</title>
</head>
<body class="bg-slate-100 flex min-h-screen ">
    <div x-data="{ activeView: 'overview' }" class="flex w-full h-full">
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

            <a href="#" class="flex item-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="opacity-70 group-hover:opacity-100">🗂️</span> Government Data
            </a>

            <a href="#" class="flex item-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="opacity-70 group-hover:opacity-100">🤝</span> Stakeholder Engagement
            </a>

            <a href="#" class="flex item-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="opacity-70 group-hover:opacity-100">📑</span> Reports
            </a>
            <div class="pt-6">
                <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mb-4 px-2">Account</p>
                <a href="#" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
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

        <button
            @click="activeView = 'job-market'"
            class="px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-md">
            Job Market Demands
        </button>
    </div>
    </div>
    </div>   
        <div class="flex items-center justify-between m-5 pb-5  border-b border-gray-200">
            <div>
                <h2 class="font-semibold text-slate-700"> Key Performance Indicators</h2>
                <p class="text-xs text-slate-500">
                    Regional employment estimates based on PSA Labor Force Survey.
                </p>
            </div>
            <div class="flex items-center gap-2 bg-white border rounded-lg  px-3 py-2 text-xs">
                <span class="txet-slate-400">📅 KPI Period:</span>
                <select class="outline-none font-semibold">
                    <option>2024</option>
                     <option>2024</option>
                </select>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-100 rounded-xl m-5 p-6 relative">
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 m-5">
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
                    <div class="bg-white border rounded-xl p-5 shadow-sm border-l-4 border-red-500">
                    <p class="text-xs text-slate-500 font-semibold uppercase mb-2">Unemployment Rate</p>
                    <h2 class="text-3xl font-bold text-slate-800">3.6%</h2>
                    <p class="text-xs text-slate-500 mt-2">86k Unemployed Persons</p>
                </div>
                    <div class="bg-white border rounded-xl p-5 shadow-sm border-l-4 border-orange-500">
                    <p class="text-xs text-slate-500 font-semibold uppercase mb-2">Underemp. Rate</p>
                    <h2 class="text-3xl font-bold text-slate-800">10.5%</h2>
                    <p class="text-xs text-slate-500 mt-2">241k Seeking More Hours</p>
                </div>
                <div class="bg-white border rounded-xl p-5 shadow-sm border-l-4 border-green-500">
            <p class="text-xs text-slate-500 font-semibold uppercase mb-2">Participation Rate</p>
            <h2 class="text-3xl font-bold text-slate-800">57.7%</h2>

                    <div class="mt-4 h-1.5 bg-slate-100 rounded-full">
                        <div class="h-full w-[58%] bg-green-500 rounded-full"></div>
                    </div>

                    <p class="text-[10px] text-slate-400 mt-2 text-right">Active Workforce vs Pop 15+</p>
        </div>
     </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 m-5">

                
                <div class="bg-white border rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-slate-800">
                                Labor Force vs Employment Rate
                            </h3>
                            <p class="text-xs text-slate-500">
                                Comparing workforce size (bars) vs employment rate (line)
                            </p>
                        </div>

                        <div class="text-xs bg-slate-100 px-3 py-1 rounded-lg">
                            2019 – 2025
                        </div>
                    </div>

                    <div class="relative h-80 w-full">
                        <canvas id="laborEmploymentChart"></canvas>
                    </div>
                </div>

               
                <div class="bg-white border rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-slate-800">
                                Unemployment Volume
                            </h3>
                            <p class="text-xs text-slate-500">
                                Headcount of unemployed persons
                            </p>
                        </div>

                        <div class="text-xs bg-slate-100 px-3 py-1 rounded-lg">
                            2012 – 2025
                        </div>
                    </div>

                    <div class="relative h-80 w-full">
                        <canvas id="unemploymentChart"></canvas>
                    </div>
                </div>

            </div>
            
            
       <div class="m-5">
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
</div>
            <div class="flex items-center justify-center m-5 ">
                <div>
                    <p class="text-xs text-slate-500">
                        Source: Tab1-Employment-Davao-Region-with-JUL2025.xlsx (Rates) | Module 2 Sources: PhilJobNet, PSA ISLE, Industry Surveys.
                    </p>
                </div>
            </div>

</div> 
        <div class="flex-1 flex flex-col overflow-y-auto">
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
           <button @click="activeView = 'overview'"
           class="px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-md">
            Regional Statistics
            </button>

            <button
            @click="activeView = 'job-market'"
            class="px-4 py-2 text-sm font-semibold bg-blue-600 text-white rounded-md">
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
            <button class="bg-emerald-500/10 border border-emerald-500 text-emerald-500 px-5 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-500/20 transition">
                Update LMI Matrix
            </button>
        </div>
    </div>

    
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex justify-between mb-4">
                <h3 class="font-bold text-gray-800">Top 10 High-Volume Job Titles</h3>
                <span class="text-gray-300">ⓘ</span>
            </div>
            <canvas id="jobsChart" height="140"></canvas>
        </div>

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
        <div class="flex gap-2 mb-8">
            @foreach(['All', 'BPO/IT', 'Construction', 'Healthcare', 'Agriculture', 'Tourism'] as $tab)
            <button class="px-4 py-1 text-sm rounded-full {{ $loop->first ? 'bg-purple-600 text-white' : 'border text-gray-500 hover:bg-gray-50' }} transition">{{ $tab }}</button>
            @endforeach
        </div>

        <div class="grid grid-cols-2 gap-12">
            <div>
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

    <div class="flex items-center justify-center m-5 ">
        <div>
            <p class="text-xs text-slate-500">
                Source: Tab1-Employment-Davao-Region-with-JUL2025.xlsx (Rates) | Module 2 Sources: PhilJobNet, PSA ISLE, Industry Surveys.
            </p>
        </div>
    </div>
</div>


<div x-show="showReportModal" 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4"
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
    const chartData = @json($charts);
    console.log("Global Chart Data:", chartData);

    document.addEventListener('DOMContentLoaded', function () {
        
       
        const laborCtx = document.getElementById('laborEmploymentChart');
        if (laborCtx && chartData && chartData.labor_vs_employment) {
            new Chart(laborCtx.getContext('2d'), {
                data: {
                    labels: chartData.labor_vs_employment.labels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Labor Force Size (thousands)',
                            data: chartData.labor_vs_employment.labor,
                            backgroundColor: '#cbd5e1',
                            borderRadius: 6,
                            yAxisID: 'y'
                        },
                        {
                            type: 'line',
                            label: 'Employment Rate (%)',
                            data: chartData.labor_vs_employment.employment_rate,
                            borderColor: '#2563eb',
                            backgroundColor: '#2563eb',
                            tension: 0.35,
                            pointRadius: 4,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true },
                        y1: { position: 'right', min: 80, max: 100, grid: { drawOnChartArea: false } }
                    }
                }
            });
        }

        const unempCtx = document.getElementById('unemploymentChart');
        if (unempCtx && chartData && chartData.unemployment_volume) {
            new Chart(unempCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartData.unemployment_volume.labels,
                    datasets: [{
                        label: 'Unemployed Persons (thousands)',
                        data: chartData.unemployment_volume.values,
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
    });
</script>

</body>
</html>