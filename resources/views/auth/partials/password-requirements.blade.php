{{-- Password policy hint, kept in sync with Password::defaults() in AppServiceProvider --}}
<ul class="text-[12.5px] text-slate-500 mt-2 space-y-0.5 list-disc pl-5">
    <li>At least {{ config('security.password.min_length', 12) }} characters</li>
    <li>Upper-case and lower-case letters</li>
    <li>At least one number and one symbol</li>
    @if (config('security.password.uncompromised'))
        <li>Must not appear in a known data breach</li>
    @endif
</ul>
