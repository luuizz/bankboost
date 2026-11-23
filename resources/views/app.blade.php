<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <title inertia>{{ config('app.name') }}</title>
    @vite('resources/js/app.ts')
    @inertiaHead
</head>

<body class="font-body antialiased">
    @inertia
</body>

</html>
