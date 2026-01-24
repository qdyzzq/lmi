<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('path/to/chart-filtering.js') }}"></script>

    <title>LMI</title>
</head>

<body class="bg-slate-100 flex min-h-screen ">
    <div x-data="{
        activeView: 'job-market',
        showReportModal: false,
        showLmiMatrix: false,
        sidebarExpanded: true
    }" class="flex w-full h-full">


        <div :class="(showLmiMatrix) ? 'blur-sm' : ''" class="flex w-full h-full transition-all duration-200">

            <!-- SIDEBAR -->
            @include('partials.sidebar')

            <div class="flex-1 flex flex-col overflow-y-auto">
                <div x-show="activeView === 'job-market'" x-transition>
                    <div class="space-y-6 m-5">

                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                                    <span class="text-blue-600">📈</span>
                                    Davao Employment Dashboard
                                </h1>
                                <p class="text-sm text-slate-500">
                                    Regional Labor Market Intelligence & Trends
                                </p>
                            </div>
                        </div>


                        <div class="bg-slate-900 rounded-xl p-6 text-white flex justify-between items-center shadow-lg">
                            <div class="flex items-start gap-4">
                                <div class="p-2 bg-emerald-500/20 rounded-lg text-emerald-400">🤝</div>
                                <div>
                                    <h2 class="text-lg font-bold">Help us map the future of Davao's workforce.</h2>
                                    <p class="text-sm text-slate-400 max-w-xl">Official data lags behind real-time
                                        market needs. Help us bridge the gap by identifying hard-to-fill roles and
                                        critical skill shortages.</p>
                                </div>
                            </div>
                            <div class="flex gap-3">

                                <button @click="showLmiMatrix = true"
                                    class="bg-emerald-500 border border-emerald-500 text-white  px-5 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-500/20 transition">
                                    Update LMI Matrix
                                </button>
                            </div>
                        </div>


                        <div class="grid grid-cols-12 gap-6">
                            <!-- High Volume Jobs Chart -->
                            <div class="col-span-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                                <div class="flex justify-between mb-4">
                                    <h3 class="font-bold text-gray-800">Top 10 High-Volume Job Titles</h3>
                                    <span class="text-gray-300">ⓘ</span>
                                </div>
                                <canvas id="jobsChart" height="140"></canvas>
                            </div>

                            <!-- Hard to Fill Roles -->
                            <div class="col-span-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                                <div class="flex justify-between mb-4">
                                    <h3 class="font-bold text-gray-800">Hard-to-Fill Roles</h3>
                                    <span class="text-gray-300">ⓘ</span>
                                </div>
                                <div class="space-y-5">
                                    @foreach ($hard_to_fill as $job)
                                        <div class="flex justify-between items-center">
                                            <div class="space-y-1">
                                                <p class="font-bold text-sm text-slate-800">{{ $job['role'] }}</p>
                                                <p class="text-[10px] text-gray-400 flex items-center gap-1 uppercase">
                                                    🕒 Bottleneck: {{ $job['bottleneck'] }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-red-500 font-bold text-xs">{{ $job['days'] }} days</p>
                                                <p class="text-[9px] text-gray-300">({{ $job['year'] }})</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>


                        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                            <h3 class="font-bold text-lg mb-4">"Critical Skill Gaps" Per Sector</h3>


                            <div class="flex gap-2 mb-8 pb-5 border-b border-gray-200">
                                @foreach (['All', 'BPO/IT', 'Construction', 'Healthcare', 'Agriculture', 'Tourism'] as $tab)
                                    <button
                                        class="px-4 py-1 text-sm rounded-full {{ $loop->first ? 'bg-purple-600 text-white' : 'border text-gray-500 hover:bg-gray-50' }} transition">{{ $tab }}</button>
                                @endforeach
                            </div>


                            <div class="grid grid-cols-2 gap-12 ">

                                <div class="border-r border-gray-200 ">
                                    <h4 class="text-xs font-bold text-gray-400 mb-4 uppercase">🚫 Missing Soft Skills
                                        (Critical Gaps)</h4>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach ($soft_skills as $skill)
                                            <div class="bg-red-100 text-red-800 px-3 py-2 rounded-lg text-sm">
                                                {{ $skill['name'] }} <span
                                                    class="text-[10px] opacity-60">({{ $skill['sector'] }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>


                                <div>
                                    <h4 class="text-xs font-bold text-gray-400 mb-4 uppercase">🔍 Missing Technical
                                        Skills</h4>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach ($tech_skills as $skill)
                                            <div class="bg-blue-100 text-blue-800 px-3 py-2 rounded-lg text-sm">
                                                {{ $skill['name'] }} <span
                                                    class="text-[10px] opacity-60">({{ $skill['sector'] }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LMI Matrix Table -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="p-6 border-b flex justify-between items-center">
                                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                    <span class="text-emerald-500">田</span> LMI Granularity Matrix Results: Competency
                                    Gap Analysis
                                </h3>
                                <button
                                    class="text-emerald-600 border border-emerald-100 bg-emerald-50 px-3 py-1 rounded text-xs hover:bg-emerald-100 transition">
                                    Export Analysis
                                </button>
                            </div>
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-400 uppercase text-[10px] font-bold">
                                    <tr>
                                        <th class="px-6 py-4">Job Title / Role</th>
                                        <th class="px-6 py-4">Sector</th>
                                        <th class="px-6 py-4">Missing Skill / Competency</th>
                                        <th class="px-6 py-4 text-center">Type</th>
                                        <th class="px-6 py-4">Required Level</th>
                                        <th class="px-6 py-4">Observed Level</th>
                                        <th class="px-6 py-4">Gap Impact</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y border-t">
                                    @foreach ($matrix_results as $row)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 font-bold text-slate-800">{{ $row['role'] }}</td>
                                            <td class="px-6 py-4 text-xs font-medium text-gray-500 uppercase">
                                                {{ $row['sector'] }}</td>
                                            <td class="px-6 py-4 text-blue-600 font-medium">{{ $row['skill'] }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span
                                                    class="px-2 py-0.5 rounded text-[10px] border {{ $row['type'] == 'Hard' ? 'text-blue-500 border-blue-200' : 'text-pink-500 border-pink-200' }}">{{ $row['type'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-gray-500">{{ $row['req'] }}</td>
                                            <td class="px-6 py-4 text-gray-500">{{ $row['obs'] }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span
                                                    class="px-3 py-1 rounded-md text-[10px] font-bold {{ $row['impact'] == 'Critical' ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600' }}">
                                                    {{ $row['impact'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>


                        <div class="flex items-center justify-center">
                            <p class="text-xs text-slate-500">
                                Source: Tab1-Employment-Davao-Region-with-JUL2025.xlsx (Rates) | Module 2 Sources:
                                PhilJobNet, PSA ISLE, Industry Surveys.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showLmiMatrix" class="fixed inset-0 z-50 flex items-center justify-center px-4" x-cloak
            style="display: none;">
            <div @click.away="showLmiMatrix = false"
                class="bg-white rounded-2xl shadow-2xl w-full w-[96vw] h-[96vh]
         max-w-[96vw] max-h-[96vh] overflow-hidden transition-all transform">


                <div class="bg-teal-700 p-5 flex justify-between items-center text-white sticky top-0 z-10">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-bold">INDUSTRY SKILLS NEED SURVEY</h3>
                    </div>
                    <button @click="showLmiMatrix = false" class="text-white hover:bg-teal-600 p-1 rounded transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="overflow-y-auto h-[calc(96vh-96px)]">

                    <div class="p-8">
                        <h4 class="text-l font-bold pb-2">INDUSTRY SKILLS NEED SURVEY</h4>

                        <p class="text-gray-600 text-sm leading-relaxed mb-8 pb-6 border-b border-gray-200">
                            {{ __('lmip.lmi_intro') }}
                        </p>
                        <h5 class="text-l font-bold pb-2">DATA PRIVACY STATEMENT</h5>

                        <p class="text-gray-600 text-sm leading-relaxed mb-8 pb-6 border-b border-gray-200">
                            {{ __('lmip.privacy_statement') }}
                        </p>

                        <div class="bg-gray-50   rounded-lg p-6 mt-8">
                            <div class="flex items-start gap-2 text-base font-semibold mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                PART 1: COMPANY PROFILE
                            </div>


                            <form action="#" method="POST" class="space-y-5 ">
                                `
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">
                                            Comapany Name:
                                        </label>
                                        <input type="text" name="company"
                                            class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">
                                            Name of Respondent:
                                        </label>
                                        <input type="text" name="respondent"
                                            class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">
                                            Designation / Position:
                                        </label>
                                        <input type="text" name="position"
                                            class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">
                                            Contact Number:
                                        </label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm border-r pr-2 border-gray-300">
                                                    🇵🇭 +63
                                                </span>
                                            </div>

                                            <input type="tel" name="contact_number" placeholder="912 345 6789"
                                                class="w-full pl-16 pr-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">
                                            Email Address:
                                        </label>
                                        <input type="text" name="respondent_name"
                                            class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                    </div>
                                </div>

                                <div x-data="{ open: false, selected: '' }" class="relative mt-4">
                                    <label class="block text-gray-700 text-sm font-medium mb-2">
                                        Industry Sector:
                                    </label>

                                    <!-- Dropdown Button -->
                                    <button @click="open = !open" type="button"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 hover:border-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                        <span x-text="selected || 'Please select your primary operation'"
                                            :class="!selected ? 'text-gray-400' : 'text-gray-600'"></span>
                                        <svg class="w-5 h-5 text-gray-400 transition-transform"
                                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu with Scrollbar -->
                                    <div x-show="open" @click.away="open = false" x-transition
                                        class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">

                                        <div @click="selected = 'Accommodation & Food Service'; open = false"
                                            class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                            • Accommodation & Food Service (Hotels, Resorts, Restaurants, Fast Food
                                            Chains, Catering Services)
                                        </div>

                                        <div @click="selected = 'Administrative & Support Services'; open = false"
                                            class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                            • Administrative & Support Services (Security Agencies, Manpower/Recruitment
                                            Agencies, Call Centers, Travel Agencies, Janitorial Services)
                                        </div>

                                        <div @click="selected = 'Agriculture, Forestry, Fishing & Mining'; open = false"
                                            class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                            • Agriculture, Forestry, Fishing & Mining
                                        </div>

                                        <div @click="selected = 'Construction'; open = false"
                                            class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                            • Construction
                                        </div>

                                        <div @click="selected = 'Education'; open = false"
                                            class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                            • Education (Private Schools, Colleges, Universities, Training Centers)
                                        </div>

                                        <div @click="selected = 'Electricity, Gas, Water & Waste Management'; open = false"
                                            class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                            • Electricity, Gas, Water & Waste Management (Power Plants, Electric Co-ops,
                                            Water Districts, Garbage/Recycling Firms)
                                        </div>

                                        <div @click="selected = 'Financial & Insurance Activities'; open = false"
                                            class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                            • Financial & Insurance Activities (Banks, Pawnshops, Lending Investors,
                                            Insurance Companies)
                                        </div>

                                        <div @click="selected = 'Human Health & Social Work'; open = false"
                                            class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                            • Human Health & Social Work (Hospital, Medical Clinics, Diagnostic Labs,
                                            Nursing Homes)
                                        </div>

                                        <div @click="selected = 'Information & Communication'; open = false"
                                            class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                            • Information & Communication (Software Companies, ISPs, Telecoms, TV/Radio
                                            Stations, Non-Voice Tech BPO)
                                        </div>

                                        <div @click="selected = 'Other Service Activities'; open = false"
                                            class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                            • Other Service Activities (Repairs Shops, Beauty Salons, Spas, Laundry
                                            Shops, Funeral)
                                        </div>

                                        <div @click="selected = 'Professional, Scientific & Technical Services'; open = false"
                                            class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                            • Professional, Scientific & Technical Services (Law Firms,
                                            Accounting/Auditing Firms, Engineering/Architectural Firms, Advertising
                                            Agencies)
                                        </div>

                                        <div @click="selected = 'Real Estate Activities'; open = false"
                                            class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                            • Real Estate Activities (Real Estate Developers, Lessor of Apartment/Office
                                            Space)
                                        </div>

                                        <div @click="selected = 'Transportation, Storage & Logistics'; open = false"
                                            class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                            • Transportation, Storage & Logistics (Trucking/Hauling Services,
                                            Warehousing, Shipping Lines, Courier Services)
                                        </div>

                                        <div @click="selected = 'Wholesale & Retail Trade'; open = false"
                                            class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                            • Wholesale & Retail Trade (Trading Companies, Malls, Hardware Stores, Car
                                            Dealers, Online Shops, etc.)
                                        </div>

                                    </div>

                                    <!-- Hidden input for form submission -->
                                    <input type="hidden" name="industrySelector" x-model="selected">

                                    <div>
                                        <div x-data="{ open: false, selected: '' }" class="relative mt-4">
                                            <label class="block text-gray-700 text-sm font-medium mb-2">
                                                Company Size:
                                            </label>

                                            <!-- Dropdown Button -->
                                            <button @click="open = !open" type="button"
                                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 hover:border-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                                <span x-text="selected || 'Select company size'"
                                                    :class="!selected ? 'text-gray-400' : 'text-gray-600'"></span>
                                                <svg class="w-5 h-5 text-gray-400 transition-transform"
                                                    :class="open ? 'rotate-180' : ''" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <!-- Dropdown Menu (appears on click) -->
                                            <div x-show="open" @click.away="open = false" x-transition
                                                class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60   overflow-y-auto">

                                                <div @click="selected = 'Less than 50'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    Less than 50
                                                </div>

                                                <div @click="selected = '51-200'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    51-200
                                                </div>

                                                <div @click="selected = '201-500'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    201-500
                                                </div>

                                                <div @click="selected = 'More than 500'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    More than 500
                                                </div>
                                            </div>

                                            <!-- Hidden input for form submission -->
                                            <input type="hidden" name="companySize" x-model="selected">
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <!-- PART II: HARD-TO-FILL ROLES -->
                        <div class="bg-teal-50 border border-teal-200 rounded-lg p-6 mt-10 overflow-hidden">
                            <div class="flex items-start gap-2 text-teal-700 text-base font-semibold mb-2">
                                <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                PART II: HARD-TO-FILL ROLES
                            </div>
                            <p class="text-teal-600 text-xs italic mb-4">
                                Please identify the TOP Job Titles you find hardest to fill. Be as specific as possible
                                (e.g., instead of "IT Skills", say "Python Programming").
                            </p>

                            <div id="jobTitlesContainer" class="space-y-6">
                                <!-- Single Job Entry -->
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <!-- 8. Job Title -->
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-medium mb-2">
                                            8. Job Title: <span class="text-gray-400 text-xs">[Short Answer]</span>
                                        </label>
                                        <input type="text" name="job_title[]"
                                            placeholder="e.g. Senior Java Developer"
                                            class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                    </div>

                                    <!-- 9. Standard Job Classifications / Families -->
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-medium mb-2">
                                            9. Standard Job Classifications / Families:
                                        </label>
                                        <div x-data="{ open: false, selected: '' }" class="relative">
                                            <button @click="open = !open" type="button"
                                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                                <span x-text="selected || 'Select job classification'"
                                                    :class="!selected ? 'text-gray-400' : 'text-gray-600'"></span>
                                                <svg class="w-5 h-5 text-gray-400 transition-transform"
                                                    :class="open ? 'rotate-180' : ''" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <div x-show="open" @click.away="open = false" x-transition
                                                class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">

                                                <div @click="selected = 'Accounting, Finance & Banking'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • Accounting, Finance & Banking
                                                </div>

                                                <div @click="selected = 'Administrative, HR & Office Support'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • Administrative, HR & Office Support
                                                </div>

                                                <div @click="selected = 'Agriculture, Forestry & Agribusiness'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • Agriculture, Forestry & Agribusiness
                                                </div>

                                                <div @click="selected = 'Construction, Engineering & Architecture'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • Construction, Engineering & Architecture
                                                </div>

                                                <div @click="selected = 'Customer Service & BPO (Contact Center)'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • Customer Service & BPO (Contact Center)
                                                </div>

                                                <div @click="selected = 'Education, Training & Academe'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • Education, Training & Academe
                                                </div>

                                                <div @click="selected = 'Healthcare, Medical & Allied Services'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • Healthcare, Medical & Allied Services
                                                </div>

                                                <div @click="selected = 'IT, Software, Data & Digital Creative'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • IT, Software, Data & Digital Creative
                                                </div>

                                                <div @click="selected = 'Legal, Compliance & Public Service'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • Legal, Compliance & Public Service
                                                </div>

                                                <div @click="selected = 'Logistics, Transport & Supply Chain'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • Logistics, Transport & Supply Chain
                                                </div>

                                                <div @click="selected = 'Manufacturing, Production & Technical'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • Manufacturing, Production & Technical
                                                </div>

                                                <div @click="selected = 'Sales, Marketing, Retail & E-Commerce'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • Sales, Marketing, Retail & E-Commerce
                                                </div>

                                                <div @click="selected = 'Science, Research & Laboratory'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • Science, Research & Laboratory
                                                </div>

                                                <div @click="selected = 'Skilled Trades, Maintenance & General Services'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • Skilled Trades, Maintenance & General Services
                                                </div>

                                                <div @click="selected = 'Tourism, Hospitality & Food Service'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    • Tourism, Hospitality & Food Service
                                                </div>
                                            </div>

                                            <input type="hidden" name="job_classification[]" x-model="selected">
                                        </div>
                                    </div>

                                    <!-- 10. Duration that the Vacancy is Open -->
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-medium mb-2">
                                            10. Duration that the Vacancy is Open:
                                        </label>
                                        <div x-data="{ open: false, selected: '' }" class="relative">
                                            <button @click="open = !open" type="button"
                                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                                <span x-text="selected || 'Select duration'"
                                                    :class="!selected ? 'text-gray-400' : 'text-gray-600'"></span>
                                                <svg class="w-5 h-5 text-gray-400 transition-transform"
                                                    :class="open ? 'rotate-180' : ''" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <div x-show="open" @click.away="open = false" x-transition
                                                class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">

                                                <div @click="selected = 'Less than 30 Days'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    Less than 30 Days
                                                </div>

                                                <div @click="selected = '30-60 Days'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    30-60 Days
                                                </div>

                                                <div @click="selected = '60-90 Days'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    60-90 Days
                                                </div>

                                                <div @click="selected = '90+ Days'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    90+ Days
                                                </div>
                                            </div>

                                            <input type="hidden" name="vacancy_duration[]" x-model="selected">
                                        </div>
                                    </div>

                                    <!-- 11. Primary Reason For Difficulty -->
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-medium mb-2">
                                            11. Primary Reason For Difficulty (Role-Level) <span
                                                class="italic text-gray-500">(Select ONE)</span>
                                        </label>
                                        <div x-data="{ open: false, selected: '' }" class="relative">
                                            <button @click="open = !open" type="button"
                                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                                <span x-text="selected || 'Select primary reason'"
                                                    :class="!selected ? 'text-gray-400' : 'text-gray-600'"></span>
                                                <svg class="w-5 h-5 text-gray-400 transition-transform"
                                                    :class="open ? 'rotate-180' : ''" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <div x-show="open" @click.away="open = false" x-transition
                                                class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">

                                                <div @click="selected = 'Technical / Hard Skills Missing'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    <div class="font-medium">Technical / Hard Skills Missing</div>
                                                    <div class="text-xs text-gray-500 mt-1">Applicants do not have the
                                                        required tools, software, or technical knowledge</div>
                                                </div>

                                                <div @click="selected = 'Soft / Employability Skills Missing'; open = false"
                                                    class="px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                    <div class="font-medium">Soft / Employability Skills Missing</div>
                                                    <div class="text-xs text-gray-500 mt-1">Applicants cannot
                                                        communicate effectively, work in teams, or demonstrate
                                                        professionalism/problem-solving</div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="primary_reason[]" x-model="selected">

                                            <!-- Follow-up: Technical Skills Details - Only shows when Technical is selected -->
                                            <div x-show="selected === 'Technical / Hard Skills Missing'" x-transition
                                                class="mt-4">
                                                <label class="block text-gray-700 text-sm font-medium mb-2">
                                                    Follow-up: What specific technical tools, software, or machinery
                                                    knowledge is missing in applicants?
                                                </label>
                                                <textarea name="technical_skills_missing[]" rows="3"
                                                    placeholder="e.g. Python, SQL, AutoCAD, specific machinery..."
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm resize-none"></textarea>
                                            </div>

                                            <!-- Follow-up: Soft Skills Details - Only shows when Soft Skills is selected -->
                                            <div x-show="selected === 'Soft / Employability Skills Missing'"
                                                x-transition class="mt-4">
                                                <label class="block text-gray-700 text-sm font-medium mb-2">
                                                    Follow-up: What attitude or behavioral traits cause you to reject
                                                    applicants?
                                                </label>
                                                <textarea name="soft_skills_missing[]" rows="3"
                                                    placeholder="e.g. Poor communication, lack of teamwork, unprofessional behavior..."
                                                    class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm resize-none"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="addJobTitle()"
                                class="mt-4 text-teal-600 hover:text-teal-700 font-medium text-sm flex items-center gap-1">
                                <span class="text-lg">+</span> Add another Hard-to-Fill Role
                            </button>
                        </div>

                        <!-- PART III: DIAGNOSIS OF MISMATCH (Section C from your form) -->
                        <div class="bg-gray-50 rounded-lg p-6 mt-8">
                            <div class="flex items-start gap-2 text-base font-semibold mb-2">
                                <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                PART III: DIAGNOSIS OF MISMATCH
                            </div>
                            <p class="text-gray-500 text-xs italic mb-4">
                                For applicants who meet formal qualifications (degree, license), what observable factors
                                most often cause them to be rejected?
                            </p>

                            <div class="space-y-5">
                                <!-- 12. GNA IMPACT -->
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">
                                        12. Your business / sector / operations are diminished, critical or high-quality
                                        applicants for this role (important for this role impact).
                                    </label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="impact_level" value="High"
                                                class="w-4 h-4 text-teal-600 border-gray-300 focus:ring-teal-500">
                                            <span class="ml-3 text-sm text-gray-700">High - Operations are disrupted,
                                                critical tasks or projects are delayed</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="impact_level" value="Medium"
                                                class="w-4 h-4 text-teal-600 border-gray-300 focus:ring-teal-500">
                                            <span class="ml-3 text-sm text-gray-700">Medium - Operations continue but
                                                require overtime, workload, or project delays</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="impact_level" value="Low"
                                                class="w-4 h-4 text-teal-600 border-gray-300 focus:ring-teal-500">
                                            <span class="ml-3 text-sm text-gray-700">Low - Minimal impact; new hires
                                                can be trained internally without significant operational
                                                disruptions</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Additional questions from your form can be added here -->
                            </div>
                        </div>


                        <!-- PART IV: ENGAGEMENT & NEXT STEPS -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-8">
                            <!-- Changed mt-10 to mt-8 for consistency -->
                            <div class="flex items-start gap-2 text-gray-700 text-base font-semibold mb-2">
                                <!-- Changed text-gray-700 to text-blue-700 -->
                                PART IV: ENGAGEMENT & NEXT STEPS
                            </div>

                            <div class="space-y-5">
                                <!-- 20. If DOLE provides a Regional LMI Dashboard -->
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">
                                        20. If DOLE provides a Regional LMI Dashboard (What feature would be most useful
                                        for you / Select top (2)):
                                    </label>
                                    <div class="space-y-2">
                                        <label class="flex items-start">
                                            <input type="checkbox" name="lmi_features[]"
                                                value="Viewing the supply of graduates"
                                                class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-3 text-sm text-gray-700">Viewing the supply of graduates
                                                (e.g., "How many IT grads will graduate next year?")</span>
                                        </label>

                                        <label class="flex items-start">
                                            <input type="checkbox" name="lmi_features[]"
                                                value="A channel to submit real-time feedback"
                                                class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-3 text-sm text-gray-700">A channel to submit real-time
                                                feedback on curriculum quality</span>
                                        </label>

                                        <label class="flex items-start">
                                            <input type="checkbox" name="lmi_features[]"
                                                value="A directory of job placement offices"
                                                class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-3 text-sm text-gray-700">A directory of job placement
                                                offices and Public Employment offices (PESOs)</span>
                                        </label>

                                        <label class="flex items-start">
                                            <input type="checkbox" name="lmi_features[]" value="Other"
                                                class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-3 text-sm text-gray-700">Other: _____________</span>
                                        </label>
                                    </div>
                                </div>



                                <!-- Your specific inputs (optional) -->
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">
                                        Your specific inputs?
                                    </label>
                                    <textarea name="specific_inputs" rows="4" placeholder="Please share any additional insights or suggestions..."
                                        class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Consent Checkbox -->
                        <div class="flex items-start gap-3 mt-6">
                            <input type="checkbox" id="consent" name="consent"
                                class="mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                                required />
                            <label for="consent" class="text-gray-600 text-sm">
                                I agree to contribute this data to the Regional LMI Database.
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-lg transition shadow-lg mt-6">
                            Submit LMI Matrix
                        </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            const ctx = document.getElementById('jobsChart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(collect($high_volume_jobs)->pluck('title')) !!},
                    datasets: [{
                        data: {!! json_encode(collect($high_volume_jobs)->pluck('count')) !!},
                        backgroundColor: ['#2563eb', '#2563eb', '#3b82f6', '#93c5fd', '#bfdbfe', '#bfdbfe',
                            '#dbeafe', '#dbeafe', '#dbeafe', '#dbeafe'
                        ],
                        borderRadius: 4,
                        barThickness: 15
                    }]
                },
                options: {
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                stepSize: 350
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        </script>
