<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    

    <title>LMI</title>
    <style>
        [x-cloak] { display: none !important; }
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
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Scroll indicator bounce animation */
@keyframes bounce-custom {
    0%, 100% {
        transform: translateY(0);
        animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
    }
    50% {
        transform: translateY(-25%);
        animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
}

.scroll-indicator {
    animation: bounce-custom 1s infinite;
}
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
    <div x-data="{
        activeView: 'job-market-view',
        showReportModal: false,
        showLmiMatrix: false,
        mobileMenuOpen: false
    }">
        
        <!-- NAVBAR at top -->
        @include('partials.navbar')
        
        <!-- Hero Image Section -->
        <div class="relative w-full h-[500px] md:h-[700px] lg:h-[900px] overflow-hidden">
            <div class="absolute inset-0">
                <img src="{{ asset('images/navbar-bg.png') }}" alt="Job Market Background"
                    class="w-full h-full object-cover object-top">
                <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/40 to-slate-100"></div>
            </div>
            
            <!-- Hero Content -->
            <div class="relative z-10 h-full flex items-center justify-center px-4">
                <div class="text-center text-white">
                    <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4 drop-shadow-lg">
                        Davao Regional Labor Demand
                    </h1>
                    <p class="text-base md:text-xl lg:text-2xl text-slate-100 drop-shadow-md">
                        Regional Labor Market Intelligence & Trends
                    </p>
                </div>
            </div>
            
            <!-- Scroll Indicator -->
            <div class="absolute bottom-32 left-1/2 transform -translate-x-1/2 z-20 scroll-indicator">
                <a href="#job-market-section"
                   class="flex flex-col items-center cursor-pointer group"
                   @click.prevent="() => {
                       const element = document.getElementById('job-market-section');
                       if (element) {
                           element.scrollIntoView({ 
                               behavior: 'smooth', 
                               block: 'start' 
                           });
                       }
                   }">
                    <svg class="w-8 h-8 text-white group-hover:text-blue-300 transition-colors" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" 
                              stroke-linejoin="round" 
                              stroke-width="2"
                              d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                    <p class="text-white text-sm mt-2 font-medium group-hover:text-blue-300 transition-colors">
                        Scroll to explore
                    </p>
                </a>
            </div>
        </div>
        
        <!-- MAIN CONTENT with top margin to account for hero -->
        <div class="flex-1 flex flex-col overflow-y-auto mt-10 relative z-30">
            <div x-show="activeView === 'job-market-view'" x-transition>
                <div class="max-w-7xl mx-auto px-4 md:px-6 space-y-6" id="job-market-section">
                        
                        

                           
                           
                       
                        <div class="bg-slate-700 rounded-xl p-6 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-lg">
                            <div class="flex items-start gap-4">
                                <div class="p-2 bg-emerald-500/20 rounded-lg text-emerald-400">🤝</div>
                                <div>   
                                    <h2 class="text-lg font-bold">Help us map the future of Davao's workforce.</h2>
                                    <p class="text-sm text-slate-400 max-w-xl">Official data lags behind real-time market needs. Help us bridge the gap by identifying hard-to-fill roles and critical skill shortages.</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                            
                                <button id="show-lmi-matrix-btn" class="bg-emerald-500 border border-emerald-500 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-500/20 transition">
                                    Submit Labor Information
                                </button>
                            </div>
                        </div>

                        
                        <!-- High Volume Jobs Section - Original Design with Year Comparison -->
