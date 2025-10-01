<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Student;
use App\Models\TaskSession;
use App\Services\ChatClientInterface;
use Illuminate\Http\Request;

class ChatMessagesController extends Controller
{
    public function __construct(private ChatClientInterface $chatClient)
    {
    }

    public function getMessages(Request $request, $taskId)
    {
        $taskSession = $request->attributes->get('taskSession');
        return $taskSession->chatMessages()->orderBy('created_at')->get()->toArray();
    }

    public function postMessage(Request $request, $taskId)
    {
        $data = $request->validate([
            'content' => 'required|string|max:4000',
            'role' => 'required|string'
        ]);

        $taskSession = $request->attributes->get('taskSession');

        $chatMessage = new ChatMessage();
        $chatMessage->content = $data['content'];
        $chatMessage->role = $data['role'];
        $chatMessage->taskSession()->associate($taskSession);
        $chatMessage->save();

        $messages = $this->buildContext($taskSession, maxMessages: 5);

        // Логирование для отладки
        // \Log::info('Chat Messages being sent to OpenAI:', $messages);

        $response = $this->chatClient->chat($messages);
        $message = $response['choices'][0]['message'];

        $answer = new ChatMessage();
        $answer->content = $message['content'];
        $answer->role = $message['role'];
        $answer->taskSession()->associate($taskSession);
        $answer->save();


        return json_encode(['role' => $answer['role'], 'content' => $answer['content']]);
    }

    /**
     * Собираем системный промпт + окно контекста (последние N сообщений)
     */
    private function buildContext(TaskSession $session, int $maxMessages = 5): array
    {
        $task = $session->task()->first();
        $title = $task->title ?? '';
        $desc = $task->description ?? '';
        $files = $task->additionalFiles ?? null;

        $filesList = '';
        if (is_iterable($files)) {
            $paths = [];
            foreach ($files as $f) {
                $paths[] = $f->path ?? (string)$f;
            }
            if ($paths) {
                $filesList = "\nФайлы: \n- " . implode("\n- ", $paths);
            }
        }

        $system = [
            'role' => 'system',
            'content' => trim(
                "You are an experienced math and computer science teacher. Your task is to analyze the problems that a student provides and create a step-by-step solution algorithm. Do not give the final answer directly. Instead:\n\n1. Explain the problem in simple words so that the student understands it.\n2. Break the solution into logical, step-by-step instructions that a child can follow.\n3. Ask guiding questions if the student is stuck.\n4. Engage in a dialogue with the student, correcting misunderstandings, but never solving the problem entirely for them.\n\nAlways maintain a friendly, mentor-like tone. **All your responses must be in Russian**, even though this prompt is in English.\nTry to answer in 100 words and do accent on algorithm of solving task.\nIf you see, that algorithm already given say for student look at that.\nThink faster for very quick answers" .
                ($title ? "Задание: {$title}\n" : "") .
                ($desc ? "{$desc}\n" : "") .
                $filesList
            ),
        ];

        $history = $session->chatMessages()
            ->orderByDesc('created_at')
            ->limit($maxMessages)
            ->get()
            ->reverse()
            ->map(fn($m) => ['role' => $m->role, 'content' => $m->content])
            ->all();

        return [$system, ...$history];
    }
}
