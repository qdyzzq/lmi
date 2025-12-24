<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMI Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden">

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
            
            <a href="#" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="opacity-70 group-hover:opacity-100">🎓</span> HEI Graduate Data
            </a>

            <a href="#" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="opacity-70 group-hover:opacity-100">⚖️</span> Skills Gap & Demand
            </a>

            <a href="#" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="opacity-70 group-hover:opacity-100">📈</span> Job Market Overview
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
            © 2025 DOLE Region XI
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Dashboard Overview</h2>
            <div class="flex items-center gap-4">
                <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200">
                    📅 Region XI • 2024
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <p class="text-slate-500 text-xs font-bold uppercase mb-1">Total Graduates</p>
                    <h3 class="text-3xl font-black text-slate-900">46,473</h3>
                    <p class="text-blue-500 text-[10px] mt-2 font-bold">Davao Region • 2024</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <p class="text-slate-500 text-xs font-bold uppercase mb-1">Employed</p>
                    <h3 class="text-3xl font-black text-green-600">29,583</h3>
                    <p class="text-slate-400 text-[10px] mt-2 italic">63.7% within 6 months</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <p class="text-slate-500 text-xs font-bold uppercase mb-1">Unemployed</p>
                    <h3 class="text-3xl font-black text-red-500">10,000</h3>
                    <p class="text-slate-400 text-[10px] mt-2">21.5% seeking work</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <p class="text-slate-500 text-xs font-bold uppercase mb-1">Mismatch Rate</p>
                    <h3 class="text-3xl font-black text-orange-500">34.3%</h3>
                    <p class="text-slate-400 text-[10px] mt-2 font-medium">Industry-Skill Gap</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-slate-800">Job Market Overview</h3>
            <select class="text-xs border rounded-md px-2 py-1 outline-none font-medium">
                <option>2024</option>
            </select>
        </div>
        
        <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 relative overflow-hidden group">
            <div class="absolute right-[-20px] bottom-[-10px] opacity-10 text-7xl transition-transform group-hover:scale-110">📍</div>
            
            <p class="text-blue-600 font-bold text-[10px] uppercase tracking-[0.2em] mb-2">Davao Region</p>
            <div class="flex items-baseline gap-2">
                <h4 class="text-4xl font-black text-slate-900 tracking-tight">20,781</h4>
                <p class="text-sm font-bold text-slate-500">Job Vacancies</p>
            </div>
            <p class="text-green-600 text-[10px] mt-4 font-bold flex items-center gap-1 bg-white/50 w-fit px-2 py-1 rounded-full">
                <span class="text-xs">▲</span> +3,774 of HEIs region
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-slate-800">Graduate Success by Gender</h3>
            <div class="flex gap-2 text-slate-400">
                <select class="text-[10px] border rounded px-1 py-0.5 outline-none"><option>All</option></select>
                <select class="text-[10px] border rounded px-1 py-0.5 outline-none"><option>All</option></select>
            </div>
        </div>

        <div class="flex items-center justify-around">
            <div class="w-36 h-36 rounded-full border-8 border-white shadow-xl relative" 
                 style="background: conic-gradient(#3b82f6 0% 56.2%, #10b981 56.2% 81.4%, #ef4444 81.4% 100%);">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="bg-white w-16 h-16 rounded-full flex flex-col items-center justify-center shadow-inner">
                         <span class="text-[10px] font-black text-slate-700">56.2%</span>
                    </div>
                </div>
            </div>

            <div class="space-y-2.5">
                <div class="flex items-center gap-2 text-[10px] font-bold text-slate-600">
                    <span class="w-2.5 h-2.5 rounded-sm bg-blue-500"></span> 9,778 Male graduates
                </div>
                <div class="flex items-center gap-2 text-[10px] font-bold text-slate-600">
                    <span class="w-2.5 h-2.5 rounded-sm bg-green-500"></span> 1,546 iOS graduates
                </div>
                <div class="flex items-center gap-2 text-[10px] font-bold text-slate-600">
                    <span class="w-2.5 h-2.5 rounded-sm bg-red-500"></span> 700 Underemployed
                </div>
                <div class="flex items-center gap-2 text-[10px] font-bold text-slate-600">
                    <span class="w-2.5 h-2.5 rounded-sm bg-yellow-400"></span> 9,086 Graduates
                </div>
            </div>
        </div>
    </div>
</div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h3 class="font-bold text-slate-800">HEI Graduate Data</h3>
                        <div class="flex gap-2">
                            <select class="text-xs border rounded-md px-2 py-1 outline-none focus:ring-2 ring-blue-500"><option>2024</option></select>
                            <select class="text-xs border rounded-md px-2 py-1 outline-none focus:ring-2 ring-blue-500"><option>Davao City</option></select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4">Program</th>
                                    <th class="px-6 py-4 text-center">Graduates</th>
                                    <th class="px-6 py-4 text-center text-green-600">Employed</th>
                                    <th class="px-6 py-4">Mismatch Rate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-slate-700">Business Admin</td>
                                    <td class="px-6 py-4 text-center text-slate-500">6,050</td>
                                    <td class="px-6 py-4 text-center text-green-600 font-bold">62.2%</td>
                                    <td class="px-6 py-4 w-48">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                                <div class="bg-orange-500 h-full w-1/2"></div>
                                            </div>
                                            <span class="text-[10px] font-bold text-slate-400">50%</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-slate-700">Info. Tech</td>
                                    <td class="px-6 py-4 text-center text-slate-500">5,230</td>
                                    <td class="px-6 py-4 text-center text-green-600 font-bold">62.2%</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                                <div class="bg-orange-600 h-full w-[45%]"></div>
                                            </div>
                                            <span class="text-[10px] font-bold text-slate-400">45%</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="font-bold text-slate-800 mb-4">In Mismatch Feedback</h3>
                    <div class="bg-blue-600 text-white p-4 rounded-xl mb-6 shadow-lg shadow-blue-200">
                        <p class="text-[10px] font-bold uppercase opacity-80 mb-1 tracking-widest">Core Tool</p>
                        <p class="text-sm font-medium">Curriculum Alignment Tool</p>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-xs font-bold text-slate-600">IT-BPM</span>
                            <span class="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-1 rounded-md">Analytical</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-xs font-bold text-slate-600">Construction</span>
                            <span class="text-[10px] font-bold bg-orange-100 text-orange-700 px-2 py-1 rounded-md">Project Mgmt</span>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>