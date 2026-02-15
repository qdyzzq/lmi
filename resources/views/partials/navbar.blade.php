<nav x-data="{
    scrollingDown: false,
    lastScrollTop: 0,
    isAtTop: true,
    init() {
        window.addEventListener('scroll', () => {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            this.isAtTop = scrollTop < 50;

            if (scrollTop > this.lastScrollTop && scrollTop > 100) {
                // Scrolling down
                this.scrollingDown = true;
            } else if (scrollTop < this.lastScrollTop) {
                // Scrolling up
                this.scrollingDown = false;
            }

            this.lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        });
    }
}"
    :class="{
        '-translate-y-full': scrollingDown,
        'translate-y-0': !scrollingDown,
        'bg-gradient-to-b from-slate-900/95 via-slate-900/90 to-transparent backdrop-blur-md': isAtTop,
        'bg-slate-900/98 backdrop-blur-xl shadow-lg': !isAtTop
    }"
    class="fixed top-0 left-0 right-0 z-50 border-b border-white/10 transition-all duration-300 ease-in-out rounded-b-xl">

    <div class="max-w-7xl mx-auto px-6 py-4 ">
        <div class="flex items-center justify-between">
            <!-- Logo & Brand -->
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 flex items-center justify-center shrink-0 overflow-hidden bg-white rounded-full shadow-lg ring-2 ring-white/20">
                    <img src="{{ asset('images/dole_logo.png') }}" alt="LMI Logo" class="w-full h-full object-contain p-2">
                </div>
                <div class="leading-tight">
                    <p class="font-bold text-white text-lg tracking-tight">Labor Market Intelligence</p>
                    <p class="text-xs text-slate-300 italic font-light">Bridging Education & Industry</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="hidden xl:flex items-center gap-6">
                <a href="{{ route('home') }}"
                    class="px-6 py-3.5 rounded-lg text-sm font-medium transition-all duration-200 
                          {{ request()->routeIs('home')
                              ? 'bg-white/30 text-white shadow-lg backdrop-blur-sm'
                              : 'text-white hover:bg-white/20 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 3v18m4-14v14m4-10v10M3 13v8" />
                        </svg>
                        <span>Regional Statistics</span>
                    </div>
                </a>

                <a href="{{ route('Job.Market.Demands') }}"
                    class="px-6 py-3.5 rounded-lg text-sm font-medium transition-all duration-200 
                          {{ request()->routeIs('Job.Market.Demands')
                              ? 'bg-white/30 text-white shadow-lg backdrop-blur-sm'
                              : 'text-white hover:bg-white/20 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 16l4 4m-2-9a6 6 0 11-12 0 6 6 0 0112 0z" />
                        </svg>
                        <span>Labor Demand</span>
                    </div>
                </a>

                <a href="{{ route('Supply.Side') }}"
                    class="px-6 py-3.5 rounded-lg text-sm font-medium transition-all duration-200 
                          {{ request()->routeIs('Supply.Side')
                              ? 'bg-white/30 text-white shadow-lg backdrop-blur-sm'
                              : 'text-white hover:bg-white/20 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 110-8 4 4 0 010 8zm6 2a3 3 0 100-6" />
                        </svg>
                        <span>Labor Supply Data</span>
                    </div>
                </a>


                <a href="{{ route('programs.stories') }}"
                    class="px-6 py-3.5 rounded-lg text-sm font-medium transition-all duration-200 
                          {{ request()->routeIs('programs.stories')
                              ? 'bg-white/30 text-white shadow-lg backdrop-blur-sm'
                              : 'text-white hover:bg-white/20 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>Programs & Stories</span>
                    </div>
                </a>
                <a href="#"
                    class="px-6 py-3.5 rounded-lg text-sm font-medium text-white hover:bg-white/20 hover:text-white transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m8-6H4m16-6H4" />
                        </svg>
                        <span>Employment Programs</span>
                    </div>
                </a>
            </div>


            <!-- Mobile Menu Button -->
            <button
                class="lg:hidden p-2.5 rounded-lg text-slate-200 hover:bg-white/10 hover:text-white transition-all duration-200"
                @click="$root.mobileMenuOpen = !$root.mobileMenuOpen">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="$root.mobileMenuOpen" x-transition
        class="lg:hidden border-t border-white/10 bg-slate-900/98 backdrop-blur-md" style="display: none;">
        <div class="px-6 py-4 space-y-2">
            <a href="{{ route('home') }}"
                class="block px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('home') ? 'bg-white/20 text-white' : 'text-slate-200 hover:bg-white/10' }}">
                Regional Statistics
            </a>
            <a href="{{ route('Job.Market.Demands') }}"
                class="block px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('Job.Market.Demands') ? 'bg-white/20 text-white' : 'text-slate-200 hover:bg-white/10' }}">
                Labor Demand
            </a>
            <a href="{{ route('Supply.Side') }}"
                class="block px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('Supply.Side') ? 'bg-white/20 text-white' : 'text-slate-200 hover:bg-white/10' }}">
                Labor Supply Data
            </a>
            <a href="#" class="block px-4 py-3 rounded-lg text-sm font-medium text-slate-200 hover:bg-white/10">
                Employment Programs
            </a>
            <div class="border-t border-white/10 my-2 pt-2">
                <a href="{{ route('Setting') }}"
                    class="block px-4 py-3 rounded-lg text-sm font-medium text-slate-200 hover:bg-white/10">
                    Settings
                </a>
            </div>
        </div>
    </div>
</nav>
