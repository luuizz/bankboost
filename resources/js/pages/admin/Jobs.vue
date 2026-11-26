<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { PhPencilSimple, PhPlus, PhTrash } from '@phosphor-icons/vue';
import { ref } from 'vue';
import Badge from '@/components/admin/Badge.vue';
import Modal from '@/components/admin/Modal.vue';
import InputError from '@/components/InputError.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { JobOpening } from '@/types/admin';

const props = defineProps<{ jobs: JobOpening[]; seniorities: string[]; contracts: string[] }>();

const editing = ref<JobOpening | null>(null);
const open = ref(false);
const blank = { title: '', team: '', location: 'Remoto (Brasil)', seniority: props.seniorities[2], contract: props.contracts[0], description: '', active: true };
const form = useForm({ ...blank });

function openForm(job: JobOpening | null) {
    editing.value = job;
    form.defaults(job ? { title: job.title, team: job.team, location: job.location, seniority: job.seniority, contract: job.contract, description: job.description, active: job.active } : { ...blank });
    form.reset();
    form.clearErrors();
    open.value = true;
}

function submit() {
    const options = { preserveScroll: true, onSuccess: () => (open.value = false) };

    if (editing.value) form.put(`/admin/vagas/${editing.value.id}`, options);
    else form.post('/admin/vagas', options);
}

function toggle(job: JobOpening) {
    router.put(`/admin/vagas/${job.id}`, { ...job, active: !job.active }, { preserveScroll: true });
}

function destroy(job: JobOpening) {
    if (!confirm(`Remover a vaga “${job.title}”?`)) return;

    router.delete(`/admin/vagas/${job.id}`, { preserveScroll: true });
}
</script>

<template>
    <AdminLayout title="Vagas">
        <template #actions>
            <button type="button" class="btn-primary" @click="openForm(null)"><PhPlus :size="18" /> Nova vaga</button>
        </template>

        <div class="card overflow-hidden p-0">
            <ul class="divide-y divide-ink-200">
                <li v-for="job in jobs" :key="job.id" class="flex flex-wrap items-center gap-4 px-5 py-4">
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold" :class="{ 'text-ink-500': !job.active }">{{ job.title }}</p>
                        <p class="text-sm text-ink-600">{{ job.team }} · {{ job.seniority }} · {{ job.contract }} · {{ job.location }}</p>
                    </div>
                    <button type="button" @click="toggle(job)">
                        <Badge :tone="job.active ? 'green' : 'gray'">{{ job.active ? 'Aberta' : 'Fechada' }}</Badge>
                    </button>
                    <button type="button" class="rounded-lg p-1.5 text-ink-500 hover:bg-ink-100 hover:text-brand-40" aria-label="Editar" @click="openForm(job)"><PhPencilSimple :size="18" /></button>
                    <button type="button" class="rounded-lg p-1.5 text-ink-500 hover:bg-red-50 hover:text-red-600" aria-label="Remover" @click="destroy(job)"><PhTrash :size="18" /></button>
                </li>
                <li v-if="!jobs.length" class="px-5 py-10 text-center text-ink-600">Nenhuma vaga cadastrada.</li>
            </ul>
        </div>

        <Modal v-if="open" :title="editing ? 'Editar vaga' : 'Nova vaga'" @close="open = false">
            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label for="job-title" class="label">Cargo</label>
                    <input id="job-title" v-model="form.title" maxlength="160" required class="input" />
                    <InputError :message="form.errors.title" />
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label for="job-team" class="label">Time</label>
                        <input id="job-team" v-model="form.team" maxlength="60" required class="input" placeholder="Engenharia" />
                        <InputError :message="form.errors.team" />
                    </div>
                    <div>
                        <label for="job-location" class="label">Local</label>
                        <input id="job-location" v-model="form.location" maxlength="80" required class="input" />
                    </div>
                    <div>
                        <label for="job-seniority" class="label">Senioridade</label>
                        <select id="job-seniority" v-model="form.seniority" class="input">
                            <option v-for="item in seniorities" :key="item">{{ item }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="job-contract" class="label">Contrato</label>
                        <select id="job-contract" v-model="form.contract" class="input">
                            <option v-for="item in contracts" :key="item">{{ item }}</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="job-description" class="label">Descrição</label>
                    <textarea id="job-description" v-model="form.description" rows="6" maxlength="5000" required class="input"></textarea>
                    <InputError :message="form.errors.description" />
                </div>
                <label class="flex items-center gap-2 text-sm">
                    <input v-model="form.active" type="checkbox" class="size-4 accent-brand-40" /> Vaga aberta (aparece no site)
                </label>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn-ghost" @click="open = false">Cancelar</button>
                    <button type="submit" class="btn-primary" :disabled="form.processing">Salvar</button>
                </div>
            </form>
        </Modal>
    </AdminLayout>
</template>
