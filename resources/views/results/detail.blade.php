@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            <!-- Левая колонка -->
            <div class="bg-white shadow rounded-lg p-6 md:col-span-1">
                <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">
                    Информация о задаче
                </h2>

                <div class="space-y-3 text-sm text-gray-700">
                    <div>
                        <span class="text-gray-500 block">ID задачи:</span>
                        <span class="font-medium">{{ $task['id'] }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 block">Описание:</span>
                        <p class="mt-1">{{ $task['description'] }}</p>
                    </div>

                    @if(!empty($taskSession['created_at']))
                        <div>
                            <span class="text-gray-500 block">Создана:</span>
                            <span class="font-medium">{{ $taskSession['created_at'] }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Правая колонка -->
            <div class="bg-white shadow rounded-lg p-6 md:col-span-3 flex flex-col">
                <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">
                    История чата с AI агентом
                </h2>

                <div class="flex-1 overflow-y-auto pr-2" style="max-height: 70vh;">
                    @forelse($messages as $message)
                        <div class="mb-4">
                            <div class="flex items-center space-x-2 mb-1">
                            <span class="font-semibold {{ $message['role'] === 'assistant' ? 'text-indigo-600' : 'text-green-600' }}">
                                {{ $message['role'] === 'assistant' ? 'AI агент' : 'Пользователь' }}
                            </span>
                                <small class="text-gray-400">{{ $message['created_at'] }}</small>
                            </div>
                            <div class="rounded-lg px-4 py-2 text-sm leading-relaxed
                            {{ $message['role'] === 'assistant' ? 'bg-indigo-50 text-indigo-800 border border-indigo-200' : 'bg-green-50 text-green-800 border border-green-200' }}">
                                {{ $message['content'] }}
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 italic">История чата пуста.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
