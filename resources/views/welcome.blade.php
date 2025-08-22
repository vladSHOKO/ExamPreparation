@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto py-12 px-6">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">
                Добро пожаловать в <span class="text-indigo-600">Exam</span>Preparation
            </h1>

            <p class="text-gray-600 text-lg leading-relaxed mb-6">
                Это образовательная платформа, созданная для моих учеников внутри школы.
                Здесь вы сможете выполнять задания, получать домашние работы и улучшать свои знания.
                Все результаты автоматически сохраняются, и я смогу отслеживать ваш прогресс,
                чтобы давать рекомендации и помогать вам готовиться к экзаменам.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-gray-50 rounded-lg p-6 shadow hover:shadow-lg transition">
                    <h2 class="text-xl font-semibold text-indigo-600 mb-2">Задания</h2>
                    <p class="text-gray-600 text-sm">
                        Выполняйте задачи и практические упражнения, чтобы закрепить изученный материал.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6 shadow hover:shadow-lg transition">
                    <h2 class="text-xl font-semibold text-indigo-600 mb-2">Домашние работы</h2>
                    <p class="text-gray-600 text-sm">
                        Получайте задания и отправляйте ответы прямо в системе.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6 shadow hover:shadow-lg transition">
                    <h2 class="text-xl font-semibold text-indigo-600 mb-2">Анализ прогресса</h2>
                    <p class="text-gray-600 text-sm">
                        Я вижу ваш прогресс и могу давать рекомендации по темам, которые нужно подтянуть.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto mt-10 bg-white shadow-md rounded-lg p-6">
        @if(session('thanks'))
            <div class="text-green-600 font-medium mb-4">
                {{ session('thanks') }}
            </div>
        @endif
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Обратная связь</h2>
        <p class="text-gray-600 mb-6">Поскольку портал находится на этапе разработки, будем рады, если вы оставите комментарий, или сообщите нам об ошибках.</p>

        <form action="{{ route('feedback') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Сообщение</label>
                <textarea name="message" id="message" rows="4" required
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
            </div>

            <div>
                <button type="submit"
                        class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Отправить
                </button>
            </div>
        </form>
    </div>
@endsection
