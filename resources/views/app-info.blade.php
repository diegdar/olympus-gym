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
                    🌐 Entra a la pagina principal del gimnasio
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

            <!-- Stack Tecnológico -->
            <section class="my-12">
                <h2 class="text-2xl font-semibold mb-4">🔧 Stack Tecnológico</h2>
                <p class="mb-4">Olympus Gym se basa en una pila tecnológica moderna y robusta que combina las mejores prácticas de desarrollo web para garantizar un rendimiento óptimo, seguridad avanzada y escalabilidad excepcional. Esta selección de tecnologías permite crear una aplicación web completa y profesional capaz de manejar la gestión integral de un gimnasio, desde la autenticación de usuarios hasta el seguimiento de actividades y estadísticas.</p> 
                <p>La integración de Laravel como núcleo del backend, junto con PHP para la lógica del servidor, JavaScript para la interactividad del frontend, y herramientas complementarias como MySQL para la persistencia de datos, Tailwind CSS para un diseño elegante y responsivo, Composer para la gestión de dependencias, Docker para la contenerización y despliegue consistente, y Livewire para componentes reactivos, resulta en una solución técnica sólida y eficiente. Cada componente ha sido elegido por su madurez, comunidad activa y capacidad para trabajar en armonía, asegurando un desarrollo rápido, mantenible y de alta calidad. A continuación, se detalla cada elemento de la pila tecnológica utilizada en Olympus Gym:</p>
                <ul class="pl-6 space-y-4">
                    <li><strong>🛠️ Laravel:</strong> Framework PHP utilizado para el desarrollo del backend, proporcionando una estructura robusta para rutas, controladores y modelos, facilitando el desarrollo rápido y seguro de aplicaciones web. Laravel incluye características avanzadas como Eloquent ORM para interacciones con la base de datos, middleware para seguridad, y un sistema de plantillas Blade para vistas dinámicas.</li>
                    <li><strong>🐘 PHP:</strong> Lenguaje de servidor principal que impulsa la lógica del lado del servidor, manejando la autenticación, gestión de datos y procesamiento de solicitudes. PHP es ampliamente utilizado en el desarrollo web debido a su flexibilidad, velocidad y compatibilidad con servidores web como Apache o Nginx.</li>
                    <li><strong>🟨 JavaScript:</strong> Empleado para la interactividad del frontend, incluyendo componentes dinámicos y manejo de eventos en la interfaz de usuario.</li>
                    <li><strong>🔐 Spatie Laravel Permission:</strong> Paquete utilizado para la gestión granular de roles y permisos, asegurando un control de acceso preciso y escalable. Este paquete facilita la implementación de un sistema de permisos basado en roles, permitiendo definir qué acciones puede realizar cada usuario según su rol asignado.</li>
                    <img src="{{ asset('img/introduction/role-seeder-file.webp') }}" alt="">
                    <li><strong>🗄️ MySQL:</strong> Sistema de gestión de bases de datos relacional utilizado para almacenar y gestionar datos de usuarios, actividades y suscripciones de manera eficiente. MySQL ofrece alta performance, confiabilidad y facilidad de uso, siendo ideal para aplicaciones que requieren consultas complejas y transacciones seguras.</li>
                    <img src="{{ asset('img/introduction/data-base-mysql.webp') }}" alt="">
                    <li><strong>🎨 Tailwind CSS:</strong> Framework de CSS utilitario para el diseño responsivo y moderno de la interfaz, permitiendo un desarrollo rápido de estilos sin necesidad de CSS personalizado extenso. Tailwind proporciona clases predefinidas que se pueden combinar para crear diseños únicos y adaptables a diferentes dispositivos.</li>
                    <li><strong>📦 Composer:</strong> Herramienta de gestión de dependencias para PHP, utilizada para instalar y gestionar paquetes de terceros, asegurando la consistencia del entorno de desarrollo. Composer automatiza la instalación de librerías y frameworks, resolviendo dependencias automáticamente y manteniendo versiones compatibles.</li>
                    <li><strong>🐳 Docker:</strong> Utilizado para la contenerización de la aplicación, facilitando el despliegue y la consistencia entre entornos de desarrollo y producción. Docker permite empaquetar la aplicación con todas sus dependencias en contenedores ligeros, asegurando que funcione de manera idéntica en cualquier entorno.</li>
                    <img src="{{ asset('img/introduction/docker-containers.webp') }}" alt="">
                    <li><strong>⚡ Livewire:</strong> Framework para componentes reactivos en Laravel, utilizado para crear interfaces dinámicas sin necesidad de JavaScript adicional, mejorando la experiencia de usuario en formularios y dashboards. Livewire simplifica el desarrollo de aplicaciones interactivas al permitir actualizar componentes del frontend desde el backend sin recargar la página.</li>
                    <img src="{{ asset('img/introduction/livewire-use.webp') }}" alt="">
                </ul>
            </section>

            <!-- Características principales -->
            <section class="my-12">
                <h2 class="text-2xl font-semibold mb-4">✨ Características principales</h2>
                <p class="mb-4">La aplicación Olympus Gym está equipada con una serie de características principales que la convierten en una herramienta indispensable para la gestión eficiente y moderna de gimnasios. Estas funcionalidades han sido diseñadas pensando en la experiencia tanto de los administradores como de los socios/miembros del gimnasio, ofreciendo una interfaz intuitiva, segura y altamente funcional. Desde el registro y autenticación de usuarios hasta la gestión avanzada de actividades y estadísticas, cada característica contribuye a optimizar las operaciones diarias del gimnasio, mejorar la retención de miembros y facilitar la toma de decisiones estratégicas. La implementación de estas características se basa en las mejores prácticas de desarrollo web, asegurando un rendimiento excepcional y una escalabilidad que permite crecer junto con el gimnasio. A continuación, se detallan las características principales que hacen de Olympus Gym una solución completa para la gestión de gimnasios:</p>
                <ul class="pl-7 space-y-2">
                    <li><strong>Registro de usuarios:</strong> mediante formulario personalizado que incluye validación en tiempo real, verificación de email y aceptación de políticas de privacidad para asegurar la integridad y legalidad de los datos recopilados.</li>
                    <li><strong>Inicio de sesión:</strong> mediante formulario tradicional, o de manera rápida y conveniente a través de autenticación social con GitHub o Google, proporcionando múltiples opciones de acceso seguras y flexibles.</li>
                    <li><strong>Gestión de roles y permisos:</strong> control granular de accesos que permite definir permisos específicos para cada rol, asegurando que los usuarios solo puedan realizar acciones autorizadas según su nivel de responsabilidad.</li>
                    <li><strong>Datos ficticios pre-cargados:</strong> actividades, horarios, salas y usuarios listos para usar, facilitando la demostración y prueba de la aplicación sin necesidad de configuración inicial extensa.</li>
                    <li><strong>Dark mode y diseño responsivo:</strong> optimizado para tablets y móviles, con un modo oscuro que reduce la fatiga visual y mejora la experiencia en entornos con poca iluminación.</li>
                    <li><strong>Tests automáticos:</strong> unitarios y funcionales para garantizar calidad del código y evitar bugs en producción, utilizando PHPUnit para ejecutar pruebas que cubren escenarios críticos de la aplicación.</li>
                </ul>
            </section>

            <!-- Roles -->
            <section class="my-12">
                <h2 class="text-2xl font-semibold mb-4">🛡️ Roles y permisos</h2>
                <p class="mb-4">El sistema de gestión de roles y permisos en Olympus Gym es uno de sus pilares fundamentales, proporcionando un control de acceso granular y seguro que protege la integridad de los datos y las operaciones del gimnasio. Utilizando el potente paquete Spatie Laravel Permission, la aplicación define tres roles principales: Super-admin, Admin y Member, cada uno con un conjunto específico de permisos que se alinean con sus responsabilidades y necesidades. Este enfoque asegura que los usuarios solo puedan acceder y modificar la información relevante para su rol, previniendo errores accidentales y posibles brechas de seguridad. Los permisos se asignan de manera precisa, permitiendo acciones como crear, editar, eliminar y ver recursos específicos, mientras que se restringen otras para mantener la separación de responsabilidades. Esta estructura no solo mejora la seguridad, sino que también facilita la administración del gimnasio al delegar tareas apropiadas a cada rol. A continuación, se detallan exhaustivamente los roles definidos en el sistema y sus respectivos permisos:</p>
                <ul class="pl-6 space-y-6">
                    <li><strong>Super-admin:</strong>
                        <ul class="pl-6 space-y-3">
                            <li>✅<spam
                                class="font-semibold underline">Puede:
                                </spam>
                                <ul class="pl-6">
                                    <li>Ver, crear, editar y eliminar: usuarios, Roles.</li>
                                    <li>Ver estadísticas de las suscripciones y descargar reportes.</li>
                                </ul>
                            </li>
                            <li>❌<spam
                                class="font-semibold underline">No Puede:</spam>
                                <ul class="pl-6">
                                    <li>Gestionar la suscripción de un usuario ageno al suyo.</li>
                                    <li>gestionar el perfil de un usuario ageno al suyo.</li>
                                    <li>Ver, crear, editar y eliminar: salas y actividades, ademas de horarios para las actividades.</li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <img src="{{ asset('img/introduction/edit-role-superadmin.webp') }}" alt="lista de permisos del rol super-admin">
                    <li><strong>Admin:</strong>
                        <ul class="pl-6 space-y-3">
                            <li>✅<spam
                                class="font-semibold underline">Puede:
                                </spam>
                                <ul class="pl-6 space-y-3">
                                    <li>Ver estadísticas de las suscripciones y descargar reportes.</li>
                                    <li>Ver: usuarios y Roles.</li>
                                    <li>Ver, crear, editar y eliminar: salas y actividades, ademas de horarios para las actividades.</li>
                                </ul>
                            </li>
                            <li>❌<spam
                                class="font-semibold underline">No Puede:</spam>
                                <ul class="pl-6">
                                    <li>Crear, editar y eliminar: usuarios y Roles.</li>
                                    <li>Gestionar la suscripción de un usuario ageno al suyo.</li>
                                    <li>gestionar el perfil de un usuario ageno al suyo.</li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <img src="{{ asset('img/introduction/edit-role-admin.webp') }}" alt="lista de permisos del rol admin">
                    <li><strong>Member:</strong>
                        <ul class="pl-6 space-y-3">
                            <li>✅<spam
                                class="font-semibold underline">Puede:
                                </spam>
                                <ul class="pl-6">
                                    <li>Ver su dashboard y gestionarlo.</li>
                                    <li>Ver las vistas: Instalaciones, Servicios, Contacto.</li>
                                    <li>Inscribirse a una actividad.</li>
                                    <li>Ver y gestionar su perfil.</li>
                                    <li>Ver y gestionar su suscripcion.</li>
                                </ul>
                            </li>
                            <li>❌<spam
                                class="font-semibold underline">No Puede:</spam>
                                <ul class="pl-6">
                                    <li>Ver, crear, editar y eliminar: usuarios y Roles.</li>
                                    <li>Crear, editar y eliminar: salas y actividades, ademas de horarios para las actividades.</li>
                                    <li>Gestionar la suscripción de un usuario ageno al suyo.</li>
                                    <li>gestionar el perfil de un usuario ageno al suyo.</li>
                                    <li>Ver estadísticas de las suscripciones y descargar reportes.</li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <img src="{{ asset('img/introduction/edit-role-member.webp') }}" alt="lista de permisos del rol socio/miembro">
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
                <p class="mb-4">Uno de los aspectos más destacados de Olympus Gym es su sistema avanzado de estadísticas dinámicas, implementado mediante la poderosa librería JS-Tabulator. Esta funcionalidad proporciona visualizaciones de datos interactivas y personalizadas que se adaptan automáticamente según el rol del usuario, ofreciendo insights valiosos para la toma de decisiones. Las estadísticas no solo presentan información de manera atractiva, sino que permiten filtrar, ordenar y exportar datos en tiempo real, facilitando el análisis profundo del rendimiento del gimnasio. La integración de Tabulator con Laravel y Livewire asegura una experiencia fluida y responsiva, donde los datos se actualizan dinámicamente sin necesidad de recargar la página. Este enfoque moderno de presentación de datos transforma números crudos en información accionable, ayudando tanto a los miembros a seguir su progreso personal como a los administradores a optimizar las operaciones del gimnasio. A continuación, se detallan las estadísticas disponibles según el rol del usuario:</p>
                <ul class="pl-6 mt-4 space-y-2">
                    <li><strong>Member (socio):</strong> podrá visualizar en su dashboard estadísticas personalizadas sobre las clases asistidas, el porcentaje de participación en cada una de ellas y su progreso a lo largo del tiempo, incluyendo gráficos de barras para asistencia mensual y tendencias de participación.</li>
                    <li><strong>Admin / Superadmin:</strong> dispondrán de estadísticas globales del gimnasio, como el número de miembros activos según su tipo de cuota, altas y bajas del año, así como miembros activos por rango de edad, con posibilidad de drill-down para análisis detallados.</li>
                </ul>
                <p class="mt-4">Estas estadísticas permiten un análisis claro y visual, mejorando la experiencia del socio y facilitando la gestión estratégica para los administradores. La implementación utiliza técnicas avanzadas de visualización de datos, incluyendo colores dinámicos, tooltips informativos y opciones de exportación a CSV o PDF, asegurando que la información sea no solo accesible sino también profesional y fácil de compartir.</p>
                <img src="{{asset('img/introduction/member-stats.webp')}}" alt="estadisticas personales del socio/miembro" class="my-4">
                <img src="{{asset('img/introduction/admin-stats.webp')}}" alt="estadisticas del gimnasio" class="my-4">
            </section>

            <!-- Tests -->
            <section class="my-12">
                <h2 class="text-2xl font-semibold mb-4">🧪 Tests</h2>
                <p class="mb-4">La calidad y fiabilidad de Olympus Gym se garantiza mediante una suite completa de tests automáticos que cubren tanto pruebas unitarias como funcionales. Esta práctica de desarrollo orientado a pruebas (TDD/BDD) asegura que cada componente de la aplicación funcione correctamente y que las nuevas funcionalidades no rompan el código existente. Los tests se ejecutan utilizando PHPUnit, el framework estándar para testing en PHP, y se integran perfectamente con el flujo de desarrollo de Laravel. Cada test está diseñado para validar escenarios específicos, desde la autenticación de usuarios hasta la gestión compleja de reservas de actividades, garantizando que la aplicación mantenga su integridad bajo diferentes condiciones. La automatización de estos tests permite detectar errores temprano en el ciclo de desarrollo, reduciendo significativamente los bugs en producción y facilitando el mantenimiento a largo plazo. A continuación, se muestra cómo ejecutar los tests:</p>
                <pre class="bg-gray-800 text-white p-3 rounded mt-3"><code>php artisan test</code></pre>
                <p class="mt-2">Los tests cubren registro, gestión de actividades, reservas, roles y permisos, incluyendo pruebas de integración con la base de datos, validaciones de formularios, y comportamientos específicos de cada rol de usuario. Esta cobertura exhaustiva asegura que Olympus Gym no solo funcione correctamente en el momento del despliegue, sino que mantenga su calidad a través de futuras actualizaciones y expansiones.</p>
            </section>

            <!-- Datos ficticios -->
            <section class="my-12">
                <h2 class="text-2xl font-semibold mb-4">📦 Datos ficticios</h2>
                <p class="mb-4">Para facilitar la demostración y el desarrollo de Olympus Gym, la aplicación incluye un sistema robusto de datos ficticios que se cargan automáticamente en la base de datos. Utilizando las poderosas migraciones y seeders de Laravel, se crean datos realistas que representan un gimnasio completamente operativo, incluyendo actividades variadas como yoga, spinning y crossfit, horarios flexibles que cubren diferentes franjas horarias, salas equipadas con diferentes capacidades y tipos de instalaciones, y usuarios con roles diversos que permiten probar todas las funcionalidades del sistema. Estos datos no solo sirven para propósitos de desarrollo y testing, sino que también proporcionan una experiencia inmediata para nuevos usuarios que desean explorar la aplicación sin necesidad de configuración manual. La implementación utiliza factories de Laravel para generar datos coherentes y variados, asegurando que las relaciones entre entidades (como usuarios inscritos en actividades) sean lógicas y realistas. Este enfoque acelera significativamente el proceso de onboarding y permite a los desarrolladores y testers trabajar con un entorno rico en datos desde el primer momento.</p>
                <img src="{{ asset('img/introduction/activities-schedule.webp') }}" alt="horarios de las actividades del gimnasio" class="my-4">
            </section>

            <!-- Registro y acceso -->
            <section class="my-12">
                <h2 class="text-2xl font-semibold mb-4">📲 Registro y acceso</h2>
                <p class="mb-4">Olympus Gym ofrece múltiples vías seguras y convenientes para que los usuarios se registren y accedan a la plataforma, combinando métodos tradicionales con autenticación social moderna. Este enfoque flexible asegura que tanto usuarios técnicos como no técnicos puedan unirse fácilmente al gimnasio digital, mientras se mantiene un alto nivel de seguridad y verificación. La implementación utiliza Laravel Fortify para la gestión de autenticación, complementado con paquetes adicionales para la integración con proveedores sociales. Cada método de registro incluye validaciones robustas para prevenir fraudes y asegurar la integridad de los datos, mientras que el proceso de acceso es rápido y sin fricciones. A continuación, se detallan las opciones disponibles para registro y acceso:</p>
                <ul class="pl-6">
                    <li><strong>Formulario de registro:</strong> completa tus datos personales, incluyendo nombre, email y contraseña segura, además de aceptar la política de privacidad y términos de servicio para asegurar el cumplimiento legal.</li>
                    <li><strong>Acceso con GitHub o Google:</strong> inicia sesión rápidamente con tus credenciales sociales, aprovechando OAuth 2.0 para una autenticación segura y sin necesidad de recordar contraseñas adicionales.</li>
                </ul>
                <img src="{{ asset('img/introduction/log-in.webp') }}" alt="login mediante password, github o google">
            </section>

            <!-- Contacto -->
            <section class="my-12 text-center">
                <h2 class="text-2xl font-semibold mb-4">📞 Contacto del desarrollador</h2>
                <p class="mb-4">Para consultas, soporte técnico, colaboraciones o cualquier pregunta relacionada con Olympus Gym, no dudes en contactar al desarrollador principal. Diego Chacon Delgado es un apasionado desarrollador web full-stack especializado en tecnologías PHP y JavaScript, con experiencia en el desarrollo de aplicaciones empresariales escalables. Su enfoque combina buenas prácticas de desarrollo con innovación tecnológica, resultando en soluciones robustas y user-friendly. El proyecto Olympus Gym representa su compromiso con la comunidad open-source y su interés en crear herramientas útiles para la gestión de negocios locales. Si encuentras algún issue, tienes sugerencias de mejora, o simplemente quieres discutir sobre desarrollo web, el contacto está abierto para cualquier tipo de comunicación constructiva.</p>
                <p><strong>Dev:</strong> Diego Chacon Delgado</p>
                <p><strong>Email:</strong> info@diegochacondev.es</p>
            </section>

            <p class=" text-xl font-bold text-center mt-10">¡Gracias por probar Olympus Gym! 💪</p>
        </section>
    </x-slot>
</x-layouts.app.general>
