@extends('layouts.site')

@section('title', $post->title.' | Blog Bankboost')
@section('description', $post->excerpt)

@section('content')
    <article>
        <header class="hero-bg pt-32 pb-40 lg:pt-40">
            <div class="pointer-events-none absolute -top-40 -right-40 size-[36rem] rounded-full bg-brand-30/40 blur-3xl"></div>
            <div class="container-site relative max-w-6xl">
                <nav class="flex items-center gap-2 text-sm text-white/70" aria-label="Trilha">
                    <a href="{{ route('site.blog.index') }}" class="hover:text-white">Blog</a>
                    @svg('phosphor-caret-right', 'size-3.5')
                    <a href="{{ route('site.blog.index', ['categoria' => $post->category->value]) }}" class="hover:text-white">{{ $post->category_meta['label'] }}</a>
                </nav>
                <h1 class="mt-6 max-w-4xl text-4xl leading-tight font-bold text-white sm:text-5xl">{{ $post->title }}</h1>
                <p class="mt-5 max-w-3xl text-xl text-white/80">{{ $post->excerpt }}</p>
                <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-white/70">
                    <span class="flex items-center gap-2">
                        <span class="flex size-9 items-center justify-center rounded-full bg-accent font-title font-bold text-brand-10">{{ mb_substr($post->author_name, 0, 1) }}</span>
                        {{ $post->author_name }}
                    </span>
                    <span class="flex items-center gap-1.5">@svg('phosphor-calendar-blank', 'size-4') {{ $post->published_label }}</span>
                    <span class="flex items-center gap-1.5">@svg('phosphor-clock', 'size-4') {{ $post->reading_minutes }} min de leitura</span>
                </div>
            </div>
        </header>

        <div class="container-site relative -mt-28 max-w-6xl">
            <x-site.post-cover :post="$post" large wide class="rounded-[2rem] shadow-2xl shadow-brand-10/20" />
        </div>

        <div class="container-site grid max-w-6xl gap-12 py-16 lg:grid-cols-[minmax(0,1fr)_16rem]">
            <div class="prose-bank">
                {{ $post->bodyHtml() }}
            </div>

            <aside class="space-y-6 lg:sticky lg:top-8 lg:self-start">
                <div class="rounded-3xl bg-ink-100 p-6">
                    <p class="font-title font-semibold">Compartilhe</p>
                    <div class="mt-4 flex gap-2">
                        @foreach (['whatsapp-logo' => 'https://wa.me/?text=', 'linkedin-logo' => 'https://www.linkedin.com/sharing/share-offsite/?url=', 'x-logo' => 'https://x.com/intent/post?url='] as $icon => $base)
                            <a href="{{ $base.urlencode(route('site.blog.show', $post)) }}" target="_blank" rel="noopener" class="flex size-10 items-center justify-center rounded-full bg-white text-ink-700 ring-1 ring-ink-200 hover:text-brand-40" aria-label="Compartilhar">
                                @svg('phosphor-'.$icon, 'size-5')
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="rounded-3xl bg-brand-10 p-6 text-white">
                    <p class="font-title text-lg font-semibold">Coloque em prática</p>
                    <p class="mt-2 text-sm text-white/75">Abra sua conta grátis e comece hoje mesmo.</p>
                    <x-site.button :href="route('register')" class="mt-5 w-full">Abrir conta</x-site.button>
                </div>
            </aside>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="section bg-ink-100">
            <div class="container-site">
                <x-site.heading eyebrow="Continue lendo" title="Você também pode gostar" />
                <div class="mt-12 grid gap-8 md:grid-cols-3">
                    @foreach ($related as $item)
                        @include('site.partials.post-card', ['post' => $item])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
