<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/entrar', { onFinish: () => form.reset('password') });
}
</script>

<template>
    <AuthLayout title="Entrar" subtitle="Acesse sua conta Bankboost.">
        <form class="space-y-5" @submit.prevent="submit">
            <div>
                <label for="email" class="label">E-mail</label>
                <input id="email" v-model="form.email" type="email" autocomplete="username" required autofocus class="input" />
                <InputError :message="form.errors.email" />
            </div>

            <div>
                <label for="password" class="label">Senha</label>
                <input id="password" v-model="form.password" type="password" autocomplete="current-password" required class="input" />
                <InputError :message="form.errors.password" />
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-700">
                <input v-model="form.remember" type="checkbox" class="size-4 rounded accent-brand-40" />
                Manter conectado
            </label>

            <button type="submit" class="btn-primary w-full" :disabled="form.processing">Entrar</button>

            <p class="text-center text-sm text-ink-600">
                Ainda não tem conta?
                <Link href="/abrir-conta" class="font-semibold text-brand-40 hover:underline">Abra a sua</Link>
            </p>
        </form>
    </AuthLayout>
</template>
