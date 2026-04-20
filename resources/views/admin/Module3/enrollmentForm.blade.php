<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>
    <title>LMI - Discipline Enrollment Form</title>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden">
    @include('partials.sidebar')
        
    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- HEADER -->
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm shrink-0">
            <h2 class="text-xl font-bold text-slate-800">Enrollment Form • Admin</h2>
            <div class="flex items-center gap-4">
                <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200">
                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Region XI • 2024
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>
        <!-- Main Form Area -->
        <div class="flex-1 overflow-auto p-8">
            <div class="max-w-5xl mx-auto">
                <!-- Year Selection Card -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Select Academic Year</h3>
                            <p class="text-sm text-gray-600">Enter an academic year to create new data or edit existing data</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div id="yearInputGroup" class="flex items-center gap-4">
                                <input 
                                    type="text" 
                                    id="academicYear" 
                                    placeholder="e.g. 2024-2025" 
                                    pattern="\d{4}-\d{4}"
                                    required 
                                    class="w-48 px-4 py-3 border-2 border-gray-300 rounded-lg text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                <button 
                                    type="button"
                                    onclick="checkAndLoadYear()"
                                    class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Check Year
                                </button>
                            </div>
                            <div id="yearDisplay" class="hidden flex items-center gap-3">
                                <span id="displayYear" class="text-2xl font-bold text-blue-600">----</span>
                            </div>
                            <button 
                                type="button"
                                id="cancelYearChangeBtn"
                                onclick="cancelYearChange()"
                                class="hidden px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2 text-sm"
                                title="Enter a different year"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Province and Institution Type Selection Card -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Institution Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Province Selection -->
                        <div>
                            <label for="province" class="block text-sm font-semibold text-gray-700 mb-2">
                                Province
                            </label>
                            <select 
                                id="province" 
                                name="province" 
                                required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg text-base font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="" disabled hidden selected>Select Province</option>
                                <option value="Davao Region">Davao Region</option>
                                <option value="Davao City">Davao City</option>
                                <option value="Davao del Sur">Davao del Sur</option>
                                <option value="Davao del Norte">Davao del Norte</option>
                                <option value="Davao de Oro">Davao de Oro</option>
                                <option value="Davao Oriental">Davao Oriental</option>
                                <option value="Davao Occidental">Davao Occidental</option>
                            </select>
                        </div>

                        <!-- Institution Type Selection -->
                        <div id="institutionTypeWrapper">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Institution Type
                            </label>
                            <div id="institutionTypeRadios" class="flex gap-6 h-12 items-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="institution_type" 
                                        value="Private" 
                                        class="w-5 h-5 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                    >
                                    <span class="ml-3 text-base font-medium text-gray-700">Private</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="institution_type" 
                                        value="Public" 
                                        class="w-5 h-5 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                    >
                                    <span class="ml-3 text-base font-medium text-gray-700">Public</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Notification -->
                <div id="statusNotification" class="hidden mb-8 p-6 rounded-2xl shadow-lg">
                    <div class="flex items-start gap-4">
                        <div id="statusIcon" class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full"></div>
                        <div class="flex-1">
                            <h4 id="statusTitle" class="text-lg font-bold mb-1"></h4>
                            <p id="statusMessage" class="text-sm"></p>
                        </div>
                    </div>
                </div>

                <!-- Lock Banner -->
                <div id="lockBanner" class="mb-4 flex items-center gap-3 bg-amber-50 border-2 border-amber-300 rounded-xl px-5 py-3">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <p id="lockBannerText" class="text-sm font-semibold text-amber-800">Select a <strong>Province</strong> and <strong>Institution Type</strong> above, enter the academic year, then click <strong>Check Year</strong> to unlock the enrollment fields.</p>
                </div>

                <!-- Form locked until year is checked -->
                <div id="formContent" class="relative opacity-50 pointer-events-none select-none">
                <div id="formBlocker" class="absolute inset-0 z-10 cursor-not-allowed rounded-2xl bg-transparent"></div>

                   <!-- Form Content -->
