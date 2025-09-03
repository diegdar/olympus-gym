<x-layouts.app>
    <div class="p-6 space-y-6">
        <div>
            <h1 class="text-2xl font-bold mb-2">Estadísticas de Suscripciones</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-300">Porcentaje de usuarios activos por tipo de cuota.</p>
        </div>

        <div class="rounded border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
            <div id="subscription-percentages-table" data-endpoint="{{ route('admin.subscriptions.percentages') }}"></div>
            <div id="subscription-percentages-total" class="mt-4 text-sm text-zinc-700 dark:text-zinc-300"></div>
        </div>
    </div>

    @vite('resources/js/admin/subscriptions/stats.js')
</x-layouts.app>
