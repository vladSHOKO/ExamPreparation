@extends('layouts.app')

@section('content')
    <div class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Заголовок и фильтры -->
            <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Задания по информатике</h1>
                    <p class="mt-2 text-gray-600">Выберите задачу и приступайте к решению</p>
                </div>
            </div>

            <div class="mb-12 topic-section" data-topic="programming">


                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Плашка задания 1 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">Средний</span>
                                <span class="text-sm text-gray-500">10 задач</span>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Основы Python</h3>
                            <p class="text-gray-600 mb-4">Решите задачи на базовые конструкции языка Python</p>
                            <a href="#" class="inline-flex items-center text-indigo-600 hover:text-indigo-500 font-medium">
                                Начать решение
                                <svg class="ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Плашка задания 2 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Легкий</span>
                                <span class="text-sm text-gray-500">5 задач</span>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Функции в Python</h3>
                            <p class="text-gray-600 mb-4">Практика работы с функциями и возвратом значений</p>
                            <a href="#" class="inline-flex items-center text-indigo-600 hover:text-indigo-500 font-medium">
                                Начать решение
                                <svg class="ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Плашка задания 3 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Сложный</span>
                                <span class="text-sm text-gray-500">8 задач</span>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">ООП в Python</h3>
                            <p class="text-gray-600 mb-4">Задачи на классы, объекты и наследование</p>
                            <a href="#" class="inline-flex items-center text-indigo-600 hover:text-indigo-500 font-medium">
                                Начать решение
                                <svg class="ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>

    <!-- Скрипт для фильтрации по темам -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const topicFilter = document.getElementById('topic-filter');

            topicFilter.addEventListener('change', function() {
                const selectedTopic = this.value;
                const allSections = document.querySelectorAll('.topic-section');

                allSections.forEach(section => {
                    if (selectedTopic === 'all' || section.dataset.topic === selectedTopic) {
                        section.style.display = 'block';
                    } else {
                        section.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection
