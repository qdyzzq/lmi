<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <title>LMI</title>
</head>

<body class="bg-slate-100 flex h-screen overflow-hidden">
    <aside class="w-72 bg-[#1e3a8a] text-white flex flex-col shadow-xl z-10">
        <div class="p-6 border-b border-blue-800 ">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-900 font-bold">LMI
                </div>
                <div class="leading-tight">
                    <p class="font-bold text-sm">Labor Market Intelligence</p>
                    <p class="text-[10px] opacity-70 italic">Bridging Education & Industry</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-auto">
            <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mb-4 px-2">Main Menu</p>

            <a href="{{ route('home') }}"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="opacity-70 group-hover:opacity-100">📊</span> Dashboard
            </a>

            <a href="{{ route('hei.graduate') }}"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class=" opacity-70 group-hover:opacity-100">🎓</span> HEI Graduate Data
            </a>

            <a href="{{ route('Skill.Gap.Demand') }}"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="opacity-70 group-hover:opacity-100">⚖️</span> Skills Gap & Demand
            </a>

            <a href="{{ route('Job.Market.Overview') }}"
                class="flex items-center gap-3 p-3  bg-yellow-400 text-blue-900 font-bold  rounded-lg transition shadow-md">
                <span>📈</span> Job Market Overview
            </a>

            <a href="{{ route('Government.Data') }}"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class=" opacity-70 group-hover:opacity-100">🗂️</span> Government Data
            </a>

            {{-- <a href="{{ route('Stake') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class=" opacity-70 group-hover:opacity-100">🤝</span> Stakeholder Engagement
            </a> --}}

            <a href="{{ route('Report') }}"
                class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class=" opacity-70 group-hover:opacity-100">📑</span> Reports
            </a>

            <div class="pt-6">
                <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mb-4 px-2">Account</p>
                <a href="{{ route('Setting') }}"
                    class="flex items-center gap-3 p-3 text-blue-100 hover:blue-800 rounded-lg transition group">
                    <span class="opacity-70 group-hover:opacity-100">⚙️</span> Settings
                </a>
                <a href="#"
                    class="flex items-center gap-3 p-3 text-red-300 hover:bg-red-900/30 rounded-lg transition group">
                    <span class="opacity-70 group-hover:opacity-100">🚪</span> Logout
                </a>
            </div>
        </nav>

        <div class="p-4 bg-blue-950 text-[10px] text-center opacity-50">
            © 2026 DOLE Region XI
        </div>
    </aside>
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- HEADER -->
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Job Market Overview • Admin</h2>
            <div class="flex items-center gap-4">
                <div
                    class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600 border border-slate-200">
                    📅 Region XI • 2024
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500"></div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto p-8 bg-slate-100">

            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-8">
                <h3 class="text-lg font-bold text-slate-800 mb-6">
                    Labor Market Form
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Year -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">
                            Year <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="year"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200"
                            min="2000" max="2100" value="2024" required>
                    </div>

                    <!-- Month -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">
                            Month <span class="text-red-500">*</span>
                        </label>
                        <select id="month"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200"
                            required>
                            <option value="">Select Month</option>
                            <option value="1">January</option>
                            <option value="4">April</option>
                            <option value="7">July</option>
                            <option value="10">October</option>

                        </select>
                    </div>

                    <!-- Household Population -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">
                            Household Population (15 Years Old and Over)
                        </label>
                        <input type="number" id="householdPopulation"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200"
                            placeholder="e.g. 3182" step="0.001">
                    </div>

                    <!-- LFPR -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">
                            Labor Force Participation Rate (%)
                        </label>
                        <input type="number" id="lfpr"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200"
                            min="0" max="100" step="0.01" placeholder="e.g. 65.2">
                    </div>

                    <!-- Employment Rate -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">
                            Employment Rate (%)
                        </label>
                        <input type="number" id="employmentrate"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200"
                            min="0" max="100" step="0.01" placeholder="e.g. 93.9">
                    </div>

                    <!-- Underemployment Rate -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">
                            Underemployment Rate (%)
                        </label>
                        <input type="number" id="underemploymentrate"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200"
                            min="0" max="100" step="0.01" placeholder="e.g. 19.3">
                    </div>

                    <!-- Unemployment Rate -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">
                            Unemployment Rate (%)
                        </label>
                        <input type="number" id="unemploymentrate"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring focus:ring-blue-200"
                            min="0" max="100" step="0.01" placeholder="e.g. 6.1">
                    </div>

                    <!-- Labor Force (Auto) -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">
                            Labor Force (Auto-calculated)
                            <span class="text-xs text-slate-400 ml-1">(HOP * LFPR)</span>
                        </label>
                        <input type="number" id="laborForce"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-100" readonly>
                    </div>

                    <!-- Employed -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">
                            Employed (Auto-calculated)
                            <span class="text-xs text-slate-400 ml-1">(EMPR * LFPR)</span>
                        </label>
                        <input type="number" id="employed"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-100" readonly>
                    </div>

                    <!-- Underemployed -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">
                            Underemployed (Auto-calculated)
                            <span class="text-xs text-slate-400 ml-1">(EMP * UEMP)</span>
                        </label>
                        <input type="number" id="underemployed"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-100" readonly>
                    </div>

                    <!-- Unemployed -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">
                            Unemployed (Auto-calculated)
                            <span class="text-xs text-slate-400 ml-1">(LF * UEMPR)</span>
                        </label>
                        <input type="number" id="unemployed"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-100" readonly>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="mt-8 flex justify-end gap-3">
                    <button id="resetBtn"
                        class="px-6 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100">
                        Reset
                    </button>
                    <button id="saveBtn"
                        class="px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow">
                        Save Data
                    </button>
                </div>
            </div>
        </main>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // Get all input elements
                const yearInput = document.getElementById('year');
                const monthInput = document.getElementById('month');
                const householdInput = document.getElementById('householdPopulation');
                const lfprInput = document.getElementById('lfpr');
                const employmentRateInput = document.getElementById('employmentrate');
                const underemploymentRateInput = document.getElementById('underemploymentrate');
                const unemploymentRateInput = document.getElementById('unemploymentrate');

                // Get all output elements
                const laborForceInput = document.getElementById('laborForce');
                const employedInput = document.getElementById('employed');
                const underemployedInput = document.getElementById('underemployed');
                const unemployedInput = document.getElementById('unemployed');

                // Get buttons
                const resetBtn = document.getElementById('resetBtn');
                const saveBtn = document.getElementById('saveBtn');

                // Utility function to normalize rate (handles both 0-1 and 0-100 formats)
                function normalizeRate(value) {
                    return value > 1 ? value / 100 : value;
                }

                // Calculate Labor Force
                function calculateLaborForce() {
                    const household = parseFloat(householdInput.value);
                    const lfpr = parseFloat(lfprInput.value);

                    if (isNaN(household) || isNaN(lfpr)) {
                        laborForceInput.value = '';
                        return;
                    }

                    const normalizedLfpr = normalizeRate(lfpr);
                    const laborForce = household * normalizedLfpr;
                    laborForceInput.value = Math.round(laborForce);

                    // Trigger dependent calculations
                    calculateEmployed();
                    calculateUnemployed();
                }

                // Calculate Employed
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

                    // Trigger dependent calculation
                    calculateUnderemployed();
                }

                // Calculate Underemployed
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

                // Calculate Unemployed
                function calculateUnemployed() {
                    const laborForce = parseFloat(laborForceInput.value);
                    const unemploymentRate = parseFloat(unemploymentRateInput.value);

                    if (isNaN(laborForce) || isNaN(unemploymentRate)) {
                        unemployedInput.value = '';
                        return;
                    }

                    const normalizedRate = normalizeRate(unemploymentRate);
                    const unemployed = laborForce * normalizedRate; // Changed this line
                    unemployedInput.value = Math.round(unemployed);
                }

                // Attach event listeners for calculations
                householdInput.addEventListener('input', calculateLaborForce);
                lfprInput.addEventListener('input', calculateLaborForce);
                employmentRateInput.addEventListener('input', calculateEmployed);
                underemploymentRateInput.addEventListener('input', calculateUnderemployed);
                unemploymentRateInput.addEventListener('input', calculateUnemployed);

                // Reset button functionality
                resetBtn.addEventListener('click', function() {
                    document.querySelectorAll('input[type="number"]').forEach(input => {
                        if (input.id !== 'year') {
                            input.value = '';
                        }
                    });
                    monthInput.value = '';
                });

                // Save button functionality
                saveBtn.addEventListener('click', function() {
                    // Validate required fields
                    if (!yearInput.value || !monthInput.value) {
                        alert('Please select Year and Month');
                        return;
                    }

                    // Collect all data
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

                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                    'content');

                    // Disable button and show loading
                    saveBtn.disabled = true;
                    saveBtn.textContent = 'Saving...';

                    // Send to Laravel backend
                    fetch('/api/labor-market/store', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                alert(result.message);
                                console.log('Saved data:', result.data);
                                // Optional: Reset form after successful save
                                // resetBtn.click();
                            } else {
                                alert('Error: ' + (result.message || 'Failed to save data'));
                                console.error('Errors:', result.errors);
                            }
                        })
                        .catch(error => {
                            alert('Network error: ' + error.message);
                            console.error('Error:', error);
                        })
                        .finally(() => {
                            // Re-enable button
                            saveBtn.disabled = false;
                            saveBtn.textContent = 'Save Data';
                        });
                });

            });
        </script>
</body>

</html>
