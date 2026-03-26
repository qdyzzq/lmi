<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/fuse.js@7.0.0/dist/fuse.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>PESO / JPO Directory - LMI</title>
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
    </style>
</head>

<body class="bg-slate-100 min-h-screen">

    @include('partials.navbar')

    @php
        $pesoJson = collect($snapshot)->map(
            fn($offices) => collect($offices)
                ->map(
                    fn($o) => [
                        'id' => $o['id'] ?? null,
                        'name' => $o['name'] ?? '',
                        'manager' => $o['manager'] ?? ($o['manager_name'] ?? ''),
                        'email' => $o['email'] ?? '',
                        'address' => $o['address'] ?? '',
                        'type' => $o['type'] ?? ($o['office_type'] ?? ''),
                    ],
                )
                ->values(),
        );
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-16">

        {{-- ===== HERO HEADER ===== --}}
        <div class="relative rounded-2xl overflow-hidden shadow-2xl mb-4"
            style="background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 45%, #1e40af 100%);">
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-16 -right-16 w-72 h-72 rounded-full opacity-10"
                    style="background: radial-gradient(circle, #fff 0%, transparent 70%);"></div>
                <div class="absolute -bottom-20 -left-10 w-64 h-64 rounded-full opacity-[0.07]"
                    style="background: radial-gradient(circle, #fbbf24 0%, transparent 70%);"></div>
                <div class="absolute top-1/2 right-1/4 w-40 h-40 rounded-full opacity-[0.06]"
                    style="background: radial-gradient(circle, #93c5fd 0%, transparent 70%);"></div>
                <svg class="absolute inset-0 w-full h-full opacity-[0.04]" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="diag" width="20" height="20" patternUnits="userSpaceOnUse"
                            patternTransform="rotate(45)">
                            <line x1="0" y1="0" x2="0" y2="20" stroke="white"
                                stroke-width="1" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#diag)" />
                </svg>
            </div>
            <div class="relative flex flex-col sm:flex-row items-center sm:items-stretch gap-0">
                <div class="flex-shrink-0 flex items-center justify-center px-6 sm:px-8 py-6 sm:py-0"
                    style="background: rgba(255,255,255,0.06); border-right: 1px solid rgba(255,255,255,0.1);">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-full blur-xl opacity-40"
                            style="background: radial-gradient(circle, #fbbf24 0%, #1d4ed8 60%, transparent 80%); transform: scale(1.3);">
                        </div>
                        <img src="{{ asset('images/PESO.png') }}" alt="PESO Logo"
                            class="relative w-24 h-24 sm:w-28 sm:h-28 object-contain drop-shadow-2xl"
                            style="filter: drop-shadow(0 0 12px rgba(251,191,36,0.35));">
                    </div>
                </div>
                <div class="flex-1 flex flex-col justify-center px-6 sm:px-8 py-6 text-center sm:text-left">
                    <div class="flex items-center gap-2 justify-center sm:justify-start mb-1">
                        <span
                            class="inline-block text-xs font-bold uppercase tracking-[0.2em] text-blue-200 bg-blue-900/40 px-3 py-1 rounded-full border border-blue-700/50">
                            DOLE · Region XI
                        </span>
                    </div>
                    <h1
                        class="text-white font-black text-3xl sm:text-4xl md:text-5xl leading-tight tracking-tight mt-1">
                        PESO <span class="text-blue-300 font-light">/</span> JPO
                        <span
                            class="block text-2xl sm:text-3xl md:text-4xl font-bold text-blue-100 mt-0.5">Directory</span>
                    </h1>
                    <p class="text-blue-200/80 text-sm sm:text-base mt-2 max-w-lg">
                        Find Public Employment Service Offices and Job Placement Offices across the region
                    </p>
                </div>
            </div>
        </div>
        {{-- ===== END HERO HEADER ===== --}}

        {{-- ===== PESO INFO SECTION ===== --}}
        <div class="mt-4 mb-6 bg-white border border-slate-200 rounded-2xl shadow-md overflow-hidden">

            {{-- Hero banner --}}
            <div class="relative overflow-hidden px-6 sm:px-8 py-7"
                style="background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 60%, #1e40af 100%);">
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute -top-10 right-10 w-48 h-48 rounded-full opacity-10"
                        style="background: radial-gradient(circle, #fbbf24 0%, transparent 70%);"></div>
                    <div class="absolute -bottom-10 left-1/3 w-40 h-40 rounded-full opacity-10"
                        style="background: radial-gradient(circle, #93c5fd 0%, transparent 70%);"></div>
                </div>
                <div class="relative flex flex-col sm:flex-row items-center sm:items-start gap-5">
                    <div>
                        <p class="text-amber-300 text-xs font-bold uppercase tracking-[0.2em] mb-1">What is PESO?</p>
                        <div class="text-sm sm:text-base leading-relaxed peso-description-content">
                            {!! $pesoInfo['description'] ?? '' !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

                {{-- Objectives --}}
                <div class="col-span-1 md:col-span-2 bg-blue-50 border border-blue-100 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wide">Objective</h3>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        {!! $pesoInfo['objective'] ?? '' !!}
                    </p>
                </div>

                {{-- Core Services --}}
                {{-- $pesoInfo['core_services'] => [['id' => int, 'name' => string], ...] --}}
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-slate-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Core Services</h3>
                    </div>
                    <ul class="space-y-2">
                        @foreach ($pesoInfo['core_services'] as $service)
                            <li class="flex items-start gap-2 text-sm text-slate-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></span>
                                {{ $service['name'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- DOLE Programs --}}
                {{-- $pesoInfo['dole_programs'] => [['id' => int, 'name' => string, 'acronym' => string|null], ...] --}}
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-slate-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wide">DOLE Programs</h3>
                    </div>
                    <ul class="space-y-2">
                        @foreach ($pesoInfo['dole_programs'] as $program)
                            <li class="flex items-start gap-2 text-sm text-slate-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></span>
                                <span>
                                    {{ $program['name'] }}{{ $program['acronym'] ? ' (' . $program['acronym'] . ')' : '' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Beneficiaries --}}
                {{-- $pesoInfo['beneficiaries'] => [['id' => int, 'name' => string], ...] --}}
                <div class="col-span-1 md:col-span-2 xl:col-span-4 border border-slate-200 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wide">Beneficiaries</h3>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($pesoInfo['beneficiaries'] as $ben)
                            <span
                                class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                <span class="w-1 h-1 rounded-full bg-blue-400 flex-shrink-0"></span>
                                {{ $ben['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- How to avail footer --}}
            <div class="mx-6 sm:mx-8 mb-6 bg-blue-600 rounded-xl px-5 py-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-blue-200 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <div class="text-sm peso-howto-content">
                    <strong style="color: #bfdbfe !important;">How to Avail:</strong>
                    {!! $pesoInfo['how_to_avail'] ?? '' !!}
                </div>
            </div>

        </div>
        {{-- ===== END PESO INFO SECTION ===== --}}

        <script>
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

                    typeColor(type, part) {
                        const map = {
                            'PESO': {
                                main: '#16a34a',
                                bg: '#f0fdf4',
                                border: '#bbf7d0'
                            },
                            'JPO': {
                                main: '#2563eb',
                                bg: '#eff6ff',
                                border: '#bfdbfe'
                            },
                        };
                        return (map[type] ?? {
                            main: '#6366f1',
                            bg: '#eef2ff',
                            border: '#c7d2fe'
                        })[part];
                    },

                    get officeTypes() {
                        const all = Object.values(this.pesoData ?? {}).flat();
                        return [...new Set(all.map(e => e.type).filter(Boolean))].sort();
                    },

                    selectProvince(val) {
                        this.province = val;
                        this.officeType = '';
                        this.showResults = false;
                        this.showType = !!val;
                        this.search = '';
                        this._fuseCache = {};
                        this._filteredCache = null;
                        if (val) this.$nextTick(() =>
                            this.$refs.typeSection?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'nearest'
                            })
                        );
                    },

                    selectType(t) {
                        this.officeType = t;
                        this.showResults = !!t;
                        this.search = '';
                        this._filteredCache = null;
                        if (t) this.$nextTick(() =>
                            this.$refs.resultsSection?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'nearest'
                            })
                        );
                    },

                    countFor(province, type) {
                        const entries = this.pesoData?.[province] ?? [];
                        return type === 'ALL' ? entries.length : entries.filter(e => e.type === type)
                            .length;
                    },

                    filteredEntries() {
                        const cacheKey = this.province + '|' + this.officeType + '|' + this.search;
                        if (this._filteredCache !== null && this._filteredCacheKey === cacheKey)
                            return this._filteredCache;

                        let entries = this.pesoData?.[this.province] ?? [];
                        if (this.officeType !== 'ALL') entries = entries.filter(e => e.type === this
                            .officeType);

                        let result;
                        if (!this.search.trim()) {
                            result = entries;
                        } else {
                            const fuseCacheKey = this.province + '|' + this.officeType;
                            if (!this._fuseCache[fuseCacheKey] || this._fuseCache[fuseCacheKey]._list !==
                                entries) {
                                this._fuseCache[fuseCacheKey] = new Fuse(entries, {
                                    keys: [{
                                        name: 'name',
                                        weight: 0.6
                                    }, {
                                        name: 'manager',
                                        weight: 0.3
                                    }, {
                                        name: 'type',
                                        weight: 0.1
                                    }],
                                    threshold: 0.4,
                                    distance: 200,
                                    minMatchCharLength: 2,
                                    includeScore: true,
                                });
                                this._fuseCache[fuseCacheKey]._list = entries;
                            }
                            result = this._fuseCache[fuseCacheKey].search(this.search.trim()).map(r => r
                                .item);
                        }

                        this._filteredCache = result;
                        this._filteredCacheKey = cacheKey;
                        return result;
                    },
                }));
            });
        </script>

        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden" x-data="pesoDirectory()">

            {{-- Directory card top bar --}}
            <div class="flex items-center gap-3 px-6 sm:px-8 py-4 border-b border-slate-100"
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

            <div class="p-4 sm:p-6 md:p-10 space-y-8">

                {{-- STEP 1: Province --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">
                        1 · Select Province
                    </label>
                    <div class="relative w-full">
                        <select @change="selectProvince($event.target.value)" :value="province"
                            class="w-full appearance-none bg-white border-2 rounded-xl px-4 py-3 pr-10 text-sm font-semibold outline-none transition-all cursor-pointer"
                            :class="province ? 'border-orange-400 shadow-[0_0_0_3px_rgba(251,146,60,0.15)] text-slate-800' :
                                'border-slate-200 text-slate-400 hover:border-slate-300'">
                            <option value="">— Choose a province —</option>
                            @foreach ($pesoProvinceKeys as $prov)
                                <option value="{{ $prov }}">{{ $prov }}</option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
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

                    <div class="rounded-xl px-5 py-4 mb-5 flex items-center justify-between"
                        style="background:#f0fdf4; border:1.5px solid #bbf7d0;">
                        <div>
                            <p class="text-lg font-extrabold text-slate-800 uppercase" x-text="province"></p>
                            <p class="text-sm mt-0.5 text-slate-500">
                                <strong class="text-green-600"
                                    x-text="countFor(province,'PESO') + ' PESO Offices'"></strong>
                                <span class="mx-1">·</span>
                                <strong class="text-blue-600"
                                    x-text="countFor(province,'JPO') + ' JPO Offices'"></strong>
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
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
                                Reset
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

                    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-3 items-start" x-data="{ openId: null }">
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
                                        <p class="text-sm font-bold text-slate-800 truncate uppercase"
                                            x-text="entry.name"></p>
                                        <p class="text-xs truncate mt-0.5"
                                            :style="`color:${typeColor(entry.type,'main')}`"
                                            x-text="entry.manager || '—'"></p>
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
                                    class="px-4 pb-4 pt-3 flex flex-col gap-2.5"
                                    :style="`border-top:1.5px solid ${typeColor(entry.type,'border')}`">

                                    <template
                                        x-for="[label, icon, value, href] in [
                                        [entry.type === 'JPO' ? 'JPO Manager' : 'PESO Manager', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', entry.manager, null],
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

                <div x-show="!province" class="text-center py-8 text-slate-400">
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

</body>

</html>
