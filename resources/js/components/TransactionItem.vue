<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { formatDateTime } from '@/lib/money';
import type { Transaction } from '@/types';

const props = defineProps<{ transaction: Transaction }>();

const isCredit = computed(() => props.transaction.direction === 'credit');

// Com contraparte, o nome é o título e a descrição vai para a linha de baixo.
const title = computed(() => props.transaction.counterparty_name ?? props.transaction.description ?? props.transaction.type_label);
const subtitle = computed(() =>
    props.transaction.counterparty_name && props.transaction.description
        ? `${props.transaction.type_label} · ${props.transaction.description}`
        : props.transaction.type_label,
);

const icon = computed(() => {
    switch (props.transaction.type) {
        case 'pix_in':
        case 'pix_out':
            return 'PIX';
        case 'deposit':
            return '+';
        case 'withdrawal':
            return '−';
        default:
            return '⇄';
    }
});
</script>

<template>
    <li class="flex items-center gap-4 py-3.5">
        <span
            class="flex size-10 shrink-0 items-center justify-center rounded-full font-title text-xs font-bold"
            :class="isCredit ? 'bg-emerald-50 text-emerald-700' : 'bg-ink-100 text-ink-700'"
        >
            {{ icon }}
        </span>

        <div class="min-w-0 flex-1">
            <p class="truncate font-semibold text-ink-900">{{ title }}</p>
            <p class="truncate text-sm text-ink-600">{{ subtitle }}</p>
        </div>

        <div class="text-right">
            <p class="font-title font-semibold tabular-nums" :class="isCredit ? 'text-emerald-700' : 'text-ink-900'">
                {{ isCredit ? '+' : '' }}{{ transaction.amount_formatted }}
            </p>
            <p class="text-xs text-ink-500">
                <Link
                    v-if="transaction.transfer_id"
                    :href="`/painel/comprovantes/${transaction.transfer_id}`"
                    class="hover:text-brand-40 hover:underline"
                >
                    {{ formatDateTime(transaction.created_at) }}
                </Link>
                <template v-else>{{ formatDateTime(transaction.created_at) }}</template>
            </p>
        </div>
    </li>
</template>
