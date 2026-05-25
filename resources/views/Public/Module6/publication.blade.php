<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>LMI Publication</title>
</head>

<body class="bg-slate-100 min-h-screen">
    @include('partials.navbar')

    {{-- ── Hero Banner ── --}}
    <div class="relative w-full h-[500px] md:h-[700px] lg:h-[900px] overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ asset('images/carousel-peso/peso-carousel1.webp') }}" alt="RDC XI Communicator Banner"
                class="w-full h-full object-cover object-center">
            <div
                class="absolute inset-0 bg-gradient-to-b from-slate-900/80 via-slate-900/40 to-slate-100 dark:to-slate-900">
            </div>
        </div>
        <div class="relative z-10 h-full flex flex-col items-center justify-center px-4 text-center gap-1.5">
            <h2 class="text-white font-black tracking-tight leading-none drop-shadow-[0_2px_16px_rgba(0,0,0,1)]"
                style="font-size: clamp(1.4rem, 3.5vw, 2.75rem);">
                LMI Publication
            </h2>
            <p class="text-slate-200 font-medium drop-shadow-[0_1px_8px_rgba(0,0,0,1)]"
                style="font-size: clamp(0.7rem, 1.4vw, 0.95rem);">
                The Official Newsletter of the Davao Region XI
            </p>
        </div>
        {{-- Scroll down arrow --}}
        <div
            class="absolute bottom-6 sm:bottom-16 md:bottom-24 lg:bottom-32 left-1/2 transform -translate-x-1/2 z-20 animate-bounce">
            <a href="#publication-content" class="flex flex-col items-center cursor-pointer group"
                @click.prevent="document.getElementById('publication-content').scrollIntoView({ behavior: 'smooth', block: 'start' })">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white group-hover:text-blue-300 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
                <p
                    class="text-white text-xs sm:text-sm mt-1 sm:mt-2 font-medium group-hover:text-blue-300 transition-colors">
                    Scroll to explore
                </p>
            </a>
        </div>
    </div>

    {{-- ── Main Content ── --}}
    <div id="publication-content" class="max-w-9xl mx-auto px-3 sm:px-6 py-6" x-data="communicatorSection()">

        {{-- ── Lightbox Modal — teleported to <body> so nothing can overlap it ── --}}
        <template x-teleport="body">
            <div x-show="zoomImage !== null" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click.self="zoomImage = null; imgScale = 0.3; panX = 0; panY = 0;"
                @keydown.escape.window="zoomImage = null; imgScale = 0.3; panX = 0; panY = 0;"
                x-effect="zoomImage !== null ? document.body.style.overflow = 'hidden' : document.body.style.overflow = ''"
                class="fixed inset-0 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
                style="display:none; z-index:9999;">

                {{-- Close button --}}
                <button @click="zoomImage = null; imgScale = 0.3; panX = 0; panY = 0;"
                    class="absolute top-4 right-4 text-white bg-white/10 hover:bg-white/25 rounded-full w-9 h-9 flex items-center justify-center transition-colors"
                    style="z-index:10000;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Zoom controls --}}
                <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex items-center gap-2 bg-black/50 backdrop-blur-sm rounded-full px-3 py-1.5"
                    style="z-index:10000;">
                    <button @click="imgScale = Math.max(0.3, imgScale - 0.25)"
                        class="text-white w-7 h-7 flex items-center justify-center hover:text-blue-300 transition-colors text-lg font-bold">−</button>
                    <span class="text-white text-xs font-semibold w-10 text-center"
                        x-text="Math.round(imgScale * 100) + '%'"></span>
                    <button @click="imgScale = Math.min(4, imgScale + 0.25)"
                        class="text-white w-7 h-7 flex items-center justify-center hover:text-blue-300 transition-colors text-lg font-bold">+</button>
                    <span class="text-white/40 text-xs mx-1">|</span>
                    <button @click="imgScale = 0.3; panX = 0; panY = 0;"
                        class="text-white text-xs hover:text-blue-300 transition-colors font-medium">Reset</button>
                </div>

                {{-- Drag-to-pan viewport — clamped pan so image can't escape the viewport --}}
                <div x-ref="viewport" @click.stop
                    @wheel.prevent="
         imgScale = Math.min(4, Math.max(0.3, imgScale + ($event.deltaY < 0 ? 0.15 : -0.15)));
         if (imgScale <= 0.3) { panX = 0; panY = 0; }
         else {
             let vw = $refs.viewport.clientWidth, vh = $refs.viewport.clientHeight;
             let iw = $refs.img.naturalWidth * imgScale, ih = $refs.img.naturalHeight * imgScale;
             let maxX = Math.max(0, (iw - vw) / 2), maxY = Math.max(0, (ih - vh) / 2);
             panX = Math.min(maxX, Math.max(-maxX, panX));
             panY = Math.min(maxY, Math.max(-maxY, panY));
         }
     "
                    @mousedown.prevent="isDragging = true; dragStartX = $event.clientX - panX; dragStartY = $event.clientY - panY;"
                    @mousemove.prevent="
         if (isDragging) {
             let vw = $refs.viewport.clientWidth, vh = $refs.viewport.clientHeight;
             let iw = $refs.img.naturalWidth * imgScale, ih = $refs.img.naturalHeight * imgScale;
             let maxX = Math.max(0, (iw - vw) / 2), maxY = Math.max(0, (ih - vh) / 2);
             panX = Math.min(maxX, Math.max(-maxX, $event.clientX - dragStartX));
             panY = Math.min(maxY, Math.max(-maxY, $event.clientY - dragStartY));
         }
     "
                    @mouseup="isDragging = false" @mouseleave="isDragging = false"
                    @touchstart.prevent="
         if ($event.touches.length === 1) { isDragging = true; dragStartX = $event.touches[0].clientX - panX; dragStartY = $event.touches[0].clientY - panY; }
     "
                    @touchmove.prevent="
         if (isDragging && $event.touches.length === 1) {
             let vw = $refs.viewport.clientWidth, vh = $refs.viewport.clientHeight;
             let iw = $refs.img.naturalWidth * imgScale, ih = $refs.img.naturalHeight * imgScale;
             let maxX = Math.max(0, (iw - vw) / 2), maxY = Math.max(0, (ih - vh) / 2);
             panX = Math.min(maxX, Math.max(-maxX, $event.touches[0].clientX - dragStartX));
             panY = Math.min(maxY, Math.max(-maxY, $event.touches[0].clientY - dragStartY));
         }
     "
                    @touchend="isDragging = false" :style="isDragging ? 'cursor:grabbing' : 'cursor:grab'"
                    style="width:90vw; height:85vh; overflow:hidden; display:flex; align-items:center; justify-content:center; position:relative;">
                    <img x-ref="img" :src="zoomImage"
                        :style="`position:absolute; top:50%; left:50%; transform: translate(calc(-50% + ${panX}px), calc(-50% + ${panY}px)) scale(${imgScale}); transform-origin: center; transition: ${isDragging ? 'none' : 'transform 0.2s ease'};`"
                        class="rounded-xl shadow-2xl select-none"
                        style="width:auto; height:auto; max-width:none; max-height:none; pointer-events:none;"
                        draggable="false">
                </div>
            </div>
        </template>

        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">

            {{-- Header --}}
            <div
                class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-5 sm:px-6 py-4 flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center flex-shrink-0 ring-1 ring-white/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6m-4 4h2" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-bold text-[0.95rem] leading-snug">LMI Publications — Davao Region XI</h3>
                    <p class="text-slate-400 text-xs mt-0.5 leading-snug">Browse annual and weekly releases</p>
                </div>
            </div>



            {{-- ── Publication Groups ── --}}
            <div class="px-5 sm:px-6 pb-6 pt-4 flex flex-col gap-4">

                {{-- ══════════════════════════════════════════════════════════════ --}}
                {{-- ── ANNUAL PANEL TABS — cards are tabs, panel appears below    ── --}}
                {{-- ══════════════════════════════════════════════════════════════ --}}
                <div x-data="{ activeTab: null }" class="flex flex-col gap-0">

                    {{-- ── Tab Cards Row ── --}}
                    {{-- Cards are fixed-height wrappers; overflow-hidden is only on the inner image area, NOT the card itself so the chevron bar below never clips --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-10 items-start">

                        {{-- ── TAB 1: Jobs & Labor Market Forecast ── --}}
                        <div class="rounded-xl shadow-md cursor-pointer group select-none transition-all duration-200"
                            :class="activeTab === 'jlmf' ? 'ring-2 ring-blue-500 shadow-xl' :
                                'ring-1 ring-white/10 hover:shadow-xl hover:ring-blue-300/40'"
                            @click="activeTab = activeTab === 'jlmf' ? null : 'jlmf'; if(activeTab === 'jlmf' && groupYears['jlmf'] === undefined) { const y = getYearsForGroup('jlmf'); if(y.length) setGroupYear('jlmf', y[0]); } if(activeTab) { $nextTick(() => document.getElementById('annual-panel').scrollIntoView({ behavior: 'smooth', block: 'nearest' })) }">
                            {{-- Inner image+text area — overflow-hidden only here --}}
                            <div class="relative flex overflow-hidden rounded-t-xl bg-slate-800"
                                style="height: 250px;">
                                <div class="w-1/3 flex-shrink-0 relative overflow-hidden">
                                    <template x-if="getGroupBannerUrl('jlmf', 'Annual')">
                                        <div class="relative w-full h-full">
                                            <img :src="getGroupBannerUrl('jlmf', 'Annual')"
                                                class="absolute inset-0 w-full h-full object-cover scale-110 blur-[1px] opacity-80">
                                            <img :src="getGroupBannerUrl('jlmf', 'Annual')"
                                                alt="Jobs and Labor Market Forecast"
                                                class="absolute inset-0 w-full h-full object-contain p-2">
                                        </div>
                                    </template>
                                </div>
                                <div class="flex-1 flex flex-col justify-center px-5 py-4 transition-colors duration-200"
                                    :class="activeTab === 'jlmf' ? 'bg-blue-900' : 'bg-blue-900 group-hover:bg-blue-800'">
                                    <!-- TOP divider -->
                                    <div class="flex items-center gap-3 w-full max-w-m">
                                        <span class="text-white/40 text-xs">✦</span>
                                        <div class="flex-1 border-t border-white/25"></div>
                                        <span class="text-white/40 text-xs">✦</span>
                                    </div>
                                    <h2
                                        class="text-white text-[20px] font-bold text-center tracking-widest leading-snug drop-shadow mb-2">
                                        JOBS & LABOR MARKET FORECAST</h2>
                                    <h3
                                        class="text-slate-100 text-center text-[0.65rem] tracking-widest font-semibold">
                                        INDUSTRY GROWTH & ACTION AGENDA</h3>
                                    <p
                                        class="pt-5 text-slate-100 text-center text-[0.55rem] tracking-widest line-clamp-3">
                                        "Information on Key Growth Sectors, Emerging Industries, In Demand Occupations,
                                        and action agendas for industry gaps."</p>
                                    <!-- Bottom divider -->
                                    <div class="flex items-center gap-3 w-full max-w-m">
                                        <span class="text-white/40 text-xs">✦</span>
                                        <div class="flex-1 border-t border-white/25"></div>
                                        <span class="text-white/40 text-xs">✦</span>
                                    </div>
                                </div>
                            </div>
                            {{-- Chevron footer — always visible, rotates when active --}}
                            <div class="flex items-center justify-center gap-1.5 py-2 rounded-b-xl transition-colors duration-200"
                                :class="activeTab === 'jlmf' ? 'bg-blue-950' : 'bg-blue-950 group-hover:bg-blue-900'">
                                <span
                                    class="text-[0.6rem] font-bold tracking-widest uppercase transition-colors duration-200"
                                    :class="activeTab === 'jlmf' ? 'text-white' :
                                        'text-slate-400 group-hover:text-slate-200'"
                                    x-text="activeTab === 'jlmf' ? 'Close' : 'CLICK TO EXPLORE'"></span>
                                <svg class="w-3.5 h-3.5 transition-all duration-300"
                                    :class="activeTab === 'jlmf' ? 'rotate-180 text-white' :
                                        'text-slate-400 group-hover:text-slate-200'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        {{-- ── TAB 2: Jobs and Labor Market Profile ── --}}
                        <div class="rounded-xl shadow-md cursor-pointer group select-none transition-all duration-200"
                            :class="activeTab === 'lmp' ? 'ring-2 ring-blue-400 shadow-xl' :
                                'ring-1 ring-white/10 hover:shadow-xl hover:ring-blue-300/40'"
                            @click="activeTab = activeTab === 'lmp' ? null : 'lmp'; if(activeTab === 'lmp' && groupYears['lmp'] === undefined) { const y = getYearsForGroup('lmp'); if(y.length) setGroupYear('lmp', y[0]); } if(activeTab) { $nextTick(() => document.getElementById('annual-panel').scrollIntoView({ behavior: 'smooth', block: 'nearest' })) }">
                            <div class="relative flex overflow-hidden rounded-t-xl bg-slate-800"
                                style="height: 250px;">
                                <div class="w-1/3 flex-shrink-0 relative overflow-hidden">
                                    <template x-if="getGroupBannerUrl('lmp', 'Annual')">
                                        <div class="relative w-full h-full">
                                            <img :src="getGroupBannerUrl('lmp', 'Annual')"
                                                class="absolute inset-0 w-full h-full object-cover scale-110 blur-[1px] opacity-80">
                                            <img :src="getGroupBannerUrl('lmp', 'Annual')"
                                                alt="Jobs and Labor Market Profile"
                                                class="absolute inset-0 w-full h-full object-contain p-2">
                                        </div>
                                    </template>
                                </div>
                                <div class="flex-1 flex flex-col justify-center px-5 py-4 transition-colors duration-200"
                                    :class="activeTab === 'lmp' ? 'bg-red-900' : 'bg-red-900 group-hover:bg-red-800'">
                                    <div class="flex items-center gap-3 w-full max-w-m">
                                        <!-- Top divider -->
                                        <span class="text-white/40 text-xs">✦</span>
                                        <div class="flex-1 border-t border-white/25"></div>
                                        <span class="text-white/40 text-xs">✦</span>
                                    </div>
                                    <h2
                                        class="text-white text-[20px] font-bold text-center tracking-widest leading-snug drop-shadow mb-2">
                                        LABOR MARKET PROFILE</h2>
                                    <h3
                                        class="text-slate-100 text-center text-[0.65rem] tracking-widest font-semibold">
                                        DEMOGRAPHIC & ECONOMIC ANALYSIS</h3>
                                    <p
                                        class="pt-5 text-slate-100 text-center text-[0.55rem] tracking-widest line-clamp-3">
                                        "Comprehensive demographic and economic landscape analysis. Ideal for policy
                                        makers and investors seeking regional depth."</p>
                                    <div class="flex items-center gap-3 w-full max-w-m">
                                        <!-- Bottom divider -->
                                        <span class="text-white/40 text-xs">✦</span>
                                        <div class="flex-1 border-t border-white/25"></div>
                                        <span class="text-white/40 text-xs">✦</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center gap-1.5 py-2 rounded-b-xl transition-colors duration-200"
                                :class="activeTab === 'lmp' ? 'bg-red-950' : 'bg-red-950 group-hover:bg-red-900'">
                                <span
                                    class="text-[0.6rem] font-bold tracking-widest uppercase transition-colors duration-200"
                                    :class="activeTab === 'lmp' ? 'text-white' :
                                        'text-slate-400 group-hover:text-slate-200'"
                                    x-text="activeTab === 'lmp' ? 'Close' : 'CLICK TO EXPLORE'"></span>
                                <svg class="w-3.5 h-3.5 transition-all duration-300"
                                    :class="activeTab === 'lmp' ? 'rotate-180 text-white' :
                                        'text-slate-400 group-hover:text-slate-200'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        {{-- ── TAB 3: Labor Market Updates ── --}}
                        <div class="rounded-xl shadow-md cursor-pointer group select-none transition-all duration-200"
                            :class="activeTab === 'lmu' ? 'ring-2 ring-blue-400 shadow-xl' :
                                'ring-1 ring-white/10 hover:shadow-xl hover:ring-blue-300/40'"
                            @click="activeTab = activeTab === 'lmu' ? null : 'lmu'; if(activeTab === 'lmu' && groupYears['lmu'] === undefined) { const y = getYearsForGroup('lmu'); if(y.length) setGroupYear('lmu', y[0]); } if(activeTab) { $nextTick(() => document.getElementById('annual-panel').scrollIntoView({ behavior: 'smooth', block: 'nearest' })) }">
                            <div class="relative flex overflow-hidden rounded-t-xl bg-slate-800"
                                style="height: 250px;">
                                <div class="w-1/3 flex-shrink-0 relative overflow-hidden">
                                    <template x-if="getGroupBannerUrl('lmu', 'Annual')">
                                        <div class="relative w-full h-full">
                                            <img :src="getGroupBannerUrl('lmu', 'Annual')"
                                                class="absolute inset-0 w-full h-full object-cover scale-110 blur-[1px] opacity-80">
                                            <img :src="getGroupBannerUrl('lmu', 'Annual')" alt="Labor Market Updates"
                                                class="absolute inset-0 w-full h-full object-contain p-2">
                                        </div>
                                    </template>
                                </div>
                                <div class="flex-1 flex flex-col justify-center px-5 py-4 transition-colors duration-200"
                                    :class="activeTab === 'lmu' ? 'bg-[#8B6B5A]' : 'bg-[#8B6B5A] group-hover:bg-[#A67C6A]'">
                                    <div class="flex items-center gap-3 w-full max-w-m">
                                        <!-- Top divider -->
                                        <span class="text-white/40 text-xs">✦</span>
                                        <div class="flex-1 border-t border-white/25"></div>
                                        <span class="text-white/40 text-xs">✦</span>
                                    </div>
                                    <h2
                                        class="text-white text-[20px] font-bold text-center tracking-widest leading-snug drop-shadow mb-2">
                                        LABOR MARKET UPDATE</h2>
                                    <h3
                                        class="text-slate-100 text-center text-[0.65rem] tracking-widest font-semibold">
                                        REGIONAL SKILLS PROFILE</h3>
                                    <p
                                        class="pt-5 text-slate-100 text-center text-[0.55rem] tracking-widest line-clamp-3">
                                        "Annual publication providing labor market information based on data from the
                                        PESO Employment Information System (PEIS)"</p>
                                    <div class="flex items-center gap-3 w-full max-w-m">
                                        <!-- Bottom divider -->
                                        <span class="text-white/40 text-xs">✦</span>
                                        <div class="flex-1 border-t border-white/25"></div>
                                        <span class="text-white/40 text-xs">✦</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center gap-1.5 py-2 rounded-b-xl transition-colors duration-200"
                                :class="activeTab === 'lmu' ? 'bg-[#6F4E37]' : 'bg-[#6F4E37] group-hover:bg-[#8B6B5A]'">
                                <span
                                    class="text-[0.6rem] font-bold tracking-widest uppercase transition-colors duration-200"
                                    :class="activeTab === 'lmu' ? 'text-white' :
                                        'text-slate-400 group-hover:text-slate-200'"
                                    x-text="activeTab === 'lmu' ? 'Close' : 'CLICK TO EXPLORE'"></span>
                                <svg class="w-3.5 h-3.5 transition-all duration-300"
                                    :class="activeTab === 'lmu' ? 'rotate-180 text-white' :
                                        'text-slate-400 group-hover:text-slate-200'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                    </div>{{-- /tab cards row --}}

                    {{-- ── Shared Panel — shows content for whichever tab is active ── --}}
                    <div id="annual-panel" x-show="activeTab !== null"
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="w-full mt-3 rounded-xl overflow-hidden shadow-md border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50"
                        style="display:none;">

                        {{-- Panel header — title + year switcher --}}
                        <div class="flex items-center justify-between px-5 pt-4 pb-3 gap-4">
                            <div class="flex flex-col gap-1 min-w-0">
                                <span
                                    class="inline-flex items-center gap-1.5 text-[0.62rem] font-bold tracking-widest uppercase text-rose-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 flex-shrink-0"></span>
                                    Regional Archives
                                </span>
                                <h2 class="text-slate-800 dark:text-slate-100 font-black tracking-wide leading-tight"
                                    style="font-size: clamp(1.1rem, 2.2vw, 1.6rem);">
                                    <span x-show="activeTab === 'jlmf'">JOBS AND LABOR MARKET FORECAST</span>
                                    <span x-show="activeTab === 'lmp'">LABOR MARKET PROFILE</span>
                                    <span x-show="activeTab === 'lmu'">LABOR MARKET UPDATE</span>
                                </h2>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span
                                    class="text-[0.72rem] font-semibold text-slate-600 dark:text-slate-600  tracking-wider hidden sm:block">ARCHIVE
                                    YEAR</span>
                                {{-- Year selects per tab — only the active one is shown --}}
                                <template x-if="activeTab === 'jlmf'">
                                    <select @change="setGroupYear('jlmf', String($event.target.value))"
                                        :value="getGroupYear('jlmf')" @click.stop
                                        class="text-[0.75rem] font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg px-4.5 py-3 shadow-sm cursor-pointer hover:border-[#023E8A] focus:outline-none focus:ring-2 focus:ring-[#023E8A]/30 transition-colors">
                                        <template x-for="year in getYearsForGroup('jlmf')" :key="year">
                                            <option :value="year" :selected="getGroupYear('jlmf') === year"
                                                x-text="year + ' Series'"></option>
                                        </template>
                                    </select>
                                </template>
                                <template x-if="activeTab === 'lmp'">
                                    <select @change="setGroupYear('lmp', String($event.target.value))"
                                        :value="getGroupYear('lmp')" @click.stop
                                        class="text-[0.75rem] font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg px-4.5 py-3 shadow-sm cursor-pointer hover:border-[#023E8A] focus:outline-none focus:ring-2 focus:ring-[#023E8A]/30 transition-colors">
                                        <template x-for="year in getYearsForGroup('lmp')" :key="year">
                                            <option :value="year" :selected="getGroupYear('lmp') === year"
                                                x-text="year + ' Series'"></option>
                                        </template>
                                    </select>
                                </template>
                                <template x-if="activeTab === 'lmu'">
                                    <select @change="setGroupYear('lmu', String($event.target.value))"
                                        :value="getGroupYear('lmu')" @click.stop
                                        class="text-[0.75rem] font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg px-4.5 py-3 shadow-sm cursor-pointer hover:border-[#023E8A] focus:outline-none focus:ring-2 focus:ring-[#023E8A]/30 transition-colors">
                                        <template x-for="year in getYearsForGroup('lmu')" :key="year">
                                            <option :value="year" :selected="getGroupYear('lmu') === year"
                                                x-text="year + ' Series'"></option>
                                        </template>
                                    </select>
                                </template>
                            </div>
                        </div>
                        <div class="border-b border-slate-200 dark:border-slate-700 mx-5"></div>

                        {{-- Panel body — JLMF content --}}
                        <template x-if="activeTab === 'jlmf'">
                            <div class="px-5 py-4">
                                <template x-if="getIssues(getGroupYear('jlmf'), 'jlmf').length > 0">
                                    <div class="flex flex-col gap-4">
                                        <template x-for="issue in getIssues(getGroupYear('jlmf'), 'jlmf')"
                                            :key="issue.id">
                                            <div class="w-full rounded-2xl overflow-hidden shadow-xl flex flex-row"
                                                style="height: 480px; background: linear-gradient(120deg, #1a3a7a 0%, #1d4fa3 40%, #1a3a80 100%);">
                                                <div class="relative flex-shrink-0 overflow-hidden"
                                                    style="width: 28%;">
                                                    <img :src="driveThumbnailUrl(issue.driveFileId)"
                                                        :alt="issue.label"
                                                        class="absolute inset-0 w-full h-full object-cover scale-110 blur-[2px] opacity-80"
                                                        loading="lazy" onerror="this.style.display='none'">
                                                    <img :src="driveThumbnailUrl(issue.driveFileId)"
                                                        :alt="issue.label"
                                                        class="absolute inset-0 w-full h-full object-contain p-8"
                                                        loading="lazy" onerror="this.style.display='none'">
                                                    <div class="absolute inset-0"
                                                        style="background: linear-gradient(to right, transparent 55%, #1a2035 100%);">
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex flex-col justify-center items-center flex-1 px-10 py-10 gap-8 min-w-0 relative text-center">
                                                    <div class="flex items-center gap-2 w-full max-w-m mb-1">
                                                        <span class="text-white/50 text-xs">✦</span>
                                                        <div class="flex-1 border-t border-white/30"></div>
                                                        <span class="text-white/50 text-xs">✦</span>
                                                    </div>
                                                    <h2 class="text-white font-black leading-tight"
                                                        style="font-size: clamp(1.6rem, 3.2vw, 2.6rem); letter-spacing: 0.15em; text-shadow: 0 2px 16px rgba(0,0,0,0.5);">
                                                        LABOR MARKET<br> PROFILE</br></h2>
                                                    <p class="text-white text-[1rem] font-bold tracking-[0.3em] ">
                                                        INDUSTRY GROWTH & ACTION AGENDA</p>
                                                    <p
                                                        class="text-slate-100 text-[0.95rem] font-semibold leading-relaxed italic max-w-2xl">
                                                        "Information on Key Growth Sectors, Emerging Industries, In
                                                        Demand Occupations, and action agendas for industry gaps."</p>
                                                    <div class="flex items-center justify-center gap-6 flex-wrap pt-2">
                                                        <template x-if="issue.driveFileId">
                                                            <a :href="driveViewUrl(issue.driveFileId)" target="_blank"
                                                                rel="noopener noreferrer" @click.stop
                                                                class="inline-flex items-center gap-2 text-[0.7rem] font-bold tracking-widest  text-slate-900 bg-white hover:bg-slate-100 px-8 py-4 rounded-lg transition-colors shadow-md">
                                                                <svg class="w-3.5 h-3.5" fill="none"
                                                                    stroke="currentColor" stroke-width="2.5"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                                </svg>
                                                                DOWNLOAD
                                                            </a>
                                                        </template>
                                                        <template x-if="issue.driveFileId">
                                                            <a :href="driveViewUrl(issue.driveFileId)" target="_blank"
                                                                rel="noopener noreferrer" @click.stop
                                                                class="inline-flex items-center gap-2 text-[0.7rem] font-bold tracking-widest  text-white/80 hover:text-white border border-white/30 hover:border-white/60 px-8 py-4 rounded-lg transition-colors">
                                                                <svg class="w-3.5 h-3.5" fill="none"
                                                                    stroke="currentColor" stroke-width="2.5"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                                </svg>
                                                                READ ONLINE
                                                            </a>
                                                        </template>
                                                    </div>
                                                    <div class="flex items-center gap-2 w-full max-w-m mt-1">
                                                        <span class="text-white/50 text-xs">✦</span>
                                                        <div class="flex-1 border-t border-white/30"></div>
                                                        <span class="text-white/50 text-xs">✦</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="getIssues(getGroupYear('jlmf'), 'jlmf').length === 0">
                                    <div class="py-10 flex flex-col items-center justify-center gap-2 text-slate-400">
                                        <svg class="w-7 h-7 opacity-40" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        <p class="text-sm">No issues available yet.</p>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Panel body — LMP content --}}
                        <template x-if="activeTab === 'lmp'">
                            <div class="px-5 py-4">
                                <template x-if="getIssues(getGroupYear('lmp'), 'lmp').length > 0">
                                    <div class="flex flex-col gap-4">
                                        <template x-for="issue in getIssues(getGroupYear('lmp'), 'lmp')"
                                            :key="issue.id">
                                            <div class="w-full rounded-2xl overflow-hidden shadow-xl flex flex-row" +
                                                style="height: 480px; background: linear-gradient(120deg, #6b1528 0%, #8b1e35 40%, #6b1528 100%);">
                                                <div class="relative flex-shrink-0 overflow-hidden"
                                                    style="width: 28%;">
                                                    <img :src="driveThumbnailUrl(issue.driveFileId)"
                                                        :alt="issue.label"
                                                        class="absolute inset-0 w-full h-full object-cover scale-110 blur-[2px] opacity-80"
                                                        loading="lazy" onerror="this.style.display='none'">
                                                    <img :src="driveThumbnailUrl(issue.driveFileId)"
                                                        :alt="issue.label"
                                                        class="absolute inset-0 w-full h-full object-contain p-8"
                                                        loading="lazy" onerror="this.style.display='none'">
                                                    <div class="absolute inset-0" +
                                                        style="background: linear-gradient(to right, transparent 55%, #6b1528 100%);">
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex flex-col justify-center items-center flex-1 px-10 py-10 gap-8 min-w-0 relative text-center">
                                                    <div class="flex items-center gap-2 w-full max-w-m mb-1">
                                                        <span class="text-white/50 text-xs">✦</span>
                                                        <div class="flex-1 border-t border-white/30"></div>
                                                        <span class="text-white/50 text-xs">✦</span>
                                                    </div>
                                                    <h2 class="text-white font-black leading-tight"
                                                        style="font-size: clamp(1.6rem, 3.2vw, 2.6rem); letter-spacing: 0.15em; text-shadow: 0 2px 16px rgba(0,0,0,0.5);">
                                                        JOBS AND LABOR MARKET PROFILE</h2>
                                                    <p class="text-white text-[1rem] font-bold tracking-[0.3em] ">
                                                        DEMOGRAPHIC & ECONOMIC ANALYSIS</p>
                                                    <p
                                                        class="text-slate-100 text-[0.95rem] font-semibold leading-relaxed italic max-w-2xl">
                                                        "Comprehensive demographic and economic landscape analysis.
                                                        Ideal for policy makers and investors seeking regional depth."
                                                    </p>
                                                    <div class="flex items-center justify-center gap-6 flex-wrap pt-2">
                                                        <template x-if="issue.driveFileId">
                                                            <a :href="driveViewUrl(issue.driveFileId)" target="_blank"
                                                                rel="noopener noreferrer" @click.stop
                                                                class="inline-flex items-center gap-2 text-[0.7rem] font-bold tracking-widest  text-slate-900 bg-white hover:bg-slate-100 px-8 py-4 rounded-lg transition-colors shadow-md">
                                                                <svg class="w-3.5 h-3.5" fill="none"
                                                                    stroke="currentColor" stroke-width="2.5"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                                </svg>
                                                                DOWNLOAD
                                                            </a>
                                                        </template>
                                                        <template x-if="issue.driveFileId">
                                                            <a :href="driveViewUrl(issue.driveFileId)" target="_blank"
                                                                rel="noopener noreferrer" @click.stop
                                                                class="inline-flex items-center gap-2 text-[0.7rem] font-bold tracking-widest  text-white/80 hover:text-white border border-white/30 hover:border-white/60 px-8 py-4 rounded-lg transition-colors">
                                                                <svg class="w-3.5 h-3.5" fill="none"
                                                                    stroke="currentColor" stroke-width="2.5"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                                </svg>
                                                                READ ONLINE
                                                            </a>
                                                        </template>
                                                    </div>
                                                    <div class="flex items-center gap-2 w-full max-w-m mt-1">
                                                        <span class="text-white/50 text-xs">✦</span>
                                                        <div class="flex-1 border-t border-white/30"></div>
                                                        <span class="text-white/50 text-xs">✦</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="getIssues(getGroupYear('lmp'), 'lmp').length === 0">
                                    <div class="py-10 flex flex-col items-center justify-center gap-2 text-slate-400">
                                        <svg class="w-7 h-7 opacity-40" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        <p class="text-sm">No issues available yet.</p>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Panel body — LMU content --}}
                        <template x-if="activeTab === 'lmu'">
                            <div class="px-5 py-4">
                                <template x-if="getIssues(getGroupYear('lmu'), 'lmu').length > 0">
                                    <div class="flex flex-col gap-4">
                                        <template x-for="issue in getIssues(getGroupYear('lmu'), 'lmu')"
                                            :key="issue.id">
                                            <div class="w-full rounded-2xl overflow-hidden shadow-xl flex flex-row"
                                                style="height: 480px; background: linear-gradient(120deg, #6b5035 0%, #8b6d4c 40%, #6b5035 100%);">
                                                <div class="relative flex-shrink-0 overflow-hidden"
                                                    style="width: 28%;">
                                                    <img :src="driveThumbnailUrl(issue.driveFileId)"
                                                        :alt="issue.label"
                                                        class="absolute inset-0 w-full h-full object-cover scale-110 blur-[2px] opacity-80"
                                                        loading="lazy" onerror="this.style.display='none'">
                                                    <img :src="driveThumbnailUrl(issue.driveFileId)"
                                                        :alt="issue.label"
                                                        class="absolute inset-0 w-full h-full object-contain p-8"
                                                        loading="lazy" onerror="this.style.display='none'">
                                                    <div class="absolute inset-0"
                                                        style="background: linear-gradient(to right, transparent 55%, #6b5035 100%);">
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex flex-col justify-center items-center flex-1 px-10 py-10 gap-8 min-w-0 relative text-center">
                                                    <div class="flex items-center gap-2 w-full max-w-m mb-1">
                                                        <span class="text-white/50 text-xs">✦</span>
                                                        <div class="flex-1 border-t border-white/30"></div>
                                                        <span class="text-white/50 text-xs">✦</span>
                                                    </div>
                                                    <h2 class="text-white font-black leading-tight"
                                                        style="font-size: clamp(1.6rem, 3.2vw, 2.6rem); letter-spacing: 0.15em; text-shadow: 0 2px 16px rgba(0,0,0,0.5);">
                                                        LABOR MARKET <br>UPDATES</br></h2>
                                                    <p class="text-white text-[1rem] font-bold tracking-[0.3em] ">
                                                        REGIONAL SKILLS PROFILE</p>
                                                    <p
                                                        class="text-slate-100 text-[0.95rem]  font-semibold leading-relaxed italic max-w-2xl">
                                                        "Annual publication providing labor market information based on
                                                        data from the PESO Employment Information System (PEIS)"</p>
                                                    <div class="flex items-center justify-center gap-6 flex-wrap pt-2">
                                                        <template x-if="issue.driveFileId">
                                                            <a :href="driveViewUrl(issue.driveFileId)" target="_blank"
                                                                rel="noopener noreferrer" @click.stop
                                                                class="inline-flex items-center gap-2 text-[0.7rem] font-bold tracking-widest  text-slate-900 bg-white hover:bg-slate-100 px-8 py-4 rounded-lg transition-colors shadow-md">
                                                                <svg class="w-3.5 h-3.5" fill="none"
                                                                    stroke="currentColor" stroke-width="2.5"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                                </svg>
                                                                DOWNLOAD
                                                            </a>
                                                        </template>
                                                        <template x-if="issue.driveFileId">
                                                            <a :href="driveViewUrl(issue.driveFileId)" target="_blank"
                                                                rel="noopener noreferrer" @click.stop
                                                                class="inline-flex items-center gap-2 text-[0.7rem] font-bold tracking-widest  text-white/80 hover:text-white border border-white/30 hover:border-white/60 px-8 py-4 rounded-lg transition-colors">
                                                                <svg class="w-3.5 h-3.5" fill="none"
                                                                    stroke="currentColor" stroke-width="2.5"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                                </svg>
                                                                READ ONLINE
                                                            </a>
                                                        </template>
                                                    </div>
                                                    <div class="flex items-center gap-2 w-full max-w-m mt-1">
                                                        <span class="text-white/50 text-xs">✦</span>
                                                        <div class="flex-1 border-t border-white/30"></div>
                                                        <span class="text-white/50 text-xs">✦</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="getIssues(getGroupYear('lmu'), 'lmu').length === 0">
                                    <div class="py-10 flex flex-col items-center justify-center gap-2 text-slate-400">
                                        <svg class="w-7 h-7 opacity-40" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        <p class="text-sm">No issues available yet.</p>
                                    </div>
                                </template>
                            </div>
                        </template>

                    </div>{{-- /shared panel --}}

                </div>{{-- /annual panel tabs --}}



                {{-- ══════════════════════════════════════════════════════════════ --}}
                {{-- ── WEEKLY CARD — Annual-style tab card design ── --}}
                {{-- ══════════════════════════════════════════════════════════════ --}}
                <div class="flex flex-col pt-5 gap-0">

                    {{-- ── Tab Card: Regional LMI Weekly (PESO Weekly Highlights) ── --}}
                    <div class="rounded-xl shadow-md cursor-pointer group select-none transition-all duration-200"
                        :class="activeGroups['peso-highlights'] ? 'ring-2 ring-blue-400 shadow-xl' :
                            'ring-1 ring-white/10 hover:shadow-xl hover:ring-blue-300/40'"
                        @click="toggleGroup('peso-highlights'); if(activeGroups['peso-highlights']) { $nextTick(() => document.getElementById('weekly-panel').scrollIntoView({ behavior: 'smooth', block: 'nearest' })) }">

                        {{-- Inner image+text area — same layout as annual cards --}}
                        <div class="relative flex overflow-hidden rounded-t-xl bg-slate-800" style="height: 340px;">

                            {{-- Left: static clickable image — replace src and href below --}}
                            <div class="w-2/5 flex-shrink-0 relative overflow-hidden group/img">
                                <a href="{{ url('https://philjobnet.gov.ph/') }}" target="_blank"
                                    rel="noopener noreferrer" class="absolute inset-0 block">
                                    {{-- Blurred background layer --}}
                                    <img src="{{ asset($weeklyCardImagePath ?? 'images/philjobnet.png') }}"
                                        alt=""
                                        class="absolute inset-0 w-full h-full object-cover scale-110 blur-[1px] opacity-80">
                                    {{-- Sharp foreground image --}}
                                    <img src="{{ asset($weeklyCardImagePath ?? 'images/philjobnet.png') }}"
                                        alt="Regional LMI Weekly — Latest Issue"
                                        class="absolute inset-0 w-full h-full object-contain p-2 transition-opacity duration-200 group-hover/img:opacity-80">
                                    {{-- Hover overlay with link icon --}}
                                    <div
                                        class="absolute inset-0 flex items-center justify-center opacity-0 group-hover/img:opacity-100 transition-opacity duration-200 bg-black/25">
                                        <svg class="w-8 h-8 text-white drop-shadow" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </div>
                                </a>
                            </div>

                            {{-- Right: text panel --}}
                            <div class="flex-1 flex flex-col justify-between gap-4 px-5 py-6 transition-colors duration-200"
                                :class="activeGroups['peso-highlights'] ? 'bg-slate-700' :
                                    'bg-slate-900 group-hover:bg-slate-800'">
                                {{-- Top divider --}}
                                <div class="flex items-center gap-3 w-full">
                                    <span class="text-white/40 text-xs">✦</span>
                                    <div class="flex-1 border-t border-white/25"></div>
                                    <span class="text-white/40 text-xs">✦</span>
                                </div>
                                <div class="flex flex-col items-center gap-3">
                                    <h2 class="text-white font-black text-center tracking-widest leading-tight drop-shadow"
                                        style="font-size: clamp(1.6rem, 3.2vw, 2.6rem); letter-spacing: 0.15em; text-shadow: 0 2px 16px rgba(0,0,0,0.5);">
                                        REGIONAL LMI<br>WEEKLY</h2>
                                    <h3 class="text-white text-center font-bold tracking-[0.3em]"
                                        style="font-size: clamp(0.6rem, 1vw, 1rem);">WEEKLY TRENDS BULLETIN</h3>
                                    <p class="text-slate-100 text-center font-semibold leading-relaxed italic max-w-2xl pt-2"
                                        style="font-size: clamp(0.75rem, 1.2vw, 0.95rem);">"Direct insights on weekly
                                        hiring trends and vacancy fluctuations in the Davao region. (Based on
                                        PhilJobNet)"</p>
                                </div>
                                {{-- Bottom divider --}}
                                <div class="flex items-center gap-3 w-full">
                                    <span class="text-white/40 text-xs">✦</span>
                                    <div class="flex-1 border-t border-white/25"></div>
                                    <span class="text-white/40 text-xs">✦</span>
                                </div>
                            </div>
                        </div>

                        {{-- Chevron footer — same as annual cards --}}
                        <div class="flex items-center justify-center gap-1.5 py-2 rounded-b-xl transition-colors duration-200"
                            :class="activeGroups['peso-highlights'] ? 'bg-slate-800' : 'bg-slate-800 group-hover:bg-slate-700'">
                            <span
                                class="text-[0.6rem] font-bold tracking-widest uppercase transition-colors duration-200"
                                :class="activeGroups['peso-highlights'] ? 'text-white' :
                                    'text-slate-400 group-hover:text-slate-200'"
                                x-text="activeGroups['peso-highlights'] ? 'Close' : 'CLICK TO EXPLORE'"></span>
                            <svg class="w-3.5 h-3.5 transition-all duration-300"
                                :class="activeGroups['peso-highlights'] ? 'rotate-180 text-white' :
                                    'text-slate-400 group-hover:text-slate-200'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {{-- ── Weekly Panel (expands below the card) ── --}}
                    <div id="weekly-panel" x-show="activeGroups['peso-highlights']"
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="w-full mt-3 rounded-xl overflow-hidden shadow-md border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50"
                        style="display:none;">

                        {{-- Panel header — title + year switcher --}}
                        <div class="flex items-center justify-between px-5 pt-4 pb-3 gap-4">
                            <div class="flex flex-col gap-1 min-w-0">
                                <span
                                    class="inline-flex items-center gap-1.5 text-[0.62rem] font-bold tracking-widest uppercase text-rose-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 flex-shrink-0"></span>
                                    Regional Archives
                                </span>
                                <h2 class="text-slate-800 dark:text-slate-100 font-black tracking-wide leading-tight"
                                    style="font-size: clamp(1.1rem, 2.2vw, 1.6rem);">
                                    REGIONAL LMI WEEKLY
                                </h2>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span
                                    class="text-[0.62rem] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider hidden sm:block">Archive
                                    Year</span>
                                <select @change="setGroupYear('peso-highlights', String($event.target.value))"
                                    :value="getGroupYear('peso-highlights')" @click.stop
                                    class="text-[0.75rem] font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg px-2.5 py-1 shadow-sm cursor-pointer hover:border-[#023E8A] focus:outline-none focus:ring-2 focus:ring-[#023E8A]/30 transition-colors">
                                    <template x-for="year in getYearsForGroup('peso-highlights')"
                                        :key="year">
                                        <option :value="year"
                                            :selected="getGroupYear('peso-highlights') === year"
                                            x-text="year + ' Series'"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="border-b border-slate-200 dark:border-slate-700 mx-5"></div>

                        {{-- Weekly content --}}
                        <div class="px-5 py-4">
                            <div class="flex flex-col gap-5">
                                <template
                                    x-for="monthGroup in getWeeklyByMonth(getGroupYear('peso-highlights'), 'peso-highlights')"
                                    :key="monthGroup.month">
                                    <div>
                                        <div class="flex items-center gap-2 mb-3">
                                            <div class="h-px flex-1 bg-slate-200 dark:bg-slate-700"></div>
                                            <span
                                                class="text-[0.7rem] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider px-2"
                                                x-text="monthGroup.month"></span>
                                            <div class="h-px flex-1 bg-slate-200 dark:bg-slate-700"></div>
                                        </div>
                                        <div class="flex flex-wrap justify-center gap-15">
                                            <template x-for="issue in monthGroup.issues" :key="issue.id">
                                                <div
                                                    class="group/card w-60 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-0.5 hover:border-slate-300 transition-all duration-300 flex flex-col">
                                                    <div
                                                        class="px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700">
                                                        <p class="text-[0.68rem] font-bold text-slate-700 dark:text-slate-200 leading-snug"
                                                            x-html="issue.weekLabel"></p>
                                                        <p class="text-[0.62rem] text-slate-400 mt-0.5"
                                                            x-text="issue.dateRange"></p>
                                                    </div>

                                                    {{-- Image — clicks open linkUrl in new tab if set, otherwise fallback to zoom --}}
                                                    <div class="block flex-shrink-0 bg-slate-100 dark:bg-slate-700 relative"
                                                        style="overflow:hidden;"
                                                        :class="(issue.linkUrl || issue.imageUrl) ? 'cursor-pointer' : ''"
                                                        @click="issue.linkUrl ? window.open(issue.linkUrl, '_blank') : (issue.imageUrl && (zoomImage = issue.imageUrl, imgScale = 0.3))">
                                                        <template x-if="issue.imageUrl">
                                                            <div class="relative">
                                                                <img :src="issue.imageUrl" :alt="issue.weekLabel"
                                                                    class="w-full object-contain transition-opacity duration-300 hover:opacity-80"
                                                                    style="aspect-ratio: 3/4; display:block; background:#f1f5f9;"
                                                                    loading="lazy"
                                                                    onerror="this.parentElement.innerHTML='<div class=\'w-full flex flex-col items-center justify-center gap-2 bg-slate-100 dark:bg-slate-700 text-slate-400\' style=\'aspect-ratio:3/4\'><svg class=\'w-6 h-6\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.5\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg><span class=\'text-[0.65rem]\'>Image unavailable</span></div>'">
                                                                {{-- Link icon overlay when linkUrl is set --}}
                                                                <template x-if="issue.linkUrl">
                                                                    <div
                                                                        class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-200 bg-black/30">
                                                                        <svg class="w-8 h-8 text-white drop-shadow"
                                                                            fill="none" stroke="currentColor"
                                                                            stroke-width="2" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                                        </svg>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </template>
                                                        <template x-if="!issue.imageUrl">
                                                            <div class="w-full flex flex-col items-center justify-center gap-2 bg-slate-100 dark:bg-slate-700 text-slate-400"
                                                                style="aspect-ratio:3/4">
                                                                <svg class="w-6 h-6" fill="none"
                                                                    stroke="currentColor" stroke-width="1.5"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                                <span class="text-[0.65rem]">No image yet</span>
                                                            </div>
                                                        </template>
                                                    </div>

                                                    {{-- Footer button --}}
                                                    <div
                                                        class="px-3 py-2 flex items-center justify-center gap-2 mt-auto border-t border-slate-100 dark:border-slate-700">
                                                        <template x-if="issue.linkUrl">
                                                            <a :href="issue.linkUrl" target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="inline-flex items-center gap-1.5 text-[0.62rem] font-semibold text-[#023E8A] hover:underline">
                                                                <svg class="w-3 h-3" fill="none"
                                                                    stroke="currentColor" stroke-width="2.5"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                                </svg>
                                                                View Full Issue
                                                            </a>
                                                        </template>
                                                        <template x-if="!issue.linkUrl && issue.imageUrl">
                                                            <button @click="zoomImage = issue.imageUrl; imgScale = 0.3"
                                                                class="text-[0.62rem] font-semibold text-[#023E8A] hover:underline">View
                                                                Image</button>
                                                        </template>
                                                        <template x-if="!issue.linkUrl && !issue.imageUrl">
                                                            <span class="text-[0.62rem] text-slate-400 italic">Coming
                                                                soon</span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                <template
                                    x-if="getWeeklyByMonth(getGroupYear('peso-highlights'), 'peso-highlights').length === 0">
                                    <div class="py-10 flex flex-col items-center gap-2 text-slate-400">
                                        <svg class="w-7 h-7 opacity-40" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        <p class="text-sm">No issues available yet.</p>
                                    </div>
                                </template>
                            </div>
                        </div>{{-- /weekly content --}}

                    </div>{{-- /weekly panel --}}

                </div>{{-- /weekly section --}}

            </div>{{-- /publication groups --}}

        </div>{{-- /card --}}

    </div>{{-- /max-w-7xl --}}

    <div class="p-4 bg-slate-50 border-t border-slate-200 text-center">
        <p class="text-xs text-slate-500">Source: Philippine Statistics Authority; Labor Force Survey</p>
    </div>

    {{-- ── Alpine Component ── --}}
    <script>
        function communicatorSection() {
            return {
                activeGroups: {}, // tracks open/closed state per group id independently
                groupYears: {}, // tracks active CY year per group id
                zoomImage: null, // holds the imageUrl currently shown in lightbox
                imgScale: 0.3, // zoom level for lightbox image
                panX: 0, // horizontal pan offset (px)
                panY: 0, // vertical pan offset (px)
                isDragging: false,
                dragStartX: 0,
                dragStartY: 0,

                years: ['2025', '2024', '2023'],


                // ─────────────────────────────────────────────────────────────────────
                // ISSUES (non-weekly)
                // Fields: id, year, groupId, label (HTML ok), driveFileId
                // groupId options: 'jlmf' | 'lmp' | 'lmu'
                // ─────────────────────────────────────────────────────────────────────
                issues: [

                    // ── Labor Market Profile — 2024 ──
                    {
                        id: 'lmp-2024',
                        year: '2024',
                        groupId: 'lmp',
                        label: 'Labor Market Profile 2024',
                        driveFileId: '17vaLumk0G6-Gh-GGye0A9H2G6p91gMJL',
                    },
                    // ── Labor Market Profile — 2023 ──
                    {
                        id: 'lmp-2023',
                        year: '2023',
                        groupId: 'lmp',
                        label: 'Labor Market Profile 2023',
                        driveFileId: '1avAbfnmOP7PG1m0_AVxnLfPOA-XD9TNh',
                    },

                    // ── Jobs and Labor Market Forecast — 2025 ──
                    {
                        id: 'jlmf-2026-2027',
                        year: '2026-2027',
                        groupId: 'jlmf',
                        label: 'Jobs and Labor Market Forecast 2026-2027',
                        driveFileId: '1d54shlQNyCxxokdGEbpGYgOCLAw-__2T',
                    },

                    {
                        id: 'jlmf-2025-2026',
                        year: '2025-2026',
                        groupId: 'jlmf',
                        label: 'Jobs and Labor Market Forecast 2025-2026',
                        driveFileId: '1xyj084tRCGh0JSy3_NPdhZBAoJB7y888',
                    },


                    // ── Labor Market Updates — 2025 ──
                    {
                        id: 'lmu-2025',
                        year: '2025',
                        groupId: 'lmu',
                        label: 'Labor Market Updates: Regional Skills Profile 2025',
                        driveFileId: '1Hh8iQI_YRXi2E9DKNWUm6FI4Uzhx5WOF',
                    },
                    // ── Labor Market Updates — 2024 ──
                    {
                        id: 'lmu-2024',
                        year: '2024',
                        groupId: 'lmu',
                        label: 'Labor Market Updates: Regional Skills Profile 2024',
                        driveFileId: '1doGdSjFBXqRNT0cNXQ_EU8pwS6NXEc7C',
                    },
                    // ── Labor Market Updates — 2023 ──
                    {
                        id: 'lmu-2023',
                        year: '2023',
                        groupId: 'lmu',
                        label: 'Labor Market Updates: Regional Skills Profile 2023',
                        driveFileId: '19V6O_8FpGt5hfNvlEdJB6-_0-onxLhPc',
                    },
                    // ── Labor Market Updates — 2024 ──
                    {
                        id: 'lmu-2022',
                        year: '2022',
                        groupId: 'lmu',
                        label: 'Labor Market Updates: Regional Skills Profile 2022',
                        driveFileId: '1bZTwhmE6mcZ_Bb8bFW0K0dewkVYtg7DI',
                    },
                    // ── Labor Market Updates — 2024 ──
                    {
                        id: 'lmu-2021',
                        year: '2021',
                        groupId: 'lmu',
                        label: 'Labor Market Updates: Regional Skills Profile 2021',
                        driveFileId: '1rYZMfknwoh5FFl__cfq_Qkprt21c-1cN',
                    },
                    // ── Labor Market Updates — 2024 ──
                    {
                        id: 'lmu-2020',
                        year: '2020',
                        groupId: 'lmu',
                        label: 'Labor Market Updates: Regional Skills Profile 2020',
                        driveFileId: '1I4f9TRrT_3u9xX4B4RXD0cwgy6hhnY4e',
                    },
                ],

                // ─────────────────────────────────────────────────────────────────────
                // WEEKLY ISSUES — separate array with month + week structure
                // Fields:
                //   id          — unique string
                //   year        — number
                //   groupId     — must be a group with frequency === 'Weekly'
                //   month       — full month name, e.g. 'January' (used as section header)
                //   monthOrder  — number for sorting months (1 = Jan, 2 = Feb, ...)
                //   weekLabel   — e.g. 'Week 1'
                //   dateRange   — e.g. 'Jan 1–7, 2025'
                //   imageUrl    — full URL to admin-uploaded image (e.g. storage path or CDN URL)
                // ─────────────────────────────────────────────────────────────────────
                weeklyIssues: [
                    // ── April 2026 ──
                    // linkUrl: the URL the image and button will open when clicked (Google Drive, external site, etc.)
                    {
                        id: 'pw-2026-april-w1',
                        year: 2026,
                        groupId: 'peso-highlights',
                        month: 'April',
                        monthOrder: 1,
                        weekLabel: 'Week 1',
                        dateRange: 'April 24–30, 2025',
                        imageUrl: "{{ asset('images/LMIAPRILWEEK1.jpg') }}",
                        linkUrl: ''
                    },
                    {
                        id: 'pw-2026-april-w2',
                        year: 2026,
                        groupId: 'peso-highlights',
                        month: 'April',
                        monthOrder: 2,
                        weekLabel: 'Week 2',
                        dateRange: 'April 6–10, 2026',
                        imageUrl: "{{ asset('images/LMIAPRILWEEK2.png') }}",
                        linkUrl: ''
                    },
                    {
                        id: 'pw-2025-april-w3',
                        year: 2026,
                        groupId: 'peso-highlights',
                        month: 'April',
                        monthOrder: 3,
                        weekLabel: 'Week 3',
                        dateRange: 'April 17–23, 2026',
                        imageUrl: "{{ asset('images/LMIAPRILWEEK3.png') }}",
                        linkUrl: ''
                    },
                    {
                        id: 'pw-2025-jan-w4',
                        year: 2026,
                        groupId: 'peso-highlights',
                        month: 'April',
                        monthOrder: 4,
                        weekLabel: 'Week 4',
                        dateRange: 'April 24–30, 2026',
                        imageUrl: "{{ asset('images/LMIAPRILWEEK4.png') }}",
                        linkUrl: ''
                    },


                ],

                // ─── Helpers ──────────────────────────────────────────────────────────

                // Returns all years that have data for a given group (sorted descending)
                getYearsForGroup(groupId) {
                    const fromIssues = this.issues.filter(i => i.groupId === groupId).map(i => i.year);
                    const fromWeekly = this.weeklyIssues.filter(i => i.groupId === groupId).map(i => i.year);
                    return [...new Set([...fromIssues, ...fromWeekly])].sort((a, b) => String(b).localeCompare(String(a)));
                },

                // Get the currently active year for a group (defaults to most recent)
                getGroupYear(groupId) {
                    if (this.groupYears[groupId] !== undefined) return this.groupYears[groupId];
                    const years = this.getYearsForGroup(groupId);
                    return years.length > 0 ? years[0] : this.years[0];
                },

                // Set active year for a specific group
                setGroupYear(groupId, year) {
                    this.groupYears[groupId] = year;
                    this.groupYears = {
                        ...this.groupYears
                    };
                },

                // Toggle accordion open/close, initialise group year on first open
                toggleGroup(groupId) {
                    this.activeGroups = {
                        ...this.activeGroups,
                        [groupId]: !this.activeGroups[groupId]
                    };
                    if (this.activeGroups[groupId] && this.groupYears[groupId] === undefined) {
                        const years = this.getYearsForGroup(groupId);
                        if (years.length > 0) this.setGroupYear(groupId, years[0]);
                    }
                },

                getIssues(year, groupId) {
                    return this.issues.filter(i => i.year === year && i.groupId === groupId);
                },

                // Groups weekly issues by month, returns array of { month, issues[] }
                getWeeklyByMonth(year, groupId) {
                    const filtered = this.weeklyIssues.filter(i => i.year === year && i.groupId === groupId);
                    const monthMap = {};
                    filtered.forEach(issue => {
                        if (!monthMap[issue.month]) {
                            monthMap[issue.month] = {
                                month: issue.month,
                                order: issue.monthOrder,
                                issues: []
                            };
                        }
                        monthMap[issue.month].issues.push(issue);
                    });
                    return Object.values(monthMap).sort((a, b) => a.order - b.order);
                },

                driveThumbnailUrl(fileId) {
                    if (!fileId || fileId.startsWith('REPLACE')) return '';
                    return `https://drive.google.com/thumbnail?id=${fileId}&sz=s500`;
                },

                driveViewUrl(fileId) {
                    return `https://drive.google.com/file/d/${fileId}/view?usp=sharing`;
                },

                // Auto-picks the most recent issue's Drive thumbnail for the group banner.
                // For weekly groups, falls back to the most recent week's imageUrl.
                getGroupBannerUrl(groupId, frequency) {
                    const years = this.getYearsForGroup(groupId);
                    if (years.length === 0) return '';
                    const latestYear = years[0];
                    if (frequency !== 'Weekly') {
                        const issue = this.issues.find(i => i.groupId === groupId && i.year === latestYear);
                        return issue ? this.driveThumbnailUrl(issue.driveFileId) : '';
                    } else {
                        // Weekly: find most recent week that has an imageUrl
                        const weeklies = this.weeklyIssues
                            .filter(i => i.groupId === groupId && i.year === latestYear && i.imageUrl)
                            .sort((a, b) => b.monthOrder - a.monthOrder);
                        return weeklies.length > 0 ? weeklies[0].imageUrl : '';
                    }
                },
            };
        }
    </script>
</body>

</html>
