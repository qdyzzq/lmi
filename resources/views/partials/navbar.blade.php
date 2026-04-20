<nav x-data="{
    scrollingDown: false,
    lastScrollTop: 0,
    isAtTop: true,
    isScrolled: false,
    mobileMenuOpen: false,
    init() {
        window.addEventListener('scroll', () => {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            this.isAtTop = scrollTop < 50;
            this.isScrolled = scrollTop >= 50;

            if (scrollTop > this.lastScrollTop && scrollTop > 100) {
                {{-- Never hide the navbar while the mobile menu is open --}}
                if (!this.mobileMenuOpen) {
                    this.scrollingDown = true;
                }
            } else if (scrollTop < this.lastScrollTop) {
                this.scrollingDown = false;
            }

            this.lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        });
    }
}"
    :class="{
        '-translate-y-full': scrollingDown && !mobileMenuOpen,
        'translate-y-0': !scrollingDown || mobileMenuOpen,
        'bg-gradient-to-b from-slate-900/95 via-slate-900/90 to-transparent backdrop-blur-md': isAtTop && !mobileMenuOpen,
        'bg-slate-900/98 backdrop-blur-xl shadow-lg': !isAtTop || mobileMenuOpen
    }"
    class="fixed top-0 left-0 right-0 z-50 border-b border-white/10 transition-all duration-300 ease-in-out rounded-b-xl"
