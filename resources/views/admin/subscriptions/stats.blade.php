<x-layouts.app>
    @section('title', 'Estadisticas suscripciones')
    <div class="p-6 space-y-6">
        <div>
            <h1 class="text-2xl font-bold mb-2 text-2nd">Estadísticas de Suscripciones</h1>
        </div>
        {{-- Users by monthly fee --}}
        <div class="rounded border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
            <div class="flex justify-between gap-2 mb-3 text-xs items-center flex-wrap">
                <h2 class="font-semibold text-center sm:text-left">Miembros activos por cuota</h2>

                <article class="ml-auto flex gap-2 text-xs items-center">
                    <strong>Descargar:</strong>
                    <a href="{{ route('admin.subscriptions.percentages.export.json') }}"
                        class="px-2 py-1 border rounded hover:bg-zinc-100 dark:hover:bg-zinc-800">JSON</a>
                    <a href="{{ route('admin.subscriptions.percentages.export.excel') }}"
                        class="px-2 py-1 border rounded hover:bg-zinc-100 dark:hover:bg-zinc-800">CSV</a>
                </article>
            </div>
            <div class="overflow-x-auto -mx-1 px-1">
                <div id="subscription-percentages-table" data-endpoint="{{ route('admin.subscriptions.percentages') }}"></div>
            </div>
            <div id="subscription-percentages-total" class="mt-4 text-sm text-zinc-700 dark:text-zinc-300"></div>
        </div>
        {{-- Membership Sign-Ups and Cancellations per Month --}}
        <div class="rounded border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900 space-y-4">
            <div class="flex flex-col sm:flex-row items-center justify-center sm:justify-between gap-3">    
                <div class="flex flex-wrap items-center justify-center">
                    <h2 class="font-semibold text-center">Altas/Bajas en el año:</h2>
                    <label class="text-sm flex items-center gap-2">
                        <select id="subscription-year-select"
                            class="border rounded px-2 py-1 bg-white dark:bg-zinc-800 text-sm"
                            data-endpoint="{{ route('admin.subscriptions.monthly-net') }}">
                            @php $currentYear = now()->year; @endphp
                            @for ($y = $currentYear; $y >= $currentYear - 4; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </label>
                </div>            
                <div class="ml-auto flex gap-2 text-xs items-center">
                    @php $currentYear = now()->year; @endphp
                    <strong>Descargar:</strong>
                        <a id="export-monthly-json"
                            href="{{ route('admin.subscriptions.monthly-net.export.json', ['year' => $currentYear]) }}"
                            class="px-2 py-1 border rounded hover:bg-zinc-100 dark:hover:bg-zinc-800">JSON</a>
                        <a id="export-monthly-excel"
                            href="{{ route('admin.subscriptions.monthly-net.export.excel', ['year' => $currentYear]) }}"
                            class="px-2 py-1 border rounded hover:bg-zinc-100 dark:hover:bg-zinc-800">CSV</a>
                </div>
            </div>
            <div class="overflow-x-auto -mx-1 px-1">
                <div id="subscription-monthly-net-table"></div>
            </div>
        </div>

        {{-- Age metrics --}}
        <div class="rounded border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900 space-y-4">
            <div class="flex justify-between gap-2 mb-3 text-xs items-center flex-wrap">
                <h2 class="font-semibold text-center">Miembros activos por edad</h2>
                <article class="ml-auto flex gap-2 text-xs items-center">
                    <strong>Descargar:</strong>
                    <a href="{{ route('admin.subscriptions.ages.export.json') }}"
                        class="px-2 py-1 border rounded hover:bg-zinc-100 dark:hover:bg-zinc-800">JSON</a>
                    <a href="{{ route('admin.subscriptions.ages.export.excel') }}"
                        class="px-2 py-1 border rounded hover:bg-zinc-100 dark:hover:bg-zinc-800">CSV</a>
                </article>
            </div>
            <div class="overflow-x-auto -mx-1 px-1">
                <div id="subscription-ages-table" data-endpoint="{{ route('admin.subscriptions.ages') }}"></div>
            </div>
        </div>
    </div>

    @vite('resources/js/admin/subscriptions/stats.js')

    <style>
        /* Ajustes responsive Tabulator */
        @media (max-width: 640px) {
            #subscription-percentages-table .tabulator, 
            #subscription-monthly-net-table .tabulator {
                font-size: 0.75rem;
            }
            #subscription-monthly-net-table .tabulator .tabulator-header .tabulator-col,
            #subscription-percentages-table .tabulator .tabulator-header .tabulator-col {
                padding: .25rem .35rem;
            }
            #subscription-monthly-net-table .tabulator .tabulator-cell,
            #subscription-percentages-table .tabulator .tabulator-cell {
                padding: .35rem .4rem;
            }
        }
    </style>
</x-layouts.app>