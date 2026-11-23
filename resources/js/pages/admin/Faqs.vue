<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { PhPencilSimple, PhPlus, PhTrash } from '@phosphor-icons/vue';
import { computed, ref } from 'vue';
import Badge from '@/components/admin/Badge.vue';
import Modal from '@/components/admin/Modal.vue';
import InputError from '@/components/InputError.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Option } from '@/types';
import type { Faq } from '@/types/admin';

const props = defineProps<{ faqs: Faq[]; categories: Option[] }>();

const grouped = computed(() =>
    props.categories.map((category) => ({ ...category, items: props.faqs.filter((faq) => faq.category === category.value) })),
);

const editing = ref<Faq | null>(null);
const open = ref(false);
const form = useForm({ category: props.categories[0].value, question: '', answer: '', position: 0, active: true });

function create(category: string) {
    editing.value = null;
    form.defaults({ category, question: '', answer: '', position: props.faqs.filter((faq) => faq.category === category).length, active: true });
    form.reset();
    form.clearErrors();
    open.value = true;
}

function edit(faq: Faq) {
    editing.value = faq;
    form.defaults({ category: faq.category, question: faq.question, answer: faq.answer, position: faq.position, active: faq.active });
    form.reset();
    form.clearErrors();
    open.value = true;
}

function submit() {
    const options = { preserveScroll: true, onSuccess: () => (open.value = false) };

    if (editing.value) form.put(`/admin/faq/${editing.value.id}`, options);
    else form.post('/admin/faq', options);
}

function destroy(faq: Faq) {
    if (!confirm(`Remover “${faq.question}”?`)) return;

    router.delete(`/admin/faq/${faq.id}`, { preserveScroll: true });
}
</script>

<template>
    <AdminLayout title="Perguntas frequentes">
        <template #actions>
            <button type="button" class="btn-primary" @click="create(categories[0].value)"><PhPlus :size="18" /> Nova pergunta</button>
        </template>

        <p class="mb-6 text-sm text-ink-600">As perguntas ativas aparecem na central de ajuda, agrupadas por categoria e na ordem da posição.</p>

        <div class="space-y-6">
            <section v-for="group in grouped" :key="group.value" class="card p-0">
                <div class="flex items-center justify-between border-b border-ink-200 px-5 py-3">
                    <h2 class="font-title font-semibold">{{ group.label }} <span class="text-ink-500">({{ group.items.length }})</span></h2>
                    <button type="button" class="text-sm font-semibold text-brand-40 hover:underline" @click="create(group.value)">Adicionar</button>
                </div>
                <ul v-if="group.items.length" class="divide-y divide-ink-200">
                    <li v-for="faq in group.items" :key="faq.id" class="flex items-start gap-4 px-5 py-3.5">
                        <span class="mt-0.5 w-6 shrink-0 text-sm text-ink-400 tabular-nums">{{ faq.position }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold" :class="{ 'text-ink-500': !faq.active }">{{ faq.question }}</p>
                            <p class="line-clamp-2 text-sm text-ink-600">{{ faq.answer }}</p>
                        </div>
                        <Badge v-if="!faq.active">Inativa</Badge>
                        <button type="button" class="rounded-lg p-1.5 text-ink-500 hover:bg-ink-100 hover:text-brand-40" aria-label="Editar" @click="edit(faq)"><PhPencilSimple :size="18" /></button>
                        <button type="button" class="rounded-lg p-1.5 text-ink-500 hover:bg-red-50 hover:text-red-600" aria-label="Remover" @click="destroy(faq)"><PhTrash :size="18" /></button>
                    </li>
                </ul>
                <p v-else class="px-5 py-4 text-sm text-ink-500">Nenhuma pergunta nesta categoria.</p>
            </section>
        </div>

        <Modal v-if="open" :title="editing ? 'Editar pergunta' : 'Nova pergunta'" @close="open = false">
            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid grid-cols-[1fr_6rem] gap-3">
                    <div>
                        <label for="faq-category" class="label">Categoria</label>
                        <select id="faq-category" v-model="form.category" class="input">
                            <option v-for="category in categories" :key="category.value" :value="category.value">{{ category.label }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="faq-position" class="label">Posição</label>
                        <input id="faq-position" v-model.number="form.position" type="number" min="0" max="999" class="input" />
                    </div>
                </div>
                <div>
                    <label for="faq-question" class="label">Pergunta</label>
                    <input id="faq-question" v-model="form.question" maxlength="255" required class="input" />
                    <InputError :message="form.errors.question" />
                </div>
                <div>
                    <label for="faq-answer" class="label">Resposta</label>
                    <textarea id="faq-answer" v-model="form.answer" rows="5" maxlength="3000" required class="input"></textarea>
                    <InputError :message="form.errors.answer" />
                </div>
                <label class="flex items-center gap-2 text-sm">
                    <input v-model="form.active" type="checkbox" class="size-4 accent-brand-40" /> Mostrar na central de ajuda
                </label>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn-ghost" @click="open = false">Cancelar</button>
                    <button type="submit" class="btn-primary" :disabled="form.processing">Salvar</button>
                </div>
            </form>
        </Modal>
    </AdminLayout>
</template>
