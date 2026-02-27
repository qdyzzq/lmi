<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    </style>
</head>

<body class="bg-slate-100 min-h-screen" x-data="adminPage()">

    {{-- ===== OUTER LAYOUT WRAPPER ===== --}}
    <div class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR ===== --}}
    @include('partials.sidebar')

    {{-- ===== MAIN CONTENT AREA ===== --}}
    <div class="flex-1 flex flex-col overflow-y-auto">

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

    {{-- ===== CAROUSEL SECTION ===== --}}
    <div class="relative w-full shrink-0 overflow-hidden" style="height: calc(100vh - 42px);" x-data="{
        currentSlide: 0,
        slides: {{ $carouselSlides->map(fn($s) => ['image' => asset($s->image_path), 'title' => $s->title, 'excerpt' => $s->excerpt, 'link' => $s->link, 'program' => $s->program_label, 'color' => $s->color, 'id' => $s->id])->toJson() }},
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
                    <div class="text-center text-white max-w-5xl">
                        <div class="inline-block mb-6">
                            <span class="px-6 py-3 rounded-full text-base font-bold backdrop-blur-md shadow-2xl"
                                :class="{
                                    'bg-green-500/40 border-2 border-green-300/60': slide.color === 'green',
                                    'bg-red-500/40 border-2 border-red-300/60': slide.color === 'red',
                                    'bg-blue-500/40 border-2 border-blue-300/60': slide.color === 'blue',
                                    'bg-yellow-500/40 border-2 border-yellow-300/60': slide.color === 'yellow',
                                    'bg-cyan-500/40 border-2 border-cyan-300/60': slide.color === 'cyan'
                                }"
                                x-text="slide.program + ' Success Story'"></span>
                        </div>
                        <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold mb-8 drop-shadow-2xl leading-tight"
                            x-text="slide.title"></h1>
                        <p class="text-xl md:text-2xl lg:text-3xl text-slate-50 drop-shadow-lg mb-12 max-w-4xl mx-auto leading-relaxed font-light"
                            x-text="slide.excerpt"></p>
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
                @endphp

                <div x-data="{ open: false }">
                    <div class="relative program-row">
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
                                    <h3 class="text-xl md:text-2xl font-bold text-slate-900 transition-colors"
                                        :style="open ? 'color:{{ $c['600'] }}' : ''">
                                        {{ $program->name }}
                                    </h3>
                                    <p class="text-sm md:text-base text-slate-500 mt-1">{{ $program->subtitle }}</p>
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
                                @click="$dispatch('open-modal', { type: 'edit-program', id: {{ $program->id }}, data: {{ json_encode(['name' => $program->name, 'subtitle' => $program->subtitle, 'description' => $program->description, 'color' => $program->color]) }} })"
                                class="flex items-center gap-1 px-2.5 py-1 bg-indigo-100 hover:bg-indigo-600 text-indigo-600 hover:text-white rounded-lg text-xs font-semibold transition shadow-sm">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </button>
                            <button
                                @click="$dispatch('open-modal', { type: 'delete-program', id: {{ $program->id }} })"
                                class="flex items-center gap-1 px-2.5 py-1 bg-red-100 hover:bg-red-600 text-red-500 hover:text-white rounded-lg text-xs font-semibold transition shadow-sm">
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
                                        <p class="text-slate-700 leading-relaxed">{{ $program->description }}</p>
                                        <button
                                            @click="$dispatch('open-modal', { type: 'edit-description', id: {{ $program->id }}, data: { description: {{ json_encode($program->description) }} } })"
                                            class="plus-btn absolute top-3 right-3 w-7 h-7 bg-indigo-100 hover:bg-indigo-600 text-indigo-600 hover:text-white rounded-full flex items-center justify-center shadow transition opacity-0 group-hover/card:opacity-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
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
                                                <ul class="space-y-3 text-slate-700 text-sm">
                                                    @foreach ($items as $q)
                                                        <li class="flex items-start gap-2 group/item">
                                                            <span class="font-bold mt-0.5"
                                                                style="color:{{ $c['500'] }}">•</span>
                                                            <span class="flex-1">{{ $q->content }}</span>
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
                                                                    @click="$dispatch('open-modal', { type: 'delete-item', id: {{ $q->id }}, endpoint: '/admin/qualifications/{{ $q->id }}' })"
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

                                        {{-- Add New Category card --}}
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
                                                Add Step
                                            </button>
                                        </div>
                                        <ol class="space-y-3 text-sm">
                                            @foreach ($program->howToApply as $step)
                                                <li class="flex items-start gap-3 group/item">
                                                    <span class="font-bold flex-shrink-0"
                                                        style="color:{{ $c['200'] }}">{{ $loop->iteration }}.</span>
                                                    <span class="flex-1">
                                                        {{ $step->content }}
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
                                                            @click="$dispatch('open-modal', { type: 'delete-item', id: {{ $step->id }}, endpoint: '/admin/steps/{{ $step->id }}' })"
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

                                    {{-- Success Stories --}}
                                    <div>
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-1 h-6 rounded-full" style="background:{{ $c['600'] }}">
                                            </div>
                                            <h4 class="font-bold text-slate-800">{{ $program->name }} Success Stories
                                            </h4>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                                            @foreach ($program->stories as $story)
                                                <div
                                                    class="group/story relative bg-white rounded-xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                                                    <div class="relative overflow-hidden h-28">
                                                        <img src="{{ asset($story->image_path) }}"
                                                            alt="{{ $story->title }}"
                                                            class="w-full h-full object-cover group-hover/story:scale-105 transition-transform duration-500">
                                                        <div
                                                            class="absolute inset-0 bg-gradient-to-t from-slate-900/50 to-transparent">
                                                        </div>
                                                        <span
                                                            class="absolute bottom-1.5 right-1.5 text-white text-xs font-bold px-1.5 py-0.5 rounded-full"
                                                            style="background:{{ $c['600'] }}">{{ $program->name }}</span>
                                                    </div>
                                                    <div class="p-2.5">
                                                        <p
                                                            class="text-xs font-semibold text-slate-700 line-clamp-2 leading-snug">
                                                            {{ $story->title }}</p>
                                                        <span class="text-xs font-medium mt-1 block"
                                                            style="color:{{ $c['600'] }}">Read →</span>
                                                    </div>
                                                    <div
                                                        class="absolute top-1 left-1 flex gap-1 opacity-0 group-hover/story:opacity-100 transition">
                                                        <button
                                                            @click.stop="$dispatch('open-modal', { type: 'edit-story', id: {{ $story->id }}, data: { title: {{ json_encode($story->title) }}, link: {{ json_encode($story->link) }} } })"
                                                            class="w-6 h-6 bg-indigo-600 text-white rounded flex items-center justify-center shadow">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                        <button
                                                            @click.stop="$dispatch('open-modal', { type: 'delete-item', id: {{ $story->id }}, endpoint: '/admin/stories/{{ $story->id }}' })"
                                                            class="w-6 h-6 bg-red-600 text-white rounded flex items-center justify-center shadow">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <button
                                                @click="$dispatch('open-modal', { type: 'add-story', programId: {{ $program->id }} })"
                                                class="add-zone rounded-xl min-h-[140px] flex flex-col items-center justify-center gap-2 text-indigo-400 hover:text-indigo-600 transition">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                <span class="text-xs font-semibold">Add Story</span>
                                            </button>
                                        </div>
                                    </div>

                                </div>

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
                                                    "{{ $program->testimonial->quote }}"</p>
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
                                            <button
                                                @click="$dispatch('open-modal', { type: 'edit-testimonial', id: {{ $program->testimonial->id }}, data: { quote: {{ json_encode($program->testimonial->quote) }}, author_name: {{ json_encode($program->testimonial->author_name) }}, author_role: {{ json_encode($program->testimonial->author_role) }} } })"
                                                class="plus-btn absolute top-3 right-3 w-7 h-7 bg-indigo-100 hover:bg-indigo-600 text-indigo-600 hover:text-white rounded-full flex items-center justify-center shadow transition opacity-0 group-hover/testimonial:opacity-100">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
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

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>

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
                <template x-if="modal.type === 'add-slide' || modal.type === 'edit-slide'">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Story Title</label>
                            <input type="text" x-model="form.title"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Short Excerpt</label>
                            <textarea rows="2" x-model="form.excerpt"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none resize-none"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Program Label</label>
                                <input type="text" x-model="form.program_label" placeholder="e.g. GIP"
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Badge Color</label>
                                <select x-model="form.color"
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                                    <option value="green">Green</option>
                                    <option value="red">Red</option>
                                    <option value="blue">Blue</option>
                                    <option value="yellow">Yellow</option>
                                    <option value="cyan">Cyan</option>
                                </select>
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
                                <span x-show="modal.type === 'edit-slide'" class="text-slate-400 font-normal">(leave
                                    blank to keep current)</span>
                            </label>
                            {{-- Live preview: shows current image on edit, updates instantly when a new file is picked --}}
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
                </template>

                {{-- ADD / EDIT PROGRAM --}}
                <template x-if="modal.type === 'add-program' || modal.type === 'edit-program'">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Program Name</label>
                            <input type="text" x-model="form.name"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Subtitle</label>
                            <input type="text" x-model="form.subtitle"
                                placeholder="e.g. 3–6 month internship in government"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Description</label>
                            <textarea rows="3" x-model="form.description"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none resize-none"></textarea>
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
                                    class="font-semibold text-slate-600 capitalize" x-text="form.color || '—'"></span>
                            </p>
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
                                <span x-show="!modal.loading">Save</span>
                                <span x-show="modal.loading" x-cloak>Saving…</span>
                            </button>
                        </div>
                    </div>
                </template>

                {{-- EDIT DESCRIPTION --}}
                <template x-if="modal.type === 'edit-description'">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Program Description</label>
                            <textarea rows="5" x-model="form.description"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none resize-none"></textarea>
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
                </template>

                {{-- ADD / EDIT QUALIFICATION --}}
                <template x-if="modal.type === 'add-qualification' || modal.type === 'edit-qualification'">
                    <div class="space-y-4">
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
                            <textarea rows="3" x-model="form.content"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none resize-none"></textarea>
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
                </template>

                {{-- ADD / EDIT STEP --}}
                <template x-if="modal.type === 'add-step' || modal.type === 'edit-step'">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Step Content</label>
                            <textarea rows="3" x-model="form.content"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Link <span class="text-slate-400 font-normal">(optional — e.g.
                                    https://gip.dole11portal.org)</span>
                            </label>
                            <input type="url" x-model="form.link" placeholder="https://..."
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                            <p class="text-xs text-slate-400 mt-1">If provided, the URL will appear as a clickable link
                                next to the step text.</p>
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
                </template>

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
                <template x-if="modal.type === 'add-testimonial' || modal.type === 'edit-testimonial'">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Quote</label>
                            <textarea rows="4" x-model="form.quote"
                                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none resize-none"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Author Name</label>
                                <input type="text" x-model="form.author_name"
                                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Author Role</label>
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
                </template>

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

            </div>
        </div>
    </div>
    {{-- ===== END MODALS ===== --}}

    <script>
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
                        endpoint: detail.endpoint ?? null,
                        data: detail.data ?? null,
                        loading: false,
                        error: null
                    };
                    this.form = detail.data ? {
                        ...detail.data
                    } : {};
                    if (detail.data?.defaultType) this.form.type = detail.data.defaultType;

                    // Remap slide fields + clear stale preview
                    if (detail.type === 'edit-slide' && detail.data) {
                        this.form.program_label = detail.data.program ?? '';
                        this.form.image_preview = null; // ← clear any leftover preview from last open
                    }
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
                    const res = await formRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/admin/carousel/${this.modal.id}` :
                        '/admin/carousel', data);
                    res.success ? this.done() : this.fail(res.message);
                },

                async submitProgram() {
                    this.modal.loading = true;
                    this.modal.error = null;
                    const isEdit = this.modal.type === 'edit-program';
                    const data = {
                        name: this.form.name,
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
                    this.modal.loading = true;
                    this.modal.error = null;
                    const isEdit = this.modal.type === 'edit-qualification';
                    const body = {
                        type: this.form.type,
                        content: this.form.content
                    };
                    if (!isEdit) body.program_id = this.modal.programId;
                    const res = await jsonRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/admin/qualifications/${this.modal.id}` :
                        '/admin/qualifications', body);
                    res.success ? this.done() : this.fail(res.message);
                },

                async submitStep() {
                    this.modal.loading = true;
                    this.modal.error = null;
                    const isEdit = this.modal.type === 'edit-step';
                    const body = {
                        content: this.form.content,
                        link: this.form.link || null,
                    };
                    if (!isEdit) body.program_id = this.modal.programId;
                    const res = await jsonRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/admin/steps/${this.modal.id}` :
                        '/admin/steps', body);
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
                    const res = await formRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/admin/stories/${this.modal.id}` :
                        '/admin/stories', data);
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
                    const res = await jsonRequest(isEdit ? 'PUT' : 'POST', isEdit ? `/admin/testimonials/${this.modal.id}` :
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
                    res.success ? this.done() : this.fail(res.message);
                },
            }
        }
    </script>

    </div>{{-- end main content area --}}
    </div>{{-- end outer layout wrapper --}}

</body>

</html>