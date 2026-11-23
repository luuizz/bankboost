@props(['icon', 'tone' => 'lilac', 'size' => 'md'])

@php
    $tones = [
        'lilac' => 'bg-lilac-100 text-brand-40',
        'dark' => 'bg-white/10 text-white ring-1 ring-white/15',
        'accent' => 'bg-accent/20 text-[#8a6d00]',
        'white' => 'bg-white text-brand-40 shadow-sm',
    ];
    $sizes = [
        'sm' => 'size-10 rounded-xl [&>svg]:size-5',
        'md' => 'size-12 rounded-2xl [&>svg]:size-6',
        'round' => 'size-11 rounded-full [&>svg]:size-5',
        'lg' => 'size-16 rounded-3xl [&>svg]:size-8',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center '.$tones[$tone].' '.$sizes[$size]]) }}>
    @svg('phosphor-'.$icon.'-duotone')
</span>