<!-- Two Column Layout: Chart Left, Hard-to-Fill Right -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- LEFT SIDE: High Volume Jobs Chart (Takes 2 columns) -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="flex justify-between items-center p-6 pb-4 border-b border-gray-100">
            <div>
                <h3 class="font-bold text-gray-800">Top 10 High-Volume Job Titles</h3>
                
                @if($selected_year && isset($selected_year))
                    <p class="text-xs text-gray-500 mt-1" id="chartSubtitle" style="{{ collect($comparison_data ?? [])->some(fn($d) => $d['previous_count'] > 0) ? '' : 'display:none' }}">
                        <span id="prevYearLabel" class="text-emerald-600 font-medium">{{ $selected_year - 1 }}</span> vs 
                        <span id="currentYearLabel" class="text-indigo-600 font-medium">{{ $selected_year }}</span>
                    </p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <!-- Year Selector -->
                @if(isset($available_years) && count($available_years) > 0)
                    <select 
                        id="yearSelector" 
                        class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        onchange="updateChart(this.value)"
                    >
                        @foreach($available_years as $year)
                            <option value="{{ $year }}" {{ $year == $selected_year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                @endif
                
                <!-- Expand Chart Button -->
                <button 
                    onclick="expandChart()" 
                    class="p-2 hover:bg-gray-100 rounded-lg transition"
                    title="Expand chart"
                >
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                </button>
                
                <!-- Info Icon -->
                <span class="text-gray-300 cursor-help" title="Job titles with highest demand">ⓘ</span>
            </div>
        </div>

        <!-- Chart Container -->
        <div class="p-6" id="chartContainer">
            <div style="height: 360px;">
                <canvas id="highVolumeHorizontalChart"></canvas>
            </div>
        </div>
            <div class="px-6 pb-4 pt-2 border-t border-gray-100">
                    <p class="text-xs text-gray-500 text-center italic">
                        Source: PhilJobNet
                    </p>
    </div>

    </div>

    <!-- RIGHT SIDE: Hard-to-Fill Roles (Takes 1 column) -->
     <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 pb-4">
    <div class="flex justify-between mb-3">
        <div>
            <h3 class="font-bold text-gray-800">Hard-to-Fill Roles</h3>
            <p class="text-xs text-gray-500 mt-1">Jobs that are consistently difficult to recruit for</p>
        </div>
        <span class="text-gray-300 cursor-help" title="Click to expand details">ⓘ</span>
    </div>
    
    {{-- Quarter Banner Inside Hard-to-Fill Section --}}
    @if(isset($quarter_info))
    <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded-md">
        <div class="flex items-center">
            <svg class="h-4 w-4 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <div>
                <p class="text-xs font-semibold text-blue-900">Last 90 Days</p>
                <p class="text-xs text-blue-700">{{ $quarter_info['display_text'] }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
        
        @if(isset($groupedRoles) && count($groupedRoles) > 0)
            <div class="max-h-96 overflow-y-auto px-6 pb-6">
                <div class="space-y-3">
                    @foreach($groupedRoles as $normalizedTitle => $roleGroup)
                        @foreach($roleGroup as $item)
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
                                            <p class="font-bold text-sm text-slate-800">{{ $role->formatted_job_title }}</p>
                                            <p class="text-xs text-gray-400 mt-1">Vacancy Duration: {{ $role->vacancy_duration }}</p>
                                        </div>
                                        
                                        <!-- Expand Icon -->
                                        <svg class="expand-icon w-4 h-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Expandable Details -->
                                <div class="role-details hidden" id="role-details-{{ $submission->id }}-{{ $index }}">
                                    <div class="border-t border-slate-200 bg-slate-50 p-4">
                                        <div class="space-y-3 text-sm">
                                            <!-- Classification -->
                                            <div>
                                                <span class="font-medium text-slate-600">Classification:</span>
                                                <p class="text-slate-800">{{ $role->job_classification }}</p>
                                            </div>

                                            <!-- Difficulty Reasons -->
                                            @php
                                                $reasons = $role->difficulty_reasons;
                                                if (is_string($reasons)) {
                                                    $reasons = json_decode($reasons, true) ?? [];
                                                }
                                                if (!is_array($reasons)) {
                                                    $reasons = [];
                                                }
                                            @endphp
                                            
                                            @if(count($reasons) > 0)
                                                <div>
                                                    <span class="font-medium text-slate-600">Difficulty Reasons:</span>
                                                    <ul class="list-disc list-inside mt-1 text-slate-700 text-xs">
                                                        @foreach($reasons as $reason)
                                                            @if(is_array($reason))
                                                                @foreach($reason as $item)
                                                                    @if(!empty($item))
                                                                        <li>{{ $item }}</li>
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
                                                    $techSkills = json_decode($techSkills, true) ?? [];
                                                }
                                                if (!is_array($techSkills)) {
                                                    $techSkills = [];
                                                }
                                            @endphp
                                            
                                            @if(count($techSkills) > 0)
                                                <div>
                                                    <span class="font-medium text-slate-600">Technical Skills Missing:</span>
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @foreach($techSkills as $skill)
                                                            @if(!empty($skill))
                                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">{{ strtoupper($skill) }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Soft Skills -->
                                            @php
                                                $softSkills = $role->soft_skills_missing;
                                                if (is_string($softSkills)) {
                                                    $softSkills = json_decode($softSkills, true) ?? [];
                                                }
                                                if (!is_array($softSkills)) {
                                                    $softSkills = [];
                                                }
                                            @endphp
                                            
                                            @if(count($softSkills) > 0)
                                                <div>
                                                    <span class="font-medium text-slate-600">Soft Skills Missing:</span>
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @foreach($softSkills as $skill)
                                                            @if(!empty($skill))
                                                                <span class="px-2 py-0.5 bg-pink-100 text-pink-700 rounded text-xs">{{ strtoupper($skill) }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Company Info -->
                                            <div class="pt-2 border-t">
                                                <p class="text-xs text-slate-500">
                                                    <strong>Sector:</strong> {{ $submission->industry_sector }}
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
                    @foreach($approvedSubmissions as $submission)
                        @foreach($submission->hardToFillRoles as $index => $role)
                            <div class="role-card border border-slate-200 rounded-lg overflow-hidden hover:border-blue-400 transition cursor-pointer"
                                 onclick="toggleRoleDetails({{ $submission->id }}, {{ $index }})">
                                
                                <div class="p-3 bg-white hover:bg-slate-50 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="font-bold text-sm text-slate-800">{{ $role->formatted_job_title }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $role->vacancy_duration }}</p>
                                        </div>
                                        
                                        <svg class="expand-icon w-4 h-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="role-details hidden" id="role-details-{{ $submission->id }}-{{ $index }}">
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
                    @foreach($hard_to_fill as $job)
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
        @endif
    </div>
</div>

<!-- Fullscreen Modal -->
<div id="chartModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" onclick="closeChart()">
    <div class="bg-white rounded-xl shadow-2xl w-11/12 h-5/6 p-6 relative" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">High-Volume Job Titles - Expanded View</h3>
            <button onclick="closeChart()" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div style="height: calc(100% - 60px);">
            <canvas id="highVolumeExpandedChart"></canvas>
        </div>
          <div class="absolute  left-0 right-0 text-center">
            <p class="text-xs text-gray-500 italic">
                Source: PhilJobNet
            </p>
        </div>
    </div>
</div>              
                        <!-- Critical Skill Gaps Per Sector -->
<div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
    <h3 class="font-bold text-lg mb-4">Critical Skill Gaps Per Sector</h3>
    
    <!-- Sector Filter Tabs -->
    <div class="mb-8 pb-5 border-b border-gray-100">
        <div class="flex items-center gap-2">
            <!-- Left Arrow -->
            <button id="filter-left"
                    class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <!-- Scrollable Filters -->
            <div id="sector-filter-scroll" class="flex gap-2 overflow-x-auto flex-1" style="scrollbar-width:none; -webkit-overflow-scrolling:touch;">
                <style>#sector-filter-scroll::-webkit-scrollbar { display: none; }</style>

                <button onclick="filterSkills('All')"
                        class="sector-tab flex-shrink-0 px-5 py-2 text-xs font-bold rounded-xl transition-all whitespace-nowrap bg-gray-900 text-white shadow-sm"
                        data-sector="All">
                    All Sectors
                </button>
                @foreach($sectors as $sector)
                    <button onclick="filterSkills('{{ addslashes($sector) }}')"
                            class="sector-tab flex-shrink-0 px-5 py-2 text-xs font-semibold rounded-xl border border-gray-200 text-gray-500 bg-white hover:border-gray-900 hover:text-gray-900 transition-all whitespace-nowrap"
                            data-sector="{{ $sector }}">
                        {{ $sector }}
                    </button>
                @endforeach
            </div>

            <!-- Right Arrow -->
            <button id="filter-right"
                    class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-12">
    
        <!-- Missing Technical Skills -->
        <div class="md:border-r border-gray-200 md:pr-6">
        <h4 class="text-xs font-bold text-gray-400 mb-4 uppercase bg-white pb-2">
            🔍 In demand Technical Skills 
        </h4>
        <div class="flex flex-wrap gap-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar" 
             id="tech-skills-container"
             style="overflow-y: scroll;">  <!-- Force scrollbar to always show for testing -->
            @foreach($tech_skills as $skill)
                    <div class="skill-tag tech-skill bg-blue-100 text-gray-800 font-semibold px-3 py-2 rounded-lg text-sm h-fit flex flex-col gap-0.5" 
                         data-sector="{{ $skill['sector'] }}">
                        <div class="flex items-center gap-1">
                            {{ $skill['name'] }}
                            @if(isset($skill['count']) && $skill['count'] > 1)
                                <span class="px-1.5 py-0.5 bg-blue-200 rounded-full text-[9px] font-bold">{{ $skill['count'] }}×</span>
                            @endif
                        </div>
                        <span class="text-[11px] opacity-60 font-normal">({{ $skill['sector'] }})</span>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Missing Soft Skills -->
    <div class="md:pl-6">
        <h4 class="text-xs font-bold text-gray-400 mb-4 uppercase bg-white pb-2">
            🚫 In demand Soft Skills 
        </h4>
        <div class="flex flex-wrap gap-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar" 
             id="soft-skills-container"
             style="overflow-y: scroll;">  <!-- Force scrollbar to always show for testing -->
            @foreach($soft_skills as $skill)
                    <div class="skill-tag soft-skill bg-red-100 text-gray-800 font-semibold px-3 py-2 rounded-lg text-sm h-fit flex flex-col gap-0.5" 
                         data-sector="{{ $skill['sector'] }}">
                        <div class="flex items-center gap-1">
                            {{ $skill['name'] }}
                            @if(isset($skill['count']) && $skill['count'] > 1)
                                <span class="px-1.5 py-0.5 bg-red-200 rounded-full text-[9px] font-bold">{{ $skill['count'] }}×</span>
                            @endif
                        </div>
                        <span class="text-[11px] opacity-60 font-normal">({{ $skill['sector'] }})</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
                 <!-- LMI Matrix - Improved Design with Laravel Blade -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" x-data="{ 
    openItem: null,
    currentPage: 1, 
    itemsPerPage: 10,
    get sortedData() {
        const impactOrder = { 'High': 1, 'Medium': 2, 'Low': 3 };
        return (window.matrixResultsData || []).sort((a, b) => {
            const impactA = impactOrder[a.impact] || 2; // Default to Medium if no impact
            const impactB = impactOrder[b.impact] || 2;
            return impactA - impactB;
        });
    },
    get totalPages() { 
        return Math.ceil((this.sortedData?.length || 0) / this.itemsPerPage); 
    },
    get paginatedData() {
        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        return this.sortedData.slice(start, end);
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
    <div class="p-6 border-b flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
    <h3 class="font-bold text-gray-900 flex items-center gap-3 text-lg">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 4v16M3 4h18a1 1 0 011 1v14a1 1 0 01-1 1H3a1 1 0 01-1-1V5a1 1 0 011-1z"/>
        </svg>
        <span>Critical Skills Requirements</span>
    </h3>
    <button id="exportLMIMatrixBtn" class="text-emerald-600 border border-emerald-200 bg-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-emerald-50 transition-all shadow-sm hover:shadow">
    Export Analysis
    </button>
</div>



@if(count($matrix_results) > 0)
    <div class="overflow-x-auto">
    <div class="min-w-[700px]">
    <!-- Sticky Table Header Row - Improved proportions with Salary Range -->
    <div class="sticky top-0 z-20 bg-gradient-to-r from-gray-900 to-gray-800 border-b border-gray-700 shadow-md">
        <div class="grid grid-cols-12 gap-4 px-8 py-4 items-center">
            <div class="col-span-2 flex items-center justify-center">
                <span class="text-s font-small font-bold text-white uppercase tracking-wider">Job Title / Role</span>
            </div>
            <div class="col-span-3 flex items-center justify-center">
                <span class="text-s font-small font-bold text-white uppercase tracking-wider">Sector</span>
            </div>
            <div class="col-span-3 flex items-center justify-center">
                <span class="text-s font-small font-bold text-white uppercase tracking-wider">Missing Skills / Competency</span>
            </div>
            <div class="col-span-2 flex items-center justify-center">
                <span class="text-s font-small font-bold text-white uppercase tracking-wider">Salary Range</span>
            </div>
            <div class="col-span-2 flex items-center justify-center">
                <span class="text-s font-small font-bold text-white uppercase tracking-wider text-center leading-tight">Job Gap Impact<br>to Industry</span>
            </div>
        </div>
    </div>

    <!-- Scrollable Content Area -->
    <div class="max-h-[600px] overflow-y-auto bg-gray-50">
        <!-- Accordion Items -->
        <div class="divide-y divide-gray-200">
            <template x-for="(result, index) in paginatedData" :key="index">
                <div class="bg-white hover:bg-gray-50 transition-all duration-200 border-l-4" 
                     :class="openItem === index ? 'border-l-gray-500 shadow-md' : 'border-l-transparent'">
                    <!-- Accordion Header (Collapsed View) -->
<div 
    @click="(result.hard_skills && result.hard_skills.length > 0) || (result.soft_skills && result.soft_skills.length > 0) ? (openItem = openItem === index ? null : index) : null"
    class="grid grid-cols-12 gap-4 px-8 py-6 items-center" :class="((result.hard_skills && result.hard_skills.length > 0) || (result.soft_skills && result.soft_skills.length > 0)) ? 'cursor-pointer' : 'cursor-default'">
    
    <!-- LEFT-ALIGNED: Job Title (2 cols) -->
    <div class="col-span-2 flex items-center justify-start">
        <h4 class="font-bold text-gray-900 text-base" x-text="result.role"></h4>
    </div>

    <!-- LEFT-ALIGNED: Sector (3 cols) -->
    <div class="col-span-3 flex items-center justify-start">
        <p class="text-xs font-bold text-gray-700 uppercase tracking-wide leading-relaxed" x-text="result.sector"></p>
    </div>

  <!-- CENTER-ALIGNED: Skills Preview (3 cols) -->
<div class="col-span-3 flex items-center justify-center">
    <div class="flex flex-col gap-1" style="min-width: 140px;">

        <!-- Technical row — always rendered, hidden if not applicable -->
        <div class="flex items-center gap-2" x-show="result.has_technical_checkbox">
            <span class="text-gray-400 font-medium text-xs">•</span>
            <span class="text-sm text-gray-700">
                <template x-if="result.hard_skills && result.hard_skills.length > 0">
                    <span><span class="font-bold" x-text="result.hard_skills.length"></span> <span class="font-bold">Technical Skill</span><span x-show="result.hard_skills.length > 1">s</span></span>
                </template>
                <template x-if="!result.hard_skills || result.hard_skills.length === 0">
                    <span class="font-semibold">Technical Skills</span>
                </template>
            </span>
        </div>

        <!-- Soft row — always rendered, hidden if not applicable -->
        <div class="flex items-center gap-2" x-show="result.has_soft_checkbox">
            <span class="text-gray-400 font-medium text-xs">•</span>
            <span class="text-sm text-gray-700">
                <template x-if="result.soft_skills && result.soft_skills.length > 0">
                    <span><span class="font-bold" x-text="result.soft_skills.length"></span> <span class="font-bold">Soft Skill</span><span x-show="result.soft_skills.length > 1">s</span></span>
                </template>
                <template x-if="!result.soft_skills || result.soft_skills.length === 0">
                    <span class="font-semibold">Soft Skills</span>
                </template>
            </span>
        </div>

        <!-- No skills -->
        <template x-if="!result.has_technical_checkbox && !result.has_soft_checkbox">
            <span class="text-xs text-gray-400 italic">No skills specified</span>
        </template>

        <!-- Click to view -->
        <span class="text-xs text-gray-400 italic mt-0.5"
              x-show="openItem !== index && ((result.hard_skills && result.hard_skills.length > 0) || (result.soft_skills && result.soft_skills.length > 0))">
            Click to view details
        </span>
    </div>
</div>
    <!-- CENTER-ALIGNED: Salary Range (2 cols) -->
    <div class="col-span-2 flex items-center justify-center">
        <div class="flex flex-col">
            <template x-if="result.salary_range && result.salary_range !== 'Not specified'">
                <span class="text-sm font-semibold text-gray-900" x-text="result.salary_range"></span>
            </template>
            <template x-if="!result.salary_range || result.salary_range === 'Not specified'">
                <span class="text-xs text-gray-400 italic">Not specified</span>
            </template>
        </div>
    </div>

    <!-- CENTER-ALIGNED: Impact Badge + Chevron (2 cols) -->
    <div class="col-span-2 flex items-center justify-center relative">
        <!-- Badge centered -->
        <span 
            class="px-4 py-2 rounded-lg text-sm font-bold min-w-[80px] text-center shadow-sm"
            :class="{
                'bg-red-50 text-red-700 border border-red-200': result.impact === 'High',
                'bg-green-50 text-green-700 border border-green-200': result.impact === 'Low',
                'bg-amber-50 text-amber-700 border border-amber-200': result.impact === 'Medium' || !result.impact
            }"
            x-text="result.impact || 'Medium'">
        </span>
        <!-- Chevron positioned absolutely to the right so it doesn't shift the badge -->
        <svg 
            class="w-5 h-5 transition-all duration-300 flex-shrink-0 absolute right-2"
            :class="[
                openItem === index ? 'rotate-180 text-gray-600' : 'text-gray-400',
                ((result.hard_skills && result.hard_skills.length > 0) || (result.soft_skills && result.soft_skills.length > 0)) ? 'opacity-100' : 'opacity-0'
            ]"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>
</div>
                    <!-- Accordion Content (Expanded View) - Formal black styling -->
                    <div 
                        x-show="openItem === index"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-4"
                        class="border-t border-gray-200 bg-gray-50"
                        style="display: none;">
                        
                        <div class="px-8 py-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Technical Skills -->
                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <div class="flex items-center gap-2 mb-4">
                                        <span class="text-sm font-bold text-gray-900 uppercase tracking-wide">Missing Technical Skills</span>
                                    </div>
                                    <template x-if="result.hard_skills && result.hard_skills.length > 0">
                                        <div class="flex flex-wrap gap-2.5">
                                            <template x-for="skill in result.hard_skills" :key="skill.name || skill">
                                                <span 
                                                    class="px-4 py-2.5 bg-white text-gray-800 border border-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm"
                                                    x-text="skill.name || skill">
                                                </span>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!result.hard_skills || result.hard_skills.length === 0">
                                        <div class="text-center py-6">
                                            <div class="text-3xl mb-2 opacity-20">✓</div>
                                            <p class="text-sm text-gray-400 font-medium">No technical skill gaps identified</p>
                                        </div>
                                    </template>
                                </div>

                                <!-- Soft Skills -->
                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <div class="flex items-center gap-2 mb-4">
                                        <div>
                                            <span class="text-sm font-bold text-gray-900 uppercase tracking-wide block">Missing Soft Skills</span>
                                            <span class="text-xs text-gray-600 font-medium">(Critical Gaps)</span>
                                        </div>
                                    </div>
                                    <template x-if="result.soft_skills && result.soft_skills.length > 0">
                                        <div class="flex flex-wrap gap-2.5">
                                            <template x-for="skill in result.soft_skills" :key="skill.name || skill">
                                                <span 
                                                    class="px-4 py-2.5 bg-white text-gray-800 border border-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm"
                                                    x-text="skill.name || skill">
                                                </span>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!result.soft_skills || result.soft_skills.length === 0">
                                        <div class="text-center py-6">
                                            <div class="text-3xl mb-2 opacity-20">✓</div>
                                            <p class="text-sm text-gray-400 font-medium">No soft skill gaps identified</p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Salary Range Details in Expanded View (Optional) -->
                            <template x-if="result.salary_min && result.salary_max">
                                <div class="mt-6 bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <span class="text-lg">💰</span>
                                            </div>
                                            <span class="text-sm font-bold text-gray-900 uppercase tracking-wide">Salary Range</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-gray-900">
                                                ₱<span x-text="Number(result.salary_min).toLocaleString()"></span> - ₱<span x-text="Number(result.salary_max).toLocaleString()"></span>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">Monthly compensation</div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Pagination Controls -->
    <div class="px-8 py-5 border-t bg-white flex items-center justify-between shadow-inner">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <span>Showing</span>
            <span class="font-bold text-gray-900" x-text="(currentPage - 1) * itemsPerPage + 1"></span>
            <span>to</span>
            <span class="font-bold text-gray-900" x-text="Math.min(currentPage * itemsPerPage, (window.matrixResultsData?.length || 0))"></span>
            <span>of</span>
            <span class="font-bold text-gray-900" x-text="(sortedData?.length || 0)"></span>
            <span>results</span>
        </div>

        <div class="flex items-center gap-2">
            <!-- Previous Button -->
            <button 
                @click="prevPage()"
                :disabled="currentPage === 1"
                :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50 hover:border-gray-400'"
                class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 transition-all">
                Previous
            </button>

            <!-- Page Numbers -->
            <div class="flex gap-1.5">
                <template x-for="page in totalPages" :key="page">
                    <button 
                        @click="goToPage(page)"
                        :class="currentPage === page ? 'bg-emerald-500 text-white border-emerald-500 shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300'"
                        class="min-w-[44px] px-4 py-2.5 rounded-lg border text-sm font-bold transition-all"
                        x-text="page">
                    </button>
                </template>
            </div>

            <!-- Next Button -->
            <button 
                @click="nextPage()"
                :disabled="currentPage === totalPages"
                :class="currentPage === totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50 hover:border-gray-400'"
                class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 transition-all">
                Next
            </button>
        </div>
    </div>
</div>

    <div class="flex items-center justify-center">
                        <p class="text-xs text-slate-500">
                            Source: Tab1-Employment-Davao-Region-with-JUL2025.xlsx (Rates) | Module 2 Sources: PhilJobNet, PSA ISLE, Industry Surveys.
                        </p>
                    </div>
    </div><!-- end min-w -->
    </div><!-- end overflow-x-auto -->

@else
    <!-- Empty State -->
    <div class="p-12 text-center bg-white">
        <div class="text-6xl mb-4 opacity-20">📊</div>
        <p class="text-slate-500 font-medium">No competency gap data available yet.</p>
        <p class="text-slate-400 text-sm mt-2">Data will appear once submissions are approved.</p>
    </div>
@endif

<div id="lmi-matrix-modal" class="fixed inset-0 z-[9999] flex items-center justify-center px-4 hidden">
    <div id="modal-backdrop" class="absolute inset-0 backdrop-blur-md bg-white/30 pointer-events-none"></div>
    <div id="lmi-form-content" class="bg-white rounded-2xl shadow-2xl w-full w-[96vw] h-[96vh] max-w-[96vw] max-h-[96vh] overflow-hidden relative z-10 pointer-events-auto">
        
        <div class="bg-teal-700 p-5 flex justify-between items-center text-white sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-bold">INDUSTRY SKILLS NEED SURVEY</h3>
            </div>
            <button id="close-modal-btn" class="text-white hover:bg-teal-600 p-1 rounded transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- ► STEP INDICATOR ◄ -->
        <div class="bg-teal-600 px-5 py-4 sticky top-[68px] z-10">
            <div class="flex items-center justify-between max-w-3xl mx-auto">
                <div class="flex flex-col items-center">
                    <div class="step-circle w-8 h-8 rounded-full bg-white text-teal-700 flex items-center justify-center text-sm font-bold">1</div>
                    <span class="text-white text-xs mt-1 hidden sm:block">Company</span>
                </div>
                <div class="step-line flex-1 h-1 bg-teal-500 mx-2"></div>
                <div class="flex flex-col items-center">
                    <div class="step-circle w-8 h-8 rounded-full bg-teal-500 text-white flex items-center justify-center text-sm font-bold">2</div>
                    <span class="text-white text-xs mt-1 hidden sm:block">Roles</span>
                </div>
                <div class="step-line flex-1 h-1 bg-teal-500 mx-2"></div>
                <div class="flex flex-col items-center">
                    <div class="step-circle w-8 h-8 rounded-full bg-teal-500 text-white flex items-center justify-center text-sm font-bold">3</div>
                    <span class="text-white text-xs mt-1 hidden sm:block">Diagnosis</span>
                </div>
                <div class="step-line flex-1 h-1 bg-teal-500 mx-2"></div>
                <div class="flex flex-col items-center">
                    <div class="step-circle w-8 h-8 rounded-full bg-teal-500 text-white flex items-center justify-center text-sm font-bold">4</div>
                    <span class="text-white text-xs mt-1 hidden sm:block">Engagement</span>
                </div>
            </div>
        </div>
        <!-- ► END STEP INDICATOR ◄ -->

        <div class="overflow-y-auto h-[calc(98vh-250px)]">
    <div class="p-8">
        <!-- ► ONLY SHOW IN STEP 1 ◄ -->
        <div id="intro-section">
            <h4 class="text-l font-bold pb-2">INDUSTRY SKILLS NEED SURVEY</h4>
            <p class="text-gray-600 text-sm leading-relaxed mb-8 pb-6 border-b border-gray-200">
                {{__('lmip.lmi_intro')}}
            </p>
            <h5 class="text-l font-bold pb-2">DATA PRIVACY STATEMENT</h5>
            <p class="text-gray-600 text-sm leading-relaxed mb-8 pb-6 border-b border-gray-200">
                {{ __('lmip.privacy_statement') }}
            </p>
        </div>

            <!-- ════════════════════════════════════════════════════════
                 SINGLE FORM — all 4 steps live inside here
                 ════════════════════════════════════════════════════════ -->
            <form action="{{ route('lmi.submit') }}" method="POST" class="space-y-5" id="lmi-form">
            @csrf
            <input type="hidden" name="test_form_start" value="FORM_STARTED">


            <!-- ─── STEP 1: COMPANY PROFILE ─────────────────────── -->
            <div class="lmi-step" data-step="0">

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mt-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="text-sm font-bold text-gray-800 uppercase tracking-wide">Part 1: Company Profile</div>
                    </div>
                    <div class="h-px bg-gray-100 mb-5"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">

                        <!-- LEFT COLUMN: Company Name + Designation + Email -->
                        <div class="flex flex-col gap-5">
                            <div>
                                <label class="block text-gray-800 text-sm font-semibold mb-2">Company Name:<span class="text-red-500">*</span></label>
                                <input type="text" name="company" required
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"/>
                            </div>
                            <div>
                                <label class="block text-gray-800 text-sm font-semibold mb-2">Designation / Position:<span class="text-red-500">*</span></label>
                                <input type="text" name="position" required
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"/>
                            </div>
                            <div>
                                <label class="block text-gray-800 text-sm font-semibold mb-2">Email Address:<span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="emailInput" required
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"/>
                                <p id="emailError" class="hidden text-red-500 text-xs mt-1.5 font-medium">Please enter a valid email address (e.g. <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="204e414d45604558414d504c450e434f4d">[email&#160;protected]</a>)</p>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: Name of Respondent + Contact Number -->
                        <div class="flex flex-col gap-5">
                            <div>
                                <label class="block text-gray-800 text-sm font-semibold mb-2">Name of Respondent:<span class="text-red-500">*</span></label>
                                <input type="text" name="respondent" placeholder="e.g., John Quincy Adams" required
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"/>
                            </div>
                            <div>
                                <label class="block text-gray-800 text-sm font-semibold mb-2">
                                    Contact Number:<span class="text-red-500">*</span>
                                </label>

                                <!-- Segmented Control Toggle -->
                                <div class="inline-flex bg-gray-100 rounded-lg p-1 mb-3">
                                    <button type="button" id="toggle-mobile"
                                        onclick="switchContactType('mobile')"
                                        class="contact-type-btn flex items-center gap-1.5 px-4 py-1.5 rounded-md text-sm font-semibold bg-white text-teal-700 shadow-sm border border-gray-200 transition-all duration-200">
                                        <span class="text-base">📱</span> Mobile
                                    </button>
                                    <button type="button" id="toggle-telephone"
                                        onclick="switchContactType('telephone')"
                                        class="contact-type-btn flex items-center gap-1.5 px-4 py-1.5 rounded-md text-sm font-semibold text-gray-500 transition-all duration-200">
                                        <span class="text-base">☎️</span> Telephone
                                    </button>
                                </div>

                                <!-- Mobile Input -->
                                <div id="mobile-input-wrapper" class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pr-3 border-r border-gray-300 pointer-events-none">
                                        <span class="text-lg">🇵🇭</span>
                                        <span class="ml-1.5 text-sm font-semibold text-gray-600">+63</span>
                                    </div>
                                    <input type="tel" name="contact_number" id="mobile-input"
                                        maxlength="10" placeholder="912 345 6789" required
                                        inputmode="numeric"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        class="w-full pl-20 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent focus:bg-white transition-all"/>
                                </div>

                                <!-- Telephone Input -->
                                <div id="telephone-input-wrapper" class="relative hidden">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pr-3 border-r border-gray-300 pointer-events-none">
                                        <span class="text-lg">☎️</span>
                                        <span class="ml-1.5 text-sm font-semibold text-gray-600">PH</span>
                                    </div>
                                    <input type="tel" name="contact_number" id="telephone-input"
                                        maxlength="12" placeholder="e.g. 082-123-4567"
                                        inputmode="numeric"
                                        autocomplete="off"
                                        class="w-full pl-20 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent focus:bg-white transition-all"
                                        disabled/>
                                    <!-- Area code suggestions dropdown -->
                                    <div id="area-code-suggestions"
                                        class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl z-50 hidden overflow-hidden">
                                        <div class="px-3 py-2 bg-gray-50 border-b border-gray-100">
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Matching Area Codes</p>
                                        </div>
                                        <div id="area-code-list" class="max-h-52 overflow-y-auto"></div>
                                    </div>
                                </div>

                                <input type="hidden" name="contact_type" id="contact_type_input" value="mobile">

                                <!-- Hint -->
                                <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1" id="contact-hint">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    10-digit mobile number
                                </p>
                            </div>
                        </div>

                    </div>

                    <!-- Industry Sector Dropdown -->
                    <div class="relative mt-4">
                        <label class="block text-gray-800 text-sm font-semibold mb-2">Industry Sector:<span class="text-red-500">*</span></label>
                        <button type="button" id="industry-dropdown-btn"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 hover:border-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                            <span id="industry-selected-text" class="text-gray-400">Please select your primary operation</span>
                            <svg id="industry-dropdown-arrow" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="industry-dropdown-menu"
                            class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                            <div data-value="Accommodation &amp; Food Service" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Accommodation &amp; Food Service (Hotels, Resorts, Restaurants, Fast Food Chains, Catering Services)</div>
                            <div data-value="Administrative &amp; Support Services" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Administrative &amp; Support Services (Security Agencies, Manpower/Recruitment Agencies, Call Centers, Travel Agencies, Janitorial Services)</div>
                            <div data-value="Agriculture, Forestry, Fishing &amp; Mining" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Agriculture, Forestry, Fishing &amp; Mining</div>
                            <div data-value="Construction" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Construction</div>
                            <div data-value="Education" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Education (Private Schools, Colleges, Universities, Training Centers)</div>
                            <div data-value="Electricity, Gas, Water &amp; Waste Management" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Electricity, Gas, Water &amp; Waste Management (Power Plants, Electric Co-ops, Water Districts, Garbage/Recycling Firms)</div>
                            <div data-value="Financial &amp; Insurance Activities" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Financial &amp; Insurance Activities (Banks, Pawnshops, Lending Investors, Insurance Companies)</div>
                            <div data-value="Human Health &amp; Social Work" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Human Health &amp; Social Work (Hospital, Medical Clinics, Diagnostic Labs, Nursing Homes)</div>
                            <div data-value="Information &amp; Communication" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Information &amp; Communication (Software Companies, ISPs, Telecoms, TV/Radio Stations, Non-Voice Tech BPO)</div>
                            <div data-value="Other Service Activities" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Other Service Activities (Repairs Shops, Beauty Salons, Spas, Laundry Shops, Funeral)</div>
                            <div data-value="Professional, Scientific &amp; Technical Services" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Professional, Scientific &amp; Technical Services (Law Firms, Accounting/Auditing Firms, Engineering/Architectural Firms, Advertising Agencies)</div>
                            <div data-value="Real Estate Activities" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Real Estate Activities (Real Estate Developers, Lessor of Apartment/Office Space)</div>
                            <div data-value="Transportation, Storage &amp; Logistics" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Transportation, Storage &amp; Logistics (Trucking/Hauling Services, Warehousing, Shipping Lines, Courier Services)</div>
                            <div data-value="Wholesale &amp; Retail Trade" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Wholesale &amp; Retail Trade (Trading Companies, Malls, Hardware Stores, Car Dealers, Online Shops, etc.)</div>
                        </div>
                        <input type="hidden" id="industry-selector-input" name="industrySelector" required>
                    </div>

                    <!-- Company Size Dropdown -->
                    <div class="relative mt-4">
                        <label class="block text-gray-800 text-sm font-semibold mb-2">Company Size:<span class="text-red-500">*</span></label>
                        <button type="button" id="company-size-btn"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 hover:border-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                            <span id="company-size-selected-text" class="text-gray-400">Select company size</span>
                            <svg id="company-size-arrow" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="company-size-dropdown" class="absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                            <div data-value="Less than 50" class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">Less than 50</div>
                            <div data-value="51-200" class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">51-200</div>
                            <div data-value="201-500" class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">201-500</div>
                            <div data-value="More than 500" class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">More than 500</div>
                        </div>
                        <input type="hidden" id="company-size-input" name="companySize" required>
                    </div>
                </div>

                <!-- NAV -->
                <div class="flex justify-end mt-6">
                    <button type="button" class="btn-next bg-teal-600 hover:bg-teal-700 text-white font-semibold px-8 py-2.5 rounded-lg transition shadow-md">Next </button>
                </div>
            </div>
            <!-- ─── END STEP 1 ──────────────────────────────────── -->


            <!-- ─── STEP 2: HARD-TO-FILL ROLES ──────────────────── -->
            <div class="lmi-step" data-step="1" style="display:none;">

                <div class="bg-teal-50 border border-teal-200 rounded-lg p-6 mt-10 overflow-hidden">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="text-sm font-bold text-gray-800 uppercase tracking-wide">Part II: Hard-to-Fill Roles</div>
                    </div>
                    <div class="h-px bg-gray-100 mb-4"></div>
                    <p class="text-teal-700 text-xs font-medium mb-4">
                        Please identify the TOP Job Titles you find hardest to fill. Be as specific as possible (e.g., instead of "IT Skills", say "Python Programming").
                    </p>

                    <div id="jobTitlesContainer" class="space-y-6">
                        <div class="bg-white rounded-lg p-4 border border-gray-200 job-entry">
                            <!-- 8 -->
                            <div class="mb-4">
                                <label class="block text-gray-800 text-sm font-semibold mb-2"> Job Title: <span class="text-gray-700 text-sm font-medium">(Please list only one job title)</span><span class="text-red-500">*</span></label>
                                <input type="text" name="job_title[]" placeholder="e.g. Senior Java Developer" required
                                    class="job-title-input w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"/>
                            </div>
                            <!-- 9 -->
                            <div class="mb-4">
                                <label class="block text-gray-800 text-sm font-semibold mb-2"> Standard Job Classifications / Families: <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <button type="button" class="job-classification-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                        <span class="job-classification-text text-gray-400">Select job classification</span>
                                        <svg class="job-classification-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="job-classification-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                        <div data-value="Accounting, Finance &amp; Banking" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Accounting, Finance &amp; Banking</div>
                                        <div data-value="Administrative, HR &amp; Office Support" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Administrative, HR &amp; Office Support</div>
                                        <div data-value="Agriculture, Forestry &amp; Agribusiness" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Agriculture, Forestry &amp; Agribusiness</div>
                                        <div data-value="Construction, Engineering &amp; Architecture" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Construction, Engineering &amp; Architecture</div>
                                        <div data-value="Customer Service &amp; BPO (Contact Center)" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Customer Service &amp; BPO (Contact Center)</div>
                                        <div data-value="Education, Training &amp; Academe" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Education, Training &amp; Academe</div>
                                        <div data-value="Healthcare, Medical &amp; Allied Services" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Healthcare, Medical &amp; Allied Services</div>
                                        <div data-value="IT, Software, Data &amp; Digital Creative" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• IT, Software, Data &amp; Digital Creative</div>
                                        <div data-value="Legal, Compliance &amp; Public Service" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Legal, Compliance &amp; Public Service</div>
                                        <div data-value="Logistics, Transport &amp; Supply Chain" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Logistics, Transport &amp; Supply Chain</div>
                                        <div data-value="Manufacturing, Production &amp; Technical" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Manufacturing, Production &amp; Technical</div>
                                        <div data-value="Sales, Marketing, Retail &amp; E-Commerce" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Sales, Marketing, Retail &amp; E-Commerce</div>
                                        <div data-value="Science, Research &amp; Laboratory" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Science, Research &amp; Laboratory</div>
                                        <div data-value="Skilled Trades, Maintenance &amp; General Services" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Skilled Trades, Maintenance &amp; General Services</div>
                                        <div data-value="Tourism, Hospitality &amp; Food Service" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Tourism, Hospitality &amp; Food Service</div>
                                    </div>
                                    <input type="hidden" class="job-classification-input" name="job_classification[]" required>
                                </div>
                            </div>
                            
                            <!-- 10 - Salary Range -->
                            <div class="mb-4">
                                <label class="block text-gray-800 text-sm font-semibold mb-2"> Salary Range: <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <button type="button" class="salary-range-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                        <span class="salary-range-text text-gray-400">Select salary range</span>
                                        <svg class="salary-range-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="salary-range-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden hidden">
                                        <div data-value="₱30,000 - ₱59,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱30,000 - ₱59,999</div>
                                        <div data-value="₱60,000 - ₱89,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱60,000 - ₱89,999</div>
                                        <div data-value="₱90,000 - ₱149,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱90,000 - ₱149,999</div>
                                        <div data-value="₱150,000 - ₱499,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱150,000 - ₱499,999</div>
                                        <div data-value="₱500,000 and above" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱500,000 and above</div>
                                        <div data-value="Below ₱30,000" class="salary-range-option below-30k-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition"> 
                                        Below ₱30,000 (please specify)
                                    </div>
                                    </div>
                                    <input type="hidden" class="salary-range-input" name="salary_range[]">
                                </div>
                                
                                <!-- Below 30k input field (shown when "Below ₱30,000" is selected) -->
                                <div class="below-30k-input-container mt-3 hidden">
                                <label class="block text-gray-600 text-xs font-medium mb-2">Please specify the exact salary amount:</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">₱</span>
                                    <input type="text" 
                                        name="below_30k_salary[]"
                                        class="below-30k-salary-input w-full pl-8 pr-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm" 
                                        placeholder="e.g. 25,000"
                                        inputmode="numeric">
                                </div>
                            </div>
                            </div>
                            <!-- 11 -->
                            <div class="mb-4">
                                <label class="block text-gray-800 text-sm font-semibold mb-2">Duration that the Vacancy is Open: <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <button type="button" class="duration-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                        <span class="duration-text text-gray-400">Select duration</span>
                                        <svg class="duration-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="duration-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden hidden">
                                        <div data-value="Less than 30 Days" class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">Less than 30 Days</div>
                                        <div data-value="30-60 Days" class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">30-60 Days</div>
                                        <div data-value="60-90 Days" class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">60-90 Days</div>
                                        <div data-value="90+ Days" class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">90+ Days</div>
                                    </div>
                                    <input type="hidden" class="duration-input" name="vacancy_duration[]" required>
                                </div>
                            </div>
                            <!-- 12 -->
                            <div class="mb-4">
                                <label class="block text-gray-800 text-sm font-semibold mb-2">
                                    Reasons For Difficulty (Role-Level) <span class="italic text-gray-500">(Check all that apply)</span>
                                </label>
                                <div class="difficulty-reasons space-y-3">
                                    <div class="technical-skills-label p-3 border rounded-lg transition-all border-gray-200">
                                        <label class="flex items-start cursor-pointer">
                                            <input type="checkbox" name="difficulty_reasons_0[]" value="Technical / Hard Skills Missing"
                                                class="technical-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                            <div class="ml-3">
                                                <div class="font-semibold text-gray-800">Technical / Hard Skills Missing</div>
                                                <div class="text-xs text-gray-500 mt-1">Applicants do not have the required tools, software, or technical knowledge</div>
                                            </div>
                                        </label>
                                        <div class="technical-details mt-3 hidden">
                                            <label class="block text-gray-600 text-xs font-medium mb-1">What specific technical tools, software, or machinery knowledge is missing?</label>
                                            <div class="technical-tags-container flex flex-wrap gap-2 mb-2"></div>
                                            <div class="flex gap-2">
                                                <input type="text" class="technical-skill-input flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                                    placeholder="Type a skill and press Enter (e.g. Python, SQL, AutoCAD...)"/>
                                                <button type="button" class="add-technical-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Enter</button>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Press Enter or comma to add each skill</p>
                                            <input type="hidden" class="technical-skills-input" name="technical_skills_missing[]">
                                        </div>
                                    </div>
                                    <div class="soft-skills-label p-3 border rounded-lg transition-all border-gray-200">
                                        <label class="flex items-start cursor-pointer">
                                            <input type="checkbox" name="difficulty_reasons_0[]" value="Soft / Employability Skills Missing"
                                                class="soft-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                            <div class="ml-3">
                                                <div class="font-semibold text-gray-800">Soft / Employability Skills Missing</div>
                                                <div class="text-xs text-gray-500 mt-1">Applicants cannot communicate effectively, work in teams, or demonstrate professionalism</div>
                                            </div>
                                        </label>
                                        <div class="soft-details mt-3 hidden">
                                            <label class="block text-gray-600 text-xs font-medium mb-1">What attitude or behavioral traits cause you to reject applicants?</label>
                                            <div class="soft-tags-container flex flex-wrap gap-2 mb-2"></div>
                                            <div class="flex gap-2">
                                                <input type="text" class="soft-skill-input flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                                    placeholder="Type a trait and press Enter (e.g. Poor communication, Unprofessional...)"/>
                                                <button type="button" class="add-soft-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Enter</button>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Press Enter or comma to add each trait</p>
                                            <input type="hidden" class="soft-skills-input" name="soft_skills_missing[]">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 13 -->
                            <div class="mb-4 mt-6 pt-4 border-t border-gray-200">
                                <label class="block text-gray-800 text-sm font-semibold mb-3">
                                    How much does the difficulty finding qualified applicants for this role impact your business operations? <span class="text-red-500">*</span>
                                </label>
                                <div class="impact-level space-y-3">
                                    <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                        <input type="radio" name="impact_level_0" value="High" required class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                        <div class="ml-3 flex-1">
                                            <div class="font-semibold text-gray-900">High Impact</div>
                                            <div class="text-xs text-gray-500 mt-1">Operations are significantly disrupted, critical tasks or projects are delayed, affecting productivity and revenue</div>
                                        </div>
                                    </label>
                                    <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                        <input type="radio" name="impact_level_0" value="Medium" required class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                        <div class="ml-3 flex-1">
                                            <div class="font-semibold text-gray-900">Medium Impact</div>
                                            <div class="text-xs text-gray-500 mt-1">Operations continue but require overtime, increased workload for existing staff, or minor project delays</div>
                                        </div>
                                    </label>
                                    <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                        <input type="radio" name="impact_level_0" value="Low" required class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                        <div class="ml-3 flex-1">
                                            <div class="font-semibold text-gray-900">Low Impact</div>
                                            <div class="text-xs text-gray-500 mt-1">Minimal impact; new hires can be trained internally without significant operational disruptions</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-job-title-btn"
                        class="w-full mt-4 px-4 py-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg transition shadow-md flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Another Job Title
                    </button>
                </div>


                <!-- NAV -->
                <div class="flex justify-between mt-6">
                    <button type="button" class="btn-prev bg-white hover:bg-gray-50 text-gray-700 font-semibold px-8 py-2.5 rounded-lg transition border border-gray-300 shadow-sm"> Previous</button>
                    <button type="button" class="btn-next bg-teal-600 hover:bg-teal-700 text-white font-semibold px-8 py-2.5 rounded-lg transition shadow-md">Next </button>
                </div>
            </div>
            <!-- ─── END STEP 2 ──────────────────────────────────── -->


            <!-- ─── STEP 3: DIAGNOSIS OF MISMATCH ───────────────── -->
            <div class="lmi-step" data-step="2" style="display:none;">

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mt-10">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-sm font-bold text-gray-800 uppercase tracking-wide">Part III: Diagnosis of Mismatch</div>
                    </div>
                    <div class="h-px bg-gray-100 mb-4"></div>
                    <p class="text-gray-600 text-xs font-medium mb-6">
                        For applicants who meet formal qualifications (degree, license, or certification), which observable factors most often cause them to be rejected?
                    </p>

                    <div class="space-y-6">
                        <!-- 13 -->
                        <div class="bg-white rounded-lg p-5 border border-gray-200">
                            <label class="block text-gray-800 text-sm font-semibold mb-3">
                                Reason Qualified Applicants Are Rejected (Applicant-Level) <span class="text-gray-500 italic text-xs">(Check all that apply)</span>
                            </label>
                            <div class="space-y-3">
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="checkbox" name="rejection_reasons[]" value="Lack of practical / hands-on experience" class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <div class="font-semibold text-gray-900">Lack of practical / hands-on experience</div>
                                        <div class="text-xs text-gray-500 mt-1">Cannot apply theory to real work; requires supervision</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="checkbox" name="rejection_reasons[]" value="Skills are outdated" class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <div class="font-semibold text-gray-900">Skills are outdated</div>
                                        <div class="text-xs text-gray-500 mt-1">Training received does not match current tools, systems, or industry practices</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="checkbox" name="rejection_reasons[]" value="Poor communication skills" class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <div class="font-semibold text-gray-900">Poor communication skills</div>
                                        <div class="text-xs text-gray-500 mt-1">Oral, written, presentation, or cross-cultural communication issues</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="checkbox" name="rejection_reasons[]" value="Low job readiness / poor interview performance" class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <div class="font-semibold text-gray-900">Low job readiness / poor interview performance</div>
                                        <div class="text-xs text-gray-500 mt-1">Cannot demonstrate readiness during recruitment; fails assessments; lacks workplace etiquette</div>
                                    </div>
                                </label>
                                <div class="other-rejection-option border rounded-lg transition-all border-gray-200">
                                    <label class="flex items-start p-3 cursor-pointer">
                                        <input type="checkbox" name="rejection_reasons[]" value="Other" class="other-rejection-checkbox mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                        <div class="ml-3 flex-1"><div class="font-semibold text-gray-900">Other (please specify)</div></div>
                                    </label>
                                    <div class="other-rejection-input px-3 pb-3 ml-7 hidden">
                                        <input type="text" name="rejection_reasons_other" placeholder="Please specify other reasons..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 14 -->
                        <div class="bg-white rounded-lg p-5 border border-gray-200">
                            <label class="block text-gray-800 text-sm font-semibold mb-3">
                                How often do you coordinate with Universities/Colleges to discuss your skills requirements? <span class="text-gray-500 italic text-xs">(Select ONE)</span>
                            </label>
                            <div class="coordination-options space-y-3">
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="radio" name="coordination_frequency" value="Never" required class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                    <div class="ml-3 flex-1"><div class="font-semibold text-gray-900">Never</div></div>
                                </label>
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="radio" name="coordination_frequency" value="Rarely" required class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <div class="font-semibold text-gray-900">Rarely</div>
                                        <div class="text-xs text-gray-500 mt-1">Only when invited to graduations/events</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="radio" name="coordination_frequency" value="Occasionally" required class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <div class="font-semibold text-gray-900">Occasionally</div>
                                        <div class="text-xs text-gray-500 mt-1">During OJT placement</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="radio" name="coordination_frequency" value="Frequently" required class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <div class="font-semibold text-gray-900">Frequently</div>
                                        <div class="text-xs text-gray-500 mt-1">We sit on advisory boards/curriculum reviews</div>
                                    </div>
                                </label>
                                <div class="other-coordination-option border rounded-lg transition-all border-gray-200">
                                    <label class="flex items-start p-3 cursor-pointer">
                                        <input type="radio" name="coordination_frequency" value="Other" required class="other-coordination-radio mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                        <div class="ml-3 flex-1"><div class="font-semibold text-gray-900">Other (please specify)</div></div>
                                    </label>
                                    <div class="other-coordination-input px-3 pb-3 ml-7 hidden">
                                        <input type="text" name="coordination_frequency_other" placeholder="Please specify..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- NAV -->
                <div class="flex justify-between mt-6">
                    <button type="button" class="btn-prev bg-white hover:bg-gray-50 text-gray-700 font-semibold px-8 py-2.5 rounded-lg transition border border-gray-300 shadow-sm"> Previous</button>
                    <button type="button" class="btn-next bg-teal-600 hover:bg-teal-700 text-white font-semibold px-8 py-2.5 rounded-lg transition shadow-md">Next </button>
                </div>
            </div>
            <!-- ─── END STEP 3 ──────────────────────────────────── -->


            <!-- ─── STEP 4: ENGAGEMENT & NEXT STEPS ─────────────── -->
            <div class="lmi-step" data-step="3" style="display:none;">

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mt-8">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                            </svg>
                        </div>
                        <div class="text-sm font-bold text-gray-800 uppercase tracking-wide">Part IV: Engagement &amp; Next Steps</div>
                    </div>
                    <div class="h-px bg-gray-100 mb-4"></div>
                    <p class="text-gray-600 text-xs font-medium mb-4">Help us understand what features would be most valuable to you.</p>

                    <div class="space-y-5">
                        <!-- 20 -->
                        <div>
                            <label class="block text-gray-800 text-sm font-semibold mb-3">
                                If DOLE provides a Regional LMI Dashboard, what features would be most useful for you? <span class="text-gray-500 text-xs">(Select top 2)</span>
                            </label>
                            <div class="space-y-3" id="lmi-features-group">
                                <label class="lmi-feature-label flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">
                                    <input type="checkbox" name="lmi_features[]" value="Viewing the supply of graduates" class="lmi-feature-checkbox mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3 flex-1"><div class="font-semibold text-gray-900">Viewing the supply of graduates (e.g., "How many IT grads will graduate next year?")</div></div>
                                </label>
                                <label class="lmi-feature-label flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">
                                    <input type="checkbox" name="lmi_features[]" value="A channel to submit real-time feedback" class="lmi-feature-checkbox mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3 flex-1"><div class="font-semibold text-gray-900">A channel to submit real-time feedback on curriculum quality</div></div>
                                </label>
                                <label class="lmi-feature-label flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">
                                    <input type="checkbox" name="lmi_features[]" value="A directory of job placement offices" class="lmi-feature-checkbox mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3 flex-1"><div class="font-semibold text-gray-900">A directory of job placement offices and Public Employment offices (PESOs)</div></div>
                                </label>
                                <div class="lmi-other-option border rounded-lg border-gray-200 transition-all">
                                    <label class="lmi-feature-label flex items-start p-3 cursor-pointer hover:bg-blue-50 hover:border-blue-300">
                                        <input type="checkbox" name="lmi_features[]" value="Other" class="lmi-feature-checkbox lmi-other-checkbox mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <div class="ml-3 flex-1"><div class="font-semibold text-gray-900">Other (please specify)</div></div>
                                    </label>
                                    <div class="lmi-other-input px-3 pb-3 ml-7 hidden">
                                        <input type="text" name="lmi_features_other" placeholder="Please specify..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Additional -->
                        <div>
                            <label class="block text-gray-800 text-sm font-semibold mb-2">
                                Additional Insights or Suggestions: <span class="text-gray-500 text-xs">(Optional)</span>
                            </label>
                            <textarea name="specific_inputs" rows="4" placeholder="Please share any additional insights or suggestions..."
                                class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Consent -->
                <div class="mt-6 mb-2">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" name="consent" value="1" required class="consent-checkbox mt-1 w-4 h-4 text-teal-600">
                        <span class="ml-3 text-l text-gray-700">
                            By proceeding, I signify my consent to the processing of my personal data for labor market intelligence purposes, in accordance with RA 10173 (Data Privacy Act of 2012) and its IRR. <span class="text-red-500">*</span>
                        </span>
                    </label>
                </div>

                <!-- NAV -->
                <div class="flex justify-between mt-6">
                    <button type="button" class="btn-prev bg-white hover:bg-gray-50 text-gray-700 font-semibold px-8 py-2.5 rounded-lg transition border border-gray-300 shadow-sm"> Previous</button>
                    <button type="submit" class="btn-submit-lmi bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-8 rounded-lg transition shadow-lg">
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
<div id="confirmation-modal" class="fixed inset-0 flex items-center justify-center px-4 hidden" style="z-index: 9999;">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative p-6 z-10">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Submission</h3>
            <p class="text-sm text-gray-500 mb-6">
                Are you sure you want to submit this Industry Skills Need Survey? Please ensure all information is accurate before proceeding.
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
<div id="success-modal" class="fixed inset-0 flex items-center justify-center px-4 hidden" style="z-index: 9999;">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative p-6 z-10">
    
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Successfully Submitted!</h3>
            <p class="text-sm text-gray-500 mb-6">
                Your Industry Skills Need Survey has been submitted successfully. Thank you for your contribution to the Labor Market Intelligence system.
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
 <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
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

// Prepare comparison data (initial load from server)
let comparisonData = @json($comparison_data ?? []);
let currentSelectedYear = {{ $selected_year ?? 'null' }};

// Build and render the main chart
let mainChart = null;
let expandedChart = null;

function renderMainChart() {
    const ctx = document.getElementById('highVolumeHorizontalChart');
    if (!ctx || !comparisonData.length) return;
    if (mainChart) mainChart.destroy();
    mainChart = new Chart(ctx, buildChartConfig(comparisonData));
}

function buildChartConfig(data, axisSize = 12) {
    const labels      = data.map(d => d.title);
    const currentData = data.map(d => d.current_count);
    const prevData    = data.map(d => d.previous_count);
    const hasPrev     = prevData.some(v => v && v > 0);

    const datasets = [
        ...(hasPrev ? [{
            label: String(currentSelectedYear - 1),
            data: prevData,
            backgroundColor: 'rgba(16, 185, 129, 0.85)',
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 0, borderRadius: 4, barThickness: 14,
        }] : []),
        {
            label: String(currentSelectedYear),
            data: currentData,
            backgroundColor: 'rgba(99, 102, 241, 0.9)',
            borderColor: 'rgba(99, 102, 241, 1)',
            borderWidth: 0, borderRadius: 4, barThickness: 14,
        }
    ];

    return {
        type: 'bar',
        data: { labels, datasets },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true, position: 'top', align: 'end',
                    labels: { boxWidth: 12, boxHeight: 12, font: { size: axisSize, weight: '500' }, padding: 15, usePointStyle: true, pointStyle: 'circle' }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)', padding: 12,
                    titleFont: { size: 13, weight: 'bold' }, bodyFont: { size: 12 },
                    callbacks: {
                        title: ctx => ctx[0].label,
                        label: function(context) {
                            let label = (context.dataset.label || '') + ': ';
                            label += context.parsed.x.toLocaleString();
                            if (context.datasetIndex === 1 && comparisonData[context.dataIndex]) {
                                const { change, is_new } = comparisonData[context.dataIndex];
                                if (is_new) label += ' (NEW)';
                                else if (change !== 0) label += ` (${change > 0 ? '+' : ''}${change}%)`;
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { display: true, color: 'rgba(0,0,0,0.03)' },
                    ticks: { font: { size: axisSize }, callback: v => v >= 1000 ? (v/1000)+'k' : v }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: axisSize, weight: '500' }, color: '#374151' }
                }
            },
            interaction: { mode: 'y', intersect: false }
        }
    };
}

// Fetch new chart data when year changes — no page reload
async function updateChart(year) {
    try {
        const res  = await fetch(`/api/job-market/chart-data?year=${year}`);
        const json = await res.json();

        comparisonData      = json.comparison_data;
        currentSelectedYear = json.selected_year;

        // Update subtitle - show only when previous data exists
        const subtitle     = document.getElementById('chartSubtitle');
        const prevLabel    = document.getElementById('prevYearLabel');
        const currentLabel = document.getElementById('currentYearLabel');

        if (prevLabel)    prevLabel.textContent    = json.previous_year;
        if (currentLabel) currentLabel.textContent = json.selected_year;
        if (subtitle)     subtitle.style.display   = json.has_previous_data ? '' : 'none';

        renderMainChart();
    } catch (e) {
        console.error('Chart update failed:', e);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    renderMainChart();
});

// Expand chart function
function expandChart() {
    const modal = document.getElementById('chartModal');
    modal.classList.remove('hidden');
    
    if (expandedChart) {
        expandedChart.destroy();
    }
    
    const expandedCtx = document.getElementById('highVolumeExpandedChart');
    if (expandedCtx && comparisonData && comparisonData.length > 0) {
        expandedChart = new Chart(expandedCtx, buildChartConfig(comparisonData, 13));
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
document.addEventListener('DOMContentLoaded', function() {

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
    
    // Hide navbar by setting z-index lower than modal
    const navbar = document.querySelector('nav');
    if (navbar) {
        navbar.style.zIndex = '-1';
        navbar.style.visibility = 'hidden';
    }
    
    // ADD THIS: Initialize autocomplete when modal opens
    setTimeout(() => {

        initializeAllAutocompletes();

    }, 200);
}

function hideModal() {

    lmiMatrixModal.classList.add('hidden');
    
    // Remove blur if mainContent exists
    if (mainContent) {
        mainContent.classList.remove('blur-sm');
    }
    
    // CRITICAL: Restore scrolling

    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('overflow-y');
    document.documentElement.style.removeProperty('overflow');
    
    // Double-check after a tiny delay
    setTimeout(() => {
        if (document.body.style.overflow === 'hidden') {
            console.warn('⚠️ Body still has overflow:hidden! Forcing fix...');
            document.body.style.overflow = 'auto';
        }

    }, 50);
    
    // Show navbar by restoring z-index
    const navbar = document.querySelector('nav');
    if (navbar) {
        navbar.style.zIndex = '';
        navbar.style.visibility = '';

    }
    
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


        
        // Submit via AJAX
        const response = await fetch(lmiForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        

        
        // Try to get the response text for debugging
        const responseText = await response.text();

        
        if (response.ok) {
            // Show success modal
            showSuccessModal();
            // Reset the form
            lmiForm.reset();
            
            // Reset all dropdowns to placeholder state
            resetFormDropdowns();

            // Reset step back to step 1
            showStep(0);
            
        } else {
            // Try to parse as JSON for error messages
            try {
                const errorData = JSON.parse(responseText);
                throw new Error(errorData.message || 'Submission failed with status: ' + response.status);
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
        
        // Clear skill tags — call reset() to also clear the internal tags array in the closure,
        // so old values do not ghost back when the user starts typing in a new session.
        const techTagsContainer = entry.querySelector('.technical-tags-container');
        if (techTagsContainer) {
            if (techTagsContainer._tagSystem) {
                techTagsContainer._tagSystem.reset();
            } else {
                techTagsContainer.innerHTML = '';
            }
        }
        
        const softTagsContainer = entry.querySelector('.soft-tags-container');
        if (softTagsContainer) {
            if (softTagsContainer._tagSystem) {
                softTagsContainer._tagSystem.reset();
            } else {
                softTagsContainer.innerHTML = '';
            }
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

        return { button, menu, selectedText, hiddenInput };
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
                tagElement.className = 'inline-flex items-center gap-1 px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm';
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
            
            // Bug fix: stopPropagation so clicking remove does not bubble up to the
            // parent <label> and accidentally toggle the checkbox.
            tagsContainer.querySelectorAll('.remove-tag').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const index = parseInt(e.target.closest('.remove-tag').getAttribute('data-index'));
                    tags.splice(index, 1);
                    updateTags();
                });
            });
        }
        
        function addTag() {
            const tag = input.value.trim();
            // Bug fix: case-insensitive duplicate check so "Apple" and "APPLE" are treated as the same.
            if (tag && !tags.some(t => t.toLowerCase() === tag.toLowerCase())) {
                tags.push(tag);
                input.value = '';
                updateTags();
            } else {
                input.value = '';
            }
        }
        
        // Bug fix: expose reset() so resetFormDropdowns can clear the internal tags array,
        // not just the DOM — otherwise old tags ghost back when the user types in a new session.
        function reset() {
            tags.length = 0;
            tagsContainer.innerHTML = '';
            hiddenInput.value = '';
            input.value = '';
        }
        
        addButton.addEventListener('click', addTag);
        
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                addTag();
            }
        });
        
        return { tags, updateTags, addTag, reset };
    }

    // Checkbox show/hide functionality
    function setupCheckboxToggle(checkbox, targetElement) {
        checkbox.addEventListener('change', () => {
            // Outer wrapper is now a <div> (not a <label>) so we target the
            // nearest element that carries the border/bg classes.
            const wrapper = checkbox.closest('.technical-skills-label, .soft-skills-label');
            if (checkbox.checked) {
                targetElement.classList.remove('hidden');
                wrapper?.classList.add('border-teal-500', 'bg-teal-50');
                wrapper?.classList.remove('border-gray-200');
            } else {
                targetElement.classList.add('hidden');
                wrapper?.classList.remove('border-teal-500', 'bg-teal-50');
                wrapper?.classList.add('border-gray-200');
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
                const techTagSystem = createSkillTagSystem(
                    techDetails,
                    techAddBtn,
                    techInput,
                    techHiddenInput,
                    techTagsContainer
                );
                // Store reset reference so resetFormDropdowns can clear the internal tags array
                techTagsContainer._tagSystem = techTagSystem;
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
                const softTagSystem = createSkillTagSystem(
                    softDetails,
                    softAddBtn,
                    softInput,
                    softHiddenInput,
                    softTagsContainer
                );
                // Store reset reference so resetFormDropdowns can clear the internal tags array
                softTagsContainer._tagSystem = softTagSystem;
            }
        }
    }
   document.addEventListener('click', function(e) {
    // Toggle salary range dropdown
    if (e.target.closest('.salary-range-btn')) {
        const btn = e.target.closest('.salary-range-btn');
        const menu = btn.nextElementSibling;
        const arrow = btn.querySelector('.salary-range-arrow');
        
        menu.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
        
        // Close other dropdowns
        document.querySelectorAll('.salary-range-menu').forEach(m => {
            if (m !== menu) m.classList.add('hidden');
        });
    }
    
    // Select salary range option
    
if (e.target.closest('.salary-range-option')) {
    const option = e.target.closest('.salary-range-option');
    const container = option.closest('.mb-4');
    const btn = container.querySelector('.salary-range-btn');
    const menu = container.querySelector('.salary-range-menu');
    const text = btn.querySelector('.salary-range-text');
    const arrow = btn.querySelector('.salary-range-arrow');
    const input = container.querySelector('.salary-range-input');
    const below30kContainer = container.querySelector('.below-30k-input-container');
    const below30kInput = container.querySelector('.below-30k-salary-input');

    const value = option.dataset.value;

    text.textContent = option.textContent.trim();
    text.classList.remove('text-gray-400');
    text.classList.add('text-gray-700');

    menu.classList.add('hidden');
    arrow.classList.remove('rotate-180');

    if (value === 'Below ₱30,000') {
    input.value = '__below_30k__'; // sentinel value
    below30kContainer.classList.remove('hidden');
    below30kInput.required = true;
} else {
    input.value = value;
    below30kContainer.classList.add('hidden');
    below30kInput.required = false;
    below30kInput.value = '';
}
}
    
    // Close dropdown when clicking outside
    if (!e.target.closest('.salary-range-btn') && !e.target.closest('.salary-range-menu')) {
        document.querySelectorAll('.salary-range-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
        document.querySelectorAll('.salary-range-arrow').forEach(arrow => {
            arrow.classList.remove('rotate-180');
        });
    }
});

// Limit Below 30k Salary Input to 5 digits with comma formatting
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('below-30k-salary-input')) {
        let value = e.target.value;
        
        // Remove any non-numeric characters (including existing commas)
        value = value.replace(/[^0-9]/g, '');
        
        // Limit to 5 characters (29999)
        if (value.length > 5) {
            value = value.substring(0, 5);
        }
        
        // Check if value exceeds 29999
        const numValue = parseInt(value);
        if (numValue >= 30000) {
            value = '30000';
        }
        
        // Add comma formatting (e.g., 25000 becomes 25,000)
       if (value) {
            value = parseInt(value).toLocaleString('en-US');
        }

        e.target.value = value;

        // 🔥 ADD THIS NEW CODE HERE:
        if (value) {
            const container = e.target.closest('.mb-4');
            const salaryRangeInput = container.querySelector('.salary-range-input');
            const numericValue = value.replace(/,/g, ''); // Remove comma (25,000 → 25000)
            
            if (salaryRangeInput) {
                salaryRangeInput.value = numericValue; // Replace __below_30k__ with 25000
            }
        }
    }
});

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
            <label class="block text-gray-800 text-sm font-semibold mb-2">
                Job Title: <span class="text-red-500">*</span>
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
            <label class="block text-gray-800 text-sm font-semibold mb-2">
                Standard Job Classifications / Families: <span class="text-red-500">*</span>
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

        <!-- 10. Salary Range -->
        <div class="mb-4">
            <label class="block text-gray-800 text-sm font-semibold mb-2">Salary Range: <span class="text-red-500">*</span></label>
            <div class="relative">
                <button type="button" class="salary-range-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                    <span class="salary-range-text text-gray-400">Select salary range</span>
                    <svg class="salary-range-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="salary-range-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden hidden">
                    <div data-value="₱30,000 - ₱59,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱30,000 - ₱59,999</div>
                    <div data-value="₱60,000 - ₱89,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱60,000 - ₱89,999</div>
                    <div data-value="₱90,000 - ₱149,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱90,000 - ₱149,999</div>
                    <div data-value="₱150,000 - ₱499,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱150,000 - ₱499,999</div>
                    <div data-value="₱500,000 and above" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱500,000 and above</div>
                    <div data-value="Below ₱30,000" class="salary-range-option below-30k-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">Below ₱30,000 (please specify)</div>
                </div>
                <input type="hidden" class="salary-range-input" name="salary_range[]" >
            </div>
            
            <!-- Below 30k input field -->
            <div class="below-30k-input-container mt-3 hidden">
                <label class="block text-gray-600 text-xs font-medium mb-2">Please specify the exact salary amount:</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">₱</span>
                    <input type="text" 
                        class="below-30k-salary-input w-full pl-8 pr-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm" 
                        placeholder="e.g. 25,000"
                        inputmode="numeric">
                </div>
            </div>
        </div>

        <!-- 11. Duration -->
        <div class="mb-4">
            <label class="block text-gray-800 text-sm font-semibold mb-2">
                Duration that the Vacancy is Open: <span class="text-red-500">*</span>
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

        <!-- 12. Reasons For Difficulty -->
        <div class="mb-4">
            <label class="block text-gray-800 text-sm font-semibold mb-2">
                 Reasons For Difficulty (Role-Level) <span class="italic text-gray-500">(Check all that apply)</span>
            </label>
            <div class="difficulty-reasons space-y-3">
                
                <!-- Technical Skills -->
                <div class="technical-skills-label p-3 border rounded-lg transition-all border-gray-200">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" 
                            name="difficulty_reasons_${entryIndex}[]" 
                            value="Technical / Hard Skills Missing"
                            class="technical-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                        <div class="ml-3">
                            <div class="font-semibold text-gray-800">Technical / Hard Skills Missing</div>
                            <div class="text-xs text-gray-500 mt-1">Applicants do not have the required tools, software, or technical knowledge</div>
                        </div>
                    </label>
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
                                    class="add-technical-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Enter</button>
                        </div>
                        <input type="hidden" class="technical-skills-input" name="technical_skills_missing[]">
                    </div>
                </div>

                <!-- Soft Skills -->
                <div class="soft-skills-label p-3 border rounded-lg transition-all border-gray-200">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" 
                            name="difficulty_reasons_${entryIndex}[]" 
                            value="Soft / Employability Skills Missing"
                            class="soft-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                        <div class="ml-3">
                            <div class="font-semibold text-gray-800">Soft / Employability Skills Missing</div>
                            <div class="text-xs text-gray-500 mt-1">Applicants cannot communicate effectively, work in teams, or demonstrate professionalism</div>
                        </div>
                    </label>
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
                                    class="add-soft-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Enter</button>
                        </div>
                        <input type="hidden" class="soft-skills-input" name="soft_skills_missing[]">
                    </div>
                </div>
            </div>
        </div>

        <!-- 13. Impact Level -->
        <div class="mb-4 mt-6 pt-4 border-t border-gray-200">
            <label class="block text-gray-800 text-sm font-semibold mb-3">
                 How much does the difficulty finding qualified applicants for this role impact your business operations? 
                <span class="text-red-500">*</span>
            </label>
            <div class="impact-level space-y-3">
                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                    <input type="radio" name="impact_level_${entryIndex}" value="High" required
                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                    <div class="ml-3 flex-1">
                        <div class="font-semibold text-gray-900">High Impact</div>
                        <div class="text-xs text-gray-500 mt-1">Operations are significantly disrupted</div>
                    </div>
                </label>
                
                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                    <input type="radio" name="impact_level_${entryIndex}" value="Medium" required
                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                    <div class="ml-3 flex-1">
                        <div class="font-semibold text-gray-900">Medium Impact</div>
                        <div class="text-xs text-gray-500 mt-1">Operations continue with adjustments</div>
                    </div>
                </label>
                
                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                    <input type="radio" name="impact_level_${entryIndex}" value="Low" required
                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                    <div class="ml-3 flex-1">
                        <div class="font-semibold text-gray-900">Low Impact</div>
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
    newJobEntry.scrollIntoView({ behavior: 'smooth', block: 'start' });
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

    // LMI Features: max 2 selections + Other text toggle
    const lmiCheckboxes = document.querySelectorAll('.lmi-feature-checkbox');
    const lmiOtherCheckbox = document.querySelector('.lmi-other-checkbox');
    const lmiOtherInput = document.querySelector('.lmi-other-input');

    lmiCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const checked = document.querySelectorAll('.lmi-feature-checkbox:checked');

            // Enforce max 2
            if (checked.length > 2) {
                this.checked = false;
                return;
            }

            // Disable unchecked when 2 selected, re-enable when below 2
            if (checked.length === 2) {
                lmiCheckboxes.forEach(cb => {
                    if (!cb.checked) {
                        cb.disabled = true;
                        const wrapper = cb.closest('label') || cb.closest('.lmi-other-option');
                        if (wrapper) { wrapper.style.opacity = '0.4'; wrapper.style.cursor = 'not-allowed'; }
                    }
                });
            } else {
                lmiCheckboxes.forEach(cb => {
                    cb.disabled = false;
                    const wrapper = cb.closest('label') || cb.closest('.lmi-other-option');
                    if (wrapper) { wrapper.style.opacity = ''; wrapper.style.cursor = ''; }
                });
            }

            // Toggle "Other" text input
            if (lmiOtherCheckbox && lmiOtherInput) {
                lmiOtherCheckbox.checked
                    ? lmiOtherInput.classList.remove('hidden')
                    : lmiOtherInput.classList.add('hidden');
            }
        });
    });

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

        });
    });

    // Export analysis button
    const exportBtn = document.querySelector('.export-analysis-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            alert('Export functionality would be implemented here.');
        });
    }

}); // end DOMContentLoaded
    </script>
    <script>function toggleRoleDetails(submissionId, roleIndex) {
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
}</script>
    
