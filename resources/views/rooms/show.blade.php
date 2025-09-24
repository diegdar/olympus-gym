<x-layouts.app>
    @section('title', 'Ver sala')
    <h1 class="mb-3 text-2xl font-bold mt-2 mx-2 text-2nd">sala: {{ $room->name }}</h1>
    <!-- descripcion -->
    <article class="flex flex-wrap gap-2n mt-5 mx-3">
        <div class="flex flex-col">
            <p name="description"
                class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2"
                id="description">{{ $room->description }}</p>
        </div>
    </article>
</x-layouts.app>
