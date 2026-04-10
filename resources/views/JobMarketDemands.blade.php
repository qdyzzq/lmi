<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <title>Labor Market Data</title>
    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }
        .tab-btn.active {
    color: #2563eb;
    border-bottom-color: #2563eb;
}
.tab-btn:not(.active) {
    color: #6b7280;
    border-bottom-color: transparent;
}
.tab-content {
    animation: fadeIn 0.3s ease-in;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Scroll indicator bounce animation */
@keyframes bounce-custom {
    0%, 100% {
        transform: translateY(0);
        animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
    }
    50% {
        transform: translateY(-25%);
        animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
}

.scroll-indicator {
    animation: bounce-custom 1s infinite;
}

/* ── Responsive chart container ── */
.chart-responsive {
    position: relative;
    width: 100%;
}
@media (max-width: 640px)  { .chart-responsive { height: 280px; } }
@media (min-width: 641px) and (max-width: 1023px) { .chart-responsive { height: 320px; } }
@media (min-width: 1024px) { .chart-responsive { height: 360px; } }

/* ── Mobile scroll hint for LMI matrix table ── */
.table-scroll-hint { display: none; }
@media (max-width: 767px) { .table-scroll-hint { display: flex; } }

/* ── Matrix: hide table on mobile, show cards ── */
.matrix-table-view  { display: block; }
.matrix-cards-view  { display: none; }
@media (max-width: 767px) {
    .matrix-table-view  { display: none; }
    .matrix-cards-view  { display: block; }
}

/* ── Matrix mobile card styles ── */
.matrix-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1rem;
    transition: box-shadow 0.15s;
}
.matrix-card.is-open {
    border-color: #94a3b8;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
}
.matrix-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}
.matrix-card-title {
    font-size: 0.9375rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1.3;
}
.matrix-card-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.6rem 0.75rem;
}
.matrix-card-field-label {
    font-size: 0.65rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin: 0 0 0.15rem;
}
.matrix-card-field-value {
    font-size: 0.8rem;
    color: #374151;
    margin: 0;
    line-height: 1.4;
}
.matrix-card-expand-btn {
    margin-top: 0.75rem;
    width: 100%;
    padding: 0.5rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: #2563eb;
    background: #eff6ff;
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    transition: background 0.12s;
}
.matrix-card-expand-btn:hover { background: #dbeafe; }
.matrix-card-expanded {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid #f1f5f9;
    display: none;
}
.matrix-card-expanded.open { display: block; }
.matrix-skill-tag {
    display: inline-block;
    padding: 0.3rem 0.65rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.4rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #374151;
    margin: 0.2rem 0.2rem 0 0;
}

/* ── Skills cloud scroll indicator ── */
.skills-scroll-wrapper {
    position: relative;
}
.skills-scroll-wrapper::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 48px;
    background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.95));
    pointer-events: none;
    border-radius: 0 0 0.5rem 0.5rem;
    transition: opacity 0.2s;
}
.skills-scroll-wrapper.at-bottom::after {
    opacity: 0;
}
.skills-scroll-hint {
    display: none;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.7rem;
    color: #9ca3af;
    margin-top: 0.4rem;
    justify-content: center;
}

/* ── LMI Matrix table: horizontal scroll on mobile (keep original grid) ── */

/* ── Pagination: hide page numbers on mobile, show Prev/Next only ── */
@media (max-width: 480px) {
    .pagination-page-numbers { display: none; }
    .pagination-controls { justify-content: space-between; width: 100%; }
}

/* ── Banner CTA: stack buttons on mobile ── */
@media (max-width: 480px) {
    .cta-buttons { flex-direction: column; width: 100%; }
    .cta-buttons button { width: 100%; text-align: center; }
}

/* ── Survey form: mobile input fixes ── */
/* "Other (please specify)" textarea */
.other-specify-textarea {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1.5px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    line-height: 1.5;
    resize: none;
    overflow-y: auto;
    max-height: 120px;
    min-height: 44px;
    font-family: inherit;
    color: #111827;
    background: #fff;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.other-specify-textarea:focus {
    outline: none;
    border-color: #f97316;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
}
.other-specify-textarea.focus-teal:focus {
    border-color: #14b8a6;
    box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
}
.other-specify-textarea.focus-blue:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}
/* Validation error state */
.other-specify-textarea.border-red-500 {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12);
}

/* Additional Insights textarea: prevent runaway scroll */
textarea[name="specific_inputs"] {
    max-height: 180px;
    overflow-y: auto;
    resize: vertical;
}

