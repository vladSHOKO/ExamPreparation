@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                {{ isset($task) && $task->id ? 'Редактирование задачи #' . $task->id : 'Просмотр задачи' }}
            </h1>
            <a href="{{ route('tasksList') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md
                      font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2
                      focus:ring-offset-2 focus:ring-gray-500">
                Назад к списку
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                <ul class="list-disc list-inside text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="taskForm" class="space-y-6">
            @csrf

            {{-- Предмет --}}
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Предмет</label>
                <select id="subject" name="subject" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Выберите предмет</option>
                    <option value="Информатика" {{ (isset($task) && $task->subject === 'Информатика') ? 'selected' : '' }}>Информатика</option>
                    <option value="Математика" {{ (isset($task) && $task->subject === 'Математика') ? 'selected' : '' }}>Математика</option>
                </select>
            </div>

            {{-- Тип задачи --}}
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Тип задачи</label>
                <input type="text" id="type" name="type" value="{{ $task->type ?? '' }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            {{-- Описание задачи --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Описание задачи</label>
                <textarea id="description" name="description" rows="6" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $task->description ?? '' }}</textarea>
            </div>

            {{-- Ответ на задачу --}}
            <div>
                <label for="answer" class="block text-sm font-medium text-gray-700 mb-1">Правильный ответ</label>
                <input type="text" id="answer" name="answer" value="{{ $task->answer ?? '' }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            {{-- Дополнительные файлы --}}
            @if(isset($task) && $task->additionalFiles && count($task->additionalFiles) > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Прикрепленные файлы</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($task->additionalFiles as $file)
                            <div class="border border-gray-200 rounded-md p-3 flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-700">{{ $file->name }}</p>
                                    @if(str_contains($file->path, '.jpg') || str_contains($file->path, '.png') || str_contains($file->path, '.jpeg'))
                                        <img src="{{ asset($file->path) }}" alt="{{ $file->name }}" 
                                             class="mt-2 max-w-full h-auto rounded max-h-32 object-contain">
                                    @endif
                                </div>
                                <button type="button" onclick="deleteFile({{ $file->id }})"
                                        class="ml-3 text-red-600 hover:text-red-800 text-sm">
                                    Удалить
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Добавление новых файлов --}}
            <div>
                <label for="files" class="block text-sm font-medium text-gray-700 mb-1">Добавить файлы (опционально)</label>
                <input type="file" id="files" name="files[]" multiple
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0 file:text-sm file:font-semibold
                              file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-1 text-xs text-gray-500">Можно выбрать несколько файлов (PDF, DOC, DOCX, XLS, XLSX, TXT, JPG, PNG)</p>
            </div>

            {{-- Кнопки действий --}}
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="location.href='{{ route('tasksList') }}'"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md
                               font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2
                               focus:ring-offset-2 focus:ring-gray-500">
                    Отмена
                </button>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md
                               font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2
                               focus:ring-offset-2 focus:ring-indigo-500">
                    Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const taskId = {{ isset($task) && $task->id ? $task->id : 'null' }};
    const updateRoute = '{{ route("task.update", ":id") }}'.replace(':id', taskId);

    document.getElementById('taskForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        fetch(updateRoute, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            return response.json().then(err => Promise.reject(err));
        })
        .then(data => {
            alert('Задача успешно обновлена');
            location.reload();
        })
        .catch(error => {
            let errorMessage = 'Ошибка при сохранении задачи';
            if (error.errors) {
                errorMessage = Object.values(error.errors).flat().join('\n');
            } else if (error.message) {
                errorMessage = error.message;
            }
            alert(errorMessage);
        });
    });

    function deleteFile(fileId) {
        if (confirm('Вы уверены, что хотите удалить этот файл?')) {
            fetch('{{ route("task.file.delete", ":id") }}'.replace(':id', fileId), {
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
                throw new Error('Ошибка при удалении файла');
            })
            .then(data => {
                alert('Файл успешно удален');
                location.reload();
            })
            .catch(error => {
                alert('Ошибка: ' + error.message);
            });
        }
    }
</script>
@endsection

