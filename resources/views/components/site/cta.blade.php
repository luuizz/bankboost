@props([
    'title' => 'Abra sua conta em poucos minutos',
    'text' => 'Sem tarifa de manutenção, sem anuidade e com tudo na palma da mão. Leva menos tempo que um café.',
])

<section class="section pt-0">
    <div class="container-site">
        <div class="relative overflow-hidden rounded-[2rem] bg-linear-to-br from-brand-10 to-brand-30 px-8 py-14 text-white sm:px-14 lg:flex lg:items-center lg:justify-between lg:gap-10" data-aos="fade-up">
            <div class="pointer-events-none absolute -right-20 -bottom-24 size-80 rounded-full bg-accent/25 blur-3xl"></div>
            <div class="relative max-w-xl">
                <h2 class="text-3xl leading-tight font-bold text-white sm:text-4xl">{{ $title }}</h2>
                <p class="mt-4 text-lg text-white/75">{{ $text }}</p>
            </div>
            <div class="relative mt-8 flex flex-wrap gap-4 lg:mt-0">
                <x-site.button :href="route('register')" icon="arrow-right" size="lg">Abrir minha conta</x-site.button>
                <x-site.button :href="route('site.help')" variant="outline-light" size="lg">Tirar dúvidas</x-site.button>
            </div>
        </div>
    </div>
</section>
