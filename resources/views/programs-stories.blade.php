<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
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
    </style>
</head>

<body class="bg-slate-100 min-h-screen">
    <div x-data="{ activeProgram: null }">
        @include('partials.navbar')

        <!-- ===== CAROUSEL (UNCHANGED) ===== -->
        <div class="relative w-full h-screen overflow-hidden" x-data="{
            currentSlide: 0,
            slides: [
                { image: '{{ asset('images/testimonials/GIP.jpg') }}', title: 'From GIP Beneficiary to City HR Leader', excerpt: 'Genevieve Elan Palmera\'s journey from an intern to a permanent HR position showcases the transformative power of the Government Internship Program.', link: 'https://ro11.dole.gov.ph/news/from-dole-gip-beneficiary-to-city-hr-leader-a-youth-employment-success-story/', program: 'GIP', color: 'green' },
                { image: '{{ asset('images/testimonials/jobstart.jpg') }}', title: 'Camp Holidays JobStart Success', excerpt: 'From interns to full-fledged employees - discover how JobStart graduates transformed their careers through hands-on experience and dedication.', link: 'https://ro11.dole.gov.ph/news/the-success-story-of-camp-holidays-jobstart-graduates-from-interns-to-full-fledged-employees/', program: 'JobStart', color: 'red' },
                { image: '{{ asset('images/testimonials/spes.jpeg') }}', title: 'SPES Grantee Achieves Latin Honors', excerpt: 'Khacley Marino\'s inspiring journey from SPES beneficiary to graduating with Latin honors proves that financial support can unlock academic excellence.', link: 'https://ro11.dole.gov.ph/news/spes-grantee-achieves-latin-honors-and-graduation-success-khacley-marinos-inspiring-journey/', program: 'SPES', color: 'blue' },
                { image: '{{ asset('images/testimonials/CDSP.jpg') }}', title: 'The Courage to Begin with CDSP', excerpt: 'Philip Tecson\'s journey fresh out of college with DOLE\'s Career Development Service Program shows how proper guidance can shape a successful career path.', link: 'https://ro11.dole.gov.ph/news/the-courage-to-begin-philip-tecsons-journey-fresh-out-of-college-with-doles-career-development-service-program/', program: 'CDSP', color: 'yellow' }
            ],
            autoplayInterval: null,
            nextSlide() { this.currentSlide = (this.currentSlide + 1) % this.slides.length; },
            prevSlide() { this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length; },
            goToSlide(index) { this.currentSlide = index; },
            startAutoplay() { this.autoplayInterval = setInterval(() => { this.nextSlide(); }, 5000); },
            stopAutoplay() { if (this.autoplayInterval) { clearInterval(this.autoplayInterval); } }
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
                        <img :src="slide.image" :alt="slide.title"
                            class="w-full h-full object-cover object-center">
                        <div
                            class="absolute inset-0 bg-gradient-to-b from-slate-900/85 via-slate-900/70 to-slate-900/50">
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
                                        'bg-yellow-500/40 border-2 border-yellow-300/60': slide.color === 'yellow'
                                    }"
                                    x-text="slide.program + ' Success Story'"></span>
                            </div>
                            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold mb-8 drop-shadow-2xl leading-tight"
                                x-text="slide.title"></h1>
                            <p class="text-xl md:text-2xl lg:text-3xl text-slate-50 drop-shadow-lg mb-12 max-w-4xl mx-auto leading-relaxed font-light"
                                x-text="slide.excerpt"></p>
                            <a :href="slide.link" target="_blank"
                                class="inline-flex items-center gap-3 px-10 py-5 bg-white text-slate-900 font-bold text-lg rounded-xl hover:bg-slate-100 transition-all duration-300 shadow-2xl transform hover:-translate-y-2 hover:scale-105">
                                <span>READ FULL STORY</span>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <a href="#programs-section"
                        class="absolute bottom-1 left-1/2 transform -translate-x-1/2 animate-bounce cursor-pointer z-20"
                        @click.prevent="document.getElementById('programs-section').scrollIntoView({ behavior: 'smooth' })">
                        <div class="flex flex-col items-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            <p class="text-white text-sm mt-2 font-medium">Scroll to explore programs</p>
                        </div>
                    </a>
                </div>
            </template>
            <button @click="prevSlide()"
                class="absolute left-6 top-1/2 -translate-y-1/2 z-20 w-14 h-14 bg-white/25 hover:bg-white/40 backdrop-blur-md rounded-full flex items-center justify-center transition-all duration-300 group border border-white/30">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button @click="nextSlide()"
                class="absolute right-6 top-1/2 -translate-y-1/2 z-20 w-14 h-14 bg-white/25 hover:bg-white/40 backdrop-blur-md rounded-full flex items-center justify-center transition-all duration-300 group border border-white/30">
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
        </div>
        <!-- ===== END CAROUSEL ===== -->


        <!-- ===== PROGRAMS SECTION ===== -->
        <div id="programs-section" class="max-w-7xl mx-auto px-4 sm:px-6 py-16">

            <!-- Section Header -->
            <div
                class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 rounded-2xl px-8 py-7 shadow-2xl mb-2">
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

            <!-- Accordion Container -->
            <div
                class="bg-white rounded-2xl shadow-2xl border border-slate-200 divide-y divide-slate-200 overflow-hidden">

                <!-- ══════════════════════════════ GIP (GREEN) ══════════════════════════════ -->
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-full px-6 md:px-10 py-6 flex items-center justify-between hover:bg-green-50 transition-colors duration-200 group text-left">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl flex-shrink-0 flex items-center justify-center shadow-md transition-all duration-300 border-2"
                                :class="open ? 'bg-white border-green-400' :
                                    'bg-green-50 border-transparent group-hover:bg-green-100'">
                                <img src="{{ asset('images/logo-programs/gip_logo.png') }}" alt="GIP Logo"
                                    class="w-10 h-10 md:w-14 md:h-14 object-contain">
                            </div>
                            <div>
                                <h3 class="text-xl md:text-2xl font-bold text-slate-900 group-hover:text-green-600 transition-colors"
                                    :class="open ? 'text-green-600' : ''">
                                    Government Internship Program
                                </h3>
                                <p class="text-sm md:text-base text-slate-500 mt-1">GIP — 3–6 month internship
                                    opportunity in government</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                            <span class="text-xs md:text-sm font-semibold hidden sm:block"
                                :class="open ? 'text-green-600' : 'text-slate-400'">
                                <span x-show="!open">Click to expand</span>
                                <span x-show="open" x-cloak>Click to collapse</span>
                            </span>
                            <div class="w-9 h-9 rounded-full flex items-center justify-center transition-colors duration-200"
                                :class="open ? 'bg-green-600' : 'bg-slate-100 group-hover:bg-green-100'">
                                <svg class="w-5 h-5 transition-transform duration-300 chevron-icon"
                                    :class="open ? 'open text-white' : 'text-slate-500'" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <div x-show="open" x-collapse x-cloak>
                        <div
                            class="border-t border-slate-200 bg-gradient-to-br from-slate-50 to-green-50/20 p-6 md:p-10">
                            <div class="grid lg:grid-cols-3 gap-8">

                                <div class="lg:col-span-2 space-y-6">

                                    <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                                        <h4 class="font-bold text-slate-800 mb-3 flex items-center gap-2 text-lg">
                                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Program Details
                                        </h4>
                                        <p class="text-slate-700 leading-relaxed">
                                            A youth employability program which aims to provide 3–6 months internship
                                            opportunity in the government for high school, technical-vocational or
                                            college graduates to build their capabilities and make them more employable.
                                        </p>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Qualifications
                                            </h4>
                                            <ul class="space-y-3 text-slate-700 text-sm">
                                                <li class="flex items-start gap-2"><span
                                                        class="text-green-500 font-bold mt-0.5">•</span><span>18 to 30
                                                        years old, with exceptions determined by DOLE Regional
                                                        Offices</span></li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-green-500 font-bold mt-0.5">•</span><span>Individuals
                                                        aged 31+ may qualify under specific conditions (no/intermittent
                                                        work experience, laid off, or displaced by disasters)</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                                Education Requirements
                                            </h4>
                                            <ul class="space-y-3 text-slate-700 text-sm">
                                                <li class="flex items-start gap-2"><span
                                                        class="text-green-500 font-bold mt-0.5">•</span><span>High
                                                        school / Senior High School Graduate or equivalent, or
                                                        technical-vocational graduate</span></li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-green-500 font-bold mt-0.5">•</span><span>Victims
                                                        of
                                                        armed conflicts, rebel returnees, PWDs and Indigenous Peoples
                                                        also eligible</span></li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-green-500 font-bold mt-0.5">•</span><span>Transcript
                                                        of Records or Form 137; Certificate of Graduation for voc-tech;
                                                        Certificate of Indigence from Barangay</span></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="bg-green-600 text-white rounded-xl p-6">
                                        <h4 class="font-bold mb-4 flex items-center gap-2 text-lg">
                                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            How to Apply
                                        </h4>
                                        <ol class="space-y-3 text-sm">
                                            <li class="flex items-start gap-3"><span
                                                    class="font-bold text-green-200 flex-shrink-0">1.</span><span>Visit
                                                    the GIP system for Region 11: <a href="http://gip.dole11portal.org"
                                                        class="underline font-semibold hover:text-green-200"
                                                        target="_blank">gip.dole11portal.org</a> to register</span>
                                            </li>
                                            <li class="flex items-start gap-3"><span
                                                    class="font-bold text-green-200 flex-shrink-0">2.</span><span>Wait
                                                    for correspondence on approval/acceptance</span></li>
                                        </ol>
                                    </div>

                                    <!-- GIP Success Stories -->
                                    <div>
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-1 h-6 bg-green-600 rounded-full"></div>
                                            <h4 class="font-bold text-slate-800">GIP Success Stories</h4>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                                            @php
                                                $gipStories = [
                                                    [
                                                        'img' => asset('images/gip-story/gipstory-1.jpg'),
                                                        'title' => 'From GIP Beneficiary to City HR Leader',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/from-dole-gip-beneficiary-to-city-hr-leader-a-youth-employment-success-story/',
                                                    ],
                                                    [
                                                        'img' => asset('images/gip-story/gipstory-2.jpg'),
                                                        'title' => 'From Financial Hardship to Summa Cum Laude',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/from-financial-hardship-to-academic-excellence-sharny-lee-basartes-journey-from-dole-gip-beneficiary-to-summa-cum-laude-graduate/',
                                                    ],
                                                    [
                                                        'img' => asset('images/gip-story/gipstory-3.jpg'),
                                                        'title' => 'From GIP to Development Management Officer II',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/from-dole-gip-beneficiary-to-development-management-officer-ii-rasty-vistars-path-to-success-in-samals-local-government/',
                                                    ],
                                                    [
                                                        'img' => asset('images/gip-story/gipstory-4.jpg'),
                                                        'title' => 'From GIP Beneficiary to Information Officer II',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/from-gip-beneficiary-to-information-officer-ii-novy-b-cretas-journey-of-excellence/',
                                                    ],
                                                    [
                                                        'img' => asset('images/gip-story/gipstory-5.jpg'),
                                                        'title' => 'A Journey of Youth Employability',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/a-journey-of-youth-employability-program-beneficiary/',
                                                    ],
                                                ];
                                            @endphp
                                            @foreach ($gipStories as $story)
                                                <a href="{{ $story['link'] }}" target="_blank"
                                                    class="group block bg-white rounded-xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                                                    <div class="relative overflow-hidden h-28">
                                                        <img src="{{ $story['img'] }}" alt="{{ $story['title'] }}"
                                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                        <div
                                                            class="absolute inset-0 bg-gradient-to-t from-slate-900/50 to-transparent">
                                                        </div>
                                                        <span
                                                            class="absolute bottom-1.5 right-1.5 bg-green-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">GIP</span>
                                                    </div>
                                                    <div class="p-2.5">
                                                        <p
                                                            class="text-xs font-semibold text-slate-700 line-clamp-2 group-hover:text-green-600 transition-colors leading-snug">
                                                            {{ $story['title'] }}</p>
                                                        <span
                                                            class="text-xs text-green-600 font-medium mt-1 block">Read
                                                            →</span>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="lg:col-span-1">
                                    <div
                                        class="bg-white border-2 border-green-200 rounded-xl p-6 shadow-lg sticky top-6">
                                        <div class="flex items-center gap-3 mb-6">
                                            <div
                                                class="w-11 h-11 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
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
                                                "The program allowed me to prove my capability and work ethic. It was
                                                the bridge from where I started, uncertain and struggling, to where I
                                                stand now, more confident, more skilled, and part of an institution I
                                                deeply respect."
                                            </p>
                                        </blockquote>
                                        <div class="flex items-center gap-3 pt-4 border-t border-green-100">

                                            <div>
                                                <p class="font-bold text-slate-900 text-sm">Genevieve Elan Palmera</p>
                                                <p class="text-xs text-slate-500">GIP Beneficiary</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- ══════════════════════════════ END GIP ══════════════════════════════ -->


                <!-- ══════════════════════════════ JOBSTART (RED) ══════════════════════════════ -->
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-full px-6 md:px-10 py-6 flex items-center justify-between hover:bg-red-50 transition-colors duration-200 group text-left">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl flex-shrink-0 flex items-center justify-center shadow-md transition-all duration-300 border-2"
                                :class="open ? 'bg-white border-red-400' : 'bg-red-50 border-transparent group-hover:bg-red-100'">
                                <img src="{{ asset('images/logo-programs/jobstart_logo.png') }}" alt="JobStart Logo"
                                    class="w-10 h-10 md:w-14 md:h-14 object-contain">
                            </div>
                            <div>
                                <h3 class="text-xl md:text-2xl font-bold text-slate-900 group-hover:text-red-600 transition-colors"
                                    :class="open ? 'text-red-600' : ''">
                                    JobStart Program
                                </h3>
                                <p class="text-sm md:text-base text-slate-500 mt-1">Youth employment initiative with
                                    career coaching &amp; training</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                            <span class="text-xs md:text-sm font-semibold hidden sm:block"
                                :class="open ? 'text-red-600' : 'text-slate-400'">
                                <span x-show="!open">Click to expand</span>
                                <span x-show="open" x-cloak>Click to collapse</span>
                            </span>
                            <div class="w-9 h-9 rounded-full flex items-center justify-center transition-colors duration-200"
                                :class="open ? 'bg-red-600' : 'bg-slate-100 group-hover:bg-red-100'">
                                <svg class="w-5 h-5 transition-transform duration-300 chevron-icon"
                                    :class="open ? 'open text-white' : 'text-slate-500'" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <div x-show="open" x-collapse x-cloak>
                        <div
                            class="border-t border-slate-200 bg-gradient-to-br from-slate-50 to-red-50/20 p-6 md:p-10">
                            <div class="grid lg:grid-cols-3 gap-8">
                                <div class="lg:col-span-2 space-y-6">

                                    <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                                        <h4 class="font-bold text-slate-800 mb-3 flex items-center gap-2 text-lg">
                                            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Program Details
                                        </h4>
                                        <p class="text-slate-700 leading-relaxed">
                                            A youth employability program which aims to shorten the school-to-work
                                            transition of youth not in education, employment, or training by providing
                                            them with career coaching, life skills and technical training, and
                                            internships with employers.
                                        </p>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Qualifications
                                            </h4>
                                            <ul class="space-y-3 text-slate-700 text-sm">
                                                <li class="flex items-start gap-2"><span
                                                        class="text-red-500 font-bold mt-0.5">•</span><span>Filipino
                                                        Citizen; 18–24 years old</span></li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-red-500 font-bold mt-0.5">•</span><span>Currently
                                                        not in education, employment, or training (NEET)</span></li>
                                            </ul>
                                        </div>
                                        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                                Requirements
                                            </h4>
                                            <ul class="space-y-3 text-slate-700 text-sm">
                                                <li class="flex items-start gap-2"><span
                                                        class="text-red-500 font-bold mt-0.5">•</span><span>Reached
                                                        at least Grade 7 or first year high school</span></li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-red-500 font-bold mt-0.5">•</span><span>0–12
                                                        months work experience</span></li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-red-500 font-bold mt-0.5">•</span><span>Actively
                                                        looking for work</span></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="bg-red-600 text-white rounded-xl p-6">
                                        <h4 class="font-bold mb-4 flex items-center gap-2 text-lg">
                                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            How to Apply
                                        </h4>
                                        <p class="text-sm mb-4">Registration at participating PESO offices (first-come,
                                            first-served) at JobStart sites:</p>
                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                            <span>• Davao City</span><span>• Panabo City</span>
                                            <span>• Tagum City</span><span>• IGACOS</span>
                                            <span>• Davao del Sur</span><span>• Davao Oriental</span>
                                        </div>
                                    </div>

                                    <!-- JobStart Stories -->
                                    <div>
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-1 h-6 bg-red-600 rounded-full"></div>
                                            <h4 class="font-bold text-slate-800">JobStart Success Stories</h4>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                                            @php
                                                $jobstartStories = [
                                                    [
                                                        'img' => asset('images/jobstart-story/jobstart-1.jpg'),
                                                        'title' =>
                                                            'Camp Holidays: From Interns to Full-Fledged Employees',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/the-success-story-of-camp-holidays-jobstart-graduates-from-interns-to-full-fledged-employees/',
                                                    ],
                                                    [
                                                        'img' => asset('images/jobstart-story/jobstart-2.jpg'),
                                                        'title' =>
                                                            "From Job Seeker to Job Maker: A Woman's Rise Through JobStart",
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/from-job-seeker-to-job-maker-a-womans-rise-through-jobstart-philippines-program/',
                                                    ],
                                                    [
                                                        'img' => asset('images/jobstart-story/jobstart-3.jpg'),
                                                        'title' =>
                                                            "Building a Brighter Future: Malijah Mamalinta's Journey Through JobStart",
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/building-a-brighter-future-malijah-mamalintas-journey-through-jobstart-philippines/',
                                                    ],
                                                    [
                                                        'img' => asset('images/jobstart-story/jobstart-4.jpg'),
                                                        'title' =>
                                                            'First JobStart Batch in Davao Oriental: 87 Graduates, 45 Hired on the Spot',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/first-jobstart-batch-in-davao-oriental-87-graduates-45-hired-on-the-spot/',
                                                    ],
                                                    [
                                                        'img' => asset('images/jobstart-story/jobstart-5.jpg'),
                                                        'title' =>
                                                            'DOLE JobStart Paves the Way for Young Samaleños to Land Full-Time Jobs at CRBC',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/dole-jobstart-paves-the-way-for-young-samalenos-to-land-full-time-jobs-at-crbc-after-graduation/',
                                                    ],
                                                ];
                                            @endphp
                                            @foreach ($jobstartStories as $story)
                                                <a href="{{ $story['link'] }}" target="_blank"
                                                    class="group block bg-white rounded-xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                                                    <div class="relative overflow-hidden h-28">
                                                        <img src="{{ $story['img'] }}" alt="{{ $story['title'] }}"
                                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                        <div
                                                            class="absolute inset-0 bg-gradient-to-t from-slate-900/50 to-transparent">
                                                        </div>
                                                        <span
                                                            class="absolute bottom-1.5 right-1.5 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">JobStart</span>
                                                    </div>
                                                    <div class="p-2.5">
                                                        <p
                                                            class="text-xs font-semibold text-slate-700 line-clamp-2 group-hover:text-red-600 transition-colors leading-snug">
                                                            {{ $story['title'] }}</p>
                                                        <span class="text-xs text-red-600 font-medium mt-1 block">Read
                                                            →</span>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                                <div class="lg:col-span-1">
                                    <div
                                        class="bg-white border-2 border-red-200 rounded-xl p-6 shadow-lg sticky top-6">
                                        <div class="flex items-center gap-3 mb-6">
                                            <div
                                                class="w-11 h-11 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
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
                                                "This program gave me not just a job, but also confidence, direction,
                                                and growth. I am truly grateful to DOLE and the JobStart Program for
                                                helping me believe in myself and shaping my career journey."
                                            </p>
                                        </blockquote>
                                        <div class="flex items-center gap-3 pt-4 border-t border-red-100">

                                            <div>
                                                <p class="font-bold text-slate-900 text-sm">Elen C. Ocon</p>
                                                <p class="text-xs text-slate-500">JobStart Beneficiary</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ══════════════════════════════ END JOBSTART ══════════════════════════════ -->


                <!-- ══════════════════════════════ SPES (BLUE) ══════════════════════════════ -->
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-full px-6 md:px-10 py-6 flex items-center justify-between hover:bg-blue-50 transition-colors duration-200 group text-left">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl flex-shrink-0 flex items-center justify-center shadow-md transition-all duration-300 border-2"
                                :class="open ? 'bg-white border-blue-400' :
                                    'bg-blue-50 border-transparent group-hover:bg-blue-100'">
                                <img src="{{ asset('images/logo-programs/spes_logo.png') }}" alt="SPES Logo"
                                    class="w-10 h-10 md:w-14 md:h-14 object-contain">
                            </div>
                            <div>
                                <h3 class="text-xl md:text-2xl font-bold text-slate-900 group-hover:text-blue-600 transition-colors"
                                    :class="open ? 'text-blue-600' : ''">
                                    Special Program for Employment of Students
                                </h3>
                                <p class="text-sm md:text-base text-slate-500 mt-1">SPES — Short-term employment for
                                    underprivileged students</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                            <span class="text-xs md:text-sm font-semibold hidden sm:block"
                                :class="open ? 'text-blue-600' : 'text-slate-400'">
                                <span x-show="!open">Click to expand</span>
                                <span x-show="open" x-cloak>Click to collapse</span>
                            </span>
                            <div class="w-9 h-9 rounded-full flex items-center justify-center transition-colors duration-200"
                                :class="open ? 'bg-blue-600' : 'bg-slate-100 group-hover:bg-blue-100'">
                                <svg class="w-5 h-5 transition-transform duration-300 chevron-icon"
                                    :class="open ? 'open text-white' : 'text-slate-500'" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <div x-show="open" x-collapse x-cloak>
                        <div
                            class="border-t border-slate-200 bg-gradient-to-br from-slate-50 to-blue-50/20 p-6 md:p-10">
                            <div class="grid lg:grid-cols-3 gap-8">
                                <div class="lg:col-span-2 space-y-6">

                                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                                        <h4 class="font-bold text-slate-800 mb-3 flex items-center gap-2 text-lg">
                                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Program Details
                                        </h4>
                                        <p class="text-slate-700 leading-relaxed">
                                            A youth employability program which aims to provide short-term employment to
                                            underprivileged students, out-of-school youth, and dependents of displaced
                                            or would-be displaced workers. The program helps augment the family's income
                                            and ensures beneficiaries are able to pursue their education.
                                        </p>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Qualifications
                                            </h4>
                                            <ul class="space-y-3 text-slate-700 text-sm">
                                                <li class="flex items-start gap-2"><span
                                                        class="text-blue-500 font-bold mt-0.5">•</span><span>Students
                                                        or OSY who are at least 15 but not more than 30 years of
                                                        age</span></li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-blue-500 font-bold mt-0.5">•</span><span>Combined
                                                        net income after tax of parents does not exceed the regional
                                                        poverty threshold</span></li>
                                            </ul>
                                        </div>
                                        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                                Requirements
                                            </h4>
                                            <ul class="space-y-3 text-slate-700 text-sm">
                                                <li class="flex items-start gap-2"><span
                                                        class="text-blue-500 font-bold mt-0.5">•</span><span>Must
                                                        have obtained a passing general weighted average during the last
                                                        semester or school year attended</span></li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-blue-500 font-bold mt-0.5">•</span><span>Must be
                                                        certified by the barangay or local SWDO as OSY</span></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="bg-blue-600 text-white rounded-xl p-6">
                                        <h4 class="font-bold mb-3 flex items-center gap-2 text-lg">
                                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            How to Apply
                                        </h4>
                                        <p class="text-sm">Registration takes place at participating Public Employment
                                            Service Offices (PESO) on a first-come, first-served basis.</p>
                                    </div>

                                    <!-- SPES Stories -->
                                    <div>
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                                            <h4 class="font-bold text-slate-800">SPES Success Stories</h4>
                                        </div>
                                        @php
                                            $spesStories = [
                                                [
                                                    'img' => asset('images/spes-story/spes-1.jpg'),
                                                    'title' =>
                                                        'From SPES Beneficiary to DOLE XI Accountant: Risha\'s Full-Circle Journey',
                                                    'link' =>
                                                        'https://ro11.dole.gov.ph/news/from-spes-beneficiary-to-dole-xi-accountant-rishas-full-circle-journey/',
                                                ],
                                                [
                                                    'img' => asset('images/spes-story/spes-2.jpg'),
                                                    'title' => 'Former SPES Beneficiary Now Leads PESO San Isidro',
                                                    'link' =>
                                                        'https://ro11.dole.gov.ph/news/former-spes-beneficiary-now-leads-peso-san-isidro/',
                                                ],
                                                [
                                                    'img' => asset('images/spes-story/spes-3.jpeg'),
                                                    'title' => 'Once SPES Baby, Now Proud Regular Employee',
                                                    'link' =>
                                                        'https://ro11.dole.gov.ph/news/once-spes-baby-now-proud-regular-employee/',
                                                ],
                                                [
                                                    'img' => asset('images/spes-story/spes-4.jpg'),
                                                    'title' =>
                                                        'SPES Grantee Achieves Latin Honors and Graduation Success: Khacley Marino\'s Inspiring Journey',
                                                    'link' =>
                                                        'https://ro11.dole.gov.ph/news/spes-grantee-achieves-latin-honors-and-graduation-success-khacley-marinos-inspiring-journey/',
                                                ],
                                            ];
                                        @endphp
                                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                                            @foreach ($spesStories as $story)
                                                <a href="{{ $story['link'] }}" target="_blank"
                                                    class="group block bg-white rounded-xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                                                    <div class="relative overflow-hidden h-28">
                                                        <img src="{{ $story['img'] }}" alt="{{ $story['title'] }}"
                                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                        <div
                                                            class="absolute inset-0 bg-gradient-to-t from-slate-900/50 to-transparent">
                                                        </div>
                                                        <span
                                                            class="absolute bottom-1.5 right-1.5 bg-blue-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">SPES</span>
                                                    </div>
                                                    <div class="p-2.5">
                                                        <p
                                                            class="text-xs font-semibold text-slate-700 line-clamp-2 group-hover:text-blue-600 transition-colors leading-snug">
                                                            {{ $story['title'] }}</p>
                                                        <span class="text-xs text-blue-600 font-medium mt-1 block">Read
                                                            →</span>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                                <div class="lg:col-span-1">
                                    <div
                                        class="bg-white border-2 border-blue-200 rounded-xl p-6 shadow-lg sticky top-6">
                                        <div class="flex items-center gap-3 mb-6">
                                            <div
                                                class="w-11 h-11 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
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
                                                "The program helped me by easing the financial pressure that made it
                                                hard for me to stay in school. The income I earned supported my
                                                education and personal needs, allowing me to focus more on my studies
                                                instead of worrying every day about expenses."
                                            </p>
                                        </blockquote>
                                        <div class="flex items-center gap-3 pt-4 border-t border-blue-100">

                                            <div>
                                                <p class="font-bold text-slate-900 text-sm">Mark Jay G. Quinto</p>
                                                <p class="text-xs text-slate-500">SPES Beneficiary</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ══════════════════════════════ END SPES ══════════════════════════════ -->


                <!-- ══════════════════════════════ CDSP (YELLOW) ══════════════════════════════ -->
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-full px-6 md:px-10 py-6 flex items-center justify-between hover:bg-yellow-50 transition-colors duration-200 group text-left">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl flex-shrink-0 flex items-center justify-center shadow-md transition-all duration-300 border-2"
                                :class="open ? 'bg-white border-yellow-400' :
                                    'bg-yellow-50 border-transparent group-hover:bg-yellow-100'">
                                <img src="{{ asset('images/logo-programs/cdsp_logo.png') }}" alt="CDSP Logo"
                                    class="w-10 h-10 md:w-14 md:h-14 object-contain">
                            </div>
                            <div>
                                <h3 class="text-xl md:text-2xl font-bold text-slate-900 group-hover:text-yellow-600 transition-colors"
                                    :class="open ? 'text-yellow-600' : ''">
                                    Career Development Support Program
                                </h3>
                                <p class="text-sm md:text-base text-slate-500 mt-1">CDSP — Career counseling &amp;
                                    employment support services</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                            <span class="text-xs md:text-sm font-semibold hidden sm:block"
                                :class="open ? 'text-yellow-600' : 'text-slate-400'">
                                <span x-show="!open">Click to expand</span>
                                <span x-show="open" x-cloak>Click to collapse</span>
                            </span>
                            <div class="w-9 h-9 rounded-full flex items-center justify-center transition-colors duration-200"
                                :class="open ? 'bg-yellow-500' : 'bg-slate-100 group-hover:bg-yellow-100'">
                                <svg class="w-5 h-5 transition-transform duration-300 chevron-icon"
                                    :class="open ? 'open text-white' : 'text-slate-500'" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <div x-show="open" x-collapse x-cloak>
                        <div
                            class="border-t border-slate-200 bg-gradient-to-br from-slate-50 to-yellow-50/20 p-6 md:p-10">
                            <div class="grid lg:grid-cols-3 gap-8">
                                <div class="lg:col-span-2 space-y-6">

                                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                                        <h4 class="font-bold text-slate-800 mb-3 flex items-center gap-2 text-lg">
                                            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Program Details
                                        </h4>
                                        <p class="text-slate-700 leading-relaxed mb-3">
                                            CDSP is a public employment service which aims to address gaps in
                                            employability dimensions — personal and environmental factors, job
                                            objectives, skills and requirements to perform the job, job search skills,
                                            and ability to maintain a job — through career, vocational, and employment
                                            counseling.
                                        </p>
                                        <p class="text-slate-700 leading-relaxed">
                                            The objective is to assist individuals to find the right job, identify
                                            appropriate upskilling or reskilling interventions, and progress in their
                                            chosen career path.
                                        </p>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                Beneficiaries
                                            </h4>
                                            <ul class="space-y-2 text-slate-700 text-sm">
                                                <li class="flex items-center gap-2"><span
                                                        class="text-yellow-500 font-bold">•</span> Jobseekers</li>
                                                <li class="flex items-center gap-2"><span
                                                        class="text-yellow-500 font-bold">•</span> Employers</li>
                                                <li class="flex items-center gap-2"><span
                                                        class="text-yellow-500 font-bold">•</span> Students &amp; Youth
                                                </li>
                                                <li class="flex items-center gap-2"><span
                                                        class="text-yellow-500 font-bold">•</span> Migrant Workers</li>
                                                <li class="flex items-center gap-2"><span
                                                        class="text-yellow-500 font-bold">•</span> Long-term Unemployed
                                                </li>
                                                <li class="flex items-center gap-2"><span
                                                        class="text-yellow-500 font-bold">•</span> Persons with
                                                    Disabilities</li>
                                            </ul>
                                        </div>
                                        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                                Services Offered
                                            </h4>
                                            <ul class="space-y-3 text-slate-700 text-sm">
                                                <li class="flex items-start gap-2"><span
                                                        class="text-yellow-500 font-bold mt-0.5">•</span> Career
                                                    Counseling</li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-yellow-500 font-bold mt-0.5">•</span> Vocational
                                                    Counseling</li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-yellow-500 font-bold mt-0.5">•</span> Employment
                                                    Counseling</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="bg-yellow-500 text-white rounded-xl p-6">
                                        <h4 class="font-bold mb-3 flex items-center gap-2 text-lg">
                                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            How to Avail
                                        </h4>
                                        <p class="text-sm">Registration takes place at participating Public Employment
                                            Service Offices (PESO) and Partner Job Placement Offices for Students.</p>
                                    </div>

                                    <!-- CDSP Stories -->
                                    <div>
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-1 h-6 bg-yellow-500 rounded-full"></div>
                                            <h4 class="font-bold text-slate-800">CDSP Success Stories</h4>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                                            @php
                                                $cdspStories = [
                                                    [
                                                        'img' => asset('images/cdsp-story/cdsp-1.jpg'),
                                                        'title' =>
                                                            "The Courage to Begin: Philip Tecson's Journey with DOLE's CDSP",
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/the-courage-to-begin-philip-tecsons-journey-fresh-out-of-college-with-doles-career-development-service-program/',
                                                    ],
                                                    [
                                                        'img' => asset('images/cdsp-story/cdsp-2.jpg'),
                                                        'title' =>
                                                            'DOLE and PESO Advance CDSP Through Unified School-to-Work Framework',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/dole-and-peso-advances-cdsp-through-unified-school-to-work-transition-framework-for-oriental-dabawenyos/',
                                                    ],
                                                    [
                                                        'img' => asset('images/cdsp-story/cdsp-3.png'),
                                                        'title' =>
                                                            'CDSP Prepares Tech-Voc Students for the Realities of Work',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/cdsp-prepares-tech-voc-students-for-the-realities-of-work/',
                                                    ],
                                                    [
                                                        'img' => asset('images/cdsp-story/cdsp-4.jpg'),
                                                        'title' => 'Byaheng CDSP Kickstarts at Far-Flung High School',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/byaheng-cdsp-kickstarts-at-far-flung-high-school/',
                                                    ],
                                                    [
                                                        'img' => asset('images/cdsp-story/cdsp-5.jpg'),
                                                        'title' =>
                                                            'Davao Job Mismatch Concern for Career Advocates, CDSP Among Its Solutions',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/davor-job-mismatch-concern-for-career-advocates-cdsp-among-its-solutions/',
                                                    ],
                                                ];
                                            @endphp
                                            @foreach ($cdspStories as $story)
                                                <a href="{{ $story['link'] }}" target="_blank"
                                                    class="group block bg-white rounded-xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                                                    <div class="relative overflow-hidden h-28">
                                                        <img src="{{ $story['img'] }}" alt="{{ $story['title'] }}"
                                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                        <div
                                                            class="absolute inset-0 bg-gradient-to-t from-slate-900/50 to-transparent">
                                                        </div>
                                                        <span
                                                            class="absolute bottom-1.5 right-1.5 bg-yellow-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">CDSP</span>
                                                    </div>
                                                    <div class="p-2.5">
                                                        <p
                                                            class="text-xs font-semibold text-slate-700 line-clamp-2 group-hover:text-yellow-600 transition-colors leading-snug">
                                                            {{ $story['title'] }}</p>
                                                        <span
                                                            class="text-xs text-yellow-600 font-medium mt-1 block">Read
                                                            →</span>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                                <div class="lg:col-span-1">
                                    <div
                                        class="bg-white border-2 border-yellow-200 rounded-xl p-6 shadow-lg sticky top-6">
                                        <div class="flex items-center gap-3 mb-6">
                                            <div
                                                class="w-11 h-11 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0">
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
                                                "Overall, the program gave me more confidence, discipline, and a clearer
                                                perspective on the path I took."
                                            </p>
                                        </blockquote>
                                        <div class="flex items-center gap-3 pt-4 border-t border-yellow-100">

                                            <div>
                                                <p class="font-bold text-slate-900 text-sm">Rian Jes Kryst L. Lamban
                                                </p>
                                                <p class="text-xs text-slate-500">CDSP Beneficiary</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ══════════════════════════════ END CDSP ══════════════════════════════ -->

                <!-- ══════════════════════════════ JOB FAIRS (CYAN) ══════════════════════════════ -->
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-full px-6 md:px-10 py-6 flex items-center justify-between hover:bg-cyan-50 transition-colors duration-200 group text-left">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl flex-shrink-0 flex items-center justify-center shadow-md transition-all duration-300 border-2"
                                :class="open ? 'bg-white border-cyan-400' :
                                    'bg-cyan-50 border-transparent group-hover:bg-cyan-100'">
                                <img src="{{ asset('images/logo-programs/jobfair_logo.jpg') }}" alt="Job Fair Logo"
                                    class="w-10 h-10 md:w-14 md:h-14 object-contain">
                            </div>
                            <div>
                                <h3 class="text-xl md:text-2xl font-bold text-slate-900 group-hover:text-cyan-600 transition-colors"
                                    :class="open ? 'text-cyan-600' : ''">
                                    Job Fairs
                                </h3>
                                <p class="text-sm md:text-base text-slate-500 mt-1">Employment facilitation —
                                    connecting jobseekers &amp; employers in one venue</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                            <span class="text-xs md:text-sm font-semibold hidden sm:block"
                                :class="open ? 'text-cyan-600' : 'text-slate-400'">
                                <span x-show="!open">Click to expand</span>
                                <span x-show="open" x-cloak>Click to collapse</span>
                            </span>
                            <div class="w-9 h-9 rounded-full flex items-center justify-center transition-colors duration-200"
                                :class="open ? 'bg-cyan-600' : 'bg-slate-100 group-hover:bg-cyan-100'">
                                <svg class="w-5 h-5 transition-transform duration-300 chevron-icon"
                                    :class="open ? 'open text-white' : 'text-slate-500'" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <div x-show="open" x-collapse x-cloak>
                        <div
                            class="border-t border-slate-200 bg-gradient-to-br from-slate-50 to-cyan-50/20 p-6 md:p-10">
                            <div class="grid lg:grid-cols-3 gap-8">
                                <div class="lg:col-span-2 space-y-6">

                                    <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-6">
                                        <h4 class="font-bold text-slate-800 mb-3 flex items-center gap-2 text-lg">
                                            <svg class="w-5 h-5 text-cyan-600 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Program Details
                                        </h4>
                                        <p class="text-slate-700 leading-relaxed">
                                            An employment facilitation strategy aimed to fast-track the meeting of
                                            jobseekers and employers/recruitment agencies in one venue at a specific
                                            date to reduce cost, time, and effort particularly on the part of the
                                            applicants. This is open to all unemployed, skilled and unskilled workers,
                                            college and senior high school graduates, graduates of training
                                            institutions, displaced workers, and employees seeking advancement.
                                        </p>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-cyan-600 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Objectives
                                            </h4>
                                            <ul class="space-y-3 text-slate-700 text-sm">
                                                <li class="flex items-start gap-2"><span
                                                        class="text-cyan-500 font-bold mt-0.5">•</span><span>Provide a
                                                        convenient venue for job seekers to meet potential employers,
                                                        reducing expenses and travel burdens</span></li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-cyan-500 font-bold mt-0.5">•</span><span>Support
                                                        employers in sourcing skilled workers and combat illegal
                                                        recruitment</span></li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-cyan-500 font-bold mt-0.5">•</span><span>Offer
                                                        training, self-employment assistance, and welfare services for
                                                        OFWs and their dependents</span></li>
                                            </ul>
                                        </div>
                                        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-cyan-600 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                                Services Offered
                                            </h4>
                                            <ul class="space-y-3 text-slate-700 text-sm">
                                                <li class="flex items-start gap-2"><span
                                                        class="text-cyan-500 font-bold mt-0.5">•</span> Job matching
                                                    through PhilJobNet</li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-cyan-500 font-bold mt-0.5">•</span> Career,
                                                    Vocational, and Employment Counseling</li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-cyan-500 font-bold mt-0.5">•</span> Training and
                                                    Referral Services</li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-cyan-500 font-bold mt-0.5">•</span> Livelihood
                                                    Assistance</li>
                                                <li class="flex items-start gap-2"><span
                                                        class="text-cyan-500 font-bold mt-0.5">•</span> Assistance on
                                                    the Issuance of Pre-Employment Requirements</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="bg-cyan-600 text-white rounded-xl p-6">
                                        <h4 class="font-bold mb-3 flex items-center gap-2 text-lg">
                                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            How to Avail
                                        </h4>
                                        <p class="text-sm">See the list of upcoming Job Fairs at <a
                                                href="https://ro11.dole.gov.ph/job-fair-schedules/" target="_blank"
                                                class="underline font-semibold hover:text-cyan-200">ro11.dole.gov.ph/job-fair-schedules/</a>
                                        </p>
                                    </div>

                                    <!-- Job Fair Stories -->
                                    <div>
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-1 h-6 bg-cyan-600 rounded-full"></div>
                                            <h4 class="font-bold text-slate-800">Job Fair Success Stories</h4>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                                            @php
                                                $jobfairStories = [
                                                    [
                                                        'img' => asset('images/jobfair-story/jobfair-1.jpg'),
                                                        'title' =>
                                                            'Successful Kadayawan Job Fair 2025: 129 Hired On-the-Spot Amidst 3,900 Vacancies',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/successful-kadayawan-job-fair-2025-129-hired-on-the-spot-amidst-3900-vacancies/',
                                                    ],
                                                    [
                                                        'img' => asset('images/jobfair-story/jobfair-2.jpg'),
                                                        'title' =>
                                                            'First JobStart Batch in Davao Oriental: 87 Graduates, 45 Hired on the Spot',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/first-jobstart-batch-in-davao-oriental-87-graduates-45-hired-on-the-spot/',
                                                    ],
                                                    [
                                                        'img' => asset('images/jobfair-story/jobfair-3.png'),
                                                        'title' =>
                                                            '172 Jobseekers Hired On-the-Spot at Kalayaan Job Fair in Davao City',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/172-jobseekers-hired-on-the-spot-at-kalayaan-job-fair-in-davao-city/',
                                                    ],
                                                    [
                                                        'img' => asset('images/jobfair-story/jobfair-4.png'),
                                                        'title' =>
                                                            '21.6% of Jobseekers Hired On-the-Spot in Davao Labor Day Job Fair',
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/21-6-jobseekers-hired-on-the-spot-in-davao-labor-day-job-fair/',
                                                    ],
                                                    [
                                                        'img' => asset('images/jobfair-story/jobfair-5.jpg'),
                                                        'title' => "Instant Yes: Arnel's Job Fair Success Story",
                                                        'link' =>
                                                            'https://ro11.dole.gov.ph/news/instant-yes-arnels-job-fair-success-story/',
                                                    ],
                                                ];
                                            @endphp
                                            @foreach ($jobfairStories as $story)
                                                <a href="{{ $story['link'] }}" target="_blank"
                                                    class="group block bg-white rounded-xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                                                    <div class="relative overflow-hidden h-28">
                                                        <img src="{{ $story['img'] }}" alt="{{ $story['title'] }}"
                                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                        <div
                                                            class="absolute inset-0 bg-gradient-to-t from-slate-900/50 to-transparent">
                                                        </div>
                                                        <span
                                                            class="absolute bottom-1.5 right-1.5 bg-cyan-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">Job
                                                            Fair</span>
                                                    </div>
                                                    <div class="p-2.5">
                                                        <p
                                                            class="text-xs font-semibold text-slate-700 line-clamp-2 group-hover:text-cyan-600 transition-colors leading-snug">
                                                            {{ $story['title'] }}</p>
                                                        <span class="text-xs text-cyan-600 font-medium mt-1 block">Read
                                                            →</span>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                                <div class="lg:col-span-1">
                                    <div
                                        class="bg-white border-2 border-cyan-200 rounded-xl p-6 shadow-lg sticky top-6">
                                        <div class="flex items-center gap-3 mb-6">
                                            <div
                                                class="w-11 h-11 bg-cyan-600 rounded-full flex items-center justify-center flex-shrink-0">
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
                                                "Participating in the job fair has been highly beneficial, providing
                                                numerous opportunities. As a result, companies have been reaching out
                                                for second interviews and extending job offers. This has significantly
                                                helped me advance my career and secure a position."
                                            </p>
                                        </blockquote>
                                        <div class="flex items-center gap-3 pt-4 border-t border-cyan-100">
                                            <div>
                                                <p class="font-bold text-slate-900 text-sm">Kenn Zyrez A. Unabia</p>
                                                <p class="text-xs text-slate-500">Job Fair Participant</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ══════════════════════════════ END JOB FAIRS ══════════════════════════════ -->

            </div>
        </div>
        <!-- ===== END PROGRAMS SECTION ===== -->


        <!-- Call to Action -->
        <div class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 py-20 mt-16">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <h3 class="text-4xl font-bold text-white mb-6">Ready to Start Your Journey?</h3>
                <p class="text-slate-300 text-xl mb-10 max-w-3xl mx-auto">
                    Join thousands of youth who have transformed their careers through DOLE's employment programs.
                </p>
                <div class="flex flex-wrap justify-center gap-6">
                    <a href="http://gip.dole11portal.org" target="_blank"
                        class="px-10 py-5 bg-white text-slate-900 font-bold text-lg rounded-xl hover:bg-slate-100 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                        Apply for GIP
                    </a>
                    <a href="#"
                        class="px-10 py-5 bg-transparent border-2 border-white text-white font-bold text-lg rounded-xl hover:bg-white hover:text-slate-900 transition-all duration-300 shadow-xl">
                        Visit Your Local PESO
                    </a>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
