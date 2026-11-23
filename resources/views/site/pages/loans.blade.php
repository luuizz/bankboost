@extends('layouts.site')

@section('title', 'Empréstimo pessoal | Bankboost')
@section('description', 'Empréstimo pessoal Bankboost: simulação na hora, taxa clara, até 24 parcelas e dinheiro na conta no mesmo dia.')

@php
    $rate = 2.49; // taxa mensal ilustrativa
    $benefits = [
        ['lightning', 'Dinheiro no mesmo dia', 'Contratou até as 18h? O valor cai na conta no mesmo dia útil.'],
        ['calendar-blank', 'Até 45 dias para começar', 'Escolha a data da primeira parcela, de 30 a 45 dias depois.'],
        ['percent', 'Taxa clara', 'O custo efetivo total (CET) aparece antes da contratação, sem letra miúda.'],
        ['arrow-counter-clockwise', 'Antecipe parcelas', 'Pague antes e tenha desconto proporcional nos juros, pelo próprio app.'],
    ];
    $uses = [
        ['Quitar dívidas caras', 'Troque o rotativo do cartão ou o cheque especial por uma taxa bem menor.', 'credit-card'],
        ['Reformar a casa', 'Aquela obra que está esperando há tempos pode sair do papel.', 'paint-roller'],
        ['Investir em você', 'Curso, pós-graduação ou certificação que vai mudar sua carreira.', 'graduation-cap'],
        ['Imprevistos', 'Conserto do carro, saúde, mudança: o fôlego que você precisa agora.', 'first-aid-kit'],
    ];
    $steps = ['Simule no app ou aqui no site', 'Veja a proposta com taxa e CET', 'Confirme com sua senha e biometria', 'Receba o dinheiro na conta'];
    $faq = [
        ['question' => 'Quem pode pedir o empréstimo?', 'answer' => 'Clientes com conta Bankboost há pelo menos 30 dias. A oferta aparece no app depois da análise de crédito.'],
        ['question' => 'Consulta o meu CPF em órgãos de proteção ao crédito?', 'answer' => 'Sim, como qualquer instituição financeira. A análise considera também o uso da sua conta.'],
        ['question' => 'Posso cancelar depois de contratar?', 'answer' => 'Pode, em até 7 dias, devolvendo o valor recebido. Não há cobrança de juros nesse caso.'],
        ['question' => 'O que acontece se eu atrasar uma parcela?', 'answer' => 'São cobrados multa de 2% e juros de mora proporcionais. O app avisa antes do vencimento para evitar atraso.'],
    ];
@endphp

@section('content')
    <x-site.page-hero eyebrow="Empréstimo pessoal" title="O fôlego que você precisa, com taxa que você entende" text="Simule em segundos, escolha as parcelas e receba o dinheiro no mesmo dia. Sem gerente, sem papelada e sem surpresa." art="loan-approved">
        <x-site.button href="#simulador" size="lg" icon="calculator">Simular agora</x-site.button>
    </x-site.page-hero>

    {{-- Simulador --}}
    <section id="simulador" class="section">
        <div class="container-site grid items-center gap-12 lg:grid-cols-[1fr_1.2fr]">
            <div>
                <x-site.heading eyebrow="Simulador" title="Simule sem compromisso" :text="'Taxa ilustrativa de '.number_format($rate, 2, ',', '.').'% ao mês, no sistema de parcelas fixas (Tabela Price). A taxa real depende da análise de crédito.'" />
                <ul class="mt-8 space-y-3 text-ink-700">
                    @foreach ($steps as $i => $step)
                        <li class="flex items-center gap-3"><span class="flex size-8 items-center justify-center rounded-full bg-lilac-100 font-title text-sm font-bold text-brand-40">{{ $i + 1 }}</span>{{ $step }}</li>
                    @endforeach
                </ul>
            </div>

            <form class="rounded-[2rem] bg-white p-7 shadow-xl shadow-brand-10/5 ring-1 ring-ink-200 sm:p-9" data-simulator="loan" data-rate="{{ $rate }}" onsubmit="return false">
                <div class="space-y-7">
                    <label class="block">
                        <span class="flex justify-between text-sm font-semibold"><span>Quanto você precisa?</span><output data-output="value" class="text-brand-40"></output></span>
                        <input type="range" name="value" min="500" max="50000" step="500" value="8000" class="range mt-3">
                    </label>
                    <label class="block">
                        <span class="flex justify-between text-sm font-semibold"><span>Em quantas parcelas?</span><output data-output="installments" class="text-brand-40"></output></span>
                        <input type="range" name="installments" min="3" max="24" step="1" value="18" class="range mt-3">
                    </label>
                </div>
                <div class="mt-9 rounded-3xl bg-linear-to-br from-brand-30 to-brand-10 p-7 text-white">
                    <p class="text-sm text-white/70">Parcela mensal</p>
                    <p class="font-title text-4xl font-bold" data-result="payment">—</p>
                    <dl class="mt-5 grid grid-cols-2 gap-4 border-t border-white/15 pt-5 text-sm">
                        <div><dt class="text-white/70">Total a pagar</dt><dd class="font-semibold" data-result="total">—</dd></div>
                        <div><dt class="text-white/70">Juros no período</dt><dd class="font-semibold" data-result="interest">—</dd></div>
                    </dl>
                </div>
            </form>
        </div>
    </section>

    {{-- Vantagens --}}
    <section class="section bg-ink-100">
        <div class="container-site">
            <x-site.heading align="center" eyebrow="Vantagens" title="Crédito sem dor de cabeça" />
            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($benefits as $i => [$icon, $title, $text])
                    <div class="rounded-3xl bg-white p-7 ring-1 ring-ink-200" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        <x-site.feature :icon="$icon" :title="$title">{{ $text }}</x-site.feature>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Para que usar --}}
    <section class="section">
        <div class="container-site">
            <x-site.heading eyebrow="Para que usar" title="Um empréstimo bem usado muda a sua vida" text="Antes de contratar, vale pensar no objetivo. Estes são os mais comuns entre nossos clientes." />
            <div class="mt-14 grid gap-6 md:grid-cols-2">
                @foreach ($uses as $i => [$title, $text, $icon])
                    <div class="flex gap-5 rounded-3xl p-7 ring-1 ring-ink-200" data-aos="fade-up" data-aos-delay="{{ ($i % 2) * 80 }}">
                        <x-site.icon-tile :icon="$icon" tone="accent" />
                        <div>
                            <h3 class="text-lg font-semibold">{{ $title }}</h3>
                            <p class="mt-1 text-ink-600">{{ $text }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="mt-10 flex items-start gap-3 rounded-2xl bg-accent/15 p-5 text-sm text-ink-700">
                @svg('phosphor-warning-circle-duotone', 'size-6 shrink-0 text-[#8a6d00]')
                Crédito é responsabilidade. Contrate só o que cabe no seu orçamento e prefira parcelas que não passem de 30% da sua renda mensal.
            </p>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="section bg-ink-100">
        <div class="container-site grid gap-12 lg:grid-cols-[1fr_1.4fr]">
            <x-site.heading eyebrow="Dúvidas" title="Perguntas sobre o empréstimo" />
            <x-site.faq :items="$faq" />
        </div>
    </section>

    <x-site.cta title="Precisa de um fôlego?" text="Abra a conta e, em 30 dias, a oferta de crédito aparece no seu app." />
@endsection
