@extends('layouts.site')

@php
    $documents = [
        'terms' => [
            'title' => 'Termos de uso',
            'intro' => 'Regras para usar o site, o app e os produtos do Bankboost.',
            'sections' => [
                ['Sobre este documento', 'O Bankboost é um projeto fictício, criado para portfólio. Estes termos existem para demonstrar a estrutura de um site institucional e não criam obrigações reais entre ninguém.'],
                ['Cadastro e conta', 'Para abrir uma conta é preciso ter 18 anos ou mais, CPF válido e informar dados verdadeiros. Você é responsável por manter sua senha em sigilo e por todas as operações feitas com ela.'],
                ['Uso dos serviços', 'Os serviços devem ser usados de forma lícita. Podemos bloquear operações suspeitas de fraude ou que contrariem a legislação, avisando você pelos canais oficiais.'],
                ['Tarifas e limites', 'As tarifas e os limites de cada produto ficam publicados no site e no app. Qualquer mudança é comunicada com, no mínimo, 30 dias de antecedência.'],
                ['Encerramento', 'Você pode encerrar sua conta a qualquer momento pelo app, desde que não existam saldos devedores ou operações pendentes.'],
                ['Atualizações', 'Estes termos podem ser atualizados. A versão vigente fica sempre nesta página, com a data da última alteração.'],
            ],
        ],
        'privacy' => [
            'title' => 'Política de privacidade',
            'intro' => 'Como tratamos seus dados pessoais, em linha com a LGPD.',
            'sections' => [
                ['Sobre este documento', 'O Bankboost é um projeto fictício. Os dados cadastrados neste site de demonstração ficam apenas no banco de dados do projeto e não são compartilhados com ninguém.'],
                ['Quais dados coletamos', 'Dados de cadastro (nome, CPF, e-mail), dados das operações que você faz na conta e dados técnicos de acesso, como IP e dispositivo, para segurança.'],
                ['Para que usamos', 'Para abrir e manter sua conta, prevenir fraudes, cumprir obrigações legais e melhorar os produtos. Não vendemos dados pessoais.'],
                ['Cookies', 'Usamos apenas cookies essenciais, necessários para manter sua sessão e proteger os formulários contra ataques.'],
                ['Seus direitos', 'Você pode pedir acesso, correção, portabilidade ou exclusão dos seus dados, respeitados os prazos legais de guarda, pelo e-mail privacidade@bankboost.com.br.'],
                ['Segurança', 'Senhas são guardadas com criptografia de mão única, as conexões usam HTTPS e o acesso aos dados é restrito a quem precisa deles para trabalhar.'],
            ],
        ],
    ];
    $document = $documents[$page];
@endphp

@section('title', $document['title'].' | Bankboost')
@section('description', $document['intro'])

@section('content')
    <section class="hero-bg pt-32 pb-16 lg:pt-40">
        <div class="container-site relative">
            <p class="font-title text-sm font-semibold tracking-wide text-accent uppercase">Institucional</p>
            <h1 class="mt-4 text-4xl font-bold text-white sm:text-5xl">{{ $document['title'] }}</h1>
            <p class="mt-4 text-lg text-white/80">{{ $document['intro'] }} Última atualização: 23 de novembro de 2026.</p>
        </div>
    </section>

    <section class="section">
        <div class="container-site grid gap-12 lg:grid-cols-[16rem_1fr]">
            <nav class="lg:sticky lg:top-8 lg:self-start" aria-label="Índice">
                <p class="font-title text-sm font-semibold text-ink-500 uppercase">Nesta página</p>
                <ol class="mt-4 space-y-2 text-sm">
                    @foreach ($document['sections'] as $i => [$heading])
                        <li><a href="#secao-{{ $i + 1 }}" class="text-ink-600 hover:text-brand-40">{{ $i + 1 }}. {{ $heading }}</a></li>
                    @endforeach
                </ol>
            </nav>
            <div class="max-w-3xl space-y-10">
                @foreach ($document['sections'] as $i => [$heading, $text])
                    <div id="secao-{{ $i + 1 }}" class="scroll-mt-10">
                        <h2 class="text-2xl font-bold">{{ $i + 1 }}. {{ $heading }}</h2>
                        <p class="mt-3 text-lg leading-relaxed text-ink-700">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
