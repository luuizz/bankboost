@extends('layouts.site')

@section('title', 'Investimentos | Boost Mais | Bankboost')
@section('description', 'Invista a partir de R$ 1 com o Bankboost: Boost Mais com liquidez diária, CDB, Tesouro Direto e fundos, sem taxa de corretagem.')

@php
    // Taxas usadas no simulador (ilustrativas): CDI anual e poupança anual
    $cdi = 13.15;
    $savings = 7.2;
    $products = [
        ['piggy-bank', 'Boost Mais', '100% do CDI', 'Liquidez diária', 'O saldo da conta rende sozinho e você resgata a qualquer momento.'],
        ['bank', 'CDB Boost', 'até 115% do CDI', 'De 6 meses a 3 anos', 'Quanto mais tempo o dinheiro fica, maior a taxa. Garantido pelo FGC.'],
        ['flag-banner', 'Tesouro Direto', 'Selic, IPCA+ e prefixado', 'Títulos públicos', 'Os investimentos mais seguros do país, sem taxa de custódia no Bankboost.'],
        ['chart-pie-slice', 'Fundos', 'Renda fixa, multimercado e ações', 'Curadoria do time', 'Fundos escolhidos a dedo, com aplicação inicial a partir de R$ 100.'],
    ];
    $why = [
        ['currency-circle-dollar', 'Comece com R$ 1', 'Não precisa juntar uma fortuna para começar a investir.'],
        ['receipt', 'Sem taxa de corretagem', 'Aplicação e resgate sem custo. Você só paga o imposto previsto em lei.'],
        ['graduation-cap', 'Conteúdo para aprender', 'Guias curtos no app e no blog explicam cada produto antes de você investir.'],
        ['target', 'Objetivos com prazo', 'Defina meta e data, e o app sugere quanto investir por mês.'],
    ];
    $profiles = [
        ['Conservador', 'shield-check', 'Prioriza segurança e liquidez.', ['Boost Mais', 'Tesouro Selic', 'CDB de liquidez diária']],
        ['Moderado', 'scales', 'Aceita alguma oscilação para ganhar mais.', ['CDB de prazo longo', 'Tesouro IPCA+', 'Fundos multimercado']],
        ['Arrojado', 'rocket-launch', 'Pensa no longo prazo e tolera variação.', ['Fundos de ações', 'Tesouro prefixado longo', 'Previdência']],
    ];
@endphp

