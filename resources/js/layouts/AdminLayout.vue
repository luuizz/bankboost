<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    PhArrowSquareOut,
    PhArrowsDownUp,
    PhBriefcase,
    PhChartPieSlice,
    PhEnvelopeSimple,
    PhList,
    PhNewspaper,
    PhQuestion,
    PhSignOut,
    PhUsersThree,
    PhX,
} from '@phosphor-icons/vue';
import { ref } from 'vue';
import FlashMessage from '@/components/FlashMessage.vue';
import type { SharedProps } from '@/types';

defineProps<{ title: string }>();

const page = usePage<SharedProps>();
const open = ref(false);

const groups = [
    {
        label: 'Banco',
        items: [
            { label: 'Visão geral', href: '/admin', icon: PhChartPieSlice },
            { label: 'Clientes', href: '/admin/clientes', icon: PhUsersThree },
            { label: 'Transações', href: '/admin/transacoes', icon: PhArrowsDownUp },
        ],
    },
    {
        label: 'Site',
        items: [
            { label: 'Blog', href: '/admin/posts', icon: PhNewspaper },
            { label: 'Perguntas frequentes', href: '/admin/faq', icon: PhQuestion },
            { label: 'Vagas', href: '/admin/vagas', icon: PhBriefcase },
            { label: 'Mensagens', href: '/admin/mensagens', icon: PhEnvelopeSimple },
        ],
    },
];

function isActive(href: string): boolean {
    const path = page.url.split('?')[0];

    return href === '/admin' ? path === '/admin' : path.startsWith(href);
}
</script>

<template>
    <Head :title="`${title} · Admin`" />

    <div class="min-h-screen bg-ink-100 text-ink-900 lg:grid lg:grid-cols-[16rem_minmax(0,1fr)]">
        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-brand-10 text-white transition-transform lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
            :class="open ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex items-center justify-between px-6 py-5">
                <Link href="/admin" class="flex items-center gap-2">
                    <img src="/logo-white.svg" alt="Bankboost" class="h-5" />
                    <span class="rounded bg-accent px-1.5 py-0.5 text-[0.65rem] font-bold text-brand-10 uppercase">Admin</span>
                </Link>
                <button type="button" class="lg:hidden" aria-label="Fechar menu" @click="open = false"><PhX :size="22" /></button>
            </div>

            <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-2" aria-label="Administração">
                <div v-for="group in groups" :key="group.label">
                    <p class="px-3 pb-2 text-xs font-semibold tracking-wide text-white/50 uppercase">{{ group.label }}</p>
                    <Link
                        v-for="item in group.items"
                        :key="item.href"
                        :href="item.href"
                        class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition"
                        :class="isActive(item.href) ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white'"
                        @click="open = false"
                    >
                        <component :is="item.icon" :size="20" weight="duotone" />
                        {{ item.label }}
                    </Link>
                </div>
            </nav>

            <div class="space-y-1 border-t border-white/10 p-3 text-sm">
                <a href="/" target="_blank" class="flex items-center gap-3 rounded-lg px-3 py-2 text-white/70 hover:text-white">
                    <PhArrowSquareOut :size="18" /> Ver o site
                </a>
                <Link href="/sair" method="post" as="button" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-white/70 hover:text-white">
                    <PhSignOut :size="18" /> Sair
                </Link>
                <p class="truncate px-3 pt-2 text-xs text-white/50">{{ page.props.auth.user?.email }}</p>
            </div>
        </aside>

        <div v-if="open" class="fixed inset-0 z-30 bg-black/40 lg:hidden" @click="open = false"></div>

        <div class="min-w-0">
            <header class="sticky top-0 z-20 flex items-center gap-4 border-b border-ink-200 bg-white/90 px-5 py-4 backdrop-blur lg:px-8">
                <button type="button" class="lg:hidden" aria-label="Abrir menu" @click="open = true"><PhList :size="24" /></button>
                <h1 class="font-title text-xl font-semibold">{{ title }}</h1>
                <div class="ml-auto flex items-center gap-3"><slot name="actions" /></div>
            </header>

            <main class="px-5 py-8 lg:px-8">
                <slot />
            </main>
        </div>

        <FlashMessage />
    </div>
</template>
