<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Bankboost | Conta digital, cartão e investimentos')</title>
    <meta name="description" content="@yield('description', 'Bankboost é um banco 100% digital fictício: conta sem tarifa, cartão sem anuidade, Pix, investimentos e conta PJ.')">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Bankboost">
    <meta property="og:title" content="@yield('title', 'Bankboost')">
    <meta property="og:description" content="@yield('description', 'Banco 100% digital.')">
    <meta name="theme-color" content="#35189c">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script>document.documentElement.classList.replace('no-js', 'js')</script>
    @vite(['resources/css/site.css', 'resources/js/site/main.ts'])
</head>

<body>
    <a href="#conteudo" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-brand-40">Pular para o conteúdo</a>

    @include('site.partials.header')

    <main id="conteudo">
        @yield('content')
    </main>

    @include('site.partials.footer')
</body>

</html>