/* Tag skill input row: stacked on mobile, side-by-side on sm+ */
.skill-input-row {
    flex-direction: column !important;
    gap: 0.5rem !important;
}
.skill-input-row input {
    width: 100%;
}
.skill-input-row button {
    width: 100%;
    justify-content: center;
}
@media (min-width: 480px) {
    .skill-input-row {
        flex-direction: row !important;
    }
    .skill-input-row button {
        width: auto;
    }
}
    </style>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <style>
        /* ── Trigger button ── */
        #matrixFilterTrigger {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.85rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 0.55rem;
            background: white;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
            font-family: inherit;
            line-height: 1.4;
            max-width: 260px;
            overflow: hidden;
        }
        #matrixFilterTrigger #mfpTriggerText {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 0;
        }
        #matrixFilterTrigger:hover { border-color: #93c5fd; color: #2563eb; }
        #matrixFilterTrigger.mft-active {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        #matrixFilterTrigger .mft-arrow {
            font-size: 0.6rem;
            opacity: 0.55;
            transition: transform 0.18s;
            margin-left: 0.1rem;
        }
        #matrixFilterTrigger.mft-open .mft-arrow { transform: rotate(180deg); }

        /* ── Dropdown panel ── */
        #matrixFilterPanel {
            position: absolute;
            top: calc(100% + 0.35rem);
            right: 0;
            z-index: 200;
            width: 300px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.875rem;
            box-shadow: 0 8px 28px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.05);
            padding: 1rem;
            padding-bottom: 1.25rem;
            display: none;
            overflow: visible;
        }
        /* On mobile, anchor left instead of right so it doesn't overflow off-screen */
        @media (max-width: 639px) {
            #matrixFilterPanel {
                right: auto;
                left: 0;
                width: calc(100vw - 3rem);
                max-width: 320px;
                padding-bottom: 1.5rem;
            }
        }
        #matrixFilterPanel.mfp-open {
            display: block;
            animation: mfpDrop 0.14s ease;
        }
        @keyframes mfpDrop {
            from { opacity: 0; transform: translateY(-5px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Section ── */
        .mfp-section { margin-bottom: 0.8rem; }
        .mfp-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.4rem;
        }
        .mfp-section-title {
            font-size: 0.68rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }
        .mfp-section-hint {
            font-size: 0.65rem;
            color: #d1d5db;
            font-weight: 400;
        }

        /* ── Chips ── */
        .mfp-chips { display: flex; flex-wrap: wrap; gap: 0.3rem; }
        .mfp-chip {
            padding: 0.28rem 0.65rem;
            border-radius: 0.4rem;
            border: 1.5px solid #e5e7eb;
            background: #f9fafb;
            color: #374151;
            font-size: 0.775rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.12s;
            user-select: none;
            font-family: inherit;
            line-height: 1.4;
        }
        .mfp-chip:hover:not(.mfp-disabled) {
            border-color: #93c5fd;
            color: #1d4ed8;
            background: #eff6ff;
        }
        .mfp-chip.mfp-selected {
            background: #1e293b;
            border-color: #1e293b;
            color: white;
            font-weight: 600;
        }
        .mfp-chip.mfp-disabled {
            opacity: 0.28;
            cursor: not-allowed;
            pointer-events: none;
        }
        .mfp-chip.mfp-placeholder {
            width: 100%;
            text-align: center;
            color: #9ca3af;
            font-style: italic;
            font-size: 0.75rem;
            border-style: dashed;
            cursor: default;
            pointer-events: none;
        }

        /* ── Range / Exact mode toggle ── */
        .mfp-mode-toggle {
            display: inline-flex;
            background: #f3f4f6;
            border-radius: 0.4rem;
            padding: 0.18rem;
            gap: 0.18rem;
            margin-bottom: 0.45rem;
        }
        .mfp-mode-btn {
            padding: 0.22rem 0.65rem;
            border-radius: 0.28rem;
            border: none;
            background: transparent;
            font-size: 0.72rem;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.14s;
            font-family: inherit;
            line-height: 1.4;
        }
        .mfp-mode-btn.mfp-mode-active {
            background: white;
            color: #1d4ed8;
            box-shadow: 0 1px 3px rgba(0,0,0,0.10);
        }
        .mfp-mode-hint {
            font-size: 0.67rem;
            color: #9ca3af;
            margin-bottom: 0.45rem;
            line-height: 1.4;
        }
        .mfp-mode-hint strong { color: #6b7280; font-weight: 600; }

        /* ── Divider ── */
        .mfp-divider { height: 1px; background: #f3f4f6; margin: 0.75rem 0; }

        /* ── Footer ── */
        .mfp-footer {
            display: flex;
            gap: 0.45rem;
            padding-top: 0.75rem;
            border-top: 1px solid #f3f4f6;
            margin-top: 0.75rem;
        }
        .mfp-btn-apply {
            flex: 1;
            padding: 0.48rem 0.9rem;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 0.45rem;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
        }
        .mfp-btn-apply:hover { background: #1d4ed8; }
        .mfp-btn-reset {
            padding: 0.48rem 0.8rem;
            background: #fef2f2;
            color: #ef4444;
            border: 1.5px solid #fecaca;
            border-radius: 0.45rem;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
            font-family: inherit;
        }
        .mfp-btn-reset:hover { background: #fee2e2; }

        /* ── HTF (Hard-to-Fill) Flat Dropdown Panel — scoped with htf- prefix ── */
        #htfFilterTrigger {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 0.5rem;
            background: white;
            font-size: 0.75rem;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
            font-family: inherit;
            line-height: 1.4;
            max-width: 240px;
            overflow: hidden;
        }
        #htfFilterTrigger #htfTriggerText {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 0;
        }
        #htfFilterTrigger:hover { border-color: #93c5fd; color: #2563eb; }
        #htfFilterTrigger.htf-active {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        #htfFilterTrigger .htf-arrow {
            font-size: 0.6rem;
            opacity: 0.5;
            transition: transform 0.18s;
        }
        #htfFilterTrigger.htf-open .htf-arrow { transform: rotate(180deg); }

        #htfFilterPanel {
            position: absolute;
            top: calc(100% + 0.35rem);
            left: 0;
            z-index: 60;
            width: 260px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.05);
            padding: 0.875rem;
            padding-bottom: 1.1rem;
            display: none;
            overflow: visible;
        }
        @media (max-width: 639px) {
            #htfFilterPanel {
                left: 0;
                right: auto;
                width: calc(100vw - 3rem);
                max-width: 300px;
                padding-bottom: 1.5rem;
            }
        }
        #htfFilterPanel.htf-panel-open {
            display: block;
            animation: htfDrop 0.14s ease;
        }
        @keyframes htfDrop {
            from { opacity: 0; transform: translateY(-5px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .htfp-section { margin-bottom: 0.7rem; }
        .htfp-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.35rem;
        }
        .htfp-section-title {
            font-size: 0.65rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }
        .htfp-section-hint {
            font-size: 0.62rem;
            color: #d1d5db;
        }

        .htfp-chips { display: flex; flex-wrap: wrap; gap: 0.28rem; }
        .htfp-chip {
            padding: 0.25rem 0.6rem;
            border-radius: 0.4rem;
            border: 1.5px solid #e5e7eb;
            background: #f9fafb;
            color: #374151;
            font-size: 0.72rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.12s;
            user-select: none;
            font-family: inherit;
        }
        .htfp-chip:hover:not(.htfp-disabled) {
            border-color: #93c5fd;
            color: #1d4ed8;
            background: #eff6ff;
        }
        .htfp-chip.htfp-selected {
            background: #1e293b;
            border-color: #1e293b;
            color: white;
            font-weight: 600;
        }
        .htfp-chip.htfp-disabled {
            opacity: 0.28;
            cursor: not-allowed;
            pointer-events: none;
        }
        .htfp-chip.htfp-placeholder {
            width: 100%;
            text-align: center;
            color: #9ca3af;
            font-style: italic;
            font-size: 0.72rem;
            border-style: dashed;
            cursor: default;
            pointer-events: none;
        }

        .htfp-divider { height: 1px; background: #f3f4f6; margin: 0.65rem 0; }

        .htfp-footer {
            display: flex;
            gap: 0.4rem;
            padding-top: 0.65rem;
            border-top: 1px solid #f3f4f6;
            margin-top: 0.65rem;
        }
        .htfp-btn-apply {
            flex: 1;
            padding: 0.42rem 0.75rem;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 0.4rem;
            font-size: 0.775rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
        }
        .htfp-btn-apply:hover { background: #1d4ed8; }
        .htfp-btn-reset {
            padding: 0.42rem 0.7rem;
            background: #fef2f2;
            color: #ef4444;
            border: 1.5px solid #fecaca;
            border-radius: 0.4rem;
            font-size: 0.775rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
            font-family: inherit;
        }
        .htfp-btn-reset:hover { background: #fee2e2; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
    <div x-data="{
        activeView: 'job-market-view',
        showReportModal: false,
        showLmiMatrix: false,
        mobileMenuOpen: false
    }">
        
        @include('partials.navbar')
        
        <div class="relative w-full h-[500px] md:h-[700px] lg:h-[900px] overflow-hidden">
            <div class="absolute inset-0">
                <img src="{{ asset('images/navbar-bg.jpg') }}" alt="Job Market Background"
                    class="w-full h-full object-cover object-top">
                <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/40 to-slate-100"></div>
            </div>
            
            <div class="relative z-10 h-full flex items-center justify-center px-4">
            <div class="text-center text-white pointer-events-none">
                <h1 class="text-white font-black leading-tight tracking-tight"
                    style="font-size: clamp(1.25rem, 4vw, 3.5rem); text-shadow: 0 2px 16px rgba(0,0,0,1), 0 0 40px rgba(0,0,0,0.7);">
                    Davao Regional Labor Demand
                </h1>
                <p class="text-slate-200 font-medium mt-2"
                    style="font-size: clamp(0.75rem, 1.5vw, 1.125rem); text-shadow: 0 1px 8px rgba(0,0,0,1);">
                    Regional Labor Market Intelligence & Trends
                </p>
            </div>
        </div>
            
            <div class="absolute bottom-6 sm:bottom-16 md:bottom-24 lg:bottom-32 left-1/2 transform -translate-x-1/2 z-20 animate-bounce">
            <a href="#job-market-section"
            class="flex flex-col items-center cursor-pointer group"
            onclick="event.preventDefault(); document.getElementById('job-market-section').scrollIntoView({ behavior: 'smooth', block: 'start' });">
                <svg class="w-8 h-8 text-white group-hover:text-blue-300 transition-colors" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2"
                        d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
                <p class="text-white text-sm mt-2 font-medium group-hover:text-blue-300 transition-colors">
                    Scroll to explore
                </p>
            </a>
        </div>
        </div>
        
        <div class="flex-1 flex flex-col overflow-y-auto mt-10 relative z-30">
            <div x-show="activeView === 'job-market-view'" x-transition>
                <div class="max-w-7xl mx-auto px-4 md:px-6 space-y-6" id="job-market-section">
                        
                        <div class="bg-slate-700 rounded-xl p-6 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-lg">
                            <div class="flex items-start gap-4">
                                <div class="p-2 bg-emerald-500/20 rounded-lg text-emerald-400">🤝</div>
                                <div>   
                                    <h2 class="text-lg font-bold">Help us map the future of Davao's workforce.</h2>
                                    <p class="text-sm text-slate-400 max-w-xl">Official data lags behind real-time market needs. Help us bridge the gap by identifying hard-to-fill roles and critical skill shortages.</p>
                                </div>
                            </div>
                            <div class="flex gap-3 cta-buttons">
                            
                                <button id="show-lmi-matrix-btn" class="bg-emerald-500 border border-emerald-500 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-500/20 transition">
                                    Submit Labor Information
                                </button>
                            </div>
                        </div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex flex-wrap justify-between items-start gap-3 p-4 sm:p-6 pb-4 border-b border-gray-100">
            <div>
                <h3 class="font-bold text-gray-800">Top 10 High-Volume Job Titles</h3>
                
                @if($selected_year && isset($selected_year))
                    <p class="text-xs text-gray-500 mt-1" id="chartSubtitle" style="{{ collect($comparison_data ?? [])->some(fn($d) => $d['previous_count'] > 0) ? '' : 'display:none' }}">
                        <span id="prevYearLabel" class="text-emerald-600 font-medium">{{ $selected_year - 1 }}</span> vs 
                        <span id="currentYearLabel" class="text-indigo-600 font-medium">{{ $selected_year }}</span>
                    </p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @if(isset($available_years) && count($available_years) > 0)
                    <select 
                        id="yearSelector" 
                        class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        onchange="updateChart(this.value)"
                    >
                        @foreach($available_years as $year)
                            <option value="{{ $year }}" {{ $year == $selected_year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                @endif
                
                <button 
                    onclick="expandChart()" 
                    class="p-2 hover:bg-gray-100 rounded-lg transition"
                    title="Expand chart"
                >
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                </button>
                
                <span class="text-gray-300 cursor-help" title="Job titles with highest demand">ⓘ</span>
            </div>
        </div>

        <div class="p-4 sm:p-6" id="chartContainer">
            <div class="chart-responsive">
                <canvas id="highVolumeHorizontalChart"></canvas>
            </div>
        </div>
            <div class="px-6 pb-4 pt-2 border-t border-gray-100">
                    <p class="text-xs text-gray-500 text-center italic">
                        Source: PhilJobNet
                    </p>
    </div>

    </div>

     <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 pb-4">
    <div class="flex justify-between mb-3">
        <div>
            <h3 class="font-bold text-gray-800">Hard-to-Fill Roles</h3>
            <p class="text-xs text-gray-500 mt-1">Jobs that are consistently difficult to recruit for</p>
        </div>
        <span class="text-gray-300 cursor-help" title="Click to expand details">ⓘ</span>
    </div>
    <div class="mb-3">
        <div class="flex items-center gap-2 flex-wrap">

            <div class="relative" id="htfFilterWrapper">

                <button id="htfFilterTrigger" type="button" onclick="htfPanelToggle()">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4h18M7 12h10M11 20h2"/>
                    </svg>
                    <span id="htfTriggerText">Filter by Period</span>
                    <span class="htf-arrow">▾</span>
                </button>

                <div id="htfFilterPanel">
<div class="htfp-section">
                        <div class="htfp-section-head">
                            <span class="htfp-section-title">Year</span>
                            <span class="htfp-section-hint" id="htfpYearHint">select From & To</span>
                        </div>
                        <div class="mfp-mode-toggle">
                            <button type="button" class="mfp-mode-btn mfp-mode-active" id="htfModeRange" onclick="htfSetMode('range')">Range</button>
                            <button type="button" class="mfp-mode-btn" id="htfModeExact" onclick="htfSetMode('exact')">Exact</button>
                        </div>
                        <p class="mfp-mode-hint" id="htfModeHint">Select <strong>From</strong> &amp; <strong>To</strong> year — all years &amp; months in between will be included</p>
                        <div class="htfp-chips" id="htfpYearChips">
                            <span class="htfp-chip htfp-placeholder">No archived data</span>
                        </div>
                    </div>

                    <div class="htfp-divider"></div>
<div class="htfp-section">
                        <div class="htfp-section-head">
                            <span class="htfp-section-title">Month</span>
                            <span class="htfp-section-hint" id="htfpMonthHint">optional</span>
                        </div>
                        <div class="htfp-chips" id="htfpMonthChips">
                            <span class="htfp-chip htfp-placeholder">Select a year to continue</span>
                        </div>
                    </div>

                    <div class="htfp-footer">
                        <button class="htfp-btn-apply" type="button" onclick="htfPanelApply()">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            Apply
                        </button>
                        <button class="htfp-btn-reset" type="button" onclick="htfPanelReset()">Reset</button>
                    </div>
                </div>
            </div>
<svg id="htfSpinner" class="w-4 h-4 text-blue-400 animate-spin" fill="none" viewBox="0 0 24 24" style="display:none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>

        </div>
<p id="htfArchiveBadge"
           class="mt-2 text-xs text-amber-700 font-medium items-center gap-1"
           style="display:none">
            <svg class="w-3 h-3 inline-block flex-shrink-0 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
            <span id="htfArchiveLabel"></span>
        </p>
    </div>
@if(isset($quarter_info))
    <div id="htfBanner" class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded-md">
        <div class="flex items-center">
            <svg class="h-4 w-4 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <div>
                <p class="text-xs font-semibold text-blue-900">Last 90 Days</p>
                <p class="text-xs text-blue-700">{{ $quarter_info['display_text'] }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
        
        @if(isset($groupedRoles) && count($groupedRoles) > 0)
            <div id="htfRolesList" class="max-h-96 overflow-y-auto px-6 pb-6">
                <div class="space-y-3">
                    @foreach($groupedRoles as $normalizedTitle => $roleGroup)
                        @foreach($roleGroup as $item)
                            @php
                                $role = $item['role'];
                                $submission = $item['submission'];
                                $index = $item['index'];
                            @endphp
                            
                            <div class="role-card border border-slate-200 rounded-lg overflow-hidden hover:border-blue-400 transition cursor-pointer"
                                 onclick="toggleRoleDetails({{ $submission->id }}, {{ $index }})">
                                
                                <div class="p-3 bg-white hover:bg-slate-50 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="font-bold text-sm text-slate-800">{{ $role->formatted_job_title }}</p>
                                            <p class="text-xs text-gray-400 mt-1">Vacancy Duration: {{ $role->vacancy_duration }}</p>
                                        </div>
                                        
                                        <svg class="expand-icon w-4 h-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="role-details hidden" id="role-details-{{ $submission->id }}-{{ $index }}">
                                    <div class="border-t border-slate-200 bg-slate-50 p-4">
                                        <div class="space-y-3 text-sm">
                                            <div>
                                                <span class="font-medium text-slate-600">Classification:</span>
                                                <p class="text-slate-800">{{ $role->job_classification }}</p>
                                            </div>

                                            @php
                                                $reasons = $role->difficulty_reasons;
                                                if (is_string($reasons)) {
                                                    $reasons = json_decode($reasons, true) ?? [];
                                                }
                                                if (!is_array($reasons)) {
                                                    $reasons = [];
                                                }
                                            @endphp
                                            
                                            @if(count($reasons) > 0)
                                                <div>
                                                    <span class="font-medium text-slate-600">Difficulty Reasons:</span>
                                                    <ul class="list-disc list-inside mt-1 text-slate-700 text-xs">
                                                        @foreach($reasons as $reason)
                                                            @if(is_array($reason))
                                                                @foreach($reason as $item)
                                                                    @if(!empty($item))
                                                                        <li>{{ $item }}</li>
                                                                    @endif
                                                                @endforeach
                                                            @elseif(is_string($reason) && !empty($reason))
                                                                <li>{{ $reason }}</li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            @php
                                                $techSkills = $role->technical_skills_missing;
                                                if (is_string($techSkills)) {
                                                    $techSkills = json_decode($techSkills, true) ?? [];
                                                }
                                                if (!is_array($techSkills)) {
                                                    $techSkills = [];
                                                }
                                            @endphp
                                            
                                            @if(count($techSkills) > 0)
                                                <div>
                                                    <span class="font-medium text-slate-600">Technical Skills Missing:</span>
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @foreach($techSkills as $skill)
                                                            @if(!empty($skill))
                                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">{{ strtoupper($skill) }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @php
                                                $softSkills = $role->soft_skills_missing;
                                                if (is_string($softSkills)) {
                                                    $softSkills = json_decode($softSkills, true) ?? [];
                                                }
                                                if (!is_array($softSkills)) {
                                                    $softSkills = [];
                                                }
                                            @endphp
                                            
                                            @if(count($softSkills) > 0)
                                                <div>
                                                    <span class="font-medium text-slate-600">Soft Skills Missing:</span>
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @foreach($softSkills as $skill)
                                                            @if(!empty($skill))
                                                                <span class="px-2 py-0.5 bg-pink-100 text-pink-700 rounded text-xs">{{ strtoupper($skill) }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="pt-2 border-t">
                                                <p class="text-xs text-slate-500">
                                                    <strong>Sector:</strong> {{ $submission->industry_sector }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        @elseif($approvedSubmissions && $approvedSubmissions->count() > 0)
            <div class="max-h-96 overflow-y-auto px-6 pb-6">
                <div class="space-y-3">
                    @foreach($approvedSubmissions as $submission)
                        @foreach($submission->hardToFillRoles as $index => $role)
                            <div class="role-card border border-slate-200 rounded-lg overflow-hidden hover:border-blue-400 transition cursor-pointer"
                                 onclick="toggleRoleDetails({{ $submission->id }}, {{ $index }})">
                                
                                <div class="p-3 bg-white hover:bg-slate-50 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="font-bold text-sm text-slate-800">{{ $role->formatted_job_title }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $role->vacancy_duration }}</p>
                                        </div>
                                        
                                        <svg class="expand-icon w-4 h-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="role-details hidden" id="role-details-{{ $submission->id }}-{{ $index }}">
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        @else
            <div class="px-6 pb-6">
                <div class="space-y-5">
                    @foreach($hard_to_fill as $job)
                    <div class="flex justify-between items-center">
                        <div class="space-y-1">
                            <p class="font-bold text-sm text-slate-800">{{ $job['role'] }}</p>
                            <p class="text-[10px] text-gray-400 flex items-center gap-1 uppercase">
                                🕒 Bottleneck: {{ $job['bottleneck'] }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-red-500 font-bold text-xs">{{ $job['days'] }} days</p>
                            <p class="text-[9px] text-gray-300">({{ $job['year'] }})</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<div id="chartModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-2 sm:p-4" onclick="closeChart()">
    <div class="bg-white rounded-xl shadow-2xl w-full h-full sm:w-11/12 sm:h-5/6 p-4 sm:p-6 relative flex flex-col" onclick="event.stopPropagation()">
        <div class="flex justify-between items-start gap-2 mb-3 sm:mb-4 flex-shrink-0">
            <h3 class="text-base sm:text-xl font-bold text-gray-800 leading-tight">High-Volume Job Titles - Expanded View</h3>
            <button onclick="closeChart()" class="flex-shrink-0 p-2 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="flex-1 min-h-0">
            <canvas id="highVolumeExpandedChart"></canvas>
        </div>
        <div class="text-center pt-2 flex-shrink-0">
            <p class="text-xs text-gray-500 italic">Source: PhilJobNet</p>
        </div>
    </div>
</div>              
<div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
    <h3 class="font-bold text-lg mb-4">Critical Skill Gaps Per Sector</h3>
    
    <div class="mb-8 pb-5 border-b border-gray-100">
        <div class="flex items-center gap-2">
            <button id="filter-left"
                    class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <div id="sector-filter-scroll" class="flex gap-2 overflow-x-auto flex-1" style="scrollbar-width:none; -webkit-overflow-scrolling:touch;">
                <style>#sector-filter-scroll::-webkit-scrollbar { display: none; }</style>

                <button onclick="filterSkills('All')"
                        class="sector-tab flex-shrink-0 px-5 py-2 text-xs font-bold rounded-xl transition-all whitespace-nowrap bg-gray-900 text-white shadow-sm"
                        data-sector="All">
                    All Sectors
                </button>
                @foreach($sectors as $sector)
                    <button onclick="filterSkills('{{ addslashes($sector) }}')"
                            class="sector-tab flex-shrink-0 px-5 py-2 text-xs font-semibold rounded-xl border border-gray-200 text-gray-500 bg-white hover:border-gray-900 hover:text-gray-900 transition-all whitespace-nowrap"
                            data-sector="{{ $sector }}">
                        {{ $sector }}
                    </button>
                @endforeach
            </div>

            <button id="filter-right"
                    class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-12">
    
        <div class="md:border-r border-gray-200 md:pr-6">
        <h4 class="text-xs font-bold text-gray-400 mb-4 uppercase bg-white pb-2">
            🔍 In demand Technical Skills 
        </h4>
        <div class="skills-scroll-wrapper" id="tech-skills-scroll-wrapper">
        <div class="flex flex-wrap gap-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar" 
             id="tech-skills-container">
            @foreach($tech_skills as $skill)
                    <div class="skill-tag tech-skill bg-blue-100 text-gray-800 font-semibold px-3 py-2 rounded-lg text-sm h-fit flex flex-col gap-0.5" 
                         data-sector="{{ $skill['sector'] }}">
                        <div class="flex items-center gap-1">
                            {{ $skill['name'] }}
                            @if(isset($skill['count']) && $skill['count'] > 1)
                                <span class="px-1.5 py-0.5 bg-blue-200 rounded-full text-[9px] font-bold">{{ $skill['count'] }}×</span>
                            @endif
                        </div>
                        <span class="text-[11px] opacity-60 font-normal">({{ $skill['sector'] }})</span>
                    </div>
                @endforeach
            </div>
        </div>
        <p class="skills-scroll-hint" id="tech-scroll-hint">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            Scroll to see more
        </p>
        </div>
    <div class="md:pl-6">
        <h4 class="text-xs font-bold text-gray-400 mb-4 uppercase bg-white pb-2">
            🚫 In demand Soft Skills 
        </h4>
        <div class="skills-scroll-wrapper" id="soft-skills-scroll-wrapper">
        <div class="flex flex-wrap gap-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar" 
             id="soft-skills-container">
            @foreach($soft_skills as $skill)
                    <div class="skill-tag soft-skill bg-red-100 text-gray-800 font-semibold px-3 py-2 rounded-lg text-sm h-fit flex flex-col gap-0.5" 
                         data-sector="{{ $skill['sector'] }}">
                        <div class="flex items-center gap-1">
                            {{ $skill['name'] }}
                            @if(isset($skill['count']) && $skill['count'] > 1)
                                <span class="px-1.5 py-0.5 bg-red-200 rounded-full text-[9px] font-bold">{{ $skill['count'] }}×</span>
                            @endif
                        </div>
                        <span class="text-[11px] opacity-60 font-normal">({{ $skill['sector'] }})</span>
                    </div>
                @endforeach
            </div>
        </div>
        <p class="skills-scroll-hint" id="soft-scroll-hint">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            Scroll to see more
        </p>
        </div>

    </div>
</div>
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm" x-data="{ 
    openItem: null,
    currentPage: 1, 
    itemsPerPage: 10,
    matrixFilterActive: false,
    matrixShowAll: false,
    tableData: [],
    init() {
        // Seed tableData from the global once Alpine boots
        this.tableData = (window.matrixResultsData || []).slice();
    },
    get sortedData() {
        const impactOrder = { 'High': 1, 'Medium': 2, 'Low': 3 };
        return (this.tableData || []).slice().sort((a, b) => {
            const impactA = impactOrder[a.impact] || 2; // Default to Medium if no impact
            const impactB = impactOrder[b.impact] || 2;
            return impactA - impactB;
        });
    },
    get totalPages() {
        if (this.matrixShowAll) return 1;
        return Math.ceil((this.sortedData?.length || 0) / this.itemsPerPage); 
    },
    get paginatedData() {
        if (this.matrixShowAll) return this.sortedData;
        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        return this.sortedData.slice(start, end);
    },
    nextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            this.openItem = null;
        }
    },
    prevPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.openItem = null;
        }
    },
    goToPage(page) {
        this.currentPage = page;
        this.openItem = null;
    }
}"
@matrix-filter-update="
    tableData          = $event.detail.tableData;
    matrixFilterActive = $event.detail.filterActive;
    matrixShowAll      = $event.detail.showAll;
    currentPage        = 1;
    openItem           = null;
"
>
    <div class="p-4 sm:p-6 border-b flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 bg-gradient-to-r from-gray-50 to-white overflow-visible rounded-t-2xl">
    <h3 class="font-bold text-gray-900 flex items-center gap-3 text-lg">
        <svg class="w-6 h-6 text-gray-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 4v16M3 4h18a1 1 0 011 1v14a1 1 0 01-1 1H3a1 1 0 01-1-1V5a1 1 0 011-1z"/>
        </svg>
        <span>Critical Skills Requirements</span>
    </h3>
    <div class="flex flex-wrap items-center gap-2">
<div class="relative" id="matrixFilterWrapper">
<button id="matrixFilterTrigger" type="button" onclick="mfpToggle()">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4h18M7 12h10M11 20h2"/>
                </svg>
                <span id="mfpTriggerText">Filter by Period</span>
                <span class="mft-arrow">▾</span>
            </button>
<div id="matrixFilterPanel">
<div class="mfp-section">
                    <div class="mfp-section-head">
                        <span class="mfp-section-title">Year</span>
                        <span class="mfp-section-hint" id="mfpYearHint">select From & To</span>
                    </div>
                    <div class="mfp-mode-toggle">
                        <button type="button" class="mfp-mode-btn mfp-mode-active" id="mfpModeRange" onclick="mfpSetMode('range')">Range</button>
                        <button type="button" class="mfp-mode-btn" id="mfpModeExact" onclick="mfpSetMode('exact')">Exact</button>
                    </div>
                    <p class="mfp-mode-hint" id="mfpModeHint">Select <strong>From</strong> &amp; <strong>To</strong> year — all years &amp; months in between will be included</p>
                    <div class="mfp-chips" id="mfpYearChips">
</div>
                </div>

                <div class="mfp-divider"></div>
<div class="mfp-section">
                    <div class="mfp-section-head">
                        <span class="mfp-section-title">Month</span>
                        <span class="mfp-section-hint" id="mfpMonthHint">select a year first</span>
                    </div>
                    <div class="mfp-chips" id="mfpMonthChips">
                        <span class="mfp-chip mfp-placeholder">Select a year to see months</span>
                    </div>
                </div>
<div class="mfp-footer">
                    <button class="mfp-btn-apply" type="button" onclick="mfpApply()">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Apply Filter
                    </button>
                    <button class="mfp-btn-reset" type="button" onclick="mfpReset()">Reset</button>
                </div>
            </div>
        </div>
<svg id="matrixSpinner" class="w-5 h-5 text-blue-400 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24" style="display:none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>

        <button id="exportLMIMatrixBtn" class="text-emerald-600 border border-emerald-200 bg-white px-4 py-2 sm:px-5 sm:py-2.5 rounded-lg text-sm font-semibold hover:bg-emerald-50 transition-all shadow-sm hover:shadow whitespace-nowrap">
            Export Analysis
        </button>
    </div>
</div>

@if(count($matrix_results) > 0)
    <div class="overflow-hidden rounded-b-2xl">

    {{-- ── MOBILE CARD VIEW (shown on < 768px) ── --}}
    <div class="matrix-cards-view px-4 py-4 bg-gray-50 space-y-3" id="matrixCardsContainer">
        <template x-for="(result, index) in paginatedData" :key="'card-'+index">
            <div class="matrix-card" :class="openItem === index ? 'is-open' : ''">
<div class="matrix-card-header">
                    <p class="matrix-card-title" x-text="result.role"></p>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold whitespace-nowrap flex-shrink-0"
                        :class="{
                            'bg-red-50 text-red-700 border border-red-200': result.impact === 'High',
                            'bg-green-50 text-green-700 border border-green-200': result.impact === 'Low',
                            'bg-amber-50 text-amber-700 border border-amber-200': result.impact === 'Medium' || !result.impact
                        }"
                        x-text="result.impact || 'Medium'">
                    </span>
                </div>
<div class="matrix-card-grid">
                    <div>
                        <p class="matrix-card-field-label">Sector</p>
                        <p class="matrix-card-field-value" x-text="result.sector"></p>
                    </div>
                    <div>
                        <p class="matrix-card-field-label">Salary Range</p>
                        <p class="matrix-card-field-value"
                            x-text="(result.salary_range && result.salary_range !== 'Not specified') ? result.salary_range : '—'">
                        </p>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <p class="matrix-card-field-label">Missing Skills</p>
                        <p class="matrix-card-field-value">
                            <template x-if="result.has_technical_checkbox || result.has_soft_checkbox">
                                <span>
                                    <template x-if="result.has_technical_checkbox">
                                        <span x-text="(result.hard_skills && result.hard_skills.length > 0) ? result.hard_skills.length + ' Technical Skill' + (result.hard_skills.length > 1 ? 's' : '') : 'Technical Skills'"></span>
                                    </template>
                                    <template x-if="result.has_technical_checkbox && result.has_soft_checkbox">
                                        <span> · </span>
                                    </template>
                                    <template x-if="result.has_soft_checkbox">
                                        <span x-text="(result.soft_skills && result.soft_skills.length > 0) ? result.soft_skills.length + ' Soft Skill' + (result.soft_skills.length > 1 ? 's' : '') : 'Soft Skills'"></span>
                                    </template>
                                </span>
                            </template>
                            <template x-if="!result.has_technical_checkbox && !result.has_soft_checkbox">
                                <span class="text-gray-400 italic">None specified</span>
                            </template>
                        </p>
                    </div>
                </div>
<template x-if="(result.hard_skills && result.hard_skills.length > 0) || (result.soft_skills && result.soft_skills.length > 0)">
                    <button class="matrix-card-expand-btn"
                        @click="openItem = openItem === index ? null : index">
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="openItem === index ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <span x-text="openItem === index ? 'Hide details' : 'View skill details'"></span>
                    </button>
                </template>
<div class="matrix-card-expanded" :class="openItem === index ? 'open' : ''">
                    <template x-if="result.hard_skills && result.hard_skills.length > 0">
                        <div class="mb-3">
                            <p class="matrix-card-field-label mb-1">Missing Technical Skills</p>
                            <div>
                                <template x-for="skill in result.hard_skills" :key="skill.name || skill">
                                    <span class="matrix-skill-tag" x-text="skill.name || skill"></span>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="result.soft_skills && result.soft_skills.length > 0">
                        <div>
                            <p class="matrix-card-field-label mb-1">Missing Soft Skills</p>
                            <div>
                                <template x-for="skill in result.soft_skills" :key="skill.name || skill">
                                    <span class="matrix-skill-tag" x-text="skill.name || skill"></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

            </div>
        </template>
    </div>

    {{-- ── DESKTOP TABLE VIEW (hidden on < 768px) ── --}}
    <div class="matrix-table-view">
    <div class="table-scroll-hint items-center gap-2 px-4 py-2 bg-blue-50 border-b border-blue-100 text-xs text-blue-600 font-medium">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
        </svg>
        Scroll horizontally to see all columns
    </div>
    <div class="overflow-x-auto">
    <div class="min-w-[860px]">
    <div class="sticky top-0 z-20 bg-slate-800 border-b border-gray-700 shadow-md">
        <div class="grid grid-cols-12 gap-3 px-4 sm:px-8 py-4 items-center lmi-row-grid">
            <div class="col-span-3 flex items-center justify-start">
                <span class="text-s font-small font-bold text-white uppercase tracking-wider">Job Title / Role</span>
            </div>
            <div class="col-span-2 flex items-center justify-start">
                <span class="text-s font-small font-bold text-white uppercase tracking-wider">Sector</span>
            </div>
            <div class="col-span-3 flex items-center justify-center">
                <span class="text-s font-small font-bold text-white uppercase tracking-wider">Missing Skills / Competency</span>
            </div>
            <div class="col-span-2 flex items-center justify-center">
                <span class="text-s font-small font-bold text-white uppercase tracking-wider">Salary Range</span>
            </div>
            <div class="col-span-2 flex items-center justify-center">
                <span class="text-s font-small font-bold text-white uppercase tracking-wider text-center leading-tight">Impact</span>
            </div>
        </div>
    </div>

    <div id="matrixScrollArea" class="max-h-[600px] overflow-y-auto bg-gray-50">
        <div class="divide-y divide-gray-200">
            <template x-for="(result, index) in paginatedData" :key="index">
                <div class="bg-white hover:bg-gray-50 transition-all duration-200 border-l-4" 
                     :class="openItem === index ? 'border-l-gray-500 shadow-md' : 'border-l-transparent'">
<div 
    @click="(result.hard_skills && result.hard_skills.length > 0) || (result.soft_skills && result.soft_skills.length > 0) ? (openItem = openItem === index ? null : index) : null"
    class="grid grid-cols-12 gap-3 px-4 sm:px-8 py-4 sm:py-6 items-center lmi-row-grid" :class="((result.hard_skills && result.hard_skills.length > 0) || (result.soft_skills && result.soft_skills.length > 0)) ? 'cursor-pointer' : 'cursor-default'">
    
    <div class="col-span-3 flex items-center justify-start">
        <h4 class="font-bold text-gray-900 text-base" x-text="result.role"></h4>
    </div>

    <div class="col-span-2 flex items-center justify-start">
        <p class="text-xs font-bold text-gray-700 uppercase tracking-wide leading-relaxed" x-text="result.sector"></p>
    </div>

<div class="col-span-3 flex items-center justify-center">
    <div class="flex flex-col gap-1" style="min-width: 140px;">

        <div class="flex items-center gap-2" x-show="result.has_technical_checkbox">
            <span class="text-gray-400 font-medium text-xs">•</span>
            <span class="text-sm text-gray-700">
                <template x-if="result.hard_skills && result.hard_skills.length > 0">
                    <span><span class="font-bold" x-text="result.hard_skills.length"></span> <span class="font-bold">Technical Skill</span><span x-show="result.hard_skills.length > 1">s</span></span>
                </template>
                <template x-if="!result.hard_skills || result.hard_skills.length === 0">
                    <span class="font-semibold">Technical Skills</span>
                </template>
            </span>
        </div>

        <div class="flex items-center gap-2" x-show="result.has_soft_checkbox">
            <span class="text-gray-400 font-medium text-xs">•</span>
            <span class="text-sm text-gray-700">
                <template x-if="result.soft_skills && result.soft_skills.length > 0">
                    <span><span class="font-bold" x-text="result.soft_skills.length"></span> <span class="font-bold">Soft Skill</span><span x-show="result.soft_skills.length > 1">s</span></span>
                </template>
                <template x-if="!result.soft_skills || result.soft_skills.length === 0">
                    <span class="font-semibold">Soft Skills</span>
                </template>
            </span>
        </div>

        <template x-if="!result.has_technical_checkbox && !result.has_soft_checkbox">
            <span class="text-xs text-gray-400 italic">No skills specified</span>
        </template>

        <span class="text-xs text-gray-400 italic mt-0.5"
              x-show="openItem !== index && ((result.hard_skills && result.hard_skills.length > 0) || (result.soft_skills && result.soft_skills.length > 0))">
            Click to view details
        </span>
    </div>
</div>
    <div class="col-span-2 flex items-center justify-center">
        <div class="flex flex-col">
            <template x-if="result.salary_range && result.salary_range !== 'Not specified'">
                <span class="text-sm font-semibold text-gray-900" x-text="result.salary_range"></span>
            </template>
            <template x-if="!result.salary_range || result.salary_range === 'Not specified'">
                <span class="text-xs text-gray-400 italic">Not specified</span>
            </template>
        </div>
    </div>

    <div class="col-span-2 flex items-center justify-center gap-1">
        <span 
            class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-center shadow-sm whitespace-nowrap"
            :class="{
                'bg-red-50 text-red-700 border border-red-200': result.impact === 'High',
                'bg-green-50 text-green-700 border border-green-200': result.impact === 'Low',
                'bg-amber-50 text-amber-700 border border-amber-200': result.impact === 'Medium' || !result.impact
            }"
            x-text="result.impact || 'Medium'">
        </span>
        <svg 
            class="w-3.5 h-3.5 flex-shrink-0 transition-all duration-300"
            :class="[
                openItem === index ? 'rotate-180 text-gray-600' : 'text-gray-400',
                ((result.hard_skills && result.hard_skills.length > 0) || (result.soft_skills && result.soft_skills.length > 0)) ? 'opacity-100' : 'opacity-0'
            ]"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>
</div>
                    <div 
                        x-show="openItem === index"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-4"
                        class="border-t border-gray-200 bg-gray-50"
                        style="display: none;">
                        
                        <div class="px-8 py-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <div class="flex items-center gap-2 mb-4">
                                        <span class="text-sm font-bold text-gray-900 uppercase tracking-wide">Missing Technical Skills</span>
                                    </div>
                                    <template x-if="result.hard_skills && result.hard_skills.length > 0">
                                        <div class="flex flex-wrap gap-2.5">
                                            <template x-for="skill in result.hard_skills" :key="skill.name || skill">
                                                <span 
                                                    class="px-4 py-2.5 bg-white text-gray-800 border border-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm"
                                                    x-text="skill.name || skill">
                                                </span>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!result.hard_skills || result.hard_skills.length === 0">
                                        <div class="text-center py-6">
                                            <div class="text-3xl mb-2 opacity-20">✓</div>
                                            <p class="text-sm text-gray-400 font-medium">No specific technical skill gaps identified</p>
                                        </div>
                                    </template>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <div class="flex items-center gap-2 mb-4">
                                        <div>
                                            <span class="text-sm font-bold text-gray-900 uppercase tracking-wide block">Missing Soft Skills</span>
                                            <span class="text-xs text-gray-600 font-medium">(Critical Gaps)</span>
                                        </div>
                                    </div>
                                    <template x-if="result.soft_skills && result.soft_skills.length > 0">
                                        <div class="flex flex-wrap gap-2.5">
                                            <template x-for="skill in result.soft_skills" :key="skill.name || skill">
                                                <span 
                                                    class="px-4 py-2.5 bg-white text-gray-800 border border-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm"
                                                    x-text="skill.name || skill">
                                                </span>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!result.soft_skills || result.soft_skills.length === 0">
                                        <div class="text-center py-6">
                                            <div class="text-3xl mb-2 opacity-20">✓</div>
                                            <p class="text-sm text-gray-400 font-medium">No soft skill gaps identified</p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <template x-if="result.salary_min && result.salary_max">
                                <div class="mt-6 bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <span class="text-lg">💰</span>
                                            </div>
                                            <span class="text-sm font-bold text-gray-900 uppercase tracking-wide">Salary Range</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-gray-900">
                                                ₱<span x-text="Number(result.salary_min).toLocaleString()"></span> - ₱<span x-text="Number(result.salary_max).toLocaleString()"></span>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">Monthly compensation</div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    </div><!-- end min-w -->
    </div><!-- end overflow-x-auto -->
    </div><!-- end matrix-table-view -->

    {{-- ── SHARED PAGINATION (works for both cards and table) ── --}}
    <div class="px-4 sm:px-8 py-4 sm:py-5 border-t bg-white flex flex-wrap items-center justify-between gap-3 shadow-inner pagination-controls">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <span>Showing</span>
            <span class="font-bold text-gray-900" x-text="matrixShowAll ? 1 : (currentPage - 1) * itemsPerPage + 1"></span>
            <span>to</span>
            <span class="font-bold text-gray-900" x-text="matrixShowAll ? (sortedData?.length || 0) : Math.min(currentPage * itemsPerPage, (sortedData?.length || 0))"></span>
            <span>of</span>
            <span class="font-bold text-gray-900" x-text="(sortedData?.length || 0)"></span>
            <span>results</span>
            <span id="matrixFilterBadge"
                  class="ml-2 hidden px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full">
            </span>
        </div>
        <div class="flex items-center gap-2" x-show="!matrixShowAll">
            <button 
                @click="prevPage()"
                :disabled="currentPage === 1"
                :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50 hover:border-gray-400'"
                class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 transition-all">
                Previous
            </button>
            <div class="flex gap-1.5 pagination-page-numbers">
                <template x-for="page in totalPages" :key="page">
                    <button 
                        @click="goToPage(page)"
                        :class="currentPage === page ? 'bg-emerald-500 text-white border-emerald-500 shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300'"
                        class="min-w-[44px] px-4 py-2.5 rounded-lg border text-sm font-bold transition-all"
                        x-text="page">
                    </button>
                </template>
            </div>
            <button 
                @click="nextPage()"
                :disabled="currentPage === totalPages"
                :class="currentPage === totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50 hover:border-gray-400'"
                class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 transition-all">
                Next
            </button>
        </div>
    </div>
</div>

    </div><!-- end overflow-hidden rounded-b-2xl -->
@else
    <div class="p-12 text-center bg-white">
        <div class="text-6xl mb-4 opacity-20">📊</div>
        <p class="text-slate-500 font-medium">No competency gap data available yet.</p>
        <p class="text-slate-400 text-sm mt-2">Data will appear once submissions are approved.</p>
    </div>
@endif
</div>
<div class="mt-4 px-4 py-3 bg-white border border-gray-100 rounded-xl text-center">
    <p class="text-xs text-slate-400 italic">
        Source: Tab1-Employment-Davao-Region-with-JUL2025.xlsx (Rates) | Module 2 Sources: PhilJobNet, PSA ISLE, Industry Surveys.
    </p>
</div>

<div id="lmi-matrix-modal" class="fixed inset-0 z-[9999] flex items-center justify-center p-0 sm:px-4 hidden">
    <div id="modal-backdrop" class="absolute inset-0 backdrop-blur-md bg-white/30 pointer-events-none"></div>
    <div id="lmi-form-content" class="bg-white sm:rounded-2xl shadow-2xl w-full h-full sm:w-[96vw] sm:h-[96vh] sm:max-w-[96vw] sm:max-h-[96vh] overflow-hidden relative z-10 pointer-events-auto">
        
        <div class="bg-teal-700 px-4 py-3 sm:p-5 flex justify-between items-center text-white sticky top-0 z-10">
            <div class="flex items-center gap-2 sm:gap-3">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-sm sm:text-lg font-bold leading-tight">INDUSTRY SKILLS NEED SURVEY</h3>
            </div>
            <button id="close-modal-btn" class="text-white hover:bg-teal-600 p-1.5 rounded transition flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="bg-teal-600 px-3 sm:px-5 py-3 sm:py-4 sticky top-[52px] sm:top-[68px] z-10">
            <div class="flex items-center justify-between max-w-3xl mx-auto">
                <div class="flex flex-col items-center">
                    <div class="step-circle w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white text-teal-700 flex items-center justify-center text-xs sm:text-sm font-bold">1</div>
                    <span class="text-white text-[10px] sm:text-xs mt-1 hidden sm:block">Company</span>
                </div>
                <div class="step-line flex-1 h-1 bg-teal-500 mx-1 sm:mx-2"></div>
                <div class="flex flex-col items-center">
                    <div class="step-circle w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-teal-500 text-white flex items-center justify-center text-xs sm:text-sm font-bold">2</div>
                    <span class="text-white text-[10px] sm:text-xs mt-1 hidden sm:block">Roles</span>
                </div>
                <div class="step-line flex-1 h-1 bg-teal-500 mx-1 sm:mx-2"></div>
                <div class="flex flex-col items-center">
                    <div class="step-circle w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-teal-500 text-white flex items-center justify-center text-xs sm:text-sm font-bold">3</div>
                    <span class="text-white text-[10px] sm:text-xs mt-1 hidden sm:block">Diagnosis</span>
                </div>
                <div class="step-line flex-1 h-1 bg-teal-500 mx-1 sm:mx-2"></div>
                <div class="flex flex-col items-center">
                    <div class="step-circle w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-teal-500 text-white flex items-center justify-center text-xs sm:text-sm font-bold">4</div>
                    <span class="text-white text-[10px] sm:text-xs mt-1 hidden sm:block">Engagement</span>
                </div>
            </div>
        </div>

        <div class="overflow-y-auto h-[calc(100vh-120px)] sm:h-[calc(98vh-140px)] pb-24">
    <div class="p-4 sm:p-8">
        <div id="intro-section">
            <h4 class="text-l font-bold pb-2">INDUSTRY SKILLS NEED SURVEY</h4>
            <p class="text-gray-600 text-sm leading-relaxed mb-8 pb-6 border-b border-gray-200">
                {{__('lmip.lmi_intro')}}
            </p>
            <h5 class="text-l font-bold pb-2">DATA PRIVACY STATEMENT</h5>
            <p class="text-gray-600 text-sm leading-relaxed mb-8 pb-6 border-b border-gray-200">
                {{ __('lmip.privacy_statement') }}
            </p>
        </div>

<form action="{{ route('lmi.submit') }}" method="POST" class="space-y-5" id="lmi-form">
            @csrf
            <input type="hidden" name="test_form_start" value="FORM_STARTED">

<div class="lmi-step" data-step="0">

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mt-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="text-sm font-bold text-gray-800 uppercase tracking-wide">Part 1: Company Profile</div>
                    </div>
                    <div class="h-px bg-gray-100 mb-5"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">

                        <div class="flex flex-col gap-5">
                            <div>
                                <label class="block text-gray-800 text-sm font-semibold mb-2">Company Name:<span class="text-red-500">*</span></label>
                                <input type="text" name="company" required
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"/>
                            </div>
                            <div>
                                <label class="block text-gray-800 text-sm font-semibold mb-2">Designation / Position:<span class="text-red-500">*</span></label>
                                <input type="text" name="position" required
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"/>
                            </div>
                            <div>
                                <label class="block text-gray-800 text-sm font-semibold mb-2">Email Address:<span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="emailInput" required
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"/>
                                <p id="emailError" class="hidden text-red-500 text-xs mt-1.5 font-medium">Please enter a valid email address (e.g. <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="204e414d45604558414d504c450e434f4d">[@email.com]</a>)</p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-5">
                            <div>
                                <label class="block text-gray-800 text-sm font-semibold mb-2">Name of Respondent:<span class="text-red-500">*</span></label>
                                <input type="text" name="respondent" placeholder="e.g., John Quincy Adams" required
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"/>
                            </div>
                            <div>
                                <label class="block text-gray-800 text-sm font-semibold mb-2">
                                    Contact Number:<span class="text-red-500">*</span>
                                </label>

                                <div class="inline-flex bg-gray-100 rounded-lg p-1 mb-3">
                                    <button type="button" id="toggle-mobile"
                                    onclick="switchContactType('mobile')"
                                    class="contact-type-btn flex items-center gap-1.5 px-4 py-1.5 rounded-md text-sm font-semibold bg-white text-teal-700 shadow-sm border border-gray-200 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Mobile
                                </button>
                                    <button type="button" id="toggle-telephone"
                                    onclick="switchContactType('telephone')"
                                    class="contact-type-btn flex items-center gap-1.5 px-4 py-1.5 rounded-md text-sm font-semibold text-gray-500 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    Telephone
                                </button>
                                </div>

                                <div id="mobile-input-wrapper" class="relative">
                                    <div class="flex gap-2">
                                        <div class="relative">
                                            <button type="button" id="country-code-btn"
                                                class="flex items-center gap-1.5 px-3 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all whitespace-nowrap">
                                                <span id="country-flag">🇵🇭</span>
                                                <span id="country-dial-code">+63</span>
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                            <div id="country-dropdown" class="hidden absolute z-50 left-0 top-full mt-1 w-72 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                                                <div class="p-2 border-b border-gray-100">
                                                    <input type="text" id="country-search" placeholder="Search country..."
                                                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"/>
                                                </div>
                                                <div id="country-list" class="max-h-52 overflow-y-auto"></div>
                                            </div>
                                        </div>
                                        <input type="tel" id="mobile-input"
                                            placeholder="912 345 6789" required
                                            inputmode="numeric"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent focus:bg-white transition-all"/>
                                    </div>
                                </div>

                                <div id="telephone-input-wrapper" class="relative hidden">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pr-3 border-r border-gray-300 pointer-events-none">
                                        <span class="text-lg">☎️</span>
                                        <span class="ml-1.5 text-sm font-semibold text-gray-600">PH</span>
                                    </div>
                                    <input type="tel" id="telephone-input"
                                        maxlength="12" placeholder="e.g. 082-123-4567"
                                        inputmode="numeric"
                                        autocomplete="off"
                                        class="w-full pl-20 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent focus:bg-white transition-all"
                                        disabled/>
                                    <div id="area-code-suggestions"
                                        class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl z-50 hidden overflow-hidden">
                                        <div class="px-3 py-2 bg-gray-50 border-b border-gray-100">
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Matching Area Codes</p>
                                        </div>
                                        <div id="area-code-list" class="max-h-52 overflow-y-auto"></div>
                                    </div>
                                </div>

                                <input type="hidden" name="contact_type" id="contact_type_input" value="mobile">
                                <input type="hidden" name="contact_number" id="contact_number_carrier">

                                <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1" id="contact-hint">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Enter your mobile number with country code
                                </p>
                            </div>
                        </div>

                    </div>

                    <div class="relative mt-4">
                        <label class="block text-gray-800 text-sm font-semibold mb-2">Industry Sector:<span class="text-red-500">*</span></label>
                        <button type="button" id="industry-dropdown-btn"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 hover:border-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                            <span id="industry-selected-text" class="text-gray-400">Please select your primary operation</span>
                            <svg id="industry-dropdown-arrow" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="industry-dropdown-menu"
                            class="fixed z-[999] bg-white border border-gray-200 rounded-xl shadow-lg max-h-96 overflow-y-auto hidden">
                            <div data-value="Accommodation &amp; Food Service (Hotels, Resorts, Restaurants, Fast Food Chains, Catering Services)" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Accommodation &amp; Food Service (Hotels, Resorts, Restaurants, Fast Food Chains, Catering Services)</div>
                            <div data-value="Administrative &amp; Support Services (Security Agencies, Manpower/Recruitment Agencies, Call Centers, Travel Agencies, Janitorial Services)" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Administrative &amp; Support Services (Security Agencies, Manpower/Recruitment Agencies, Call Centers, Travel Agencies, Janitorial Services)</div>
                            <div data-value="Agriculture, Forestry, Fishing &amp; Mining" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Agriculture, Forestry, Fishing &amp; Mining</div>
                            <div data-value="Construction" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Construction</div>
                            <div data-value="Education (Private Schools, Colleges, Universities, Training Centers)" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Education (Private Schools, Colleges, Universities, Training Centers)</div>
                            <div data-value="Electricity, Gas, Water &amp; Waste Management (Power Plants, Electric Co-ops, Water Districts, Garbage/Recycling Firms)" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Electricity, Gas, Water &amp; Waste Management (Power Plants, Electric Co-ops, Water Districts, Garbage/Recycling Firms)</div>
                            <div data-value="Financial &amp; Insurance Activities (Banks, Pawnshops, Lending Investors, Insurance Companies)" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Financial &amp; Insurance Activities (Banks, Pawnshops, Lending Investors, Insurance Companies)</div>
                            <div data-value="Human Health &amp; Social Work (Hospital, Medical Clinics, Diagnostic Labs, Nursing Homes)" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Human Health &amp; Social Work (Hospital, Medical Clinics, Diagnostic Labs, Nursing Homes)</div>
                            <div data-value="Information &amp; Communication (Software Companies, ISPs, Telecoms, TV/Radio Stations, Non-Voice Tech BPO)" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Information &amp; Communication (Software Companies, ISPs, Telecoms, TV/Radio Stations, Non-Voice Tech BPO)</div>
                            <div data-value="Other Service Activities (Repairs Shops, Beauty Salons, Spas, Laundry Shops, Funeral)" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Other Service Activities (Repairs Shops, Beauty Salons, Spas, Laundry Shops, Funeral)</div>
                            <div data-value="Professional, Scientific &amp; Technical Services (Law Firms, Accounting/Auditing Firms, Engineering/Architectural Firms, Advertising Agencies)" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Professional, Scientific &amp; Technical Services (Law Firms, Accounting/Auditing Firms, Engineering/Architectural Firms, Advertising Agencies)</div>
                            <div data-value="Real Estate Activities (Real Estate Developers, Lessor of Apartment/Office Space)" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Real Estate Activities (Real Estate Developers, Lessor of Apartment/Office Space)</div>
                            <div data-value="Transportation, Storage &amp; Logistics (Trucking/Hauling Services, Warehousing, Shipping Lines, Courier Services)" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Transportation, Storage &amp; Logistics (Trucking/Hauling Services, Warehousing, Shipping Lines, Courier Services)</div>
                            <div data-value="Wholesale &amp; Retail Trade (Trading Companies, Malls, Hardware Stores, Car Dealers, Online Shops, etc.)" class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Wholesale &amp; Retail Trade (Trading Companies, Malls, Hardware Stores, Car Dealers, Online Shops, etc.)</div>
                        </div>
                        <input type="hidden" id="industry-selector-input" name="industrySelector" required>
                    </div>

                    <div class="relative mt-4">
                        <label class="block text-gray-800 text-sm font-semibold mb-2">Company Size:<span class="text-red-500">*</span></label>
                        <button type="button" id="company-size-btn"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 hover:border-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                            <span id="company-size-selected-text" class="text-gray-400">Select company size</span>
                            <svg id="company-size-arrow" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="company-size-dropdown" class="fixed z-[999] bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                            <div data-value="Less than 50" class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">Less than 50</div>
                            <div data-value="51-200" class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">51-200</div>
                            <div data-value="201-500" class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">201-500</div>
                            <div data-value="More than 500" class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">More than 500</div>
                        </div>
                        <input type="hidden" id="company-size-input" name="companySize" required>
                    </div>
                </div>

                <div class="flex justify-end mt-6 gap-2">
                    <button type="button" class="btn-next bg-teal-600 hover:bg-teal-700 text-white font-semibold px-5 sm:px-8 py-2.5 rounded-lg transition shadow-md w-full sm:w-auto">Next </button>
                </div>
            </div>
<div class="lmi-step" data-step="1" style="display:none;">

                <div class="bg-teal-50 border border-teal-200 rounded-lg p-6 mt-10 overflow-hidden">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="text-sm font-bold text-gray-800 uppercase tracking-wide">Part II: Hard-to-Fill Roles</div>
                    </div>
                    <div class="h-px bg-gray-100 mb-4"></div>
                    <p class="text-teal-700 text-xs font-medium mb-4">
                        Please identify the TOP Job Titles you find hardest to fill. Be as specific as possible (e.g., instead of "IT Skills", say "Python Programming").
                    </p>

                    <div id="jobTitlesContainer" class="space-y-6">
                        <div class="bg-white rounded-lg p-4 border border-gray-200 job-entry">
                            <div class="mb-4">
                                <label class="block text-gray-800 text-sm font-semibold mb-2"> Job Title: <span class="text-gray-700 text-sm font-medium">(Please list only one job title)</span><span class="text-red-500">*</span></label>
                                <input type="text" name="job_title[]" placeholder="e.g. Senior Java Developer" required
                                    class="job-title-input w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"/>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-800 text-sm font-semibold mb-2"> Standard Job Classifications / Families: <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <button type="button" class="job-classification-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                        <span class="job-classification-text text-gray-400">Select job classification</span>
                                        <svg class="job-classification-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="job-classification-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                        <div data-value="Accounting, Finance &amp; Banking" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Accounting, Finance &amp; Banking</div>
                                        <div data-value="Administrative, HR &amp; Office Support" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Administrative, HR &amp; Office Support</div>
                                        <div data-value="Agriculture, Forestry &amp; Agribusiness" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Agriculture, Forestry &amp; Agribusiness</div>
                                        <div data-value="Construction, Engineering &amp; Architecture" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Construction, Engineering &amp; Architecture</div>
                                        <div data-value="Customer Service &amp; BPO (Contact Center)" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Customer Service &amp; BPO (Contact Center)</div>
                                        <div data-value="Education, Training &amp; Academe" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Education, Training &amp; Academe</div>
                                        <div data-value="Healthcare, Medical &amp; Allied Services" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Healthcare, Medical &amp; Allied Services</div>
                                        <div data-value="IT, Software, Data &amp; Digital Creative" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• IT, Software, Data &amp; Digital Creative</div>
                                        <div data-value="Legal, Compliance &amp; Public Service" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Legal, Compliance &amp; Public Service</div>
                                        <div data-value="Logistics, Transport &amp; Supply Chain" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Logistics, Transport &amp; Supply Chain</div>
                                        <div data-value="Manufacturing, Production &amp; Technical" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Manufacturing, Production &amp; Technical</div>
                                        <div data-value="Sales, Marketing, Retail &amp; E-Commerce" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Sales, Marketing, Retail &amp; E-Commerce</div>
                                        <div data-value="Science, Research &amp; Laboratory" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Science, Research &amp; Laboratory</div>
                                        <div data-value="Skilled Trades, Maintenance &amp; General Services" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Skilled Trades, Maintenance &amp; General Services</div>
                                        <div data-value="Tourism, Hospitality &amp; Food Service" class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">• Tourism, Hospitality &amp; Food Service</div>
                                    </div>
                                    <input type="hidden" class="job-classification-input" name="job_classification[]" required>
                                </div>
                            </div>
                            
<div class="mb-4">
                                <label class="block text-gray-800 text-sm font-semibold mb-2"> Salary Range: <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <button type="button" class="salary-range-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                        <span class="salary-range-text text-gray-400">Select salary range</span>
                                        <svg class="salary-range-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="salary-range-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden hidden">
                                        <div data-value="₱30,000 - ₱59,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱30,000 - ₱59,999</div>
                                        <div data-value="₱60,000 - ₱89,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱60,000 - ₱89,999</div>
                                        <div data-value="₱90,000 - ₱149,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱90,000 - ₱149,999</div>
                                        <div data-value="₱150,000 - ₱499,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱150,000 - ₱499,999</div>
                                        <div data-value="₱500,000 and above" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱500,000 and above</div>
                                        <div data-value="Below ₱30,000" class="salary-range-option below-30k-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition"> 
                                        Below ₱30,000 (please specify)
                                    </div>
                                    </div>
                                    <input type="hidden" class="salary-range-input" name="salary_range[]">
                                </div>
                                
                                <div class="below-30k-input-container mt-3 hidden">
                                <label class="block text-gray-600 text-xs font-medium mb-2">Please specify the exact salary amount:</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">₱</span>
                                    <input type="text" 
                                        name="below_30k_salary[]"
                                        class="below-30k-salary-input w-full pl-8 pr-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm" 
                                        placeholder="e.g. 25,000"
                                        inputmode="numeric">
                                </div>
                            </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-800 text-sm font-semibold mb-2">Duration that the Vacancy is Open: <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <button type="button" class="duration-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                        <span class="duration-text text-gray-400">Select duration</span>
                                        <svg class="duration-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="duration-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden hidden">
                                        <div data-value="Less than 30 Days" class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">Less than 30 Days</div>
                                        <div data-value="30-60 Days" class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">30-60 Days</div>
                                        <div data-value="60-90 Days" class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">60-90 Days</div>
                                        <div data-value="90+ Days" class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">90+ Days</div>
                                    </div>
                                    <input type="hidden" class="duration-input" name="vacancy_duration[]" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-800 text-sm font-semibold mb-2">
                                    Reasons For Difficulty (Role-Level) <span class="italic text-gray-500">(Check all that apply)</span>
                                </label>
                                <div class="difficulty-reasons space-y-3">
                                    <div class="technical-skills-label p-3 border rounded-lg transition-all border-gray-200">
                                        <label class="flex items-start cursor-pointer">
                                            <input type="checkbox" name="difficulty_reasons_0[]" value="Technical / Hard Skills Missing"
                                                class="technical-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                            <div class="ml-3">
                                                <div class="font-semibold text-gray-800">Technical / Hard Skills Missing</div>
                                                <div class="text-xs text-gray-500 mt-1">Applicants do not have the required tools, software, or technical knowledge</div>
                                            </div>
                                        </label>
                                        <div class="technical-details mt-3 hidden">
                                            <label class="block text-gray-600 text-xs font-medium mb-1">What specific technical tools, software, or machinery knowledge is missing?</label>
                                            <div class="technical-tags-container flex flex-wrap gap-2 mb-2"></div>
                                            <div class="flex gap-2 skill-input-row">
                                                <input type="text" class="technical-skill-input flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                                    placeholder="Type a skill and press Enter (e.g. Python, SQL, AutoCAD...)"
                                                    enterkeyhint="done" inputmode="text"/>
                                                <button type="button" class="add-technical-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Enter</button>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Press Enter or comma to add each skill</p>
                                            <input type="hidden" class="technical-skills-input" name="technical_skills_missing[]">
                                        </div>
                                    </div>
                                    <div class="soft-skills-label p-3 border rounded-lg transition-all border-gray-200">
                                        <label class="flex items-start cursor-pointer">
                                            <input type="checkbox" name="difficulty_reasons_0[]" value="Soft / Employability Skills Missing"
                                                class="soft-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                            <div class="ml-3">
                                                <div class="font-semibold text-gray-800">Soft / Employability Skills Missing</div>
                                                <div class="text-xs text-gray-500 mt-1">Applicants cannot communicate effectively, work in teams, or demonstrate professionalism</div>
                                            </div>
                                        </label>
                                        <div class="soft-details mt-3 hidden">
                                            <label class="block text-gray-600 text-xs font-medium mb-1">What attitude or behavioral traits cause you to reject applicants?</label>
                                            <div class="soft-tags-container flex flex-wrap gap-2 mb-2"></div>
                                            <div class="flex gap-2 skill-input-row">
                                                <input type="text" class="soft-skill-input flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                                    placeholder="Type a trait and press Enter (e.g. Poor communication, Unprofessional...)"
                                                    enterkeyhint="done" inputmode="text"/>
                                                <button type="button" class="add-soft-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Enter</button>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Press Enter or comma to add each trait</p>
                                            <input type="hidden" class="soft-skills-input" name="soft_skills_missing[]">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4 mt-6 pt-4 border-t border-gray-200">
                                <label class="block text-gray-800 text-sm font-semibold mb-3">
                                    How much does the difficulty finding qualified applicants for this role impact your business operations? <span class="text-red-500">*</span>
                                </label>
                                <div class="impact-level space-y-3">
                                    <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                        <input type="radio" name="impact_level_0" value="High" required class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                        <div class="ml-3 flex-1">
                                            <div class="font-semibold text-gray-900">High Impact</div>
                                            <div class="text-xs text-gray-500 mt-1">Operations are significantly disrupted, critical tasks or projects are delayed, affecting productivity and revenue</div>
                                        </div>
                                    </label>
                                    <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                        <input type="radio" name="impact_level_0" value="Medium" required class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                        <div class="ml-3 flex-1">
                                            <div class="font-semibold text-gray-900">Medium Impact</div>
                                            <div class="text-xs text-gray-500 mt-1">Operations continue but require overtime, increased workload for existing staff, or minor project delays</div>
                                        </div>
                                    </label>
                                    <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                        <input type="radio" name="impact_level_0" value="Low" required class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                        <div class="ml-3 flex-1">
                                            <div class="font-semibold text-gray-900">Low Impact</div>
                                            <div class="text-xs text-gray-500 mt-1">Minimal impact; new hires can be trained internally without significant operational disruptions</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-job-title-btn"
                        class="w-full mt-4 px-4 py-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg transition shadow-md flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Another Job Title
                    </button>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-between mt-6 gap-3">
                    <button type="button" class="btn-prev bg-white hover:bg-gray-50 text-gray-700 font-semibold px-5 sm:px-8 py-2.5 rounded-lg transition border border-gray-300 shadow-sm w-full sm:w-auto"> Previous</button>
                    <button type="button" class="btn-next bg-teal-600 hover:bg-teal-700 text-white font-semibold px-5 sm:px-8 py-2.5 rounded-lg transition shadow-md w-full sm:w-auto">Next </button>
                </div>
            </div>
<div class="lmi-step" data-step="2" style="display:none;">

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mt-10">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-sm font-bold text-gray-800 uppercase tracking-wide">Part III: Diagnosis of Mismatch</div>
                    </div>
                    <div class="h-px bg-gray-100 mb-4"></div>
                    <p class="text-gray-600 text-xs font-medium mb-6">
                        For applicants who meet formal qualifications (degree, license, or certification), which observable factors most often cause them to be rejected?
                    </p>

                    <div class="space-y-6">
                        <div class="bg-white rounded-lg p-5 border border-gray-200">
                            <label class="block text-gray-800 text-sm font-semibold mb-3">
                                Reason Qualified Applicants Are Rejected (Applicant-Level) <span class="text-gray-500 italic text-xs">(Check all that apply)</span>
                            </label>
                            <div class="space-y-3">
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="checkbox" name="rejection_reasons[]" value="Lack of practical / hands-on experience" class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <div class="font-semibold text-gray-900">Lack of practical / hands-on experience</div>
                                        <div class="text-xs text-gray-500 mt-1">Cannot apply theory to real work; requires supervision</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="checkbox" name="rejection_reasons[]" value="Skills are outdated" class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <div class="font-semibold text-gray-900">Skills are outdated</div>
                                        <div class="text-xs text-gray-500 mt-1">Training received does not match current tools, systems, or industry practices</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="checkbox" name="rejection_reasons[]" value="Poor communication skills" class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <div class="font-semibold text-gray-900">Poor communication skills</div>
                                        <div class="text-xs text-gray-500 mt-1">Oral, written, presentation, or cross-cultural communication issues</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="checkbox" name="rejection_reasons[]" value="Low job readiness / poor interview performance" class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <div class="font-semibold text-gray-900">Low job readiness / poor interview performance</div>
                                        <div class="text-xs text-gray-500 mt-1">Cannot demonstrate readiness during recruitment; fails assessments; lacks workplace etiquette</div>
                                    </div>
                                </label>
                                <div class="other-rejection-option border rounded-lg transition-all border-gray-200">
                                    <label class="flex items-start p-3 cursor-pointer">
                                        <input type="checkbox" name="rejection_reasons[]" value="Other" class="other-rejection-checkbox mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                        <div class="ml-3 flex-1"><div class="font-semibold text-gray-900">Other (please specify)</div></div>
                                    </label>
                                    <div class="other-rejection-input px-3 pb-3 ml-7 hidden">
                                        <textarea name="rejection_reasons_other" placeholder="Please specify other reasons..."
                                            rows="2" enterkeyhint="done"
                                            class="other-specify-textarea"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-5 border border-gray-200">
                            <label class="block text-gray-800 text-sm font-semibold mb-3">
                                How often do you coordinate with Universities/Colleges to discuss your skills requirements? <span class="text-gray-500 italic text-xs">(Select ONE)</span>
                            </label>
                            <div class="coordination-options space-y-3">
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="radio" name="coordination_frequency" value="Never" required class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                    <div class="ml-3 flex-1"><div class="font-semibold text-gray-900">Never</div></div>
                                </label>
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="radio" name="coordination_frequency" value="Rarely" required class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <div class="font-semibold text-gray-900">Rarely</div>
                                        <div class="text-xs text-gray-500 mt-1">Only when invited to graduations/events</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="radio" name="coordination_frequency" value="Occasionally" required class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <div class="font-semibold text-gray-900">Occasionally</div>
                                        <div class="text-xs text-gray-500 mt-1">During OJT placement</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                    <input type="radio" name="coordination_frequency" value="Frequently" required class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                    <div class="ml-3 flex-1">
                                        <div class="font-semibold text-gray-900">Frequently</div>
                                        <div class="text-xs text-gray-500 mt-1">We sit on advisory boards/curriculum reviews</div>
                                    </div>
                                </label>
                                <div class="other-coordination-option border rounded-lg transition-all border-gray-200">
                                    <label class="flex items-start p-3 cursor-pointer">
                                        <input type="radio" name="coordination_frequency" value="Other" required class="other-coordination-radio mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                        <div class="ml-3 flex-1"><div class="font-semibold text-gray-900">Other (please specify)</div></div>
                                    </label>
                                    <div class="other-coordination-input px-3 pb-3 ml-7 hidden">
                                        <textarea name="coordination_frequency_other" placeholder="Please specify..."
                                            rows="2" enterkeyhint="done"
                                            class="other-specify-textarea"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-between mt-6 gap-3">
                    <button type="button" class="btn-prev bg-white hover:bg-gray-50 text-gray-700 font-semibold px-5 sm:px-8 py-2.5 rounded-lg transition border border-gray-300 shadow-sm w-full sm:w-auto"> Previous</button>
                    <button type="button" class="btn-next bg-teal-600 hover:bg-teal-700 text-white font-semibold px-5 sm:px-8 py-2.5 rounded-lg transition shadow-md w-full sm:w-auto">Next </button>
                </div>
            </div>
<div class="lmi-step" data-step="3" style="display:none;">

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mt-8">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                            </svg>
                        </div>
                        <div class="text-sm font-bold text-gray-800 uppercase tracking-wide">Part IV: Engagement &amp; Next Steps</div>
                    </div>
                    <div class="h-px bg-gray-100 mb-4"></div>
                    <p class="text-gray-600 text-xs font-medium mb-4">Help us understand what features would be most valuable to you.</p>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-gray-800 text-sm font-semibold mb-3">
                                If DOLE provides a Regional LMI Dashboard, what features would be most useful for you? <span class="text-gray-500 text-xs">(Select top 2)</span>
                            </label>
                            <div class="space-y-3" id="lmi-features-group">
                                <label class="lmi-feature-label flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">
                                    <input type="checkbox" name="lmi_features[]" value="Viewing the supply of graduates" class="lmi-feature-checkbox mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3 flex-1"><div class="font-semibold text-gray-900">Viewing the supply of graduates (e.g., "How many IT grads will graduate next year?")</div></div>
                                </label>
                                <label class="lmi-feature-label flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">
                                    <input type="checkbox" name="lmi_features[]" value="A channel to submit real-time feedback" class="lmi-feature-checkbox mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3 flex-1"><div class="font-semibold text-gray-900">A channel to submit real-time feedback on curriculum quality</div></div>
                                </label>
                                <label class="lmi-feature-label flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">
                                    <input type="checkbox" name="lmi_features[]" value="A directory of job placement offices" class="lmi-feature-checkbox mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3 flex-1"><div class="font-semibold text-gray-900">A directory of job placement offices and Public Employment offices (PESOs)</div></div>
                                </label>
                                <div class="lmi-other-option border rounded-lg border-gray-200 transition-all">
                                    <label class="lmi-feature-label flex items-start p-3 cursor-pointer hover:bg-blue-50 hover:border-blue-300">
                                        <input type="checkbox" name="lmi_features[]" value="Other" class="lmi-feature-checkbox lmi-other-checkbox mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <div class="ml-3 flex-1"><div class="font-semibold text-gray-900">Other (please specify)</div></div>
                                    </label>
                                    <div class="lmi-other-input px-3 pb-3 ml-7 hidden">
                                        <textarea name="lmi_features_other" placeholder="Please specify..."
                                            rows="2" enterkeyhint="done"
                                            class="other-specify-textarea focus-blue"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
<div>
                            <label class="block text-gray-800 text-sm font-semibold mb-2">
                                Additional Insights or Suggestions: <span class="text-gray-500 text-xs">(Optional)</span>
                            </label>
                            <textarea name="specific_inputs" rows="4" placeholder="Please share any additional insights or suggestions..."
                                class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none"
                                style="max-height:180px; overflow-y:auto;"></textarea>
                        </div>
                    </div>
                </div>

<div class="mt-6 mb-2">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" name="consent" value="1" required class="consent-checkbox mt-1 w-4 h-4 text-teal-600">
                        <span class="ml-3 text-l text-gray-700">
                            By proceeding, I signify my consent to the processing of my personal data for labor market intelligence purposes, in accordance with RA 10173 (Data Privacy Act of 2012) and its IRR. <span class="text-red-500">*</span>
                        </span>
                    </label>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-between mt-6 gap-3">
                    <button type="button" class="btn-prev bg-white hover:bg-gray-50 text-gray-700 font-semibold px-5 sm:px-8 py-2.5 rounded-lg transition border border-gray-300 shadow-sm w-full sm:w-auto"> Previous</button>
                    <button type="submit" class="btn-submit-lmi bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-5 sm:px-8 rounded-lg transition shadow-lg w-full sm:w-auto">
                        Submit LMI Matrix
                    </button>
                </div>
            </div>
</form>
        </div>
        </div>
    </div>
</div>

<div id="confirmation-modal" class="fixed inset-0 flex items-center justify-center px-4 hidden" style="z-index: 9999;">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative p-6 z-10">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Submission</h3>
            <p class="text-sm text-gray-500 mb-6">
                Are you sure you want to submit this Industry Skills Need Survey? Please ensure all information is accurate before proceeding.
            </p>
            <div class="flex gap-3">
                <button type="button" id="cancel-submit-btn"
                        class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
                    No, Cancel
                </button>
                <button type="button" id="confirm-submit-btn"
                        class="flex-1 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg transition">
                    Yes, Submit
                </button>
            </div>
        </div>
    </div>
</div>
</div>

<div id="success-modal" class="fixed inset-0 flex items-center justify-center px-4 hidden" style="z-index: 9999;">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative p-6 z-10">
    
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Successfully Submitted!</h3>
            <p class="text-sm text-gray-500 mb-6">
                Your Industry Skills Need Survey has been submitted successfully. Thank you for your contribution to the Labor Market Intelligence system.
            </p>
            <button type="button" id="close-success-btn"
                    class="w-full px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg transition">
                Close
            </button>
        </div>
    </div>
</div>
</div>
</div>
</div>
 <script>
// Toggle role details function
function toggleRoleDetails(submissionId, index) {
    const details = document.getElementById(`role-details-${submissionId}-${index}`);
    const icon = details.previousElementSibling.querySelector('.expand-icon');
    
    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        details.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

// Prepare comparison data (initial load from server)
let comparisonData = @json($comparison_data ?? []);
let currentSelectedYear = {{ $selected_year ?? 'null' }};

// Build and render the main chart
let mainChart = null;
let expandedChart = null;

function renderMainChart() {
    const ctx = document.getElementById('highVolumeHorizontalChart');
    if (!ctx || !comparisonData.length) return;
    if (mainChart) mainChart.destroy();
    mainChart = new Chart(ctx, buildChartConfig(comparisonData));
}

function buildChartConfig(data, axisSize = null) {
    const isMobile = window.innerWidth < 640;
    const isTablet = window.innerWidth < 1024;
    axisSize = axisSize ?? (isMobile ? 9 : isTablet ? 11 : 12);

    const labels      = data.map(d => d.title);
    const currentData = data.map(d => d.current_count);
    const prevData    = data.map(d => d.previous_count);
    const hasPrev     = prevData.some(v => v && v > 0);

    const datasets = [
        ...(hasPrev ? [{
            label: String(currentSelectedYear - 1),
            data: prevData,
            backgroundColor: 'rgba(16, 185, 129, 0.85)',
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 0, borderRadius: 4, barThickness: isMobile ? 8 : 14,
        }] : []),
        {
            label: String(currentSelectedYear),
            data: currentData,
            backgroundColor: 'rgba(99, 102, 241, 0.9)',
            borderColor: 'rgba(99, 102, 241, 1)',
            borderWidth: 0, borderRadius: 4, barThickness: isMobile ? 8 : 14,
        }
    ];

    return {
        type: 'bar',
        data: { labels, datasets },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: { right: isMobile ? 8 : 16 }
            },
            plugins: {
                legend: {
                    display: true, position: 'top', align: 'end',
                    labels: { boxWidth: 10, boxHeight: 10, font: { size: axisSize, weight: '500' }, padding: isMobile ? 8 : 15, usePointStyle: true, pointStyle: 'circle' }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)', padding: 12,
                    titleFont: { size: 13, weight: 'bold' }, bodyFont: { size: 12 },
                    callbacks: {
                        title: ctx => ctx[0].label,
                        label: function(context) {
                            let label = (context.dataset.label || '') + ': ';
                            label += context.parsed.x.toLocaleString();
                            if (context.datasetIndex === 1 && comparisonData[context.dataIndex]) {
                                const { change, is_new } = comparisonData[context.dataIndex];
                                if (is_new) label += ' (NEW)';
                                else if (change !== 0) label += ` (${change > 0 ? '+' : ''}${change}%)`;
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { display: true, color: 'rgba(0,0,0,0.03)' },
                    ticks: {
                        font: { size: axisSize },
                        maxTicksLimit: isMobile ? 4 : 6,
                        callback: v => v >= 1000 ? (v/1000)+'k' : v
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: {
                        font: { size: axisSize, weight: '500' },
                        color: '#374151',
                        callback: function(value) {
                            const label = this.getLabelForValue(value);
                            if (isMobile && label && label.length > 22) {
                                return label.substring(0, 20) + '…';
                            }
                            return label;
                        }
                    }
                }
            },
            interaction: { mode: 'y', intersect: false }
        }
    };
}

// Fetch new chart data when year changes — no page reload
async function updateChart(year) {
    try {
        const res  = await fetch(`/api/job-market/chart-data?year=${year}`);
        const json = await res.json();

        comparisonData      = json.comparison_data;
        currentSelectedYear = json.selected_year;

        // Update subtitle - show only when previous data exists
        const subtitle     = document.getElementById('chartSubtitle');
        const prevLabel    = document.getElementById('prevYearLabel');
        const currentLabel = document.getElementById('currentYearLabel');

        if (prevLabel)    prevLabel.textContent    = json.previous_year;
        if (currentLabel) currentLabel.textContent = json.selected_year;
        if (subtitle)     subtitle.style.display   = json.has_previous_data ? '' : 'none';

        renderMainChart();
    } catch (e) {
        console.error('Chart update failed:', e);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    renderMainChart();
});

// Expand chart function
function expandChart() {
    const modal = document.getElementById('chartModal');
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');

    // Hide navbar so it doesn't overlap the modal
    const navbar = document.querySelector('nav');
    if (navbar) {
        navbar.style.zIndex = '-1';
        navbar.style.visibility = 'hidden';
    }

    if (expandedChart) {
        expandedChart.destroy();
    }
    
    const expandedCtx = document.getElementById('highVolumeExpandedChart');
    if (expandedCtx && comparisonData && comparisonData.length > 0) {
        const expandedSize = window.innerWidth < 640 ? 10 : window.innerWidth < 1024 ? 12 : 14;
        expandedChart = new Chart(expandedCtx, buildChartConfig(comparisonData, expandedSize));
    }
}

// Close chart function
function closeChart() {
    const modal = document.getElementById('chartModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');

    // Restore navbar
    const navbar = document.querySelector('nav');
    if (navbar) {
        navbar.style.zIndex = '';
        navbar.style.visibility = '';
    }

    if (expandedChart) {
        expandedChart.destroy();
        expandedChart = null;
    }
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeChart();
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Main application state
const appState = {
    showLmiMatrix: false
};

// Modal functionality
const lmiMatrixModal = document.getElementById('lmi-matrix-modal');
const showLmiMatrixBtn = document.getElementById('show-lmi-matrix-btn');
const closeModalBtn = document.getElementById('close-modal-btn');
const modalBackdrop = document.getElementById('modal-backdrop');
const mainContent = document.getElementById('main-content');

// Confirmation and Success Modals
const confirmationModal = document.getElementById('confirmation-modal');
const successModal = document.getElementById('success-modal');
const lmiForm = document.getElementById('lmi-form');
const cancelSubmitBtn = document.getElementById('cancel-submit-btn');
const confirmSubmitBtn = document.getElementById('confirm-submit-btn');
const closeSuccessBtn = document.getElementById('close-success-btn');

// LMI Matrix Modal Functions
function showModal() {
    lmiMatrixModal.classList.remove('hidden');
    //mainContent.classList.add('blur-sm');
    appState.showLmiMatrix = true;
    document.body.style.overflow = 'hidden';
    
    // Hide navbar by setting z-index lower than modal
    const navbar = document.querySelector('nav');
    if (navbar) {
        navbar.style.zIndex = '-1';
        navbar.style.visibility = 'hidden';
    }
    
    // ADD THIS: Initialize autocomplete when modal opens
    setTimeout(() => {

        initializeAllAutocompletes();

    }, 200);
}

function hideModal() {

    lmiMatrixModal.classList.add('hidden');
    
    // Remove blur if mainContent exists
    if (mainContent) {
        mainContent.classList.remove('blur-sm');
    }
    
    // CRITICAL: Restore scrolling

    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('overflow-y');
    document.documentElement.style.removeProperty('overflow');
    
    // Double-check after a tiny delay
    setTimeout(() => {
        if (document.body.style.overflow === 'hidden') {
            console.warn('⚠️ Body still has overflow:hidden! Forcing fix...');
            document.body.style.overflow = 'auto';
        }

    }, 50);
    
    // Show navbar by restoring z-index
    const navbar = document.querySelector('nav');
    if (navbar) {
        navbar.style.zIndex = '';
        navbar.style.visibility = '';

    }
    
    appState.showLmiMatrix = false;

}

// Function to hide confirmation modal
function hideConfirmationModal() {
    confirmationModal.classList.add('hidden');
}

// Function to show success modal (NO BLUR)
function showSuccessModal() {
    successModal.classList.remove('hidden');
    // Don't blur the LMI form - keep it clear behind the modal
}

// Function to hide success modal AND close the LMI form
function hideSuccessModal() {
    // First hide the success modal
    successModal.classList.add('hidden');
    
    // Then close the LMI Matrix modal after a brief delay for smooth transition
    setTimeout(() => {
        hideModal();
    }, 300);
}

// Intercept form submission to show confirmation modal instead
lmiForm.addEventListener('submit', function(e) {
    e.preventDefault();
    e.stopPropagation();
    // Run Step 4 validation before showing the confirmation modal
    if (!validateStep(3)) return;
    confirmationModal.classList.remove('hidden');
});

// Cancel button in confirmation modal
cancelSubmitBtn.addEventListener('click', hideConfirmationModal);

// Confirm submission button
confirmSubmitBtn.addEventListener('click', async () => {
    // Hide confirmation modal
    hideConfirmationModal();
    
    // Validate form
    const consentCheckbox = document.querySelector('.consent-checkbox');
    if (!consentCheckbox || !consentCheckbox.checked) {
        alert('Please consent to submit this data for labor market intelligence purposes.');
        consentCheckbox.focus();
        return;
    }
    
    // Populate contact number carrier before collecting FormData
    const contactTypeVal = document.getElementById('contact_type_input')?.value;
    const carrier = document.getElementById('contact_number_carrier');
    if (carrier) {
        if (contactTypeVal === 'mobile') {
            const mob = document.getElementById('mobile-input');
            carrier.value = mob ? selectedCountry.dial + mob.value : '';
        } else {
            const tel = document.getElementById('telephone-input');
            carrier.value = tel ? tel.value : '';
        }
    }

    // Gather form data
    const formData = new FormData(lmiForm);
    
    // Show loading state
    const originalText = confirmSubmitBtn.textContent;
    confirmSubmitBtn.textContent = 'Submitting...';
    confirmSubmitBtn.disabled = true;
    
    try {
        // Log what we're sending (for debugging)

        // Submit via AJAX
        const response = await fetch(lmiForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        // Try to get the response text for debugging
        const responseText = await response.text();

        if (response.ok) {
            // Show success modal
            showSuccessModal();
            // Reset the form
            lmiForm.reset();
            
            // Reset all dropdowns to placeholder state
            resetFormDropdowns();

            // Reset step back to step 1
            showStep(0);
            
        } else {
            // Try to parse as JSON for error messages
            try {
                const errorData = JSON.parse(responseText);
                throw new Error(errorData.message || 'Submission failed with status: ' + response.status);
            } catch (e) {
                throw new Error('Submission failed with status: ' + response.status);
            }
        }
    } catch (error) {
        console.error('Full submission error:', error);
        alert('There was an error submitting the form. Please try again. Error: ' + error.message);
    } finally {
        // Reset button state
        confirmSubmitBtn.textContent = originalText;
        confirmSubmitBtn.disabled = false;
    }
});

// Close success modal button - closes both modals
closeSuccessBtn.addEventListener('click', hideSuccessModal);

// Helper function to reset all form dropdowns
function resetFormDropdowns() {
    // Reset industry dropdown
    const industryText = document.getElementById('industry-selected-text');
    const industryInput = document.getElementById('industry-selector-input');
    if (industryText && industryInput) {
        industryText.textContent = 'Please select your primary operation';
        industryText.classList.add('text-gray-400');
        industryText.classList.remove('text-gray-600');
        industryInput.value = '';
    }
    
    // Reset company size dropdown
    const companySizeText = document.getElementById('company-size-selected-text');
    const companySizeInput = document.getElementById('company-size-input');
    if (companySizeText && companySizeInput) {
        companySizeText.textContent = 'Select company size';
        companySizeText.classList.add('text-gray-400');
        companySizeText.classList.remove('text-gray-600');
        companySizeInput.value = '';
    }
    
    // Reset all job entry dropdowns
    document.querySelectorAll('.job-entry').forEach(entry => {
        const classText = entry.querySelector('.job-classification-text');
        const classInput = entry.querySelector('.job-classification-input');
        if (classText && classInput) {
            classText.textContent = 'Select job classification';
            classText.classList.add('text-gray-400');
            classText.classList.remove('text-gray-600');
            classInput.value = '';
        }
        
        const durationText = entry.querySelector('.duration-text');
        const durationInput = entry.querySelector('.duration-input');
        if (durationText && durationInput) {
            durationText.textContent = 'Select duration';
            durationText.classList.add('text-gray-400');
            durationText.classList.remove('text-gray-600');
            durationInput.value = '';
        }
        
        // Clear skill tags — call reset() to also clear the internal tags array in the closure,
        // so old values do not ghost back when the user starts typing in a new session.
        const techTagsContainer = entry.querySelector('.technical-tags-container');
        if (techTagsContainer) {
            if (techTagsContainer._tagSystem) {
                techTagsContainer._tagSystem.reset();
            } else {
                techTagsContainer.innerHTML = '';
            }
        }
        
        const softTagsContainer = entry.querySelector('.soft-tags-container');
        if (softTagsContainer) {
            if (softTagsContainer._tagSystem) {
                softTagsContainer._tagSystem.reset();
            } else {
                softTagsContainer.innerHTML = '';
            }
        }
        
        // Uncheck and hide detail sections
        const techCheckbox = entry.querySelector('.technical-checkbox');
        const techDetails = entry.querySelector('.technical-details');
        if (techCheckbox && techDetails) {
            techCheckbox.checked = false;
            techDetails.classList.add('hidden');
            techCheckbox.closest('label')?.classList.remove('border-teal-500', 'bg-teal-50');
            techCheckbox.closest('label')?.classList.add('border-gray-200', 'hover:bg-gray-50');
        }
        
        const softCheckbox = entry.querySelector('.soft-checkbox');
        const softDetails = entry.querySelector('.soft-details');
        if (softCheckbox && softDetails) {
            softCheckbox.checked = false;
            softDetails.classList.add('hidden');
            softCheckbox.closest('label')?.classList.remove('border-teal-500', 'bg-teal-50');
            softCheckbox.closest('label')?.classList.add('border-gray-200', 'hover:bg-gray-50');
        }
    });
    
    // Remove all additional job entries (keep only the first one)
    const jobEntries = document.querySelectorAll('.job-entry');
    jobEntries.forEach((entry, index) => {
        if (index > 0) {
            entry.remove();
        }
    });

    // FIX 1: Reset salary range dropdown for every job entry
    document.querySelectorAll('.job-entry').forEach(entry => {
        const salaryText = entry.querySelector('.salary-range-text');
        const salaryInput = entry.querySelector('.salary-range-input');
        const salaryBtn = entry.querySelector('.salary-range-btn');
        const salaryArrow = entry.querySelector('.salary-range-arrow');
        const below30kContainer = entry.querySelector('.below-30k-input-container');
        const below30kInput = entry.querySelector('.below-30k-salary-input');
        if (salaryText) {
            salaryText.textContent = 'Select salary range';
            salaryText.classList.add('text-gray-400');
            salaryText.classList.remove('text-gray-700');
        }
        if (salaryInput) salaryInput.value = '';
        if (salaryBtn) salaryBtn.classList.remove('border-red-500');
        if (salaryArrow) salaryArrow.classList.remove('rotate-180');
        if (below30kContainer) below30kContainer.classList.add('hidden');
        if (below30kInput) { below30kInput.value = ''; below30kInput.required = false; }
    });

    // FIX 2 (part of): Reset lmi-feature checkboxes: uncheck, re-enable, restore opacity/cursor
    document.querySelectorAll('.lmi-feature-checkbox').forEach(cb => {
        cb.checked = false;
        cb.disabled = false;
        const wrapper = cb.closest('label') || cb.closest('.lmi-other-option');
        if (wrapper) { wrapper.style.opacity = ''; wrapper.style.cursor = ''; }
    });

    // Hide the "Other" text input if visible
    const lmiOtherInput = document.querySelector('.lmi-other-input');
    if (lmiOtherInput) {
        lmiOtherInput.classList.add('hidden');
        const otherTextField = lmiOtherInput.querySelector('textarea[name="lmi_features_other"]');
        if (otherTextField) otherTextField.value = '';
    }
}

// Make sure these elements exist before adding event listeners
if (showLmiMatrixBtn) {
    showLmiMatrixBtn.addEventListener('click', showModal);
}

if (closeModalBtn) {
    closeModalBtn.addEventListener('click', hideModal);
}

if (modalBackdrop) {
    modalBackdrop.addEventListener('click', hideModal);
}

// Also close modals when clicking on backdrop
confirmationModal.addEventListener('click', (e) => {
    if (e.target === confirmationModal || e.target.classList.contains('absolute')) {
        hideConfirmationModal();
    }
});

successModal.addEventListener('click', (e) => {
    if (e.target === successModal || e.target.classList.contains('absolute')) {
        hideSuccessModal();
    }
});

// Close modals with ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        if (!confirmationModal.classList.contains('hidden')) {
            hideConfirmationModal();
        } else if (!successModal.classList.contains('hidden')) {
            hideSuccessModal();
        } else if (appState.showLmiMatrix) {
            hideModal();
        }
    }
});

    // Dropdown functionality
    function createDropdown(buttonId, menuId, selectedTextId, hiddenInputId, optionsSelector, arrowId = null) {
        const button = document.getElementById(buttonId);
        const menu = document.getElementById(menuId);
        const selectedText = document.getElementById(selectedTextId);
        const hiddenInput = document.getElementById(hiddenInputId);
        const arrow = arrowId ? document.getElementById(arrowId) : null;
        const options = menu.querySelectorAll(optionsSelector);

        function positionMenu() {
            const rect = button.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom - 12;
            menu.style.top = (rect.bottom + 8) + 'px';
            menu.style.left = rect.left + 'px';
            menu.style.width = rect.width + 'px';
            menu.style.maxHeight = Math.min(384, spaceBelow) + 'px';
        }

        function toggleMenu() {
            const isHidden = menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            if (arrow) {
                arrow.classList.toggle('rotate-180');
            }
            if (isHidden) {
                positionMenu();
                menu.scrollTop = 0;
            }
            
            // Close other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                if (otherMenu !== menu && !otherMenu.classList.contains('hidden')) {
                    otherMenu.classList.add('hidden');
                    const otherArrow = otherMenu.previousElementSibling?.querySelector('.rotate-180');
                    if (otherArrow) {
                        otherArrow.classList.remove('rotate-180');
                    }
                }
            });
        }

        button.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });

        options.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.getAttribute('data-value');
                selectedText.textContent = value;
                selectedText.classList.remove('text-gray-400');
                selectedText.classList.add('text-gray-600');
                hiddenInput.value = value;
                menu.classList.add('hidden');
                if (arrow) {
                    arrow.classList.remove('rotate-180');
                }
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!button.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
                if (arrow) {
                    arrow.classList.remove('rotate-180');
                }
            }
        });

        // Reposition on scroll or resize
        window.addEventListener('scroll', () => { if (!menu.classList.contains('hidden')) positionMenu(); }, true);
        window.addEventListener('resize', () => { if (!menu.classList.contains('hidden')) positionMenu(); });

        return { button, menu, selectedText, hiddenInput };
    }

    // Initialize dropdowns
    const industryDropdown = createDropdown(
        'industry-dropdown-btn',
        'industry-dropdown-menu',
        'industry-selected-text',
        'industry-selector-input',
        '.industry-option',
        'industry-dropdown-arrow'
    );

    const companySizeDropdown = createDropdown(
        'company-size-btn',
        'company-size-dropdown',
        'company-size-selected-text',
        'company-size-input',
        '.company-size-option',
        'company-size-arrow'
    );

    // Job entry functionality
    function createJobEntryDropdown(button, menu, textElement, inputElement, arrowElement, optionsSelector) {
        function toggleMenu() {
            menu.classList.toggle('hidden');
            arrowElement.classList.toggle('rotate-180');
            
            // Close other dropdowns in the same job entry
            const jobEntry = button.closest('.job-entry');
            jobEntry.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                if (otherMenu !== menu && !otherMenu.classList.contains('hidden')) {
                    otherMenu.classList.add('hidden');
                    const otherArrow = otherMenu.previousElementSibling?.querySelector('.rotate-180');
                    if (otherArrow) {
                        otherArrow.classList.remove('rotate-180');
                    }
                }
            });
        }

        button.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });

        const options = menu.querySelectorAll(optionsSelector);
        options.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.getAttribute('data-value');
                textElement.textContent = value;
                textElement.classList.remove('text-gray-400');
                textElement.classList.add('text-gray-600');
                inputElement.value = value;
                menu.classList.add('hidden');
                arrowElement.classList.remove('rotate-180');
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!button.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
                arrowElement.classList.remove('rotate-180');
            }
        });
    }

    // Skill tags functionality
    function createSkillTagSystem(container, addButton, input, hiddenInput, tagsContainer) {
        const tags = [];
        
        function updateTags() {
            tagsContainer.innerHTML = '';
            tags.forEach((tag, index) => {
                const tagElement = document.createElement('span');
                tagElement.className = 'inline-flex items-center gap-1 px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm';
                tagElement.innerHTML = `
                    <span>${tag}</span>
                    <button type="button" class="remove-tag hover:bg-teal-200 rounded-full p-0.5" data-index="${index}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                tagsContainer.appendChild(tagElement);
            });
            
            // Update hidden input
            hiddenInput.value = tags.join(', ');
            
            // Bug fix: stopPropagation so clicking remove does not bubble up to the
            // parent <label> and accidentally toggle the checkbox.
            tagsContainer.querySelectorAll('.remove-tag').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const index = parseInt(e.target.closest('.remove-tag').getAttribute('data-index'));
                    tags.splice(index, 1);
                    updateTags();
                });
            });
        }
        
        function addTag() {
            const tag = input.value.trim();
            // Bug fix: case-insensitive duplicate check so "Apple" and "APPLE" are treated as the same.
            if (tag && !tags.some(t => t.toLowerCase() === tag.toLowerCase())) {
                tags.push(tag);
                input.value = '';
                updateTags();
            } else {
                input.value = '';
            }
        }
        
        // Bug fix: expose reset() so resetFormDropdowns can clear the internal tags array,
        // not just the DOM — otherwise old tags ghost back when the user types in a new session.
        function reset() {
            tags.length = 0;
            tagsContainer.innerHTML = '';
            hiddenInput.value = '';
            input.value = '';
        }
        
        addButton.addEventListener('click', addTag);
        
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                // ← NEW: check if autocomplete has a highlighted item
                const autocompleteDropdown = input.parentElement
                    ? input.parentElement.querySelector('.autocomplete-suggestions')
                    : null;
                const hasHighlighted = autocompleteDropdown
                    && !autocompleteDropdown.classList.contains('hidden')
                    && autocompleteDropdown.querySelector('.bg-teal-100');
                if (hasHighlighted) return; // ← let autocomplete handle it
                addTag();
            }
        });
        return { tags, updateTags, addTag, reset };
    }

    // Checkbox show/hide functionality
    function setupCheckboxToggle(checkbox, targetElement) {
        checkbox.addEventListener('change', () => {
            // Outer wrapper is now a <div> (not a <label>) so we target the
            // nearest element that carries the border/bg classes.
            const wrapper = checkbox.closest('.technical-skills-label, .soft-skills-label');
            if (checkbox.checked) {
                targetElement.classList.remove('hidden');
                wrapper?.classList.add('border-teal-500', 'bg-teal-50');
                wrapper?.classList.remove('border-gray-200');
            } else {
                targetElement.classList.add('hidden');
                wrapper?.classList.remove('border-teal-500', 'bg-teal-50');
                wrapper?.classList.add('border-gray-200');

                // FIX 2: Clear all tags when the skill checkbox is unchecked
                const tagsContainer = targetElement.querySelector('.technical-tags-container, .soft-tags-container');
                if (tagsContainer) {
                    if (tagsContainer._tagSystem) {
                        tagsContainer._tagSystem.reset();
                    } else {
                        tagsContainer.innerHTML = '';
                    }
                }
                // Also clear the hidden input that holds the JSON array
                const hiddenInput = targetElement.querySelector('.technical-skills-input, .soft-skills-input');
                if (hiddenInput) hiddenInput.value = '';

                // Also clear the text input field
                const textInput = targetElement.querySelector('.technical-skill-input, .soft-skill-input');
                if (textInput) textInput.value = '';
            }
        });
    }

    // Initialize first job entry
    function initializeJobEntry(jobEntry) {
        // Classification dropdown
        const classBtn = jobEntry.querySelector('.job-classification-btn');
        const classMenu = jobEntry.querySelector('.job-classification-menu');
        const classText = jobEntry.querySelector('.job-classification-text');
        const classInput = jobEntry.querySelector('.job-classification-input');
        const classArrow = jobEntry.querySelector('.job-classification-arrow');
        
        if (classBtn && classMenu) {
            createJobEntryDropdown(
                classBtn,
                classMenu,
                classText,
                classInput,
                classArrow,
                '.job-classification-option'
            );
        }
        
        // Duration dropdown
        const durationBtn = jobEntry.querySelector('.duration-btn');
        const durationMenu = jobEntry.querySelector('.duration-menu');
        const durationText = jobEntry.querySelector('.duration-text');
        const durationInput = jobEntry.querySelector('.duration-input');
        const durationArrow = jobEntry.querySelector('.duration-arrow');
        
        if (durationBtn && durationMenu) {
            createJobEntryDropdown(
                durationBtn,
                durationMenu,
                durationText,
                durationInput,
                durationArrow,
                '.duration-option'
            );
        }
        
        // Technical skills
        const techCheckbox = jobEntry.querySelector('.technical-checkbox');
        const techDetails = jobEntry.querySelector('.technical-details');
        const techAddBtn = jobEntry.querySelector('.add-technical-skill');
        const techInput = jobEntry.querySelector('.technical-skill-input');
        const techHiddenInput = jobEntry.querySelector('.technical-skills-input');
        const techTagsContainer = jobEntry.querySelector('.technical-tags-container');
        
        if (techCheckbox && techDetails) {
            setupCheckboxToggle(techCheckbox, techDetails);
            
            if (techAddBtn && techInput && techHiddenInput && techTagsContainer) {
                const techTagSystem = createSkillTagSystem(
                    techDetails,
                    techAddBtn,
                    techInput,
                    techHiddenInput,
                    techTagsContainer
                );
                // Store reset reference so resetFormDropdowns can clear the internal tags array
                techTagsContainer._tagSystem = techTagSystem;
            }
        }
        
        // Soft skills
        const softCheckbox = jobEntry.querySelector('.soft-checkbox');
        const softDetails = jobEntry.querySelector('.soft-details');
        const softAddBtn = jobEntry.querySelector('.add-soft-skill');
        const softInput = jobEntry.querySelector('.soft-skill-input');
        const softHiddenInput = jobEntry.querySelector('.soft-skills-input');
        const softTagsContainer = jobEntry.querySelector('.soft-tags-container');
        
        if (softCheckbox && softDetails) {
            setupCheckboxToggle(softCheckbox, softDetails);
            
            if (softAddBtn && softInput && softHiddenInput && softTagsContainer) {
                const softTagSystem = createSkillTagSystem(
                    softDetails,
                    softAddBtn,
                    softInput,
                    softHiddenInput,
                    softTagsContainer
                );
                // Store reset reference so resetFormDropdowns can clear the internal tags array
                softTagsContainer._tagSystem = softTagSystem;
            }
        }
    }
   document.addEventListener('click', function(e) {
    // Toggle salary range dropdown
    if (e.target.closest('.salary-range-btn')) {
        const btn = e.target.closest('.salary-range-btn');
        const menu = btn.nextElementSibling;
        const arrow = btn.querySelector('.salary-range-arrow');
        
        menu.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
        
        // Close other dropdowns
        document.querySelectorAll('.salary-range-menu').forEach(m => {
            if (m !== menu) m.classList.add('hidden');
        });
    }
    
    // Select salary range option
    
if (e.target.closest('.salary-range-option')) {
    const option = e.target.closest('.salary-range-option');
    const container = option.closest('.mb-4');
    const btn = container.querySelector('.salary-range-btn');
    const menu = container.querySelector('.salary-range-menu');
    const text = btn.querySelector('.salary-range-text');
    const arrow = btn.querySelector('.salary-range-arrow');
    const input = container.querySelector('.salary-range-input');
    const below30kContainer = container.querySelector('.below-30k-input-container');
    const below30kInput = container.querySelector('.below-30k-salary-input');

    const value = option.dataset.value;

    text.textContent = option.textContent.trim();
    text.classList.remove('text-gray-400');
    text.classList.add('text-gray-700');

    menu.classList.add('hidden');
    arrow.classList.remove('rotate-180');

    if (value === 'Below ₱30,000') {
    input.value = '__below_30k__'; // sentinel value
    below30kContainer.classList.remove('hidden');
    below30kInput.required = true;
} else {
    input.value = value;
    below30kContainer.classList.add('hidden');
    below30kInput.required = false;
    below30kInput.value = '';
}
}
    
    // Close dropdown when clicking outside
    if (!e.target.closest('.salary-range-btn') && !e.target.closest('.salary-range-menu')) {
        document.querySelectorAll('.salary-range-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
        document.querySelectorAll('.salary-range-arrow').forEach(arrow => {
            arrow.classList.remove('rotate-180');
        });
    }
});

// Limit Below 30k Salary Input to 5 digits with comma formatting
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('below-30k-salary-input')) {
        let value = e.target.value;
        
        // Remove any non-numeric characters (including existing commas)
        value = value.replace(/[^0-9]/g, '');
        
        // Limit to 5 characters (29999)
        if (value.length > 5) {
            value = value.substring(0, 5);
        }
        
        // Check if value exceeds 29999
        const numValue = parseInt(value);
        if (numValue >= 30000) {
            value = '30000';
        }
        
        // Add comma formatting (e.g., 25000 becomes 25,000)
       if (value) {
            value = parseInt(value).toLocaleString('en-US');
        }

        e.target.value = value;

        // 🔥 ADD THIS NEW CODE HERE:
        if (value) {
            const container = e.target.closest('.mb-4');
            const salaryRangeInput = container.querySelector('.salary-range-input');
            const numericValue = value.replace(/,/g, ''); // Remove comma (25,000 → 25000)
            
            if (salaryRangeInput) {
                salaryRangeInput.value = numericValue; // Replace __below_30k__ with 25000
            }
        }
    }
});

    // Initialize existing job entries
document.querySelectorAll('.job-entry').forEach(initializeJobEntry);

// Add job title functionality
const addJobTitleBtn = document.getElementById('add-job-title-btn');
const jobTitlesContainer = document.getElementById('jobTitlesContainer');

let jobEntryCounter = 1; // starts at 1 since the first static entry uses index 0

addJobTitleBtn.addEventListener('click', () => {
    const jobCount = jobTitlesContainer.querySelectorAll('.job-entry').length;
    const entryIndex = jobEntryCounter++; // always unique, even after removals
    
    const newJobEntry = document.createElement('div');
    newJobEntry.className = 'bg-white rounded-lg p-4 border border-gray-200 job-entry relative';
    
    newJobEntry.innerHTML = `
        <button type="button" 
                class="remove-job-btn absolute top-4 right-4 text-red-500 hover:text-red-700 font-medium text-sm flex items-center gap-1 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Remove
        </button>

        <div class="mb-4 pb-2 border-b border-gray-200">
            <h4 class="text-sm font-bold text-teal-700">Job Entry #${jobCount + 1}</h4>
        </div>

        <div class="mb-4">
            <label class="block text-gray-800 text-sm font-semibold mb-2">
                Job Title: <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                name="job_title[]"
                placeholder="e.g. Senior Java Developer"
                required
                class="job-title-input w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm"
            />
        </div>

        <div class="mb-4">
            <label class="block text-gray-800 text-sm font-semibold mb-2">
                Standard Job Classifications / Families: <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <button type="button" class="job-classification-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                    <span class="job-classification-text text-gray-400">Select job classification</span>
                    <svg class="job-classification-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div class="job-classification-menu dropdown-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                    <div data-value="Accounting, Finance & Banking" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Accounting, Finance & Banking
                    </div>
                    <div data-value="Administrative, HR & Office Support" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Administrative, HR & Office Support
                    </div>
                    <div data-value="Agriculture, Forestry & Agribusiness" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Agriculture, Forestry & Agribusiness
                    </div>
                    <div data-value="Construction, Engineering & Architecture" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Construction, Engineering & Architecture
                    </div>
                    <div data-value="Customer Service & BPO (Contact Center)" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Customer Service & BPO (Contact Center)
                    </div>
                    <div data-value="Education, Training & Academe" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Education, Training & Academe
                    </div>
                    <div data-value="Healthcare, Medical & Allied Services" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Healthcare, Medical & Allied Services
                    </div>
                    <div data-value="IT, Software, Data & Digital Creative" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • IT, Software, Data & Digital Creative
                    </div>
                    <div data-value="Legal, Compliance & Public Service" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Legal, Compliance & Public Service
                    </div>
                    <div data-value="Logistics, Transport & Supply Chain" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Logistics, Transport & Supply Chain
                    </div>
                    <div data-value="Manufacturing, Production & Technical" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Manufacturing, Production & Technical
                    </div>
                    <div data-value="Sales, Marketing, Retail & E-Commerce" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Sales, Marketing, Retail & E-Commerce
                    </div>
                    <div data-value="Science, Research & Laboratory" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Science, Research & Laboratory
                    </div>
                    <div data-value="Skilled Trades, Maintenance & General Services" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Skilled Trades, Maintenance & General Services
                    </div>
                    <div data-value="Tourism, Hospitality & Food Service" 
                        class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        • Tourism, Hospitality & Food Service
                    </div>
                </div>
                
                <input type="hidden" class="job-classification-input" name="job_classification[]" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-800 text-sm font-semibold mb-2">Salary Range: <span class="text-red-500">*</span></label>
            <div class="relative">
                <button type="button" class="salary-range-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                    <span class="salary-range-text text-gray-400">Select salary range</span>
                    <svg class="salary-range-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="salary-range-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden hidden">
                    <div data-value="₱30,000 - ₱59,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱30,000 - ₱59,999</div>
                    <div data-value="₱60,000 - ₱89,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱60,000 - ₱89,999</div>
                    <div data-value="₱90,000 - ₱149,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱90,000 - ₱149,999</div>
                    <div data-value="₱150,000 - ₱499,999" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱150,000 - ₱499,999</div>
                    <div data-value="₱500,000 and above" class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">₱500,000 and above</div>
                    <div data-value="Below ₱30,000" class="salary-range-option below-30k-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">Below ₱30,000 (please specify)</div>
                </div>
                <input type="hidden" class="salary-range-input" name="salary_range[]" >
            </div>
            
            <div class="below-30k-input-container mt-3 hidden">
                <label class="block text-gray-600 text-xs font-medium mb-2">Please specify the exact salary amount:</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">₱</span>
                    <input type="text" 
                        class="below-30k-salary-input w-full pl-8 pr-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm" 
                        placeholder="e.g. 25,000"
                        inputmode="numeric">
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-800 text-sm font-semibold mb-2">
                Duration that the Vacancy is Open: <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <button type="button" class="duration-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                    <span class="duration-text text-gray-400">Select duration</span>
                    <svg class="duration-arrow w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div class="duration-menu dropdown-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden hidden">
                    <div data-value="Less than 30 Days" 
                        class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        Less than 30 Days
                    </div>
                    <div data-value="30-60 Days" 
                        class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        30-60 Days
                    </div>
                    <div data-value="60-90 Days" 
                        class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        60-90 Days
                    </div>
                    <div data-value="90+ Days" 
                        class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                        90+ Days
                    </div>
                </div>
                
                <input type="hidden" class="duration-input" name="vacancy_duration[]" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-800 text-sm font-semibold mb-2">
                 Reasons For Difficulty (Role-Level) <span class="italic text-gray-500">(Check all that apply)</span>
            </label>
            <div class="difficulty-reasons space-y-3">
                
                <div class="technical-skills-label p-3 border rounded-lg transition-all border-gray-200">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" 
                            name="difficulty_reasons_${entryIndex}[]" 
                            value="Technical / Hard Skills Missing"
                            class="technical-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                        <div class="ml-3">
                            <div class="font-semibold text-gray-800">Technical / Hard Skills Missing</div>
                            <div class="text-xs text-gray-500 mt-1">Applicants do not have the required tools, software, or technical knowledge</div>
                        </div>
                    </label>
                    <div class="technical-details mt-3 hidden">
                        <label class="block text-gray-600 text-xs font-medium mb-1">
                            What specific technical tools, software, or machinery knowledge is missing?
                        </label>
                        
                        <div class="technical-tags-container flex flex-wrap gap-2 mb-2"></div>
                        
                        <div class="flex gap-2 skill-input-row">
                            <input type="text" 
                                class="technical-skill-input flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                placeholder="Type a skill and press Enter..."
                                enterkeyhint="done" inputmode="text"/>
                            <button type="button" 
                                    class="add-technical-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Enter</button>
                        </div>
                        <input type="hidden" class="technical-skills-input" name="technical_skills_missing[]">
                    </div>
                </div>

                <div class="soft-skills-label p-3 border rounded-lg transition-all border-gray-200">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" 
                            name="difficulty_reasons_${entryIndex}[]" 
                            value="Soft / Employability Skills Missing"
                            class="soft-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                        <div class="ml-3">
                            <div class="font-semibold text-gray-800">Soft / Employability Skills Missing</div>
                            <div class="text-xs text-gray-500 mt-1">Applicants cannot communicate effectively, work in teams, or demonstrate professionalism</div>
                        </div>
                    </label>
                    <div class="soft-details mt-3 hidden">
                        <label class="block text-gray-600 text-xs font-medium mb-1">
                            What attitude or behavioral traits cause you to reject applicants?
                        </label>
                        
                        <div class="soft-tags-container flex flex-wrap gap-2 mb-2"></div>
                        
                        <div class="flex gap-2 skill-input-row">
                            <input type="text" 
                                class="soft-skill-input flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                placeholder="Type a trait and press Enter..."
                                enterkeyhint="done" inputmode="text"/>
                            <button type="button" 
                                    class="add-soft-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Enter</button>
                        </div>
                        <input type="hidden" class="soft-skills-input" name="soft_skills_missing[]">
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4 mt-6 pt-4 border-t border-gray-200">
            <label class="block text-gray-800 text-sm font-semibold mb-3">
                 How much does the difficulty finding qualified applicants for this role impact your business operations? 
                <span class="text-red-500">*</span>
            </label>
            <div class="impact-level space-y-3">
                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                    <input type="radio" name="impact_level_${entryIndex}" value="High" required
                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                    <div class="ml-3 flex-1">
                        <div class="font-semibold text-gray-900">High Impact</div>
                        <div class="text-xs text-gray-500 mt-1">Operations are significantly disrupted</div>
                    </div>
                </label>
                
                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                    <input type="radio" name="impact_level_${entryIndex}" value="Medium" required
                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                    <div class="ml-3 flex-1">
                        <div class="font-semibold text-gray-900">Medium Impact</div>
                        <div class="text-xs text-gray-500 mt-1">Operations continue with adjustments</div>
                    </div>
                </label>
                
                <label class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                    <input type="radio" name="impact_level_${entryIndex}" value="Low" required
                        class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                    <div class="ml-3 flex-1">
                        <div class="font-semibold text-gray-900">Low Impact</div>
                        <div class="text-xs text-gray-500 mt-1">Minimal operational impact</div>
                    </div>
                </label>
            </div>
        </div>
    `;
    
    jobTitlesContainer.appendChild(newJobEntry);
    initializeJobEntry(newJobEntry);
    
    // Add remove functionality
    const removeBtn = newJobEntry.querySelector('.remove-job-btn');
    removeBtn.addEventListener('click', () => {
        newJobEntry.remove();
    });
    
    // Scroll to new entry
    newJobEntry.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

    // Other rejection reasons toggle
    const otherRejectionCheckbox = document.querySelector('.other-rejection-checkbox');
    const otherRejectionInput = document.querySelector('.other-rejection-input');
    if (otherRejectionCheckbox && otherRejectionInput) {
        otherRejectionCheckbox.addEventListener('change', () => {
            if (otherRejectionCheckbox.checked) {
                otherRejectionInput.classList.remove('hidden');
            } else {
                otherRejectionInput.classList.add('hidden');
            }
        });
    }

    // Other coordination frequency toggle
    const otherCoordinationRadio = document.querySelector('.other-coordination-radio');
    const otherCoordinationInput = document.querySelector('.other-coordination-input');
    if (otherCoordinationRadio && otherCoordinationInput) {
        otherCoordinationRadio.addEventListener('change', () => {
            if (otherCoordinationRadio.checked) {
                otherCoordinationInput.classList.remove('hidden');
            } else {
                otherCoordinationInput.classList.add('hidden');
            }
        });
    }

    // LMI Features: max 2 selections + Other text toggle
    const lmiCheckboxes = document.querySelectorAll('.lmi-feature-checkbox');
    const lmiOtherCheckbox = document.querySelector('.lmi-other-checkbox');
    const lmiOtherInput = document.querySelector('.lmi-other-input');

    lmiCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const checked = document.querySelectorAll('.lmi-feature-checkbox:checked');

            // Enforce max 2
            if (checked.length > 2) {
                this.checked = false;
                return;
            }

            // Disable unchecked when 2 selected, re-enable when below 2
            if (checked.length === 2) {
                lmiCheckboxes.forEach(cb => {
                    if (!cb.checked) {
                        cb.disabled = true;
                        const wrapper = cb.closest('label') || cb.closest('.lmi-other-option');
                        if (wrapper) { wrapper.style.opacity = '0.4'; wrapper.style.cursor = 'not-allowed'; }
                    }
                });
            } else {
                lmiCheckboxes.forEach(cb => {
                    cb.disabled = false;
                    const wrapper = cb.closest('label') || cb.closest('.lmi-other-option');
                    if (wrapper) { wrapper.style.opacity = ''; wrapper.style.cursor = ''; }
                });
            }

            // Toggle "Other" text input
            if (lmiOtherCheckbox && lmiOtherInput) {
                lmiOtherCheckbox.checked
                    ? lmiOtherInput.classList.remove('hidden')
                    : lmiOtherInput.classList.add('hidden');
            }
        });
    });

    // Sector tabs functionality
    document.querySelectorAll('.sector-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active state from all tabs
            document.querySelectorAll('.sector-tab').forEach(t => {
                t.classList.remove('bg-purple-600', 'text-white');
                t.classList.add('border', 'text-gray-500', 'hover:bg-gray-50');
            });
            
            // Add active state to clicked tab
            tab.classList.add('bg-purple-600', 'text-white');
            tab.classList.remove('border', 'text-gray-500', 'hover:bg-gray-50');
            
            // Here you would typically filter the skill gaps based on the selected sector
            // For now, we'll just log the selection

        });
    });

    // Export analysis button
    const exportBtn = document.querySelector('.export-analysis-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            alert('Export functionality would be implemented here.');
        });
    }

}); // end DOMContentLoaded
    </script>
    <script>function toggleRoleDetails(submissionId, roleIndex) {
    const detailsDiv = document.getElementById('role-details-' + submissionId + '-' + roleIndex);
    
    if (!detailsDiv) {
        return;
    }
    
    const card = detailsDiv.closest('.role-card');
    const icon = card.querySelector('.expand-icon');
    
    if (detailsDiv.classList.contains('hidden')) {
        // Close all other details
        document.querySelectorAll('.role-details').forEach(div => {
            div.classList.add('hidden');
            const parentCard = div.closest('.role-card');
            if (parentCard) {
                const parentIcon = parentCard.querySelector('.expand-icon');
                if (parentIcon) {
                    parentIcon.classList.remove('rotate-180');
                }
            }
        });
        
        // Open this one
        detailsDiv.classList.remove('hidden');
        if (icon) {
            icon.classList.add('rotate-180');
        }
    } else {
        // Close this one
        detailsDiv.classList.add('hidden');
        if (icon) {
            icon.classList.remove('rotate-180');
        }
    }
}</script>
    
<script>
function filterSkills(sector) {
    const scrollContainer = document.getElementById('sector-filter-scroll');

    // Update active tab styling + scroll active tab into view
    document.querySelectorAll('.sector-tab').forEach(tab => {
        if (tab.getAttribute('data-sector') === sector) {
            tab.classList.add('bg-gray-900', 'text-white', 'shadow-sm');
            tab.classList.remove('border', 'border-gray-200', 'text-gray-500', 'bg-white', 'hover:border-gray-900', 'hover:text-gray-900');

            // Scroll the active tab into view within the scroll container
            if (scrollContainer) {
                const contRect = scrollContainer.getBoundingClientRect();
                const tabRect  = tab.getBoundingClientRect();
                // Current scroll + tab center - container center
                const targetScroll = scrollContainer.scrollLeft
                    + (tabRect.left - contRect.left)
                    - (contRect.width / 2)
                    + (tabRect.width / 2);
                scrollContainer.scrollTo({ left: targetScroll, behavior: 'smooth' });
            }
        } else {
            tab.classList.remove('bg-gray-900', 'text-white', 'shadow-sm');
            tab.classList.add('border', 'border-gray-200', 'text-gray-500', 'bg-white', 'hover:border-gray-900', 'hover:text-gray-900');
        }
    });

    // Filter skill tags
    document.querySelectorAll('.skill-tag').forEach(tag => {
        const tagSector = tag.getAttribute('data-sector');
        tag.style.display = (sector === 'All' || tagSector === sector) ? 'flex' : 'none';
    });

    // Re-check scroll hint visibility after filtering
    setTimeout(() => {
        if (typeof window._techScrollUpdate === 'function') window._techScrollUpdate();
        if (typeof window._softScrollUpdate === 'function') window._softScrollUpdate();
    }, 50);
}

// Arrow buttons + mouse wheel scroll for filter bar
document.addEventListener('DOMContentLoaded', function () {
    const scroll = document.getElementById('sector-filter-scroll');
    const left   = document.getElementById('filter-left');
    const right  = document.getElementById('filter-right');

    if (scroll && left && right) {
        left.addEventListener('click',  () => scroll.scrollBy({ left: -200, behavior: 'smooth' }));
        right.addEventListener('click', () => scroll.scrollBy({ left:  200, behavior: 'smooth' }));

        // Mouse wheel scrolls horizontally
        scroll.addEventListener('wheel', function (e) {
            if (e.deltaY !== 0) {
                e.preventDefault();
                scroll.scrollBy({ left: e.deltaY * 2, behavior: 'smooth' });
            }
        }, { passive: false });
    }
});

// Skills cloud scroll indicators
document.addEventListener('DOMContentLoaded', function () {
    function initScrollHint(containerId, wrapperId, hintId) {
        const container = document.getElementById(containerId);
        const wrapper   = document.getElementById(wrapperId);
        const hint      = document.getElementById(hintId);
        if (!container || !wrapper || !hint) return;

        function update() {
            const scrollable = container.scrollHeight > container.clientHeight + 4;
            hint.style.display = scrollable ? 'flex' : 'none';
            if (!scrollable) { wrapper.classList.add('at-bottom'); return; }
            const atBottom = container.scrollTop + container.clientHeight >= container.scrollHeight - 8;
            wrapper.classList.toggle('at-bottom', atBottom);
            hint.style.opacity = atBottom ? '0' : '1';
        }

        container.addEventListener('scroll', update);
        // Re-check after images/fonts settle
        setTimeout(update, 300);
        update();

        // Expose so external code (e.g. filterSkills) can trigger a re-check
        return update;
    }

    window._techScrollUpdate = initScrollHint('tech-skills-container', 'tech-skills-scroll-wrapper', 'tech-scroll-hint');
    window._softScrollUpdate = initScrollHint('soft-skills-container', 'soft-skills-scroll-wrapper', 'soft-scroll-hint');
});
</script>
<script>
    // Comprehensive Autocomplete System for Job Titles and Skills
// Add this script to your blade file or separate JS file

// Store autocomplete data
let autocompleteData = {
    jobTitles: [],
    technicalSkills: [],
    softSkills: []
};

// Fetch all autocomplete data when page loads
async function fetchAutocompleteData() {
    try {
        const response = await fetch('/api/autocomplete-data');
        const data = await response.json();
        
        if (data.success) {
            autocompleteData.jobTitles = data.job_titles || [];
            autocompleteData.technicalSkills = data.technical_skills || [];
            autocompleteData.softSkills = data.soft_skills || [];
        }
    } catch (error) {
        console.error('❌ Failed to fetch autocomplete data:', error);
        // Fallback to empty arrays
        autocompleteData.jobTitles = [];
        autocompleteData.technicalSkills = [];
        autocompleteData.softSkills = [];
    }
}

// Generic autocomplete function
function createAutocomplete(inputElement, dataSource, onSelect) {
    if (inputElement.hasAttribute('data-autocomplete-initialized')) return;
    inputElement.setAttribute('data-autocomplete-initialized', 'true');
    
    // Create suggestion dropdown
    const suggestionsDiv = document.createElement('div');
    suggestionsDiv.className = 'autocomplete-suggestions absolute w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-60 overflow-y-auto hidden';
    suggestionsDiv.style.zIndex = '9999';
    suggestionsDiv.style.position = 'absolute';
    suggestionsDiv.style.top = '100%';
    suggestionsDiv.style.left = '0';
    suggestionsDiv.style.right = '0';
    // Make parent relative if not already
    if (getComputedStyle(inputElement.parentElement).position === 'static') {
        inputElement.parentElement.style.position = 'relative';
    }
    inputElement.parentElement.appendChild(suggestionsDiv);
    
    // Listen for input
    inputElement.addEventListener('input', function(e) {
        const searchTerm = e.target.value.trim().toLowerCase();
        
        if (searchTerm.length < 2) {
            suggestionsDiv.innerHTML = '';
            suggestionsDiv.classList.add('hidden');
            return;
        }
        
        // Filter matching items
        const matches = dataSource.filter(item => 
            item.toLowerCase().includes(searchTerm)
        );
        
        if (matches.length === 0) {
            // Bug fix: hide the dropdown silently when there are no matches
            // instead of showing an annoying "No result found" message.
            suggestionsDiv.innerHTML = '';
            suggestionsDiv.classList.add('hidden');
            return;
        }
        
        // Display suggestions (limit to 10)
        suggestionsDiv.innerHTML = '';
        matches.slice(0, 10).forEach(item => {
            const suggestionItem = document.createElement('div');
            suggestionItem.className = 'px-4 py-2.5 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 border-b border-gray-100 last:border-b-0 transition';
            
            // Highlight matching text
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            const highlightedItem = item.replace(regex, '<span class="font-semibold text-teal-600">$1</span>');
            suggestionItem.innerHTML = highlightedItem;
            
            // Click to select
            suggestionItem.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent click from bubbling to elements below
                
                if (onSelect) {
                    onSelect(item, inputElement);
                } else {
                    inputElement.value = item;
                }
                suggestionsDiv.innerHTML = '';
                suggestionsDiv.classList.add('hidden');
            });
            
            suggestionsDiv.appendChild(suggestionItem);
        });
        
        suggestionsDiv.classList.remove('hidden');
    });
    
    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!inputElement.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.classList.add('hidden');
        }
    });
    
    // Keyboard navigation
    inputElement.addEventListener('keydown', function(e) {
        const suggestions = suggestionsDiv.querySelectorAll('div.px-4');
        if (suggestions.length === 0) return;
        
        let currentIndex = Array.from(suggestions).findIndex(s => s.classList.contains('bg-teal-100'));
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            suggestions[currentIndex]?.classList.remove('bg-teal-100', 'bg-teal-50');
            currentIndex = currentIndex < suggestions.length - 1 ? currentIndex + 1 : 0;
            suggestions[currentIndex].classList.add('bg-teal-100');
            suggestions[currentIndex].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            suggestions[currentIndex]?.classList.remove('bg-teal-100', 'bg-teal-50');
            currentIndex = currentIndex > 0 ? currentIndex - 1 : suggestions.length - 1;
            suggestions[currentIndex].classList.add('bg-teal-100');
            suggestions[currentIndex].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter' && currentIndex >= 0) {
            e.preventDefault();
            suggestions[currentIndex].click();
        } else if (e.key === 'Escape') {
            suggestionsDiv.classList.add('hidden');
        }
    });
}

// Initialize Job Title Autocomplete
function initializeJobTitleAutocomplete() {
    document.querySelectorAll('.job-title-input').forEach(input => {
        createAutocomplete(input, autocompleteData.jobTitles);
    });
}

// Initialize Technical Skills Autocomplete
function initializeTechnicalSkillsAutocomplete() {
    document.querySelectorAll('.technical-skill-input').forEach(input => {
        createAutocomplete(input, autocompleteData.technicalSkills, function(selectedSkill, inputElement) {
            // When a skill is selected, add it as a tag
            inputElement.value = selectedSkill;
            
            // Trigger the add button click or Enter key
            const addButton = inputElement.parentElement.querySelector('.add-technical-skill');
            if (addButton) {
                addButton.click();
            }
            
            // Clear input after selection
            setTimeout(() => {
                inputElement.value = '';
                inputElement.focus();
            }, 100);
        });
    });
}

// Initialize Soft Skills Autocomplete
function initializeSoftSkillsAutocomplete() {
    document.querySelectorAll('.soft-skill-input').forEach(input => {
        createAutocomplete(input, autocompleteData.softSkills, function(selectedSkill, inputElement) {
            // When a skill is selected, add it as a tag
            inputElement.value = selectedSkill;
            
            // Trigger the add button click
            const addButton = inputElement.parentElement.querySelector('.add-soft-skill');
            if (addButton) {
                addButton.click();
            }
            
            // Clear input after selection
            setTimeout(() => {
                inputElement.value = '';
                inputElement.focus();
            }, 100);
        });
    });
}

// Initialize all autocompletes
function initializeAllAutocompletes() {
    initializeJobTitleAutocomplete();
    initializeTechnicalSkillsAutocomplete();
    initializeSoftSkillsAutocomplete();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    fetchAutocompleteData().then(() => {
        initializeAllAutocompletes();
    });
    
    // Re-initialize when new fields are added (for dynamic job entries)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                setTimeout(() => {
                    initializeAllAutocompletes();
                }, 100);
            }
        });
    });
    
    const container = document.getElementById('jobTitlesContainer');
    if (container) {
        observer.observe(container, { childList: true, subtree: true });
    }
});

// ===== CSV EXPORT FUNCTIONALITY =====
document.addEventListener('DOMContentLoaded', function() {
    const exportBtn = document.getElementById('exportAnalysisBtn');
    
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            exportDashboardToCSV();
        });
    }
});

function exportDashboardToCSV() {
    const timestamp = new Date().toLocaleString();
    const dateStr   = new Date().toISOString().split('T')[0];

    // ── Collect all section data ─────────────────────────────────────────────

    // Section 1: High-Volume Job Titles
    const jobRows = [];
    if (window.jobsChart && window.jobsChart.data) {
        window.jobsChart.data.labels.forEach((label, i) => {
            jobRows.push([i + 1, label, window.jobsChart.data.datasets[0].data[i]]);
        });
    }

    // Section 2: Hard-to-Fill Roles
    const roleRows = [];
    document.querySelectorAll('.role-card').forEach(card => {
        const title      = card.querySelector('.font-bold')?.textContent?.trim() || '';
        const duration   = card.querySelector('.text-xs.text-gray-400')?.textContent?.trim() || '';
        const details    = card.querySelector('.role-details');
        let classification = '', reasons = '', techSkills = '', softSkills = '';
        if (details) {
            const classEls = details.querySelectorAll('div > p.text-slate-800');
            if (classEls.length) classification = classEls[0].textContent.trim();
            const ul = details.querySelector('ul.list-disc');
            if (ul) reasons = Array.from(ul.querySelectorAll('li')).map(li => li.textContent.trim()).join('; ');
            const techSpan = Array.from(details.querySelectorAll('span.font-medium.text-slate-600')).find(s => s.textContent.includes('Technical Skills'));
            if (techSpan) techSkills = Array.from(techSpan.parentElement.querySelectorAll('span.bg-blue-100')).map(t => t.textContent.trim()).join('; ');
            const softSpan = Array.from(details.querySelectorAll('span.font-medium.text-slate-600')).find(s => s.textContent.includes('Soft Skills'));
            if (softSpan) softSkills = Array.from(softSpan.parentElement.querySelectorAll('span.bg-purple-100')).map(t => t.textContent.trim()).join('; ');
        }
        roleRows.push([title, classification, duration, reasons, techSkills, softSkills]);
    });

    // Section 3: Critical Skill Gaps
    const skillRows = [];
    if (window.skillGapsChart && window.skillGapsChart.data) {
        window.skillGapsChart.data.labels.forEach((label, i) => {
            skillRows.push([i + 1, label, window.skillGapsChart.data.datasets[0].data[i]]);
        });
    }

    // Section 4: Employment Trends
    const trendRows = [];
    if (window.trendsChart && window.trendsChart.data) {
        window.trendsChart.data.labels.forEach((label, i) => {
            trendRows.push([label, window.trendsChart.data.datasets[0].data[i]]);
        });
    }

    // ── Build worksheet rows ─────────────────────────────────────────────────
    const sheetRows = [
        // Title
        ['Davao Employment Dashboard Analysis'],
        ['Generated on', timestamp],
        [],
        // Section 1
        ['HIGH-VOLUME JOB TITLES'],
        ['Rank', 'Job Title', 'Count'],
        ...jobRows,
        [],
        // Section 2
        ['HARD-TO-FILL ROLES'],
        ['Job Title', 'Classification', 'Vacancy Duration', 'Difficulty Reasons', 'Technical Skills', 'Soft Skills'],
        ...roleRows,
        [],
        // Section 3
        ['CRITICAL SKILL GAPS'],
        ['Rank', 'Skill', 'Frequency'],
        ...skillRows,
        [],
        // Section 4
        ['EMPLOYMENT TRENDS (Last 6 Months)'],
        ['Month', 'Job Postings'],
        ...trendRows,
    ];

    const ws = XLSX.utils.aoa_to_sheet(sheetRows);

    // ── Styles ───────────────────────────────────────────────────────────────
    const border     = { style: 'thin', color: { rgb: 'C5D0DE' } };
    const cellBorder = { top: border, bottom: border, left: border, right: border };
    const cenAlign   = { horizontal: 'center', vertical: 'center' };
    const leftAlign  = { horizontal: 'left',   vertical: 'center' };
    const numAlign   = { horizontal: 'right',  vertical: 'center' };

    // Section header rows (detect by known labels)
    const sectionTitles  = new Set(['HIGH-VOLUME JOB TITLES', 'HARD-TO-FILL ROLES', 'CRITICAL SKILL GAPS', 'EMPLOYMENT TRENDS (Last 6 Months)']);
    const columnHeaders  = new Set(['Rank', 'Job Title', 'Count', 'Classification', 'Vacancy Duration', 'Difficulty Reasons', 'Technical Skills', 'Soft Skills', 'Skill', 'Frequency', 'Month', 'Job Postings']);

    // Determine max columns for merges
    const maxCols = sheetRows.reduce((m, r) => Math.max(m, r.length), 0);

    // Merge title row + section title rows across all columns
    ws['!merges'] = [];

    // Auto column widths
    const colWidths = Array(maxCols).fill(10);
    sheetRows.forEach(row => {
        row.forEach((cell, ci) => {
            const len = String(cell ?? '').length;
            if (len + 4 > colWidths[ci]) colWidths[ci] = len + 4;
        });
    });
    ws['!cols'] = colWidths.map(w => ({ wch: w }));

    // Apply cell styles row by row
    sheetRows.forEach((row, r) => {
        const firstCell = String(row[0] ?? '');
        const isTitle        = r === 0;
        const isGenerated    = r === 1;
        const isSectionTitle = sectionTitles.has(firstCell);
        const isColHeader    = row.length > 0 && columnHeaders.has(firstCell);

        if (isSectionTitle || isTitle) {
            ws['!merges'].push({ s: { r, c: 0 }, e: { r, c: maxCols - 1 } });
        }

        row.forEach((_, c) => {
            const addr = XLSX.utils.encode_cell({ r, c });
            if (!ws[addr]) return;

            if (isTitle) {
                ws[addr].s = {
                    fill:      { patternType: 'solid', fgColor: { rgb: '0D2137' } },
                    font:      { bold: true, color: { rgb: 'FFFFFF' }, sz: 14, name: 'Calibri' },
                    alignment: cenAlign,
                };
            } else if (isGenerated) {
                ws[addr].s = {
                    font:      { italic: true, color: { rgb: '64748B' }, sz: 10, name: 'Calibri' },
                    alignment: leftAlign,
                };
            } else if (isSectionTitle) {
                ws[addr].s = {
                    fill:      { patternType: 'solid', fgColor: { rgb: '1E3A5F' } },
                    font:      { bold: true, color: { rgb: 'FFFFFF' }, sz: 12, name: 'Calibri' },
                    alignment: leftAlign,
                };
            } else if (isColHeader) {
                ws[addr].s = {
                    fill:      { patternType: 'solid', fgColor: { rgb: '334155' } },
                    font:      { bold: true, color: { rgb: 'FFFFFF' }, sz: 10, name: 'Calibri' },
                    alignment: cenAlign,
                    border:    cellBorder,
                };
            } else if (row.length > 0) {
                // Data row — alternate shading
                const isNum = typeof row[c] === 'number';
                ws[addr].s = {
                    fill:      { patternType: 'solid', fgColor: { rgb: r % 2 === 0 ? 'F1F5F9' : 'FFFFFF' } },
                    font:      { sz: 10, name: 'Calibri' },
                    alignment: isNum ? numAlign : leftAlign,
                    border:    cellBorder,
                };
            }
        });
    });

    // ── Export ───────────────────────────────────────────────────────────────
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Dashboard Analysis');
    XLSX.writeFile(wb, `davao-employment-analysis-${dateStr}.xlsx`, { bookType: 'xlsx', cellStyles: true });

    console.log('Export complete: davao-employment-analysis-' + dateStr + '.xlsx');
}

    </script>
    <script>
        // Helper function to format salary range with peso sign and thousand separators
        function formatSalaryRange(salaryRange) {
            if (!salaryRange || salaryRange === 'Not specified') {
                return salaryRange;
            }
            
            // Convert to string if it's a number
            let salaryStr = String(salaryRange);
            
            // If it contains a range (e.g., "30000 - 59999" or "30000-59999")
            if (salaryStr.includes('-')) {
                // Split by dash, allowing spaces around it
                let parts = salaryStr.split(/\s*-\s*/);
                
                if (parts.length === 2) {
                    // Format each part
                    let min = parts[0].trim().replace(/[₱,]/g, ''); // Remove existing ₱ and commas
                    let max = parts[1].trim().replace(/[₱,]/g, '');
                    
                    // Check if they're valid numbers
                    if (!isNaN(min) && !isNaN(max)) {
                        min = Number(min).toLocaleString();
                        max = Number(max).toLocaleString();
                        return '₱' + min + ' - ₱' + max;
                    }
                }
            }
            
            // If it's a single number or already formatted
            let cleaned = salaryStr.replace(/[₱,]/g, ''); // Remove existing ₱ and commas
            
            // Check if it's a valid number
            if (!isNaN(cleaned) && cleaned.trim() !== '') {
                let formatted = Number(cleaned).toLocaleString();
                return '₱' + formatted;
            }
            
            // If already has peso sign or is text (like "Below ₱30,000"), return as is
            if (salaryStr.includes('₱')) {
                return salaryStr;
            }
            
            // Default: just add peso sign
            return '₱' + salaryStr;
        }
        
        // Process matrix results to add peso sign to salary ranges
        let matrixResultsRaw = @json($matrix_results);
        window.matrixResultsData = matrixResultsRaw.map(result => ({
            ...result,
            salary_range: formatSalaryRange(result.salary_range)
        }));
        // ↓ INSERTED: keep a pristine copy so the Clear filter can restore original data
        window.matrixResultsDataOriginal = window.matrixResultsData.slice();
        // ↑ END INSERTED

function exportLMIMatrixToCSV() {
    const timestamp = new Date().toLocaleString();
    const dateStr   = new Date().toISOString().split('T')[0];

    // ── Helpers ───────────────────────────────────────────────────────────────
    const mkCell = (v, s) => ({ t: 's', v: String(v ?? ''), s });
    const border      = { style: 'thin', color: { rgb: 'C5D0DE' } };
    const bAll        = { top: border, bottom: border, left: border, right: border };
    const FONT        = (opts = {}) => ({ sz: opts.sz || 10, name: 'Calibri', bold: !!opts.bold, color: { rgb: opts.color || '1A1A1A' }, italic: !!opts.italic });
    const FILL        = rgb  => ({ patternType: 'solid', fgColor: { rgb } });
    const AL          = (h, v, wrap) => ({ horizontal: h, vertical: v, wrapText: !!wrap });
    const impactFill  = { 'High': 'FEE2E2', 'Medium': 'FFF9C4', 'Low': 'DCFCE7' };

    // ── Get data to export (all filtered data if a filter is active, else current page) ──
    const impactOrder = { 'High': 1, 'Medium': 2, 'Low': 3 };
    const sorted = (window.matrixResultsData || []).slice().sort((a, b) =>
        (impactOrder[a.impact] || 2) - (impactOrder[b.impact] || 2)
    );
    const itemsPerPage = 10;
    const totalPages   = Math.ceil(sorted.length / itemsPerPage);

    // If a period filter is active (badge is visible), export ALL filtered results
    const filterBadge   = document.getElementById('matrixFilterBadge');
    const filterIsActive = filterBadge && !filterBadge.classList.contains('hidden') && filterBadge.textContent.trim() !== '';

    let exportData, currentPage, exportLabel;
    if (filterIsActive) {
        // Export every record matching the active filter
        exportData  = sorted;
        currentPage = null; // N/A — exporting all
        exportLabel = `All ${sorted.length} filtered roles`;
    } else {
        // No filter active — export only the current visible page (original behaviour)
        const paginationSpans = document.querySelectorAll('.pagination-controls span.font-bold');
        const startItem = parseInt(paginationSpans[0]?.textContent) || 1;
        currentPage     = Math.ceil(startItem / itemsPerPage);
        exportData      = sorted.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);
        exportLabel     = `Page ${currentPage} of ${totalPages}`;
    }

    const pageData = exportData; // alias kept so the rest of the function is unchanged

    // ── Parse each row into skill arrays + plain text ─────────────────────────
    const parseSkills = arr =>
        (arr || []).map(s => typeof s === 'object' ? (s.name || '') : String(s || '')).filter(Boolean);

    // FIXED: added salary_range to row mapping (was missing before)
    const rows = pageData.map(result => ({
        role:   result.role          || '',
        sector: result.sector        || '',
        salary: result.salary_range  || '',
        tech:   parseSkills(result.hard_skills),   // array of strings
        soft:   parseSkills(result.soft_skills),   // array of strings
        impact: result.impact        || 'Medium',
    }));

    // ── Column order: Job Title | Sector | Salary Range | Tech Skills | Soft Skills | Gap Impact ──
    // (Gap Impact moved to last column per design requirement)
    const NC         = 6;
    const colHeaders = [
        'Job Title / Role',
        'Sector',
        'Salary Range',
        'Missing Technical Skills',
        'Missing Soft Skills',
        'Gap Impact',
    ];

    // ── Auto-fit column widths — based on the longest actual value in each column ──
    // Skill columns: measure the longest individual skill string (each will be on its own line)
    const safeMax = (...vals) => {
        const nums = vals.filter(v => typeof v === 'number' && isFinite(v));
        return nums.length ? Math.max(...nums) : 0;
    };
    const colW = [
        Math.min(safeMax(colHeaders[0].length, ...rows.map(r => r.role.length))                          + 4, 50),
        Math.min(safeMax(colHeaders[1].length, ...rows.map(r => r.sector.length))                        + 4, 45),
        Math.min(safeMax(colHeaders[2].length, ...rows.map(r => r.salary.length))                        + 4, 30),
        Math.min(safeMax(colHeaders[3].length, ...rows.flatMap(r => r.tech.map(s => s.length)))          + 4, 55),
        Math.min(safeMax(colHeaders[4].length, ...rows.flatMap(r => r.soft.map(s => s.length)))          + 4, 55),
        Math.min(safeMax(colHeaders[5].length, ...rows.map(r => r.impact.length))                        + 4, 18),
    ];

    // ── Build worksheet manually — cell by cell ───────────────────────────────
    // SheetJS CE: aoa_to_sheet silently strips \n — must assign t:'s' cells directly.
    const ws = {};
    const C  = (r, c) => XLSX.utils.encode_cell({ r, c });

    // Row 0 — Title
    ws[C(0,0)] = mkCell('LMI Granularity Matrix - Competency Gap Analysis', {
        fill: FILL('064E3B'), font: FONT({ bold: true, color: 'FFFFFF', sz: 13 }),
        alignment: AL('center','center')
    });
    for (let c = 1; c < NC; c++) ws[C(0,c)] = mkCell('', { fill: FILL('064E3B'), alignment: AL('center','center') });

    // Row 1 — Generated on
    ws[C(1,0)] = mkCell(`Generated on: ${timestamp}`, {
        font: FONT({ italic: true, color: '64748B' }), alignment: AL('left','center')
    });
    for (let c = 1; c < NC; c++) ws[C(1,c)] = mkCell('', { font: FONT({ color: '64748B' }) });

    // Row 2 — Page / filter info
    const infoLine = filterIsActive
        ? `Filtered export: ${exportLabel}  (${pageData.length} of ${sorted.length} roles in dataset)`
        : `Showing page ${currentPage} of ${totalPages}  (${pageData.length} of ${sorted.length} total roles)`;
    ws[C(2,0)] = mkCell(infoLine, {
        font: FONT({ italic: true, color: '64748B' }), alignment: AL('left','center')
    });
    for (let c = 1; c < NC; c++) ws[C(2,c)] = mkCell('', { font: FONT({ color: '64748B' }) });

    // Row 3 — Spacer (empty)
    for (let c = 0; c < NC; c++) ws[C(3,c)] = mkCell('', {});

    // Row 4 — Column headers
    colHeaders.forEach((h, c) => {
        ws[C(4,c)] = mkCell(h, {
            fill: FILL('065F46'), font: FONT({ bold: true, color: 'FFFFFF' }),
            alignment: AL('center','center', true), border: bAll
        });
    });

    // Rows 5+ — Data rows
    const rowHeights = [
        { hpt: 30 }, // title
        { hpt: 18 }, // generated on
        { hpt: 18 }, // page info
        { hpt:  5 }, // spacer
        { hpt: 22 }, // col headers
    ];

    rows.forEach((row, di) => {
        const r    = 5 + di;
        const bg   = di % 2 === 0 ? 'F8FAFC' : 'FFFFFF';
        const iBg  = impactFill[row.impact] || 'FFF9C4';

        // Each skill on its own line.
        // IMPORTANT: Excel requires \r\n (not just \n) to honour line breaks on first open.
        // Using only \n causes Excel to collapse all skills onto one line until the column
        // is resized/clicked — \r\n forces the cell to render correctly immediately.
        const techText = row.tech.length ? row.tech.join('\r\n') : '';
        const softText = row.soft.length ? row.soft.join('\r\n') : '';

        // Row height driven by the column with the most line-items.
        // hpt = 15pt per line (matches Calibri 10 single-spaced) + 8pt top/bottom padding.
        // `customHeight: true` tells Excel this is a manually set height — do NOT auto-fit.
        // Without this flag Excel ignores hpt and collapses the row on open.
        const lines  = Math.max(row.tech.length, row.soft.length, 1);
        const rowHpt = lines * 15 + 8;

        // Col 0 — Job Title / Role
        ws[C(r,0)] = mkCell(row.role, {
            fill: FILL(bg), font: FONT({ bold: true }),
            alignment: AL('left','top', true), border: bAll
        });
        // Col 1 — Sector
        ws[C(r,1)] = mkCell(row.sector, {
            fill: FILL(bg), font: FONT(),
            alignment: AL('left','top', true), border: bAll
        });
        // Col 2 — Salary Range
        ws[C(r,2)] = mkCell(row.salary, {
            fill: FILL(bg), font: FONT(),
            alignment: AL('left','center'), border: bAll
        });
        // Col 3 — Missing Technical Skills (each skill on its own line via \r\n)
        ws[C(r,3)] = {
            t: 's',
            v: techText,
            s: { fill: FILL(bg), font: FONT(), alignment: AL('left','top', true), border: bAll }
        };
        // Col 4 — Missing Soft Skills (each skill on its own line via \r\n)
        ws[C(r,4)] = {
            t: 's',
            v: softText,
            s: { fill: FILL(bg), font: FONT(), alignment: AL('left','top', true), border: bAll }
        };
        // Col 5 — Gap Impact (last column)
        ws[C(r,5)] = mkCell(row.impact, {
            fill: FILL(iBg), font: FONT({ bold: true }),
            alignment: AL('center','center'), border: bAll
        });

        // customHeight:true locks the hpt so Excel won't collapse it on open
        rowHeights.push({ hpt: rowHpt, customHeight: true });
    });

    // ── Sheet metadata ────────────────────────────────────────────────────────
    ws['!ref'] = XLSX.utils.encode_range({ s: { r: 0, c: 0 }, e: { r: 4 + rows.length, c: NC - 1 } });
    ws['!merges'] = [
        { s: { r: 0, c: 0 }, e: { r: 0, c: NC - 1 } }, // title
        { s: { r: 1, c: 0 }, e: { r: 1, c: NC - 1 } }, // generated on
        { s: { r: 2, c: 0 }, e: { r: 2, c: NC - 1 } }, // page info
        { s: { r: 3, c: 0 }, e: { r: 3, c: NC - 1 } }, // spacer
    ];
    ws['!cols'] = colW.map(wch => ({ wch }));
    ws['!rows'] = rowHeights;

    // ── Export ────────────────────────────────────────────────────────────────
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Competency Gap Analysis');
    const filenamePart = filterIsActive ? 'all-filtered' : `page${currentPage}`;
    XLSX.writeFile(wb, `lmi-competency-gap-analysis-${filenamePart}-${dateStr}.xlsx`, { bookType: 'xlsx', cellStyles: true });
}

// Helper function to escape CSV values
function escapeCSVValue(value) {
    if (value === null || value === undefined) {
        return '';
    }
    
    const stringValue = String(value);
    
    // If value contains comma, quote, or newline, wrap in quotes and escape quotes
    if (stringValue.includes(',') || stringValue.includes('"') || stringValue.includes('\n')) {
        return '"' + stringValue.replace(/"/g, '""') + '"';
    }
    
    return stringValue;
}

// Initialize the export button when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const exportBtn = document.getElementById('exportLMIMatrixBtn');
    
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            exportLMIMatrixToCSV();
        });
    }
});
    </script>
    <script>
       document.addEventListener('DOMContentLoaded', function () {

    const steps    = document.querySelectorAll('.lmi-step');   // the 4 <div> wrappers
    const circles  = document.querySelectorAll('.step-circle');
    const lines    = document.querySelectorAll('.step-line');
    let current    = 0;

    // ─── INIT: hide all except first ────────────────────────
    window.showStep = function showStep(n) {
    steps.forEach((s, i) => s.style.display = (i === n) ? 'block' : 'none');
    current = n;
    updateIndicator();
    updateButtons();
    
    // ► HIDE INTRO SECTION AFTER STEP 1 ◄
    const introSection = document.getElementById('intro-section');
    if (introSection) {
        introSection.style.display = (n === 0) ? 'block' : 'none';
    }
    
    // scroll modal back to top
    const scrollable = document.querySelector('#lmi-form-content .overflow-y-auto');
    if (scrollable) scrollable.scrollTo({ top: 0, behavior: 'smooth' });
}

    // ─── INDICATOR ──────────────────────────────────────────
    function updateIndicator() {
        circles.forEach((c, i) => {
            c.classList.remove('bg-white','text-teal-700','bg-teal-500','text-white');
            if (i < current) {
                c.classList.add('bg-white','text-teal-700');
                c.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
            } else if (i === current) {
                c.classList.add('bg-white','text-teal-700');
                c.innerHTML = (i + 1).toString();
            } else {
                c.classList.add('bg-teal-500','text-white');
                c.innerHTML = (i + 1).toString();
            }
        });
        lines.forEach((l, i) => {
            l.classList.toggle('bg-white', i < current);
            l.classList.toggle('bg-teal-500', i >= current);
        });
    }

    // ─── BUTTONS ────────────────────────────────────────────
    function updateButtons() {
        steps.forEach((step, i) => {
            const prev   = step.querySelector('.btn-prev');
            const next   = step.querySelector('.btn-next');
            const submit = step.querySelector('.btn-submit-lmi');

            if (prev)   prev.style.display   = (i === 0) ? 'none' : 'inline-flex';
            if (next)   next.style.display   = (i === steps.length - 1) ? 'none' : 'inline-flex';
            if (submit) submit.style.display = (i === steps.length - 1) ? 'inline-flex' : 'none';
        });
    }

    // ─── VALIDATION ─────────────────────────────────────────
    window.validateStep = function validateStep(idx) {
        const step  = steps[idx];
        let   valid = true;

        // -- text / email / tel --
        step.querySelectorAll('input[type="text"][required], input[type="email"][required], input[type="tel"][required]').forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('border-red-500');
                valid = false;
            } else {
                input.classList.remove('border-red-500');
            }
        });

        // -- Contact number length check (step 0 only) --
        if (idx === 0) {
            const contactType   = document.getElementById('contact_type_input');
            const mobileInp     = document.getElementById('mobile-input');
            const telephoneInp  = document.getElementById('telephone-input');
            const contactHint   = document.getElementById('contact-hint');

            if (contactType && contactType.value === 'mobile' && mobileInp && !mobileInp.disabled) {
                const digits = mobileInp.value.replace(/\D/g, '');
                const required = selectedCountry.maxDigits;
                if (digits.length !== required) {
                    mobileInp.classList.add('border-red-500');
                    if (contactHint) {
                        contactHint.innerHTML = `<svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span class="text-red-500">Mobile number must be exactly ${required} digits for ${selectedCountry.name}</span>`;
                    }
                    valid = false;
                } else {
                    mobileInp.classList.remove('border-red-500');
                    if (contactHint) {
                        contactHint.innerHTML = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> ${required}-digit mobile number (${selectedCountry.name})`;
                    }
                }
            } else if (contactType && contactType.value === 'telephone' && telephoneInp && !telephoneInp.disabled) {
                const digits = telephoneInp.value.replace(/\D/g, '');
                if (digits.length !== 10) {
                    telephoneInp.classList.add('border-red-500');
                    if (contactHint) {
                        contactHint.innerHTML = '<svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span class="text-red-500">Telephone number must be exactly 10 digits</span>';
                    }
                    valid = false;
                } else {
                    telephoneInp.classList.remove('border-red-500');
                    if (contactHint) {
                        contactHint.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> 10-digit telephone number';
                    }
                }
            }
        }

        // -- Email format check (step 0 only) --
        if (idx === 0) {
            const emailInput = step.querySelector('input[type="email"]');
            const emailError = document.getElementById('emailError');
            if (emailInput && emailInput.value.trim()) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value.trim())) {
                    emailInput.classList.add('border-red-500');
                    if (emailError) emailError.classList.remove('hidden');
                    valid = false;
                } else {
                    emailInput.classList.remove('border-red-500');
                    if (emailError) emailError.classList.add('hidden');
                }
            } else if (emailInput && !emailInput.value.trim()) {
                if (emailError) emailError.classList.add('hidden');
            }
        }

        // -- hidden inputs (dropdowns: industrySelector, companySize, job_classification, vacancy_duration, salary_range) --
        step.querySelectorAll('input[type="hidden"][required]').forEach(input => {
            const wrapper = input.closest('.relative');
            const btn     = wrapper ? wrapper.querySelector('button[type="button"]') : null;
            if (!input.value) {
                valid = false;
                if (btn) btn.classList.add('border-red-500');
            } else {
                if (btn) btn.classList.remove('border-red-500');
            }
        });

        // -- Step 2 (idx 1): validate salary range and "Below ₱30,000" for every job entry --
        if (idx === 1) {
            step.querySelectorAll('.job-entry').forEach(jobEntry => {
                const salaryRangeInput = jobEntry.querySelector('.salary-range-input');
                const salaryRangeBtn = jobEntry.querySelector('.salary-range-btn');
                const below30kInput = jobEntry.querySelector('.below-30k-salary-input');
                const below30kContainer = jobEntry.querySelector('.below-30k-input-container');
                
                // Check if salary range is selected
                if (salaryRangeInput && !salaryRangeInput.value) {
                    valid = false;
                    if (salaryRangeBtn) salaryRangeBtn.classList.add('border-red-500');
                } else {
                    if (salaryRangeBtn) salaryRangeBtn.classList.remove('border-red-500');
                    
                    // If "Below ₱30,000" is selected, validate the input field
                    if (salaryRangeInput && salaryRangeInput.value === '__below_30k__') {
                        if (below30kInput && !below30kInput.value.trim()) {
                            valid = false;
                            below30kInput.classList.add('border-red-500');
                        } else if (below30kInput) {
                            below30kInput.classList.remove('border-red-500');
                            // Validate that the amount is less than 30,000
                            const amount = parseInt(below30kInput.value.replace(/,/g, ''));
                            if (isNaN(amount) || amount >= 30000) {
                                valid = false;
                                below30kInput.classList.add('border-red-500');
                                alert('Salary amount must be less than ₱30,000');
                            } else {
                                // Replace sentinel with the actual numeric value
                                salaryRangeInput.value = String(amount);
                            }
                        }
                    } else if (salaryRangeInput && salaryRangeInput.value === 'Below ₱30,000') {
                        if (below30kInput && !below30kInput.value.trim()) {
                            valid = false;
                            below30kInput.classList.add('border-red-500');
                        } else if (below30kInput) {
                            below30kInput.classList.remove('border-red-500');
                            
                            // Validate that the amount is less than 30,000
                            const amount = parseInt(below30kInput.value.replace(/,/g, ''));
                            if (isNaN(amount) || amount >= 30000) {
                                valid = false;
                                below30kInput.classList.add('border-red-500');
                                alert('Salary amount must be less than ₱30,000');
                            }
                        }
                    }
                }

                // FIX 3: At least one skill must be checked, and if checked must have >=1 tag
                // NOTE: hidden input stores tags as comma-separated string e.g. "Python, SQL"
                // so we check .trim().length > 0, NOT JSON.parse
                const techCheckbox = jobEntry.querySelector('.technical-checkbox');
                const techSkillsLabel = jobEntry.querySelector('.technical-skills-label');
                const techSkillInput = jobEntry.querySelector('.technical-skill-input');

                const softCheckbox = jobEntry.querySelector('.soft-checkbox');
                const softSkillsLabel = jobEntry.querySelector('.soft-skills-label');
                const softSkillInput = jobEntry.querySelector('.soft-skill-input');

                const eitherChecked = (techCheckbox && techCheckbox.checked) || (softCheckbox && softCheckbox.checked);

                if (!eitherChecked) {
                    // Condition 1: neither checked - highlight both, block next
                    valid = false;
                    if (techSkillsLabel) techSkillsLabel.classList.add('border-red-500');
                    if (softSkillsLabel) softSkillsLabel.classList.add('border-red-500');
                } else {
                    // Condition 2 & 3: at least one is checked
                    if (techCheckbox && techCheckbox.checked) {
                        const techHidden = jobEntry.querySelector('.technical-skills-input');
                        const hasTechTags = techHidden && techHidden.value.trim().length > 0;
                        if (!hasTechTags) {
                            valid = false;
                            if (techSkillsLabel) techSkillsLabel.classList.add('border-red-500');
                            if (techSkillInput) techSkillInput.classList.add('border-red-500');
                        } else {
                            if (techSkillsLabel) techSkillsLabel.classList.remove('border-red-500');
                            if (techSkillInput) techSkillInput.classList.remove('border-red-500');
                        }
                    } else {
                        // Unchecked - always clear any lingering red border
                        if (techSkillsLabel) techSkillsLabel.classList.remove('border-red-500');
                        if (techSkillInput) techSkillInput.classList.remove('border-red-500');
                    }

                    if (softCheckbox && softCheckbox.checked) {
                        const softHidden = jobEntry.querySelector('.soft-skills-input');
                        const hasSoftTags = softHidden && softHidden.value.trim().length > 0;
                        if (!hasSoftTags) {
                            valid = false;
                            if (softSkillsLabel) softSkillsLabel.classList.add('border-red-500');
                            if (softSkillInput) softSkillInput.classList.add('border-red-500');
                        } else {
                            if (softSkillsLabel) softSkillsLabel.classList.remove('border-red-500');
                            if (softSkillInput) softSkillInput.classList.remove('border-red-500');
                        }
                    } else {
                        // Unchecked - always clear any lingering red border
                        if (softSkillsLabel) softSkillsLabel.classList.remove('border-red-500');
                        if (softSkillInput) softSkillInput.classList.remove('border-red-500');
                    }
                }
            });
        }

        // -- radio groups --
        const radioNames = new Set();
        step.querySelectorAll('input[type="radio"][required]').forEach(r => radioNames.add(r.name));
        radioNames.forEach(name => {
            const checked = step.querySelector(`input[type="radio"][name="${name}"]:checked`);
            const radios  = step.querySelectorAll(`input[type="radio"][name="${name}"]`);
            if (!checked) {
                valid = false;
                radios.forEach(r => { const lbl = r.closest('label'); if (lbl) lbl.classList.add('border-red-500'); });
            } else {
                radios.forEach(r => { const lbl = r.closest('label'); if (lbl) lbl.classList.remove('border-red-500'); });
            }
        });

        // -- Step 3 (idx 2): at least one rejection_reasons checkbox --
        if (idx === 2) {
            const checked = step.querySelectorAll('input[name="rejection_reasons[]"]:checked');
            if (checked.length === 0) {
                valid = false;
                step.querySelectorAll('input[name="rejection_reasons[]"]').forEach(cb => {
                    const p = cb.closest('label') || cb.closest('.other-rejection-option');
                    if (p) p.classList.add('border-red-500');
                });
            } else {
                step.querySelectorAll('input[name="rejection_reasons[]"]').forEach(cb => {
                    const p = cb.closest('label') || cb.closest('.other-rejection-option');
                    if (p) p.classList.remove('border-red-500');
                });
            }

            // Always check: if "Other" rejection is checked, text must be filled
            const otherRejectionCb = step.querySelector('.other-rejection-checkbox');
            const otherRejectionText = step.querySelector('textarea[name="rejection_reasons_other"]');
            if (otherRejectionCb && otherRejectionCb.checked) {
                if (otherRejectionText && !otherRejectionText.value.trim()) {
                    valid = false;
                    otherRejectionText.classList.add('border-red-500');
                    otherRejectionText.placeholder = 'This field is required';
                } else if (otherRejectionText) {
                    otherRejectionText.classList.remove('border-red-500');
                }
            } else if (otherRejectionText) {
                otherRejectionText.classList.remove('border-red-500');
            }

            // Always check: if "Other" coordination is selected, text must be filled
            const otherCoordCb = step.querySelector('.other-coordination-radio');
            const otherCoordText = step.querySelector('textarea[name="coordination_frequency_other"]');
            if (otherCoordCb && otherCoordCb.checked) {
                if (otherCoordText && !otherCoordText.value.trim()) {
                    valid = false;
                    otherCoordText.classList.add('border-red-500');
                    otherCoordText.placeholder = 'This field is required';
                } else if (otherCoordText) {
                    otherCoordText.classList.remove('border-red-500');
                }
            } else if (otherCoordText) {
                otherCoordText.classList.remove('border-red-500');
            }
        }

        // -- Step 4 (idx 3): consent + at least one lmi_features --
        if (idx === 3) {
            const consent = step.querySelector('input[name="consent"]');
            if (consent && !consent.checked) {
                valid = false;
                const lbl = consent.closest('label');
                if (lbl) lbl.classList.add('border-red-500');
            } else if (consent) {
                const lbl = consent.closest('label');
                if (lbl) lbl.classList.remove('border-red-500');
            }

            const lmiChecked = step.querySelectorAll('input[name="lmi_features[]"]:checked');
            const lmiFeaturesGroup = step.querySelector('#lmi-features-group');
            if (lmiChecked.length < 2 ) {
                valid = false;
                if (lmiFeaturesGroup) lmiFeaturesGroup.classList.add('border-2', 'border-red-400', 'rounded-lg', 'p-2');
            } else {
                if (lmiFeaturesGroup) lmiFeaturesGroup.classList.remove('border-2', 'border-red-400', 'rounded-lg', 'p-2');
            }

            // If "Other" LMI feature is checked, its text must be filled
            const lmiOtherCb = step.querySelector('.lmi-other-checkbox');
            const otherText = step.querySelector('textarea[name="lmi_features_other"]');
            if (lmiOtherCb && lmiOtherCb.checked) {
                if (otherText && !otherText.value.trim()) {
                    valid = false;
                    otherText.classList.add('border-red-500');
                    otherText.placeholder = 'This field is required';
                } else if (otherText) {
                    otherText.classList.remove('border-red-500');
                }
            } else if (otherText) {
                otherText.classList.remove('border-red-500');
            }
        }

        // scroll to first red field
        if (!valid) {
            const bad = step.querySelector('.border-red-500');
            if (bad) bad.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return valid;
    }

    // ─── BIND NEXT / PREV clicks ────────────────────────────
    steps.forEach((step, i) => {
        const next = step.querySelector('.btn-next');
        const prev = step.querySelector('.btn-prev');

        if (next) {
            next.addEventListener('click', function () {
                if (validateStep(i)) showStep(i + 1);
            });
        }
        if (prev) {
            prev.addEventListener('click', function () {
                if (i > 0) showStep(i - 1);
            });
        }
    });
        
    // ─── INIT ───────────────────────────────────────────────
    showStep(0);
});
</script>
    <script>