@section('content')
    <x-site.page-hero eyebrow="Investimentos · Boost Mais" title="Seu dinheiro rendendo mais, desde o primeiro real" text="Do Boost Mais, que rende todo dia, ao Tesouro Direto: investimentos simples de entender, sem taxa de corretagem e a partir de R$ 1." art="invest-chart">
        <x-site.button href="#simulador" size="lg" icon="calculator">Simular rendimento</x-site.button>
        <x-site.button :href="route('register')" variant="outline-light" size="lg">Começar a investir</x-site.button>
    </x-site.page-hero>

    {{-- Produtos --}}
    <section class="section">
        <div class="container-site">
            <x-site.heading align="center" eyebrow="Onde investir" title="Opções para cada objetivo" :text="'Rentabilidades ilustrativas, com base no CDI de '.number_format($cdi, 2, ',', '.').'% ao ano.'" />
            <div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach ($products as $i => [$icon, $name, $rate, $term, $text])
                    <article class="flex flex-col rounded-3xl p-7 ring-1 ring-ink-200 {{ $i === 0 ? 'bg-brand-40 text-white ring-0' : 'bg-white' }}" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        <x-site.icon-tile :icon="$icon" :tone="$i === 0 ? 'dark' : 'lilac'" />
                        <h3 class="mt-6 text-xl font-semibold {{ $i === 0 ? 'text-white' : '' }}">{{ $name }}</h3>
                        <p class="mt-1 font-title text-lg font-semibold {{ $i === 0 ? 'text-accent' : 'text-brand-40' }}">{{ $rate }}</p>
                        <p class="text-sm {{ $i === 0 ? 'text-white/70' : 'text-ink-500' }}">{{ $term }}</p>
                        <p class="mt-4 {{ $i === 0 ? 'text-white/85' : 'text-ink-600' }}">{{ $text }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Simulador --}}
    <section id="simulador" class="section bg-ink-100">
        <div class="container-site grid items-center gap-12 lg:grid-cols-[1fr_1.2fr]">
            <x-site.heading eyebrow="Simulador" title="Quanto o seu dinheiro pode render?" text="Arraste os controles e compare o Boost Mais com a poupança. É uma estimativa: não considera imposto de renda e a taxa pode mudar." />

            <form class="rounded-[2rem] bg-white p-7 shadow-xl shadow-brand-10/5 ring-1 ring-ink-200 sm:p-9" data-simulator="investment" data-rate-boost="{{ $cdi }}" data-rate-savings="{{ $savings }}" onsubmit="return false">
                <div class="space-y-7">
                    <label class="block">
                        <span class="flex justify-between text-sm font-semibold"><span>Valor inicial</span><output data-output="initial" class="text-brand-40"></output></span>
                        <input type="range" name="initial" min="0" max="50000" step="500" value="5000" class="range mt-3">
                    </label>
                    <label class="block">
                        <span class="flex justify-between text-sm font-semibold"><span>Aporte mensal</span><output data-output="monthly" class="text-brand-40"></output></span>
                        <input type="range" name="monthly" min="0" max="5000" step="50" value="300" class="range mt-3">
                    </label>
                    <label class="block">
                        <span class="flex justify-between text-sm font-semibold"><span>Prazo</span><output data-output="months" class="text-brand-40"></output></span>
                        <input type="range" name="months" min="6" max="120" step="6" value="36" class="range mt-3">
                    </label>
                </div>

                <div class="mt-9 space-y-4 border-t border-ink-200 pt-7">
                    <div>
                        <div class="flex justify-between text-sm"><span class="font-semibold">Boost Mais</span><span class="font-title text-lg font-bold text-brand-40" data-result="boost">—</span></div>
                        <div class="mt-2 h-3 rounded-full bg-brand-40"></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm"><span class="font-semibold text-ink-600">Poupança</span><span class="font-semibold text-ink-600" data-result="savings">—</span></div>
                        <div class="mt-2 h-3 rounded-full bg-ink-300 transition-all" data-result-bar="savings" style="width: 70%"></div>
                    </div>
                    <p class="rounded-2xl bg-emerald-50 p-4 text-sm text-emerald-800">
                        Você teria <strong data-result="difference">—</strong> a mais no Boost Mais, investindo <strong data-result="invested">—</strong> no total.
                    </p>
                </div>
            </form>
        </div>
    </section>

    {{-- Por que --}}
    <section class="section">
        <div class="container-site">
            <x-site.heading eyebrow="Por que investir aqui" title="Investir não precisa ser complicado" />
            <div class="mt-14 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($why as $i => [$icon, $title, $text])
                    <x-site.feature :icon="$icon" :title="$title" data-aos="fade-up" :data-aos-delay="$i * 80">{{ $text }}</x-site.feature>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Perfis --}}
    <section class="section bg-linear-to-br from-brand-10 to-brand-20 text-white">
        <div class="container-site">
            <x-site.heading dark align="center" eyebrow="Perfil de investidor" title="Qual é o seu jeito de investir?" text="Responda o questionário no app e receba sugestões que combinam com você." />
            <div class="mt-14 grid gap-6 md:grid-cols-3">
                @foreach ($profiles as $i => [$name, $icon, $text, $items])
                    <article class="rounded-3xl bg-white/5 p-8 ring-1 ring-white/10" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        <x-site.icon-tile :icon="$icon" tone="dark" />
                        <h3 class="mt-6 text-xl font-semibold text-white">{{ $name }}</h3>
                        <p class="mt-2 text-white/70">{{ $text }}</p>
                        <ul class="mt-6 space-y-2.5 text-sm">
                            @foreach ($items as $item)
                                <li class="flex items-center gap-2">@svg('phosphor-check-bold', 'size-4 text-accent') {{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Segurança --}}
    <section class="section">
        <div class="container-site">
            <div class="grid items-center gap-10 rounded-[2rem] bg-ink-100 p-8 sm:p-12 lg:grid-cols-[auto_1fr_auto]">
                <x-site.icon-tile icon="shield-check" size="lg" />
                <div>
                    <h2 class="text-2xl font-bold">Protegido pelo FGC</h2>
                    <p class="mt-2 text-ink-600">CDBs e o saldo do Boost Mais têm garantia do Fundo Garantidor de Créditos de até R$ 250 mil por CPF. Títulos do Tesouro são garantidos pelo Governo Federal. Como o Bankboost é fictício, esses valores servem só para demonstração.</p>
                </div>
                <x-site.button :href="route('site.blog.index')" variant="outline" icon="arrow-right">Aprender mais</x-site.button>
            </div>
        </div>
    </section>

    <x-site.cta title="Comece a investir hoje" text="Abra a conta grátis e veja o saldo render já no dia seguinte." />
@endsection
