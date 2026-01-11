<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      @vite('resources/css/app.css')
    <title>LMI</title>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden">
    <aside class="w-72 bg-[#1e3a8a] text-white flex flex-col shadow-xl z-10">
        <div class="p-6 border-b border-blue-800 ">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-900 font-bold">LMI</div>
                <div class="leading-tight">
                    <p class="font-bold text-sm">Labor Market Intelligence</p>
                    <p class="text-[10px] opacity-70 italic">Bridging Education & Industry</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-auto">
            <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mb-4 px-2">Main Menu</p>

            <a href="{{ route('home')}}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="opacity-70 group-hover:opacity-100">📊</span> Dashboard
            </a>
            
            <a href="{{ route('hei.graduate') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class=" opacity-70 group-hover:opacity-100">🎓</span> HEI Graduate Data
            </a>

            <a href="{{ route('Skill.Gap.Demand') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="opacity-70 group-hover:opacity-100">⚖️</span> Skills Gap & Demand
            </a>

             <a href="{{ route('Job.Market.Overview') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="opacity-70 group-hover:opacity-100">📈</span> Job Market Overview
            </a>
            
            <a href="{{ route('Government.Data') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class=" opacity-70 group-hover:opacity-100">🗂️</span> Government Data
            </a>

            <a href="{{ route('Stake.Holder.Engagement') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class=" opacity-70 group-hover:opacity-100">🤝</span> Stakeholder Engagement
            </a>

             <a href="{{ route('Report') }}" class="flex items-center gap-3 p-3  bg-yellow-400 text-blue-900 font-bold  rounded-lg transition shadow-md">
                <span class=" opacity-70 group-hover:opacity-100">📑</span> Reports
            </a>

            <div class="pt-6">
                <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mb-4 px-2">Account</p>
                <a href="{{ route('Setting') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:blue-800 rounded-lg transition group">
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
        <div class="flex-1 flex flex-col overflow-hidden">
        
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Reports</h2>
            <div class="flex items-center gap-4">
                <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200">
                    📅 Region XI • 2024
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>
        </div>

</body>
</html>