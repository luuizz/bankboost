<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { PhCaretLeft, PhCaretRight } from '@phosphor-icons/vue';

defineProps<{
    prev: string | null;
    next: string | null;
    current: number;
    last: number;
    total: number;
}>();
</script>

<template>
    <nav v-if="last > 1" class="flex items-center justify-between gap-4 border-t border-ink-200 px-5 py-4 text-sm" aria-label="Paginação">
        <span class="text-ink-500">{{ total }} registros · página {{ current }} de {{ last }}</span>
        <div class="flex gap-2">
            <Link :href="prev ?? ''" preserve-scroll class="btn-ghost px-3 py-2" :class="{ 'pointer-events-none opacity-40': !prev }" aria-label="Anterior">
                <PhCaretLeft :size="16" />
            </Link>
            <Link :href="next ?? ''" preserve-scroll class="btn-ghost px-3 py-2" :class="{ 'pointer-events-none opacity-40': !next }" aria-label="Próxima">
                <PhCaretRight :size="16" />
            </Link>
        </div>
    </nav>
</template>
