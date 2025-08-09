@extends('layouts.app')

@section('content')
    <div class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Задания по информатике</h1>
                    <p class="mt-2 text-gray-600">Выберите задачу и приступайте к решению</p>
                </div>
            </div>
            <div class="mb-12 topic-section" data-topic="programming">


                <div class="space-y-4"> {{-- Общий вертикальный контейнер для всех типов --}}
                    @foreach($result as $type => $tasks)
                        <div> {{-- Блок одного типа задач --}}
                            <p class="text-gray-800 font-semibold mb-2">Задачи типа {{$type}}</p>

                            <div class="flex flex-wrap gap-1">
                                @foreach($tasks as $task)
                                    <a href="{{ url('/tasks/' . $task['id']) }}">
                                        <div class="w-10 h-10 flex items-center justify-center
                            rounded-lg shadow-md text-sm font-medium
                            {{ $task['status'] === 'completed' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-white text-gray-800 border border-gray-300' }}">
                                            {{ $task['id'] }}
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>


        </div>
    </div>
@endsection
