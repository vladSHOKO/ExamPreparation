@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Форма фильтрации --}}
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Фильтрация результатов</h2>
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                {{-- Поле для логина --}}
                <div>
                    <label for="login" class="block text-sm font-medium text-gray-700 mb-1">
                        Логин ученика
                    </label>
                    <input type="text"
                           id="login"
                           name="login"
                           placeholder="Введите логин"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                  focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                {{-- Поле для номера класса --}}
                <div>
                    <label for="class_number" class="block text-sm font-medium text-gray-700 mb-1">
                        Номер класса
                    </label>
                    <input type="text"
                           id="class_number"
                           name="class_number"
                           placeholder="Введите номер класса"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                  focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                {{-- Кнопки действий --}}
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2
                                   bg-indigo-600 border border-transparent rounded-md
                                   font-semibold text-white hover:bg-indigo-700
                                   focus:outline-none focus:ring-2 focus:ring-offset-2
                                   focus:ring-indigo-500">
                        Применить фильтр
                    </button>
                    <button type="button"
                            id="resetFilter"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2
                                   bg-gray-600 border border-transparent rounded-md
                                   font-semibold text-white hover:bg-gray-700
                                   focus:outline-none focus:ring-2 focus:ring-offset-2
                                   focus:ring-gray-500">
                        Сбросить
                    </button>
                </div>
            </form>
        </div>

        {{-- Контейнер для таблицы результатов --}}
        <div id="resultsContainer" class="overflow-x-auto">
            @include('results.partials.results-table', ['students' => $students])
        </div>

        {{-- Индикатор загрузки --}}
        <div id="loadingIndicator" class="hidden text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <p class="mt-2 text-gray-600">Загрузка...</p>
        </div>

        {{-- Сообщение об отсутствии результатов --}}
        <div id="noResults" class="hidden text-center py-8">
            <p class="text-gray-600 text-lg">Результаты не найдены</p>
        </div>
    </div>

    <script>
        const filterForm = document.getElementById('filterForm');
        const resetFilterBtn = document.getElementById('resetFilter');
        const resultsContainer = document.getElementById('resultsContainer');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const noResults = document.getElementById('noResults');
        const apiEndpoint = '{{ route('showFilterResults') }}';
        const detailRouteBase = '{{ route("showDetail", 0) }}'.replace('/0', '');

        // Обработка отправки формы фильтрации
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            applyFilter();
        });

        // Обработка сброса фильтра
        resetFilterBtn.addEventListener('click', function() {
            document.getElementById('login').value = '';
            document.getElementById('class_number').value = '';
            applyFilter();
        });

        // Функция применения фильтра
        function applyFilter() {
            const login = document.getElementById('login').value.trim();
            const classNumber = document.getElementById('class_number').value.trim();

            // Построение URL с параметрами
            const params = new URLSearchParams();
            if (login) {
                params.append('login', login);
            }
            if (classNumber) {
                params.append('class_number', classNumber);
            }

            const url = apiEndpoint + (params.toString() ? '?' + params.toString() : '');

            // Показываем индикатор загрузки
            showLoading();

            // Получение CSRF токена
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfInput = document.querySelector('input[name="_token"]');
            const csrfToken = (csrfMeta && csrfMeta.content) || (csrfInput && csrfInput.value) || '';

            // Отправка запроса
            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка при загрузке данных');
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                updateResultsTable(data);
            })
            .catch(error => {
                hideLoading();
                console.error('Ошибка:', error);
                alert('Произошла ошибка при загрузке результатов. Попробуйте еще раз.');
            });
        }

        // Функция обновления таблицы результатов
        function updateResultsTable(students) {
            if (!students || Object.keys(students).length === 0) {
                resultsContainer.classList.add('hidden');
                noResults.classList.remove('hidden');
                return;
            }

            noResults.classList.add('hidden');
            resultsContainer.classList.remove('hidden');

            // Генерируем HTML для таблицы
            let tableHTML = `
                <table class="min-w-full border border-gray-200 rounded-lg shadow">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left font-semibold text-gray-700 border-b border-gray-200">
                                Имя ученика
                            </th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700 border-b border-gray-200">
                                Выполненные задания
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
            `;

            // Обрабатываем данные студентов
            // Если данные приходят как объект с числовыми ключами
            const studentsArray = Array.isArray(students) ? students : Object.values(students);

            studentsArray.forEach(student => {
                const studentData = typeof student === 'object' ? student : { name: student, tasks: [] };
                const name = studentData.name || studentData.login || 'Неизвестно';
                const tasks = studentData.tasks || [];

                tableHTML += `
                    <tr>
                        <td class="px-4 py-3 text-gray-800 font-medium whitespace-nowrap">
                            ${escapeHtml(name)}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                `;

                if (tasks.length > 0) {
                    tasks.forEach(task => {
                        const taskId = task.task_id || task.id || task;
                        const taskSessionId = task.id || task.task_session_id || taskId;
                        const detailUrl = detailRouteBase + '/' + taskSessionId;
                        tableHTML += `
                            <a href="${detailUrl}"
                               class="w-10 h-10 flex items-center justify-center
                                      bg-blue-600 text-white font-semibold rounded
                                      hover:bg-blue-700 transition shadow">
                                ${escapeHtml(taskId)}
                            </a>
                        `;
                    });
                } else {
                    tableHTML += '<span class="text-gray-400">Нет выполненных заданий</span>';
                }

                tableHTML += `
                            </div>
                        </td>
                    </tr>
                `;
            });

            tableHTML += `
                    </tbody>
                </table>
            `;

            resultsContainer.innerHTML = tableHTML;
        }

        // Функция экранирования HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Показать индикатор загрузки
        function showLoading() {
            resultsContainer.classList.add('hidden');
            noResults.classList.add('hidden');
            loadingIndicator.classList.remove('hidden');
        }

        // Скрыть индикатор загрузки
        function hideLoading() {
            loadingIndicator.classList.add('hidden');
        }
    </script>
@endsection
