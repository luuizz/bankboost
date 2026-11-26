<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { PhEnvelopeSimple, PhLightning, PhShieldCheck, PhUserPlus, PhUsersThree, PhWallet, PhWarning } from '@phosphor-icons/vue';
import { computed } from 'vue';
import TransactionItem from '@/components/TransactionItem.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { formatCents } from '@/lib/money';
import type { Transaction } from '@/types';

const props = defineProps<{
    stats: {
        customers: number;
        activeAccounts: number;
        blockedAccounts: number;
        deposits: number;
        newCustomers: number;
        transfersToday: number;
        creditsMonth: number;
        publishedPosts: number;
        unreadMessages: number;
    };
    volume: { date: string; total: number; count: number }[];
    audit: { ok: boolean; divergent: number; checked_at: string };
    latest: Transaction[];
}>();

const cards = computed(() => [
    { label: 'Clientes', value: props.stats.customers.toLocaleString('pt-BR'), hint: `${props.stats.blockedAccounts} bloqueados`, icon: PhUsersThree },
    { label: 'Dinheiro em contas', value: formatCents(props.stats.deposits), hint: `${formatCents(props.stats.creditsMonth)} de entradas no mês`, icon: PhWallet },
    { label: 'Novos em 30 dias', value: props.stats.newCustomers.toLocaleString('pt-BR'), hint: 'contas abertas', icon: PhUserPlus },
    { label: 'Transferências hoje', value: props.stats.transfersToday.toLocaleString('pt-BR'), hint: 'internas e Pix', icon: PhLightning },
]);

const max = computed(() => Math.max(1, ...props.volume.map((day) => day.total)));
const totalVolume = computed(() => props.volume.reduce((sum, day) => sum + day.total, 0));

function dayLabel(date: string): string {
    return new Date(`${date}T12:00:00`).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
}
</script>

<template>
    <AdminLayout title="Visão geral">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div v-for="card in cards" :key="card.label" class="card p-5">
                <div class="flex items-center justify-between text-ink-600">
                    <span class="text-sm font-semibold">{{ card.label }}</span>
                    <component :is="card.icon" :size="22" weight="duotone" class="text-brand-40" />
                </div>
                <p class="mt-3 font-title text-2xl font-bold tabular-nums">{{ card.value }}</p>
                <p class="mt-1 text-sm text-ink-500">{{ card.hint }}</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
            <section class="card xl:col-span-2">
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <h2 class="font-title text-lg font-semibold">Volume de transferências</h2>
                    <p class="text-sm text-ink-500">Últimos 14 dias · <strong class="text-ink-900">{{ formatCents(totalVolume) }}</strong></p>
                </div>
                <div class="mt-6 flex h-48 items-end gap-1.5 sm:gap-2" role="img" aria-label="Gráfico de barras do volume diário">
                    <div v-for="day in volume" :key="day.date" class="group relative flex h-full flex-1 flex-col justify-end">
                        <div
                            class="rounded-t-md bg-brand-30/80 transition group-hover:bg-brand-40"
                            :style="{ height: `${Math.max(2, (day.total / max) * 100)}%` }"
                        ></div>
                        <span class="pointer-events-none absolute -top-9 left-1/2 hidden -translate-x-1/2 rounded-lg bg-ink-900 px-2 py-1 text-xs whitespace-nowrap text-white group-hover:block">
                            {{ formatCents(day.total) }} · {{ day.count }}
                        </span>
                    </div>
                </div>
                <div class="mt-2 flex gap-1.5 text-[0.65rem] text-ink-500 sm:gap-2">
                    <span v-for="day in volume" :key="day.date" class="flex-1 text-center">{{ dayLabel(day.date) }}</span>
                </div>
            </section>

            <div class="space-y-6">
                <section class="card" :class="audit.ok ? '' : 'border-red-300 bg-red-50'">
                    <div class="flex items-start gap-3">
                        <component :is="audit.ok ? PhShieldCheck : PhWarning" :size="28" weight="duotone" :class="audit.ok ? 'text-emerald-600' : 'text-red-600'" />
                        <div>
                            <h2 class="font-title font-semibold">Saúde do livro-razão</h2>
                            <p class="mt-1 text-sm text-ink-600">
                                <template v-if="audit.ok">Todos os saldos batem com a soma dos lançamentos.</template>
                                <template v-else>{{ audit.divergent }} conta(s) com saldo diferente dos lançamentos. Rode <code>bankboost:audit</code> para detalhes.</template>
                            </p>
                        </div>
                    </div>
                </section>

                <Link href="/admin/mensagens?nao_lidas=1" class="card flex items-center gap-3 transition hover:border-brand-30">
                    <PhEnvelopeSimple :size="28" weight="duotone" class="text-brand-40" />
                    <div class="flex-1">
                        <h2 class="font-title font-semibold">Mensagens do site</h2>
                        <p class="text-sm text-ink-600">{{ stats.unreadMessages }} não lida(s)</p>
                    </div>
                </Link>

                <Link href="/admin/posts" class="card block transition hover:border-brand-30">
                    <h2 class="font-title font-semibold">Blog</h2>
                    <p class="text-sm text-ink-600">{{ stats.publishedPosts }} posts publicados</p>
                </Link>
            </div>
        </div>

        <section class="card mt-6">
            <div class="flex items-center justify-between">
                <h2 class="font-title text-lg font-semibold">Últimos lançamentos</h2>
                <Link href="/admin/transacoes" class="text-sm font-semibold text-brand-40 hover:underline">Ver todos</Link>
            </div>
            <ul v-if="latest.length" class="mt-2 divide-y divide-ink-200">
                <TransactionItem v-for="transaction in latest" :key="transaction.id" :transaction="transaction" admin />
            </ul>
            <p v-else class="mt-4 text-ink-600">Nenhum lançamento ainda.</p>
        </section>
    </AdminLayout>
</template>
