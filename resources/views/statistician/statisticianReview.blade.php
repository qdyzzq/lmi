<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>LMI - Statistician Review</title>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden" >
    @include('partials.statisticianSidebar')

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- HEADER -->
        <header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Pending Data Verification • Statistician</h2>
            <div class="flex items-center gap-4">
                <div class="bg-yellow-100 px-4 py-2 rounded-lg text-sm font-medium text-yellow-700 border border-yellow-300">
                    <span id="pending-badge-count" class="font-bold">{{ $pendingRecords->total() }}</span> Total Pending
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full border-2 border-blue-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
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

<!-- ─── Confirm Modal: Summary + Diff ──────────────────────────────────── -->
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    window.currentPostButton = null;

    // --- Number Formatting Helpers ---

    // Format a numeric string with commas, preserving decimals and trailing dot
    function formatWithCommas(str) {
        // Allow digits, one dot, and nothing else
        let clean = str.replace(/[^0-9.]/g, '');
        // Prevent multiple dots
        const parts = clean.split('.');
        if (parts.length > 2) clean = parts[0] + '.' + parts.slice(1).join('');
        const [intPart, decPart] = clean.split('.');
        const formatted = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return decPart !== undefined ? formatted + '.' + decPart : formatted;
    }

    // Get raw numeric value from a formatted-number input (strips commas)
    function getRaw(input) {
        return parseFloat(input.value.replace(/,/g, '')) || 0;
    }

    // Set formatted display value on a formatted-number input
    function setFormatted(input, rawValue) {
        if (rawValue === '' || isNaN(rawValue)) {
            input.value = '';
            return;
        }
        const rounded = Math.round(rawValue);
        input.value = rounded.toLocaleString('en-US');
    }

    // Attach live formatting to all .formatted-number inputs (not readonly)
    document.querySelectorAll('input.formatted-number:not([readonly])').forEach(function(input) {
        input.addEventListener('input', function(e) {
            const raw = e.target.value;
            const cursorPos = e.target.selectionStart;
            const prevLen = raw.length;

            // Allow typing decimals: don't reformat mid-decimal entry
            if (raw.endsWith('.') || raw.endsWith('.0') || /\.\d*0$/.test(raw)) {
                // Just clean non-numeric chars except dot, keep as-is for now
                const cleaned = raw.replace(/[^0-9.]/g, '');
                const cParts = cleaned.split('.');
                const intFormatted = cParts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                const newVal = cParts.length > 1 ? intFormatted + '.' + cParts[1] : intFormatted;
                e.target.value = newVal;
                return;
            }

            const formatted = formatWithCommas(raw);
            e.target.value = formatted;
            // Adjust cursor for added/removed commas
            const diff = formatted.length - prevLen;
            try { e.target.setSelectionRange(cursorPos + diff, cursorPos + diff); } catch(ex) {}
        });

        // On blur: fully clean up (e.g. trailing dot)
        input.addEventListener('blur', function(e) {
            const numVal = parseFloat(e.target.value.replace(/,/g, ''));
            if (!isNaN(numVal)) {
                e.target.value = numVal.toLocaleString('en-US', { maximumFractionDigits: 4 });
            }
        });
    });

    // --- Calculation Logic ---
    function normalizeRate(value) {
        return value > 1 ? value / 100 : value;
    }

    function getRawField(card, field) {
        const el = card.querySelector(`[data-field="${field}"]`);
        if (!el) return NaN;
        // formatted-number inputs need comma-stripped; number inputs read directly
        return parseFloat(el.value.replace(/,/g, ''));
    }

    function setAutoField(card, field, rawValue) {
        const el = card.querySelector(`[data-field="${field}"]`);
        if (!el) return;
        const rounded = Math.round(rawValue);
        el.value = rounded.toLocaleString('en-US');
    }

    function calculateLaborForce(card) {
        const household = getRawField(card, 'household_population');
        const lfpr = getRawField(card, 'lfpr');

        if (isNaN(household) || isNaN(lfpr)) {
            card.querySelector('[data-field="labor_force"]').value = '';
            return;
        }

        const laborForce = household * normalizeRate(lfpr);
        setAutoField(card, 'labor_force', laborForce);
        
        calculateEmployed(card);
        calculateUnemployed(card);
    }

    function calculateEmployed(card) {
        const laborForce = getRawField(card, 'labor_force');
        const employmentRate = getRawField(card, 'employment_rate');

        if (isNaN(laborForce) || isNaN(employmentRate)) {
            card.querySelector('[data-field="employed"]').value = '';
            return;
        }

        const employed = laborForce * normalizeRate(employmentRate);
        setAutoField(card, 'employed', employed);
        calculateUnderemployed(card);
    }

    function calculateUnderemployed(card) {
        const employed = getRawField(card, 'employed');
        const underemploymentRate = getRawField(card, 'underemployment_rate');

        if (isNaN(employed) || isNaN(underemploymentRate)) {
            card.querySelector('[data-field="underemployed"]').value = '';
            return;
        }

        const underemployed = employed * normalizeRate(underemploymentRate);
        setAutoField(card, 'underemployed', underemployed);
    }

    function calculateUnemployed(card) {
        const laborForce = getRawField(card, 'labor_force');
        const unemploymentRate = getRawField(card, 'unemployment_rate');

        if (isNaN(laborForce) || isNaN(unemploymentRate)) {
            card.querySelector('[data-field="unemployed"]').value = '';
            return;
        }

        const unemployed = laborForce * normalizeRate(unemploymentRate);
        setAutoField(card, 'unemployed', unemployed);
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
        buttonElement.innerHTML = '<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Posting...';

        const verifiedData = {
            pending_id: pendingId,
            year: card.querySelector('[data-field="year"]').value,
            month: card.querySelector('[data-field="month"]').value,
            household_population: parseFloat(card.querySelector('[data-field="household_population"]').value.replace(/,/g, '')),
            labor_force: parseFloat(card.querySelector('[data-field="labor_force"]').value.replace(/,/g, '')),
            employed: parseFloat(card.querySelector('[data-field="employed"]').value.replace(/,/g, '')),
            underemployed: parseFloat(card.querySelector('[data-field="underemployed"]').value.replace(/,/g, '')),
            unemployed: parseFloat(card.querySelector('[data-field="unemployed"]').value.replace(/,/g, '')),
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
            buttonElement.innerHTML = '<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Post to Database';
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
    buttonElement.innerHTML = '<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Checking...';

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
        if (!response.ok) throw new Error(`Server error: ${response.status}`);
        return response.json();
    })
    .then(result => {
        buttonElement.disabled = false;
        buttonElement.innerHTML = '<svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Approve';

        if (result.exists === true) {
            document.getElementById('errorMessage').textContent = result.message || 'Data for this period already exists in the database.';
            document.getElementById('errorModal').classList.remove('hidden');
            window.currentPostButton = null;
        } else if (result.exists === false) {
            populateConfirmSummary(card);
            document.getElementById('confirmModal').classList.remove('hidden');
        } else {
            throw new Error('Unexpected response format from server');
        }
    })
    .catch(error => {
        console.error('Error during check:', error);
        document.getElementById('errorMessage').textContent = `Error: ${error.message}.`;
        document.getElementById('errorModal').classList.remove('hidden');
        buttonElement.disabled = false;
        buttonElement.innerHTML = '<svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Approve';
        window.currentPostButton = null;
    });
};

