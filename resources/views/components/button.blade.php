@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'size' => 'md',
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 rounded-xl text-xs font-bold transition-all duration-150 ease-in-out cursor-pointer select-none focus:outline-none focus:ring-2 focus:ring-offset-1 shrink-0';

    $sizeClasses = match ($size) {
        'sm' => 'h-8 px-3 text-[11px]',
        'lg' => 'h-12 px-5 text-sm',
        default => 'h-10 px-4 text-xs', // Identik 40px (h-10) dengan search textbox
    };

    $variantClasses = match ($variant) {
        'secondary', 'outline' => 'border border-slate-200 bg-white text-slate-700 shadow-2xs hover:bg-slate-50 hover:border-slate-300 active:bg-slate-100 focus:ring-[#033F63]/30',
        'danger', 'rose' => 'border border-rose-200 bg-rose-50/70 text-rose-600 hover:bg-rose-100 hover:border-rose-300 active:bg-rose-200/60 focus:ring-rose-400/40',
        'amber' => 'border border-amber-200 bg-amber-50/70 text-amber-700 hover:bg-amber-100 hover:border-amber-300 focus:ring-amber-400/40',
        'sky' => 'border border-sky-200 bg-sky-50/70 text-sky-600 hover:bg-sky-100 hover:border-sky-300 focus:ring-sky-400/40',
        'ghost' => 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 border border-transparent',
        default => 'border border-transparent bg-[#033F63] text-white shadow-xs hover:bg-[#022B44] active:bg-[#011724] focus:ring-[#033F63]/40',
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
