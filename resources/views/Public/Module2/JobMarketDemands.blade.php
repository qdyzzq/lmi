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
        [x-cloak] {
            display: none !important;
        }

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
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Scroll indicator bounce animation */
        @keyframes bounce-custom {

            0%,
            100% {
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

        @media (max-width: 640px) {
            .chart-responsive {
                height: 280px;
            }
        }

        @media (min-width: 641px) and (max-width: 1023px) {
            .chart-responsive {
                height: 320px;
            }
        }

        @media (min-width: 1024px) {
            .chart-responsive {
                height: 360px;
            }
        }

        /* ── Mobile scroll hint for LMI matrix table ── */
        .table-scroll-hint {
            display: none;
        }

        @media (max-width: 767px) {
            .table-scroll-hint {
                display: flex;
            }
        }

        /* ── Matrix: hide table on mobile, show cards ── */
        .matrix-table-view {
            display: block;
        }

        .matrix-cards-view {
            display: none;
        }

        @media (max-width: 767px) {
            .matrix-table-view {
                display: none;
            }

            .matrix-cards-view {
                display: block;
            }
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
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

        .matrix-card-expand-btn:hover {
            background: #dbeafe;
        }

        .matrix-card-expanded {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid #f1f5f9;
            display: none;
        }

        .matrix-card-expanded.open {
            display: block;
        }

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
            background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.95));
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
            .pagination-page-numbers {
                display: none;
            }

            .pagination-controls {
                justify-content: space-between;
                width: 100%;
            }
        }

        /* ── Banner CTA: stack buttons on mobile ── */
        @media (max-width: 480px) {
            .cta-buttons {
                flex-direction: column;
                width: 100%;
            }

            .cta-buttons button {
                width: 100%;
                text-align: center;
            }
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

        #matrixFilterTrigger:hover {
            border-color: #93c5fd;
            color: #2563eb;
        }

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

        #matrixFilterTrigger.mft-open .mft-arrow {
            transform: rotate(180deg);
        }

        /* ── Sector Skill Gaps filter trigger (mirrors #matrixFilterTrigger) ── */
        #sectorFilterTrigger {
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

        #sectorFilterTrigger #sgpTriggerText {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 0;
        }

        #sectorFilterTrigger:hover {
            border-color: #93c5fd;
            color: #2563eb;
        }

        #sectorFilterTrigger.mft-active {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }

        #sectorFilterTrigger .mft-arrow {
            font-size: 0.6rem;
            opacity: 0.55;
            transition: transform 0.18s;
            margin-left: 0.1rem;
        }

        #sectorFilterTrigger.mft-open .mft-arrow {
            transform: rotate(180deg);
        }

        #sectorFilterPanel {
            position: absolute;
            top: calc(100% + 0.35rem);
            right: 0;
            z-index: 200;
            width: 300px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.875rem;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.10), 0 2px 6px rgba(0, 0, 0, 0.05);
            padding: 1rem;
            padding-bottom: 1.25rem;
            display: none;
            overflow: visible;
        }

        @media (max-width: 639px) {
            #sectorFilterPanel {
                right: auto;
                left: 0;
                width: calc(100vw - 3rem);
                max-width: 320px;
                padding-bottom: 1.5rem;
            }
        }

        #sectorFilterPanel.mfp-open {
            display: block;
            animation: mfpDrop 0.14s ease;
        }
        /* ── End Sector Skill Gaps filter trigger ── */

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
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.10), 0 2px 6px rgba(0, 0, 0, 0.05);
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
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Section ── */
        .mfp-section {
            margin-bottom: 0.8rem;
        }

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
        .mfp-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.3rem;
        }

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
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.10);
        }

        .mfp-mode-hint {
            font-size: 0.67rem;
            color: #9ca3af;
            margin-bottom: 0.45rem;
            line-height: 1.4;
        }

        .mfp-mode-hint strong {
            color: #6b7280;
            font-weight: 600;
        }

        /* ── Divider ── */
        .mfp-divider {
            height: 1px;
            background: #f3f4f6;
            margin: 0.75rem 0;
        }

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

        .mfp-btn-apply:hover {
            background: #1d4ed8;
        }

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

        .mfp-btn-reset:hover {
            background: #fee2e2;
        }

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

        #htfFilterTrigger:hover {
            border-color: #93c5fd;
            color: #2563eb;
        }

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

        #htfFilterTrigger.htf-open .htf-arrow {
            transform: rotate(180deg);
        }

        #htfFilterPanel {
            position: absolute;
            top: calc(100% + 0.35rem);
            left: 0;
            z-index: 60;
            width: 260px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.10), 0 2px 6px rgba(0, 0, 0, 0.05);
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
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .htfp-section {
            margin-bottom: 0.7rem;
        }

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

        .htfp-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.28rem;
        }

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

        .htfp-divider {
            height: 1px;
            background: #f3f4f6;
            margin: 0.65rem 0;
        }

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

        .htfp-btn-apply:hover {
            background: #1d4ed8;
        }

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

        .htfp-btn-reset:hover {
            background: #fee2e2;
        }
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
                <img src="{{ asset('images/labordemand.JPG') }}" alt="Job Market Background"
                    class="w-full h-full object-cover object-center">
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
                        Regional Labor Market Information & Trends
                    </p>
                </div>
            </div>

            <div
                class="absolute bottom-6 sm:bottom-16 md:bottom-24 lg:bottom-32 left-1/2 transform -translate-x-1/2 z-20 animate-bounce">
                <a href="#job-market-section" class="flex flex-col items-center cursor-pointer group"
                    onclick="event.preventDefault(); document.getElementById('job-market-section').scrollIntoView({ behavior: 'smooth', block: 'start' });">
                    <svg class="w-8 h-8 text-white group-hover:text-blue-300 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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

                    <div
                        class="bg-slate-700 rounded-xl p-6 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="p-2 bg-emerald-500/20 rounded-lg text-emerald-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m11 17 2 2a1 1 0 1 0 3-3" />
                                    <path
                                        d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4" />
                                    <path d="m21 3 1 11h-2" />
                                    <path d="M3 3 2 14l6.5 6.5a1 1 0 1 0 3-3" />
                                    <path d="M3 4h8" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold">Help us map the future of Davao's workforce.</h2>
                                <p class="text-sm text-slate-400 max-w-xl">Official data lags behind real-time market
                                    needs. Help us bridge the gap by identifying hard-to-fill roles and critical skill
                                    shortages.</p>
                            </div>
                        </div>
                        <div class="flex gap-3 cta-buttons">

                            <button id="show-lmi-matrix-btn"
                                class="bg-emerald-500 border border-emerald-500 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-500/20 transition">
                                Submit Labor Information
                            </button>
                        </div>
                    </div>

                    <div class="mb-8">

                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden min-h-[450px]">
                            <div class="p-6 pb-4">
                                <div class="flex justify-between mb-3">
                                    <div>
                                        <h3 class="font-bold text-gray-800">Hard-to-Fill Roles / Skill Requirements</h3>
                                        <p class="text-xs text-gray-500 mt-1">Jobs that are consistently difficult to
                                            recruit for</p>
                                    </div>
                                    <span class="text-gray-300 cursor-help" title="Click to expand details">ⓘ</span>
                                </div>
                                <div class="mb-3">
                                    <div class="flex items-center gap-2 flex-wrap">

                                        <div class="relative" id="htfFilterWrapper">

                                            <button id="htfFilterTrigger" type="button" onclick="htfPanelToggle()">
                                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M3 4h18M7 12h10M11 20h2" />
                                                </svg>
                                                <span id="htfTriggerText">Filter by Period</span>
                                                <span class="htf-arrow">▾</span>
                                            </button>

                                            <div id="htfFilterPanel">
                                                <div class="htfp-section">
                                                    <div class="htfp-section-head">
                                                        <span class="htfp-section-title">Year</span>
                                                        <span class="htfp-section-hint" id="htfpYearHint">select From
                                                            & To</span>
                                                    </div>
                                                    <div class="mfp-mode-toggle">
                                                        <button type="button" class="mfp-mode-btn mfp-mode-active"
                                                            id="htfModeRange"
                                                            onclick="htfSetMode('range')">Range</button>
                                                        <button type="button" class="mfp-mode-btn" id="htfModeExact"
                                                            onclick="htfSetMode('exact')">Exact</button>
                                                    </div>
                                                    <p class="mfp-mode-hint" id="htfModeHint">Select
                                                        <strong>From</strong> &amp; <strong>To</strong> year — all years
                                                        &amp; months in between will be included
                                                    </p>
                                                    <div class="htfp-chips" id="htfpYearChips">
                                                        <span class="htfp-chip htfp-placeholder">No archived
                                                            data</span>
                                                    </div>
                                                </div>

                                                <div class="htfp-divider"></div>
                                                <div class="htfp-section">
                                                    <div class="htfp-section-head">
                                                        <span class="htfp-section-title">Month</span>
                                                        <span class="htfp-section-hint"
                                                            id="htfpMonthHint">optional</span>
                                                    </div>
                                                    <div class="htfp-chips" id="htfpMonthChips">
                                                        <span class="htfp-chip htfp-placeholder">Select a year to
                                                            continue</span>
                                                    </div>
                                                </div>

                                                <div class="htfp-footer">
                                                    <button class="htfp-btn-apply" type="button"
                                                        onclick="htfPanelApply()">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Apply
                                                    </button>
                                                    <button class="htfp-btn-reset" type="button"
                                                        onclick="htfPanelReset()">Reset</button>
                                                </div>
                                            </div>
                                        </div>
                                        <svg id="htfSpinner" class="w-4 h-4 text-blue-400 animate-spin"
                                            fill="none" viewBox="0 0 24 24" style="display:none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z">
                                            </path>
                                        </svg>

                                    </div>
                                    <p id="htfArchiveBadge"
                                        class="mt-2 text-xs text-amber-700 font-medium items-center gap-1"
                                        style="display:{{ ($htf_is_archive ?? false) ? 'flex' : 'none' }}">
                                        <svg class="w-3 h-3 inline-block flex-shrink-0 mr-1" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                        <span id="htfArchiveLabel">{{ ($htf_is_archive ?? false) ? 'Viewing archived: ' . $htf_archive_label : '' }}</span>
                                    </p>
                                </div>
                                @if (isset($quarter_info))
                                    <div id="htfBanner" class="bg-blue-50 border-l-4 {{ ($htf_is_archive ?? false) ? 'border-amber-400' : 'border-blue-500' }} p-3 rounded-md">
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 {{ ($htf_is_archive ?? false) ? 'text-amber-500' : 'text-blue-500' }} mr-2 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <div>
                                                <p class="text-xs font-semibold {{ ($htf_is_archive ?? false) ? 'text-amber-900' : 'text-blue-900' }}">{{ ($htf_is_archive ?? false) ? 'Archived Quarter' : 'Last 90 Days' }}</p>
                                                <p class="text-xs {{ ($htf_is_archive ?? false) ? 'text-amber-700' : 'text-blue-700' }}">{{ $quarter_info['display_text'] }}
                                                </p>
                                                @unless ($htf_is_archive ?? false)
                                                    <p class="text-xs font-semibold text-amber-700">Some data are archived and are not displayed by default. Use the Year or Month filter to view archived records.</p>
                                                @endunless
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if (isset($groupedRoles) && count($groupedRoles) > 0)
                                <div id="htfRolesList" class="max-h-96 overflow-y-auto px-6 pb-6">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 items-start">
                                        @foreach ($groupedRoles as $normalizedTitle => $roleGroup)
                                            @foreach ($roleGroup as $item)
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
                                                                <p class="font-bold text-sm text-slate-800">
                                                                    {{ $role->formatted_job_title }}</p>
                                                                <p class="text-xs text-gray-400 mt-1">Vacancy Duration:
                                                                    {{ $role->vacancy_duration }}</p>
                                                            </div>

                                                            <svg class="expand-icon w-4 h-4 text-slate-400 transition-transform"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </div>
                                                    </div>

                                                    <div class="role-details hidden"
                                                        id="role-details-{{ $submission->id }}-{{ $index }}">
                                                        <div class="border-t border-slate-200 bg-slate-50 p-4">
                                                            <div class="space-y-3 text-sm">
                                                                <div>
                                                                    <span
                                                                        class="font-medium text-slate-600">Classification:</span>
                                                                    <p class="text-slate-800">
                                                                        {{ $role->job_classification }}</p>
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

                                                                @if (count($reasons) > 0)
                                                                    <div>
                                                                        <span
                                                                            class="font-medium text-slate-600">Difficulty
                                                                            Reasons:</span>
                                                                        <ul
                                                                            class="list-disc list-inside mt-1 text-slate-700 text-xs">
                                                                            @foreach ($reasons as $reason)
                                                                                @if (is_array($reason))
                                                                                    @foreach ($reason as $item)
                                                                                        @if (!empty($item))
                                                                                            <li>{{ $item }}
                                                                                            </li>
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
                                                                        $techSkills =
                                                                            json_decode($techSkills, true) ?? [];
                                                                    }
                                                                    if (!is_array($techSkills)) {
                                                                        $techSkills = [];
                                                                    }
                                                                @endphp

                                                                @if (count($techSkills) > 0)
                                                                    <div>
                                                                        <span
                                                                            class="font-medium text-slate-600">Technical
                                                                            Skills Missing:</span>
                                                                        <div class="flex flex-wrap gap-1 mt-1">
                                                                            @foreach ($techSkills as $skill)
                                                                                @if (!empty($skill))
                                                                                    <span
                                                                                        class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">{{ strtoupper($skill) }}</span>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @php
                                                                    $softSkills = $role->soft_skills_missing;
                                                                    if (is_string($softSkills)) {
                                                                        $softSkills =
                                                                            json_decode($softSkills, true) ?? [];
                                                                    }
                                                                    if (!is_array($softSkills)) {
                                                                        $softSkills = [];
                                                                    }
                                                                @endphp

                                                                @if (count($softSkills) > 0)
                                                                    <div>
                                                                        <span class="font-medium text-slate-600">Soft
                                                                            Skills Missing:</span>
                                                                        <div class="flex flex-wrap gap-1 mt-1">
                                                                            @foreach ($softSkills as $skill)
                                                                                @if (!empty($skill))
                                                                                    <span
                                                                                        class="px-2 py-0.5 bg-pink-100 text-pink-700 rounded text-xs">{{ strtoupper($skill) }}</span>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                <div class="pt-2 border-t">
                                                                    <p class="text-xs text-slate-500">
                                                                        <strong>Sector:</strong>
                                                                        {{ $submission->industry_sector }}
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
                                        @foreach ($approvedSubmissions as $submission)
                                            @foreach ($submission->hardToFillRoles as $index => $role)
                                                <div class="role-card border border-slate-200 rounded-lg overflow-hidden hover:border-blue-400 transition cursor-pointer"
                                                    onclick="toggleRoleDetails({{ $submission->id }}, {{ $index }})">

                                                    <div class="p-3 bg-white hover:bg-slate-50 transition">
                                                        <div class="flex items-start justify-between">
                                                            <div class="flex-1">
                                                                <p class="font-bold text-sm text-slate-800">
                                                                    {{ $role->formatted_job_title }}</p>
                                                                <p class="text-xs text-gray-400 mt-1">
                                                                    {{ $role->vacancy_duration }}</p>
                                                            </div>

                                                            <svg class="expand-icon w-4 h-4 text-slate-400 transition-transform"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </div>
                                                    </div>

                                                    <div class="role-details hidden"
                                                        id="role-details-{{ $submission->id }}-{{ $index }}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="px-6 pb-6">
                                    <div class="space-y-5">
                                        @foreach ($hard_to_fill as $job)
                                            <div class="flex justify-between items-center">
                                                <div class="space-y-1">
                                                    <p class="font-bold text-sm text-slate-800">{{ $job['role'] }}
                                                    </p>
                                                    <p
                                                        class="text-[10px] text-gray-400 flex items-center gap-1 uppercase">
                                                        🕒 Bottleneck: {{ $job['bottleneck'] }}
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-red-500 font-bold text-xs">{{ $job['days'] }} days
                                                    </p>
                                                    <p class="text-[9px] text-gray-300">({{ $job['year'] }})</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div id="chartModal"
                        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-2 sm:p-4"
                        onclick="closeChart()">
                        <div class="bg-white rounded-xl shadow-2xl w-full h-full sm:w-11/12 sm:h-5/6 p-4 sm:p-6 relative flex flex-col"
                            onclick="event.stopPropagation()">
                            <div class="flex justify-between items-start gap-2 mb-3 sm:mb-4 flex-shrink-0">
                                <h3 class="text-base sm:text-xl font-bold text-gray-800 leading-tight">High-Volume Job
                                    Titles - Expanded View</h3>
                                <button onclick="closeChart()"
                                    class="flex-shrink-0 p-2 hover:bg-gray-100 rounded-lg transition">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
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
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                            <div>
                            <h3 class="font-bold text-lg">Critical Skill Gaps Per Sector</h3>
                            <p class="text-xs font-bold text-amber-700">Some data are archived and are not displayed by default. Use the Year or Month filter to view archived records.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="relative" id="sectorFilterWrapper">
                                    <button id="sectorFilterTrigger" type="button" onclick="sgpToggle()">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M3 4h18M7 12h10M11 20h2" />
                                        </svg>
                                        <span id="sgpTriggerText">Filter by Period</span>
                                        <span class="mft-arrow">▾</span>
                                    </button>
                                    <div id="sectorFilterPanel">
                                        <div class="mfp-section">
                                            <div class="mfp-section-head">
                                                <span class="mfp-section-title">Year</span>
                                                <span class="mfp-section-hint" id="sgpYearHint">select From &
                                                    To</span>
                                            </div>
                                            <div class="mfp-mode-toggle">
                                                <button type="button" class="mfp-mode-btn mfp-mode-active"
                                                    id="sgpModeRange" onclick="sgpSetMode('range')">Range</button>
                                                <button type="button" class="mfp-mode-btn" id="sgpModeExact"
                                                    onclick="sgpSetMode('exact')">Exact</button>
                                            </div>
                                            <p class="mfp-mode-hint" id="sgpModeHint">Select <strong>From</strong>
                                                &amp; <strong>To</strong> year — all years &amp; months in between will
                                                be included</p>
                                            <div class="mfp-chips" id="sgpYearChips">
                                            </div>
                                        </div>

                                        <div class="mfp-divider"></div>
                                        <div class="mfp-section">
                                            <div class="mfp-section-head">
                                                <span class="mfp-section-title">Month</span>
                                                <span class="mfp-section-hint" id="sgpMonthHint">select a year
                                                    first</span>
                                            </div>
                                            <div class="mfp-chips" id="sgpMonthChips">
                                                <span class="mfp-chip mfp-placeholder">Select a year to see
                                                    months</span>
                                            </div>
                                        </div>
                                        <div class="mfp-footer">
                                            <button class="mfp-btn-apply" type="button" onclick="sgpApply()">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Apply Filter
                                            </button>
                                            <button class="mfp-btn-reset" type="button"
                                                onclick="sgpReset()">Reset</button>
                                        </div>
                                    </div>
                                </div>
                                <svg id="sectorSpinner" class="w-5 h-5 text-blue-400 animate-spin flex-shrink-0"
                                    fill="none" viewBox="0 0 24 24" style="display:none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="mb-8 pb-5 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <button id="filter-left"
                                    class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>

                                <div id="sector-filter-scroll" class="flex gap-2 overflow-x-auto flex-1"
                                    style="scrollbar-width:none; -webkit-overflow-scrolling:touch;">
                                    <style>
                                        #sector-filter-scroll::-webkit-scrollbar {
                                            display: none;
                                        }
                                    </style>

                                    <button onclick="filterSkills('All')"
                                        class="sector-tab flex-shrink-0 px-5 py-2 text-xs font-bold rounded-xl transition-all whitespace-nowrap bg-gray-900 text-white shadow-sm"
                                        data-sector="All">
                                        All Sectors
                                    </button>
                                    @foreach ($sectors as $sector)
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-12">

                            <div class="md:border-r border-gray-200 md:pr-6">
                                <h4 class="text-xs font-bold text-gray-400 mb-4 uppercase bg-white pb-2">
                                    In demand Technical Skills
                                </h4>
                                <div class="skills-scroll-wrapper" id="tech-skills-scroll-wrapper">
                                    <div class="flex flex-wrap gap-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar"
                                        id="tech-skills-container">
                                        @foreach ($tech_skills as $skill)
                                            <div class="skill-tag tech-skill bg-blue-100 text-gray-800 font-semibold px-3 py-2 rounded-lg text-sm h-fit flex flex-col gap-0.5"
                                                data-sector="{{ $skill['sector'] }}">
                                                <div class="flex items-center gap-1">
                                                    {{ $skill['name'] }}
                                                    @if (isset($skill['count']) && $skill['count'] > 1)
                                                        <span
                                                            class="px-1.5 py-0.5 bg-blue-200 rounded-full text-[9px] font-bold">{{ $skill['count'] }}×</span>
                                                    @endif
                                                </div>
                                                <span
                                                    class="text-[11px] opacity-60 font-normal">({{ $skill['sector'] }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <p class="skills-scroll-hint font-bold" id="tech-scroll-hint">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                    Scroll to see more
                                </p>
                            </div>
                            <div class="md:pl-6">
                                <h4 class="text-xs font-bold text-gray-400 mb-4 uppercase bg-white pb-2">
                                    In demand Soft Skills
                                </h4>
                                <div class="skills-scroll-wrapper" id="soft-skills-scroll-wrapper">
                                    <div class="flex flex-wrap gap-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar"
                                        id="soft-skills-container">
                                        @foreach ($soft_skills as $skill)
                                            <div class="skill-tag soft-skill bg-red-100 text-gray-800 font-semibold px-3 py-2 rounded-lg text-sm h-fit flex flex-col gap-0.5"
                                                data-sector="{{ $skill['sector'] }}">
                                                <div class="flex items-center gap-1">
                                                    {{ $skill['name'] }}
                                                    @if (isset($skill['count']) && $skill['count'] > 1)
                                                        <span
                                                            class="px-1.5 py-0.5 bg-red-200 rounded-full text-[9px] font-bold">{{ $skill['count'] }}×</span>
                                                    @endif
                                                </div>
                                                <span
                                                    class="text-[11px] opacity-60 font-normal">({{ $skill['sector'] }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <p class="skills-scroll-hint font-bold" id="soft-scroll-hint">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
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
                            // window.matrixResultsData is set by the JS file inside DOMContentLoaded,
                            // which may run AFTER Alpine boots. Seed directly from the raw Blade data
                            // so the table is never empty on first render.
                            const raw = (window._jobMarketData?.matrixResults || []);
                            this.tableData = raw.map(r => ({
                                ...r,
                                salary_range: r.salary_range ?? ''
                            }));
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
">
                        <div
                            class="p-4 sm:p-6 border-b flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 bg-gradient-to-r from-gray-50 to-white overflow-visible rounded-t-2xl">
                            <h3 class="font-bold text-gray-900 flex items-center gap-3 text-lg">
                                <svg class="w-6 h-6 text-gray-700 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M3 14h18M10 4v16M3 4h18a1 1 0 011 1v14a1 1 0 01-1 1H3a1 1 0 01-1-1V5a1 1 0 011-1z" />
                                </svg>
                                <span>Critical Skills Requirements</span>
                            </h3>
                            <div class="flex flex-wrap items-center gap-2">
                                <div class="relative" id="matrixFilterWrapper">
                                    <button id="matrixFilterTrigger" type="button" onclick="mfpToggle()">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M3 4h18M7 12h10M11 20h2" />
                                        </svg>
                                        <span id="mfpTriggerText">Filter by Period</span>
                                        <span class="mft-arrow">▾</span>
                                    </button>
                                    <div id="matrixFilterPanel">
                                        <div class="mfp-section">
                                            <div class="mfp-section-head">
                                                <span class="mfp-section-title">Year</span>
                                                <span class="mfp-section-hint" id="mfpYearHint">select From &
                                                    To</span>
                                            </div>
                                            <div class="mfp-mode-toggle">
                                                <button type="button" class="mfp-mode-btn mfp-mode-active"
                                                    id="mfpModeRange" onclick="mfpSetMode('range')">Range</button>
                                                <button type="button" class="mfp-mode-btn" id="mfpModeExact"
                                                    onclick="mfpSetMode('exact')">Exact</button>
                                            </div>
                                            <p class="mfp-mode-hint" id="mfpModeHint">Select <strong>From</strong>
                                                &amp; <strong>To</strong> year — all years &amp; months in between will
                                                be included</p>
                                            <div class="mfp-chips" id="mfpYearChips">
                                            </div>
                                        </div>

                                        <div class="mfp-divider"></div>
                                        <div class="mfp-section">
                                            <div class="mfp-section-head">
                                                <span class="mfp-section-title">Month</span>
                                                <span class="mfp-section-hint" id="mfpMonthHint">select a year
                                                    first</span>
                                            </div>
                                            <div class="mfp-chips" id="mfpMonthChips">
                                                <span class="mfp-chip mfp-placeholder">Select a year to see
                                                    months</span>
                                            </div>
                                        </div>
                                        <div class="mfp-footer">
                                            <button class="mfp-btn-apply" type="button" onclick="mfpApply()">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Apply Filter
                                            </button>
                                            <button class="mfp-btn-reset" type="button"
                                                onclick="mfpReset()">Reset</button>
                                        </div>
                                    </div>
                                </div>
                                <svg id="matrixSpinner" class="w-5 h-5 text-blue-400 animate-spin flex-shrink-0"
                                    fill="none" viewBox="0 0 24 24" style="display:none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>

                                <button id="exportLMIMatrixBtn"
                                    class="text-emerald-600 border border-emerald-200 bg-white px-4 py-2 sm:px-5 sm:py-2.5 rounded-lg text-sm font-semibold hover:bg-emerald-50 transition-all shadow-sm hover:shadow whitespace-nowrap">
                                    Export Analysis
                                </button>
                            </div>
                        </div>

                        @if (count($matrix_results) > 0)
                            <div class="overflow-hidden rounded-b-2xl">

                                {{-- ── MOBILE CARD VIEW (shown on < 768px) ── --}}
                                <div class="matrix-cards-view px-4 py-4 bg-gray-50 space-y-3"
                                    id="matrixCardsContainer">
                                    <template x-for="(result, index) in paginatedData" :key="'card-' + index">
                                        <div class="matrix-card" :class="openItem === index ? 'is-open' : ''">
                                            <div class="matrix-card-header">
                                                <p class="matrix-card-title" x-text="result.role"></p>
                                                <span
                                                    class="px-2.5 py-1 rounded-full text-xs font-bold whitespace-nowrap flex-shrink-0"
                                                    :class="{
                                                        'bg-red-50 text-red-700 border border-red-200': result
                                                            .impact === 'High',
                                                        'bg-green-50 text-green-700 border border-green-200': result
                                                            .impact === 'Low',
                                                        'bg-amber-50 text-amber-700 border border-amber-200': result
                                                            .impact === 'Medium' || !result.impact
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
                                                        <template
                                                            x-if="result.has_technical_checkbox || result.has_soft_checkbox">
                                                            <span>
                                                                <template x-if="result.has_technical_checkbox">
                                                                    <span
                                                                        x-text="(result.hard_skills && result.hard_skills.length > 0) ? result.hard_skills.length + ' Technical Skill' + (result.hard_skills.length > 1 ? 's' : '') : 'Technical Skills'"></span>
                                                                </template>
                                                                <template
                                                                    x-if="result.has_technical_checkbox && result.has_soft_checkbox">
                                                                    <span> · </span>
                                                                </template>
                                                                <template x-if="result.has_soft_checkbox">
                                                                    <span
                                                                        x-text="(result.soft_skills && result.soft_skills.length > 0) ? result.soft_skills.length + ' Soft Skill' + (result.soft_skills.length > 1 ? 's' : '') : 'Soft Skills'"></span>
                                                                </template>
                                                            </span>
                                                        </template>
                                                        <template
                                                            x-if="!result.has_technical_checkbox && !result.has_soft_checkbox">
                                                            <span class="text-gray-400 italic">None specified</span>
                                                        </template>
                                                    </p>
                                                </div>
                                            </div>
                                            <template
                                                x-if="(result.hard_skills && result.hard_skills.length > 0) || (result.soft_skills && result.soft_skills.length > 0)">
                                                <button class="matrix-card-expand-btn"
                                                    @click="openItem = openItem === index ? null : index">
                                                    <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                                        :class="openItem === index ? 'rotate-180' : ''" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                    <span
                                                        x-text="openItem === index ? 'Hide details' : 'View skill details'"></span>
                                                </button>
                                            </template>
                                            <div class="matrix-card-expanded"
                                                :class="openItem === index ? 'open' : ''">
                                                <template x-if="result.hard_skills && result.hard_skills.length > 0">
                                                    <div class="mb-3">
                                                        <p class="matrix-card-field-label mb-1">Missing Technical
                                                            Skills</p>
                                                        <div>
                                                            <template x-for="skill in result.hard_skills"
                                                                :key="skill.name || skill">
                                                                <span class="matrix-skill-tag"
                                                                    x-text="skill.name || skill"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template x-if="result.soft_skills && result.soft_skills.length > 0">
                                                    <div>
                                                        <p class="matrix-card-field-label mb-1">Missing Soft Skills</p>
                                                        <div>
                                                            <template x-for="skill in result.soft_skills"
                                                                :key="skill.name || skill">
                                                                <span class="matrix-skill-tag"
                                                                    x-text="skill.name || skill"></span>
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
                                    <div
                                        class="table-scroll-hint items-center gap-2 px-4 py-2 bg-blue-50 border-b border-blue-100 text-xs text-blue-600 font-medium">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                        Scroll horizontally to see all columns
                                    </div>
                                    <div class="overflow-x-auto">
                                        <div class="min-w-[860px]">
                                            <div
                                                class="sticky top-0 z-20 bg-slate-800 border-b border-gray-700 shadow-md">
                                                <div
                                                    class="grid grid-cols-12 gap-3 px-4 sm:px-8 py-4 items-center lmi-row-grid">
                                                    <div class="col-span-3 flex items-center justify-start">
                                                        <span
                                                            class="text-s font-small font-bold text-white uppercase tracking-wider">Job
                                                            Title / Role</span>
                                                    </div>
                                                    <div class="col-span-2 flex items-center justify-start">
                                                        <span
                                                            class="text-s font-small font-bold text-white uppercase tracking-wider">Sector</span>
                                                    </div>
                                                    <div class="col-span-3 flex items-center justify-center">
                                                        <span
                                                            class="text-s font-small font-bold text-white uppercase tracking-wider">Missing
                                                            Skills / Competency</span>
                                                    </div>
                                                    <div class="col-span-2 flex items-center justify-center">
                                                        <span
                                                            class="text-s font-small font-bold text-white uppercase tracking-wider">Salary
                                                            Range</span>
                                                    </div>
                                                    <div class="col-span-2 flex items-center justify-center">
                                                        <span
                                                            class="text-s font-small font-bold text-white uppercase tracking-wider text-center leading-tight">Impact</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="matrixScrollArea"
                                                class="max-h-[600px] overflow-y-auto bg-gray-50">
                                                <div class="divide-y divide-gray-200">
                                                    <template x-for="(result, index) in paginatedData"
                                                        :key="index">
                                                        <div class="bg-white hover:bg-gray-50 transition-all duration-200 border-l-4"
                                                            :class="openItem === index ? 'border-l-gray-500 shadow-md' :
                                                                'border-l-transparent'">
                                                            <div @click="(result.hard_skills && result.hard_skills.length > 0) || (result.soft_skills && result.soft_skills.length > 0) ? (openItem = openItem === index ? null : index) : null"
                                                                class="grid grid-cols-12 gap-3 px-4 sm:px-8 py-4 sm:py-6 items-center lmi-row-grid"
                                                                :class="((result.hard_skills && result.hard_skills.length >
                                                                    0) || (result.soft_skills && result.soft_skills
                                                                    .length > 0)) ? 'cursor-pointer' :
                                                                'cursor-default'">

                                                                <div
                                                                    class="col-span-3 flex items-center justify-start">
                                                                    <h4 class="font-bold text-gray-900 text-base"
                                                                        x-text="result.role"></h4>
                                                                </div>

                                                                <div
                                                                    class="col-span-2 flex items-center justify-start">
                                                                    <p class="text-xs font-bold text-gray-700 uppercase tracking-wide leading-relaxed"
                                                                        x-text="result.sector"></p>
                                                                </div>

                                                                <div
                                                                    class="col-span-3 flex items-center justify-center">
                                                                    <div class="flex flex-col gap-1"
                                                                        style="min-width: 140px;">

                                                                        <div class="flex items-center gap-2"
                                                                            x-show="result.has_technical_checkbox">
                                                                            <span
                                                                                class="text-gray-400 font-medium text-xs">•</span>
                                                                            <span class="text-sm text-gray-700">
                                                                                <template
                                                                                    x-if="result.hard_skills && result.hard_skills.length > 0">
                                                                                    <span><span class="font-bold"
                                                                                            x-text="result.hard_skills.length"></span>
                                                                                        <span
                                                                                            class="font-bold">Technical
                                                                                            Skill</span><span
                                                                                            class="font-bold"
                                                                                            x-show="result.hard_skills.length > 1">s</span></span>
                                                                                </template>
                                                                                <template
                                                                                    x-if="!result.hard_skills || result.hard_skills.length === 0">
                                                                                    <span
                                                                                        class="font-semibold">Technical
                                                                                        Skills</span>
                                                                                </template>
                                                                            </span>
                                                                        </div>

                                                                        <div class="flex items-center gap-2"
                                                                            x-show="result.has_soft_checkbox">
                                                                            <span
                                                                                class="text-gray-400 font-medium text-xs">•</span>
                                                                            <span class="text-sm text-gray-700">
                                                                                <template
                                                                                    x-if="result.soft_skills && result.soft_skills.length > 0">
                                                                                    <span><span class="font-bold"
                                                                                            x-text="result.soft_skills.length"></span>
                                                                                        <span class="font-bold">Soft
                                                                                            Skill</span><span
                                                                                            class="font-bold"
                                                                                            x-show="result.soft_skills.length > 1">s</span></span>
                                                                                </template>
                                                                                <template
                                                                                    x-if="!result.soft_skills || result.soft_skills.length === 0">
                                                                                    <span class="font-semibold">Soft
                                                                                        Skills</span>
                                                                                </template>
                                                                            </span>
                                                                        </div>

                                                                        <template
                                                                            x-if="!result.has_technical_checkbox && !result.has_soft_checkbox">
                                                                            <span
                                                                                class="text-xs text-gray-400 italic">No
                                                                                skills specified</span>
                                                                        </template>

                                                                        <span
                                                                            class="text-xs text-gray-400 italic mt-0.5"
                                                                            x-show="openItem !== index && ((result.hard_skills && result.hard_skills.length > 0) || (result.soft_skills && result.soft_skills.length > 0))">
                                                                            Click to view details
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="col-span-2 flex items-center justify-center">
                                                                    <div class="flex flex-col">
                                                                        <template
                                                                            x-if="result.salary_range && result.salary_range !== 'Not specified'">
                                                                            <span
                                                                                class="text-sm font-semibold text-gray-900"
                                                                                x-text="result.salary_range"></span>
                                                                        </template>
                                                                        <template
                                                                            x-if="!result.salary_range || result.salary_range === 'Not specified'">
                                                                            <span
                                                                                class="text-xs text-gray-400 italic">Not
                                                                                specified</span>
                                                                        </template>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="col-span-2 flex items-center justify-center gap-1">
                                                                    <span
                                                                        class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-center shadow-sm whitespace-nowrap"
                                                                        :class="{
                                                                            'bg-red-50 text-red-700 border border-red-200': result
                                                                                .impact === 'High',
                                                                            'bg-green-50 text-green-700 border border-green-200': result
                                                                                .impact === 'Low',
                                                                            'bg-amber-50 text-amber-700 border border-amber-200': result
                                                                                .impact === 'Medium' || !result
                                                                                .impact
                                                                        }"
                                                                        x-text="result.impact || 'Medium'">
                                                                    </span>
                                                                    <svg class="w-3.5 h-3.5 flex-shrink-0 transition-all duration-300"
                                                                        :class="[
                                                                            openItem === index ?
                                                                            'rotate-180 text-gray-600' :
                                                                            'text-gray-400',
                                                                            ((result.hard_skills && result.hard_skills
                                                                                .length > 0) || (result
                                                                                .soft_skills && result.soft_skills
                                                                                .length > 0)) ? 'opacity-100' :
                                                                            'opacity-0'
                                                                        ]"
                                                                        fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2.5"
                                                                            d="M19 9l-7 7-7-7"></path>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                            <div x-show="openItem === index"
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
                                                                        <div
                                                                            class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                                                            <div class="flex items-center gap-2 mb-4">
                                                                                <span
                                                                                    class="text-sm font-bold text-gray-900 uppercase tracking-wide">Missing
                                                                                    Technical Skills</span>
                                                                            </div>
                                                                            <template
                                                                                x-if="result.hard_skills && result.hard_skills.length > 0">
                                                                                <div class="flex flex-wrap gap-2.5">
                                                                                    <template
                                                                                        x-for="skill in result.hard_skills"
                                                                                        :key="skill.name || skill">
                                                                                        <span
                                                                                            class="px-4 py-2.5 bg-white text-gray-800 border border-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm"
                                                                                            x-text="skill.name || skill">
                                                                                        </span>
                                                                                    </template>
                                                                                </div>
                                                                            </template>
                                                                            <template
                                                                                x-if="!result.hard_skills || result.hard_skills.length === 0">
                                                                                <div class="text-center py-6">
                                                                                    <div
                                                                                        class="text-3xl mb-2 opacity-20">
                                                                                        ✓</div>
                                                                                    <p
                                                                                        class="text-sm text-gray-400 font-medium">
                                                                                        No specific technical skill gaps
                                                                                        identified</p>
                                                                                </div>
                                                                            </template>
                                                                        </div>

                                                                        <div
                                                                            class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                                                            <div class="flex items-center gap-2 mb-4">
                                                                                <div>
                                                                                    <span
                                                                                        class="text-sm font-bold text-gray-900 uppercase tracking-wide block">Missing
                                                                                        Soft Skills</span>

                                                                                </div>
                                                                            </div>
                                                                            <template
                                                                                x-if="result.soft_skills && result.soft_skills.length > 0">
                                                                                <div class="flex flex-wrap gap-2.5">
                                                                                    <template
                                                                                        x-for="skill in result.soft_skills"
                                                                                        :key="skill.name || skill">
                                                                                        <span
                                                                                            class="px-4 py-2.5 bg-white text-gray-800 border border-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm"
                                                                                            x-text="skill.name || skill">
                                                                                        </span>
                                                                                    </template>
                                                                                </div>
                                                                            </template>
                                                                            <template
                                                                                x-if="!result.soft_skills || result.soft_skills.length === 0">
                                                                                <div class="text-center py-6">
                                                                                    <div
                                                                                        class="text-3xl mb-2 opacity-20">
                                                                                        ✓</div>
                                                                                    <p
                                                                                        class="text-sm text-gray-400 font-medium">
                                                                                        No soft skill gaps identified
                                                                                    </p>
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                    </div>

                                                                    <template
                                                                        x-if="result.salary_min && result.salary_max">
                                                                        <div
                                                                            class="mt-6 bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                                                            <div
                                                                                class="flex items-center justify-between">
                                                                                <div class="flex items-center gap-2">
                                                                                    <div
                                                                                        class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                                                                        <span class="text-lg">💰</span>
                                                                                    </div>
                                                                                    <span
                                                                                        class="text-sm font-bold text-gray-900 uppercase tracking-wide">Salary
                                                                                        Range</span>
                                                                                </div>
                                                                                <div class="text-right">
                                                                                    <div
                                                                                        class="text-lg font-bold text-gray-900">
                                                                                        ₱<span
                                                                                            x-text="Number(result.salary_min).toLocaleString()"></span>
                                                                                        - ₱<span
                                                                                            x-text="Number(result.salary_max).toLocaleString()"></span>
                                                                                    </div>
                                                                                    <div
                                                                                        class="text-xs text-gray-500 mt-1">
                                                                                        Monthly compensation</div>
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
                                <div
                                    class="px-4 sm:px-8 py-4 sm:py-5 border-t bg-white flex flex-wrap items-center justify-between gap-3 shadow-inner pagination-controls">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <span>Showing</span>
                                        <span class="font-bold text-gray-900"
                                            x-text="matrixShowAll ? 1 : (currentPage - 1) * itemsPerPage + 1"></span>
                                        <span>to</span>
                                        <span class="font-bold text-gray-900"
                                            x-text="matrixShowAll ? (sortedData?.length || 0) : Math.min(currentPage * itemsPerPage, (sortedData?.length || 0))"></span>
                                        <span>of</span>
                                        <span class="font-bold text-gray-900"
                                            x-text="(sortedData?.length || 0)"></span>
                                        <span>results</span>
                                        <span id="matrixFilterBadge"
                                            class="ml-2 hidden px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full">
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2" x-show="!matrixShowAll">
                                        <button @click="prevPage()" :disabled="currentPage === 1"
                                            :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed' :
                                                'hover:bg-gray-50 hover:border-gray-400'"
                                            class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 transition-all">
                                            Previous
                                        </button>
                                        <div class="flex gap-1.5 pagination-page-numbers">
                                            <template x-for="page in totalPages" :key="page">
                                                <button @click="goToPage(page)"
                                                    :class="currentPage === page ?
                                                        'bg-emerald-500 text-white border-emerald-500 shadow-md' :
                                                        'bg-white text-gray-700 hover:bg-gray-50 border-gray-300'"
                                                    class="min-w-[44px] px-4 py-2.5 rounded-lg border text-sm font-bold transition-all"
                                                    x-text="page">
                                                </button>
                                            </template>
                                        </div>
                                        <button @click="nextPage()" :disabled="currentPage === totalPages"
                                            :class="currentPage === totalPages ? 'opacity-40 cursor-not-allowed' :
                                                'hover:bg-gray-50 hover:border-gray-400'"
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
                        Source: Tab1-Employment-Davao-Region-with-JUL2025.xlsx (Rates) | Module 2 Sources: PhilJobNet,
                        PSA ISLE, Industry Surveys.
                    </p>
                </div>

                <div id="lmi-matrix-modal"
                    class="fixed inset-0 z-[9999] flex items-center justify-center p-0 sm:px-4 hidden">
                    <div id="modal-backdrop"
                        class="absolute inset-0 backdrop-blur-md bg-white/30 pointer-events-none"></div>
                    <div id="lmi-form-content"
                        class="bg-white sm:rounded-2xl shadow-2xl w-full h-full sm:w-[96vw] sm:h-[96vh] sm:max-w-[96vw] sm:max-h-[96vh] overflow-hidden relative z-10 pointer-events-auto">

                        <div
                            class="bg-teal-700 px-4 py-3 sm:p-5 flex justify-between items-center text-white sticky top-0 z-10">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="text-sm sm:text-lg font-bold leading-tight">INDUSTRY SKILLS NEED SURVEY</h3>
                            </div>
                            <button id="close-modal-btn"
                                class="text-white hover:bg-teal-600 p-1.5 rounded transition flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="bg-teal-600 px-3 sm:px-5 py-3 sm:py-4 sticky top-[52px] sm:top-[68px] z-10">
                            <div class="flex items-center justify-between max-w-3xl mx-auto">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="step-circle w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white text-teal-700 flex items-center justify-center text-xs sm:text-sm font-bold">
                                        1</div>
                                    <span class="text-white text-[10px] sm:text-xs mt-1 hidden sm:block">Company</span>
                                </div>
                                <div class="step-line flex-1 h-1 bg-teal-500 mx-1 sm:mx-2"></div>
                                <div class="flex flex-col items-center">
                                    <div
                                        class="step-circle w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-teal-500 text-white flex items-center justify-center text-xs sm:text-sm font-bold">
                                        2</div>
                                    <span class="text-white text-[10px] sm:text-xs mt-1 hidden sm:block">Roles</span>
                                </div>
                                <div class="step-line flex-1 h-1 bg-teal-500 mx-1 sm:mx-2"></div>
                                <div class="flex flex-col items-center">
                                    <div
                                        class="step-circle w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-teal-500 text-white flex items-center justify-center text-xs sm:text-sm font-bold">
                                        3</div>
                                    <span
                                        class="text-white text-[10px] sm:text-xs mt-1 hidden sm:block">Diagnosis</span>
                                </div>
                                <div class="step-line flex-1 h-1 bg-teal-500 mx-1 sm:mx-2"></div>
                                <div class="flex flex-col items-center">
                                    <div
                                        class="step-circle w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-teal-500 text-white flex items-center justify-center text-xs sm:text-sm font-bold">
                                        4</div>
                                    <span
                                        class="text-white text-[10px] sm:text-xs mt-1 hidden sm:block">Engagement</span>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-y-auto h-[calc(100vh-120px)] sm:h-[calc(98vh-140px)] pb-24">
                            <div class="p-4 sm:p-8">
                                <div id="intro-section">
                                    <h4 class="text-l font-bold pb-2">INDUSTRY SKILLS NEED SURVEY</h4>
                                    <p
                                        class="text-gray-600 text-sm leading-relaxed mb-8 pb-6 border-b border-gray-200">
                                        {{ __('lmip.lmi_intro') }}
                                    </p>
                                    <h5 class="text-l font-bold pb-2">DATA PRIVACY STATEMENT</h5>
                                    <p
                                        class="text-gray-600 text-sm leading-relaxed mb-8 pb-6 border-b border-gray-200">
                                        {{ __('lmip.privacy_statement') }}
                                    </p>
                                </div>

                                <form action="{{ route('lmi.submit') }}" method="POST" class="space-y-5"
                                    id="lmi-form">
                                    @csrf
                                    <input type="hidden" name="test_form_start" value="FORM_STARTED">

                                    <div class="lmi-step" data-step="0">

                                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mt-8">
                                            <div class="flex items-center gap-3 mb-4">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-teal-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                </div>
                                                <div class="text-sm font-bold text-gray-800 uppercase tracking-wide">
                                                    Part 1: Company Profile</div>
                                            </div>
                                            <div class="h-px bg-gray-100 mb-5"></div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">

                                                <div class="flex flex-col gap-5">
                                                    <div>
                                                        <label
                                                            class="block text-gray-800 text-sm font-semibold mb-2">Company
                                                            Name:<span class="text-red-500">*</span></label>
                                                        <input type="text" name="company" required
                                                            class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-gray-800 text-sm font-semibold mb-2">Designation
                                                            / Position:<span class="text-red-500">*</span></label>
                                                        <input type="text" name="position" required
                                                            class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-gray-800 text-sm font-semibold mb-2">Email
                                                            Address:<span class="text-red-500">*</span></label>
                                                        <input type="email" name="email" id="emailInput" required
                                                            class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                                        <p id="emailError"
                                                            class="hidden text-red-500 text-xs mt-1.5 font-medium">
                                                            Please enter a valid email address (e.g. <a
                                                                href="/cdn-cgi/l/email-protection"
                                                                class="__cf_email__"
                                                                data-cfemail="204e414d45604558414d504c450e434f4d">[@email.com]</a>)
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="flex flex-col gap-5">
                                                    <div>
                                                        <label
                                                            class="block text-gray-800 text-sm font-semibold mb-2">Name
                                                            of Respondent:<span class="text-red-500">*</span></label>
                                                        <input type="text" name="respondent"
                                                            placeholder="e.g., John Quincy Adams" required
                                                            class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                                    </div>
                                                    <div>
                                                        <label class="block text-gray-800 text-sm font-semibold mb-2">
                                                            Contact Number:<span class="text-red-500">*</span>
                                                        </label>

                                                        <div class="inline-flex bg-gray-100 rounded-lg p-1 mb-3">
                                                            <button type="button" id="toggle-mobile"
                                                                onclick="switchContactType('mobile')"
                                                                class="contact-type-btn flex items-center gap-1.5 px-4 py-1.5 rounded-md text-sm font-semibold bg-white text-teal-700 shadow-sm border border-gray-200 transition-all duration-200">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                                </svg>
                                                                Mobile
                                                            </button>
                                                            <button type="button" id="toggle-telephone"
                                                                onclick="switchContactType('telephone')"
                                                                class="contact-type-btn flex items-center gap-1.5 px-4 py-1.5 rounded-md text-sm font-semibold text-gray-500 transition-all duration-200">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
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
                                                                        <svg class="w-3 h-3 text-gray-400"
                                                                            fill="none" stroke="currentColor"
                                                                            viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2" d="M19 9l-7 7-7-7" />
                                                                        </svg>
                                                                    </button>
                                                                    <div id="country-dropdown"
                                                                        class="hidden absolute z-50 left-0 top-full mt-1 w-72 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                                                                        <div class="p-2 border-b border-gray-100">
                                                                            <input type="text" id="country-search"
                                                                                placeholder="Search country..."
                                                                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" />
                                                                        </div>
                                                                        <div id="country-list"
                                                                            class="max-h-52 overflow-y-auto"></div>
                                                                    </div>
                                                                </div>
                                                                <input type="tel" id="mobile-input"
                                                                    placeholder="912 345 6789" required
                                                                    inputmode="numeric"
                                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                                    class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent focus:bg-white transition-all" />
                                                            </div>
                                                        </div>

                                                        <div id="telephone-input-wrapper" class="relative hidden">
                                                            <div
                                                                class="absolute inset-y-0 left-0 flex items-center pl-3 pr-3 border-r border-gray-300 pointer-events-none">
                                                                <span class="text-lg">☎️</span>
                                                                <span
                                                                    class="ml-1.5 text-sm font-semibold text-gray-600">PH</span>
                                                            </div>
                                                            <input type="tel" id="telephone-input" maxlength="12"
                                                                placeholder="e.g. 082-123-4567" inputmode="numeric"
                                                                autocomplete="off"
                                                                class="w-full pl-20 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent focus:bg-white transition-all"
                                                                disabled />
                                                            <div id="area-code-suggestions"
                                                                class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl z-50 hidden overflow-hidden">
                                                                <div
                                                                    class="px-3 py-2 bg-gray-50 border-b border-gray-100">
                                                                    <p
                                                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                                        Matching Area Codes</p>
                                                                </div>
                                                                <div id="area-code-list"
                                                                    class="max-h-52 overflow-y-auto"></div>
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="contact_type"
                                                            id="contact_type_input" value="mobile">
                                                        <input type="hidden" name="contact_number"
                                                            id="contact_number_carrier">

                                                        <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1"
                                                            id="contact-hint">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            Enter your mobile number with country code
                                                        </p>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="relative mt-4">
                                                <label class="block text-gray-800 text-sm font-semibold mb-2">Industry
                                                    Sector:<span class="text-red-500">*</span></label>
                                                <button type="button" id="industry-dropdown-btn"
                                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 hover:border-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                                    <span id="industry-selected-text" class="text-gray-400">Please
                                                        select your primary operation</span>
                                                    <svg id="industry-dropdown-arrow"
                                                        class="w-5 h-5 text-gray-400 transition-transform"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                                <div id="industry-dropdown-menu"
                                                    class="fixed z-[999] bg-white border border-gray-200 rounded-xl shadow-lg max-h-96 overflow-y-auto hidden">
                                                    <div data-value="Accommodation &amp; Food Service (Hotels, Resorts, Restaurants, Fast Food Chains, Catering Services)"
                                                        class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        • Accommodation &amp; Food Service (Hotels, Resorts,
                                                        Restaurants, Fast Food Chains, Catering Services)</div>
                                                    <div data-value="Administrative &amp; Support Services (Security Agencies, Manpower/Recruitment Agencies, Call Centers, Travel Agencies, Janitorial Services)"
                                                        class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        • Administrative &amp; Support Services (Security Agencies,
                                                        Manpower/Recruitment Agencies, Call Centers, Travel Agencies,
                                                        Janitorial Services)</div>
                                                    <div data-value="Agriculture, Forestry, Fishing &amp; Mining"
                                                        class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        • Agriculture, Forestry, Fishing &amp; Mining</div>
                                                    <div data-value="Construction"
                                                        class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        • Construction</div>
                                                    <div data-value="Education (Private Schools, Colleges, Universities, Training Centers)"
                                                        class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        • Education (Private Schools, Colleges, Universities, Training
                                                        Centers)</div>
                                                    <div data-value="Electricity, Gas, Water &amp; Waste Management (Power Plants, Electric Co-ops, Water Districts, Garbage/Recycling Firms)"
                                                        class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        • Electricity, Gas, Water &amp; Waste Management (Power Plants,
                                                        Electric Co-ops, Water Districts, Garbage/Recycling Firms)</div>
                                                    <div data-value="Financial &amp; Insurance Activities (Banks, Pawnshops, Lending Investors, Insurance Companies)"
                                                        class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        • Financial &amp; Insurance Activities (Banks, Pawnshops,
                                                        Lending Investors, Insurance Companies)</div>
                                                    <div data-value="Human Health &amp; Social Work (Hospital, Medical Clinics, Diagnostic Labs, Nursing Homes)"
                                                        class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        • Human Health &amp; Social Work (Hospital, Medical Clinics,
                                                        Diagnostic Labs, Nursing Homes)</div>
                                                    <div data-value="Information &amp; Communication (Software Companies, ISPs, Telecoms, TV/Radio Stations, Non-Voice Tech BPO)"
                                                        class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        • Information &amp; Communication (Software Companies, ISPs,
                                                        Telecoms, TV/Radio Stations, Non-Voice Tech BPO)</div>
                                                    <div data-value="Other Service Activities (Repairs Shops, Beauty Salons, Spas, Laundry Shops, Funeral)"
                                                        class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        • Other Service Activities (Repairs Shops, Beauty Salons, Spas,
                                                        Laundry Shops, Funeral)</div>
                                                    <div data-value="Professional, Scientific &amp; Technical Services (Law Firms, Accounting/Auditing Firms, Engineering/Architectural Firms, Advertising Agencies)"
                                                        class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        • Professional, Scientific &amp; Technical Services (Law Firms,
                                                        Accounting/Auditing Firms, Engineering/Architectural Firms,
                                                        Advertising Agencies)</div>
                                                    <div data-value="Real Estate Activities (Real Estate Developers, Lessor of Apartment/Office Space)"
                                                        class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        • Real Estate Activities (Real Estate Developers, Lessor of
                                                        Apartment/Office Space)</div>
                                                    <div data-value="Transportation, Storage &amp; Logistics (Trucking/Hauling Services, Warehousing, Shipping Lines, Courier Services)"
                                                        class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        • Transportation, Storage &amp; Logistics (Trucking/Hauling
                                                        Services, Warehousing, Shipping Lines, Courier Services)</div>
                                                    <div data-value="Wholesale &amp; Retail Trade (Trading Companies, Malls, Hardware Stores, Car Dealers, Online Shops, etc.)"
                                                        class="industry-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        • Wholesale &amp; Retail Trade (Trading Companies, Malls,
                                                        Hardware Stores, Car Dealers, Online Shops, etc.)</div>
                                                </div>
                                                <input type="hidden" id="industry-selector-input"
                                                    name="industrySelector" required>
                                            </div>

                                            <div class="relative mt-4">
                                                <label class="block text-gray-800 text-sm font-semibold mb-2">Company
                                                    Size:<span class="text-red-500">*</span></label>
                                                <button type="button" id="company-size-btn"
                                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 hover:border-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                                    <span id="company-size-selected-text" class="text-gray-400">Select
                                                        company size</span>
                                                    <svg id="company-size-arrow"
                                                        class="w-5 h-5 text-gray-400 transition-transform"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                                <div id="company-size-dropdown"
                                                    class="fixed z-[999] bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                                    <div data-value="Less than 50"
                                                        class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        Less than 50</div>
                                                    <div data-value="51-200"
                                                        class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        51-200</div>
                                                    <div data-value="201-500"
                                                        class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        201-500</div>
                                                    <div data-value="More than 500"
                                                        class="company-size-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                        More than 500</div>
                                                </div>
                                                <input type="hidden" id="company-size-input" name="companySize"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="flex justify-end mt-6 gap-2">
                                            <button type="button"
                                                class="btn-next bg-teal-600 hover:bg-teal-700 text-white font-semibold px-5 sm:px-8 py-2.5 rounded-lg transition shadow-md w-full sm:w-auto">Next
                                            </button>
                                        </div>
                                    </div>
                                    <div class="lmi-step" data-step="1" style="display:none;">

                                        <div
                                            class="bg-teal-50 border border-teal-200 rounded-lg p-6 mt-10 overflow-hidden">
                                            <div class="flex items-center gap-3 mb-3">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-teal-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <div class="text-sm font-bold text-gray-800 uppercase tracking-wide">
                                                    Part II: Hard-to-Fill Roles / Skill Requirements</div>
                                            </div>
                                            <div class="h-px bg-gray-100 mb-4"></div>
                                            <p class="text-teal-700 text-xs font-medium mb-4">
                                                Please identify the TOP Job Titles you find hardest to fill. Be as
                                                specific as possible (e.g., instead of "IT Skills", say "Python
                                                Programming").
                                            </p>

                                            <div id="jobTitlesContainer" class="space-y-6">
                                                <div class="bg-white rounded-lg p-4 border border-gray-200 job-entry">
                                                    <div class="mb-4">
                                                        <label class="block text-gray-800 text-sm font-semibold mb-2">
                                                            Job Title: <span
                                                                class="text-gray-700 text-sm font-medium">(Please list
                                                                only one job title)</span><span
                                                                class="text-red-500">*</span></label>
                                                        <input type="text" name="job_title[]"
                                                            placeholder="e.g. Senior Java Developer" required
                                                            class="job-title-input w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm" />
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="block text-gray-800 text-sm font-semibold mb-2">
                                                            Standard Job Classifications / Families: <span
                                                                class="text-red-500">*</span></label>
                                                        <div class="relative">
                                                            <button type="button"
                                                                class="job-classification-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                                                <span
                                                                    class="job-classification-text text-gray-400">Select
                                                                    job classification</span>
                                                                <svg class="job-classification-arrow w-5 h-5 text-gray-400 transition-transform"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M19 9l-7 7-7-7"></path>
                                                                </svg>
                                                            </button>
                                                            <div
                                                                class="job-classification-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                                                <div data-value="Accounting, Finance &amp; Banking"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • Accounting, Finance &amp; Banking</div>
                                                                <div data-value="Administrative, HR &amp; Office Support"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • Administrative, HR &amp; Office Support</div>
                                                                <div data-value="Agriculture, Forestry &amp; Agribusiness"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • Agriculture, Forestry &amp; Agribusiness</div>
                                                                <div data-value="Construction, Engineering &amp; Architecture"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • Construction, Engineering &amp; Architecture</div>
                                                                <div data-value="Customer Service &amp; BPO (Contact Center)"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • Customer Service &amp; BPO (Contact Center)</div>
                                                                <div data-value="Education, Training &amp; Academe"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • Education, Training &amp; Academe</div>
                                                                <div data-value="Healthcare, Medical &amp; Allied Services"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • Healthcare, Medical &amp; Allied Services</div>
                                                                <div data-value="IT, Software, Data &amp; Digital Creative"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • IT, Software, Data &amp; Digital Creative</div>
                                                                <div data-value="Legal, Compliance &amp; Public Service"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • Legal, Compliance &amp; Public Service</div>
                                                                <div data-value="Logistics, Transport &amp; Supply Chain"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • Logistics, Transport &amp; Supply Chain</div>
                                                                <div data-value="Manufacturing, Production &amp; Technical"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • Manufacturing, Production &amp; Technical</div>
                                                                <div data-value="Sales, Marketing, Retail &amp; E-Commerce"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • Sales, Marketing, Retail &amp; E-Commerce</div>
                                                                <div data-value="Science, Research &amp; Laboratory"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • Science, Research &amp; Laboratory</div>
                                                                <div data-value="Skilled Trades, Maintenance &amp; General Services"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • Skilled Trades, Maintenance &amp; General Services
                                                                </div>
                                                                <div data-value="Tourism, Hospitality &amp; Food Service"
                                                                    class="job-classification-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    • Tourism, Hospitality &amp; Food Service</div>
                                                            </div>
                                                            <input type="hidden" class="job-classification-input"
                                                                name="job_classification[]" required>
                                                        </div>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label class="block text-gray-800 text-sm font-semibold mb-2">
                                                            Salary Range: <span class="text-red-500">*</span></label>
                                                        <div class="relative">
                                                            <button type="button"
                                                                class="salary-range-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                                                <span class="salary-range-text text-gray-400">Select
                                                                    salary range</span>
                                                                <svg class="salary-range-arrow w-5 h-5 text-gray-400 transition-transform"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M19 9l-7 7-7-7"></path>
                                                                </svg>
                                                            </button>
                                                            <div
                                                                class="salary-range-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden hidden">
                                                                <div data-value="₱30,000 - ₱59,999"
                                                                    class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    ₱30,000 - ₱59,999</div>
                                                                <div data-value="₱60,000 - ₱89,999"
                                                                    class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    ₱60,000 - ₱89,999</div>
                                                                <div data-value="₱90,000 - ₱149,999"
                                                                    class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    ₱90,000 - ₱149,999</div>
                                                                <div data-value="₱150,000 - ₱499,999"
                                                                    class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    ₱150,000 - ₱499,999</div>
                                                                <div data-value="₱500,000 and above"
                                                                    class="salary-range-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    ₱500,000 and above</div>
                                                                <div data-value="Below ₱30,000"
                                                                    class="salary-range-option below-30k-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    Below ₱30,000 (please specify)
                                                                </div>
                                                            </div>
                                                            <input type="hidden" class="salary-range-input"
                                                                name="salary_range[]">
                                                        </div>

                                                        <div class="below-30k-input-container mt-3 hidden">
                                                            <label
                                                                class="block text-gray-600 text-xs font-medium mb-2">Please
                                                                specify the exact salary amount:</label>
                                                            <div class="relative">
                                                                <span
                                                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">₱</span>
                                                                <input type="text" name="below_30k_salary[]"
                                                                    class="below-30k-salary-input w-full pl-8 pr-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                                                    placeholder="e.g. 25,000" inputmode="numeric">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label
                                                            class="block text-gray-800 text-sm font-semibold mb-2">Duration
                                                            that the Vacancy is Open: <span
                                                                class="text-red-500">*</span></label>
                                                        <div class="relative">
                                                            <button type="button"
                                                                class="duration-btn w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 outline-none bg-white text-gray-600 shadow-sm text-left flex items-center justify-between">
                                                                <span class="duration-text text-gray-400">Select
                                                                    duration</span>
                                                                <svg class="duration-arrow w-5 h-5 text-gray-400 transition-transform"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M19 9l-7 7-7-7"></path>
                                                                </svg>
                                                            </button>
                                                            <div
                                                                class="duration-menu absolute z-20 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden hidden">
                                                                <div data-value="Less than 30 Days"
                                                                    class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    Less than 30 Days</div>
                                                                <div data-value="30-60 Days"
                                                                    class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    30-60 Days</div>
                                                                <div data-value="60-90 Days"
                                                                    class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    60-90 Days</div>
                                                                <div data-value="90+ Days"
                                                                    class="duration-option px-4 py-3 hover:bg-teal-50 cursor-pointer text-sm text-gray-700 transition">
                                                                    90+ Days</div>
                                                            </div>
                                                            <input type="hidden" class="duration-input"
                                                                name="vacancy_duration[]" required>
                                                        </div>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="block text-gray-800 text-sm font-semibold mb-2">
                                                            Reasons For Difficulty (Role-Level) <span
                                                                class="italic text-gray-500">(Check all that
                                                                apply)</span>
                                                        </label>
                                                        <div class="difficulty-reasons space-y-3">
                                                            <div
                                                                class="technical-skills-label p-3 border rounded-lg transition-all border-gray-200">
                                                                <label class="flex items-start cursor-pointer">
                                                                    <input type="checkbox"
                                                                        name="difficulty_reasons_0[]"
                                                                        value="Technical / Hard Skills Missing"
                                                                        class="technical-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                                                    <div class="ml-3">
                                                                        <div class="font-semibold text-gray-800">
                                                                            Technical / Hard Skills Missing</div>
                                                                        <div class="text-xs text-gray-500 mt-1">
                                                                            Applicants do not have the required tools,
                                                                            software, or technical knowledge</div>
                                                                    </div>
                                                                </label>
                                                                <div class="technical-details mt-3 hidden">
                                                                    <label
                                                                        class="block text-gray-600 text-xs font-medium mb-1">What
                                                                        specific technical tools, software, or machinery
                                                                        knowledge is missing?</label>
                                                                    <div
                                                                        class="technical-tags-container flex flex-wrap gap-2 mb-2">
                                                                    </div>
                                                                    <div class="flex gap-2 skill-input-row">
                                                                        <input type="text"
                                                                            class="technical-skill-input flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                                                            placeholder="Type a skill and press Enter (e.g. Python, SQL, AutoCAD...)"
                                                                            enterkeyhint="done" inputmode="text" />
                                                                        <button type="button"
                                                                            class="add-technical-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Enter</button>
                                                                    </div>
                                                                    <p class="text-xs text-gray-500 mt-1">Press Enter
                                                                        or comma to add each skill</p>
                                                                    <input type="hidden"
                                                                        class="technical-skills-input"
                                                                        name="technical_skills_missing[]">
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="soft-skills-label p-3 border rounded-lg transition-all border-gray-200">
                                                                <label class="flex items-start cursor-pointer">
                                                                    <input type="checkbox"
                                                                        name="difficulty_reasons_0[]"
                                                                        value="Soft / Employability Skills Missing"
                                                                        class="soft-checkbox mt-1 w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                                                    <div class="ml-3">
                                                                        <div class="font-semibold text-gray-800">Soft
                                                                            / Employability Skills Missing</div>
                                                                        <div class="text-xs text-gray-500 mt-1">
                                                                            Applicants cannot communicate effectively,
                                                                            work in teams, or demonstrate
                                                                            professionalism</div>
                                                                    </div>
                                                                </label>
                                                                <div class="soft-details mt-3 hidden">
                                                                    <label
                                                                        class="block text-gray-600 text-xs font-medium mb-1">What
                                                                        attitude or behavioral traits cause you to
                                                                        reject applicants?</label>
                                                                    <div
                                                                        class="soft-tags-container flex flex-wrap gap-2 mb-2">
                                                                    </div>
                                                                    <div class="flex gap-2 skill-input-row">
                                                                        <input type="text"
                                                                            class="soft-skill-input flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                                                                            placeholder="Type a trait and press Enter (e.g. Poor communication, Unprofessional...)"
                                                                            enterkeyhint="done" inputmode="text" />
                                                                        <button type="button"
                                                                            class="add-soft-skill px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium text-sm transition-colors shadow-sm">Enter</button>
                                                                    </div>
                                                                    <p class="text-xs text-gray-500 mt-1">Press Enter
                                                                        or comma to add each trait</p>
                                                                    <input type="hidden" class="soft-skills-input"
                                                                        name="soft_skills_missing[]">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-4 mt-6 pt-4 border-t border-gray-200">
                                                        <label class="block text-gray-800 text-sm font-semibold mb-3">
                                                            How much does the difficulty finding qualified applicants
                                                            for this role impact your business operations? <span
                                                                class="text-red-500">*</span>
                                                        </label>
                                                        <div class="impact-level space-y-3">
                                                            <label
                                                                class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                                <input type="radio" name="impact_level_0"
                                                                    value="High" required
                                                                    class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                                <div class="ml-3 flex-1">
                                                                    <div class="font-semibold text-gray-900">High
                                                                        Impact</div>
                                                                    <div class="text-xs text-gray-500 mt-1">Operations
                                                                        are significantly disrupted, critical tasks or
                                                                        projects are delayed, affecting productivity and
                                                                        revenue</div>
                                                                </div>
                                                            </label>
                                                            <label
                                                                class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                                <input type="radio" name="impact_level_0"
                                                                    value="Medium" required
                                                                    class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                                <div class="ml-3 flex-1">
                                                                    <div class="font-semibold text-gray-900">Medium
                                                                        Impact</div>
                                                                    <div class="text-xs text-gray-500 mt-1">Operations
                                                                        continue but require overtime, increased
                                                                        workload for existing staff, or minor project
                                                                        delays</div>
                                                                </div>
                                                            </label>
                                                            <label
                                                                class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                                <input type="radio" name="impact_level_0"
                                                                    value="Low" required
                                                                    class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                                <div class="ml-3 flex-1">
                                                                    <div class="font-semibold text-gray-900">Low
                                                                        Impact</div>
                                                                    <div class="text-xs text-gray-500 mt-1">Minimal
                                                                        impact; new hires can be trained internally
                                                                        without significant operational disruptions
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="button" id="add-job-title-btn"
                                                class="w-full mt-4 px-4 py-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg transition shadow-md flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Add Another Job Title
                                            </button>
                                        </div>

                                        <div class="flex flex-col-reverse sm:flex-row justify-between mt-6 gap-3">
                                            <button type="button"
                                                class="btn-prev bg-white hover:bg-gray-50 text-gray-700 font-semibold px-5 sm:px-8 py-2.5 rounded-lg transition border border-gray-300 shadow-sm w-full sm:w-auto">
                                                Previous</button>
                                            <button type="button"
                                                class="btn-next bg-teal-600 hover:bg-teal-700 text-white font-semibold px-5 sm:px-8 py-2.5 rounded-lg transition shadow-md w-full sm:w-auto">Next
                                            </button>
                                        </div>
                                    </div>
                                    <div class="lmi-step" data-step="2" style="display:none;">

                                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mt-10">
                                            <div class="flex items-center gap-3 mb-3">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-orange-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div class="text-sm font-bold text-gray-800 uppercase tracking-wide">
                                                    Part III: Diagnosis of Mismatch</div>
                                            </div>
                                            <div class="h-px bg-gray-100 mb-4"></div>
                                            <p class="text-gray-600 text-xs font-medium mb-6">
                                                For applicants who meet formal qualifications (degree, license, or
                                                certification), which observable factors most often cause them to be
                                                rejected?
                                            </p>

                                            <div class="space-y-6">
                                                <div class="bg-white rounded-lg p-5 border border-gray-200">
                                                    <label class="block text-gray-800 text-sm font-semibold mb-3">
                                                        Reason Qualified Applicants Are Rejected (Applicant-Level) <span
                                                            class="text-gray-500 italic text-xs">(Check all that
                                                            apply)</span>
                                                    </label>
                                                    <div class="space-y-3">
                                                        <label
                                                            class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                            <input type="checkbox" name="rejection_reasons[]"
                                                                value="Lack of practical / hands-on experience"
                                                                class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                                            <div class="ml-3 flex-1">
                                                                <div class="font-semibold text-gray-900">Lack of
                                                                    practical / hands-on experience</div>
                                                                <div class="text-xs text-gray-500 mt-1">Cannot apply
                                                                    theory to real work; requires supervision</div>
                                                            </div>
                                                        </label>
                                                        <label
                                                            class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                            <input type="checkbox" name="rejection_reasons[]"
                                                                value="Skills are outdated"
                                                                class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                                            <div class="ml-3 flex-1">
                                                                <div class="font-semibold text-gray-900">Skills are
                                                                    outdated</div>
                                                                <div class="text-xs text-gray-500 mt-1">Training
                                                                    received does not match current tools, systems, or
                                                                    industry practices</div>
                                                            </div>
                                                        </label>
                                                        <label
                                                            class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                            <input type="checkbox" name="rejection_reasons[]"
                                                                value="Poor communication skills"
                                                                class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                                            <div class="ml-3 flex-1">
                                                                <div class="font-semibold text-gray-900">Poor
                                                                    communication skills</div>
                                                                <div class="text-xs text-gray-500 mt-1">Oral, written,
                                                                    presentation, or cross-cultural communication issues
                                                                </div>
                                                            </div>
                                                        </label>
                                                        <label
                                                            class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                            <input type="checkbox" name="rejection_reasons[]"
                                                                value="Low job readiness / poor interview performance"
                                                                class="mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                                            <div class="ml-3 flex-1">
                                                                <div class="font-semibold text-gray-900">Low job
                                                                    readiness / poor interview performance</div>
                                                                <div class="text-xs text-gray-500 mt-1">Cannot
                                                                    demonstrate readiness during recruitment; fails
                                                                    assessments; lacks workplace etiquette</div>
                                                            </div>
                                                        </label>
                                                        <div
                                                            class="other-rejection-option border rounded-lg transition-all border-gray-200">
                                                            <label class="flex items-start p-3 cursor-pointer">
                                                                <input type="checkbox" name="rejection_reasons[]"
                                                                    value="Other"
                                                                    class="other-rejection-checkbox mt-1 w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                                                <div class="ml-3 flex-1">
                                                                    <div class="font-semibold text-gray-900">Other
                                                                        (please specify)</div>
                                                                </div>
                                                            </label>
                                                            <div class="other-rejection-input px-3 pb-3 ml-7 hidden">
                                                                <textarea name="rejection_reasons_other" placeholder="Please specify other reasons..." rows="2"
                                                                    enterkeyhint="done" class="other-specify-textarea"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-white rounded-lg p-5 border border-gray-200">
                                                    <label class="block text-gray-800 text-sm font-semibold mb-3">
                                                        How often do you coordinate with Universities/Colleges to
                                                        discuss your skills requirements? <span
                                                            class="text-gray-500 italic text-xs">(Select ONE)</span>
                                                    </label>
                                                    <div class="coordination-options space-y-3">
                                                        <label
                                                            class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                            <input type="radio" name="coordination_frequency"
                                                                value="Never" required
                                                                class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                            <div class="ml-3 flex-1">
                                                                <div class="font-semibold text-gray-900">Never</div>
                                                            </div>
                                                        </label>
                                                        <label
                                                            class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                            <input type="radio" name="coordination_frequency"
                                                                value="Rarely" required
                                                                class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                            <div class="ml-3 flex-1">
                                                                <div class="font-semibold text-gray-900">Rarely</div>
                                                                <div class="text-xs text-gray-500 mt-1">Only when
                                                                    invited to graduations/events</div>
                                                            </div>
                                                        </label>
                                                        <label
                                                            class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                            <input type="radio" name="coordination_frequency"
                                                                value="Occasionally" required
                                                                class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                            <div class="ml-3 flex-1">
                                                                <div class="font-semibold text-gray-900">Occasionally
                                                                </div>
                                                                <div class="text-xs text-gray-500 mt-1">During OJT
                                                                    placement</div>
                                                            </div>
                                                        </label>
                                                        <label
                                                            class="flex items-start p-3 border rounded-lg cursor-pointer transition-all hover:bg-orange-50 hover:border-orange-300">
                                                            <input type="radio" name="coordination_frequency"
                                                                value="Frequently" required
                                                                class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                            <div class="ml-3 flex-1">
                                                                <div class="font-semibold text-gray-900">Frequently
                                                                </div>
                                                                <div class="text-xs text-gray-500 mt-1">We sit on
                                                                    advisory boards/curriculum reviews</div>
                                                            </div>
                                                        </label>
                                                        <div
                                                            class="other-coordination-option border rounded-lg transition-all border-gray-200">
                                                            <label class="flex items-start p-3 cursor-pointer">
                                                                <input type="radio" name="coordination_frequency"
                                                                    value="Other" required
                                                                    class="other-coordination-radio mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                                <div class="ml-3 flex-1">
                                                                    <div class="font-semibold text-gray-900">Other
                                                                        (please specify)</div>
                                                                </div>
                                                            </label>
                                                            <div
                                                                class="other-coordination-input px-3 pb-3 ml-7 hidden">
                                                                <textarea name="coordination_frequency_other" placeholder="Please specify..." rows="2" enterkeyhint="done"
                                                                    class="other-specify-textarea"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-col-reverse sm:flex-row justify-between mt-6 gap-3">
                                            <button type="button"
                                                class="btn-prev bg-white hover:bg-gray-50 text-gray-700 font-semibold px-5 sm:px-8 py-2.5 rounded-lg transition border border-gray-300 shadow-sm w-full sm:w-auto">
                                                Previous</button>
                                            <button type="button"
                                                class="btn-next bg-teal-600 hover:bg-teal-700 text-white font-semibold px-5 sm:px-8 py-2.5 rounded-lg transition shadow-md w-full sm:w-auto">Next
                                            </button>
                                        </div>
                                    </div>
                                    <div class="lmi-step" data-step="3" style="display:none;">

                                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mt-8">
                                            <div class="flex items-center gap-3 mb-3">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-blue-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                                    </svg>
                                                </div>
                                                <div class="text-sm font-bold text-gray-800 uppercase tracking-wide">
                                                    Part IV: Engagement &amp; Next Steps</div>
                                            </div>
                                            <div class="h-px bg-gray-100 mb-4"></div>
                                            <p class="text-gray-600 text-xs font-medium mb-4">Help us understand what
                                                features would be most valuable to you.</p>

                                            <div class="space-y-5">
                                                <div>
                                                    <label class="block text-gray-800 text-sm font-semibold mb-3">
                                                        If DOLE provides a Regional LMI Dashboard, what features would
                                                        be most useful for you? <span
                                                            class="text-gray-500 text-xs">(Select top 2)</span>
                                                    </label>
                                                    <div class="space-y-3" id="lmi-features-group">
                                                        <label
                                                            class="lmi-feature-label flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">
                                                            <input type="checkbox" name="lmi_features[]"
                                                                value="Viewing the supply of graduates"
                                                                class="lmi-feature-checkbox mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                            <div class="ml-3 flex-1">
                                                                <div class="font-semibold text-gray-900">Viewing the
                                                                    supply of graduates (e.g., "How many IT grads will
                                                                    graduate next year?")</div>
                                                            </div>
                                                        </label>
                                                        <label
                                                            class="lmi-feature-label flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">
                                                            <input type="checkbox" name="lmi_features[]"
                                                                value="A channel to submit real-time feedback"
                                                                class="lmi-feature-checkbox mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                            <div class="ml-3 flex-1">
                                                                <div class="font-semibold text-gray-900">A channel to
                                                                    submit real-time feedback on curriculum quality
                                                                </div>
                                                            </div>
                                                        </label>
                                                        <label
                                                            class="lmi-feature-label flex items-start p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition">
                                                            <input type="checkbox" name="lmi_features[]"
                                                                value="A directory of job placement offices"
                                                                class="lmi-feature-checkbox mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                            <div class="ml-3 flex-1">
                                                                <div class="font-semibold text-gray-900">A directory
                                                                    of job placement offices and Public Employment
                                                                    offices (PESOs)</div>
                                                            </div>
                                                        </label>
                                                        <div
                                                            class="lmi-other-option border rounded-lg border-gray-200 transition-all">
                                                            <label
                                                                class="lmi-feature-label flex items-start p-3 cursor-pointer hover:bg-blue-50 hover:border-blue-300">
                                                                <input type="checkbox" name="lmi_features[]"
                                                                    value="Other"
                                                                    class="lmi-feature-checkbox lmi-other-checkbox mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                                <div class="ml-3 flex-1">
                                                                    <div class="font-semibold text-gray-900">Other
                                                                        (please specify)</div>
                                                                </div>
                                                            </label>
                                                            <div class="lmi-other-input px-3 pb-3 ml-7 hidden">
                                                                <textarea name="lmi_features_other" placeholder="Please specify..." rows="2" enterkeyhint="done"
                                                                    class="other-specify-textarea focus-blue"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-gray-800 text-sm font-semibold mb-2">
                                                        Additional Insights or Suggestions: <span
                                                            class="text-gray-500 text-xs">(Optional)</span>
                                                    </label>
                                                    <textarea name="specific_inputs" rows="4"
                                                        placeholder="Please share any additional insights or suggestions..."
                                                        class="w-full px-3 py-2.5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none"
                                                        style="max-height:180px; overflow-y:auto;"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-6 mb-2">
                                            <label class="flex items-start cursor-pointer">
                                                <input type="checkbox" name="consent" value="1" required
                                                    class="consent-checkbox mt-1 w-4 h-4 text-teal-600">
                                                <span class="ml-3 text-l text-gray-700">
                                                    By proceeding, I signify my consent to the processing of my personal
                                                    data for labor market Information purposes, in accordance with RA
                                                    10173 (Data Privacy Act of 2012) and its IRR. <span
                                                        class="text-red-500">*</span>
                                                </span>
                                            </label>
                                        </div>

                                        <div class="flex flex-col-reverse sm:flex-row justify-between mt-6 gap-3">
                                            <button type="button"
                                                class="btn-prev bg-white hover:bg-gray-50 text-gray-700 font-semibold px-5 sm:px-8 py-2.5 rounded-lg transition border border-gray-300 shadow-sm w-full sm:w-auto">
                                                Previous</button>
                                            <button type="submit"
                                                class="btn-submit-lmi bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-5 sm:px-8 rounded-lg transition shadow-lg w-full sm:w-auto">
                                                Submit LMI Matrix
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="confirmation-modal" class="fixed inset-0 flex items-center justify-center px-4 hidden"
                    style="z-index: 9999;">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative p-6 z-10">
                        <div class="text-center">
                            <div
                                class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Submission</h3>
                            <p class="text-sm text-gray-500 mb-6">
                                Are you sure you want to submit this Industry Skills Need Survey? Please ensure all
                                information is accurate before proceeding.
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

            <div id="success-modal" class="fixed inset-0 flex items-center justify-center px-4 hidden"
                style="z-index: 9999;">
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative p-6 z-10">

                    <div class="text-center">
                        <div
                            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Successfully Submitted!</h3>
                        <p class="text-sm text-gray-500 mb-6">
                            Your Industry Skills Need Survey has been submitted successfully. Thank you for your
                            contribution to the Labor Market Information system.
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

    {{-- ─── Blade → JS Data Bridge ────────────────────────────────────────────── --}}
    {{-- PHP values that cannot live in .js files are passed here.               --}}
    <script>
        window._jobMarketData = {
            comparisonData: @json($comparison_data ?? []),
            selectedYear: {{ $selected_year ?? 'null' }},
            matrixResults: @json($matrix_results),
            archiveOptions: @json($archive_options ?? []),
            matrixDateOptions: @json($matrix_date_options ?? []),
            matrixSelectedYear: {{ $matrix_selected_year ?? 'null' }},
        };
    </script>
    @vite('resources/js/public/job-market-demands.js')

</html>