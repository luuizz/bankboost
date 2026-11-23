<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import MoneyInput from '@/components/MoneyInput.vue';
import TransactionItem from '@/components/TransactionItem.vue';
import PanelLayout from '@/layouts/PanelLayout.vue';
import { firstError } from '@/lib/forms';
import { formatCents } from '@/lib/money';
import type { Account, Transaction } from '@/types';

defineProps<{
    account: Account;
    recent: Transaction[];
    month: { credits: number; debits: number };
    pixKeys: number;
}>();

const showBalance = ref(true);
const mode = ref<'deposit' | 'withdraw'>('deposit');

const movement = useForm({ amount: 0, description: '' });

function submitMovement() {
    const url = mode.value === 'deposit' ? '/painel/depositos' : '/painel/saques';

    movement.post(url, {
        preserveScroll: true,
        onSuccess: () => movement.reset(),
    });
}
</script>

<template>
    <PanelLayout title="Início">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <section class="card relative overflow-hidden border-0 bg-linear-to-br from-brand-20 to-brand-10 text-white lg:col-span-2">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-white/70">Saldo disponível</p>
                        <p class="mt-1 font-title text-4xl font-bold tabular-nums">
                            {{ showBalance ? account.balance_formatted : 'R$ ••••••' }}
                        </p>
                    </div>
                    <button type="button" class="rounded-lg px-3 py-1.5 text-sm text-white/80 hover:bg-white/10" @click="showBalance = !showBalance">
                        {{ showBalance ? 'Ocultar' : 'Mostrar' }}
                    </button>
                </div>

                <p class="mt-6 text-sm text-white/70">
                    Agência <strong class="text-white">{{ account.agency }}</strong> · Conta
                    <strong class="text-white">{{ account.number }}</strong>
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <Link href="/painel/pix" class="btn-primary bg-white text-brand-10 hover:bg-ink-100">Pix</Link>
                    <Link href="/painel/transferir" class="btn-ghost border-white/30 bg-transparent text-white hover:border-white hover:text-white">Transferir</Link>
                    <Link href="/painel/extrato" class="btn-ghost border-white/30 bg-transparent text-white hover:border-white hover:text-white">Extrato</Link>
                </div>
            </section>

            <section class="card">
                <h2 class="font-title text-lg font-semibold">Neste mês</h2>
                <dl class="mt-4 space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-ink-600">Entradas</dt>
                        <dd class="font-semibold text-emerald-700 tabular-nums">{{ formatCents(month.credits) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-600">Saídas</dt>
                        <dd class="font-semibold tabular-nums">{{ formatCents(month.debits) }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-ink-200 pt-3">
                        <dt class="text-ink-600">Chaves Pix</dt>
                        <dd class="font-semibold">{{ pixKeys }}</dd>
                    </div>
                </dl>
            </section>

            <section class="card lg:col-span-2">
                <div class="flex items-center justify-between">
                    <h2 class="font-title text-lg font-semibold">Últimas movimentações</h2>
                    <Link href="/painel/extrato" class="text-sm font-semibold text-brand-40 hover:underline">Ver extrato</Link>
                </div>

                <ul v-if="recent.length" class="mt-2 divide-y divide-ink-200">
                    <TransactionItem v-for="transaction in recent" :key="transaction.id" :transaction="transaction" />
                </ul>
                <p v-else class="mt-4 text-ink-600">Nenhuma movimentação ainda. Que tal fazer um depósito simulado?</p>
            </section>

            <section class="card">
                <h2 class="font-title text-lg font-semibold">Movimentação simulada</h2>
                <p class="mt-1 text-sm text-ink-600">Dinheiro de mentira para testar a conta.</p>

                <div class="mt-4 grid grid-cols-2 rounded-xl bg-ink-100 p-1 text-sm font-semibold">
                    <button
                        v-for="option in [
                            { value: 'deposit', label: 'Depositar' },
                            { value: 'withdraw', label: 'Sacar' },
                        ] as const"
                        :key="option.value"
                        type="button"
                        class="rounded-lg py-2 transition"
                        :class="mode === option.value ? 'bg-white text-brand-40 shadow-sm' : 'text-ink-600'"
                        @click="mode = option.value"
                    >
                        {{ option.label }}
                    </button>
                </div>

                <form class="mt-4 space-y-4" @submit.prevent="submitMovement">
                    <div>
                        <label for="movement-amount" class="label">Valor</label>
                        <MoneyInput id="movement-amount" v-model="movement.amount" />
                        <InputError :message="firstError(movement.errors, 'amount', 'account')" />
                    </div>
                    <div>
                        <label for="movement-description" class="label">Descrição (opcional)</label>
                        <input id="movement-description" v-model="movement.description" maxlength="140" class="input" />
                    </div>
                    <button type="submit" class="btn-primary w-full" :disabled="movement.processing || movement.amount < 1">
                        {{ mode === 'deposit' ? 'Depositar' : 'Sacar' }}
                    </button>
                </form>
            </section>
        </div>
    </PanelLayout>
</template>
