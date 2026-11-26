<article class="group flex flex-col overflow-hidden rounded-3xl bg-white ring-1 ring-ink-200 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-10/10">
    <a href="{{ route('site.blog.show', $post) }}" class="block overflow-hidden" tabindex="-1" aria-hidden="true">
        <x-site.post-cover :post="$post" class="transition duration-500 group-hover:scale-105" />
    </a>
    <div class="flex flex-1 flex-col p-6">
        <div class="flex items-center gap-3 text-sm">
            <span class="rounded-full bg-lilac-50 px-3 py-1 font-semibold text-brand-40">{{ $post->category_meta['label'] }}</span>
            <span class="text-ink-500">{{ $post->reading_minutes }} min de leitura</span>
        </div>
        <h3 class="mt-4 text-xl leading-snug font-semibold">
            <a href="{{ route('site.blog.show', $post) }}" class="hover:text-brand-40">{{ $post->title }}</a>
        </h3>
        <p class="mt-2 flex-1 text-ink-600">{{ $post->excerpt }}</p>
        <p class="mt-5 text-sm text-ink-500">{{ $post->published_label }}</p>
    </div>
</article>
