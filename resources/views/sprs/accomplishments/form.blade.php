@extends('sprs.layouts.layout')

@section('title', 'Accomplishment Tracker')

@section('content')

@php
    $submitReason = match (true) {
        $status === 'submitted'            => 'Already submitted',
        $status === 'endorsed_to_ro'       => 'With the regional office',
        $status === 'approved'             => 'Approved',
        // FO filing for an office below it is allowed, so this is no longer
        // a blanket block. The banner above already makes clear whose
        // report is on screen.
        $isOtherOffice && ! ($bootstrap['canSubmit'] ?? false)
                                           => 'Only this office can submit its own report',
        // Named specifically. "This month is closed" and "the deadline was
        // 21 August" send someone to different places — one is a wait, the
        // other is a phone call to their field office.
        ($bootstrap['pastDeadline'] ?? false) && $status !== 'returned'
                                           => 'Deadline passed',
        ! ($bootstrap['canEdit'] ?? false) => 'This month is closed',
        default                            => null,
    };

    // Naming the office that currently holds it, not just the status.
    // "Submitted" stops meaning much once a report has been forwarded
    // twice — the office wants to know it reached RO, not merely that it
    // left here.
    $badges = [
        'draft'          => ['Draft',                                              'bg-gray-200 text-gray-700'],
        'submitted'      => ['With ' . ($holderName ?? 'the next office'),          'bg-teal-100 text-teal-900'],
        'endorsed_to_ro' => ['With ' . ($holderName ?? 'the regional office'),      'bg-yellow-100 text-yellow-800'],
        'rejected'       => ['Sent back — needs correcting',                        'bg-red-100 text-red-700'],
        'returned'       => ['Sent back — needs correcting',                        'bg-red-100 text-red-700'],
        'approved'       => ['Approved',                                            'bg-green-100 text-green-800'],
    ];
    [$statusLabel, $statusCls] = $badges[$status] ?? ['Draft', 'bg-gray-200 text-gray-700'];
@endphp

