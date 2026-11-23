<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <title>@yield('title', 'Bankboost | Abra sua conta digital agora!')</title>
    @vite('resources/js/landing/main.ts')
</head>

<body>
    @include('landing.partials.header')

    @yield('content')

    @include('landing.partials.footer')
</body>

</html>
