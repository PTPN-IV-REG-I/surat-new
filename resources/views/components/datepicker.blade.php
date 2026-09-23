@props([
    'name',
    'id' => null,
    'value' => null,
    'placeholder' => 'Pilih tanggal...',
    'mode' => 'single', // 'single' atau 'range'
    'wrapperClass' => '',
    'dateFormat' => 'Y-m-d',
    'altFormat' => 'd M Y',
])

@php
    $inputId = $id ?? $name;
    $val = old($name, $value ?? request($name));
@endphp

<div class="relative {{ $wrapperClass }}">
    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 z-10">
        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
    </div>

    <input
        type="text"
        name="{{ $name }}"
        id="{{ $inputId }}"
        value="{{ $val }}"
        placeholder="{{ $placeholder }}"
        data-mode="{{ $mode }}"
        data-date-format="{{ $dateFormat }}"
        data-alt-format="{{ $altFormat }}"
        {{ $attributes->merge([
            'class' => 'datepicker-input w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 pl-10 pr-8 text-xs text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63] cursor-pointer'
        ]) }}
    >

    @if ($val)
        <button type="button"
                class="datepicker-clear absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition cursor-pointer z-10"
                title="Hapus filter tanggal">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
        </button>
    @endif
</div>
