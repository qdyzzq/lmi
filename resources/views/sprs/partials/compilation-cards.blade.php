{{--
    resources/views/sprs/partials/compilation-cards.blade.php

    Office-by-office list. A card expands in place to show that report's
    figures for the selected section, in the same layout as the entry
    form.

    One card open at a time — opening a second closes the first, so only
    one section is ever fetched. Rendering all of them upfront would be
    fifteen offices across eight tabs on page load.

    Expects: $cards, $year, $month, $sections, $sectionKey, $canOpenForm,
             $isRegional, $viewerOfficeId
--}}

@php
    $mine = (int) ($viewerOfficeId ?? 0);

    // Whose turn it is comes from who holds the report, not its status.
    // The chain cascades, so `submitted` might mean a PESO has it, or the
    // province, or FO — current_office_id says which.
    // Own report first in every group. It's the one row the viewer is
    // personally responsible for, and alphabetical ordering dropped it
    // somewhere in the middle of fifteen offices.
    $ownFirst = fn ($group) => $group
        ->sortByDesc(fn ($c) => (int) $c->office_id === $mine)
        ->values();

    $held    = $ownFirst($cards->filter(fn ($c) => (int) $c->current_office_id === $mine
                    && in_array($c->status, ['submitted', 'endorsed_to_ro'], true)));
    $passed  = $ownFirst($cards->filter(fn ($c) => $c->status === 'approved'
                    || ((int) $c->current_office_id !== $mine
                        && in_array($c->status, ['submitted', 'endorsed_to_ro'], true))));
    $back    = $ownFirst($cards->filter(fn ($c) => in_array($c->status, ['rejected', 'returned'], true)));
    // Own report first. It's the one thing on this list the person can
    // act on right now, and buried among five JPOs it reads as just
    // another office that didn't file.
    $missing = $cards
        ->filter(fn ($c) => in_array($c->status, [null, 'draft'], true))
        ->sortByDesc(fn ($c) => (int) $c->office_id === $mine)
        ->values();

    $monthLabel = \Carbon\Carbon::create($year, $month, 1)->format('F');
@endphp

{{-- The card's section picker is deliberately separate from the one at
     the top of the page. That one drives the combined figure; this one
     drives whichever report is open. You might want the combined panel
     on Vacancies while checking a JPO's CGEC, and tying them together
     made that impossible. --}}
