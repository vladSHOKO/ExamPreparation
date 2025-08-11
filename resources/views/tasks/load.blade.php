@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Добавить новую задачу</h1>
        @if(session('success'))
            <div class="text-green-600 font-medium mb-4">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{route('loadTask')}}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Предмет --}}
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Предмет</label>
                <select id="subject" name="subject" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Выберите предмет</option>
                    <option value="Информатика">Информатика</option>
                    <option value="Математика">Математика</option>
                </select>
            </div>

            {{-- Тип задачи --}}
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Тип задачи</label>
                <input type="text" id="type" name="type" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            {{-- Описание задачи --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Описание задачи</label>
                <textarea id="description" name="description" rows="4" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
            </div>

            {{-- Ответ на задачу --}}
            <div>
                <label for="answer" class="block text-sm font-medium text-gray-700">Ответ на задачу</label>
                <input type="text" id="answer" name="answer" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            {{-- Файлы --}}
            <div>
                <label for="files" class="block text-sm font-medium text-gray-700">Файлы</label>
                <input type="file" id="files" name="files[]" multiple
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                           file:rounded-md file:border-0 file:text-sm file:font-semibold
                           file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>

            {{-- Кнопка отправки --}}
            <div>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md
                           font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2
                           focus:ring-offset-2 focus:ring-indigo-500">
                    Сохранить задачу
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
