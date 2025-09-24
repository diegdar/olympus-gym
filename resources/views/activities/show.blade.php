<x-layouts.app>
    @section('title', 'Ver actividad')
    <h1 class="mb-3 text-2xl font-bold mt-2 mx-2 text-2nd">Actividad: {{ $activity->name }}</h1>
    <!-- duration -->
    <article class="flex flex-wrap gap-2n mt-5 mx-3">
        <div class="flex flex-col sm:flex-row">
            <span class="font-bold dark:text-red-300">
                Duración:</span>
            <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                {{ $activity->duration }} minutos</p>
        </div>
    </article>
    <!-- descripcion -->
    <article class="flex flex-wrap gap-2n mt-5 mx-3">
        <div class="flex flex-col sm:flex-row">
            <span class="font-bold dark:text-red-300">
                Descripción:</span>
            <p name="description" class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                {{ $activity->description }}</p>
        </div>
    </article>

</x-layouts.app>
