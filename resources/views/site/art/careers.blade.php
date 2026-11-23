<div class="relative mx-auto max-w-md">
    <div class="rounded-[2rem] bg-white p-6 text-ink-900 shadow-2xl shadow-brand-10/30">
        <p class="text-sm font-semibold text-ink-500"># time-produto</p>
        <div class="mt-4 space-y-4">
            @foreach ([['L', 'bg-emerald-400', 'Luana', 'Deploy do novo extrato feito! 🚀'], ['R', 'bg-accent', 'Rafa', 'Clientes já mandaram elogio no chat 💜'], ['M', 'bg-brand-40', 'Marcos', 'Bora de retro na sexta, com pão de queijo?']] as [$initial, $color, $name, $message])
                <div class="flex gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full font-title font-bold text-white {{ $color }}">{{ $initial }}</span>
                    <div>
                        <p class="text-sm font-semibold">{{ $name }} <span class="font-normal text-ink-400">· agora</span></p>
                        <p class="text-sm text-ink-700">{{ $message }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-5 flex gap-2">
            @foreach (['🎉 12', '💜 8', '🙌 5'] as $reaction)
                <span class="rounded-full bg-ink-100 px-3 py-1 text-xs">{{ $reaction }}</span>
            @endforeach
        </div>
    </div>
    <div class="animate-float absolute -right-4 -bottom-6 flex items-center gap-2 rounded-2xl bg-accent px-4 py-3 text-sm font-bold text-brand-10 shadow-xl">
        @svg('phosphor-house-line-duotone', 'size-5') 100% remoto
    </div>
</div>
