<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{ title: string; subtitle?: string }>();
</script>

<template>
    <Head :title="title" />

    <div class="grid min-h-screen lg:grid-cols-2">
        <aside class="relative hidden flex-col justify-between overflow-hidden bg-brand-10 p-12 text-white lg:flex">
            <Link href="/"><img src="/logo-white.svg" alt="Bankboost" class="h-8" /></Link>

            <div class="relative z-10 max-w-md">
                <p class="mb-3 font-title text-sm font-semibold tracking-wide text-accent uppercase">Banco 100% digital</p>
                <h2 class="font-title text-4xl leading-tight font-bold">Desperte seu potencial financeiro com o Bankboost!</h2>
            </div>

            <img src="/card-front.png" alt="" class="absolute top-36 -right-20 w-[26rem] rotate-[-12deg] opacity-90" />
        </aside>

        <main class="flex items-center justify-center bg-ink-100 px-5 py-12">
            <div class="w-full max-w-md">
                <Link href="/" class="mb-8 block lg:hidden"><img src="/logo-black.svg" alt="Bankboost" class="h-7" /></Link>

                <h1 class="font-title text-3xl font-semibold text-ink-900">{{ title }}</h1>
                <p v-if="subtitle" class="mt-2 text-ink-600">{{ subtitle }}</p>

                <div class="mt-8">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>
