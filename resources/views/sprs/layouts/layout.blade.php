<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'SPRS')</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css'])
    
</head>
<body class="bg-slate-50 h-screen overflow-hidden flex">

    @if($sidebarPartial)
        @include($sidebarPartial)
    @endif

    <div class="flex-1 flex flex-col h-screen min-w-0">
        @include('sprs.partials.topbar')

        <main class="flex-1 overflow-y-auto min-h-0">
            @yield('content')
        </main>
    </div>
@stack('scripts')
</body>
</html>