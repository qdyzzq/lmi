@extends('sprs.layouts.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="p-6">

        {{-- Levels 1-3 file their own report. --}}
        @if ($showOwnData)
            <div class="mb-6 bg-white border border-slate-200 rounded-xl px-5 py-4">
                <h2 class="text-[15px] font-bold text-slate-900">{{ $officeName }}</h2>
                <p class="text-[13px] text-slate-500 mt-0.5">
                    {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                </p>
                <a href="{{ route('sprs.accomplishment') }}"
                   class="inline-block mt-3 text-[13px] font-semibold text-blue-600 hover:underline">
                    Open the accomplishment form →
                </a>
            </div>
        @endif

        {{-- RO works in field offices; province and FO in individual
             offices. Different unit, different partial. --}}
        @if ($isRegional)
            @include('sprs.partials.dashboard-field-offices', [
                'root'            => $root,
                'fieldOffices'    => $fieldOffices,
                'year'            => $year,
                'month'           => $month,
                'years'           => $years,
                'canApprove'      => $canApprove,
                'deadlineLabel'   => $deadlineLabel,
                'daysLeft'        => $daysLeft,
                'isCurrentPeriod' => $isCurrentPeriod,
            ])
        @endif

        {{-- Levels 3-5 oversee offices below them. --}}
        @if ($showTeam)
            @include('sprs.partials.dashboard-summary', [
                'root'          => $root,
                'completion'    => $completion,
                'outstanding'   => $outstanding,
                'year'          => $year,
                'month'         => $month,
                'canOpenForm'   => $canOpenForm,
                'deadlineLabel' => $deadlineLabel,
                'daysLeft'        => $daysLeft,
                'years'           => $years,
                'isCurrentPeriod' => $isCurrentPeriod,
            ])
        @endif

    </div>
@endsection