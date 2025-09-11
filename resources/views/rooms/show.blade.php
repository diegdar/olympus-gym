<x-layouts.app>
    @section('title', 'Ver sala')
    <h1>sala: {{ $room->name }}</h1>
    <!-- descripcion -->
    <article class="flex flex-wrap gap-2n mt-5 mx-3">
        <div class="flex flex-col">
            <p name="description"
                class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2"
                id="description">{{ $room->description }}</p>
        </div>
    </article>
</x-layouts.app>