<section x-data="{
    open: null,
    // Arriving from a field office card is a request to see its figures,
    // so open the first waiting report rather than making the person
    // click again for what they already asked for.
    autoOpen: {{ request()->boolean('expand') ? 'true' : 'false' }},
    section: '{{ $sectionKey }}',
    sections: {{ Js::from($sections->map(fn ($s) => $s['label'])) }},
    rows: [],
    breakdown: [],
    showBreakdown: false,
    loading: false,
    failed: false,

    init() {
        if (! this.autoOpen) return;

        const first = this.$el.querySelector('[data-first-held]');

        if (first) {
            this.toggle(Number(first.dataset.firstHeld));
        }
    },

    async toggle(id) {
        if (this.open === id) { this.open = null; return; }

        // Each card starts on whatever the page is showing, then goes its
        // own way.
        this.section = '{{ $sectionKey }}';
        this.showBreakdown = false;
        this.open = id;
        await this.load();
    },

    async pick(key) {
        if (this.section === key) return;
        this.section = key;
        await this.load();
    },

    async load() {
        this.loading = true;
        this.failed = false;
        this.rows = [];
        this.breakdown = [];

        try {
            const res = await fetch(`/sprs/submissions/${this.open}/section/${this.section}`, {
                headers: { Accept: 'application/json' },
            });
            if (!res.ok) throw new Error();
            const body = await res.json();
            this.rows = body.rows;
            this.breakdown = body.breakdown ?? [];
        } catch {
            this.failed = true;
        } finally {
            this.loading = false;
        }
    }
}">

    <p class="text-[11.5px] font-semibold uppercase tracking-wide text-slate-400 mb-2">
        Office by office
    </p>

    @foreach ($held as $card)
        {{-- Own report gets a ring so it reads as yours at a glance,
             sitting first in the group. --}}
        <div @if ($loop->first) data-first-held="{{ $card->submission_id }}" @endif
             class="bg-white border rounded-xl mb-1.5 overflow-hidden
                    {{ (int) $card->office_id === $mine ? 'ring-1 ring-teal-300' : '' }}"
             :class="open === {{ $card->submission_id }} ? 'border-slate-400' : 'border-slate-200'">

            <div class="px-4 py-3 flex items-center justify-between gap-3 flex-wrap">
                <div class="min-w-0">
                    <p class="text-[14px] font-semibold text-slate-900">
                        {{ $card->office_name }}
                        <span class="text-[11px] font-normal text-slate-400">
                            {{ $card->office_id === $mine ? 'own report' : $card->role_name }}
                        </span>
                    </p>
                    <p class="text-[12px] text-slate-500 mt-0.5">
                        @if ($isRegional && $card->parent_name){{ $card->parent_name }} · @endif
                        {{ $card->submitted_by_name ?? 'Unknown' }} ·
                        {{ \Carbon\Carbon::parse($card->submitted_at)->format('j M') }} ·
                        @if ($card->empty_tabs > 0)
                            {{ $card->filled_tabs }} of {{ $card->total_tabs }} tabs filled
                        @else
                            all {{ $card->total_tabs }} tabs filled
                        @endif
                        {{-- The figures below are what this office
                             forwarded, not its own slice, so say so. --}}
                        {{-- Only on someone else's card. Yours shows your
                             own figures, because the offices beneath you
                             are already rows further down this page. --}}
                        @if ($card->sub_office_count > 0 && (int) $card->office_id !== $mine)
                            · compiled from {{ $card->sub_office_count + 1 }} offices
                        @endif
                    </p>
                    @if ($card->filed_on_behalf)
                        <p class="text-[11.5px] text-amber-700 mt-0.5">Filed on their behalf</p>
                    @endif
                </div>

                <button type="button" x-on:click="toggle({{ $card->submission_id }})"
                    class="px-3 py-1 rounded-lg text-[12px] font-semibold text-teal-700 border border-slate-200 hover:bg-slate-50 whitespace-nowrap">
                    <span x-text="open === {{ $card->submission_id }} ? 'Close' : 'Open'">Open</span>
                    <span x-text="open === {{ $card->submission_id }} ? '▴' : '▾'">▾</span>
                </button>
            </div>

            <div x-show="open === {{ $card->submission_id }}" x-cloak class="border-t border-slate-200">

                {{-- This report's own section picker. --}}
                <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200 flex flex-wrap gap-1.5">
                    <template x-for="(label, key) in sections" :key="key">
                        <button type="button" x-on:click="pick(key)"
                            class="px-2.5 py-1 rounded-full text-[11px] font-semibold"
                            :class="section === key
                                ? 'bg-teal-700 text-white'
                                : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            x-text="label"></button>
                    </template>
                </div>

                <template x-if="loading">
                    <p class="px-4 py-4 text-[12.5px] text-slate-400">Loading…</p>
                </template>

                <template x-if="!loading && failed">
                    <p class="px-4 py-4 text-[12.5px] text-red-600">Couldn't load this section. Try again.</p>
                </template>

                <template x-if="!loading && !failed">
                    <div>
                        <table class="w-full border-collapse text-[13px]">
                            <thead>
                                <tr class="bg-slate-100">
                                    <th class="text-left px-4 py-2 font-medium text-slate-600">Item</th>
                                    <th class="text-right px-4 py-2 font-medium text-slate-600 w-[120px]">{{ $monthLabel }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, i) in rows" :key="i">
                                    <tr :class="row.header ? 'bg-slate-50' : ''">
                                        <template x-if="row.header">
                                            <td colspan="2" class="px-4 py-1.5 text-[12.5px] font-semibold text-slate-800 border-t border-slate-200" x-text="row.label"></td>
                                        </template>
                                        <template x-if="!row.header">
                                            <td class="py-1.5 pr-4 border-t border-slate-100 text-slate-600"
                                                :class="row.indent ? 'pl-8' : 'pl-4'">
                                                <span x-text="row.label"></span>
                                                <span x-show="row.computed" class="ml-1 text-[10.5px] text-slate-400">calculated</span>
                                            </td>
                                        </template>
                                        <template x-if="!row.header">
                                            <td class="px-4 py-1.5 text-right border-t border-slate-100 tabular-nums"
                                                :class="row.value === null ? 'text-slate-300' : 'font-semibold text-slate-900'"
                                                x-text="row.value === null ? '–' : row.value"></td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <p x-show="rows.length === 0" class="px-4 py-4 text-[12.5px] text-slate-400">
                            Nothing recorded in this section.
                        </p>

                        @include('sprs.partials.compilation-breakdown')

                        @if ($canOpenForm)
                            <div class="px-4 py-2.5 border-t border-slate-100 text-right">
                                <a href="{{ route('sprs.accomplishment', ['office' => $card->office_id, 'year' => $year]) }}"
                                   class="text-[12px] font-semibold text-teal-700 hover:underline">
                                    Open the full form →
                                </a>
                            </div>
                        @endif
                    </div>
                </template>
            </div>
        </div>
    @endforeach

    @if ($held->isEmpty())
        <p class="text-[12.5px] text-slate-400 py-3">No reports are sitting with you for this month.</p>
    @endif

    {{-- ALREADY MOVED

         Still expandable. Forwarding a report doesn't stop anyone wanting
         to look at what they sent — collapsing these to a name and a link
         put the figures a page away at exactly the wrong moment. Closed
         by default, because they're reference rather than work. --}}
    @foreach ([['Sent back', $back], ['Passed on', $passed]] as [$label, $group])
        @if ($group->isNotEmpty())
            <p class="text-[11.5px] font-semibold uppercase tracking-wide text-slate-400 mt-4 mb-2">
                {{ $label }} — {{ $group->count() }}
            </p>

            @foreach ($group as $card)
                <div class="bg-slate-50 border rounded-lg mb-1 overflow-hidden"
                     :class="open === {{ $card->submission_id }} ? 'border-slate-300' : 'border-transparent'">

                    <div class="px-4 py-2 flex items-center justify-between gap-3 flex-wrap">
                        <p class="text-[13.5px] text-slate-600">
                            {{ $card->office_name }}
                            <span class="text-[11px] text-slate-400">
                                {{ (int) $card->office_id === $mine ? 'own report' : $card->role_name }}
                            </span>
                            @if ($card->status === 'approved')
                                <span class="ml-1 text-[11px] text-green-700">approved</span>
                            @endif
                        </p>

                        <button type="button" x-on:click="toggle({{ $card->submission_id }})"
                            class="px-2.5 py-0.5 rounded text-[11.5px] font-semibold text-teal-700 hover:bg-white">
                            <span x-text="open === {{ $card->submission_id }} ? 'Close' : 'View'">View</span>
                        </button>
                    </div>

                    <div x-show="open === {{ $card->submission_id }}" x-cloak class="border-t border-slate-200 bg-white">
                        <div class="px-4 py-2 bg-slate-50 border-b border-slate-200 flex flex-wrap gap-1.5">
                            <template x-for="(label, key) in sections" :key="key">
                                <button type="button" x-on:click="pick(key)"
                                    class="px-2.5 py-1 rounded-full text-[11px] font-semibold"
                                    :class="section === key
                                        ? 'bg-teal-700 text-white'
                                        : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                                    x-text="label"></button>
                            </template>
                        </div>

                        <template x-if="loading">
                            <p class="px-4 py-3 text-[12.5px] text-slate-400">Loading…</p>
                        </template>

                        <template x-if="!loading && failed">
                            <p class="px-4 py-3 text-[12.5px] text-red-600">Couldn't load this section.</p>
                        </template>

                        <template x-if="!loading && !failed">
                            <div>
                                <table class="w-full border-collapse text-[13px]">
                                    <thead>
                                        <tr class="bg-slate-100">
                                            <th class="text-left px-4 py-2 font-medium text-slate-600">Item</th>
                                            <th class="text-right px-4 py-2 font-medium text-slate-600 w-[120px]">{{ $monthLabel }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(row, i) in rows" :key="i">
                                            <tr :class="row.header ? 'bg-slate-50' : ''">
                                                <template x-if="row.header">
                                                    <td colspan="2" class="px-4 py-1.5 text-[12.5px] font-semibold text-slate-800 border-t border-slate-200" x-text="row.label"></td>
                                                </template>
                                                <template x-if="!row.header">
                                                    <td class="py-1.5 pr-4 border-t border-slate-100 text-slate-600"
                                                        :class="row.indent ? 'pl-8' : 'pl-4'">
                                                        <span x-text="row.label"></span>
                                                        <span x-show="row.computed" class="ml-1 text-[10.5px] text-slate-400">calculated</span>
                                                    </td>
                                                </template>
                                                <template x-if="!row.header">
                                                    <td class="px-4 py-1.5 text-right border-t border-slate-100 tabular-nums"
                                                        :class="row.value === null ? 'text-slate-300' : 'font-semibold text-slate-900'"
                                                        x-text="row.value === null ? '–' : row.value"></td>
                                                </template>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>

                                <p x-show="rows.length === 0" class="px-4 py-3 text-[12.5px] text-slate-400">
                                    Nothing recorded in this section.
                                </p>

                                @include('sprs.partials.compilation-breakdown')
                            </div>
                        </template>
                    </div>
                </div>
            @endforeach
        @endif
    @endforeach

    @if ($missing->isNotEmpty())
        <p class="text-[11.5px] font-semibold uppercase tracking-wide text-slate-400 mt-4 mb-2">
            Hasn't submitted — {{ $missing->count() }}
        </p>
        @foreach ($missing as $card)
            @php $isOwn = (int) $card->office_id === $mine; @endphp

            <div class="rounded-lg px-4 py-2 mb-1 flex items-center justify-between gap-3 flex-wrap
                        {{ $isOwn ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50' }}">
                <p class="text-[13.5px] {{ $isOwn ? 'text-slate-800 font-medium' : 'text-slate-600' }}">
                    {{ $card->office_name }}
                    @if ($isOwn)
                        <span class="text-[11px] text-amber-700">own report</span>
                    @else
                        <span class="text-[11px] text-slate-400">{{ $card->role_name }}</span>
                    @endif
                </p>
                <div class="flex items-center gap-3">
                    @if ($card->status === 'draft')
                        <span class="text-[11px] text-slate-600 bg-slate-200 px-2 py-0.5 rounded">Draft</span>
                    @else
                        <span class="text-[11px] text-red-700 bg-red-50 px-2 py-0.5 rounded">Not started</span>
                    @endif
                    @if ($canOpenForm && ! $isRegional)
                        <a href="{{ route('sprs.accomplishment', ['office' => $card->office_id, 'year' => $year]) }}"
                           class="text-[12px] font-semibold text-teal-700 hover:underline">File for them</a>
                    @endif
                </div>
            </div>
        @endforeach
    @endif

</section>