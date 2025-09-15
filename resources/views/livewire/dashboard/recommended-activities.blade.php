<div class="p-5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 flex flex-col">
  <h3 class="text-md uppercase tracking-wide text-zinc-500 mb-2 text-center">Actividades recomendadas (48h)</h3>
  <ul class="text-sm flex-1 divide-y divide-zinc-700/40">
    @forelse($items as $rec)
      <li class="py-2 flex flex-wrap items-center">
        <!-- activity info -->
        <div class="flex flex-wrap w-full sm:w-[65%]">
          <section class="font-medium w-full sm:w-1/2">{{ $rec['activity_name'] }}</section>
          <section class="text-xs text-zinc-400 w-full sm:w-1/4"><strong>Fecha: </strong>{{ \Carbon\Carbon::parse($rec['start_datetime'])->format('d/m H:i') }}</section>
          <section class="text-xs text-zinc-400 w-full sm:w-1/4"><strong>Sala: </strong>{{ $rec['room_name'] }}</section>
        </div>
        {{-- free slots --}}
        <div class="w-full sm:w-[30%]">
          @if(($rec['free_slots'] ?? 0) > 0)
            <div class="flex justify-between sm:gap-2 items-center">
              <span class="text-s text-zinc-400"><strong>Plazas libres: </strong>{{ $rec['free_slots'] }}</span>
              <button wire:click="enroll({{ $rec['id'] }})"
          class="px-2 py-1 text-xs rounded bg-emerald-600 hover:bg-emerald-500 text-white">
          Inscribirse
              </button>
            </div>
          @else
            <span class="text-xs text-rose-400">Lleno</span>
          @endif
        </div>
      </li>
    @empty
      <li class="py-2 text-xs text-zinc-400">Sin sugerencias.</li>
    @endforelse
  </ul>
  <a href="{{ route('activity.schedules.index') }}" class="mt-3 text-xs text-indigo-400 hover:text-indigo-300">Ver
    horario completo →</a>
</div>