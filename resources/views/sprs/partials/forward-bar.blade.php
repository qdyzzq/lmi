{{--
    resources/views/sprs/partials/forward-bar.blade.php

    Pass everything sitting with this office up to the next one.

    Replaces endorse-bar. The chain cascades now — JPO to PESO, PESO to
    province, province to FO, FO to RO — so this isn't FO-only. Levels 2,
    3 and 4 all do the same thing, and only the label changes.

    Expects: $office, $parentName, $held, $cards, $year, $month,
             $canForward, $ownSubmitted
--}}

@if ($canForward && $parentName)
    <div x-data="{ confirming: false }" class="mb-4">

        <div class="flex items-center justify-between gap-4 flex-wrap rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
            <p class="text-[13.5px] text-slate-700">
                @if ($held->isNotEmpty())
                    <b class="font-semibold">{{ $held->count() }} report{{ $held->count() === 1 ? '' : 's' }} sitting with you.</b>
                    <span class="text-slate-500">Forwarding sends them to {{ $parentName }}.</span>
                    @unless ($ownSubmitted)
                        <span class="text-amber-700">Your own report isn't among them.</span>
                    @endunless
                @else
                    <span class="text-slate-500">No reports are sitting with you for this month.</span>
                @endif
            </p>

            <button type="button"
                @disabled($held->isEmpty())
                @if ($held->isNotEmpty()) x-on:click="confirming = true" @endif
                class="px-4 py-2 rounded-lg text-[13px] font-semibold whitespace-nowrap
                       {{ $held->isEmpty()
                            ? 'text-slate-400 bg-slate-200 cursor-not-allowed'
                            : 'text-white bg-teal-700 hover:bg-teal-900' }}">
                Forward to {{ $parentName }}
            </button>
        </div>

        {{-- Names what's going. A count alone makes the person go back to
             the list and work out which reports are in the thirteen — the
             answer is already known here. --}}
        <div x-show="confirming" x-cloak
             x-on:keydown.escape.window="confirming = false"
             class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4"
             x-on:click.self="confirming = false">

            <div class="bg-white rounded-2xl w-full max-w-lg max-h-[85vh] flex flex-col">

                <div class="px-6 pt-6 pb-3">
                    <h3 class="text-base font-bold text-teal-900">Forward to {{ $parentName }}?</h3>
                    <p class="text-sm text-slate-600 mt-1">
                        {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }} ·
                        {{ $held->count() }} report{{ $held->count() === 1 ? '' : 's' }}.
                        Once forwarded you can no longer edit them.
                    </p>

                    {{-- Warns, doesn't block. An office with genuinely
                         nothing to report shouldn't be stopped from
                         forwarding its JPOs — but it should have to
                         notice it's leaving itself out. --}}
                    @unless ($ownSubmitted)
                        <div class="mt-3 rounded-lg border border-amber-300 bg-amber-50 px-3 py-2.5">
                            <p class="text-[13px] text-amber-900">
                                <b class="font-semibold">Your own report hasn't been submitted.</b>
                                It won't be included, and {{ $parentName }} will see
                                {{ $office->name }} as not started.
                            </p>
                        </div>
                    @endunless
                </div>

                <div class="px-6 overflow-y-auto flex-1">
                    <p class="text-[11.5px] font-semibold uppercase tracking-wide text-slate-400 pt-2 pb-1">
                        Going to {{ $parentName }}
                    </p>
                    {{-- Indented, because a JPO listed level with the PESO
                         it reports through reads as a peer office. Someone
                         checking whether a batch is complete needs to see
                         which offices sit under which. --}}
                    <ul class="pb-3">
                        @foreach ($held as $row)
                            <li class="text-[13.5px] py-1 border-b border-slate-100 flex items-center justify-between gap-3
                                       {{ (int) $row->office_id === (int) $office->id ? 'text-teal-800 font-medium' : 'text-slate-800' }}"
                                style="padding-left: {{ (int) ($row->depth ?? 0) * 16 }}px">
                                <span>
                                    {{ $row->office_name }}
                                    @if (($row->role_name ?? null) && (int) $row->office_id !== (int) $office->id)
                                        <span class="text-[10.5px] text-slate-400">{{ $row->role_name }}</span>
                                    @endif
                                </span>
                                @if ((int) $row->office_id === (int) $office->id)
                                    <span class="text-[11px] text-teal-700">your own report</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    {{-- Both sides, not just what's going. A list of what's
                         leaving tells you nothing about what's missing, and
                         the second list is the one that prompts a phone
                         call before the batch goes up. --}}
                    @php
                        $absent = $cards
                            ->filter(fn ($c) => in_array($c->status, [null, 'draft'], true))
                            ->sortByDesc(fn ($c) => (int) $c->office_id === (int) $office->id);
                    @endphp

                    @if ($absent->isNotEmpty())
                        <p class="text-[11.5px] font-semibold uppercase tracking-wide text-slate-400 pt-2 pb-1">
                            Nothing submitted — {{ $parentName }} will see these as empty
                        </p>
                        <ul class="pb-4">
                            @foreach ($absent as $row)
                                <li class="text-[13.5px] py-1 border-b border-slate-100 flex items-center justify-between gap-3
                                           {{ (int) $row->office_id === (int) $office->id ? 'text-amber-800 font-medium' : 'text-slate-500' }}">
                                    <span>
                                        {{ $row->office_name }}
                                        @if ((int) $row->office_id === (int) $office->id)
                                            <span class="text-[11px] text-amber-700">own report</span>
                                        @endif
                                    </span>
                                    <span class="text-[11px] {{ $row->status === 'draft' ? 'text-slate-500' : 'text-red-600' }}">
                                        {{ $row->status === 'draft' ? 'Draft' : 'Not started' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-slate-200 flex gap-3 justify-end">
                    <button type="button" x-on:click="confirming = false"
                        class="px-4 py-2.5 rounded-lg text-sm font-semibold text-slate-600 border-2 border-slate-200 hover:bg-slate-50">
                        Cancel
                    </button>

                    <form method="POST" action="{{ route('sprs.review.forward') }}">
                        @csrf
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="month" value="{{ $month }}">
                        <button type="submit"
                            class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-teal-700 hover:bg-teal-900">
                            Forward {{ $held->count() }} report{{ $held->count() === 1 ? '' : 's' }}
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endif