<script>
function filterSkills(sector) {
    // Update active tab styling
    document.querySelectorAll('.sector-tab').forEach(tab => {
        if (tab.getAttribute('data-sector') === sector) {
            tab.classList.add('bg-gray-900', 'text-white', 'shadow-sm');
            tab.classList.remove('border', 'border-gray-200', 'text-gray-500', 'bg-white', 'hover:border-gray-900', 'hover:text-gray-900');
        } else {
            tab.classList.remove('bg-gray-900', 'text-white', 'shadow-sm');
            tab.classList.add('border', 'border-gray-200', 'text-gray-500', 'bg-white', 'hover:border-gray-900', 'hover:text-gray-900');
        }
    });

    // Filter skill tags
    document.querySelectorAll('.skill-tag').forEach(tag => {
        const tagSector = tag.getAttribute('data-sector');
        tag.style.display = (sector === 'All' || tagSector === sector) ? 'flex' : 'none';
    });
}

// Arrow buttons + mouse wheel scroll for filter bar
document.addEventListener('DOMContentLoaded', function () {
    const scroll = document.getElementById('sector-filter-scroll');
    const left   = document.getElementById('filter-left');
    const right  = document.getElementById('filter-right');

    if (scroll && left && right) {
        left.addEventListener('click',  () => scroll.scrollBy({ left: -200, behavior: 'smooth' }));
        right.addEventListener('click', () => scroll.scrollBy({ left:  200, behavior: 'smooth' }));

        // Mouse wheel scrolls horizontally
        scroll.addEventListener('wheel', function (e) {
            if (e.deltaY !== 0) {
                e.preventDefault();
                scroll.scrollBy({ left: e.deltaY * 2, behavior: 'smooth' });
            }
        }, { passive: false });
    }
});
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
    suggestionsDiv.className = 'autocomplete-suggestions absolute w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-60 overflow-y-auto hidden';
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
            // Bug fix: hide the dropdown silently when there are no matches
            // instead of showing an annoying "No result found" message.
            suggestionsDiv.innerHTML = '';
            suggestionsDiv.classList.add('hidden');
            return;
        }
        
        // Display suggestions (limit to 10)
        suggestionsDiv.innerHTML = '';
        matches.slice(0, 10).forEach(item => {
            const suggestionItem = document.createElement('div');
            suggestionItem.className = 'px-4 py-2.5 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 border-b border-gray-100 last:border-b-0 transition';
            
            // Highlight matching text
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            const highlightedItem = item.replace(regex, '<span class="font-semibold text-teal-600">$1</span>');
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
            suggestions[currentIndex].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            suggestions[currentIndex]?.classList.remove('bg-teal-100', 'bg-teal-50');
            currentIndex = currentIndex > 0 ? currentIndex - 1 : suggestions.length - 1;
            suggestions[currentIndex].classList.add('bg-teal-100');
            suggestions[currentIndex].scrollIntoView({ block: 'nearest' });
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
        observer.observe(container, { childList: true, subtree: true });
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
    csvData.push(['Job Title', 'Classification', 'Vacancy Duration', 'Difficulty Reasons', 'Technical Skills', 'Soft Skills']);
    
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
            const techSkillsContainer = Array.from(detailsDiv.querySelectorAll('span.font-medium.text-slate-600'))
                .find(span => span.textContent.includes('Technical Skills'));
            if (techSkillsContainer) {
                const skillTags = techSkillsContainer.parentElement.querySelectorAll('span.bg-blue-100');
                techSkills = Array.from(skillTags).map(tag => tag.textContent.trim()).join('; ');
            }
            
            // Soft Skills
            const softSkillsContainer = Array.from(detailsDiv.querySelectorAll('span.font-medium.text-slate-600'))
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
    const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
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
        // Helper function to format salary range with peso sign and thousand separators
        function formatSalaryRange(salaryRange) {
            if (!salaryRange || salaryRange === 'Not specified') {
                return salaryRange;
            }
            
            // Convert to string if it's a number
            let salaryStr = String(salaryRange);
            
            // If it contains a range (e.g., "30000 - 59999" or "30000-59999")
            if (salaryStr.includes('-')) {
                // Split by dash, allowing spaces around it
                let parts = salaryStr.split(/\s*-\s*/);
                
                if (parts.length === 2) {
                    // Format each part
                    let min = parts[0].trim().replace(/[₱,]/g, ''); // Remove existing ₱ and commas
                    let max = parts[1].trim().replace(/[₱,]/g, '');
                    
                    // Check if they're valid numbers
                    if (!isNaN(min) && !isNaN(max)) {
                        min = Number(min).toLocaleString();
                        max = Number(max).toLocaleString();
                        return '₱' + min + ' - ₱' + max;
                    }
                }
            }
            
            // If it's a single number or already formatted
            let cleaned = salaryStr.replace(/[₱,]/g, ''); // Remove existing ₱ and commas
            
            // Check if it's a valid number
            if (!isNaN(cleaned) && cleaned.trim() !== '') {
                let formatted = Number(cleaned).toLocaleString();
                return '₱' + formatted;
            }
            
            // If already has peso sign or is text (like "Below ₱30,000"), return as is
            if (salaryStr.includes('₱')) {
                return salaryStr;
            }
            
            // Default: just add peso sign
            return '₱' + salaryStr;
        }
        
        // Process matrix results to add peso sign to salary ranges
        let matrixResultsRaw = @json($matrix_results);
        window.matrixResultsData = matrixResultsRaw.map(result => ({
            ...result,
            salary_range: formatSalaryRange(result.salary_range)
        }));

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
    const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
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
       document.addEventListener('DOMContentLoaded', function () {

    const steps    = document.querySelectorAll('.lmi-step');   // the 4 <div> wrappers
    const circles  = document.querySelectorAll('.step-circle');
    const lines    = document.querySelectorAll('.step-line');
    let current    = 0;

    // ─── INIT: hide all except first ────────────────────────
    window.showStep = function showStep(n) {
    steps.forEach((s, i) => s.style.display = (i === n) ? 'block' : 'none');
    current = n;
    updateIndicator();
    updateButtons();
    
    // ► HIDE INTRO SECTION AFTER STEP 1 ◄
    const introSection = document.getElementById('intro-section');
    if (introSection) {
        introSection.style.display = (n === 0) ? 'block' : 'none';
    }
    
    // scroll modal back to top
    const scrollable = document.querySelector('#lmi-form-content .overflow-y-auto');
    if (scrollable) scrollable.scrollTo({ top: 0, behavior: 'smooth' });
}

    // ─── INDICATOR ──────────────────────────────────────────
    function updateIndicator() {
        circles.forEach((c, i) => {
            c.classList.remove('bg-white','text-teal-700','bg-teal-500','text-white');
            if (i < current) {
                c.classList.add('bg-white','text-teal-700');
                c.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
            } else if (i === current) {
                c.classList.add('bg-white','text-teal-700');
                c.innerHTML = (i + 1).toString();
            } else {
                c.classList.add('bg-teal-500','text-white');
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
            const prev   = step.querySelector('.btn-prev');
            const next   = step.querySelector('.btn-next');
            const submit = step.querySelector('.btn-submit-lmi');

            if (prev)   prev.style.display   = (i === 0) ? 'none' : 'inline-flex';
            if (next)   next.style.display   = (i === steps.length - 1) ? 'none' : 'inline-flex';
            if (submit) submit.style.display = (i === steps.length - 1) ? 'inline-flex' : 'none';
        });
    }

    // ─── VALIDATION ─────────────────────────────────────────
    function validateStep(idx) {
        const step  = steps[idx];
        let   valid = true;

        // -- text / email / tel --
        step.querySelectorAll('input[type="text"][required], input[type="email"][required], input[type="tel"][required]').forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('border-red-500');
                valid = false;
            } else {
                input.classList.remove('border-red-500');
            }
        });

        // -- Contact number length check (step 0 only) --
        if (idx === 0) {
            const contactType   = document.getElementById('contact_type_input');
            const mobileInp     = document.getElementById('mobile-input');
            const telephoneInp  = document.getElementById('telephone-input');
            const contactHint   = document.getElementById('contact-hint');

            if (contactType && contactType.value === 'mobile' && mobileInp && !mobileInp.disabled) {
                const digits = mobileInp.value.replace(/\D/g, '');
                if (digits.length !== 10) {
                    mobileInp.classList.add('border-red-500');
                    if (contactHint) {
                        contactHint.innerHTML = '<svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span class="text-red-500">Mobile number must be exactly 10 digits</span>';
                    }
                    valid = false;
                } else {
                    mobileInp.classList.remove('border-red-500');
                    if (contactHint) {
                        contactHint.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> 10-digit mobile number';
                    }
                }
            } else if (contactType && contactType.value === 'telephone' && telephoneInp && !telephoneInp.disabled) {
                const digits = telephoneInp.value.replace(/\D/g, '');
                if (digits.length !== 10) {
                    telephoneInp.classList.add('border-red-500');
                    if (contactHint) {
                        contactHint.innerHTML = '<svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span class="text-red-500">Telephone number must be exactly 10 digits</span>';
                    }
                    valid = false;
                } else {
                    telephoneInp.classList.remove('border-red-500');
                    if (contactHint) {
                        contactHint.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> 10-digit telephone number';
                    }
                }
            }
        }

        // -- Email format check (step 0 only) --
        if (idx === 0) {
            const emailInput = step.querySelector('input[type="email"]');
            const emailError = document.getElementById('emailError');
            if (emailInput && emailInput.value.trim()) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value.trim())) {
                    emailInput.classList.add('border-red-500');
                    if (emailError) emailError.classList.remove('hidden');
                    valid = false;
                } else {
                    emailInput.classList.remove('border-red-500');
                    if (emailError) emailError.classList.add('hidden');
                }
            } else if (emailInput && !emailInput.value.trim()) {
                if (emailError) emailError.classList.add('hidden');
            }
        }

        // -- hidden inputs (dropdowns: industrySelector, companySize, job_classification, vacancy_duration, salary_range) --
        step.querySelectorAll('input[type="hidden"][required]').forEach(input => {
            const wrapper = input.closest('.relative');
            const btn     = wrapper ? wrapper.querySelector('button[type="button"]') : null;
            if (!input.value) {
                valid = false;
                if (btn) btn.classList.add('border-red-500');
            } else {
                if (btn) btn.classList.remove('border-red-500');
            }
        });

        // -- Step 1 (idx 0): Special validation for salary range and "Below ₱30,000" input --
        if (idx === 0) {
            step.querySelectorAll('.job-entry').forEach(jobEntry => {
                const salaryRangeInput = jobEntry.querySelector('.salary-range-input');
                const salaryRangeBtn = jobEntry.querySelector('.salary-range-btn');
                const below30kInput = jobEntry.querySelector('.below-30k-salary-input');
                const below30kContainer = jobEntry.querySelector('.below-30k-input-container');
                
                // Check if salary range is selected
                if (salaryRangeInput && !salaryRangeInput.value) {
                    valid = false;
                    if (salaryRangeBtn) salaryRangeBtn.classList.add('border-red-500');
                } else {
                    if (salaryRangeBtn) salaryRangeBtn.classList.remove('border-red-500');
                    
                    // If "Below ₱30,000" is selected, validate the input field
                    if (salaryRangeInput && salaryRangeInput.value === 'Below ₱30,000') {
                        if (below30kInput && !below30kInput.value.trim()) {
                            valid = false;
                            below30kInput.classList.add('border-red-500');
                        } else if (below30kInput) {
                            below30kInput.classList.remove('border-red-500');
                            
                            // Validate that the amount is less than 30,000
                            const amount = parseInt(below30kInput.value.replace(/,/g, ''));
                            if (isNaN(amount) || amount >= 30000) {
                                valid = false;
                                below30kInput.classList.add('border-red-500');
                                alert('Salary amount must be less than ₱30,000');
                            }
                        }
                    }
                }
            });
        }

        // -- radio groups --
        const radioNames = new Set();
        step.querySelectorAll('input[type="radio"][required]').forEach(r => radioNames.add(r.name));
        radioNames.forEach(name => {
            const checked = step.querySelector(`input[type="radio"][name="${name}"]:checked`);
            const radios  = step.querySelectorAll(`input[type="radio"][name="${name}"]`);
            if (!checked) {
                valid = false;
                radios.forEach(r => { const lbl = r.closest('label'); if (lbl) lbl.classList.add('border-red-500'); });
            } else {
                radios.forEach(r => { const lbl = r.closest('label'); if (lbl) lbl.classList.remove('border-red-500'); });
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
            if (bad) bad.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return valid;
    }

    // ─── BIND NEXT / PREV clicks ────────────────────────────
    steps.forEach((step, i) => {
        const next = step.querySelector('.btn-next');
        const prev = step.querySelector('.btn-prev');

        if (next) {
            next.addEventListener('click', function () {
                if (validateStep(i)) showStep(i + 1);
            });
        }
        if (prev) {
            prev.addEventListener('click', function () {
                if (i > 0) showStep(i - 1);
            });
        }
    });
        

    
  
    // ─── INIT ───────────────────────────────────────────────
    showStep(0);
});
</script>
    <script>
