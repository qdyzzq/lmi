{{--
    resources/views/sprs/partials/dashboard-summary.blade.php

    The at-a-glance panel for anyone overseeing offices — three counts and
    the list of who hasn't filed.

    Deliberately not the wide figure table. That's a report, and it lives
    on the Submissions page. This answers one question: is anything waiting
    on me today?

    Expects: $root, $completion, $outstanding, $year, $month, $years,
             $canOpenForm, $deadlineLabel, $daysLeft, $isCurrentPeriod
--}}

@php
    $submitted = ($completion['by_status']['submitted'] ?? 0)
               + ($completion['by_status']['endorsed_to_ro'] ?? 0)
               + ($completion['by_status']['approved'] ?? 0)
               + ($completion['by_status']['rejected'] ?? 0);

    $drafts     = $completion['by_status']['draft'] ?? 0;
    $notStarted = $completion['total'] - $submitted - $drafts;
@endphp

<section class="mb-6">

    {{-- HEADER --}}
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
            View full submissions →
        </a>
    </div>

    {{-- PERIOD

         Same controls as the Submissions page — labelled selects and an
         explicit Show button. Two different-looking filters doing the
         same job on adjacent pages is the kind of thing people notice
         without being able to say why it feels off.

         A GET form, so the chosen month lands in the URL and can be
         bookmarked or sent to someone. --}}
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

    {{-- COUNTS

         Numbers in boxes, not charts. Three values don't need a pie — it
         would take five times the space and read slower. --}}
    <div class="grid gap-3 mb-5" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr))">
        <div class="rounded-lg bg-slate-50 px-4 py-3">
            <p class="text-[13px] text-slate-500">Submitted</p>
            <p class="text-[24px] font-semibold text-slate-900 mt-0.5">
                {{ $submitted }}
                <span class="text-[14px] font-normal text-slate-400">of {{ $completion['total'] }}</span>
            </p>
        </div>

        <div class="rounded-lg bg-slate-50 px-4 py-3">
            <p class="text-[13px] text-slate-500">In progress</p>
            <p class="text-[24px] font-semibold text-slate-900 mt-0.5">{{ $drafts }}</p>
        </div>

        <div class="rounded-lg px-4 py-3 {{ $notStarted > 0 ? 'bg-red-50' : 'bg-slate-50' }}">
            <p class="text-[13px] {{ $notStarted > 0 ? 'text-red-700' : 'text-slate-500' }}">Not started</p>
            <p class="text-[24px] font-semibold mt-0.5 {{ $notStarted > 0 ? 'text-red-700' : 'text-slate-900' }}">
                {{ $notStarted }}
            </p>
        </div>
    </div>

    {{-- WHO HASN'T FILED

         The reason this page exists. Draft and not-started are kept apart
         because they need different conversations — one office stalled,
         the other may not know it's due. --}}
    <div class="bg-white border border-slate-200 rounded-xl px-5 py-4">
        <p class="text-[14px] font-semibold text-slate-900 mb-3">Hasn't submitted</p>

        {{-- Own report first and marked. It's the one row here the person
             can act on immediately, and alphabetical ordering left it
             indistinguishable from the offices they're waiting on. --}}
        @php
            $ordered = $outstanding->sortByDesc(fn ($o) => (int) $o->id === (int) $root->id)->values();
        @endphp

        @forelse ($ordered as $office)
            @php $isOwn = (int) $office->id === (int) $root->id; @endphp

            <div class="flex items-center justify-between gap-3 py-2 px-2 -mx-2 rounded
                        {{ $isOwn ? 'bg-amber-50' : '' }}
                        {{ ! $loop->last ? 'border-b border-slate-100' : '' }}">
                <div class="min-w-0">
                    @if ($canOpenForm || $isOwn)
                        <a href="{{ route('sprs.accomplishment', ['office' => $office->id, 'year' => $year]) }}"
                           class="text-[14px] {{ $isOwn ? 'font-medium text-slate-900' : 'text-blue-600' }} hover:underline">{{ $office->name }}</a>
                    @else
                        <span class="text-[14px] text-slate-800">{{ $office->name }}</span>
                    @endif

                    @if ($isOwn)
                        <span class="ml-1.5 text-[11px] text-amber-700">own report</span>
                    @elseif ($office->role_name)
                        <span class="ml-1.5 text-[11px] text-slate-400">{{ $office->role_name }}</span>
                    @endif
                </div>

                @if ($office->status === 'draft')
                    <span class="text-[12px] text-slate-600 bg-slate-100 px-2 py-0.5 rounded whitespace-nowrap">Draft</span>
                @else
                    <span class="text-[12px] text-red-700 bg-red-50 px-2 py-0.5 rounded whitespace-nowrap">Not started</span>
                @endif
            </div>
        @empty
            <p class="text-[13px] text-slate-400 py-2">
                Every office submitted for {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}.
            </p>
        @endforelse
    </div>

</section>