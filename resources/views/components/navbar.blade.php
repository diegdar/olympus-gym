<header class="relative bg-white shadow-xl dark:bg-bg_darkMode dark:text-white ring-1 ring-gray-900/5">
    {{-- navbar --}}
    <nav class="px-4 py-4 sm:flex sm:items-center">
        {{-- logo and menu button --}}
        <section class="flex justify-between">
            {{-- logo --}}
            <img src="/img/logo.svg" class="h-6" alt="Tailwind Play" />
            {{-- button menu --}}
            <button id="menu-toggle" class="text-gray-700 sm:hidden">
                <svg class="w-6 h-6 fill-current" xmlns="http://www.w3.org/2000/svg" width="100" height="100"
                    viewBox="0 0 50 50">
                    <path
                        d="M 0 7.5 L 0 12.5 L 50 12.5 L 50 7.5 Z M 0 22.5 L 0 27.5 L 50 27.5 L 50 22.5 Z M 0 37.5 L 0 42.5 L 50 42.5 L 50 37.5 Z">
                    </path>
                </svg>
            </button>
        </section>
        {{-- nav-links --}}
        <div id="nav-links" 
            class="sm:flex sm:justify-between sm:m-0 w-full hidden">
            {{-- web-links --}}
            <div class="sm:flex sm:flex-row sm:items-center">
                <article
                    class="text-gray-600 dark:text-white hover:bg-gray-200 w-full text-left px-2 rounded hover:text-gray-900">
                    Blog
                </article>
                <article
                    class="text-gray-600 dark:text-white hover:bg-gray-200 w-full text-left px-2 rounded hover:text-gray-900">
                    Docs
                </article>
                <article
                    class="text-gray-600 dark:text-white hover:bg-gray-200 w-full text-left px-2 rounded hover:text-gray-900">
                    Contacto
                </article>
            </div>
            {{-- auth-links --}}
            <div id="nav-auth" class="self-end flex justify-end">
                @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                    Register
                                </a>
                            @endif
                        @endauth
                @endif
            </div>
        </div>
    </nav>
</header>