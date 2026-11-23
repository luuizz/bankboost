@props(['eyebrow' => null, 'title', 'text' => null, 'art' => null])

{{-- Primeira dobra das páginas internas: o cabeçalho fica por cima do gradiente --}}
<section class="hero-bg pt-32 pb-20 lg:pt-40 lg:pb-28">
    <div class="pointer-events-none absolute -top-40 -right-40 size-[36rem] rounded-full bg-brand-30/40 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-48 -left-32 size-[28rem] rounded-full bg-brand-50/30 blur-3xl"></div>

    <div class="container-site relative grid items-center gap-14 lg:grid-cols-2">
        <div data-aos="fade-right">
            @if ($eyebrow)
                <p class="font-title text-sm font-semibold tracking-wide text-accent uppercase">{{ $eyebrow }}</p>
            @endif
            <h1 class="mt-4 text-4xl leading-[1.1] font-bold text-white sm:text-5xl lg:text-6xl">{{ $title }}</h1>
            @if ($text)
                <p class="mt-6 max-w-xl text-lg text-white/80">{{ $text }}</p>
            @endif
            @if ($slot->isNotEmpty())
                <div class="mt-9 flex flex-wrap items-center gap-4">{{ $slot }}</div>
            @endif
        </div>

        @if ($art)
            <div class="relative" data-aos="fade-left">
                @include('site.art.'.$art)
            </div>
        @endif
    </div>
</section>
