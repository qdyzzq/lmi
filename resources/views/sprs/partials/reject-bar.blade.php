{{--
    resources/views/sprs/partials/reject-bar.blade.php

    Send the batch back down, with a reason.

    Sits next to the forward or approve action, because it's the other
    half of the same decision — whoever is looking at a batch either
    passes it on or sends it back.

    Expects: $held, $year, $month, $canReject, $senderName
--}}

@if ($canReject && $held->isNotEmpty())
    <div x-data="{ confirming: false }" class="mb-4">

        <button type="button" x-on:click="confirming = true"
            class="text-[13px] font-semibold text-red-700 hover:underline">
            Send these back for correction
        </button>

        <div x-show="confirming" x-cloak
             x-on:keydown.escape.window="confirming = false"
             class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4"
             x-on:click.self="confirming = false">

            <form method="POST" action="{{ route('sprs.review.reject') }}"
                  class="bg-white rounded-2xl w-full max-w-lg max-h-[85vh] flex flex-col">
                @csrf
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="month" value="{{ $month }}">

                <div class="px-6 pt-6 pb-3">
                    <h3 class="text-base font-bold text-red-700">Send back for correction?</h3>
                    <p class="text-sm text-slate-600 mt-1">
                        {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }} ·
                        all {{ $held->count() }} report{{ $held->count() === 1 ? '' : 's' }}
                        @if ($senderName)
                            return to {{ $senderName }}.
                        @else
                            go back to whoever sent them.
                        @endif
                    </p>
                </div>

                <div class="px-6 overflow-y-auto flex-1">
                    <label class="block text-[12.5px] font-semibold text-slate-700 mb-1.5">
                        Reason — the office below sees exactly this
                    </label>

                    {{-- Required, and long enough to be useful. "Wrong"
                         sends someone back to the phone, which is the
                         call this is meant to replace. Naming the office
                         and the figure is the difference between a note
                         that works and one that doesn't. --}}
                    <textarea name="reason" rows="4" required minlength="10" maxlength="2000"
                        placeholder="Name the office and what needs checking. For example: PESO Bansalan's overseas placements of 240 is far above its monthly average — verify against the referral records."
                        class="w-full rounded-lg border-2 border-slate-200 px-3 py-2 text-[13.5px] text-slate-900 focus:outline-none focus:border-red-400"></textarea>

                    @error('reason')
                        <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    <p class="text-[12px] text-slate-500 mt-2">
                        All {{ $held->count() }} report{{ $held->count() === 1 ? '' : 's' }} go back together —
                        the batch moves as one, the same way it came up.
                    </p>
                </div>

                <div class="px-6 py-4 border-t border-slate-200 flex gap-3 justify-end">
                    <button type="button" x-on:click="confirming = false"
                        class="px-4 py-2.5 rounded-lg text-sm font-semibold text-slate-600 border-2 border-slate-200 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700">
                        Send back
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif