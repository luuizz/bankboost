@props(['variant' => 'purple', 'name' => 'Ana Souza', 'label' => null])

@php
    $bg = [
        'purple' => 'from-brand-30 via-brand-40 to-brand-10',
        'black' => 'from-ink-800 via-ink-900 to-black',
        'gold' => 'from-[#f3d34a] via-accent to-[#b68f00]',
    ][$variant];
    $text = $variant === 'gold' ? 'text-brand-10' : 'text-white';
@endphp

{{-- Cartão desenhado em HTML/CSS: escala bem e troca de cor sem precisar de imagem --}}
<div {{ $attributes->merge(['class' => "relative aspect-[1.586] w-full overflow-hidden rounded-[1.4rem] bg-linear-to-br $bg p-[7%] shadow-2xl shadow-brand-10/40 $text"]) }}>
    <div class="pointer-events-none absolute -top-1/2 -right-1/4 size-[120%] rounded-full bg-white/10"></div>
    <div class="relative flex h-full flex-col justify-between">
        <div class="flex items-start justify-between">
            <span class="font-title text-[clamp(0.9rem,2.2vw,1.35rem)] font-bold tracking-tight">Bank<span class="font-medium">boost</span></span>
            @if ($label)
                <span class="rounded-full bg-white/15 px-2.5 py-0.5 text-[0.65rem] font-semibold tracking-wide uppercase">{{ $label }}</span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <span class="h-[18%] min-h-6 w-[14%] rounded-md bg-linear-to-br from-[#f7e7a1] to-[#c9a227]"></span>
            @svg('phosphor-contactless-payment', 'size-6 opacity-80')
        </div>
        <div class="flex items-end justify-between">
            <span class="text-[clamp(0.6rem,1.4vw,0.8rem)] font-semibold tracking-[0.2em] uppercase opacity-90">{{ $name }}</span>
            <span class="flex">
                <span class="size-7 rounded-full bg-white/70"></span>
                <span class="-ml-3 size-7 rounded-full bg-white/40"></span>
            </span>
        </div>
    </div>
</div>
