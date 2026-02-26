<<<<<<< HEAD
<aside
    x-data="{
        sidebarExpanded: JSON.parse(localStorage.getItem('sidebarExpanded') ?? 'true'),
        toggleSidebar() {
            this.sidebarExpanded = !this.sidebarExpanded;
            localStorage.setItem('sidebarExpanded', this.sidebarExpanded);
        }
    }"
    x-init="
        $el.classList.remove('w-64', 'w-16');
        $el.classList.remove('transition-all', 'duration-300', 'ease-in-out');
        $el.classList.add(sidebarExpanded ? 'w-64' : 'w-16');
        $nextTick(() => {
            $el.style.visibility = 'visible';
            setTimeout(() => $el.classList.add('transition-all', 'duration-300', 'ease-in-out'), 50);
        });
    "
    :class="sidebarExpanded ? 'w-64' : 'w-16'"
    style="visibility: hidden;"
    class="bg-white text-slate-700 flex flex-col shadow-sm z-10 h-screen overflow-visible shrink-0 transition-all duration-300 ease-in-out border-r border-slate-200 relative">

    <!-- Top accent line -->
    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-blue-600 via-blue-400 to-sky-300"></div>

    <!-- Edge toggle handle — floats on the right border, vertically centered -->
    <button @click="toggleSidebar()"
        class="absolute -right-3.5 top-1/2 -translate-y-1/2 z-50 w-7 h-7 flex items-center justify-center rounded-full bg-white border border-slate-200 shadow-md hover:bg-blue-50 hover:border-blue-400 hover:text-blue-600 text-slate-400 transition-all duration-200 cursor-pointer">
        <svg x-show="sidebarExpanded" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
        </svg>
        <svg x-show="!sidebarExpanded" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Logo Header -->
    <div class="pt-5 pb-4 border-b border-slate-100" :class="sidebarExpanded ? 'px-3' : 'px-2'">
        <!-- Expanded state -->
        <div x-show="sidebarExpanded" class="flex items-center gap-2 min-w-0">
            <div class="w-9 h-9 flex items-center justify-center shrink-0 bg-slate-50 border border-slate-200 rounded-xl p-1.5 shadow-sm">
                <img src="{{ asset('images/dole_logo.png') }}" alt="DOLE Logo" class="w-full h-full object-contain">
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-[13px] text-slate-900 tracking-tight truncate">Labor Market Intelligence</p>
                <p class="text-[10px] text-slate-400 italic mt-0.5 truncate">Bridging Education & Industry</p>
            </div>
        </div>

        <!-- Collapsed state — just logo centered -->
        <div x-show="!sidebarExpanded" class="flex justify-center">
            <div class="w-9 h-9 flex items-center justify-center bg-slate-50 border border-slate-200 rounded-xl p-1.5 shadow-sm">
                <img src="{{ asset('images/dole_logo.png') }}" alt="DOLE Logo" class="w-full h-full object-contain">
            </div>
=======
<aside :class="sidebarExpanded ? 'w-72' : 'w-20'"
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
>>>>>>> mod4final
        </div>
        <!-- Collapse Toggle -->
        <button @click="sidebarExpanded = !sidebarExpanded"
            class="absolute -right-3 top-6 z-20
                   w-7 h-7 rounded-full
                   bg-white shadow-md border border-slate-200
                   flex items-center justify-center
                   text-slate-600 hover:text-slate-900
                   transition-all">
            <!-- PanelLeftClose -->
            <svg x-show="sidebarExpanded" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="16" rx="2" />
                <path d="M9 4v16" />
                <path d="M14 8l-3 4 3 4" />
            </svg>
            <!-- PanelLeftOpen -->
            <svg x-show="!sidebarExpanded" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="16" rx="2" />
                <path d="M9 4v16" />
                <path d="M10 8l3 4-3 4" />
            </svg>
        </button>
    </div>
