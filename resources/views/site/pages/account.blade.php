@extends('layouts.site')

@section('title', 'Conta digital sem tarifa | Bankboost')
@section('description', 'Conta digital Bankboost: Pix e transferências ilimitadas, boletos, cofrinhos e saldo que rende todo dia, sem tarifa de manutenção.')

@php
    $benefits = [
        ['qr-code', 'Pix ilimitado', 'Envie e receba quantos Pix quiser, a qualquer hora, inclusive no fim de semana.'],
        ['arrows-left-right', 'Transferências grátis', 'TED para qualquer banco sem custo e transferência entre contas Bankboost na hora.'],
        ['barcode', 'Pague boletos pelo app', 'Aponte a câmera, confira os dados e pronto. Dá para agendar também.'],
        ['piggy-bank', 'Cofrinhos', 'Separe o dinheiro por objetivo, como viagem, reserva ou presente, e veja cada um render.'],
        ['bell-ringing', 'Notificação em tempo real', 'Cada movimentação chega no seu celular no mesmo segundo.'],
        ['shield-check', 'Segurança de banco grande', 'Biometria, dispositivo autorizado e limites que você mesmo ajusta.'],
    ];
    $steps = [
        ['Baixe o app', 'Disponível para Android e iPhone, ou comece por aqui mesmo, no site.'],
        ['Envie seus dados', 'Nome, CPF e e-mail. Sem papelada e sem comprovante de renda.'],
        ['Tire uma selfie', 'A gente confirma que é você mesmo, em segundos.'],
        ['Pronto!', 'Conta aberta, chave Pix criada e cartão virtual liberado na hora.'],
    ];
    $pix = [
        'Até 5 chaves: CPF, e-mail, celular ou chave aleatória',
        'Pix agendado e Pix por aproximação',
        'Comprovante com autenticação para compartilhar',
        'Devolução facilitada pelo app em caso de golpe (MED)',
    ];
    $fees = [
        ['Manutenção mensal da conta', 'Grátis', 'R$ 34,90'],
        ['Pix (envio e recebimento)', 'Grátis', 'Grátis'],
        ['TED para outros bancos', 'Grátis', 'R$ 11,50'],
        ['Saque na rede 24 horas', '4 grátis por mês', 'R$ 6,90'],
        ['Emissão de boleto de depósito', 'Grátis', 'R$ 3,50'],
        ['Segunda via do cartão', 'Grátis', 'R$ 15,00'],
    ];
    $faq = [
        ['question' => 'A conta é realmente gratuita?', 'answer' => 'Sim. Não existe tarifa de manutenção, nem pacote de serviços obrigatório. O que tem custo fica escrito na tabela de tarifas, antes de você usar.'],
        ['question' => 'Preciso comprovar renda para abrir a conta?', 'answer' => 'Não. Para a conta digital basta ter CPF, ser maior de 18 anos e um celular com câmera.'],
        ['question' => 'O dinheiro da conta rende?', 'answer' => 'Rende. O saldo é aplicado automaticamente no Boost Mais, que paga 100% do CDI com liquidez diária.'],
        ['question' => 'Meu dinheiro está protegido?', 'answer' => 'Os valores em conta e em CDB contam com a garantia do FGC até R$ 250 mil por CPF. (Lembrando: o Bankboost é fictício, então aqui é só demonstração.)'],
    ];
@endphp

