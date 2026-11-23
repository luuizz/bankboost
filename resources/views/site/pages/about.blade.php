@extends('layouts.site')

@section('title', 'Quem somos | Bankboost')
@section('description', 'Conheça a história, os valores e o time por trás do Bankboost, o banco digital que quer simplificar a vida financeira dos brasileiros.')

@php
    $stats = [['3 mi', 'clientes em todo o Brasil'], ['5.570', 'municípios atendidos'], ['4,8', 'de nota nas lojas de apps'], ['280', 'pessoas no time']];
    $values = [
        ['heart', 'Cliente no centro', 'Antes de lançar qualquer coisa, a pergunta é: isso deixa a vida de alguém mais simples?'],
        ['eye', 'Transparência sempre', 'Taxa escondida, letra miúda e pegadinha não entram aqui. Tudo fica claro antes de você decidir.'],
        ['rocket-launch', 'Fazer e aprender', 'Lançamos pequeno, ouvimos muito e melhoramos rápido. Erro honesto faz parte.'],
        ['users-three', 'Diversos de verdade', 'Um banco para o Brasil inteiro precisa de um time com a cara do Brasil.'],
    ];
    $timeline = [
        ['2019', 'A ideia', 'Três amigos cansados de pagar tarifa para ter o próprio dinheiro resolvem criar um banco diferente.'],
        ['2020', 'Primeira conta', 'O app chega às lojas com conta digital, Pix no lançamento e 10 mil clientes na lista de espera.'],
        ['2022', 'Cartão e Boost Mais', 'Cartão sem anuidade com cashback e o saldo da conta passa a render todo dia.'],
        ['2024', 'Boost Pejota', 'Lançamento da conta PJ para MEI e ME, com maquininha virtual.'],
        ['2026', '3 milhões', 'Investimentos completos, empréstimo pessoal e 3 milhões de clientes.'],
    ];
    $leaders = [
        ['Carolina Mendes', 'CEO e cofundadora', 'C', 'bg-accent text-brand-10'],
        ['Rodrigo Alves', 'CTO e cofundador', 'R', 'bg-brand-40 text-white'],
        ['Luana Ferreira', 'Diretora de Produto', 'L', 'bg-emerald-400 text-white'],
        ['Marcos Oliveira', 'Diretor de Riscos', 'M', 'bg-pink-400 text-white'],
    ];
@endphp

@section('content')
    <x-site.page-hero eyebrow="Quem somos" title="A gente existe para descomplicar o seu dinheiro" text="O Bankboost nasceu de uma pergunta simples: por que cuidar do próprio dinheiro precisa ser tão caro e tão chato? Desde então, a resposta é construída junto com milhões de clientes." art="about" />

    {{-- Números --}}
    <section class="section">
        <div class="container-site grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($stats as $i => [$value, $label])
                <x-site.stat :value="$value" :label="$label" class="border-l-4 border-accent pl-6" data-aos="fade-up" :data-aos-delay="$i * 80" />
            @endforeach
        </div>
    </section>

    {{-- Manifesto --}}
    <section class="section bg-ink-100">
        <div class="container-site grid items-center gap-14 lg:grid-cols-2">
            <div data-aos="fade-right">
                <p class="eyebrow">Manifesto</p>
                <blockquote class="mt-4 font-title text-3xl leading-snug font-semibold text-ink-900 sm:text-4xl">“Banco bom é aquele que você quase esquece que existe, porque tudo simplesmente funciona.”</blockquote>
                <p class="mt-6 text-ink-600">Carolina Mendes, CEO e cofundadora</p>
            </div>
            <div class="space-y-5 text-lg text-ink-700" data-aos="fade-left">
                <p>Acreditamos que serviço financeiro de qualidade não deveria ser privilégio. Por isso começamos tirando as tarifas do caminho e colocando a tecnologia a serviço das pessoas.</p>
                <p>Hoje somos um time de 280 pessoas espalhadas pelo Brasil, trabalhando de forma remota, com um objetivo em comum: fazer cada cliente sentir que tem um banco do lado dele.</p>
            </div>
        </div>
    </section>

    {{-- Valores --}}
    <section class="section">
        <div class="container-site">
            <x-site.heading align="center" eyebrow="Nossos valores" title="O que guia cada decisão" />
            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($values as $i => [$icon, $title, $text])
                    <div class="rounded-3xl p-7 ring-1 ring-ink-200" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        <x-site.feature :icon="$icon" :title="$title">{{ $text }}</x-site.feature>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Linha do tempo --}}
    <section class="section bg-linear-to-br from-brand-10 to-brand-20">
        <div class="container-site">
            <x-site.heading dark eyebrow="Nossa história" title="De uma ideia a 3 milhões de clientes" />
            <ol class="mt-14 grid gap-8 md:grid-cols-5">
                @foreach ($timeline as $i => [$year, $title, $text])
                    <li class="relative border-t-2 border-white/20 pt-8" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        <span class="absolute -top-2 left-0 size-4 rounded-full bg-accent ring-4 ring-brand-10"></span>
                        <p class="font-title text-3xl font-bold text-accent">{{ $year }}</p>
                        <h3 class="mt-2 text-lg font-semibold text-white">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-white/70">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Liderança --}}
    <section class="section">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <x-site.heading eyebrow="Liderança" title="Quem está à frente" text="Pessoas fictícias, desafios bem reais." />
                <x-site.button :href="route('site.careers')" variant="outline" icon="arrow-right">Trabalhe com a gente</x-site.button>
            </div>
            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($leaders as $i => [$name, $role, $initial, $color])
                    <div class="rounded-3xl bg-ink-100 p-7 text-center" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        <span class="mx-auto flex size-24 items-center justify-center rounded-full font-title text-3xl font-bold {{ $color }}">{{ $initial }}</span>
                        <h3 class="mt-5 text-lg font-semibold">{{ $name }}</h3>
                        <p class="text-ink-600">{{ $role }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <x-site.cta title="Faça parte dessa história" text="Abra sua conta e ajude a construir o banco que você sempre quis ter." />
@endsection
