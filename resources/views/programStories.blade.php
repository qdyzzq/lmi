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
    </style>
</head>

<body class="bg-slate-100 min-h-screen">
    <div x-data="{
        activeProgram: null,
        mobileMenuOpen: false,
        toggleProgram(program) {
            this.activeProgram = this.activeProgram === program ? null : program;
        }
    }">
        @include('partials.navbar')

        <!-- Success Stories Carousel - Full Height -->
        <div class="relative w-full h-screen overflow-hidden" x-data="{
            currentSlide: 0,
            slides: [{
                    image: '{{ asset('images/testimonials/GIP.jpg') }}',
                    title: 'From GIP Beneficiary to City HR Leader',
                    excerpt: 'Genevieve Elan Palmera\'s journey from an intern to a permanent HR position showcases the transformative power of the Government Internship Program.',
                    link: 'https://ro11.dole.gov.ph/news/from-dole-gip-beneficiary-to-city-hr-leader-a-youth-employment-success-story/',
                    program: 'GIP',
                    color: 'blue'
                },
                {
                    image: '{{ asset('images/testimonials/jobstart.jpg') }}',
                    title: 'Camp Holidays JobStart Success',
                    excerpt: 'From interns to full-fledged employees - discover how JobStart graduates transformed their careers through hands-on experience and dedication.',
                    link: 'https://ro11.dole.gov.ph/news/the-success-story-of-camp-holidays-jobstart-graduates-from-interns-to-full-fledged-employees/',
                    program: 'JobStart',
                    color: 'green'
                },
                {
                    image: '{{ asset('images/testimonials/SPES.jpg') }}',
                    title: 'SPES Grantee Achieves Latin Honors',
                    excerpt: 'Khacley Marino\'s inspiring journey from SPES beneficiary to graduating with Latin honors proves that financial support can unlock academic excellence.',
                    link: 'https://ro11.dole.gov.ph/news/spes-grantee-achieves-latin-honors-and-graduation-success-khacley-marinos-inspiring-journey/',
                    program: 'SPES',
                    color: 'orange'
                },
                {
                    image: '{{ asset('images/testimonials/CDSP.jpg') }}',
                    title: 'The Courage to Begin with CDSP',
                    excerpt: 'Philip Tecson\'s journey fresh out of college with DOLE\'s Career Development Service Program shows how proper guidance can shape a successful career path.',
                    link: 'https://ro11.dole.gov.ph/news/the-courage-to-begin-philip-tecsons-journey-fresh-out-of-college-with-doles-career-development-service-program/',
                    program: 'CDSP',
                    color: 'purple'
                }
            ],
            autoplay: true,
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
                this.autoplayInterval = setInterval(() => {
                    this.nextSlide();
                }, 5000);
            },
            stopAutoplay() {
                if (this.autoplayInterval) {
                    clearInterval(this.autoplayInterval);
                }
            }
        }" x-init="startAutoplay()"
            @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">

            <!-- Slides -->
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="currentSlide === index" x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0 transform translate-x-full"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform -translate-x-full" class="absolute inset-0">

                    <!-- Background Image -->
                    <div class="absolute inset-0">
                        <img :src="slide.image" :alt="slide.title"
                            class="w-full h-full object-cover object-center">
                        <div
                            class="absolute inset-0 bg-gradient-to-b from-slate-900/85 via-slate-900/70 to-slate-900/50">
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="relative z-10 h-full flex items-center justify-center px-4">
                        <div class="text-center text-white max-w-5xl">
                            <!-- Program Badge -->
                            <div class="inline-block mb-6 animate-fade-in">
                                <span class="px-6 py-3 rounded-full text-base font-bold backdrop-blur-md shadow-2xl"
                                    :class="{
                                        'bg-blue-500/40 border-2 border-blue-300/60': slide.color === 'blue',
                                        'bg-green-500/40 border-2 border-green-300/60': slide.color === 'green',
                                        'bg-orange-500/40 border-2 border-orange-300/60': slide.color === 'orange',
                                        'bg-purple-500/40 border-2 border-purple-300/60': slide.color === 'purple'
                                    }"
                                    x-text="slide.program + ' Success Story'">
                                </span>
                            </div>

                            <!-- Title -->
                            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold mb-8 drop-shadow-2xl leading-tight"
                                x-text="slide.title">
                            </h1>

                            <!-- Excerpt -->
                            <p class="text-xl md:text-2xl lg:text-3xl text-slate-50 drop-shadow-lg mb-12 max-w-4xl mx-auto leading-relaxed font-light"
                                x-text="slide.excerpt">
                            </p>

                            <!-- Read More Button -->
                            <a :href="slide.link" target="_blank"
                                class="inline-flex items-center gap-3 px-10 py-5 bg-white text-slate-900 font-bold text-lg rounded-xl hover:bg-slate-100 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:-translate-y-2 hover:scale-105">
                                <span>READ FULL STORY</span>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Scroll Down Indicator -->
                    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce hidden md:block">
                        <div class="flex flex-col items-center gap-2 text-white/70">
                            <span class="text-sm font-medium">Scroll to explore programs</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Navigation Arrows -->
            <button @click="prevSlide()"
                class="absolute left-6 top-1/2 -translate-y-1/2 z-20 w-14 h-14 bg-white/25 hover:bg-white/40 backdrop-blur-md rounded-full flex items-center justify-center transition-all duration-300 group border border-white/30">
                <svg class="w-7 h-7 text-white group-hover:scale-125 transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <button @click="nextSlide()"
                class="absolute right-6 top-1/2 -translate-y-1/2 z-20 w-14 h-14 bg-white/25 hover:bg-white/40 backdrop-blur-md rounded-full flex items-center justify-center transition-all duration-300 group border border-white/30">
                <svg class="w-7 h-7 text-white group-hover:scale-125 transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Slide Indicators -->
            <div class="absolute bottom-24 left-1/2 -translate-x-1/2 z-20 flex items-center gap-4">
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="goToSlide(index)" class="transition-all duration-300"
                        :class="currentSlide === index ? 'w-16 h-4' : 'w-4 h-4'">
                        <div class="w-full h-full rounded-full backdrop-blur-md border-2"
                            :class="currentSlide === index ? 'bg-white border-white' :
                                'bg-white/40 border-white/60 hover:bg-white/70'">
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <!-- Programs Section -->
        <div class="max-w-7xl mx-auto px-6 py-16">

            <div
                class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 rounded-2xl px-10 py-8 shadow-2xl mb-1">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/10 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-3xl">DOLE Employment Programs</h2>
                        <p class="text-slate-300 text-base">Click on any program below to view details and success
                            stories
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">

                <!-- GIP Program -->
                <div class="border-b border-slate-200">
                    <button @click="toggleProgram('gip')"
                        class="w-full px-10 py-8 flex items-center justify-between hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-50/30 transition-all duration-300 group">
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div
                                    class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-50 rounded-2xl flex items-center justify-center group-hover:from-blue-600 group-hover:to-blue-700 transition-all duration-300 shadow-lg group-hover:shadow-xl group-hover:scale-110">
                                    <img src="{{ asset('images/logo-programs/gip_logo.png') }}" alt="GIP Logo"
                                        class="w-14 h-14 object-contain">
                                </div>
                            </div>
                            <div class="text-left">
                                <h3
                                    class="text-2xl font-bold text-slate-900 group-hover:text-blue-600 transition-colors mb-2">
                                    Government Internship Program
                                </h3>
                                <p class="text-base text-slate-600">GIP - 3-6 months internship opportunity in
                                    government
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-semibold text-slate-500 hidden lg:block"
                                x-show="activeProgram !== 'gip'">Click to expand</span>
                            <span class="text-sm font-semibold text-blue-600 hidden lg:block"
                                x-show="activeProgram === 'gip'">Click to collapse</span>
                            <div
                                class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-blue-100 flex items-center justify-center transition-all">
                                <svg class="w-6 h-6 text-slate-600 group-hover:text-blue-600 transition-all duration-300"
                                    :class="activeProgram === 'gip' ? 'rotate-180' : ''" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <div x-show="activeProgram === 'gip'" x-collapse class="border-t border-slate-200">
                        <div class="p-10 bg-gradient-to-br from-slate-50 to-blue-50/30">
                            <div class="grid lg:grid-cols-3 gap-10">
                                <div class="lg:col-span-2 space-y-8">
                                    <div
                                        class="bg-gradient-to-br from-blue-50 to-blue-100/50 border-2 border-blue-200 rounded-2xl p-8 shadow-lg">
                                        <h4 class="font-bold text-slate-900 mb-4 flex items-center gap-3 text-xl">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Program Details
                                        </h4>
                                        <p class="text-slate-700 leading-relaxed text-lg">
                                            A youth employability program which aims to provide 3-6 months internship
                                            opportunity in the government for high school, technical-vocational or
                                            college graduates to build their capabilities and make them more employable.
                                        </p>
                                    </div>

                                    <div class="bg-white border-2 border-slate-200 rounded-2xl p-8 shadow-lg">
                                        <h4 class="font-bold text-slate-900 mb-6 flex items-center gap-3 text-xl">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Qualifications
                                        </h4>
                                        <ul class="space-y-4 text-slate-700">
                                            <li class="flex items-start gap-4">
                                                <span
                                                    class="text-blue-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                <span class="text-base">18 to 30 years old, with exceptions determined
                                                    by DOLE Regional Offices</span>
                                            </li>
                                            <li class="flex items-start gap-4">
                                                <span
                                                    class="text-blue-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                <span class="text-base">Individuals aged 31 and above may qualify under
                                                    specific conditions, including no or intermittent work experience,
                                                    being laid off or terminated due to establishment closure, or being
                                                    displaced by disasters</span>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="bg-white border-2 border-slate-200 rounded-2xl p-8 shadow-lg">
                                        <h4 class="font-bold text-slate-900 mb-6 flex items-center gap-3 text-xl">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                            Education Requirements
                                        </h4>
                                        <ul class="space-y-4 text-slate-700">
                                            <li class="flex items-start gap-4">
                                                <span
                                                    class="text-blue-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                <span class="text-base">High school/Senior High School Graduate or
                                                    equivalent, or technical-vocational graduate</span>
                                            </li>
                                            <li class="flex items-start gap-4">
                                                <span
                                                    class="text-blue-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                <span class="text-base">Additional eligible groups: Victims of armed
                                                    conflicts, rebel returnees, Persons with Disabilities (PWDs) and
                                                    Indigenous Peoples</span>
                                            </li>
                                            <li class="flex items-start gap-4">
                                                <span
                                                    class="text-blue-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                <span class="text-base">Work experience requirement is not
                                                    needed/exempted and applicable to individuals identified under
                                                    Section 2 (d) and (e) of Department Order No. 204-A</span>
                                            </li>
                                            <li class="flex items-start gap-4">
                                                <span
                                                    class="text-blue-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                <span class="text-base">Transcript of Records for college students or
                                                    Form 137 for high school graduates</span>
                                            </li>
                                            <li class="flex items-start gap-4">
                                                <span
                                                    class="text-blue-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                <span class="text-base">Certificate of Graduation in case of voc-tech
                                                    graduates; and Certificate of Indigence from the Barangay</span>
                                            </li>
                                        </ul>
                                    </div>

                                    <div
                                        class="bg-gradient-to-br from-blue-600 to-blue-700 text-white rounded-2xl p-8 shadow-xl">
                                        <h4 class="font-bold mb-6 flex items-center gap-3 text-2xl">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            How to Apply
                                        </h4>
                                        <ol class="space-y-4 text-lg">
                                            <li class="flex items-start gap-4">
                                                <span class="font-bold text-blue-200 text-xl flex-shrink-0">1.</span>
                                                <span>Visit the GIP system for Region 11: <a
                                                        href="http://gip.dole11portal.org"
                                                        class="underline hover:text-blue-200 font-semibold"
                                                        target="_blank">gip.dole11portal.org</a> to register</span>
                                            </li>
                                            <li class="flex items-start gap-4">
                                                <span class="font-bold text-blue-200 text-xl flex-shrink-0">2.</span>
                                                <span>Wait for correspondence on approval/acceptance</span>
                                            </li>
                                        </ol>
                                    </div>
                                </div>

                                <div class="lg:col-span-1">
                                    <div
                                        class="bg-gradient-to-br from-white to-blue-50 border-2 border-blue-200 rounded-2xl p-8 shadow-xl sticky top-6">
                                        <div class="flex items-center gap-4 mb-8">
                                            <div
                                                class="w-14 h-14 bg-blue-600 rounded-full flex items-center justify-center shadow-lg">
                                                <svg class="w-7 h-7 text-white" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                                </svg>
                                            </div>
                                            <h4 class="text-2xl font-bold text-slate-900">Success Story</h4>
                                        </div>

                                        <blockquote class="mb-8">
                                            <p class="text-slate-700 text-lg leading-relaxed italic">
                                                "The program allowed me to prove my capability and work ethic. It was
                                                the bridge from where I started, uncertain and struggling, to where I
                                                stand now, more confident, more skilled, and part of an institution I
                                                deeply respect."
                                            </p>
                                        </blockquote>

                                        <div class="flex items-center gap-4 pt-6 border-t-2 border-blue-200">
                                            <div
                                                class="w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-700 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                                                GE
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 text-lg">Genevieve Elan Palmera</p>
                                                <p class="text-sm text-slate-600">GIP Beneficiary</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- JobStart Program -->
                <div class="border-b border-slate-200">
                    <button @click="toggleProgram('jobstart')"
                        class="w-full px-10 py-8 flex items-center justify-between hover:bg-gradient-to-r hover:from-green-50 hover:to-green-50/30 transition-all duration-300 group">
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div
                                    class="w-20 h-20 bg-gradient-to-br from-green-100 to-green-50 rounded-2xl flex items-center justify-center group-hover:from-green-600 group-hover:to-green-700 transition-all duration-300 shadow-lg group-hover:shadow-xl group-hover:scale-110">
                                    <img src="{{ asset('images/logo-programs/jobstart_logo.png') }}"
                                        alt="JobStart Logo" class="w-14 h-14 object-contain">
                                </div>
                            </div>
                            <div class="text-left">
                                <h3
                                    class="text-2xl font-bold text-slate-900 group-hover:text-green-600 transition-colors mb-2">
                                    JobStart Program
                                </h3>
                                <p class="text-base text-slate-600">Youth employment initiative with career coaching &
                                    training</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-semibold text-slate-500 hidden lg:block"
                                x-show="activeProgram !== 'jobstart'">Click to expand</span>
                            <span class="text-sm font-semibold text-green-600 hidden lg:block"
                                x-show="activeProgram === 'jobstart'">Click to collapse</span>
                            <div
                                class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-green-100 flex items-center justify-center transition-all">
                                <svg class="w-6 h-6 text-slate-600 group-hover:text-green-600 transition-all duration-300"
                                    :class="activeProgram === 'jobstart' ? 'rotate-180' : ''" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <div x-show="activeProgram === 'jobstart'" x-collapse class="border-t border-slate-200">
                        <div class="p-10 bg-gradient-to-br from-slate-50 to-green-50/30">
                            <div class="grid lg:grid-cols-3 gap-10">
                                <div class="lg:col-span-2 space-y-8">
                                    <div
                                        class="bg-gradient-to-br from-green-50 to-green-100/50 border-2 border-green-200 rounded-2xl p-8 shadow-lg">
                                        <h4 class="font-bold text-slate-900 mb-4 flex items-center gap-3 text-xl">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Program Details
                                        </h4>
                                        <p class="text-slate-700 leading-relaxed text-lg">
                                            A youth employability program which aims to shorten the school-to-work
                                            transition of youth not in education, employment, or training by providing
                                            them with career coaching, life skills and technical training, and
                                            internships with employers.
                                        </p>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div class="bg-white border-2 border-slate-200 rounded-2xl p-8 shadow-lg">
                                            <h4 class="font-bold text-slate-900 mb-6 flex items-center gap-3 text-xl">
                                                <svg class="w-6 h-6 text-green-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Qualifications
                                            </h4>
                                            <ul class="space-y-4 text-slate-700">
                                                <li class="flex items-start gap-4">
                                                    <span
                                                        class="text-green-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                    <span class="text-base">Filipino Citizen; 18-24 years old</span>
                                                </li>
                                                <li class="flex items-start gap-4">
                                                    <span
                                                        class="text-green-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                    <span class="text-base">Currently not in education, employment, or
                                                        training (NEET)</span>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="bg-white border-2 border-slate-200 rounded-2xl p-8 shadow-lg">
                                            <h4 class="font-bold text-slate-900 mb-6 flex items-center gap-3 text-xl">
                                                <svg class="w-6 h-6 text-purple-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                                Requirements
                                            </h4>
                                            <ul class="space-y-4 text-slate-700">
                                                <li class="flex items-start gap-4">
                                                    <span
                                                        class="text-green-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                    <span class="text-base">Reached at least grade 7 or first year high
                                                        school</span>
                                                </li>
                                                <li class="flex items-start gap-4">
                                                    <span
                                                        class="text-green-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                    <span class="text-base">0-12 months work experience</span>
                                                </li>
                                                <li class="flex items-start gap-4">
                                                    <span
                                                        class="text-green-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                    <span class="text-base">Actively looking for work</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div
                                        class="bg-gradient-to-br from-green-600 to-green-700 text-white rounded-2xl p-8 shadow-xl">
                                        <h4 class="font-bold mb-6 flex items-center gap-3 text-2xl">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            How to Apply
                                        </h4>
                                        <p class="mb-6 text-lg">Registration takes place at participating Public
                                            Employment Service Office (PESO) on a first-come, first served basis on
                                            JobStart implementation sites:</p>
                                        <ul class="grid md:grid-cols-2 gap-3 ml-4 text-lg">
                                            <li>• Davao City</li>
                                            <li>• Panabo City</li>
                                            <li>• Tagum City</li>
                                            <li>• Island Garden City of Samal (IGACOS)</li>
                                            <li>• Davao del Sur</li>
                                            <li>• Davao Oriental</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="lg:col-span-1">
                                    <div
                                        class="bg-gradient-to-br from-white to-green-50 border-2 border-green-200 rounded-2xl p-8 shadow-xl sticky top-6">
                                        <div class="flex items-center gap-4 mb-8">
                                            <div
                                                class="w-14 h-14 bg-green-600 rounded-full flex items-center justify-center shadow-lg">
                                                <svg class="w-7 h-7 text-white" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                                </svg>
                                            </div>
                                            <h4 class="text-2xl font-bold text-slate-900">Success Story</h4>
                                        </div>

                                        <blockquote class="mb-8">
                                            <p class="text-slate-700 text-lg leading-relaxed italic">
                                                "This program gave me not just a job, but also confidence, direction,
                                                and growth. I am truly grateful to DOLE and the JobStart Program for
                                                helping me believe in myself and shaping my career journey."
                                            </p>
                                        </blockquote>

                                        <div class="flex items-center gap-4 pt-6 border-t-2 border-green-200">
                                            <div
                                                class="w-16 h-16 bg-gradient-to-br from-green-600 to-green-700 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                                                EC
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 text-lg">Elen C. Ocon</p>
                                                <p class="text-sm text-slate-600">JobStart Beneficiary</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SPES Program -->
                <div class="border-b border-slate-200">
                    <button @click="toggleProgram('spes')"
                        class="w-full px-10 py-8 flex items-center justify-between hover:bg-gradient-to-r hover:from-orange-50 hover:to-orange-50/30 transition-all duration-300 group">
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div
                                    class="w-20 h-20 bg-gradient-to-br from-orange-100 to-orange-50 rounded-2xl flex items-center justify-center group-hover:from-orange-600 group-hover:to-orange-700 transition-all duration-300 shadow-lg group-hover:shadow-xl group-hover:scale-110">
                                    <img src="{{ asset('images/logo-programs/spes_logo.png') }}" alt="SPES Logo"
                                        class="w-14 h-14 object-contain">
                                </div>
                            </div>
                            <div class="text-left">
                                <h3
                                    class="text-2xl font-bold text-slate-900 group-hover:text-orange-600 transition-colors mb-2">
                                    Special Program for Employment of Students
                                </h3>
                                <p class="text-base text-slate-600">SPES - Short-term employment for underprivileged
                                    students</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-semibold text-slate-500 hidden lg:block"
                                x-show="activeProgram !== 'spes'">Click to expand</span>
                            <span class="text-sm font-semibold text-orange-600 hidden lg:block"
                                x-show="activeProgram === 'spes'">Click to collapse</span>
                            <div
                                class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-orange-100 flex items-center justify-center transition-all">
                                <svg class="w-6 h-6 text-slate-600 group-hover:text-orange-600 transition-all duration-300"
                                    :class="activeProgram === 'spes' ? 'rotate-180' : ''" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <div x-show="activeProgram === 'spes'" x-collapse class="border-t border-slate-200">
                        <div class="p-10 bg-gradient-to-br from-slate-50 to-orange-50/30">
                            <div class="grid lg:grid-cols-3 gap-10">
                                <div class="lg:col-span-2 space-y-8">
                                    <div
                                        class="bg-gradient-to-br from-orange-50 to-orange-100/50 border-2 border-orange-200 rounded-2xl p-8 shadow-lg">
                                        <h4 class="font-bold text-slate-900 mb-4 flex items-center gap-3 text-xl">
                                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Program Details
                                        </h4>
                                        <p class="text-slate-700 leading-relaxed text-lg">
                                            A youth employability program which aims to provide short-term employment to
                                            underprivileged students, out-of-school youth, and dependents of displaced
                                            or would-be displaced workers. The program helps in augmenting the family's
                                            income and in ensuring that beneficiaries are able to pursue their
                                            education.
                                        </p>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div class="bg-white border-2 border-slate-200 rounded-2xl p-8 shadow-lg">
                                            <h4 class="font-bold text-slate-900 mb-6 flex items-center gap-3 text-xl">
                                                <svg class="w-6 h-6 text-orange-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Qualifications
                                            </h4>
                                            <ul class="space-y-4 text-slate-700">
                                                <li class="flex items-start gap-4">
                                                    <span
                                                        class="text-orange-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                    <span class="text-base">Students or OSY who are at least 15 but not
                                                        more than 30 years of age</span>
                                                </li>
                                                <li class="flex items-start gap-4">
                                                    <span
                                                        class="text-orange-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                    <span class="text-base">Combined net income after tax of parents,
                                                        including his or her own, if any, does not exceed the regional
                                                        poverty threshold</span>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="bg-white border-2 border-slate-200 rounded-2xl p-8 shadow-lg">
                                            <h4 class="font-bold text-slate-900 mb-6 flex items-center gap-3 text-xl">
                                                <svg class="w-6 h-6 text-purple-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                                Requirements
                                            </h4>
                                            <ul class="space-y-4 text-slate-700">
                                                <li class="flex items-start gap-4">
                                                    <span
                                                        class="text-orange-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                    <span class="text-base">Must have obtained a passing general
                                                        weighted average during the last semester or school year
                                                        attended</span>
                                                </li>
                                                <li class="flex items-start gap-4">
                                                    <span
                                                        class="text-orange-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                    <span class="text-base">Must be certified by the barangay or local
                                                        social welfare and development office (SWDO) as OSY</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div
                                        class="bg-gradient-to-br from-orange-600 to-orange-700 text-white rounded-2xl p-8 shadow-xl">
                                        <h4 class="font-bold mb-6 flex items-center gap-3 text-2xl">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            How to Apply
                                        </h4>
                                        <p class="text-lg">Registration takes place at participating Public Employment
                                            Service Offices (PESO) on a first-come, first served basis.</p>
                                    </div>
                                </div>

                                <div class="lg:col-span-1">
                                    <div
                                        class="bg-gradient-to-br from-white to-orange-50 border-2 border-orange-200 rounded-2xl p-8 shadow-xl sticky top-6">
                                        <div class="flex items-center gap-4 mb-8">
                                            <div
                                                class="w-14 h-14 bg-orange-600 rounded-full flex items-center justify-center shadow-lg">
                                                <svg class="w-7 h-7 text-white" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                                </svg>
                                            </div>
                                            <h4 class="text-2xl font-bold text-slate-900">Success Story</h4>
                                        </div>

                                        <blockquote class="mb-8">
                                            <p class="text-slate-700 text-lg leading-relaxed italic">
                                                "The program helped me by easing the financial pressure that made it
                                                hard for me to stay in school. The income I earned supported my
                                                education and personal needs, allowing me to focus more on my studies
                                                instead of worrying every day about expenses. SPES helped me push
                                                through my challenges and complete my degree, which opened the door for
                                                better opportunities."
                                            </p>
                                        </blockquote>

                                        <div class="flex items-center gap-4 pt-6 border-t-2 border-orange-200">
                                            <div
                                                class="w-16 h-16 bg-gradient-to-br from-orange-600 to-orange-700 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                                                MQ
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 text-lg">Mark Jay G. Quinto</p>
                                                <p class="text-sm text-slate-600">SPES Beneficiary</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CDSP Program -->
                <div>
                    <button @click="toggleProgram('cdsp')"
                        class="w-full px-10 py-8 flex items-center justify-between hover:bg-gradient-to-r hover:from-purple-50 hover:to-purple-50/30 transition-all duration-300 group">
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div
                                    class="w-20 h-20 bg-gradient-to-br from-purple-100 to-purple-50 rounded-2xl flex items-center justify-center group-hover:from-purple-600 group-hover:to-purple-700 transition-all duration-300 shadow-lg group-hover:shadow-xl group-hover:scale-110">
                                    <img src="{{ asset('images/logo-programs/cdsp_logo.png') }}" alt="CDSP Logo"
                                        class="w-14 h-14 object-contain">
                                </div>
                            </div>
                            <div class="text-left">
                                <h3
                                    class="text-2xl font-bold text-slate-900 group-hover:text-purple-600 transition-colors mb-2">
                                    Career Development Support Program
                                </h3>
                                <p class="text-base text-slate-600">CDSP - Career counseling & employment support
                                    services</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-semibold text-slate-500 hidden lg:block"
                                x-show="activeProgram !== 'cdsp'">Click to expand</span>
                            <span class="text-sm font-semibold text-purple-600 hidden lg:block"
                                x-show="activeProgram === 'cdsp'">Click to collapse</span>
                            <div
                                class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-purple-100 flex items-center justify-center transition-all">
                                <svg class="w-6 h-6 text-slate-600 group-hover:text-purple-600 transition-all duration-300"
                                    :class="activeProgram === 'cdsp' ? 'rotate-180' : ''" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <div x-show="activeProgram === 'cdsp'" x-collapse class="border-t border-slate-200">
                        <div class="p-10 bg-gradient-to-br from-slate-50 to-purple-50/30">
                            <div class="grid lg:grid-cols-3 gap-10">
                                <div class="lg:col-span-2 space-y-8">
                                    <div
                                        class="bg-gradient-to-br from-purple-50 to-purple-100/50 border-2 border-purple-200 rounded-2xl p-8 shadow-lg">
                                        <h4 class="font-bold text-slate-900 mb-4 flex items-center gap-3 text-xl">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Program Details
                                        </h4>
                                        <p class="text-slate-700 leading-relaxed text-lg mb-4">
                                            CDSP is a public employment service which aims to address gaps in
                                            employability dimensions i.e personal and environmental factors, job
                                            objectives, skills and requirements to perform the job, job search skills,
                                            and ability to maintain a job, through career, vocational, and employment
                                            counseling.
                                        </p>
                                        <p class="text-slate-700 leading-relaxed text-lg">
                                            The objective is to assist individuals to find the right job, identify
                                            appropriate upskilling or reskilling interventions and progress in their
                                            chosen career path.
                                        </p>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div class="bg-white border-2 border-slate-200 rounded-2xl p-8 shadow-lg">
                                            <h4 class="font-bold text-slate-900 mb-6 flex items-center gap-3 text-xl">
                                                <svg class="w-6 h-6 text-purple-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                Beneficiaries
                                            </h4>
                                            <ul class="space-y-3 text-slate-700 text-sm">
                                                <li class="flex items-start gap-2">
                                                    <span
                                                        class="text-purple-600 mt-1 text-lg font-bold flex-shrink-0">•</span>
                                                    <span>Jobseekers</span>
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <span
                                                        class="text-purple-600 mt-1 text-lg font-bold flex-shrink-0">•</span>
                                                    <span>Employers</span>
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <span
                                                        class="text-purple-600 mt-1 text-lg font-bold flex-shrink-0">•</span>
                                                    <span>Students, Youth</span>
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <span
                                                        class="text-purple-600 mt-1 text-lg font-bold flex-shrink-0">•</span>
                                                    <span>Migrant Workers</span>
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <span
                                                        class="text-purple-600 mt-1 text-lg font-bold flex-shrink-0">•</span>
                                                    <span>Long-term Unemployed</span>
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <span
                                                        class="text-purple-600 mt-1 text-lg font-bold flex-shrink-0">•</span>
                                                    <span>Persons with Disabilities</span>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="bg-white border-2 border-slate-200 rounded-2xl p-8 shadow-lg">
                                            <h4 class="font-bold text-slate-900 mb-6 flex items-center gap-3 text-xl">
                                                <svg class="w-6 h-6 text-purple-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                                Services
                                            </h4>
                                            <ul class="space-y-4 text-slate-700">
                                                <li class="flex items-start gap-4">
                                                    <span
                                                        class="text-purple-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                    <span class="text-base">Career Counseling</span>
                                                </li>
                                                <li class="flex items-start gap-4">
                                                    <span
                                                        class="text-purple-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                    <span class="text-base">Vocational Counseling</span>
                                                </li>
                                                <li class="flex items-start gap-4">
                                                    <span
                                                        class="text-purple-600 mt-1 text-2xl font-bold flex-shrink-0">•</span>
                                                    <span class="text-base">Employment Counseling</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div
                                        class="bg-gradient-to-br from-purple-600 to-purple-700 text-white rounded-2xl p-8 shadow-xl">
                                        <h4 class="font-bold mb-6 flex items-center gap-3 text-2xl">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            How to Avail
                                        </h4>
                                        <p class="text-lg">Registration takes place at participating Public Employment
                                            Service Offices (PESO) and Partner Job Placement Offices for Students.</p>
                                    </div>
                                </div>

                                <div class="lg:col-span-1">
                                    <div
                                        class="bg-gradient-to-br from-white to-purple-50 border-2 border-purple-200 rounded-2xl p-8 shadow-xl sticky top-6">
                                        <div class="flex items-center gap-4 mb-8">
                                            <div
                                                class="w-14 h-14 bg-purple-600 rounded-full flex items-center justify-center shadow-lg">
                                                <svg class="w-7 h-7 text-white" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                                </svg>
                                            </div>
                                            <h4 class="text-2xl font-bold text-slate-900">Success Story</h4>
                                        </div>

                                        <blockquote class="mb-8">
                                            <p class="text-slate-700 text-lg leading-relaxed italic">
                                                "Overall, the program gave me more confidence, discipline, and a clearer
                                                perspective on the path I took."
                                            </p>
                                        </blockquote>

                                        <div class="flex items-center gap-4 pt-6 border-t-2 border-purple-200">
                                            <div
                                                class="w-16 h-16 bg-gradient-to-br from-purple-600 to-purple-700 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                                                RL
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 text-lg">Rian Jes Kryst L. Lamban
                                                </p>
                                                <p class="text-sm text-slate-600">CDSP Beneficiary</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Call to Action -->
        <div class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 py-20 mt-20">
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