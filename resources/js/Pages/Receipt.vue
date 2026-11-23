<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import PanelLayout from '@/Layouts/PanelLayout.vue';
import { formatDateTime } from '@/lib/money';
import type { TransferReceipt } from '@/types';

defineProps<{ transfer: TransferReceipt; sent: boolean }>();
</script>

<template>
    <PanelLayout title="Comprovante">
        <div class="mx-auto max-w-lg">
            <article class="card">
                <div class="text-center">
                    <span class="inline-flex size-12 items-center justify-center rounded-full bg-emerald-50 text-xl text-emerald-700">✓</span>
                    <p class="mt-3 text-sm text-ink-600">{{ transfer.method_label }} {{ sent ? 'enviado' : 'recebido' }}</p>
                    <p class="font-title text-3xl font-bold tabular-nums">{{ transfer.amount_formatted }}</p>
                    <p class="mt-1 text-sm text-ink-600">{{ formatDateTime(transfer.created_at) }}</p>
                </div>

                <dl class="mt-6 space-y-4 border-t border-dashed border-ink-300 pt-6 text-sm">
                    <div>
                        <dt class="text-ink-600">De</dt>
                        <dd class="font-semibold">{{ transfer.source.name }}</dd>
                        <dd class="text-ink-600">Ag. {{ transfer.source.agency }} · Conta {{ transfer.source.number }}</dd>
                    </div>
                    <div>
                        <dt class="text-ink-600">Para</dt>
                        <dd class="font-semibold">{{ transfer.destination.name }}</dd>
                        <dd class="text-ink-600">Ag. {{ transfer.destination.agency }} · Conta {{ transfer.destination.number }}</dd>
                    </div>
                    <div v-if="transfer.pix_key">
                        <dt class="text-ink-600">Chave Pix</dt>
                        <dd class="font-semibold break-all">{{ transfer.pix_key }}</dd>
                    </div>
                    <div v-if="transfer.description">
                        <dt class="text-ink-600">Descrição</dt>
                        <dd class="font-semibold">{{ transfer.description }}</dd>
                    </div>
                    <div>
                        <dt class="text-ink-600">Autenticação</dt>
                        <dd class="font-mono text-xs break-all">{{ transfer.id }}</dd>
                    </div>
                </dl>
            </article>

            <div class="mt-6 flex justify-center gap-3">
                <button type="button" class="btn-ghost" onclick="window.print()">Imprimir</button>
                <Link href="/painel" class="btn-primary">Voltar ao início</Link>
            </div>
        </div>
    </PanelLayout>
</template>
