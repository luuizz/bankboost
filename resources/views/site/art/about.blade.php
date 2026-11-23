@php
    $people = [
        ['C', 'bg-accent text-brand-10', 'top-0 left-8'],
        ['R', 'bg-white text-brand-40', 'top-10 right-6'],
        ['L', 'bg-emerald-400 text-white', 'bottom-24 left-0'],
        ['M', 'bg-brand-50 text-white ring-4 ring-white/30', 'right-20 bottom-6'],
        ['J', 'bg-pink-400 text-white', 'bottom-4 left-28'],
    ];
@endphp

<div class="relative mx-auto aspect-square max-w-md">
    <div class="absolute inset-12 rounded-full border border-dashed border-white/30"></div>
    <div class="absolute inset-28 rounded-full border border-dashed border-white/20"></div>
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="flex size-36 flex-col items-center justify-center rounded-full bg-white text-center text-brand-10 shadow-2xl">
            <span class="font-title text-3xl font-bold">3 mi</span>
            <span class="text-xs font-semibold text-ink-600">de clientes</span>
        </div>
    </div>
    @foreach ($people as $i => [$initial, $color, $position])
        <span class="animate-float absolute {{ $position }} flex size-16 items-center justify-center rounded-full font-title text-xl font-bold shadow-xl {{ $color }}" style="animation-delay: -{{ $i * 1.2 }}s">{{ $initial }}</span>
    @endforeach
    <span class="absolute right-0 bottom-36 flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-ink-900 shadow-xl">@svg('phosphor-heart-fill', 'size-4 text-pink-500') 4,8 nas lojas</span>
</div>
