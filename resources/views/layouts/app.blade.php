<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Tailwind CSS (если используете CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased">
<header>
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @auth
                <div class="flex justify-between h-16">
                    <!-- Логотип и основные ссылки -->
                    @auth
                        <div class="flex items-center">
                            <a href="{{ route('login') }}" class="flex-shrink-0 flex items-center">
                                <span class="text-xl font-bold text-indigo-600">Exam</span>
                                <span class="text-xl font-bold text-gray-800">Preparation</span>
                            </a>
                            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">

                                <a href="{{ route('welcome') }}"
                                   class="{{ request()->routeIs('welcome') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Главная
                                </a>
                                @if(request()->user()->role === 'student')
                                    <a href="{{ route('tasks') }}"
                                       class="{{ request()->routeIs('tasks') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                        Задания
                                    </a>
                                @endif
                                @if (request()->user()->role === 'teacher')
                                    <a href="{{ route('register') }}"
                                       class="{{ request()->routeIs('register') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                        Добавить ученика
                                    </a>

                                    <a href="{{ route('showLoadForm') }}"
                                       class="{{ request()->routeIs('showLoadForm') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                        Добавить Задачу
                                    </a>
                                @endif

                            </div>
                        </div>
                    @endauth
                    <!-- Правая часть (авторизация/профиль) -->
                    @auth
                        <div class="hidden sm:ml-6 sm:flex sm:items-center">
                            <!-- Меню пользователя -->
                            <div class="ml-3 relative">
                                <div>
                                    <button type="button"
                                            class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            id="user-menu-button"
                                            aria-expanded="false"
                                            aria-haspopup="true">
                                        <span class="sr-only">Открыть меню</span>
                                        <span
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100">
                                    <span class="text-sm font-medium text-indigo-600">
                                        {{ strtoupper(substr(auth()->user()->login, 0, 1)) }}
                                    </span>
                                </span>
                                    </button>
                                </div>

                                <!-- Выпадающее меню -->
                                <div
                                    class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                                    role="menu"
                                    id="user-menu">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                role="menuitem">
                                            Выйти
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endauth


                    <!-- Мобильное меню (кнопка) -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button type="button"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                aria-controls="mobile-menu"
                                aria-expanded="false"
                                onclick="toggleMobileMenu()">
                            <span class="sr-only">Открыть меню</span>
                            <!-- Иконка меню -->
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            <!-- Иконка закрытия -->
                            <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endauth
        </div>

        <!-- Мобильное меню (контент) -->
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ route('login') }}"
                   class="bg-indigo-50 border-indigo-500 text-indigo-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Главная
                </a>
                <a href="{{ route('tasks') }}"
                   class="border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Задачи
                </a>
            </div>
            @auth
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="mt-3 space-y-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100 w-full text-left">
                                Выйти
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </nav>

    <script>
        // Функция для мобильного меню
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const buttons = document.querySelectorAll('[aria-controls="mobile-menu"] svg');

            menu.classList.toggle('hidden');
            buttons.forEach(button => button.classList.toggle('hidden'));
        }

        // Функция для выпадающего меню пользователя
        document.getElementById('user-menu-button').addEventListener('click', function () {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</header>
<div class="min-h-screen bg-gray-100">
    <!-- Page Content -->
    <main>
        @yield('content')
    </main>
</div>
</body>
</html>
