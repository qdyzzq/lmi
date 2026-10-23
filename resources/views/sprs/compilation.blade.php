@extends('sprs.layouts.layout')

@section('title', 'Compilation')

@section('content')

<header class="bg-white border-b border-hairline">
  <div class="max-w-[1400px] mx-auto px-6 py-4">

    {{-- Only when there's an actual trail. For a PESO looking at its own
         compilation the breadcrumb was one item repeating the title; it
         earns its place when RO has drilled into a field office. --}}
    @if (count($breadcrumb) > 1)
      <nav class="text-[12.5px] text-ink-600 mb-2 flex items-center gap-1.5 flex-wrap">
        @foreach ($breadcrumb as $i => $node)
          @if ($i > 0)<span class="text-ink-400">/</span>@endif
          @if ($loop->last)
            <span class="font-semibold text-teal-900">{{ $node->name }}</span>
          @else
            <a href="{{ route('sprs.compilation', ['office' => $node->id, 'year' => $year, 'month' => $month, 'section' => $sectionKey]) }}"
               class="text-teal-700 hover:underline">{{ $node->name }}</a>
          @endif
        @endforeach
      </nav>
    @endif

    <div class="flex items-start justify-between gap-4 flex-wrap">
      <div>
        <div class="text-[19px] font-bold text-teal-900">
          {{ $isRegional ? 'Submissions' : 'Compilation' }}
        </div>
        <p class="text-[12.5px] text-ink-600 mt-0.5">
          {{ $root->name }} ·
          {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}

          {{-- Same rule as the dashboard: amber inside five days, red
               once passed. This is the page where the date bites. --}}
          @if ($daysLeft !== null)
            @if ($daysLeft < 0)
              · <span class="text-red-600">your deadline was {{ $deadlineLabel }}</span>
            @elseif ($daysLeft === 0)
              · <span class="text-red-600">today is your deadline</span>
            @elseif ($daysLeft <= 5)
              · <span class="text-amber-700">{{ $daysLeft }} day{{ $daysLeft === 1 ? '' : 's' }} until your deadline</span>
            @else
              · {{ $daysLeft }} days until your deadline
            @endif
          @endif
        </p>
      </div>

      {{-- What this page is for, said once at the top. Otherwise the only
           clue is the forward bar further down, which a new user has to
           scroll to find. --}}
      @if ($canForward && $parentName)
        <div class="text-right shrink-0">
          <p class="text-[11px] text-ink-400">Forwards to</p>
          <p class="text-[13px] text-ink-900">{{ $parentName }}</p>
        </div>
      @endif
    </div>

    {{-- WHERE THE MONTH IS

         Without this the only sign a batch had been forwarded was the
         button going grey, which reads as something broken rather than
         something done. --}}
    @php
        [$batchLabel, $batchCls] = match ($batch['state']) {
            'approved'   => ['Approved' . ($batch['at'] ? ' · ' . \Carbon\Carbon::parse($batch['at'])->format('j M') : ''), 'bg-green-100 text-green-800'],
            'forwarded'  => ['Forwarded · now with ' . ($batch['holder'] ?? 'the next office')
                             . ($batch['at'] ? ' · ' . \Carbon\Carbon::parse($batch['at'])->format('j M') : ''), 'bg-teal-100 text-teal-800'],
            'returned'   => ['Sent back for correction', 'bg-red-100 text-red-700'],
            'with_you'   => [$batch['held'] . ' report' . ($batch['held'] === 1 ? '' : 's') . ' waiting on you', 'bg-amber-100 text-amber-800'],
            'not_started'=> ['Nothing submitted yet', 'bg-slate-100 text-slate-600'],
            default      => [null, ''],
        };
    @endphp

    @if ($batchLabel)
      <div class="mt-3">
        <span class="inline-block px-3 py-1 rounded-full text-[12px] font-semibold {{ $batchCls }}">
          {{ $batchLabel }}
        </span>
      </div>
    @endif

    {{-- The counts have to describe the list below them, and the list
         changes unit: RO sees six field offices, everyone else sees the
         offices reporting to them. "0 of 0" above four pending reports
         was the old version counting a tier that doesn't file. --}}
    @php
      $countLabel = $listsOffices ? 'Field offices reporting' : 'Reported';
      $countNow   = $listsOffices
          ? $childOffices->filter(fn ($o) => $o->awaiting || $o->approved)->count()
          : $completion['counted'];
      $countTotal = $listsOffices ? $childOffices->count() : $completion['total'];
      $countMissing = $listsOffices
          ? $childOffices->filter(fn ($o) => ! $o->awaiting && ! $o->approved)->count()
          : $completion['missing'];
    @endphp

    <div class="grid gap-2.5 mt-4" style="grid-template-columns: repeat(auto-fit, minmax(130px, 1fr))">
      <div class="rounded-lg bg-slate-100 px-3.5 py-2.5">
        <p class="text-[12px] text-slate-600">{{ $countLabel }}</p>
        <p class="text-[20px] font-semibold text-slate-900 mt-0.5">
          {{ $countNow }}
          <span class="text-[12.5px] font-normal text-slate-500">of {{ $countTotal }}</span>
        </p>
      </div>

      {{-- Teal when there's something here, grey when there isn't. The
           middle box is the one that decides whether this page needs
           attention, and a row of identical grey boxes made it easy to
           miss. --}}
      <div class="rounded-lg px-3.5 py-2.5 {{ $held->count() > 0 ? 'bg-teal-100' : 'bg-slate-100' }}">
        <p class="text-[12px] {{ $held->count() > 0 ? 'text-teal-800' : 'text-slate-600' }}">
          {{ $isRegional ? 'Awaiting approval' : 'Sitting with you' }}
        </p>
        <p class="text-[20px] font-semibold mt-0.5 {{ $held->count() > 0 ? 'text-teal-900' : 'text-slate-900' }}">
          {{ $held->count() }}
        </p>
      </div>

      <div class="rounded-lg px-3.5 py-2.5 {{ $countMissing > 0 ? 'bg-red-100' : 'bg-slate-100' }}">
        <p class="text-[12px] {{ $countMissing > 0 ? 'text-red-700' : 'text-slate-600' }}">
          {{ $listsOffices ? 'Nothing sent' : 'Not started' }}
        </p>
        <p class="text-[20px] font-semibold mt-0.5 {{ $countMissing > 0 ? 'text-red-700' : 'text-slate-900' }}">
          {{ $countMissing }}
        </p>
      </div>
    </div>

  </div>
</header>

<main class="max-w-[1400px] mx-auto px-6 pt-6 pb-16">

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

  {{-- PERIOD --}}
  <form method="GET" class="flex items-end gap-3 mb-4 flex-wrap">
    <input type="hidden" name="office" value="{{ $root->id }}">
    <input type="hidden" name="section" value="{{ $sectionKey }}">

    <div class="flex flex-col gap-1">
      <label class="text-[12.5px] font-medium text-ink-600">Month</label>
      <select name="month" class="h-10 px-3 border-2 border-hairline rounded-lg text-[13.5px] font-semibold text-teal-900 focus:outline-none focus:border-teal-600">
        @foreach (range(1, 12) as $m)
          <option value="{{ $m }}" @selected($m === $month)>{{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}</option>
        @endforeach
      </select>
    </div>

    <div class="flex flex-col gap-1">
      <label class="text-[12.5px] font-medium text-ink-600">Year</label>
      <select name="year" class="h-10 px-3 border-2 border-hairline rounded-lg text-[13.5px] font-semibold text-teal-900 focus:outline-none focus:border-teal-600">
        {{-- Years that actually have data, plus the current one. A fixed
             window offers empty years on a fresh install and hides real
             ones once there's more history than it allows for. --}}
        @foreach ($years as $y)
          <option value="{{ $y }}" @selected($y === $year)>{{ $y }}</option>
        @endforeach
      </select>
    </div>

    <button type="submit" class="h-10 px-4 rounded-lg text-[13.5px] font-semibold text-white bg-teal-700 hover:bg-teal-900">
      Show
    </button>
  </form>

  {{-- SECTION — for the combined figure only.

       Each expanded card carries its own picker. Two people looking at
       this page want different things: whoever is forwarding cares about
       one combined section at a time, while checking a particular JPO
       often means flicking through all eight of theirs. Tying both to one
       control made the second impossible. --}}
  <p class="text-[11.5px] font-semibold uppercase tracking-wide text-slate-400 mb-2">
    Combined figure — section
  </p>

  <form method="GET" id="section-form" class="flex flex-wrap gap-1.5 mb-5">
    <input type="hidden" name="office" value="{{ $root->id }}">
    <input type="hidden" name="year" value="{{ $year }}">
    <input type="hidden" name="month" value="{{ $month }}">

    @foreach ($sections as $key => $section)
      <button type="submit" name="section" value="{{ $key }}"
        class="px-3 py-1.5 rounded-full text-[11.5px] font-semibold
               {{ $key === $sectionKey
                    ? 'bg-teal-700 text-white'
                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
        {{ $section['label'] }}
      </button>
    @endforeach
  </form>

  {{-- THE AREA'S FIGURES

       Above the button, because that's where the combined figure matters
       when you're about to forward. It stays after forwarding too —
       these are the area's numbers for the month, and moving reports
       upward doesn't unmake them. Emptying the panel at the exact moment
       someone wants to check what they sent was the wrong instinct. --}}
  @if ($canForward)
    <p class="text-[11.5px] font-semibold uppercase tracking-wide text-slate-400 mb-2">
      {{ $held->isNotEmpty() ? "What you're forwarding" : 'What you sent up' }}
    </p>

    <div class="border border-slate-300 rounded-xl overflow-hidden mb-4">
      <div class="bg-teal-700 px-4 py-2.5 flex items-center justify-between gap-3 flex-wrap">
        <p class="text-[13.5px] font-semibold text-white">
          Combined — {{ $reportedCount }} report{{ $reportedCount === 1 ? '' : 's' }}
          @if ($held->isNotEmpty())
            <span class="font-normal text-white/85">· {{ $held->count() }} still with you</span>
          @endif
        </p>
        <p class="text-[11.5px] text-white/85">
          {{ $sections[$sectionKey]['label'] }}
        </p>
      </div>

      @include('sprs.partials.section-table', [
          'rows'       => $combined,
          'monthLabel' => \Carbon\Carbon::create($year, $month, 1)->format('F'),
      ])
    </div>
  @endif

  @include('sprs.partials.rejection-notice', [
      'rejection'     => $rejection,
      'returnedCount' => $returnedCount,
  ])

  @include('sprs.partials.forward-bar', [
      'office'     => $office,
      'parentName' => $parentName,
      'held'       => $held,
      'cards'      => $cards,
      'year'       => $year,
      'month'      => $month,
      'canForward' => $canForward,
      'ownSubmitted' => $ownSubmitted,
  ])

  @include('sprs.partials.reject-bar', [
      'held'       => $held,
      'year'       => $year,
      'month'      => $month,
      'canReject'  => $canReject,
      'senderName' => $senderName,
  ])

  @include('sprs.partials.approve-bar', [
      'root'       => $root,
      'rows'       => $cards,
      'year'       => $year,
      'month'      => $month,
      'canApprove' => $canApprove,
  ])

  @if ($listsOffices)
    @include('sprs.partials.compilation-offices', [
        'offices' => $childOffices,
        'year'    => $year,
        'month'   => $month,
    ])
  @else
  @include('sprs.partials.compilation-cards', [
      'cards'          => $cards,
      'year'           => $year,
      'month'          => $month,
      'sections'       => $sections,
      'sectionKey'     => $sectionKey,
      'canOpenForm'    => $canOpenForm,
      'isRegional'     => $isRegional,
      'viewerOfficeId' => auth()->user()->office_id,
  ])
  @endif

</main>

@endsection