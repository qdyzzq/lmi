<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <title>LMI</title>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden">
    <aside id="sidebar" class="w-72 bg-[#1e3a8a] text-white flex flex-col shadow-xl z-10 transition-all duration-300">
        <div class="p-6 border-b border-blue-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-900 font-bold">LMI</div>
                <div class="leading-tight">
                    <p class="font-bold text-sm">Labor Market Intelligence</p>
                    <p class="text-[10px] opacity-70 italic">Bridging Education & Industry</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-auto">
            <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mb-4 px-2">Main Menu</p>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 p-3 bg-yellow-400 text-blue-900 font-bold rounded-lg transition shadow-md">
                <span>📋</span> Regional Statistics
            </a>

             <a href="{{ route('admin.lmi-submissions.index') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
        <span class="opacity-70 group-hover:opacity-100">📋</span> LMI Submissions
            </a>


            <a href="{{ route('admin.job-titles.form') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
        <span class="opacity-70 group-hover:opacity-100"> 💼</span> Job Titles Form
            </a>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="flex items-center gap-3 p-3 text-red-300 hover:bg-red-900/30 rounded-lg transition group w-full text-left">
                    <span class="opacity-70 group-hover:opacity-100">🚪</span> Logout
                </button>
            </form>
        </nav>

        <div class="p-4 bg-blue-950 text-[10px] text-center opacity-50">
            © 2026 DOLE Region XI
        </div>
    </aside>

    <div id="mainContent" class="flex-1 flex flex-col overflow-hidden transition-all duration-300">
        <!-- HEADER -->
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Job Market Overview • Admin</h2>
            <div class="flex items-center gap-4">
                <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200">
                    📅 Region XI • 2024
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto p-8 bg-slate-100">
            <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-8">
                <h3 class="text-lg font-bold text-slate-800 mb-6">
                    Labor Market Form
                </h3>

                <!-- Year and Month Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">
                            Year <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="year"
                            class="w-full border border-slate-800  rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-200 focus:border-blue-400"
                            min="2000"
                            max="2100"
                            value="2024"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">
                            Month <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="month"
                            class="w-full border border-slate-800  rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-200 focus:border-blue-400"
                            required
                        >
                            <option value="">Select Month</option>
                            <option value="1">January</option>
                            <option value="4">April</option>
                            <option value="7">July</option>
                            <option value="10">October</option>
                        </select>
                    </div>
                </div>

                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- LEFT COLUMN -->
                    <div class="space-y-6">
                        <!-- Household Population -->
                        <div>
                            <label class="block text-sm font-bold text-slate-600 mb-2">
                                Household Population (15 Years Old and Over)
                            </label>
                            <input
                                type="number"
                                id="householdPopulation"
                                class="w-full border border-slate-800  rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-200 focus:border-blue-400"
                                placeholder="e.g. 3182"
                                step="0.001"
                            >
                        </div>

                        <!-- Labor Force Participation Rate -->
                        <div>
                            <label class="block text-sm font-bold text-slate-600 mb-2">
                                Labor Force Participation Rate (%)
                            </label>
                            <input
                                type="number"
                                id="lfpr"
                                class="w-full border border-slate-800  rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-200 focus:border-blue-400"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="e.g. 65.2"
                            >
                        </div>

                        <!-- Employment Rate -->
                        <div>
                            <label class="block text-sm font-bold text-slate-600 mb-2">
                                Employment Rate (%)
                            </label>
                            <input
                                type="number"
                                id="employmentrate"
                                class="w-full border border-slate-800  rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-200 focus:border-blue-400"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="e.g. 93.9"
                            >
                        </div>

                        <!-- Underemployment Rate -->
                        <div>
                            <label class="block text-sm font-bold text-slate-600 mb-2">
                                Underemployment Rate (%)
                            </label>
                            <input 
                                type="number"
                                id="underemploymentrate"
                                class="w-full border-2 border-slate-800 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-200 focus:border-blue-400"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="e.g. 19.3"
                            >
                        </div>

                        <!-- Unemployment Rate -->
                        <div>
                            <label class="block text-sm font-bold text-slate-600 mb-2">
                                Unemployment Rate (%)
                            </label>
                            <input 
                                type="number"
                                id="unemploymentrate"
                                class="w-full border-2 border-slate-800 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-200 focus:border-blue-400"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="e.g. 6.1"
                            >
                        </div>
                    </div>

                    <!-- RIGHT COLUMN -->
                    <div class="space-y-6">
                        <!-- Labor Force (Auto) - Centered between HOP and LFPR -->
                        <div class="mt-[34px]">
                            <label class="block text-sm font-semibold text-slate-600 mb-2">
                                Labor Force (Auto-calculated) <span class="text-[12px] text-slate-700 font-normal">(HOP * LFPR)</span>
                            </label>
                            <input
                                type="number"
                                id="laborForce"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm bg-slate-50"
                                readonly
                            >
                        </div>

                        <!-- Employed (Auto) - Centered between Employment Rate and Labor Force -->
                        <div class="mt-[34px]">
                            <label class="block text-sm font-semibold text-slate-600 mb-2">
                                Employed (Auto-calculated) <span class="text-[12px] text-slate-700 font-normal">(EMPR * LFPR)</span>
                            </label>
                            <input
                                type="number"
                                id="employed"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm bg-slate-50"
                                readonly
                            >
                        </div>

                        <!-- Underemployed (Auto) - Centered between Underemployment Rate and Employed -->
                        <div class="mt-[34px]">
                            <label class="block text-sm font-semibold text-slate-600 mb-2">
                                Underemployed (Auto-calculated) <span class="text-[12px] text-slate-700 font-normal">(EMP * UEMP)</span>
                            </label>
                            <input
                                type="number"
                                id="underemployed"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm bg-slate-50"
                                readonly
                            >
                        </div>

                        <!-- Unemployed (Auto) - Centered between Unemployment Rate and Labor Force -->
                        <div class="mt-[34px]">
                            <label class="block text-sm font-semibold text-slate-600 mb-2">
                                Unemployed (Auto-calculated) <span class="text-[12px] text-slate-700 font-normal">(LF * UEMPR)</span>
                            </label>
                            <input
                                type="number"
                                id="unemployed"
                                class="w-full border border-slate-300 rounded px-3 py-2 text-sm bg-slate-50"
                                readonly
                            >
                        </div>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="mt-8 flex justify-end gap-3">
                    <button id="resetBtn" class="px-6 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100 text-sm font-medium">
                        Reset
                    </button>
                    <button id="saveBtn" class="px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow text-sm">
                        Save Data
                    </button>
                </div>
            </div>
        </main>
    </div>

    <!-- Confirmation Modal with Blur Backdrop -->
    <div id="confirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- Blur Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="document.getElementById('confirmModal').classList.add('hidden')"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Confirm Submission</h3>
                <p class="text-sm text-slate-600 mb-6">
                    Are you sure you want to submit this data to the pending queue? The statistician will review and verify it before posting to the database.
                </p>
                <div class="flex gap-3 w-full">
                    <button 
                        id="cancelBtn"
                        class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition font-medium"
                    >
                        Cancel
                    </button>
                    <button 
                        id="confirmBtn"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                    >
                        Yes, Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal with Blur Backdrop -->
    <div id="successModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- Blur Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="document.getElementById('successModal').classList.add('hidden')"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Successfully Submitted!</h3>
                <p class="text-sm text-slate-600 mb-6">
                    Your data has been successfully submitted to the pending queue. It will be reviewed by a statistician before being posted to the database.
                </p>
                <button 
                    id="closeModalBtn"
                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                >
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Error Modal with Blur Backdrop -->
    <div id="errorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- Blur Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="document.getElementById('errorModal').classList.add('hidden')"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Error</h3>
                <p id="errorMessage" class="text-sm text-slate-600 mb-6">
                    An error occurred. Please try again.
                </p>
                <button 
                    id="closeErrorBtn"
                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium"
                >
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const yearInput = document.getElementById('year');
        const monthInput = document.getElementById('month');
        const householdInput = document.getElementById('householdPopulation');
        const lfprInput = document.getElementById('lfpr');
        const employmentRateInput = document.getElementById('employmentrate');
        const underemploymentRateInput = document.getElementById('underemploymentrate');
        const unemploymentRateInput = document.getElementById('unemploymentrate');
        const laborForceInput = document.getElementById('laborForce');
        const employedInput = document.getElementById('employed');
        const underemployedInput = document.getElementById('underemployed');
        const unemployedInput = document.getElementById('unemployed');
        const resetBtn = document.getElementById('resetBtn');
        const saveBtn = document.getElementById('saveBtn');

        function normalizeRate(rate) {
            if (rate > 1) {
                return rate / 100;
            }
            return rate;
        }

        function calculateLaborForce() {
            const household = parseFloat(householdInput.value);
            const lfpr = parseFloat(lfprInput.value);

            if (isNaN(household) || isNaN(lfpr)) {
                laborForceInput.value = '';
                employedInput.value = '';
                underemployedInput.value = '';
                return;
            }

            const normalizedLfpr = normalizeRate(lfpr);
            const laborForce = household * normalizedLfpr;
            laborForceInput.value = Math.round(laborForce);
            
            calculateEmployed();
            calculateUnemployed();
        }

        function calculateEmployed() {
            const laborForce = parseFloat(laborForceInput.value);
            const employmentRate = parseFloat(employmentRateInput.value);

            if (isNaN(laborForce) || isNaN(employmentRate)) {
                employedInput.value = '';
                return;
            }

            const normalizedRate = normalizeRate(employmentRate);
            const employed = laborForce * normalizedRate;
            employedInput.value = Math.round(employed);
            
            calculateUnderemployed();
        }

        function calculateUnderemployed() {
            const employed = parseFloat(employedInput.value);
            const underemploymentRate = parseFloat(underemploymentRateInput.value);

            if (isNaN(employed) || isNaN(underemploymentRate)) {
                underemployedInput.value = '';
                return;
            }

            const normalizedRate = normalizeRate(underemploymentRate);
            const underemployed = employed * normalizedRate;
            underemployedInput.value = Math.round(underemployed);
        }

        function calculateUnemployed() {
            const laborForce = parseFloat(laborForceInput.value);
            const unemploymentRate = parseFloat(unemploymentRateInput.value);

            if (isNaN(laborForce) || isNaN(unemploymentRate)) {
                unemployedInput.value = '';
                return;
            }

            const normalizedRate = normalizeRate(unemploymentRate);
            const unemployed = laborForce * normalizedRate;
            unemployedInput.value = Math.round(unemployed);
        }

        householdInput.addEventListener('input', calculateLaborForce);
        lfprInput.addEventListener('input', calculateLaborForce);
        employmentRateInput.addEventListener('input', calculateEmployed);
        underemploymentRateInput.addEventListener('input', calculateUnderemployed);
        unemploymentRateInput.addEventListener('input', calculateUnemployed);

        resetBtn.addEventListener('click', function() {
            document.querySelectorAll('input[type="number"]').forEach(input => {
                if (input.id !== 'year') {
                    input.value = '';
                }
            });
            monthInput.value = '';
        });

        saveBtn.addEventListener('click', function() {
            if (!yearInput.value || !monthInput.value) {
                alert('Please select Year and Month');
                return;
            }

            saveBtn.disabled = true;
            saveBtn.textContent = 'Checking...';

            fetch('{{ route('labor.market.check') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    year: yearInput.value,
                    month: monthInput.value
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.exists) {
                    document.getElementById('errorMessage').textContent = result.message;
                    document.getElementById('errorModal').classList.remove('hidden');
                } else {
                    document.getElementById('confirmModal').classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Check failed:', error);
                alert('An error occurred while checking for existing data. Please try again.');
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Data';
            });
        });

        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('confirmModal').classList.add('hidden');
        });

        document.getElementById('confirmBtn').addEventListener('click', function() {
            document.getElementById('confirmModal').classList.add('hidden');

            const data = {
                year: parseInt(yearInput.value),
                month: parseInt(monthInput.value),
                household_population: parseFloat(householdInput.value) || null,
                labor_force: parseFloat(laborForceInput.value) || null,
                employed: parseFloat(employedInput.value) || null,
                underemployed: parseFloat(underemployedInput.value) || null,
                unemployed: parseFloat(unemployedInput.value) || null,
                labor_force_participation_rate: parseFloat(lfprInput.value) || null,
                employment_rate: parseFloat(employmentRateInput.value) || null,
                underemployment_rate: parseFloat(underemploymentRateInput.value) || null,
                unemployment_rate: parseFloat(unemploymentRateInput.value) || null
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            saveBtn.disabled = true;
            saveBtn.textContent = 'Submitting...';

            fetch('{{ route('labor.market.submit.pending') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(result => {
                if (result.success === true) {
                    document.getElementById('successModal').classList.remove('hidden');
                    console.log('Saved data:', result.data);
                } else {
                    document.getElementById('errorMessage').textContent = result.message || 'Failed to save data';
                    document.getElementById('errorModal').classList.remove('hidden');
                }
            })
            .catch(error => {
                let errorMsg = 'Network error occurred';
                
                if (error.message) {
                    errorMsg = error.message;
                } else if (error.errors) {
                    errorMsg = Object.values(error.errors).flat().join('\n');
                }
                
                document.getElementById('errorMessage').textContent = errorMsg;
                document.getElementById('errorModal').classList.remove('hidden');
                console.error('Error:', error);
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Data';
            });
        });

        document.getElementById('closeModalBtn').addEventListener('click', function() {
            document.getElementById('successModal').classList.add('hidden');
            resetBtn.click();
        });

        document.getElementById('closeErrorBtn').addEventListener('click', function() {
            document.getElementById('errorModal').classList.add('hidden');
        });
    });
    </script>
</body>
</html>