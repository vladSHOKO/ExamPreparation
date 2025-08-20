<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Student;
use App\Models\Task;
use App\Models\TaskSession;
use Illuminate\Http\Request;

class ChatMessagesController extends Controller
{
    public function getMessages(Request $request, $taskId)
    {
        $user = $request->user();
        $studentId = Student::query()->where('user_id', $user->id)->first()->id;
        $taskSessionId = TaskSession::query()->where('task_id', $taskId)->where('student_id', $studentId)->first()->id;

        return ChatMessage::query()->where('task_session_id', $taskSessionId)->orderBy('created_at')->get();
    }

    public function postMessage(Request $request, $taskId)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'role' => 'required|string'
        ]);
        $user = $request->user();

        $taskSession = TaskSession::query()->where('task_id', $taskId)->first();

        $chatMessage = new ChatMessage();
        $chatMessage->content = $data['content'];
        $chatMessage->role = $data['role'];



        return json_encode(['role' => 'assistant', 'content' => 'Тестовый ответ от сервера']);
    }
}
