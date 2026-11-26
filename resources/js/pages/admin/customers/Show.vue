<script setup lang="ts">
import { Link, router, useForm } from '@inertiajs/vue3';
import { PhArrowLeft, PhLock, PhLockOpen, PhWarning } from '@phosphor-icons/vue';
import { ref } from 'vue';
import Badge from '@/components/admin/Badge.vue';
import Modal from '@/components/admin/Modal.vue';
import InputError from '@/components/InputError.vue';
import TransactionItem from '@/components/TransactionItem.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { formatCents, formatDateTime } from '@/lib/money';
import type { Paginated, PixKey, Transaction } from '@/types';
import type { AdminAccount } from '@/types/admin';

const props = defineProps<{
    account: AdminAccount & { blocked_reason: string | null; blocked_at: string | null; computed_balance: number };
    pixKeys: PixKey[];
    transactions: Paginated<Transaction>;
}>();

const blocking = ref(false);
const blockForm = useForm({ reason: '' });

function block() {
    blockForm.post(`/admin/clientes/${props.account.id}/bloquear`, {
        preserveScroll: true,
        onSuccess: () => {
            blocking.value = false;
            blockForm.reset();
        },
    });
}

function unblock() {
    if (!confirm('Desbloquear esta conta?')) return;

    router.post(`/admin/clientes/${props.account.id}/desbloquear`, {}, { preserveScroll: true });
}
</script>

<template>
    <AdminLayout :title="account.holder.name">
        <template #actions>
            <button v-if="account.status === 'active'" type="button" class="btn-ghost border-red-200 text-red-700 hover:border-red-400 hover:text-red-700" @click="blocking = true">
                <PhLock :size="18" /> Bloquear
            </button>
            <button v-else type="button" class="btn-primary" @click="unblock"><PhLockOpen :size="18" /> Desbloquear</button>
        </template>

        <Link href="/admin/clientes" class="mb-6 inline-flex items-center gap-2 text-sm font-semibold text-ink-600 hover:text-brand-40">
            <PhArrowLeft :size="16" /> Clientes
        </Link>

        <div v-if="account.status === 'blocked'" class="mb-6 flex gap-3 rounded-2xl bg-red-50 p-4 text-sm text-red-800 ring-1 ring-red-200">
            <PhWarning :size="22" weight="duotone" class="shrink-0" />
            <p>
                Conta bloqueada<template v-if="account.blocked_at"> em {{ formatDateTime(account.blocked_at) }}</template>.
                Motivo: <strong>{{ account.blocked_reason ?? 'não informado' }}</strong>. O saldo fica preservado, só as movimentações param.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="space-y-6">
                <section class="card">
                    <h2 class="font-title text-lg font-semibold">Conta</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-ink-600">Agência / conta</dt><dd class="font-semibold tabular-nums">{{ account.agency }} / {{ account.number }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-ink-600">Status</dt><dd><Badge :tone="account.status === 'active' ? 'green' : 'red'">{{ account.status === 'active' ? 'Ativa' : 'Bloqueada' }}</Badge></dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-ink-600">Saldo</dt><dd class="font-title text-lg font-bold tabular-nums">{{ account.balance_formatted }}</dd></div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-ink-600">Soma dos lançamentos</dt>
                            <dd class="font-semibold tabular-nums" :class="account.computed_balance === account.balance ? 'text-emerald-700' : 'text-red-700'">
                                {{ formatCents(account.computed_balance) }}
                            </dd>
                        </div>
                        <div class="flex justify-between gap-4"><dt class="text-ink-600">Abertura</dt><dd>{{ formatDateTime(account.created_at) }}</dd></div>
                    </dl>
                </section>

                <section class="card">
                    <h2 class="font-title text-lg font-semibold">Titular</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div><dt class="text-ink-600">Nome</dt><dd class="font-semibold">{{ account.holder.name }}</dd></div>
                        <div><dt class="text-ink-600">E-mail</dt><dd class="font-semibold break-all">{{ account.holder.email }}</dd></div>
                        <div><dt class="text-ink-600">CPF</dt><dd class="font-semibold tabular-nums">{{ account.holder.cpf }}</dd></div>
                    </dl>
                </section>

                <section class="card">
                    <h2 class="font-title text-lg font-semibold">Chaves Pix</h2>
                    <ul v-if="pixKeys.length" class="mt-3 divide-y divide-ink-200 text-sm">
                        <li v-for="key in pixKeys" :key="key.id" class="py-2.5">
                            <p class="text-ink-500">{{ key.type_label }}</p>
                            <p class="font-semibold break-all">{{ key.key }}</p>
                        </li>
                    </ul>
                    <p v-else class="mt-3 text-sm text-ink-600">Nenhuma chave cadastrada.</p>
                </section>
            </div>

            <section class="card xl:col-span-2">
                <h2 class="font-title text-lg font-semibold">Lançamentos</h2>
                <ul v-if="transactions.data.length" class="mt-2 divide-y divide-ink-200">
                    <TransactionItem v-for="transaction in transactions.data" :key="transaction.id" :transaction="transaction" admin />
                </ul>
                <p v-else class="mt-4 text-ink-600">Nenhum lançamento.</p>
                <nav v-if="transactions.meta.last_page > 1" class="mt-4 flex items-center justify-between border-t border-ink-200 pt-4 text-sm">
                    <Link :href="transactions.links.prev ?? ''" preserve-scroll class="btn-ghost" :class="{ 'pointer-events-none opacity-40': !transactions.links.prev }">Anterior</Link>
                    <span class="text-ink-500">Página {{ transactions.meta.current_page }} de {{ transactions.meta.last_page }}</span>
                    <Link :href="transactions.links.next ?? ''" preserve-scroll class="btn-ghost" :class="{ 'pointer-events-none opacity-40': !transactions.links.next }">Próxima</Link>
                </nav>
            </section>
        </div>

        <Modal v-if="blocking" title="Bloquear conta" @close="blocking = false">
            <form class="space-y-4" @submit.prevent="block">
                <p class="text-sm text-ink-600">A conta não poderá depositar, sacar, enviar nem receber. O saldo continua guardado e o cliente ainda vê o extrato.</p>
                <div>
                    <label for="reason" class="label">Motivo</label>
                    <textarea id="reason" v-model="blockForm.reason" rows="3" maxlength="200" required class="input" placeholder="Ex.: suspeita de fraude reportada pelo cliente"></textarea>
                    <InputError :message="blockForm.errors.reason" />
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn-ghost" @click="blocking = false">Cancelar</button>
                    <button type="submit" class="btn-primary bg-red-600 hover:bg-red-700" :disabled="blockForm.processing">Bloquear conta</button>
                </div>
            </form>
        </Modal>
    </AdminLayout>
</template>
