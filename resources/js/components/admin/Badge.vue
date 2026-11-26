<script setup lang="ts">
withDefaults(defineProps<{ tone?: 'green' | 'red' | 'yellow' | 'gray' | 'brand' }>(), { tone: 'gray' });

const tones = {
    green: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    red: 'bg-red-50 text-red-700 ring-red-600/20',
    yellow: 'bg-amber-50 text-amber-800 ring-amber-600/20',
    gray: 'bg-ink-100 text-ink-700 ring-ink-500/20',
    brand: 'bg-lilac-50 text-brand-40 ring-brand-30/20',
};
</script>

<template>
    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset" :class="tones[tone]">
        <slot />
    </span>
</template>
