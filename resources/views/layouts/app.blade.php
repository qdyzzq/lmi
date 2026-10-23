<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('page-title', 'Dashboard') — SPRS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
{{-- If Tailwind isn't set up via Vite yet, swap the line above for:
<script src="https://cdn.tailwindcss.com"></script> --}}
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased">
<div class="flex min-h-screen">

  {{-- ---------- sidebar ---------- --}}
  <aside class="w-62 shrink-0 bg-slate-900 text-slate-300 flex flex-col sticky top-0 h-screen">
    <div class="px-5 pt-6 pb-4 border-b border-white/10">
      <div class="font-mono text-[10px] tracking-widest uppercase text-amber-500 mb-1">SPRS</div>
      <div class="font-serif text-lg text-white font-bold leading-tight">Regional Labor<br>Market Data</div>
      <div class="text-xs text-slate-400 mt-1">Region XI — DOLE</div>
    </div>

    <nav class="flex-1 px-3 py-3 overflow-y-auto">
      <div class="font-mono text-[10px] tracking-wider uppercase text-slate-500 px-2 pt-3 pb-1">Overview</div>
      <a href="#" class="flex items-center gap-2 px-2.5 py-2 rounded-md text-sm mb-0.5 text-slate-300 hover:bg-white/5 hover:text-white transition">
        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span> Dashboard
      </a>

      <div class="font-mono text-[10px] tracking-wider uppercase text-slate-500 px-2 pt-3 pb-1">Accounts</div>
      <a href="#" class="flex items-center gap-2 px-2.5 py-2 rounded-md text-sm mb-0.5 bg-amber-100 text-slate-900 font-semibold">
        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span> Manage users
      </a>
      <a href="#" class="flex items-center gap-2 px-2.5 py-2 rounded-md text-sm mb-0.5 text-slate-300 hover:bg-white/5 hover:text-white transition">
        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span> Offices
      </a>

      <div class="font-mono text-[10px] tracking-wider uppercase text-slate-500 px-2 pt-3 pb-1">Submissions</div>
      <a href="#" class="flex items-center gap-2 px-2.5 py-2 rounded-md text-sm mb-0.5 text-slate-300 hover:bg-white/5 hover:text-white transition">
        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span> Labor Market Form
      </a>
      <a href="#" class="flex items-center gap-2 px-2.5 py-2 rounded-md text-sm mb-0.5 text-slate-300 hover:bg-white/5 hover:text-white transition">
        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span> GIP
      </a>
      <a href="#" class="flex items-center gap-2 px-2.5 py-2 rounded-md text-sm mb-0.5 text-slate-300 hover:bg-white/5 hover:text-white transition">
        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span> SPES
      </a>

      <div class="font-mono text-[10px] tracking-wider uppercase text-slate-500 px-2 pt-3 pb-1">System</div>
      <a href="#" class="flex items-center gap-2 px-2.5 py-2 rounded-md text-sm mb-0.5 text-slate-300 hover:bg-white/5 hover:text-white transition">
        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span> Settings
      </a>
    </nav>

    <div class="px-4 py-3.5 border-t border-white/10 flex items-center gap-2.5">
      <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center font-mono font-bold text-xs text-slate-900 shrink-0">SA</div>
      <div>
        <div class="text-xs font-semibold text-white">Super Admin</div>
        <div class="font-mono text-[9.5px] uppercase tracking-wide text-amber-500">Platform access</div>
      </div>
    </div>
  </aside>

  {{-- ---------- main ---------- --}}
  <div class="flex-1 min-w-0">
    <header class="bg-white border-b border-slate-200 px-8 py-4.5 flex items-center justify-between sticky top-0 z-10">
      <div>
        <div class="font-mono text-[10.5px] tracking-wider uppercase text-slate-500 mb-1">@yield('crumb', 'SPRS')</div>
        <h1 class="font-serif text-xl font-bold tracking-tight">@yield('page-title', 'Dashboard')</h1>
      </div>
      @yield('topbar-actions')
    </header>

    <div class="p-8 max-w-4xl">
      @yield('content')
    </div>
  </div>
</div>
</body>
</html>