<form id="disciplineForm">
    <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Discipline Enrollment Data</h2>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800">
                <strong>Note:</strong> Enter the enrollment count for each discipline. Leave blank if no data is available.
            </p>
        </div>

        <!-- Search Bar -->
        <div class="relative mb-4">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input 
                type="text" 
                id="disciplineSearch" 
                placeholder="Search discipline..." 
                oninput="filterDisciplines(this.value)"
                class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
        </div>

        <!-- No results message -->
        <div id="noResultsMsg" class="hidden text-center py-6 text-sm text-gray-500 italic">No disciplines match your search.</div>

        <div class="space-y-3 overflow-y-auto max-h-[420px] pr-1" id="disciplineList">
            <!-- Agriculture, Forestry, Fisheries -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Agricultural, Forestry, and Fisheries</label>
                <input 
                    type="text" 
                    name="agriculture" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Architecture and Town Planning -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Architecture and Town Planning</label>
                <input 
                    type="text" 
                    name="architecture" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Business Administration -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Business Administration and Related</label>
                <input 
                    type="text" 
                    name="business" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Criminal Justice -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Criminal Justice / Criminology</label>
                <input 
                    type="text" 
                    name="criminal_justice" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Education Science -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Education Science and Teacher Training</label>
                <input 
                    type="text" 
                    name="education" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Engineering -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Engineering and Technology</label>
                <input 
                    type="text" 
                    name="engineering" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Fine and Applied Arts -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Fine and Applied Arts</label>
                <input 
                    type="text" 
                    name="arts" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- General -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">General</label>
                <input 
                    type="text" 
                    name="general" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Home Economics -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Home Economics</label>
                <input 
                    type="text" 
                    name="home_economics" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Humanities -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Humanities</label>
                <input 
                    type="text" 
                    name="humanities" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Information Technology -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">IT-Related Disciplines</label>
                <input 
                    type="text" 
                    name="it" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Law -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Law and Jurisprudence</label>
                <input 
                    type="text" 
                    name="law" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Maritime -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Maritime</label>
                <input 
                    type="text" 
                    name="maritime" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Mass Communication -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Mass Communication and Documentation</label>
                <input 
                    type="text" 
                    name="mass_comm" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Mathematics -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Mathematics</label>
                <input 
                    type="text" 
                    name="mathematics" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Medical and Health -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Medical and Allied</label>
                <input 
                    type="text" 
                    name="medical" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Natural Science -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Natural Science</label>
                <input 
                    type="text" 
                    name="natural_science" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Other Disciplines -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Other Disciplines</label>
                <input 
                    type="text" 
                    name="other_disciplines" 
                    placeholder="0"  
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Religion -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Religion and Theology</label>
                <input 
                    type="text" 
                    name="religion" 
                    placeholder="0" 
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Service Trades -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Service Trades</label>
                <input 
                    type="text" 
                    name="service_trades" 
                    placeholder="0" 
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Social Sciences -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Social and Behavioral Sciences</label>
                <input 
                    type="text" 
                    name="social_sciences" 
                    placeholder="0" 
                                        inputmode="numeric" class="discipline-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

        </div><!-- end disciplineList -->

        <div class="mt-8 pt-6 border-t-2 border-gray-300">
            <div class="flex justify-between items-center bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-xl border-2 border-blue-200">
                <span class="text-lg font-bold text-gray-900">Grand Total Enrollments:</span>
                <span id="grandTotal" class="text-3xl font-bold text-blue-600">0</span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-4 justify-end mb-8">
        <button 
            type="button"
            onclick="confirmReset()"
            class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg"
        >
            Reset Form
        </button>
        <button 
            type="submit"
            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Submit Enrollment Data
        </button>
    </div>
