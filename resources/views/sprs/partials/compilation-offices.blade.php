{{--
    resources/views/sprs/partials/compilation-offices.blade.php

    The tier below, when it doesn't file for itself.

    RO's children are field offices: they review rather than report, so
    they never show up in a list built from submissions. Listing the
    fifteen PESOs beneath them instead was the wrong unit — RO wants to
    see that Davao del Sur hasn't sent theirs, and to click through for
    which of its offices is holding it up.

    Expects: $offices (from fieldOfficeSummaries()), $year, $month
--}}

@php
    $states = [
        'awaiting'        => ['Awaiting your approval',  'bg-amber-100 text-amber-800'],
        'approved'        => ['Approved',                'bg-green-100 text-green-800'],
        'partly_approved' => ['Partly approved',         'bg-green-50 text-green-700'],
        'with_fo'         => ['Still with them',         'bg-slate-100 text-slate-600'],
        'nothing'         => ['Nothing submitted',       'bg-red-50 text-red-700'],
        'no_offices'      => ['No offices',              'bg-slate-100 text-slate-500'],
    ];
@endphp

<section>
    <p class="text-[11.5px] font-semibold uppercase tracking-wide text-slate-400 mb-2">
        Office by office
    </p>

    @forelse ($offices as $office)
        @php [$label, $cls] = $states[$office->state] ?? $states['nothing']; @endphp

        <div class="bg-white border rounded-xl px-5 py-3.5 mb-1.5
                    {{ $office->state === 'awaiting' ? 'border-amber-300' : 'border-slate-200' }}">
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div class="min-w-0">
                    {{-- Clicking through opens that office's own
                         compilation, which is the view its own staff see.
                         That's where the detail belongs. --}}
                    {{-- `expand` opens the first waiting card on arrival.
                         Clicking a field office is a request to see its
                         figures, and landing on a collapsed list means
                         one more click to reach what you asked for. --}}
                    <a href="{{ route('sprs.compilation', ['office' => $office->id, 'year' => $year, 'month' => $month, 'expand' => 1]) }}"
                       class="text-[14.5px] font-semibold text-blue-600 hover:underline">
                        {{ $office->name }}
                    </a>

                    <p class="text-[12.5px] text-slate-500 mt-0.5">
                        @php
                            $parts = [];
                            if ($office->awaiting)    $parts[] = $office->awaiting . ' awaiting approval';
                            if ($office->approved)    $parts[] = $office->approved . ' approved';
                            if ($office->with_fo)     $parts[] = $office->with_fo . ' still with them';
                            if ($office->drafts)      $parts[] = $office->drafts . ' in progress';
                            if ($office->not_started) $parts[] = $office->not_started . ' not started';
                        @endphp

                        @if ($office->total === 0)
                            No offices under this one yet.
                        @else
                            {{ implode(' · ', $parts) }}
                        @endif
                    </p>
                </div>

                <span class="px-2 py-0.5 rounded text-[11px] font-semibold whitespace-nowrap {{ $cls }}">
                    {{ $label }}
                </span>
            </div>
        </div>
    @empty
        <p class="text-[13px] text-slate-400 py-6 text-center">No offices in scope for this period.</p>
    @endforelse
</section>