@section('content')
    <x-site.page-hero eyebrow="Conta digital" title="Uma conta sem tarifa que trabalha por você" text="Pix e transferências ilimitadas, pagamento de boletos e um saldo que rende todo dia. Tudo no app, sem fila e sem letras miúdas." art="phone-account">
        <x-site.button :href="route('register')" size="lg" icon="arrow-right">Abrir conta grátis</x-site.button>
        <x-site.button href="#tarifas" variant="outline-light" size="lg">Ver tarifas</x-site.button>
    </x-site.page-hero>

    {{-- Benefícios --}}
    <section class="section">
        <div class="container-site">
            <x-site.heading align="center" eyebrow="Por que Bankboost" title="Tudo o que você precisa no dia a dia" text="Os serviços que você mais usa, sem custo e sem complicação." />
            <div class="mt-16 grid gap-x-10 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($benefits as $i => [$icon, $title, $text])
                    <x-site.feature :icon="$icon" :title="$title" data-aos="fade-up" :data-aos-delay="($i % 3) * 80">{{ $text }}</x-site.feature>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Como abrir --}}
    <section class="section bg-ink-100">
        <div class="container-site">
            <x-site.heading eyebrow="Passo a passo" title="Sua conta aberta em quatro passos" text="Leva uns cinco minutos. Dá tempo de abrir na fila do pão." />
            <ol class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach ($steps as $i => [$title, $text])
                    <li class="relative rounded-3xl bg-white p-7 ring-1 ring-ink-200" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        <span class="font-title text-5xl font-bold text-lilac-200">0{{ $i + 1 }}</span>
                        <h3 class="mt-4 text-lg font-semibold">{{ $title }}</h3>
                        <p class="mt-2 text-ink-600">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Pix --}}
    <section class="section">
        <div class="container-site grid items-center gap-16 lg:grid-cols-2">
            <div class="relative" data-aos="fade-right">
                <div class="rounded-[2rem] bg-linear-to-br from-brand-10 to-brand-30 p-8 text-white sm:p-10">
                    <p class="text-sm text-white/70">Pix para</p>
                    <p class="font-title text-2xl font-semibold">Bruno Lima</p>
                    <p class="text-sm text-white/70">CPF ***.982.247-** · Bankboost</p>
                    <p class="mt-8 text-sm text-white/70">Valor</p>
                    <p class="font-title text-5xl font-bold">R$ 45,90</p>
                    <div class="mt-8 flex items-center gap-3 rounded-2xl bg-white/10 p-4 text-sm">
                        @svg('phosphor-chat-text-duotone', 'size-6 text-accent')
                        Pizza de sexta 🍕
                    </div>
                    <span class="mt-8 flex items-center justify-center gap-2 rounded-full bg-accent py-3.5 font-title font-semibold text-brand-10">@svg('phosphor-paper-plane-tilt-fill', 'size-5') Enviar Pix</span>
                </div>
                <div class="animate-float absolute -right-4 -bottom-6 flex items-center gap-2 rounded-2xl bg-white p-3 text-sm font-semibold shadow-xl ring-1 ring-ink-200">
                    @svg('phosphor-lightning-duotone', 'size-6 text-brand-40') Cai na hora, 24/7
                </div>
            </div>
            <div data-aos="fade-left">
                <x-site.heading eyebrow="Pix" title="O Pix do jeito que deveria ser" text="Confira o nome de quem vai receber antes de confirmar, guarde os contatos frequentes e compartilhe o comprovante em um toque." />
                <ul class="mt-8 space-y-4">
                    @foreach ($pix as $item)
                        <li class="flex gap-3 text-ink-700">@svg('phosphor-check-circle-duotone', 'size-6 shrink-0 text-brand-40') {{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- Tarifas --}}
    <section id="tarifas" class="section bg-ink-100">
        <div class="container-site">
            <x-site.heading align="center" eyebrow="Tarifas" title="Compare e faça as contas" text="Valores de bancos tradicionais são uma média ilustrativa, só para comparação." />
            <div class="mx-auto mt-14 max-w-4xl overflow-hidden rounded-3xl bg-white ring-1 ring-ink-200" data-aos="fade-up">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-ink-100/60 text-sm">
                            <th class="px-6 py-4 font-semibold text-ink-600">Serviço</th>
                            <th class="px-6 py-4 font-title font-semibold text-brand-40">Bankboost</th>
                            <th class="px-6 py-4 font-semibold text-ink-600">Banco tradicional</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-200">
                        @foreach ($fees as [$service, $ours, $theirs])
                            <tr>
                                <td class="px-6 py-4 text-ink-800">{{ $service }}</td>
                                <td class="px-6 py-4 font-semibold text-emerald-700">{{ $ours }}</td>
                                <td class="px-6 py-4 text-ink-500">{{ $theirs }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="section">
        <div class="container-site grid gap-12 lg:grid-cols-[1fr_1.4fr]">
            <x-site.heading eyebrow="Dúvidas" title="Perguntas frequentes sobre a conta">
            </x-site.heading>
            <x-site.faq :items="$faq" />
        </div>
    </section>

    <x-site.cta />
@endsection
