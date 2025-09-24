<x-layouts.app>
    @section('title', 'Mi suscripción')
    <h1 class="mb-3 text-2nd">Mi suscripción</h1>
    {{-- Mensaje de alerta --}}
    @if (session('msg'))
        <div id="message"
            class="absolute left-0 top-5 w-full z-50 inline-block bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded dark:bg-green-800 dark:border-green-700 dark:text-green-200 text-center"
            room="alert">
            <span class="block sm:inline font-bold">
                {{ session('msg') }}
            </span>
        </div>
    @endif

    {{-- Subscription details --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg pl-5 py-2">
        <p class="mt-2 flex flex-col sm:flex-row"><strong class="font-bold dark:text-red-300 mr-2">Usuario:</strong>
            {{ $user->name }}
        </p>
        <p class="mt-2 flex flex-col sm:flex-row"><strong class="font-bold dark:text-red-300 mr-2">Fecha de
                inicio:</strong>
            {{ $currentSubscription->pivot->start_date_formatted ?? 'No asignada' }}
        </p>
        <p class="mt-2 flex flex-col sm:flex-row"><strong class="font-bold dark:text-red-300 mr-2">Fecha de
                finalización:</strong>
            {{ $currentSubscription->pivot->end_date_formatted ?? 'No asignada' }}
        </p>
        {{-- FEE --}}
        <div class="flex mt-2">
            <p class="flex flex-col sm:flex-row"><strong class="font-bold dark:text-red-300 mr-2">Cuota:</strong>
                {{ $currentSubscription->fee_translated ?? 'No asignada' }}
            </p>
            <article class="flex flex-wrap sm:flex-nowrap ms-3 sm:ms-10">
                <label class="font-bold dark:text-yellow-400 mr-2 whitespace-nowrap" for="subscription_id">
                    Cambiar por:
                </label>
                <form action="{{ route('member.subscription.update') }}" method="POST" class="flex flex-wrap gap-2">
                    @csrf
                    @method('PUT')
                    <div>
                        <select name="subscription_id" id="subscription_id"
                            class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 mt-1 sm:mt-0">
                            <option value="" class="w-auto">-Seleccione Suscripción-</option>
                            @foreach ($subscriptions as $subscription)
                                <option value="{{ $subscription->id }}">
                                    {{ $subscription->fee_translated }}
                                </option>
                            @endforeach
                        </select>
                        @error('subscription_id')
                            <span class="block text-red-500 text-sm dark:text-red-400 mt-1">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    {{-- Send button --}}
                    <div class="flex items-start">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-1 sm:mt-0">
                            Cambiar
                        </button>
                    </div>
                </form>
            </article>
        </div>
    </div>
    @vite('resources/js/messageTransition.js')
</x-layouts.app>