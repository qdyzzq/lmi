<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Quill.js Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Admin — Programs & Stories</title>

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

        .admin-ribbon {
            background: linear-gradient(135deg, #f59e0b, #d97706);
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

        .admin-icon-btn {
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            flex-shrink: 0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.07);
        }

        .admin-icon-btn.edit {
            background: #eef2ff;
            color: #6366f1;
            border: 1px solid #c7d2fe;
        }

        .admin-icon-btn.edit:hover {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .admin-icon-btn.delete {
            background: #fff1f2;
            color: #f43f5e;
            border: 1px solid #fecdd3;
        }

        .admin-icon-btn.delete:hover {
            background: #e11d48;
            color: white;
            border-color: #e11d48;
        }

        /* ── Quill Editor Styling ── */
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

        .ql-editor::-webkit-scrollbar {
            width: 8px;
        }

        .ql-editor::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .ql-editor::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .ql-editor::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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

        .qual-content p:has(br:only-child) {
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

        /* pt size labels */
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="8pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="8pt"]::before {
            content: '8';
        }

        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="10pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="10pt"]::before {
            content: '10';
        }

        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="11pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="11pt"]::before {
            content: '11';
        }

        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="12pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="12pt"]::before {
            content: '12';
        }

        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="14pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="14pt"]::before {
            content: '14';
        }

        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="16pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="16pt"]::before {
            content: '16';
        }

        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="18pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="18pt"]::before {
            content: '18';
        }

        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="24pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="24pt"]::before {
            content: '24';
        }

        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="36pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="36pt"]::before {
            content: '36';
        }

        .ql-snow .ql-picker.ql-size .ql-picker-label:not([data-value])::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item:not([data-value])::before {
            content: '11';
        }

        /* pt size rendering in editor */
        .ql-editor .ql-size-8pt {
            font-size: 8pt;
        }

        .ql-editor .ql-size-10pt {
            font-size: 10pt;
        }

        .ql-editor .ql-size-11pt {
            font-size: 11pt;
        }

        .ql-editor .ql-size-12pt {
            font-size: 12pt;
        }

        .ql-editor .ql-size-14pt {
            font-size: 14pt;
        }

        .ql-editor .ql-size-16pt {
            font-size: 16pt;
        }

        .ql-editor .ql-size-18pt {
            font-size: 18pt;
        }

        .ql-editor .ql-size-24pt {
            font-size: 24pt;
        }

        .ql-editor .ql-size-36pt {
            font-size: 36pt;
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

        /* ══════════════════════════════════════════
           SUCCESS STORIES CAROUSEL
        ══════════════════════════════════════════ */

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
            transition:
                transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
                box-shadow 0.3s ease,
                border-color 0.3s ease;
        }

        .story-card-slide:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.13), 0 4px 12px rgba(0, 0, 0, 0.07);
        }

        .story-card-slide:hover .story-card-img img {
            transform: scale(1.08);
        }

        .story-card-slide:hover .story-card-overlay {
            opacity: 1;
        }

        .story-card-slide:hover .story-card-read-btn {
            opacity: 1;
            transform: translateY(0);
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

        .story-card-overlay {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 10px;
        }

        .story-card-read-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: white;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            opacity: 0;
            transform: translateY(6px);
            transition: opacity 0.22s ease 0.05s, transform 0.22s ease 0.05s;
            text-decoration: none;
            letter-spacing: 0.02em;
            white-space: nowrap;
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
            transition: gap 0.2s ease;
        }

        .story-card-slide:hover .story-card-link {
            gap: 6px;
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

        /* ══════════════════════════════════════════
           PESO / JPO DIRECTORY STYLES
        ══════════════════════════════════════════ */

        .peso-province-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 10px;
            text-align: left;
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid;
        }

        .peso-office-card {
            border: 1px solid #f1f5f9;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .peso-office-card.expanded {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .peso-type-badge-p {
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 20px;
            font-weight: 600;
            background: #dcfce7;
            color: #16a34a;
        }

        .peso-type-badge-j {
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 20px;
            font-weight: 600;
            background: #dbeafe;
            color: #1d4ed8;
        }

        .peso-filter-btn {
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen" x-data="adminPage()">

    {{-- ===== ADMIN TOP BAR ===== --}}
    <div class="admin-ribbon sticky top-0 z-50 px-6 py-2 flex items-center justify-between shadow-md">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <span class="text-white font-bold text-sm tracking-wide">ADMIN MODE — Programs & Stories Editor</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="/programs-and-stories" target="_blank"
                class="text-xs bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg font-semibold transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Preview Public Page
            </a>
            <span class="text-amber-100 text-xs">Layout Preview Only</span>
        </div>
    </div>

    @php
        $programColors = $programs->pluck('color', 'name');
        $programColorsByAcronym = $programs->whereNotNull('acronym')->pluck('color', 'acronym');
        $allProgramColorMap = $programColors->merge($programColorsByAcronym)->toJson();
    @endphp

    {{-- ===== CAROUSEL SECTION ===== --}}
    @php
        $programColorMap = [];
        foreach ($programs as $p) {
            $programColorMap[$p->name] = $p->color;
            if ($p->acronym) {
                $programColorMap[$p->acronym] = $p->color;
            }
        }
    @endphp

    <div class="relative w-full h-screen overflow-hidden" x-data="{
        currentSlide: 0,
        slides: {{ $carouselSlides->map(fn($s) => ['image' => asset($s->image_path), 'title' => $s->title, 'excerpt' => html_entity_decode(strip_tags($s->excerpt), ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'link' => $s->link, 'program' => $s->program_label, 'color' => $s->color, 'id' => $s->id])->toJson() }},
        autoplayInterval: null,
        nextSlide() { this.currentSlide = (this.currentSlide + 1) % this.slides.length; },
        prevSlide() { this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length; },
        goToSlide(index) { this.currentSlide = index; },
        startAutoplay() { this.autoplayInterval = setInterval(() => this.nextSlide(), 5000); },
        stopAutoplay() { clearInterval(this.autoplayInterval); }
    }" x-init="startAutoplay()"
        @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">

        <template x-for="(slide, index) in slides" :key="index">
            <div x-show="currentSlide === index" x-transition:enter="transition ease-out duration-700"
                x-transition:enter-start="opacity-0 transform translate-x-full"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-700"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-full" class="absolute inset-0">
                <div class="absolute inset-0">
                    <img :src="slide.image" :alt="slide.title" class="w-full h-full object-cover object-center">
                    <div class="absolute inset-0 bg-gradient-to-b from-slate-900/85 via-slate-900/70 to-slate-900/50">
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
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        :class="currentSlide === index ? 'bg-white border-white' : 'bg-white/40 border-white/60'"></div>
                </button>
            </template>
        </div>

        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20">
            <button @click="$dispatch('open-modal', { type: 'add-slide' })"
                class="plus-btn flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm rounded-full shadow-2xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Add Carousel Slide
            </button>
        </div>
    </div>
    {{-- ===== END CAROUSEL ===== --}}

    {{-- ===== PROGRAMS SECTION ===== --}}
    <div id="programs-section" class="max-w-7xl mx-auto px-4 sm:px-6 py-16">

        <div class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 rounded-2xl px-8 py-7 shadow-2xl mb-2">
            <div class="flex items-center justify-between">
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
                <button @click="$dispatch('open-modal', { type: 'add-program' })"
                    class="plus-btn flex items-center gap-2 px-5 py-2.5 bg-indigo-500 hover:bg-indigo-400 text-white font-bold text-sm rounded-xl shadow-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Program
                </button>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 divide-y divide-slate-200 overflow-hidden">

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
                @endphp

                <div x-data="{ open: false }">
                    <div class="relative program-row" style="--program-color: {{ $c['600'] }}">
                        <button @click="open = !open"
                            class="w-full px-6 md:px-10 py-6 flex items-center justify-between transition-colors duration-200 group text-left"
                            onmouseover="this.style.backgroundColor='{{ $c['50'] }}'"
                            onmouseout="this.style.backgroundColor=''">
                            <div class="flex items-center gap-5">
                                <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl flex-shrink-0 flex items-center justify-center shadow-md transition-all duration-300 border-2"
                                    :style="open ? 'background:white; border-color:{{ $c['400'] }}' :
                                        'background:{{ $c['50'] }}; border-color:transparent'">
                                    <img src="{{ asset($program->logo_path) }}" alt="{{ $program->name }} Logo"
                                        class="w-10 h-10 md:w-14 md:h-14 object-contain">
                                </div>
                                <div>
                                    <h3 class="text-xl md:text-2xl font-bold text-slate-900">
                                        <span
                                            class="program-name transition-colors duration-200">{{ $program->name }}</span>
                                    </h3>
                                    <p class="text-sm md:text-base text-slate-500 mt-1">{{ $program->subtitle }}</p>
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full mt-1.5
                                        {{ $program->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full {{ $program->is_published ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                        {{ $program->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0 ml-4">
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

                        {{-- Edit/Delete --}}
                        <div class="admin-actions absolute bottom-2 right-6 md:right-10 flex items-center gap-1.5 z-10"
                            @click.stop>
                            <button
                                @click="$dispatch('open-modal', { type: 'edit-program', id: {{ $program->id }}, data: {{ json_encode(['name' => $program->name, 'acronym' => $program->acronym, 'subtitle' => $program->subtitle, 'description' => $program->description, 'color' => $program->color]) }} })"
                                class="flex items-center gap-1 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white border border-indigo-200 hover:border-indigo-600 rounded-lg text-xs font-semibold transition shadow-sm">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </button>
                            <button
                                @click="$dispatch('open-modal', { type: 'delete-program', id: {{ $program->id }} })"
                                class="flex items-center gap-1 px-2.5 py-1 bg-red-50 hover:bg-red-600 text-red-500 hover:text-white border border-red-200 hover:border-red-600 rounded-lg text-xs font-semibold transition shadow-sm">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>

                    {{-- ACCORDION BODY --}}
                    <div x-show="open" x-collapse x-cloak>
                        <div class="border-t border-slate-200 p-6 md:p-10"
                            style="background: linear-gradient(to bottom right, #f8fafc, {{ $c['50'] }}33)">
                            <div class="grid lg:grid-cols-3 gap-8">

                                <div class="lg:col-span-2 space-y-6">

                                    {{-- Description --}}
                                    <div class="rounded-xl p-6 relative group/card border"
                                        style="background:{{ $c['50'] }}; border-color:{{ $c['200'] }}">
                                        <h4 class="font-bold text-slate-800 mb-3 flex items-center gap-2 text-lg">
                                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" style="color:{{ $c['600'] }}">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Program Details
                                        </h4>
                                        <div class="text-slate-700 leading-relaxed prose prose-sm max-w-none">
                                            {!! $program->description !!}
                                        </div>
                                        <span
                                            class="absolute top-3 right-3 flex gap-1 opacity-0 group-hover/card:opacity-100 transition-opacity">
                                            <button
                                                @click="$dispatch('open-modal', { type: 'edit-description', id: {{ $program->id }}, data: { description: {{ json_encode($program->description) }} } })"
                                                class="w-5 h-5 bg-indigo-100 hover:bg-indigo-500 text-indigo-500 hover:text-white rounded flex items-center justify-center transition">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button
                                                @click="$dispatch('open-modal', { type: 'delete-item', id: {{ $program->id }}, endpoint: '/programs/{{ $program->id }}/description' })"
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
                                            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                                                <div class="flex items-center justify-between mb-4">
                                                    <h4 class="font-bold text-slate-800 flex items-center gap-2">
                                                        <svg class="w-5 h-5 flex-shrink-0" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24"
                                                            style="color:{{ $c['600'] }}">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        {{ ucfirst($type) }}s
                                                    </h4>
                                                    <button
                                                        @click="$dispatch('open-modal', { type: 'add-qualification', programId: {{ $program->id }}, data: { defaultType: '{{ $type }}' } })"
                                                        class="plus-btn w-7 h-7 bg-indigo-100 hover:bg-indigo-600 text-indigo-600 hover:text-white rounded-full flex items-center justify-center shadow transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <ul class="space-y-3 text-slate-700 text-sm text-justify">
                                                    @foreach ($items as $q)
                                                        <li class="flex items-start gap-2 group/item">
                                                            <span class="font-bold mt-0.5"
                                                                style="color:{{ $c['500'] }}">•</span>
                                                            <span
                                                                class="flex-1 qual-content">{!! $q->content !!}</span>
                                                            <span
                                                                class="flex gap-1 opacity-0 group-hover/item:opacity-100 transition flex-shrink-0">
                                                                <button
                                                                    @click="$dispatch('open-modal', { type: 'edit-qualification', id: {{ $q->id }}, data: { type: '{{ $q->type }}', content: {{ json_encode($q->content) }} } })"
                                                                    class="w-5 h-5 bg-indigo-100 hover:bg-indigo-500 text-indigo-500 hover:text-white rounded flex items-center justify-center transition">
                                                                    <svg class="w-3 h-3" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                    </svg>
                                                                </button>
                                                                <button
                                                                    @click="$dispatch('open-modal', { type: 'delete-item', id: {{ $q->id }}, endpoint: '/qualifications/{{ $q->id }}' })"
                                                                    class="w-5 h-5 bg-red-100 hover:bg-red-500 text-red-500 hover:text-white rounded flex items-center justify-center transition">
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
                                                </ul>
                                            </div>
                                        @endforeach

                                        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                                            <div class="flex items-center justify-between mb-4">
                                                <h4 class="font-bold text-slate-800 flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
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
                                    <div class="text-white rounded-xl p-6" style="background:{{ $c['600'] }}">
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
                                                Add How to Apply
                                            </button>
                                        </div>
                                        <ol class="space-y-3 text-sm">
                                            @foreach ($program->howToApply as $step)
                                                <li class="flex items-start gap-3 group/item">
                                                    <span class="font-bold flex-shrink-0"
                                                        style="color:{{ $c['200'] }}">{{ $loop->iteration }}.</span>
                                                    <span class="flex-1">
                                                        {!! trim(preg_replace('/<\/?p[^>]*>/', ' ', $step->content)) !!}
                                                        @if ($step->link)
                                                            <a href="{{ $step->link }}" target="_blank"
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
                                                    <span
                                                        class="flex gap-1 opacity-0 group-hover/item:opacity-100 transition flex-shrink-0">
                                                        <button
                                                            @click="$dispatch('open-modal', { type: 'edit-step', id: {{ $step->id }}, data: { content: {{ json_encode($step->content) }}, link: {{ json_encode($step->link) }} } })"
                                                            class="w-5 h-5 bg-white/20 hover:bg-white text-white rounded flex items-center justify-center transition"
                                                            onmouseover="this.style.color='{{ $c['600'] }}'"
                                                            onmouseout="this.style.color='white'">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                        <button
                                                            @click="$dispatch('open-modal', { type: 'delete-item', id: {{ $step->id }}, endpoint: '/steps/{{ $step->id }}' })"
                                                            class="w-5 h-5 bg-white/20 hover:bg-red-500 text-white rounded flex items-center justify-center transition">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </div>

                                    {{-- SUCCESS STORIES CAROUSEL --}}
                                    <div x-data="storiesCarousel('{{ $programCarouselId }}', '{{ $c['600'] }}')" x-init="init()"
                                        id="{{ $programCarouselId }}-wrapper">

                                        <div class="flex items-center mb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-1 h-6 rounded-full flex-shrink-0"
                                                    style="background:{{ $c['600'] }}"></div>
                                                <h4 class="font-bold text-slate-800">{{ $program->name }} Success
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
                                                        stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path d="M15 19l-7-7 7-7" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="stories-carousel-outer">
                                                <div class="stories-carousel-track" :id="trackId">
                                                    @foreach ($program->stories as $story)
                                                        <div class="story-card-slide">
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
                                                                        @click.stop="$dispatch('open-modal', { type: 'edit-story', id: {{ $story->id }}, data: { title: {{ json_encode($story->title) }}, link: {{ json_encode($story->link) }} } })"
                                                                        class="w-6 h-6 bg-indigo-600 text-white rounded flex items-center justify-center shadow hover:bg-indigo-700 transition">
                                                                        <svg class="w-3 h-3" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                        </svg>
                                                                    </button>
                                                                    <button
                                                                        @click.stop="$dispatch('open-modal', { type: 'delete-item', id: {{ $story->id }}, endpoint: '/stories/{{ $story->id }}' })"
                                                                        class="w-6 h-6 bg-red-600 text-white rounded flex items-center justify-center shadow hover:bg-red-700 transition">
                                                                        <svg class="w-3 h-3" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M6 18L18 6M6 6l12 12" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="story-card-body">
                                                                <p class="story-card-title">{{ $story->title }}</p>
                                                                @if ($story->link)
                                                                    <a href="{{ $story->link }}" target="_blank"
                                                                        rel="noopener" class="story-card-link"
                                                                        style="color:{{ $c['600'] }}" @click.stop>
                                                                        Read →
                                                                        <svg class="w-3 h-3 opacity-60" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                                        </svg>
                                                                    </a>
                                                                @else
                                                                    <span
                                                                        class="text-xs font-medium mt-1 block opacity-40 italic"
                                                                        style="color:{{ $c['600'] }}">No link
                                                                        set</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    <button
                                                        @click="$dispatch('open-modal', { type: 'add-story', programId: {{ $program->id }} })"
                                                        class="story-add-slot">
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                        <span>Add Story</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="story-nav-floating right">
                                                <button @click="next()" :disabled="currentPage >= totalPages - 1"
                                                    class="story-nav-btn shadow-md"
                                                    style="border-color:{{ $c['200'] }}; color:{{ $c['600'] }}"
                                                    onmouseover="if(!this.disabled){this.style.background='{{ $c['600'] }}';this.style.color='white';this.style.borderColor='{{ $c['600'] }}'}"
                                                    onmouseout="if(!this.disabled){this.style.background='white';this.style.color='{{ $c['600'] }}';this.style.borderColor='{{ $c['200'] }}'}">
                                                    <svg width="12" height="12" fill="none"
                                                        stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
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
                                                        :style="i === currentPage ? '--dot-color:{{ $c['600'] }}' : ''">
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- END SUCCESS STORIES CAROUSEL --}}

                                </div>{{-- end lg:col-span-2 --}}

                                {{-- RIGHT: Testimonial --}}
                                <div class="lg:col-span-1">
                                    @if ($program->testimonial)
                                        <div class="bg-white rounded-xl p-6 shadow-lg sticky top-6 relative group/testimonial border-2"
                                            style="border-color:{{ $c['200'] }}">
                                            <div class="flex items-center gap-3 mb-6">
                                                <div class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0"
                                                    style="background:{{ $c['600'] }}">
                                                    <svg class="w-6 h-6 text-white" fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                                    </svg>
                                                </div>
                                                <h4 class="font-bold text-slate-900 text-lg">Success Story</h4>
                                            </div>
                                            <blockquote class="mb-6">
                                                <p class="text-slate-600 leading-relaxed italic text-sm">
                                                    "{!! trim(preg_replace('/<\/?p[^>]*>/', ' ', $program->testimonial->quote)) !!}"
                                                </p>
                                            </blockquote>
                                            <div class="flex items-center gap-3 pt-4 border-t"
                                                style="border-color:{{ $c['100'] }}">
                                                <div>
                                                    <p class="font-bold text-slate-900 text-sm">
                                                        {{ $program->testimonial->author_name }}</p>
                                                    <p class="text-xs text-slate-500">
                                                        {{ $program->testimonial->author_role }}</p>
                                                </div>
                                            </div>
                                            <span
                                                class="absolute top-3 right-3 flex gap-1 opacity-0 group-hover/testimonial:opacity-100 transition">
                                                <button
                                                    @click="$dispatch('open-modal', { type: 'edit-testimonial', id: {{ $program->testimonial->id }}, data: { quote: {{ json_encode($program->testimonial->quote) }}, author_name: {{ json_encode($program->testimonial->author_name) }}, author_role: {{ json_encode($program->testimonial->author_role) }} } })"
                                                    class="w-7 h-7 bg-indigo-50 hover:bg-indigo-500 text-indigo-500 hover:text-white border border-indigo-200 rounded-lg flex items-center justify-center transition shadow-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    @click="$dispatch('open-modal', { type: 'delete-item', id: {{ $program->testimonial->id }}, endpoint: '/testimonials/{{ $program->testimonial->id }}' })"
                                                    class="w-7 h-7 bg-red-50 hover:bg-red-500 text-red-400 hover:text-white border border-red-200 rounded-lg flex items-center justify-center transition shadow-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </div>
                                    @else
                                        <button
                                            @click="$dispatch('open-modal', { type: 'add-testimonial', programId: {{ $program->id }} })"
                                            class="add-zone w-full rounded-xl p-6 flex flex-col items-center justify-center gap-3 text-indigo-400 hover:text-indigo-600 min-h-[200px] transition">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span class="text-sm font-semibold">Add Testimonial</span>
                                        </button>
                                    @endif
                                </div>

                            </div>

                            {{-- PUBLISH FOOTER BAR --}}
                            <div class="mt-8 pt-6 border-t border-slate-200 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-2.5">
                                    <span
                                        class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ $program->is_published ? 'bg-emerald-500' : 'bg-amber-400' }}"></span>
                                    <p
                                        class="text-sm font-semibold {{ $program->is_published ? 'text-emerald-700' : 'text-amber-700' }}">
                                        {{ $program->is_published ? 'This program is live — visible to the public.' : 'This program is a draft — not visible to the public yet.' }}
                                    </p>
                                </div>
                                <button
                                    @click="$dispatch('open-modal', {
                                        type: '{{ $program->is_published ? 'unpublish-program' : 'publish-program' }}',
                                        id: {{ $program->id }},
                                        programName: {{ json_encode($program->name) }}
                                    })"
                                    class="flex-shrink-0 flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold text-sm transition shadow-sm
                                        {{ $program->is_published ? 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-300' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                                    @if ($program->is_published)
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                        Unpublish Program
                                    @else
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        ✓ Done — Publish to Public
                                    @endif
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            @endforeach

            <button @click="$dispatch('open-modal', { type: 'add-program' })"
                class="add-zone w-full px-10 py-8 flex items-center justify-center gap-3 text-indigo-400 hover:text-indigo-600 transition">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="font-semibold text-base">Add New Program</span>
            </button>

        </div>
    </div>
    {{-- ===== END PROGRAMS SECTION ===== --}}


    {{-- ═══════════════════════════════════════════════════════════════
         PESO & JPO DIRECTORY SECTION
    ═══════════════════════════════════════════════════════════════ --}}
    <div id="peso-jpo-section" class="max-w-7xl mx-auto px-4 sm:px-6 pb-16" x-data="{
        selectedProvince: null,
        filter: 'ALL',
    
        provinceConfig: {
            'DAVAO ORIENTAL': { activeBg: '#16a34a', activeShadow: '#bbf7d0', dotColor: '#16a34a', lightBg: '#f0fdf4', borderColor: '#bbf7d0', textColor: '#15803d' },
            'DAVAO DE ORO': { activeBg: '#d97706', activeShadow: '#fde68a', dotColor: '#d97706', lightBg: '#fffbeb', borderColor: '#fde68a', textColor: '#b45309' },
            'DAVAO DEL SUR': { activeBg: '#0284c7', activeShadow: '#bae6fd', dotColor: '#0284c7', lightBg: '#f0f9ff', borderColor: '#bae6fd', textColor: '#0369a1' },
            'DAVAO OCCIDENTAL': { activeBg: '#7c3aed', activeShadow: '#ddd6fe', dotColor: '#7c3aed', lightBg: '#f5f3ff', borderColor: '#ddd6fe', textColor: '#6d28d9' },
            'DAVAO DEL NORTE': { activeBg: '#e11d48', activeShadow: '#fecdd3', dotColor: '#e11d48', lightBg: '#fff1f2', borderColor: '#fecdd3', textColor: '#be123c' },
            'DAVAO CITY': { activeBg: '#0d9488', activeShadow: '#99f6e4', dotColor: '#0d9488', lightBg: '#f0fdfa', borderColor: '#99f6e4', textColor: '#0f766e' },
        },
    
        pesoData: {
            'DAVAO ORIENTAL': [
                { type: 'PESO', name: 'PESO DAVAO ORIENTAL', manager: 'Jay O. Dayanghirang', email: 'davorpeso@gmail.com', address: 'Provincial Capitol, Mati City, Davao Oriental' },
                { type: 'PESO', name: 'PESO MATI CITY', manager: 'Pauline Elsa F. Olita', email: 'olitapaulineelsa@gmail.com', address: 'Mati City, Davao Oriental' },
                { type: 'PESO', name: 'PESO BANAYBANAY', manager: 'Analie E. Nogaliza', email: 'marialindadalapalan@gmail.com', address: 'Municipality Hall, Poblacion, Banaybanay, Davao Oriental' },
                { type: 'PESO', name: 'PESO LUPON', manager: 'Anna Mariz R. Feliciados', email: 'mariz.feliciados@gmail.com', address: 'New Municipal Hall, National Highway, Bagumbayan, Lupon, Davao Oriental' },
                { type: 'PESO', name: 'PESO SAN ISIDRO', manager: 'Edgar S. Lingatong', email: 'pesosanisidro@gmail.com', address: 'San Isidro, Davao Oriental' },
                { type: 'PESO', name: 'PESO GOVERNOR GENEROSO', manager: 'Juvy P. Marimon', email: 'juvypandac1974@gmail.com', address: 'Governor Generoso, Davao Oriental' },
                { type: 'PESO', name: 'PESO MANAY', manager: 'Anniellou Joy D. Bitac', email: 'ajdbitac@yahoo.com', address: 'LGU Main Building, Manay, Davao Oriental' },
                { type: 'PESO', name: 'PESO CARAGA', manager: 'John Kenneth G. Mendoza', email: 'verdantlatency@gmail.com', address: 'DonLeon St., Poblacion, Caraga, Davao Oriental' },
                { type: 'PESO', name: 'PESO BAGANGA', manager: null, email: 'bagangapesooffice@gmail.com', address: 'Baganga, Davao Oriental' },
                { type: 'PESO', name: 'PESO CATEEL', manager: 'Bryan Ephraem E. Miguel', email: 'pesocateel@gmail.com', address: '2F Cateel Public Transport Terminal, Poblacion, Cateel, Davao Oriental' },
                { type: 'PESO', name: 'PESO BOSTON', manager: 'Danilo G. Brillantes', email: 'danilobrillantes@gmail.com', address: 'Municipal Hall, Poblacion, Boston, Davao Oriental' },
                { type: 'PESO', name: 'PESO TARRAGONA', manager: 'Nenita T. Carasca', email: 'pesotarragona@gmail.com', address: 'LGU Tarragona, Davao Oriental' },
                { type: 'JPO', name: 'JPO DAVAO ORIENTAL STATE UNIVERSITY', manager: 'Trishea Amor C. Jacobe', email: 'pesodoscst@gmail.com', address: 'City of Mati, Davao Oriental' },
                { type: 'JPO', name: 'JPO DON BOSCO TRAINING CENTER', manager: 'Nino A. Juarez', email: 'donboscomati12.peso@gmail.com', address: 'City of Mati, Davao Oriental' },
                { type: 'JPO', name: 'JPO CHRISTIAN ACADEMY OF DAVAO ORIENTAL, INC.', manager: 'Chrystie Paz A. Jandayan', email: 'pyaxz@yahoo.com', address: 'City of Mati, Davao Oriental' },
                { type: 'JPO', name: 'JPO SAINT MARY\'S COLLEGE OF BAGANGA', manager: 'Mariz M. Labos', email: 'smcbdo.guidance@gmail.com', address: 'Baganga, Davao Oriental' },
                { type: 'JPO', name: 'JPO MATI POLYTECHNIC COLLEGE', manager: 'Dr. Janice Bernadeth G. Agbong', email: 'polytechnicmati@yahoo.com', address: 'Don Mariano Marcos Ave., Brgy. Sainz, City of Mati, Davao Oriental' },
            ],
            'DAVAO DE ORO': [
                { type: 'PESO', name: 'PESO DAVAO DE ORO', manager: 'Miles A. Atugan', email: 'pesodavaodeoro@gmail.com', address: 'Cabidianan, Nabunturan, Davao de Oro' },
                { type: 'PESO', name: 'PESO COMPOSTELA', manager: 'Loreto Jr B Doydoy', email: 'pesocteccompostela@gmail.com', address: 'Poblacion, Compostela, Davao de Oro' },
                { type: 'PESO', name: 'PESO LAAK', manager: 'Philip D. Chiu', email: 'philipdchiu0667@gmail.com', address: 'Poblacion Laak, Davao de Oro' },
                { type: 'PESO', name: 'PESO MABINI', manager: 'Edwin Rey A. Carmelotes', email: 'mabiniddo.peso8807@gmail.com', address: 'Prk. Makugihon, Cuambog, Mabini, Davao de Oro' },
                { type: 'PESO', name: 'PESO MACO', manager: 'Concepcion C. Concha', email: 'hrmo_peso_maco@yahoo.com', address: 'Binuangan, Maco, Davao de Oro' },
                { type: 'PESO', name: 'PESO MARAGUSAN', manager: 'Eveliza W. Pantinople', email: 'peesomaragusan2025@gmail.com', address: 'Brgy. Poblacion, Maragusan' },
                { type: 'PESO', name: 'PESO MAWAB', manager: 'Anna Ayesa B. Arambala', email: 'peso_mawab@yahoo.com', address: 'Brgy. Poblacion, Mawab' },
                { type: 'PESO', name: 'PESO MONKAYO', manager: 'Aisa P. Tia', email: 'pesomonkayo@gmail.com', address: 'Poblacion Monkayo, Davao de Oro' },
                { type: 'PESO', name: 'PESO MONTEVISTA', manager: 'Walter C. Dalagan', email: 'peso.montevista123@gmail.com', address: 'Poblacion Montevista, Davao de Oro' },
                { type: 'PESO', name: 'PESO NABUNTURAN', manager: 'Marieta Calago', email: 'mayetcalago@gmail.com', address: 'Brgy. Magsaysay, Nabunturan, Davao de Oro' },
                { type: 'PESO', name: 'PESO NEW BATAAN', manager: 'Estrella L. Potenciano', email: 'pesonewbataan@gmail.com', address: 'Española Street, Cabinuangan, New Bataan, Davao de Oro' },
                { type: 'PESO', name: 'PESO PANTUKAN', manager: 'Nitchell A. Acedillo, DBA', email: 'pesopantukan2022@gmail.com', address: 'Towsite, Kingking, Pantukan, Davao de Oro' },
                { type: 'JPO', name: 'JPO ASSUMPTION COLLEGE OF NABUNTURAN', manager: 'Glydyl Dalire', email: 'glendabejona1@gmail.com', address: 'Panabo City' },
                { type: 'JPO', name: 'JPO DAVAO DE ORO STATE COLLEGE', manager: 'Judalyn J. Forro, MPsych', email: 'judalyn.forro@ddosc.edu.ph', address: 'Compostela, Davao de Oro' },
                { type: 'JPO', name: 'JPO MONKAYO COLLEGE OF ARTS, SCIENCES AND TECHNOLOGY', manager: 'Mike Bacaro', email: 'rhyrhy0521@gmail.com', address: 'Monkayo, Davao de Oro' },
                { type: 'JPO', name: 'JPO MACO DE ORO COLLEGE', manager: 'Dr. Anthony Pol Fulache', email: 'macodeorocollege2019@gmail.com', address: 'Prk. 3 Hijo, Maco, Davao de Oro' },
            ],
            'DAVAO DEL SUR': [
                { type: 'PESO', name: 'PESO DAVAO DEL SUR', manager: 'Rolly M. Impas, LPT, MBA, J.D', email: 'davaodelsurpeso@gmail.com', address: 'Provincial Capitol, Matti, Digos City, Davao del Sur' },
                { type: 'PESO', name: 'PESO DIGOS CITY', manager: 'Shany Lou R. Solatorio, MPA', email: 'digoscitypeso@gmail.com', address: 'Jose Abad Santos St., Digos City, Davao del Sur' },
                { type: 'PESO', name: 'PESO BANSALAN', manager: 'Cory B. Chatto, REA', email: 'corybaloriocha2@gmail.com', address: 'Bansalan, Davao del Sur' },
                { type: 'PESO', name: 'PESO HAGONOY', manager: 'Agnes C. Labadan', email: 'neslabadan70@gmail.com', address: 'Hagonoy, Davao del Sur' },
                { type: 'PESO', name: 'PESO KIBLAWAN', manager: 'Conteza May F. Senoy', email: 'tezafiel02@gmail.com', address: 'Kiblawan, Davao del Sur' },
                { type: 'PESO', name: 'PESO MAGSAYSAY', manager: 'Leonile P. Escarpe', email: 'magsaysaypeso@gmail.com', address: 'Magsaysay, Davao del Sur' },
                { type: 'PESO', name: 'PESO MALALAG', manager: 'Juanzo G. Calumpong', email: 'jcalumpong557@gmail.com', address: 'Malalag, Davao del Sur' },
                { type: 'PESO', name: 'PESO MATANAO', manager: 'Michael Mark B. Dela Victoria', email: 'mdelavictoria24@gmail.com', address: 'Matanao, Davao del Sur' },
                { type: 'PESO', name: 'PESO PADADA', manager: 'Mark Hill M. Delos Reyes', email: 'wilmeeghot901@gmail.com', address: 'Rizal St. NCO District, Padada, Davao del Sur' },
                { type: 'PESO', name: 'PESO STA. CRUZ', manager: 'Ma. Cheryl D. Serenatas', email: 'peso_stacruz@yahoo.com', address: 'Sta. Cruz, Davao del Sur' },
                { type: 'PESO', name: 'PESO SULOP', manager: 'Mary Ann Ferenal-Codilla', email: 'pesosulop07@gmail.com', address: 'Sulop, Davao del Sur' },
                { type: 'JPO', name: 'JPO COR JESU COLLEGE, INC.', manager: 'Jingle S. Navarez, MAED', email: 'jinglenavarez@g.cjc.edu.ph', address: 'Sacred Heart Ave., Digos City, Davao del Sur' },
                { type: 'JPO', name: 'JPO DAVAO DEL SUR STATE COLLEGE (SPAMAST DIGOS)', manager: 'Pearl Lettee D. Maunes, MBA', email: 'eraaodigos@umindanao.edu.ph', address: '3361, Jose Abad Santos St., Digos City, Davao del Sur' },
                { type: 'JPO', name: 'JPO DAVAO DEL SUR STATE COLLEGE (MATTI CAMPUS)', manager: 'Arnie B. Grajo', email: 'jpo@dssc.edu.ph', address: 'Matti, Digos City, Davao del Sur' },
                { type: 'JPO', name: 'JPO POLYTECHNIC COLLEGE OF DAVAO DEL SUR', manager: 'Dr. Jesusa E. Trinidad, RST, RGC', email: 'lady-ams80@yahoo.com', address: 'Digos City, Davao del Sur' },
            ],
            'DAVAO OCCIDENTAL': [
                { type: 'PESO', name: 'PESO DAVAO OCCIDENTAL', manager: 'Wilson G. Monesit', email: 'pesodavaooccidentall@gmail.com', address: 'Poblacion, Malita, Davao Occidental' },
                { type: 'PESO', name: 'PESO DON MARCELINO', manager: 'Jorge D. Gildore', email: 'jorge.gildore@gmail.com', address: 'Brgy. Lawa, Don Marcelino, Davao Occidental' },
                { type: 'PESO', name: 'PESO JOSE ABAD SANTOS', manager: 'Winnie P. Malanas', email: 'winniemalanas43@gmail.com', address: 'Caburan Small, Jose Abad Santos, Davao Occidental' },
                { type: 'PESO', name: 'PESO MALITA', manager: 'Stelito M. Jumaran', email: 'pesomalitalgu@gmail.com', address: 'Poblacion, Malita, Davao Occidental' },
                { type: 'PESO', name: 'PESO STA. MARIA', manager: 'Angelo D. Carr', email: 'pesostamaria907@gmail.com', address: 'Poblacion, Santa Maria, Davao Occidental' },
                { type: 'PESO', name: 'PESO SARANGANI', manager: 'Reaggie Nolan L. Regino', email: 'pesodavaocc@gmail.com', address: 'Mabila, Sarangani, Davao Occidental' },
                { type: 'JPO', name: 'JPO SPAMAST', manager: 'Deanne Abigail C. Bugawisan', email: 'm.depalubos@spamast.edu.ph', address: 'Malita, Davao Occidental' },
            ],
            'DAVAO DEL NORTE': [
                { type: 'PESO', name: 'PESO DAVAO DEL NORTE', manager: 'Gloria Excelsa S. Pamugas', email: 'ddnpesolmi@gmail.com', address: 'DavNor TechVoc Center, Provincial Government Center, Mankilam, Tagum City' },
                { type: 'PESO', name: 'PESO TAGUM CITY', manager: 'Mae-Ann M. Ang', email: 'tagumpeso@gmail.com', address: 'New City Hall, Apokon, Tagum City' },
                { type: 'PESO', name: 'PESO PANABO CITY', manager: 'Cherelle B. Espinosa', email: 'pesopanabocity@gmail.com', address: 'City Parks and Plaza, Brgy. New Pandan, Panabo City' },
                { type: 'PESO', name: 'PESO ISLAND GARDEN CITY OF SAMAL (IGACOS)', manager: 'Ana Fe C. Luga', email: 'samalpeso@gmail.com', address: 'New City Hall Building, Sitio Maag, Barangay Peñaplata, Samal District, Island Garden City of Samal' },
                { type: 'PESO', name: 'PESO ASUNCION', manager: 'Eufronia J. Mangle, LPT', email: 'pesoasuncion@gmail.com', address: 'Purok 4, Cambanogoy, Asuncion, Davao del Norte' },
                { type: 'PESO', name: 'PESO BRAULIO E. DUJALI', manager: 'Allan S. Paraguya', email: 'pesodujali@gmail.com', address: 'Prk.6 Pob. Dujali, Braulio E. Dujali, Davao del Norte' },
                { type: 'PESO', name: 'PESO CARMEN', manager: 'John Kevin M. Amador', email: 'justpesocarmen@yahoo.com', address: 'Purok 7, Ising (Pob.), Carmen, Davao del Norte' },
                { type: 'PESO', name: 'PESO KAPALONG', manager: 'Delia Ramos Pernites', email: 'pesokapalongddn@gmail.com', address: 'Maniki, Kapalong, Davao del Norte' },
                { type: 'PESO', name: 'PESO NEW CORELLA', manager: 'Al B. Biotumas', email: 'pesonewcorella@gmail.com', address: 'Purok 2, Poblacion, New Corella' },
                { type: 'PESO', name: 'PESO SAN ISIDRO', manager: 'Emily D. Cabanlit', email: 'jash_eryne_triff@yahoo.com', address: 'Prk. Cadena De Amor, Visayan Village, Tagum City' },
                { type: 'PESO', name: 'PESO STO. TOMAS', manager: 'Magierose M. Flores', email: 'pesotomas23@gmail.com', address: 'Feeder Road 3, Barangay Tibal-og, Santo Tomas, Davao del Norte' },
                { type: 'PESO', name: 'PESO TALAINGOD', manager: 'Edwin Superioridad', email: 'pesotalaingod72@gmail.com', address: 'Balimba Hills, Talaingod, Davao del Norte' },
                { type: 'JPO', name: 'JPO ST. MARY\'S COLLEGE OF TAGUM', manager: 'Virginia L. Cordoves', email: 'virginia_cordoves@yahoo.com', address: 'St. Mary\'s College of Tagum, Inc., Tagum City' },
                { type: 'JPO', name: 'JPO UNIVERSITY OF MINDANAO TAGUM COLLEGE', manager: 'Ansona C. Arboiz', email: 'eraaotagum@umindanao.edu.ph', address: 'Mabini Street, Tagum City' },
                { type: 'JPO', name: 'JPO UNIVERSITY OF MINDANAO PANABO COLLEGE', manager: 'Glezer V. Niez', email: 'eraaopanabo@umindanao.edu.ph', address: 'Sto. Nino, Panabo City, DDN' },
                { type: 'JPO', name: 'JPO ACES POLYTECHNIC COLLEGE', manager: 'Mariefe S. Pospos', email: 'acespanabo.jpo@gmail.com', address: 'Brgy. San Francisco, Panabo City' },
                { type: 'JPO', name: 'JPO ACES TAGUM COLLEGE', manager: 'Alfonso R. Ventures, MSIS', email: 'atcdavnor@gmail.com', address: 'Mankilam, Tagum City, Davao del Norte' },
                { type: 'JPO', name: 'JPO DAVAO DEL NORTE STATE COLLEGE', manager: 'Maricielo Paula E. Funa', email: 'jpo@dnsc.edu.ph', address: 'Brgy. New Visayas, Panabo City' },
                { type: 'JPO', name: 'JPO KAPALONG COLLEGE OF AGRICULTURE, SCIENCES AND TECHNOLOGY', manager: 'Ronel G. Dagohoy, DPA', email: 'jpo@kcast.edu.ph', address: 'Maniki, Kapalong, Davao del Norte' },
                { type: 'JPO', name: 'JPO STO. TOMAS COLLEGE OF AGRICULTURE, SCIENCE AND TECHNOLOGY', manager: 'Anilyn C. Aguspina', email: 'anilynstcastjpo@gmail.com', address: 'STCAST, Santo Tomas, DDN' },
                { type: 'JPO', name: 'JPO SAMAL ISLAND CITY COLLEGE', manager: 'Richard S. Jimenez, RGC, RPm', email: 'richard.filuga@yahoo.com', address: 'Datu Taganiog St., Brgy. Peñaplata, Samal District, Island Garden City of Samal' },
                { type: 'JPO', name: 'JPO NORTHLINK TECHNOLOGICAL COLLEGE INC.', manager: 'Geanica N. Garcia, RPm', email: 'geagarcia@northlink.edu.ph', address: 'Along Nat\'l Highway, Brgy. New Pandan, Panabo City' },
                { type: 'JPO', name: 'JPO UM PEÑAPLATA COLLEGE', manager: 'Hector B. Aguilar', email: 'eraaopenaplata@umindanao.edu.ph', address: 'OBENZA St. Peñaplata, IGaCoS' },
            ],
            'DAVAO CITY': [
                { type: 'PESO', name: 'PESO DAVAO CITY', manager: 'Lilibeth D. Pantinople', email: 'peso@davaocity.gov.ph', address: 'Davao City Recreational Center (Formerly Almendras Gym), Quimpo Boulevard, Davao City' },
                { type: 'PESO', name: 'AGDAO DISTRICT', manager: 'Erwin W. Cagape', email: 'peso@davaocity.gov.ph', address: 'Agdao District Hall, Lapu Lapu St., Davao City' },
                { type: 'PESO', name: 'TALOMO DISTRICT', manager: 'Renato S. Salazar', email: 'peso@davaocity.gov.ph', address: '74-A, Matina Crossing, Brgy. Hall, Davao City' },
                { type: 'PESO', name: 'CALINAN DISTRICT', manager: 'Roselyn C. Tahil', email: 'peso@davaocity.gov.ph', address: 'Near Calinan, Brgy. Hall, Calinan District, Davao City' },
                { type: 'PESO', name: 'BUNAWAN DISTRICT', manager: 'Camille Bianca E. Sanchez', email: 'peso@davaocity.gov.ph', address: 'Km. 23 Bunawan District Office' },
                { type: 'PESO', name: 'TUGBOK DISTRICT', manager: 'Charisse E. Tolentino', email: 'peso@davaocity.gov.ph', address: 'Mintal District Hall, Gumamela St., Davao City' },
                { type: 'PESO', name: 'BAGUIO DISTRICT', manager: 'Alma A. Arancis', email: 'peso@davaocity.gov.ph', address: 'PESO Baguio District, Baguio Proper, Davao City' },
                { type: 'PESO', name: 'BUHANGIN DISTRICT', manager: 'Shirly D. Sahid', email: 'peso@davaocity.gov.ph', address: 'Buhangin Gym, Buhangin Proper, Davao City' },
                { type: 'PESO', name: 'TORIL DISTRICT', manager: 'Melina I. Isidro', email: 'peso@davaocity.gov.ph', address: 'Toril District Hall, Agton St., Toril Proper, Davao City' },
                { type: 'JPO', name: 'JPO UNIVERSITY OF MINDANAO (BOLTON CAMPUS)', manager: 'Reynaldo C. Castro', email: 'studentdev@umindanao.edu.ph', address: 'Bolton St., Poblacion District, Davao City' },
                { type: 'JPO', name: 'JPO HOLY CROSS OF DAVAO COLLEGE', manager: 'Romil L. Torrejos', email: 'externalrelations@hcdc.edu.ph', address: 'Sta. Ana Avenue cor. C. de Guzman St., Brgy. 14-B, Davao City' },
                { type: 'JPO', name: 'JPO UNIVERSITY OF SOUTHEASTERN PHILIPPINES', manager: 'Dr. Alma Mae G. Salinas', email: 'cac@usep.edu.ph', address: 'Iñigo St., Bo. Obrero, Davao City' },
                { type: 'JPO', name: 'JPO MAPÚA MALAYAN COLLEGES MINDANAO', manager: 'Trisha Mae A. Menoy', email: 'ccp@mcm.edu.ph', address: 'Gen. Douglas MacArthur Hwy, Talomo, Davao City' },
            ],
        },
    
        get provinces() { return Object.keys(this.pesoData); },
    
        get cfg() {
            return this.selectedProvince ? this.provinceConfig[this.selectedProvince] : null;
        },
    
        get offices() {
            if (!this.selectedProvince) return [];
            const all = this.pesoData[this.selectedProvince];
            return this.filter === 'ALL' ? all : all.filter(o => o.type === this.filter);
        },
    
        get pesoCount() {
            return this.selectedProvince ? this.pesoData[this.selectedProvince].filter(o => o.type === 'PESO').length : 0;
        },
    
        get jpoCount() {
            return this.selectedProvince ? this.pesoData[this.selectedProvince].filter(o => o.type === 'JPO').length : 0;
        },
    
        selectProvince(prov) {
            this.selectedProvince = (this.selectedProvince === prov) ? null : prov;
            this.filter = 'ALL';
        },
    
        expandedCard: null,
        toggleCard(idx) {
            this.expandedCard = (this.expandedCard === idx) ? null : idx;
        },
    }">

        {{-- Section Header --}}
        <div class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 rounded-2xl px-8 py-7 shadow-2xl mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-white font-bold text-2xl md:text-3xl">PESO & JPO Directory</h2>
                    <p class="text-slate-300 text-sm md:text-base">Public Employment Service Offices & Job Placement
                        Offices — Region XI</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
            <div class="p-6">

                {{-- Province selector label --}}
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Select a Province / City</p>

                {{-- Province Buttons Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
                    <template x-for="prov in provinces" :key="prov">
                        <button @click="selectProvince(prov)"
                            :style="selectedProvince === prov ?
                                `background:${provinceConfig[prov].activeBg}; color:white; border-color:${provinceConfig[prov].activeBg}; box-shadow:0 3px 10px ${provinceConfig[prov].activeShadow}` :
                                `background:white; color:${provinceConfig[prov].textColor}; border-color:${provinceConfig[prov].borderColor}`"
                            class="peso-province-btn">
                            <span
                                :style="selectedProvince === prov ?
                                    'background:rgba(255,255,255,0.6)' :
                                    `background:${provinceConfig[prov].dotColor}`"
                                style="width:8px;height:8px;border-radius:50%;flex-shrink:0;display:inline-block;"></span>
                            <span class="leading-snug text-left" x-text="prov"></span>
                        </button>
                    </template>
                </div>

                {{-- Province Office Panel --}}
                <div x-show="selectedProvince && cfg" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0">

                    {{-- Sub-header with counts + filter --}}
                    <div class="rounded-xl p-4 mb-4 flex flex-wrap items-center justify-between gap-3"
                        :style="`background:${cfg ? cfg.lightBg : '#f8fafc'}; border:1px solid ${cfg ? cfg.borderColor : '#e2e8f0'}`">
                        <div>
                            <h3 class="font-bold text-slate-900 text-base" x-text="selectedProvince"></h3>
                            <div class="flex gap-4 mt-1">
                                <span class="text-xs text-slate-500">
                                    <strong class="text-green-600" x-text="pesoCount"></strong> PESO Offices
                                </span>
                                <span class="text-xs text-slate-500">
                                    <strong class="text-blue-600" x-text="jpoCount"></strong> JPO Offices
                                </span>
                            </div>
                        </div>
                        {{-- Filter Pills --}}
                        <div class="flex gap-1 bg-white rounded-lg p-1 border border-slate-200">
                            <template x-for="f in ['ALL','PESO','JPO']" :key="f">
                                <button @click="filter = f; expandedCard = null" class="peso-filter-btn"
                                    :style="filter === f ? 'background:#1e293b; color:white' :
                                        'background:transparent; color:#64748b'"
                                    x-text="f">
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Office Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                        <template x-for="(office, idx) in offices" :key="idx">
                            <div class="peso-office-card" :class="expandedCard === idx ? 'expanded' : ''"
                                :style="expandedCard === idx ?
                                    `border-color:${cfg.borderColor}; background:${cfg.lightBg}` :
                                    'border-color:#f1f5f9; background:#ffffff'">

                                {{-- Card Header / Toggle --}}
                                <button @click="toggleCard(idx)"
                                    class="w-full flex items-center justify-between p-3.5 text-left"
                                    style="background:none; border:none; cursor:pointer;">
                                    <div class="flex items-center gap-3 min-w-0">
                                        {{-- Icon --}}
                                        <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center text-white text-xs font-bold"
                                            :style="office.type === 'PESO' ? 'background:#16a34a' : 'background:#2563eb'">
                                            <span x-text="office.type === 'PESO' ? 'P' : 'J'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-800 truncate"
                                                x-text="office.name"></p>
                                            <p x-show="office.manager" class="text-xs text-slate-400 truncate"
                                                x-text="office.manager"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                        <span
                                            :class="office.type === 'PESO' ? 'peso-type-badge-p' : 'peso-type-badge-j'"
                                            x-text="office.type"></span>
                                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform duration-200"
                                            :style="expandedCard === idx ? 'transform:rotate(180deg)' : ''"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </button>

                                {{-- Expanded Details --}}
                                <div x-show="expandedCard === idx" x-transition class="px-4 pb-4 grid gap-3"
                                    :style="`border-top:1px solid ${cfg.borderColor}`">

                                    <div x-show="office.manager" class="flex items-start gap-2.5 pt-3">
                                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <div>
                                            <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold"
                                                x-text="office.type === 'PESO' ? 'PESO Manager' : 'JPO Manager'"></p>
                                            <p class="text-sm text-slate-700" x-text="office.manager"></p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-2.5">
                                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <div>
                                            <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">
                                                Email Address</p>
                                            <a :href="`mailto:${office.email}`"
                                                class="text-sm text-blue-600 hover:underline break-all"
                                                x-text="office.email"></a>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-2.5">
                                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <div>
                                            <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">
                                                Address</p>
                                            <p class="text-sm text-slate-700" x-text="office.address"></p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Empty state --}}
                    <p x-show="offices.length === 0" class="text-center text-slate-400 text-sm py-10">No offices
                        found.</p>

                </div>

                {{-- Empty state: no province selected --}}
                <div x-show="!selectedProvince" class="text-center py-12 text-slate-300">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <p class="text-sm font-medium text-slate-400">Select a province above to view its PESO and JPO
                        offices.</p>
                </div>

            </div>
        </div>
    </div>
    {{-- ═══════════════════════════════════════════════════════════════
         END PESO & JPO DIRECTORY SECTION
    ═══════════════════════════════════════════════════════════════ --}}


    {{-- CTA SECTION --}}
    <div class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 py-20 mt-16 relative group/cta">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h3 class="text-4xl font-bold text-white mb-6">Ready to Start Your Journey?</h3>
            <p class="text-slate-300 text-xl mb-10 max-w-3xl mx-auto">Join thousands of youth who have transformed
                their careers through DOLE's employment programs.</p>
            <div class="flex flex-wrap justify-center gap-6">
                <a href="http://gip.dole11portal.org" target="_blank"
                    class="px-10 py-5 bg-white text-slate-900 font-bold text-lg rounded-xl hover:bg-slate-100 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">Apply
                    for GIP</a>
                <a href="#"
                    class="px-10 py-5 bg-transparent border-2 border-white text-white font-bold text-lg rounded-xl hover:bg-white hover:text-slate-900 transition-all duration-300 shadow-xl">Visit
                    Your Local PESO</a>
            </div>
        </div>
        <button @click="$dispatch('open-modal', { type: 'edit-cta' })"
            class="plus-btn absolute top-4 right-4 flex items-center gap-2 px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg shadow transition opacity-0 group-hover/cta:opacity-100">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit CTA
        </button>
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
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div x-show="modal.error" x-cloak class="mx-6 mt-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-600 text-sm" x-text="modal.error"></p>
            </div>

            <div class="px-6 py-6">

                {{-- ADD / EDIT SLIDE --}}
                <div x-show="modal.type === 'add-slide' || modal.type === 'edit-slide'" x-cloak class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Story Title</label>
                        <input type="text" x-model="form.title"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Short Excerpt</label>
                        <div id="quill-excerpt" class="rounded-lg border border-slate-300"></div>
                        <div class="mt-1 text-xs text-slate-400"><span id="quill-excerpt-wordcount">0</span> words
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Program Label</label>
                            <input type="text" x-model="form.program_label" list="slide-program-label-options"
                                placeholder="e.g. GIP"
                                @input="
                                    const map = {{ json_encode($programColorMap) }};
                                    const match = Object.entries(map).find(([k]) => k.toLowerCase() === form.program_label.toLowerCase());
                                    if (match) form.color = match[1];
                                "
                                @change="
                                    const map = {{ json_encode($programColorMap) }};
                                    const match = Object.entries(map).find(([k]) => k.toLowerCase() === form.program_label.toLowerCase());
                                    if (match) form.color = match[1];
                                "
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
                            Slide Image <span x-show="modal.type === 'edit-slide'"
                                class="text-slate-400 font-normal">(leave blank to keep current)</span>
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
                                form.image = $event.target.files[0];
                                if ($event.target.files[0]) {
                                    const reader = new FileReader();
                                    reader.onload = e => form.image_preview = e.target.result;
                                    reader.readAsDataURL($event.target.files[0]);
                                }
                            "
                            class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm" />
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
                <div x-show="modal.type === 'add-program' || modal.type === 'edit-program'" x-cloak class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Program Name</label>
                        <input type="text" x-model="form.name"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Acronym <span class="text-slate-400 font-normal">(optional)</span>
                        </label>
                        <input type="text" x-model="form.acronym" placeholder="e.g. GIP"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                        <p class="text-xs text-slate-400 mt-1">Shown on story cards instead of the full program name.
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
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Program Description</label>
                        <div id="quill-program" class="rounded-lg border border-slate-300"></div>
                        <div class="mt-1 text-xs text-slate-400"><span id="quill-program-wordcount">0</span> words
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Theme Color</label>
                        <div class="grid grid-cols-7 gap-2">
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
                                <button type="button" @click="form.color = c.name" :title="c.label"
                                    class="relative w-8 h-8 rounded-full transition-all duration-150 focus:outline-none hover:scale-110"
                                    :class="[c.bg, form.color === c.name ? 'ring-2 ring-offset-2 scale-110 shadow-lg ' + c
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
                        <p class="text-xs text-slate-400 mt-2">Selected: <span
                                class="font-semibold text-slate-600 capitalize" x-text="form.color || '—'"></span></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Logo <span x-show="modal.type === 'edit-program'"
                                class="text-slate-400 font-normal">(leave blank to keep current)</span>
                        </label>
                        <input type="file" accept="image/*" @change="form.logo = $event.target.files[0]"
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
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Program Description</label>
                        <div id="quill-description" class="rounded-lg border border-slate-300"></div>
                        <div class="mt-1 text-xs text-slate-400"><span id="quill-description-wordcount">0</span> words
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
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Content</label>
                        <div id="quill-qualification" class="rounded-lg border border-slate-300"></div>
                        <div class="mt-1 text-xs text-slate-400"><span id="quill-qualification-wordcount">0</span>
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
                <div x-show="modal.type === 'add-step' || modal.type === 'edit-step'" x-cloak class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Step Content</label>
                        <div id="quill-step" class="rounded-lg border border-slate-300"></div>
                        <div class="mt-1 text-xs text-slate-400"><span id="quill-step-wordcount">0</span> words</div>
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
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Story Title</label>
                            <input type="text" x-model="form.title"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Story Link</label>
                            <input type="url" x-model="form.link" placeholder="https://..."
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Thumbnail Image <span x-show="modal.type === 'edit-story'"
                                    class="text-slate-400 font-normal">(leave blank to keep current)</span>
                            </label>
                            <input type="file" accept="image/*" @change="form.image = $event.target.files[0]"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm" />
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
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Quote</label>
                        <div id="quill-quote" class="rounded-lg border border-slate-300"></div>
                        <div class="mt-1 text-xs text-slate-400"><span id="quill-quote-wordcount">0</span> words</div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Name</label>
                            <input type="text" x-model="form.author_name"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Program Name</label>
                            <input type="text" x-model="form.author_role" placeholder="e.g. GIP Beneficiary"
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

                {{-- DELETE CONFIRMATION --}}
                <template
                    x-if="modal.type === 'delete-slide' || modal.type === 'delete-program' || modal.type === 'delete-item'">
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
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
                        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto">
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
                        <p class="text-slate-500 text-sm">This will make the program visible to the public on the
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

                {{-- UNPUBLISH CONFIRMATION MODAL --}}
                <template x-if="modal.type === 'unpublish-program'">
                    <div class="text-center space-y-4">
                        <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </div>
                        <p class="text-slate-800 font-bold text-lg">Unpublish "<span
                                x-text="modal.programName"></span>"?</p>
                        <p class="text-slate-500 text-sm">This will hide the program from the public page. You can
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
        /* ══════════════════════════════════════════════════════
                       STORIES CAROUSEL — Alpine component factory
                    ══════════════════════════════════════════════════════ */
        function storiesCarousel(wrapperId, accentColor) {
            return {
                wrapperId,
                accentColor,
                trackId: wrapperId + '-track',
                currentPage: 0,
                totalPages: 1,
                PER_PAGE: 5,

                init() {
                    this.$nextTick(() => {
                        this.recalc();
                        window.addEventListener('resize', () => this.recalc());

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
                                setTimeout(() => {
                                    _wheelLocked = false;
                                }, 500);
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

                recalc() {
                    const track = document.getElementById(this.trackId);
                    if (!track) return;
                    const cards = track.querySelectorAll('.story-card-slide, .story-add-slot');
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
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(body),
                });
                return res.json();
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
                const res = await fetch(url, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                    body: fd,
                });
                return res.json();
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
                form: {},

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
                        loading: false,
                        error: null
                    };
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
                    location.reload();
                },
                fail(msg) {
                    this.modal.loading = false;
                    this.modal.error = msg || 'Something went wrong. Please try again.';
                },

                async submitSlide() {
                    this.modal.loading = true;
                    this.modal.error = null;
                    const isEdit = this.modal.type === 'edit-slide';
                    const data = {
                        title: this.form.title,
                        excerpt: this.form.excerpt,
                        program_label: this.form.program_label,
                        color: this.form.color,
                        link: this.form.link
                    };
                    if (this.form.image) data.image = this.form.image;
                    const res = await formRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/carousel/${this.modal.id}` :
                        '/carousel', data);
                    res.success ? this.done() : this.fail(res.message);
                },

                async submitProgram() {
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
                    const res = await formRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/programs/${this.modal.id}` :
                        '/programs', data);
                    res.success ? this.done() : this.fail(res.message);
                },

                async submitDescription() {
                    this.modal.loading = true;
                    this.modal.error = null;
                    const res = await jsonRequest('PUT', `/programs/${this.modal.id}/description`, {
                        description: this.form.description
                    });
                    res.success ? this.done() : this.fail(res.message);
                },

                async submitQualification() {
                    this.modal.loading = true;
                    this.modal.error = null;
                    const isEdit = this.modal.type === 'edit-qualification';
                    const tmp = document.createElement('div');
                    tmp.innerHTML = this.form.content || '';
                    const listItems = tmp.querySelectorAll('li');
                    if (!isEdit && listItems.length > 1) {
                        for (const li of listItems) {
                            await jsonRequest('POST', '/qualifications', {
                                type: this.form.type,
                                content: li.innerHTML,
                                program_id: this.modal.programId
                            });
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
                    const res = await jsonRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/qualifications/${this.modal.id}` :
                        '/qualifications', body);
                    res.success ? this.done() : this.fail(res.message);
                },

                async submitStep() {
                    this.modal.loading = true;
                    this.modal.error = null;
                    const isEdit = this.modal.type === 'edit-step';
                    const body = {
                        content: this.form.content,
                        link: this.form.link || null
                    };
                    if (!isEdit) body.program_id = this.modal.programId;
                    const res = await jsonRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/steps/${this.modal.id}` :
                        '/steps', body);
                    res.success ? this.done() : this.fail(res.message);
                },

                async submitStory() {
                    this.modal.loading = true;
                    this.modal.error = null;
                    const isEdit = this.modal.type === 'edit-story';
                    const data = {
                        title: this.form.title,
                        link: this.form.link
                    };
                    if (!isEdit) data.program_id = this.modal.programId;
                    if (this.form.image) data.image = this.form.image;
                    const res = await formRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/stories/${this.modal.id}` :
                        '/stories', data);
                    res.success ? this.done() : this.fail(res.message);
                },

                async submitTestimonial() {
                    this.modal.loading = true;
                    this.modal.error = null;
                    const isEdit = this.modal.type === 'edit-testimonial';
                    const body = {
                        quote: this.form.quote,
                        author_name: this.form.author_name,
                        author_role: this.form.author_role
                    };
                    if (!isEdit) body.program_id = this.modal.programId;
                    const res = await jsonRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/testimonials/${this.modal.id}` :
                        '/testimonials', body);
                    res.success ? this.done() : this.fail(res.message);
                },

                async submitDelete() {
                    this.modal.loading = true;
                    this.modal.error = null;
                    let url;
                    if (this.modal.type === 'delete-slide') url = `/carousel/${this.modal.id}`;
                    else if (this.modal.type === 'delete-program') url = `/programs/${this.modal.id}`;
                    else url = this.modal.endpoint;
                    const res = await jsonRequest('DELETE', url);
                    res.success ? this.done() : this.fail(res.message);
                },

                async submitTogglePublish() {
                    this.modal.loading = true;
                    this.modal.error = null;
                    const res = await jsonRequest('PATCH', `/programs/${this.modal.id}/toggle-publish`);
                    res.success ? this.done() : this.fail(res.message);
                },
            };
        }
    </script>

</body>

</html>
