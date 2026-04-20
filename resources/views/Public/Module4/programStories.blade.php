<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    @vite('resources/css/app.css')
    @vite('resources/js/public/program-stories.js')
    <script src="https://cdn.jsdelivr.net/npm/fuse.js@7.0.0/dist/fuse.min.js"></script>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Programs and Stories</title>

    <style>
        html {
            scroll-behavior: smooth;
        }

        [x-cloak] {
            display: none !important;
        }

        .chevron-icon {
            transition: transform 0.3s ease;
        }

        .chevron-icon.open {
            transform: rotate(180deg);
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

        /* Stories Carousel */
        .stories-carousel-outer {
            overflow: hidden;
            border-radius: 12px;
            position: relative;
        }

        .stories-carousel-track {
            display: flex;
            gap: 12px;
            transition: transform 0.45s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
        }

        .story-card-slide {
            flex: 0 0 calc(20% - 10px);
            background: white;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            position: relative;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        }

        .story-card-slide:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.13), 0 4px 12px rgba(0, 0, 0, 0.07);
        }

        .story-card-slide:hover .story-card-img img {
            transform: scale(1.08);
        }

        .story-card-img {
            position: relative;
            height: 112px;
            overflow: hidden;
            background: #e2e8f0;
        }

        .story-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: block;
        }

        .story-card-body {
            padding: 9px 9px 11px;
        }

        .story-card-title {
            font-size: 0.72rem;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 5px;
            min-height: 2em;
        }

        .story-card-link {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 0.68rem;
            font-weight: 700;
            text-decoration: none;
        }

        .story-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #d1d5db;
            cursor: pointer;
            transition: all 0.25s ease;
            border: none;
            padding: 0;
            flex-shrink: 0;
        }

        .story-dot.active {
            background: var(--dot-color, #6366f1);
            width: 20px;
            border-radius: 4px;
        }

        .story-nav-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .story-nav-btn:disabled {
            opacity: 0.3;
            cursor: default;
            pointer-events: none;
        }

        .stories-carousel-wrapper {
            position: relative;
            padding: 0 20px;
        }

        @media (max-width: 480px) {
            .stories-carousel-wrapper {
                padding: 0 28px;
            }
        }

        .story-nav-floating {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }

        .story-nav-floating.left {
            left: -6px;
        }

        .story-nav-floating.right {
            right: -6px;
        }

        /* Testimonials scrollable column */
        .testimonials-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .testimonials-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .testimonials-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .testimonials-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ── Stories carousel: responsive cards per page ── */
        @media (max-width: 480px) {
            .story-card-slide {
                flex: 0 0 calc(100% - 0px) !important;
                width: calc(100% - 0px) !important;
            }
        }

        @media (min-width: 481px) and (max-width: 767px) {
            .story-card-slide {
                flex: 0 0 calc(50% - 6px) !important;
                width: calc(50% - 6px) !important;
            }
        }

        @media (min-width: 768px) and (max-width: 1023px) {
            .story-card-slide {
                flex: 0 0 calc(33.33% - 8px) !important;
                width: calc(33.33% - 8px) !important;
            }
        }

        /* ── Testimonials column: uncap fixed height on mobile ── */
        @media (max-width: 1023px) {
            .testimonials-scroll {
                height: auto !important;
                max-height: 500px;
            }

            /* Ensure testimonials stack below stories carousel on mobile */
            .lg\:col-span-1.order-last {
                order: 9;
            }
        }

        /* ── Hero carousel: smaller title/text on mobile ── */
        @media (max-width: 480px) {
            .hero-title {
                font-size: 1.75rem !important;
                line-height: 1.2;
            }

            .hero-excerpt {
                font-size: 1rem !important;
            }

            .hero-cta-btn {
                padding: 0.75rem 1.5rem !important;
                font-size: 0.95rem !important;
            }
        }

        /* ── PESO office type grid: 2 cols on mobile ── */
        @media (max-width: 480px) {
            .office-type-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        /* CTA section buttons removed */
    </style>
</head>

<body class="bg-slate-100 min-h-screen">
    <div x-data="{ activeProgram: null }">
        @include('partials.navbar')

           <!-- ===== CAROUSEL ===== -->
        @php
            // Only allow http/https URLs to prevent javascript: open-redirect attacks
            $sanitizeUrl = fn($url) => preg_match('/^https?:\/\//i', $url ?? '') ? $url : '#';
            $carouselSlidesJson = $carouselSlides
                ->map(
                    fn($s) => [
                        'image' => str_starts_with($s->image_path, 'images/')
                            ? asset($s->image_path)
                            : asset('storage/' . $s->image_path),
                        'title' => $s->title,
                        'excerpt' => strip_tags($s->excerpt ?? ''),
                        'link' => $sanitizeUrl($s->link),
                        'program' => $s->program_label,
                        'color' => $s->color,
                    ],
                )
                ->toJson();
        @endphp
        <div class="relative w-full h-screen overflow-hidden" x-data="publicCarousel({{ $carouselSlidesJson }})" x-init="startAutoplay()"
            @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">

            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="currentSlide === index" x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0 transform translate-x-full"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform -translate-x-full" class="absolute inset-0">
                    <div class="absolute inset-0">
                        <img :src="slide.image" :alt="slide.title"
                            class="w-full h-full object-cover object-center">
                        <div
                            class="absolute inset-0 bg-gradient-to-b from-slate-900/85 via-slate-900/70 to-slate-900/50">
                        </div>
                    </div>
                    <div class="relative z-10 h-full flex items-center justify-center px-4">
                        <div
                            class="flex flex-col items-center justify-center text-center text-white max-w-5xl h-full py-16 sm:py-20">

                            <div class="flex-grow flex flex-col justify-center mb-6 sm:mb-8">
                                <h1 class="text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold mb-4 sm:mb-6 drop-shadow-2xl leading-tight line-clamp-2 hero-title"
                                    x-text="slide.title"></h1>

                                <p class="text-base sm:text-xl md:text-2xl lg:text-3xl text-slate-50 drop-shadow-lg max-w-4xl mx-auto leading-relaxed font-light line-clamp-3 hero-excerpt"
                                    x-text="slide.excerpt"></p>
                            </div>

                            <div class="flex-shrink-0 pb-16 sm:pb-20">
                                <a :href="slide.link" target="_blank"
                                    class="inline-flex items-center gap-2 sm:gap-3 px-6 sm:px-10 py-3 sm:py-5 bg-white text-slate-900 font-bold text-base sm:text-lg rounded-xl hover:bg-slate-100 transition-all duration-300 shadow-2xl transform hover:-translate-y-2 hover:scale-105 hero-cta-btn">
                                    <span>READ FULL STORY</span>
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <a href="#programs-section"
                        class="absolute bottom-6 sm:bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce cursor-pointer z-20"
                        @click.prevent="document.getElementById('programs-section').scrollIntoView({ behavior: 'smooth' })">
                        <div class="flex flex-col items-center">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            <p class="text-white text-sm mt-2 font-medium">Scroll to explore</p>
                        </div>
                    </a>
                </div>
            </template>

            <button @click="prevSlide()"
                class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-14 sm:h-14 bg-white/25 hover:bg-white/40 backdrop-blur-md rounded-full flex items-center justify-center transition-all duration-300 group border border-white/30">
                <svg class="w-5 h-5 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button @click="nextSlide()"
                class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-14 sm:h-14 bg-white/25 hover:bg-white/40 backdrop-blur-md rounded-full flex items-center justify-center transition-all duration-300 group border border-white/30">
                <svg class="w-5 h-5 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div class="absolute bottom-24 sm:bottom-32 left-1/2 -translate-x-1/2 z-20 flex items-center gap-4">
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="goToSlide(index)" class="transition-all duration-300"
                        :class="currentSlide === index ? 'w-16 h-4' : 'w-4 h-4'">
                        <div class="w-full h-full rounded-full backdrop-blur-md border-2"
                            :class="currentSlide === index ? 'bg-white border-white' : 'bg-white/40 border-white/60'">
                        </div>
                    </button>
                </template>
            </div>
        </div>
        <!-- ===== END CAROUSEL ===== -->

        <!-- ===== PROGRAMS SECTION ===== -->
        <div id="programs-section" class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-12 lg:py-16">

            <div
                class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 rounded-2xl px-5 sm:px-8 py-5 sm:py-7 shadow-2xl mb-2">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-2xl md:text-3xl">DOLE Employment Programs</h2>
                        <p class="text-slate-300 text-sm md:text-base">Click on any program below to view details and
                            eligibility</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl shadow-2xl border border-slate-200 divide-y divide-slate-200 overflow-hidden">

                @foreach ($programs as $program)
                    @php
                        // ── Read from snapshot for public, live data for draft preview ──
                        $isPreviewMode = $isPreview ?? false;
                        if (!isset($isPreview)) {
                            \Log::warning(
                                'programStories: $isPreview was not passed by the controller; defaulting to public (snapshot) mode.',
                            );
                        }
                        $snap = $isPreviewMode ? null : $program->published_snapshot ?? null;
                        $snapName = $snap['name'] ?? $program->name;
                        $snapSubtitle = $snap['subtitle'] ?? $program->subtitle;
                        $snapDescription = $snap['description'] ?? $program->description;
                        $snapColor = $snap['color'] ?? $program->color;
                        $snapLogoPath = $snap['logo_path'] ?? $program->logo_path;
                        $snapAcronym = $snap['acronym'] ?? $program->acronym;
                        $snapQualifications = collect(
                            $snap['qualifications'] ?? $program->qualifications->toArray(),
                        )->map(fn($q) => (object) $q);
                        $snapHowToApply = collect($snap['how_to_apply'] ?? $program->howToApply->toArray())->map(
                            fn($s) => (object) $s,
                        );
                        $snapStories = collect($snap['stories'] ?? $program->stories->toArray())->map(
                            fn($s) => (object) $s,
                        );
                        $snapTestimonials = collect($snap['testimonials'] ?? $program->testimonials->toArray())->map(
                            fn($t) => (object) $t,
                        );
                        $snapTestimonialsJson = $snapTestimonials
                            ->map(
                                fn($t) => [
                                    'id' => $t->id ?? null,
                                    'quote' => trim(strip_tags($t->quote ?? '')),
                                    'author_name' => $t->author_name ?? '',
                                    'author_role' => $t->author_role ?? '',
                                ],
                            )
                            ->values()
                            ->toJson();
                    @endphp
                    @php
                        $colorMap = [
                            'red' => [
                                '50' => '#fef2f2',
                                '100' => '#fee2e2',
                                '200' => '#fecaca',
                                '400' => '#f87171',
                                '500' => '#ef4444',
                                '600' => '#dc2626',
                            ],
                            'orange' => [
                                '50' => '#fff7ed',
                                '100' => '#ffedd5',
                                '200' => '#fed7aa',
                                '400' => '#fb923c',
                                '500' => '#f97316',
                                '600' => '#ea580c',
                            ],
                            'yellow' => [
                                '50' => '#fefce8',
                                '100' => '#fef9c3',
                                '200' => '#fef08a',
                                '400' => '#facc15',
                                '500' => '#eab308',
                                '600' => '#ca8a04',
                            ],
                            'green' => [
                                '50' => '#f0fdf4',
                                '100' => '#dcfce7',
                                '200' => '#bbf7d0',
                                '400' => '#4ade80',
                                '500' => '#22c55e',
                                '600' => '#16a34a',
                            ],
                            'cyan' => [
                                '50' => '#ecfeff',
                                '100' => '#cffafe',
                                '200' => '#a5f3fc',
                                '400' => '#22d3ee',
                                '500' => '#06b6d4',
                                '600' => '#0891b2',
                            ],
                            'blue' => [
                                '50' => '#eff6ff',
                                '100' => '#dbeafe',
                                '200' => '#bfdbfe',
                                '400' => '#60a5fa',
                                '500' => '#3b82f6',
                                '600' => '#2563eb',
                            ],
                            'indigo' => [
                                '50' => '#eef2ff',
                                '100' => '#e0e7ff',
                                '200' => '#c7d2fe',
                                '400' => '#818cf8',
                                '500' => '#6366f1',
                                '600' => '#4f46e5',
                            ],
                            'violet' => [
                                '50' => '#f5f3ff',
                                '100' => '#ede9fe',
                                '200' => '#ddd6fe',
                                '400' => '#a78bfa',
                                '500' => '#8b5cf6',
                                '600' => '#7c3aed',
                            ],
                            'purple' => [
                                '50' => '#faf5ff',
                                '100' => '#f3e8ff',
                                '200' => '#e9d5ff',
                                '400' => '#c084fc',
                                '500' => '#a855f7',
                                '600' => '#9333ea',
                            ],
                            'pink' => [
                                '50' => '#fdf2f8',
                                '100' => '#fce7f3',
                                '200' => '#fbcfe8',
                                '400' => '#f472b6',
                                '500' => '#ec4899',
                                '600' => '#db2777',
                            ],
                            'rose' => [
                                '50' => '#fff1f2',
                                '100' => '#ffe4e6',
                                '200' => '#fecdd3',
                                '400' => '#fb7185',
                                '500' => '#f43f5e',
                                '600' => '#e11d48',
                            ],
                            'teal' => [
                                '50' => '#f0fdfa',
                                '100' => '#ccfbf1',
                                '200' => '#99f6e4',
                                '400' => '#2dd4bf',
                                '500' => '#14b8a6',
                                '600' => '#0d9488',
                            ],
                            'sky' => [
                                '50' => '#f0f9ff',
                                '100' => '#e0f2fe',
                                '200' => '#bae6fd',
                                '400' => '#38bdf8',
                                '500' => '#0ea5e9',
                                '600' => '#0284c7',
                            ],
                            'lime' => [
                                '50' => '#f7fee7',
                                '100' => '#ecfccb',
                                '200' => '#d9f99d',
                                '400' => '#a3e635',
                                '500' => '#84cc16',
                                '600' => '#65a30d',
                            ],
                        ];
                        $c = $colorMap[$snapColor] ?? $colorMap['blue'];
                    @endphp

                    <div x-data="{ open: false }">

                        {{-- ACCORDION HEADER --}}
                        <button @click="open = !open"
                            class="w-full px-4 sm:px-6 md:px-10 py-4 sm:py-6 flex items-center justify-between transition-colors duration-200 group text-left"
                            onmouseover="this.style.backgroundColor='{{ $c['50'] }}'"
                            onmouseout="this.style.backgroundColor=''">
                            <div class="flex items-center gap-3 sm:gap-5">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 rounded-2xl flex-shrink-0 flex items-center justify-center shadow-md transition-all duration-300 border-2"
                                    :style="open ? 'background:white; border-color:{{ $c['400'] }}' :
                                        'background:{{ $c['50'] }}; border-color:transparent'">
                                    <img src="{{ asset($snapLogoPath) }}" alt="{{ $snapName }} Logo"
                                        class="w-8 h-8 sm:w-10 sm:h-10 md:w-14 md:h-14 object-contain">
                                </div>
                                <div>
                                    <h3 class="text-base sm:text-xl md:text-2xl font-bold text-slate-900 transition-colors"
                                        :style="open ? 'color:{{ $c['600'] }}' : ''">
                                        {{ $snapName }}
                                    </h3>
                                    <p class="text-xs sm:text-sm md:text-base text-slate-500 mt-0.5 sm:mt-1">
                                        {{ $snapSubtitle }}</p>
                                </div>
                            </div>
                                <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                                <span class="text-xs md:text-sm font-semibold hidden xs:hidden sm:block"
                                    :style="open ? 'color:{{ $c['600'] }}' : 'color:#94a3b8'">
                                    <span x-show="!open">Click to expand</span>
                                    <span x-show="open" x-cloak>Click to collapse</span>
                                </span>
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-colors duration-200"
                                    :style="open ? 'background:{{ $c['600'] }}' : 'background:#f1f5f9'">
                                    <svg class="w-5 h-5 transition-transform duration-300 chevron-icon"
                                        :class="open ? 'open text-white' : 'text-slate-500'" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </button>

                        {{-- ACCORDION BODY --}}
                        <div x-show="open" x-collapse x-cloak>
                            <div class="border-t border-slate-200 p-4 sm:p-6 md:p-8 lg:p-10"
                                style="background: linear-gradient(to bottom right, #f8fafc, {{ $c['50'] }}33)">
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

                                    {{-- LEFT COLUMN --}}
                                    <div class="lg:col-span-2 space-y-6">

                                        {{-- Program Description --}}
                                        <div class="rounded-xl p-6 border"
                                            style="background:{{ $c['50'] }}; border-color:{{ $c['200'] }}">
                                            <h4 class="font-bold text-slate-800 mb-3 flex items-center gap-2 text-lg">
                                                <svg class="w-5 h-5 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                    style="color:{{ $c['600'] }}">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Program Details
                                            </h4>
                                            <div class="text-slate-700 leading-relaxed prose prose-sm max-w-none">
                                                {!! $snapDescription !!}</div>
                                        </div>

                                        @php $groupedQuals = $snapQualifications->groupBy('type'); @endphp

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                            @foreach ($groupedQuals as $type => $items)
                                                <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                                                    <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                        <svg class="w-5 h-5 flex-shrink-0" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24"
                                                            style="color:{{ $c['600'] }}">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        {{ ucfirst($type) }}s
                                                    </h4>
                                                    <ul class="space-y-3 text-slate-700 text-sm">
                                                        @foreach ($items as $q)
                                                            <li class="flex items-start gap-2">
                                                                <span class="font-bold mt-0.5"
                                                                    style="color:{{ $c['500'] }}">•</span>
                                                                <span>{{ html_entity_decode(strip_tags($q->content), ENT_QUOTES | ENT_HTML5, 'UTF-8') }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- How to Apply --}}
                                        <div class="text-white rounded-xl p-6"
                                            style="background:{{ $c['600'] }}">
                                            <h4 class="font-bold mb-4 flex items-center gap-2 text-lg">
                                                <svg class="w-5 h-5 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                How to Apply
                                            </h4>
                                            <ol class="space-y-3 text-sm">
                                                @foreach ($snapHowToApply as $step)
                                                    <li class="flex items-start gap-3">
                                                        <span class="font-bold flex-shrink-0"
                                                            style="color:{{ $c['200'] }}">{{ $loop->iteration }}.</span>
                                                        <span>
                                                            {!! preg_replace_callback(
                                                                '/(https?:\/\/[^\s]+|[a-zA-Z0-9\-]+\.[a-zA-Z]{2,}(?:\/[^\s]*)?(?<![.,]))/',
                                                                function ($m) {
                                                                    $url = $m[0];
                                                                    $href = str_starts_with($url, 'http') ? $url : 'https://' . $url;
                                                                    if (!preg_match('/^https?:\/\//i', $href)) {
                                                                        return e($url);
                                                                    }
                                                                    return '<a href="' .
                                                                        htmlspecialchars($href, ENT_QUOTES, 'UTF-8') .
                                                                        '" target="_blank" class="underline font-semibold hover:opacity-80 transition" style="color:white;">' .
                                                                        e($url) .
                                                                        '</a>';
                                                                },
                                                                e(html_entity_decode(strip_tags($step->content), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                                                            ) !!}

                                                            {{-- ✅ Add this block --}}
                                                            @if (!empty($step->link) && preg_match('/^https?:\/\//i', $step->link))
                                                                <a href="{{ $step->link }}" target="_blank"
                                                                    rel="noopener"
                                                                    class="inline-flex items-center gap-1 underline font-semibold hover:opacity-80 transition ml-1"
                                                                    style="color:white;">
                                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                                    </svg>
                                                                    {{ parse_url($step->link, PHP_URL_HOST) ?? $step->link }}
                                                                </a>
                                                            @endif
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ol>
                                        </div>

                                        {{-- ══ SUCCESS STORIES CAROUSEL ══ --}}
                                        @php $programCarouselId = 'stories-carousel-' . ($program->id ?? ('prog-' . $loop->index)); @endphp
                                        <div x-data="storiesCarousel('{{ $programCarouselId }}', '{{ $c['600'] }}')" x-init="init()"
                                            id="{{ $programCarouselId }}-wrapper">
                                            <div class="flex items-center mb-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-1 h-6 rounded-full flex-shrink-0"
                                                        style="background:{{ $c['600'] }}"></div>
                                                    <h4 class="font-bold text-slate-800">{{ $snapName }} Success
                                                        Stories</h4>
                                                </div>
                                            </div>
                                            <div class="stories-carousel-wrapper">
                                                <div class="story-nav-floating left">
                                                    <button @click="prev()" :disabled="currentPage === 0"
                                                        class="story-nav-btn shadow-md"
                                                        style="border-color:{{ $c['200'] }}; color:{{ $c['600'] }}"
                                                        onmouseover="if(!this.disabled){this.style.background='{{ $c['600'] }}';this.style.color='white';this.style.borderColor='{{ $c['600'] }}'}"
                                                        onmouseout="if(!this.disabled){this.style.background='white';this.style.color='{{ $c['600'] }}';this.style.borderColor='{{ $c['200'] }}'}">
                                                        <svg width="12" height="12" fill="none"
                                                            stroke="currentColor" stroke-width="2.5"
                                                            viewBox="0 0 24 24">
                                                            <path d="M15 19l-7-7 7-7" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="stories-carousel-outer">
                                                    <div class="stories-carousel-track" :id="trackId">
                                                        @foreach ($snapStories as $story)
                                                            @php $safeStoryLink = preg_match('/^https?:\/\//i', $story->link ?? '') ? $story->link : '#'; @endphp
                                                            <a href="{{ $safeStoryLink }}" target="_blank"
                                                                rel="noopener" class="story-card-slide">
                                                                <div class="story-card-img">
                                                                    <img src="{{ asset($story->image_path) }}"
                                                                        alt="{{ $story->title }}" loading="lazy">
                                                                    <span
                                                                        class="absolute bottom-1.5 right-1.5 text-white text-xs font-bold px-1.5 py-0.5 rounded-full"
                                                                        style="background:{{ $c['600'] }}">
                                                                        {{ $snapAcronym ?? $snapName }}
                                                                    </span>
                                                                </div>
                                                                <div class="story-card-body">
                                                                    <p class="story-card-title">{{ $story->title }}
                                                                    </p>
                                                                    <span class="story-card-link"
                                                                        style="color:{{ $c['600'] }}">Read
                                                                        →</span>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="story-nav-floating right">
                                                    <button @click="next()" :disabled="currentPage >= totalPages - 1"
                                                        class="story-nav-btn shadow-md"
                                                        style="border-color:{{ $c['200'] }}; color:{{ $c['600'] }}"
                                                        onmouseover="if(!this.disabled){this.style.background='{{ $c['600'] }}';this.style.color='white';this.style.borderColor='{{ $c['600'] }}'}"
                                                        onmouseout="if(!this.disabled){this.style.background='white';this.style.color='{{ $c['600'] }}';this.style.borderColor='{{ $c['200'] }}'}">
                                                        <svg width="12" height="12" fill="none"
                                                            stroke="currentColor" stroke-width="2.5"
                                                            viewBox="0 0 24 24">
                                                            <path d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-center gap-3 mt-3">
                                                <span class="text-xs font-semibold text-slate-400">
                                                    Page <strong x-text="currentPage + 1"
                                                        style="color:{{ $c['600'] }}"></strong>
                                                    of <strong x-text="totalPages"
                                                        style="color:{{ $c['600'] }}"></strong>
                                                </span>
                                                <div class="flex items-center gap-1.5">
                                                    <template x-for="(_, i) in Array.from({length: totalPages})"
                                                        :key="i">
                                                        <button @click="goTo(i)" class="story-dot"
                                                            :class="i === currentPage ? 'active' : ''"
                                                            :style="i === currentPage ? '--dot-color:{{ $c['600'] }}' : ''"></button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- ══ END SUCCESS STORIES CAROUSEL ══ --}}

                                    </div>
                                    {{-- END LEFT COLUMN --}}

                                    {{-- RIGHT COLUMN: Testimonials --}}
                                    @if ($snapTestimonials->isNotEmpty())
                                        <div class="lg:col-span-1 order-last lg:order-none" x-data="{
                                            page: 1,
                                            perPage: 10,
                                            all: {{ $snapTestimonialsJson }},
                                            get totalPages() { return Math.max(1, Math.ceil(this.all.length / this.perPage)); },
                                            get start() { return (this.page - 1) * this.perPage; },
                                            get end() { return this.page * this.perPage; },
                                            isVisible(i) { return i >= this.start && i < this.end; },
                                            prev() { if (this.page > 1) this.page--; },
                                            next() { if (this.page < this.totalPages) this.page++; },
                                        }">

                                            {{-- Header --}}
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="w-1 h-6 rounded-full flex-shrink-0"
                                                    style="background:{{ $c['600'] }}"></div>
                                                <h4 class="font-bold text-slate-800">
                                                    Testimonials
                                                    <span
                                                        class="text-xs font-normal text-slate-400 ml-1">({{ $snapTestimonials->count() }})</span>
                                                </h4>
                                            </div>

                                            {{-- Testimonial Cards --}}
                                            <div class="testimonials-scroll space-y-3 overflow-y-auto pr-1"
                                                style="height: 830px; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;"
                                                x-ref="testimonialsScroll">
                                                <template x-for="(t, i) in all" :key="i">
                                                    <div x-show="isVisible(i)"
                                                        class="bg-white rounded-xl p-5 shadow-sm border-2"
                                                        style="border-color:{{ $c['200'] }}">
                                                        <div class="flex items-center gap-2 mb-3">
                                                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                                                style="background:{{ $c['600'] }}">
                                                                <svg class="w-4 h-4 text-white" fill="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                                                </svg>
                                                            </div>
                                                            <span
                                                                class="text-xs font-bold text-slate-400 uppercase tracking-wide">Testimonial</span>
                                                        </div>
                                                        <blockquote class="mb-3">
                                                            <p class="text-slate-600 leading-relaxed italic text-sm"
                                                                x-text="'&quot;' + t.quote + '&quot;'"></p>
                                                        </blockquote>
                                                        <div class="pt-3 border-t"
                                                            style="border-color:{{ $c['100'] }}">
                                                            <p class="font-bold text-slate-900 text-sm"
                                                                x-text="t.author_name"></p>
                                                            <p class="text-xs text-slate-500" x-text="t.author_role">
                                                            </p>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>

                                            {{-- Pagination (always rendered, hidden via x-show) --}}
                                            <div x-show="totalPages > 1"
                                                class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100">
                                                <button @click="prev()" :disabled="page === 1"
                                                    class="flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg border transition disabled:opacity-30 disabled:cursor-default bg-white border-slate-200 text-slate-600 hover:bg-slate-50">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                                    </svg>
                                                    Prev
                                                </button>
                                                <span class="text-xs text-slate-400">
                                                    Page <strong x-text="page"
                                                        style="color:{{ $c['600'] }}"></strong>
                                                    of <strong x-text="totalPages"
                                                        style="color:{{ $c['600'] }}"></strong>
                                                </span>
                                                <button @click="next()" :disabled="page === totalPages"
                                                    class="flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg border transition disabled:opacity-30 disabled:cursor-default bg-white border-slate-200 text-slate-600 hover:bg-slate-50">
                                                    Next
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </button>
                                            </div>

                                        </div>
                                    @endif
                                    {{-- END RIGHT COLUMN --}}

                                </div>
                            </div>
                        </div>
                        {{-- END ACCORDION BODY --}}

                    </div>
                @endforeach

            </div>
        </div>
        <!-- ===== END PROGRAMS SECTION ===== -->

        <div class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 py-12 sm:py-20 mt-8 sm:mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
                <h3 class="text-2xl sm:text-4xl font-bold text-white mb-4 sm:mb-6">
                    {{ $ctaSection?->published_title ?? 'Ready to Start Your Journey?' }}
                </h3>
                <p class="text-slate-300 text-base sm:text-xl max-w-3xl mx-auto">
                    {{ $ctaSection?->published_subtitle ?? "Join thousands of youth who have transformed their careers through DOLE's employment programs." }}
                </p>
            </div>
        </div>

    </div>

</body>

</html>