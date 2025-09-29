<header
    class="sticky top-0  bg-[#757575] darK:bg-bg_darkMode shadow-xl dark:bg-bg_darkMode text-white ring-1 ring-gray-900/5 z-50 opacity-80">
    {{-- navbar --}}
    <nav class="px-4 py-4 sm:flex sm:items-center">
        {{-- my-web-logo and button menu-toggle --}}
        <section class="flex justify-between">
            {{-- my web-icon --}}
            <div class="">
                <a href="https://diegochacondev.es" target="_blank">
                    <img src="{{ asset('img/logos/my-web-logo.webp') }}" class="w-[50px] h-[40px]" alt="logo Diego Chacon que redirige a su sitio web" title="Ir a portfolio Diego Chacon" />
                </a>
            </div>
            {{-- button menu-toggle --}}
            <button id="menu-toggle" class="text-gray-700 sm:hidden cursor-pointer">
                <svg class="w-6 h-6 fill-current" xmlns="http://www.w3.org/2000/svg" width="100" height="100"
                    viewBox="0 0 50 50">
                    <path
                        d="M 0 7.5 L 0 12.5 L 50 12.5 L 50 7.5 Z M 0 22.5 L 0 27.5 L 50 27.5 L 50 22.5 Z M 0 37.5 L 0 42.5 L 50 42.5 L 50 37.5 Z">
                    </path>
                </svg>
            </button>
        </section>
        {{-- nav-links --}}
        <div id="nav-links" class="sm:flex w-full sm:m-0 hidden">
            {{-- web-links --}}
            <div class="sm:flex justify-around sm:flex-row w-full gap-4 text-xl sm:text-lg md:text-xl lg:text-2xl xl:text-3xl fond-bold">
                <article
                    class="flex sm:justify-center w-auto pt-3 border-b-3 hover:border-yellow-200 transition-colors duration-300 {{ request()->routeIs('app-info') ? 'border-[#c8a27a]' : 'border-transparent hover:border-yellow-500' }}">
                    <a href="{{ route('app-info') }}">
                        Introduccion
                    </a>
                </article>
                <article
                    class="flex sm:justify-center w-auto pt-3 border-b-3 hover:border-yellow-200 transition-colors duration-300 {{ request()->routeIs('home') ? 'border-[#c8a27a]' : 'border-transparent hover:border-yellow-500' }}">
                    <a href="{{ route('home') }}">
                        Inicio
                    </a>
                </article>
                <article
                    class="flex sm:justify-center w-auto pt-3 border-b-3 hover:border-yellow-200 transition-colors duration-300 {{ request()->routeIs('facilities') ? 'border-[#c8a27a]' : 'border-transparent hover:border-yellow-500' }}">
                    <a href="{{ route('facilities') }}">
                        Instalaciones
                    </a>
                </article>
                <article
                    class="flex sm:justify-center w-auto pt-3 border-b-3 hover:border-yellow-200 transition-colors duration-300 {{ request()->routeIs('services') ? 'border-[#c8a27a]' : 'border-transparent hover:border-yellow-500' }}">
                    <a href="{{ route('services') }}">
                        Servicios
                    </a>
                </article>
                <article
                    class="flex sm:justify-center w-auto pt-3 border-b-3 hover:border-yellow-200 transition-colors duration-300 {{ request()->routeIs('contact') ? 'border-[#c8a27a]' : 'border-transparent hover:border-yellow-500' }}">
                    <a href="{{ route('contact') }}">
                        Contacto
                    </a>
                </article>
            </div>
            {{-- auth-links --}}
            <div id="nav-auth" class=" w-auto min-w-[190px] self-end flex justify-end items-center gap-2">
                @if (Route::has('login'))
                    @auth
                        @if (auth()->user()->hasRole('member'))
                            <a href="{{ url('/dashboard') }}"
                                class="text-2xl inline-block px-3 py-1.5 dark:text-[#EDEDEC] border-3 hover:hover:border-yellow-400  dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm leading-normal">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ url('/admin/subscriptions/stats') }}"
                                class=" text-2xl inline-block px-3 py-1.5 dark:text-[#EDEDEC] border-3 hover:hover:border-yellow-400  dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm leading-normal">
                                Panel
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-3 border-[#3E3E3A] hover:border-yellow-400 transition-colors duration-300 rounded-sm text-sm">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-3 border-[#3E3E3A] hover:border-yellow-400 transition-colors duration-300 rounded-sm text-sm">
                                Register
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>
    <div class="h-0.5 bg-gray-500 mx-4"></div>
</header>
