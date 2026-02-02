<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <title>LMI - Statistician Review</title>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden">
    <!-- SIDEBAR -->
    <aside class="w-72 bg-[#1e3a8a] text-white flex flex-col shadow-xl z-10">
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

            

            <a href="{{ route('statistician.review') }}" class="flex items-center gap-3 p-3 bg-yellow-400 text-blue-900 font-bold rounded-lg transition shadow-md">
                <span>📋</span> Labor Market Review
            </a>
            

            <a href="{{ route('statistician.job-titles.pending') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                <span class="opacity-70 group-hover:opacity-100">📊</span> Pending Job Titles
            </a>
            <div class="pt-6">
                <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold mb-4 px-2">Account</p>
                <a href="{{ route('Setting') }}" class="flex items-center gap-3 p-3 text-blue-100 hover:bg-blue-800 rounded-lg transition group">
                    <span class="opacity-70 group-hover:opacity-100">⚙️</span> Settings
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 p-3 text-red-300 hover:bg-red-900/30 rounded-lg transition group w-full text-left">
                        <span class="opacity-70 group-hover:opacity-100">🚪</span> Logout
                    </button>
                </form>
            </div>
        </nav>

        <div class="p-4 bg-blue-950 text-[10px] text-center opacity-50">
            © 2026 DOLE Region XI
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- HEADER -->
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Pending Data Verification • Statistician</h2>
            <div class="flex items-center gap-4">
                <div class="bg-yellow-100 px-4 py-2 rounded-lg text-sm font-medium text-yellow-700 border border-yellow-300">
                    <span class="font-bold">{{ $pendingRecords->total() }}</span> Total Pending
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500 flex items-center justify-center">
                    📊
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto p-8 bg-slate-100">
            <div class="max-w-5xl mx-auto">
                <!-- Alert Message -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Your Role:</strong> Review the submitted data and make any necessary corrections before posting to the database. You are the final checkpoint for data accuracy.
                            </p>
                        </div>
                    </div>
                </div>

                @if($pendingRecords->total() > 0)
                    @php $record = $pendingRecords->first(); @endphp
                    
                    <!-- Single Form Card -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden pending-record-card" data-id="{{ $record->id }}">
                        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                            <h3 class="text-lg font-bold text-slate-800">Submitted Labor Market Data</h3>
                            <p class="text-sm text-slate-600 mt-1">
                                Submitted by <span class="font-semibold">{{ $record->submittedBy->name ?? 'Admin' }}</span> 
                                on {{ $record->created_at->format('F d, Y \a\t h:i A') }}
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-slate-100 border-b-2 border-slate-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Field</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Submitted Value</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Verified Value (Editable)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-6 py-4 text-sm font-bold text-blue-900">Year</td>
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ $record->year }}</td>
                                        <td class="px-6 py-4"><input type="number" value="{{ $record->year }}" class="border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="year"></td>
                                    </tr>
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-6 py-4 text-sm font-bold text-blue-900">Month</td>
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ date('F', mktime(0, 0, 0, $record->month, 1)) }}</td>
                                        <td class="px-6 py-4">
                                            <select class="border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="month">
                                                @foreach([1=>'January', 4=>'April', 7=>'July', 10=>'October'] as $val => $name)
                                                    <option value="{{ $val }}" {{ $record->month == $val ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>

                                    <tr class="hover:bg-slate-50">
                                        <td class="px-6 py-4 text-sm font-bold text-blue-900">Household Population</td>
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ number_format($record->household_population) }}</td>
                                        <td class="px-6 py-4">
                                            <input type="number" value="{{ $record->household_population }}" class="calc-trigger calc-lfpr border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="household_population">
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-50 bg-blue-50/30">
                                        <td class="px-6 py-4 text-sm font-bold text-blue-900">LFPR (%)</td>
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ $record->labor_force_participation_rate + 0 }}%</td>
                                        <td class="px-6 py-4">
                                            <input type="number" step="0.01" value="{{ $record->labor_force_participation_rate + 0 }}" class="calc-trigger calc-lfpr border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="lfpr">
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-50 bg-blue-50/30">
                                        <td class="px-6 py-4 text-sm font-bold text-blue-900">Employment Rate (%)</td>
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ $record->employment_rate + 0 }}%</td>
                                        <td class="px-6 py-4">
                                            <input type="number" step="0.01" value="{{ $record->employment_rate + 0 }}" class="calc-trigger calc-employment-rate border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="employment_rate">
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-50 bg-blue-50/30">
                                        <td class="px-6 py-4 text-sm font-bold text-blue-900">Underemployment Rate (%)</td>
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ $record->underemployment_rate + 0 }}%</td>
                                        <td class="px-6 py-4">
                                            <input type="number" step="0.01" value="{{ $record->underemployment_rate + 0 }}" class="calc-trigger border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="underemployment_rate">
                                        </td>
                                    </tr>

                                    <tr class="hover:bg-slate-50 bg-blue-50/30">
                                        <td class="px-6 py-4 text-sm font-bold text-blue-900">Unemployment Rate (%)</td>
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ $record->unemployment_rate + 0 }}%</td>
                                        <td class="px-6 py-4">
                                            <input type="number" step="0.01" value="{{ $record->unemployment_rate + 0 }}" class="calc-trigger border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="unemployment_rate">
                                        </td>
                                    </tr>

                                    <tr class="bg-slate-100 italic">
                                        <td class="px-6 py-4 text-sm text-slate-500">Labor Force (Auto)</td>
                                        <td class="px-6 py-4 text-sm text-slate-400">{{ number_format($record->labor_force) }}</td>
                                        <td class="px-6 py-4">
                                            <input type="number" value="{{ $record->labor_force }}" class="output-labor-force bg-slate-200 border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="labor_force" readonly>
                                        </td>
                                    </tr>
                                    <tr class="bg-slate-100 italic">
                                        <td class="px-6 py-4 text-sm text-slate-500">Employed (Auto)</td>
                                        <td class="px-6 py-4 text-sm text-slate-400">{{ number_format($record->employed) }}</td>
                                        <td class="px-6 py-4">
                                            <input type="number" value="{{ $record->employed }}" class="output-employed bg-slate-200 border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="employed" readonly>
                                        </td>
                                    </tr>
                                    <tr class="bg-slate-100 italic">
                                        <td class="px-6 py-4 text-sm text-slate-500">Unemployed (Auto)</td>
                                        <td class="px-6 py-4 text-sm text-slate-400">{{ number_format($record->unemployed) }}</td>
                                        <td class="px-6 py-4">
                                            <input type="number" value="{{ $record->unemployed }}" class="output-unemployed bg-slate-200 border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="unemployed" readonly>
                                        </td>
                                    </tr>
                                    <tr class="bg-slate-100 italic">
                                        <td class="px-6 py-4 text-sm text-slate-500">Underemployed (Auto)</td>
                                        <td class="px-6 py-4 text-sm text-slate-400">{{ number_format($record->underemployed) }}</td>
                                        <td class="px-6 py-4">
                                            <input type="number" value="{{ $record->underemployed }}" class="output-underemployed bg-slate-200 border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="underemployed" readonly>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                            <!-- Post Button -->
                            <div class="flex justify-end mb-4">
                                <button type="button" onclick="postVerifiedData(this)" class="px-8 py-3 rounded-lg bg-green-600 text-white font-bold hover:bg-green-700 transition transform hover:scale-105">
                                    ✅ Post to Database
                                </button>
                            </div>

                            <!-- Pagination Controls -->
                            @if($pendingRecords->hasPages())
                            <div class="border-t border-slate-200 pt-4">
                                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                                    <!-- Page Info -->
                                    <div class="text-sm text-slate-600">
                                        Showing form <strong>{{ $pendingRecords->currentPage() }}</strong> of <strong>{{ $pendingRecords->total() }}</strong>
                                    </div>

                                    <!-- Pagination Buttons -->
                                    <div class="flex gap-2 flex-wrap justify-center">
                                        {{-- First Page --}}
                                        @if($pendingRecords->onFirstPage())
                                            <button disabled class="px-3 py-2 border border-slate-300 rounded text-slate-400 cursor-not-allowed text-sm">
                                                &laquo; First
                                            </button>
                                        @else
                                            <a href="{{ $pendingRecords->url(1) }}" class="px-3 py-2 border border-slate-300 rounded text-slate-700 hover:bg-slate-100 transition text-sm">
                                                &laquo; First
                                            </a>
                                        @endif

                                        {{-- Previous Page --}}
                                        @if($pendingRecords->onFirstPage())
                                            <button disabled class="px-3 py-2 border border-slate-300 rounded text-slate-400 cursor-not-allowed text-sm">
                                                &lsaquo; Prev
                                            </button>
                                        @else
                                            <a href="{{ $pendingRecords->previousPageUrl() }}" class="px-3 py-2 border border-slate-300 rounded text-slate-700 hover:bg-slate-100 transition text-sm">
                                                &lsaquo; Prev
                                            </a>
                                        @endif

                                        {{-- Page Numbers --}}
                                        @foreach(range(1, $pendingRecords->lastPage()) as $page)
                                            @if($page == $pendingRecords->currentPage())
                                                <button class="px-3 py-2 border-2 border-blue-600 bg-blue-600 text-white rounded font-bold text-sm">
                                                    {{ $page }}
                                                </button>
                                            @elseif($page === 1 || $page === $pendingRecords->lastPage() || abs($page - $pendingRecords->currentPage()) <= 2)
                                                <a href="{{ $pendingRecords->url($page) }}" class="px-3 py-2 border border-slate-300 rounded text-slate-700 hover:bg-slate-100 transition text-sm">
                                                    {{ $page }}
                                                </a>
                                            @elseif(abs($page - $pendingRecords->currentPage()) === 3)
                                                <span class="px-3 py-2 text-slate-400 text-sm">...</span>
                                            @endif
                                        @endforeach

                                        {{-- Next Page --}}
                                        @if($pendingRecords->hasMorePages())
                                            <a href="{{ $pendingRecords->nextPageUrl() }}" class="px-3 py-2 border border-slate-300 rounded text-slate-700 hover:bg-slate-100 transition text-sm">
                                                Next &rsaquo;
                                            </a>
                                        @else
                                            <button disabled class="px-3 py-2 border border-slate-300 rounded text-slate-400 cursor-not-allowed text-sm">
                                                Next &rsaquo;
                                            </button>
                                        @endif

                                        {{-- Last Page --}}
                                        @if($pendingRecords->hasMorePages())
                                            <a href="{{ $pendingRecords->url($pendingRecords->lastPage()) }}" class="px-3 py-2 border border-slate-300 rounded text-slate-700 hover:bg-slate-100 transition text-sm">
                                                Last &raquo;
                                            </a>
                                        @else
                                            <button disabled class="px-3 py-2 border border-slate-300 rounded text-slate-400 cursor-not-allowed text-sm">
                                                Last &raquo;
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-white p-12 text-center rounded-xl shadow">
                        <div class="text-6xl mb-4">🎉</div>
                        <p class="text-slate-500 text-lg font-medium">No pending data to review at this time.</p>
                        <p class="text-slate-400 text-sm mt-2">All submissions have been processed!</p>
                    </div>
                @endif
            </div>
        </main>
    </div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md mx-4 transform transition-all">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                <svg class="h-10 w-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Confirm Data Posting</h3>
            <p class="text-slate-600 mb-6">
                Are you sure you want to post this verified data to the database? This action cannot be undone.
            </p>
            <div class="flex gap-3">
                <button id="cancelBtn" class="flex-1 px-6 py-3 border-2 border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-100 transition">
                    Cancel
                </button>
                <button id="confirmBtn" class="flex-1 px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                    Yes, Post Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md mx-4 transform transition-all">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Data Posted Successfully!</h3>
            <p class="text-slate-600 mb-6">
                The verified data has been successfully posted to the database.
            </p>
            <button id="closeModalBtn" class="w-full px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                OK
            </button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md mx-4 transform transition-all">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Error</h3>
            <p id="errorMessage" class="text-slate-600 mb-6">
                <!-- Error message will be inserted here -->
            </p>
            <button id="closeErrorBtn" class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                OK
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    window.currentPostButton = null;

    // --- Calculation Logic ---
    function normalizeRate(value) {
        return value > 1 ? value / 100 : value;
    }

    function calculateLaborForce(card) {
        const household = parseFloat(card.querySelector('[data-field="household_population"]').value);
        const lfpr = parseFloat(card.querySelector('[data-field="lfpr"]').value);

        if (isNaN(household) || isNaN(lfpr)) {
            card.querySelector('[data-field="labor_force"]').value = '';
            return;
        }

        const laborForce = household * normalizeRate(lfpr);
        card.querySelector('[data-field="labor_force"]').value = Math.round(laborForce);
        
        calculateEmployed(card);
        calculateUnemployed(card);
    }

    function calculateEmployed(card) {
        const laborForce = parseFloat(card.querySelector('[data-field="labor_force"]').value);
        const employmentRate = parseFloat(card.querySelector('[data-field="employment_rate"]').value);

        if (isNaN(laborForce) || isNaN(employmentRate)) {
            card.querySelector('[data-field="employed"]').value = '';
            return;
        }

        const employed = laborForce * normalizeRate(employmentRate);
        card.querySelector('[data-field="employed"]').value = Math.round(employed);
        calculateUnderemployed(card);
    }

    function calculateUnderemployed(card) {
        const employed = parseFloat(card.querySelector('[data-field="employed"]').value);
        const underemploymentRate = parseFloat(card.querySelector('[data-field="underemployment_rate"]').value);

        if (isNaN(employed) || isNaN(underemploymentRate)) {
            card.querySelector('[data-field="underemployed"]').value = '';
            return;
        }

        const underemployed = employed * normalizeRate(underemploymentRate);
        card.querySelector('[data-field="underemployed"]').value = Math.round(underemployed);
    }

    function calculateUnemployed(card) {
        const laborForce = parseFloat(card.querySelector('[data-field="labor_force"]').value);
        const unemploymentRate = parseFloat(card.querySelector('[data-field="unemployment_rate"]').value);

        if (isNaN(laborForce) || isNaN(unemploymentRate)) {
            card.querySelector('[data-field="unemployed"]').value = '';
            return;
        }

        const unemployed = laborForce * normalizeRate(unemploymentRate);
        card.querySelector('[data-field="unemployed"]').value = Math.round(unemployed);
    }

    // Initialize calculations
    const card = document.querySelector('.pending-record-card');
    if (card) {
        calculateLaborForce(card);
    }

    // Handle input changes
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('calc-trigger')) {
            const card = e.target.closest('.pending-record-card');
            const field = e.target.getAttribute('data-field');
            
            if (field === 'household_population' || field === 'lfpr') {
                calculateLaborForce(card);
            } else if (field === 'employment_rate') {
                calculateEmployed(card);
            } else if (field === 'underemployment_rate') {
                calculateUnderemployed(card);
            } else if (field === 'unemployment_rate') {
                calculateUnemployed(card);
            }
        }
    });

    // Modal Controls
    document.getElementById('cancelBtn').addEventListener('click', function() {
        document.getElementById('confirmModal').classList.add('hidden');
        window.currentPostButton = null;
    });

    document.getElementById('closeModalBtn').addEventListener('click', function() {
        document.getElementById('successModal').classList.add('hidden');
        window.location.reload();
    });

    document.getElementById('closeErrorBtn').addEventListener('click', function() {
        document.getElementById('errorModal').classList.add('hidden');
    });

    // Submission Logic
    document.getElementById('confirmBtn').addEventListener('click', function() {
        const buttonElement = window.currentPostButton;
        if (!buttonElement) return;

        const card = buttonElement.closest('.pending-record-card');
        const pendingId = card.getAttribute('data-id');

        document.getElementById('confirmModal').classList.add('hidden');
        buttonElement.disabled = true;
        buttonElement.textContent = '🔄 Posting...';

        const verifiedData = {
            pending_id: pendingId,
            year: card.querySelector('[data-field="year"]').value,
            month: card.querySelector('[data-field="month"]').value,
            household_population: card.querySelector('[data-field="household_population"]').value,
            labor_force: card.querySelector('[data-field="labor_force"]').value,
            employed: card.querySelector('[data-field="employed"]').value,
            underemployed: card.querySelector('[data-field="underemployed"]').value,
            unemployed: card.querySelector('[data-field="unemployed"]').value,
            labor_force_participation_rate: Number(card.querySelector('[data-field="lfpr"]').value),
            employment_rate: Number(card.querySelector('[data-field="employment_rate"]').value),
            underemployment_rate: Number(card.querySelector('[data-field="underemployment_rate"]').value),
            unemployment_rate: Number(card.querySelector('[data-field="unemployment_rate"]').value)
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch('{{ route("statistician.labor-market.post") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(verifiedData)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                document.getElementById('successModal').classList.remove('hidden');
            } else {
                throw new Error(result.message || 'Failed to post data');
            }
        })
        .catch(error => {
            document.getElementById('errorMessage').textContent = error.message;
            document.getElementById('errorModal').classList.remove('hidden');
            buttonElement.disabled = false;
            buttonElement.textContent = '✅ Post to Database';
        })
        .finally(() => {
            window.currentPostButton = null;
        });
    });
});

