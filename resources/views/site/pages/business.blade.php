@extends('layouts.site')

@section('title', 'Conta PJ para MEI e ME | Bankboost')
@section('description', 'Boost Pejota: conta PJ gratuita para MEI e ME, com Pix, boletos, maquininha virtual, cartão empresarial e gestão de cobranças.')

@php
    $features = [
        ['qr-code', 'Pix com QR Code', 'Gere cobranças com valor fixo e receba na hora, com conciliação automática.'],
        ['barcode', 'Boletos', 'Emita boletos com juros e multa configurados, e acompanhe quem já pagou.'],
        ['device-mobile', 'Maquininha virtual', 'Receba no cartão pelo celular, sem aluguel de máquina.'],
        ['credit-card', 'Cartão empresarial', 'Cartões físicos e virtuais para você e para o time, com limites por pessoa.'],
        ['receipt', 'Notas e DAS', 'Lembrete e pagamento do DAS do MEI e emissão de nota de serviço pelo app.'],
        ['chart-bar', 'Gestão do caixa', 'Entradas, saídas e categorias em gráficos claros. Exporte para o contador.'],
    ];
    $plans = [
        ['MEI', 'Grátis', 'Para quem está começando ou fatura até R$ 81 mil por ano.', ['Pix e TED ilimitados', '30 boletos por mês', 'Cartão empresarial sem anuidade', 'Lembrete e pagamento do DAS'], false],
        ['ME', 'R$ 0 até R$ 50 mil/mês', 'Para micro e pequenas empresas com equipe e mais volume.', ['Tudo do plano MEI', '200 boletos por mês', 'Até 10 cartões para o time', 'Acesso para o contador', 'Integração por API'], true],
    ];
    $testimonials = [
        ['Juliana Prates', 'Confeitaria da Ju · MEI', 'Separei de vez o dinheiro da confeitaria do meu. Agora sei exatamente quanto a loja lucra.'],
        ['Thiago Andrade', 'Andrade Bikes · ME', 'Os boletos com baixa automática acabaram com a planilha de controle. Ganhei umas horas por semana.'],
        ['Camila Rocha', 'Estúdio Rocha · MEI', 'Recebo por Pix com QR Code no balcão e o dinheiro já está na conta antes do cliente sair.'],
    ];
    $steps = [
        ['buildings', 'Tenha um CNPJ', 'MEI, ME ou EI. Ainda não tem? O blog explica como abrir o MEI em uma tarde.'],
        ['user-circle', 'Abra a conta PF', 'O responsável pela empresa precisa ter conta pessoal no Bankboost.'],
        ['briefcase', 'Adicione a empresa', 'No app, toque em “Abrir conta PJ” e informe o CNPJ. A análise leva minutos.'],
    ];
@endphp

@section('content')
    <x-site.page-hero eyebrow="Boost Pejota · Conta PJ" title="A conta que faz o seu negócio decolar" text="Conta PJ gratuita para MEI e ME, com cobranças por Pix e boleto, maquininha virtual e cartão empresarial. Menos tempo no banco, mais tempo vendendo." art="business">
        <x-site.button href="#planos" size="lg" icon="arrow-right">Ver planos</x-site.button>
        <x-site.button :href="route('register')" variant="outline-light" size="lg">Abrir conta PJ</x-site.button>
    </x-site.page-hero>

    {{-- Recursos --}}
    <section class="section">
        <div class="container-site">
            <x-site.heading align="center" eyebrow="Recursos" title="Feita para quem empreende" text="Do primeiro Pix de cliente ao fechamento do mês com o contador." />
            <div class="mt-16 grid gap-x-10 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($features as $i => [$icon, $title, $text])
                    <x-site.feature :icon="$icon" :title="$title" data-aos="fade-up" :data-aos-delay="($i % 3) * 80">{{ $text }}</x-site.feature>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Planos --}}
    <section id="planos" class="section bg-ink-100">
        <div class="container-site">
            <x-site.heading align="center" eyebrow="Planos" title="MEI ou ME? Tem conta para os dois" />
            <div class="mx-auto mt-14 grid max-w-5xl gap-6 md:grid-cols-2">
                @foreach ($plans as $i => [$name, $price, $text, $items, $highlight])
                    <article class="relative flex flex-col rounded-[2rem] p-9 {{ $highlight ? 'bg-brand-10 text-white' : 'bg-white ring-1 ring-ink-200' }}" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        @if ($highlight)
                            <span class="absolute top-8 right-8 rounded-full bg-accent px-3 py-1 text-xs font-bold text-brand-10">Mais completo</span>
                        @endif
                        <h3 class="text-2xl font-bold {{ $highlight ? 'text-white' : '' }}">Plano {{ $name }}</h3>
                        <p class="mt-2 {{ $highlight ? 'text-white/70' : 'text-ink-600' }}">{{ $text }}</p>
                        <p class="mt-6 font-title text-3xl font-bold {{ $highlight ? 'text-accent' : 'text-brand-40' }}">{{ $price }}</p>
                        <ul class="mt-8 flex-1 space-y-3">
                            @foreach ($items as $item)
                                <li class="flex gap-3">@svg('phosphor-check-circle-duotone', 'size-6 shrink-0 '.($highlight ? 'text-accent' : 'text-brand-40')) {{ $item }}</li>
                            @endforeach
                        </ul>
                        <x-site.button :href="route('register')" :variant="$highlight ? 'primary' : 'brand'" class="mt-9">Abrir conta {{ $name }}</x-site.button>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Como abrir --}}
    <section class="section">
        <div class="container-site">
            <x-site.heading eyebrow="Como abrir" title="Três passos até a primeira venda" />
            <div class="mt-14 grid gap-6 md:grid-cols-3">
                @foreach ($steps as $i => [$icon, $title, $text])
                    <div class="relative rounded-3xl p-8 ring-1 ring-ink-200" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        <span class="absolute top-6 right-7 font-title text-6xl font-bold text-ink-100">{{ $i + 1 }}</span>
                        <x-site.icon-tile :icon="$icon" />
                        <h3 class="mt-6 text-lg font-semibold">{{ $title }}</h3>
                        <p class="mt-2 text-ink-600">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Depoimentos --}}
    <section class="section bg-linear-to-br from-brand-10 to-brand-20">
        <div class="container-site">
            <x-site.heading dark eyebrow="Quem já usa" title="Negócios de verdade, histórias de verdade*" text="*Bom, tão de verdade quanto um banco fictício permite." />
            <div class="mt-14 grid gap-6 md:grid-cols-3">
                @foreach ($testimonials as $i => [$name, $business, $text])
                    <figure class="flex flex-col rounded-3xl bg-white/5 p-8 text-white ring-1 ring-white/10" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        @svg('phosphor-quotes-fill', 'size-8 text-accent')
                        <blockquote class="mt-4 flex-1 text-white/85">{{ $text }}</blockquote>
                        <figcaption class="mt-6">
                            <span class="block font-semibold">{{ $name }}</span>
                            <span class="text-sm text-white/60">{{ $business }}</span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    <x-site.cta title="Seu CNPJ merece uma conta sem tarifa" text="Abra a conta PJ pelo app em poucos minutos e comece a receber dos seus clientes hoje." />
@endsection
