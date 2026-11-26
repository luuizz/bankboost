<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import MoneyInput from '@/components/MoneyInput.vue';
import PanelLayout from '@/layouts/PanelLayout.vue';
import { firstError } from '@/lib/forms';
import { formatCents, newIdempotencyKey } from '@/lib/money';
import type { Account, Option, PixKey, PixRecipient } from '@/types';

const props = defineProps<{
    account: Account;
    keys: PixKey[];
    keyTypes: Option<PixKey['type']>[];
    maxKeys: number;
}>();

const tab = ref<'send' | 'keys'>('send');

/* Enviar */
const recipient = ref<PixRecipient | null>(null);
const lookupError = ref('');
const lookingUp = ref(false);
const idempotencyKey = ref(newIdempotencyKey());

const send = useForm({ key: '', amount: 0, description: '' });

async function lookup() {
    lookupError.value = '';
    recipient.value = null;
    lookingUp.value = true;

    try {
        const response = await fetch(`/painel/pix/consulta?key=${encodeURIComponent(send.key)}`, {
            headers: { Accept: 'application/json' },
        });
        const body = await response.json();

        if (!response.ok) {
            lookupError.value = body.message ?? 'Não foi possível consultar a chave.';

            return;
        }

        recipient.value = body.data as PixRecipient;
        idempotencyKey.value = newIdempotencyKey();
    } catch {
        lookupError.value = 'Não foi possível consultar a chave.';
    } finally {
        lookingUp.value = false;
    }
}

function resetRecipient() {
    recipient.value = null;
    send.reset('amount', 'description');
}

function submitPix() {
    send.post('/painel/pix/enviar', {
        headers: { 'Idempotency-Key': idempotencyKey.value },
    });
}

/* Chaves */
const keyForm = useForm({ type: 'random' as PixKey['type'], phone: '' });
const canAddKey = computed(() => props.keys.length < props.maxKeys);

function addKey() {
    keyForm.post('/painel/pix/chaves', {
        preserveScroll: true,
        onSuccess: () => keyForm.reset(),
    });
}

function removeKey(key: PixKey) {
    if (!confirm(`Remover a chave ${key.key}?`)) return;

    router.delete(`/painel/pix/chaves/${key.id}`, { preserveScroll: true });
}
</script>

<template>
    <PanelLayout title="Pix">
        <div class="mb-6 inline-grid grid-cols-2 rounded-xl bg-white p-1 text-sm font-semibold shadow-sm">
            <button type="button" class="rounded-lg px-5 py-2" :class="tab === 'send' ? 'bg-brand-40 text-white' : 'text-ink-600'" @click="tab = 'send'">
                Enviar
            </button>
            <button type="button" class="rounded-lg px-5 py-2" :class="tab === 'keys' ? 'bg-brand-40 text-white' : 'text-ink-600'" @click="tab = 'keys'">
                Minhas chaves
            </button>
        </div>

        <div v-if="tab === 'send'" class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <div class="card space-y-5">
                <form v-if="!recipient" class="space-y-4" @submit.prevent="lookup">
                    <div>
                        <label for="pix-key" class="label">Chave Pix do recebedor</label>
                        <input id="pix-key" v-model="send.key" placeholder="CPF, e-mail, celular ou chave aleatória" required class="input" />
                        <p v-if="lookupError" class="error">{{ lookupError }}</p>
                    </div>
                    <button type="submit" class="btn-primary w-full" :disabled="lookingUp || !send.key">Continuar</button>
                </form>

                <form v-else class="space-y-5" @submit.prevent="submitPix">
                    <div class="rounded-xl border border-ink-200 bg-ink-100 p-4">
                        <p class="text-sm text-ink-600">Para</p>
                        <p class="font-title text-lg font-semibold">{{ recipient.name }}</p>
                        <p class="text-sm text-ink-600">
                            CPF {{ recipient.document }} · {{ recipient.bank }} · Ag. {{ recipient.agency }} · Conta {{ recipient.number }}
                        </p>
                        <button type="button" class="mt-2 text-sm font-semibold text-brand-40 hover:underline" @click="resetRecipient">Trocar chave</button>
                    </div>

                    <div>
                        <label for="pix-amount" class="label">Valor</label>
                        <MoneyInput id="pix-amount" v-model="send.amount" autofocus />
                        <InputError :message="firstError(send.errors, 'amount', 'key', 'account', 'destination', 'idempotency_key')" />
                    </div>

                    <div>
                        <label for="pix-description" class="label">Mensagem (opcional)</label>
                        <input id="pix-description" v-model="send.description" maxlength="140" class="input" />
                    </div>

                    <button type="submit" class="btn-primary w-full" :disabled="send.processing || send.amount < 1 || send.amount > account.balance">
                        Enviar {{ send.amount ? formatCents(send.amount) : 'Pix' }}
                    </button>
                </form>
            </div>

            <aside class="card h-fit text-sm">
                <div class="flex justify-between">
                    <span class="text-ink-600">Saldo</span>
                    <strong class="tabular-nums">{{ account.balance_formatted }}</strong>
                </div>
                <p class="mt-3 border-t border-ink-200 pt-3 text-ink-600">
                    O Pix aqui é simulado entre contas Bankboost. Confira o nome do recebedor antes de confirmar.
                </p>
            </aside>
        </div>

        <div v-else class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <section class="card">
                <h2 class="font-title text-lg font-semibold">Chaves cadastradas ({{ keys.length }}/{{ maxKeys }})</h2>

                <ul v-if="keys.length" class="mt-2 divide-y divide-ink-200">
                    <li v-for="key in keys" :key="key.id" class="flex items-center justify-between gap-4 py-3">
                        <div class="min-w-0">
                            <p class="text-sm text-ink-600">{{ key.type_label }}</p>
                            <p class="truncate font-semibold">{{ key.key }}</p>
                        </div>
                        <button type="button" class="text-sm font-semibold text-red-600 hover:underline" @click="removeKey(key)">Remover</button>
                    </li>
                </ul>
                <p v-else class="mt-4 text-ink-600">Você ainda não tem chaves. Cadastre uma para receber Pix.</p>
            </section>

            <form class="card h-fit space-y-4" @submit.prevent="addKey">
                <h2 class="font-title text-lg font-semibold">Nova chave</h2>

                <div>
                    <label for="key-type" class="label">Tipo</label>
                    <select id="key-type" v-model="keyForm.type" class="input">
                        <option v-for="type in keyTypes" :key="type.value" :value="type.value">{{ type.label }}</option>
                    </select>
                    <p v-if="keyForm.type === 'cpf' || keyForm.type === 'email'" class="mt-1.5 text-xs text-ink-600">Usa o {{ keyForm.type === 'cpf' ? 'CPF' : 'e-mail' }} do seu cadastro.</p>
                </div>

                <div v-if="keyForm.type === 'phone'">
                    <label for="key-phone" class="label">Celular</label>
                    <input id="key-phone" v-model="keyForm.phone" type="tel" placeholder="(11) 98765-4321" class="input" />
                    <InputError :message="keyForm.errors.phone" />
                </div>

                <InputError :message="firstError(keyForm.errors, 'key', 'type')" />

                <button type="submit" class="btn-primary w-full" :disabled="keyForm.processing || !canAddKey">
                    {{ canAddKey ? 'Cadastrar chave' : 'Limite de chaves atingido' }}
                </button>
            </form>
        </div>
    </PanelLayout>
</template>
