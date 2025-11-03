@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Список задач</h1>
            <a href="{{ route('showLoadForm') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md
                      font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2
                      focus:ring-offset-2 focus:ring-indigo-500">
                Добавить задачу
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(count($tasks) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Предмет
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Тип
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Описание
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ответ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Действия
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tasks as $task)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $task['id'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $task['subject'] ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $task['type'] ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="max-w-xs truncate" title="{{ $task['description'] ?? '-' }}">
                                        {{ Str::limit($task['description'] ?? '-', 50) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <div class="max-w-xs truncate" title="{{ $task['answer'] ?? '-' }}">
                                        {{ Str::limit($task['answer'] ?? '-', 30) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="editTask({{ $task['id'] }})"
                                                class="text-blue-600 hover:text-blue-900 mr-3"
                                                title="Редактировать">
                                            Редактировать
                                        </button>
                                        <button onclick="deleteTask({{ $task['id'] }})"
                                                class="text-red-600 hover:text-red-900"
                                                title="Удалить">
                                            Удалить
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 text-lg">Задач пока нет</p>
                <a href="{{ route('showLoadForm') }}"
                   class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md
                          font-semibold text-white hover:bg-indigo-700">
                    Добавить первую задачу
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    function editTask(id) {
        window.location.href = '{{ route("task.edit", ":id") }}'.replace(':id', id);
    }

    function deleteTask(id) {
        if (confirm('Вы уверены, что хотите удалить задачу #' + id + '?')) {
            // Вызов API для удаления задачи
            fetch('{{ route("task.delete", ":id") }}'.replace(':id', id), {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                console.log(response.json());
                throw new Error(response.statusText);
            })
            .then(data => {
                alert('Задача успешно удалена');
                location.reload();
            })
            .catch(error => {
                alert('Ошибка: ' + error.message);
            });
        }
    }
</script>
@endsection

