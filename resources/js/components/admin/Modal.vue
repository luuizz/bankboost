<script setup lang="ts">
import { PhX } from '@phosphor-icons/vue';
import { onBeforeUnmount, onMounted } from 'vue';

defineProps<{ title: string }>();
const emit = defineEmits<{ close: [] }>();

function onKey(event: KeyboardEvent) {
    if (event.key === 'Escape') emit('close');
}

onMounted(() => document.addEventListener('keydown', onKey));
onBeforeUnmount(() => document.removeEventListener('keydown', onKey));
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-black/40 p-4 sm:items-center" @click.self="emit('close')">
        <div class="max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-2xl bg-white shadow-2xl" role="dialog" aria-modal="true" :aria-label="title">
            <div class="flex items-center justify-between border-b border-ink-200 px-6 py-4">
                <h2 class="font-title text-lg font-semibold">{{ title }}</h2>
                <button type="button" class="rounded-lg p-1 text-ink-500 hover:bg-ink-100" aria-label="Fechar" @click="emit('close')"><PhX :size="20" /></button>
            </div>
            <div class="p-6"><slot /></div>
        </div>
    </div>
</template>
