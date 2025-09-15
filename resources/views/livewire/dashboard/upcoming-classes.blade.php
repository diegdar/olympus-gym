<div class="p-5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
  <h3 class="text-md text-center uppercase tracking-wide text-zinc-500 mb-3">Tus próximas clases (7 días)</h3>

  {{-- Mobile: list cards --}}
  <div class="space-y-3 sm:hidden">
    @forelse($rows as $row)
      <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-3 bg-white dark:bg-zinc-900">
        <div class="flex items-center justify-between gap-3">
          <div>
            <p class="text-sm font-semibold">{{ $row['activity'] }}</p>
            <p class="text-xs text-zinc-400">{{ $row['date'] }} · {{ $row['room'] }}</p>
          </div>
          <div class="text-right">
            <p class="text-xs text-zinc-400">Inscritos</p>
            <p class="text-sm font-medium">{{ $row['enrolled'] }}</p>
          </div>
        </div>
        <div class="mt-3 flex justify-center">
          <button wire:click="unenroll({{ $row['id'] }})" class="px-3 py-1.5 text-xs rounded bg-red-600 hover:bg-red-500 text-white">
            Desinscribirse
          </button>
        </div>
      </div>
    @empty
      <p class="text-xs text-zinc-500">No tienes reservas.</p>
    @endforelse
  </div>

  {{-- Desktop/Tablet: table --}}
  <div class="overflow-x-auto hidden sm:block">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-zinc-400">
          <th class="py-2 pr-4">Fecha</th>
          <th class="py-2 pr-4">Actividad</th>
          <th class="py-2 pr-4">Sala</th>
          <th class="py-2 pr-4">Inscritos</th>
          <th class="py-2 pr-4">Acción</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-zinc-800">
        @forelse($rows as $row)
          <tr>
            <td class="py-2 pr-4">{{ $row['date'] }}</td>
            <td class="py-2 pr-4">{{ $row['activity'] }}</td>
            <td class="py-2 pr-4">{{ $row['room'] }}</td>
            <td class="py-2 pr-4">{{ $row['enrolled'] }}</td>
            <td class="py-2 pr-4">
              <button wire:click="unenroll({{ $row['id'] }})" class="px-2 py-1 text-xs rounded bg-red-600 hover:bg-red-500 text-white">
                Desinscribirse
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="py-4 text-zinc-500">No tienes reservas.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