// ─── Contact Number Toggle ────────────────────────────────────────────────────
function switchContactType(type) {
    const mobileWrapper    = document.getElementById("mobile-input-wrapper");
    const telephoneWrapper = document.getElementById("telephone-input-wrapper");
    const mobileInput      = document.getElementById("mobile-input");
    const telephoneInput   = document.getElementById("telephone-input");
    const hint             = document.getElementById("contact-hint");
    const contactTypeInput = document.getElementById("contact_type_input");
    const toggleMobile     = document.getElementById("toggle-mobile");
    const toggleTelephone  = document.getElementById("toggle-telephone");

    [toggleMobile, toggleTelephone].forEach(btn => {
        btn.classList.remove("bg-white", "text-teal-700", "shadow-sm", "border", "border-gray-200");
        btn.classList.add("text-gray-500");
    });

    if (type === "mobile") {
        mobileWrapper.classList.remove("hidden");
        telephoneWrapper.classList.add("hidden");
        mobileInput.disabled = false;
        mobileInput.required = true;
        telephoneInput.disabled = true;
        telephoneInput.required = false;
        telephoneInput.value = "";
        hint.innerHTML = "<svg class=\"w-3 h-3\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"/></svg> Enter your mobile number with country code";
        contactTypeInput.value = "mobile";
        toggleMobile.classList.add("bg-white", "text-teal-700", "shadow-sm", "border", "border-gray-200");
        toggleMobile.classList.remove("text-gray-500");
        // Sync carrier
        const carrier = document.getElementById('contact_number_carrier');
        if (carrier) carrier.value = selectedCountry.dial + mobileInput.value;
    } else {
        telephoneWrapper.classList.remove("hidden");
        mobileWrapper.classList.add("hidden");
        telephoneInput.disabled = false;
        telephoneInput.required = true;
        mobileInput.disabled = true;
        mobileInput.required = false;
        mobileInput.value = "";
        hint.innerHTML = "<svg class=\"w-3 h-3\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"/></svg> Auto-formats to 082-123-4567";
        contactTypeInput.value = "telephone";
        toggleTelephone.classList.add("bg-white", "text-teal-700", "shadow-sm", "border", "border-gray-200");
        toggleTelephone.classList.remove("text-gray-500");
        // Sync carrier
        const carrier = document.getElementById('contact_number_carrier');
        if (carrier) carrier.value = telephoneInput.value;
        telephoneInput.focus();
    }
}

