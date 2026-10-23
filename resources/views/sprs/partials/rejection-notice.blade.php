{{--
    resources/views/sprs/partials/rejection-notice.blade.php

    A batch sent back, with the reason.

    Sits above everything else on the page. A row in a list saying
    "Rejected" tells nobody what to do — the note is the whole point, and
    behind a click it gets skipped.

    Expects: $rejection (from rejectionNotes()), $returnedCount
--}}

@if ($rejection['latest'])
    @php $note = $rejection['latest']; @endphp

    <div x-data="{ history: false }" class="mb-6">
        <p class="text-[11.5px] font-semibold uppercase tracking-wide text-red-700 mb-2">
            Needs your attention
        </p>

        <div class="border border-red-300 bg-red-50 rounded-xl px-5 py-4">
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div>
                    <p class="text-[15px] font-semibold text-slate-900">
                        {{ $returnedCount }} report{{ $returnedCount === 1 ? '' : 's' }} sent back
                    </p>
                    <p class="text-[12px] text-slate-600 mt-0.5">
                        {{ $note->actor_name }}
                        @if ($note->actor_office)
                            · {{ $note->actor_office }}
                        @endif
                        · {{ \Carbon\Carbon::parse($note->created_at)->format('j M') }}
                        · revision {{ $note->revision }}
                    </p>
                </div>

                <span class="px-2.5 py-0.5 rounded text-[11.5px] font-semibold bg-red-600 text-white whitespace-nowrap">
                    {{ $note->action === 'rejected' ? 'Rejected' : 'Returned' }}
                </span>
            </div>

            <div class="bg-white rounded-lg px-3.5 py-3 mt-3">
                <p class="text-[11px] text-slate-400 mb-1">Reason given</p>
                <p class="text-[13.5px] text-slate-900 whitespace-pre-line">{{ $note->note }}</p>
            </div>

            <p class="text-[12px] text-slate-600 mt-3">
                Correct what's described above, then forward the batch again.
            </p>

            {{-- Earlier rounds. On a second rejection the office needs to
                 see what was asked the first time — otherwise they fix the
                 new complaint and reintroduce the old one. --}}
            @if ($rejection['earlier']->isNotEmpty())
                <button type="button" x-on:click="history = !history"
                    class="mt-2 text-[12px] font-semibold text-red-700 hover:underline">
                    <span x-text="history ? 'Hide' : 'View'"></span>
                    earlier notes ({{ $rejection['earlier']->count() }})
                </button>

                <div x-show="history" x-cloak class="mt-2 space-y-2">
                    @foreach ($rejection['earlier'] as $old)
                        <div class="bg-white/70 rounded-lg px-3.5 py-2.5">
                            <p class="text-[11px] text-slate-400 mb-1">
                                Revision {{ $old->revision }} ·
                                {{ $old->actor_name }} ·
                                {{ \Carbon\Carbon::parse($old->created_at)->format('j M') }}
                            </p>
                            <p class="text-[13px] text-slate-700 whitespace-pre-line">{{ $old->note }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif