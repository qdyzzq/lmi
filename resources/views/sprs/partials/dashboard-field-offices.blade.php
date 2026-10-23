{{--
    resources/views/sprs/partials/dashboard-field-offices.blade.php

    RO's view: one card per field office, not twenty-one PESO rows.

    RO thinks in field offices — "Davao del Sur is done, Davao City
    hasn't sent theirs yet" — and approval happens per field office,
    because the six won't all endorse on the same day.

    Clicking a name opens that field office's own breakdown, which is
    exactly the view its FO sees.

    Expects: $root, $fieldOffices, $year, $month, $years,
             $deadlineLabel, $daysLeft, $isCurrentPeriod

    $canApprove is no longer read here — approving happens on the
    compilation page, beside the figures being approved.
--}}

@php
    $states = [
        'awaiting'        => ['Awaiting your approval', 'bg-amber-100 text-amber-800'],
        'approved'        => ['Approved',               'bg-green-100 text-green-800'],
        'partly_approved' => ['Partly approved',        'bg-green-50 text-green-700'],
        'with_fo'         => ['Still with field office','bg-slate-100 text-slate-600'],
        'nothing'         => ['Nothing submitted',      'bg-red-50 text-red-700'],
        'no_offices'      => ['No offices',             'bg-slate-100 text-slate-500'],
    ];

    $waiting = $fieldOffices->where('state', 'awaiting');
@endphp

<section class="mb-8">

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-[13.5px] text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @error('review')
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-[13.5px] text-red-700">
            {{ $message }}
        </div>
    @enderror

    {{-- HEADER

         Carried over from the summary partial, which RO no longer
         renders. Without it RO loses the office name, the deadline and
         the period picker along with the flat list. --}}
    <div class="flex items-baseline justify-between gap-3 flex-wrap mb-4">
        <div>
            <h2 class="text-[18px] font-semibold text-slate-900">{{ $root->name }}</h2>
            <p class="text-[13px] text-slate-500 mt-0.5">
                {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                @unless ($isCurrentPeriod)
                    · <span class="text-slate-400">closed</span>
                @endunless
                @if ($daysLeft !== null)
                    ·
                    @if ($daysLeft < 0)
                        <span class="text-red-600">your deadline was {{ $deadlineLabel }}</span>
                    @elseif ($daysLeft === 0)
                        <span class="text-red-600">today is your deadline</span>
                    @else
                        {{ $daysLeft }} day{{ $daysLeft === 1 ? '' : 's' }} until your deadline
                    @endif
                @endif
            </p>
        </div>

        <a href="{{ route('sprs.compilation', ['year' => $year, 'month' => $month]) }}"
           class="text-[13px] font-medium text-blue-600 hover:underline">
            View all offices →
        </a>
    </div>

    {{-- PERIOD — same controls as the Submissions page. --}}
    <form method="GET" class="flex items-end gap-3 mb-5 flex-wrap">
        <div class="flex flex-col gap-1">
            <label class="text-[12.5px] font-medium text-ink-600">Month</label>
            <select name="month"
                class="h-10 px-3 border-2 border-hairline rounded-lg text-[13.5px] font-semibold text-teal-900 focus:outline-none focus:border-teal-600">
                @foreach (range(1, 12) as $m)
                    <option value="{{ $m }}" @selected($m === $month)>
                        {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-[12.5px] font-medium text-ink-600">Year</label>
            <select name="year"
                class="h-10 px-3 border-2 border-hairline rounded-lg text-[13.5px] font-semibold text-teal-900 focus:outline-none focus:border-teal-600">
                @foreach ($years as $y)
                    <option value="{{ $y }}" @selected($y === $year)>{{ $y }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit"
            class="h-10 px-4 rounded-lg text-[13.5px] font-semibold text-white bg-teal-700 hover:bg-teal-900">
            Show
        </button>

        @unless ($isCurrentPeriod)
            <a href="{{ route('sprs.dashboard') }}"
               class="h-10 flex items-center text-[13px] font-semibold text-teal-700 hover:underline">
                Back to this month
            </a>
        @endunless
    </form>

    <div class="flex items-baseline justify-between gap-3 flex-wrap mb-3">
        <h3 class="text-[15px] font-semibold text-slate-900">Field offices</h3>
        <p class="text-[13px] text-slate-500">
            @if ($waiting->isNotEmpty())
                {{ $waiting->count() }} awaiting your approval
            @else
                Nothing awaiting your approval
            @endif
        </p>
    </div>

    <div class="grid gap-3" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr))">
        @foreach ($fieldOffices as $fo)
            @php [$stateLabel, $stateCls] = $states[$fo->state] ?? $states['nothing']; @endphp

            <div class="bg-white border border-slate-200 rounded-xl px-5 py-4
                        {{ $fo->state === 'awaiting' ? 'ring-2 ring-amber-200' : '' }}">

                <div class="flex items-start justify-between gap-3 mb-2">
                    <a href="{{ route('sprs.compilation', ['office' => $fo->id, 'year' => $year, 'month' => $month, 'expand' => 1]) }}"
                       class="text-[14.5px] font-semibold text-blue-600 hover:underline">
                        {{ $fo->name }}
                    </a>
                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold whitespace-nowrap {{ $stateCls }}">
                        {{ $stateLabel }}
                    </span>
                </div>

                {{-- The breakdown of the breakdown. A single "13 of 15"
                     hides whether the missing two are still being typed
                     or were never started. --}}
                <div class="text-[12.5px] text-slate-500 space-y-0.5">
                    @if ($fo->awaiting)
                        <p><b class="font-semibold text-slate-800">{{ $fo->awaiting }}</b> awaiting approval</p>
                    @endif
                    @if ($fo->approved)
                        <p>{{ $fo->approved }} approved</p>
                    @endif
                    @if ($fo->with_fo)
                        <p>{{ $fo->with_fo }} still with the field office</p>
                    @endif
                    @if ($fo->drafts)
                        <p>{{ $fo->drafts }} in progress</p>
                    @endif
                    @if ($fo->not_started)
                        <p class="text-red-600">{{ $fo->not_started }} not started</p>
                    @endif
                    @if ($fo->total === 0)
                        <p>No offices under this field office yet.</p>
                    @endif
                </div>

                {{-- A link, not an approve button.

                     Approving from here would mean approving without
                     having read anything — the card carries counts, not
                     figures. The approve action lives on the compilation
                     page, next to the numbers it's approving. --}}
                @if ($fo->awaiting > 0)
                    <a href="{{ route('sprs.compilation', ['office' => $fo->id, 'year' => $year, 'month' => $month, 'expand' => 1]) }}"
                       class="mt-3 block text-center px-4 py-2 rounded-lg text-[13px] font-semibold text-white bg-teal-700 hover:bg-teal-900">
                        Review {{ $fo->awaiting }} report{{ $fo->awaiting === 1 ? '' : 's' }}
                    </a>
                @else
                    <a href="{{ route('sprs.compilation', ['office' => $fo->id, 'year' => $year, 'month' => $month, 'expand' => 1]) }}"
                       class="mt-3 block text-center px-4 py-2 rounded-lg text-[13px] font-semibold text-teal-700 border-2 border-slate-200 hover:bg-slate-50">
                        View reports
                    </a>
                @endif
            </div>
        @endforeach
    </div>

</section>