<<<<<<< HEAD

    <!-- Navigation -->
    <nav class="flex-1 px-2 py-4 space-y-0.5 overflow-visible">
        <div class="mb-4">
            <p x-show="sidebarExpanded"
                class="text-[9px] uppercase tracking-[0.14em] text-slate-400 font-semibold font-mono mb-2 px-2 whitespace-nowrap">
                Main Menu
            </p>

            <!-- Regional Statistics -->
            <div class="relative" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-2.5 py-2.5 rounded-r-lg transition-all group border-l-[3px]
                        {{ request()->routeIs('admin.dashboard')
                            ? 'text-blue-700 bg-blue-50 border-blue-500'
                            : 'text-slate-800 border-transparent hover:text-blue-700 hover:bg-blue-50 hover:border-blue-500' }}">
                    <svg class="w-[18px] h-[18px] shrink-0 transition-opacity {{ request()->routeIs('admin.dashboard') ? 'opacity-100' : 'opacity-60 group-hover:opacity-100' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5h4.5v7.5H3zM9.75 9h4.5v12h-4.5zM16.5 4.5H21V21h-4.5z"/>
                    </svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-[13.5px]">Regional Statistics</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="absolute left-full top-1/2 -translate-y-1/2 ml-4 bg-slate-800 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg z-50 whitespace-nowrap pointer-events-none">
                    Regional Statistics
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-800"></div>
                </div>
            </div>

            <!-- Job Titles Form -->
            <div class="relative" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('admin.job-titles.form') }}"
                    class="flex items-center gap-3 px-2.5 py-2.5 rounded-r-lg transition-all group border-l-[3px]
                        {{ request()->routeIs('admin.job-titles.form')
                            ? 'text-blue-700 bg-blue-50 border-blue-500'
                            : 'text-slate-800 border-transparent hover:text-blue-700 hover:bg-blue-50 hover:border-blue-500' }}">
                    <svg class="w-[18px] h-[18px] shrink-0 transition-opacity {{ request()->routeIs('admin.job-titles.form') ? 'opacity-100' : 'opacity-60 group-hover:opacity-100' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-[13.5px]">Job Titles Form</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="absolute left-full top-1/2 -translate-y-1/2 ml-4 bg-slate-800 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg z-50 whitespace-nowrap pointer-events-none">
                    Job Titles Form
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-800"></div>
                </div>
            </div>

            <!-- Licensure Passing Rates -->
            <div class="relative" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('admin.licensure-rates.form') }}"
                    class="flex items-center gap-3 px-2.5 py-2.5 rounded-r-lg transition-all group border-l-[3px]
                        {{ request()->routeIs('admin.licensure-rates.form')
                            ? 'text-blue-700 bg-blue-50 border-blue-500'
                            : 'text-slate-800 border-transparent hover:text-blue-700 hover:bg-blue-50 hover:border-blue-500' }}">
                    <svg class="w-[18px] h-[18px] shrink-0 transition-opacity {{ request()->routeIs('admin.licensure-rates.form') ? 'opacity-100' : 'opacity-60 group-hover:opacity-100' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-.44 3.899 3.745 3.745 0 01-3.899.44A3.745 3.745 0 0112 21a3.745 3.745 0 01-3.068-1.593 3.745 3.745 0 01-3.899-.44 3.745 3.745 0 01-.44-3.899A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 01.44-3.899 3.745 3.745 0 013.899-.44A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.899.44 3.746 3.746 0 01.44 3.899A3.746 3.746 0 0121 12z"/>
                    </svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-[13.5px]">Licensure Passing Rates</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="absolute left-full top-1/2 -translate-y-1/2 ml-4 bg-slate-800 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg z-50 whitespace-nowrap pointer-events-none">
                    Licensure Passing Rates
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-800"></div>
                </div>
            </div>

            <!-- Enrollment Form -->
            <div class="relative" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('admin.discipline-enrollment.form') }}"
                    class="flex items-center gap-3 px-2.5 py-2.5 rounded-r-lg transition-all group border-l-[3px]
                        {{ request()->routeIs('admin.discipline-enrollment.form')
                            ? 'text-blue-700 bg-blue-50 border-blue-500'
                            : 'text-slate-800 border-transparent hover:text-blue-700 hover:bg-blue-50 hover:border-blue-500' }}">
                    <svg class="w-[18px] h-[18px] shrink-0 transition-opacity {{ request()->routeIs('admin.discipline-enrollment.form') ? 'opacity-100' : 'opacity-60 group-hover:opacity-100' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-[13.5px]">Enrollment Form</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="absolute left-full top-1/2 -translate-y-1/2 ml-4 bg-slate-800 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg z-50 whitespace-nowrap pointer-events-none">
                    Enrollment Form
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-800"></div>
                </div>
            </div>

            <!-- Graduate Form -->
            <div class="relative" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('admin.discipline-graduate.form') }}"
                    class="flex items-center gap-3 px-2.5 py-2.5 rounded-r-lg transition-all group border-l-[3px]
                        {{ request()->routeIs('admin.discipline-graduate.form')
                            ? 'text-blue-700 bg-blue-50 border-blue-500'
                            : 'text-slate-800 border-transparent hover:text-blue-700 hover:bg-blue-50 hover:border-blue-500' }}">
                    <svg class="w-[18px] h-[18px] shrink-0 transition-opacity {{ request()->routeIs('admin.discipline-graduate.form') ? 'opacity-100' : 'opacity-60 group-hover:opacity-100' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                    </svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-[13.5px]">Graduate Form</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="absolute left-full top-1/2 -translate-y-1/2 ml-4 bg-slate-800 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg z-50 whitespace-nowrap pointer-events-none">
                    Graduate Form
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-800"></div>
                </div>
            </div>
                <!-- Analysis Template -->
            <div class="relative" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('admin.supply-side-editor') }}"   {{-- ← changed --}}
                    class="flex items-center gap-3 px-2.5 py-2.5 rounded-r-lg transition-all group border-l-[3px]
                        {{ request()->routeIs('admin.supply-side-editor')   {{-- ← changed --}}
                            ? 'text-blue-700 bg-blue-50 border-blue-500'
                            : 'text-slate-800 border-transparent hover:text-blue-700 hover:bg-blue-50 hover:border-blue-500' }}">
                    <svg class="w-[18px] h-[18px] shrink-0 transition-opacity {{ request()->routeIs('admin.supply-side-editor') ? 'opacity-100' : 'opacity-60 group-hover:opacity-100' }}"   {{-- ← changed --}}
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971z"/>
                    </svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-[13.5px]">Analysis Labor Supply</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="absolute left-full top-1/2 -translate-y-1/2 ml-4 bg-slate-800 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg z-50 whitespace-nowrap pointer-events-none">
                    Analysis Labor Supply
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-800"></div>
                </div>
            </div>
            <!-- Analysis Template JobMarketDemand Side -->
            <div class="relative" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('admin.template-editor') }}"   {{-- ← changed --}}
                    class="flex items-center gap-3 px-2.5 py-2.5 rounded-r-lg transition-all group border-l-[3px]
                        {{ request()->routeIs('admin.template-editor')   {{-- ← changed --}}
                            ? 'text-blue-700 bg-blue-50 border-blue-500'
                            : 'text-slate-800 border-transparent hover:text-blue-700 hover:bg-blue-50 hover:border-blue-500' }}">
                    <svg class="w-[18px] h-[18px] shrink-0 transition-opacity {{ request()->routeIs('admin.stemplate-editor') ? 'opacity-100' : 'opacity-60 group-hover:opacity-100' }}"   {{-- ← changed --}}
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.073a2.25 2.25 0 01-2.25 2.25H5.25a2.25 2.25 0 01-2.25-2.25V6.75A2.25 2.25 0 015.25 4.5h7.5M9 4.5V3a.75.75 0 01.75-.75h4.5A.75.75 0 0115 3v1.5m-5.25 6h6M3.75 10.5h7.5"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5l1.5-1.5a1.06 1.06 0 00-1.5-1.5L18 9l1.5 1.5zm0 0l-4.5 4.5-2 .5.5-2 4-3z"/>
                    </svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-[13.5px]">Analysis JobMarketDemands</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="absolute left-full top-1/2 -translate-y-1/2 ml-4 bg-slate-800 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg z-50 whitespace-nowrap pointer-events-none">
                    Analysis JobMarketDemands
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-800"></div>
                </div>
            </div>


            <!-- LMI Submissions -->
            <div class="relative" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('admin.lmi-submissions.index') }}"
                    class="flex items-center gap-3 px-2.5 py-2.5 rounded-r-lg transition-all group border-l-[3px]
                        {{ request()->routeIs('admin.lmi-submissions.*')
                            ? 'text-blue-700 bg-blue-50 border-blue-500'
                            : 'text-slate-800 border-transparent hover:text-blue-700 hover:bg-blue-50 hover:border-blue-500' }}">
                    <svg class="w-[18px] h-[18px] shrink-0 transition-opacity {{ request()->routeIs('admin.lmi-submissions.*') ? 'opacity-100' : 'opacity-60 group-hover:opacity-100' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z"/>
                    </svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-[13.5px]">LMI Submissions</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="absolute left-full top-1/2 -translate-y-1/2 ml-4 bg-slate-800 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg z-50 whitespace-nowrap pointer-events-none">
                    LMI Submissions
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-800"></div>
=======
    <nav class="flex-1 px-4 py-6 space-y-1">
        <p x-show="sidebarExpanded"
            class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-4 px-2 whitespace-nowrap">
            Main Menu</p>
        <div class="relative group/item" x-data="{ hovered: false }" @mouseenter="hovered = true"
            @mouseleave="hovered = false">
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 p-3 rounded-lg transition group
   {{ request()->routeIs('home') ? 'bg-blue-900 border-l-4 border-blue-500 text-white' : 'text-slate-200 hover:bg-blue-800' }}">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
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
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
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
   {{ request()->routeIs('Supply.Side') ? 'bg-blue-900 border-l-4 border-blue-500 text-white' : 'text-slate-200 hover:bg-blue-800' }}">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 110-8 4 4 0 010 8zm6 2a3 3 0 100-6" />
                </svg>
                <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Labor Supply Data</span>
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
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m8-6H4m16-6H4" />
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
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
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
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
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
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
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
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
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
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
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
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
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
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
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
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                    </svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium">Logout</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="fixed left-22 bg-red-900 text-white text-xs py-2 px-3 rounded shadow-lg z-50 whitespace-nowrap border border-red-700 pointer-events-none">
                    Logout
