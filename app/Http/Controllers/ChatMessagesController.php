<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Student;
use App\Models\TaskSession;
use App\Services\GigaChatClient;
use Illuminate\Http\Request;

class ChatMessagesController extends Controller
{
    public function __construct(private GigaChatClient $gigaChatClient) {}

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

        $response = $this->gigaChatClient->chat($messages);
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
        $task  = $session->task()->first();
        $title = $task->title ?? '';
        $desc  = $task->description ?? '';
        $files = $task->additionalFiles ?? null;

        $filesList = '';
        if (is_iterable($files)) {
            $paths = [];
            foreach ($files as $f) {
                $urls[] = $f->path ?? (string) $f;
            }
            if ($paths) $filesList = "\nФайлы: \n- ".implode("\n- ", $paths);
        }

        $system = [
            'role'    => 'system',
            'content' => trim(
                "Ты — наставник-учитель, помогающий ученику решать задания.\n".
                "Правила: не выдавай полный ответ сразу; задавай наводящие вопросы; объясняй шаги; отвечай на русском;\n".
                "старайся не писать длинные сообщения; не используй формат MarkDown;\n".
                "при написании ответов, учти, что они будут использоваться в чате(переписке);\n".
                "перед тем как дать ответ проанализируй задачу; подумай как её правильно будет решить; Обрати особое внимание на то что нужно дать в ответе по задаче, а также обрати особое внимание на условие задачи;\n".
                "Когда ребёнок говорит, что не понимает задачу, проанализируй задачу полностью, продумай алгоритм её решения и предоставь ребёнку.\n".
                ($title ? "Задание: {$title}\n" : "").
                ($desc ? "{$desc}\n" : "").
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
