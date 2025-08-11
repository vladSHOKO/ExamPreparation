<?php

namespace App\Http\Controllers;

use App\Models\AdditionalFile;
use App\Models\Student;
use App\Models\Task;
use App\Models\TaskSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function showTaskCollection(Request $request)
    {
        $student = Student::query()->where('user_id', $request->user()->id)->first();
        $tasks = Task::all();
        $taskSessions = TaskSession::query()->where('student_id', $student->id)->get()->toArray();
        $types = array_unique($tasks->pluck('type')->toArray());

        $result = [];
        $statuses = [];

        foreach ($taskSessions as $taskSession) {
            $statuses[$taskSession['task_id']] = $taskSession['status'];
        }

        foreach ($types as $type) {
            foreach ($tasks as $task) {
                if ($task->type == $type) {
                    $result[$type][$task['id']] = $task->toArray();
                    $result[$type][$task['id']]['status'] = isset($statuses[$task['id']]) ? $statuses[$task['id']] : 'none';
                }
            }
        }

        return view('tasks.collection', [
            'result' => $result,
        ]);
    }

    public function showTask(Request $request, $id)
    {
        $task = Task::with('additionalFiles')->find($id);
        $user = $request->user();
        $student = Student::query()->where('user_id', $user->id)->first();

        if (TaskSession::query()->where('task_id', $id)->where('student_id', $student->id)->doesntExist()) {
            $taskSession = new TaskSession();
            $taskSession->task()->associate($task);
            $taskSession->student()->associate($student);
            $taskSession->save();
        }

        return view('tasks.task', [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'files' => $task->additionalFiles,
        ]);
    }

    public function checkAnswer(Request $request, $id)
    {
        $data = $request->toArray();
        $user = $request->user();
        $student = Student::query()->where('user_id', $user->id)->first();
        $rightAnswer = Task::query()->find($id)->answer;

        if ($data['answer'] !== $rightAnswer) {
            return redirect()->back()
                ->withErrors(['answer' => 'Ответ неверный, попробуй подумать ещё раз']);
        }

        $taskSession = TaskSession::query()->where('student_id', $student->id)->where('task_id', $id)->first();
        $taskSession->status = 'completed';
        $taskSession->save();

        return redirect()->back()->with('success', 'Ты молодец, ответ верный!');
    }

    public function showLoadForm()
    {
        return view('tasks.load');
    }

    public function loadTask(Request $request)
    {
        $request->validate([
            'files' => 'array',
            'files.*' => 'file',
            'subject' => 'string|required',
            'type' => 'string|required',
            'description' => 'string|required',
            'answer' => 'string|required',
        ]);

        $task = new Task();
        $task->fill($request->only(['subject', 'type', 'description', 'answer']));
        $task->save();

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    // Генерируем уникальное имя файла
                    $filename = time() . '_' . uniqid() . '.' . $file->extension();

                    // Перемещаем файл в public/tasks
                    $file->move(public_path('tasks'), $filename);

                    // Создаём запись в additional_files
                    $additionalFile = new AdditionalFile();
                    $additionalFile->name = $filename;
                    $additionalFile->path = 'tasks/' . $filename;
                    $additionalFile->task()->associate($task);
                    $additionalFile->save();
                }
            }
        }

        return redirect()->back()->with('success', 'Задача успешно добавлена');
    }
}
