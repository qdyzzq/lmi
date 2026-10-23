@extends('sprs.layouts.layout')

@section('title', 'Dashboard - SPRS')

@section('content')
    <div class="h-full flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-lg p-10 text-center">
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Welcome, {{ auth()->user()->name }}</h1>
            <p class="text-slate-500 mb-6">You're logged into SPRS.</p>

            <form method="POST" action="{{ route('sprs.logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-red-700 transition">
                    Logout
                </button>
            </form>
        </div>
    </div>
@endsection