</form>
                </div><!-- end formContent -->
            </div>
        </div>
    </div>

    <!-- MODAL: Existing Data Found -->
    <div id="existingDataModal" class="hidden fixed inset-0 backdrop-blur-sm bg-white/30 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900">Existing Data Found</h3>
                        <p class="text-sm text-gray-600 mt-1">This year already has enrollment data</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-blue-900">Year: <span id="existingDataYear" class="text-blue-700"></span></p>
                            <p class="text-sm text-blue-800 mt-1">Province: <span id="existingDataProvince"></span></p>
                            <p class="text-sm text-blue-800">Type: <span id="existingDataType"></span></p>
                        </div>
                    </div>
                </div>
                
                <p class="text-gray-700 mb-6">
                    Data already exists for this academic year. Would you like to <strong>edit the existing data</strong> or <strong>create new data</strong> for a different year?
                </p>
                
                <div class="flex flex-col gap-3">
                    <button 
                        onclick="confirmLoadExistingData()"
                        class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Yes, Edit Existing Data
                    </button>
                    <button 
                        onclick="closeExistingDataModal()"
                        class="w-full px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        No, Enter Different Year
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: Confirm Year Change -->
    <div id="changeYearModal" class="hidden fixed inset-0 backdrop-blur-sm bg-white/30 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900">Confirm Year Change</h3>
                        <p class="text-sm text-gray-600 mt-1">Are you sure you want to change the year?</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-4">
                    <p class="font-semibold text-amber-900">Current Year: <span id="changeYearCurrent" class="text-amber-700"></span></p>
                </div>
                
                <p class="text-gray-700 mb-2">
                    Changing the year will clear the current form data. Any unsaved changes will be lost.
                </p>
                <p class="text-sm text-gray-600 mb-6">
                    Make sure you've saved your current work before proceeding.
                </p>
                
                <div class="flex gap-3">
                    <button 
                        onclick="closeChangeYearModal()"
                        class="flex-1 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg"
                    >
                        Cancel
                    </button>
                    <button 
                        onclick="confirmChangeYear()"
                        class="flex-1 px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg"
                    >
                        Yes, Change Year
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: Year Collision (when changing TO an existing year) -->
    <div id="yearCollisionModal" class="hidden fixed inset-0 backdrop-blur-sm bg-white/30 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900">Year Already Has Data</h3>
                        <p class="text-sm text-gray-600 mt-1">Cannot change to a year with existing data</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="space-y-3 mb-4">
                    <div class="bg-red-50 border-l-4 border-red-500 p-4">
                        <p class="font-semibold text-red-900">Target Year: <span id="collisionTargetYear" class="text-red-700"></span></p>
                    </div>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                        <p class="font-semibold text-blue-900">Current Year: <span id="collisionCurrentYear" class="text-blue-700"></span></p>
                    </div>
                </div>
                
                <p class="text-gray-700 mb-6">
                    The year you're trying to switch to already contains enrollment data. You cannot change to this year as it would cause data conflicts. Please choose a different year or edit the existing data directly.
                </p>
                
                <button 
                    onclick="closeYearCollisionModal()"
                    class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg"
                >
                    Understood, Choose Different Year
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: Confirm Submit -->
    <div id="confirmModal" class="hidden fixed inset-0  backdrop-blur-sm bg-white/30 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                <h3 class="text-2xl font-bold text-gray-900">Confirm Submission</h3>
                <p class="text-sm text-gray-600 mt-1">Please review your data before submitting</p>
            </div>
            
            <div class="p-6">
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 border-l-4 border-blue-600 p-4 mb-6">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="font-semibold text-gray-900">
                            You are about to <span id="actionType" class="text-blue-700"></span> data for:
                        </p>
                    </div>
                    <p class="font-bold text-lg text-gray-900" id="confirmYear"></p>
                    <p class="text-sm text-gray-700">Province: <span id="confirmProvince"></span></p>
                    <p class="text-sm text-gray-700">Institution Type: <span id="confirmInstitutionType"></span></p>
                </div>

                <div id="deletionWarning" class="hidden bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <p class="text-sm font-semibold text-red-800">
                        <svg class="w-4 h-4 inline-block mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> <span id="deletionWarningText">This will replace the existing data for this academic year!</span>
                    </p>
                </div>
                
                <h4 class="font-semibold text-gray-900 mb-3">Enrollment Summary:</h4>
                <div id="dataSummary" class="space-y-2 mb-6 max-h-96 overflow-y-auto"></div>
                
                <div class="bg-gray-100 rounded-lg p-4 flex justify-between items-center">
                    <span class="font-bold text-gray-900">Grand Total:</span>
                    <span id="confirmGrandTotal" class="text-2xl font-bold text-blue-600"></span>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 bg-gray-50 flex gap-3">
                <button 
                    onclick="closeConfirmModal()"
                    class="flex-1 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg"
                >
                    Cancel
                </button>
                <button 
                    onclick="confirmSubmit()"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg"
                >
                    <span id="confirmActionWarning">save</span> Data
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: Success -->
    <div id="successModal" class="hidden fixed inset-0 backdrop-blur-sm bg-white/30 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-600 mb-6">Enrollment data has been submitted successfully.</p>
                <button 
                    onclick="closeSuccessModal()"
                    class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg"
                >
                    Continue
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none" style="min-width: 340px;"></div>


    {{-- Blade route data passed to JS --}}
    <script>
        window.AppRoutes = {
            storeEnrollment: "{{ route('admin.discipline-enrollment.store') }}"
        };
    </script>
    @vite('resources/js/admin/Module3/enrollment-form.js')


    <!-- Reset Confirmation Modal -->
    <div id="resetModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeResetModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-orange-100 mb-4">
                    <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3" id="resetModalTitle">Reset Form?</h3>
                <p class="text-sm text-gray-600 mb-6" id="resetModalMessage">Are you sure you want to reset all fields? All entered data will be lost and cannot be recovered.</p>
                <div class="flex gap-3">
                    <button onclick="closeResetModal()" class="flex-1 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium border border-gray-300 rounded-lg transition">
                        Cancel
                    </button>
                    <button onclick="doReset()" class="flex-1 px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition">
                        Yes, Reset
                    </button>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>