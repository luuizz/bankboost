<script setup lang="ts">
import { computed } from 'vue';

const cents = defineModel<number>({ required: true });

defineProps<{ id?: string; autofocus?: boolean }>();

const display = computed(() =>
    new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(cents.value / 100),
);

// Digitar funciona como numa maquininha: cada número entra pela direita.
function onInput(event: Event) {
    const target = event.target as HTMLInputElement;
    const digits = target.value.replace(/\D/g, '').slice(0, 11);

    cents.value = digits === '' ? 0 : Number.parseInt(digits, 10);
    target.value = display.value;
}
</script>

<template>
    <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center font-semibold text-ink-500">R$</span>
        <input
            :id="id"
            :value="display"
            :autofocus="autofocus"
            type="text"
            inputmode="numeric"
            autocomplete="off"
            class="input pl-11 text-right font-title text-lg font-semibold tabular-nums"
            @input="onInput"
        />
    </div>
</template>
