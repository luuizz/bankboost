@extends('layouts.site')

@section('title', ($activeCategory ? $activeCategory->label().' | ' : '').'Blog #focanodinheiro | Bankboost')
@section('description', 'Dicas de finanças pessoais, investimentos, segurança e empreendedorismo do blog Bankboost.')

@section('content')
    <section class="hero-bg pt-32 pb-16 lg:pt-40">
        <div class="pointer-events-none absolute -top-40 -right-40 size-[36rem] rounded-full bg-brand-30/40 blur-3xl"></div>
        <div class="container-site relative">
            <p class="font-title text-sm font-semibold tracking-wide text-accent uppercase">Blog #focanodinheiro</p>
            <h1 class="mt-4 max-w-3xl text-4xl leading-tight font-bold text-white sm:text-5xl">{{ $activeCategory?->label() ?? 'Conteúdo para cuidar bem do seu dinheiro' }}</h1>

            <form method="GET" action="{{ route('site.blog.index') }}" class="mt-8 flex max-w-xl gap-2" role="search">
                @if ($activeCategory)
                    <input type="hidden" name="categoria" value="{{ $activeCategory->value }}">
                @endif
                <label class="relative flex-1">
                    <span class="sr-only">Buscar no blog</span>
                    @svg('phosphor-magnifying-glass', 'pointer-events-none absolute top-1/2 left-5 size-5 -translate-y-1/2 text-ink-400')
                    <input type="search" name="busca" value="{{ $search }}" placeholder="Buscar artigos" class="w-full rounded-full bg-white py-3.5 pr-5 pl-13 text-ink-900 outline-none placeholder:text-ink-400 focus:ring-4 focus:ring-accent/50">
                </label>
                <button type="submit" class="rounded-full bg-accent px-6 font-title text-sm font-semibold text-brand-10 hover:bg-[#f5d220]">Buscar</button>
            </form>

            <nav class="mt-8 flex flex-wrap gap-2" aria-label="Categorias">
                <a href="{{ route('site.blog.index') }}" class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeCategory ? 'bg-white/10 text-white hover:bg-white/20' : 'bg-white text-brand-10' }}">Todos</a>
                @foreach ($categories as $category)
                    <a href="{{ route('site.blog.index', ['categoria' => $category->value]) }}" class="flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeCategory === $category ? 'bg-white text-brand-10' : 'bg-white/10 text-white hover:bg-white/20' }}">
                        @svg('phosphor-'.$category->icon(), 'size-4')
                        {{ $category->label() }}
                    </a>
                @endforeach
            </nav>
        </div>
    </section>

    @if ($featured)
        <section class="pt-16">
            <div class="container-site">
                <a href="{{ route('site.blog.show', $featured) }}" class="group grid overflow-hidden rounded-[2rem] bg-ink-100 ring-1 ring-ink-200 lg:grid-cols-2" data-aos="fade-up">
                    <div class="overflow-hidden">
                        <x-site.post-cover :post="$featured" large class="h-full transition duration-500 group-hover:scale-105 lg:aspect-auto" />
                    </div>
                    <div class="flex flex-col justify-center p-8 sm:p-12">
                        <span class="flex items-center gap-3 text-sm">
                            <span class="rounded-full bg-accent px-3 py-1 font-bold text-brand-10">Destaque</span>
                            <span class="font-semibold text-brand-40">{{ $featured->category_meta['label'] }}</span>
                        </span>
                        <h2 class="mt-5 text-3xl leading-tight font-bold group-hover:text-brand-40 sm:text-4xl">{{ $featured->title }}</h2>
                        <p class="mt-4 text-lg text-ink-600">{{ $featured->excerpt }}</p>
                        <p class="mt-6 text-sm text-ink-500">{{ $featured->published_label }} · {{ $featured->reading_minutes }} min de leitura</p>
                    </div>
                </a>
            </div>
        </section>
    @endif

    <section class="section pt-16">
        <div class="container-site">
            @if ($search !== '')
                <p class="mb-8 text-ink-600">{{ $posts->total() }} {{ $posts->total() === 1 ? 'resultado' : 'resultados' }} para “<strong class="text-ink-900">{{ $search }}</strong>”</p>
            @endif

            @if ($posts->isEmpty())
                <div class="rounded-3xl bg-ink-100 p-12 text-center">
                    @svg('phosphor-newspaper-duotone', 'mx-auto size-12 text-brand-40')
                    <p class="mt-4 text-lg font-semibold">Nenhum artigo encontrado</p>
                    <p class="mt-1 text-ink-600">Tente outra busca ou volte para todos os artigos.</p>
                </div>
            @else
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        @include('site.partials.post-card', ['post' => $post])
                    @endforeach
                </div>
            @endif

            @if ($posts->hasPages())
                <nav class="mt-14 flex items-center justify-between gap-4" aria-label="Paginação">
                    @if ($posts->previousPageUrl())
                        <x-site.button :href="$posts->previousPageUrl()" variant="outline">Anteriores</x-site.button>
                    @else
                        <span></span>
                    @endif
                    <span class="text-sm text-ink-500">Página {{ $posts->currentPage() }} de {{ $posts->lastPage() }}</span>
                    @if ($posts->nextPageUrl())
                        <x-site.button :href="$posts->nextPageUrl()" variant="outline" icon="arrow-right">Mais artigos</x-site.button>
                    @else
                        <span></span>
                    @endif
                </nav>
            @endif
        </div>
    </section>

    <section class="section pt-0">
        <div class="container-site">
            <div class="grid items-center gap-8 rounded-[2rem] bg-lilac-50 p-8 sm:p-12 lg:grid-cols-[auto_1fr_auto]">
                <x-site.icon-tile icon="envelope-simple" size="lg" tone="white" />
                <div>
                    <h2 class="text-2xl font-bold">Uma dica de dinheiro por semana</h2>
                    <p class="mt-2 text-ink-600">A newsletter do Bankboost chega toda segunda, leva 3 minutos para ler e não tem spam. (Fictícia, então ninguém vai te escrever de verdade.)</p>
                </div>
                <x-site.button :href="route('register')" variant="brand">Quero receber</x-site.button>
            </div>
        </div>
    </section>
@endsection
