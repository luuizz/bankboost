<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { PhEnvelopeSimple, PhTrash } from '@phosphor-icons/vue';
import { ref } from 'vue';
import Badge from '@/components/admin/Badge.vue';
import Pagination from '@/components/admin/Pagination.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { formatDateTime } from '@/lib/money';
import type { ContactMessage, LaravelPage } from '@/types/admin';

defineProps<{ messages: LaravelPage<ContactMessage>; unreadOnly: boolean }>();

const openId = ref<number | null>(null);

function toggle(message: ContactMessage) {
    openId.value = openId.value === message.id ? null : message.id;

    if (openId.value && !message.read) {
        router.post(`/admin/mensagens/${message.id}/lida`, {}, { preserveScroll: true, preserveState: true });
    }
}

function destroy(message: ContactMessage) {
    if (!confirm('Excluir esta mensagem?')) return;

    router.delete(`/admin/mensagens/${message.id}`, { preserveScroll: true });
}
</script>

<template>
    <AdminLayout title="Mensagens">
        <div class="mb-6 flex gap-2 text-sm font-semibold">
            <Link href="/admin/mensagens" class="rounded-full px-4 py-1.5" :class="!unreadOnly ? 'bg-brand-40 text-white' : 'bg-white text-ink-700 ring-1 ring-ink-200'">Todas</Link>
            <Link href="/admin/mensagens?nao_lidas=1" class="rounded-full px-4 py-1.5" :class="unreadOnly ? 'bg-brand-40 text-white' : 'bg-white text-ink-700 ring-1 ring-ink-200'">Não lidas</Link>
        </div>

        <div class="card overflow-hidden p-0">
            <ul class="divide-y divide-ink-200">
                <li v-for="message in messages.data" :key="message.id">
                    <button type="button" class="flex w-full items-center gap-4 px-5 py-4 text-left hover:bg-ink-100/50" :aria-expanded="openId === message.id" @click="toggle(message)">
                        <span class="size-2 shrink-0 rounded-full" :class="message.read ? 'bg-transparent' : 'bg-brand-40'" aria-hidden="true"></span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate" :class="message.read ? 'text-ink-700' : 'font-semibold text-ink-900'">{{ message.name }} <span class="font-normal text-ink-500">· {{ message.email }}</span></p>
                            <p class="truncate text-sm text-ink-600">{{ message.message }}</p>
                        </div>
                        <Badge tone="brand">{{ message.subject }}</Badge>
                        <span class="hidden text-sm whitespace-nowrap text-ink-500 sm:block">{{ formatDateTime(message.created_at) }}</span>
                    </button>
                    <div v-if="openId === message.id" class="border-t border-ink-200 bg-ink-100/40 px-5 py-4 sm:pl-11">
                        <p class="text-sm whitespace-pre-line text-ink-800">{{ message.message }}</p>
                        <div class="mt-4 flex gap-3">
                            <a :href="`mailto:${message.email}?subject=${encodeURIComponent('Re: ' + message.subject)}`" class="btn-primary"><PhEnvelopeSimple :size="18" /> Responder</a>
                            <button type="button" class="btn-ghost text-red-600" @click="destroy(message)"><PhTrash :size="18" /> Excluir</button>
                        </div>
                    </div>
                </li>
                <li v-if="!messages.data.length" class="px-5 py-10 text-center text-ink-600">Nenhuma mensagem por aqui.</li>
            </ul>
            <Pagination :prev="messages.prev_page_url" :next="messages.next_page_url" :current="messages.current_page" :last="messages.last_page" :total="messages.total" />
        </div>
    </AdminLayout>
</template>
