@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto py-12 px-6 space-y-12">

        <!-- Блок обновлений -->
        <div class="bg-white shadow-xl rounded-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Обновления платформы</h2>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-5 rounded-lg">
                <p class="text-gray-900 mb-3">
                    ⚡ Важное обновление: <span class="font-semibold text-yellow-700">AI в чате стал отвечать в несколько раз быстрее!</span>
                    Теперь вы получаете помощь почти мгновенно.
                </p>
                <p class="text-gray-800 mb-3">
                    Используйте это преимущество — задавайте больше вопросов и проверяйте свои решения.
                    Чем активнее вы общаетесь с AI, тем лучше закрепляете знания.
                </p>
                <p class="text-yellow-700 text-sm font-semibold italic">
                    Попробуйте прямо сейчас — спросите что-нибудь и почувствуйте скорость!
                </p>
            </div>
        </div>

        <!-- Приветственный блок -->
        <div class="bg-white shadow-xl rounded-2xl p-10">
            <h2 class="text-3xl font-extrabold text-indigo-600 mb-6">Добро пожаловать на образовательную платформу!</h2>
            <p class="text-gray-700 text-lg leading-relaxed mb-4">
                Эта платформа предназначена <span class="font-semibold text-indigo-500">исключительно для учеников нашей школы</span>.
                Её основная цель — помочь вам учиться по-новому: вместо поиска готовых ответов в интернете,
                вы получаете <span class="font-semibold">подсказки и объяснения</span> от встроенного AI-агента.
            </p>

            <p class="text-gray-700 text-lg leading-relaxed mb-4">
                Помните: <span class="italic text-indigo-600">обучение — это инвестиция в ваше будущее</span>.
                Никто не знает, где и когда пригодятся полученные знания, но пока у вас есть возможность
                — используйте её на максимум.
            </p>

            <p class="text-gray-700 text-lg leading-relaxed mb-6">
                Если при решении задач у вас возникнут трудности — не стесняйтесь обращаться за помощью в чат.
            </p>

            <div class="bg-indigo-50 border-l-4 border-indigo-400 p-5 rounded-lg mb-6">
                <h3 class="text-indigo-700 font-semibold mb-3 flex items-center">
                    Важно знать:
                </h3>
                <ul class="list-disc list-inside text-gray-700 space-y-2">
                    <li>Задачи с прикреплёнными файлами и картинками агент пока <span class="font-semibold">не умеет читать</span>. В таких случаях нужно объяснить ему суть задания текстом.</li>
                    <li>Текстовые задачи агент понимает полностью.</li>
                    <li>Со временем количество предметов будет расширяться.</li>
                </ul>
            </div>

            <p class="text-gray-600 text-base">
                <span class="font-medium text-indigo-500">Проект сейчас находится в разработке</span>,
                поэтому возможны небольшие ограничения, но мы активно работаем над улучшением платформы.
            </p>
        </div>

        <!-- Блок обратной связи -->
        <div class="bg-white shadow-xl rounded-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Обратная связь</h2>
            <p class="mb-6 text-gray-600">
                Ваше мнение важно для нас! Расскажите о проблеме или предложите идею —
                это поможет нам сделать платформу лучше.
            </p>

            @if(session('thanks'))
                <div class="bg-green-100 text-green-800 p-3 rounded-lg mb-4 shadow">
                    {{ session('thanks') }}
                </div>
            @endif

            <form action="{{ route('feedback') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                        Сообщение
                    </label>
                    <textarea name="message" id="message" rows="4" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>

                <div>
                    <button type="submit"
                            class="w-full inline-flex justify-center py-3 px-6 text-lg font-semibold rounded-xl shadow-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        ✉️ Отправить
                    </button>
                </div>
            </form>
        </div>


    </div>
@endsection
