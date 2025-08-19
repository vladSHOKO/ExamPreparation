<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Task;
use App\Models\TaskSession;
use Illuminate\Http\Request;

class ChatMessagesController extends Controller
{
    public function getMessages(Request $request, $taskId)
    {
        $task = Task::find($taskId);
        $taskSessionId = TaskSession::query()->where('task_id', $task->id)->first()->id;

        return ChatMessage::query()->where('task_session_id', $taskSessionId)->get();
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
