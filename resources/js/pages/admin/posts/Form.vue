<script setup lang="ts">
import { Link, router, useForm } from '@inertiajs/vue3';
import { PhArrowLeft, PhArrowSquareOut, PhImage, PhTrash } from '@phosphor-icons/vue';
import { computed, ref } from 'vue';
import Badge from '@/components/admin/Badge.vue';
import InputError from '@/components/InputError.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { postJson } from '@/lib/http';
import type { Option } from '@/types';
import type { AdminPost } from '@/types/admin';

const props = defineProps<{ post: AdminPost | null; categories: Option[] }>();

const editing = computed(() => props.post !== null);

const form = useForm({
    title: props.post?.title ?? '',
    slug: props.post?.slug ?? '',
    category: props.post?.category ?? props.categories[0].value,
    excerpt: props.post?.excerpt ?? '',
    body: props.post?.body ?? '',
    author_name: props.post?.author_name ?? 'Equipe Bankboost',
    featured: props.post?.featured ?? false,
    published_at: props.post?.published_at ?? '',
    cover: null as File | null,
    remove_cover: false,
});

const coverPreview = ref<string | null>(props.post?.cover_url ?? null);
const tab = ref<'write' | 'preview'>('write');
const previewHtml = ref('');
const loadingPreview = ref(false);

function onCover(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;

    form.cover = file;
    form.remove_cover = false;
    coverPreview.value = file ? URL.createObjectURL(file) : (props.post?.cover_url ?? null);
}

function removeCover() {
    form.cover = null;
    form.remove_cover = true;
    coverPreview.value = null;
}

async function showPreview() {
    tab.value = 'preview';
    loadingPreview.value = true;

    try {
        previewHtml.value = (await postJson<{ html: string }>('/admin/posts/preview', { body: form.body })).html;
    } catch {
        previewHtml.value = '<p>Não foi possível gerar a prévia.</p>';
    } finally {
        loadingPreview.value = false;
    }
}

function publishNow() {
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    form.published_at = now.toISOString().slice(0, 16);
}

function submit() {
    if (editing.value) {
        // Upload de arquivo precisa ir como POST com _method=PUT
        form.transform((data) => ({ ...data, _method: 'put' })).post(`/admin/posts/${props.post!.slug}`, {
            forceFormData: true,
            preserveScroll: true,
        });
    } else {
        form.post('/admin/posts', { forceFormData: true });
    }
}

function destroy() {
    if (!confirm('Excluir este post? Essa ação não pode ser desfeita.')) return;

    router.delete(`/admin/posts/${props.post!.slug}`);
}
</script>

