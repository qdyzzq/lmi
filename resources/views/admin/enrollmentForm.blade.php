<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                    📅 Region XI • 2024
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
                                <button 
                                    type="button"
                                    onclick="changeYear()"
                                    title="Change to a different year"
                                    class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2 text-sm"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Change Year
                                </button>
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
                                <option value="">Select Province</option>
                                <option value="All Provinces">All Provinces (Davao Region)</option>
                                <option value="Davao City">Davao City</option>
                                <option value="Davao del Sur">Davao del Sur</option>
                                <option value="Davao del Norte">Davao del Norte</option>
                                <option value="Davao de Oro">Davao de Oro</option>
                                <option value="Davao Oriental">Davao Oriental</option>
                                <option value="Davao Occidental">Davao Occidental</option>
                            </select>
                        </div>

                        <!-- Institution Type Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Institution Type
                            </label>
                            <div class="flex gap-6 h-12 items-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="institution_type" 
                                        value="Private" 
                                        required
                                        class="w-5 h-5 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                    >
                                    <span class="ml-3 text-base font-medium text-gray-700">Private</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="institution_type" 
                                        value="Public" 
                                        required
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

        <div class="space-y-3">
            <!-- Agriculture, Forestry, Fisheries -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Agriculture, Forestry, Fisheries</label>
                <input 
                    type="number" 
                    name="agriculture" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Architecture and Town Planning -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Architecture and Town Planning</label>
                <input 
                    type="number" 
                    name="architecture" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Business Administration -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Business Administration and Related</label>
                <input 
                    type="number" 
                    name="business" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Criminal Justice -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Criminal Justice</label>
                <input 
                    type="number" 
                    name="criminal_justice" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Education Science -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Education Science and Teacher Training</label>
                <input 
                    type="number" 
                    name="education" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Engineering -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Engineering and Engineering Trades</label>
                <input 
                    type="number" 
                    name="engineering" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Fine and Applied Arts -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Fine and Applied Arts</label>
                <input 
                    type="number" 
                    name="arts" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- General -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">General Programs</label>
                <input 
                    type="number" 
                    name="general" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Home Economics -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Home Economics</label>
                <input 
                    type="number" 
                    name="home_economics" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Humanities -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Humanities</label>
                <input 
                    type="number" 
                    name="humanities" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Information Technology -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Information Technology</label>
                <input 
                    type="number" 
                    name="it" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Law -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Law</label>
                <input 
                    type="number" 
                    name="law" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Maritime -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Maritime Education</label>
                <input 
                    type="number" 
                    name="maritime" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Mass Communication -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Mass Communication and Documentation</label>
                <input 
                    type="number" 
                    name="mass_comm" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Mathematics -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Mathematics and Statistics</label>
                <input 
                    type="number" 
                    name="mathematics" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Medical and Health -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Medical and Health Related</label>
                <input 
                    type="number" 
                    name="medical" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Natural Science -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Natural Science</label>
                <input 
                    type="number" 
                    name="natural_science" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Other Disciplines -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Other Disciplines</label>
                <input 
                    type="number" 
                    name="other_disciplines" 
                    placeholder="0"  
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Religion -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Religion and Theology</label>
                <input 
                    type="number" 
                    name="religion" 
                    placeholder="0" 
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Service Trades -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Service Trades</label>
                <input 
                    type="number" 
                    name="service_trades" 
                    placeholder="0" 
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Social Sciences -->
            <div class="grid grid-cols-1 md:grid-cols-[1fr_200px] gap-3 items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <label class="text-sm font-medium text-gray-900">Social and Behavioral Sciences</label>
                <input 
                    type="number" 
                    name="social_sciences" 
                    placeholder="0" 
                    min="0" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
        </div>

        <!-- Grand Total Display -->
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
                        ⚠️ This will replace the existing data for this academic year!
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

    <script>
        let existingData = null;
        let oldYear = null;
        let isChangingYear = false;
        let pendingData = null;
        let pendingYearData = null; // Store data when existing year is found

        // ─── Toast Notification System ──────────────────────────────────────────
        function showToast(message, type = 'error') {
            const container = document.getElementById('toastContainer');

            const configs = {
                error: {
                    bg: 'bg-red-50 border-red-400',
                    icon: `<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                           </svg>`,
                    text: 'text-red-800',
                    bar: 'bg-red-400',
                },
                warning: {
                    bg: 'bg-amber-50 border-amber-400',
                    icon: `<svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                           </svg>`,
                    text: 'text-amber-800',
                    bar: 'bg-amber-400',
                },
                success: {
                    bg: 'bg-green-50 border-green-400',
                    icon: `<svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                           </svg>`,
                    text: 'text-green-800',
                    bar: 'bg-green-400',
                },
                info: {
                    bg: 'bg-blue-50 border-blue-400',
                    icon: `<svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                           </svg>`,
                    text: 'text-blue-800',
                    bar: 'bg-blue-400',
                },
            };

            const c = configs[type] || configs.error;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto w-full border-l-4 ${c.bg} rounded-xl shadow-xl overflow-hidden
                               transform transition-all duration-300 translate-x-full opacity-0`;

            toast.innerHTML = `
                <div class="flex items-start gap-3 px-4 py-4">
                    ${c.icon}
                    <p class="text-sm font-medium ${c.text} flex-1 leading-snug">${message}</p>
                    <button onclick="this.closest('.pointer-events-auto').remove()"
                            class="text-gray-400 hover:text-gray-600 transition ml-1 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="h-1 ${c.bar} animate-shrink" style="animation: shrink 4s linear forwards;"></div>
            `;

            container.appendChild(toast);

            // Slide in
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                });
            });

            // Auto-remove after 4s
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // CSS for the shrink progress bar
        if (!document.getElementById('toastStyle')) {
            const style = document.createElement('style');
            style.id = 'toastStyle';
            style.textContent = `@keyframes shrink { from { width: 100%; } to { width: 0%; } }`;
            document.head.appendChild(style);
        }

        const disciplineLabels = {
            agriculture: 'Agriculture, Forestry, Fisheries',
            architecture: 'Architecture and Town Planning',
            business: 'Business Administration',
            criminal_justice: 'Criminal Justice Education',
            education: 'Education Science and Teacher Training',
            engineering: 'Engineering and Technology',
            arts: 'Fine and Applied Arts',
            general: 'General Programs',
            home_economics: 'Home Economics',
            humanities: 'Humanities',
            it: 'IT-Related Disciplines',
            law: 'Law and Jurisprudence',
            maritime: 'Maritime',
            mass_comm: 'Mass Communication',
            mathematics: 'Mathematics',
            medical: 'Medical and Allied',
            natural_science: 'Natural Science',
            other_disciplines: 'Other Disciplines',
            religion: 'Religion and Theology',
            service_trades: 'Service Trades',
            social_sciences: 'Social and Behavioral Sciences'
        };

        // Handle "All Provinces" selection
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('province');
            if (provinceSelect) {
                provinceSelect.addEventListener('change', function() {
                    const isAllProvinces = this.value === 'All Provinces';
                    const allInputs = document.querySelectorAll('input[type="number"]');
                    const submitBtn = document.querySelector('button[type="submit"]');
                    
                    if (isAllProvinces) {
                        allInputs.forEach(input => {
                            input.disabled = true;
                            input.classList.add('bg-gray-100', 'cursor-not-allowed');
                        });
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                            submitBtn.title = 'Cannot submit aggregated data. Select a specific province.';
                        }
                    } else {
                        allInputs.forEach(input => {
                            input.disabled = false;
                            input.classList.remove('bg-gray-100', 'cursor-not-allowed');
                        });
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                            submitBtn.title = '';
                        }
                    }
                });
            }
        });

        async function checkAndLoadYear() {
            const yearInput = document.getElementById('academicYear');
            const year = yearInput.value.trim();
            
            if (!year) {
                showToast('Please enter an academic year', 'error');
                return;
            }

            const yearPattern = /^\d{4}-\d{4}$/;
            if (!yearPattern.test(year)) {
                showToast('Please enter year in format: YYYY-YYYY (e.g., 2024-2025)', 'error');
                return;
            }

            const province = document.getElementById('province').value;
            const institutionType = document.querySelector('input[name="institution_type"]:checked');

            if (!province) {
                showToast('Please select a province', 'error');
                return;
            }

            if (!institutionType) {
                showToast('Please select an institution type (Private or Public)', 'error');
                return;
            }

            try {
                const response = await fetch(`/api/discipline-enrollment/check/${year}?province=${encodeURIComponent(province)}&institution_type=${encodeURIComponent(institutionType.value)}`);
                
                if (!response.ok) {
                    if (response.status === 404) {
                        // No existing data - load as new
                        loadNewYear(year, province, institutionType.value);
                        return;
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.exists && data.data) {
                    // Check if changing year to an existing year
                    if (isChangingYear) {
                        showYearCollisionModal(year, province, institutionType.value);
                        isChangingYear = false;
                        return;
                    }
                    
                    // Show confirmation modal for existing data
                    pendingYearData = {
                        year: year,
                        province: province,
                        institutionType: institutionType.value,
                        data: data.data
                    };
                    showExistingDataModal(year, province, institutionType.value);
                } else {
                    // No existing data
                    loadNewYear(year, province, institutionType.value);
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred while checking the year. Please try again.', 'error');
                isChangingYear = false;
            }
        }

        function loadNewYear(year, province, institutionType) {
            document.getElementById('displayYear').textContent = year;
            existingData = null;
            if (!isChangingYear) {
                clearForm();
            }
            showStatusNotification(year, false, province, institutionType);
            toggleYearDisplay(true);
            document.getElementById('cancelYearChangeBtn').classList.remove('hidden');
            isChangingYear = false;
        }

        function showExistingDataModal(year, province, institutionType) {
            document.getElementById('existingDataYear').textContent = year;
            document.getElementById('existingDataProvince').textContent = province;
            document.getElementById('existingDataType').textContent = institutionType;
            document.getElementById('existingDataModal').classList.remove('hidden');
        }

        function closeExistingDataModal() {
            document.getElementById('existingDataModal').classList.add('hidden');
            pendingYearData = null;
            // Clear the input and refocus
            document.getElementById('academicYear').value = '';
            setTimeout(() => {
                document.getElementById('academicYear').focus();
            }, 100);
        }

        function confirmLoadExistingData() {
            if (!pendingYearData) return;
            
            const { year, province, institutionType, data } = pendingYearData;
            
            document.getElementById('displayYear').textContent = year;
            existingData = data;
            populateForm(data.disciplines);
            showStatusNotification(year, true, province, institutionType);
            toggleYearDisplay(true);
            document.getElementById('cancelYearChangeBtn').classList.remove('hidden');
            
            closeExistingDataModal();
            isChangingYear = false;
        }

        function toggleYearDisplay(showDisplay) {
            const inputGroup = document.getElementById('yearInputGroup');
            const yearDisplay = document.getElementById('yearDisplay');
            
            if (showDisplay) {
                inputGroup.classList.add('hidden');
                yearDisplay.classList.remove('hidden');
            } else {
                inputGroup.classList.remove('hidden');
                yearDisplay.classList.add('hidden');
            }
        }

        function changeYear() {
            const currentYear = document.getElementById('displayYear').textContent;
            document.getElementById('changeYearCurrent').textContent = currentYear;
            document.getElementById('changeYearModal').classList.remove('hidden');
        }

        function closeChangeYearModal() {
            document.getElementById('changeYearModal').classList.add('hidden');
        }

        function confirmChangeYear() {
            document.getElementById('changeYearModal').classList.add('hidden');
            isChangingYear = true;
            oldYear = document.getElementById('displayYear').textContent;
            document.getElementById('academicYear').value = '';
            document.getElementById('displayYear').textContent = '----';
            hideStatusNotification();
            existingData = null;
            toggleYearDisplay(false);
            
            setTimeout(() => {
                document.getElementById('academicYear').focus();
            }, 100);
        }

        function showYearCollisionModal(targetYear, province, institutionType) {
            const currentYear = oldYear && oldYear !== '----' ? oldYear : document.getElementById('displayYear').textContent;
            document.getElementById('collisionTargetYear').textContent = `${targetYear} - ${province} - ${institutionType}`;
            document.getElementById('collisionCurrentYear').textContent = currentYear;
            document.getElementById('yearCollisionModal').classList.remove('hidden');
        }

        function closeYearCollisionModal() {
            document.getElementById('yearCollisionModal').classList.add('hidden');
            document.getElementById('academicYear').value = '';
            document.getElementById('academicYear').focus();
        }

        function cancelYearChange() {
            document.getElementById('academicYear').value = '';
            document.getElementById('displayYear').textContent = '----';
            hideStatusNotification();
            toggleYearDisplay(false);

            // Reset province dropdown
            document.getElementById('province').value = '';

            // Reset institution type radio buttons
            document.querySelectorAll('input[name="institution_type"]').forEach(r => r.checked = false);

            // Reset all discipline input fields
            clearForm();

            // Reset grand total display
            document.getElementById('grandTotal').textContent = '0';
            
            if (oldYear && oldYear !== '----') {
                isChangingYear = false;
                oldYear = null;
                existingData = null;
            }
            
            document.getElementById('cancelYearChangeBtn').classList.add('hidden');
            
            setTimeout(() => {
                document.getElementById('academicYear').focus();
            }, 100);
        }

        function showStatusNotification(year, exists, province, institutionType) {
            const notification = document.getElementById('statusNotification');
            const icon = document.getElementById('statusIcon');
            const title = document.getElementById('statusTitle');
            const message = document.getElementById('statusMessage');

            if (exists) {
                notification.className = 'mb-8 p-6 rounded-2xl shadow-lg bg-blue-50 border-2 border-blue-200';
                icon.className = 'flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-blue-500 text-white text-2xl';
                icon.innerHTML = '📝';
                title.textContent = 'Editing Existing Data';
                title.className = 'text-lg font-bold mb-1 text-blue-900';
                message.textContent = `Loading data for ${year} - ${province} - ${institutionType}. You can now edit the existing enrollment data.`;
                message.className = 'text-sm text-blue-800';
            } else {
                notification.className = 'mb-8 p-6 rounded-2xl shadow-lg bg-green-50 border-2 border-green-200';
                icon.className = 'flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-green-500 text-white text-2xl';
                icon.innerHTML = '✨';
                title.textContent = 'Creating New Data';
                title.className = 'text-lg font-bold mb-1 text-green-900';
                message.textContent = `No existing data found for ${year} - ${province} - ${institutionType}. You can now enter new enrollment data.`;
                message.className = 'text-sm text-green-800';
            }

            notification.classList.remove('hidden');
        }

        function hideStatusNotification() {
            document.getElementById('statusNotification').classList.add('hidden');
        }
        function updateGrandTotal() {
            const inputs = document.querySelectorAll('input[type="number"][name]');
            let total = 0;
            inputs.forEach(input => {
                total += parseInt(input.value) || 0;
            });
            document.getElementById('grandTotal').textContent = total.toLocaleString();
        }
        // Add event listeners for auto-update
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="number"][name]');
            inputs.forEach(input => {
                input.addEventListener('input', updateGrandTotal);
            });
        });

        function populateForm(disciplines) {
            for (const [key, value] of Object.entries(disciplines)) {
                const input = document.querySelector(`input[name="${key}"]`);
                if (input) {
                    // Only populate if value > 0, otherwise leave empty
                    input.value = (value && value > 0) ? value : '';
                }
            }
            updateGrandTotal();
        }

        function clearForm() {
            const inputs = document.querySelectorAll('input[type="number"]');
            inputs.forEach(input => {
                input.value = '';
            });
        }

        function confirmReset() {
            document.getElementById('resetModal').classList.remove('hidden');
        }

        function closeResetModal() {
            document.getElementById('resetModal').classList.add('hidden');
        }

        function doReset() {
            document.getElementById('resetModal').classList.add('hidden');
            // Reset discipline input fields
            clearForm();
            // Reset grand total display
            document.getElementById('grandTotal').textContent = '0';
            // Reset province dropdown
            document.getElementById('province').value = '';
            // Reset institution type radios
            document.querySelectorAll('input[name="institution_type"]').forEach(r => r.checked = false);
        }

        function showConfirmModal(data) {
            pendingData = data;
            
            document.getElementById('confirmYear').textContent = data.academic_year;
            document.getElementById('confirmProvince').textContent = data.province;
            document.getElementById('confirmInstitutionType').textContent = data.institution_type;
            
            const actionType = document.getElementById('actionType');
            const confirmActionWarning = document.getElementById('confirmActionWarning');
            const deletionWarning = document.getElementById('deletionWarning');
            
            if (existingData) {
                actionType.textContent = 'update';
                confirmActionWarning.textContent = 'Update';
                deletionWarning.classList.remove('hidden');
            } else {
                actionType.textContent = 'create new';
                confirmActionWarning.textContent = 'Save';
                deletionWarning.classList.add('hidden');
            }
            
            const dataSummary = document.getElementById('dataSummary');
            dataSummary.innerHTML = '';
            
            let grandTotal = 0;
            for (const [key, value] of Object.entries(data.disciplines)) {
                const numValue = parseInt(value) || 0;
                grandTotal += numValue;
                
                const row = document.createElement('div');
                row.className = 'flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition';
                row.innerHTML = `
                    <span class="text-sm font-medium text-gray-700">${disciplineLabels[key]}</span>
                    <span class="text-sm font-bold ${numValue > 0 ? 'text-blue-600' : 'text-gray-400'}">${numValue.toLocaleString()}</span>
                `;
                dataSummary.appendChild(row);
            }
            
            document.getElementById('confirmGrandTotal').textContent = grandTotal.toLocaleString();
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            pendingData = null;
        }

        function showSuccessModal() {
            document.getElementById('successModal').classList.remove('hidden');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
            
            // Clear the form silently after successful submission
            clearForm();
            
            // Reset the year field and status message
            document.getElementById('academicYear').value = '';
            document.getElementById('yearStatusMessage').textContent = '';
            document.getElementById('yearStatusMessage').className = 'text-sm mt-1';
            
            // Clear existing data flag
            existingData = null;
            oldYear = null;
        }

        async function confirmSubmit() {
            const dataToSubmit = pendingData;
            closeConfirmModal();

            try {
                if (oldYear && oldYear !== '----') {
                    try {
                        const province = dataToSubmit.province;
                        const institutionType = dataToSubmit.institution_type;
                        await fetch(`/api/discipline-enrollment/delete/${oldYear}?province=${encodeURIComponent(province)}&institution_type=${encodeURIComponent(institutionType)}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        console.log(`Deleted old year data: ${oldYear} - ${province} - ${institutionType}`);
                    } catch (deleteError) {
                        console.error('Error deleting old year:', deleteError);
                    }
                }

                const response = await fetch('{{ route("admin.discipline-enrollment.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(dataToSubmit)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    oldYear = null;
                    showSuccessModal();
                } else {
                    showToast('Error: ' + (result.message || 'An error occurred while saving the data.'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred while saving the data.', 'error');
            }
        }

        document.getElementById('disciplineForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const academicYear = document.getElementById('displayYear').textContent;
            
            if (academicYear === '----') {
                showToast('Please click "Check Year" first to select an academic year.', 'warning');
                return;
            }

            const province = document.getElementById('province').value;
            const institutionType = document.querySelector('input[name="institution_type"]:checked');

            if (!province) {
                showToast('Please select a province', 'error');
                return;
            }

            if (!institutionType) {
                showToast('Please select an institution type (Private or Public)', 'error');
                return;
            }

            // Get all discipline values
            const disciplines = {
                agriculture: document.querySelector('input[name="agriculture"]').value,
                architecture: document.querySelector('input[name="architecture"]').value,
                business: document.querySelector('input[name="business"]').value,
                criminal_justice: document.querySelector('input[name="criminal_justice"]').value,
                education: document.querySelector('input[name="education"]').value,
                engineering: document.querySelector('input[name="engineering"]').value,
                arts: document.querySelector('input[name="arts"]').value,
                general: document.querySelector('input[name="general"]').value,
                home_economics: document.querySelector('input[name="home_economics"]').value,
                humanities: document.querySelector('input[name="humanities"]').value,
                it: document.querySelector('input[name="it"]').value,
                law: document.querySelector('input[name="law"]').value,
                maritime: document.querySelector('input[name="maritime"]').value,
                mass_comm: document.querySelector('input[name="mass_comm"]').value,
                mathematics: document.querySelector('input[name="mathematics"]').value,
                medical: document.querySelector('input[name="medical"]').value,
                natural_science: document.querySelector('input[name="natural_science"]').value,
                other_disciplines: document.querySelector('input[name="other_disciplines"]').value,
                religion: document.querySelector('input[name="religion"]').value,
                service_trades: document.querySelector('input[name="service_trades"]').value,
                social_sciences: document.querySelector('input[name="social_sciences"]').value
            };

            const cleanedDisciplines = {};
            for (const [key, value] of Object.entries(disciplines)) {
                cleanedDisciplines[key] = value ? parseInt(value) : 0;
            }

            const dataToSave = {
                academic_year: academicYear,
                province: province,
                institution_type: institutionType.value,
                disciplines: cleanedDisciplines
            };

            console.log('📤 Data being sent to server:', dataToSave);
            console.log('📝 Existing data flag:', existingData ? 'UPDATE MODE' : 'CREATE MODE');

            showConfirmModal(dataToSave);
        });
    </script>

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
                <h3 class="text-xl font-bold text-gray-900 mb-3">Reset Form?</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to reset all fields? All entered data will be lost and cannot be recovered.</p>
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