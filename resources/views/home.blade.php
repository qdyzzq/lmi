<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>LMI</title>
</head>
<body class="bg-slate-100 flex min-h-screen ">
    
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

        <button class="px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-md">
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