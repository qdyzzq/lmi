<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    @vite('resources/css/app.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Quill.js Rich Text Editor -->

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/fuse.js@7.0.0/dist/fuse.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.15.8/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>
    <title>Employment Programs</title>

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

        .plus-btn {
            transition: all 0.2s ease;
        }

        .plus-btn:hover {
            transform: scale(1.08);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.25);
        }

        .modal-backdrop {
            backdrop-filter: blur(4px);
        }

        .add-zone {
            border: 2px dashed #c7d2fe;
            transition: all 0.2s;
        }

        .add-zone:hover {
            border-color: #6366f1;
            background: #eef2ff;
        }

        .program-row .admin-actions {
            opacity: 0;
            transition: opacity 0.15s ease;
        }

        .program-row:hover .admin-actions {
            opacity: 1;
        }

        .program-row:hover .program-name {
            color: var(--program-color);
        }

        /* Quill */
        .ql-toolbar.ql-snow {
            padding: 12px 8px;
            border-radius: 8px 8px 0 0;
        }

        .ql-toolbar.ql-snow .ql-formats {
            margin-right: 20px;
        }

        .ql-toolbar.ql-snow button {
            width: 32px !important;
            height: 32px !important;
            padding: 4px;
        }

        .ql-toolbar.ql-snow .ql-stroke {
            stroke-width: 2.5;
        }

        .ql-toolbar.ql-snow select {
            height: 32px !important;
            padding: 4px 8px;
        }

        .ql-container.ql-snow {
            border-radius: 0 0 8px 8px;
        }

        .ql-editor {
            min-height: 180px;
            font-size: 14px;
            line-height: 1.6;
        }

        .qual-content p {
            margin: 0;
            padding: 0;
        }

        .qual-content p+p {
            margin-top: 0.25rem;
        }

        .qual-content p:empty {
            display: none;
        }

        .qual-content ul {
            list-style: disc;
            padding-left: 1.25rem;
        }

        .qual-content ol {
            list-style: decimal;
            padding-left: 1.25rem;
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

        .story-card-admin {
            position: absolute;
            top: 5px;
            right: 5px;
            display: flex;
            gap: 3px;
            opacity: 0;
            transition: opacity 0.2s ease;
            z-index: 10;
        }

        .story-card-slide:hover .story-card-admin {
            opacity: 1;
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

        .story-add-slot {
            flex: 0 0 calc(20% - 10px);
            min-height: 175px;
            border: 2px dashed #c7d2fe;
            border-radius: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 7px;
            color: #818cf8;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            background: white;
            transition: all 0.2s ease;
        }

        .story-add-slot:hover {
            border-color: #6366f1;
            background: #eef2ff;
            color: #6366f1;
            transform: translateY(-4px);
        }

        .stories-carousel-wrapper {
            position: relative;
            padding: 0 20px;
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

        /* Testimonials scroll container */
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
    </style>
</head>

<body class="bg-slate-100 min-h-screen" x-data="{ ...adminPage(), previewOpen: false, previewSrc: '' }">

    {{-- ===== OUTER LAYOUT WRAPPER ===== --}}
    <div class="flex h-screen overflow-hidden">

        {{-- ===== SIDEBAR ===== --}}
        @include('partials.sidebar')

        {{-- ===== MAIN CONTENT AREA ===== --}}
        <div class="flex-1 flex flex-col overflow-y-auto" x-ref="mainContent">

            {{-- ===== ADMIN TOP BAR ===== --}}
            <header
                class="bg-white h-16 shrink-0 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm sticky top-0 z-50">
                <h2 class="text-xl font-bold text-slate-800">Programs &amp; Stories Editor • Admin</h2>
                <div class="flex items-center gap-3">
                    {{-- FIX #6: previewSrc is locked to a safe constant — never derived from user input --}}
                    <button @click="previewOpen = true; previewSrc = '/admin/programs-stories/preview'"
                        class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:border-indigo-300 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Preview Draft
                    </button>
                </div>
            </header>

            {{-- ===== INLINE PREVIEW PANEL ===== --}}
            {{-- Backdrop --}}
            <div x-show="previewOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="previewOpen = false"
                class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[60]" x-cloak>
            </div>

            {{-- Panel --}}
            <div x-show="previewOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
                class="fixed top-0 right-0 h-screen z-[70] flex flex-col shadow-2xl" x-init="$el.style.left = ($refs.mainContent?.offsetLeft ?? 256) + 'px'" x-cloak>

                {{-- Panel header --}}
                <div class="flex items-center justify-between px-5 py-3 shrink-0 bg-white border-b border-slate-200">
                    <span class="text-sm font-semibold text-slate-700">Draft Preview</span>
                    <div class="flex items-center gap-2">
                        <button @click="$refs.previewFrame.src = $refs.previewFrame.src" title="Refresh preview"
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Refresh
                        </button>
                        <button @click="previewOpen = false"
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Close
                        </button>
                    </div>
                </div>

                {{-- iframe --}}
                <iframe x-ref="previewFrame" :src="previewOpen ? previewSrc : ''"
                    class="flex-1 w-full bg-white border-0">
                </iframe>
            </div>
            {{-- ===== END INLINE PREVIEW PANEL ===== --}}

            @php
                $programColorMap = [];
                foreach ($programs as $p) {
                    $programColorMap[$p->name] = $p->color;
                    if ($p->acronym) {
                        $programColorMap[$p->acronym] = $p->color;
                    }
                }
            @endphp

            {{-- ===== CAROUSEL SECTION ===== --}}
            @php
                $slidesJson = $carouselSlides
                    ->map(
                        fn($s) => [
                            'image' => asset($s->image_path),
                            'title' => $s->title,
                            'excerpt' => html_entity_decode(strip_tags($s->excerpt), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                            'link' => $s->link,
                            'program' => $s->program_label,
                            'color' => $s->color,
                            'id' => $s->id,
                        ],
                    )
                    ->toJson();
            @endphp
            <script>
                function carouselSection(slides) {
                    return {
                        currentSlide: 0,
                        slides: slides,
                        autoplayInterval: null,
                        nextSlide() {
                            this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                        },
                        prevSlide() {
                            this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
                        },
                        goToSlide(index) {
                            this.currentSlide = index;
                        },
                        startAutoplay() {
                            this.autoplayInterval = setInterval(() => this.nextSlide(), 5000);
                        },
                        stopAutoplay() {
                            clearInterval(this.autoplayInterval);
                        },
                    };
                }
            </script>
            <div id="carousel-section" class="relative w-full shrink-0 overflow-hidden"
                style="height: calc(100vh - 64px);" x-data="carouselSection({{ $slidesJson }})" x-init="startAutoplay()"
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
                                class="flex flex-col items-center justify-center text-center text-white max-w-5xl h-full py-20">
                                <div class="flex-grow flex flex-col justify-center mb-8">
                                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4 drop-shadow-2xl leading-tight line-clamp-2"
                                        x-text="slide.title"></h1>
                                    <p class="text-lg md:text-xl lg:text-2xl text-slate-50 drop-shadow-lg max-w-4xl mx-auto leading-relaxed font-light line-clamp-3"
                                        x-text="slide.excerpt"></p>
                                </div>
                                <div class="flex-shrink-0 pb-12">
                                    <div
                                        class="inline-flex items-center gap-3 px-10 py-5 bg-white text-slate-900 font-bold text-lg rounded-xl opacity-80 cursor-default">
                                        <span>READ FULL STORY</span>
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-6 right-6 z-30 flex gap-2">
                            <button
                                @click="$dispatch('open-modal', { type: 'edit-slide', id: slide.id, data: { ...slide, image_url: slide.image, image: null } })"
                                class="flex items-center gap-1.5 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Slide
                            </button>
                            <button @click="$dispatch('open-modal', { type: 'delete-slide', id: slide.id })"
                                class="flex items-center gap-1.5 px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </template>

                <button @click="prevSlide()"
                    class="absolute left-6 top-1/2 -translate-y-1/2 z-20 w-14 h-14 bg-white/25 hover:bg-white/40 backdrop-blur-md rounded-full flex items-center justify-center transition-all duration-300 border border-white/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button @click="nextSlide()"
                    class="absolute right-6 top-1/2 -translate-y-1/2 z-20 w-14 h-14 bg-white/25 hover:bg-white/40 backdrop-blur-md rounded-full flex items-center justify-center transition-all duration-300 border border-white/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div class="absolute bottom-24 left-1/2 -translate-x-1/2 z-20 flex items-center gap-4">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button @click="goToSlide(index)" class="transition-all duration-300"
                            :class="currentSlide === index ? 'w-16 h-4' : 'w-4 h-4'">
                            <div class="w-full h-full rounded-full backdrop-blur-md border-2"
                                :class="currentSlide === index ? 'bg-white border-white' : 'bg-white/40 border-white/60'">
                            </div>
                        </button>
                    </template>
                </div>

                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20">
                    <button @click="$dispatch('open-modal', { type: 'add-slide' })"
                        class="plus-btn flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm rounded-full shadow-2xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Carousel Slide
                    </button>
                </div>
            </div>
            {{-- ===== END CAROUSEL ===== --}}

            {{-- ===== PROGRAMS SECTION ===== --}}
            <div id="programs-section" class="w-full py-16" style="padding-left: 7.5rem; padding-right: 7.5rem;">

                <div
                    class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 rounded-2xl px-8 py-7 shadow-2xl mb-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-white font-bold text-2xl md:text-3xl">DOLE Employment Programs</h2>
                                <p class="text-slate-300 text-sm md:text-base">Click on any program below to view
                                    details and eligibility</p>
                            </div>
                        </div>
                        <button @click="$dispatch('open-modal', { type: 'add-program' })"
                            class="plus-btn flex items-center gap-2 px-5 py-2.5 bg-indigo-500 hover:bg-indigo-400 text-white font-bold text-sm rounded-xl shadow-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Program
                        </button>
                    </div>
                </div>

                <div id="programs-ajax-container" x-data="{ openId: null }"
                    class="bg-white rounded-2xl shadow-2xl border border-slate-200 divide-y divide-slate-200 overflow-hidden">

                    @foreach ($programs as $program)
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
                            $c = $colorMap[$program->color] ?? $colorMap['blue'];
                            $programCarouselId = 'stories-carousel-' . $program->id;
                            $editProgramData = json_encode([
                                'name' => $program->name,
                                'acronym' => $program->acronym,
                                'subtitle' => $program->subtitle,
                                'description' => $program->description,
                                'color' => $program->color,
                            ]);
                            $testimonialsJson = ($program->testimonials ?? collect())
                                ->map(
                                    fn($t) => [
                                        'id' => $t->id,
                                        'quote' => trim(preg_replace('/<\/?p[^>]*>/', ' ', $t->quote)),
                                        'author_name' => $t->author_name,
                                        'author_role' => $t->author_role,
                                    ],
                                )
                                ->toJson();
                        @endphp

                        <div id="program-card-{{ $program->id }}" x-data="{ id: {{ $program->id }} }">
                            <div class="relative program-row" style="--program-color: {{ $c['600'] }}">
                                <button @click="openId = (openId === id) ? null : id"
                                    class="w-full px-6 md:px-10 py-6 flex items-center justify-between transition-colors duration-200 group text-left"
                                    onmouseover="this.style.backgroundColor='{{ $c['50'] }}'"
                                    onmouseout="this.style.backgroundColor=''">
                                    <div class="flex items-center gap-5">
                                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl flex-shrink-0 flex items-center justify-center shadow-md transition-all duration-300 border-2"
                                            :style="openId === id ? 'background:white; border-color:{{ $c['400'] }}' :
                                                'background:{{ $c['50'] }}; border-color:transparent'">
                                            <img src="{{ asset($program->logo_path) }}"
                                                alt="{{ $program->name }} Logo"
                                                class="w-10 h-10 md:w-14 md:h-14 object-contain">
                                        </div>
                                        <div>
                                            <h3 class="text-xl md:text-2xl font-bold text-slate-900">
                                                <span
                                                    class="program-name transition-colors duration-200">{{ $program->name }}</span>
                                            </h3>
                                            <p class="text-sm md:text-base text-slate-500 mt-1">
                                                {{ $program->subtitle }}</p>
                                            <div class="flex items-center gap-2 mt-1.5">
                                                <span
                                                    class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full {{ $program->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                    <span
                                                        class="w-1.5 h-1.5 rounded-full {{ $program->is_published ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                                    {{ $program->is_published ? 'Published' : 'Draft' }}
                                                </span>
                                                @if ($program->is_published && $program->has_draft_changes)
                                                    <span
                                                        class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-orange-100 text-orange-600 border border-orange-200">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                                        </svg>
                                                        Unpublished changes
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                                        <span class="text-xs md:text-sm font-semibold hidden sm:block"
                                            :style="openId === id ? 'color:{{ $c['600'] }}' : 'color:#94a3b8'">
                                            <span x-show="openId !== id">Click to expand</span>
                                            <span x-show="openId === id" x-cloak>Click to collapse</span>
                                        </span>
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center transition-colors duration-200"
                                            :style="openId === id ? 'background:{{ $c['600'] }}' : 'background:#f1f5f9'">
                                            <svg class="w-5 h-5 transition-transform duration-300 chevron-icon"
                                                :class="openId === id ? 'open text-white' : 'text-slate-500'"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </button>

                                {{-- Edit / Delete --}}
                                <div class="admin-actions absolute bottom-2 right-6 md:right-10 flex items-center gap-1.5 z-10"
                                    @click.stop>
                                    <button
                                        @click="$dispatch('open-modal', { type: 'edit-program', id: {{ $program->id }}, data: {{ $editProgramData }} })"
                                        class="flex items-center gap-1 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white border border-indigo-200 hover:border-indigo-600 rounded-lg text-xs font-semibold transition shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </button>
                                    <button
                                        @click="$dispatch('open-modal', { type: 'delete-program', id: {{ $program->id }} })"
                                        class="flex items-center gap-1 px-2.5 py-1 bg-red-50 hover:bg-red-600 text-red-500 hover:text-white border border-red-200 hover:border-red-600 rounded-lg text-xs font-semibold transition shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>

                            {{-- ACCORDION BODY --}}
                            <div x-show="openId === id" x-collapse x-cloak>
                                <div id="program-body-{{ $program->id }}"
                                    class="border-t border-slate-200 p-6 md:p-10"
                                    style="background: linear-gradient(to bottom right, #f8fafc, {{ $c['50'] }}33)">
                                    <div class="grid lg:grid-cols-3 gap-8">

                                        <div class="lg:col-span-2 space-y-6">

                                            {{-- Description --}}
                                            <div class="rounded-xl p-6 relative group/card border"
                                                style="background:{{ $c['50'] }}; border-color:{{ $c['200'] }}">
                                                <h4
                                                    class="font-bold text-slate-800 mb-3 flex items-center gap-2 text-lg">
                                                    <svg class="w-5 h-5 flex-shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24"
                                                        style="color:{{ $c['600'] }}">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Program Details
                                                </h4>
                                                {{-- FIX #1: Sanitize HTML output — allow only safe formatting tags --}}
                                                <div class="text-slate-700 leading-relaxed prose prose-sm max-w-none">
                                                    {!! strip_tags($program->description, '<b><strong><em><i><u><ul><ol><li><p><br><a><span><h1><h2><h3><h4>') !!}</div>
                                                <span
                                                    class="absolute top-3 right-3 flex gap-1 opacity-0 group-hover/card:opacity-100 transition-opacity">
                                                    <button
                                                        @click="$dispatch('open-modal', { type: 'edit-description', id: {{ $program->id }}, data: { description: {{ Js::from($program->description) }} } })"
                                                        class="w-5 h-5 bg-indigo-100 hover:bg-indigo-500 text-indigo-500 hover:text-white rounded flex items-center justify-center transition">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button
                                                        @click="$dispatch('open-modal', { type: 'delete-item', id: {{ $program->id }}, programId: {{ $program->id }}, endpoint: '/admin/programs/{{ $program->id }}/description' })"
                                                        class="w-5 h-5 bg-red-100 hover:bg-red-500 text-red-500 hover:text-white rounded flex items-center justify-center transition">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </span>
                                            </div>

                                            {{-- Qualifications --}}
                                            @php $groupedQuals = $program->qualifications->groupBy('type'); @endphp
                                            <div class="grid md:grid-cols-2 gap-6">
                                                @foreach ($groupedQuals as $type => $items)
                                                    <div
                                                        class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm group/catcard">
                                                        <div class="flex items-center justify-between mb-4">
                                                            <h4
                                                                class="font-bold text-slate-800 flex items-center gap-2">
                                                                <svg class="w-5 h-5 flex-shrink-0" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                                    style="color:{{ $c['600'] }}">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                {{ ucfirst($type) }}s
                                                            </h4>
                                                            <button
                                                                @click="$dispatch('open-modal', { type: 'delete-item', id: {{ $items->first()->id }}, programId: {{ $program->id }}, endpoint: '/admin/qualifications/type/{{ urlencode($type) }}/program/{{ $program->id }}' })"
                                                                class="opacity-0 group-hover/catcard:opacity-100 transition w-7 h-7 bg-red-50 hover:bg-red-500 text-red-400 hover:text-white border border-red-200 rounded-lg flex items-center justify-center shadow-sm"
                                                                title="Delete entire {{ ucfirst($type) }} category">
                                                                <svg class="w-3.5 h-3.5" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <ul class="space-y-3 text-slate-700 text-sm text-justify">
                                                            @foreach ($items as $q)
                                                                <li class="flex items-start gap-2 group/item">
                                                                    <span class="font-bold mt-0.5"
                                                                        style="color:{{ $c['500'] }}">•</span>
                                                                    {{-- FIX #1: Sanitize HTML output — allow only safe formatting tags --}}
                                                                    <span
                                                                        class="flex-1 qual-content">{!! strip_tags($q->content, '<b><strong><em><i><u><ul><ol><li><p><br><a><span>') !!}</span>
                                                                    <span
                                                                        class="flex gap-1 opacity-0 group-hover/item:opacity-100 transition flex-shrink-0">
                                                                        <button
                                                                            @click="$dispatch('open-modal', { type: 'edit-qualification', id: {{ $q->id }}, programId: {{ $program->id }}, data: { type: '{{ e($q->type) }}', content: {{ Js::from($q->content) }} } })"
                                                                            class="w-5 h-5 bg-indigo-100 hover:bg-indigo-500 text-indigo-500 hover:text-white rounded flex items-center justify-center transition">
                                                                            <svg class="w-3 h-3" fill="none"
                                                                                stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                            </svg>
                                                                        </button>
                                                                        <button
                                                                            @click="$dispatch('open-modal', { type: 'delete-item', id: {{ $q->id }}, programId: {{ $program->id }}, endpoint: '/admin/qualifications/{{ $q->id }}' })"
                                                                            class="w-5 h-5 bg-red-100 hover:bg-red-500 text-red-500 hover:text-white rounded flex items-center justify-center transition">
                                                                            <svg class="w-3 h-3" fill="none"
                                                                                stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M6 18L18 6M6 6l12 12" />
                                                                            </svg>
                                                                        </button>
                                                                    </span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach

                                                <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                                                    <div class="flex items-center justify-between mb-4">
                                                        <h4 class="font-bold text-slate-800 flex items-center gap-2">
                                                            <svg class="w-5 h-5 text-slate-400 flex-shrink-0"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                            Add New Category
                                                        </h4>
                                                        <button
                                                            @click="$dispatch('open-modal', { type: 'add-qualification', programId: {{ $program->id }}, data: {} })"
                                                            class="plus-btn w-7 h-7 bg-indigo-100 hover:bg-indigo-600 text-indigo-600 hover:text-white rounded-full flex items-center justify-center shadow transition">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <p class="text-xs text-slate-400">Add a qualification, requirement,
                                                        beneficiary, service, or objective.</p>
                                                </div>
                                            </div>

                                            {{-- How to Apply --}}
                                            <div class="text-white rounded-xl p-6"
                                                style="background:{{ $c['600'] }}">
                                                <div class="flex items-center justify-between mb-4">
                                                    <h4 class="font-bold flex items-center gap-2 text-lg">
                                                        <svg class="w-5 h-5 flex-shrink-0" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        How to Apply
                                                    </h4>
                                                    <button
                                                        @click="$dispatch('open-modal', { type: 'add-step', programId: {{ $program->id }} })"
                                                        class="plus-btn flex items-center gap-1 px-3 py-1.5 bg-white/20 hover:bg-white/40 text-white text-xs font-bold rounded-lg transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                        Add Step
                                                    </button>
                                                </div>
                                                <ol class="space-y-3 text-sm">
                                                    @foreach ($program->howToApply as $step)
                                                        <li class="flex items-start gap-3 group/item">
                                                            <span class="font-bold flex-shrink-0"
                                                                style="color:{{ $c['200'] }}">{{ $loop->iteration }}.</span>

                                                            @php
                                                                $stepContent = strip_tags(
                                                                    $step->content,
                                                                    '<b><strong><em><i><u><ul><ol><li><br><a><span>',
                                                                );
                                                                $stepContent = preg_replace(
                                                                    '/^<p>(.*)<\/p>$/s',
                                                                    '$1',
                                                                    trim($stepContent),
                                                                );
                                                            @endphp

                                                            <span class="flex-1">
                                                                @if ($step->link)
                                                                    {!! preg_replace('/(<\/?\s*(p|div|br)\s*\/?>)/i', ' ', $stepContent) !!}
                                                                    <a href="{{ $step->link }}" target="_blank"
                                                                        class="inline-flex items-center gap-1 underline font-semibold hover:opacity-80 transition ml-1"
                                                                        style="color:white;">
                                                                        <svg class="w-3 h-3 flex-shrink-0"
                                                                            fill="none" stroke="currentColor"
                                                                            viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                                        </svg>
                                                                        {{ parse_url($step->link, PHP_URL_HOST) ?? $step->link }}
                                                                    </a>
                                                                @else
                                                                    {!! $stepContent !!}
                                                                @endif
                                                            </span>
                                                            <span
                                                                class="flex gap-1 opacity-0 group-hover/item:opacity-100 transition flex-shrink-0">
                                                                <button
                                                                    @click="$dispatch('open-modal', { type: 'edit-step', id: {{ $step->id }}, programId: {{ $program->id }}, data: { content: {{ Js::from($step->content) }}, link: {{ Js::from($step->link) }} } })"
                                                                    class="w-5 h-5 bg-white/20 hover:bg-white text-white rounded flex items-center justify-center transition"
                                                                    onmouseover="this.style.color='{{ $c['600'] }}'"
                                                                    onmouseout="this.style.color='white'">
                                                                    <svg class="w-3 h-3" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                    </svg>
                                                                </button>
                                                                <button
                                                                    @click="$dispatch('open-modal', { type: 'delete-item', id: {{ $step->id }}, programId: {{ $program->id }}, endpoint: '/admin/steps/{{ $step->id }}' })"
                                                                    class="w-5 h-5 bg-white/20 hover:bg-red-500 text-white rounded flex items-center justify-center transition">
                                                                    <svg class="w-3 h-3" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M6 18L18 6M6 6l12 12" />
                                                                    </svg>
                                                                </button>
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            </div>

                                            {{-- ══ SUCCESS STORIES CAROUSEL ══ --}}
                                            <div x-data="storiesCarousel('{{ $programCarouselId }}', '{{ $c['600'] }}', {{ $program->id }})" x-init="init()"
                                                id="{{ $programCarouselId }}-wrapper">
                                                <div class="flex items-center justify-between mb-3 gap-3 flex-wrap">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-1 h-6 rounded-full flex-shrink-0"
                                                            style="background:{{ $c['600'] }}"></div>
                                                        <h4 class="font-bold text-slate-800">
                                                            Success Stories</h4>
                                                    </div>
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        {{-- Year range filter --}}
                                                        <div class="flex items-center gap-1"
                                                            x-show="availableYears.length > 0" x-cloak>
                                                            <div class="relative">
                                                                <select x-model="yearFrom" @change="filterByYear()"
                                                                    class="text-xs font-semibold border rounded-lg pl-3 pr-7 py-1.5 outline-none appearance-none cursor-pointer transition"
                                                                    style="border-color:{{ $c['200'] }}; color:{{ $c['700'] ?? $c['600'] }}; background:white;">
                                                                    <option value="">From</option>
                                                                    <template x-for="yr in availableYears"
                                                                        :key="yr">
                                                                        <option :value="yr" x-text="yr"
                                                                            :disabled="yearTo !== '' && yr > parseInt(
                                                                                yearTo)">
                                                                        </option>
                                                                    </template>
                                                                </select>
                                                                <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24"
                                                                    style="color:{{ $c['600'] }}">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2.5"
                                                                        d="M19 9l-7 7-7-7" />
                                                                </svg>
                                                            </div>
                                                            <span class="text-xs font-semibold px-0.5"
                                                                style="color:{{ $c['400'] ?? '#94a3b8' }}">–</span>
                                                            <div class="relative">
                                                                <select x-model="yearTo" @change="filterByYear()"
                                                                    class="text-xs font-semibold border rounded-lg pl-3 pr-7 py-1.5 outline-none appearance-none cursor-pointer transition"
                                                                    style="border-color:{{ $c['200'] }}; color:{{ $c['700'] ?? $c['600'] }}; background:white;">
                                                                    <option value="">To</option>
                                                                    <template x-for="yr in availableYears"
                                                                        :key="yr">
                                                                        <option :value="yr" x-text="yr"
                                                                            :disabled="yearFrom !== '' && yr < parseInt(
                                                                                yearFrom)">
                                                                        </option>
                                                                    </template>
                                                                </select>
                                                                <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24"
                                                                    style="color:{{ $c['600'] }}">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2.5"
                                                                        d="M19 9l-7 7-7-7" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        {{-- Clear filter button --}}
                                                        <button x-show="yearFrom || yearTo" x-cloak
                                                            @click="clearFilter()"
                                                            class="text-xs font-semibold px-2.5 py-1.5 rounded-lg border transition"
                                                            style="border-color:{{ $c['200'] }}; color:{{ $c['600'] }};"
                                                            onmouseover="this.style.background='{{ $c['50'] ?? '#f5f3ff' }}'"
                                                            onmouseout="this.style.background='white'">
                                                            ✕ Clear
                                                        </button>
                                                        {{-- Export CSV button --}}
                                                        <button @click="exportStories()"
                                                            class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg border transition"
                                                            style="border-color:{{ $c['200'] }}; color:{{ $c['600'] }}; background:white;"
                                                            onmouseover="this.style.background='{{ $c['600'] }}';this.style.color='white';this.style.borderColor='{{ $c['600'] }}'"
                                                            onmouseout="this.style.background='white';this.style.color='{{ $c['600'] }}';this.style.borderColor='{{ $c['200'] }}'">
                                                            <svg class="w-3.5 h-3.5" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            Export CSV
                                                        </button>
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
                                                            @foreach ($program->stories as $story)
                                                                <div class="story-card-slide"
                                                                    data-story-id="{{ $story->id }}"
                                                                    data-story-year="{{ $story->story_year ?? '' }}"
                                                                    data-story-title="{{ $story->title }}"
                                                                    data-story-link="{{ $story->link ?? '' }}"
                                                                    data-program-name="{{ $program->name }}">
                                                                    <div class="story-card-img">
                                                                        <img src="{{ asset($story->image_path) }}"
                                                                            alt="{{ $story->title }}" loading="lazy">
                                                                        <span
                                                                            class="absolute bottom-1.5 right-1.5 text-white text-xs font-bold px-1.5 py-0.5 rounded-full"
                                                                            style="background:{{ $c['600'] }}">
                                                                            {{ $program->acronym ?? $program->name }}
                                                                        </span>
                                                                        <div class="story-card-admin">
                                                                            <button
                                                                                @click.stop="$dispatch('open-modal', { type: 'edit-story', id: {{ $story->id }}, programId: {{ $program->id }}, data: { title: {{ Js::from($story->title) }}, link: {{ Js::from($story->link) }}, story_year: {{ $story->story_year ?? 'null' }} } })"
                                                                                class="w-6 h-6 bg-indigo-600 text-white rounded flex items-center justify-center shadow hover:bg-indigo-700 transition">
                                                                                <svg class="w-3 h-3" fill="none"
                                                                                    stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        stroke-width="2"
                                                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                                </svg>
                                                                            </button>
                                                                            <button
                                                                                @click.stop="$dispatch('open-modal', { type: 'delete-item', id: {{ $story->id }}, programId: {{ $program->id }}, endpoint: '/admin/stories/{{ $story->id }}' })"
                                                                                class="w-6 h-6 bg-red-600 text-white rounded flex items-center justify-center shadow hover:bg-red-700 transition">
                                                                                <svg class="w-3 h-3" fill="none"
                                                                                    stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        stroke-width="2"
                                                                                        d="M6 18L18 6M6 6l12 12" />
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="story-card-body">
                                                                        <p class="story-card-title">
                                                                            {{ $story->title }}</p>
                                                                        @if ($story->story_year)
                                                                            <span
                                                                                class="inline-block text-xs font-bold px-1.5 py-0.5 rounded-full mb-1"
                                                                                style="background:{{ $c['100'] ?? '#e0e7ff' }}; color:{{ $c['600'] }}">
                                                                                {{ $story->story_year }}
                                                                            </span>
                                                                        @endif
                                                                        @if ($story->link)
                                                                            <a href="{{ $story->link }}"
                                                                                target="_blank" rel="noopener"
                                                                                class="story-card-link"
                                                                                style="color:{{ $c['600'] }}"
                                                                                @click.stop>Read →</a>
                                                                        @else
                                                                            <span
                                                                                class="text-xs font-medium mt-1 block opacity-40 italic"
                                                                                style="color:{{ $c['600'] }}">No
                                                                                link set</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            <button
                                                                @click="$dispatch('open-modal', { type: 'add-story', programId: {{ $program->id }} })"
                                                                class="story-add-slot">
                                                                <svg class="w-7 h-7" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M12 4v16m8-8H4" />
                                                                </svg>
                                                                <span>Add Story</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="story-nav-floating right">
                                                        <button @click="next()"
                                                            :disabled="currentPage >= totalPages - 1"
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
                                                                :style="i === currentPage ?
                                                                    '--dot-color:{{ $c['600'] }}' :
                                                                    ''"></button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- ══ END SUCCESS STORIES CAROUSEL ══ --}}

                                        </div>{{-- end lg:col-span-2 --}}

                                        {{-- RIGHT: Testimonials (multi, paginated) --}}
                                        <div class="lg:col-span-1" x-data="{
                                            page: 1,
                                            perPage: 10,
                                            testimonials: {{ $testimonialsJson }},
                                            get paginated() { return this.testimonials.slice((this.page - 1) * this.perPage, this.page * this.perPage); },
                                            get totalPages() { return Math.max(1, Math.ceil(this.testimonials.length / this.perPage)); },
                                        }">

                                            {{-- Header --}}
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-1 h-6 rounded-full flex-shrink-0"
                                                        style="background:{{ $c['600'] }}"></div>
                                                    <h4 class="font-bold text-slate-800">Testimonials
                                                        <span class="text-xs font-normal text-slate-400 ml-1"
                                                            x-text="'(' + testimonials.length + ')'"></span>
                                                    </h4>
                                                </div>
                                                <button
                                                    @click="$dispatch('open-modal', { type: 'add-testimonial', programId: {{ $program->id }} })"
                                                    class="plus-btn flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg shadow-sm transition text-white"
                                                    style="background:{{ $c['600'] }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Add
                                                </button>
                                            </div>

                                            {{-- Testimonial Cards --}}
                                            <div class="testimonials-scroll space-y-3 overflow-y-auto pr-1"
                                                style="height: 830px; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
                                                <template x-for="t in paginated" :key="t.id">
                                                    <div class="bg-white rounded-xl p-4 shadow-sm border-2 relative group/testimonial"
                                                        style="border-color:{{ $c['200'] }}">
                                                        {{-- Quote icon --}}
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
                                                            <p class="text-slate-600 leading-relaxed italic text-sm line-clamp-3"
                                                                x-text="`\u201c` + t.quote + `\u201d`"></p>
                                                        </blockquote>
                                                        <div class="pt-3 border-t"
                                                            style="border-color:{{ $c['100'] }}">
                                                            <p class="font-bold text-slate-900 text-sm"
                                                                x-text="t.author_name"></p>
                                                            <p class="text-xs text-slate-500" x-text="t.author_role">
                                                            </p>
                                                        </div>
                                                        {{-- Edit / Delete --}}
                                                        <span
                                                            class="absolute top-2.5 right-2.5 flex gap-1 opacity-0 group-hover/testimonial:opacity-100 transition">
                                                            <button
                                                                @click="$dispatch('open-modal', { type: 'edit-testimonial', id: t.id, programId: {{ $program->id }}, data: { quote: t.quote, author_name: t.author_name, author_role: t.author_role } })"
                                                                class="w-6 h-6 bg-indigo-50 hover:bg-indigo-500 text-indigo-500 hover:text-white border border-indigo-200 rounded-lg flex items-center justify-center transition shadow-sm">
                                                                <svg class="w-3 h-3" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                            </button>
                                                            <button
                                                                @click="$dispatch('open-modal', { type: 'delete-item', id: t.id, programId: {{ $program->id }}, endpoint: '/admin/testimonials/' + t.id })"
                                                                class="w-6 h-6 bg-red-50 hover:bg-red-500 text-red-400 hover:text-white border border-red-200 rounded-lg flex items-center justify-center transition shadow-sm">
                                                                <svg class="w-3 h-3" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </template>

                                                {{-- Empty state --}}
                                                <template x-if="testimonials.length === 0">
                                                    <button
                                                        @click="$dispatch('open-modal', { type: 'add-testimonial', programId: {{ $program->id }} })"
                                                        class="add-zone w-full rounded-xl p-6 flex flex-col items-center justify-center gap-3 text-indigo-400 hover:text-indigo-600 min-h-[160px] transition">
                                                        <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                        <span class="text-sm font-semibold">Add First
                                                            Testimonial</span>
                                                    </button>
                                                </template>
                                            </div>

                                            {{-- Pagination --}}
                                            <template x-if="totalPages > 1">
                                                <div
                                                    class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100">
                                                    <button @click="page = Math.max(1, page - 1)"
                                                        :disabled="page === 1"
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
                                                    <button @click="page = Math.min(totalPages, page + 1)"
                                                        :disabled="page === totalPages"
                                                        class="flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg border transition disabled:opacity-30 disabled:cursor-default bg-white border-slate-200 text-slate-600 hover:bg-slate-50">
                                                        Next
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2.5" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>

                                        </div>

                                    </div>

                                    {{-- ===== PUBLISH FOOTER BAR ===== --}}
                                    <div class="mt-8 pt-6 border-t border-slate-200 space-y-3">

                                        {{-- Unpublished changes warning --}}
                                        @if ($program->is_published && $program->has_draft_changes)
                                            <div
                                                class="flex items-center gap-3 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl">
                                                <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                                </svg>
                                                <div class="flex-1">
                                                    <p class="text-sm font-semibold text-amber-800">You have
                                                        unpublished changes</p>
                                                    <p class="text-xs text-amber-600 mt-0.5">The public page still
                                                        shows the last published version. Click
                                                        <strong>Republish</strong> to push your changes live.
                                                    </p>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-2.5">
                                                <span
                                                    class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ $program->is_published ? 'bg-emerald-500' : 'bg-amber-400' }}"></span>
                                                <p
                                                    class="text-sm font-semibold {{ $program->is_published ? 'text-emerald-700' : 'text-amber-700' }}">
                                                    @if ($program->is_published && $program->has_draft_changes)
                                                        Live — but has unpublished draft changes.
                                                    @elseif ($program->is_published)
                                                        This program is live — visible to the public.
                                                    @else
                                                        This program is a draft — not visible to the public yet.
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0">

                                                {{-- Republish button — only shows when published but has changes --}}
                                                @if ($program->is_published && $program->has_draft_changes)
                                                    @php
                                                        $snap = $program->published_snapshot ?? [];
                                                        $changes = [];

                                                        // Name / subtitle / color / logo
                                                        if (($snap['name'] ?? '') !== $program->name) {
                                                            $changes[] = [
                                                                'icon' => 'tag',
                                                                'text' => 'Program name updated',
                                                            ];
                                                        }
                                                        if (($snap['acronym'] ?? '') !== ($program->acronym ?? '')) {
                                                            $changes[] = ['icon' => 'tag', 'text' => 'Acronym updated'];
                                                        }
                                                        if (($snap['subtitle'] ?? '') !== $program->subtitle) {
                                                            $changes[] = [
                                                                'icon' => 'text',
                                                                'text' => 'Subtitle updated',
                                                            ];
                                                        }
                                                        if (($snap['description'] ?? '') !== $program->description) {
                                                            $changes[] = [
                                                                'icon' => 'doc',
                                                                'text' => 'Description updated',
                                                            ];
                                                        }
                                                        if (($snap['color'] ?? '') !== $program->color) {
                                                            $changes[] = [
                                                                'icon' => 'color',
                                                                'text' => 'Theme color changed',
                                                            ];
                                                        }
                                                        if (($snap['logo_path'] ?? null) !== $program->logo_path) {
                                                            $changes[] = ['icon' => 'image', 'text' => 'Logo updated'];
                                                        }

                                                        // Qualifications
                                                        $snapQualCount = count($snap['qualifications'] ?? []);
                                                        $liveQualCount = $program->qualifications->count();
                                                        if ($liveQualCount > $snapQualCount) {
                                                            $changes[] = [
                                                                'icon' => 'plus',
                                                                'text' =>
                                                                    $liveQualCount -
                                                                    $snapQualCount .
                                                                    ' qualification(s) added',
                                                            ];
                                                        } elseif ($liveQualCount < $snapQualCount) {
                                                            $changes[] = [
                                                                'icon' => 'minus',
                                                                'text' =>
                                                                    $snapQualCount -
                                                                    $liveQualCount .
                                                                    ' qualification(s) removed',
                                                            ];
                                                        } elseif ($snapQualCount > 0) {
                                                            $snapQualJson = collect($snap['qualifications'])
                                                                ->sortBy('id')
                                                                ->values()
                                                                ->toJson();
                                                            $liveQualJson = $program->qualifications
                                                                ->map(
                                                                    fn($q) => [
                                                                        'id' => $q->id,
                                                                        'type' => $q->type,
                                                                        'content' => $q->content,
                                                                    ],
                                                                )
                                                                ->sortBy('id')
                                                                ->values()
                                                                ->toJson();
                                                            if ($snapQualJson !== $liveQualJson) {
                                                                $changes[] = [
                                                                    'icon' => 'edit',
                                                                    'text' => 'Qualifications edited',
                                                                ];
                                                            }
                                                        }

                                                        // Steps
                                                        $snapStepCount = count($snap['how_to_apply'] ?? []);
                                                        $liveStepCount = $program->howToApply->count();
                                                        if ($liveStepCount > $snapStepCount) {
                                                            $changes[] = [
                                                                'icon' => 'plus',
                                                                'text' =>
                                                                    $liveStepCount - $snapStepCount . ' step(s) added',
                                                            ];
                                                        } elseif ($liveStepCount < $snapStepCount) {
                                                            $changes[] = [
                                                                'icon' => 'minus',
                                                                'text' =>
                                                                    $snapStepCount -
                                                                    $liveStepCount .
                                                                    ' step(s) removed',
                                                            ];
                                                        } elseif ($snapStepCount > 0) {
                                                            $snapSteps = collect($snap['how_to_apply'])
                                                                ->map(
                                                                    fn($s) => [
                                                                        'id' => $s['id'],
                                                                        'content' => $s['content'],
                                                                        'link' => $s['link'] ?? null,
                                                                    ],
                                                                )
                                                                ->keyBy('id');
                                                            $liveSteps = $program->howToApply
                                                                ->map(
                                                                    fn($s) => [
                                                                        'id' => $s->id,
                                                                        'content' => $s->content,
                                                                        'link' => $s->link,
                                                                    ],
                                                                )
                                                                ->keyBy('id');
                                                            $editedCount = $liveSteps
                                                                ->filter(
                                                                    fn($s, $id) => isset($snapSteps[$id]) &&
                                                                        ($snapSteps[$id]['content'] !== $s['content'] ||
                                                                            ($snapSteps[$id]['link'] ?? null) !==
                                                                                $s['link']),
                                                                )
                                                                ->count();
                                                            if ($editedCount > 0) {
                                                                $changes[] = [
                                                                    'icon' => 'edit',
                                                                    'text' =>
                                                                        $editedCount . ' How to Apply step(s) edited',
                                                                ];
                                                            }
                                                        }

                                                        // Stories
                                                        $snapStoryCount = count($snap['stories'] ?? []);
                                                        $liveStoryCount = $program->stories->count();
                                                        if ($liveStoryCount > $snapStoryCount) {
                                                            $changes[] = [
                                                                'icon' => 'plus',
                                                                'text' =>
                                                                    $liveStoryCount -
                                                                    $snapStoryCount .
                                                                    ' story/stories added',
                                                            ];
                                                        } elseif ($liveStoryCount < $snapStoryCount) {
                                                            $changes[] = [
                                                                'icon' => 'minus',
                                                                'text' =>
                                                                    $snapStoryCount -
                                                                    $liveStoryCount .
                                                                    ' story/stories removed',
                                                            ];
                                                        } elseif ($snapStoryCount > 0) {
                                                            $snapStories = collect($snap['stories'])
                                                                ->map(
                                                                    fn($s) => [
                                                                        'id' => $s['id'],
                                                                        'title' => $s['title'],
                                                                        'link' => $s['link'] ?? null,
                                                                    ],
                                                                )
                                                                ->keyBy('id');
                                                            $liveStories = $program->stories
                                                                ->map(
                                                                    fn($s) => [
                                                                        'id' => $s->id,
                                                                        'title' => $s->title,
                                                                        'link' => $s->link,
                                                                    ],
                                                                )
                                                                ->keyBy('id');
                                                            $editedCount = $liveStories
                                                                ->filter(
                                                                    fn($s, $id) => isset($snapStories[$id]) &&
                                                                        ($snapStories[$id]['title'] !== $s['title'] ||
                                                                            ($snapStories[$id]['link'] ?? null) !==
                                                                                $s['link']),
                                                                )
                                                                ->count();
                                                            if ($editedCount > 0) {
                                                                $changes[] = [
                                                                    'icon' => 'edit',
                                                                    'text' =>
                                                                        $editedCount . ' success story/stories edited',
                                                                ];
                                                            }
                                                        }

                                                        // Testimonials
                                                        $snapTestCount = count($snap['testimonials'] ?? []);
                                                        $liveTestCount = $program->testimonials->count();
                                                        if ($liveTestCount > $snapTestCount) {
                                                            $changes[] = [
                                                                'icon' => 'plus',
                                                                'text' =>
                                                                    $liveTestCount -
                                                                    $snapTestCount .
                                                                    ' testimonial(s) added',
                                                            ];
                                                        } elseif ($liveTestCount < $snapTestCount) {
                                                            $changes[] = [
                                                                'icon' => 'minus',
                                                                'text' =>
                                                                    $snapTestCount -
                                                                    $liveTestCount .
                                                                    ' testimonial(s) removed',
                                                            ];
                                                        } elseif ($snapTestCount > 0) {
                                                            $snapTests = collect($snap['testimonials'])
                                                                ->map(
                                                                    fn($t) => [
                                                                        'id' => $t['id'],
                                                                        'quote' => $t['quote'],
                                                                        'author_name' => $t['author_name'],
                                                                        'author_role' => $t['author_role'] ?? null,
                                                                    ],
                                                                )
                                                                ->keyBy('id');
                                                            $liveTests = $program->testimonials
                                                                ->map(
                                                                    fn($t) => [
                                                                        'id' => $t->id,
                                                                        'quote' => $t->quote,
                                                                        'author_name' => $t->author_name,
                                                                        'author_role' => $t->author_role,
                                                                    ],
                                                                )
                                                                ->keyBy('id');
                                                            $editedCount = $liveTests
                                                                ->filter(
                                                                    fn($t, $id) => isset($snapTests[$id]) &&
                                                                        ($snapTests[$id]['quote'] !== $t['quote'] ||
                                                                            $snapTests[$id]['author_name'] !==
                                                                                $t['author_name'] ||
                                                                            ($snapTests[$id]['author_role'] ?? null) !==
                                                                                $t['author_role']),
                                                                )
                                                                ->count();
                                                            if ($editedCount > 0) {
                                                                $changes[] = [
                                                                    'icon' => 'edit',
                                                                    'text' => $editedCount . ' testimonial(s) edited',
                                                                ];
                                                            }
                                                        }

                                                        if (empty($changes)) {
                                                            $changes[] = [
                                                                'icon' => 'edit',
                                                                'text' => 'Content updated',
                                                            ];
                                                        }
                                                    @endphp
                                                    <button
                                                        @click="$dispatch('open-modal', {
                                                    type: 'republish-program',
                                                    id: {{ $program->id }},
                                                    programName: {{ json_encode($program->name) }},
                                                    changes: {{ json_encode($changes) }}
                                                })"
                                                        class="flex-shrink-0 flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold text-sm transition shadow-sm bg-amber-500 hover:bg-amber-600 text-white">
                                                        <svg class="w-4 h-4 flex-shrink-0" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                        Republish Changes
                                                    </button>
                                                @endif

                                                {{-- Publish / Unpublish button --}}
                                                <button
                                                    @click="$dispatch('open-modal', {
                                                type: '{{ $program->is_published ? 'unpublish-program' : 'publish-program' }}',
                                                id: {{ $program->id }},
                                                programName: {{ json_encode($program->name) }}
                                            })"
                                                    class="flex-shrink-0 flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold text-sm transition shadow-sm
                                                {{ $program->is_published ? 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-300' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                                                    @if ($program->is_published)
                                                        <svg class="w-4 h-4 flex-shrink-0" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                        </svg>
                                                        Unpublish Program
                                                    @else
                                                        <svg class="w-4 h-4 flex-shrink-0" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        ✓ Done — Publish to Public
                                                    @endif
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- ===== END PUBLISH FOOTER BAR ===== --}}

                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button @click="$dispatch('open-modal', { type: 'add-program' })"
                        class="add-zone w-full px-10 py-8 flex items-center justify-center gap-3 text-indigo-400 hover:text-indigo-600 transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="font-semibold text-base">Add New Program</span>
                    </button>
                </div>
            </div>

            {{-- CTA SECTION --}}
            @php
                $ctaIsPublished = !is_null($ctaSection?->published_at);
                $ctaHasDraft =
                    $ctaSection && $ctaSection->published_at
                        ? $ctaSection->updated_at->gt($ctaSection->published_at)
                        : (bool) $ctaSection;
            @endphp
            <div id="cta-section-root"
                class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 py-20 mt-16 relative group/cta"
                x-data="{
                    ctaTitle: '{{ addslashes($ctaSection->title ?? 'Ready to Start Your Journey?') }}',
                    ctaSubtitle: '{{ addslashes($ctaSection->subtitle ?? "Join thousands of youth who have transformed their careers through DOLE\'s employment programs.") }}',
                    ctaHasDraft: {{ $ctaHasDraft ? 'true' : 'false' }},
                    ctaIsPublished: {{ $ctaIsPublished ? 'true' : 'false' }},
                    ctaPublishing: false
                }"
                @cta-updated.window="ctaTitle = $event.detail.title; ctaSubtitle = $event.detail.subtitle; ctaHasDraft = true">

                <div class="w-full mx-auto px-6 text-center">
                    <h3 class="text-4xl font-bold text-white mb-6" x-text="ctaTitle"></h3>
                    <p class="text-slate-300 text-xl max-w-3xl mx-auto" x-text="ctaSubtitle"></p>
                </div>

                {{-- Top-right buttons: Edit + Publish — appear on hover --}}
                <div
                    class="absolute top-4 right-4 flex items-center gap-2 opacity-0 group-hover/cta:opacity-100 transition-opacity duration-200">

                    {{-- Edit CTA --}}
                    <button
                        @click="$dispatch('open-modal', { type: 'edit-cta', data: { title: ctaTitle, subtitle: ctaSubtitle } })"
                        class="plus-btn flex items-center gap-1.5 px-3 py-2 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-lg shadow transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit CTA
                    </button>

                    {{-- Publish / Update Published --}}
                    <button @click="publishCta()" :disabled="ctaPublishing || (!ctaHasDraft && ctaIsPublished)"
                        :class="ctaHasDraft
                            ?
                            'bg-amber-500 hover:bg-amber-600 text-white' :
                            (ctaIsPublished ? 'bg-white/10 text-white/40 cursor-not-allowed' :
                                'bg-emerald-600 hover:bg-emerald-700 text-white')"
                        class="plus-btn flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg shadow transition disabled:cursor-not-allowed">
                        {{-- Spinner --}}
                        <svg x-show="ctaPublishing" class="w-3.5 h-3.5 animate-spin flex-shrink-0" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                        </svg>
                        {{-- Republish icon --}}
                        <svg x-show="!ctaPublishing && ctaHasDraft" class="w-3.5 h-3.5 flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{-- First publish icon --}}
                        <svg x-show="!ctaPublishing && !ctaHasDraft && !ctaIsPublished"
                            class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{-- Already published checkmark --}}
                        <svg x-show="!ctaPublishing && !ctaHasDraft && ctaIsPublished"
                            class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        <span x-show="ctaPublishing">Publishing…</span>
                        <span x-show="!ctaPublishing && ctaHasDraft" x-cloak>Update Published</span>
                        <span x-show="!ctaPublishing && !ctaHasDraft && !ctaIsPublished" x-cloak>Publish to
                            Public</span>
                        <span x-show="!ctaPublishing && !ctaHasDraft && ctaIsPublished" x-cloak>Published</span>
                    </button>
                </div>
            </div>

            {{-- ===== MODALS ===== --}}
            <div x-show="modal.open" x-cloak
                class="fixed inset-0 z-[999] flex items-center justify-center modal-backdrop bg-slate-900/60 p-4"
                @keydown.escape.window="modal.open = false" @open-modal.window="openModal($event.detail)">

                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto" @click.stop>

                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-lg" x-text="modal.title"></h3>
                        <button @click="modal.open = false"
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-6">

                        {{-- ADD / EDIT SLIDE --}}
                        <div x-show="modal.type === 'add-slide' || modal.type === 'edit-slide'" x-cloak
                            class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Story Title</label>
                                <input type="text" x-model="form.title"
                                    :class="formErrors.title ? 'border-red-500 ring-2 ring-red-200' :
                                        'border-slate-300 focus:ring-indigo-400'"
                                    @input="formErrors.title = false"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                <p x-show="formErrors.title" class="mt-1 text-xs text-red-500 font-semibold" x-cloak>
                                    This field is required.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Short Excerpt</label>
                                <div id="quill-excerpt" class="rounded-lg border border-slate-300"></div>
                                <div class="mt-1 text-xs text-slate-400"><span id="quill-excerpt-wordcount">0</span>
                                    words
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Program
                                        Label</label>
                                    <input type="text" x-model="form.program_label"
                                        list="slide-program-label-options" placeholder="e.g. GIP"
                                        @input="syncProgramColor(form, $event.target.value, {{ json_encode($programColorMap) }})"
                                        @change="syncProgramColor(form, $event.target.value, {{ json_encode($programColorMap) }})"
                                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                                    <datalist id="slide-program-label-options">
                                        @foreach ($programs as $p)
                                            <option value="{{ $p->name }}">{{ $p->name }}</option>
                                            @if ($p->acronym)
                                                <option value="{{ $p->acronym }}">{{ $p->acronym }}</option>
                                            @endif
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Full Story Link</label>
                                <input type="url" x-model="form.link" placeholder="https://..."
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">
                                    Slide Image
                                    <span x-show="modal.type === 'edit-slide'"
                                        class="text-slate-400 font-normal">(leave blank
                                        to keep current)</span>
                                </label>
                                <div x-show="form.image_preview || (modal.type === 'edit-slide' && form.image_url)"
                                    class="mb-2">
                                    <img :src="form.image_preview || form.image_url"
                                        class="w-full rounded-lg border border-slate-200 object-contain max-h-72"
                                        alt="Slide image preview">
                                    <p class="text-xs text-slate-400 mt-1"
                                        x-text="form.image_preview ? 'New image preview' : 'Current image — choose a file below to replace it'">
                                    </p>
                                </div>
                                <input type="file" accept="image/*"
                                    @change="
                                const file = $event.target.files[0];
                                if (file && file.size > 5 * 1024 * 1024) {
                                    showToast('Image must be under 5MB.', 'warning');
                                    $event.target.value = '';
                                    return;
                                }
                                form.image = file;
                                formErrors.image = false;
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = function(e) { form.image_preview = e.target.result; };
                                    reader.readAsDataURL(file);
                                }
                            "
                                    :class="formErrors.image ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-300'"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm" />
                                <p x-show="formErrors.image" class="mt-1 text-xs text-red-500 font-semibold" x-cloak>
                                    Please upload an image.</p>
                            </div>
                            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
                                <button type="button" @click="modal.open = false"
                                    class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="submitSlide()" :disabled="modal.loading"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                    <span x-show="!modal.loading">Save</span>
                                    <span x-show="modal.loading" x-cloak>Saving…</span>
                                </button>
                            </div>
                        </div>

                        {{-- ADD / EDIT PROGRAM --}}
                        <div x-show="modal.type === 'add-program' || modal.type === 'edit-program'" x-cloak
                            class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Program Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" x-model="form.name"
                                    :class="formErrors.name ? 'border-red-500 ring-2 ring-red-200' :
                                        'border-slate-300 focus:ring-indigo-400'"
                                    @input="formErrors.name = false"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                <p x-show="formErrors.name" class="mt-1 text-xs text-red-500 font-semibold" x-cloak>
                                    Program name is required.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">
                                    Acronym <span class="text-slate-400 font-normal">(optional)</span>
                                </label>
                                <input type="text" x-model="form.acronym" placeholder="e.g. GIP"
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                                <p class="text-xs text-slate-400 mt-1">Shown on story cards instead of the full
                                    program name.
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">
                                    Subtitle <span class="text-slate-400 font-normal">(optional)</span>
                                </label>
                                <input type="text" x-model="form.subtitle"
                                    placeholder="e.g. 3–6 month internship in government"
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Program
                                    Description</label>
                                <div id="quill-program" class="rounded-lg border border-slate-300"></div>
                                <div class="mt-1 text-xs text-slate-400"><span id="quill-program-wordcount">0</span>
                                    words
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Theme Color <span
                                        class="text-red-500">*</span></label>
                                <div :class="formErrors.color ? 'rounded-xl ring-2 ring-red-300 p-2 bg-red-50' : ''"
                                    class="grid grid-cols-7 gap-2">
                                    <template
                                        x-for="c in [
                                    { name: 'red',    bg: 'bg-red-500',    ring: 'ring-red-500',    label: 'Red' },
                                    { name: 'orange', bg: 'bg-orange-500', ring: 'ring-orange-500', label: 'Orange' },
                                    { name: 'yellow', bg: 'bg-yellow-400', ring: 'ring-yellow-400', label: 'Yellow' },
                                    { name: 'green',  bg: 'bg-green-500',  ring: 'ring-green-500',  label: 'Green' },
                                    { name: 'cyan',   bg: 'bg-cyan-500',   ring: 'ring-cyan-500',   label: 'Cyan' },
                                    { name: 'blue',   bg: 'bg-blue-500',   ring: 'ring-blue-500',   label: 'Blue' },
                                    { name: 'indigo', bg: 'bg-indigo-500', ring: 'ring-indigo-500', label: 'Indigo' },
                                    { name: 'violet', bg: 'bg-violet-500', ring: 'ring-violet-500', label: 'Violet' },
                                    { name: 'purple', bg: 'bg-purple-500', ring: 'ring-purple-500', label: 'Purple' },
                                    { name: 'pink',   bg: 'bg-pink-500',   ring: 'ring-pink-500',   label: 'Pink' },
                                    { name: 'rose',   bg: 'bg-rose-500',   ring: 'ring-rose-500',   label: 'Rose' },
                                    { name: 'teal',   bg: 'bg-teal-500',   ring: 'ring-teal-500',   label: 'Teal' },
                                    { name: 'sky',    bg: 'bg-sky-500',    ring: 'ring-sky-500',    label: 'Sky' },
                                    { name: 'lime',   bg: 'bg-lime-500',   ring: 'ring-lime-500',   label: 'Lime' },
                                ]"
                                        :key="c.name">
                                        <button type="button" @click="form.color = c.name; formErrors.color = false"
                                            :title="c.label"
                                            class="relative w-8 h-8 rounded-full transition-all duration-150 focus:outline-none hover:scale-110"
                                            :class="[c.bg, form.color === c.name ? 'ring-2 ring-offset-2 scale-110 shadow-lg ' +
                                                c
                                                .ring : 'opacity-70 hover:opacity-100'
                                            ]">
                                            <svg x-show="form.color === c.name"
                                                class="w-4 h-4 text-white absolute inset-0 m-auto" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                                <p x-show="formErrors.color" class="mt-1 text-xs text-red-500 font-semibold" x-cloak>
                                    Please select a theme color.</p>
                                <p x-show="!formErrors.color" class="text-xs text-slate-400 mt-2">Selected: <span
                                        class="font-semibold text-slate-600 capitalize"
                                        x-text="form.color || '—'"></span></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">
                                    Logo <span x-show="modal.type === 'edit-program'"
                                        class="text-slate-400 font-normal">(leave blank to keep current)</span>
                                </label>
                                <input type="file" accept="image/*"
                                    @change="
                                const file = $event.target.files[0];
                                if (file && file.size > 5 * 1024 * 1024) {
                                    showToast('Logo must be under 5MB.', 'warning');
                                    $event.target.value = '';
                                    return;
                                }
                                form.logo = file;
                            "
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm" />
                            </div>
                            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
                                <button type="button" @click="modal.open = false"
                                    class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="submitProgram()" :disabled="modal.loading"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                    <span x-show="!modal.loading">Save as Draft</span>
                                    <span x-show="modal.loading" x-cloak>Saving…</span>
                                </button>
                            </div>
                        </div>

                        {{-- EDIT DESCRIPTION --}}
                        <div x-show="modal.type === 'edit-description'" x-cloak class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Program
                                    Description</label>
                                <div id="quill-description" class="rounded-lg border border-slate-300"></div>
                                <div class="mt-1 text-xs text-slate-400"><span
                                        id="quill-description-wordcount">0</span> words
                                </div>
                            </div>
                            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
                                <button type="button" @click="modal.open = false"
                                    class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="submitDescription()" :disabled="modal.loading"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                    <span x-show="!modal.loading">Save</span>
                                    <span x-show="modal.loading" x-cloak>Saving…</span>
                                </button>
                            </div>
                        </div>

                        {{-- ADD / EDIT QUALIFICATION --}}
                        <div x-show="modal.type === 'add-qualification' || modal.type === 'edit-qualification'" x-cloak
                            class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Type</label>
                                <input type="text" x-model="form.type" list="qualification-types"
                                    placeholder="Select or type a new type..."
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                                <datalist id="qualification-types">
                                    @foreach ($qualificationTypes as $type)
                                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Content <span
                                        class="text-red-500">*</span></label>
                                <div id="quill-qualification"
                                    :class="formErrors.content ? 'rounded-lg border-2 border-red-500' :
                                        'rounded-lg border border-slate-300'">
                                </div>
                                <p x-show="formErrors.content" class="mt-1 text-xs text-red-500 font-semibold"
                                    x-cloak>Content cannot be empty.</p>
                                <div class="mt-1 text-xs text-slate-400"><span
                                        id="quill-qualification-wordcount">0</span>
                                    words</div>
                            </div>
                            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
                                <button type="button" @click="modal.open = false"
                                    class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="submitQualification()" :disabled="modal.loading"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                    <span x-show="!modal.loading">Save</span>
                                    <span x-show="modal.loading" x-cloak>Saving…</span>
                                </button>
                            </div>
                        </div>

                        {{-- ADD / EDIT STEP --}}
                        <div x-show="modal.type === 'add-step' || modal.type === 'edit-step'" x-cloak
                            class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Step Content <span
                                        class="text-red-500">*</span></label>
                                <div id="quill-step"
                                    :class="formErrors.content ? 'rounded-lg border-2 border-red-500' :
                                        'rounded-lg border border-slate-300'">
                                </div>
                                <p x-show="formErrors.content" class="mt-1 text-xs text-red-500 font-semibold"
                                    x-cloak>Content cannot be empty.</p>
                                <div class="mt-1 text-xs text-slate-400"><span id="quill-step-wordcount">0</span>
                                    words</div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">
                                    Link <span class="text-slate-400 font-normal">(optional)</span>
                                </label>
                                <input type="url" x-model="form.link" placeholder="https://..."
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                            </div>
                            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
                                <button type="button" @click="modal.open = false"
                                    class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="submitStep()" :disabled="modal.loading"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                    <span x-show="!modal.loading">Save</span>
                                    <span x-show="modal.loading" x-cloak>Saving…</span>
                                </button>
                            </div>
                        </div>

                        {{-- ADD / EDIT STORY --}}
                        <template x-if="modal.type === 'add-story' || modal.type === 'edit-story'">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Story Title
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="form.title"
                                        :class="formErrors.title ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @input="formErrors.title = false"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                    <p x-show="formErrors.title" class="mt-1 text-xs text-red-500 font-semibold"
                                        x-cloak>Story title is required.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Story Link</label>
                                    <input type="url" x-model="form.link" placeholder="https://..."
                                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">
                                        Year <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" x-model.number="form.story_year" placeholder="e.g. 2024"
                                        min="2000" max="2100"
                                        :class="formErrors.story_year ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @input="formErrors.story_year = false"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                    <p x-show="formErrors.story_year"
                                        class="mt-1 text-xs text-red-500 font-semibold" x-cloak>
                                        Year is required.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">
                                        Thumbnail Image <span x-show="modal.type === 'edit-story'"
                                            class="text-slate-400 font-normal">(leave blank to keep current)</span>
                                    </label>
                                    <input type="file" accept="image/*" x-init="$el.value = '';
                                    form.image = null;"
                                        @change="
                                const file = $event.target.files[0];
                                if (file && file.size > 5 * 1024 * 1024) {
                                    showToast('Image must be under 5MB.', 'warning');
                                    $event.target.value = '';
                                    return;
                                }
                                form.image = file;
                                formErrors.image = false;
                            "
                                        :class="formErrors.image ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-300'"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm" />
                                    <p x-show="formErrors.image" class="mt-1 text-xs text-red-500 font-semibold"
                                        x-cloak>Please upload a thumbnail image.</p>
                                </div>
                                <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
                                    <button type="button" @click="modal.open = false"
                                        class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                    <button type="button" @click="submitStory()" :disabled="modal.loading"
                                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading">Save</span>
                                        <span x-show="modal.loading" x-cloak>Saving…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- ADD / EDIT TESTIMONIAL --}}
                        <div x-show="modal.type === 'add-testimonial' || modal.type === 'edit-testimonial'" x-cloak
                            class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Quote <span
                                        class="text-red-500">*</span></label>
                                <textarea x-model="form.quote" rows="4" placeholder="Enter the testimonial quote..."
                                    :class="formErrors.quote ? 'border-red-500 ring-2 ring-red-200' :
                                        'border-slate-300 focus:ring-indigo-400'"
                                    @input="formErrors.quote = false"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none resize-none"></textarea>
                                <p x-show="formErrors.quote" class="mt-1 text-xs text-red-500 font-semibold"
                                    x-cloak>Quote is required.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" x-model="form.author_name"
                                        placeholder="e.g. Juan dela Cruz"
                                        :class="formErrors.author_name ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @input="formErrors.author_name = false"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                    <p x-show="formErrors.author_name"
                                        class="mt-1 text-xs text-red-500 font-semibold" x-cloak>Author name is
                                        required.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Role /
                                        Program</label>
                                    <input type="text" x-model="form.author_role"
                                        placeholder="e.g. GIP Beneficiary"
                                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                                </div>
                            </div>
                            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
                                <button type="button" @click="modal.open = false"
                                    class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="submitTestimonial()" :disabled="modal.loading"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                    <span x-show="!modal.loading">Save</span>
                                    <span x-show="modal.loading" x-cloak>Saving…</span>
                                </button>
                            </div>
                        </div>

                        {{-- ADD / EDIT PESO --}}
                        {{-- EDIT CTA SECTION --}}
                        <div x-show="modal.type === 'edit-cta'" x-cloak class="space-y-5">
                            <div class="rounded-xl bg-slate-800 px-6 py-5 text-center mb-2">
                                <p class="text-white font-bold text-xl mb-1"
                                    x-text="form.title || 'Ready to Start Your Journey?'"></p>
                                <p class="text-slate-300 text-sm"
                                    x-text="form.subtitle || 'Your subtitle will appear here.'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Title <span
                                        class="text-red-500">*</span></label>
                                <input type="text" x-model="form.title"
                                    :class="formErrors.title ? 'border-red-500 ring-2 ring-red-200' :
                                        'border-slate-300 focus:ring-indigo-400'"
                                    @input="formErrors.title = false"
                                    placeholder="e.g. Ready to Start Your Journey?"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                <p x-show="formErrors.title" class="mt-1 text-xs text-red-500 font-semibold"
                                    x-cloak>Title is required.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Subtitle <span
                                        class="text-red-500">*</span></label>
                                <textarea x-model="form.subtitle"
                                    :class="formErrors.subtitle ? 'border-red-500 ring-2 ring-red-200' :
                                        'border-slate-300 focus:ring-indigo-400'"
                                    @input="formErrors.subtitle = false"
                                    placeholder="e.g. Join thousands of youth who have transformed their careers through DOLE's employment programs."
                                    rows="3" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none resize-none"></textarea>
                                <p x-show="formErrors.subtitle" class="mt-1 text-xs text-red-500 font-semibold"
                                    x-cloak>Subtitle is required.</p>
                            </div>
                            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                                <button type="button" @click="modal.open = false"
                                    class="px-5 py-2 rounded-lg border border-slate-300 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition">
                                    Cancel
                                </button>
                                <button type="button" @click="openCtaConfirm()" :disabled="modal.loading"
                                    class="px-6 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold transition disabled:opacity-50 flex items-center gap-2">
                                    <span>Save Changes</span>
                                </button>
                            </div>
                            <p x-show="modal.error" x-text="modal.error"
                                class="text-xs text-red-500 font-semibold text-center" x-cloak></p>
                        </div>
                        {{-- END EDIT CTA --}}

                        <div x-show="modal.type === 'add-peso' || modal.type === 'edit-peso'" x-cloak
                            class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Office Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" x-model="form.name" placeholder="e.g. PESO MATI CITY"
                                        :class="formErrors.name ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @input="formErrors.name = false"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                    <p x-show="formErrors.name" class="mt-1 text-xs text-red-500 font-semibold"
                                        x-cloak>Office name is required.</p>
                                </div>
                                <div x-data="officeTypeSelector()" x-init="init()">
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Office Type <span
                                            class="text-red-500">*</span></label>

                                    {{-- Normal dropdown + action buttons --}}
                                    <div x-show="mode === 'select'" class="space-y-2">
                                        <div class="flex gap-2">
                                            <select x-model="form.type"
                                                :class="formErrors.type ? 'border-red-500 ring-2 ring-red-200' :
                                                    'border-slate-300 focus:ring-indigo-400'"
                                                @change="formErrors.type = false"
                                                class="flex-1 border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none bg-white cursor-pointer">
                                                <option value="" disabled selected hidden>— Select type —
                                                </option>
                                                <template x-for="t in types" :key="t">
                                                    <option :value="t" x-text="t"
                                                        :selected="form.type === t"></option>
                                                </template>
                                            </select>
                                            {{-- Add --}}
                                            <button type="button" @click="mode = 'add'; inputName = ''"
                                                title="Add new type"
                                                class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-600 flex items-center justify-center transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                            {{-- Edit --}}
                                            <button type="button" @click="startEdit()" :disabled="!form.type"
                                                title="Edit selected type"
                                                class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-600 flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            {{-- Delete --}}
                                            <button type="button" @click="mode = 'delete'" :disabled="!form.type"
                                                title="Delete selected type"
                                                class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 text-red-500 flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                        <p x-show="typeError" x-text="typeError"
                                            class="text-xs text-red-500 font-semibold" x-cloak></p>
                                        <p x-show="formErrors.type && !typeError"
                                            class="text-xs text-red-500 font-semibold" x-cloak>Please select an office
                                            type.</p>
                                    </div>

                                    {{-- Add new type --}}
                                    <div x-show="mode === 'add'" x-cloak class="space-y-2">
                                        <div class="flex gap-2">
                                            <input type="text" x-model="inputName"
                                                @keydown.enter.prevent="saveNewType(form)"
                                                @keydown.escape.prevent="mode = 'select'" placeholder="e.g. SFO"
                                                class="flex-1 border border-indigo-400 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none"
                                                x-ref="addInput" x-ref="addInput">
                                            <button type="button" @click="saveNewType(form)"
                                                :disabled="saving"
                                                class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition disabled:opacity-50">
                                                <span x-show="!saving">Save</span>
                                                <span x-show="saving" x-cloak>…</span>
                                            </button>
                                            <button type="button" @click="mode = 'select'; typeError = ''"
                                                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-lg transition">Cancel</button>
                                        </div>
                                        <p class="text-xs text-slate-400">Enter the new type code (e.g. SFO) and press
                                            Save.</p>
                                        <p x-show="typeError" x-text="typeError"
                                            class="text-xs text-red-500 font-semibold" x-cloak></p>
                                    </div>

                                    {{-- Edit type --}}
                                    <div x-show="mode === 'edit'" x-cloak class="space-y-2">
                                        <p class="text-xs text-slate-500">Renaming: <strong
                                                x-text="form.type"></strong></p>
                                        <div class="flex gap-2">
                                            <input type="text" x-model="inputName"
                                                @keydown.enter.prevent="updateType(form)"
                                                @keydown.escape.prevent="mode = 'select'" placeholder="New name..."
                                                class="flex-1 border border-amber-400 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 outline-none"
                                                x-ref="editInput" x-ref="editInput">
                                            <button type="button" @click="updateType(form)" :disabled="saving"
                                                class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition disabled:opacity-50">
                                                <span x-show="!saving">Rename</span>
                                                <span x-show="saving" x-cloak>…</span>
                                            </button>
                                            <button type="button" @click="mode = 'select'; typeError = ''"
                                                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-lg transition">Cancel</button>
                                        </div>
                                        <p x-show="typeError" x-text="typeError"
                                            class="text-xs text-red-500 font-semibold" x-cloak></p>
                                    </div>

                                    {{-- Delete confirm --}}
                                    <div x-show="mode === 'delete'" x-cloak
                                        class="rounded-lg border border-red-200 bg-red-50 p-3 space-y-2">
                                        <p class="text-sm text-red-700 font-semibold">Delete type "<span
                                                x-text="form.type"></span>"?</p>
                                        <p class="text-xs text-red-500">This only removes it from the type list.
                                            Existing offices with this type are not affected.</p>
                                        <div class="flex gap-2">
                                            <button type="button" @click="deleteType(form)" :disabled="saving"
                                                class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition disabled:opacity-50">
                                                <span x-show="!saving">Yes, Delete</span>
                                                <span x-show="saving" x-cloak>…</span>
                                            </button>
                                            <button type="button" @click="mode = 'select'"
                                                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-lg transition">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Province <span
                                            class="text-red-500">*</span></label>
                                    <select x-model="form.province"
                                        :class="formErrors.province ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @change="formErrors.province = false"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none bg-white cursor-pointer">
                                        <option value="" disabled selected hidden>— Select province —</option>
                                        <option value="DAVAO CITY">DAVAO CITY</option>
                                        <option value="DAVAO DE ORO">DAVAO DE ORO</option>
                                        <option value="DAVAO DEL NORTE">DAVAO DEL NORTE</option>
                                        <option value="DAVAO DEL SUR">DAVAO DEL SUR</option>
                                        <option value="DAVAO OCCIDENTAL">DAVAO OCCIDENTAL</option>
                                        <option value="DAVAO ORIENTAL">DAVAO ORIENTAL</option>
                                    </select>
                                    <p x-show="formErrors.province" class="mt-1 text-xs text-red-500 font-semibold"
                                        x-cloak>Province is required.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Manager / Head Name
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="form.manager" placeholder="e.g. Juan dela Cruz"
                                        :class="formErrors.manager ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @input="formErrors.manager = false"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                    <p x-show="formErrors.manager" class="mt-1 text-xs text-red-500 font-semibold"
                                        x-cloak>Manager name is required.</p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Email Address <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </span>
                                    <input type="email" x-model="form.email" placeholder="office@gmail.com"
                                        :class="formErrors.email ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @input="formErrors.email = false"
                                        class="w-full border rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                </div>
                                <p x-show="formErrors.email" class="mt-1 text-xs text-red-500 font-semibold"
                                    x-cloak>Email address is required.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Address <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </span>
                                    <textarea x-model="form.address" rows="2" placeholder="Full address..."
                                        :class="formErrors.address ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @input="formErrors.address = false"
                                        class="w-full border rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 outline-none resize-none"></textarea>
                                </div>
                                <p x-show="formErrors.address" class="mt-1 text-xs text-red-500 font-semibold"
                                    x-cloak>Address is required.</p>
                            </div>
                            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
                                <button type="button" @click="modal.open = false"
                                    class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="submitFieldOffice()" :disabled="modal.loading"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                    <span x-show="!modal.loading">Save</span>
                                    <span x-show="modal.loading" x-cloak>Saving…</span>
                                </button>
                            </div>
                        </div>

                        {{-- DELETE PESO --}}
                        <template x-if="modal.type === 'delete-peso'">
                            <div class="text-center space-y-4">
                                <div
                                    class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <p class="text-slate-700 font-semibold">Delete this PESO/JPO office?</p>
                                <p class="text-slate-500 text-sm">This action cannot be undone.</p>
                                <div class="flex gap-3 justify-center mt-4">
                                    <button type="button" @click="modal.open = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                    <button type="button" @click="destroyFieldOffice()" :disabled="modal.loading"
                                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading">Yes, Delete</span>
                                        <span x-show="modal.loading" x-cloak>Deleting…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- PUBLISH DIRECTORY --}}
                        <template x-if="modal.type === 'publish-directory'">
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-slate-800 font-bold text-lg">Publish PESO / JPO Directory?</p>
                                        <p class="text-slate-500 text-sm">This will push all your changes live to the
                                            public page.</p>
                                    </div>
                                </div>

                                {{-- Change summary list --}}
                                <div x-show="$store.pesoDirectory.changeLog.length > 0"
                                    class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2.5">
                                        Changes to be published</p>
                                    <ul class="space-y-2">
                                        <template x-for="(entry, i) in $store.pesoDirectory.changeLog"
                                            :key="i">
                                            <li class="flex items-center gap-2 text-xs">
                                                <span
                                                    class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center"
                                                    :class="{ 'bg-emerald-100 text-emerald-600': entry.action==='added', 'bg-blue-100 text-blue-600': entry.action==='edited', 'bg-red-100 text-red-500': entry.action==='deleted' }">
                                                    <template x-if="entry.action === 'added'">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                    </template>
                                                    <template x-if="entry.action === 'edited'">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </template>
                                                    <template x-if="entry.action === 'deleted'">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </template>
                                                </span>
                                                <span class="font-semibold"
                                                    :class="{ 'text-emerald-700': entry.action==='added', 'text-blue-700': entry.action==='edited', 'text-red-600': entry.action==='deleted' }"
                                                    x-text="entry.action.charAt(0).toUpperCase() + entry.action.slice(1)"></span>
                                                <span class="text-slate-400">·</span>
                                                <span class="text-slate-700 font-medium"
                                                    x-text="entry.label"></span>
                                                <span class="text-slate-400"
                                                    x-text="'(' + entry.type + ', ' + entry.province + ')'"></span>
                                                <span class="text-slate-400 ml-auto flex-shrink-0"
                                                    x-text="entry.time"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                                <div class="flex gap-3 justify-end pt-1 border-t border-slate-100">
                                    <button type="button" @click="modal.open = false"
                                        class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                    <button type="button" @click="submitPublishDirectory()"
                                        :disabled="modal.loading"
                                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading">✓ Publish Now</span>
                                        <span x-show="modal.loading" x-cloak>Publishing…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- DELETE CONFIRMATION --}}
                        <template
                            x-if="modal.type === 'delete-slide' || modal.type === 'delete-program' || modal.type === 'delete-item'">
                            <div class="text-center space-y-4">
                                <div
                                    class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <p class="text-slate-700 font-semibold">Are you sure you want to delete this?</p>
                                <p class="text-slate-500 text-sm">This action cannot be undone.</p>
                                <div class="flex gap-3 justify-center mt-4">
                                    <button type="button" @click="modal.open = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                    <button type="button" @click="submitDelete()" :disabled="modal.loading"
                                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading">Yes, Delete</span>
                                        <span x-show="modal.loading" x-cloak>Deleting…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- PUBLISH CONFIRMATION MODAL --}}
                        <template x-if="modal.type === 'publish-program'">
                            <div class="text-center space-y-4">
                                <div
                                    class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                                <p class="text-slate-800 font-bold text-lg">Publish "<span
                                        x-text="modal.programName"></span>"?</p>
                                <p class="text-slate-500 text-sm">This will make the program visible to the public on
                                    the
                                    Programs & Stories page.</p>
                                <div class="flex gap-3 justify-center mt-4">
                                    <button type="button" @click="modal.open = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Not
                                        yet</button>
                                    <button type="button" @click="submitTogglePublish()" :disabled="modal.loading"
                                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading">Yes, Publish Now</span>
                                        <span x-show="modal.loading" x-cloak>Publishing…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- REPUBLISH CONFIRMATION MODAL --}}
                        <template x-if="modal.type === 'republish-program'">
                            <div class="space-y-4">
                                {{-- Header --}}
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-slate-800 font-bold text-lg leading-tight">Republish "<span
                                                x-text="modal.programName"></span>"?</p>
                                        <p class="text-slate-500 text-xs mt-0.5">These changes will go live on the
                                            public page.</p>
                                    </div>
                                </div>

                                {{-- Changelog --}}
                                <div class="bg-slate-50 rounded-xl border border-slate-200 px-4 py-3 space-y-2">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">What's
                                        changing</p>
                                    <template x-for="(change, i) in (modal.changes || [])" :key="i">
                                        <div class="flex items-center gap-2.5">
                                            {{-- Plus --}}
                                            <template x-if="change.icon === 'plus'">
                                                <span
                                                    class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-3 h-3 text-emerald-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </span>
                                            </template>
                                            {{-- Minus --}}
                                            <template x-if="change.icon === 'minus'">
                                                <span
                                                    class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-3 h-3 text-red-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M20 12H4" />
                                                    </svg>
                                                </span>
                                            </template>
                                            {{-- Edit --}}
                                            <template x-if="change.icon === 'edit'">
                                                <span
                                                    class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-3 h-3 text-blue-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </span>
                                            </template>
                                            {{-- Tag / name --}}
                                            <template x-if="change.icon === 'tag'">
                                                <span
                                                    class="w-5 h-5 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-3 h-3 text-indigo-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                                                    </svg>
                                                </span>
                                            </template>
                                            {{-- Doc / description --}}
                                            <template x-if="change.icon === 'doc'">
                                                <span
                                                    class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-3 h-3 text-blue-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </span>
                                            </template>
                                            {{-- Color --}}
                                            <template x-if="change.icon === 'color'">
                                                <span
                                                    class="w-5 h-5 rounded-full bg-pink-100 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-3 h-3 text-pink-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                                    </svg>
                                                </span>
                                            </template>
                                            {{-- Image --}}
                                            <template x-if="change.icon === 'image'">
                                                <span
                                                    class="w-5 h-5 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-3 h-3 text-purple-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </span>
                                            </template>
                                            {{-- Text --}}
                                            <template x-if="change.icon === 'text'">
                                                <span
                                                    class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-3 h-3 text-slate-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                                    </svg>
                                                </span>
                                            </template>
                                            <span class="text-sm text-slate-700" x-text="change.text"></span>
                                        </div>
                                    </template>
                                </div>

                                {{-- Buttons --}}
                                <div class="flex gap-3 justify-end pt-1">
                                    <button type="button" @click="modal.open = false"
                                        class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Not
                                        yet</button>
                                    <button type="button" @click="submitRepublish()" :disabled="modal.loading"
                                        class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading">Yes, Republish Now</span>
                                        <span x-show="modal.loading" x-cloak>Republishing…</span>
                                    </button>
                                </div>
                            </div>
                        </template>


                        {{-- UNPUBLISH CONFIRMATION MODAL --}}
                        <template x-if="modal.type === 'unpublish-program'">
                            <div class="text-center space-y-4">
                                <div
                                    class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </div>
                                <p class="text-slate-800 font-bold text-lg">Unpublish "<span
                                        x-text="modal.programName"></span>"?</p>
                                <p class="text-slate-500 text-sm">This will hide the program from the public page. You
                                    can
                                    republish it anytime.</p>
                                <div class="flex gap-3 justify-center mt-4">
                                    <button type="button" @click="modal.open = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                    <button type="button" @click="submitTogglePublish()" :disabled="modal.loading"
                                        class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading">Yes, Unpublish</span>
                                        <span x-show="modal.loading" x-cloak>Unpublishing…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>
            </div>
            {{-- ===== END MODALS ===== --}}

            <script>
                /* ── Global reactive store for PESO directory draft state ── */
                // FIX #11: Helper to safely escape user-supplied strings used in event details
                // Prevents HTML injection if strings end up rendered via innerHTML elsewhere.
                function escapeText(str) {
                    const div = document.createElement('div');
                    div.textContent = String(str ?? '');
                    return div.innerHTML;
                }

                document.addEventListener('alpine:init', () => {
                    Alpine.store('pesoDirectory', {
                        hasDraftChanges: @json($directoryHasDraft),
                        changeLog: @json($directoryChangelog),
                        markDirty(entry) {
                            this.hasDraftChanges = true;
                            this.changeLog.push(entry);
                        },
                        reset() {
                            this.hasDraftChanges = false;
                            this.changeLog = [];
                        },
                    });

                    Alpine.data('pesoCard', (id) => ({
                        entryId: id,
                    }));
                });

                /* ══════════════════════════════════════════════════════
                                   STORIES CAROUSEL — Alpine component factory
                                   One independent instance per program accordion.
                                ══════════════════════════════════════════════════════ */
                function storiesCarousel(wrapperId, accentColor, programId) {
                    return {
                        wrapperId,
                        accentColor,
                        programId,
                        trackId: wrapperId + '-track',
                        currentPage: 0,
                        totalPages: 1,
                        PER_PAGE: 5,

                        // Year range filter state
                        yearFrom: '',
                        yearTo: '',
                        availableYears: [],
                        isFiltering: false,

                        init() {
                            this.$nextTick(() => {
                                this.recalc();
                                this.loadYears();
                                window.addEventListener('resize', () => this.recalc());

                                // ── Scroll wheel → horizontal page navigation ──
                                const wrapper = document.getElementById(this.wrapperId + '-wrapper');
                                if (wrapper) {
                                    let _wheelLocked = false;
                                    wrapper.addEventListener('wheel', (e) => {
                                        // Only intercept when there are multiple pages
                                        if (this.totalPages <= 1) return;

                                        const isScrollingDown = e.deltaY > 0;
                                        const atStart = this.currentPage === 0;
                                        const atEnd = this.currentPage >= this.totalPages - 1;

                                        // If we're at a boundary in the scroll direction, let the
                                        // page scroll normally so the user isn't trapped.
                                        if ((isScrollingDown && atEnd) || (!isScrollingDown && atStart)) return;

                                        // Otherwise hijack the scroll: move carousel page instead.
                                        e.preventDefault();
                                        if (_wheelLocked) return;
                                        _wheelLocked = true;
                                        setTimeout(() => {
                                            _wheelLocked = false;
                                        }, 500); // debounce

                                        if (isScrollingDown) {
                                            this.next();
                                        } else {
                                            this.prev();
                                        }
                                    }, {
                                        passive: false
                                    });
                                }
                            });
                        },

                        async loadYears() {
                            if (!this.programId) return;
                            try {
                                const res = await fetch(`/admin/stories/years?program_id=${this.programId}`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                                const data = await res.json();
                                this.availableYears = data.years ?? [];
                            } catch (e) {
                                this.availableYears = [];
                            }
                        },

                        filterByYear() {
                            this.isFiltering = true;

                            if (this.yearFrom && this.yearTo) {
                                const from = parseInt(this.yearFrom);
                                const to = parseInt(this.yearTo);
                                if (from > to) {
                                    this.yearTo = this.yearFrom;
                                }
                            }

                            const track = document.getElementById(this.trackId);
                            if (!track) {
                                this.isFiltering = false;
                                return;
                            }

                            // ── Filter visibility ──
                            const cards = track.querySelectorAll('.story-card-slide');
                            const from = this.yearFrom ? parseInt(this.yearFrom) : null;
                            const to = this.yearTo ? parseInt(this.yearTo) : null;

                            cards.forEach(card => {
                                if (!from && !to) {
                                    card.style.display = '';
                                } else {
                                    const year = parseInt(card.dataset.storyYear);
                                    const inRange = (!from || year >= from) && (!to || year <= to);
                                    card.style.display = inRange ? '' : 'none';
                                }
                            });

                            // ── Sort visible story cards ascending by year (oldest first) ──
                            this.sortCardsByYearAsc(track);

                            this.currentPage = 0;
                            this.recalc();
                            this.isFiltering = false;
                        },

                        sortCardsByYearAsc(track) {
                            if (!track) return;
                            // Grab all story cards and sort by data-story-year ASC
                            const storyCards = Array.from(track.querySelectorAll('.story-card-slide'));
                            const addSlot = track.querySelector('.story-add-slot');

                            storyCards.sort((a, b) => {
                                const ya = parseInt(a.dataset.storyYear) || 0;
                                const yb = parseInt(b.dataset.storyYear) || 0;
                                return ya - yb; // ascending: oldest first
                            });

                            // Re-insert sorted cards before the add-slot (or at end if no slot)
                            storyCards.forEach(card => {
                                if (addSlot) {
                                    track.insertBefore(card, addSlot);
                                } else {
                                    track.appendChild(card);
                                }
                            });
                        },

                        clearFilter() {
                            this.yearFrom = '';
                            this.yearTo = '';
                            this.filterByYear();
                        },

                        exportStories() {
                            const track = document.getElementById(this.trackId);
                            if (!track) return;

                            // Collect only visible cards (respects active year filter)
                            const cards = Array.from(track.querySelectorAll('.story-card-slide'))
                                .filter(c => c.style.display !== 'none');

                            if (cards.length === 0) {
                                if (window.showToast) showToast('No stories to export.', 'error');
                                return;
                            }

                            const headers = ['Story Title', 'Year', 'Link', 'Program'];

                            const csvContent = [
                                headers.join(','),
                                ...cards.map(card => [
                                    `"${(card.dataset.storyTitle  || '').replace(/"/g, '""')}"`,
                                    card.dataset.storyYear || '',
                                    `"${(card.dataset.storyLink   || '').replace(/"/g, '""')}"`,
                                    `"${(card.dataset.programName || '').replace(/"/g, '""')}"`,
                                ].join(','))
                            ].join('\n');

                            const blob = new Blob([csvContent], {
                                type: 'text/csv'
                            });
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;

                            // Descriptive filename that includes the year range when filtered
                            let filename = 'success-stories';
                            if (this.yearFrom && this.yearTo) filename += `-${this.yearFrom}-${this.yearTo}`;
                            else if (this.yearFrom) filename += `-from-${this.yearFrom}`;
                            else if (this.yearTo) filename += `-to-${this.yearTo}`;
                            a.download = filename + '.csv';

                            a.click();
                            window.URL.revokeObjectURL(url);
                        },

                        recalc() {
                            const track = document.getElementById(this.trackId);
                            if (!track) return;

                            // Only count visible cards for pagination
                            const cards = Array.from(track.querySelectorAll('.story-card-slide, .story-add-slot'))
                                .filter(c => c.style.display !== 'none');
                            const total = cards.length;
                            this.totalPages = Math.max(1, Math.ceil(total / this.PER_PAGE));

                            // Clamp currentPage after possible DOM changes
                            if (this.currentPage >= this.totalPages) {
                                this.currentPage = this.totalPages - 1;
                            }

                            // Set card widths dynamically so 5 fit perfectly
                            const outerWidth = track.parentElement.offsetWidth;
                            const gap = 12;
                            const cardWidth = (outerWidth - gap * (this.PER_PAGE - 1)) / this.PER_PAGE;

                            // Size ALL cards (including hidden) so layout is consistent when filter changes
                            track.querySelectorAll('.story-card-slide, .story-add-slot').forEach(card => {
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
                            const pageWidth = this.PER_PAGE * cardWidth + (this.PER_PAGE - 1) * gap +
                                gap; // +gap for the gap after last card

                            track.style.transform = `translateX(-${this.currentPage * pageWidth}px)`;
                        },

                        prev() {
                            if (this.currentPage > 0) {
                                this.currentPage--;
                                this.slide();
                            }
                        },

                        next() {
                            if (this.currentPage < this.totalPages - 1) {
                                this.currentPage++;
                                this.slide();
                            }
                        },

                        goTo(page) {
                            this.currentPage = page;
                            this.slide();
                        },
                    };
                }

                /* ══════════════════════════════════════════════════════
                   ADMIN PAGE — main Alpine component
                ══════════════════════════════════════════════════════ */
                function adminPage() {
                    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    async function jsonRequest(method, url, body = {}) {
                        try {
                            const res = await fetch(url, {
                                method,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': CSRF,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(body),
                            });
                            if (!res.ok) {
                                try {
                                    const errBody = await res.json();
                                    const msg = errBody.message ||
                                        Object.values(errBody.errors || {})[0]?.[0] ||
                                        `Server error (${res.status}). Please try again.`;
                                    return {
                                        success: false,
                                        message: msg
                                    };
                                } catch {
                                    return {
                                        success: false,
                                        message: `Server error (${res.status}). Please try again.`
                                    };
                                }
                            }
                            return res.json();
                        } catch (e) {
                            return {
                                success: false,
                                message: 'Network error. Please check your connection.'
                            };
                        }
                    }

                    async function formRequest(method, url, data = {}) {
                        const fd = new FormData();
                        if (method === 'PUT') {
                            fd.append('_method', 'PUT');
                            method = 'POST';
                        }
                        for (const [k, v] of Object.entries(data)) {
                            if (v !== null && v !== undefined) fd.append(k, v);
                        }
                        try {
                            const res = await fetch(url, {
                                method,
                                headers: {
                                    'X-CSRF-TOKEN': CSRF,
                                    'Accept': 'application/json'
                                },
                                body: fd,
                            });
                            if (!res.ok) {
                                try {
                                    const errBody = await res.json();
                                    const msg = errBody.message ||
                                        Object.values(errBody.errors || {})[0]?.[0] ||
                                        `Server error (${res.status}). Please try again.`;
                                    return {
                                        success: false,
                                        message: msg
                                    };
                                } catch {
                                    return {
                                        success: false,
                                        message: `Server error (${res.status}). Please try again.`
                                    };
                                }
                            }
                            return res.json();
                        } catch (e) {
                            return {
                                success: false,
                                message: 'Network error. Please check your connection.'
                            };
                        }
                    }

                    return {
                        modal: {
                            open: false,
                            type: null,
                            title: '',
                            id: null,
                            programId: null,
                            programName: '',
                            endpoint: null,
                            data: null,
                            loading: false,
                            error: null
                        },
                        fieldErrors: {},
                        form: {},
                        formErrors: {},

                        openModal(detail) {
                            const titles = {
                                'add-slide': 'Add Carousel Slide',
                                'edit-slide': 'Edit Carousel Slide',
                                'delete-slide': 'Delete Slide',
                                'add-program': 'Add New Program',
                                'edit-program': 'Edit Program',
                                'delete-program': 'Delete Program',
                                'publish-program': 'Publish Program',
                                'unpublish-program': 'Unpublish Program',
                                'republish-program': 'Republish Program',
                                'edit-description': 'Edit Program Description',
                                'add-qualification': 'Add Item',
                                'edit-qualification': 'Edit Item',
                                'add-step': 'Add Step',
                                'edit-step': 'Edit Step',
                                'add-story': 'Add Success Story',
                                'edit-story': 'Edit Success Story',
                                'add-testimonial': 'Add Testimonial',
                                'edit-testimonial': 'Edit Testimonial',
                                'delete-item': 'Delete Item',
                                'edit-cta': 'Edit CTA Section',
                                'add-peso': 'Add PESO / JPO Office',
                                'edit-peso': 'Edit PESO / JPO Office',
                                'delete-peso': 'Delete PESO / JPO Office',
                                'publish-directory': 'Publish PESO / JPO Directory',
                            };
                            this.modal = {
                                open: true,
                                type: detail.type,
                                title: titles[detail.type] ?? 'Edit',
                                id: detail.id ?? null,
                                programId: detail.programId ?? null,
                                programName: detail.programName ?? '',
                                endpoint: detail.endpoint ?? null,
                                data: detail.data ?? null,
                                changes: detail.changes ?? [],
                                loading: false,
                                error: null
                            };
                            this.formErrors = {};
                            this.form = detail.data ? {
                                ...detail.data
                            } : {};
                            if (detail.data?.defaultType) this.form.type = detail.data.defaultType;
                            if (detail.type === 'edit-slide' && detail.data) {
                                this.form.program_label = detail.data.program ?? '';
                                this.form.image_preview = null;
                            }

                            if (['add-program', 'edit-program'].includes(detail.type)) {
                                this.$nextTick(() => this.initQuill('quill-program', 'quill-program-wordcount', 'description'));
                            }
                            if (detail.type === 'edit-description') {
                                this.$nextTick(() => this.initQuill('quill-description', 'quill-description-wordcount',
                                    'description'));
                            }
                            if (['add-slide', 'edit-slide'].includes(detail.type)) {
                                this.$nextTick(() => this.initQuill('quill-excerpt', 'quill-excerpt-wordcount', 'excerpt'));
                            }
                            if (['add-qualification', 'edit-qualification'].includes(detail.type)) {
                                this.$nextTick(() => this.initQuill('quill-qualification', 'quill-qualification-wordcount',
                                    'content'));
                            }
                            if (['add-step', 'edit-step'].includes(detail.type)) {
                                this.$nextTick(() => this.initQuill('quill-step', 'quill-step-wordcount', 'content'));
                            }
                            if (['add-testimonial', 'edit-testimonial'].includes(detail.type)) {
                                this.$nextTick(() => this.initQuill('quill-quote', 'quill-quote-wordcount', 'quote'));
                            }
                        },

                        initQuill(editorId, wordCountId, formField) {
                            if (!window._quillSizeRegistered) {
                                const SizeStyle = Quill.import('attributors/style/size');
                                SizeStyle.whitelist = ['8pt', '10pt', '11pt', '12pt', '14pt', '16pt', '18pt', '24pt', '36pt'];
                                Quill.register(SizeStyle, true);
                                window._quillSizeRegistered = true;
                            }
                            const el = document.getElementById(editorId);
                            if (!el) return;
                            if (!window._quillInstances) window._quillInstances = {};
                            let quill = window._quillInstances[editorId];
                            if (!quill) {
                                quill = new Quill('#' + editorId, {
                                    theme: 'snow',
                                    placeholder: 'Enter text...',
                                    modules: {
                                        toolbar: [
                                            [{
                                                font: []
                                            }, {
                                                size: ['8pt', '10pt', '11pt', '12pt', '14pt', '16pt', '18pt',
                                                    '24pt', '36pt'
                                                ]
                                            }],
                                            ['bold', 'italic', 'underline', 'strike'],
                                            [{
                                                color: []
                                            }, {
                                                background: []
                                            }],
                                            [{
                                                header: [1, 2, 3, false]
                                            }],
                                            [{
                                                align: []
                                            }],
                                            [{
                                                list: 'ordered'
                                            }, {
                                                list: 'bullet'
                                            }],
                                            ['link', 'clean'],
                                        ]
                                    }
                                });
                                window._quillInstances[editorId] = quill;
                            }
                            quill.off('text-change');
                            quill.root.innerHTML = this.form[formField] || '';
                            const updateWordCount = () => {
                                const text = quill.root.innerText.trim();
                                const count = text ? text.split(/\s+/).filter(w => w.length > 0).length : 0;
                                const wc = document.getElementById(wordCountId);
                                if (wc) wc.textContent = count;
                            };
                            updateWordCount();
                            quill.on('text-change', () => {
                                this.form[formField] = quill.root.innerHTML;
                                updateWordCount();
                            });
                        },

                        done() {
                            this.modal.open = false;

                            // Slide actions: refresh only the carousel
                            const slideTypes = ['add-slide', 'edit-slide', 'delete-slide'];
                            // Whole-list actions: refresh the full programs container
                            const wholeListTypes = [
                                'add-program', 'edit-program', 'delete-program',
                                'publish-program', 'unpublish-program', 'republish-program',
                            ];
                            // Body-only actions: refresh just the open program's accordion body
                            const bodyOnlyTypes = [
                                'edit-description', 'delete-item',
                                'add-qualification', 'edit-qualification',
                                'add-step', 'edit-step',
                                'add-story', 'edit-story',
                                'add-testimonial', 'edit-testimonial',
                            ];

                            const type = this.modal.type;
                            const programId = this.modal.programId ?? null;

                            if (slideTypes.includes(type)) {
                                setTimeout(() => refreshCarousel(), 150);
                            } else if (bodyOnlyTypes.includes(type) && programId) {
                                setTimeout(() => refreshProgramBody(programId), 150);
                            } else {
                                setTimeout(() => refreshProgramsContainer(), 150);
                            }
                        },
                        fail(msg) {
                            this.modal.loading = false;
                            showToast(msg || 'Something went wrong. Please try again.', 'error');
                        },
                        showSuccess(title, message) {
                            this.modal.open = false;
                            this.modal.loading = false;
                            this.fieldErrors = {};
                            window.dispatchEvent(new CustomEvent('show-success-modal', {
                                detail: {
                                    title,
                                    message
                                }
                            }));
                        },
                        clearFieldErrors() {
                            this.fieldErrors = {};
                            document.querySelectorAll('.field-error-highlight').forEach(el => {
                                el.classList.remove('border-red-400', 'ring-2', 'ring-red-200', 'field-error-highlight');
                            });
                        },

                        // Validates a list of {key, label, check} rules.
                        // Marks all failing fields in formErrors and fires a warning
                        // toast naming the first empty field. Returns true if all pass.
                        validateFields(rules) {
                            this.formErrors = {};
                            for (const rule of rules) {
                                if (!rule.check) this.formErrors[rule.key] = true;
                            }
                            const failed = rules.filter(r => !r.check);
                            if (failed.length === 0) return true;
                            const label = failed[0].label;
                            showToast(
                                failed.length === 1 ?
                                `"${label}" is required. Please fill it in before saving.` :
                                `"${label}" is required — and ${failed.length - 1} other field${failed.length > 2 ? 's are' : ' is'} also empty.`,
                                'warning'
                            );
                            return false;
                        },

                        async submitSlide() {
                            const isEdit = this.modal.type === 'edit-slide';
                            const rules = [{
                                    key: 'title',
                                    label: 'Story Title',
                                    check: !!this.form.title?.trim()
                                },
                                ...(!isEdit ? [{
                                    key: 'image',
                                    label: 'Slide Image',
                                    check: !!this.form.image
                                }] : []),
                            ];
                            if (!this.validateFields(rules)) return;
                            this.modal.loading = true;
                            this.modal.error = null;
                            const data = {
                                title: this.form.title,
                                excerpt: this.form.excerpt,
                                program_label: this.form.program_label,
                                color: this.form.color,
                                link: this.form.link || null,
                            };
                            if (this.form.image) data.image = this.form.image;
                            const res = await formRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/admin/carousel/${this.modal.id}` :
                                '/admin/carousel', data);
                            res.success ? this.done() : this.fail(res.message);
                        },

                        async submitProgram() {
                            const rules = [{
                                    key: 'name',
                                    label: 'Program Name',
                                    check: !!this.form.name?.trim()
                                },
                                {
                                    key: 'color',
                                    label: 'Theme Color',
                                    check: !!this.form.color
                                },
                            ];
                            if (!this.validateFields(rules)) return;
                            this.modal.loading = true;
                            this.modal.error = null;
                            const isEdit = this.modal.type === 'edit-program';
                            const data = {
                                name: this.form.name,
                                acronym: this.form.acronym || null,
                                subtitle: this.form.subtitle,
                                description: this.form.description,
                                color: this.form.color
                            };
                            if (this.form.logo) data.logo = this.form.logo;
                            const res = await formRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/admin/programs/${this.modal.id}` :
                                '/admin/programs', data);
                            res.success ? this.done() : this.fail(res.message);
                        },

                        async submitDescription() {
                            this.modal.loading = true;
                            this.modal.error = null;
                            const res = await jsonRequest('PUT', `/admin/programs/${this.modal.id}/description`, {
                                description: this.form.description
                            });
                            res.success ? this.done() : this.fail(res.message);
                        },

                        async submitQualification() {
                            const isBlankContent = !this.form.content?.trim() || this.form.content === '<p><br></p>';
                            const rules = [{
                                key: 'content',
                                label: 'Content',
                                check: !isBlankContent
                            }, ];
                            if (!this.validateFields(rules)) return;
                            this.modal.loading = true;
                            this.modal.error = null;
                            const isEdit = this.modal.type === 'edit-qualification';
                            const tmp = document.createElement('div');
                            tmp.innerHTML = this.form.content || '';
                            const listItems = tmp.querySelectorAll('li');
                            if (!isEdit && listItems.length > 1) {
                                for (const li of listItems) {
                                    const res = await jsonRequest('POST', '/admin/qualifications', {
                                        type: this.form.type,
                                        content: li.innerHTML,
                                        program_id: this.modal.programId
                                    });
                                    if (!res.success) {
                                        this.fail(res.message);
                                        return;
                                    }
                                }
                                this.done();
                                return;
                            }
                            let content = this.form.content || '';
                            if (tmp.children.length === 1 && tmp.children[0].tagName === 'P') content = tmp.children[0]
                                .innerHTML;
                            const body = {
                                type: this.form.type,
                                content
                            };
                            if (!isEdit) body.program_id = this.modal.programId;
                            const res = await jsonRequest(isEdit ? 'PUT' : 'POST', isEdit ?
                                `/admin/qualifications/${this.modal.id}` :
                                '/admin/qualifications', body);
                            res.success ? this.done() : this.fail(res.message);
                        },

                        async submitStep() {
                            const isBlankStep = !this.form.content?.trim() || this.form.content === '<p><br></p>';
                            const rules = [{
                                key: 'content',
                                label: 'Step Content',
                                check: !isBlankStep
                            }, ];
                            if (!this.validateFields(rules)) return;
                            this.modal.loading = true;
                            this.modal.error = null;
                            const isEdit = this.modal.type === 'edit-step';
                            const body = {
                                content: this.form.content,
                                link: this.form.link || null
                            };
                            if (!isEdit) body.program_id = this.modal.programId;
                            const res = await jsonRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/admin/steps/${this.modal.id}` :
                                '/admin/steps', body);
                            res.success ? this.done() : this.fail(res.message);
                        },

                        async submitStory() {
                            const isAddStory = this.modal.type === 'add-story';
                            const rules = [{
                                    key: 'title',
                                    label: 'Story Title',
                                    check: !!this.form.title?.trim()
                                },
                                {
                                    key: 'story_year',
                                    label: 'Year',
                                    check: !!this.form.story_year
                                },
                                ...(isAddStory ? [{
                                    key: 'image',
                                    label: 'Thumbnail Image',
                                    check: !!this.form.image
                                }] : []),
                            ];
                            if (!this.validateFields(rules)) return;
                            this.modal.loading = true;
                            this.modal.error = null;
                            const isEdit = this.modal.type === 'edit-story';
                            const data = {
                                title: this.form.title,
                                link: this.form.link,
                                story_year: this.form.story_year || null
                            };
                            if (!isEdit) data.program_id = this.modal.programId;
                            if (this.form.image) data.image = this.form.image;
                            const res = await formRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/admin/stories/${this.modal.id}` :
                                '/admin/stories', data);
                            res.success ? this.done() : this.fail(res.message);
                        },

                        async submitTestimonial() {
                            const rules = [{
                                    key: 'quote',
                                    label: 'Quote',
                                    check: !!this.form.quote?.trim()
                                },
                                {
                                    key: 'author_name',
                                    label: 'Author Name',
                                    check: !!this.form.author_name?.trim()
                                },
                            ];
                            if (!this.validateFields(rules)) return;
                            this.modal.loading = true;
                            this.modal.error = null;
                            const isEdit = this.modal.type === 'edit-testimonial';
                            const body = {
                                quote: this.form.quote,
                                author_name: this.form.author_name,
                                author_role: this.form.author_role
                            };
                            if (!isEdit) body.program_id = this.modal.programId;
                            const res = await jsonRequest(isEdit ? 'PUT' : 'POST', isEdit ?
                                `/admin/testimonials/${this.modal.id}` :
                                '/admin/testimonials', body);
                            res.success ? this.done() : this.fail(res.message);
                        },

                        async submitDelete() {
                            this.modal.loading = true;
                            this.modal.error = null;
                            let url;
                            if (this.modal.type === 'delete-slide') url = `/admin/carousel/${this.modal.id}`;
                            else if (this.modal.type === 'delete-program') url = `/admin/programs/${this.modal.id}`;
                            else url = this.modal.endpoint;
                            const res = await jsonRequest('DELETE', url);
                            if (res.success) {
                                this.done();
                                window.dispatchEvent(new CustomEvent('show-success-modal', {
                                    detail: {
                                        message: 'Item deleted successfully.'
                                    }
                                }));
                            } else {
                                this.fail(res.message);
                            }
                        },

                        async submitTogglePublish() {
                            this.modal.loading = true;
                            this.modal.error = null;
                            const res = await jsonRequest('PATCH', `/admin/programs/${this.modal.id}/toggle-publish`);
                            res.success ? this.done() : this.fail(res.message);
                        },

                        async submitRepublish() {
                            this.modal.loading = true;
                            this.modal.error = null;
                            const res = await jsonRequest('PATCH', `/admin/programs/${this.modal.id}/republish`);
                            res.success ? this.done() : this.fail(res.message);
                        },

                        openCtaConfirm() {
                            const rules = [{
                                    key: 'title',
                                    label: 'CTA Title',
                                    check: !!this.form.title?.trim()
                                },
                                {
                                    key: 'subtitle',
                                    label: 'CTA Subtitle',
                                    check: !!this.form.subtitle?.trim()
                                },
                            ];
                            if (!this.validateFields(rules)) return;
                            // Stash values, close edit modal, show plain-JS confirm modal
                            window._ctaPending = {
                                title: this.form.title.trim(),
                                subtitle: this.form.subtitle.trim(),
                            };
                            this.modal.open = false;
                            document.getElementById('ctaConfirmModal').classList.remove('hidden');
                        },

                        submitCta() {
                            // No-op — submission is handled by ctaConfirmedSubmit() plain JS function
                        },

                        async submitFieldOffice() {
                            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            const validEmail = !!this.form.email?.trim() && emailRegex.test(this.form.email.trim());
                            const rules = [{
                                    key: 'name',
                                    label: 'Office Name',
                                    check: !!this.form.name?.trim()
                                },
                                {
                                    key: 'type',
                                    label: 'Office Type',
                                    check: !!this.form.type
                                },
                                {
                                    key: 'province',
                                    label: 'Province',
                                    check: !!this.form.province?.trim()
                                },
                                {
                                    key: 'manager',
                                    label: 'Manager / Head Name',
                                    check: !!this.form.manager?.trim()
                                },
                                {
                                    key: 'email',
                                    label: 'Email Address',
                                    check: validEmail
                                },
                                {
                                    key: 'address',
                                    label: 'Address',
                                    check: !!this.form.address?.trim()
                                },
                            ];
                            if (!this.validateFields(rules)) return;
                            this.modal.loading = true;
                            this.modal.error = null;
                            const isEdit = this.modal.type === 'edit-peso';
                            const body = {
                                name: this.form.name,
                                office_type: this.form.type,
                                province: this.form.province,
                                manager_name: this.form.manager,
                                email: this.form.email,
                                address: this.form.address,
                            };
                            const res = await jsonRequest(
                                isEdit ? 'PUT' : 'POST',
                                isEdit ? `/admin/field-offices/${this.modal.id}` : '/admin/field-offices',
                                body
                            );
                            if (res.success) {
                                const prov = this.form.province;
                                const pesoState = document.getElementById('peso-ajax-container')?._x_dataStack?.[0];
                                if (pesoState) {
                                    if (!pesoState.pesoData[prov]) pesoState.pesoData[prov] = [];
                                    if (isEdit) {
                                        const idx = pesoState.pesoData[prov].findIndex(e => e.id === this.modal.id);
                                        if (idx !== -1) {
                                            pesoState.pesoData[prov][idx] = {
                                                ...pesoState.pesoData[prov][idx],
                                                name: body.name,
                                                type: body.office_type,
                                                manager: body.manager_name,
                                                email: body.email,
                                                address: body.address,
                                                id: this.modal.id,
                                            };
                                            pesoState.pesoData[prov] = [...pesoState.pesoData[prov]];
                                        }
                                    } else {
                                        pesoState.pesoData[prov] = [
                                            ...pesoState.pesoData[prov],
                                            {
                                                id: res.id ?? Date.now(),
                                                name: body.name,
                                                type: body.office_type,
                                                manager: body.manager_name,
                                                email: body.email,
                                                address: body.address
                                            }
                                        ];
                                    }
                                }
                                // Mark dirty via global store — triggers reactive button/banner
                                Alpine.store('pesoDirectory').markDirty({
                                    action: isEdit ? 'edited' : 'added',
                                    label: body.name,
                                    type: body.office_type,
                                    province: prov,
                                    time: new Date().toLocaleTimeString([], {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }),
                                });
                                // Persist dirty flag on server so button stays orange after refresh
                                jsonRequest('POST', '/admin/field-offices/touch', {
                                    action: isEdit ? 'edited' : 'added',
                                    label: body.name,
                                    type: body.office_type,
                                    province: prov,
                                    time: new Date().toLocaleTimeString([], {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }),
                                }).catch(() => {});
                                this.modal.open = false;
                                window.dispatchEvent(new CustomEvent('show-success-modal', {
                                    detail: {
                                        title: isEdit ? 'Office Updated!' : 'Office Added!',
                                        // FIX #11: escapeText() prevents HTML injection from user-typed names
                                        message: isEdit ?
                                            escapeText(body.name) + ' has been updated successfully.' : escapeText(
                                                body.name) + ' has been added to ' + escapeText(prov) + '.'
                                    }
                                }));
                            } else {
                                this.fail(res.message);
                            }
                        },

                        async destroyFieldOffice() {
                            this.modal.loading = true;
                            this.modal.error = null;
                            const res = await jsonRequest('DELETE', `/admin/field-offices/${this.modal.id}`);
                            if (res.success) {
                                const pesoState = document.getElementById('peso-ajax-container')?._x_dataStack?.[0];
                                let deletedName = 'Unknown',
                                    deletedType = '',
                                    deletedProv = '';
                                if (pesoState) {
                                    for (const prov in pesoState.pesoData) {
                                        const found = pesoState.pesoData[prov].find(e => e.id === this.modal.id);
                                        if (found) {
                                            deletedName = found.name;
                                            deletedType = found.type;
                                            deletedProv = prov;
                                            break;
                                        }
                                    }
                                    for (const prov in pesoState.pesoData) {
                                        pesoState.pesoData[prov] = pesoState.pesoData[prov].filter(e => e.id !== this.modal.id);
                                    }
                                }
                                // Mark dirty via global store
                                Alpine.store('pesoDirectory').markDirty({
                                    action: 'deleted',
                                    label: deletedName,
                                    type: deletedType,
                                    province: deletedProv,
                                    time: new Date().toLocaleTimeString([], {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }),
                                });
                                // Persist dirty flag on server so button stays orange after refresh
                                jsonRequest('POST', '/admin/field-offices/touch', {
                                    action: 'deleted',
                                    label: deletedName,
                                    type: deletedType,
                                    province: deletedProv,
                                    time: new Date().toLocaleTimeString([], {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }),
                                }).catch(() => {});
                                this.modal.open = false;
                                window.dispatchEvent(new CustomEvent('show-success-modal', {
                                    detail: {
                                        title: 'Office Deleted',
                                        message: escapeText(deletedName) + ' has been removed from the directory.'
                                    }
                                }));
                            } else {
                                this.fail(res.message);
                            }
                        },

                        async submitPublishDirectory() {
                            this.modal.loading = true;
                            this.modal.error = null;
                            const res = await jsonRequest('POST', '/admin/field-offices/publish');
                            if (res.success) {
                                Alpine.store('pesoDirectory').reset();
                                this.modal.open = false;
                                window.dispatchEvent(new CustomEvent('show-success-modal', {
                                    detail: {
                                        title: 'Directory Published!',
                                        message: 'The PESO / JPO Directory is now live and visible to the public.'
                                    }
                                }));
                            } else {
                                this.fail(res.message ?? 'Failed to publish directory.');
                            }
                        },
                    };
                }
            </script>

            <script>
                // ─── AJAX Refresh Helpers ────────────────────────────────────────────────

                // Refreshes only the accordion body of a specific program card.
                // Used for descriptions, qualifications, steps, stories, testimonials.
                function refreshProgramBody(programId) {
                    const body = document.getElementById('program-body-' + programId);
                    if (!body) {
                        refreshProgramsContainer();
                        return;
                    }

                    body.style.transition = 'opacity 0.1s';
                    body.style.opacity = '0.6';
                    body.style.pointerEvents = 'none';

                    fetch(window.location.href, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            return res.text();
                        })
                        .then(html => {
                            const doc = new DOMParser().parseFromString(html, 'text/html');
                            const newBody = doc.getElementById('program-body-' + programId);
                            if (newBody) {
                                body.innerHTML = newBody.innerHTML;
                                // Re-init Alpine on the new inner nodes (stories carousel,
                                // buttons, testimonials etc.) so they are fully reactive.
                                if (window.Alpine) Alpine.initTree(body);

                                // Re-load year dropdowns for any story carousels in this body
                                // (must run after Alpine.initTree so the component state exists)
                                body.querySelectorAll('[id$="-wrapper"]').forEach(wrapper => {
                                    const alpine = wrapper._x_dataStack?.[0];
                                    if (alpine && typeof alpine.loadYears === 'function') {
                                        alpine.loadYears();
                                    }
                                });
                            }
                            body.style.opacity = '1';
                            body.style.pointerEvents = '';
                        })
                        .catch(() => {
                            body.style.opacity = '1';
                            body.style.pointerEvents = '';
                            window.location.reload();
                        });
                }

                // Refreshes just the carousel section (used after add/edit/delete slide).
                // Instead of replacing the DOM node (which breaks Alpine's existing instance,
                // leaks the autoplay interval, and causes double-init), we fetch the fresh
                // server-rendered page, parse the new slides JSON from the x-data attribute,
                // and directly mutate the live Alpine component's reactive `slides` array.
                // Alpine's reactivity system then updates the DOM automatically.
                function refreshCarousel() {
                    const section = document.getElementById('carousel-section');
                    if (!section) {
                        window.location.reload();
                        return;
                    }

                    section.style.transition = 'opacity 0.15s';
                    section.style.opacity = '0.5';
                    section.style.pointerEvents = 'none';

                    fetch(window.location.href, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            return res.text();
                        })
                        .then(html => {
                            const doc = new DOMParser().parseFromString(html, 'text/html');
                            const newSection = doc.getElementById('carousel-section');

                            if (newSection) {
                                // Parse the fresh slides array from the server-rendered x-data attribute.
                                // The attribute looks like: x-data="carouselSection([{...}, ...])"
                                const xDataAttr = newSection.getAttribute('x-data') || '';
                                const match = xDataAttr.match(/^carouselSection\(([\s\S]*)\)$/);

                                if (match) {
                                    try {
                                        const freshSlides = JSON.parse(match[1]);
                                        // Mutate the LIVE Alpine instance — no DOM swap, no re-init.
                                        const alpineData = section._x_dataStack?.[0];
                                        if (alpineData) {
                                            alpineData.slides = freshSlides;
                                            // Clamp currentSlide to valid range after add/delete.
                                            alpineData.currentSlide = Math.min(
                                                alpineData.currentSlide,
                                                Math.max(0, freshSlides.length - 1)
                                            );
                                        } else {
                                            window.location.reload();
                                            return;
                                        }
                                    } catch (e) {
                                        window.location.reload();
                                        return;
                                    }
                                } else {
                                    window.location.reload();
                                    return;
                                }
                            }

                            section.style.opacity = '1';
                            section.style.pointerEvents = '';
                        })
                        .catch(() => {
                            section.style.opacity = '1';
                            section.style.pointerEvents = '';
                            window.location.reload();
                        });
                }

                // Used for add/edit/delete program, publish toggle, carousel changes.
                function refreshProgramsContainer() {
                    const container = document.getElementById('programs-ajax-container');
                    if (!container) {
                        window.location.reload();
                        return;
                    }

                    // Remember which accordion is currently open so we can restore it after the DOM swap
                    const currentOpenId = container._x_dataStack?.[0]?.openId ?? null;

                    container.style.transition = 'opacity 0.1s';
                    container.style.opacity = '0.6';
                    container.style.pointerEvents = 'none';

                    fetch(window.location.href, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            return res.text();
                        })
                        .then(html => {
                            const doc = new DOMParser().parseFromString(html, 'text/html');
                            const newContainer = doc.getElementById('programs-ajax-container');
                            if (newContainer) {
                                container.innerHTML = newContainer.innerHTML;
                                // Re-init Alpine on new child nodes so accordions and
                                // inner components are fully reactive after the swap.
                                if (window.Alpine) Alpine.initTree(container);
                            }
                            container.style.opacity = '1';
                            container.style.pointerEvents = '';

                            // Re-open the accordion that was open before the swap
                            if (currentOpenId !== null) {
                                // Wait for Alpine to initialise the new DOM nodes
                                setTimeout(() => {
                                    if (container._x_dataStack?.[0]) {
                                        container._x_dataStack[0].openId = currentOpenId;
                                    }
                                }, 50);
                            }
                        })
                        .catch(() => {
                            container.style.opacity = '1';
                            container.style.pointerEvents = '';
                            window.location.reload();
                        });
                }



                // ─── Office Type Selector ────────────────────────────────────────────────
                function officeTypeSelector() {
                    return {
                        types: [],
                        mode: 'select', // 'select' | 'add' | 'edit' | 'delete'
                        inputName: '',
                        saving: false,
                        typeError: '',

                        async init() {
                            try {
                                const res = await fetch('/admin/office-types', {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                                if (res.ok) this.types = await res.json();
                            } catch (e) {
                                // fallback — admin can still add a type manually
                            }
                        },

                        startEdit() {
                            this.inputName = this.form ? '' : '';
                            this.inputName = '';
                            this.mode = 'edit';
                        },

                        async saveNewType(form) {
                            this.typeError = '';
                            const name = this.inputName.trim().toUpperCase();
                            if (!name) {
                                this.typeError = 'Please enter a type name.';
                                return;
                            }
                            if (this.types.includes(name)) {
                                this.typeError = 'That type already exists.';
                                return;
                            }

                            this.saving = true;
                            try {
                                const res = await fetch('/admin/office-types', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                    body: JSON.stringify({
                                        name
                                    }),
                                });
                                const data = await res.json();
                                if (res.ok && data.success) {
                                    this.types.push(data.name);
                                    this.types.sort();
                                    form.type = data.name;
                                    this.mode = 'select';
                                    this.inputName = '';
                                    window.dispatchEvent(new CustomEvent('office-type-added', {
                                        detail: {
                                            name: data.name
                                        }
                                    }));
                                } else {
                                    this.typeError = data.message ?? 'Failed to save type.';
                                }
                            } catch (e) {
                                this.typeError = 'Network error. Please try again.';
                            }
                            this.saving = false;
                        },

                        async updateType(form) {
                            this.typeError = '';
                            const oldName = form.type;
                            const newName = this.inputName.trim().toUpperCase();
                            if (!newName) {
                                this.typeError = 'Please enter a new name.';
                                return;
                            }
                            if (newName === oldName) {
                                this.mode = 'select';
                                return;
                            }
                            if (this.types.includes(newName)) {
                                this.typeError = 'That type already exists.';
                                return;
                            }

                            this.saving = true;
                            try {
                                const res = await fetch('/admin/office-types/' + encodeURIComponent(oldName), {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                    body: JSON.stringify({
                                        name: newName
                                    }),
                                });
                                const data = await res.json();
                                if (res.ok && data.success) {
                                    const idx = this.types.indexOf(oldName);
                                    if (idx !== -1) this.types.splice(idx, 1, newName);
                                    this.types.sort();
                                    form.type = newName;
                                    this.mode = 'select';
                                    this.inputName = '';
                                    window.dispatchEvent(new CustomEvent('office-type-renamed', {
                                        detail: {
                                            oldName,
                                            newName
                                        }
                                    }));
                                } else {
                                    this.typeError = data.message ?? 'Failed to rename type.';
                                }
                            } catch (e) {
                                this.typeError = 'Network error. Please try again.';
                            }
                            this.saving = false;
                        },

                        async deleteType(form) {
                            const name = form.type;
                            this.saving = true;
                            try {
                                const res = await fetch('/admin/office-types/' + encodeURIComponent(name), {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                });
                                const data = await res.json();
                                if (res.ok && data.success) {
                                    this.types = this.types.filter(t => t !== name);
                                    form.type = '';
                                    this.mode = 'select';
                                    window.dispatchEvent(new CustomEvent('office-type-deleted', {
                                        detail: {
                                            name
                                        }
                                    }));
                                    window.dispatchEvent(new CustomEvent('show-success-modal', {
                                        detail: {
                                            title: 'Type Deleted',
                                            message: 'Office type "' + escapeText(name) + '" has been removed.'
                                        }
                                    }));
                                } else {
                                    this.typeError = data.message ?? 'Failed to delete type.';
                                    this.mode = 'select';
                                }
                            } catch (e) {
                                this.typeError = 'Network error. Please try again.';
                                this.mode = 'select';
                            }
                            this.saving = false;
                        },
                    };
                }

                function showToast(message, type = 'error') {
                    const container = document.getElementById('toastContainer');
                    if (!container) return;

                    const configs = {
                        error: {
                            bg: 'bg-white',
                            border: 'border-red-500',
                            icon: `<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                            labelColor: 'text-red-600',
                            label: 'Error'
                        },
                        success: {
                            bg: 'bg-white',
                            border: 'border-green-500',
                            icon: `<svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                            labelColor: 'text-green-600',
                            label: 'Success'
                        },
                        info: {
                            bg: 'bg-white',
                            border: 'border-blue-500',
                            icon: `<svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                            labelColor: 'text-blue-600',
                            label: 'Info'
                        },
                        warning: {
                            bg: 'bg-white',
                            border: 'border-yellow-500',
                            icon: `<svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`,
                            labelColor: 'text-yellow-600',
                            label: 'Warning'
                        }
                    };

                    const c = configs[type] || configs.error;

                    const toast = document.createElement('div');
                    toast.className = `pointer-events-auto w-full border-l-4 ${c.bg} rounded-xl shadow-xl overflow-hidden
            transition-all duration-300 ease-out translate-x-full opacity-0`;
                    toast.classList.add('toast-item');

                    // FIX #2: Build toast DOM via safe DOM API instead of innerHTML
                    // to prevent XSS when message comes from server error responses.
                    const inner = document.createElement('div');
                    inner.className = 'flex items-start gap-3 px-4 py-3.5';

                    // Icon (static SVG — safe)
                    const iconWrap = document.createElement('div');
                    iconWrap.innerHTML = c.icon; // icon is a trusted static string, not user data
                    inner.appendChild(iconWrap.firstChild);

                    // Text block
                    const textDiv = document.createElement('div');
                    textDiv.className = 'flex-1 min-w-0';

                    const labelEl = document.createElement('p');
                    labelEl.className = `text-xs font-bold uppercase tracking-wide ${c.labelColor} mb-0.5`;
                    labelEl.textContent = c.label; // safe — static string

                    const msgEl = document.createElement('p');
                    msgEl.className = 'text-sm text-slate-700 leading-snug';
                    msgEl.textContent = message; // FIX: textContent prevents any HTML injection

                    textDiv.appendChild(labelEl);
                    textDiv.appendChild(msgEl);
                    inner.appendChild(textDiv);

                    // Close button
                    const closeBtn = document.createElement('button');
                    closeBtn.className =
                        'flex-shrink-0 w-5 h-5 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition mt-0.5';
                    closeBtn.innerHTML =
                        `<svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
                    closeBtn.addEventListener('click', () => toast.remove());
                    inner.appendChild(closeBtn);

                    toast.appendChild(inner);

                    // Progress bar
                    const progress = document.createElement('div');
                    progress.className = `toast-progress h-1 ${c.border.replace('border-', 'bg-')} w-full origin-left`;
                    toast.appendChild(progress);

                    container.appendChild(toast);

                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => {
                            toast.classList.remove('translate-x-full', 'opacity-0');
                        });
                    });

                    const duration = type === 'error' ? 5000 : 3500;
                    if (progress) {
                        progress.style.transition = `transform ${duration}ms linear`;
                        requestAnimationFrame(() => {
                            progress.style.transform = 'scaleX(0)';
                        });
                    }

                    setTimeout(() => {
                        toast.classList.add('translate-x-full', 'opacity-0');
                        setTimeout(() => toast.remove(), 300);
                    }, duration);
                }

                function closectaConfirmModal() {
                    document.getElementById('ctaConfirmModal').classList.add('hidden');
                }

                async function publishCta() {
                    const cta = Alpine.$data(document.getElementById('cta-section-root'));
                    if (!cta) return;
                    cta.ctaPublishing = true;

                    try {
                        const res = await fetch('/admin/cta-section/publish', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });
                        const data = await res.json();
                        if (data.success) {
                            cta.ctaHasDraft = false;
                            cta.ctaIsPublished = true;
                            const successEl = document.getElementById('ctaSuccessModal');
                            const alpineData = successEl?._x_dataStack?.[0];
                            if (alpineData) {
                                alpineData.title = 'CTA Published!';
                                alpineData.message = 'The CTA section is now live and visible to the public.';
                                alpineData.open = true;
                            }
                        } else {
                            showToast(data.message ?? 'Publish failed. Please try again.', 'error');
                        }
                    } catch (err) {
                        showToast('An error occurred. Please try again.', 'error');
                    }

                    cta.ctaPublishing = false;
                }

                async function ctaConfirmedSubmit() {
                    const pending = window._ctaPending;
                    if (!pending) return;
                    try {
                        const res = await fetch('/admin/cta-section', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify(pending),
                        });
                        const data = await res.json();
                        if (data.success) {
                            // Directly mutate Alpine state — use server-confirmed has_draft value
                            const ctaRoot = Alpine.$data(document.getElementById('cta-section-root'));
                            if (ctaRoot) {
                                ctaRoot.ctaTitle = pending.title;
                                ctaRoot.ctaSubtitle = pending.subtitle;
                                ctaRoot.ctaHasDraft = data.has_draft ?? true;
                            }
                            // Also dispatch event as fallback
                            window.dispatchEvent(new CustomEvent('cta-updated', {
                                detail: {
                                    title: pending.title,
                                    subtitle: pending.subtitle
                                }
                            }));
                            // Directly set Alpine state on the success modal and open it
                            const successEl = document.getElementById('ctaSuccessModal');
                            const alpineData = successEl._x_dataStack?.[0];
                            if (alpineData) {
                                alpineData.title = 'CTA Saved!';
                                alpineData.message =
                                    'Changes saved. Click \'Publish to Public\' or \'Update Published\' to make them live.';
                                alpineData.open = true;
                            }
                        } else {
                            showToast(data.message ?? 'Something went wrong. Please try again.', 'error');
                        }
                    } catch (err) {
                        showToast('An error occurred. Please try again.', 'error');
                    }
                    window._ctaPending = null;
                }
            </script>

            {{-- ===== CTA CONFIRM MODAL ===== --}}
            <div id="ctaConfirmModal"
                class="hidden fixed inset-0 z-[1200] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
                <div class="absolute inset-0" onclick="closectaConfirmModal()"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 pt-8 pb-6 text-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-9 h-9 text-indigo-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white">Save Changes?</h3>
                    </div>
                    <div class="px-6 py-6 text-center">
                        <p class="text-slate-700 text-sm font-medium mb-6">
                            You're about to save changes to the CTA section. Use <strong>Update Published</strong> to
                            push changes to the public page.
                        </p>
                        <div class="flex gap-3">
                            <button onclick="closectaConfirmModal()"
                                class="flex-1 px-5 py-2.5 border border-slate-300 text-slate-700 font-semibold rounded-lg text-sm hover:bg-slate-50 transition">
                                Cancel
                            </button>
                            <button onclick="closectaConfirmModal(); ctaConfirmedSubmit()"
                                class="flex-1 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-sm transition">
                                Yes, Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ===== END CTA CONFIRM MODAL ===== --}}

            {{-- ===== SUCCESS MODAL ===== --}}
            <div id="ctaSuccessModal" x-data="{ open: false, title: 'Success!', message: '' }" x-show="open" x-cloak
                @keydown.escape.window="open = false"
                @show-success-modal.window="open = true; title = $event.detail.title || 'Success!'; message = $event.detail.message"
                @success-modal-open.window="open = true; title = $event.detail.title || 'Success!'; message = $event.detail.message"
                class="fixed inset-0 z-[1300] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
                <div class="absolute inset-0" @click="open = false"></div>
                <div @click.stop class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 pt-8 pb-6 text-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-9 h-9 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white" x-text="title"></h3>
                    </div>
                    <div class="px-6 py-6 text-center">
                        <p class="text-slate-700 text-sm font-medium mb-6" x-text="message"></p>
                        <button @click="open = false"
                            class="w-full px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition text-sm">
                            Done
                        </button>
                    </div>
                </div>
            </div>
            {{-- ===== END SUCCESS MODAL ===== --}}

            <!-- TOAST CONTAINER -->
            <div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"
                style="min-width: 340px;"></div>



</body>

</html>