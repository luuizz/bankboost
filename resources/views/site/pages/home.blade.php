@extends('layouts.site')

@section('title', 'Bankboost | Desperte seu potencial financeiro')

@php
    $highlights = [
        ['icon' => 'credit-card', 'text' => 'Cartão sem anuidade'],
        ['icon' => 'prohibit', 'text' => 'Conta 100% grátis'],
        ['icon' => 'trend-up', 'text' => 'Dinheiro rendendo todo dia'],
    ];
    $appFeatures = [
        ['icon' => 'credit-card', 'title' => 'Cartão de crédito sem anuidade', 'text' => 'Conta digital com cartão internacional, sem anuidade e sem complicação.'],
        ['icon' => 'percent', 'title' => 'Sem taxas escondidas', 'text' => 'Transferências, boletos de depósito e Pix sem custo, quantos você quiser.'],
        ['icon' => 'currency-circle-dollar', 'title' => 'Mais rendimento', 'text' => 'O saldo da conta rende mais que a poupança e você resgata quando quiser.'],
    ];
    $products = [
        ['route' => 'site.account', 'icon' => 'device-mobile', 'title' => 'Conta digital', 'text' => 'Pix, boletos e transferências ilimitadas, sem tarifa de manutenção.'],
        ['route' => 'site.card', 'icon' => 'credit-card', 'title' => 'Cartão de crédito', 'text' => 'Zero anuidade, cartão virtual para compras online e 1% de cashback.'],
        ['route' => 'site.investments', 'icon' => 'chart-line-up', 'title' => 'Investimentos', 'text' => 'CDB com liquidez diária, Tesouro Direto e fundos a partir de R$ 1.'],
        ['route' => 'site.loans', 'icon' => 'hand-coins', 'title' => 'Empréstimo', 'text' => 'Crédito pessoal com taxa clara, simulação na hora e dinheiro no mesmo dia.'],
    ];
    $testimonials = [
        ['name' => 'Kleriene Duarte', 'role' => 'Designer, Recife', 'text' => 'Eu uso o Boost Mais pra fazer render o pouquinho que eu tenho. Abro o app e o rendimento do dia já está lá. Simples assim.'],
        ['name' => 'Márcio Gurka', 'role' => 'Professor, Curitiba', 'text' => 'O cartão virtual com bloqueio na hora me deu muita segurança para comprar online. E a embalagem do cartão físico é linda!'],
        ['name' => 'Juliana Prates', 'role' => 'Dona de confeitaria, BH', 'text' => 'Abri a conta PJ do MEI em dez minutos. Recebo por Pix, emito boleto e separo o dinheiro da loja do meu.'],
        ['name' => 'Rafael Nogueira', 'role' => 'Desenvolvedor, Porto Alegre', 'text' => 'Atendimento por chat que resolve de verdade, sem URA e sem transferir para outro setor. Raro hoje em dia.'],
        ['name' => 'Patrícia Lemos', 'role' => 'Enfermeira, Salvador', 'text' => 'Consegui organizar as contas da casa com os cofrinhos. Cada objetivo tem o seu e eu vejo tudo crescer.'],
    ];
@endphp

