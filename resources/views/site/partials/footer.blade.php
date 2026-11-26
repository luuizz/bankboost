@php
    $columns = [
        'Produtos' => [
            ['Conta digital', route('site.account')],
            ['Cartão de crédito', route('site.card')],
            ['Investimentos', route('site.investments')],
            ['Empréstimo', route('site.loans')],
        ],
        'Para empresas' => [
            ['Conta PJ', route('site.business')],
            ['Plano MEI', route('site.business').'#planos'],
            ['Plano ME', route('site.business').'#planos'],
        ],
        'Institucional' => [
            ['Quem somos', route('site.about')],
            ['Carreiras', route('site.careers')],
            ['Blog', route('site.blog.index')],
            ['Termos de uso', route('site.terms')],
            ['Privacidade', route('site.privacy')],
        ],
        'Ajuda' => [
            ['Central de ajuda', route('site.help')],
            ['Fale conosco', route('site.help').'#contato'],
            ['Acessar conta', route('login')],
        ],
    ];
    $socials = ['youtube-logo' => 'YouTube', 'linkedin-logo' => 'LinkedIn', 'facebook-logo' => 'Facebook', 'instagram-logo' => 'Instagram', 'x-logo' => 'X'];
@endphp

<footer class="bg-brand-10 text-white/80">
    <div class="container-site">
        <div class="flex flex-col gap-6 border-b border-white/10 py-12 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('home') }}" aria-label="Bankboost, página inicial">
                <img src="{{ asset('logo-white.svg') }}" alt="Bankboost" class="h-8 w-auto">
            </a>
            <div class="flex items-center gap-5">
                <span class="text-sm">Acompanhe as redes</span>
                <ul class="flex gap-2">
                    @foreach ($socials as $icon => $label)
                        <li>
                            <a href="#" class="flex size-10 items-center justify-center rounded-full bg-white/5 text-white transition hover:bg-white/15" aria-label="{{ $label }}">
                                @svg('phosphor-'.$icon, 'size-5')
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="grid gap-10 py-12 sm:grid-cols-2 lg:grid-cols-[repeat(4,1fr)_1.4fr]">
            @foreach ($columns as $title => $links)
                <div>
                    <h2 class="font-title text-base font-semibold text-white">{{ $title }}</h2>
                    <ul class="mt-4 space-y-3 text-sm">
                        @foreach ($links as [$label, $href])
                            <li><a href="{{ $href }}" class="transition hover:text-white">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            <div class="space-y-3 text-sm">
                <div class="flex gap-3 rounded-2xl bg-white/5 p-4">
                    @svg('phosphor-envelope-simple', 'size-6 shrink-0 text-accent')
                    <p><strong class="block text-white">Atendimento 24 horas</strong>oi@bankboost.com.br</p>
                </div>
                <div class="flex gap-3 rounded-2xl bg-white/5 p-4">
                    @svg('phosphor-chat-circle-dots', 'size-6 shrink-0 text-accent')
                    <p><strong class="block text-white">Imprensa</strong>imprensa@bankboost.com.br</p>
                </div>
                <div class="flex gap-3 rounded-2xl bg-white/5 p-4">
                    @svg('phosphor-phone', 'size-6 shrink-0 text-accent')
                    <p><strong class="block text-white">Ouvidoria</strong>0800 000 0000 (dias úteis, 8h às 18h)</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-4 border-t border-white/10 py-8 text-sm sm:flex-row sm:items-start sm:justify-between">
            <p class="flex max-w-2xl gap-3">
                @svg('phosphor-smiley-wink-duotone', 'size-6 shrink-0 text-accent')
                <span>Oi! Leu até aqui? Você se preocupa com os mínimos detalhes, a gente também. O Bankboost é um projeto fictício de portfólio: nenhum dado aqui é real e nenhum dinheiro de verdade é movimentado.</span>
            </p>
            <p class="shrink-0">© {{ now()->year }} Bankboost</p>
        </div>
    </div>
    <div class="h-2 bg-accent"></div>
</footer>