// ─── Contact Number Toggle ────────────────────────────────────────────────────
function switchContactType(type) {
    const mobileWrapper    = document.getElementById("mobile-input-wrapper");
    const telephoneWrapper = document.getElementById("telephone-input-wrapper");
    const mobileInput      = document.getElementById("mobile-input");
    const telephoneInput   = document.getElementById("telephone-input");
    const hint             = document.getElementById("contact-hint");
    const contactTypeInput = document.getElementById("contact_type_input");
    const toggleMobile     = document.getElementById("toggle-mobile");
    const toggleTelephone  = document.getElementById("toggle-telephone");

    [toggleMobile, toggleTelephone].forEach(btn => {
        btn.classList.remove("bg-white", "text-teal-700", "shadow-sm", "border", "border-gray-200");
        btn.classList.add("text-gray-500");
    });

    if (type === "mobile") {
        mobileWrapper.classList.remove("hidden");
        telephoneWrapper.classList.add("hidden");
        mobileInput.disabled = false;
        mobileInput.required = true;
        telephoneInput.disabled = true;
        telephoneInput.required = false;
        telephoneInput.value = "";
        hint.innerHTML = "<svg class=\"w-3 h-3\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"/></svg> 10-digit mobile number";
        contactTypeInput.value = "mobile";
        toggleMobile.classList.add("bg-white", "text-teal-700", "shadow-sm", "border", "border-gray-200");
        toggleMobile.classList.remove("text-gray-500");
    } else {
        telephoneWrapper.classList.remove("hidden");
        mobileWrapper.classList.add("hidden");
        telephoneInput.disabled = false;
        telephoneInput.required = true;
        mobileInput.disabled = true;
        mobileInput.required = false;
        mobileInput.value = "";
        hint.innerHTML = "<svg class=\"w-3 h-3\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"/></svg> Auto-formats to 082-123-4567";
        contactTypeInput.value = "telephone";
        toggleTelephone.classList.add("bg-white", "text-teal-700", "shadow-sm", "border", "border-gray-200");
        toggleTelephone.classList.remove("text-gray-500");
        telephoneInput.focus();
    }
}

