<div class="relative mx-auto max-w-md">
    <div class="absolute inset-10 rounded-full bg-accent/30 blur-3xl"></div>
    <x-site.phone class="relative">
        <div class="flex h-full flex-col">
            <div class="bg-linear-to-br from-brand-30 to-brand-10 px-5 pt-10 pb-6 text-white">
                <p class="text-xs text-white/70">Olá, Ana 👋</p>
                <p class="mt-4 text-[0.7rem] text-white/70">Saldo disponível</p>
                <p class="font-title text-2xl font-bold">R$ 4.129,10</p>
                <div class="mt-4 grid grid-cols-4 gap-2 text-center text-[0.6rem]">
                    @foreach (['qr-code' => 'Pix', 'arrows-left-right' => 'Transferir', 'barcode' => 'Pagar', 'piggy-bank' => 'Cofrinho'] as $icon => $label)
                        <span class="flex flex-col items-center gap-1">
                            <span class="flex size-9 items-center justify-center rounded-xl bg-white/15">@svg('phosphor-'.$icon, 'size-4')</span>
                            {{ $label }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="flex-1 space-y-2.5 p-4">
                <p class="text-[0.65rem] font-semibold text-ink-500 uppercase">Hoje</p>
                @foreach ([['Pix recebido', 'Bruno Lima', '+ R$ 45,90', true], ['Mercado Bom Preço', 'Cartão de crédito', '- R$ 132,40', false], ['Rendimento', 'Boost Mais', '+ R$ 1,32', true]] as [$title, $sub, $value, $in])
                    <div class="flex items-center justify-between rounded-xl bg-white p-2.5 shadow-sm">
                        <div>
                            <p class="text-[0.7rem] font-semibold">{{ $title }}</p>
                            <p class="text-[0.6rem] text-ink-500">{{ $sub }}</p>
                        </div>
                        <p class="text-[0.7rem] font-semibold {{ $in ? 'text-emerald-600' : '' }}">{{ $value }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </x-site.phone>

    <div class="animate-float absolute bottom-24 -left-6 hidden rounded-2xl bg-white p-3 text-ink-900 shadow-xl sm:flex sm:items-center sm:gap-3">
        <span class="flex size-9 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">@svg('phosphor-check-circle-fill', 'size-5')</span>
        <span class="text-xs"><strong class="block">Pix enviado</strong>em 2 segundos</span>
    </div>
</div>