<header class="bg-white border-b border-hairline">
  <div class="max-w-[1400px] mx-auto px-6 py-4.5">

    {{-- Back to the breakdown, only when FO or RO arrived from there. --}}
    @if ($isOtherOffice)
      {{-- Back to where they came from. FO opens an office's form from
           the compilation, so that's where the link returns to. --}}
      <a href="{{ route('sprs.compilation', ['office' => $parentOffice?->id, 'year' => $year, 'month' => $bootstrap['openMonth']]) }}"
         class="inline-flex items-center gap-1 text-[13px] font-semibold text-teal-700 hover:underline mb-2">
        ← Back to the compilation
      </a>
    @endif

    <div class="flex items-start justify-between gap-4 flex-wrap">
      <div>
        <div class="text-[19px] font-bold text-teal-900">Accomplishment Tracker</div>
        <div class="text-[13px] text-ink-600 mt-0.5">
          {{ $officeName }} · Department of Labor and Employment XI
        </div>
      </div>

      <span class="inline-block px-2.5 py-1 rounded text-[12px] font-semibold {{ $statusCls }}">
        {{ $statusLabel }}
      </span>
    </div>

    {{-- Editing someone else's numbers should never be ambiguous. --}}
    @if ($isOtherOffice)
      <div class="mt-3 bg-yellow-50 border border-yellow-300 rounded-lg px-4 py-2.5 text-[13px] text-ink-900">
        You're working on <b class="font-semibold">{{ $officeName }}</b>'s report, not your own.
        If you file it, the report belongs to that office and the audit log records that you filed it.
      </div>
    @endif

  </div>
</header>

<main class="max-w-[1400px] mx-auto px-6 pt-6 pb-32">

  {{-- YEAR SELECT --}}
  <div class="flex items-center justify-between gap-4 mb-5 flex-wrap">
    <div class="flex items-center gap-2.5">
      <label class="text-sm font-medium text-ink-600">Year</label>
      <select id="year-select"
        class="h-11 px-3.5 border-2 border-hairline rounded-lg text-[15px] font-semibold text-teal-900 bg-white focus:outline-none focus:border-teal-600">
        @foreach ($years as $y)
          <option value="{{ $y }}" @selected($y === $year)>{{ $y }}</option>
        @endforeach
      </select>
    </div>
  </div>

  {{-- DEADLINE

       Shown before the tabs rather than tucked in the footer: someone
       opening the form on the 20th needs to know today, not after they've
       filled in eight tabs. --}}
  @php
      // Null for accounts outside the hierarchy — super admin, statistician.
      $daysLeft = ($bootstrap['deadline'] ?? null)
          ? (int) now()->startOfDay()->diffInDays(
                \Carbon\Carbon::parse($bootstrap['deadline'])->startOfDay(),
                absolute: false
            )
          : null;
  @endphp

  @if ($daysLeft === null)
    {{-- no deadline applies --}}
  @elseif ($bootstrap['pastDeadline'] ?? false)
    <div class="bg-red-100 border border-red-300 rounded-lg px-4 py-3 mb-5 text-[13.5px] text-red-700">
      <b class="font-semibold">Your deadline for this month was {{ $bootstrap['deadlineLabel'] }}.</b>
      @if ($status === 'returned')
        This report was sent back to you, so you can still correct and resubmit it.
      @else
        Entry is closed. If this month still needs filing, contact the office above yours.
      @endif
    </div>
  @elseif ($daysLeft <= 5)
    <div class="bg-yellow-50 border border-yellow-400 rounded-lg px-4 py-3 mb-5 text-[13.5px] text-ink-900">
      <b class="font-semibold">{{ $daysLeft === 0 ? 'Today is the deadline' : $daysLeft . ' day' . ($daysLeft === 1 ? '' : 's') . ' left' }}.</b>
      This month must be submitted by {{ $bootstrap['deadlineLabel'] }}.
    </div>
  @else
    <p class="text-[12.5px] text-ink-600 mb-5">
      Submit by <b class="font-semibold text-ink-900">{{ $bootstrap['deadlineLabel'] }}</b>
      — {{ $daysLeft }} days left.
    </p>
  @endif

  {{-- CATEGORY TABS --}}
  <div id="tabs" class="flex flex-wrap gap-2 mb-5 border-b-2 border-hairline"></div>

  <p id="cat-desc" class="text-sm text-ink-600 mb-4"></p>

  {{-- ARCHIVE / READ-ONLY BANNER --}}
  <div id="archive-banner" class="hidden bg-teal-50 border border-teal-100 rounded-lg px-4 py-3 mb-4 text-[13.5px] text-teal-900"></div>

  {{-- RECOVERY BANNER

       Work that was typed on this device but never reached the server —
       a power cut, a closed laptop, a connection that dropped.

       Never restored automatically. The local copy can be older than the
       server if someone edited the same office elsewhere, so the choice
       is the person's. --}}
  <div id="recovery-banner" class="hidden bg-yellow-50 border border-yellow-400 rounded-lg px-4 py-3.5 mb-4">
    <p class="text-[13.5px] text-ink-900" data-recovery-text></p>
    <div class="flex gap-2.5 mt-3">
      <button type="button" data-recovery-restore
        class="px-4 py-2 rounded-lg text-[13px] font-semibold text-white bg-teal-700 hover:bg-teal-900">
        Restore them
      </button>
      <button type="button" data-recovery-discard
        class="px-4 py-2 rounded-lg text-[13px] font-semibold text-ink-600 border-2 border-hairline hover:bg-paper">
        Discard
      </button>
    </div>
  </div>

  {{-- EXPAND CONTROLS --}}
  <div id="expand-row" class="flex justify-between items-center mb-2.5">
    <button type="button" id="btn-past" class="bg-transparent border-0 text-[13.5px] font-semibold text-teal-700 underline py-1.5"></button>
    <button type="button" id="btn-future" class="bg-transparent border-0 text-[13.5px] font-semibold text-teal-700 underline py-1.5"></button>
  </div>

  {{-- JUMP TO SECTION (grouped tabs only) --}}
  <div id="jump-row" class="hidden items-center gap-2.5 mb-3">
    <label class="text-[13.5px] font-medium text-ink-600">Jump to section</label>
    <select id="jump-select"
      class="h-10 px-3 border-2 border-hairline rounded-lg text-[13.5px] font-semibold text-teal-900 focus:outline-none focus:border-teal-600"></select>
  </div>

  {{-- TABLE --}}
  <div class="bg-white border border-hairline rounded-xl overflow-x-auto">
    <table class="w-full border-collapse text-[14.5px]">
      <thead><tr id="table-head"></tr></thead>
      <tbody id="table-body"></tbody>
    </table>
  </div>

  {{-- Rows on a teal band are headings from the paper form, not inputs.
       Where a heading is the sum of the rows beneath it, the figure is
       computed and read-only. --}}
  <p class="mt-3 text-[12.5px] text-ink-400">
    Shaded rows are section headings. Grey figures are calculated from the rows below them and can't be edited.
  </p>

  <button type="button" disabled
    class="mt-4 font-semibold text-white bg-green-700 opacity-50 px-5 py-3 rounded-lg text-[15px] cursor-not-allowed"
    title="Not built yet">
    Save as Excel File
  </button>

</main>

{{-- STICKY BAR

     One action only. Drafts save themselves — on blur, after a few
     seconds idle, and into localStorage on every keystroke — so a
     manual save button did nothing the form wasn't already doing, and
     sitting next to Submit it read as the same action.

     The hint on the left is the save indicator: "Saving…", "All changes
     saved 8:57:07 AM", "Offline — kept on this device".

     Submit hands the whole period up to FO. It's the one moment the
     office says "these are our numbers", and it locks the form.

     Always rendered. Hiding it behind a condition meant that when
     anything upstream was off, the button silently vanished with
     nothing on screen to explain why. Disabled with a reason is easier
     to act on than absent. --}}
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-hairline">
  <div class="max-w-[1400px] mx-auto px-6 py-3.5 flex items-center justify-between gap-3">
    <p id="save-hint" class="text-[13px] text-ink-600 hidden sm:block"></p>

    <button type="button" id="submit-btn"
      @disabled($submitReason !== null)
      title="{{ $submitReason ?? ($recipientName
            ? 'Send this month to ' . $recipientName
            : 'Add this month to your compilation') }}"
      class="ml-auto font-semibold px-6 py-3.5 rounded-lg text-[15px]
             {{ $submitReason
                  ? 'text-gray-500 bg-gray-200 cursor-not-allowed'
                  : 'text-white bg-green-700 hover:bg-green-800' }}">
      {{ $submitReason ?? ($isOtherOffice ? 'File for this office' : 'Submit') }}
    </button>
  </div>
</div>

{{-- SUBMIT CONFIRMATION

     Everything still blank, listed per tab, before anything is sent.
     A blank field is often legitimate — an office with no overseas
     placements this month — so this doesn't block. It just makes sure
     nobody submits a gap they didn't know about. --}}
<div id="submit-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl w-full max-w-lg max-h-[85vh] flex flex-col">

    <div class="px-6 pt-6 pb-3">
      {{-- A JPO hands its report straight to its PESO. An office that
           compiles keeps its own until it forwards the batch, so the two
           need different wording — "submit to the province" would be
           wrong for a PESO, since nothing leaves yet. --}}
      <h3 class="text-base font-bold text-teal-900">
        @if ($recipientName)
          Submit to {{ $recipientName }}?
        @else
          Submit your report?
        @endif
      </h3>

      <p class="text-sm text-ink-600 mt-1" id="submit-modal-summary"></p>

      @unless ($recipientName)
        <p class="text-[13px] text-ink-600 mt-2">
          It joins your compilation and goes to
          <b class="font-semibold">{{ $forwardsTo ?? 'the next office up' }}</b>
          when you forward the batch.
        </p>
      @endunless
    </div>

    <div class="px-6 overflow-y-auto flex-1" id="submit-modal-body"></div>

    <div class="px-6 py-4 border-t border-hairline flex gap-3 justify-end">
      <button type="button" id="submit-cancel"
        class="px-4 py-2.5 rounded-lg text-sm font-semibold text-ink-600 border-2 border-hairline hover:bg-paper">
        Keep editing
      </button>
      <button type="button" id="submit-confirm"
        class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-green-700 hover:bg-green-800">
        Submit
      </button>
    </div>

  </div>
</div>

{{-- TOAST --}}
<div id="toast" class="hidden fixed bottom-24 left-1/2 -translate-x-1/2">
  <div class="bg-green-700 text-white px-5 py-3 rounded-lg text-sm font-medium shadow-lg">Saved</div>
</div>

@endsection

@push('scripts')
{{--
  Everything the script needs comes from the controller. The section
  structure lives in config/sprs_sections.php, so adding a row to the form
  is a config change, not a JS change.
--}}
<script>
  window.SPRS = @json($bootstrap);
</script>
@vite(['resources/js/sprs/section-save.js', 'resources/js/sprs/accomplishment.js'])
@endpush