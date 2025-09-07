@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto py-10 px-6"> <!-- увеличили ширину контейнера -->

        <div class="bg-white shadow-md rounded-lg p-8"> <!-- блок задания -->
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
                    <img src="{{ asset($file['path']) }}" alt="files" class="w-full rounded">
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

                <div class="flex gap-2">
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
                <div class="border rounded-lg bg-gray-50 flex flex-col h-96 w-full"> <!-- сделали шире -->
                    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3">
                        <!-- Сообщения будут добавляться JS-ом -->
                    </div>

                    <form id="chat-form" class="p-3 border-t flex gap-2 w-full">
                        <meta name="csrf-token" content="{{ csrf_token() }}">
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
            const form = document.querySelector('#chat-form');
            const input = document.getElementById('chat-input');
            const loaderDiv = '<div id="chat-loader" class="flex items-center space-x-2 p-2"> <div class="w-2 h-2 bg-indigo-600 rounded-full animate-bounce"></div> <div class="w-2 h-2 bg-indigo-600 rounded-full animate-bounce [animation-delay:-.2s]"></div> <div class="w-2 h-2 bg-indigo-600 rounded-full animate-bounce [animation-delay:-.4s]"></div> </div>';

            const TASK_ID = {{ $id }};

            function scrollChatToBottom() {
                messagesBox.scrollTo({
                    top: messagesBox.scrollHeight,
                    behavior: "smooth"
                });
            }

            function showLoader() {
                let div = document.createElement('div');
                div.innerHTML = loaderDiv;
                messagesBox.appendChild(div);
            }

            function deleteLoader() {
                let div = document.getElementById('chat-loader');
                div.closest('div').remove();
            }

            async function getMessages() {
                return await fetch("{{route('getMessages', [$id])}}").then(response => response.json());
            }

            function createDivInBoxByMessage(msg){
                const div = document.createElement('div');
                div.className = `flex ${msg.role === 'user' ? 'justify-end' : 'justify-start'}`;
                div.innerHTML = `<div class="${msg.role === 'user'
                    ? 'bg-indigo-600 text-white'
                    : 'bg-gray-200 text-gray-900'} px-4 py-2 rounded-lg max-w-full whitespace-pre-wrap">${msg.content}</div>`; // сделали ширину max-full и перенос строк
                messagesBox.appendChild(div);
                scrollChatToBottom();
            }

            async function renderMessages() {
                messagesBox.innerHTML = '';
                showLoader();
                let messages = await getMessages();

                messages.forEach(msg => {
                    createDivInBoxByMessage(msg);
                });
                messagesBox.scrollTop = messagesBox.scrollHeight;
                deleteLoader();
            }

            renderMessages();

            form.addEventListener('submit', e => {
                e.preventDefault();
                const text = input.value.trim();
                if (!text) return;

                let message = { role: 'user', content: text };
                createDivInBoxByMessage(message);
                input.value = '';

                showLoader();
                fetch("{{route('postMessage', [$id])}}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: "same-origin",
                    body: JSON.stringify(message)
                })
                    .then(result => result.json())
                    .then(data => {
                        deleteLoader();
                        createDivInBoxByMessage(data);
                    });
            });
        });
    </script>
@endsection