// ─── Telephone Auto-Formatter + Area Code Suggestions ────────────────────────
document.addEventListener("DOMContentLoaded", function () {
    const telInput   = document.getElementById("telephone-input");
    const suggestBox = document.getElementById("area-code-suggestions");
    const suggestList= document.getElementById("area-code-list");
    if (!telInput) return;

    // ── Complete PH Area Code Directory ──────────────────────────────────────
    // Format: { code: "0XX", label: "Province / City" }
    // Source: Wikipedia "Telephone numbers in the Philippines" + NTC 2025
    const PH_AREA_CODES = [
        // Metro Manila & surroundings (area code 2 — 8-digit local)
        { code: "02", label: "Metro Manila, Rizal, Bacoor, San Pedro" },

        // Luzon
        { code: "032", label: "Cebu" },
        { code: "033", label: "Guimaras, Iloilo (part)" },
        { code: "034", label: "Iloilo, Negros Occidental" },
        { code: "035", label: "Negros Oriental, Siquijor" },
        { code: "036", label: "Aklan, Antique, Capiz" },
        { code: "038", label: "Bohol" },
        { code: "042", label: "Aurora, Marinduque, Quezon" },
        { code: "043", label: "Batangas, Occidental Mindoro, Oriental Mindoro" },
        { code: "044", label: "Bulacan, Nueva Ecija" },
        { code: "045", label: "Pampanga, Tarlac" },
        { code: "046", label: "Cavite (except Bacoor)" },
        { code: "047", label: "Bataan, Zambales" },
        { code: "048", label: "Palawan" },
        { code: "049", label: "Laguna (except San Pedro)" },
        { code: "052", label: "Albay, Catanduanes" },
        { code: "053", label: "Biliran, Leyte, Southern Leyte" },
        { code: "054", label: "Camarines Norte, Camarines Sur, Romblon" },
        { code: "055", label: "Eastern Samar, Northern Samar, Samar" },
        { code: "056", label: "Masbate, Sorsogon" },
        { code: "062", label: "Basilan, Zamboanga del Sur, Zamboanga Sibugay" },
        { code: "063", label: "Lanao del Norte" },
        { code: "064", label: "Lanao del Sur, Maguindanao, North Cotabato, Sultan Kudarat" },
        { code: "065", label: "Zamboanga del Norte" },
        { code: "068", label: "Tawi-Tawi" },
        { code: "072", label: "La Union" },
        { code: "074", label: "Abra, Benguet, Ifugao, Kalinga, Mountain Province" },
        { code: "075", label: "Pangasinan" },
        { code: "077", label: "Ilocos Norte, Ilocos Sur" },
        { code: "078", label: "Apayao, Batanes, Cagayan, Isabela, Nueva Vizcaya, Quirino" },

        // Mindanao
        { code: "082", label: "Davao del Sur, Davao Occidental" },
        { code: "083", label: "Sarangani, South Cotabato" },
        { code: "084", label: "Compostela Valley, Davao del Norte" },
        { code: "085", label: "Agusan del Norte, Agusan del Sur, Sulu" },
        { code: "086", label: "Dinagat Islands, Surigao del Norte, Surigao del Sur" },
        { code: "087", label: "Davao de Oro, Davao Oriental" },
        { code: "088", label: "Bukidnon, Camiguin, Misamis Occidental, Misamis Oriental" },
    ];

    // ── Format telephone digits → readable string ─────────────────────────────
    // Area code "2"  (Metro Manila): 02-XXXX-XXXX  (1+8 digits)
    // All others:                    0XX-XXX-XXXX   (2+7 digits)
    function formatTelephone(digits) {
        if (!digits) return "";
        if (!digits.startsWith("0")) digits = "0" + digits;

        const withoutTrunk = digits.slice(1);

        if (withoutTrunk.startsWith("2")) {
            const local = withoutTrunk.slice(1);
            if (local.length === 0) return "02";
            if (local.length <= 4)  return "02-" + local;
            return "02-" + local.slice(0, 4) + "-" + local.slice(4);
        }

        const area  = withoutTrunk.slice(0, 2);
        const local = withoutTrunk.slice(2);
        if (local.length === 0) return "0" + area;
        if (local.length <= 3)  return "0" + area + "-" + local;
        return "0" + area + "-" + local.slice(0, 3) + "-" + local.slice(3);
    }

    // ── Show/hide suggestion dropdown ─────────────────────────────────────────
    let activeIndex = -1; // for keyboard navigation

    function showSuggestions(typedDigits) {
        if (!typedDigits || typedDigits.length < 2) {
            hideSuggestions();
            return;
        }

        // Only show suggestions while user is still typing the area code
        // (i.e. total digits typed is 3 or less — "0", "08", "082")
        // Once they go past the area code into local number, hide suggestions
        if (typedDigits.length > 3) {
            hideSuggestions();
            return;
        }

        const matches = PH_AREA_CODES.filter(ac =>
            ac.code.startsWith(typedDigits)
        );

        if (matches.length === 0) {
            hideSuggestions();
            return;
        }

        suggestList.innerHTML = "";
        activeIndex = -1;

        matches.forEach((ac, i) => {
            const item = document.createElement("div");
            item.className = "suggestion-item flex items-center gap-3 px-4 py-2.5 hover:bg-teal-50 cursor-pointer border-b border-gray-50 last:border-b-0 transition-colors";
            item.dataset.index = i;

            // Highlight the matching part of the area code
            const typed      = typedDigits;
            const codeHtml   = `<span class="font-bold text-teal-600">${ac.code.slice(0, typed.length)}</span><span class="font-bold text-gray-800">${ac.code.slice(typed.length)}</span>`;

            item.innerHTML = `
                <span class="shrink-0 text-xs font-mono bg-teal-50 text-teal-700 border border-teal-200 rounded px-2 py-0.5">${codeHtml}</span>
                <span class="text-sm text-gray-600 truncate">${ac.label}</span>
            `;

            item.addEventListener("mousedown", function (e) {
                e.preventDefault(); // prevent input blur before click fires
                selectAreaCode(ac);
            });

            suggestList.appendChild(item);
        });

        suggestBox.classList.remove("hidden");
    }

    function hideSuggestions() {
        suggestBox.classList.add("hidden");
        activeIndex = -1;
    }

    function selectAreaCode(ac) {
        // Fill the input with the area code + dash, ready for local number
        // e.g. selecting "082" → input becomes "082-"
        telInput.value = ac.code + "-";
        hideSuggestions();
        telInput.focus();
    }

    // ── Keyboard navigation through suggestions ───────────────────────────────
    function navigateSuggestions(direction) {
        const items = suggestList.querySelectorAll(".suggestion-item");
        if (items.length === 0) return;

        // Remove highlight from current
        items.forEach(i => i.classList.remove("bg-teal-50"));

        activeIndex += direction;
        if (activeIndex < 0)             activeIndex = items.length - 1;
        if (activeIndex >= items.length) activeIndex = 0;

        items[activeIndex].classList.add("bg-teal-50");
        items[activeIndex].scrollIntoView({ block: "nearest" });
    }

    // ── Event Listeners ───────────────────────────────────────────────────────
    telInput.addEventListener("input", function (e) {
        let digits = e.target.value.replace(/\D/g, "");
        if (digits.length > 10) digits = digits.slice(0, 10);

        // Show suggestions only when typing area code portion
        showSuggestions(digits.length <= 3 ? digits : null);

        // Format the number
        e.target.value = formatTelephone(digits);
    });

    telInput.addEventListener("keydown", function (e) {
        // Navigation keys for suggestion dropdown
        if (!suggestBox.classList.contains("hidden")) {
            if (e.key === "ArrowDown") { e.preventDefault(); navigateSuggestions(1);  return; }
            if (e.key === "ArrowUp")   { e.preventDefault(); navigateSuggestions(-1); return; }
            if (e.key === "Enter") {
                e.preventDefault();
                const items = suggestList.querySelectorAll(".suggestion-item");
                if (activeIndex >= 0 && items[activeIndex]) {
                    const code  = items[activeIndex].querySelector("span").textContent.trim();
                    const match = PH_AREA_CODES.find(ac => ac.code === items[activeIndex].querySelector("span").textContent.replace(/\s/g,'').replace(/[^0-9]/g,'') );
                    // Simpler: just click the active item
                    items[activeIndex].dispatchEvent(new MouseEvent("mousedown"));
                }
                return;
            }
            if (e.key === "Escape") { hideSuggestions(); return; }
        }

        // Block non-numeric keys
        const allowedKeys = ["Backspace","Delete","ArrowLeft","ArrowRight",
                             "ArrowUp","ArrowDown","Tab","Home","End"];
        const isDigit = e.key >= "0" && e.key <= "9";
        const isCtrl  = e.ctrlKey || e.metaKey;
        if (!isDigit && !allowedKeys.includes(e.key) && !isCtrl) {
            e.preventDefault();
        }
    });

    telInput.addEventListener("paste", function (e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData("text");
        let digits   = pasted.replace(/\D/g, "").slice(0, 10);
        e.target.value = formatTelephone(digits);
        hideSuggestions();
    });

    // Hide suggestions when clicking outside
    document.addEventListener("click", function (e) {
        if (!telInput.contains(e.target) && !suggestBox.contains(e.target)) {
            hideSuggestions();
        }
    });

    telInput.addEventListener("blur", function () {
        // Small delay so mousedown on suggestion fires first
        setTimeout(hideSuggestions, 150);
    });
});
</script>
</body>
</html>