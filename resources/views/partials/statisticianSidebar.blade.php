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
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-2 py-4 space-y-0.5 overflow-visible">
        <div class="mb-4">
            <p x-show="sidebarExpanded"
                class="text-[9px] uppercase tracking-[0.14em] text-slate-400 font-semibold font-mono mb-2 px-2 whitespace-nowrap">
                Main Menu
            </p>

            <!-- Statistician Review -->
            <div class="relative" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('statistician.review') }}"
                    class="flex items-center gap-3 px-2.5 py-2.5 rounded-r-lg transition-all group border-l-[3px]
                        {{ request()->routeIs('statistician.review')
                            ? 'text-blue-700 bg-blue-50 border-blue-500'
                            : 'text-slate-800 border-transparent hover:text-blue-700 hover:bg-blue-50 hover:border-blue-500' }}">
                    <svg class="w-[18px] h-[18px] shrink-0 transition-opacity {{ request()->routeIs('statistician.review') ? 'opacity-100' : 'opacity-60 group-hover:opacity-100' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5h4.5v7.5H3zM9.75 9h4.5v12h-4.5zM16.5 4.5H21V21h-4.5z"/>
                    </svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-[13.5px]">Statistician Review</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="absolute left-full top-1/2 -translate-y-1/2 ml-4 bg-slate-800 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg z-50 whitespace-nowrap pointer-events-none">
                    Statistician Review
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-800"></div>
                </div>
            </div>

            <!-- Job Titles Pending -->
            <div class="relative" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('statistician.job-titles.pending') }}"
                    class="flex items-center gap-3 px-2.5 py-2.5 rounded-r-lg transition-all group border-l-[3px]
                        {{ request()->routeIs('statistician.job-titles.pending')
                            ? 'text-blue-700 bg-blue-50 border-blue-500'
                            : 'text-slate-800 border-transparent hover:text-blue-700 hover:bg-blue-50 hover:border-blue-500' }}">
                    <svg class="w-[18px] h-[18px] shrink-0 transition-opacity {{ request()->routeIs('statistician.job-titles.pending') ? 'opacity-100' : 'opacity-60 group-hover:opacity-100' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-[13.5px]">Job Titles Pending</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="absolute left-full top-1/2 -translate-y-1/2 ml-4 bg-slate-800 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg z-50 whitespace-nowrap pointer-events-none">
                    Job Titles Pending
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-800"></div>
                </div>
            </div>

            <!-- Supply Side Editor -->
            <div class="relative" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('statistician.supply-side-editor') }}"
                    class="flex items-center gap-3 px-2.5 py-2.5 rounded-r-lg transition-all group border-l-[3px]
                        {{ request()->routeIs('statistician.supply-side-editor')
                            ? 'text-blue-700 bg-blue-50 border-blue-500'
                            : 'text-slate-800 border-transparent hover:text-blue-700 hover:bg-blue-50 hover:border-blue-500' }}">
                    <svg class="w-[18px] h-[18px] shrink-0 transition-opacity {{ request()->routeIs('statistician.supply-side-editor') ? 'opacity-100' : 'opacity-60 group-hover:opacity-100' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                    </svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-[13.5px]">Supply Side Editor</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="absolute left-full top-1/2 -translate-y-1/2 ml-4 bg-slate-800 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg z-50 whitespace-nowrap pointer-events-none">
                    Supply Side Editor
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-800"></div>
                </div>
            </div>

            <!-- Analysis Editor -->
            <div class="relative" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                <a href="{{ route('statistician.templates') }}"
                    class="flex items-center gap-3 px-2.5 py-2.5 rounded-r-lg transition-all group border-l-[3px]
                        {{ request()->routeIs('statistician.templates')
                            ? 'text-blue-700 bg-blue-50 border-blue-500'
                            : 'text-slate-800 border-transparent hover:text-blue-700 hover:bg-blue-50 hover:border-blue-500' }}">
                    <svg class="w-[18px] h-[18px] shrink-0 transition-opacity {{ request()->routeIs('statistician.templates') ? 'opacity-100' : 'opacity-60 group-hover:opacity-100' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                    <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-[13.5px]">Analysis Editor</span>
                </a>
                <div x-show="!sidebarExpanded && hovered"
                    class="absolute left-full top-1/2 -translate-y-1/2 ml-4 bg-slate-800 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg z-50 whitespace-nowrap pointer-events-none">
                    Analysis Editor
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-800"></div>
                </div>
            </div>
        </div>
    </nav>

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
    </div>
</aside>