// ─── Populate the confirm modal with summary / diff ──────────────────────
window.populateConfirmSummary = function(card) {
    const monthNames = { 1: 'January', 4: 'April', 7: 'July', 10: 'October' };

    // ── Collect submitted values from the "Submitted Value" td (2nd column) ──
    const rows = card.querySelectorAll('tbody tr');
    const submittedMap = {};
    rows.forEach(row => {
        const input = row.querySelector('[data-field]');
        if (!input) return;
        const f   = input.getAttribute('data-field');
        const tds = row.querySelectorAll('td');
        if (tds.length >= 2) {
            submittedMap[f] = tds[1].textContent.trim().replace(/%$/, '').replace(/,/g, '').trim();
        }
    });

    // ── Year & Month: string comparison ──────────────────────────────────────
    const verifiedYear  = card.querySelector('[data-field="year"]').value.trim();
    const verifiedMonth = parseInt(card.querySelector('[data-field="month"]').value);
    const submittedYear = (submittedMap['year']  ?? '').trim();
    const submittedMonth = parseInt(submittedMap['month'] ?? '');

    const yearChanged  = submittedYear  !== '' && submittedYear  !== verifiedYear;
    const monthChanged = !isNaN(submittedMonth) && submittedMonth !== verifiedMonth;

    // ── Reporting Period banner ───────────────────────────────────────────────
    const periodEl = document.getElementById('summaryPeriod');
    if (yearChanged || monthChanged) {
        const oldPeriod = `${monthNames[submittedMonth] ?? submittedMonth} ${submittedYear}`;
        const newPeriod = `${monthNames[verifiedMonth]  ?? verifiedMonth} ${verifiedYear}`;
        periodEl.innerHTML = `
            <span class="line-through text-blue-400 font-normal text-[13px]">${oldPeriod}</span>
            <svg class="inline w-3.5 h-3.5 text-amber-500 mx-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            <span class="text-amber-700">${newPeriod}</span>
        `;
    } else {
        periodEl.textContent = `${monthNames[verifiedMonth] ?? verifiedMonth} ${verifiedYear}`;
    }

    // ── Numeric field definitions ─────────────────────────────────────────────
    const fields = [
        { label: 'Household Population',           field: 'household_population', unit: '',  auto: false },
        { label: 'Labor Force Participation Rate',  field: 'lfpr',                unit: '%', auto: false },
        { label: 'Employment Rate',                 field: 'employment_rate',      unit: '%', auto: false },
        { label: 'Underemployment Rate',            field: 'underemployment_rate', unit: '%', auto: false },
        { label: 'Unemployment Rate',               field: 'unemployment_rate',    unit: '%', auto: false },
        { label: 'Labor Force',                     field: 'labor_force',          unit: '',  auto: true  },
        { label: 'Employed',                        field: 'employed',             unit: '',  auto: true  },
        { label: 'Underemployed',                   field: 'underemployed',        unit: '',  auto: true  },
        { label: 'Unemployed',                      field: 'unemployed',           unit: '',  auto: true  },
    ];

    // ── Count total edits (year + month + numeric fields) ────────────────────
    let editedCount = (yearChanged ? 1 : 0) + (monthChanged ? 1 : 0);
    const container = document.getElementById('summaryRows');
    container.innerHTML = '';

    // ── Year row ─────────────────────────────────────────────────────────────
    const yearRow = document.createElement('div');
    if (yearChanged) {
        yearRow.className = 'grid grid-cols-[1fr_auto_auto] gap-2 items-center px-3 py-2.5 rounded-lg bg-amber-50 border border-amber-200';
        yearRow.innerHTML = `
            <span class="text-[12.5px] font-medium text-slate-700 flex items-center gap-1.5">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                Year
            </span>
            <span class="text-[12px] text-slate-400 line-through text-right w-28">${submittedYear}</span>
            <span class="text-[13px] font-bold text-amber-700 text-right w-28">${verifiedYear}</span>
        `;
    } else {
        yearRow.className = 'flex justify-between items-center px-3 py-2 rounded-lg bg-slate-50';
        yearRow.innerHTML = `
            <span class="text-[12.5px] font-medium text-slate-600">Year</span>
            <span class="text-[13px] font-bold text-slate-800">${verifiedYear}</span>
        `;
    }
    container.appendChild(yearRow);

    // ── Month row ─────────────────────────────────────────────────────────────
    const monthRow = document.createElement('div');
    if (monthChanged) {
        monthRow.className = 'grid grid-cols-[1fr_auto_auto] gap-2 items-center px-3 py-2.5 rounded-lg bg-amber-50 border border-amber-200';
        monthRow.innerHTML = `
            <span class="text-[12.5px] font-medium text-slate-700 flex items-center gap-1.5">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                Month
            </span>
            <span class="text-[12px] text-slate-400 line-through text-right w-28">${monthNames[submittedMonth] ?? submittedMonth}</span>
            <span class="text-[13px] font-bold text-amber-700 text-right w-28">${monthNames[verifiedMonth] ?? verifiedMonth}</span>
        `;
    } else {
        monthRow.className = 'flex justify-between items-center px-3 py-2 rounded-lg bg-slate-50';
        monthRow.innerHTML = `
            <span class="text-[12.5px] font-medium text-slate-600">Month</span>
            <span class="text-[13px] font-bold text-slate-800">${monthNames[verifiedMonth] ?? verifiedMonth}</span>
        `;
    }
    container.appendChild(monthRow);

    // ── Divider between period fields and indicators ──────────────────────────
    const divider = document.createElement('hr');
    divider.className = 'border-slate-200 my-1';
    container.appendChild(divider);

    fields.forEach(f => {
        const inputEl = card.querySelector(`[data-field="${f.field}"]`);
        if (!inputEl) return;

        const verifiedRaw  = parseFloat(inputEl.value.replace(/,/g, '')) || 0;
        const submittedRaw = parseFloat(submittedMap[f.field] ?? '');
        const isEdited     = !isNaN(submittedRaw) && submittedRaw !== verifiedRaw;

        if (isEdited) editedCount++;

        const fmtVerified  = isNaN(verifiedRaw)  ? '—' : (f.unit === '%' ? verifiedRaw + '%'  : verifiedRaw.toLocaleString());
        const fmtSubmitted = isNaN(submittedRaw) ? '—' : (f.unit === '%' ? submittedRaw + '%' : submittedRaw.toLocaleString());

        // Delta badge
        let deltaBadge = '';
        if (isEdited) {
            const delta = verifiedRaw - submittedRaw;
            const sign  = delta > 0 ? '+' : '';
            const color = delta > 0 ? 'text-green-700 bg-green-50 border-green-200' : 'text-red-700 bg-red-50 border-red-200';
            const deltaFmt = f.unit === '%'
                ? sign + delta.toFixed(2) + '%'
                : sign + delta.toLocaleString();
            deltaBadge = `<span class="text-[11px] font-semibold px-1.5 py-0.5 rounded border ${color} ml-1">${deltaFmt}</span>`;
        }

        const row = document.createElement('div');

        if (isEdited) {
            // Comparison layout: 3-col grid — label | submitted (strikethrough) | verified + delta
            row.className = 'grid grid-cols-[1fr_auto_auto] gap-2 items-center px-3 py-2.5 rounded-lg bg-amber-50 border border-amber-200';
            row.innerHTML = `
                <span class="text-[12.5px] font-medium text-slate-700 flex items-center gap-1.5">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                    ${f.auto ? '<span class="text-[10px] font-semibold text-teal-600 bg-teal-50 border border-teal-200 px-1.5 py-0.5 rounded">auto</span>' : ''}
                    ${f.label}
                </span>
                <span class="text-[12px] text-slate-400 line-through text-right w-28">${fmtSubmitted}</span>
                <span class="text-[13px] font-bold text-amber-700 text-right w-28 flex items-center justify-end gap-1">
                    ${fmtVerified}${deltaBadge}
                </span>
            `;
        } else {
            // Simple row — no difference
            row.className = 'flex justify-between items-center px-3 py-2 rounded-lg ' + (f.auto ? 'bg-emerald-50' : 'bg-slate-50');
            row.innerHTML = `
                <span class="text-[12.5px] font-medium text-slate-600 flex items-center gap-1.5">
                    ${f.auto ? '<span class="text-[10px] font-semibold text-teal-600 bg-teal-50 border border-teal-200 px-1.5 py-0.5 rounded">auto</span>' : ''}
                    ${f.label}
                </span>
                <span class="text-[13px] font-bold ${f.auto ? 'text-teal-700' : 'text-slate-800'}">${fmtVerified}</span>
            `;
        }

        container.appendChild(row);
    });

    // Show/hide edit badge and column headers based on whether anything was changed
    const editBadge         = document.getElementById('editBadge');
    const comparisonHeader  = document.getElementById('comparisonHeader');
    const summaryOnlyHeader = document.getElementById('summaryOnlyHeader');

    if (editedCount > 0) {
        editBadge.classList.remove('hidden');
        editBadge.classList.add('flex');
        document.getElementById('editBadgeText').textContent = `${editedCount} field${editedCount > 1 ? 's' : ''} edited by statistician — highlighted below`;
        comparisonHeader.classList.remove('hidden');
        summaryOnlyHeader.classList.add('hidden');
    } else {
        editBadge.classList.add('hidden');
        editBadge.classList.remove('flex');
        comparisonHeader.classList.add('hidden');
        summaryOnlyHeader.classList.remove('hidden');
    }
};



