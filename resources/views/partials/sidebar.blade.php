<aside :class="sidebarExpanded ? 'w-72' : 'w-20'"
    class="bg-gradient-to-b from-slate-800 via-slate-900 to-slate-950 text-slate-200 flex flex-col shadow-2xl z-10 h-screen overflow-hidden shrink-0 transition-all duration-300 ease-in-out scrollbar-thin scrollbar-thumb-slate-600 scrollbar-track-transparent hover:scrollbar-thumb-slate-500 relative">

    <!-- Subtle top accent -->
    <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-dole-blue via-dole-red to-dole-yellow opacity-60"></div>

    <!-- Logo Header -->
    <div class="p-5 border-b border-slate-700/50 bg-slate-800/50 backdrop-blur-sm relative">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- DOLE Logo -->
                <div class="w-11 h-11 flex items-center justify-center shrink-0 bg-slate-700/50 backdrop-blur-md rounded-xl p-2 ring-1 ring-slate-600/50 shadow-lg">
                    <img src="{{ asset('images/dole_logo.png') }}" alt="DOLE Logo" class="w-full h-full object-contain drop-shadow-md">
                </div>

                <!-- Title -->
                <div x-show="sidebarExpanded" x-transition.opacity class="leading-tight whitespace-nowrap">
                    <p class="font-bold text-base tracking-tight text-slate-100">Labor Market Intelligence</p>
                    <p class="text-[11px] text-slate-400 font-medium">Bridging Education & Industry</p>
                </div>
            </div>

            <!-- Toggle Button -->
            <button @click="sidebarExpanded = !sidebarExpanded"
                class="p-2 text-slate-400 hover:text-slate-200 transition-all focus:outline-none shrink-0 cursor-pointer bg-slate-700/50 backdrop-blur-md rounded-lg hover:bg-slate-600/50 ring-1 ring-slate-600/50">
                <svg x-show="sidebarExpanded" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
                <svg x-show="!sidebarExpanded" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-6 space-y-1.5 overflow-y-auto">
        <!-- Main Menu Section -->
        <div class="mb-6">
            <p x-show="sidebarExpanded"
                class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-3 px-3 whitespace-nowrap">
                Main Menu
            </p>

            <!-- Regional Statistics -->
            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('home') }}"
                    class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-xl transition-all group backdrop-blur-sm ring-1 ring-transparent hover:ring-slate-600/50">
                    <span class="text-xl shrink-0">📊</span>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-sm">Regional Statistics</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-24 bg-slate-800 text-slate-200 text-xs py-2.5 px-4 rounded-lg shadow-2xl z-50 whitespace-nowrap border border-slate-600/50 pointer-events-none backdrop-blur-md">
                    Regional Statistics
                </div>
            </div>

            <!-- Labor Demand -->
            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('Job.Market.Demands') }}"
                    class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-xl transition-all group backdrop-blur-sm ring-1 ring-transparent hover:ring-slate-600/50">
                    <span class="text-xl shrink-0">🔍</span>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-sm">Labor Demand</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-24 bg-slate-800 text-slate-200 text-xs py-2.5 px-4 rounded-lg shadow-2xl z-50 whitespace-nowrap border border-slate-600/50 pointer-events-none backdrop-blur-md">
                    Labor Demand
                </div>
            </div>

            <!-- Labor Supply -->
            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('Supply.Side') }}"
                    class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-xl transition-all group backdrop-blur-sm ring-1 ring-transparent hover:ring-slate-600/50">
                    <span class="text-xl shrink-0">📦</span>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-sm">Labor Supply</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-24 bg-slate-800 text-slate-200 text-xs py-2.5 px-4 rounded-lg shadow-2xl z-50 whitespace-nowrap border border-slate-600/50 pointer-events-none backdrop-blur-md">
                    Labor Supply
                </div>
            </div>

            <!-- Programs & Stories -->
            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-xl transition-all group backdrop-blur-sm ring-1 ring-transparent hover:ring-slate-600/50">
                    <span class="text-xl shrink-0">📖</span>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-sm">Programs & Stories</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-24 bg-slate-800 text-slate-200 text-xs py-2.5 px-4 rounded-lg shadow-2xl z-50 whitespace-nowrap border border-slate-600/50 pointer-events-none backdrop-blur-md">
                    Programs & Stories
                </div>
            </div>

            <!-- Labor Supply Data -->
            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('hei.graduate') }}"
                    class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-xl transition-all group backdrop-blur-sm ring-1 ring-transparent hover:ring-slate-600/50">
                    <span class="text-xl shrink-0">🎓</span>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-sm">Labor Supply Data</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-24 bg-slate-800 text-slate-200 text-xs py-2.5 px-4 rounded-lg shadow-2xl z-50 whitespace-nowrap border border-slate-600/50 pointer-events-none backdrop-blur-md">
                    Labor Supply Data
                </div>
            </div>

            <!-- In Demand Skills & Gap -->
            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('Skill.Gap.Demand') }}"
                    class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-xl transition-all group backdrop-blur-sm ring-1 ring-transparent hover:ring-slate-600/50">
                    <span class="text-xl shrink-0">⚖️</span>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-sm">In Demand Skills & Gap</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-24 bg-slate-800 text-slate-200 text-xs py-2.5 px-4 rounded-lg shadow-2xl z-50 whitespace-nowrap border border-slate-600/50 pointer-events-none backdrop-blur-md">
                    In Demand Skills & Gap
                </div>
            </div>

            <!-- Job Market Overview -->
            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('Job.Market.Overview') }}"
                    class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-xl transition-all group backdrop-blur-sm ring-1 ring-transparent hover:ring-slate-600/50">
                    <span class="text-xl shrink-0">📈</span>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-sm">Job Market Overview</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-24 bg-slate-800 text-slate-200 text-xs py-2.5 px-4 rounded-lg shadow-2xl z-50 whitespace-nowrap border border-slate-600/50 pointer-events-none backdrop-blur-md">
                    Job Market Overview
                </div>
            </div>

            <!-- Government Data -->
            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('Government.Data') }}"
                    class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-xl transition-all group backdrop-blur-sm ring-1 ring-transparent hover:ring-slate-600/50">
                    <span class="text-xl shrink-0">🗂️</span>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-sm">Government Data</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-24 bg-slate-800 text-slate-200 text-xs py-2.5 px-4 rounded-lg shadow-2xl z-50 whitespace-nowrap border border-slate-600/50 pointer-events-none backdrop-blur-md">
                    Government Data
                </div>
            </div>

            <!-- Stakeholder Engagement -->
            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('Stake.Holder') }}"
                    class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-xl transition-all group backdrop-blur-sm ring-1 ring-transparent hover:ring-slate-600/50">
                    <span class="text-xl shrink-0">🤝</span>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-sm">Stakeholder Engagement</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-24 bg-slate-800 text-slate-200 text-xs py-2.5 px-4 rounded-lg shadow-2xl z-50 whitespace-nowrap border border-slate-600/50 pointer-events-none backdrop-blur-md">
                    Stakeholder Engagement
                </div>
            </div>

            <!-- Reports -->
            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('Report') }}"
                    class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-xl transition-all group backdrop-blur-sm ring-1 ring-transparent hover:ring-slate-600/50">
                    <span class="text-xl shrink-0">📑</span>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-sm">Reports</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-24 bg-slate-800 text-slate-200 text-xs py-2.5 px-4 rounded-lg shadow-2xl z-50 whitespace-nowrap border border-slate-600/50 pointer-events-none backdrop-blur-md">
                    Reports
                </div>
            </div>
        </div>

        <!-- Account Section -->
        <div class="pt-4 mt-4 border-t border-slate-700/50">
            <p x-show="sidebarExpanded"
                class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-3 px-3 whitespace-nowrap">
                Account
            </p>

            <!-- Settings -->
            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('Setting') }}"
                    class="flex items-center gap-3 px-3 py-2.5 text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-xl transition-all group backdrop-blur-sm ring-1 ring-transparent hover:ring-slate-600/50">
                    <span class="text-xl shrink-0">⚙️</span>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-sm">Settings</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-24 bg-slate-800 text-slate-200 text-xs py-2.5 px-4 rounded-lg shadow-2xl z-50 whitespace-nowrap border border-slate-600/50 pointer-events-none backdrop-blur-md">
                    Settings
                </div>
            </div>

            <!-- Logout -->
            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-xl transition-all group backdrop-blur-sm ring-1 ring-transparent hover:ring-red-500/30">
                    <span class="text-xl shrink-0">🚪</span>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-sm">Logout</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-24 bg-red-900/90 text-red-100 text-xs py-2.5 px-4 rounded-lg shadow-2xl z-50 whitespace-nowrap border border-red-700/50 pointer-events-none backdrop-blur-md">
                    Logout
                </div>
            </div>
        </div>
    </nav>

    <!-- Footer -->
    <div class="p-4 bg-slate-950/50 backdrop-blur-md text-[10px] text-center text-slate-500 border-t border-slate-800/50">
        <span x-show="sidebarExpanded" class="font-medium">© 2026 DOLE Region XI</span>
        <span x-show="!sidebarExpanded" class="font-medium">© 2026</span>
    </div>
</aside>