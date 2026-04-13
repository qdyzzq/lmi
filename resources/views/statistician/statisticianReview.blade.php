<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')

    {{-- Load our JS before Alpine so all functions are defined when Alpine boots --}}
    @vite('resources/js/statistician/statistician-review.js')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>LMI - Statistician Review</title>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden" >
    @include('partials.statisticianSidebar')

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- HEADER -->
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800">Pending Data Verification <span class="text-slate-400 font-normal">• Statistician</span></h2>
            <div class="flex items-center gap-4">
                <div class="bg-yellow-100 px-3 py-1.5 rounded-lg text-xs font-medium text-yellow-700 border border-yellow-300">
                    <span id="pending-badge-count" class="font-bold">{{ $pendingRecords->total() }}</span> Total Pending
                </div>
                <div class="bg-slate-100 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200"><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Region XI • {{ date('Y') }}</div>
                <div class="w-9 h-9 bg-blue-100 rounded-full border-2 border-blue-500"></div>
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
                                            <input type="text" inputmode="decimal" value="{{ number_format($record->household_population) }}" class="formatted-number calc-trigger calc-lfpr border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="household_population" data-raw="{{ $record->household_population }}">
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
                                            <input type="text" value="{{ number_format($record->labor_force) }}" class="output-labor-force formatted-number bg-slate-200 border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="labor_force" data-raw="{{ $record->labor_force }}" readonly>
                                        </td>
                                    </tr>
                                    <tr class="bg-slate-100 italic">
                                        <td class="px-6 py-4 text-sm text-slate-500">Employed (Auto)</td>
                                        <td class="px-6 py-4 text-sm text-slate-400">{{ number_format($record->employed) }}</td>
                                        <td class="px-6 py-4">
                                            <input type="text" value="{{ number_format($record->employed) }}" class="output-employed formatted-number bg-slate-200 border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="employed" data-raw="{{ $record->employed }}" readonly>
                                        </td>
                                    </tr>
                                    <tr class="bg-slate-100 italic">
                                        <td class="px-6 py-4 text-sm text-slate-500">Unemployed (Auto)</td>
                                        <td class="px-6 py-4 text-sm text-slate-400">{{ number_format($record->unemployed) }}</td>
                                        <td class="px-6 py-4">
                                            <input type="text" value="{{ number_format($record->unemployed) }}" class="output-unemployed formatted-number bg-slate-200 border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="unemployed" data-raw="{{ $record->unemployed }}" readonly>
                                        </td>
                                    </tr>
                                    <tr class="bg-slate-100 italic">
                                        <td class="px-6 py-4 text-sm text-slate-500">Underemployed (Auto)</td>
                                        <td class="px-6 py-4 text-sm text-slate-400">{{ number_format($record->underemployed) }}</td>
                                        <td class="px-6 py-4">
                                            <input type="text" value="{{ number_format($record->underemployed) }}" class="output-underemployed formatted-number bg-slate-200 border border-slate-300 rounded px-3 py-1 text-sm w-full max-w-[200px] font-bold" data-field="underemployed" data-raw="{{ $record->underemployed }}" readonly>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                            <!-- Post Button -->
                            <div class="flex justify-end mb-4">
                                <button type="button" onclick="postVerifiedData(this)" class="px-8 py-3 rounded-lg bg-green-600 text-white font-bold hover:bg-green-700 transition transform hover:scale-105">
                                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Approve
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
                        <div class="mb-4"><svg class="w-16 h-16 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                        <p class="text-slate-500 text-lg font-medium">No pending data to review at this time.</p>
                        <p class="text-slate-400 text-sm mt-2">All submissions have been processed!</p>
                    </div>
                @endif
            </div>
        </main>
    </div>

<!-- ─── Confirm Modal: Summary + Diff ──────────────────────────────────────── -->
<div id="confirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background-color: rgba(0,0,0,0.25); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl mx-4 max-h-[90vh] flex flex-col">

        <!-- Header -->
        <div class="px-7 pt-6 pb-5 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-green-50 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-[16px] font-bold text-slate-800 leading-tight">Review Before Posting</h3>
                    <p class="text-[11.5px] text-slate-500 mt-0.5">Verify all values are correct before posting to the database.</p>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="overflow-y-auto px-7 py-5 flex-1">

            <!-- Reporting Period -->
            <div class="flex items-center gap-3 bg-gradient-to-r from-slate-50 to-blue-50 border border-slate-200 rounded-xl px-4 py-3 mb-5">
                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <div>
                    <p class="text-[10.5px] font-700 uppercase tracking-wide text-blue-400 leading-none mb-0.5">Reporting Period</p>
                    <p class="text-[14px] font-bold text-blue-800" id="summaryPeriod">—</p>
                </div>
            </div>

            <!-- Edit summary badge — only shown when there are edits -->
            <div id="editBadge" class="hidden mb-4 flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2.5">
                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <p class="text-[12.5px] font-semibold text-amber-800" id="editBadgeText">— fields edited</p>
            </div>

            <!-- Column headers (only shown when there are edits) -->
            <div id="comparisonHeader" class="hidden grid grid-cols-[1fr_auto_auto] gap-2 px-3 mb-1">
                <span class="text-[10.5px] font-700 uppercase tracking-widest text-slate-400">Field</span>
                <span class="text-[10.5px] font-700 uppercase tracking-widest text-slate-400 text-right w-28">Admin Submitted</span>
                <span class="text-[10.5px] font-700 uppercase tracking-widest text-slate-400 text-right w-28">Verified Value</span>
            </div>

            <!-- Summary rows -->
            <p id="summaryOnlyHeader" class="text-[11px] font-700 uppercase tracking-widest text-slate-400 mb-3">Labor Market Indicators</p>
            <div class="space-y-2" id="summaryRows"></div>

            <!-- Warning note -->
            <div class="mt-5 flex items-start gap-2 bg-red-50 border border-red-200 rounded-lg px-3 py-2.5">
                <svg class="w-4 h-4 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-[12px] text-red-700 leading-snug">This action will <strong>post data permanently</strong> to the database and cannot be undone.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-7 py-5 border-t border-slate-100 flex gap-3 shrink-0">
            <button id="cancelBtn" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition text-sm">
                Cancel
            </button>
            <button id="confirmBtn" class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Yes, Post Data
            </button>
        </div>
    </div>
</div>


<!-- Success Modal -->
<div id="successModal" class="hidden fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.1); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
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
<div id="errorModal" class="hidden fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.1); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
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

{{-- ─── Blade → JS Data Bridge ─────────────────────────────────────────────── --}}
{{-- Route URLs (cannot live in .js) and initial PHP values are passed here.    --}}
<script>
    window._statisticianRoutes = {
        post:         '{{ route("statistician.labor-market.post") }}',
        checkPost:    '{{ route("statistician.labor-market.check.post") }}',
        pendingCount: '{{ route("statistician.labor-market.pending-count") }}',
    };

    window._statisticianData = {
        pendingCount: {{ $pendingRecords->total() }},
    };
</script>

<!-- TOAST NOTIFICATION CONTAINER -->
<div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none" style="min-width: 340px;"></div>

</body>
</html>