>

    <div class="w-full px-4 transition-all duration-300" :class="isScrolled ? 'py-2' : 'py-3'">
        <div class="flex items-center justify-between gap-4">

            <!-- Logo & Brand -->
            {{--
                Mobile: logo is always w-9 h-9 (small) so burger icon always has room.
                Desktop (xl+): logo animates between w-14 h-14 and w-9 h-9 based on scroll.
                Subtitle: visible when at top on both mobile & desktop, hidden when scrolled.
            --}}
            <div class="flex items-center shrink-0 gap-2 xl:gap-4 transition-all duration-300"
                :class="{ 'xl:gap-2': isScrolled, 'xl:gap-4': !isScrolled }">

                <div class="w-9 h-9 xl:w-14 xl:h-14 flex items-center justify-center shrink-0 overflow-hidden bg-white rounded-full shadow-lg ring-2 ring-white/20 transition-all duration-300"
                    :class="{ 'xl:!w-9 xl:!h-9': isScrolled, 'xl:!w-14 xl:!h-14': !isScrolled }">
                    <img src="{{ asset('images/dole_logo.png') }}"
                         alt="LMI Logo"
                         width="56" height="56"
                         class="w-full h-full object-contain p-1">
                </div>

                <div class="leading-tight overflow-hidden min-w-0">
                    <p class="font-bold text-white tracking-tight transition-all duration-300 text-sm xl:text-lg truncate"
                        :class="{ 'xl:!text-sm': isScrolled, 'xl:!text-lg': !isScrolled }">Davao Region Labor Market Information</p>
                    {{-- Show subtitle when at top on BOTH mobile and desktop; hide when scrolled --}}
                    <p class="text-slate-300 italic font-light text-xs whitespace-nowrap transition-all duration-300 overflow-hidden"
                        :class="isScrolled ? 'max-h-0 opacity-0' : 'max-h-6 opacity-100'">Bridging Education & Industry
                    </p>
                </div>
            </div>

            <!-- Navigation Links (desktop only) -->
            <div class="hidden xl:flex items-center gap-1 min-w-0 flex-1 justify-end transition-all duration-300"
                :class="isScrolled ? 'gap-0.5' : 'gap-1'">

                <a href="{{ route('Public.Module1.home') }}"
                    class="flex items-center gap-1.5 px-3 py-2 text-xs whitespace-nowrap rounded-lg font-medium transition-all duration-300
                          {{ request()->routeIs('Public.Module1.home')
                              ? 'bg-white/30 text-white shadow-lg backdrop-blur-sm'
                              : 'text-white hover:bg-white/20' }}"
                    :class="isScrolled ? 'gap-1 px-2 py-1.5 text-xs' : 'gap-1.5 px-3 py-2 text-xs'">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"
                        class="w-5 h-5 shrink-0 transition-all duration-300"
                        :class="isScrolled ? 'w-3.5 h-3.5' : 'w-5 h-5'">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 3v18m4-14v14m4-10v10M3 13v8" />
                    </svg>
                    <span>Regional Statistics</span>
                </a>

                <a href="{{ route('Public.Module2.Job.Market.Demands') }}"
                    class="flex items-center gap-1.5 px-3 py-2 text-xs whitespace-nowrap rounded-lg font-medium transition-all duration-300
                          {{ request()->routeIs('Public.Module2.Job.Market.Demands')
                              ? 'bg-white/30 text-white shadow-lg backdrop-blur-sm'
                              : 'text-white hover:bg-white/20' }}"
                    :class="isScrolled ? 'gap-1 px-2 py-1.5 text-xs' : 'gap-1.5 px-3 py-2 text-xs'">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"
                        class="w-5 h-5 shrink-0 transition-all duration-300"
                        :class="isScrolled ? 'w-3.5 h-3.5' : 'w-5 h-5'">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 16l4 4m-2-9a6 6 0 11-12 0 6 6 0 0112 0z" />
                    </svg>
                    <span>Labor Demand Data</span>
                </a>

                <a href="{{ route('Public.Module3.supply.side') }}"
                    class="flex items-center gap-1.5 px-3 py-2 text-xs whitespace-nowrap rounded-lg font-medium transition-all duration-300
                          {{ request()->routeIs('Public.Module3.supply.side')
                              ? 'bg-white/30 text-white shadow-lg backdrop-blur-sm'
                              : 'text-white hover:bg-white/20' }}"
                    :class="isScrolled ? 'gap-1 px-2 py-1.5 text-xs' : 'gap-1.5 px-3 py-2 text-xs'">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"
                        class="w-5 h-5 shrink-0 transition-all duration-300"
                        :class="isScrolled ? 'w-3.5 h-3.5' : 'w-5 h-5'">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 110-8 4 4 0 010 8zm6 2a3 3 0 100-6" />
                    </svg>
                    <span>Labor Supply Data</span>
                </a>

                <a href="{{ route('Public.Module4.programStories') }}"
                    class="flex items-center gap-1.5 px-3 py-2 text-xs whitespace-nowrap rounded-lg font-medium transition-all duration-300
                          {{ request()->routeIs('Public.Module4.programStories')
                              ? 'bg-white/30 text-white shadow-lg backdrop-blur-sm'
                              : 'text-white hover:bg-white/20' }}"
                    :class="isScrolled ? 'gap-1 px-2 py-1.5 text-xs' : 'gap-1.5 px-3 py-2 text-xs'">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"
                        class="w-5 h-5 shrink-0 transition-all duration-300"
                        :class="isScrolled ? 'w-3.5 h-3.5' : 'w-5 h-5'">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Employment Programs</span>
                </a>

                <a href="{{ route('Public.Module5.peso.directory') }}"
                    class="flex items-center gap-1.5 px-3 py-2 text-xs whitespace-nowrap rounded-lg font-medium transition-all duration-300
                          {{ request()->routeIs('Public.Module5.peso.directory')
                              ? 'bg-white/30 text-white shadow-lg backdrop-blur-sm'
                              : 'text-white hover:bg-white/20' }}"
                    :class="isScrolled ? 'gap-1 px-2 py-1.5 text-xs' : 'gap-1.5 px-3 py-2 text-xs'">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"
                        class="w-5 h-5 shrink-0 transition-all duration-300"
                        :class="isScrolled ? 'w-3.5 h-3.5' : 'w-5 h-5'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>PESO/JPO Directory</span>
                </a>

            </div>

            <!-- Mobile Menu Button -->
            <button
                class="xl:hidden p-2.5 rounded-lg text-slate-200 hover:bg-white/10 hover:text-white transition-all duration-200 shrink-0"
                @click.stop="mobileMenuOpen = !mobileMenuOpen"
                :aria-expanded="mobileMenuOpen"
                aria-label="Toggle menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16"
                        :class="mobileMenuOpen ? 'origin-center rotate-45 translate-y-[6px]' : ''"
                        class="transition-all duration-300 origin-center" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16"
                        :class="mobileMenuOpen ? 'opacity-0 scale-x-0' : ''"
                        class="transition-all duration-300 origin-center" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 18h16"
                        :class="mobileMenuOpen ? 'origin-center -rotate-45 -translate-y-[6px]' : ''"
                        class="transition-all duration-300 origin-center" />
                </svg>
            </button>
        </div>
    </div>

    {{--
        Mobile dropdown: fixed position, fully opaque bg-slate-900 (no gradient bleed).
        :style dynamically reads the nav's actual offsetHeight so there is NEVER a gap
        or overlap regardless of whether the subtitle is visible or the nav has shrunk.
    --}}
    <div
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="xl:hidden  left-0 right-0 z-40 bg-slate-900 border-t border-white/10 shadow-2xl"
        :style="'top: ' + $el.closest('nav').offsetHeight + 'px'"
        style="display: none;">
        <div class="px-6 py-4 space-y-2">
            <a href="{{ route('Public.Module1.home') }}"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('Public.Module1.home') ? 'bg-white/20 text-white' : 'text-slate-200 hover:bg-white/10' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" class="w-5 h-5 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 3v18m4-14v14m4-10v10M3 13v8" />
                </svg>
                Regional Statistics
            </a>
            <a href="{{ route('Public.Module2.Job.Market.Demands') }}"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('Public.Module2.Job.Market.Demands') ? 'bg-white/20 text-white' : 'text-slate-200 hover:bg-white/10' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" class="w-5 h-5 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 16l4 4m-2-9a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
                Labor Demand Data
            </a>
            <a href="{{ route('Public.Module3.supply.side') }}"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('Public.Module3.supply.side') ? 'bg-white/20 text-white' : 'text-slate-200 hover:bg-white/10' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" class="w-5 h-5 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 110-8 4 4 0 010 8zm6 2a3 3 0 100-6" />
                </svg>
                Labor Supply Data
            </a>
            <a href="{{ route('Public.Module4.programStories') }}"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('Public.Module4.programStories') ? 'bg-white/20 text-white' : 'text-slate-200 hover:bg-white/10' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" class="w-5 h-5 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Employment Programs
            </a>
            <a href="{{ route('Public.Module5.peso.directory') }}"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('Public.Module5.peso.directory') ? 'bg-white/20 text-white' : 'text-slate-200 hover:bg-white/10' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" class="w-5 h-5 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                PESO/JPO Directory
            </a>
        </div>
    </div>
</nav>