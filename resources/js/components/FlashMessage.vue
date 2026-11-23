<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import type { SharedProps } from '@/types';

const page = usePage<SharedProps>();
const visible = ref(false);
let timer: ReturnType<typeof setTimeout> | undefined;

watch(
    () => page.props.flash.success,
    (message) => {
        if (!message) return;

        visible.value = true;
        clearTimeout(timer);
        timer = setTimeout(() => (visible.value = false), 4000);
    },
    { immediate: true },
);
</script>

<template>
    <Transition
        enter-from-class="translate-y-2 opacity-0"
        enter-active-class="transition duration-200"
        leave-active-class="transition duration-200"
        leave-to-class="translate-y-2 opacity-0"
    >
        <div
            v-if="visible && page.props.flash.success"
            role="status"
            class="fixed bottom-6 left-1/2 z-50 -translate-x-1/2 rounded-xl bg-ink-900 px-5 py-3 text-sm font-semibold text-white shadow-lg"
        >
            {{ page.props.flash.success }}
        </div>
    </Transition>
</template>
