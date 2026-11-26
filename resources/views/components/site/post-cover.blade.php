@props(['post', 'large' => false, 'wide' => false])

@php
    $aspect = $wide ? 'aspect-[16/10] sm:aspect-[21/9]' : 'aspect-[16/10]';
@endphp

@if ($post->cover_url)
    <img src="{{ $post->cover_url }}" alt="" loading="lazy" {{ $attributes->merge(['class' => $aspect.' w-full object-cover']) }}>
@else
    {{-- Capa gerada: gradiente e ícone da categoria, para posts sem imagem --}}
    @php
        $category = $post->category_meta;
    @endphp
    <div {{ $attributes->merge(['class' => 'relative flex '.$aspect.' w-full items-center justify-center overflow-hidden bg-linear-to-br '.$category['gradient']]) }}>
        <div class="absolute inset-0 opacity-20 [background-image:radial-gradient(circle_at_1px_1px,white_1px,transparent_0)] [background-size:22px_22px]"></div>
        <div class="absolute -right-10 -bottom-10 size-48 rounded-full bg-white/10"></div>
        <div class="absolute -top-8 -left-8 size-32 rounded-full bg-white/10"></div>
        @svg('phosphor-'.$category['icon'].'-duotone', ($large ? 'size-32' : 'size-20').' relative text-white drop-shadow-lg')
    </div>
@endif
