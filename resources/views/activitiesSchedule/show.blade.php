<x-layouts.app>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mx-2 sm:mx-5"> 
        <!-- name -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Actividad:</span>
                <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                    {{ $activitySchedule->activity->name }}</p>
            </div>
        </article>
        <!-- sala -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Sala:</span>
                <p name="description" class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3"
                    id="description">
                    {{ $activitySchedule->room->name }}</p>
            </div>
        </article>
        <!-- day date -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Dia/Fecha:</span>
                <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                    {{ $dayDateFormatted }}</p>
            </div>
        </article>
        <!-- start time -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Hora inicio:</span>
                <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                    {{ $startTimeFormatted }}</p>
            </div>
        </article>
        <!-- duration -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Duración:</span>
                <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                    {{ $activitySchedule->activity->duration }} minutos</p>
            </div>
        </article>
        <!-- available slots -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Plazas disponibles:</span>
                <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                    {{ $availableSlots }}</p>
            </div>
        </article>
        <!-- total slots -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Total plazas:</span>
                <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                    {{ $activitySchedule->max_enrollment }}</p>
            </div>
        </article>
        <!-- current enrollment -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Total inscritos:</span>
                <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                    {{ $realEnrollment }}</p>
            </div>
        </article>
    </div>
    @role('admin')
        <div class="mt-10 mx-2 sm:mx-5 space-y-4">
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-xl font-semibold w-full text-center sm:text-left sm:w-auto">Usuarios inscritos</h2>
                <div class="w-full sm:w-auto sm:ml-auto flex flex-wrap justify-center sm:justify-end gap-2 text-xs items-center">
                <strong>Descargar:</strong>
                <a id="enrolled-json" class="px-2 py-1 border rounded hover:bg-zinc-100 dark:hover:bg-zinc-800" href="{{ route('activity.schedules.enrolled-users', $activitySchedule) }}?format=json">JSON</a>
                <a id="enrolled-csv" class="px-2 py-1 border rounded hover:bg-zinc-100 dark:hover:bg-zinc-800" href="{{ route('activity.schedules.enrolled-users', $activitySchedule) }}?format=csv">CSV</a>
                <button id="save-attendance" class="px-3 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700 disabled:opacity-50" data-endpoint="{{ route('activity.schedules.attendance', $activitySchedule) }}">Guardar asistencia</button>
                </div>
            </div>

            <div class="rounded border border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900">
                <div id="activity-schedule-enrolled-table" data-endpoint="{{ route('activity.schedules.enrolled-users', $activitySchedule) }}"></div>
            </div>
        </div>
    @endrole

    @vite(['resources/js/activity-schedule/show.js'])

</x-layouts.app>