<template>
    <AdminLayout :title="editing ? 'Editar post' : 'Novo post'">
        <template #actions>
            <a v-if="post && post.status === 'published'" :href="post.url" target="_blank" class="btn-ghost"><PhArrowSquareOut :size="18" /> Ver no site</a>
            <button type="submit" form="post-form" class="btn-primary" :disabled="form.processing">{{ editing ? 'Salvar' : 'Criar post' }}</button>
        </template>

        <Link href="/admin/posts" class="mb-6 inline-flex items-center gap-2 text-sm font-semibold text-ink-600 hover:text-brand-40">
            <PhArrowLeft :size="16" /> Posts
        </Link>

        <form id="post-form" class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]" @submit.prevent="submit">
            <div class="space-y-6">
                <section class="card space-y-5">
                    <div>
                        <label for="title" class="label">Título</label>
                        <input id="title" v-model="form.title" maxlength="160" required class="input font-title text-lg font-semibold" />
                        <InputError :message="form.errors.title" />
                    </div>
                    <div>
                        <label for="slug" class="label">Endereço</label>
                        <div class="flex items-center rounded-xl border border-ink-300 bg-ink-100 focus-within:border-brand-30">
                            <span class="pl-4 text-sm text-ink-500">/blog/</span>
                            <input id="slug" v-model="form.slug" placeholder="gerado a partir do título" class="w-full bg-transparent px-1 py-2.5 outline-none" />
                        </div>
                        <InputError :message="form.errors.slug" />
                    </div>
                    <div>
                        <label for="excerpt" class="label">Resumo</label>
                        <textarea id="excerpt" v-model="form.excerpt" rows="2" maxlength="300" required class="input"></textarea>
                        <p class="mt-1 text-right text-xs text-ink-500">{{ form.excerpt.length }}/300</p>
                        <InputError :message="form.errors.excerpt" />
                    </div>
                </section>

                <section class="card p-0">
                    <div class="flex items-center justify-between border-b border-ink-200 px-5 py-3">
                        <div class="flex gap-1 rounded-lg bg-ink-100 p-1 text-sm font-semibold">
                            <button type="button" class="rounded-md px-3 py-1" :class="tab === 'write' ? 'bg-white shadow-sm' : 'text-ink-600'" @click="tab = 'write'">Escrever</button>
                            <button type="button" class="rounded-md px-3 py-1" :class="tab === 'preview' ? 'bg-white shadow-sm' : 'text-ink-600'" @click="showPreview">Prévia</button>
                        </div>
                        <span class="text-xs text-ink-500">Markdown: ## título, **negrito**, - lista, > citação</span>
                    </div>
                    <textarea v-show="tab === 'write'" v-model="form.body" rows="22" required class="block w-full resize-y rounded-b-2xl px-5 py-4 font-mono text-sm outline-none" aria-label="Conteúdo"></textarea>
                    <div v-show="tab === 'preview'" class="prose max-w-none px-5 py-4">
                        <p v-if="loadingPreview" class="text-ink-500">Gerando prévia…</p>
                        <div v-else v-html="previewHtml"></div>
                    </div>
                    <InputError :message="form.errors.body" class="px-5 pb-4" />
                </section>
            </div>

            <aside class="space-y-6">
                <section class="card space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="font-title font-semibold">Publicação</h2>
                        <Badge v-if="post" :tone="post.status === 'published' ? 'green' : post.status === 'scheduled' ? 'yellow' : 'gray'">
                            {{ { draft: 'Rascunho', scheduled: 'Agendado', published: 'Publicado' }[post.status] }}
                        </Badge>
                    </div>
                    <div>
                        <label for="published_at" class="label">Data de publicação</label>
                        <input id="published_at" v-model="form.published_at" type="datetime-local" class="input" />
                        <p class="mt-1.5 text-xs text-ink-500">Vazio = rascunho. Data futura = agendado.</p>
                        <InputError :message="form.errors.published_at" />
                        <div class="mt-2 flex gap-3 text-sm font-semibold">
                            <button type="button" class="text-brand-40 hover:underline" @click="publishNow">Publicar agora</button>
                            <button type="button" class="text-ink-500 hover:underline" @click="form.published_at = ''">Voltar a rascunho</button>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="form.featured" type="checkbox" class="size-4 accent-brand-40" />
                        Destacar no topo do blog
                    </label>
                </section>

                <section class="card space-y-4">
                    <div>
                        <label for="category" class="label">Categoria</label>
                        <select id="category" v-model="form.category" class="input">
                            <option v-for="category in categories" :key="category.value" :value="category.value">{{ category.label }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="author" class="label">Autor</label>
                        <input id="author" v-model="form.author_name" maxlength="80" required class="input" />
                        <InputError :message="form.errors.author_name" />
                    </div>
                </section>

                <section class="card space-y-3">
                    <h2 class="font-title font-semibold">Capa</h2>
                    <div v-if="coverPreview" class="relative overflow-hidden rounded-xl">
                        <img :src="coverPreview" alt="" class="aspect-[16/10] w-full object-cover" />
                        <button type="button" class="absolute top-2 right-2 rounded-lg bg-white/90 p-1.5 text-red-600 shadow" aria-label="Remover capa" @click="removeCover">
                            <PhTrash :size="18" />
                        </button>
                    </div>
                    <label class="flex cursor-pointer flex-col items-center gap-2 rounded-xl border-2 border-dashed border-ink-300 p-5 text-center text-sm text-ink-600 hover:border-brand-30">
                        <PhImage :size="28" weight="duotone" class="text-brand-40" />
                        <span>{{ coverPreview ? 'Trocar imagem' : 'Enviar imagem' }}</span>
                        <span class="text-xs text-ink-500">JPG, PNG ou WebP, até 2 MB, mínimo 800 px de largura. Sem capa, o site gera uma pela categoria.</span>
                        <input type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="onCover" />
                    </label>
                    <InputError :message="form.errors.cover" />
                </section>

                <button v-if="editing" type="button" class="flex w-full items-center justify-center gap-2 rounded-xl py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50" @click="destroy">
                    <PhTrash :size="18" /> Excluir post
                </button>
            </aside>
        </form>
    </AdminLayout>
</template>