// ─── Country Code Selector ────────────────────────────────────────────────────
const COUNTRIES = [
    { flag: '🇵🇭', name: 'Philippines',   dial: '+63',  maxDigits: 10 },
    { flag: '🇺🇸', name: 'United States', dial: '+1',   maxDigits: 10 },
    { flag: '🇬🇧', name: 'United Kingdom',dial: '+44',  maxDigits: 10 },
    { flag: '🇦🇺', name: 'Australia',     dial: '+61',  maxDigits: 9  },
    { flag: '🇨🇦', name: 'Canada',        dial: '+1',   maxDigits: 10 },
    { flag: '🇯🇵', name: 'Japan',         dial: '+81',  maxDigits: 10 },
    { flag: '🇰🇷', name: 'South Korea',   dial: '+82',  maxDigits: 10 },
    { flag: '🇸🇬', name: 'Singapore',     dial: '+65',  maxDigits: 8  },
    { flag: '🇲🇾', name: 'Malaysia',      dial: '+60',  maxDigits: 9  },
    { flag: '🇮🇩', name: 'Indonesia',     dial: '+62',  maxDigits: 11 },
    { flag: '🇹🇭', name: 'Thailand',      dial: '+66',  maxDigits: 9  },
    { flag: '🇻🇳', name: 'Vietnam',       dial: '+84',  maxDigits: 9  },
    { flag: '🇮🇳', name: 'India',         dial: '+91',  maxDigits: 10 },
    { flag: '🇨🇳', name: 'China',         dial: '+86',  maxDigits: 11 },
    { flag: '🇭🇰', name: 'Hong Kong',     dial: '+852', maxDigits: 8  },
    { flag: '🇹🇼', name: 'Taiwan',        dial: '+886', maxDigits: 9  },
    { flag: '🇸🇦', name: 'Saudi Arabia',  dial: '+966', maxDigits: 9  },
    { flag: '🇦🇪', name: 'UAE',           dial: '+971', maxDigits: 9  },
    { flag: '🇶🇦', name: 'Qatar',         dial: '+974', maxDigits: 8  },
    { flag: '🇩🇪', name: 'Germany',       dial: '+49',  maxDigits: 11 },
    { flag: '🇫🇷', name: 'France',        dial: '+33',  maxDigits: 9  },
    { flag: '🇮🇹', name: 'Italy',         dial: '+39',  maxDigits: 10 },
    { flag: '🇪🇸', name: 'Spain',         dial: '+34',  maxDigits: 9  },
    { flag: '🇳🇱', name: 'Netherlands',   dial: '+31',  maxDigits: 9  },
    { flag: '🇳🇿', name: 'New Zealand',   dial: '+64',  maxDigits: 9  },
    { flag: '🇧🇷', name: 'Brazil',        dial: '+55',  maxDigits: 11 },
    { flag: '🇲🇽', name: 'Mexico',        dial: '+52',  maxDigits: 10 },
    { flag: '🇿🇦', name: 'South Africa',  dial: '+27',  maxDigits: 9  },
    { flag: '🇳🇬', name: 'Nigeria',       dial: '+234', maxDigits: 10 },
    { flag: '🇰🇪', name: 'Kenya',         dial: '+254', maxDigits: 9  },
];

