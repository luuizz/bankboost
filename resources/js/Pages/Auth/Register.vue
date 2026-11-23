<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const form = useForm({
    name: '',
    email: '',
    cpf: '',
    password: '',
    password_confirmation: '',
});

function maskCpf(event: Event) {
    const digits = (event.target as HTMLInputElement).value.replace(/\D/g, '').slice(0, 11);

    form.cpf = digits
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
}

function submit() {
    form.post('/abrir-conta', { onFinish: () => form.reset('password', 'password_confirmation') });
}
</script>

<template>
    <AuthLayout title="Abra sua conta" subtitle="Sem tarifa, sem anuidade e em poucos minutos.">
        <form class="space-y-5" @submit.prevent="submit">
            <div>
                <label for="name" class="label">Nome completo</label>
                <input id="name" v-model="form.name" type="text" autocomplete="name" required autofocus class="input" />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="email" class="label">E-mail</label>
                    <input id="email" v-model="form.email" type="email" autocomplete="email" required class="input" />
                    <InputError :message="form.errors.email" />
                </div>

                <div>
                    <label for="cpf" class="label">CPF</label>
                    <input id="cpf" :value="form.cpf" type="text" inputmode="numeric" placeholder="000.000.000-00" required class="input" @input="maskCpf" />
                    <InputError :message="form.errors.cpf" />
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="password" class="label">Senha</label>
                    <input id="password" v-model="form.password" type="password" autocomplete="new-password" required class="input" />
                    <InputError :message="form.errors.password" />
                </div>

                <div>
                    <label for="password_confirmation" class="label">Confirme a senha</label>
                    <input id="password_confirmation" v-model="form.password_confirmation" type="password" autocomplete="new-password" required class="input" />
                </div>
            </div>

            <button type="submit" class="btn-primary w-full" :disabled="form.processing">Abrir minha conta</button>

            <p class="text-center text-sm text-ink-600">
                Já tem conta?
                <Link href="/entrar" class="font-semibold text-brand-40 hover:underline">Entrar</Link>
            </p>
        </form>
    </AuthLayout>
</template>
