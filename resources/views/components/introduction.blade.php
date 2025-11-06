<div class="mb-20">
    <h1 class="font-bold text-center mb-2 text-2nd">Bienvenido a Olympus Gym</h1>
    <p class="text-2xl font-semibold text-center font-shadows-light mb-2">tu santuario del fitness</p>
    <!-- abanico de imagenes -->
    <section class="flex w-full h-[430px]">
        <img class="w-0 grow object-cover opacity-70 transition-all duration-300 ease-in-out hover:cursor-crosshair hover:w-[300px] hover:opacity-100 hidden sm:block hover:contrast-125" src="{{ asset('img/home/crossfit_class.webp') }}" alt="personas haciendo crossfit">
        <img class="w-0 grow object-cover opacity-70 transition-all duration-300 ease-in-out hover:cursor-crosshair hover:w-[300px] hover:opacity-100 hidden sm:block hover:contrast-125" src="{{ asset('img/home/exercise_two_girls.webp') }}" alt="una chica instruyendo a otra como hacer ejercicio">
        <img class="w-0 grow object-cover opacity-70 transition-all duration-300 ease-in-out hover:cursor-crosshair hover:w-[300px] hover:opacity-100 hidden md:block hover:contrast-125" src="{{ asset('img/home/girl_using_small_weights.webp') }}" alt="chica haciendo ejercicio con pequeños pesos">
        <img class="w-0 grow object-cover opacity-100 sm:opacity-70 transition-all duration-300 ease-in-out hover:cursor-crosshair hover:w-[300px] hover:opacity-100 hover:contrast-125" src="{{ asset('img/home/spinning.webp') }}" alt="chico haciendo spinning">
        <img class="w-0 grow object-cover opacity-100 sm:opacity-70 transition-all duration-300 ease-in-out hover:cursor-crosshair hover:w-[300px] hover:opacity-100 hover:contrast-125" src="{{ asset('img/home/zumba_class.webp') }}" alt="personas haciendo una clase de zumba">
    </section>
    <!-- introduction gym text -->
    <section class="m-5 mt-3 sm:mt-15">
        <h2>¿Qué nos hace únicos?</h2>
        <p>
            En Olympus Gym, no solo encontrarás un gimnasio, sino una comunidad apasionada por el bienestar y el crecimiento personal. Nos enorgullece ofrecerte un espacio donde puedes desafiar tus límites, alcanzar tus metas y descubrir tu máximo potencial.
        </p>
        <ul>
            <li>
                <p class="mt-2"><span class="text-2nd font-semibold">Entrenadores expertos: </span>Nuestro equipo de profesionales certificados te guiará en cada paso de tu camino, diseñando planes personalizados y brindándote el apoyo que necesitas para alcanzar tus objetivos.
                </p>
            </li>
            <li>
                <p class="mt-2"><span class="text-2nd font-semibold">Variedad de clases: </span>Desde sesiones de alta intensidad hasta clases de yoga y pilates, ofrecemos una amplia gama de opciones para que encuentres la actividad que mejor se adapte a tus gustos y necesidades.
                </p>
            </li>
            <li>
                <p class="mt-2"><span class="text-2nd font-semibold">Comunidad inspiradora: </span>Únete a un grupo de personas motivadas que comparten tu pasión por el fitness y te impulsarán a superar tus propios límites.</p>
            </li>
            <li>
                <p class="mt-2"><span class="text-2nd font-semibold">Ambiente motivador: </span>En Olympus Gym, te sentirás como en casa. Nuestra atmósfera cálida y acogedora te inspirará a dar lo mejor de ti en cada entrenamiento.
                </p>
            </li>
        </ul>
        <p class="mt-2">
            Te invitamos a explorar nuestra página web y descubrir todo lo que Olympus Gym tiene para ofrecerte. ¡Contáctanos para programar una visita y comenzar a construir la mejor versión de ti mismo!
        </p>
        <p class="font-permanent-marker text-right text-2nd text-2xl">
            ¡En Olympus Gym, tu éxito es nuestra meta!
        </p>
    </section>
    <!-- Fees -->
    @if(!auth()->check())
        <section class="m-5 mt-20">
            <h2 class="mb-8">Elige tu cuota</h2>
            <article class="flex flex-wrap justify-center gap-4">
                <!-- fee-card 1 -->
                <div class="w-full min-w-[200px] max-w-[280px] mx-3 border-1 min-h-[300px]  rounded-lg shadow-md p-6 flex flex-col justify-between hover:cursor-pointer hover:scale-105
                ">
                    <a href="/register?fee=monthly">
                        <h3 class="text-2xl font-semibold text-center mb-8">Cuota Mensual</h3>
                        <p class="mb-2">-Acceso ilimitado al gimnasio</p>
                        <p class="mb-2">-Clases grupales incluidas</p>
                        <p class="text-3xl text-center font-bold text-green-500 mt-17">50€ / mes</p>
                        <p class="mt-9 bg-green-500 hover:bg-green-600 font-semibold py-2 px-4 rounded w-full text-center block">
                            ¡Inscríbete!
                        </p>
                    </a>
                </div>             
                <!-- fee-card 2 -->
                <div class="w-full min-w-[200px] max-w-[280px] mx-3 border-1 min-h-[300px]  rounded-lg shadow-md p-6 flex flex-col justify-between hover:cursor-pointer hover:scale-105
                ">
                    <a href="/register?fee=quarterly">
                        <h3 class="text-2xl font-semibold text-center mb-8">Cuota Trimestral</h3>
                        <p class="mb-2">-Acceso ilimitado al gimnasio</p>
                        <p class="mb-2">-Clases grupales incluidas</p>
                        <p class="mb-2">-1 sesión de entrenamiento personal</p>
                        <p class="text-3xl text-center font-bold text-blue-500 mt-3">135€ / trimestre</p>
                        <p class="mt-8 bg-blue-500 hover:bg-blue-600 font-semibold py-2 px-4 rounded w-full text-center block">
                            ¡Inscríbete!
                        </p>
                    </a>
                </div>
                <!-- fee-card 3 -->
                <div class="w-full min-w-[200px] max-w-[280px] mx-3 border-1 min-h-[300px]  rounded-lg shadow-md p-6 flex flex-col justify-between hover:cursor-pointer hover:scale-105
                ">
                    <a href="/register?fee=yearly">
                        <h3 class="text-2xl font-semibold text-center mb-8">Cuota Anual</h3>
                        <p class="mb-2">-Acceso ilimitado al gimnasio</p>
                        <p class="mb-2">-Clases grupales incluidas</p>
                        <p class="mb-2">-4 sesiones de entrenamiento personal</p>
                        <p class="text-3xl text-center font-bold text-purple-500">480€ / año</p>
                        <p class="mt-8 bg-purple-500 hover:bg-purple-600 font-semibold py-2 px-4 rounded w-full text-center block">
                            ¡Inscríbete!
                        </p>
                    </a>
                </div>
            </article>
        </section>             
    @endif
    <!-- gym schedule -->
    <section class="m-5">
        <h2>Horario del gym</h2>
        <ul>
            <li class="my-2"><span class="font-bold text-2nd">Lunes a viernes: </span>de 07:00 a 23:00 h</li>
            <li class="my-2"><span class="font-bold text-2nd">Sábados: </span>de 09:00 a 21:00 h</li>
            <li class="my-2"><span class="font-bold text-2nd">Domingos: </span>de 09:00 a 14:00 h</li>
        </ul>
        <p class="italic mt-3">
            *Club abierto todo el año, salvo 25 de diciembre y 1 de enero.
        </p>
    </section>
</div>