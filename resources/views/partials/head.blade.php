<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>@yield('title', $title ?? 'Olympus Gym:Diego Chacon')</title>

{{-- Favicon --}}
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('img/favicon/site.webmanifest') }}">


@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