let selectedCountry = COUNTRIES[0]; // Default: Philippines

function renderCountryList(filter = '') {
    const list = document.getElementById('country-list');
    if (!list) return;

    const filtered = COUNTRIES.filter(c =>
        c.name.toLowerCase().includes(filter.toLowerCase()) ||
        c.dial.includes(filter)
    );

    list.innerHTML = filtered.length
        ? filtered.map(c => `
            <div class="country-option flex items-center gap-3 px-4 py-2.5 hover:bg-teal-50 cursor-pointer text-sm transition border-b border-gray-50 last:border-b-0"
                 data-dial="${c.dial}" data-flag="${c.flag}" data-name="${c.name}" data-max-digits="${c.maxDigits}">
                <span class="text-lg">${c.flag}</span>
                <span class="flex-1 text-gray-700">${c.name}</span>
                <span class="text-gray-400 font-mono text-xs">${c.dial}</span>
            </div>`).join('')
        : '<div class="px-4 py-3 text-sm text-gray-400 text-center">No results found</div>';

    list.querySelectorAll('.country-option').forEach(opt => {
        opt.addEventListener('click', () => {
            selectedCountry = {
                flag: opt.dataset.flag,
                name: opt.dataset.name,
                dial: opt.dataset.dial,
                maxDigits: parseInt(opt.dataset.maxDigits),
            };
            document.getElementById('country-flag').textContent  = selectedCountry.flag;
            document.getElementById('country-dial-code').textContent = selectedCountry.dial;
            document.getElementById('country-dropdown').classList.add('hidden');

            // Update maxlength and hint based on selected country
            const mobileInput = document.getElementById('mobile-input');
            if (mobileInput) {
                mobileInput.maxLength = selectedCountry.maxDigits;
                mobileInput.value = mobileInput.value.slice(0, selectedCountry.maxDigits);
            }
            const hint = document.getElementById('contact-hint');
            if (hint) {
                hint.innerHTML = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> ${selectedCountry.maxDigits}-digit mobile number (${selectedCountry.name})`;
            }

            // Keep carrier in sync
            const carrier = document.getElementById('contact_number_carrier');
            if (carrier) carrier.value = selectedCountry.dial + (mobileInput?.value || '');
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    renderCountryList();

    const ccBtn     = document.getElementById('country-code-btn');
    const ccDropdown= document.getElementById('country-dropdown');
    const ccSearch  = document.getElementById('country-search');

    // Set initial maxlength for default country (Philippines = 10)
    const mobileInputInit = document.getElementById('mobile-input');
    if (mobileInputInit) mobileInputInit.maxLength = selectedCountry.maxDigits;

    if (ccBtn) {
        ccBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            ccDropdown.classList.toggle('hidden');
            if (!ccDropdown.classList.contains('hidden')) {
                ccSearch.value = '';
                renderCountryList();
                setTimeout(() => ccSearch.focus(), 50);
            }
        });
    }

    if (ccSearch) {
        ccSearch.addEventListener('input', () => renderCountryList(ccSearch.value));
        // Prevent Enter from submitting form while searching
        ccSearch.addEventListener('keydown', (e) => { if (e.key === 'Enter') e.preventDefault(); });
    }

    // Close dropdown on outside click
    document.addEventListener('click', (e) => {
        if (!ccBtn?.contains(e.target) && !ccDropdown?.contains(e.target)) {
            ccDropdown?.classList.add('hidden');
        }
    });

    // Keep carrier in sync as user types the number
    const mobileInput = document.getElementById('mobile-input');
    if (mobileInput) {
        mobileInput.addEventListener('input', () => {
            const carrier = document.getElementById('contact_number_carrier');
            if (carrier) carrier.value = selectedCountry.dial + mobileInput.value;
        });
    }
});

// ─── Telephone Auto-Formatter + Area Code Suggestions ────────────────────────
document.addEventListener("DOMContentLoaded", function () {
    const telInput   = document.getElementById("telephone-input");
    const suggestBox = document.getElementById("area-code-suggestions");
    const suggestList= document.getElementById("area-code-list");
    if (!telInput) return;

    // ── Complete PH Area Code Directory ──────────────────────────────────────
    // Format: { code: "0XX", label: "Province / City" }
    // Source: Wikipedia "Telephone numbers in the Philippines" + NTC 2025
    const PH_AREA_CODES = [
        // Metro Manila & surroundings (area code 2 — 8-digit local)
        { code: "02", label: "Metro Manila, Rizal, Bacoor, San Pedro" },

        // Luzon
        { code: "032", label: "Cebu" },
        { code: "033", label: "Guimaras, Iloilo (part)" },
        { code: "034", label: "Iloilo, Negros Occidental" },
        { code: "035", label: "Negros Oriental, Siquijor" },
        { code: "036", label: "Aklan, Antique, Capiz" },
        { code: "038", label: "Bohol" },
        { code: "042", label: "Aurora, Marinduque, Quezon" },
        { code: "043", label: "Batangas, Occidental Mindoro, Oriental Mindoro" },
        { code: "044", label: "Bulacan, Nueva Ecija" },
        { code: "045", label: "Pampanga, Tarlac" },
        { code: "046", label: "Cavite (except Bacoor)" },
        { code: "047", label: "Bataan, Zambales" },
        { code: "048", label: "Palawan" },
        { code: "049", label: "Laguna (except San Pedro)" },
        { code: "052", label: "Albay, Catanduanes" },
        { code: "053", label: "Biliran, Leyte, Southern Leyte" },
        { code: "054", label: "Camarines Norte, Camarines Sur, Romblon" },
        { code: "055", label: "Eastern Samar, Northern Samar, Samar" },
        { code: "056", label: "Masbate, Sorsogon" },
        { code: "062", label: "Basilan, Zamboanga del Sur, Zamboanga Sibugay" },
        { code: "063", label: "Lanao del Norte" },
        { code: "064", label: "Lanao del Sur, Maguindanao, North Cotabato, Sultan Kudarat" },
        { code: "065", label: "Zamboanga del Norte" },
        { code: "068", label: "Tawi-Tawi" },
        { code: "072", label: "La Union" },
        { code: "074", label: "Abra, Benguet, Ifugao, Kalinga, Mountain Province" },
        { code: "075", label: "Pangasinan" },
        { code: "077", label: "Ilocos Norte, Ilocos Sur" },
        { code: "078", label: "Apayao, Batanes, Cagayan, Isabela, Nueva Vizcaya, Quirino" },

        // Mindanao
        { code: "082", label: "Davao del Sur, Davao Occidental" },
        { code: "083", label: "Sarangani, South Cotabato" },
        { code: "084", label: "Compostela Valley, Davao del Norte" },
        { code: "085", label: "Agusan del Norte, Agusan del Sur, Sulu" },
        { code: "086", label: "Dinagat Islands, Surigao del Norte, Surigao del Sur" },
        { code: "087", label: "Davao de Oro, Davao Oriental" },
        { code: "088", label: "Bukidnon, Camiguin, Misamis Occidental, Misamis Oriental" },
    ];

    // ── Format telephone digits → readable string ─────────────────────────────
    // Area code "2"  (Metro Manila): 02-XXXX-XXXX  (1+8 digits)
    // All others:                    0XX-XXX-XXXX   (2+7 digits)
    function formatTelephone(digits) {
        if (!digits) return "";
        if (!digits.startsWith("0")) digits = "0" + digits;

        const withoutTrunk = digits.slice(1);

        if (withoutTrunk.startsWith("2")) {
            const local = withoutTrunk.slice(1);
            if (local.length === 0) return "02";
            if (local.length <= 4)  return "02-" + local;
            return "02-" + local.slice(0, 4) + "-" + local.slice(4);
        }

        const area  = withoutTrunk.slice(0, 2);
        const local = withoutTrunk.slice(2);
        if (local.length === 0) return "0" + area;
        if (local.length <= 3)  return "0" + area + "-" + local;
        return "0" + area + "-" + local.slice(0, 3) + "-" + local.slice(3);
    }

    // ── Show/hide suggestion dropdown ─────────────────────────────────────────
    let activeIndex = -1; // for keyboard navigation

    function showSuggestions(typedDigits) {
        if (!typedDigits || typedDigits.length < 2) {
            hideSuggestions();
            return;
        }

        // Only show suggestions while user is still typing the area code
        // (i.e. total digits typed is 3 or less — "0", "08", "082")
        // Once they go past the area code into local number, hide suggestions
        if (typedDigits.length > 3) {
            hideSuggestions();
            return;
        }

        const matches = PH_AREA_CODES.filter(ac =>
            ac.code.startsWith(typedDigits)
        );

        if (matches.length === 0) {
            hideSuggestions();
            return;
        }

        suggestList.innerHTML = "";
        activeIndex = -1;

        matches.forEach((ac, i) => {
            const item = document.createElement("div");
            item.className = "suggestion-item flex items-center gap-3 px-4 py-2.5 hover:bg-teal-50 cursor-pointer border-b border-gray-50 last:border-b-0 transition-colors";
            item.dataset.index = i;

            // Highlight the matching part of the area code
            const typed      = typedDigits;
            const codeHtml   = `<span class="font-bold text-teal-600">${ac.code.slice(0, typed.length)}</span><span class="font-bold text-gray-800">${ac.code.slice(typed.length)}</span>`;

            item.innerHTML = `
                <span class="shrink-0 text-xs font-mono bg-teal-50 text-teal-700 border border-teal-200 rounded px-2 py-0.5">${codeHtml}</span>
                <span class="text-sm text-gray-600 truncate">${ac.label}</span>
            `;

            item.addEventListener("mousedown", function (e) {
                e.preventDefault(); // prevent input blur before click fires
                selectAreaCode(ac);
            });

            suggestList.appendChild(item);
        });

        suggestBox.classList.remove("hidden");
    }

    function hideSuggestions() {
        suggestBox.classList.add("hidden");
        activeIndex = -1;
    }

    function selectAreaCode(ac) {
        // Fill the input with the area code + dash, ready for local number
        // e.g. selecting "082" → input becomes "082-"
        telInput.value = ac.code + "-";
        hideSuggestions();
        telInput.focus();
    }

    // ── Keyboard navigation through suggestions ───────────────────────────────
    function navigateSuggestions(direction) {
        const items = suggestList.querySelectorAll(".suggestion-item");
        if (items.length === 0) return;

        // Remove highlight from current
        items.forEach(i => i.classList.remove("bg-teal-50"));

        activeIndex += direction;
        if (activeIndex < 0)             activeIndex = items.length - 1;
        if (activeIndex >= items.length) activeIndex = 0;

        items[activeIndex].classList.add("bg-teal-50");
        items[activeIndex].scrollIntoView({ block: "nearest" });
    }

    // ── Event Listeners ───────────────────────────────────────────────────────
    telInput.addEventListener("input", function (e) {
        let digits = e.target.value.replace(/\D/g, "");
        if (digits.length > 10) digits = digits.slice(0, 10);

        // Show suggestions only when typing area code portion
        showSuggestions(digits.length <= 3 ? digits : null);

        // Format the number
        e.target.value = formatTelephone(digits);
    });

    telInput.addEventListener("keydown", function (e) {
        // Navigation keys for suggestion dropdown
        if (!suggestBox.classList.contains("hidden")) {
            if (e.key === "ArrowDown") { e.preventDefault(); navigateSuggestions(1);  return; }
            if (e.key === "ArrowUp")   { e.preventDefault(); navigateSuggestions(-1); return; }
            if (e.key === "Enter") {
                e.preventDefault();
                const items = suggestList.querySelectorAll(".suggestion-item");
                if (activeIndex >= 0 && items[activeIndex]) {
                    const code  = items[activeIndex].querySelector("span").textContent.trim();
                    const match = PH_AREA_CODES.find(ac => ac.code === items[activeIndex].querySelector("span").textContent.replace(/\s/g,'').replace(/[^0-9]/g,'') );
                    // Simpler: just click the active item
                    items[activeIndex].dispatchEvent(new MouseEvent("mousedown"));
                }
                return;
            }
            if (e.key === "Escape") { hideSuggestions(); return; }
        }

        // Block non-numeric keys
        const allowedKeys = ["Backspace","Delete","ArrowLeft","ArrowRight",
                             "ArrowUp","ArrowDown","Tab","Home","End"];
        const isDigit = e.key >= "0" && e.key <= "9";
        const isCtrl  = e.ctrlKey || e.metaKey;
        if (!isDigit && !allowedKeys.includes(e.key) && !isCtrl) {
            e.preventDefault();
        }
    });

    telInput.addEventListener("paste", function (e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData("text");
        let digits   = pasted.replace(/\D/g, "").slice(0, 10);
        e.target.value = formatTelephone(digits);
        hideSuggestions();
    });

    // Hide suggestions when clicking outside
    document.addEventListener("click", function (e) {
        if (!telInput.contains(e.target) && !suggestBox.contains(e.target)) {
            hideSuggestions();
        }
    });

    telInput.addEventListener("blur", function () {
        // Small delay so mousedown on suggestion fires first
        setTimeout(hideSuggestions, 150);
    });
});
</script>
<script>
(function () {
    const MONTH_SHORT = ['','Jan','Feb','Mar','Apr','May','Jun',
                         'Jul','Aug','Sep','Oct','Nov','Dec'];

    const htfArchiveOptions = @json($archive_options ?? []);

    // Pending = inside open panel, not yet applied
    let htfPendingYears  = [];
    let htfPendingMonths = []; // empty = all months
    let htfPendingMode  = 'range'; // 'range' | 'exact'

    // Applied = last committed values
    let htfAppliedYears  = [];
    let htfAppliedMonths = []; // empty = all months
    let htfAppliedMode   = 'range';

    /* ════════════════════════════
       BOOT
    ════════════════════════════ */
    document.addEventListener('DOMContentLoaded', function () {
        buildHtfYearChips();

        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('htfFilterWrapper');
            if (wrapper && !wrapper.contains(e.target)) htfPanelClose();
        });
    });

    /* ════════════════════════════
       BUILD YEAR CHIPS
    ════════════════════════════ */
    function buildHtfYearChips() {
        const container = document.getElementById('htfpYearChips');
        const yearHint  = document.getElementById('htfpYearHint');
        if (!container) return;
        container.innerHTML = '';

        // Update year hint based on mode
        if (yearHint) {
            yearHint.textContent = htfPendingMode === 'range'
                ? 'select From & To'
                : 'pick any years';
        }

        const years = [...new Set(htfArchiveOptions.map(o => String(o.year)))]
            .sort((a, b) => Number(b) - Number(a));

        if (!years.length) {
            container.innerHTML = '<span class="htfp-chip htfp-placeholder">No archived data</span>';
            return;
        }

        years.forEach(yr => {
            const btn = document.createElement('button');
            btn.type        = 'button';
            btn.className   = 'htfp-chip' + (htfPendingYears.includes(yr) ? ' htfp-selected' : '');
            btn.textContent = yr;
            btn.dataset.val = yr;
            btn.onclick     = () => htfToggleYear(btn, yr);
            container.appendChild(btn);
        });
    }

    /* ════════════════════════════
       BUILD MONTH CHIPS
    ════════════════════════════ */
    function buildHtfMonthChips() {
        const container = document.getElementById('htfpMonthChips');
        const hint      = document.getElementById('htfpMonthHint');
        if (!container) return;
        container.innerHTML = '';

        if (!htfPendingYears.length) {
            if (hint) hint.textContent = 'select a year first';
            container.innerHTML = '<span class="htfp-chip htfp-placeholder">Select a year to continue</span>';
            return;
        }

        // Update hint based on mode
        if (hint) {
            hint.textContent = htfPendingMode === 'range'
                ? 'select From & To month (optional)'
                : 'pick any specific months';
        }

        // In Range mode expand to all years between the two selected
        let yearsForMonths;
        if (htfPendingMode === 'range' && htfPendingYears.length === 2) {
            const minY = Math.min(...htfPendingYears.map(Number));
            const maxY = Math.max(...htfPendingYears.map(Number));
            yearsForMonths = [];
            for (let y = minY; y <= maxY; y++) yearsForMonths.push(String(y));
        } else {
            yearsForMonths = htfPendingYears.slice();
        }

        const availableMonths = [...new Set(
            htfArchiveOptions
                .filter(o => yearsForMonths.includes(String(o.year)))
                .map(o => o.month)
        )].sort((a, b) => a - b);

        if (!availableMonths.length) {
            container.innerHTML = '<span class="htfp-chip htfp-placeholder">No months available</span>';
            return;
        }

        availableMonths.forEach(m => {
            const btn = document.createElement('button');
            btn.type        = 'button';
            btn.className   = 'htfp-chip' + (htfPendingMonths.includes(String(m)) ? ' htfp-selected' : '');
            btn.textContent = MONTH_SHORT[m];
            btn.dataset.val = String(m);
            btn.onclick     = () => htfToggleMonth(btn, String(m));
            container.appendChild(btn);
        });
    }

    /* ════════════════════════════
       TOGGLE HELPERS
    ════════════════════════════ */
    function htfToggleYear(btn, yr) {
        if (htfPendingYears.includes(yr)) {
            htfPendingYears = htfPendingYears.filter(y => y !== yr);
            btn.classList.remove('htfp-selected');
        } else {
            // Range mode: max 2 (From → To). Exact mode: unlimited picks.
            if (htfPendingMode === 'range' && htfPendingYears.length >= 2) {
                const evicted = htfPendingYears.shift();
                document.querySelector(`#htfpYearChips [data-val="${evicted}"]`)
                    ?.classList.remove('htfp-selected');
            }
            htfPendingYears.push(yr);
            btn.classList.add('htfp-selected');
        }
        htfPendingMonths = [];
        buildHtfMonthChips();
    }

    function htfToggleMonth(btn, m) {
        if (htfPendingMonths.includes(m)) {
            htfPendingMonths = htfPendingMonths.filter(x => x !== m);
            btn.classList.remove('htfp-selected');
        } else {
            // Range mode: max 2 (From → To). Exact mode: unlimited picks.
            if (htfPendingMode === 'range' && htfPendingMonths.length >= 2) {
                const evicted = htfPendingMonths.shift();
                document.querySelector(`#htfpMonthChips [data-val="${evicted}"]`)
                    ?.classList.remove('htfp-selected');
            }
            htfPendingMonths.push(m);
            btn.classList.add('htfp-selected');
        }
    }

    /* ════════════════════════════
       MODE TOGGLE (Range vs Exact)
    ════════════════════════════ */
    window.htfSetMode = function (mode) {
        htfPendingMode = mode;
        document.getElementById('htfModeRange')?.classList.toggle('mfp-mode-active', mode === 'range');
        document.getElementById('htfModeExact')?.classList.toggle('mfp-mode-active', mode === 'exact');
        const hint = document.getElementById('htfModeHint');
        if (hint) {
            hint.innerHTML = mode === 'range'
                ? 'Select <strong>From</strong> &amp; <strong>To</strong> year — all years &amp; months in between will be included'
                : 'Select up to <strong>2 specific years</strong> — then pick <strong>any months</strong> you want';
        }
        htfPendingYears  = [];
        htfPendingMonths = [];
        buildHtfYearChips();
        buildHtfMonthChips();
    };

    /* ════════════════════════════
       PANEL OPEN / CLOSE
    ════════════════════════════ */
    window.htfPanelToggle = function () {
        const panel = document.getElementById('htfFilterPanel');
        if (panel.classList.contains('htf-panel-open')) {
            htfPanelClose();
        } else {
            // Sync pending to applied state when reopening
            htfPendingYears  = htfAppliedYears.slice();
            htfPendingMonths = htfAppliedMonths.slice();
            htfPendingMode   = htfAppliedMode;
            document.getElementById('htfModeRange')?.classList.toggle('mfp-mode-active', htfPendingMode === 'range');
            document.getElementById('htfModeExact')?.classList.toggle('mfp-mode-active', htfPendingMode === 'exact');
            const htfModeHintEl = document.getElementById('htfModeHint');
            if (htfModeHintEl) {
                htfModeHintEl.innerHTML = htfPendingMode === 'range'
                    ? 'Select <strong>From</strong> &amp; <strong>To</strong> year — all years &amp; months in between will be included'
                    : 'Select up to <strong>2 specific years</strong> — then pick <strong>any months</strong> you want';
            }
            buildHtfYearChips();
            buildHtfMonthChips();
            panel.classList.add('htf-panel-open');
            document.getElementById('htfFilterTrigger').classList.add('htf-open');
        }
    };

    function htfPanelClose() {
        document.getElementById('htfFilterPanel')?.classList.remove('htf-panel-open');
        document.getElementById('htfFilterTrigger')?.classList.remove('htf-open');
    }

    /* ════════════════════════════
       APPLY
    ════════════════════════════ */
    window.htfPanelApply = function () {
        if (!htfPendingYears.length) return;
        htfAppliedYears  = htfPendingYears.slice();
        htfAppliedMonths = htfPendingMonths.slice();
        htfAppliedMode   = htfPendingMode;
        htfPanelClose();
        htfUpdateTriggerLabel();
        updateHardToFill(htfAppliedYears, htfAppliedMonths, htfAppliedMode);
    };

    /* ════════════════════════════
       RESET
    ════════════════════════════ */
    window.htfPanelReset = function () {
        htfPendingYears  = [];
        htfPendingMonths = [];
        htfAppliedYears  = [];
        htfAppliedMonths = [];
        htfPendingMode   = 'range';
        htfAppliedMode   = 'range';
        htfPanelClose();
        htfUpdateTriggerLabel();
        updateHardToFill([], [], 'range');
    };

    /* ════════════════════════════
       TRIGGER LABEL
    ════════════════════════════ */
    function htfUpdateTriggerLabel() {
        const trigger = document.getElementById('htfFilterTrigger');
        const text    = document.getElementById('htfTriggerText');
        if (!text) return;
        if (htfAppliedYears.length) {
            let yLabel;
            if (htfAppliedMode === 'range' && htfAppliedYears.length === 2) {
                const mn = Math.min(...htfAppliedYears.map(Number));
                const mx = Math.max(...htfAppliedYears.map(Number));
                yLabel = mn === mx ? String(mn) : `${mn} – ${mx}`;
            } else {
                yLabel = htfAppliedYears.join(' & ');
            }
            const mLabel = htfAppliedMonths.length === 0
                ? 'All Months'
                : (htfAppliedMode === 'range' && htfAppliedMonths.length === 2)
                    ? (() => {
                        const mn = Math.min(...htfAppliedMonths.map(Number));
                        const mx = Math.max(...htfAppliedMonths.map(Number));
                        return mn === mx ? MONTH_SHORT[mn] : `${MONTH_SHORT[mn]} – ${MONTH_SHORT[mx]}`;
                    })()
                    : htfAppliedMonths.map(m => MONTH_SHORT[Number(m)]).join(' & ');
            text.textContent = `${yLabel} — ${mLabel}`;
            trigger.classList.add('htf-active');
        } else {
            text.textContent = 'Filter by Period';
            trigger.classList.remove('htf-active');
        }
    }

    /* ════════════════════════════
       CORE FETCH (unchanged logic)
    ════════════════════════════ */
    async function updateHardToFill(years, months, mode) {
        const spinner   = document.getElementById('htfSpinner');
        const badge     = document.getElementById('htfArchiveBadge');
        const label     = document.getElementById('htfArchiveLabel');
        const banner    = document.getElementById('htfBanner');
        const rolesList = document.getElementById('htfRolesList');

        spinner.style.display = 'inline-block';

        try {
            const params = new URLSearchParams();
            if (years && years.length) {
                // Range mode: expand [2025, 2028] → 2025,2026,2027,2028
                let yearsToSend;
                if (mode === 'range' && years.length === 2) {
                    const minY = Math.min(...years.map(Number));
                    const maxY = Math.max(...years.map(Number));
                    yearsToSend = [];
                    for (let y = minY; y <= maxY; y++) yearsToSend.push(String(y));
                } else {
                    yearsToSend = years.slice();
                }
                yearsToSend.forEach(y => params.append('archive_years[]', y));

                // Range mode: expand [3, 10] → 3,4,5,6,7,8,9,10
                // Exact mode: send the specific months as-is
                if (months && months.length) {
                    let monthsToSend;
                    if (mode === 'range' && months.length === 2) {
                        const minM = Math.min(...months.map(Number));
                        const maxM = Math.max(...months.map(Number));
                        monthsToSend = [];
                        for (let m = minM; m <= maxM; m++) monthsToSend.push(String(m));
                    } else {
                        monthsToSend = months.slice();
                    }
                    monthsToSend.forEach(m => params.append('archive_months[]', m));
                }
            }

            const res  = await fetch(`/api/job-market/hard-to-fill-data?${params.toString()}`);
            const json = await res.json();

            // Update banner
            if (banner) {
                banner.className = `bg-blue-50 border-l-4 ${json.is_archive ? 'border-amber-400' : 'border-blue-500'} p-3 rounded-md`;
                banner.innerHTML = `
                    <div class="flex items-center">
                        <svg class="h-4 w-4 ${json.is_archive ? 'text-amber-500' : 'text-blue-500'} mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-semibold ${json.is_archive ? 'text-amber-900' : 'text-blue-900'}">${json.is_archive ? 'Archived Quarter' : 'Last 90 Days'}</p>
                            <p class="text-xs ${json.is_archive ? 'text-amber-700' : 'text-blue-700'}">${json.quarter_text}</p>
                        </div>
                    </div>`;
            }

            // Update roles list
            if (rolesList) {
                if (json.roles.length === 0) {
                    rolesList.innerHTML = '<p class="text-center text-gray-400 text-sm py-8">No roles found for this period.</p>';
                } else {
                    rolesList.innerHTML = '<div class="space-y-3">' +
                        json.roles.map(role => `
                            <div class="role-card border border-slate-200 rounded-lg overflow-hidden hover:border-blue-400 transition cursor-pointer"
                                 onclick="toggleRoleDetails(${role.submission_id}, ${role.index})">
                                <div class="p-3 bg-white hover:bg-slate-50 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="font-bold text-sm text-slate-800">${role.job_title}</p>
                                            <p class="text-xs text-gray-400 mt-1">Vacancy Duration: ${role.vacancy_duration}</p>
                                        </div>
                                        <svg class="expand-icon w-4 h-4 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="role-details hidden" id="role-details-${role.submission_id}-${role.index}">
                                    <div class="border-t border-slate-200 bg-slate-50 p-4">
                                        <div class="space-y-3 text-sm">
                                            <div>
                                                <span class="font-medium text-slate-600">Classification:</span>
                                                <p class="text-slate-800">${role.classification}</p>
                                            </div>
                                            ${role.difficulty_reasons.length ? `
                                            <div>
                                                <span class="font-medium text-slate-600">Difficulty Reasons:</span>
                                                <ul class="list-disc list-inside mt-1 text-slate-700 text-xs">
                                                    ${role.difficulty_reasons.map(r => `<li>${r}</li>`).join('')}
                                                </ul>
                                            </div>` : ''}
                                            ${role.tech_skills.length ? `
                                            <div>
                                                <span class="font-medium text-slate-600">Technical Skills Missing:</span>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    ${role.tech_skills.map(s => `<span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">${s}</span>`).join('')}
                                                </div>
                                            </div>` : ''}
                                            ${role.soft_skills.length ? `
                                            <div>
                                                <span class="font-medium text-slate-600">Soft Skills Missing:</span>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    ${role.soft_skills.map(s => `<span class="px-2 py-0.5 bg-pink-100 text-pink-700 rounded text-xs">${s}</span>`).join('')}
                                                </div>
                                            </div>` : ''}
                                            <div class="pt-2 border-t">
                                                <p class="text-xs text-slate-500"><strong>Sector:</strong> ${role.sector}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`
                        ).join('') + '</div>';
                }
            }

            // Badge
            const isArchive = json.is_archive;
            badge.style.display = isArchive ? 'flex' : 'none';
            if (label) label.textContent = isArchive ? `Viewing archived: ${json.archive_label}` : '';

        } catch (e) {
            console.error('Hard-to-fill update failed:', e);
        }

        spinner.style.display = 'none';
    }
})();
</script>

<script>
(function () {
    const MONTH_SHORT = ['','Jan','Feb','Mar','Apr','May','Jun',
                         'Jul','Aug','Sep','Oct','Nov','Dec'];
    const MONTH_FULL  = ['','January','February','March','April','May','June',
                         'July','August','September','October','November','December'];

    // Server-rendered fallback; overwritten by fresh API fetch on boot
    let matrixDateOptions = @json($matrix_date_options ?? []);

    // Pending selections (inside open panel, not yet applied)
    let pendingYears  = [];
    let pendingMonths = [];
    let pendingMode   = 'range'; // 'range' | 'exact'

    // Last applied selections (used for trigger label after Apply)
    let appliedYears  = [];
    let appliedMonths = [];
    let appliedMode   = 'range';

    /* ════════════════════════════════════════
       BOOT
    ════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', async function () {

        // Fetch fresh date options
        try {
            const res  = await fetch('/api/job-market/matrix-date-options');
            const json = await res.json();
            if (json.options && json.options.length) matrixDateOptions = json.options;
        } catch (e) {
            console.warn('Matrix date options fetch failed, using server fallback.');
        }

        buildYearChips();

        // Close panel when clicking outside wrapper
        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('matrixFilterWrapper');
            if (wrapper && !wrapper.contains(e.target)) mfpClose();
        });
    });

    /* ════════════════════════════════════════
       BUILD YEAR CHIPS
    ════════════════════════════════════════ */
    function buildYearChips() {
        const container = document.getElementById('mfpYearChips');
        const yearHint  = document.getElementById('mfpYearHint');
        if (!container) return;
        container.innerHTML = '';

        // Update year hint based on mode
        if (yearHint) {
            yearHint.textContent = pendingMode === 'range'
                ? 'select From & To'
                : 'pick any years';
        }

        const years = [...new Set(matrixDateOptions.map(o => String(o.year)))]
            .sort((a, b) => Number(b) - Number(a));

        if (!years.length) {
            container.innerHTML = '<span class="mfp-chip mfp-disabled mfp-placeholder">No data available</span>';
            return;
        }

        years.forEach(yr => {
            const btn = document.createElement('button');
            btn.type        = 'button';
            btn.className   = 'mfp-chip' + (pendingYears.includes(yr) ? ' mfp-selected' : '');
            btn.textContent = yr;
            btn.dataset.val = yr;
            btn.onclick     = () => toggleYear(btn, yr);
            container.appendChild(btn);
        });
    }

    /* ════════════════════════════════════════
       BUILD MONTH CHIPS
    ════════════════════════════════════════ */
    function buildMonthChips() {
        const container = document.getElementById('mfpMonthChips');
        const hint      = document.getElementById('mfpMonthHint');
        if (!container) return;
        container.innerHTML = '';

        if (!pendingYears.length) {
            if (hint) hint.textContent = 'select a year first';
            container.innerHTML = '<span class="mfp-chip mfp-placeholder">Select a year to see months</span>';
            return;
        }

        // In Range mode, expand pendingYears to cover all years in between
        // e.g. [2025, 2028] → [2025, 2026, 2027, 2028] so months from middle years show up
        let yearsForMonths;
        if (pendingMode === 'range' && pendingYears.length === 2) {
            const minY = Math.min(...pendingYears.map(Number));
            const maxY = Math.max(...pendingYears.map(Number));
            yearsForMonths = [];
            for (let y = minY; y <= maxY; y++) yearsForMonths.push(String(y));
        } else {
            yearsForMonths = pendingYears.slice();
        }

        // Months available for the full year range
        const available = new Set(
            matrixDateOptions
                .filter(o => yearsForMonths.includes(String(o.year)))
                .map(o => o.month)
        );

        if (hint) hint.textContent = pendingMode === 'range'
            ? 'select From & To month (optional)'
            : 'pick any specific months';

        // Always show all 12 slots; disable those with no data
        for (let m = 1; m <= 12; m++) {
            const btn = document.createElement('button');
            btn.type        = 'button';
            btn.dataset.val = String(m);

            if (available.has(m)) {
                btn.className   = 'mfp-chip' + (pendingMonths.includes(String(m)) ? ' mfp-selected' : '');
                btn.textContent = MONTH_SHORT[m];
                btn.onclick     = () => toggleMonth(btn, String(m));
            } else {
                btn.className   = 'mfp-chip mfp-disabled';
                btn.textContent = MONTH_SHORT[m];
            }
            container.appendChild(btn);
        }
    }

    /* ════════════════════════════════════════
       TOGGLE HELPERS
    ════════════════════════════════════════ */
    function toggleYear(btn, yr) {
        if (pendingYears.includes(yr)) {
            pendingYears = pendingYears.filter(y => y !== yr);
            btn.classList.remove('mfp-selected');
        } else {
            // Range mode: max 2 (From → To). Exact mode: unlimited picks.
            if (pendingMode === 'range' && pendingYears.length >= 2) {
                const evicted = pendingYears.shift();
                document.querySelector(`#mfpYearChips [data-val="${evicted}"]`)
                    ?.classList.remove('mfp-selected');
            }
            pendingYears.push(yr);
            btn.classList.add('mfp-selected');
        }
        // Reset months when year selection changes
        pendingMonths = [];
        buildMonthChips();
    }

    function toggleMonth(btn, m) {
        if (pendingMonths.includes(m)) {
            pendingMonths = pendingMonths.filter(x => x !== m);
            btn.classList.remove('mfp-selected');
        } else {
            // Range mode: max 2 (From → To). Exact mode: unlimited picks.
            if (pendingMode === 'range' && pendingMonths.length >= 2) {
                const evicted = pendingMonths.shift();
                document.querySelector(`#mfpMonthChips [data-val="${evicted}"]`)
                    ?.classList.remove('mfp-selected');
            }
            pendingMonths.push(m);
            btn.classList.add('mfp-selected');
        }
    }

    /* ════════════════════════════════════════
       MODE TOGGLE (Range vs Exact)
    ════════════════════════════════════════ */
    window.mfpSetMode = function (mode) {
        pendingMode = mode;
        document.getElementById('mfpModeRange')?.classList.toggle('mfp-mode-active', mode === 'range');
        document.getElementById('mfpModeExact')?.classList.toggle('mfp-mode-active', mode === 'exact');
        const hint = document.getElementById('mfpModeHint');
        if (hint) {
            hint.innerHTML = mode === 'range'
                ? 'Select <strong>From</strong> &amp; <strong>To</strong> year — all years &amp; months in between will be included'
                : 'Select up to <strong>2 specific years</strong> — then pick <strong>any months</strong> you want';
        }
        // Clear year/month selections when switching so the user starts fresh
        pendingYears  = [];
        pendingMonths = [];
        buildYearChips();
        buildMonthChips();
    };
    window.mfpToggle = function () {
        const panel = document.getElementById('matrixFilterPanel');
        if (panel.classList.contains('mfp-open')) {
            mfpClose();
        } else {
            // Sync pending selections from last applied state when reopening
            pendingYears  = appliedYears.slice();
            pendingMonths = appliedMonths.slice();
            pendingMode   = appliedMode;
            document.getElementById('mfpModeRange')?.classList.toggle('mfp-mode-active', pendingMode === 'range');
            document.getElementById('mfpModeExact')?.classList.toggle('mfp-mode-active', pendingMode === 'exact');
            const modeHint = document.getElementById('mfpModeHint');
            if (modeHint) {
                modeHint.innerHTML = pendingMode === 'range'
                    ? 'Select <strong>From</strong> &amp; <strong>To</strong> year — all years &amp; months in between will be included'
                    : 'Select up to <strong>2 specific years</strong> — then pick <strong>any months</strong> you want';
            }
            buildYearChips();
            buildMonthChips();
            panel.classList.add('mfp-open');
            document.getElementById('matrixFilterTrigger').classList.add('mft-open');
        }
    };

    function mfpClose() {
        document.getElementById('matrixFilterPanel')?.classList.remove('mfp-open');
        document.getElementById('matrixFilterTrigger')?.classList.remove('mft-open');
    }

    /* ════════════════════════════════════════
       APPLY
    ════════════════════════════════════════ */
    window.mfpApply = async function () {
        if (!pendingYears.length) {
            mfpReset();
            return;
        }
        appliedYears  = pendingYears.slice();
        appliedMonths = pendingMonths.slice();
        appliedMode   = pendingMode;
        mfpClose();
        updateTriggerLabel();
        await matrixFetch(appliedYears, appliedMonths);
    };

    /* ════════════════════════════════════════
       RESET
    ════════════════════════════════════════ */
    window.mfpReset = function () {
        pendingYears  = [];
        pendingMonths = [];
        appliedYears  = [];
        appliedMonths = [];
        pendingMode   = 'range';
        appliedMode   = 'range';
        mfpClose();
        updateTriggerLabel();
        window.matrixResultsData = window.matrixResultsDataOriginal.slice();
        matrixApplyToAlpine(false, false, '', window.matrixResultsDataOriginal.slice());
    };

    /* ════════════════════════════════════════
       TRIGGER LABEL
    ════════════════════════════════════════ */
    function updateTriggerLabel() {
        const trigger = document.getElementById('matrixFilterTrigger');
        const text    = document.getElementById('mfpTriggerText');
        if (!text) return;

        if (!appliedYears.length) {
            text.textContent = 'Filter by Period';
            trigger.classList.remove('mft-active');
        } else {
            const yLabel = (appliedMode === 'range' && appliedYears.length === 2)
                ? (() => { const mn = Math.min(...appliedYears.map(Number)), mx = Math.max(...appliedYears.map(Number)); return mn === mx ? String(mn) : `${mn} – ${mx}`; })()
                : appliedYears.join(' & ');
            const mLabel = appliedMonths.length === 0
                ? 'All Months'
                : (appliedMode === 'range' && appliedMonths.length === 2)
                    ? (() => {
                        const mn = Math.min(...appliedMonths.map(Number)), mx = Math.max(...appliedMonths.map(Number));
                        return mn === mx ? MONTH_SHORT[mn] : `${MONTH_SHORT[mn]} – ${MONTH_SHORT[mx]}`;
                    })()
                    : appliedMonths.map(m => MONTH_SHORT[Number(m)]).join(' & ');
            text.textContent = `${yLabel} — ${mLabel}`;
            trigger.classList.add('mft-active');
        }
    }

    /* ════════════════════════════════════════
       CORE FETCH
    ════════════════════════════════════════ */
    async function matrixFetch(years, months) {
        const spinner = document.getElementById('matrixSpinner');
        if (spinner) spinner.style.display = 'inline-block';

        try {
            const params = new URLSearchParams();

            // Range mode: expand e.g. [2026, 2028] → [2026, 2027, 2028]
            // Exact mode: send only the selected years as-is
            let yearsToFetch;
            if (appliedMode === 'range' && years.length === 2) {
                const minY = Math.min(...years.map(Number));
                const maxY = Math.max(...years.map(Number));
                yearsToFetch = [];
                for (let y = minY; y <= maxY; y++) yearsToFetch.push(String(y));
            } else {
                yearsToFetch = years.slice();
            }
            yearsToFetch.forEach(y => params.append('years[]', y));

            // Range mode: expand months e.g. [3, 10] → [3,4,5,6,7,8,9,10]
            // Exact mode: send the selected months as-is
            let monthsToFetch;
            if (appliedMode === 'range' && months.length === 2) {
                const minM = Math.min(...months.map(Number));
                const maxM = Math.max(...months.map(Number));
                monthsToFetch = [];
                for (let m = minM; m <= maxM; m++) monthsToFetch.push(String(m));
            } else {
                monthsToFetch = months.slice();
            }
            monthsToFetch.forEach(m => params.append('months[]', m));

            const res  = await fetch(`/api/job-market/matrix-data?${params}`);
            const json = await res.json();

            const formatted = (json.results || []).map(r => ({
                ...r,
                salary_range: typeof formatSalaryRange === 'function'
                    ? formatSalaryRange(r.salary_range)
                    : r.salary_range,
            }));

            window.matrixResultsData = formatted;

            const yLabel = (appliedMode === 'range' && years.length === 2)
                ? (() => { const mn = Math.min(...years.map(Number)), mx = Math.max(...years.map(Number)); return mn === mx ? String(mn) : `${mn} – ${mx}`; })()
                : years.join(' & ');
            // Range mode badge: "Jan – Dec", Exact mode: "Jan & Dec"
            const mLabel = months.length === 0
                ? 'All Months'
                : (appliedMode === 'range' && months.length === 2)
                    ? (() => {
                        const mn = Math.min(...months.map(Number)), mx = Math.max(...months.map(Number));
                        return mn === mx ? MONTH_FULL[mn] : `${MONTH_FULL[mn]} – ${MONTH_FULL[mx]}`;
                    })()
                    : months.map(m => MONTH_FULL[Number(m)]).join(' & ');

            matrixApplyToAlpine(true, true, `${yLabel} — ${mLabel}`, formatted);

        } catch (e) {
            console.error('Matrix filter fetch failed:', e);
        }

        if (spinner) spinner.style.display = 'none';
    }

    /* ════════════════════════════════════════
       PUSH INTO ALPINE
    ════════════════════════════════════════ */
    function matrixApplyToAlpine(filterActive, showAll, badgeText, data) {
        const scrollArea = document.getElementById('matrixScrollArea');
        if (scrollArea) scrollArea.style.maxHeight = showAll ? '85vh' : '600px';

        const badge = document.getElementById('matrixFilterBadge');
        if (badge) {
            badge.textContent = badgeText ? `Filtered: ${badgeText}` : '';
            badge.classList.toggle('hidden', !badgeText);
        }

        const alpineRoot = document.querySelector('#matrixScrollArea')?.closest('[x-data]');
        if (alpineRoot) {
            alpineRoot.dispatchEvent(new CustomEvent('matrix-filter-update', {
                bubbles: false,
                detail: { tableData: data, filterActive, showAll }
            }));
        }

        if (filterActive && scrollArea) {
            setTimeout(() => scrollArea.scrollTo({ top: 0, behavior: 'smooth' }), 120);
        }
    }
})();
</script>
</html>