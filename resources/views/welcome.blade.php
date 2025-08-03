@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Герой-секция -->
        <div class="relative bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                    <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 lg:mt-16 lg:px-8 xl:mt-20">
                        <div class="sm:text-center lg:text-left">
                            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                <span class="block text-indigo-600">ExamPreparation</span>
                                <span class="block">Ваш успех начинается здесь</span>
                            </h1>
                            <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                Платформа для эффективной подготовки к экзаменам с персональным подходом и современными методиками обучения.
                            </p>
                            <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                                <div class="rounded-md shadow">
                                    <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                                        Начать обучение
                                    </a>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-3">
                                    <a href="{{ route('login') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 md:py-4 md:text-lg md:px-10">
                                        Войти
                                    </a>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>

        <!-- Секция временной информации -->
        <div class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center">
                    <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Новости платформы</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                        Что нового?
                    </p>
                </div>

                <div class="mt-10">
                    <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                        <!-- Временная новость 1 -->
                        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-2">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <h3 class="ml-3 text-lg leading-6 font-medium text-gray-900">Запуск платформы</h3>
                            </div>
                            <p class="mt-2 text-base text-gray-500">
                                Мы рады сообщить о запуске нашей образовательной платформы! Теперь подготовка к экзаменам стала еще удобнее.
                            </p>
                            <p class="mt-3 text-sm text-gray-400">15 мая 2023</p>
                        </div>

                        <!-- Временная новость 2 -->
                        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-2">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <h3 class="ml-3 text-lg leading-6 font-medium text-gray-900">Новые материалы</h3>
                            </div>
                            <p class="mt-2 text-base text-gray-500">
                                Добавлены новые учебные материалы по математике и физике. Теперь доступно более 500 тестовых заданий.
                            </p>
                            <p class="mt-3 text-sm text-gray-400">20 мая 2023</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Секция статистики -->
        <div class="bg-indigo-700">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8 lg:py-20">
                <div class="max-w-4xl mx-auto text-center">
                    <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                        <span class="block">Нас уже выбирают</span>
                    </h2>
                </div>
                <div class="mt-10 text-center sm:max-w-3xl sm:mx-auto sm:grid sm:grid-cols-3 sm:gap-8">
                    <div>
                        <p class="text-5xl font-extrabold text-white">1,200+</p>
                        <p class="mt-2 text-base font-medium text-indigo-200">Пользователей</p>
                    </div>
                    <div class="mt-10 sm:mt-0">
                        <p class="text-5xl font-extrabold text-white">500+</p>
                        <p class="mt-2 text-base font-medium text-indigo-200">Тестовых заданий</p>
                    </div>
                    <div class="mt-10 sm:mt-0">
                        <p class="text-5xl font-extrabold text-white">50+</p>
                        <p class="mt-2 text-base font-medium text-indigo-200">Учебных материалов</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Секция призыва к действию -->
        <div class="bg-white">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    <span class="block">Готовы начать?</span>
                    <span class="block text-indigo-600">Зарегистрируйтесь прямо сейчас.</span>
                </h2>
                <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                    <div class="inline-flex rounded-md shadow">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Создать аккаунт
                        </a>
                    </div>
                    <div class="ml-3 inline-flex rounded-md shadow">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                            Войти
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
