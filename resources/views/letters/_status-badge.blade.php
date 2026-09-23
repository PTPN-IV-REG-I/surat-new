@php
    $telat = ! $letter->follow_up
        && $letter->received_date
        && $letter->received_date->diffInDays(now()) >= 7;
@endphp
@if ($letter->follow_up)
    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Sudah Ditindaklanjuti</span>
@elseif ($telat)
    <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">Belum Ditindaklanjuti</span>
@else
    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">Belum Ditindaklanjuti</span>
@endif