>>>>>>> mod4final
                </div>
            </div>
        </div>
    </nav>
<<<<<<< HEAD

    <!-- Account Section (pinned to bottom) -->
    <div class="px-2 pb-2 border-t border-slate-100">
        <p x-show="sidebarExpanded"
            class="text-[9px] uppercase tracking-[0.14em] text-slate-400 font-semibold font-mono mb-1 px-2 whitespace-nowrap pt-3">
            Account
        </p>

        <!-- Logout -->
        <div class="relative" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 px-2.5 py-2.5 w-full text-left text-slate-700 hover:text-red-500 hover:bg-red-50 border-l-[3px] border-transparent hover:border-red-400 rounded-r-lg transition-all group">
                    <svg class="w-[18px] h-[18px] shrink-0 opacity-50 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                    </svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-[13.5px]">Logout</span>
                </button>
            </form>
            <div x-show="!sidebarExpanded && hovered"
                class="absolute left-full top-1/2 -translate-y-1/2 ml-4 bg-slate-800 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg z-50 whitespace-nowrap pointer-events-none">
                Logout
                <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-800"></div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="px-4 py-3 border-t border-slate-100 text-[9.5px] text-center text-slate-700 font-mono tracking-wide">
        <span x-show="sidebarExpanded">© 2026 DOLE Region XI</span>
        <span x-show="!sidebarExpanded">© 26</span>
=======
    <!-- Footer -->
    <div class="p-4 bg-blue-950 text-[10px] text-center opacity-50">
        <span x-show="sidebarExpanded">© 2026 DOLE Region XI</span>
        <span x-show="!sidebarExpanded">© 2026</span>
>>>>>>> mod4final
    </div>
</aside>
