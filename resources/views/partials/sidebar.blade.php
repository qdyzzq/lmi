<aside
    :class="sidebarExpanded ? 'w-72' : 'w-20'"
    class="relative bg-slate-900 text-white flex flex-col shadow-xl z-10
           overflow-y-auto overflow-x-visible
           transition-all duration-300 ease-in-out
           scrollbar-thin scrollbar-thumb-white/20 scrollbar-track-transparent
           hover:scrollbar-thumb-white/40">

    <!-- Header -->
    <div class="p-4 border-b border-slate-700/50 relative">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex items-center justify-center shrink-0 overflow-hidden">
                <img src="{{ asset('images/dole_logo.png') }}" alt="LMI Logo" class="w-full h-full object-contain">
            </div>

            <div x-show="sidebarExpanded" x-transition.opacity class="leading-tight whitespace-nowrap">
                <p class="font-bold text-sm">Labor Market Intelligence</p>
                <p class="text-[10px] opacity-70 italic">Bridging Education & Industry</p>
            </div>
        </div>

        <!-- Collapse Toggle -->
        <button
            @click="sidebarExpanded = !sidebarExpanded"
            class="absolute -right-3 top-6 z-20
                   w-7 h-7 rounded-full
                   bg-white shadow-md border border-slate-200
                   flex items-center justify-center
                   text-slate-600 hover:text-slate-900
                   transition-all">

            <!-- PanelLeftClose -->
            <svg x-show="sidebarExpanded" xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4"
                 fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="16" rx="2"/>
                <path d="M9 4v16"/>
                <path d="M14 8l-3 4 3 4"/>
            </svg>

            <!-- PanelLeftOpen -->
            <svg x-show="!sidebarExpanded" xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4"
                 fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="16" rx="2"/>
                <path d="M9 4v16"/>
                <path d="M10 8l3 4-3 4"/>
            </svg>
        </button>
    </div>

<nav class="flex-1 px-4 py-6 space-y-1">
        <p x-show="sidebarExpanded"
            class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-4 px-2 whitespace-nowrap">
            Main Menu</p>

        <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
            @mouseleave="hovered = false">
            <a href="{{ route('home') }}"
                 class="flex items-center gap-3 p-3 rounded-lg transition group
   {{ request()->routeIs('home') ? 'bg-blue-900 border-l-4 border-blue-500 text-white' : 'text-slate-200 hover:bg-blue-800' }}">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0"
     fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M11 3v18m4-14v14m4-10v10M3 13v8" />
</svg>
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
                class="flex items-center gap-3 p-3 rounded-lg transition group
   {{ request()->routeIs('Job.Market.Demands') ? 'bg-blue-900 border-l-4 border-blue-500 text-white' : 'text-slate-200 hover:bg-blue-800' }}">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0"
     fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M16 16l4 4m-2-9a6 6 0 11-12 0 6 6 0 0112 0z" />
</svg>
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
class="flex items-center gap-3 p-3 rounded-lg transition group
   {{ request()->routeIs('Supply.Side') ? 'bg-blue-900 border-l-4 border-blue-500 text-white' : 'text-slate-200 hover:bg-blue-800' }}">             <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0"
     fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 110-8 4 4 0 010 8zm6 2a3 3 0 100-6" />
</svg>
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
                class="flex items-center gap-3 p-3 text-slate-200 hover:bg-blue-800 rounded-lg transition group">
             <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0"
     fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 6v12m8-6H4m16-6H4" />
</svg>
                <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Employment Programs
</span>
            </a>
            <div x-show="!sidebarExpanded && hovered"
                class="fixed left-22 bg-blue-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-blue-700 pointer-events-none">
                DOLE Employment Programs
            </div>
        </div>

        <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
            @mouseleave="hovered = false">
            <a href="{{ route('hei.graduate') }}"
                class="flex items-center gap-3 p-3 text-slate-200 hover:bg-blue-800 rounded-lg transition group">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0"
     fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m6-3v3" />
</svg>
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
                class="flex items-center gap-3 p-3 text-slate-200 hover:bg-blue-800 rounded-lg transition group">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0"
     fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 3v18m-7-7h14M5 14l-2 4h4l-2-4zm14 0l-2 4h4l-2-4z" />
</svg>
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
                class="flex items-center gap-3 p-3 text-slate-200 hover:bg-blue-800 rounded-lg transition group">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0"
     fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M3 3v18h18M7 14l4-4 3 3 5-6" />
</svg>
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
                class="flex items-center gap-3 p-3 text-slate-200 hover:bg-blue-800 rounded-lg transition group">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0"
     fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <ellipse cx="12" cy="5" rx="9" ry="3" />
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M3 5v14c0 1.7 4 3 9 3s9-1.3 9-3V5" />
</svg>
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
                class="flex items-center gap-3 p-3 text-slate-2 \
                00 hover:bg-blue-800 rounded-lg transition group">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0"
     fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M17 8l4 4-4 4M7 16l-4-4 4-4m4-2h2a4 4 0 014 4v2a4 4 0 01-4 4h-2a4 4 0 01-4-4v-2a4 4 0 014-4z" />
</svg>
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
                class="flex items-center gap-3 p-3 text-slate-200 hover:bg-blue-800 rounded-lg transition group">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0"
     fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M7 3h7l5 5v13H7zM14 3v5h5" />
</svg>
                <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Reports</span>
            </a>
            <div x-show="!sidebarExpanded && hovered"
                class="fixed left-22 bg-blue-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-blue-700 pointer-events-none">
                Reports
            </div>
        </div>

        <div class="pt-6">
            <p x-show="sidebarExpanded"
                class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-4 px-2 whitespace-nowrap">
                Account</p>

            <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
                @mouseleave="hovered = false">
                <a href="{{ route('Setting') }}"
                    class="flex items-center gap-3 p-3 text-slate-200 hover:bg-blue-800 rounded-lg transition group">
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0"
     fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 8a4 4 0 100 8 4 4 0 000-8zm8 4l-2 1 1 2-2 2-2-1-1 2h-4l-1-2-2 1-2-2 1-2-2-1V8l2-1-1-2 2-2 2 1 1-2h4l1 2 2-1 2 2-1 2 2 1v4z" />
</svg>
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
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0"
     fill="none" stroke="currentColor" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
</svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Logout</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-22 bg-red-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-red-700 pointer-events-none">
                    Logout
                </div>
            </div>
        </div>
    </nav>

    <!-- Footer -->
    <div class="p-4 bg-blue-950 text-[10px] text-center opacity-50">
        <span x-show="sidebarExpanded">© 2026 DOLE Region XI</span>
        <span x-show="!sidebarExpanded">© 2026</span>
    </div>
</aside>