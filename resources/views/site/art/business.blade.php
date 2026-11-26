<div class="relative mx-auto max-w-md">
    <div class="rounded-[2rem] bg-white p-6 text-ink-900 shadow-2xl shadow-brand-10/30">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-ink-500">Confeitaria da Ju · MEI</p>
                <p class="font-title text-2xl font-bold">R$ 12.480,00</p>
                <p class="text-xs text-emerald-600">+ 18% de vendas no mês</p>
            </div>
            <x-site.icon-tile icon="storefront" />
        </div>
        <div class="mt-6 flex h-32 items-end gap-2">
            @foreach ([38, 52, 44, 66, 58, 80, 72, 95] as $h)
                <span class="flex-1 rounded-t-lg {{ $h === 95 ? 'bg-brand-40' : 'bg-lilac-100' }}" style="height: {{ $h }}%"></span>
            @endforeach
        </div>
        <div class="mt-6 space-y-2 text-sm">
            @foreach ([['qr-code', 'Pix de cliente', '+ R$ 89,00'], ['barcode', 'Boleto pago', '+ R$ 1.250,00'], ['receipt', 'Nota fiscal emitida', 'NF 0482']] as [$icon, $label, $value])
                <div class="flex items-center justify-between rounded-xl bg-ink-100 px-3 py-2.5">
                    <span class="flex items-center gap-2">@svg('phosphor-'.$icon.'-duotone', 'size-5 text-brand-40') {{ $label }}</span>
                    <span class="font-semibold">{{ $value }}</span>
                </div>
            @endforeach
        </div>
    </div>
    <img src="{{ asset('card-front-pj.svg') }}" alt="" class="animate-float absolute -top-10 -right-10 hidden w-48 rotate-12 drop-shadow-2xl sm:block">
</div>
