@extends('layouts.site')

@section('title', 'Carreiras | Trabalhe no Bankboost')
@section('description', 'Vagas abertas no Bankboost: trabalho remoto, benefícios flexíveis e um time que constrói o banco digital mais simples do Brasil.')

@php
    $perks = [
        ['house-line', 'Remoto de verdade', 'Trabalhe de onde quiser no Brasil, com auxílio para montar o home office.'],
        ['heartbeat', 'Saúde completa', 'Plano de saúde e odontológico sem coparticipação, extensivo a dependentes.'],
        ['bowl-food', 'Flexível de verdade', 'Cartão de benefícios para usar em alimentação, mobilidade, cultura ou saúde.'],
        ['graduation-cap', 'Aprender sempre', 'Verba anual para cursos, livros e eventos, e um dia por mês só para estudar.'],
        ['baby', 'Parentalidade', 'Licença de 6 meses para todas as pessoas que se tornarem mães ou pais.'],
        ['chart-line-up', 'Participação', 'Programa de participação nos lucros e opções de ações para todo o time.'],
    ];
    $process = [
        ['Inscrição', 'Você manda o currículo ou LinkedIn e conta por que quer trabalhar aqui.'],
        ['Conversa inicial', 'Um papo de 30 minutos com o time de pessoas, para nos conhecermos.'],
        ['Desafio prático', 'Um exercício curto e remunerado, parecido com o dia a dia da vaga.'],
        ['Entrevista com o time', 'Você conhece quem vai trabalhar com você e tira todas as dúvidas.'],
        ['Proposta', 'Retorno em até 5 dias úteis, independentemente do resultado.'],
    ];
@endphp

@section('content')
    <x-site.page-hero eyebrow="Carreiras" title="Venha construir o banco que a gente queria ter" text="Somos 280 pessoas trabalhando de forma remota para deixar a vida financeira de milhões de brasileiros mais simples. Tem lugar para você." art="careers">
        <x-site.button href="#vagas" size="lg" icon="arrow-down">Ver vagas abertas</x-site.button>
    </x-site.page-hero>

    {{-- Cultura --}}
    <section class="section">
        <div class="container-site grid items-center gap-14 lg:grid-cols-2">
            <x-site.heading eyebrow="Cultura" title="Autonomia, confiança e muita conversa" text="Times pequenos, donos dos seus produtos, que falam direto com clientes. Decisões documentadas, reuniões só quando precisam existir e feedback como hábito." />
            <div class="grid grid-cols-2 gap-6">
                <x-site.stat value="280" label="pessoas no time" />
                <x-site.stat value="26" label="estados representados" />
                <x-site.stat value="52%" label="de mulheres na liderança" />
                <x-site.stat value="4,7" label="de nota no eNPS interno" />
            </div>
        </div>
    </section>

    {{-- Benefícios --}}
    <section class="section bg-ink-100">
        <div class="container-site">
            <x-site.heading align="center" eyebrow="Benefícios" title="Cuidar de quem cuida dos clientes" />
            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($perks as $i => [$icon, $title, $text])
                    <div class="rounded-3xl bg-white p-7 ring-1 ring-ink-200" data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 80 }}">
                        <x-site.feature :icon="$icon" :title="$title">{{ $text }}</x-site.feature>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Vagas --}}
    <section id="vagas" class="section">
        <div class="container-site">
            <x-site.heading eyebrow="Vagas abertas" title="Encontre o seu lugar" text="Não achou a vaga ideal? Mande seu currículo para talentos@bankboost.com.br que a gente guarda com carinho." />

            @forelse ($jobs as $team => $teamJobs)
                <div class="mt-12">
                    <h3 class="flex items-center gap-3 text-xl font-semibold">
                        {{ $team }}
                        <span class="rounded-full bg-lilac-50 px-2.5 py-0.5 text-sm font-semibold text-brand-40">{{ count($teamJobs) }}</span>
                    </h3>
                    <div class="mt-5 divide-y divide-ink-200 rounded-3xl ring-1 ring-ink-200">
                        @foreach ($teamJobs as $job)
                            <details class="group p-6 [&_summary::-webkit-details-marker]:hidden">
                                <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-4">
                                    <span>
                                        <span class="block font-title text-lg font-semibold text-ink-900 group-hover:text-brand-40">{{ $job->title }}</span>
                                        <span class="mt-1 flex flex-wrap gap-x-5 gap-y-1 text-sm text-ink-500">
                                            <span class="flex items-center gap-1.5">@svg('phosphor-map-pin', 'size-4') {{ $job->location }}</span>
                                            <span class="flex items-center gap-1.5">@svg('phosphor-medal', 'size-4') {{ $job->seniority }}</span>
                                            <span class="flex items-center gap-1.5">@svg('phosphor-file-text', 'size-4') {{ $job->contract }}</span>
                                        </span>
                                    </span>
                                    <span class="flex items-center gap-2 font-title text-sm font-semibold text-brand-40">
                                        Detalhes @svg('phosphor-caret-down-bold', 'size-4 transition group-open:rotate-180')
                                    </span>
                                </summary>
                                <div class="mt-5 max-w-3xl space-y-4 text-ink-600">
                                    <p>{!! nl2br(e($job->description)) !!}</p>
                                    <x-site.button :href="'mailto:talentos@bankboost.com.br?subject='.rawurlencode('Vaga: '.$job->title)" variant="brand" icon="paper-plane-tilt">Quero me candidatar</x-site.button>
                                </div>
                            </details>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="mt-12 rounded-3xl bg-ink-100 p-8 text-ink-600">Nenhuma vaga aberta no momento. Volte em breve!</p>
            @endforelse
        </div>
    </section>

    {{-- Processo --}}
    <section class="section bg-linear-to-br from-brand-10 to-brand-20">
        <div class="container-site">
            <x-site.heading dark eyebrow="Processo seletivo" title="Transparente do começo ao fim" text="Você sabe em que etapa está e recebe retorno em todas elas." />
            <ol class="mt-14 grid gap-6 md:grid-cols-5">
                @foreach ($process as $i => [$title, $text])
                    <li class="rounded-3xl bg-white/5 p-6 ring-1 ring-white/10" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        <span class="flex size-10 items-center justify-center rounded-full bg-accent font-title font-bold text-brand-10">{{ $i + 1 }}</span>
                        <h3 class="mt-5 font-semibold text-white">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-white/70">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
@endsection
