<x-layouts.app>
    @section('title', 'Dashboard')

        <section class="space-y-8 mx-2 sm:mx-3 mt-2">
            {{-- Name and avatar --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex items-center gap-4 justify-center sm:justify-start w-full sm:w-auto">
                    <div
                        class="h-16 w-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-xl font-bold text-white uppercase">
                        {{ auth()->user()->initials() }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">Hola {{ auth()->user()->name }} 👋</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Bienvenido de nuevo a tu panel</p>
                    </div>
                </div>
            </div>
            {{-- Subscription and weekly progress --}}
            <div class="grid gap-4 sm:grid-cols-2">
                {{-- Subscription --}}
                <div class="p-5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                    <h3 class="text-md uppercase tracking-wide text-zinc-500 mb-2 text-center">Suscripción</h3>
                    @if($currentSubscription)
                        <p class="text-lg font-semibold">
                            <strong>Cuota: </strong>{{ $currentSubscription->fee_translated ?? ucfirst($currentSubscription->fee) }}
                        </p>
                        <p class="text-sm mt-1"><strong>Vigente hasta: </strong>{{ $endCarbon?->format('d/m/Y') ?? '—' }}
                        </p>
                        @if(!is_null($daysLeft))
                            <p class="text-xs mt-1 {{ $daysLeft <= 7 ? 'text-amber-500' : 'text-zinc-400' }}">
                                {{ $daysLeft >= 0 ? "Quedan $daysLeft día(s)" : 'Expirada' }}
                            </p>
                        @endif
                    @else
                        <p class="text-sm text-zinc-400">Sin suscripción activa</p>
                    @endif
                    <div class="mt-3 flex gap-2">
                        <a href="{{ route('member.subscription') }}"
                            class="px-3 py-1 text-xs rounded bg-indigo-600 hover:bg-indigo-500 text-white">Ver detalle</a>
                    </div>
                </div>
                {{-- Weekly progress --}}
                <div class="p-5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                    <h3 class="text-md uppercase tracking-wide text-zinc-500 mb-2 text-center">Progreso semana actual</h3>
                    <div class="flex flex-col mt-0 sm:mt-8">
                       <p class="text-sm mb-1">Asistencias(reales/deseadas): <strong>{{ $attended }}</strong> / {{ $goal }}</p>
                        <div class="h-2 w-full rounded bg-zinc-700/30 overflow-hidden">
                            <div class="h-full bg-emerald-500 transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                        <p class="text-xs mt-2 {{ $attended >= $goal ? 'text-emerald-400' : 'text-zinc-400' }}">
                            {{ $attended >= $goal ? '¡Objetivo alcanzado!' : 'Te faltan ' . max(0, $goal - $attended) . ' clase(s)' }}
                        </p>
                    </div>
                </div>
            </div>
            {{-- Recommended Activities (Livewire) --}}
            <livewire:dashboard.recommended-activities />            

            {{-- Upcoming classes (week) --}}
            <livewire:dashboard.upcoming-classes />

            {{-- Charts --}}
            <div class="grid gap-6 lg:grid-cols-3">
                {{-- Weekly Attendance --}}
                <div
                    class="p-5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 lg:col-span-2">
                    <h3 class="text-md text-center uppercase tracking-wide text-zinc-500 mb-3">Asistencias últimas 8 semanas</h3>
                    <div class="relative h-56 sm:h-64 md:h-72 lg:h-80 overflow-hidden">
                        <canvas id="member-weekly-attendance-chart" class="block w-full h-full max-w-full"
                            data-endpoint="{{ route('member.stats.weekly-attendance') }}"></canvas>
                    </div>
                </div>
                {{-- Activity Distribution --}}
                <div class="p-5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                    <h3 class="text-md text-center uppercase tracking-wide text-zinc-500 mb-3">Distribución por tipo</h3>
                    <div class="relative h-56 sm:h-64 md:h-72 overflow-hidden">
                        <canvas id="member-activity-distribution-chart" class="block w-full h-full max-w-full"
                            data-endpoint="{{ route('member.stats.activity-distribution') }}"></canvas>
                    </div>
                    <div id="member-activity-distribution-legend" class="mt-3 text-sm space-y-1"></div>
                </div>
            </div>
        </section>

    @vite(['resources/js/dashboard/memberCharts.js'])
</x-layouts.app>