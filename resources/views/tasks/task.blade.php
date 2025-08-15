@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto py-10 px-4">
        <div class="bg-white shadow-md rounded-lg p-6">

            @if(session('success'))
                <div class="text-green-600 font-medium mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4 text-sm text-gray-500">
                Задание №{{ $id }}
            </div>

            <div class="mb-6 text-lg font-medium text-gray-800">
                {{ $description }}
            </div>

            @foreach($files as $file)
                <div class="mb-6 text-lg font-medium text-gray-800">
                    <img src="{{ asset($file['path']) }}" alt="files">
                </div>
            @endforeach

            <form action="{{ route('checkAnswer', ['id' => $id]) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="answer" class="block text-sm font-medium text-gray-700 mb-1">
                        Ваш ответ:
                    </label>
                    <input type="text" name="answer" id="answer"
                           class="block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2"
                           required>
                </div>

                <div>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Отправить
                    </button>
                    <a href="{{ route('tasks') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Назад к заданиям
                    </a>
                </div>
            </form>

            @error('answer')
            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror

            <!-- Чат с AI -->
            <div class="mt-10">
                <h2 class="text-lg font-semibold mb-3">Чат с AI помощником</h2>
                <div class="border rounded-lg bg-gray-50 flex flex-col h-96">
                    <!-- Сообщения -->
                    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3">
                        <!-- Сообщения будут добавляться сюда JS-ом -->
                    </div>

                    <!-- Форма отправки -->
                    <form id="chat-form" class="p-3 border-t flex gap-2">
                        <input type="text" id="chat-input" placeholder="Напишите сообщение..."
                               class="flex-1 border rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Отправить
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const messagesBox = document.getElementById('chat-messages');
            const form = document.getElementById('chat-form');
            const input = document.getElementById('chat-input');

            // Вставь ID задания в JS
            const TASK_ID = {{ $id }};

            // Заглушка для теста
            let messages = [
                { sender: 'ai', message: 'Привет! Я твой помощник.' },
                { sender: 'user', message: 'Привет! Помоги с заданием.' }
            ];

            function renderMessages() {
                messagesBox.innerHTML = '';
                messages.forEach(msg => {
                    const div = document.createElement('div');
                    div.className = `flex ${msg.sender === 'user' ? 'justify-end' : 'justify-start'}`;
                    div.innerHTML = `<div class="${msg.sender === 'user'
                        ? 'bg-indigo-600 text-white'
                        : 'bg-gray-200 text-gray-900'} px-4 py-2 rounded-lg max-w-xs">${msg.message}</div>`;
                    messagesBox.appendChild(div);
                });
                messagesBox.scrollTop = messagesBox.scrollHeight;
            }

            renderMessages();

            form.addEventListener('submit', e => {
                e.preventDefault();
                const text = input.value.trim();
                if (!text) return;

                // Добавляем сообщение на фронте
                messages.push({ sender: 'user', message: text });
                renderMessages();
                input.value = '';

                // Здесь потом будет твой POST запрос на бэк
                // fetch(`/tasks/${TASK_ID}/chat/messages`, {...})
                setTimeout(() => {
                    messages.push({ sender: 'ai', message: 'Это ответ от AI (заглушка)' });
                    renderMessages();
                }, 1000);
            });
        });
    </script>
@endsection
