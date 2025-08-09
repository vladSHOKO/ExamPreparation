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
@endsection
