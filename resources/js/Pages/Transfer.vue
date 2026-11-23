<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import MoneyInput from '@/Components/MoneyInput.vue';
import PanelLayout from '@/Layouts/PanelLayout.vue';
import { firstError } from '@/lib/forms';
import { formatCents, newIdempotencyKey } from '@/lib/money';
import type { Account } from '@/types';

const props = defineProps<{
    account: Account;
    limits: { max_transfer: number; daily_outgoing: number };
}>();

const form = useForm({
    agency: '0001',
    number: '',
    amount: 0,
    description: '',
});

// Uma chave por tentativa: se a requisição for repetida (duplo clique, rede), o débito não duplica.
const idempotencyKey = ref(newIdempotencyKey());
const confirming = ref(false);

const exceedsBalance = computed(() => form.amount > props.account.balance);

function back() {
    confirming.value = false;
    idempotencyKey.value = newIdempotencyKey();
}

function submit() {
    form.post('/painel/transferir', {
        headers: { 'Idempotency-Key': idempotencyKey.value },
        onError: () => (confirming.value = false),
    });
}
</script>

<template>
    <PanelLayout title="Transferir">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <form class="card space-y-5" @submit.prevent="confirming ? submit() : (confirming = true)">
                <div class="grid gap-5 sm:grid-cols-[8rem_1fr]">
                    <div>
                        <label for="agency" class="label">Agência</label>
                        <input id="agency" v-model="form.agency" maxlength="4" inputmode="numeric" required class="input" :disabled="confirming" />
                    </div>
                    <div>
                        <label for="number" class="label">Conta</label>
                        <input id="number" v-model="form.number" placeholder="123456-7" maxlength="10" required class="input" :disabled="confirming" />
                    </div>
                </div>
                <InputError :message="firstError(form.errors, 'agency', 'number', 'destination')" />

                <div>
                    <label for="amount" class="label">Valor</label>
                    <MoneyInput id="amount" v-model="form.amount" />
                    <p v-if="exceedsBalance" class="error">Valor maior que o saldo disponível.</p>
                    <InputError :message="firstError(form.errors, 'amount', 'account', 'idempotency_key')" />
                </div>

                <div>
                    <label for="description" class="label">Descrição (opcional)</label>
                    <input id="description" v-model="form.description" maxlength="140" class="input" :disabled="confirming" />
                </div>

                <div v-if="confirming" class="rounded-xl bg-brand-30/10 p-4 text-sm text-ink-800">
                    Confirma a transferência de <strong>{{ formatCents(form.amount) }}</strong> para a conta
                    <strong>{{ form.agency }} / {{ form.number }}</strong>?
                </div>

                <div class="flex gap-3">
                    <button v-if="confirming" type="button" class="btn-ghost" @click="back">Voltar</button>
                    <button type="submit" class="btn-primary flex-1" :disabled="form.processing || form.amount < 1 || exceedsBalance">
                        {{ confirming ? 'Confirmar transferência' : 'Continuar' }}
                    </button>
                </div>
            </form>

            <aside class="card h-fit space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-ink-600">Saldo</span>
                    <strong class="tabular-nums">{{ account.balance_formatted }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-600">Máximo por transferência</span>
                    <strong class="tabular-nums">{{ formatCents(limits.max_transfer) }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-600">Limite diário de envio</span>
                    <strong class="tabular-nums">{{ formatCents(limits.daily_outgoing) }}</strong>
                </div>
                <p class="border-t border-ink-200 pt-3 text-ink-600">Transferências entre contas Bankboost caem na hora, a qualquer momento.</p>
            </aside>
        </div>
    </PanelLayout>
</template>
