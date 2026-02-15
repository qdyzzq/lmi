<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>


    <title>LMI</title>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        .tab-btn.active {
            color: #2563eb;
            border-bottom-color: #2563eb;
        }

        .tab-btn:not(.active) {
            color: #6b7280;
            border-bottom-color: transparent;
        }

        .tab-content {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-slate-100 flex h-screen overflow-hidden">
    <div x-data="{
        activeView: 'job-market-view',
        showReportModal: false,
        showLmiMatrix: false,
        sidebarExpanded: true
    }" class="flex w-full h-full">


        <div id="main-content" class="flex w-full h-full transition-all duration-200">

            @include('partials.navbar')




            <div class="flex-1 flex flex-col overflow-y-auto">
                <div x-show="activeView === 'job-market-view'" x-transition>
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




                        <div class="bg-slate-700 rounded-xl p-6 text-white flex justify-between items-center shadow-lg">
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

                                <button id="show-lmi-matrix-btn"
                                    class="bg-emerald-500 border border-emerald-500 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-500/20 transition">
                                    Update LMI Matrix
                                </button>
                            </div>
                        </div>


                        <!-- High Volume Jobs Section - Original Design with Year Comparison -->
                        <!-- Two Column Layout: Chart Left, Hard-to-Fill Right -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

                            <!-- LEFT SIDE: High Volume Jobs Chart (Takes 2 columns) -->
                            <div
                                class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                                <!-- Header -->
                                <div class="flex justify-between items-center p-6 pb-4 border-b border-gray-100">
                                    <div>
                                        <h3 class="font-bold text-gray-800">Top 10 High-Volume Job Titles</h3>
                                        @if ($selected_year && isset($selected_year))
                                            <p class="text-xs text-gray-500 mt-1">
                                                <span class="text-green-600 font-medium">{{ $selected_year - 1 }}</span>
                                                vs
                                                <span class="text-blue-600 font-medium">{{ $selected_year }}</span>
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <!-- Year Selector -->
                                        @if (isset($available_years) && count($available_years) > 0)
                                            <select id="yearSelector"
                                                class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                                onchange="window.location.href = '{{ route('Job.Market.Demands') }}?year=' + this.value">
                                                @foreach ($available_years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $year == $selected_year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif

                                        <!-- Expand Chart Button -->
                                        <button onclick="expandChart()"
                                            class="p-2 hover:bg-gray-100 rounded-lg transition" title="Expand chart">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                            </svg>
                                        </button>

                                        <!-- Info Icon -->
                                        <span class="text-gray-300 cursor-help"
                                            title="Job titles with highest demand">ⓘ</span>
                                    </div>
                                </div>

                                <!-- Chart Container -->
                                <div class="p-6" id="chartContainer">
                                    <div style="height: 360px;">
                                        <canvas id="highVolumeHorizontalChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT SIDE: Hard-to-Fill Roles (Takes 1 column) -->
                            <div
                                class="lg:col-span-1 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                                <div class="flex justify-between p-6 pb-4">
                                    <h3 class="font-bold text-gray-800">Hard-to-Fill Roles</h3>
                                    <span class="text-gray-300 cursor-help" title="Click to expand details">ⓘ</span>
                                </div>

                                @if (isset($groupedRoles) && count($groupedRoles) > 0)
                                    <div class="max-h-96 overflow-y-auto px-6 pb-6">
                                        <div class="space-y-3">
                                            @foreach ($groupedRoles as $normalizedTitle => $roleGroup)
                                                @foreach ($roleGroup as $item)
                                                    @php
                                                        $role = $item['role'];
                                                        $submission = $item['submission'];
                                                        $index = $item['index'];
                                                    @endphp

                                                    <!-- Clickable Role Card -->
                                                    <div class="role-card border border-slate-200 rounded-lg overflow-hidden hover:border-blue-400 transition cursor-pointer"
                                                        onclick="toggleRoleDetails({{ $submission->id }}, {{ $index }})">

                                                        <!-- Summary (Always Visible) -->
                                                        <div class="p-3 bg-white hover:bg-slate-50 transition">
                                                            <div class="flex items-start justify-between">
                                                                <div class="flex-1">
                                                                    <p class="font-bold text-sm text-slate-800">
                                                                        {{ ucwords(strtolower(trim($role->job_title))) }}
                                                                    </p>
                                                                    <p class="text-xs text-gray-400 mt-1">
                                                                        {{ $role->vacancy_duration }}</p>
                                                                </div>

                                                                <!-- Expand Icon -->
                                                                <svg class="expand-icon w-4 h-4 text-slate-400 transition-transform"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                </svg>
                                                            </div>
                                                        </div>

                                                        <!-- Expandable Details -->
                                                        <div class="role-details hidden"
                                                            id="role-details-{{ $submission->id }}-{{ $index }}">
                                                            <div class="border-t border-slate-200 bg-slate-50 p-4">
                                                                <div class="space-y-3 text-sm">
                                                                    <!-- Classification -->
                                                                    <div>
                                                                        <span
                                                                            class="font-medium text-slate-600">Classification:</span>
                                                                        <p class="text-slate-800">
                                                                            {{ $role->job_classification }}</p>
                                                                    </div>

                                                                    <!-- Difficulty Reasons -->
                                                                    @php
                                                                        $reasons = $role->difficulty_reasons;
                                                                        if (is_string($reasons)) {
                                                                            $reasons =
                                                                                json_decode($reasons, true) ?? [];
                                                                        }
                                                                        if (!is_array($reasons)) {
                                                                            $reasons = [];
                                                                        }
                                                                    @endphp

                                                                    @if (count($reasons) > 0)
                                                                        <div>
                                                                            <span
                                                                                class="font-medium text-slate-600">Difficulty
                                                                                Reasons:</span>
                                                                            <ul
                                                                                class="list-disc list-inside mt-1 text-slate-700 text-xs">
                                                                                @foreach ($reasons as $reason)
                                                                                    @if (is_array($reason))
                                                                                        @foreach ($reason as $item)
                                                                                            @if (!empty($item))
                                                                                                <li>{{ $item }}
                                                                                                </li>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    @elseif(is_string($reason) && !empty($reason))
                                                                                        <li>{{ $reason }}</li>
                                                                                    @endif
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>
                                                                    @endif

                                                                    <!-- Technical Skills -->
                                                                    @php
                                                                        $techSkills = $role->technical_skills_missing;
                                                                        if (is_string($techSkills)) {
                                                                            $techSkills =
                                                                                json_decode($techSkills, true) ?? [];
                                                                        }
                                                                        if (!is_array($techSkills)) {
                                                                            $techSkills = [];
                                                                        }
                                                                    @endphp

                                                                    @if (count($techSkills) > 0)
                                                                        <div>
                                                                            <span
                                                                                class="font-medium text-slate-600">Technical
                                                                                Skills Missing:</span>
                                                                            <div class="flex flex-wrap gap-1 mt-1">
                                                                                @foreach ($techSkills as $skill)
                                                                                    @if (!empty($skill))
                                                                                        <span
                                                                                            class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">{{ $skill }}</span>
                                                                                    @endif
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    <!-- Soft Skills -->
                                                                    @php
                                                                        $softSkills = $role->soft_skills_missing;
                                                                        if (is_string($softSkills)) {
                                                                            $softSkills =
                                                                                json_decode($softSkills, true) ?? [];
                                                                        }
                                                                        if (!is_array($softSkills)) {
                                                                            $softSkills = [];
                                                                        }
                                                                    @endphp

                                                                    @if (count($softSkills) > 0)
                                                                        <div>
                                                                            <span
                                                                                class="font-medium text-slate-600">Soft
                                                                                Skills Missing:</span>
                                                                            <div class="flex flex-wrap gap-1 mt-1">
                                                                                @foreach ($softSkills as $skill)
                                                                                    @if (!empty($skill))
                                                                                        <span
                                                                                            class="px-2 py-0.5 bg-pink-100 text-pink-700 rounded text-xs">{{ $skill }}</span>
                                                                                    @endif
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    <!-- Company Info -->
                                                                    <div class="pt-2 border-t">
                                                                        <p class="text-xs text-slate-500">
                                                                            <strong>Company:</strong>
                                                                            {{ $submission->company_name }}<br>
                                                                            <strong>Sector:</strong>
                                                                            {{ $submission->industry_sector }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif($approvedSubmissions && $approvedSubmissions->count() > 0)
                                    <!-- Fallback: Display ungrouped if groupedRoles not available -->
                                    <div class="max-h-96 overflow-y-auto px-6 pb-6">
                                        <div class="space-y-3">
                                            @foreach ($approvedSubmissions as $submission)
                                                @foreach ($submission->hardToFillRoles as $index => $role)
                                                    <div class="role-card border border-slate-200 rounded-lg overflow-hidden hover:border-blue-400 transition cursor-pointer"
                                                        onclick="toggleRoleDetails({{ $submission->id }}, {{ $index }})">

                                                        <div class="p-3 bg-white hover:bg-slate-50 transition">
                                                            <div class="flex items-start justify-between">
                                                                <div class="flex-1">
                                                                    <p class="font-bold text-sm text-slate-800">
                                                                        {{ ucwords(strtolower(trim($role->job_title))) }}
                                                                    </p>
                                                                    <p class="text-xs text-gray-400 mt-1">
                                                                        {{ $role->vacancy_duration }}</p>
                                                                </div>

                                                                <svg class="expand-icon w-4 h-4 text-slate-400 transition-transform"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M19 9l-7 7-7-7"></path>
                                                                </svg>
                                                            </div>
                                                        </div>

                                                        <div class="role-details hidden"
                                                            id="role-details-{{ $submission->id }}-{{ $index }}">
                                                            <!-- Same detail structure as above -->
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <!-- Fallback to static data if no approved submissions -->
                                    <div class="px-6 pb-6">
                                        <div class="space-y-5">
                                            @foreach ($hard_to_fill as $job)
                                                <div class="flex justify-between items-center">
                                                    <div class="space-y-1">
                                                        <p class="font-bold text-sm text-slate-800">
                                                            {{ $job['role'] }}</p>
                                                        <p
                                                            class="text-[10px] text-gray-400 flex items-center gap-1 uppercase">
                                                            🕒 Bottleneck: {{ $job['bottleneck'] }}
                                                        </p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-red-500 font-bold text-xs">{{ $job['days'] }}
                                                            days</p>
                                                        <p class="text-[9px] text-gray-300">({{ $job['year'] }})</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Fullscreen Modal -->
                        <div id="chartModal"
                            class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                            onclick="closeChart()">
                            <div class="bg-white rounded-xl shadow-2xl w-11/12 h-5/6 p-6 relative"
                                onclick="event.stopPropagation()">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-bold text-gray-800">High-Volume Job Titles - Expanded View
                                    </h3>
                                    <button onclick="closeChart()"
                                        class="p-2 hover:bg-gray-100 rounded-lg transition">
                                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div style="height: calc(100% - 60px);">
                                    <canvas id="highVolumeExpandedChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- Critical Skill Gaps Per Sector -->
                        <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                            <h3 class="font-bold text-lg mb-4">"Critical Skill Gaps" Per Sector</h3>

                            <!-- Sector Filter Tabs -->
                            <div class="flex gap-2 mb-8 pb-5 border-b border-gray-200 overflow-x-auto">
                                <button onclick="filterSkills('All')"
                                    class="sector-tab active px-4 py-1 text-sm rounded-full bg-purple-600 text-white transition whitespace-nowrap"
                                    data-sector="All">
                                    All
                                </button>
                                @foreach ($sectors as $sector)
                                    <button onclick="filterSkills('{{ addslashes($sector) }}')"
                                        class="sector-tab px-4 py-1 text-sm rounded-full border text-gray-500 hover:bg-gray-50 transition whitespace-nowrap"
                                        data-sector="{{ $sector }}">
                                        {{ $sector }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="grid grid-cols-2 gap-12">
                                <!-- Missing Soft Skills -->
                                <div class="border-r border-gray-200 pr-6">
                                    <h4 class="text-xs font-bold text-gray-400 mb-4 uppercase bg-white pb-2">
                                        🚫 Missing Soft Skills (Critical Gaps)
                                    </h4>
                                    <div class="flex flex-wrap gap-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar"
                                        id="soft-skills-container" style="overflow-y: scroll;">
                                        <!-- Force scrollbar to always show for testing -->
                                        @foreach ($soft_skills as $skill)
                                            <div class="skill-tag soft-skill bg-red-100 text-gray-800 font-semibold px-3 py-2 rounded-lg text-sm h-fit"
                                                data-sector="{{ $skill['sector'] }}">
                                                {{ $skill['name'] }}
                                                <span class="text-[12px] opacity-70">({{ $skill['sector'] }})</span>
                                                @if (isset($skill['count']) && $skill['count'] > 1)
                                                    <span
                                                        class="ml-1 px-1.5 py-0.5 bg-red-200 rounded-full text-[9px] font-bold">{{ $skill['count'] }}×</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Missing Technical Skills -->
                                <div class="pl-6">
                                    <h4 class="text-xs font-bold text-gray-400 mb-4 uppercase bg-white pb-2">
                                        🔍 Missing Technical Skills
                                    </h4>
                                    <div class="flex flex-wrap gap-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar"
                                        id="tech-skills-container" style="overflow-y: scroll;">
                                        <!-- Force scrollbar to always show for testing -->
                                        @foreach ($tech_skills as $skill)
                                            <div class="skill-tag tech-skill bg-blue-100 text-gray-800 font-semibold px-3 py-2 rounded-lg text-sm h-fit"
                                                data-sector="{{ $skill['sector'] }}">
                                                {{ $skill['name'] }}
                                                <span class="text-[12px] opacity-70">({{ $skill['sector'] }})</span>
                                                @if (isset($skill['count']) && $skill['count'] > 1)
                                                    <span
                                                        class="ml-1 px-1.5 py-0.5 bg-blue-200 rounded-full text-[9px] font-bold">{{ $skill['count'] }}×</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- LMI Matrix - Improved Design with Laravel Blade -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"
                            x-data="{
                                openItem: null,
                                currentPage: 1,
                                itemsPerPage: 10,
                                get totalPages() { return Math.ceil({{ count($matrix_results) }} / this.itemsPerPage); },
                                get paginatedData() {
                                    const start = (this.currentPage - 1) * this.itemsPerPage;
                                    const end = start + this.itemsPerPage;
                                    return @js($matrix_results).slice(start, end);
                                },
                                nextPage() {
                                    if (this.currentPage < this.totalPages) {
                                        this.currentPage++;
                                        this.openItem = null;
                                    }
                                },
                                prevPage() {
                                    if (this.currentPage > 1) {
                                        this.currentPage--;
                                        this.openItem = null;
                                    }
                                },
                                goToPage(page) {
                                    this.currentPage = page;
                                    this.openItem = null;
                                }
                            }">
                            <div
                                class="p-6 border-b flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
                                <h3 class="font-bold text-gray-900 flex items-center gap-3 text-lg">
                                    <span class="text-2xl">📊</span>
                                    <span>LMI Granularity Matrix Results: Competency Gap Analysis</span>
                                </h3>
                                <button id="exportLMIMatrixBtn"
                                    class="text-emerald-600 border border-emerald-200 bg-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-emerald-50 transition-all shadow-sm hover:shadow">
                                    Export Analysis
                                </button>
                            </div>

                            @if (count($matrix_results) > 0)
                                <!-- Sticky Table Header Row - Improved proportions -->
                                <div
                                    class="sticky top-0 z-20 bg-gradient-to-r from-gray-900 to-gray-800 border-b border-gray-700 shadow-md">
                                    <div class="grid grid-cols-12 gap-6 px-8 py-4">
                                        <div class="col-span-2">
                                            <span class="text-xs font-bold text-white uppercase tracking-wider">Job
                                                Title / Role</span>
                                        </div>
                                        <div class="col-span-4">
                                            <span
                                                class="text-xs font-bold text-white uppercase tracking-wider">Sector</span>
                                        </div>
                                        <div class="col-span-4">
                                            <span class="text-xs font-bold text-white uppercase tracking-wider">Missing
                                                Skills / Competency</span>
                                        </div>
                                        <div class="col-span-2 text-right">
                                            <span class="text-xs font-bold text-white uppercase tracking-wider">Gap
                                                Impact</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Scrollable Content Area -->
                                <div class="max-h-[600px] overflow-y-auto bg-gray-50">
                                    <!-- Accordion Items -->
                                    <div class="divide-y divide-gray-200">
                                        <template x-for="(result, index) in paginatedData" :key="index">
                                            <div class="bg-white hover:bg-gray-50 transition-all duration-200 border-l-4"
                                                :class="openItem === index ? 'border-l-blue-500 shadow-md' :
                                                    'border-l-transparent'">
                                                <!-- Accordion Header (Collapsed View) -->
                                                <div @click="openItem = openItem === index ? null : index"
                                                    class="grid grid-cols-12 gap-6 px-8 py-6 cursor-pointer items-center">

                                                    <!-- Job Title (2 cols) -->
                                                    <div class="col-span-2">
                                                        <h4 class="font-bold text-gray-900 text-base"
                                                            x-text="result.role"></h4>
                                                    </div>

                                                    <!-- Sector (4 cols) -->
                                                    <div class="col-span-4">
                                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide leading-relaxed"
                                                            x-text="result.sector"></p>
                                                    </div>

                                                    <!-- Skills Preview (4 cols) - Simplified with counts -->
                                                    <div class="col-span-4">
                                                        <div class="flex items-center gap-3">
                                                            <!-- Skill count summary instead of showing individual pills -->
                                                            <div class="flex items-center gap-4">
                                                                <template
                                                                    x-if="result.hard_skills && result.hard_skills.length > 0">
                                                                    <div class="flex items-center gap-2">
                                                                        <div class="w-2 h-2 rounded-full bg-blue-500">
                                                                        </div>
                                                                        <span
                                                                            class="text-sm font-semibold text-gray-700">
                                                                            <span
                                                                                x-text="result.hard_skills.length"></span>
                                                                            Technical
                                                                        </span>
                                                                    </div>
                                                                </template>

                                                                <template
                                                                    x-if="result.soft_skills && result.soft_skills.length > 0">
                                                                    <div class="flex items-center gap-2">
                                                                        <div class="w-2 h-2 rounded-full bg-pink-500">
                                                                        </div>
                                                                        <span
                                                                            class="text-sm font-semibold text-gray-700">
                                                                            <span
                                                                                x-text="result.soft_skills.length"></span>
                                                                            Soft
                                                                        </span>
                                                                    </div>
                                                                </template>
                                                            </div>

                                                            <!-- View details prompt -->
                                                            <span class="text-xs text-gray-400 italic ml-auto"
                                                                x-show="openItem !== index">
                                                                Click to view
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Impact Badge (2 cols) -->
                                                    <div class="col-span-2 flex items-center justify-end gap-3">
                                                        <span
                                                            class="px-4 py-2 rounded-lg text-sm font-bold min-w-[90px] text-center shadow-sm"
                                                            :class="{
                                                                'bg-red-50 text-red-700 border border-red-200': result
                                                                    .impact === 'High',
                                                                'bg-green-50 text-green-700 border border-green-200': result
                                                                    .impact === 'Low',
                                                                'bg-amber-50 text-amber-700 border border-amber-200': result
                                                                    .impact === 'Medium' || !result.impact
                                                            }"
                                                            x-text="result.impact || 'Medium'">
                                                        </span>

                                                        <!-- Expand Icon -->
                                                        <svg class="w-5 h-5 text-gray-400 transition-all duration-300 flex-shrink-0"
                                                            :class="openItem === index ? 'rotate-180 text-blue-600' : ''"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    </div>
                                                </div>

                                                <!-- Accordion Content (Expanded View) -->
                                                <div x-show="openItem === index"
                                                    x-transition:enter="transition ease-out duration-300"
                                                    x-transition:enter-start="opacity-0 -translate-y-4"
                                                    x-transition:enter-end="opacity-100 translate-y-0"
                                                    x-transition:leave="transition ease-in duration-200"
                                                    x-transition:leave-start="opacity-100 translate-y-0"
                                                    x-transition:leave-end="opacity-0 -translate-y-4"
                                                    class="border-t border-gray-200 bg-gradient-to-br from-blue-50/30 to-pink-50/30"
                                                    style="display: none;">

                                                    <div class="px-8 py-8">
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                                            <!-- Technical Skills -->
                                                            <div
                                                                class="bg-white rounded-xl p-6 shadow-sm border border-blue-100">
                                                                <div class="flex items-center gap-2 mb-4">
                                                                    <div
                                                                        class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                                                        <span class="text-lg">🔧</span>
                                                                    </div>
                                                                    <span
                                                                        class="text-sm font-bold text-blue-900 uppercase tracking-wide">Missing
                                                                        Technical Skills</span>
                                                                </div>
                                                                <template
                                                                    x-if="result.hard_skills && result.hard_skills.length > 0">
                                                                    <div class="flex flex-wrap gap-2.5">
                                                                        <template x-for="skill in result.hard_skills"
                                                                            :key="skill.name || skill">
                                                                            <span
                                                                                class="px-4 py-2.5 bg-blue-50 text-blue-800 border border-blue-200 rounded-lg text-sm font-semibold hover:bg-blue-100 hover:border-blue-300 transition-all shadow-sm"
                                                                                x-text="skill.name || skill">
                                                                            </span>
                                                                        </template>
                                                                    </div>
                                                                </template>
                                                                <template
                                                                    x-if="!result.hard_skills || result.hard_skills.length === 0">
                                                                    <div class="text-center py-6">
                                                                        <div class="text-3xl mb-2 opacity-20">✓</div>
                                                                        <p class="text-sm text-gray-400 font-medium">No
                                                                            technical skill gaps identified</p>
                                                                    </div>
                                                                </template>
                                                            </div>

                                                            <!-- Soft Skills -->
                                                            <div
                                                                class="bg-white rounded-xl p-6 shadow-sm border border-pink-100">
                                                                <div class="flex items-center gap-2 mb-4">
                                                                    <div
                                                                        class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center">
                                                                        <span class="text-lg">💬</span>
                                                                    </div>
                                                                    <div>
                                                                        <span
                                                                            class="text-sm font-bold text-pink-900 uppercase tracking-wide block">Missing
                                                                            Soft Skills</span>
                                                                        <span
                                                                            class="text-xs text-pink-600 font-medium">(Critical
                                                                            Gaps)</span>
                                                                    </div>
                                                                </div>
                                                                <template
                                                                    x-if="result.soft_skills && result.soft_skills.length > 0">
                                                                    <div class="flex flex-wrap gap-2.5">
                                                                        <template x-for="skill in result.soft_skills"
                                                                            :key="skill.name || skill">
                                                                            <span
                                                                                class="px-4 py-2.5 bg-pink-50 text-pink-800 border border-pink-200 rounded-lg text-sm font-semibold hover:bg-pink-100 hover:border-pink-300 transition-all shadow-sm"
                                                                                x-text="skill.name || skill">
                                                                            </span>
                                                                        </template>
                                                                    </div>
                                                                </template>
                                                                <template
                                                                    x-if="!result.soft_skills || result.soft_skills.length === 0">
                                                                    <div class="text-center py-6">
                                                                        <div class="text-3xl mb-2 opacity-20">✓</div>
                                                                        <p class="text-sm text-gray-400 font-medium">No
                                                                            soft skill gaps identified</p>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Pagination Controls -->
                                <div
                                    class="px-8 py-5 border-t bg-white flex items-center justify-between shadow-inner">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <span>Showing</span>
                                        <span class="font-bold text-gray-900"
                                            x-text="(currentPage - 1) * itemsPerPage + 1"></span>
                                        <span>to</span>
                                        <span class="font-bold text-gray-900"
                                            x-text="Math.min(currentPage * itemsPerPage, {{ count($matrix_results) }})"></span>
                                        <span>of</span>
                                        <span class="font-bold text-gray-900">{{ count($matrix_results) }}</span>
                                        <span>results</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <!-- Previous Button -->
                                        <button @click="prevPage()" :disabled="currentPage === 1"
                                            :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed' :
                                                'hover:bg-gray-50 hover:border-gray-400'"
                                            class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 transition-all">
                                            Previous
                                        </button>

                                        <!-- Page Numbers -->
                                        <div class="flex gap-1.5">
                                            <template x-for="page in totalPages" :key="page">
                                                <button @click="goToPage(page)"
                                                    :class="currentPage === page ?
                                                        'bg-emerald-500 text-white border-emerald-500 shadow-md' :
                                                        'bg-white text-gray-700 hover:bg-gray-50 border-gray-300'"
                                                    class="min-w-[44px] px-4 py-2.5 rounded-lg border text-sm font-bold transition-all"
                                                    x-text="page">
                                                </button>
                                            </template>
                                        </div>

                                        <!-- Next Button -->
                                        <button @click="nextPage()" :disabled="currentPage === totalPages"
                                            :class="currentPage === totalPages ? 'opacity-40 cursor-not-allowed' :
                                                'hover:bg-gray-50 hover:border-gray-400'"
                                            class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 transition-all">
                                            Next
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="p-12 text-center bg-white">
                                    <div class="text-6xl mb-4 opacity-20">📊</div>
                                    <p class="text-slate-500 font-medium">No competency gap data available yet.</p>
                                    <p class="text-slate-400 text-sm mt-2">Data will appear once submissions are
                                        approved.</p>
                                </div>
                            @endif
                        </div>

                        <div class="p-4 bg-slate-50 border-t text-center">
                            <p class="text-xs text-slate-500">
                                Source: Tab1-Employment-Davao-Region-with-JUL2025.xlsx (Rates) | Module 2 Sources:
                                PhilJobNet, PSA ISLE, Industry Surveys.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div id="lmi-matrix-modal" class="fixed inset-0 z-50 flex items-center justify-center px-4 hidden">
            <div id="modal-backdrop" class="absolute inset-0 backdrop-blur-md bg-white/30 pointer-events-none"></div>
            <div id="lmi-form-content"
                class="bg-white rounded-2xl shadow-2xl w-full w-[96vw] h-[96vh] max-w-[96vw] max-h-[96vh] overflow-hidden relative z-10 pointer-events-auto">

                <div class="bg-teal-700 p-5 flex justify-between items-center text-white sticky top-0 z-10">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-bold">INDUSTRY SKILLS NEED SURVEY</h3>
                    </div>
                    <button id="close-modal-btn" class="text-white hover:bg-teal-600 p-1 rounded transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- ► STEP INDICATOR ◄ -->
                <div class="bg-teal-600 px-5 py-4 sticky top-[68px] z-10">
                    <div class="flex items-center justify-between max-w-3xl mx-auto">
                        <div class="flex flex-col items-center">
                            <div
                                class="step-circle w-8 h-8 rounded-full bg-white text-teal-700 flex items-center justify-center text-sm font-bold">
                                1</div>
                            <span class="text-white text-xs mt-1 hidden sm:block">Company</span>
                        </div>
                        <div class="step-line flex-1 h-1 bg-teal-500 mx-2"></div>
                        <div class="flex flex-col items-center">
                            <div
                                class="step-circle w-8 h-8 rounded-full bg-teal-500 text-white flex items-center justify-center text-sm font-bold">
                                2</div>
                            <span class="text-white text-xs mt-1 hidden sm:block">Roles</span>
                        </div>
                        <div class="step-line flex-1 h-1 bg-teal-500 mx-2"></div>
                        <div class="flex flex-col items-center">
                            <div
                                class="step-circle w-8 h-8 rounded-full bg-teal-500 text-white flex items-center justify-center text-sm font-bold">
                                3</div>
                            <span class="text-white text-xs mt-1 hidden sm:block">Diagnosis</span>
                        </div>
                        <div class="step-line flex-1 h-1 bg-teal-500 mx-2"></div>
                        <div class="flex flex-col items-center">
                            <div
                                class="step-circle w-8 h-8 rounded-full bg-teal-500 text-white flex items-center justify-center text-sm font-bold">
                                4</div>
                            <span class="text-white text-xs mt-1 hidden sm:block">Engagement</span>
                        </div>
                    </div>
                </div>
                <!-- ► END STEP INDICATOR ◄ -->

                <div class="overflow-y-auto h-[calc(98vh-250px)]">
                    <div class="p-8">
                        <h4 class="text-l font-bold pb-2">INDUSTRY SKILLS NEED SURVEY</h4>
                        <p class="text-gray-600 text-sm leading-relaxed mb-8 pb-6 border-b border-gray-200">
                            {{ __('lmip.lmi_intro') }}
                        </p>
                        <h5 class="text-l font-bold pb-2">DATA PRIVACY STATEMENT</h5>
                        <p class="text-gray-600 text-sm leading-relaxed mb-8 pb-6 border-b border-gray-200">
                            {{ __('lmip.privacy_statement') }}
                        </p>

                        <!-- ════════════════════════════════════════════════════════
                 SINGLE FORM — all 4 steps live inside here
                 ════════════════════════════════════════════════════════ -->
                        <form action="{{ route('lmi.submit') }}" method="POST" class="space-y-5" id="lmi-form">
                            @csrf
                            <input type="hidden" name="test_form_start" value="FORM_STARTED">


                            <!-- ─── STEP 1: COMPANY PROFILE ─────────────────────── -->
                            <div class="lmi-step" data-step="0">

                                <div class="bg-gray-50 rounded-lg p-6 mt-8">
                                    <div class="flex items-start gap-2 text-base font-semibold mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        PART 1: COMPANY PROFILE
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                                        <div>
                                            <label class="block text-gray-700 text-sm font-medium mb-2">Company
                                                Name:</label>
                                            <input type="text" name="company" required
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 text-sm font-medium mb-2">Name of
                                                Respondent:</label>
                                            <input type="text" name="respondent" required
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 text-sm font-medium mb-2">Designation /
                                                Position:</label>
                                            <input type="text" name="position" required
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 text-sm font-medium mb-2">Contact
                                                Number:</label>
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span
                                                        class="text-gray-500 sm:text-sm border-r pr-2 border-gray-300">🇵🇭
                                                        +63</span>
                                                </div>
                                                <input type="tel" name="contact_number" maxlength="11"
                                                    placeholder="912 345 6789" required
                                                    class="w-full pl-16 pr-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                                        <div>
                                            <label class="block text-gray-700 text-sm font-medium mb-2">Email
                                                Address:</label>
                                            <input type="email" name="email" required
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                        </div>
                                    </div>

                                    <!-- Industry Sector Dropdown -->
                                    <div class="relative mt-4">
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Industry
                                            Sector:</label>
                                        <button type="button" id="industry-dropdown-btn"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 hover:border-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                            <span id="industry-selected-text" class="text-gray-400">Please select your
                                                primary operation</span>
                                            <svg id="industry-dropdown-arrow"
                                                class="w-5 h-5 text-gray-400 transition-transform" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div id="industry-dropdown-menu"
                                            class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                            <div data-value="Accommodation &amp; Food Service"
                                                class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                • Accommodation &amp; Food Service (Hotels, Resorts, Restaurants, Fast
                                                Food Chains, Catering Services)</div>
                                            <div data-value="Administrative &amp; Support Services"
                                                class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                • Administrative &amp; Support Services (Security Agencies,
                                                Manpower/Recruitment Agencies, Call Centers, Travel Agencies, Janitorial
                                                Services)</div>
                                            <div data-value="Agriculture, Forestry, Fishing &amp; Mining"
                                                class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                • Agriculture, Forestry, Fishing &amp; Mining</div>
                                            <div data-value="Construction"
                                                class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                • Construction</div>
                                            <div data-value="Education"
                                                class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                • Education (Private Schools, Colleges, Universities, Training Centers)
                                            </div>
                                            <div data-value="Electricity, Gas, Water &amp; Waste Management"
                                                class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                • Electricity, Gas, Water &amp; Waste Management (Power Plants, Electric
                                                Co-ops, Water Districts, Garbage/Recycling Firms)</div>
                                            <div data-value="Financial &amp; Insurance Activities"
                                                class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                • Financial &amp; Insurance Activities (Banks, Pawnshops, Lending
                                                Investors, Insurance Companies)</div>
                                            <div data-value="Human Health &amp; Social Work"
                                                class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                • Human Health &amp; Social Work (Hospital, Medical Clinics, Diagnostic
                                                Labs, Nursing Homes)</div>
                                            <div data-value="Information &amp; Communication"
                                                class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                • Information &amp; Communication (Software Companies, ISPs, Telecoms,
                                                TV/Radio Stations, Non-Voice Tech BPO)</div>
                                            <div data-value="Other Service Activities"
                                                class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                • Other Service Activities (Repairs Shops, Beauty Salons, Spas, Laundry
                                                Shops, Funeral)</div>
                                            <div data-value="Professional, Scientific &amp; Technical Services"
                                                class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                • Professional, Scientific &amp; Technical Services (Law Firms,
                                                Accounting/Auditing Firms, Engineering/Architectural Firms, Advertising
                                                Agencies)</div>
                                            <div data-value="Real Estate Activities"
                                                class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                • Real Estate Activities (Real Estate Developers, Lessor of
                                                Apartment/Office Space)</div>
                                            <div data-value="Transportation, Storage &amp; Logistics"
                                                class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                • Transportation, Storage &amp; Logistics (Trucking/Hauling Services,
                                                Warehousing, Shipping Lines, Courier Services)</div>
                                            <div data-value="Wholesale &amp; Retail Trade"
                                                class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                • Wholesale &amp; Retail Trade (Trading Companies, Malls, Hardware
                                                Stores, Car Dealers, Online Shops, etc.)</div>
                                        </div>
                                        <input type="hidden" id="industry-selector-input" name="industrySelector"
                                            required>
                                    </div>

                                    <!-- Company Size Dropdown -->
                                    <div class="relative mt-4">
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Company
                                            Size:</label>
                                        <button type="button" id="company-size-btn"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 hover:border-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                            <span id="company-size-selected-text" class="text-gray-400">Select company
                                                size</span>
                                            <svg id="company-size-arrow"
                                                class="w-5 h-5 text-gray-400 transition-transform" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div id="company-size-dropdown"
                                            class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                            <div data-value="Less than 50"
                                                class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                Less than 50</div>
                                            <div data-value="51-200"
                                                class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                51-200</div>
                                            <div data-value="201-500"
                                                class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                201-500</div>
                                            <div data-value="More than 500"
                                                class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                More than 500</div>
                                        </div>
                                        <input type="hidden" id="company-size-input" name="companySize" required>
                                    </div>
                                </div>

                                <!-- NAV -->
                                <div class="flex justify-end mt-6">
                                    <button type="button"
                                        class="btn-next bg-teal-600 hover:bg-teal-700 text-white font-semibold px-8 py-2.5 rounded-lg transition shadow">Next
                                        →</button>
                                </div>
                            </div>
                            <!-- ─── END STEP 1 ──────────────────────────────────── -->


                            <!-- ─── STEP 2: HARD-TO-FILL ROLES ──────────────────── -->
                            <div class="lmi-step" data-step="1" style="display:none;">

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
                                        Please identify the TOP Job Titles you find hardest to fill. Be as specific as
                                        possible (e.g., instead of "IT Skills", say "Python Programming").
                                    </p>

                                    <div id="jobTitlesContainer" class="space-y-6">
                                        <div class="bg-white rounded-lg p-4 border border-gray-200 job-entry">
                                            <!-- 8 -->
                                            <div class="mb-4">
                                                <label class="block text-gray-700 text-sm font-medium mb-2">8. Job
                                                    Title: <span class="text-red-500">*</span></label>
                                                <input type="text" name="job_title[]"
                                                    placeholder="e.g. Senior Java Developer" required
                                                    class="job-title-input w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                            </div>
                                            <!-- 9 -->
                                            <div class="mb-4">
                                                <label class="block text-gray-700 text-sm font-medium mb-2">9. Standard
                                                    Job Classifications / Families: <span
                                                        class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <button type="button"
                                                        class="job-classification-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                                        <span class="job-classification-text text-gray-400">Select job
                                                            classification</span>
                                                        <svg class="job-classification-arrow w-5 h-5 text-gray-400 transition-transform"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    </button>
                                                    <div
                                                        class="job-classification-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                                        <div data-value="Accounting, Finance &amp; Banking"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • Accounting, Finance &amp; Banking</div>
                                                        <div data-value="Administrative, HR &amp; Office Support"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • Administrative, HR &amp; Office Support</div>
                                                        <div data-value="Agriculture, Forestry &amp; Agribusiness"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • Agriculture, Forestry &amp; Agribusiness</div>
                                                        <div data-value="Construction, Engineering &amp; Architecture"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • Construction, Engineering &amp; Architecture</div>
                                                        <div data-value="Customer Service &amp; BPO (Contact Center)"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • Customer Service &amp; BPO (Contact Center)</div>
                                                        <div data-value="Education, Training &amp; Academe"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • Education, Training &amp; Academe</div>
                                                        <div data-value="Healthcare, Medical &amp; Allied Services"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • Healthcare, Medical &amp; Allied Services</div>
                                                        <div data-value="IT, Software, Data &amp; Digital Creative"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • IT, Software, Data &amp; Digital Creative</div>
                                                        <div data-value="Legal, Compliance &amp; Public Service"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • Legal, Compliance &amp; Public Service</div>
                                                        <div data-value="Logistics, Transport &amp; Supply Chain"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • Logistics, Transport &amp; Supply Chain</div>
                                                        <div data-value="Manufacturing, Production &amp; Technical"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • Manufacturing, Production &amp; Technical</div>
                                                        <div data-value="Sales, Marketing, Retail &amp; E-Commerce"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • Sales, Marketing, Retail &amp; E-Commerce</div>
                                                        <div data-value="Science, Research &amp; Laboratory"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • Science, Research &amp; Laboratory</div>
                                                        <div data-value="Skilled Trades, Maintenance &amp; General Services"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • Skilled Trades, Maintenance &amp; General Services</div>
                                                        <div data-value="Tourism, Hospitality &amp; Food Service"
                                                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            • Tourism, Hospitality &amp; Food Service</div>
                                                    </div>
                                                    <input type="hidden" class="job-classification-input"
                                                        name="job_classification[]" required>
                                                </div>
                                            </div>
                                            <!-- 10 -->
                                            <div class="mb-4">
                                                <label class="block text-gray-700 text-sm font-medium mb-2">10.
                                                    Duration that the Vacancy is Open: <span
                                                        class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <button type="button"
                                                        class="duration-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                                        <span class="duration-text text-gray-400">Select
                                                            duration</span>
                                                        <svg class="duration-arrow w-5 h-5 text-gray-400 transition-transform"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    </button>
                                                    <div
                                                        class="duration-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden hidden">
                                                        <div data-value="Less than 30 Days"
                                                            class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            Less than 30 Days</div>
                                                        <div data-value="30-60 Days"
                                                            class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            30-60 Days</div>
                                                        <div data-value="60-90 Days"
                                                            class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            60-90 Days</div>
                                                        <div data-value="90+ Days"
                                                            class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                            90+ Days</div>
                                                    </div>
                                                    <input type="hidden" class="duration-input"
                                                        name="vacancy_duration[]" required>
                                                </div>
                                            </div>
                                            <!-- 11 -->
                                            <div class="mb-4">
                                                <label class="block text-gray-700 text-sm font-medium mb-2">
                                                    11. Reasons For Difficulty (Role-Level) <span
                                                        class="italic text-gray-500">(Check all that apply)</span>
                                                </label>
                                                <div class="difficulty-reasons space-y-3">
                                                    <label
                                                        class="technical-skills-label flex items-start p-3 border rounded-lg cursor-pointer transition-all border-gray-200 hover:bg-gray-50">
                                                        <input type="checkbox" name="difficulty_reasons_0[]"
                                                            value="Technical / Hard Skills Missing"
                                                            class="technical-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                                        <div class="ml-3 flex-1">
                                                            <div class="font-medium text-gray-700">Technical / Hard
                                                                Skills Missing</div>
                                                            <div class="text-xs text-gray-500 mt-1">Applicants do not
                                                                have the required tools, software, or technical
                                                                knowledge</div>
                                                            <div class="technical-details mt-3 hidden">
                                                                <label
                                                                    class="block text-gray-600 text-xs font-medium mb-1">What
                                                                    specific technical tools, software, or machinery
                                                                    knowledge is missing?</label>
                                                                <div
                                                                    class="technical-tags-container flex flex-wrap gap-2 mb-2">
                                                                </div>
                                                                <div class="flex gap-2">
                                                                    <input type="text"
                                                                        class="technical-skill-input flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                                                        placeholder="Type a skill and press Enter (e.g. Python, SQL, AutoCAD...)" />
                                                                    <button type="button"
                                                                        class="add-technical-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Add</button>
                                                                </div>
                                                                <p class="text-xs text-gray-500 mt-1">Press Enter or
                                                                    comma to add each skill</p>
                                                                <input type="hidden" class="technical-skills-input"
                                                                    name="technical_skills_missing[]">
                                                            </div>
                                                        </div>
                                                    </label>
                                                    <label
                                                        class="soft-skills-label flex items-start p-3 border rounded-lg cursor-pointer transition-all border-gray-200 hover:bg-gray-50">
                                                        <input type="checkbox" name="difficulty_reasons_0[]"
                                                            value="Soft / Employability Skills Missing"
                                                            class="soft-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                                        <div class="ml-3 flex-1">
                                                            <div class="font-medium text-gray-700">Soft / Employability
                                                                Skills Missing</div>
                                                            <div class="text-xs text-gray-500 mt-1">Applicants cannot
                                                                communicate effectively, work in teams, or demonstrate
                                                                professionalism</div>
                                                            <div class="soft-details mt-3 hidden">
                                                                <label
                                                                    class="block text-gray-600 text-xs font-medium mb-1">What
                                                                    attitude or behavioral traits cause you to reject
                                                                    applicants?</label>
                                                                <div
                                                                    class="soft-tags-container flex flex-wrap gap-2 mb-2">
                                                                </div>
                                                                <div class="flex gap-2">
                                                                    <input type="text"
                                                                        class="soft-skill-input flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                                                        placeholder="Type a trait and press Enter (e.g. Poor communication, Unprofessional...)" />
                                                                    <button type="button"
                                                                        class="add-soft-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Add</button>
                                                                </div>
                                                                <p class="text-xs text-gray-500 mt-1">Press Enter or
                                                                    comma to add each trait</p>
                                                                <input type="hidden" class="soft-skills-input"
                                                                    name="soft_skills_missing[]">
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                            <!-- 12 -->
                                            <div class="mb-4 mt-6 pt-4 border-t border-gray-200">
                                                <label class="block text-gray-700 text-sm font-medium mb-3">
                                                    12. How much does the difficulty finding qualified applicants for
                                                    this role impact your business operations? <span
                                                        class="text-red-500">*</span>
                                                </label>
                                                <div class="impact-level space-y-3">
                                                    <label
                                                        class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                        <input type="radio" name="impact_level_0" value="High"
                                                            required
                                                            class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                        <div class="ml-3 flex-1">
                                                            <div class="font-medium text-gray-800">High Impact</div>
                                                            <div class="text-xs text-gray-500 mt-1">Operations are
                                                                significantly disrupted, critical tasks or projects are
                                                                delayed, affecting productivity and revenue</div>
                                                        </div>
                                                    </label>
                                                    <label
                                                        class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                        <input type="radio" name="impact_level_0" value="Medium"
                                                            required
                                                            class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                        <div class="ml-3 flex-1">
                                                            <div class="font-medium text-gray-800">Medium Impact</div>
                                                            <div class="text-xs text-gray-500 mt-1">Operations continue
                                                                but require overtime, increased workload for existing
                                                                staff, or minor project delays</div>
                                                        </div>
                                                    </label>
                                                    <label
                                                        class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                        <input type="radio" name="impact_level_0" value="Low"
                                                            required
                                                            class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                        <div class="ml-3 flex-1">
                                                            <div class="font-medium text-gray-800">Low Impact</div>
                                                            <div class="text-xs text-gray-500 mt-1">Minimal impact; new
                                                                hires can be trained internally without significant
                                                                operational disruptions</div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" id="add-job-title-btn"
                                        class="w-full mt-4 px-4 py-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg transition shadow-md flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add Another Job Title
                                    </button>
                                </div>

                                <!-- NAV -->
                                <div class="flex justify-between mt-6">
                                    <button type="button"
                                        class="btn-prev bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-8 py-2.5 rounded-lg transition shadow">←
                                        Previous</button>
                                    <button type="button"
                                        class="btn-next bg-teal-600 hover:bg-teal-700 text-white font-semibold px-8 py-2.5 rounded-lg transition shadow">Next
                                        →</button>
                                </div>
                            </div>
                            <!-- ─── END STEP 2 ──────────────────────────────────── -->


                            <!-- ─── STEP 3: DIAGNOSIS OF MISMATCH ───────────────── -->
                            <div class="lmi-step" data-step="2" style="display:none;">

                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 mt-10">
                                    <div class="flex items-start gap-2 text-orange-700 text-base font-semibold mb-2">
                                        <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        PART III: DIAGNOSIS OF MISMATCH
                                    </div>
                                    <p class="text-gray-700 text-xs italic mb-6">
                                        For applicants who meet formal qualifications (degree, license, or
                                        certification), which observable factors most often cause them to be rejected?
                                    </p>

                                    <div class="space-y-6">
                                        <!-- 13 -->
                                        <div class="bg-white rounded-lg p-5 border border-gray-200">
                                            <label class="block text-gray-700 text-sm font-medium mb-3">
                                                13. Reason Qualified Applicants Are Rejected (Applicant-Level) <span
                                                    class="text-gray-500 italic text-xs">(Check all that apply)</span>
                                            </label>
                                            <div class="space-y-3">
                                                <label
                                                    class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                    <input type="checkbox" name="rejection_reasons[]"
                                                        value="Lack of practical / hands-on experience"
                                                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                                    <div class="ml-3 flex-1">
                                                        <div class="font-medium text-gray-800">Lack of practical /
                                                            hands-on experience</div>
                                                        <div class="text-xs text-gray-500 mt-1">Cannot apply theory to
                                                            real work; requires supervision</div>
                                                    </div>
                                                </label>
                                                <label
                                                    class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                    <input type="checkbox" name="rejection_reasons[]"
                                                        value="Skills are outdated"
                                                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                                    <div class="ml-3 flex-1">
                                                        <div class="font-medium text-gray-800">Skills are outdated
                                                        </div>
                                                        <div class="text-xs text-gray-500 mt-1">Training received does
                                                            not match current tools, systems, or industry practices
                                                        </div>
                                                    </div>
                                                </label>
                                                <label
                                                    class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                    <input type="checkbox" name="rejection_reasons[]"
                                                        value="Poor communication skills"
                                                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                                    <div class="ml-3 flex-1">
                                                        <div class="font-medium text-gray-800">Poor communication
                                                            skills</div>
                                                        <div class="text-xs text-gray-500 mt-1">Oral, written,
                                                            presentation, or cross-cultural communication issues</div>
                                                    </div>
                                                </label>
                                                <label
                                                    class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                    <input type="checkbox" name="rejection_reasons[]"
                                                        value="Low job readiness / poor interview performance"
                                                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                                    <div class="ml-3 flex-1">
                                                        <div class="font-medium text-gray-800">Low job readiness / poor
                                                            interview performance</div>
                                                        <div class="text-xs text-gray-500 mt-1">Cannot demonstrate
                                                            readiness during recruitment; fails assessments; lacks
                                                            workplace etiquette</div>
                                                    </div>
                                                </label>
                                                <div
                                                    class="other-rejection-option border rounded-lg transition-all border-gray-200">
                                                    <label class="flex items-start p-3 cursor-pointer">
                                                        <input type="checkbox" name="rejection_reasons[]"
                                                            value="Other"
                                                            class="other-rejection-checkbox mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                                        <div class="ml-3 flex-1">
                                                            <div class="font-medium text-gray-800">Other (please
                                                                specify)</div>
                                                        </div>
                                                    </label>
                                                    <div class="other-rejection-input px-3 pb-3 ml-7 hidden">
                                                        <input type="text" name="rejection_reasons_other"
                                                            placeholder="Please specify other reasons..."
                                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- 14 -->
                                        <div class="bg-white rounded-lg p-5 border border-gray-200">
                                            <label class="block text-gray-700 text-sm font-medium mb-3">
                                                14. How often do you coordinate with Universities/Colleges to discuss
                                                your skills requirements? <span
                                                    class="text-gray-500 italic text-xs">(Select ONE)</span>
                                            </label>
                                            <div class="coordination-options space-y-3">
                                                <label
                                                    class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                    <input type="radio" name="coordination_frequency"
                                                        value="Never" required
                                                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                    <div class="ml-3 flex-1">
                                                        <div class="font-medium text-gray-800">Never</div>
                                                    </div>
                                                </label>
                                                <label
                                                    class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                    <input type="radio" name="coordination_frequency"
                                                        value="Rarely" required
                                                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                    <div class="ml-3 flex-1">
                                                        <div class="font-medium text-gray-800">Rarely</div>
                                                        <div class="text-xs text-gray-500 mt-1">Only when invited to
                                                            graduations/events</div>
                                                    </div>
                                                </label>
                                                <label
                                                    class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                    <input type="radio" name="coordination_frequency"
                                                        value="Occasionally" required
                                                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                    <div class="ml-3 flex-1">
                                                        <div class="font-medium text-gray-800">Occasionally</div>
                                                        <div class="text-xs text-gray-500 mt-1">During OJT placement
                                                        </div>
                                                    </div>
                                                </label>
                                                <label
                                                    class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                    <input type="radio" name="coordination_frequency"
                                                        value="Frequently" required
                                                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                    <div class="ml-3 flex-1">
                                                        <div class="font-medium text-gray-800">Frequently</div>
                                                        <div class="text-xs text-gray-500 mt-1">We sit on advisory
                                                            boards/curriculum reviews</div>
                                                    </div>
                                                </label>
                                                <div
                                                    class="other-coordination-option border rounded-lg transition-all border-gray-200">
                                                    <label class="flex items-start p-3 cursor-pointer">
                                                        <input type="radio" name="coordination_frequency"
                                                            value="Other" required
                                                            class="other-coordination-radio mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                        <div class="ml-3 flex-1">
                                                            <div class="font-medium text-gray-800">Other (please
                                                                specify)</div>
                                                        </div>
                                                    </label>
                                                    <div class="other-coordination-input px-3 pb-3 ml-7 hidden">
                                                        <input type="text" name="coordination_frequency_other"
                                                            placeholder="Please specify..."
                                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- NAV -->
                                <div class="flex justify-between mt-6">
                                    <button type="button"
                                        class="btn-prev bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-8 py-2.5 rounded-lg transition shadow">←
                                        Previous</button>
                                    <button type="button"
                                        class="btn-next bg-teal-600 hover:bg-teal-700 text-white font-semibold px-8 py-2.5 rounded-lg transition shadow">Next
                                        →</button>
                                </div>
                            </div>
                            <!-- ─── END STEP 3 ──────────────────────────────────── -->


                            <!-- ─── STEP 4: ENGAGEMENT & NEXT STEPS ─────────────── -->
                            <div class="lmi-step" data-step="3" style="display:none;">

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-8">
                                    <div class="flex items-start gap-2 text-blue-700 text-base font-semibold mb-2">
                                        <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                        </svg>
                                        PART IV: ENGAGEMENT &amp; NEXT STEPS
                                    </div>
                                    <p class="text-blue-600 text-xs italic mb-4">Help us understand what features would
                                        be most valuable to you.</p>

                                    <div class="space-y-5">
                                        <!-- 20 -->
                                        <div>
                                            <label class="block text-gray-700 text-sm font-medium mb-3">
                                                20. If DOLE provides a Regional LMI Dashboard, what features would be
                                                most useful for you? <span class="text-gray-500 text-xs">(Select top
                                                    2)</span>
                                            </label>
                                            <div class="space-y-3">
                                                <label
                                                    class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">
                                                    <input type="checkbox" name="lmi_features[]"
                                                        value="Viewing the supply of graduates"
                                                        class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                    <div class="ml-3 flex-1">
                                                        <div class="font-medium text-gray-800">Viewing the supply of
                                                            graduates (e.g., "How many IT grads will graduate next
                                                            year?")</div>
                                                    </div>
                                                </label>
                                                <label
                                                    class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">
                                                    <input type="checkbox" name="lmi_features[]"
                                                        value="A channel to submit real-time feedback"
                                                        class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                    <div class="ml-3 flex-1">
                                                        <div class="font-medium text-gray-800">A channel to submit
                                                            real-time feedback on curriculum quality</div>
                                                    </div>
                                                </label>
                                                <label
                                                    class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">
                                                    <input type="checkbox" name="lmi_features[]"
                                                        value="A directory of job placement offices"
                                                        class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                    <div class="ml-3 flex-1">
                                                        <div class="font-medium text-gray-800">A directory of job
                                                            placement offices and Public Employment offices (PESOs)
                                                        </div>
                                                    </div>
                                                </label>
                                                <label
                                                    class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">
                                                    <input type="checkbox" name="lmi_features[]" value="Other"
                                                        class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                    <div class="ml-3 flex-1">
                                                        <div class="font-medium text-gray-800">Other</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Additional -->
                                        <div>
                                            <label class="block text-gray-700 text-sm font-medium mb-2">
                                                Additional Insights or Suggestions: <span
                                                    class="text-gray-500 text-xs">(Optional)</span>
                                            </label>
                                            <textarea name="specific_inputs" rows="4" placeholder="Please share any additional insights or suggestions..."
                                                class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Consent -->
                                <div class="mt-6 mb-2">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="checkbox" name="consent" value="1" required
                                            class="consent-checkbox mt-1 w-4 h-4 text-teal-600">
                                        <span class="ml-3 text-sm text-gray-700">
                                            I consent to submit this data for labor market intelligence purposes. <span
                                                class="text-red-500">*</span>
                                        </span>
                                    </label>
                                </div>

                                <!-- NAV -->
                                <div class="flex justify-between mt-6">
                                    <button type="button"
                                        class="btn-prev bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-8 py-2.5 rounded-lg transition shadow">←
                                        Previous</button>
                                    <button type="submit"
                                        class="btn-submit-lmi bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-8 rounded-lg transition shadow-lg">
                                        Submit LMI Matrix
                                    </button>
                                </div>
                            </div>
                            <!-- ─── END STEP 4 ──────────────────────────────────── -->

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ► paste lmi-steps-final.js in your script stack here ◄ -->

        <!-- Confirmation Modal -->
        <div id="confirmation-modal" class="fixed inset-0 flex items-center justify-center px-4 hidden"
            style="z-index: 9999;">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative p-6 z-10">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Submission</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Are you sure you want to submit this Industry Skills Need Survey? Please ensure all information
                        is accurate before proceeding.
                    </p>
                    <div class="flex gap-3">
                        <button type="button" id="cancel-submit-btn"
                            class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
                            No, Cancel
                        </button>
                        <button type="button" id="confirm-submit-btn"
                            class="flex-1 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg transition">
                            Yes, Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="fixed inset-0 flex items-center justify-center px-4 hidden"
        style="z-index: 9999;">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative p-6 z-10">

            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Successfully Submitted!</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Your Industry Skills Need Survey has been submitted successfully. Thank you for your contribution to
                    the Labor Market Intelligence system.
                </p>
                <button type="button" id="close-success-btn"
                    class="w-full px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg transition">
                    Close
                </button>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    <script>
        // Toggle role details function
        function toggleRoleDetails(submissionId, index) {
            const details = document.getElementById(`role-details-${submissionId}-${index}`);
            const icon = details.previousElementSibling.querySelector('.expand-icon');

            if (details.classList.contains('hidden')) {
                details.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                details.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Prepare comparison data
        const comparisonData = @json($comparison_data ?? []);

        // Create labels and datasets
        const labels = comparisonData.map(d => d.title);
        const currentYearData = comparisonData.map(d => d.current_count);
        const previousYearData = comparisonData.map(d => d.previous_count);

        // Chart configuration
        const chartConfig = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: '{{ $selected_year - 1 ?? 'Previous Year' }}',
                        data: previousYearData,
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 0,
                        borderRadius: 4,
                        barThickness: 18,
                    },
                    {
                        label: '{{ $selected_year ?? 'Current Year' }}',
                        data: currentYearData,
                        backgroundColor: 'rgba(59, 130, 246, 0.9)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 0,
                        borderRadius: 4,
                        barThickness: 18,
                    }
                ]
            },
            options: {
                indexAxis: 'y', // Horizontal bars
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 12,
                            boxHeight: 12,
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 13,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.x.toLocaleString();

                                // Add change info for current year
                                if (context.datasetIndex === 1) {
                                    const dataIndex = context.dataIndex;
                                    if (comparisonData[dataIndex]) {
                                        const change = comparisonData[dataIndex].change;
                                        const isNew = comparisonData[dataIndex].is_new;

                                        if (isNew) {
                                            label += ' (NEW)';
                                        } else if (change !== 0) {
                                            label += ` (${change > 0 ? '+' : ''}${change}%)`;
                                        }
                                    }
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.03)'
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            callback: function(value) {
                                if (value >= 1000) {
                                    return (value / 1000) + 'k';
                                }
                                return value;
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            color: '#374151'
                        }
                    }
                },
                interaction: {
                    mode: 'y',
                    intersect: false
                }
            }
        };

        // Initialize main chart
        let mainChart = null;
        let expandedChart = null;

        if (comparisonData && comparisonData.length > 0) {
            const ctx = document.getElementById('highVolumeHorizontalChart');
            if (ctx) {
                mainChart = new Chart(ctx, chartConfig);
            }
        }

        // Expand chart function
        function expandChart() {
            const modal = document.getElementById('chartModal');
            modal.classList.remove('hidden');

            // Destroy existing expanded chart if any
            if (expandedChart) {
                expandedChart.destroy();
            }

            // Create expanded chart
            const expandedCtx = document.getElementById('highVolumeExpandedChart');
            if (expandedCtx && comparisonData && comparisonData.length > 0) {
                expandedChart = new Chart(expandedCtx, {
                    ...chartConfig,
                    options: {
                        ...chartConfig.options,
                        plugins: {
                            ...chartConfig.options.plugins,
                            legend: {
                                ...chartConfig.options.plugins.legend,
                                labels: {
                                    ...chartConfig.options.plugins.legend.labels,
                                    font: {
                                        size: 14,
                                        weight: '500'
                                    }
                                }
                            }
                        },
                        scales: {
                            ...chartConfig.options.scales,
                            x: {
                                ...chartConfig.options.scales.x,
                                ticks: {
                                    font: {
                                        size: 12
                                    },
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                }
                            },
                            y: {
                                ...chartConfig.options.scales.y,
                                ticks: {
                                    font: {
                                        size: 13,
                                        weight: '500'
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Close chart function
        function closeChart() {
            const modal = document.getElementById('chartModal');
            modal.classList.add('hidden');

            if (expandedChart) {
                expandedChart.destroy();
                expandedChart = null;
            }
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeChart();
            }
        });
    </script>
    <script>
        // Main application state
        const appState = {
            showLmiMatrix: false
        };

        // Modal functionality
        const lmiMatrixModal = document.getElementById('lmi-matrix-modal');
        const showLmiMatrixBtn = document.getElementById('show-lmi-matrix-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const modalBackdrop = document.getElementById('modal-backdrop');
        const mainContent = document.getElementById('main-content');

        // Confirmation and Success Modals
        const confirmationModal = document.getElementById('confirmation-modal');
        const successModal = document.getElementById('success-modal');
        const lmiForm = document.getElementById('lmi-form');
        const cancelSubmitBtn = document.getElementById('cancel-submit-btn');
        const confirmSubmitBtn = document.getElementById('confirm-submit-btn');
        const closeSuccessBtn = document.getElementById('close-success-btn');

        // LMI Matrix Modal Functions
        function showModal() {
            lmiMatrixModal.classList.remove('hidden');
            //mainContent.classList.add('blur-sm');
            appState.showLmiMatrix = true;
            document.body.style.overflow = 'hidden';

            // ADD THIS: Initialize autocomplete when modal opens
            setTimeout(() => {
                console.log('🎯 Modal opened, initializing autocomplete...');
                initializeAllAutocompletes();
                console.log('✅ Autocomplete initialized for modal!');
            }, 200);
        }

        function hideModal() {
            lmiMatrixModal.classList.add('hidden');
            mainContent.classList.remove('blur-sm');
            appState.showLmiMatrix = false;
        }

        // Function to show confirmation modal (NO BLUR)
        function showConfirmationModal(e) {
            e.preventDefault();
            e.stopPropagation();
            confirmationModal.classList.remove('hidden');
            // Don't blur the LMI form - keep it clear behind the modal
        }

        // Function to hide confirmation modal
        function hideConfirmationModal() {
            confirmationModal.classList.add('hidden');
        }

        // Function to show success modal (NO BLUR)
        function showSuccessModal() {
            successModal.classList.remove('hidden');
            // Don't blur the LMI form - keep it clear behind the modal
        }

        // Function to hide success modal AND close the LMI form
        function hideSuccessModal() {
            // First hide the success modal
            successModal.classList.add('hidden');

            // Then close the LMI Matrix modal after a brief delay for smooth transition
            setTimeout(() => {
                hideModal();
            }, 300);
        }

        // Intercept form submission to show confirmation modal instead
        lmiForm.addEventListener('submit', showConfirmationModal);

        // Cancel button in confirmation modal
        cancelSubmitBtn.addEventListener('click', hideConfirmationModal);

        // Confirm submission button
        confirmSubmitBtn.addEventListener('click', async () => {
            // Hide confirmation modal
            hideConfirmationModal();

            // Validate form
            const consentCheckbox = document.querySelector('.consent-checkbox');
            if (!consentCheckbox || !consentCheckbox.checked) {
                alert('Please consent to submit this data for labor market intelligence purposes.');
                consentCheckbox.focus();
                return;
            }

            // Gather form data
            const formData = new FormData(lmiForm);

            // Show loading state
            const originalText = confirmSubmitBtn.textContent;
            confirmSubmitBtn.textContent = 'Submitting...';
            confirmSubmitBtn.disabled = true;

            try {
                // Log what we're sending (for debugging)
                console.log('Form data being sent:', Object.fromEntries(formData));
                console.log('Form action:', lmiForm.action);

                // Submit via AJAX
                const response = await fetch(lmiForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                            '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                console.log('Response status:', response.status);

                // Try to get the response text for debugging
                const responseText = await response.text();
                console.log('Response text:', responseText);

                if (response.ok) {
                    // Show success modal
                    showSuccessModal();
                    // Reset the form
                    lmiForm.reset();

                    // Reset all dropdowns to placeholder state
                    resetFormDropdowns();

                } else {
                    // Try to parse as JSON for error messages
                    try {
                        const errorData = JSON.parse(responseText);
                        throw new Error(errorData.message || 'Submission failed with status: ' + response
                            .status);
                    } catch (e) {
                        throw new Error('Submission failed with status: ' + response.status);
                    }
                }
            } catch (error) {
                console.error('Full submission error:', error);
                alert('There was an error submitting the form. Please try again. Error: ' + error.message);
            } finally {
                // Reset button state
                confirmSubmitBtn.textContent = originalText;
                confirmSubmitBtn.disabled = false;
            }
        });

        // Close success modal button - closes both modals
        closeSuccessBtn.addEventListener('click', hideSuccessModal);

        // Helper function to reset all form dropdowns
        function resetFormDropdowns() {
            // Reset industry dropdown
            const industryText = document.getElementById('industry-selected-text');
            const industryInput = document.getElementById('industry-selector-input');
            if (industryText && industryInput) {
                industryText.textContent = 'Please select your primary operation';
                industryText.classList.add('text-gray-400');
                industryText.classList.remove('text-gray-600');
                industryInput.value = '';
            }

            // Reset company size dropdown
            const companySizeText = document.getElementById('company-size-selected-text');
            const companySizeInput = document.getElementById('company-size-input');
            if (companySizeText && companySizeInput) {
                companySizeText.textContent = 'Select company size';
                companySizeText.classList.add('text-gray-400');
                companySizeText.classList.remove('text-gray-600');
                companySizeInput.value = '';
            }

            // Reset all job entry dropdowns
            document.querySelectorAll('.job-entry').forEach(entry => {
                const classText = entry.querySelector('.job-classification-text');
                const classInput = entry.querySelector('.job-classification-input');
                if (classText && classInput) {
                    classText.textContent = 'Select job classification';
                    classText.classList.add('text-gray-400');
                    classText.classList.remove('text-gray-600');
                    classInput.value = '';
                }

                const durationText = entry.querySelector('.duration-text');
                const durationInput = entry.querySelector('.duration-input');
                if (durationText && durationInput) {
                    durationText.textContent = 'Select duration';
                    durationText.classList.add('text-gray-400');
                    durationText.classList.remove('text-gray-600');
                    durationInput.value = '';
                }

                // Clear skill tags
                const techTagsContainer = entry.querySelector('.technical-tags-container');
                if (techTagsContainer) {
                    techTagsContainer.innerHTML = '';
                }

                const softTagsContainer = entry.querySelector('.soft-tags-container');
                if (softTagsContainer) {
                    softTagsContainer.innerHTML = '';
                }

                // Uncheck and hide detail sections
                const techCheckbox = entry.querySelector('.technical-checkbox');
                const techDetails = entry.querySelector('.technical-details');
                if (techCheckbox && techDetails) {
                    techCheckbox.checked = false;
                    techDetails.classList.add('hidden');
                    techCheckbox.closest('label')?.classList.remove('border-teal-500', 'bg-teal-50');
                    techCheckbox.closest('label')?.classList.add('border-gray-200', 'hover:bg-gray-50');
                }

                const softCheckbox = entry.querySelector('.soft-checkbox');
                const softDetails = entry.querySelector('.soft-details');
                if (softCheckbox && softDetails) {
                    softCheckbox.checked = false;
                    softDetails.classList.add('hidden');
                    softCheckbox.closest('label')?.classList.remove('border-teal-500', 'bg-teal-50');
                    softCheckbox.closest('label')?.classList.add('border-gray-200', 'hover:bg-gray-50');
                }
            });

            // Remove all additional job entries (keep only the first one)
            const jobEntries = document.querySelectorAll('.job-entry');
            jobEntries.forEach((entry, index) => {
                if (index > 0) {
                    entry.remove();
                }
            });
        }

        // Make sure these elements exist before adding event listeners
        if (showLmiMatrixBtn) {
            showLmiMatrixBtn.addEventListener('click', showModal);
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', hideModal);
        }

        if (modalBackdrop) {
            modalBackdrop.addEventListener('click', hideModal);
        }

        // Also close modals when clicking on backdrop
        confirmationModal.addEventListener('click', (e) => {
            if (e.target === confirmationModal || e.target.classList.contains('absolute')) {
                hideConfirmationModal();
            }
        });

        successModal.addEventListener('click', (e) => {
            if (e.target === successModal || e.target.classList.contains('absolute')) {
                hideSuccessModal();
            }
        });

        // Close modals with ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (!confirmationModal.classList.contains('hidden')) {
                    hideConfirmationModal();
                } else if (!successModal.classList.contains('hidden')) {
                    hideSuccessModal();
                } else if (appState.showLmiMatrix) {
                    hideModal();
                }
            }
        });


        // Dropdown functionality
        function createDropdown(buttonId, menuId, selectedTextId, hiddenInputId, optionsSelector, arrowId = null) {
            const button = document.getElementById(buttonId);
            const menu = document.getElementById(menuId);
            const selectedText = document.getElementById(selectedTextId);
            const hiddenInput = document.getElementById(hiddenInputId);
            const arrow = arrowId ? document.getElementById(arrowId) : null;
            const options = menu.querySelectorAll(optionsSelector);

            function toggleMenu() {
                menu.classList.toggle('hidden');
                if (arrow) {
                    arrow.classList.toggle('rotate-180');
                }

                // Close other dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                    if (otherMenu !== menu && !otherMenu.classList.contains('hidden')) {
                        otherMenu.classList.add('hidden');
                        const otherArrow = otherMenu.previousElementSibling?.querySelector('.rotate-180');
                        if (otherArrow) {
                            otherArrow.classList.remove('rotate-180');
                        }
                    }
                });
            }

            button.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleMenu();
            });

            options.forEach(option => {
                option.addEventListener('click', () => {
                    const value = option.getAttribute('data-value');
                    selectedText.textContent = value;
                    selectedText.classList.remove('text-gray-400');
                    selectedText.classList.add('text-gray-600');
                    hiddenInput.value = value;
                    menu.classList.add('hidden');
                    if (arrow) {
                        arrow.classList.remove('rotate-180');
                    }
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!button.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                    if (arrow) {
                        arrow.classList.remove('rotate-180');
                    }
                }
            });

            return {
                button,
                menu,
                selectedText,
                hiddenInput
            };
        }

        // Initialize dropdowns
        const industryDropdown = createDropdown(
            'industry-dropdown-btn',
            'industry-dropdown-menu',
            'industry-selected-text',
            'industry-selector-input',
            '.industry-option',
            'industry-dropdown-arrow'
        );

        const companySizeDropdown = createDropdown(
            'company-size-btn',
            'company-size-dropdown',
            'company-size-selected-text',
            'company-size-input',
            '.company-size-option',
            'company-size-arrow'
        );

        // Job entry functionality
        function createJobEntryDropdown(button, menu, textElement, inputElement, arrowElement, optionsSelector) {
            function toggleMenu() {
                menu.classList.toggle('hidden');
                arrowElement.classList.toggle('rotate-180');

                // Close other dropdowns in the same job entry
                const jobEntry = button.closest('.job-entry');
                jobEntry.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                    if (otherMenu !== menu && !otherMenu.classList.contains('hidden')) {
                        otherMenu.classList.add('hidden');
                        const otherArrow = otherMenu.previousElementSibling?.querySelector('.rotate-180');
                        if (otherArrow) {
                            otherArrow.classList.remove('rotate-180');
                        }
                    }
                });
            }

            button.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleMenu();
            });

            const options = menu.querySelectorAll(optionsSelector);
            options.forEach(option => {
                option.addEventListener('click', () => {
                    const value = option.getAttribute('data-value');
                    textElement.textContent = value;
                    textElement.classList.remove('text-gray-400');
                    textElement.classList.add('text-gray-600');
                    inputElement.value = value;
                    menu.classList.add('hidden');
                    arrowElement.classList.remove('rotate-180');
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!button.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                    arrowElement.classList.remove('rotate-180');
                }
            });
        }

        // Skill tags functionality
        function createSkillTagSystem(container, addButton, input, hiddenInput, tagsContainer) {
            const tags = [];

            function updateTags() {
                tagsContainer.innerHTML = '';
                tags.forEach((tag, index) => {
                    const tagElement = document.createElement('span');
                    tagElement.className =
                        'inline-flex items-center gap-1 px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm';
                    tagElement.innerHTML = `
                    <span>${tag}</span>
                    <button type="button" class="remove-tag hover:bg-teal-200 rounded-full p-0.5" data-index="${index}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                    tagsContainer.appendChild(tagElement);
                });

                // Update hidden input
                hiddenInput.value = tags.join(', ');

                // Add event listeners to remove buttons
                tagsContainer.querySelectorAll('.remove-tag').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const index = parseInt(e.target.closest('.remove-tag').getAttribute('data-index'));
                        tags.splice(index, 1);
                        updateTags();
                    });
                });
            }

            function addTag() {
                const tag = input.value.trim();
                if (tag && !tags.includes(tag)) {
                    tags.push(tag);
                    input.value = '';
                    updateTags();
                }
            }

            addButton.addEventListener('click', addTag);

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    addTag();
                }
            });

            return {
                tags,
                updateTags,
                addTag
            };
        }

        // Checkbox show/hide functionality
        function setupCheckboxToggle(checkbox, targetElement) {
            checkbox.addEventListener('change', () => {
                if (checkbox.checked) {
                    targetElement.classList.remove('hidden');
                    // Add active styles
                    checkbox.closest('label').classList.add('border-teal-500', 'bg-teal-50');
                    checkbox.closest('label').classList.remove('border-gray-200', 'hover:bg-gray-50');
                } else {
                    targetElement.classList.add('hidden');
                    // Remove active styles
                    checkbox.closest('label').classList.remove('border-teal-500', 'bg-teal-50');
                    checkbox.closest('label').classList.add('border-gray-200', 'hover:bg-gray-50');
                }
            });
        }

        // Initialize first job entry
        function initializeJobEntry(jobEntry) {
            // Classification dropdown
            const classBtn = jobEntry.querySelector('.job-classification-btn');
            const classMenu = jobEntry.querySelector('.job-classification-menu');
            const classText = jobEntry.querySelector('.job-classification-text');
            const classInput = jobEntry.querySelector('.job-classification-input');
            const classArrow = jobEntry.querySelector('.job-classification-arrow');

            if (classBtn && classMenu) {
                createJobEntryDropdown(
                    classBtn,
                    classMenu,
                    classText,
                    classInput,
                    classArrow,
                    '.job-classification-option'
                );
            }

            // Duration dropdown
            const durationBtn = jobEntry.querySelector('.duration-btn');
            const durationMenu = jobEntry.querySelector('.duration-menu');
            const durationText = jobEntry.querySelector('.duration-text');
            const durationInput = jobEntry.querySelector('.duration-input');
            const durationArrow = jobEntry.querySelector('.duration-arrow');

            if (durationBtn && durationMenu) {
                createJobEntryDropdown(
                    durationBtn,
                    durationMenu,
                    durationText,
                    durationInput,
                    durationArrow,
                    '.duration-option'
                );
            }

            // Technical skills
            const techCheckbox = jobEntry.querySelector('.technical-checkbox');
            const techDetails = jobEntry.querySelector('.technical-details');
            const techAddBtn = jobEntry.querySelector('.add-technical-skill');
            const techInput = jobEntry.querySelector('.technical-skill-input');
            const techHiddenInput = jobEntry.querySelector('.technical-skills-input');
            const techTagsContainer = jobEntry.querySelector('.technical-tags-container');

            if (techCheckbox && techDetails) {
                setupCheckboxToggle(techCheckbox, techDetails);

                if (techAddBtn && techInput && techHiddenInput && techTagsContainer) {
                    createSkillTagSystem(
                        techDetails,
                        techAddBtn,
                        techInput,
                        techHiddenInput,
                        techTagsContainer
                    );
                }
            }

            // Soft skills
            const softCheckbox = jobEntry.querySelector('.soft-checkbox');
            const softDetails = jobEntry.querySelector('.soft-details');
            const softAddBtn = jobEntry.querySelector('.add-soft-skill');
            const softInput = jobEntry.querySelector('.soft-skill-input');
            const softHiddenInput = jobEntry.querySelector('.soft-skills-input');
            const softTagsContainer = jobEntry.querySelector('.soft-tags-container');

            if (softCheckbox && softDetails) {
                setupCheckboxToggle(softCheckbox, softDetails);

                if (softAddBtn && softInput && softHiddenInput && softTagsContainer) {
                    createSkillTagSystem(
                        softDetails,
                        softAddBtn,
                        softInput,
                        softHiddenInput,
                        softTagsContainer
                    );
                }
            }
        }

        // Initialize existing job entries
        document.querySelectorAll('.job-entry').forEach(initializeJobEntry);

        // Add job title functionality
        const addJobTitleBtn = document.getElementById('add-job-title-btn');
        const jobTitlesContainer = document.getElementById('jobTitlesContainer');

        addJobTitleBtn.addEventListener('click', () => {
            const jobCount = jobTitlesContainer.querySelectorAll('.job-entry').length;
            const entryIndex = jobCount;

            const newJobEntry = document.createElement('div');
            newJobEntry.className = 'bg-white rounded-lg p-4 border border-gray-200 job-entry relative';

            newJobEntry.innerHTML = `
            <!-- Remove Button -->
            <button type="button" 
                    class="remove-job-btn absolute top-4 right-4 text-red-500 hover:text-red-700 font-medium text-sm flex items-center gap-1 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Remove
            </button>

            <!-- Job Entry Number -->
            <div class="mb-4 pb-2 border-b border-gray-200">
                <h4 class="text-sm font-bold text-teal-700">Job Entry #${jobCount + 1}</h4>
            </div>

            <!-- 8. Job Title -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">
                    8. Job Title: <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="job_title[]"
                    placeholder="e.g. Senior Java Developer"
                    required
                    class="job-title-input w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"
                />
            </div>

            <!-- 9. Standard Job Classifications -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">
                    9. Standard Job Classifications / Families: <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <button type="button" class="job-classification-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                        <span class="job-classification-text text-gray-400">Select job classification</span>
                        <svg class="job-classification-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div class="job-classification-menu dropdown-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                        
                        <div data-value="Accounting, Finance & Banking" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • Accounting, Finance & Banking
                        </div>
                        <div data-value="Administrative, HR & Office Support" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • Administrative, HR & Office Support
                        </div>
                        <div data-value="Agriculture, Forestry & Agribusiness" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • Agriculture, Forestry & Agribusiness
                        </div>
                        <div data-value="Construction, Engineering & Architecture" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • Construction, Engineering & Architecture
                        </div>
                        <div data-value="Customer Service & BPO (Contact Center)" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • Customer Service & BPO (Contact Center)
                        </div>
                        <div data-value="Education, Training & Academe" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • Education, Training & Academe
                        </div>
                        <div data-value="Healthcare, Medical & Allied Services" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • Healthcare, Medical & Allied Services
                        </div>
                        <div data-value="IT, Software, Data & Digital Creative" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • IT, Software, Data & Digital Creative
                        </div>
                        <div data-value="Legal, Compliance & Public Service" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • Legal, Compliance & Public Service
                        </div>
                        <div data-value="Logistics, Transport & Supply Chain" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • Logistics, Transport & Supply Chain
                        </div>
                        <div data-value="Manufacturing, Production & Technical" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • Manufacturing, Production & Technical
                        </div>
                        <div data-value="Sales, Marketing, Retail & E-Commerce" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • Sales, Marketing, Retail & E-Commerce
                        </div>
                        <div data-value="Science, Research & Laboratory" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • Science, Research & Laboratory
                        </div>
                        <div data-value="Skilled Trades, Maintenance & General Services" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • Skilled Trades, Maintenance & General Services
                        </div>
                        <div data-value="Tourism, Hospitality & Food Service" 
                            class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            • Tourism, Hospitality & Food Service
                        </div>
                    </div>
                    
                    <input type="hidden" class="job-classification-input" name="job_classification[]" required>
                </div>
            </div>

            <!-- 10. Duration -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">
                    10. Duration that the Vacancy is Open: <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <button type="button" class="duration-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                        <span class="duration-text text-gray-400">Select duration</span>
                        <svg class="duration-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div class="duration-menu dropdown-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden hidden">
                        
                        <div data-value="Less than 30 Days" 
                            class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            Less than 30 Days
                        </div>
                        <div data-value="30-60 Days" 
                            class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            30-60 Days
                        </div>
                        <div data-value="60-90 Days" 
                            class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            60-90 Days
                        </div>
                        <div data-value="90+ Days" 
                            class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                            90+ Days
                        </div>
                    </div>
                    
                    <input type="hidden" class="duration-input" name="vacancy_duration[]" required>
                </div>
            </div>

            <!-- 11. Reasons For Difficulty -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">
                    11. Reasons For Difficulty (Role-Level) <span class="italic text-gray-500">(Check all that apply)</span>
                </label>
                <div class="difficulty-reasons space-y-3">
                    
                    <!-- Technical Skills -->
                    <label class="technical-skills-label flex items-start p-3 border rounded-lg cursor-pointer transition-all border-gray-200 hover:bg-gray-50">
                        <input type="checkbox" 
                            name="difficulty_reasons_${entryIndex}[]" 
                            value="Technical / Hard Skills Missing"
                            class="technical-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                        <div class="ml-3 flex-1">
                            <div class="font-medium text-gray-700">Technical / Hard Skills Missing</div>
                            <div class="text-xs text-gray-500 mt-1">Applicants do not have the required tools, software, or technical knowledge</div>
                            
                            <!-- Technical Skills Input -->
                            <div class="technical-details mt-3 hidden">
                                <label class="block text-gray-600 text-xs font-medium mb-1">
                                    What specific technical tools, software, or machinery knowledge is missing?
                                </label>
                                
                                <div class="technical-tags-container flex flex-wrap gap-2 mb-2"></div>
                                
                                <div class="flex gap-2">
                                    <input type="text" 
                                        class="technical-skill-input flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                        placeholder="Type a skill and press Enter..."/>
                                    <button type="button" 
                                            class="add-technical-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Add</button>
                                </div>
                                <input type="hidden" class="technical-skills-input" name="technical_skills_missing[]">
                            </div>
                        </div>
                    </label>

                    <!-- Soft Skills -->
                    <label class="soft-skills-label flex items-start p-3 border rounded-lg cursor-pointer transition-all border-gray-200 hover:bg-gray-50">
                        <input type="checkbox" 
                            name="difficulty_reasons_${entryIndex}[]" 
                            value="Soft / Employability Skills Missing"
                            class="soft-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                        <div class="ml-3 flex-1">
                            <div class="font-medium text-gray-700">Soft / Employability Skills Missing</div>
                            <div class="text-xs text-gray-500 mt-1">Applicants cannot communicate effectively, work in teams, or demonstrate professionalism</div>
                            
                            <!-- Soft Skills Input -->
                            <div class="soft-details mt-3 hidden">
                                <label class="block text-gray-600 text-xs font-medium mb-1">
                                    What attitude or behavioral traits cause you to reject applicants?
                                </label>
                                
                                <div class="soft-tags-container flex flex-wrap gap-2 mb-2"></div>
                                
                                <div class="flex gap-2">
                                    <input type="text" 
                                        class="soft-skill-input flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                        placeholder="Type a trait and press Enter..."/>
                                    <button type="button" 
                                            class="add-soft-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Add</button>
                                </div>
                                <input type="hidden" class="soft-skills-input" name="soft_skills_missing[]">
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 12. Impact Level -->
            <div class="mb-4 mt-6 pt-4 border-t border-gray-200">
                <label class="block text-gray-700 text-sm font-medium mb-3">
                    12. How much does the difficulty finding qualified applicants for this role impact your business operations? 
                    <span class="text-red-500">*</span>
                </label>
                <div class="impact-level space-y-3">
                    <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                        <input type="radio" name="impact_level_${entryIndex}" value="High" required
                            class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                        <div class="ml-3 flex-1">
                            <div class="font-medium text-gray-800">High Impact</div>
                            <div class="text-xs text-gray-500 mt-1">Operations are significantly disrupted</div>
                        </div>
                    </label>
                    
                    <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                        <input type="radio" name="impact_level_${entryIndex}" value="Medium" required
                            class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                        <div class="ml-3 flex-1">
                            <div class="font-medium text-gray-800">Medium Impact</div>
                            <div class="text-xs text-gray-500 mt-1">Operations continue with adjustments</div>
                        </div>
                    </label>
                    
                    <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                        <input type="radio" name="impact_level_${entryIndex}" value="Low" required
                            class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                        <div class="ml-3 flex-1">
                            <div class="font-medium text-gray-800">Low Impact</div>
                            <div class="text-xs text-gray-500 mt-1">Minimal operational impact</div>
                        </div>
                    </label>
                </div>
            </div>
        `;

            jobTitlesContainer.appendChild(newJobEntry);
            initializeJobEntry(newJobEntry);

            // Add remove functionality
            const removeBtn = newJobEntry.querySelector('.remove-job-btn');
            removeBtn.addEventListener('click', () => {
                newJobEntry.remove();
            });

            // Scroll to new entry
            newJobEntry.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });

        // Other rejection reasons toggle
        const otherRejectionCheckbox = document.querySelector('.other-rejection-checkbox');
        const otherRejectionInput = document.querySelector('.other-rejection-input');
        if (otherRejectionCheckbox && otherRejectionInput) {
            otherRejectionCheckbox.addEventListener('change', () => {
                if (otherRejectionCheckbox.checked) {
                    otherRejectionInput.classList.remove('hidden');
                } else {
                    otherRejectionInput.classList.add('hidden');
                }
            });
        }

        // Other coordination frequency toggle
        const otherCoordinationRadio = document.querySelector('.other-coordination-radio');
        const otherCoordinationInput = document.querySelector('.other-coordination-input');
        if (otherCoordinationRadio && otherCoordinationInput) {
            otherCoordinationRadio.addEventListener('change', () => {
                if (otherCoordinationRadio.checked) {
                    otherCoordinationInput.classList.remove('hidden');
                } else {
                    otherCoordinationInput.classList.add('hidden');
                }
            });
        }

        // Sector tabs functionality
        document.querySelectorAll('.sector-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active state from all tabs
                document.querySelectorAll('.sector-tab').forEach(t => {
                    t.classList.remove('bg-purple-600', 'text-white');
                    t.classList.add('border', 'text-gray-500', 'hover:bg-gray-50');
                });

                // Add active state to clicked tab
                tab.classList.add('bg-purple-600', 'text-white');
                tab.classList.remove('border', 'text-gray-500', 'hover:bg-gray-50');

                // Here you would typically filter the skill gaps based on the selected sector
                // For now, we'll just log the selection
                console.log('Selected sector:', tab.textContent);
            });
        });

        // Export analysis button
        document.querySelector('.export-analysis-btn').addEventListener('click', () => {
            alert('Export functionality would be implemented here.');
        });
    </script>
    <script>
        function toggleRoleDetails(submissionId, roleIndex) {
            const detailsDiv = document.getElementById('role-details-' + submissionId + '-' + roleIndex);

            if (!detailsDiv) {
                return;
            }

            const card = detailsDiv.closest('.role-card');
            const icon = card.querySelector('.expand-icon');

            if (detailsDiv.classList.contains('hidden')) {
                // Close all other details
                document.querySelectorAll('.role-details').forEach(div => {
                    div.classList.add('hidden');
                    const parentCard = div.closest('.role-card');
                    if (parentCard) {
                        const parentIcon = parentCard.querySelector('.expand-icon');
                        if (parentIcon) {
                            parentIcon.classList.remove('rotate-180');
                        }
                    }
                });

                // Open this one
                detailsDiv.classList.remove('hidden');
                if (icon) {
                    icon.classList.add('rotate-180');
                }
            } else {
                // Close this one
                detailsDiv.classList.add('hidden');
                if (icon) {
                    icon.classList.remove('rotate-180');
                }
            }
        }
    </script>

    <script>
        function filterSkills(sector) {
            // Update active tab
            document.querySelectorAll('.sector-tab').forEach(tab => {
                if (tab.getAttribute('data-sector') === sector) {
                    tab.classList.add('bg-purple-600', 'text-white');
                    tab.classList.remove('border', 'text-gray-500', 'hover:bg-gray-50');
                } else {
                    tab.classList.remove('bg-purple-600', 'text-white');
                    tab.classList.add('border', 'text-gray-500', 'hover:bg-gray-50');
                }
            });

            // Filter skill tags
            document.querySelectorAll('.skill-tag').forEach(tag => {
                const tagSector = tag.getAttribute('data-sector');
                if (sector === 'All' || tagSector === sector) {
                    tag.style.display = 'flex';
                } else {
                    tag.style.display = 'none';
                }
            });
            let autocompleteData = {
                jobTitles: [],
                technicalSkills: [],
                softSkills: []
            };
        }
    </script>
    <script>
        // Comprehensive Autocomplete System for Job Titles and Skills
        // Add this script to your blade file or separate JS file

        // Store autocomplete data
        let autocompleteData = {
            jobTitles: [],
            technicalSkills: [],
            softSkills: []
        };

        // Fetch all autocomplete data when page loads
        async function fetchAutocompleteData() {
            try {
                const response = await fetch('/api/autocomplete-data');
                const data = await response.json();

                if (data.success) {
                    autocompleteData.jobTitles = data.job_titles || [];
                    autocompleteData.technicalSkills = data.technical_skills || [];
                    autocompleteData.softSkills = data.soft_skills || [];

                    console.log('✅ Autocomplete data loaded from database:', {
                        jobTitles: autocompleteData.jobTitles.length,
                        technicalSkills: autocompleteData.technicalSkills.length,
                        softSkills: autocompleteData.softSkills.length
                    });
                }
            } catch (error) {
                console.error('❌ Failed to fetch autocomplete data:', error);
                // Fallback to empty arrays
                autocompleteData.jobTitles = [];
                autocompleteData.technicalSkills = [];
                autocompleteData.softSkills = [];
            }
        }

        // Generic autocomplete function
        function createAutocomplete(inputElement, dataSource, onSelect) {
            if (inputElement.hasAttribute('data-autocomplete-initialized')) return;
            inputElement.setAttribute('data-autocomplete-initialized', 'true');

            // Create suggestion dropdown
            const suggestionsDiv = document.createElement('div');
            suggestionsDiv.className =
                'autocomplete-suggestions absolute w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-60 overflow-y-auto hidden';
            suggestionsDiv.style.zIndex = '9999';
            suggestionsDiv.style.position = 'absolute';
            suggestionsDiv.style.top = '100%';
            suggestionsDiv.style.left = '0';
            suggestionsDiv.style.right = '0';
            // Make parent relative if not already
            if (getComputedStyle(inputElement.parentElement).position === 'static') {
                inputElement.parentElement.style.position = 'relative';
            }
            inputElement.parentElement.appendChild(suggestionsDiv);

            // Listen for input
            inputElement.addEventListener('input', function(e) {
                const searchTerm = e.target.value.trim().toLowerCase();

                if (searchTerm.length < 2) {
                    suggestionsDiv.innerHTML = '';
                    suggestionsDiv.classList.add('hidden');
                    return;
                }

                // Filter matching items
                const matches = dataSource.filter(item =>
                    item.toLowerCase().includes(searchTerm)
                );

                if (matches.length === 0) {
                    suggestionsDiv.innerHTML =
                        '<div class="px-4 py-3 text-sm text-gray-500 italic">No matching suggestions found</div>';
                    suggestionsDiv.classList.remove('hidden');
                    return;
                }

                // Display suggestions (limit to 10)
                suggestionsDiv.innerHTML = '';
                matches.slice(0, 10).forEach(item => {
                    const suggestionItem = document.createElement('div');
                    suggestionItem.className =
                        'px-4 py-2.5 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 border-b border-gray-100 last:border-b-0 transition';

                    // Highlight matching text
                    const regex = new RegExp(`(${searchTerm})`, 'gi');
                    const highlightedItem = item.replace(regex,
                        '<span class="font-semibold text-teal-600">$1</span>');
                    suggestionItem.innerHTML = highlightedItem;

                    // Click to select
                    suggestionItem.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation(); // Prevent click from bubbling to elements below

                        if (onSelect) {
                            onSelect(item, inputElement);
                        } else {
                            inputElement.value = item;
                        }
                        suggestionsDiv.innerHTML = '';
                        suggestionsDiv.classList.add('hidden');
                    });

                    suggestionsDiv.appendChild(suggestionItem);
                });

                suggestionsDiv.classList.remove('hidden');
            });

            // Close suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!inputElement.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                    suggestionsDiv.classList.add('hidden');
                }
            });

            // Keyboard navigation
            inputElement.addEventListener('keydown', function(e) {
                const suggestions = suggestionsDiv.querySelectorAll('div.px-4');
                if (suggestions.length === 0) return;

                let currentIndex = Array.from(suggestions).findIndex(s => s.classList.contains('bg-teal-100'));

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    suggestions[currentIndex]?.classList.remove('bg-teal-100', 'bg-teal-50');
                    currentIndex = currentIndex < suggestions.length - 1 ? currentIndex + 1 : 0;
                    suggestions[currentIndex].classList.add('bg-teal-100');
                    suggestions[currentIndex].scrollIntoView({
                        block: 'nearest'
                    });
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    suggestions[currentIndex]?.classList.remove('bg-teal-100', 'bg-teal-50');
                    currentIndex = currentIndex > 0 ? currentIndex - 1 : suggestions.length - 1;
                    suggestions[currentIndex].classList.add('bg-teal-100');
                    suggestions[currentIndex].scrollIntoView({
                        block: 'nearest'
                    });
                } else if (e.key === 'Enter' && currentIndex >= 0) {
                    e.preventDefault();
                    suggestions[currentIndex].click();
                } else if (e.key === 'Escape') {
                    suggestionsDiv.classList.add('hidden');
                }
            });
        }

        // Initialize Job Title Autocomplete
        function initializeJobTitleAutocomplete() {
            document.querySelectorAll('.job-title-input').forEach(input => {
                createAutocomplete(input, autocompleteData.jobTitles);
            });
        }

        // Initialize Technical Skills Autocomplete
        function initializeTechnicalSkillsAutocomplete() {
            document.querySelectorAll('.technical-skill-input').forEach(input => {
                createAutocomplete(input, autocompleteData.technicalSkills, function(selectedSkill, inputElement) {
                    // When a skill is selected, add it as a tag
                    inputElement.value = selectedSkill;

                    // Trigger the add button click or Enter key
                    const addButton = inputElement.parentElement.querySelector('.add-technical-skill');
                    if (addButton) {
                        addButton.click();
                    }

                    // Clear input after selection
                    setTimeout(() => {
                        inputElement.value = '';
                        inputElement.focus();
                    }, 100);
                });
            });
        }

        // Initialize Soft Skills Autocomplete
        function initializeSoftSkillsAutocomplete() {
            document.querySelectorAll('.soft-skill-input').forEach(input => {
                createAutocomplete(input, autocompleteData.softSkills, function(selectedSkill, inputElement) {
                    // When a skill is selected, add it as a tag
                    inputElement.value = selectedSkill;

                    // Trigger the add button click
                    const addButton = inputElement.parentElement.querySelector('.add-soft-skill');
                    if (addButton) {
                        addButton.click();
                    }

                    // Clear input after selection
                    setTimeout(() => {
                        inputElement.value = '';
                        inputElement.focus();
                    }, 100);
                });
            });
        }

        // Initialize all autocompletes
        function initializeAllAutocompletes() {
            initializeJobTitleAutocomplete();
            initializeTechnicalSkillsAutocomplete();
            initializeSoftSkillsAutocomplete();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetchAutocompleteData().then(() => {
                initializeAllAutocompletes();
            });

            // Re-initialize when new fields are added (for dynamic job entries)
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        setTimeout(() => {
                            initializeAllAutocompletes();
                        }, 100);
                    }
                });
            });

            const container = document.getElementById('jobTitlesContainer');
            if (container) {
                observer.observe(container, {
                    childList: true,
                    subtree: true
                });
            }
        });

        // ===== CSV EXPORT FUNCTIONALITY =====
        document.addEventListener('DOMContentLoaded', function() {
            const exportBtn = document.getElementById('exportAnalysisBtn');

            if (exportBtn) {
                exportBtn.addEventListener('click', function() {
                    exportDashboardToCSV();
                });
            }
        });

        function exportDashboardToCSV() {
            const csvData = [];
            const timestamp = new Date().toLocaleString();

            // Header
            csvData.push(['Davao Employment Dashboard Analysis']);
            csvData.push(['Generated on', timestamp]);
            csvData.push(['']); // Empty row

            // Section 1: High-Volume Job Titles
            csvData.push(['HIGH-VOLUME JOB TITLES']);
            csvData.push(['Rank', 'Job Title', 'Count']);

            // Get data from the chart
            if (window.jobsChart && window.jobsChart.data) {
                const labels = window.jobsChart.data.labels;
                const data = window.jobsChart.data.datasets[0].data;

                labels.forEach((label, index) => {
                    csvData.push([index + 1, label, data[index]]);
                });
            }

            csvData.push(['']); // Empty row

            // Section 2: Hard-to-Fill Roles
            csvData.push(['HARD-TO-FILL ROLES']);
            csvData.push(['Job Title', 'Classification', 'Vacancy Duration', 'Difficulty Reasons', 'Technical Skills',
                'Soft Skills'
            ]);

            const roleCards = document.querySelectorAll('.role-card');
            roleCards.forEach((card) => {
                const title = card.querySelector('.font-bold')?.textContent?.trim() || '';
                const duration = card.querySelector('.text-xs.text-gray-400')?.textContent?.trim() || '';

                // Get details from the expandable section
                const detailsDiv = card.querySelector('.role-details');
                let classification = '';
                let reasons = '';
                let techSkills = '';
                let softSkills = '';

                if (detailsDiv) {
                    // Classification
                    const classificationElements = detailsDiv.querySelectorAll('div > p.text-slate-800');
                    if (classificationElements.length > 0) {
                        classification = classificationElements[0].textContent.trim();
                    }

                    // Difficulty Reasons
                    const reasonsList = detailsDiv.querySelector('ul.list-disc');
                    if (reasonsList) {
                        const reasonItems = reasonsList.querySelectorAll('li');
                        reasons = Array.from(reasonItems).map(li => li.textContent.trim()).join('; ');
                    }

                    // Technical Skills
                    const techSkillsContainer = Array.from(detailsDiv.querySelectorAll(
                            'span.font-medium.text-slate-600'))
                        .find(span => span.textContent.includes('Technical Skills'));
                    if (techSkillsContainer) {
                        const skillTags = techSkillsContainer.parentElement.querySelectorAll('span.bg-blue-100');
                        techSkills = Array.from(skillTags).map(tag => tag.textContent.trim()).join('; ');
                    }

                    // Soft Skills
                    const softSkillsContainer = Array.from(detailsDiv.querySelectorAll(
                            'span.font-medium.text-slate-600'))
                        .find(span => span.textContent.includes('Soft Skills'));
                    if (softSkillsContainer) {
                        const skillTags = softSkillsContainer.parentElement.querySelectorAll('span.bg-purple-100');
                        softSkills = Array.from(skillTags).map(tag => tag.textContent.trim()).join('; ');
                    }
                }

                csvData.push([
                    escapeCSV(title),
                    escapeCSV(classification),
                    escapeCSV(duration),
                    escapeCSV(reasons),
                    escapeCSV(techSkills),
                    escapeCSV(softSkills)
                ]);
            });

            csvData.push(['']); // Empty row

            // Section 3: Critical Skill Gaps
            csvData.push(['CRITICAL SKILL GAPS']);
            csvData.push(['Rank', 'Skill', 'Frequency']);

            if (window.skillGapsChart && window.skillGapsChart.data) {
                const labels = window.skillGapsChart.data.labels;
                const data = window.skillGapsChart.data.datasets[0].data;

                labels.forEach((label, index) => {
                    csvData.push([index + 1, label, data[index]]);
                });
            }

            csvData.push(['']); // Empty row

            // Section 4: Employment Trends
            csvData.push(['EMPLOYMENT TRENDS (Last 6 Months)']);
            csvData.push(['Month', 'Job Postings']);

            if (window.trendsChart && window.trendsChart.data) {
                const labels = window.trendsChart.data.labels;
                const data = window.trendsChart.data.datasets[0].data;

                labels.forEach((label, index) => {
                    csvData.push([label, data[index]]);
                });
            }

            // Convert to CSV string
            const csvString = csvData.map(row => row.join(',')).join('\n');

            // Create and download file
            const blob = new Blob([csvString], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            const filename = `davao-employment-analysis-${new Date().toISOString().split('T')[0]}.csv`;

            link.setAttribute('href', url);
            link.setAttribute('download', filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Show success message
            alert('Analysis exported successfully as ' + filename);
        }

        // Helper function to escape CSV values
        function escapeCSV(value) {
            if (value === null || value === undefined) {
                return '';
            }

            const stringValue = String(value);

            // If value contains comma, quote, or newline, wrap in quotes and escape quotes
            if (stringValue.includes(',') || stringValue.includes('"') || stringValue.includes('\n')) {
                return '"' + stringValue.replace(/"/g, '""') + '"';
            }

            return stringValue;
        }
    </script>
    <script>
        let matrixResultsData = @json($matrix_results);

        function exportLMIMatrixToCSV() {
            const csvData = [];
            const timestamp = new Date().toLocaleString();

            // Header
            csvData.push(['LMI Granularity Matrix - Competency Gap Analysis']);
            csvData.push(['Generated on', timestamp]);
            csvData.push(['']); // Empty row

            // Column Headers
            csvData.push(['Job Title / Role', 'Sector', 'Gap Impact', 'Missing Technical Skills', 'Missing Soft Skills']);

            // Process all matrix results
            if (matrixResultsData && matrixResultsData.length > 0) {
                matrixResultsData.forEach((result) => {
                    const role = result.role || '';
                    const sector = result.sector || '';
                    const impact = result.impact || 'Medium';

                    // Process Technical Skills
                    let technicalSkills = '';
                    if (result.hard_skills && Array.isArray(result.hard_skills) && result.hard_skills.length > 0) {
                        technicalSkills = result.hard_skills.map(skill => {
                            return typeof skill === 'object' ? (skill.name || '') : skill;
                        }).filter(s => s).join('; ');
                    }

                    // Process Soft Skills
                    let softSkills = '';
                    if (result.soft_skills && Array.isArray(result.soft_skills) && result.soft_skills.length > 0) {
                        softSkills = result.soft_skills.map(skill => {
                            return typeof skill === 'object' ? (skill.name || '') : skill;
                        }).filter(s => s).join('; ');
                    }

                    csvData.push([
                        escapeCSVValue(role),
                        escapeCSVValue(sector),
                        escapeCSVValue(impact),
                        escapeCSVValue(technicalSkills),
                        escapeCSVValue(softSkills)
                    ]);
                });

                // Add summary statistics
                csvData.push(['']); // Empty row
                csvData.push(['SUMMARY STATISTICS']);
                csvData.push(['Total Roles Analyzed', matrixResultsData.length]);

                // Count high/medium/low impact
                const highImpact = matrixResultsData.filter(r => r.impact === 'High').length;
                const mediumImpact = matrixResultsData.filter(r => r.impact === 'Medium' || !r.impact).length;
                const lowImpact = matrixResultsData.filter(r => r.impact === 'Low').length;

                csvData.push(['High Impact Gaps', highImpact]);
                csvData.push(['Medium Impact Gaps', mediumImpact]);
                csvData.push(['Low Impact Gaps', lowImpact]);
            } else {
                csvData.push(['No data available']);
            }

            // Convert to CSV string
            const csvString = csvData.map(row => row.join(',')).join('\n');

            // Create and download file
            const blob = new Blob([csvString], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            const filename = `lmi-competency-gap-analysis-${new Date().toISOString().split('T')[0]}.csv`;

            link.setAttribute('href', url);
            link.setAttribute('download', filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);


        }

        // Helper function to escape CSV values
        function escapeCSVValue(value) {
            if (value === null || value === undefined) {
                return '';
            }

            const stringValue = String(value);

            // If value contains comma, quote, or newline, wrap in quotes and escape quotes
            if (stringValue.includes(',') || stringValue.includes('"') || stringValue.includes('\n')) {
                return '"' + stringValue.replace(/"/g, '""') + '"';
            }

            return stringValue;
        }

        // Initialize the export button when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const exportBtn = document.getElementById('exportLMIMatrixBtn');

            if (exportBtn) {
                exportBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    exportLMIMatrixToCSV();
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const steps = document.querySelectorAll('.lmi-step'); // the 4 <div> wrappers
            const circles = document.querySelectorAll('.step-circle');
            const lines = document.querySelectorAll('.step-line');
            let current = 0;

            // ─── INIT: hide all except first ────────────────────────
            function showStep(n) {
                steps.forEach((s, i) => s.style.display = (i === n) ? 'block' : 'none');
                current = n;
                updateIndicator();
                updateButtons();
                // scroll modal back to top
                const scrollable = document.querySelector('.overflow-y-auto');
                if (scrollable) scrollable.scrollTop = 0;
            }

            // ─── INDICATOR ──────────────────────────────────────────
            function updateIndicator() {
                circles.forEach((c, i) => {
                    c.classList.remove('bg-white', 'text-teal-700', 'bg-teal-500', 'text-white');
                    if (i < current) {
                        c.classList.add('bg-white', 'text-teal-700');
                        c.innerHTML =
                            '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
                    } else if (i === current) {
                        c.classList.add('bg-white', 'text-teal-700');
                        c.innerHTML = (i + 1).toString();
                    } else {
                        c.classList.add('bg-teal-500', 'text-white');
                        c.innerHTML = (i + 1).toString();
                    }
                });
                lines.forEach((l, i) => {
                    l.classList.toggle('bg-white', i < current);
                    l.classList.toggle('bg-teal-500', i >= current);
                });
            }

            // ─── BUTTONS ────────────────────────────────────────────
            function updateButtons() {
                steps.forEach((step, i) => {
                    const prev = step.querySelector('.btn-prev');
                    const next = step.querySelector('.btn-next');
                    const submit = step.querySelector('.btn-submit-lmi');

                    if (prev) prev.style.display = (i === 0) ? 'none' : 'inline-flex';
                    if (next) next.style.display = (i === steps.length - 1) ? 'none' : 'inline-flex';
                    if (submit) submit.style.display = (i === steps.length - 1) ? 'inline-flex' : 'none';
                });
            }

            // ─── VALIDATION ─────────────────────────────────────────
            function validateStep(idx) {
                const step = steps[idx];
                let valid = true;

                // -- text / email / tel --
                step.querySelectorAll(
                        'input[type="text"][required], input[type="email"][required], input[type="tel"][required]')
                    .forEach(input => {
                        if (!input.value.trim()) {
                            input.classList.add('border-red-500');
                            valid = false;
                        } else {
                            input.classList.remove('border-red-500');
                        }
                    });

                // -- hidden inputs (dropdowns: industrySelector, companySize, job_classification, vacancy_duration) --
                step.querySelectorAll('input[type="hidden"][required]').forEach(input => {
                    const wrapper = input.closest('.relative');
                    const btn = wrapper ? wrapper.querySelector('button[type="button"]') : null;
                    if (!input.value) {
                        valid = false;
                        if (btn) btn.classList.add('border-red-500');
                    } else {
                        if (btn) btn.classList.remove('border-red-500');
                    }
                });

                // -- radio groups --
                const radioNames = new Set();
                step.querySelectorAll('input[type="radio"][required]').forEach(r => radioNames.add(r.name));
                radioNames.forEach(name => {
                    const checked = step.querySelector(`input[type="radio"][name="${name}"]:checked`);
                    const radios = step.querySelectorAll(`input[type="radio"][name="${name}"]`);
                    if (!checked) {
                        valid = false;
                        radios.forEach(r => {
                            const lbl = r.closest('label');
                            if (lbl) lbl.classList.add('border-red-500');
                        });
                    } else {
                        radios.forEach(r => {
                            const lbl = r.closest('label');
                            if (lbl) lbl.classList.remove('border-red-500');
                        });
                    }
                });

                // -- Step 3 (idx 2): at least one rejection_reasons checkbox --
                if (idx === 2) {
                    const checked = step.querySelectorAll('input[name="rejection_reasons[]"]:checked');
                    if (checked.length === 0) {
                        valid = false;
                        step.querySelectorAll('input[name="rejection_reasons[]"]').forEach(cb => {
                            const p = cb.closest('label') || cb.closest('.other-rejection-option');
                            if (p) p.classList.add('border-red-500');
                        });
                    } else {
                        step.querySelectorAll('input[name="rejection_reasons[]"]').forEach(cb => {
                            const p = cb.closest('label') || cb.closest('.other-rejection-option');
                            if (p) p.classList.remove('border-red-500');
                        });
                    }
                }

                // -- Step 4 (idx 3): consent + at least one lmi_features --
                if (idx === 3) {
                    const consent = step.querySelector('input[name="consent"]');
                    if (consent && !consent.checked) {
                        valid = false;
                        const lbl = consent.closest('label');
                        if (lbl) lbl.classList.add('border-red-500');
                    } else if (consent) {
                        const lbl = consent.closest('label');
                        if (lbl) lbl.classList.remove('border-red-500');
                    }

                    const lmiChecked = step.querySelectorAll('input[name="lmi_features[]"]:checked');
                    if (lmiChecked.length === 0) {
                        valid = false;
                    }
                }

                // scroll to first red field
                if (!valid) {
                    const bad = step.querySelector('.border-red-500');
                    if (bad) bad.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
                return valid;
            }

            // ─── BIND NEXT / PREV clicks ────────────────────────────
            steps.forEach((step, i) => {
                const next = step.querySelector('.btn-next');
                const prev = step.querySelector('.btn-prev');

                if (next) {
                    next.addEventListener('click', function() {
                        if (validateStep(i)) showStep(i + 1);
                    });
                }
                if (prev) {
                    prev.addEventListener('click', function() {
                        if (i > 0) showStep(i - 1);
                    });
                }
            });

            // ─── INIT ───────────────────────────────────────────────
            showStep(0);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the data from Laravel
            const comparisonData = @json($comparison_data ?? []);

            if (comparisonData.length === 0) {
                console.log('No comparison data available');
                return;
            }

            // Prepare data for Chart.js
            const labels = comparisonData.map(item => item.title);
            const currentYearData = comparisonData.map(item => item.current_count);
            const previousYearData = comparisonData.map(item => item.previous_count);

            const currentYear = comparisonData[0]?.current_year || {{ $selected_year }};
            const previousYear = comparisonData[0]?.previous_year || {{ $selected_year - 1 }};

            // Create the chart
            const ctx = document.getElementById('highVolumeHorizontalChart');

            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: previousYear.toString(),
                                data: previousYearData,
                                backgroundColor: 'rgba(34, 197, 94, 0.7)', // Green
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 1
                            },
                            {
                                label: currentYear.toString(),
                                data: currentYearData,
                                backgroundColor: 'rgba(59, 130, 246, 0.7)', // Blue
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        indexAxis: 'y', // Makes it horizontal
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.x +
                                            ' jobs';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    precision: 0
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
            }
        });
    </script>
</body>

</html>
