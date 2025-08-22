<?php

namespace App\Http\Middleware;

use App\Models\Student;
use App\Models\TaskSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUserBelongsToStudySession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $taskId = (int) $request->route('taskId');
        $user = $request->user();
        $studentId = $user->role === 'student' ? Student::query()->where('user_id', $user->id)->first()->id : null;

        $session = TaskSession::query()->where('task_id', $taskId)
            ->where('student_id', $studentId)
            ->first();

        if (!$session) {
            abort(403, 'Нет доступа к этой учебной сессии.');
        }

        // прикрепим к запросу, чтобы второй раз не искать
        $request->attributes->set('taskSession', $session);

        return $next($request);
    }
}
