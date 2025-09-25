<x-layouts.app.general>
    <x-slot name="title">
        Contacto        
    </x-slot>
    <x-slot name="content">
        {{-- Contact info and contact form --}}
        <h1 class="text-3xl font-bold text-center text-2nd">Contacta con nosotros</h1>
        <section class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <livewire:guest.contact-form />
            {{-- <div class="ml-3 sm:ml-0 mt-5"> --}}
            <div class="flex flex-col justify-around gap-8 mt-5 sm:mt-3 ml-3 sm:ml-0">
                <section class="flex flex-wrap items-center gap-3">
                    <h2 class="text-xl font-semibold">Teléfono</h2>
                    <p class="text-zinc-700 dark:text-zinc-300 text-xl font-semibold">
                        <a href="tel:+34910000000" class="underline hover:text-blue-600 dark:hover:text-blue-400 transition">+34 910 00 00 00</a>
                    </p>
                </section>
                <section class="flex flex-wrap items-center gap-3">
                    <h2 class="text-xl font-semibold">Email</h2>
                    <p class="text-zinc-700 dark:text-zinc-300 text-xl font-semibold">
                        <a href="mailto:info@olympusgym.es" class="underline hover:text-blue-600 dark:hover:text-blue-400 transition">info@olympusgym.es</a>
                    </p>      
                </section>          
                <section class="flex flex-wrap items-center gap-3">
                    <h2 class="text-xl font-semibold">instagram</h2>
                    <p class="text-zinc-700 dark:text-zinc-300 text-xl font-semibold">
                        <a href="https://www.instagram.com/olympusgym" class="underline hover:text-blue-600 dark:hover:text-blue-400 transition">@olympusgym</a>
                    </p>      
                </section>          
            </div>
        </section>
        {{-- Contact and Location --}}
        <section class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-8 text-center text-2nd">¿Dónde estamos?</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Location and Contact Info -->
            <div class="flex flex-col justify-center bg-white dark:bg-zinc-900 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Dirección</h2>
                <p class="text-zinc-700 dark:text-zinc-300 mb-2">
                Calle Ejemplo 123, 08001 Barcelona, España
                </p>
                <h2 class="text-xl font-semibold mb-4 mt-6">Horario</h2>
                <ul class="text-zinc-700 dark:text-zinc-300 mb-2">
                <li>Lunes a Viernes: 7:00 - 22:00</li>
                <li>Sábado: 9:00 - 20:00</li>
                <li>Domingo: Cerrado</li>
                </ul>
            </div>
            <!-- Google Maps Embed -->
            <div class="flex items-center justify-center">
                <iframe
                title="Olympus Gym Location"
                src="https://www.google.com/maps?q=Calle+Ejemplo+1,+08001+Barcelona,+España&output=embed"
                width="100%"
                height="350"
                style="border:0; border-radius: 0.75rem;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                class="shadow-lg"
                ></iframe>
            </div>
            </div>
        </section>
    </x-slot>
</x-layouts.app.general>