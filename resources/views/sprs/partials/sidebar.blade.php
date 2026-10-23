{{--
    resources/views/sprs/partials/sidebar.blade.php

    Markup unchanged from the original — Alpine collapse, localStorage
    state, hover tooltips, accent line, footer. The only difference is that
    the nav items come from $navGroups instead of being written out by hand,
    so one file serves every role.

    Add or move items in config/sprs_nav.php.

    Two fixes carried in from the old version: Dashboard's active check
    pointed at sprs.superadmin.dashboard while the link went to
    sprs.dashboard, so it never highlighted; and the Module 1 wrapper was
    closed before the last item, leaving an unbalanced </div>.
--}}

@php
    // Icon paths, keyed by the 'icon' value in config/sprs_nav.php.
    $icons = [
        'chart' => 'M3 13.5h4.5v7.5H3zM9.75 9h4.5v12h-4.5zM16.5 4.5H21V21h-4.5z',
        'form'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'list'  => 'M3.75 6h16.5M3.75 12h16.5m-16.5 6h16.5',
        'user'  => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
    ];
@endphp

<aside
    x-data="{
        sidebarExpanded: JSON.parse(localStorage.getItem('sprsSidebarExpanded') ?? 'true'),
        toggleSidebar() {
            this.sidebarExpanded = !this.sidebarExpanded;
            localStorage.setItem('sprsSidebarExpanded', this.sidebarExpanded);
        }
    }"
    x-init="
        $el.classList.remove('w-64', 'w-16');
        $el.classList.remove('transition-all', 'duration-300', 'ease-in-out');
        $el.classList.add(sidebarExpanded ? 'w-64' : 'w-16');
        $nextTick(() => {
            $el.style.visibility = 'visible';
            setTimeout(() => $el.classList.add('transition-all', 'duration-300', 'ease-in-out'), 50);
        });
    "
    :class="sidebarExpanded ? 'w-64' : 'w-16'"
    style="visibility: hidden;"
    class="bg-white text-slate-700 flex flex-col shadow-sm z-10 h-screen overflow-visible shrink-0 transition-all duration-300 ease-in-out border-r border-slate-200 relative">

    <!-- Top accent line -->
    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-blue-600 via-blue-400 to-sky-300"></div>

    <!-- Edge toggle handle -->
    <button @click="toggleSidebar()"
        class="absolute -right-3.5 top-1/2 -translate-y-1/2 z-50 w-7 h-7 flex items-center justify-center rounded-full bg-white border border-slate-200 shadow-md hover:bg-blue-50 hover:border-blue-400 hover:text-blue-600 text-slate-400 transition-all duration-200 cursor-pointer">
        <svg x-show="sidebarExpanded" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
        </svg>
        <svg x-show="!sidebarExpanded" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Logo Header -->
    <div class="pt-5 pb-4 border-b border-slate-100" :class="sidebarExpanded ? 'px-3' : 'px-2'">
        <!-- Expanded state -->
        <div x-show="sidebarExpanded" class="flex items-center gap-2 min-w-0">
            <div class="w-9 h-9 flex items-center justify-center shrink-0 bg-slate-50 border border-slate-200 rounded-xl p-1.5 shadow-sm">
                <img src="{{ asset('images/sprs_logo.png') }}" alt="SPRS Logo" class="w-full h-full object-contain">
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-[13px] text-slate-900 tracking-tight truncate">SPRS</p>
                {{-- The office, rather than a placeholder tagline. An FO user
                     seeing "Davao del Sur Field Office" here is worth more
                     than seeing it twice in the topbar. --}}
                <p class="text-[10px] text-slate-400 italic mt-0.5 truncate">
                    {{ $navUser['office'] ?? 'Department of Labor and Employment XI' }}
                </p>
            </div>
        </div>

        <!-- Collapsed state -->
        <div x-show="!sidebarExpanded" class="flex justify-center">
            <div class="w-9 h-9 flex items-center justify-center bg-slate-50 border border-slate-200 rounded-xl p-1.5 shadow-sm">
                <img src="{{ asset('images/sprs_logo.png') }}" alt="SPRS Logo" class="w-full h-full object-contain">
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-2 py-4 space-y-0.5 overflow-y-auto min-h-0">

        @forelse ($navGroups as $groupIndex => $group)

            @if ($groupIndex > 0)
                <div class="border-t border-slate-200 my-2 mx-2"></div>
            @endif

            <div class="mb-1">
                @if ($group['label'])
                    <p x-show="sidebarExpanded"
                        class="text-[9px] uppercase tracking-[0.14em] text-slate-400 font-semibold font-mono mb-2 px-2 whitespace-nowrap {{ $groupIndex > 0 ? 'mt-2' : '' }}">
                        {{ $group['label'] }}
                    </p>
                @endif

                @foreach ($group['items'] as $item)
                    <div class="relative"
                        x-data="{ hovered: false, tooltipY: 0 }"
                        @mouseenter="hovered = true; tooltipY = $el.getBoundingClientRect().top + $el.getBoundingClientRect().height / 2"
                        @mouseleave="hovered = false">

                        <a href="{{ $item['url'] }}"
                            class="flex items-center gap-3 px-2.5 py-2.5 rounded-r-lg transition-all group border-l-[3px]
                                {{ $item['active']
                                    ? 'text-blue-700 bg-blue-50 border-blue-500'
                                    : 'text-slate-800 border-transparent hover:text-blue-700 hover:bg-blue-50 hover:border-blue-500' }}">
                            <svg class="w-[18px] h-[18px] shrink-0 transition-opacity {{ $item['active'] ? 'opacity-100' : 'opacity-60 group-hover:opacity-100' }}"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="{{ $icons[$item['icon'] ?? ''] ?? $icons['chart'] }}"/>
                            </svg>
                            <span x-show="sidebarExpanded" class="whitespace-nowrap font-medium text-[13.5px]">{{ $item['label'] }}</span>
                        </a>

                        {{-- Tooltip when collapsed. Named per item — the old
                             Create Account tooltip said "Dashboard". --}}
                        <div x-show="!sidebarExpanded && hovered"
                            :style="{ top: tooltipY + 'px' }"
                            class="fixed left-16 ml-2 -translate-y-1/2 bg-slate-800 text-white text-xs py-1.5 px-3 rounded-lg shadow-lg z-50 whitespace-nowrap">
                            {{ $item['label'] }}
                            <div class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-slate-800"></div>
                        </div>
                    </div>
                @endforeach
            </div>

        @empty
            <p x-show="sidebarExpanded" class="px-2 py-3 text-[11px] text-slate-400">
                No pages available for this account.
            </p>
        @endforelse

    </nav>

    <!-- Footer -->
    <div class="px-4 py-3 border-t border-slate-100 text-[9.5px] text-center text-slate-700 font-mono tracking-wide">
        <span x-show="sidebarExpanded">© {{ now()->year }} SPRS</span>
        <span x-show="!sidebarExpanded">© {{ now()->format('y') }}</span>
    </div>
</aside>