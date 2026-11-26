@props(['value', 'label', 'dark' => false])

<div {{ $attributes }}>
    <p class="font-title text-4xl font-bold {{ $dark ? 'text-white' : 'text-brand-40' }} sm:text-5xl">{{ $value }}</p>
    <p class="mt-2 {{ $dark ? 'text-white/70' : 'text-ink-600' }}">{{ $label }}</p>
</div>
