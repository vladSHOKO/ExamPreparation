@extends('layouts.app')

@section('content')
    <div class="overflow-x-auto">
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
            @foreach($students as $student)
                <tr>
                    {{-- Имя ученика --}}
                    <td class="px-4 py-3 text-gray-800 font-medium whitespace-nowrap">
                        {{ $student['name'] }}
                    </td>

                    {{-- Список выполненных заданий --}}
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            @foreach($student['tasks'] as $task)
                                <a href="{{ route('showDetail', $task['id']) }}"
                                   class="w-10 h-10 flex items-center justify-center
                                          bg-blue-600 text-white font-semibold rounded
                                          hover:bg-blue-700 transition shadow">
                                    {{ $task['task_id'] }}
                                </a>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
