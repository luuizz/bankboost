@extends('layouts.site')

@section('title', 'Central de ajuda | Bankboost')
@section('description', 'Tire suas dúvidas sobre conta digital, Pix, cartão, investimentos e conta PJ do Bankboost, ou fale com o nosso time.')

@php
    $channels = [
        ['chat-circle-dots', 'Chat no app', 'Atendimento humano 24 horas, todos os dias. Tempo médio de resposta: menos de 1 minuto.', 'Abrir o app'],
        ['envelope-simple', 'E-mail', 'oi@bankboost.com.br. Respondemos em até 1 dia útil.', 'Enviar e-mail'],
        ['phone', 'Ouvidoria', '0800 000 0000, de segunda a sexta, das 8h às 18h, para casos que não foram resolvidos.', 'Ligar'],
    ];
@endphp

@section('content')
    <x-site.page-hero eyebrow="Central de ajuda" title="Como a gente pode te ajudar hoje?" text="Procure sua dúvida abaixo. Se não encontrar, nosso time responde rapidinho pelo chat ou pelo formulário." art="help">
        <label class="relative block w-full max-w-xl">
            <span class="sr-only">Buscar nas perguntas frequentes</span>
            @svg('phosphor-magnifying-glass', 'pointer-events-none absolute top-1/2 left-5 size-5 -translate-y-1/2 text-ink-400')
            <input type="search" placeholder="Ex.: limite do Pix, cartão virtual, MEI…" class="w-full rounded-full bg-white py-4 pr-5 pl-13 text-ink-900 shadow-xl outline-none placeholder:text-ink-400 focus:ring-4 focus:ring-accent/50" data-faq-search>
        </label>
    </x-site.page-hero>

    {{-- Categorias --}}
    <section class="section pb-0">
        <div class="container-site">
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                @foreach ($groups as $group)
                    <a href="#{{ $group['category']->value }}" class="flex flex-col items-center gap-3 rounded-3xl p-5 text-center ring-1 ring-ink-200 transition hover:-translate-y-1 hover:ring-brand-30">
                        <x-site.icon-tile :icon="$group['category']->icon()" />
                        <span class="font-title text-sm font-semibold">{{ $group['category']->label() }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Perguntas --}}
    <section class="section">
        <div class="container-site max-w-4xl space-y-14">
            @foreach ($groups as $group)
                <div id="{{ $group['category']->value }}" class="scroll-mt-10" data-faq-group>
                    <h2 class="flex items-center gap-3 text-2xl font-bold">
                        @svg('phosphor-'.$group['category']->icon().'-duotone', 'size-7 text-brand-40')
                        {{ $group['category']->label() }}
                    </h2>
                    <x-site.faq :items="$group['items']" class="mt-6" />
                </div>
            @endforeach
            <p class="rounded-3xl bg-ink-100 p-8 text-center text-ink-600" data-faq-empty hidden>
                Não encontramos nada com esse termo. Tente outras palavras ou fale com a gente logo abaixo.
            </p>
        </div>
    </section>

    {{-- Canais --}}
    <section class="section bg-ink-100">
        <div class="container-site">
            <x-site.heading align="center" eyebrow="Canais de atendimento" title="Prefere falar com alguém?" />
            <div class="mt-14 grid gap-6 md:grid-cols-3">
                @foreach ($channels as $i => [$icon, $title, $text, $action])
                    <div class="flex flex-col rounded-3xl bg-white p-8 ring-1 ring-ink-200" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        <x-site.icon-tile :icon="$icon" />
                        <h3 class="mt-6 text-xl font-semibold">{{ $title }}</h3>
                        <p class="mt-2 flex-1 text-ink-600">{{ $text }}</p>
                        <span class="mt-6 font-title text-sm font-semibold text-brand-40">{{ $action }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Formulário --}}
    <section id="contato" class="section scroll-mt-10">
        <div class="container-site grid gap-14 lg:grid-cols-[1fr_1.3fr]">
            <div>
                <x-site.heading eyebrow="Fale conosco" title="Mande sua mensagem" text="Para assuntos da sua conta, prefira o chat do app: lá a gente já sabe quem você é. Nunca peça nem envie senha por aqui." />
                <div class="mt-8 flex items-start gap-3 rounded-2xl bg-accent/15 p-5 text-sm text-ink-700">
                    @svg('phosphor-shield-warning-duotone', 'size-6 shrink-0 text-[#8a6d00]')
                    O Bankboost nunca liga pedindo senha, código de verificação ou transferência para “conta de segurança”.
                </div>
            </div>

            <form method="POST" action="{{ route('site.help.contact') }}" class="rounded-[2rem] bg-white p-7 shadow-xl shadow-brand-10/5 ring-1 ring-ink-200 sm:p-9">
                @csrf

                @if (session('contact_sent'))
                    <div class="mb-6 flex items-center gap-3 rounded-2xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800" role="status">
                        @svg('phosphor-check-circle-fill', 'size-6 shrink-0')
                        {{ session('contact_sent') }}
                    </div>
                @endif

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-semibold">Nome</label>
                        <input id="name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name" class="field">
                        @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold">E-mail</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="field">
                        @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="subject" class="mb-1.5 block text-sm font-semibold">Assunto</label>
                        <select id="subject" name="subject" required class="field">
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->value }}" @selected(old('subject') === $subject->value)>{{ $subject->label() }}</option>
                            @endforeach
                        </select>
                        @error('subject') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="message" class="mb-1.5 block text-sm font-semibold">Mensagem</label>
                        <textarea id="message" name="message" rows="5" required minlength="10" maxlength="2000" class="field">{{ old('message') }}</textarea>
                        @error('message') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="hidden" aria-hidden="true">
                        <label for="website">Não preencha</label>
                        <input id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>
                </div>
                <button type="submit" class="mt-7 inline-flex w-full items-center justify-center gap-2 rounded-full bg-brand-40 px-6 py-3.5 font-title font-semibold text-white transition hover:bg-brand-20">
                    Enviar mensagem @svg('phosphor-paper-plane-tilt-fill', 'size-5')
                </button>
            </form>
        </div>
    </section>
@endsection
