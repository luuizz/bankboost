@php
    $products = [
        ['route' => 'site.account', 'icon' => 'device-mobile', 'title' => 'Conta digital', 'text' => 'Sem tarifa, com Pix ilimitado'],
        ['route' => 'site.card', 'icon' => 'credit-card', 'title' => 'Cartão de crédito', 'text' => 'Zero anuidade e cashback'],
        ['route' => 'site.investments', 'icon' => 'chart-line-up', 'title' => 'Investimentos', 'text' => 'Boost Mais rende todo dia'],
        ['route' => 'site.loans', 'icon' => 'hand-coins', 'title' => 'Empréstimo', 'text' => 'Crédito pessoal sem burocracia'],
    ];
    $links = [
        ['route' => 'site.business', 'label' => 'Conta PJ'],
        ['route' => 'site.about', 'label' => 'Quem somos'],
        ['route' => 'site.blog.*', 'href' => route('site.blog.index'), 'label' => 'Blog'],
        ['route' => 'site.help', 'label' => 'Ajuda'],
    ];
    $productActive = request()->routeIs('site.account', 'site.card', 'site.investments', 'site.loans');
@endphp

<header class="absolute inset-x-0 top-0 z-40 text-white" data-header>
    <div class="container-site flex items-center justify-between gap-6 border-b border-white/10 py-5">
        <a href="{{ route('home') }}" class="shrink-0" aria-label="Bankboost, página inicial">
            <img src="{{ asset('logo-white.svg') }}" alt="Bankboost" class="h-7 w-auto">
        </a>

        <nav class="hidden items-center gap-1 lg:flex" aria-label="Principal">
            <div class="group relative">
                <button type="button" class="flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-medium transition hover:bg-white/10 {{ $productActive ? 'bg-white/10' : '' }}" aria-haspopup="true">
                    Produtos
                    @svg('phosphor-caret-down-bold', 'size-3.5 transition group-hover:rotate-180 group-focus-within:rotate-180')
                </button>
                <div class="invisible absolute top-full left-1/2 w-[34rem] -translate-x-1/2 pt-3 opacity-0 transition group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                    <div class="grid grid-cols-2 gap-1 rounded-3xl bg-white p-3 text-ink-900 shadow-2xl shadow-brand-10/20">
                        @foreach ($products as $product)
                            <a href="{{ route($product['route']) }}" class="flex gap-3 rounded-2xl p-3 transition hover:bg-lilac-50">
                                <x-site.icon-tile :icon="$product['icon']" size="sm" />
                                <span>
                                    <span class="block font-title text-sm font-semibold">{{ $product['title'] }}</span>
                                    <span class="block text-sm text-ink-600">{{ $product['text'] }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            @foreach ($links as $link)
                <a href="{{ $link['href'] ?? route($link['route']) }}" class="rounded-full px-4 py-2 text-sm font-medium transition hover:bg-white/10 {{ request()->routeIs($link['route']) ? 'bg-white/10' : '' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-3 lg:flex">
            <a href="{{ route('login') }}" class="flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold hover:bg-white/10">
                @svg('phosphor-sign-in', 'size-5')
                Entrar
            </a>
            <x-site.button :href="route('register')" variant="outline-light">Abra sua conta</x-site.button>
        </div>

        <button type="button" class="flex size-11 items-center justify-center rounded-full hover:bg-white/10 lg:hidden" aria-label="Abrir menu" aria-expanded="false" aria-controls="menu-mobile" data-menu-toggle>
            @svg('phosphor-list', 'size-7')
        </button>
    </div>

    <div id="menu-mobile" class="container-site hidden pb-6 lg:hidden" data-menu>
        <nav class="rounded-3xl bg-white p-4 text-ink-900 shadow-2xl" aria-label="Menu">
            <p class="px-3 pt-2 pb-1 text-xs font-semibold tracking-wide text-ink-500 uppercase">Produtos</p>
            @foreach ($products as $product)
                <a href="{{ route($product['route']) }}" class="flex items-center gap-3 rounded-2xl px-3 py-2.5 font-medium hover:bg-lilac-50">
                    @svg('phosphor-'.$product['icon'].'-duotone', 'size-5 text-brand-40')
                    {{ $product['title'] }}
                </a>
            @endforeach
            <hr class="my-3 border-ink-200">
            @foreach ($links as $link)
                <a href="{{ $link['href'] ?? route($link['route']) }}" class="block rounded-2xl px-3 py-2.5 font-medium hover:bg-lilac-50">{{ $link['label'] }}</a>
            @endforeach
            <div class="mt-4 grid grid-cols-2 gap-3">
                <x-site.button :href="route('login')" variant="outline">Entrar</x-site.button>
                <x-site.button :href="route('register')" variant="brand">Abrir conta</x-site.button>
            </div>
        </nav>
    </div>
</header>