@section('content')
    {{-- 1. Hero --}}
    <section class="hero-bg pt-32 pb-24 lg:pt-44 lg:pb-32">
        <div class="pointer-events-none absolute -top-40 -right-40 size-[40rem] rounded-full bg-brand-30/50 blur-3xl"></div>

        <div class="container-site relative grid items-center gap-16 lg:grid-cols-[1.1fr_1fr]">
            <div data-aos="fade-right">
                <p class="font-title text-lg font-semibold text-white/60">Abra sua conta, é só baixar o app!</p>
                <h1 class="mt-5 text-5xl leading-[1.05] font-bold text-white sm:text-6xl lg:text-7xl">Desperte seu potencial financeiro com o Bankboost!</h1>
                <div class="mt-10 flex flex-wrap gap-4">
                    <x-site.button :href="route('register')" size="lg" icon="arrow-right">Abra sua conta digital</x-site.button>
                    <x-site.button :href="route('site.account')" variant="outline-light" size="lg">Conheça a conta</x-site.button>
                </div>
                <ul class="mt-12 grid gap-5 sm:grid-cols-3">
                    @foreach ($highlights as $item)
                        <li class="flex items-center gap-3 text-sm font-medium">
                            <x-site.icon-tile :icon="$item['icon']" tone="dark" size="round" />
                            {{ $item['text'] }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="relative mx-auto w-full max-w-lg" data-aos="fade-left">
                <p class="text-right font-title text-4xl font-bold text-white/25 lg:text-5xl">Banco 100% digital</p>
                <div class="relative mt-6 h-80 sm:h-96">
                    <img src="{{ asset('card-back.png') }}" alt="" class="absolute right-0 bottom-0 w-[78%] rotate-[-8deg] drop-shadow-2xl">
                    <img src="{{ asset('card-front.png') }}" alt="Cartão Bankboost" class="animate-float absolute top-0 left-0 w-[80%] rotate-[8deg] drop-shadow-2xl">
                </div>
            </div>
        </div>
    </section>

    {{-- 2. App --}}
    <section class="section">
        <div class="container-site grid items-center gap-16 lg:grid-cols-2">
            <div class="relative order-2 flex flex-col items-center gap-8 lg:order-1" data-aos="fade-up">
                <div class="absolute inset-x-10 top-10 bottom-32 -z-10 rounded-[3rem] bg-lilac-50"></div>
                <img src="{{ asset('mockup-iphone-01.svg') }}" alt="Tela inicial do app Bankboost com saldo, crédito e investimentos" class="w-64 drop-shadow-2xl">
                <div class="text-center">
                    <h3 class="text-xl font-semibold">Baixe nosso app</h3>
                    <p class="mt-1 text-ink-600">Que tal ver como a gente deixa sua vida mais simples?</p>
                    <div class="mt-5 flex flex-wrap justify-center gap-3">
                        <a href="#" class="flex items-center gap-2 rounded-xl bg-ink-900 px-4 py-2.5 text-white hover:bg-ink-800">
                            @svg('phosphor-apple-logo-fill', 'size-6')
                            <span class="text-left text-xs leading-tight">Baixar na<br><strong class="text-sm">App Store</strong></span>
                        </a>
                        <a href="#" class="flex items-center gap-2 rounded-xl bg-ink-900 px-4 py-2.5 text-white hover:bg-ink-800">
                            @svg('phosphor-google-play-logo-fill', 'size-6')
                            <span class="text-left text-xs leading-tight">Disponível no<br><strong class="text-sm">Google Play</strong></span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2" data-aos="fade-left">
                <x-site.heading eyebrow="App Bankboost" title="Resolva sua vida direto pelo app boost!" text="Tudo o que você faz no banco, sem fila, sem papel e sem horário comercial." />
                <ul class="mt-10 divide-y divide-ink-200">
                    @foreach ($appFeatures as $feature)
                        <li class="flex gap-5 py-6">
                            <x-site.icon-tile :icon="$feature['icon']" />
                            <div>
                                <h3 class="text-lg font-semibold">{{ $feature['title'] }}</h3>
                                <p class="mt-1 text-ink-600">{{ $feature['text'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <a href="#produtos" class="mt-4 inline-flex items-center gap-2 font-title font-semibold text-brand-40 hover:gap-3 transition-all">
                    Conheça outros produtos
                    @svg('phosphor-arrow-right-bold', 'size-4')
                </a>
            </div>
        </div>
    </section>

    {{-- 3. Produtos --}}
    <section id="produtos" class="section bg-ink-100">
        <div class="container-site">
            <x-site.heading align="center" eyebrow="Produtos Boost" title="Um banco inteiro, do tamanho do seu bolso" text="Comece pela conta e vá adicionando o que fizer sentido para você." />
            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($products as $i => $product)
                    <a href="{{ route($product['route']) }}" class="group flex flex-col rounded-3xl bg-white p-7 shadow-sm ring-1 ring-ink-200 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-10/10" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        <x-site.icon-tile :icon="$product['icon']" />
                        <h3 class="mt-6 text-xl font-semibold">{{ $product['title'] }}</h3>
                        <p class="mt-2 flex-1 text-ink-600">{{ $product['text'] }}</p>
                        <span class="mt-6 inline-flex items-center gap-2 font-title text-sm font-semibold text-brand-40">
                            Saiba mais
                            @svg('phosphor-arrow-right-bold', 'size-4 transition group-hover:translate-x-1')
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 4. Conta PJ --}}
    <section class="section">
        <div class="container-site">
            <div class="relative overflow-hidden rounded-[2.5rem] bg-linear-to-br from-brand-20 to-brand-30 px-8 py-14 text-white sm:px-14 lg:py-20" data-aos="fade-up">
                <div class="relative z-10 max-w-xl">
                    <p class="font-title text-sm font-semibold tracking-wide text-accent uppercase">Boost Pejota</p>
                    <h2 class="mt-3 text-3xl leading-tight font-bold text-white sm:text-5xl">Contas digitais PJ gratuitas para decolar seu negócio!</h2>
                    <p class="mt-5 text-lg text-white/80">As melhores contas para fazer pagamentos, compras e receber dos seus clientes. Com maquininha virtual e emissão de boletos.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <x-site.button :href="route('site.business').'#planos'">Sou MEI</x-site.button>
                        <x-site.button :href="route('site.business').'#planos'" variant="outline-light">Sou ME</x-site.button>
                    </div>
                </div>
                <img src="{{ asset('card-front-pj.svg') }}" alt="Cartão Bankboost Pejota" class="pointer-events-none absolute -right-16 -bottom-10 hidden w-[30rem] rotate-[-10deg] drop-shadow-2xl lg:block">
            </div>
        </div>
    </section>

    {{-- 5. Depoimentos e números --}}
    <section class="section bg-ink-100">
        <div class="container-site">
            <div class="grid gap-10 lg:grid-cols-[1fr_auto] lg:items-end">
                <x-site.heading eyebrow="Prático, fácil, moderno" title="Você resolve tudo sem estresse 😊" />
                <div class="grid grid-cols-3 gap-8">
                    <x-site.stat value="4,8" label="nota nas lojas" />
                    <x-site.stat value="3 mi" label="de clientes" />
                    <x-site.stat value="24h" label="de atendimento" />
                </div>
            </div>

            <div class="swiper mt-14 !pb-12" data-slider>
                <div class="swiper-wrapper">
                    @foreach ($testimonials as $testimonial)
                        <figure class="swiper-slide !h-auto rounded-3xl bg-white p-8 shadow-sm ring-1 ring-ink-200">
                            @svg('phosphor-quotes-fill', 'size-8 text-lilac-200')
                            <blockquote class="mt-4 text-ink-700">{{ $testimonial['text'] }}</blockquote>
                            <figcaption class="mt-6 flex items-center gap-3">
                                <span class="flex size-11 items-center justify-center rounded-full bg-brand-40 font-title font-semibold text-white">{{ mb_substr($testimonial['name'], 0, 1) }}</span>
                                <span>
                                    <span class="block font-semibold text-ink-900">{{ $testimonial['name'] }}</span>
                                    <span class="block text-sm text-ink-500">{{ $testimonial['role'] }}</span>
                                </span>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    {{-- 6. Blog --}}
    @if ($posts->isNotEmpty())
        <section class="section">
            <div class="container-site">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <x-site.heading eyebrow="#focanodinheiro" title="Conteúdo para cuidar do seu dinheiro" />
                    <x-site.button :href="route('site.blog.index')" variant="outline" icon="arrow-right">Ver o blog</x-site.button>
                </div>
                <div class="mt-12 grid gap-8 md:grid-cols-3">
                    @foreach ($posts as $post)
                        @include('site.partials.post-card', ['post' => $post])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-site.cta title="Domine o seu dinheiro com uma conta 100% digital" />
@endsection
