<aside :class="sidebarExpanded ? 'w-72' : 'w-20'"
    class="bg-[#39527F] text-white flex flex-col shadow-xl z-10 overflow-y-auto overflow-x-hidden transition-all duration-300 ease-in-out scrollbar-thin scrollbar-thumb-white/20 scrollbar-track-transparent hover:scrollbar-thumb-white/40">

    <div class="p-4 border-b border-blue-800">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex items-center justify-center shrink-0 overflow-hidden">
                    <img src="{{ asset('images/dole_logo.png') }}" alt="LMI Logo" class="w-full h-full object-contain">
                </div>

                <div x-show="sidebarExpanded" x-transition.opacity class="leading-tight whitespace-nowrap">
                    <p class="font-bold text-sm">Labor Market Intelligence</p>
                    <p class="text-[10px] opacity-70 italic">Bridging Education & Industry</p>
                </div>
            </div>

            <button @click="sidebarExpanded = !sidebarExpanded"
                class="p-1 text-white/70 hover:text-white transition-colors focus:outline-none shrink-0 cursor-pointer bg-blue-700/50 rounded hover:bg-blue-600">
                <svg x-show="sidebarExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
                <svg x-show="!sidebarExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-1">
        <p x-show="sidebarExpanded"
            class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mb-4 px-2 whitespace-nowrap">
            Main Menu</p>

        <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
            @mouseleave="hovered = false">
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="text-xl shrink-0">📊</span>
                <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Regional Statistics</span>
            </a>
            <div x-show="!sidebarExpanded && hovered"
                class="fixed left-22 bg-blue-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-blue-700 pointer-events-none">
                Regional Statistics
            </div>
        </div>

        <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
            @mouseleave="hovered = false">
            <a href="{{ route('Job.Market.Demands') }}"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="text-xl shrink-0">🔍</span>
                <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Labor Demand</span>
            </a>
            <div x-show="!sidebarExpanded && hovered"
                class="fixed left-22 bg-blue-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-blue-700 pointer-events-none">
                Labor Demand
            </div>
        </div>

        <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
            @mouseleave="hovered = false">
            <a href="{{ route('Supply.Side') }}"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="text-xl shrink-0">📦</span>
                <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Labor Supply</span>
            </a>
            <div x-show="!sidebarExpanded && hovered"
                class="fixed left-22 bg-blue-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-blue-700 pointer-events-none">
                Labor Supply
            </div>
        </div>

        <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
            @mouseleave="hovered = false">
            <a href="#"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="text-xl shrink-0">📖</span>
                <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Programs & Stories</span>
            </a>
            <div x-show="!sidebarExpanded && hovered"
                class="fixed left-22 bg-blue-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-blue-700 pointer-events-none">
                Programs & Stories
            </div>
        </div>

        <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
            @mouseleave="hovered = false">
            <a href="{{ route('hei.graduate') }}"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="text-xl shrink-0">🎓</span>
                <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Labor Supply Data</span>
            </a>
            <div x-show="!sidebarExpanded && hovered"
                class="fixed left-22 bg-blue-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-blue-700 pointer-events-none">
                Labor Supply Data
            </div>
        </div>

        <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
            @mouseleave="hovered = false">
            <a href="{{ route('Skill.Gap.Demand') }}"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="text-xl shrink-0">⚖️</span>
                <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">In Demand Skills &
                    Gap</span>
            </a>
            <div x-show="!sidebarExpanded && hovered"
                class="fixed left-22 bg-blue-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-blue-700 pointer-events-none">
                In Demand Skills & Gap
            </div>
        </div>

        <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
            @mouseleave="hovered = false">
            <a href="{{ route('Job.Market.Overview') }}"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="text-xl shrink-0">📈</span>
                <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Job Market
                    Overview</span>
            </a>
            <div x-show="!sidebarExpanded && hovered"
                class="fixed left-22 bg-blue-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-blue-700 pointer-events-none">
                Job Market Overview
            </div>
        </div>

        <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
            @mouseleave="hovered = false">
            <a href="{{ route('Government.Data') }}"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="text-xl shrink-0">🗂️</span>
                <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Government Data</span>
            </a>
            <div x-show="!sidebarExpanded && hovered"
                class="fixed left-22 bg-blue-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-blue-700 pointer-events-none">
                Government Data
            </div>
        </div>

        <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
            @mouseleave="hovered = false">
            <a href="{{ route('Stake.Holder') }}"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="text-xl shrink-0">🤝</span>
                <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Stakeholder
                    Engagement</span>
            </a>
            <div x-show="!sidebarExpanded && hovered"
                class="fixed left-22 bg-blue-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-blue-700 pointer-events-none">
                Stakeholder Engagement
            </div>
        </div>

        <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
            @mouseleave="hovered = false">
            <a href="{{ route('Report') }}"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="text-xl shrink-0">📑</span>
                <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Reports</span>
            </a>
            <div x-show="!sidebarExpanded && hovered"
                class="fixed left-22 bg-blue-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-blue-700 pointer-events-none">
                Reports
            </div>
        </div>

        <div class="pt-6">
            <p x-show="sidebarExpanded"
                class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mb-4 px-2 whitespace-nowrap">
                Account</p>

            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
                @mouseleave="hovered = false">
                <a href="{{ route('Setting') }}"
                    class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                    <span class="text-xl shrink-0">⚙️</span>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Settings</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-22 bg-blue-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-blue-700 pointer-events-none">
                    Settings
                </div>
            </div>

            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
                @mouseleave="hovered = false">
                <a href="#"
                    class="flex items-center gap-3 p-3 text-red-300 hover:bg-red-900/30 rounded-lg transition group">
                    <span class="text-xl shrink-0">🚪</span>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Logout</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-22 bg-red-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-red-700 pointer-events-none">
                    Logout
                </div>
            </div>
        </div>
    </nav>

    <div class="p-4 bg-blue-950 text-[10px] text-center opacity-50">
        <span x-show="sidebarExpanded">© 2026 DOLE Region XI</span>
        <span x-show="!sidebarExpanded">© 2026</span>
    </div>
</aside>
