{{--
    resources/views/sprs/partials/section-table.blade.php

    One section's figures, laid out the way the entry form lays them out —
    same Item column, same heading rows, same indentation. Read-only.

    Someone who fills the form should recognise this without translating
    between two shapes, which is why it isn't a generic key/value list.

    Expects: $rows (from sectionRows() or combinedRows()), $monthLabel
--}}

<table class="w-full border-collapse text-[13px]">
    <thead>
        <tr class="bg-slate-100">
            <th class="text-left px-4 py-2 font-medium text-slate-600">Item</th>
            <th class="text-right px-4 py-2 font-medium text-slate-600 w-[120px]">{{ $monthLabel }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rows as $row)
            @if ($row['header'])
                {{-- A heading from the paper form. No value of its own. --}}
                <tr class="bg-slate-50">
                    <td colspan="2" class="px-4 py-1.5 text-[12.5px] font-semibold text-slate-800 border-t border-slate-200">
                        {{ $row['label'] }}
                    </td>
                </tr>
            @else
                <tr>
                    <td class="py-1.5 pr-4 border-t border-slate-100 text-slate-600 {{ $row['indent'] ? 'pl-8' : 'pl-4' }}">
                        {{ $row['label'] }}
                        @if ($row['computed'])
                            <span class="ml-1 text-[10.5px] text-slate-400">calculated</span>
                        @endif
                    </td>
                    <td class="px-4 py-1.5 text-right border-t border-slate-100 tabular-nums
                               {{ $row['value'] === null ? 'text-slate-300' : 'font-semibold text-slate-900' }}">
                        {{-- Blank means no accomplishment, not zero. Kept
                             as a dash so it reads the same here as it does
                             in the form. --}}
                        {{ $row['value'] ?? '–' }}
                    </td>
                </tr>
            @endif
        @empty
            <tr>
                <td colspan="2" class="px-4 py-4 text-center text-[12.5px] text-slate-400 border-t border-slate-100">
                    Nothing recorded in this section.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>