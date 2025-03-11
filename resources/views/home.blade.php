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
<x-navbar>    
</x-navbar>
<main>
    <section >
        <h1 class="text-4xl font-bold text-center">Bienvenido a nuestro Gimnasio</h1>
        <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Unde fugiat cum beatae harum corporis vitae, aspernatur perferendis doloremque id optio aperiam reiciendis, quae illum sequi ad expedita adipisci aliquam quia?
            Magni laboriosam tenetur illum voluptates. Et modi nobis placeat dignissimos doloribus consequatur mollitia fugit impedit quas quam omnis explicabo possimus eveniet nulla asperiores ab quisquam, laboriosam nam ducimus dolore dolorem?
            Consequatur possimus illum sed fugiat nihil fuga porro! Fugit porro, dolore voluptatibus eveniet adipisci soluta tenetur vitae quibusdam laboriosam harum praesentium tempore alias ullam dignissimos. Voluptates saepe quas autem cupiditate!
            Id, quo. Nam, officiis ut nihil magnam dolorem delectus. Sint expedita natus est, magni rem unde quaerat quibusdam ex. Veritatis voluptatem ad temporibus natus aperiam nostrum minima rerum aliquam error?
            Dolores quo id minus, non magni, veniam ex a fugiat repellendus quaerat itaque, sint recusandae nisi ut esse pariatur vel! Aut vero nulla, voluptatem placeat sit labore minima voluptatum ullam.
        </p>
    </section>
</main>

    <footer class="text-center py-4 bg-gray-500 text-white dark:bg-bg_darkMode dark:text-white">
        <p>&copy; 2024 Gimnasio. Todos los derechos reservados.</p>
    </footer>

</body>
@vite('resources/js/navbar.js')


</html>
