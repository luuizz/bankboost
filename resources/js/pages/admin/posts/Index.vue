<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { PhArrowSquareOut, PhMagnifyingGlass, PhPlus, PhStar } from '@phosphor-icons/vue';
import { ref } from 'vue';
import Badge from '@/components/admin/Badge.vue';
import Pagination from '@/components/admin/Pagination.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { formatDateTime } from '@/lib/money';
import type { AdminPostRow, LaravelPage } from '@/types/admin';

const props = defineProps<{ posts: LaravelPage<AdminPostRow>; filters: { search: string } }>();

const search = ref(props.filters.search);
const statuses = {
    draft: { label: 'Rascunho', tone: 'gray' },
    scheduled: { label: 'Agendado', tone: 'yellow' },
    published: { label: 'Publicado', tone: 'green' },
} as const;

function apply() {
    router.get('/admin/posts', { search: search.value || undefined }, { preserveState: true, replace: true });
}
</script>

<template>
    <AdminLayout title="Blog">
        <template #actions>
            <Link href="/admin/posts/create" class="btn-primary"><PhPlus :size="18" /> Novo post</Link>
        </template>

        <form class="mb-6 flex gap-3" @submit.prevent="apply">
            <label class="relative flex-1">
                <span class="sr-only">Buscar</span>
                <PhMagnifyingGlass :size="18" class="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-ink-400" />
                <input v-model="search" type="search" placeholder="Buscar pelo título" class="input pl-11" />
            </label>
            <button type="submit" class="btn-ghost">Buscar</button>
        </form>

        <div class="card overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[40rem] text-left text-sm">
                    <thead class="bg-ink-100/60 text-ink-600">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Título</th>
                            <th class="px-5 py-3 font-semibold">Categoria</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold">Publicação</th>
                            <th class="px-5 py-3"><span class="sr-only">Ações</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-200">
                        <tr v-for="post in posts.data" :key="post.id" class="hover:bg-ink-100/50">
                            <td class="px-5 py-3">
                                <Link :href="`/admin/posts/${post.slug}/edit`" class="flex items-center gap-2 font-semibold text-ink-900 hover:text-brand-40">
                                    <PhStar v-if="post.featured" :size="16" weight="fill" class="shrink-0 text-accent" aria-label="Destaque" />
                                    {{ post.title }}
                                </Link>
                            </td>
                            <td class="px-5 py-3 text-ink-600">{{ post.category }}</td>
                            <td class="px-5 py-3"><Badge :tone="statuses[post.status].tone">{{ statuses[post.status].label }}</Badge></td>
                            <td class="px-5 py-3 text-ink-600">{{ post.published_at ? formatDateTime(post.published_at) : '—' }}</td>
                            <td class="px-5 py-3 text-right">
                                <a v-if="post.status === 'published'" :href="post.url" target="_blank" class="inline-flex text-ink-500 hover:text-brand-40" aria-label="Ver no site">
                                    <PhArrowSquareOut :size="18" />
                                </a>
                            </td>
                        </tr>
                        <tr v-if="!posts.data.length">
                            <td colspan="5" class="px-5 py-10 text-center text-ink-600">Nenhum post encontrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :prev="posts.prev_page_url" :next="posts.next_page_url" :current="posts.current_page" :last="posts.last_page" :total="posts.total" />
        </div>
    </AdminLayout>
</template>
