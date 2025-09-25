<x-layouts.app.general>
    <x-slot name="title">Introducción</x-slot>
    <x-slot name="content">
        <section class="container mx-auto px-4 py-8 mb-15">
            <h1 class="text-3xl font-bold text-center mb-6">🏋️ Olympus Gym - Gestión de Gimnasio</h1>
            <p class="text-lg leading-relaxed text-center max-w-3xl mx-auto">
                ¡Bienvenido a <strong>Olympus Gym</strong> 🚀<br>
                Esta aplicación web te permite gestionar un gimnasio de forma profesional y moderna, con una experiencia
                pensada tanto para administradores como para socios.
            </p>
            <div class="text-center my-6">
                <a href="https://project-gym.diegochacondev.es/"
                    class="inline-block px-6 py-3 text-lg font-semibold text-white bg-green-800 hover:bg-green-600 rounded-lg shadow">
                    🌐 Accede a la demo aquí
                </a>
            </div>
            {{-- link to repo --}}
            <div class="text-center m-4">
                <a href="https://github.com/diegdar/olympus-gym"
                    class="inline-flex items-center px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-lg transition duration-150 ease-in-out dark:bg-gray-800 dark:hover:bg-gray-600"
                    target="_blank">
                    Ver repositorio
                    <i class="fa-brands fa-github fa-lg ml-3" style="color: #eef6ff;"></i>
                </a>
            </div>

            <!-- Características principales -->
            <section class="my-12">
                <h2 class="text-2xl font-semibold mb-4">✨ Características principales</h2>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Registro de usuarios:</strong> mediante formulario.</li>
                    <li><strong>Inicio de sesión:</strong> mediante formulario, GitHub o Google.</li>
                    <li><strong>Gestión de roles y permisos:</strong> control granular de accesos.</li>
                    <li><strong>Datos ficticios pre-cargados:</strong> actividades, horarios, salas y usuarios listos
                        para usar.</li>
                    <li><strong>Dark mode y diseño responsivo:</strong> optimizado para tablets y móviles.</li>
                    <li><strong>Tests automáticos:</strong> unitarios y funcionales para garantizar calidad del código y
                        evitar bugs en producción.</li>
                </ul>
            </section>

            <!-- Roles -->
            <section class="my-12">
                <h2 class="text-2xl font-semibold mb-4">🛡️ Roles y permisos</h2>
                <ul class="list-disc pl-6 space-y-3">
                    <li><strong>Super-admin:</strong> 
                        <ul class="list-square pl-6 mt-2"> 
                            <li>Puede:
                                <ul class="list-circle pl-6">
                                    <li>Ver, crear, editar y eliminar: usuarios, Roles.</li>
                                    <li>Ver estadísticas de las suscripciones y descargar reportes.</li>
                                </ul>
                            </li>
                            <li>No Puede:
                                <ul class="list-circle pl-6">
                                    <li>Gestionar la suscripción de un usuario ageno al suyo.</li>
                                    <li>gestionar el perfil de un usuario ageno al suyo.</li>
                                    <li>Ver, crear, editar y eliminar: salas y actividades, ademas de horarios para las actividades.</li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li><strong>Admin:</strong> 
                        <ul class="list-square pl-6 mt-2"> 
                            <li>Puede:
                                <ul class="list-circle pl-6">
                                    <li>Ver estadísticas de las suscripciones y descargar reportes.</li>
                                    <li>Ver: usuarios y Roles.</li>
                                    <li>Ver, crear, editar y eliminar: salas y actividades, ademas de horarios para las actividades.</li>
                                </ul>
                            </li>
                            <li>No Puede:
                                <ul class="list-circle pl-6">
                                    <li>Crear, editar y eliminar: usuarios y Roles.</li>
                                    <li>Gestionar la suscripción de un usuario ageno al suyo.</li>
                                    <li>gestionar el perfil de un usuario ageno al suyo.</li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li><strong>Member:</strong> 
                        <ul class="list-square pl-6 mt-2"> 
                            <li>Puede:
                                <ul class="list-circle pl-6">
                                    <li>Ver su dashboard y gestionarlo.</li>
                                    <li>Ver las vistas: Instalaciones, Servicios, Contacto.</li>
                                    <li>Inscribirse a una actividad.</li>
                                    <li>Ver y gestionar su perfil.</li>
                                    <li>Ver y gestionar su suscripcion.</li>
                                </ul>
                            </li>
                            <li>No Puede:
                                <ul class="list-circle pl-6">
                                    <li>Ver, crear, editar y eliminar: usuarios y Roles.</li>
                                    <li>Crear, editar y eliminar: salas y actividades, ademas de horarios para las actividades.</li>
                                    <li>Gestionar la suscripción de un usuario ageno al suyo.</li>
                                    <li>gestionar el perfil de un usuario ageno al suyo.</li>
                                    <li>Ver estadísticas de las suscripciones y descargar reportes.</li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </section>

            <!-- Usuarios de prueba -->
            <section class="my-12">
                <h2 class="text-2xl font-semibold mb-4">👤 Usuarios de prueba</h2>
                {{-- pc and tablet devices --}}
                <div class="overflow-x-auto hidden sm:block">
                    <table class="w-full border border-gray-300 dark:border-gray-700 text-sm sm:table">
                        <thead class="bg-gray-200 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2">Usuario</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Contraseña</th>
                                <th class="px-4 py-2">Rol</th>
                                <th class="px-4 py-2">Permisos principales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t dark:border-gray-700">
                                <td class="px-4 py-2">diego chacon</td>
                                <td class="px-4 py-2">diego_chacon@superadmin.com</td>
                                <td class="px-4 py-2">PassNix$123</td>
                                <td class="px-4 py-2">super-admin</td>
                                <td class="px-4 py-2">Acceso total a usuarios, roles, actividades, salas, suscripciones.
                                </td>
                            </tr>
                            <tr class="border-t dark:border-gray-700">
                                <td class="px-4 py-2">luis guillermo</td>
                                <td class="px-4 py-2">luis_admin@admin.com</td>
                                <td class="px-4 py-2">PassNix$123</td>
                                <td class="px-4 py-2">admin</td>
                                <td class="px-4 py-2">Gestión de actividades, salas, horarios y estadísticas.</td>
                            </tr>
                            <tr class="border-t dark:border-gray-700">
                                <td class="px-4 py-2">raul prieto</td>
                                <td class="px-4 py-2">raul_prieto@socio.com</td>
                                <td class="px-4 py-2">PassNix$123</td>
                                <td class="px-4 py-2">member</td>
                                <td class="px-4 py-2">Reservar actividades, ver suscripción y gestionar perfil.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- mobile devices --}}
                <div class="space-y-4 sm:hidden text-sm">
                    <div
                        class="p-4 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 shadow-sm">
                        <p class="font-bold text-lg mb-1">👤 diego chacon</p>
                        <p><span class="font-semibold text-indigo-600 dark:text-indigo-400">Rol:</span> super-admin</p>
                        <p><span class="font-semibold">Email:</span> diego_chacon@superadmin.com</p>
                        <p><span class="font-semibold">Contraseña:</span> PassNix$123</p>
                        <p class="mt-2"><span class="font-semibold">Permisos:</span> Acceso total a usuarios, roles,
                            actividades, salas, suscripciones.</p>
                    </div>

                    <div
                        class="p-4 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 shadow-sm">
                        <p class="font-bold text-lg mb-1">👤 luis guillermo</p>
                        <p><span class="font-semibold text-indigo-600 dark:text-indigo-400">Rol:</span> admin</p>
                        <p><span class="font-semibold">Email:</span> luis_admin@admin.com</p>
                        <p><span class="font-semibold">Contraseña:</span> PassNix$123</p>
                        <p class="mt-2"><span class="font-semibold">Permisos:</span> Gestión de actividades, salas,
                            horarios y estadísticas.</p>
                    </div>

                    <div
                        class="p-4 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 shadow-sm">
                        <p class="font-bold text-lg mb-1">👤 raul prieto</p>
                        <p><span class="font-semibold text-indigo-600 dark:text-indigo-400">Rol:</span> member</p>
                        <p><span class="font-semibold">Email:</span> raul_prieto@socio.com</p>
                        <p><span class="font-semibold">Contraseña:</span> PassNix$123</p>
                        <p class="mt-2"><span class="font-semibold">Permisos:</span> Reservar actividades, ver
                            suscripción y gestionar perfil.</p>
                    </div>
                </div>

                <p class="text-sm mt-2">Puedes usar estos datos para iniciar sesión y probar la aplicación con
                    diferentes permisos.</p>
            </section>

            <!-- Estadísticas con Tabulator -->
            <section class="my-12">
                <h2 class="text-2xl font-semibold mb-4">📊 Estadísticas con Tabulator</h2>
                <p>
                    La aplicación también incluye <strong>estadísticas dinámicas</strong> utilizando la librería <a
                        href="https://tabulator.info/" target="_blank"
                        class="text-indigo-600 font-semibold">JS-Tabulator</a>, las cuales se adaptan según el rol del
                    usuario:
                </p>
                <ul class="list-disc pl-6 mt-4 space-y-2">
                    <li><strong>Member (socio):</strong> podrá visualizar en su dashboard estadísticas personalizadas
                        sobre las clases asistidas, el porcentaje de participación en cada una de ellas y su progreso a
                        lo largo del tiempo.</li>
                    <li><strong>Admin / Superadmin:</strong> dispondrán de estadísticas globales del gimnasio, como el
                        número de miembros activos según su tipo de cuota, altas y bajas del año, así como miembros
                        activos por rango de edad.</li>
                </ul>
                <p class="mt-4">Estas estadísticas permiten un análisis claro y visual, mejorando la experiencia del
                    socio y facilitando la gestión estratégica para los administradores.</p>
            </section>

            <!-- Tests -->
            <section class="my-12">
                <h2 class="text-2xl font-semibold mb-4">🧪 Tests</h2>
                <p>La aplicación incluye tests unitarios y funcionales para asegurar la calidad del código.</p>
                <pre class="bg-gray-800 text-white p-3 rounded mt-3"><code>php artisan test</code></pre>
                <p class="mt-2">Los tests cubren registro, gestión de actividades, reservas, roles y permisos.</p>
            </section>

            <!-- Datos ficticios -->
            <section class="my-12">
                <h2 class="text-2xl font-semibold mb-4">📦 Datos ficticios</h2>
                <p>La base de datos se inicializa automáticamente con actividades, horarios, salas y usuarios mediante
                    migraciones y seeders.</p>
            </section>

            <!-- Registro y acceso -->
            <section class="my-12">
                <h2 class="text-2xl font-semibold mb-4">📲 Registro y acceso</h2>
                <ul class="list-disc pl-6">
                    <li><strong>Formulario de registro:</strong> completa tus datos y acepta la política de privacidad.
                    </li>
                    <li><strong>Acceso con GitHub o Google:</strong> inicia sesión rápidamente con tus credenciales
                        sociales.</li>
                </ul>
            </section>

            <!-- Contacto -->
            <section class="my-12 text-center">
                <h2 class="text-2xl font-semibold mb-4">📞 Contacto del desarrollador</h2>
                <p><strong>Dev:</strong> Diego Chacon Delgado</p>
                <p><strong>Email:</strong> info@diegochacondev.es</p>
            </section>

            <p class=" text-xl font-bold text-center mt-10">¡Gracias por probar Olympus Gym! 💪</p>
        </section>
    </x-slot>
</x-layouts.app.general>