window.postVerifiedData = function(buttonElement) {
    const card = buttonElement.closest('.pending-record-card');
    const year = card.querySelector('[data-field="year"]').value;
    const month = card.querySelector('[data-field="month"]').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    window.currentPostButton = buttonElement;
    buttonElement.disabled = true;
    buttonElement.textContent = '🔄 Checking...';

    // Use the FINAL database check route, not the pending check
    fetch('{{ route("statistician.labor-market.check.post") }}', {
        method: 'POST',
        headers: {  
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ year: year, month: month })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Server error: ${response.status}`);
        }
        return response.json();
    })
    .then(result => {
        console.log('Check result:', result); // Debug log
        
        // Check if duplicate exists in FINAL database
        if (result.exists === true) {
            // DUPLICATE: Show error modal
            document.getElementById('errorMessage').textContent = result.message || 'Data for this period already exists in the database.';
            document.getElementById('errorModal').classList.remove('hidden');
            buttonElement.disabled = false;
            buttonElement.textContent = '✅ Post to Database';
            window.currentPostButton = null;
        } else if (result.exists === false) {
            // NO DUPLICATE: Show confirmation modal
            buttonElement.disabled = false;
            buttonElement.textContent = '✅ Post to Database';
            document.getElementById('confirmModal').classList.remove('hidden');
        } else {
            // Unexpected response format
            throw new Error('Unexpected response format from server');
        }
    })
    .catch(error => {
        console.error('Error during check:', error);
        document.getElementById('errorMessage').textContent = `Error: ${error.message}. Please check console for details.`;
        document.getElementById('errorModal').classList.remove('hidden');
        buttonElement.disabled = false;
        buttonElement.textContent = '✅ Post to Database';
        window.currentPostButton = null;
    });
};

</script>
</body>
</html>