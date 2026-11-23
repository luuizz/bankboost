@props(['href' => '#', 'variant' => 'primary', 'icon' => null, 'size' => 'md'])

@php
    $styles = [
        'primary' => 'bg-accent text-brand-10 hover:bg-[#f5d220] shadow-lg shadow-black/10',
        'brand' => 'bg-brand-40 text-white hover:bg-brand-20',
        'outline-light' => 'border border-white/40 text-white hover:bg-white/10',
        'outline' => 'border border-ink-300 text-ink-800 hover:border-brand-30 hover:text-brand-40',
        'light' => 'bg-white text-brand-10 hover:bg-lilac-50',
    ];
    $sizes = ['md' => 'px-6 py-3 text-sm', 'lg' => 'px-7 py-3.5 text-base'];
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center gap-2 rounded-full font-title font-semibold transition '.$styles[$variant].' '.$sizes[$size]]) }}>
    {{ $slot }}
    @if ($icon)
        @svg('phosphor-'.$icon, 'size-5')
    @endif
</a>
