{{--
    resources/views/sprs/partials/compilation-breakdown.blade.php

    What an office compiled, shown inside its expanded card.

    The card carries the total that office forwarded — its own report plus
    everything it gathered. This splits that total into its parts, the
    office's own report first.

    It's what keeps the compilation list to one tier: PESO Bansalan appears
    in Digos's list, UM Bansalan College doesn't, and Digos reaches it by
    asking rather than having it mixed in with its direct reports.

    Reads `breakdown` and `showBreakdown` from the surrounding Alpine
    scope, so it takes no props.
--}}

{{-- What this office compiled.

     The card shows that office's own figures. The
     offices behind them are one click further in,
     which is what keeps the compilation list to
     one tier — PESO Bansalan appears in Digos's
     list, UM Bansalan College doesn't, and Digos
     reaches it by asking. --}}
<template x-if="breakdown.length > 0">
    <div class="border-t border-slate-200">
        <button type="button" x-on:click="showBreakdown = !showBreakdown"
            class="w-full px-4 py-2 text-left text-[12px] font-semibold text-teal-700 hover:bg-slate-50">
            <span x-text="showBreakdown ? 'Hide' : 'Show'"></span>
            what this office compiled
            (<span x-text="breakdown.length"></span>)
            <span x-text="showBreakdown ? '▴' : '▾'"></span>
        </button>

        <div x-show="showBreakdown" x-cloak class="bg-slate-50 px-4 py-2">
            <template x-for="(sub, si) in breakdown" :key="si">
                <div class="mb-2 last:mb-0">
                    <p class="text-[12.5px] font-semibold text-slate-700 py-1"
                       :style="`padding-left:${sub.depth * 14}px`">
                        <span x-text="sub.office"></span>
                        <span class="text-[10.5px] font-normal text-slate-400" x-text="sub.role"></span>
                        <span x-show="!sub.status" class="ml-1 text-[10.5px] text-red-600">not started</span>
                        <span x-show="sub.status === 'draft'" class="ml-1 text-[10.5px] text-slate-500">draft</span>
                    </p>

                    <template x-if="sub.rows.length > 0">
                        <div class="bg-white rounded border border-slate-200 overflow-hidden"
                             :style="`margin-left:${sub.depth * 14}px`">
                            <table class="w-full border-collapse text-[12.5px]">
                                <template x-for="(row, ri) in sub.rows" :key="ri">
                                    <tr :class="row.header ? 'bg-slate-50' : ''">
                                        <template x-if="row.header">
                                            <td colspan="2" class="px-3 py-1 text-[11.5px] font-semibold text-slate-700 border-t border-slate-100" x-text="row.label"></td>
                                        </template>
                                        <template x-if="!row.header">
                                            <td class="py-1 pr-3 border-t border-slate-100 text-slate-600"
                                                :class="row.indent ? 'pl-6' : 'pl-3'" x-text="row.label"></td>
                                        </template>
                                        <template x-if="!row.header">
                                            <td class="px-3 py-1 text-right border-t border-slate-100 tabular-nums w-[90px]"
                                                :class="row.value === null ? 'text-slate-300' : 'font-semibold text-slate-900'"
                                                x-text="row.value === null ? '–' : row.value"></td>
                                        </template>
                                    </tr>
                                </template>
                            </table>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</template>