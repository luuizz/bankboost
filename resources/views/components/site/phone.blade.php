@props(['dark' => false])

{{-- Moldura de celular; o conteúdo da tela vem pelo slot --}}
<div {{ $attributes->merge(['class' => 'relative mx-auto w-full max-w-[17rem] rounded-[2.6rem] bg-ink-900 p-2.5 shadow-2xl shadow-brand-10/30 ring-1 ring-black/5']) }}>
    <div class="absolute top-2.5 left-1/2 z-10 h-6 w-24 -translate-x-1/2 rounded-b-2xl bg-ink-900"></div>
    <div class="relative aspect-[9/19] overflow-hidden rounded-[2.1rem] {{ $dark ? 'bg-brand-10 text-white' : 'bg-ink-100 text-ink-900' }}">
        {{ $slot }}
    </div>
</div>
