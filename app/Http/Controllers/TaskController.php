<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Task;
use App\Models\TaskSession;
use Illuminate\Http\Request;

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
        $task = Task::query()->find($id);
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
}
