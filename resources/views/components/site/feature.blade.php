@props(['icon', 'title', 'dark' => false])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4']) }}>
    <x-site.icon-tile :icon="$icon" :tone="$dark ? 'dark' : 'lilac'" />
    <div>
        <h3 class="text-lg font-semibold {{ $dark ? 'text-white' : '' }}">{{ $title }}</h3>
        <p class="mt-1.5 {{ $dark ? 'text-white/70' : 'text-ink-600' }}">{{ $slot }}</p>
    </div>
</div>
