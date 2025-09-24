<x-layouts.app>
    @section('title', 'Editar horario actividad')
    <h1 class="mb-3 text-2xl font-bold mt-2 mx-2 text-2nd">Editar horario de la actividad</h1>
    <!-- form -->
    <form action="{{ route('activity.schedules.update', $activitySchedule) }}" method="post" class="my-4">
        @csrf
        @method('PUT')

        <section class="ml-3">
            <!-- submit boton -->
            <div class="flex-col text-center mb-5 mt-8">
                <button type="submit"
                    class="bg-blue-500 
                hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline dark:bg-blue-600 dark:hover:bg-blue-700 cursor-pointer">
                    Guardar
                </button>
            </div>
            {{-- form fields --}}
            <div class="flex flex-wrap justify-start 
                sm:justify-center gap-4">
                <!-- activity -->
                <article class="flex flex-col sm:flex-wrap gap-2">
                    <label for="activity_id" class="font-bold sm:text-xl">Actividad:</label>
                    <div class="flex flex-col">
                        <select name="activity_id" id="activity_id"
                            class="w-full shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <option value="">-Seleccione una actividad-</option>
                            @foreach ($activities as $activity)
                                <option value="{{ $activity->id }}"
                                    {{ $activity->id == $activitySchedule->activity_id ? 'selected' : '' }}>
                                    {{ $activity->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('activity_id')
                            <span class="text-red-500 text-sm
                                dark:text-red-400">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </article>
                <!-- max_enrollment -->
                <article class="flex flex-col sm:flex-wrap gap-2">
                    <label for="max_enrollment" 
                        class="font-bold sm:text-xl">
                        Total plazas:
                    </label>
                    <div class="flex flex-col">
                        <select name="max_enrollment" id="max_enrollment"
                            class="w-full shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 text-center">
                            <option value="">-Seleccione plazas-</option>
                            @foreach ([20, 30, 40, 50] as $capacity)
                                <option value="{{ $capacity }}"
                                    {{ $capacity == ($activitySchedule->max_enrollment ?? '') ? 'selected' : '' }}>
                                    {{ $capacity }} plazas
                                </option>
                            @endforeach
                        </select>
                        @error('max_enrollment')
                            <span class="text-red-500 text-sm dark:text-red-400">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </article>                    
                <!-- activity dateTime -->
                <article class="flex flex-col sm:flex-wrap gap-2">
                    <label for="start_datetime" 
                        class="font-bold sm:text-xl">
                        Fecha/hora:
                    </label>
                    <div class="flex flex-col">
                        <input name="start_datetime"
                            class="w-full shadow 
                            appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                            id="start_datetime"
                            type="datetime-local"
                            value="{{ old('start_datetime', $activitySchedule->start_datetime ?? '') }}">
                        @error('start_datetime')
                            <span class="text-red-500 text-sm dark:text-red-400">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </article> 
                <!-- room -->
                <article class="flex flex-col sm:flex-wrap gap-2">
                    <label for="room_id" class="font-bold sm:text-xl">Sala:</label>
                    <div class="flex flex-col">
                        <select name="room_id" id="room_id"
                            class="w-full shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <option value="">-Seleccione una sala-</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}"
                                    {{ $room->id == $activitySchedule->room_id ? 'selected' : '' }}>
                                    {{ $room->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('room_id')
                            <span class="text-red-500 text-sm
                                dark:text-red-400">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </article>            
            </div>
        </section>
    </form>
</x-layouts.app>
