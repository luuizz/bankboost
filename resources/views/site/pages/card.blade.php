@extends('layouts.site')

@section('title', 'Cartão de crédito sem anuidade | Bankboost')
@section('description', 'Cartão de crédito Bankboost: zero anuidade, 1% de cashback, cartão virtual para compras online e controle total pelo app.')

@php
    $benefits = [
        ['prohibit', 'Zero anuidade', 'Nem no primeiro ano, nem depois. Sem gasto mínimo para isentar.'],
        ['coins', '1% de cashback', 'Parte de cada compra volta como desconto na fatura ou direto no Boost Mais.'],
        ['globe-hemisphere-west', 'Internacional', 'Aceito em milhões de lojas no mundo todo, com IOF mostrado antes da compra.'],
        ['contactless-payment', 'Aproximação', 'Pague encostando o cartão ou o celular, com Apple Pay e Google Pay.'],
    ];
    $controls = [
        ['eye-slash', 'Cartão virtual', 'Crie um cartão para cada assinatura ou loja online e apague quando quiser.'],
        ['lock-key', 'Bloqueio na hora', 'Perdeu o cartão? Um toque e ele fica bloqueado, sem ligar para ninguém.'],
        ['sliders-horizontal', 'Limite sob medida', 'Você ajusta o limite para baixo quando quiser e pede aumento pelo app.'],
        ['bell-ringing', 'Aviso de cada compra', 'Notificação com valor e loja no mesmo segundo da compra.'],
    ];
    $steps = [
        ['Abra a conta', 'O cartão de crédito é pedido pelo próprio app, depois da conta aberta.'],
        ['Análise na hora', 'A gente avalia seu perfil em minutos e mostra o limite inicial.'],
        ['Use o virtual', 'O cartão virtual fica liberado na hora, enquanto o físico vai pelo correio.'],
        ['Limite que cresce', 'Pagando em dia e usando a conta, o limite aumenta com o tempo.'],
    ];
    $faq = [
        ['question' => 'Como peço o cartão de crédito?', 'answer' => 'Depois de abrir a conta, toque em “Cartão” no app e siga a análise. Ela leva poucos minutos.'],
        ['question' => 'O cashback tem limite?', 'answer' => 'Não há teto mensal. O valor aparece na fatura seguinte e você escolhe se vira desconto ou investimento.'],
        ['question' => 'Posso usar o cartão no exterior?', 'answer' => 'Pode. O app mostra a cotação e o IOF antes de cada compra internacional, sem surpresa na fatura.'],
        ['question' => 'O cartão físico tem custo de entrega?', 'answer' => 'Não. O envio é grátis para todo o Brasil e leva até 10 dias úteis.'],
    ];
@endphp

@section('content')
    <x-site.page-hero eyebrow="Cartão de crédito" title="Zero anuidade, cashback e controle na palma da mão" text="Um cartão internacional que devolve parte do que você gasta e se ajusta à sua rotina, não o contrário." art="card-stack">
        <x-site.button :href="route('register')" size="lg" icon="arrow-right">Quero meu cartão</x-site.button>
    </x-site.page-hero>

    {{-- Benefícios --}}
    <section class="section">
        <div class="container-site">
            <x-site.heading align="center" eyebrow="Benefícios" title="Mais vantagem, menos taxa" />
            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($benefits as $i => [$icon, $title, $text])
                    <div class="rounded-3xl bg-ink-100 p-7" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        <x-site.feature :icon="$icon" :title="$title">{{ $text }}</x-site.feature>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Cashback --}}
    <section class="section bg-linear-to-br from-brand-10 to-brand-20 text-white">
        <div class="container-site grid items-center gap-14 lg:grid-cols-2">
            <div data-aos="fade-right">
                <x-site.heading dark eyebrow="Cashback" title="Seu dinheiro de volta, todo mês" text="Gastou R$ 2.000 no mês? R$ 20 voltam para você. Sem pontos que expiram e sem catálogo de prêmios." />
                <div class="mt-10 grid grid-cols-3 gap-6">
                    <x-site.stat dark value="1%" label="de volta em toda compra" />
                    <x-site.stat dark value="R$ 0" label="de anuidade" />
                    <x-site.stat dark value="40 dias" label="para pagar" />
                </div>
            </div>
            <div class="rounded-[2rem] bg-white/5 p-6 ring-1 ring-white/10 sm:p-8" data-aos="fade-left">
                <p class="text-sm text-white/70">Fatura de novembro</p>
                <ul class="mt-4 divide-y divide-white/10 text-sm">
                    @foreach ([['shopping-cart', 'Mercado', 'R$ 640,20', 'R$ 6,40'], ['gas-pump', 'Combustível', 'R$ 320,00', 'R$ 3,20'], ['fork-knife', 'Restaurantes', 'R$ 210,50', 'R$ 2,11'], ['film-strip', 'Streaming', 'R$ 153,30', 'R$ 1,53']] as [$icon, $label, $spent, $back])
                        <li class="flex items-center justify-between py-3.5">
                            <span class="flex items-center gap-3">@svg('phosphor-'.$icon.'-duotone', 'size-5 text-accent') {{ $label }}</span>
                            <span class="text-right"><span class="block">{{ $spent }}</span><span class="text-xs text-emerald-300">+ {{ $back }}</span></span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4 flex items-center justify-between rounded-2xl bg-accent px-5 py-4 font-title font-semibold text-brand-10">
                    <span>Cashback do mês</span><span>R$ 13,24</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Controle --}}
    <section class="section">
        <div class="container-site">
            <x-site.heading eyebrow="Controle" title="Quem manda no cartão é você" text="Tudo o que antes exigia uma ligação para a central agora está a um toque." />
            <div class="mt-14 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($controls as $i => [$icon, $title, $text])
                    <x-site.feature :icon="$icon" :title="$title" data-aos="fade-up" :data-aos-delay="$i * 80">{{ $text }}</x-site.feature>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Como pedir --}}
    <section class="section bg-ink-100">
        <div class="container-site grid gap-14 lg:grid-cols-2">
            <div>
                <x-site.heading eyebrow="Como pedir" title="Do pedido à primeira compra" />
                <ol class="mt-10 space-y-6">
                    @foreach ($steps as $i => [$title, $text])
                        <li class="flex gap-5" data-aos="fade-up">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-brand-40 font-title font-bold text-white">{{ $i + 1 }}</span>
                            <div>
                                <h3 class="text-lg font-semibold">{{ $title }}</h3>
                                <p class="mt-1 text-ink-600">{{ $text }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
            <div>
                <h3 class="text-xl font-semibold">Dúvidas sobre o cartão</h3>
                <x-site.faq :items="$faq" class="mt-6" />
            </div>
        </div>
    </section>

    <x-site.cta title="Peça seu cartão sem anuidade" text="Abra a conta, faça a análise no app e comece a usar o cartão virtual no mesmo dia." />
@endsection
