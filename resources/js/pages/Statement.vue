<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import TransactionItem from '@/components/TransactionItem.vue';
import PanelLayout from '@/layouts/PanelLayout.vue';
import { formatCents } from '@/lib/money';
import type { Option, Paginated, Transaction, TransactionType } from '@/types';

interface Filters {
    type: TransactionType[];
    direction: '' | 'credit' | 'debit';
    from: string;
    to: string;
    min_amount: number | null;
    max_amount: number | null;
    search: string;
}

const props = defineProps<{
    transactions: Paginated<Transaction>;
    filters: Filters;
    summary: { credits: number; debits: number };
    types: Option<TransactionType>[];
}>();

// Valores dos campos em reais; a API recebe centavos.
const form = reactive({
    ...props.filters,
    min: props.filters.min_amount !== null ? String(props.filters.min_amount / 100) : '',
    max: props.filters.max_amount !== null ? String(props.filters.max_amount / 100) : '',
});

function toCents(value: string): number | undefined {
    const number = Number.parseFloat(value.replace(',', '.'));

    return Number.isFinite(number) ? Math.round(number * 100) : undefined;
}

function apply() {
    const query: Record<string, unknown> = {
        type: form.type.length ? form.type : undefined,
        direction: form.direction || undefined,
        from: form.from || undefined,
        to: form.to || undefined,
        min_amount: toCents(form.min),
        max_amount: toCents(form.max),
        search: form.search || undefined,
    };

    router.get('/painel/extrato', query as never, { preserveState: true, preserveScroll: true, replace: true });
}

function clear() {
    router.get('/painel/extrato');
}

const periods = [
    { label: '7 dias', days: 7 },
    { label: '30 dias', days: 30 },
    { label: '90 dias', days: 90 },
];

function lastDays(days: number) {
    const today = new Date();
    const start = new Date();
    start.setDate(today.getDate() - (days - 1));

    const iso = (date: Date) => date.toLocaleDateString('sv-SE');
    form.from = iso(start);
    form.to = iso(today);
    apply();
}
</script>

<template>
    <PanelLayout title="Extrato">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[18rem_minmax(0,1fr)]">
            <aside class="card h-fit">
                <form class="space-y-5" @submit.prevent="apply">
                    <div>
                        <label for="search" class="label">Buscar</label>
                        <input id="search" v-model="form.search" type="search" placeholder="Descrição ou nome" class="input" />
                    </div>

                    <fieldset>
                        <legend class="label">Período</legend>
                        <div class="mb-3 flex flex-wrap gap-2">
                            <button
                                v-for="period in periods"
                                :key="period.days"
                                type="button"
                                class="rounded-full border border-ink-300 px-3 py-1 text-xs font-semibold text-ink-700 hover:border-brand-30 hover:text-brand-40"
                                @click="lastDays(period.days)"
                            >
                                {{ period.label }}
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <input v-model="form.from" type="date" aria-label="De" class="input px-3 text-sm" />
                            <input v-model="form.to" type="date" aria-label="Até" class="input px-3 text-sm" />
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="label">Direção</legend>
                        <select v-model="form.direction" class="input">
                            <option value="">Entradas e saídas</option>
                            <option value="credit">Só entradas</option>
                            <option value="debit">Só saídas</option>
                        </select>
                    </fieldset>

                    <fieldset>
                        <legend class="label">Tipo</legend>
                        <div class="space-y-2">
                            <label v-for="type in types" :key="type.value" class="flex items-center gap-2 text-sm text-ink-700">
                                <input v-model="form.type" type="checkbox" :value="type.value" class="size-4 accent-brand-40" />
                                {{ type.label }}
                            </label>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="label">Valor (R$)</legend>
                        <div class="grid grid-cols-2 gap-2">
                            <input v-model="form.min" inputmode="decimal" placeholder="Mín." aria-label="Valor mínimo" class="input px-3 text-sm" />
                            <input v-model="form.max" inputmode="decimal" placeholder="Máx." aria-label="Valor máximo" class="input px-3 text-sm" />
                        </div>
                    </fieldset>

                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary flex-1">Filtrar</button>
                        <button type="button" class="btn-ghost" @click="clear">Limpar</button>
                    </div>

                    <p v-if="Object.keys($page.props.errors).length" class="error">Confira os filtros: {{ Object.values($page.props.errors)[0] }}</p>
                </form>
            </aside>

            <section class="card">
                <div class="grid gap-3 border-b border-ink-200 pb-4 sm:grid-cols-3">
                    <div>
                        <p class="text-sm text-ink-600">Entradas</p>
                        <p class="font-title text-lg font-semibold text-emerald-700 tabular-nums">{{ formatCents(summary.credits) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-ink-600">Saídas</p>
                        <p class="font-title text-lg font-semibold tabular-nums">{{ formatCents(summary.debits) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-ink-600">Resultado</p>
                        <p class="font-title text-lg font-semibold tabular-nums">{{ formatCents(summary.credits - summary.debits) }}</p>
                    </div>
                </div>

                <ul v-if="transactions.data.length" class="divide-y divide-ink-200">
                    <TransactionItem v-for="transaction in transactions.data" :key="transaction.id" :transaction="transaction" />
                </ul>
                <p v-else class="py-10 text-center text-ink-600">Nenhuma movimentação encontrada.</p>

                <nav v-if="transactions.meta.last_page > 1" class="mt-4 flex items-center justify-between border-t border-ink-200 pt-4 text-sm">
                    <Link
                        :href="transactions.links.prev ?? ''"
                        preserve-scroll
                        class="btn-ghost"
                        :class="{ 'pointer-events-none opacity-50': !transactions.links.prev }"
                    >
                        Anterior
                    </Link>
                    <span class="text-ink-600">
                        Página {{ transactions.meta.current_page }} de {{ transactions.meta.last_page }} · {{ transactions.meta.total }} lançamentos
                    </span>
                    <Link
                        :href="transactions.links.next ?? ''"
                        preserve-scroll
                        class="btn-ghost"
                        :class="{ 'pointer-events-none opacity-50': !transactions.links.next }"
                    >
                        Próxima
                    </Link>
                </nav>
            </section>
        </div>
    </PanelLayout>
</template>