</script>

<!-- ─── Live Polling — detect new pending submissions every 30s ─────── -->
<script>
(function () {
    let knownPending   = parseInt('{{ $pendingRecords->total() }}');
    const POLL_INTERVAL = 30_000;
    let accumulatedNew  = 0;
    let notifToast      = null;

    function fetchCounts() {
        fetch('{{ route("statistician.labor-market.pending-count") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (!data) return;
            const newPending = parseInt(data.pending ?? 0);

            // Update the header badge live
            const badge = document.getElementById('pending-badge-count');
            if (badge) badge.textContent = newPending;

            if (newPending > knownPending) {
                accumulatedNew += (newPending - knownPending);
                showOrUpdateNotifToast();
            }
            knownPending = newPending;
        })
        .catch(() => {});
    }

    function showOrUpdateNotifToast() {
        const msgText   = `[!] ${accumulatedNew} new pending record${accumulatedNew > 1 ? 's' : ''} submitted — click to refresh`;
        const container = document.getElementById('toastContainer');

        if (notifToast && container.contains(notifToast)) {
            notifToast.querySelector('.notif-text').textContent = msgText;
            notifToast.classList.add('scale-105');
            setTimeout(() => notifToast.classList.remove('scale-105'), 200);
            return;
        }

        notifToast = document.createElement('div');
        notifToast.className = [
            'pointer-events-auto w-full rounded-xl shadow-xl overflow-hidden',
            'border-l-4 border-blue-500 bg-blue-50',
            'transform transition-all duration-300 translate-x-full opacity-0',
            'cursor-pointer hover:shadow-2xl hover:scale-[1.02] active:scale-[0.99]',
            'transition-transform'
        ].join(' ');

        notifToast.innerHTML = `
            <div class="flex items-center gap-3 px-4 py-4">
                <span class="relative flex-shrink-0 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                </span>
                <p class="notif-text text-sm font-semibold text-blue-800 flex-1 leading-snug">${msgText}</p>
                <button class="notif-dismiss text-blue-400 hover:text-blue-700 transition ml-1 flex-shrink-0" title="Dismiss">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;

        // Click toast body → reload page
        notifToast.addEventListener('click', function (e) {
            if (e.target.closest('.notif-dismiss')) return;
            dismissNotifToast();
            window.location.reload();
        });

        // Dismiss button → close only
        notifToast.querySelector('.notif-dismiss').addEventListener('click', function (e) {
            e.stopPropagation();
            dismissNotifToast();
        });

        container.appendChild(notifToast);
        requestAnimationFrame(() => requestAnimationFrame(() => {
            notifToast.classList.remove('translate-x-full', 'opacity-0');
        }));
    }

    function dismissNotifToast() {
        if (!notifToast) return;
        notifToast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            notifToast?.remove();
            notifToast = null;
            accumulatedNew = 0;
        }, 300);
    }

    setInterval(fetchCounts, POLL_INTERVAL);
})();
</script>

<!-- TOAST NOTIFICATION CONTAINER -->
<div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none" style="min-width: 340px;"></div>

</body>
</html>