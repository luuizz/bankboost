<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import FlashMessage from '@/components/FlashMessage.vue';
import type { SharedProps } from '@/types';

defineProps<{ title: string }>();

const page = usePage<SharedProps>();

const nav = [
    { label: 'Início', href: '/painel' },
    { label: 'Extrato', href: '/painel/extrato' },
    { label: 'Transferir', href: '/painel/transferir' },
    { label: 'Pix', href: '/painel/pix' },
];

function isActive(href: string): boolean {
    const path = page.url.split('?')[0];

    return href === '/painel' ? path === '/painel' : path.startsWith(href);
}
</script>

<template>
    <Head :title="title" />

    <div class="min-h-screen bg-ink-100 text-ink-900">
        <header class="bg-brand-10 text-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-5 py-4">
                <Link href="/painel" class="shrink-0">
                    <img src="/logo-white.svg" alt="Bankboost" class="h-7" />
                </Link>

                <nav class="hidden gap-1 md:flex" aria-label="Painel">
                    <Link
                        v-for="item in nav"
                        :key="item.href"
                        :href="item.href"
                        class="rounded-lg px-4 py-2 text-sm font-semibold transition"
                        :class="isActive(item.href) ? 'bg-white/15 text-white' : 'text-white/75 hover:text-white'"
                    >
                        {{ item.label }}
                    </Link>
                </nav>

                <div class="flex items-center gap-4">
                    <span class="hidden text-sm text-white/80 sm:inline">{{ page.props.auth.user?.name }}</span>
                    <Link href="/sair" method="post" as="button" class="rounded-lg border border-white/30 px-3 py-1.5 text-sm font-semibold hover:bg-white/10">
                        Sair
                    </Link>
                </div>
            </div>

            <nav class="flex gap-1 overflow-x-auto px-3 pb-3 md:hidden" aria-label="Painel">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                    :class="isActive(item.href) ? 'bg-white/15 text-white' : 'text-white/75'"
                >
                    {{ item.label }}
                </Link>
            </nav>
        </header>

        <main class="mx-auto max-w-6xl px-5 py-8">
            <h1 class="mb-6 font-title text-2xl font-semibold text-ink-900">{{ title }}</h1>
            <slot />
        </main>

        <FlashMessage />
    </div>
</template>
