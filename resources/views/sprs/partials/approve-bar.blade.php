{{--
    resources/views/sprs/partials/approve-bar.blade.php

    RO's approve action, on the reports page rather than on the dashboard
    card.

    Approving from a card would mean approving without having read
    anything — the card shows counts, the table shows figures. This sits
    above the numbers it's approving.

    Renders nothing unless the viewer is RO, the office being viewed is a
    field office, and something under it is awaiting approval.

    Expects:
      $root        the office being viewed
      $rows        collection with ->office_name and ->status
      $year, $month
      $canApprove
--}}

@if ($canApprove)
    @php
        $awaiting = $rows->filter(fn ($r) => $r->status === 'endorsed_to_ro');
    @endphp

    @if ($awaiting->isNotEmpty())
        <div x-data="{ confirming: false }" class="mb-4">

            <div class="flex items-center justify-between gap-4 flex-wrap rounded-lg border border-amber-300 bg-amber-50 px-4 py-3">
                <p class="text-[13.5px] text-slate-800">
                    <b class="font-semibold">{{ $awaiting->count() }} report{{ $awaiting->count() === 1 ? '' : 's' }} from {{ $root->name }} awaiting your approval.</b>
                    <span class="text-slate-600">Check the figures below before approving.</span>
                </p>

                <button type="button" x-on:click="confirming = true"
                    class="px-4 py-2 rounded-lg text-[13px] font-semibold text-white bg-teal-700 hover:bg-teal-900 whitespace-nowrap">
                    Approve {{ $awaiting->count() }} report{{ $awaiting->count() === 1 ? '' : 's' }}
                </button>
            </div>

            <div x-show="confirming" x-cloak
                 x-on:keydown.escape.window="confirming = false"
                 class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4"
                 x-on:click.self="confirming = false">

                <div class="bg-white rounded-2xl w-full max-w-lg max-h-[85vh] flex flex-col">

                    <div class="px-6 pt-6 pb-3">
                        <h3 class="text-base font-bold text-teal-900">Approve these reports?</h3>
                        <p class="text-sm text-slate-600 mt-1">
                            {{ $root->name }} ·
                            {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }} ·
                            {{ $awaiting->count() }} report{{ $awaiting->count() === 1 ? '' : 's' }}.
                            Approval is final — there is no way to reopen them afterwards.
                        </p>
                    </div>

                    <div class="px-6 overflow-y-auto flex-1">
                        <p class="text-[11.5px] font-semibold uppercase tracking-wide text-slate-400 pt-2 pb-1">
                            Being approved
                        </p>
                        <ul class="pb-3">
                            @foreach ($awaiting as $row)
                                <li class="text-[13.5px] text-slate-800 py-1 border-b border-slate-100"
                                    style="padding-left: {{ (int) ($row->depth ?? 0) * 16 }}px">
                                    {{ $row->office_name }}
                                    @if ($row->role_name ?? null)
                                        <span class="text-[10.5px] text-slate-400">{{ $row->role_name }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                        {{-- What's absent, alongside what's being approved.
                             Approval is final, so this is the last point
                             anyone sees which offices never reported. --}}
                        @php
                            $absent = $rows->filter(fn ($r) => in_array($r->status, [null, 'draft'], true));
                        @endphp

                        @if ($absent->isNotEmpty())
                            {{-- Called out, not just listed. Approval is
                                 final, so this is the last moment anyone
                                 sees which offices never filed — and once
                                 the month is approved it closes with those
                                 gaps in it. --}}
                            <div class="rounded-lg border border-amber-300 bg-amber-50 px-3.5 py-2.5 mt-3">
                                <p class="text-[13px] text-amber-900">
                                    <b class="font-semibold">{{ $absent->count() }}
                                    office{{ $absent->count() === 1 ? '' : 's' }} never reported.</b>
                                    Approving closes the month with those figures missing.
                                </p>
                            </div>

                            <p class="text-[11.5px] font-semibold uppercase tracking-wide text-slate-400 pt-3 pb-1">
                                Never reported — {{ $absent->count() }}
                            </p>
                            <ul class="pb-4">
                                @foreach ($absent as $row)
                                    <li class="text-[13.5px] text-slate-500 py-1 border-b border-slate-100 flex items-center justify-between gap-3">
                                        <span>{{ $row->office_name }}</span>
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

                        <form method="POST" action="{{ route('sprs.review.approve') }}">
                            @csrf
                            <input type="hidden" name="field_office_id" value="{{ $root->id }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="month" value="{{ $month }}">
                            <button type="submit"
                                class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-teal-700 hover:bg-teal-900">
                                Approve {{ $awaiting->count() }} report{{ $awaiting->count() === 1 ? '' : 's' }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    @endif
@endif