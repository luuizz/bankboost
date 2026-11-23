@props(['eyebrow' => null, 'title', 'text' => null, 'align' => 'left', 'dark' => false])

<div {{ $attributes->merge(['class' => $align === 'center' ? 'mx-auto max-w-2xl text-center' : 'max-w-2xl']) }}>
    @if ($eyebrow)
        <p class="eyebrow {{ $dark ? 'text-accent' : '' }}">{{ $eyebrow }}</p>
    @endif
    <h2 class="mt-3 text-3xl leading-tight font-bold sm:text-4xl {{ $dark ? 'text-white' : '' }}">{{ $title }}</h2>
    @if ($text)
        <p class="mt-4 text-lg {{ $dark ? 'text-white/75' : 'text-ink-600' }}">{{ $text }}</p>
    @endif
</div>
