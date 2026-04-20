<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    @vite('resources/css/app.css')
    @vite('resources/js/public/peso-directory.js')
    <script src="https://cdn.jsdelivr.net/npm/fuse.js@7.0.0/dist/fuse.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>PESO / JPO Directory</title>
    <style>
        html {
            scroll-behavior: smooth;
        }

        [x-cloak] {
            display: none !important;
        }

        @media (max-width: 480px) {
            .office-type-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        .peso-description-content,
        .peso-description-content * {
            color: white !important;
        }

        .peso-howto-content,
        .peso-howto-content * {
            color: white !important;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-4 {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        /* ── PESO Carousel ── */
        #peso-carousel-section {
            height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .peso-carousel-slide {
            position: absolute;
            inset: 0;
            transition: opacity 0.7s ease-in-out;
        }

        .peso-carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        /* Subtle dark vignette so arrows/dots are always visible */
        .peso-carousel-slide::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom,
                    rgba(0, 0, 0, 0.15) 0%,
                    rgba(0, 0, 0, 0.05) 40%,
                    rgba(0, 0, 0, 0.05) 60%,
                    rgba(0, 0, 0, 0.35) 100%);
            pointer-events: none;
        }

        .peso-objective-content,
        .peso-objective-content * {
            font-size: 0.75rem !important;
            line-height: 1.5 !important;
            color: #475569 !important;
        }

        /* ── Mobile: office type grid ── */
        @media (max-width: 480px) {
            .office-type-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            /* Tighten office type card padding on very small screens */
            .office-type-grid button {
                padding: 0.625rem !important;
                gap: 0.375rem !important;
            }
        }

        /* ── Mobile: results header filter row wrapping ── */
        @media (max-width: 640px) {
            .results-filter-row {
                flex-wrap: wrap;
            }
        }

        /* ── Prevent horizontal overflow on all screens ── */
        body {
            overflow-x: hidden;
        }

        /* ── Tablet: info section hero flex ── */
        @media (max-width: 639px) {
            .peso-hero-flex {
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
            }
            .peso-hero-flex .peso-description-content {
                text-align: left !important;
            }
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen">

    @include('partials.navbar')

    {{-- ===== PESO PHOTO CAROUSEL ===== --}}
    @php
        $pesoCarouselImages = $slides ?? [];
    @endphp

    @if (count($pesoCarouselImages) > 0)

        <div id="peso-carousel-section" x-data="pesoPhotoCarousel({{ json_encode($pesoCarouselImages) }})" x-init="startAutoplay()" @mouseenter="stopAutoplay()"
            @mouseleave="startAutoplay()">

            {{-- Slides --}}
            <template x-for="(src, index) in slides" :key="index">
                <div class="peso-carousel-slide"
                    :style="current === index ?
                        'opacity:1; z-index:1;' :
                        'opacity:0; z-index:0;'">
                    <img :src="src" :alt="'PESO Slide ' + (index + 1)">
                </div>
            </template>

            {{-- Carousel Title Overlay — bottom-left, avoids covering faces --}}
            <div class="absolute bottom-36 sm:bottom-44 md:bottom-48 left-4 sm:left-6 md:left-12 z-20 pointer-events-none">
                <p class="text-blue-200 text-xs font-bold uppercase tracking-[0.25em] mb-1"
                    style="text-shadow: 0 1px 8px rgba(0,0,0,1);">DOLE · Region XI</p>
                <h2 class="text-white font-black leading-tight tracking-tight"
                    style="font-size: clamp(1.25rem, 4vw, 3.5rem); text-shadow: 0 2px 16px rgba(0,0,0,1), 0 0 40px rgba(0,0,0,0.7);">
                    Davao Region
                </h2>
                <h2 class="font-bold leading-tight tracking-tight"
                    style="color: #93c5fd; font-size: clamp(0.9rem, 2.5vw, 2.25rem); text-shadow: 0 2px 12px rgba(0,0,0,1);">
                    PESO / JPO
                </h2>
            </div>

            {{-- Prev arrow --}}
            <button @click="prev()"
                class="absolute left-4 sm:left-6 top-1/2 -translate-y-1/2 z-20
                   w-11 h-11 sm:w-14 sm:h-14
                   bg-white/20 hover:bg-white/40 backdrop-blur-md
                   rounded-full border border-white/30
                   flex items-center justify-center
                   transition-all duration-200"
                aria-label="Previous slide">
                <svg class="w-5 h-5 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            {{-- Next arrow --}}
            <button @click="next()"
                class="absolute right-4 sm:right-6 top-1/2 -translate-y-1/2 z-20
                   w-11 h-11 sm:w-14 sm:h-14
                   bg-white/20 hover:bg-white/40 backdrop-blur-md
                   rounded-full border border-white/30
                   flex items-center justify-center
                   transition-all duration-200"
                aria-label="Next slide">
                <svg class="w-5 h-5 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            {{-- Dot indicators --}}
            <div
                class="absolute bottom-24 sm:bottom-32 left-0 right-0 z-20
                    flex items-center justify-center gap-2 sm:gap-3">
                <template x-for="(src, index) in slides" :key="index">
                    <button @click="goTo(index)"
                        class="transition-all duration-300"
                        :class="current === index ?
                            'w-16 h-4' :
                            'w-4 h-4'"
                        :aria-label="'Go to slide ' + (index + 1)">
                        <div class="w-full h-full rounded-full backdrop-blur-md border-2"
                            :class="current === index ?
                                'bg-white border-white' :
                                'bg-white/40 border-white/60'">
                        </div>
                    </button>
                </template>
            </div>

            {{-- Scroll down arrow --}}
            <a href="#peso-directory-content"
                class="absolute bottom-6 sm:bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce cursor-pointer z-20"
                @click.prevent="document.getElementById('peso-directory-content').scrollIntoView({ behavior: 'smooth' })">
                <div class="flex flex-col items-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-8 md:h-8 text-white drop-shadow-lg" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                    <p class="text-white text-xs sm:text-sm mt-0.5 sm:mt-2 font-medium"
                       style="text-shadow: 0 1px 6px rgba(0,0,0,0.8);">Scroll to explore</p>
                </div>
            </a>

        </div>
        {{-- ===== END PESO PHOTO CAROUSEL ===== --}}
    @endif

    <div id="peso-directory-content" class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 py-8 sm:py-12 md:py-16">

        @php
            $pesoJson = collect($snapshot)->map(
                fn($offices) => collect($offices)
                    ->map(
                        fn($o) => [
                            'id' => $o['id'] ?? null,
                            'name' => $o['name'] ?? '',
                            'position_title' => $o['position_title'] ?? '',
                            'persons_name' => $o['persons_name'] ?? '',
                            'email' => $o['email'] ?? '',
                            'address' => $o['address'] ?? '',
                            'type' => $o['type'] ?? ($o['office_type'] ?? ''),
                        ],
                    )
                    // Sort: PESO group first, JPO group second.
                    // Within each group: shorter names (fewer clamp lines) first, longer names last.
                    // Within the same clamp bucket: oldest first, latest added last.
                    ->sortBy(fn($a) => sprintf(
                        '%d-%04d-%010d',
                        ($a['type'] === 'PESO' ? 0 : 1),
                        (int) ceil(mb_strlen($a['name']) / 25),
                        $a['id']
                    ))
                    ->values(),
            );
        @endphp

        {{-- ===== PESO INFO SECTION ===== --}}
        <div class="mt-4 mb-6 bg-white border border-slate-200 rounded-2xl shadow-md overflow-hidden">

            {{-- Hero banner with PESO logo integrated --}}
            <div class="relative overflow-hidden px-4 sm:px-6 md:px-8 py-5 sm:py-7"
                style="background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 60%, #1e40af 100%);">
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute -top-10 right-10 w-48 h-48 rounded-full opacity-10"
                        style="background: radial-gradient(circle, #fbbf24 0%, transparent 70%);"></div>
                    <div class="absolute -bottom-10 left-1/3 w-40 h-40 rounded-full opacity-10"
                        style="background: radial-gradient(circle, #93c5fd 0%, transparent 70%);"></div>
                </div>
                <div class="relative flex flex-col sm:flex-row items-center sm:items-start gap-5 peso-hero-flex">
                    {{-- PESO Logo --}}
                    <div class="flex-shrink-0 flex items-center justify-center">
                        <div class="relative">
                            <div class="absolute inset-0 rounded-full blur-xl opacity-40"
                                style="background: radial-gradient(circle, #fbbf24 0%, #1d4ed8 60%, transparent 80%); transform: scale(1.3);">
                            </div>
                            <img src="{{ asset('images/PESO.png') }}" alt="PESO Logo"
                                class="relative w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-2xl"
                                style="filter: drop-shadow(0 0 12px rgba(251,191,36,0.35));">
                        </div>
                    </div>
                    <div>
                        <p class="text-amber-300 text-xs font-bold uppercase tracking-[0.2em] mb-1">What is PESO?</p>
                        <div class="text-sm sm:text-base leading-relaxed peso-description-content"
                            style="text-align: justify;">
                            {!! $pesoInfo['description'] ?? '' !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-6 md:p-8 grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">

                {{-- Objectives — compact --}}
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold text-blue-800 uppercase tracking-wide">Objective</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed peso-objective-content">
                        {!! $pesoInfo['objective'] ?? '' !!}
                    </p>
                </div>

                {{-- Core Services --}}
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-lg bg-slate-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Core Services</h3>
                    </div>
                    <ul class="space-y-1.5">
                        @foreach ($pesoInfo['core_services'] as $service)
                            <li class="flex items-start gap-2 text-xs text-slate-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1 flex-shrink-0"></span>
                                {{ $service['name'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Beneficiaries --}}
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold text-blue-800 uppercase tracking-wide">Beneficiaries</h3>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($pesoInfo['beneficiaries'] as $ben)
                            <span
                                class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                <span class="w-1 h-1 rounded-full bg-blue-400 flex-shrink-0"></span>
                                {{ $ben['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- How to avail footer --}}
            <div class="mx-3 sm:mx-6 md:mx-8 mb-4 sm:mb-6 bg-blue-600 rounded-xl px-4 sm:px-5 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3 flex-1">
                    <svg class="w-5 h-5 text-blue-200 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 016 0z" />
                    </svg>
                    <div class="text-sm peso-howto-content">
                        <strong style="color: #bfdbfe !important;">How to Avail:</strong>
                        {!! $pesoInfo['how_to_avail'] ?? '' !!}
                    </div>
                </div>
                {{-- Scroll-to-directory indicator --}}
                <button
                    onclick="document.querySelector('.bg-white.rounded-2xl.shadow-2xl')?.scrollIntoView({behavior:'smooth'})"
                    class="w-full sm:w-auto flex-shrink-0 flex flex-row sm:flex-col items-center justify-center gap-2 sm:gap-1 group cursor-pointer bg-white/10 hover:bg-white/20 border border-white/25 rounded-xl px-4 py-2.5 transition-all duration-200">
                    <span class="text-white/80 text-xs font-semibold whitespace-nowrap">View Directory</span>
                    <svg class="w-4 h-4 text-white group-hover:text-blue-200 transition-all duration-200"
                        style="animation: howtoArrowBounce 1.4s ease-in-out infinite;" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
            <style>
                @keyframes howtoArrowBounce {

                    0%,
                    100% {
                        transform: translateY(0);
                    }

                    50% {
                        transform: translateY(4px);
                    }
                }
            </style>

        </div>
        {{-- ===== END PESO INFO SECTION ===== --}}


        {{-- ─── Blade → JS Data Bridge ──────────────────────────────────────────── --}}
        {{-- $pesoJson cannot live in .js — injected here so Alpine reads it on init. --}}
        <script>
            window._pesoDirectoryData = {
                pesoJson: @json($pesoJson),
            };
        </script>


        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200" x-data="pesoDirectory()">

            {{-- Directory card top bar --}}
            <div class="flex flex-wrap items-center gap-3 px-4 sm:px-6 md:px-8 py-4 border-b border-slate-100"
                style="background: linear-gradient(90deg, #f8fafc 0%, #eff6ff 100%);">
                <img src="{{ asset('images/PESO.png') }}" alt="PESO"
                    class="w-8 h-8 object-contain flex-shrink-0">
                <div class="flex-1">
                    <p class="text-sm font-bold text-slate-700">PESO / JPO Directory</p>
                    <p class="text-xs text-slate-400">Select a province, then filter by Office Type</p>
                </div>
                <span class="text-xs font-bold text-blue-600 bg-blue-50 border border-blue-200 px-3 py-1 rounded-full">
                    {{ count($pesoProvinceKeys ?? []) }} Province{{ count($pesoProvinceKeys ?? []) !== 1 ? 's' : '' }}
                </span>
            </div>

            <div class="p-3 sm:p-4 md:p-6 lg:p-10 space-y-6 sm:space-y-8">

                {{-- STEP 1: Province --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">
                        1 · Select Province
                    </label>
                    {{-- Custom dropdown: avoids overflow-clipping & off-screen issues on mobile --}}
                    <div class="relative w-full" x-data="{ open: false }" @keydown.escape.window="open = false">
                        {{-- Trigger button --}}
                        <button type="button"
                            @click="open = !open"
                            class="w-full flex items-center justify-between bg-white border-2 rounded-xl px-4 py-3 pr-4 text-sm font-semibold outline-none transition-all cursor-pointer text-left"
                            :class="province ? 'border-orange-400 shadow-[0_0_0_3px_rgba(251,146,60,0.15)] text-slate-800' : 'border-slate-200 text-slate-400 hover:border-slate-300'">
                            <span x-text="province || 'Select Province'"></span>
                            <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200 text-slate-400"
                                :class="open ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Dropdown panel --}}
                        <div x-show="open"
                            @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            x-cloak
                            style="position:absolute; top:calc(100% + 4px); left:0; right:0; z-index:9999; background:white; border:2px solid #e2e8f0; border-radius:0.75rem; box-shadow:0 8px 24px rgba(0,0,0,0.12); max-height:min(260px, 45vh); overflow-y:auto;">
                            @foreach ($pesoProvinceKeys as $prov)
                                <button type="button"
                                    @click="selectProvince('{{ $prov }}'); open = false"
                                    class="w-full text-left px-4 py-2.5 text-sm font-semibold transition-colors hover:bg-orange-50 hover:text-orange-600"
                                    :class="province === '{{ $prov }}' ? 'bg-orange-50 text-orange-600' : 'text-slate-700'">
                                    {{ $prov }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- STEP 2: Office Type --}}
                <div x-ref="typeSection" x-show="showType" x-transition:enter="transition ease-out duration-350"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">
                        2 · Office Type
                    </label>
                    <div class="grid grid-cols-3 gap-3 w-full office-type-grid">

                        <button @click="selectType('ALL')" type="button"
                            class="rounded-xl p-4 flex flex-col items-center gap-2 border-2 transition-all duration-200 cursor-pointer text-center"
                            :style="officeType === 'ALL' ?
                                'background:#eef2ff; border-color:#6366f1; box-shadow:0 0 0 3px #eef2ff; transform:translateY(-2px);' :
                                'background:white; border-color:#e2e8f0;'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                :style="officeType === 'ALL' ? 'color:#6366f1' : 'color:#94a3b8'">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="text-xs font-bold"
                                :style="officeType === 'ALL' ? 'color:#6366f1' : 'color:#64748b'">All
                                Offices</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                :style="officeType === 'ALL' ?
                                    'background:white; color:#6366f1; border:1px solid #c7d2fe' :
                                    'background:#f1f5f9; color:#94a3b8; border:1px solid #e2e8f0'"
                                x-text="countFor(province, 'ALL') + ' offices'"></span>
                        </button>

                        <template x-for="t in officeTypes" :key="t">
                            <button @click="selectType(t)" type="button"
                                class="rounded-xl p-4 flex flex-col items-center gap-2 border-2 transition-all duration-200 cursor-pointer text-center"
                                :style="officeType === t ?
                                    `background:${typeColor(t,'bg')}; border-color:${typeColor(t,'main')}; box-shadow:0 0 0 3px ${typeColor(t,'bg')}; transform:translateY(-2px);` :
                                    'background:white; border-color:#e2e8f0;'">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    :style="`color:${officeType === t ? typeColor(t,'main') : '#94a3b8'}`">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs font-bold"
                                    :style="`color:${officeType === t ? typeColor(t,'main') : '#64748b'}`"
                                    x-text="t + ' Only'"></span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                    :style="officeType === t ?
                                        `background:white; color:${typeColor(t,'main')}; border:1px solid ${typeColor(t,'border')}` :
                                        'background:#f1f5f9; color:#94a3b8; border:1px solid #e2e8f0'"
                                    x-text="countFor(province, t) + ' offices'"></span>
                            </button>
                        </template>

                    </div>
                </div>

                {{-- STEP 3: Results --}}
                <div x-ref="resultsSection" x-show="showResults"
                    x-transition:enter="transition ease-out duration-350"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0" x-cloak>

                    <div class="rounded-xl px-4 sm:px-5 py-3 sm:py-4 mb-4 sm:mb-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3"
                        style="background:#f0fdf4; border:1.5px solid #bbf7d0;">
                        <div>
                            <p class="text-base sm:text-lg font-extrabold text-slate-800 uppercase" x-text="province"></p>
                            <p class="text-xs sm:text-sm mt-0.5 text-slate-500">
                                <strong class="text-green-600"
                                    x-text="countFor(province,'PESO') + ' PESO Offices'"></strong>
                                <span class="mx-1">·</span>
                                <strong class="text-blue-600"
                                    x-text="countFor(province,'JPO') + ' JPO Offices'"></strong>
                            </p>
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                            <div class="flex gap-1 bg-white rounded-xl p-1 border border-slate-200 shadow-sm">
                                <template x-for="opt in ['ALL','PESO','JPO']" :key="opt">
                                    <button @click="selectType(opt)" type="button"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all"
                                        :style="officeType === opt ?
                                            `background:${ opt==='JPO' ? '#2563eb' : opt==='PESO' ? '#16a34a' : '#1e293b' }; color:white` :
                                            'color:#64748b'"
                                        x-text="opt">
                                    </button>
                                </template>
                            </div>
                            <button @click="province=''; officeType=''; showType=false; showResults=false; search='';"
                                class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Cancel
                            </button>
                        </div>
                    </div>

                    <div class="relative mb-4">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                            </svg>
                        </span>
                        <input type="text" x-model="search" placeholder="Search by office name, manager..."
                            class="w-full border border-slate-200 rounded-xl pl-9 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none transition bg-slate-50 focus:bg-white" />
                        <button x-show="search.trim()" @click="search = ''"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition"
                            x-cloak>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div x-show="filteredEntries().length === 0" class="text-center py-10 text-slate-400" x-cloak>
                        <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                        </svg>
                        <p class="text-sm font-semibold">No offices found</p>
                        <p class="text-xs mt-1">Try a different search term</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 items-start" x-data="{ openId: null }">
                        <template x-for="entry in filteredEntries()" :key="entry.id">
                            <div class="rounded-xl border-2 overflow-hidden transition-all duration-200"
                                :style="openId === entry.id ?
                                    `border-color:${typeColor(entry.type,'border')}; background:${typeColor(entry.type,'bg')}; box-shadow:0 4px 16px rgba(0,0,0,0.08)` :
                                    'border-color:#e2e8f0; background:white; box-shadow:0 1px 4px rgba(0,0,0,0.04)'">

                                <div class="flex items-center gap-3 px-4 py-3 cursor-pointer select-none"
                                    @click="openId = (openId === entry.id) ? null : entry.id">
                                    <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center text-white font-bold text-sm"
                                        :style="`background:${typeColor(entry.type,'main')}`"
                                        x-text="entry.name.charAt(0)"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-slate-800 line-clamp-2 uppercase"
                                        :title="entry.name"
                                        x-text="entry.name"></p>
                                        {{-- FIX 2: Show position_title · persons_name instead of entry.manager --}}
                                        <p class="text-xs truncate mt-0.5 font-semibold"
                                            :style="`color:${typeColor(entry.type,'main')}`"
                                            x-text="(entry.position_title ? entry.position_title + ' · ' : '') + (entry.persons_name || '—')">
                                        </p>
                                    </div>
                                    <span
                                        class="inline-flex items-center text-xs font-bold px-2.5 py-1 rounded-lg flex-shrink-0"
                                        :style="`background:${typeColor(entry.type,'bg')}; color:${typeColor(entry.type,'main')}; border:1.5px solid ${typeColor(entry.type,'border')}`"
                                        x-text="entry.type"></span>
                                    <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200"
                                        :class="openId === entry.id ? 'rotate-180' : ''"
                                        :style="`color:${typeColor(entry.type,'main')}`" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>

                                <div x-show="openId === entry.id" x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-1"
                                    class="px-3 sm:px-4 pb-3 sm:pb-4 pt-2 sm:pt-3 flex flex-col gap-2 sm:gap-2.5"
                                    :style="`border-top:1.5px solid ${typeColor(entry.type,'border')}`">

                                    {{-- FIX 3: label = actual position_title, value = persons_name --}}
                                    <template
                                        x-for="[label, icon, value, href] in [
                                        [entry.position_title || (entry.type === 'JPO' ? 'JPO Manager' : 'PESO Manager'), 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', entry.persons_name, null],
                                        ['Email Address', 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', entry.email, entry.email ? `mailto:${entry.email}` : null],
                                        ['Address', 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z', entry.address, null],
                                    ].filter(r => r[2])">
                                        <div class="flex items-start gap-2.5">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-slate-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    :d="icon" />
                                            </svg>
                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-0.5"
                                                    x-text="label"></p>
                                                <template x-if="href">
                                                    <a :href="href"
                                                        class="text-sm text-blue-500 hover:underline"
                                                        x-text="value"></a>
                                                </template>
                                                <template x-if="!href">
                                                    <span class="text-sm text-slate-700" x-text="value"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                </div>
                            </div>
                        </template>
                    </div>

                </div>
                {{-- END STEP 3 --}}

                <div x-show="!province" class="text-center py-6 sm:py-8 text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <p class="text-sm font-medium">Select a province above to browse offices</p>
                </div>

            </div>
        </div>

    </div>
    {{-- ===== END PESO DIRECTORY CONTENT ===== --}}

</body>

</html>