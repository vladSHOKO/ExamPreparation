@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto py-10 px-4">
        <div class="bg-white shadow-md rounded-lg p-6">

            <div class="mb-4 text-sm text-gray-500">
                Задание №{{$id}}
            </div>

            <div class="mb-6 text-lg font-medium text-gray-800">
                {{$description}}
            </div>

            <form action="action" method="POST" class="space-y-4">
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
                </div>
            </form>
        </div>
    </div>
@endsection
