<!DOCTYPE html>
<html lang="es" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto: Gimnasio</title>
    {{-- SEO --}}
    <meta name="description" content="Portafolio de Diego Chacón Delgado, desarrollador web">
    <meta name="keywords"
        content="portafolio, desarrollador web, backend, frondtend, css, html, php, c#, mysql, sqlserver, mongodb, laravel, postman, docker, phpmyadmin, Diego Chacón Delgado">
    {{-- Favicon --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('img/favicon/site.webmanifest') }}">
    {{-- Tailwind styles --}}
    @vite('resources/css/app.css')
    <title>{{ $title ?? 'Home' }}</title>
</head>

<body class="bg-white-100 text-black dark:bg-bg_darkMode dark:text-white">
    <!-- Navbar -->
    <x-navbar>
    </x-navbar>
    
    <!-- Main -->
    <main>
        {{ $content }}
    </main>

    <!-- Footer -->
    <footer class="text-center py-4 bg-gray-500 text-white dark:bg-bg_darkMode dark:text-white">
        <p>&copy; 2024 Gimnasio. Todos los derechos reservados.</p>
    </footer>

</body>
@vite('resources/js/navbar.js')


</html>