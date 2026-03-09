<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/fuse.js@7.0.0/dist/fuse.min.js"></script>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Programs and Stories - LMI</title>

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
        .stories-carousel-outer { overflow: hidden; border-radius: 12px; position: relative; }
        .stories-carousel-track { display: flex; gap: 12px; transition: transform 0.45s cubic-bezier(0.4,0,0.2,1); will-change: transform; }
        .story-card-slide { flex: 0 0 calc(20% - 10px); background: white; border-radius: 14px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.06); position: relative; transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.3s ease; }
        .story-card-slide:hover { transform: translateY(-8px); box-shadow: 0 16px 36px rgba(0,0,0,0.13), 0 4px 12px rgba(0,0,0,0.07); }
        .story-card-slide:hover .story-card-img img { transform: scale(1.08); }
        .story-card-img { position: relative; height: 112px; overflow: hidden; background: #e2e8f0; }
        .story-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s cubic-bezier(0.4,0,0.2,1); display: block; }
        .story-card-body { padding: 9px 9px 11px; }
        .story-card-title { font-size: 0.72rem; font-weight: 600; color: #1e293b; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 5px; min-height: 2em; }
        .story-card-link { display: inline-flex; align-items: center; gap: 3px; font-size: 0.68rem; font-weight: 700; text-decoration: none; }
        .story-dot { width: 7px; height: 7px; border-radius: 50%; background: #d1d5db; cursor: pointer; transition: all 0.25s ease; border: none; padding: 0; flex-shrink: 0; }
        .story-dot.active { background: var(--dot-color, #6366f1); width: 20px; border-radius: 4px; }
        .story-nav-btn { width: 30px; height: 30px; border-radius: 50%; border: 2px solid; background: white; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; flex-shrink: 0; }
        .story-nav-btn:disabled { opacity: 0.3; cursor: default; pointer-events: none; }
        .stories-carousel-wrapper { position: relative; padding: 0 20px; }
        .story-nav-floating { position: absolute; top: 50%; transform: translateY(-50%); z-index: 10; }
        .story-nav-floating.left { left: -6px; }
        .story-nav-floating.right { right: -6px; }

        /* Testimonials scrollable column */
        .testimonials-scroll::-webkit-scrollbar { width: 4px; }
        .testimonials-scroll::-webkit-scrollbar-track { background: transparent; }
        .testimonials-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .testimonials-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ── Stories carousel: responsive cards per page ── */
        @media (max-width: 480px) {
            .story-card-slide { flex: 0 0 calc(100% - 0px) !important; width: calc(100% - 0px) !important; }
        }
        @media (min-width: 481px) and (max-width: 767px) {
            .story-card-slide { flex: 0 0 calc(50% - 6px) !important; width: calc(50% - 6px) !important; }
        }
        @media (min-width: 768px) and (max-width: 1023px) {
            .story-card-slide { flex: 0 0 calc(33.33% - 8px) !important; width: calc(33.33% - 8px) !important; }
        }

        /* ── Testimonials column: uncap fixed height on mobile ── */
        @media (max-width: 1023px) {
            .testimonials-scroll { height: auto !important; max-height: 500px; }
        }

        /* ── Hero carousel: smaller title/text on mobile ── */
        @media (max-width: 480px) {
            .hero-title  { font-size: 1.75rem !important; line-height: 1.2; }
            .hero-excerpt { font-size: 1rem !important; }
            .hero-cta-btn { padding: 0.75rem 1.5rem !important; font-size: 0.95rem !important; }
        }

        /* ── PESO office type grid: 2 cols on mobile ── */
        @media (max-width: 480px) {
            .office-type-grid { grid-template-columns: repeat(2, 1fr) !important; }
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
            $carouselSlidesJson = $carouselSlides->map(fn($s) => [
                'image'   => asset($s->image_path),
                'title'   => $s->title,
                'excerpt' => strip_tags($s->excerpt ?? ''),
                'link'    => $sanitizeUrl($s->link),
                'program' => $s->program_label,
                'color'   => $s->color,
            ])->toJson();
        @endphp
        <script>
        function publicCarousel(slides) {
            return {
                currentSlide: 0, slides: slides, autoplayInterval: null,
                nextSlide()      { this.currentSlide = (this.currentSlide + 1) % this.slides.length; },
                prevSlide()      { this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length; },
                goToSlide(index) { this.currentSlide = index; },
                startAutoplay()  { this.autoplayInterval = setInterval(() => { this.nextSlide(); }, 5000); },
                stopAutoplay()   { if (this.autoplayInterval) { clearInterval(this.autoplayInterval); } },
            };
        }
        </script>
        <div class="relative w-full h-screen overflow-hidden"
            x-data="publicCarousel({{ $carouselSlidesJson }})"
            x-init="startAutoplay()"
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
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            <p class="text-white text-sm mt-2 font-medium">Scroll to explore programs</p>
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
        <div id="programs-section" class="max-w-7xl mx-auto px-4 sm:px-6 py-16">

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
                            \Log::warning('programStories: $isPreview was not passed by the controller; defaulting to public (snapshot) mode.');
                        }
                        $snap            = $isPreviewMode ? null : ($program->published_snapshot ?? null);
                        $snapName        = $snap["name"]        ?? $program->name;
                        $snapSubtitle    = $snap["subtitle"]    ?? $program->subtitle;
                        $snapDescription = $snap["description"] ?? $program->description;
                        $snapColor       = $snap["color"]       ?? $program->color;
                        $snapLogoPath    = $snap["logo_path"]   ?? $program->logo_path;
                        $snapAcronym     = $snap["acronym"]     ?? $program->acronym;
                        $snapQualifications = collect($snap["qualifications"] ?? $program->qualifications->toArray())->map(fn($q) => (object) $q);
                        $snapHowToApply     = collect($snap["how_to_apply"]   ?? $program->howToApply->toArray())->map(fn($s) => (object) $s);
                        $snapStories        = collect($snap["stories"]        ?? $program->stories->toArray())->map(fn($s) => (object) $s);
                        $snapTestimonials   = collect($snap["testimonials"]   ?? $program->testimonials->toArray())->map(fn($t) => (object) $t);
                        $snapTestimonialsJson = $snapTestimonials->map(fn($t) => [
                            'id'          => $t->id          ?? null,
                            'quote'       => trim(strip_tags($t->quote ?? '')),
                            'author_name' => $t->author_name ?? '',
                            'author_role' => $t->author_role ?? '',
                        ])->values()->toJson();
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
                                    <p class="text-xs sm:text-sm md:text-base text-slate-500 mt-0.5 sm:mt-1">{{ $snapSubtitle }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                                <span class="text-xs md:text-sm font-semibold hidden sm:block"
                                    :style="open ? 'color:{{ $c['600'] }}' : 'color:#94a3b8'">
                                    <span x-show="!open">Click to expand</span>
                                    <span x-show="open" x-cloak>Click to collapse</span>
                                </span>
                                <div class="w-9 h-9 rounded-full flex items-center justify-center transition-colors duration-200"
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
                            <div class="border-t border-slate-200 p-4 sm:p-6 md:p-10"
                                style="background: linear-gradient(to bottom right, #f8fafc, {{ $c['50'] }}33)">
                                <div class="grid lg:grid-cols-3 gap-8">

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

                                        <div class="grid md:grid-cols-2 gap-6">
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
                                                                <span>{{ $q->content }}</span>
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
                                                        <span>{!! preg_replace_callback(
                                                            '/(https?:\/\/[^\s]+|[a-zA-Z0-9\-]+\.[a-zA-Z]{2,}(?:\/[^\s]*)?(?<![.,]))/',
                                                            function ($m) {
                                                                $url = $m[0];
                                                                $href = str_starts_with($url, 'http') ? $url : 'https://' . $url;
                                                                // Only allow http/https — reject javascript: and other schemes
                                                                if (!preg_match('/^https?:\/\//i', $href)) return e($url);
                                                                return '<a href="' .
                                                                    htmlspecialchars($href, ENT_QUOTES, 'UTF-8') .
                                                                    '" target="_blank" class="underline font-semibold hover:opacity-80 transition" style="color:white;">' .
                                                                    e($url) .
                                                                    '</a>';
                                                            },
                                                            e(strip_tags($step->content)),
                                                        ) !!}</span>
                                                    </li>
                                                @endforeach
                                            </ol>
                                        </div>

                                        {{-- ══ SUCCESS STORIES CAROUSEL ══ --}}
                                        @php $programCarouselId = 'stories-carousel-' . ($program->id ?? ('prog-' . $loop->index)); @endphp
                                        <div x-data="storiesCarousel('{{ $programCarouselId }}', '{{ $c['600'] }}')" x-init="init()" id="{{ $programCarouselId }}-wrapper">
                                            <div class="flex items-center mb-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-1 h-6 rounded-full flex-shrink-0" style="background:{{ $c['600'] }}"></div>
                                                    <h4 class="font-bold text-slate-800">{{ $snapName }} Success Stories</h4>
                                                </div>
                                            </div>
                                            <div class="stories-carousel-wrapper">
                                                <div class="story-nav-floating left">
                                                    <button @click="prev()" :disabled="currentPage === 0" class="story-nav-btn shadow-md"
                                                        style="border-color:{{ $c['200'] }}; color:{{ $c['600'] }}"
                                                        onmouseover="if(!this.disabled){this.style.background='{{ $c['600'] }}';this.style.color='white';this.style.borderColor='{{ $c['600'] }}'}"
                                                        onmouseout="if(!this.disabled){this.style.background='white';this.style.color='{{ $c['600'] }}';this.style.borderColor='{{ $c['200'] }}'}">
                                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" /></svg>
                                                    </button>
                                                </div>
                                                <div class="stories-carousel-outer">
                                                    <div class="stories-carousel-track" :id="trackId">
                                                        @foreach ($snapStories as $story)
                                                            @php $safeStoryLink = preg_match('/^https?:\/\//i', $story->link ?? '') ? $story->link : '#'; @endphp
                                                            <a href="{{ $safeStoryLink }}" target="_blank" rel="noopener" class="story-card-slide">
                                                                <div class="story-card-img">
                                                                    <img src="{{ asset($story->image_path) }}" alt="{{ $story->title }}" loading="lazy">
                                                                    <span class="absolute bottom-1.5 right-1.5 text-white text-xs font-bold px-1.5 py-0.5 rounded-full" style="background:{{ $c['600'] }}">
                                                                        {{ $snapAcronym ?? $snapName }}
                                                                    </span>
                                                                </div>
                                                                <div class="story-card-body">
                                                                    <p class="story-card-title">{{ $story->title }}</p>
                                                                    <span class="story-card-link" style="color:{{ $c['600'] }}">Read →</span>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="story-nav-floating right">
                                                    <button @click="next()" :disabled="currentPage >= totalPages - 1" class="story-nav-btn shadow-md"
                                                        style="border-color:{{ $c['200'] }}; color:{{ $c['600'] }}"
                                                        onmouseover="if(!this.disabled){this.style.background='{{ $c['600'] }}';this.style.color='white';this.style.borderColor='{{ $c['600'] }}'}"
                                                        onmouseout="if(!this.disabled){this.style.background='white';this.style.color='{{ $c['600'] }}';this.style.borderColor='{{ $c['200'] }}'}">
                                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" /></svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-center gap-3 mt-3">
                                                <span class="text-xs font-semibold text-slate-400">
                                                    Page <strong x-text="currentPage + 1" style="color:{{ $c['600'] }}"></strong>
                                                    of <strong x-text="totalPages" style="color:{{ $c['600'] }}"></strong>
                                                </span>
                                                <div class="flex items-center gap-1.5">
                                                    <template x-for="(_, i) in Array.from({length: totalPages})" :key="i">
                                                        <button @click="goTo(i)" class="story-dot" :class="i === currentPage ? 'active' : ''" :style="i === currentPage ? '--dot-color:{{ $c['600'] }}' : ''"></button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- ══ END SUCCESS STORIES CAROUSEL ══ --}}

                                    </div>
                                    {{-- END LEFT COLUMN --}}

                                    {{-- RIGHT COLUMN: Testimonials --}}
                                    @if ($snapTestimonials->isNotEmpty())
                                        <div class="lg:col-span-1"
                                            x-data="{
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
                                                <div class="w-1 h-6 rounded-full flex-shrink-0" style="background:{{ $c['600'] }}"></div>
                                                <h4 class="font-bold text-slate-800">
                                                    Testimonials
                                                    <span class="text-xs font-normal text-slate-400 ml-1">({{ $snapTestimonials->count() }})</span>
                                                </h4>
                                            </div>

                                            {{-- Testimonial Cards --}}
                                            <div class="testimonials-scroll space-y-3 overflow-y-auto pr-1" style="height: 830px; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;" x-ref="testimonialsScroll">
                                                <template x-for="(t, i) in all" :key="i">
                                                    <div x-show="isVisible(i)"
                                                        class="bg-white rounded-xl p-5 shadow-sm border-2"
                                                        style="border-color:{{ $c['200'] }}">
                                                        <div class="flex items-center gap-2 mb-3">
                                                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                                                style="background:{{ $c['600'] }}">
                                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                                                </svg>
                                                            </div>
                                                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Testimonial</span>
                                                        </div>
                                                        <blockquote class="mb-3">
                                                            <p class="text-slate-600 leading-relaxed italic text-sm"
                                                                x-text="'&quot;' + t.quote + '&quot;'"></p>
                                                        </blockquote>
                                                        <div class="pt-3 border-t" style="border-color:{{ $c['100'] }}">
                                                            <p class="font-bold text-slate-900 text-sm" x-text="t.author_name"></p>
                                                            <p class="text-xs text-slate-500" x-text="t.author_role"></p>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>

                                            {{-- Pagination (always rendered, hidden via x-show) --}}
                                            <div x-show="totalPages > 1"
                                                class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100">
                                                <button @click="prev()" :disabled="page === 1"
                                                    class="flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg border transition disabled:opacity-30 disabled:cursor-default bg-white border-slate-200 text-slate-600 hover:bg-slate-50">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                                    </svg>
                                                    Prev
                                                </button>
                                                <span class="text-xs text-slate-400">
                                                    Page <strong x-text="page" style="color:{{ $c['600'] }}"></strong>
                                                    of <strong x-text="totalPages" style="color:{{ $c['600'] }}"></strong>
                                                </span>
                                                <button @click="next()" :disabled="page === totalPages"
                                                    class="flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg border transition disabled:opacity-30 disabled:cursor-default bg-white border-slate-200 text-slate-600 hover:bg-slate-50">
                                                    Next
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
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

        <!-- ===== PESO / JPO DIRECTORY SECTION ===== -->
        <div id="peso-section" class="max-w-7xl mx-auto px-4 sm:px-6 py-16">

            {{-- Section Header --}}
            <div class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 rounded-2xl px-5 sm:px-8 py-5 sm:py-7 shadow-2xl mb-2">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-2xl md:text-3xl">PESO / JPO Directory</h2>
                        <p class="text-slate-300 text-sm md:text-base">Click on any program below to view details and offices</p>
                    </div>
                </div>
            </div>

            {{-- Progressive Reveal Directory --}}
            @php
                // ── Public view reads from published snapshot; preview uses live DB ──
                if ($isPreview ?? false) {
                    $pesoProvinces = $fieldOffices->groupBy('province')->map(fn($items) => $items->values());
                    $pesoJson = $pesoProvinces->map(fn($items) => $items->map(fn($o) => [
                        'id'      => $o->id,
                        'name'    => $o->name,
                        'manager' => $o->manager_name ?? '',
                        'email'   => $o->email ?? '',
                        'address' => $o->address ?? '',
                        'type'    => $o->office_type,
                    ]));
                    $pesoProvinceKeys = $pesoProvinces->keys();
                } else {
                    // Read from published snapshot (set by admin Publish Directory action)
                    $snapshot = \Cache::get('field_offices_published_snapshot', []);
                    $pesoJson = collect($snapshot)->map(fn($offices) => collect($offices)->map(fn($o) => [
                        'id'      => $o['id']           ?? null,
                        'name'    => $o['name']         ?? '',
                        'manager' => $o['manager']      ?? $o['manager_name'] ?? '',
                        'email'   => $o['email']        ?? '',
                        'address' => $o['address']      ?? '',
                        'type'    => $o['type']         ?? $o['office_type'] ?? '',
                    ])->values());
                    $pesoProvinceKeys = collect(array_keys($snapshot));
                }
            @endphp
            <script>
            // Inject PHP-serialized PESO data into a scoped JS variable (not window-global)
            const _pesoDataset = @json($pesoJson);

            document.addEventListener('alpine:init', () => {
                Alpine.data('pesoDirectory', () => ({
                    pesoData: _pesoDataset,
                    province: '',
                    officeType: '',
                    showType: false,
                    showResults: false,
                    search: '',
                    _fuseCache: {},
                    _filteredCache: null,
                    _filteredCacheKey: '',
                    typeColors: [
                        { main: '#3b82f6', bg: '#eff6ff', border: '#bfdbfe' },
                        { main: '#f97316', bg: '#fff7ed', border: '#fed7aa' },
                        { main: '#10b981', bg: '#ecfdf5', border: '#a7f3d0' },
                        { main: '#8b5cf6', bg: '#f5f3ff', border: '#ddd6fe' },
                        { main: '#ec4899', bg: '#fdf2f8', border: '#fbcfe8' },
                        { main: '#14b8a6', bg: '#f0fdfa', border: '#99f6e4' },
                        { main: '#f59e0b', bg: '#fffbeb', border: '#fde68a' },
                        { main: '#6366f1', bg: '#eef2ff', border: '#c7d2fe' },
                    ],
                    get officeTypes() {
                        const all = Object.values(this.pesoData ?? {}).flat();
                        return [...new Set(all.map(e => e.type).filter(Boolean))].sort();
                    },
                    typeColor(type, part) {
                        const idx = this.officeTypes.indexOf(type);
                        return (this.typeColors[(idx === -1 ? 0 : idx) % this.typeColors.length])[part];
                    },
                    selectProvince(val) {
                        this.province = val;
                        this.officeType = '';
                        this.showResults = false;
                        this.showType = !!val;
                        this.search = '';
                        this._fuseCache = {};
                        this._filteredCache = null;
                        if (val) this.$nextTick(() => this.$refs.typeSection?.scrollIntoView({ behavior: 'smooth', block: 'nearest' }));
                    },
                    selectType(t) {
                        this.officeType = t;
                        this.showResults = !!t;
                        this.search = '';
                        this._filteredCache = null;
                        if (t) this.$nextTick(() => this.$refs.resultsSection?.scrollIntoView({ behavior: 'smooth', block: 'nearest' }));
                    },
                    countFor(province, type) {
                        const entries = this.pesoData?.[province] ?? [];
                        if (type === 'ALL') return entries.length;
                        return entries.filter(e => e.type === type).length;
                    },
                    filteredEntries() {
                        const cacheKey = this.province + '|' + this.officeType + '|' + this.search;
                        if (this._filteredCache !== null && this._filteredCacheKey === cacheKey) {
                            return this._filteredCache;
                        }
                        let entries = this.pesoData?.[this.province] ?? [];
                        if (this.officeType !== 'ALL') {
                            entries = entries.filter(e => e.type === this.officeType);
                        }
                        let result;
                        if (!this.search.trim()) {
                            result = entries;
                        } else {
                            const fuseCacheKey = this.province + '|' + this.officeType;
                            if (!this._fuseCache[fuseCacheKey] || this._fuseCache[fuseCacheKey]._list !== entries) {
                                this._fuseCache[fuseCacheKey] = new Fuse(entries, {
                                    keys: [
                                        { name: 'name',    weight: 0.6 },
                                        { name: 'manager', weight: 0.3 },
                                        { name: 'type',    weight: 0.1 },
                                    ],
                                    threshold: 0.4, distance: 200, minMatchCharLength: 2, includeScore: true,
                                });
                                this._fuseCache[fuseCacheKey]._list = entries;
                            }
                            result = this._fuseCache[fuseCacheKey].search(this.search.trim()).map(r => r.item);
                        }
                        this._filteredCache = result;
                        this._filteredCacheKey = cacheKey;
                        return result;
                    },
                }));
            });
            </script>
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden"
                x-data="pesoDirectory()">

                <div class="p-4 sm:p-6 md:p-10 space-y-8">

                    {{-- STEP 1: Province --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">
                            1 · Select Province
                        </label>
                        <div class="relative w-full">
                            <select
                                @change="selectProvince($event.target.value)"
                                :value="province"
                                class="w-full appearance-none bg-white border-2 rounded-xl px-4 py-3 pr-10 text-sm font-semibold outline-none transition-all cursor-pointer"
                                :class="province ? 'border-orange-400 shadow-[0_0_0_3px_rgba(251,146,60,0.15)] text-slate-800' : 'border-slate-200 text-slate-400 hover:border-slate-300'">
                                <option value="">— Choose a province —</option>
                                @foreach ($pesoProvinceKeys as $province)
                                    <option value="{{ $province }}">{{ $province }}</option>
                                @endforeach
                            </select>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    {{-- STEP 2: Office Type --}}
                    <div x-ref="typeSection"
                        x-show="showType"
                        x-transition:enter="transition ease-out duration-350"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-cloak>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">
                            2 · Office Type
                        </label>
                        <div class="grid grid-cols-3 gap-3 w-full office-type-grid">
                            {{-- All Offices --}}
                            <button @click="selectType('ALL')" type="button"
                                class="rounded-xl p-4 flex flex-col items-center gap-2 border-2 transition-all duration-200 cursor-pointer text-center"
                                :style="officeType === 'ALL'
                                    ? 'background:#eef2ff; border-color:#6366f1; box-shadow:0 0 0 3px #eef2ff; transform:translateY(-2px);'
                                    : 'background:white; border-color:#e2e8f0;'">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    :style="officeType === 'ALL' ? 'color:#6366f1' : 'color:#94a3b8'">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span class="text-xs font-bold"
                                    :style="officeType === 'ALL' ? 'color:#6366f1' : 'color:#64748b'">All Offices</span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                    :style="officeType === 'ALL'
                                        ? 'background:white; color:#6366f1; border:1px solid #c7d2fe'
                                        : 'background:#f1f5f9; color:#94a3b8; border:1px solid #e2e8f0'"
                                    x-text="countFor(province, 'ALL') + ' offices'"></span>
                            </button>
                            {{-- Dynamic type buttons — derived from snapshot data, auto-color by index --}}
                            <template x-for="t in officeTypes" :key="t">
                                <button @click="selectType(t)" type="button"
                                    class="rounded-xl p-4 flex flex-col items-center gap-2 border-2 transition-all duration-200 cursor-pointer text-center"
                                    :style="officeType === t
                                        ? `background:${typeColor(t,'bg')}; border-color:${typeColor(t,'main')}; box-shadow:0 0 0 3px ${typeColor(t,'bg')}; transform:translateY(-2px);`
                                        : 'background:white; border-color:#e2e8f0;'">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        :style="`color:${officeType === t ? typeColor(t,'main') : '#94a3b8'}`">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs font-bold"
                                        :style="`color:${officeType === t ? typeColor(t,'main') : '#64748b'}`"
                                        x-text="t + ' Only'"></span>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                        :style="officeType === t
                                            ? `background:white; color:${typeColor(t,'main')}; border:1px solid ${typeColor(t,'border')}`
                                            : 'background:#f1f5f9; color:#94a3b8; border:1px solid #e2e8f0'"
                                        x-text="countFor(province, t) + ' offices'"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- STEP 3: Results --}}
                    <div x-ref="resultsSection"
                        x-show="showResults"
                        x-transition:enter="transition ease-out duration-350"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-cloak>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest">3 · Results</label>
                                <p class="text-sm text-slate-500 mt-1">
                                    Showing <strong class="text-orange-500" x-text="filteredEntries().length"></strong>
                                    <span x-text="search.trim() ? 'matches' : 'offices'"></span>
                                    in <strong class="text-slate-800" x-text="province"></strong>
                                </p>
                            </div>
                            <button @click="province=''; officeType=''; showType=false; showResults=false; search='';"
                                class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset
                            </button>
                        </div>

                        {{-- Search bar --}}
                        <div class="relative mb-4">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                </svg>
                            </span>
                            <input type="text" x-model="search" placeholder="Search by office name, manager..."
                                class="w-full border border-slate-200 rounded-xl pl-9 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none transition bg-slate-50 focus:bg-white" />
                            <button x-show="search.trim()" @click="search = ''"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition"
                                x-cloak>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- No results state --}}
                        <div x-show="filteredEntries().length === 0" class="text-center py-10 text-slate-400" x-cloak>
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                            </svg>
                            <p class="text-sm font-semibold">No offices found</p>
                            <p class="text-xs mt-1">Try a different search term</p>
                        </div>

                        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-3 items-start" x-data="{ openId: null }">
                            <template x-for="entry in filteredEntries()" :key="entry.id">
                                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-200">

                                    {{-- Row header --}}
                                    <div class="flex items-center gap-3 px-4 py-3 cursor-pointer select-none"
                                        @click="openId = (openId === entry.id) ? null : entry.id">
                                        <div class="w-9 h-9 rounded-lg flex-shrink-0 flex items-center justify-center text-white font-bold text-sm"
                                            :style="`background:${typeColor(entry.type,'main')}`"
                                            x-text="entry.name.charAt(0)">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-slate-800 truncate" style="text-transform: capitalize;" x-text="entry.name.toLowerCase()"></p>
                                            <p class="text-xs text-slate-400 truncate" style="text-transform: capitalize;" x-text="(entry.manager || '—').toLowerCase()"></p>
                                        </div>
                                        <span class="inline-flex items-center text-xs font-bold px-2 py-0.5 rounded flex-shrink-0"
                                            :style="`background:${typeColor(entry.type,'bg')}; color:${typeColor(entry.type,'main')}; border:1px solid ${typeColor(entry.type,'border')}`"
                                            x-text="entry.type">
                                        </span>
                                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform duration-200"
                                            :class="openId === entry.id ? 'rotate-180' : ''"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>

                                    {{-- Dropdown details --}}
                                    <div x-show="openId === entry.id"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 -translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 -translate-y-1"
                                        class="border-t border-slate-100 px-4 py-3 flex flex-col gap-2 bg-slate-50"
                                        x-cloak>
                                        <template x-if="entry.email">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                <a :href="'mailto:' + entry.email" class="text-xs text-blue-500 hover:underline truncate" x-text="entry.email"></a>
                                            </div>
                                        </template>
                                        <template x-if="entry.address">
                                            <div class="flex items-start gap-2">
                                                <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="text-xs text-slate-500 leading-relaxed" style="text-transform: capitalize;" x-text="entry.address.toLowerCase()"></span>
                                            </div>
                                        </template>
                                    </div>

                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Idle hint --}}
                    <div x-show="!province" class="text-center py-8 text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="text-sm font-medium">Select a province above to browse offices</p>
                    </div>

                </div>
            </div>
        </div>
        <!-- ===== END PESO / JPO DIRECTORY SECTION ===== -->

        <div class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 py-12 sm:py-20 mt-16">
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

    <script>
        function storiesCarousel(wrapperId, accentColor) {
            return {
                wrapperId,
                accentColor,
                trackId: wrapperId + '-track',
                currentPage: 0,
                totalPages: 1,
                PER_PAGE: 5,
                _resizeHandler: null,

                init() {
                    this.$nextTick(() => {
                        this.recalc();
                        this._resizeHandler = () => this.recalc();
                        window.addEventListener('resize', this._resizeHandler);

                        const wrapper = document.getElementById(this.wrapperId + '-wrapper');
                        if (wrapper) {
                            let _wheelLocked = false;
                            wrapper.addEventListener('wheel', (e) => {
                                if (this.totalPages <= 1) return;
                                const isScrollingDown = e.deltaY > 0;
                                const atStart = this.currentPage === 0;
                                const atEnd = this.currentPage >= this.totalPages - 1;
                                if ((isScrollingDown && atEnd) || (!isScrollingDown && atStart)) return;
                                e.preventDefault();
                                if (_wheelLocked) return;
                                _wheelLocked = true;
                                setTimeout(() => { _wheelLocked = false; }, 500);
                                if (isScrollingDown) { this.next(); } else { this.prev(); }
                            }, { passive: false });
                        }
                    });
                },

                destroy() {
                    if (this._resizeHandler) {
                        window.removeEventListener('resize', this._resizeHandler);
                        this._resizeHandler = null;
                    }
                },

                recalc() {
                    const track = document.getElementById(this.trackId);
                    if (!track) return;

                    // Responsive cards per page
                    const w = window.innerWidth;
                    this.PER_PAGE = w < 481 ? 1 : w < 768 ? 2 : w < 1024 ? 3 : 5;

                    const cards = track.querySelectorAll('.story-card-slide');
                    const total = cards.length;
                    this.totalPages = Math.max(1, Math.ceil(total / this.PER_PAGE));
                    if (this.currentPage >= this.totalPages) {
                        this.currentPage = this.totalPages - 1;
                    }
                    const outerWidth = track.parentElement.offsetWidth;
                    const gap = 12;
                    const cardWidth = (outerWidth - gap * (this.PER_PAGE - 1)) / this.PER_PAGE;
                    cards.forEach(card => {
                        card.style.flex = `0 0 ${cardWidth}px`;
                        card.style.width = `${cardWidth}px`;
                    });
                    this.slide();
                },

                slide() {
                    const track = document.getElementById(this.trackId);
                    if (!track) return;
                    const outerWidth = track.parentElement.offsetWidth;
                    const gap = 12;
                    const cardWidth = (outerWidth - gap * (this.PER_PAGE - 1)) / this.PER_PAGE;
                    const pageWidth = this.PER_PAGE * cardWidth + (this.PER_PAGE - 1) * gap + gap;
                    track.style.transform = `translateX(-${this.currentPage * pageWidth}px)`;
                },

                prev() {
                    if (this.currentPage > 0) { this.currentPage--; this.slide(); }
                },
                next() {
                    if (this.currentPage < this.totalPages - 1) { this.currentPage++; this.slide(); }
                },
                goTo(page) {
                    this.currentPage = page; this.slide();
                },
            };
        }
    </script>
</body>

</html>