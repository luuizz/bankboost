<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import TransactionItem from '@/components/TransactionItem.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Option, Paginated, Transaction, TransactionType } from '@/types';

const props = defineProps<{
    transactions: Paginated<Transaction>;
    filters: { type: TransactionType[]; direction: string; from: string; to: string; search: string };
    types: Option<TransactionType>[];
}>();

const form = reactive({ ...props.filters, type: props.filters.type[0] ?? '' });

function apply() {
    router.get(
        '/admin/transacoes',
        {
            type: form.type ? [form.type] : undefined,
            direction: form.direction || undefined,
            from: form.from || undefined,
            to: form.to || undefined,
            search: form.search || undefined,
        },
        { preserveState: true, replace: true },
    );
}
</script>

<template>
    <AdminLayout title="Transações">
        <form class="card mb-6 grid grid-cols-1 gap-3 p-5 sm:grid-cols-2 lg:grid-cols-6" @submit.prevent="apply">
            <input v-model="form.search" type="search" placeholder="Descrição ou contraparte" class="input lg:col-span-2" aria-label="Buscar" />
            <select v-model="form.type" class="input" aria-label="Tipo">
                <option value="">Todos os tipos</option>
                <option v-for="type in types" :key="type.value" :value="type.value">{{ type.label }}</option>
            </select>
            <input v-model="form.from" type="date" class="input" aria-label="De" />
            <input v-model="form.to" type="date" class="input" aria-label="Até" />
            <button type="submit" class="btn-primary">Filtrar</button>
            <p v-if="Object.keys($page.props.errors).length" class="error lg:col-span-6">{{ Object.values($page.props.errors)[0] }}</p>
        </form>

        <section class="card">
            <ul v-if="transactions.data.length" class="divide-y divide-ink-200">
                <TransactionItem v-for="transaction in transactions.data" :key="transaction.id" :transaction="transaction" admin />
            </ul>
            <p v-else class="py-10 text-center text-ink-600">Nenhum lançamento encontrado.</p>

            <nav v-if="transactions.meta.last_page > 1" class="mt-4 flex items-center justify-between border-t border-ink-200 pt-4 text-sm">
                <Link :href="transactions.links.prev ?? ''" preserve-scroll class="btn-ghost" :class="{ 'pointer-events-none opacity-40': !transactions.links.prev }">Anterior</Link>
                <span class="text-ink-500">Página {{ transactions.meta.current_page }} de {{ transactions.meta.last_page }} · {{ transactions.meta.total }} lançamentos</span>
                <Link :href="transactions.links.next ?? ''" preserve-scroll class="btn-ghost" :class="{ 'pointer-events-none opacity-40': !transactions.links.next }">Próxima</Link>
            </nav>
        </section>
    </AdminLayout>
</template>
