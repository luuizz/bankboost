<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { PhMagnifyingGlass } from '@phosphor-icons/vue';
import { reactive } from 'vue';
import Badge from '@/components/admin/Badge.vue';
import Pagination from '@/components/admin/Pagination.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { formatDateTime } from '@/lib/money';
import type { AdminAccount, LaravelPage } from '@/types/admin';

const props = defineProps<{
    accounts: LaravelPage<AdminAccount>;
    filters: { search: string; status: string };
}>();

const form = reactive({ ...props.filters });

function apply() {
    router.get('/admin/clientes', { search: form.search || undefined, status: form.status || undefined }, { preserveState: true, replace: true });
}
</script>

<template>
    <AdminLayout title="Clientes">
        <form class="mb-6 flex flex-col gap-3 sm:flex-row" @submit.prevent="apply">
            <label class="relative flex-1">
                <span class="sr-only">Buscar</span>
                <PhMagnifyingGlass :size="18" class="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-ink-400" />
                <input v-model="form.search" type="search" placeholder="Nome, e-mail, CPF ou número da conta" class="input pl-11" />
            </label>
            <select v-model="form.status" class="input sm:w-48" @change="apply">
                <option value="">Todas as contas</option>
                <option value="active">Ativas</option>
                <option value="blocked">Bloqueadas</option>
            </select>
            <button type="submit" class="btn-primary">Buscar</button>
        </form>

        <div class="card overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[44rem] text-left text-sm">
                    <thead class="bg-ink-100/60 text-ink-600">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Cliente</th>
                            <th class="px-5 py-3 font-semibold">Conta</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Saldo</th>
                            <th class="px-5 py-3 font-semibold">Abertura</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-200">
                        <tr v-for="account in accounts.data" :key="account.id" class="hover:bg-ink-100/50">
                            <td class="px-5 py-3">
                                <Link :href="`/admin/clientes/${account.id}`" class="font-semibold text-ink-900 hover:text-brand-40">{{ account.holder.name }}</Link>
                                <p class="text-ink-500">{{ account.holder.email }} · {{ account.holder.cpf }}</p>
                            </td>
                            <td class="px-5 py-3 tabular-nums">{{ account.agency }} / {{ account.number }}</td>
                            <td class="px-5 py-3">
                                <Badge :tone="account.status === 'active' ? 'green' : 'red'">{{ account.status === 'active' ? 'Ativa' : 'Bloqueada' }}</Badge>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold tabular-nums">{{ account.balance_formatted }}</td>
                            <td class="px-5 py-3 text-ink-600">{{ formatDateTime(account.created_at) }}</td>
                        </tr>
                        <tr v-if="!accounts.data.length">
                            <td colspan="5" class="px-5 py-10 text-center text-ink-600">Nenhum cliente encontrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :prev="accounts.prev_page_url" :next="accounts.next_page_url" :current="accounts.current_page" :last="accounts.last_page" :total="accounts.total" />
        </div>
    </AdminLayout>
</template>
