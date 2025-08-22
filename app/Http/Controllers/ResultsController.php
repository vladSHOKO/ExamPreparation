<?php

namespace App\Http\Controllers;

use App\Models\TaskSession;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    public function showResults(Request $request)
    {
        $user = $request->user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $students = $teacher->students()->get();
        $result = [];
        foreach($students as $student){
            $result[$student->id]['tasks'] = $student->tasks()->where('status', 'completed')->orderBy('task_id')->get()->toArray();
            $result[$student->id]['name'] = $student->user()->first()->login;
        }

        return view('results.results', ['students' => $result]);
    }

    public function showDetail(Request $request, int $taskSessionId)
    {
        $taskSession = TaskSession::where('id', $taskSessionId)->first();
        $task = $taskSession->task()->first()->toArray();
        $messages = $taskSession->chatMessages()->get()->toArray();
        $taskSession = $taskSession->toArray();

        return view('results.detail', ['task' => $task, 'taskSession' => $taskSession, 'messages' => $messages]);
    }
}
